<?php
namespace EazyDocs\Frontend;

use JetBrains\PhpStorm\NoReturn;
use WP_Query;

/**
 * Cannot access directly.
 */
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

/**
 * Class Ajax
 *
 * Handles AJAX actions for various features, such as feedback submission,
 * searching documentation, and loading single page content.
 */
class Ajax {
	public function __construct() {
		// feedback
		add_action( 'wp_ajax_eazydocs_handle_feedback', [ $this, 'handle_feedback' ] );
		add_action( 'wp_ajax_nopriv_eazydocs_handle_feedback', [ $this, 'handle_feedback' ] );
		// Search Results
		add_action( 'wp_ajax_eazydocs_search_results', [ $this, 'eazydocs_search_results' ] );
		add_action( 'wp_ajax_nopriv_eazydocs_search_results', [ $this, 'eazydocs_search_results' ] );
		// Load Doc single page
		add_action( 'wp_ajax_docs_single_content', [ $this, 'docs_single_content' ] );
		add_action( 'wp_ajax_nopriv_docs_single_content', [ $this, 'docs_single_content' ] );
		// Child docs for browse dropdown
		add_action( 'wp_ajax_eazydocs_child_docs', [ $this, 'child_docs' ] );
		add_action( 'wp_ajax_nopriv_eazydocs_child_docs', [ $this, 'child_docs' ] );
	}

	/**
	 * Store feedback for an article.
	 *
	 * @return void
	 */
	public function handle_feedback() {
		check_ajax_referer( 'eazydocs-ajax', 'security' );

		$template = '<div class="eazydocs-alert alert-%s">%s</div>';
		$previous = [];

		if ( isset( $_COOKIE['eazydocs_response'] ) ) {
			// Unsplash the cookie value first
			$cookie_value = wp_unslash( $_COOKIE['eazydocs_response'] );

			// Sanitize and explode
			$previous = explode( ',', sanitize_text_field( $cookie_value ) );
		}

		$post_id  = isset( $_POST['post_id'] ) ? absint( $_POST['post_id'] ) : 0;
		$raw_type = isset( $_POST['type'] ) ? sanitize_key( wp_unslash( $_POST['type'] ) ) : '';
		$type     = in_array( $raw_type, [ 'positive', 'negative' ], true ) ? $raw_type : false;

		// Reject votes for posts that don't exist (avoids orphan meta writes).
		if ( ! $post_id || ! get_post( $post_id ) ) {
			wp_send_json_error( sprintf( $template, 'danger', esc_html__( 'Invalid document.', 'eazydocs' ) ) );
		}

		// check previous response
		// $previous is array of strings (from explode), $post_id is int. Cast to string for strict check.
		if ( in_array( (string) $post_id, $previous, true ) ) {
			$message = sprintf( $template, 'danger', esc_html__( 'Sorry, you\'ve already recorded your feedback!', 'eazydocs' ) );
			wp_send_json_error( $message );
		}

		// seems new
		if ( $type ) {
			$count      = (int) get_post_meta( $post_id, $type, true );
			$timestamp  = current_time( 'mysql' );

			update_post_meta( $post_id, $type, $count + 1 );

			if ( 'negative' === $type ) {
				// EazyDocs Enhancement: Notify admin when negative feedback threshold is reached.
				$negative_count = $count + 1;
				/**
				 * Filter the negative feedback threshold for admin notification.
				 *
				 * @param int $threshold The number of negative feedbacks required to trigger a notification. Default 3.
				 */
				$threshold = apply_filters( 'ezd_negative_feedback_threshold', 3 );

				if ( $threshold > 0 && $negative_count >= $threshold && 0 === ( $negative_count % $threshold ) ) {
					if ( ! wp_next_scheduled( 'ezd_negative_feedback_notification', [ $post_id ] ) ) {
						wp_schedule_single_event( time(), 'ezd_negative_feedback_notification', [ $post_id ] );
					}
				}
			}

			if ( 'positive' === $type ) {
				$voters = get_post_meta( $post_id, 'positive_voter', true );
				$voters = is_array( $voters ) ? $voters : [];

				if ( ! in_array( get_current_user_id(), $voters, true ) ) {
					$voters[] = get_current_user_id();
					update_post_meta( $post_id, 'positive_voter', $voters );
				}

				update_post_meta( $post_id, 'positive_time', $timestamp );
			} else {
				$voters = get_post_meta( $post_id, 'negative_voter', true );
				$voters = is_array( $voters ) ? $voters : [];

				if ( ! in_array( get_current_user_id(), $voters, true ) ) {
					$voters[] = get_current_user_id();
					update_post_meta( $post_id, 'negative_voter', $voters );
				}

				update_post_meta( $post_id, 'negative_time', $timestamp );
				// The threshold notification is scheduled once, above. A second
				// schedule here used to send the admin duplicate alert emails.
			}

			array_push( $previous, $post_id );
			$cookie_val = implode( ',', $previous );

			$val = setcookie( 'eazydocs_response', $cookie_val, time() + WEEK_IN_SECONDS, COOKIEPATH, COOKIE_DOMAIN );
		}

		$message = sprintf( $template, 'success', esc_html__( 'Thanks for your feedback!', 'eazydocs' ) );
		wp_send_json_success( $message );
	}

	/**
	 * Ajax Search Results
	 *
	 * @return void
	 */
	public function eazydocs_search_results() {
		// Search is read-only public data, so a stale nonce (full-page caches often
		// outlive the 12–24h nonce lifetime) must not break it. The nonce only
		// gates whether the query is written to the search analytics log.
		$valid_nonce = false !== check_ajax_referer( 'eazydocs-ajax', 'security', false );
		global $wpdb;

		$keyword     = isset( $_POST['keyword'] ) ? sanitize_text_field( wp_unslash( $_POST['keyword'] ) ) : '';
		$search_mode = ezd_is_premium() ? ezd_get_opt( 'search_by', 'title_and_content' ) : 'title_and_content';

		$can_read_private = current_user_can( 'read_private_docs' ) || current_user_can( 'read_private_posts' );
		$post_status      = $can_read_private ? [ 'publish', 'private', 'protected' ] : [ 'publish', 'protected' ];

		if ( empty( $keyword ) ) {
			wp_send_json_error( [ 'message' => 'No keyword provided' ] );
		}

		$selected_type = isset( $_POST['post_type'] ) ? sanitize_text_field( $_POST['post_type'] ) : 'all';
		$allowed_types = [ 'all', 'docs', 'page', 'post', 'api_docs' ];
		if ( ! in_array( $selected_type, $allowed_types, true ) ) {
			$selected_type = 'all';
		}

		if ( 'all' === $selected_type ) {
			$search_types = [ 'docs', 'page', 'post' ];
			if ( post_type_exists( 'api_docs' ) ) {
				$search_types[] = 'api_docs';
			}
		} elseif ( 'api_docs' === $selected_type && ! post_type_exists( 'api_docs' ) ) {
			$search_types = [ 'docs', 'page', 'post' ];
		} else {
			$search_types = [ $selected_type ];
		}

		$normalized_keyword = strtolower( trim( $keyword ) );
		$results_by_type    = [];
		$found_by_type      = [];

		// Ranked IDs grouped by post type. Cached in the object cache (not a
		// transient): live search fires on every pause in typing, and transients
		// wrote two wp_options rows per keystroke per post type on sites without a
		// persistent object cache.
		$cache_key    = 'ids_' . md5( $normalized_keyword . '|' . $search_mode . '|' . implode( ',', $search_types ) . '|' . ( $can_read_private ? 'priv' : 'pub' ) );
		$ids_by_type  = wp_cache_get( $cache_key, 'ezd_search' );

		if ( ! is_array( $ids_by_type ) ) {
			$like         = '%' . $wpdb->esc_like( $keyword ) . '%';
			$type_holders = implode( ', ', array_fill( 0, count( $search_types ), '%s' ) );
			$stat_holders = implode( ', ', array_fill( 0, count( $post_status ), '%s' ) );
			$match_sql    = 'title_and_content' === $search_mode ? '( post_title LIKE %s OR post_content LIKE %s )' : 'post_title LIKE %s';
			$match_args   = 'title_and_content' === $search_mode ? [ $like, $like ] : [ $like ];

			// One scan of wp_posts for every post type, ranked exact title →
			// partial title → content, instead of three LIKE scans per post type.
			// phpcs:ignore WordPress.DB.DirectDatabaseQuery, WordPress.DB.PreparedSQL.InterpolatedNotPrepared -- placeholders are built above.
			$rows = $wpdb->get_results(
				$wpdb->prepare(
					"SELECT ID, post_type FROM {$wpdb->posts}
					 WHERE post_type IN ({$type_holders})
					   AND post_status IN ({$stat_holders})
					   AND {$match_sql}
					 ORDER BY CASE WHEN post_title = %s THEN 0 WHEN post_title LIKE %s THEN 1 ELSE 2 END, post_date ASC, ID ASC",
					array_merge( $search_types, $post_status, $match_args, [ $keyword, $like ] )
				)
			);

			$ids_by_type = array_fill_keys( $search_types, [] );
			foreach ( (array) $rows as $row ) {
				$ids_by_type[ $row->post_type ][] = (int) $row->ID;
			}

			// Docs tagged with the exact keyword are appended after text matches.
			if ( isset( $ids_by_type['docs'] ) ) {
				$tag = get_term_by( 'name', $keyword, 'doc_tag' );
				if ( $tag ) {
					$tag_ids = get_posts( [
						'post_type'              => 'docs',
						'posts_per_page'         => 200,
						'post_status'            => $post_status,
						'fields'                 => 'ids',
						'no_found_rows'          => true,
						'update_post_meta_cache' => false,
						'update_post_term_cache' => false,
						'tax_query'              => [ [ 'taxonomy' => 'doc_tag', 'field' => 'term_id', 'terms' => (int) $tag->term_id ] ],
					] );
					$ids_by_type['docs'] = array_values( array_unique( array_merge( $ids_by_type['docs'], array_map( 'intval', $tag_ids ) ) ) );
				}
			}

			wp_cache_set( $cache_key, $ids_by_type, 'ezd_search', 5 * MINUTE_IN_SECONDS );
		}

		foreach ( $search_types as $ptype ) {
			$ids = $ids_by_type[ $ptype ] ?? [];
			if ( empty( $ids ) ) {
				continue;
			}

			$found_by_type[ $ptype ] = count( $ids );

			// Only the first 10 per type are rendered; no need to count again.
			$query = new WP_Query( [
				'post_type'              => $ptype,
				'posts_per_page'         => 10,
				'post_status'            => $post_status,
				'post__in'               => array_slice( $ids, 0, 10 ),
				'orderby'                => 'post__in',
				'no_found_rows'          => true,
				'ignore_sticky_posts'    => true,
			] );
			if ( $query->have_posts() ) {
				$results_by_type[ $ptype ] = $query;
			}
		}

		// --- LOG SEARCH KEYWORD ---
		$keyword_for_db             = trim( strtolower( $keyword ) );
		$wp_eazydocs_search_keyword = $wpdb->prefix . 'eazydocs_search_keyword';
		$wp_eazydocs_search_log     = $wpdb->prefix . 'eazydocs_search_log';
		$tables_check_key           = 'ezd_search_tables_check';
		$tables_exist               = get_transient( $tables_check_key );

		if ( false === $tables_exist ) {
			$kw_exists  = $wpdb->get_var( $wpdb->prepare( "SHOW TABLES LIKE %s", $wp_eazydocs_search_keyword ) ) === $wp_eazydocs_search_keyword;
			$log_exists = $wpdb->get_var( $wpdb->prepare( "SHOW TABLES LIKE %s", $wp_eazydocs_search_log ) ) === $wp_eazydocs_search_log;
			$tables_exist = ( $kw_exists && $log_exists ) ? 1 : 0;
			set_transient( $tables_check_key, $tables_exist, DAY_IN_SECONDS );
		}

		$total_found = array_sum( $found_by_type );

		if ( $tables_exist && $valid_nonce ) {
			$wpdb->insert( $wp_eazydocs_search_keyword, [ 'keyword' => $keyword_for_db ], [ '%s' ] );
			$keyword_id = $wpdb->insert_id;
			if ( $keyword_id ) {
				$wpdb->insert( $wp_eazydocs_search_log, [
					'keyword_id'      => $keyword_id,
					'count'           => $total_found,
					'not_found_count' => $total_found ? 0 : 1,
					'created_at'      => current_time( 'mysql' ),
				], [ '%d', '%d', '%d', '%s' ] );
			}
		}

		// --- OUTPUT ---
		$type_labels = [
			'docs'     => __( 'Docs', 'eazydocs' ),
			'page'     => __( 'Page', 'eazydocs' ),
			'post'     => __( 'Post', 'eazydocs' ),
			'api_docs' => __( 'API Docs', 'eazydocs' ),
		];

		ob_start();

		if ( ! empty( $results_by_type ) ) :
			?>
			<div class="ezd-result-tabs">
				<button type="button" class="ezd-tab active" data-tab="all"><?php esc_html_e( 'All', 'eazydocs' ); ?></button>
				<?php foreach ( $results_by_type as $ptype => $query ) : ?>
					<button type="button" class="ezd-tab" data-tab="<?php echo esc_attr( $ptype ); ?>">
						<?php echo esc_html( $type_labels[ $ptype ] ?? ucfirst( $ptype ) ); ?>
					</button>
				<?php endforeach; ?>
			</div>
			<?php
			foreach ( $results_by_type as $ptype => $query ) :
				?>
				<div class="ezd-result-group" data-type="<?php echo esc_attr( $ptype ); ?>">
					<div class="ezd-result-group-label"><?php echo esc_html( $type_labels[ $ptype ] ?? ucfirst( $ptype ) ); ?></div>
					<?php
					while ( $query->have_posts() ) :
						$query->the_post();
						$no_thumbnail = ! ezd_get_opt( 'is_search_result_thumbnail' ) ? 'no-thumbnail' : '';
						?>
						<div class="search-result-item <?php echo esc_attr( $no_thumbnail ); ?>" data-url="<?php the_permalink(); ?>" data-type="<?php echo esc_attr( $ptype ); ?>">
							<a href="<?php the_permalink(); ?>" class="title">
								<?php if ( ezd_get_opt( 'is_search_result_thumbnail' ) ) :
									// has_post_thumbnail() can be true while the attachment is missing
									// (dangling _thumbnail_id), so render the thumbnail HTML and fall back
									// to the doc icon whenever it comes back empty.
									$thumbnail_html = ( has_post_thumbnail() && ezd_is_premium() )
										? get_the_post_thumbnail( null, 'ezd_searrch_thumb16x16' )
										: '';
									if ( $thumbnail_html ) {
										echo $thumbnail_html; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- Safe HTML generated by WordPress core.
									} else { ?>
										<svg width="16px" aria-labelledby="ezd-doc-icon" viewBox="0 0 17 17" fill="currentColor" class="block h-full w-auto" role="img">
											<title id="ezd-doc-icon">Document</title>
											<path d="M14.72,0H2.28A2.28,2.28,0,0,0,0,2.28V14.72A2.28,2.28,0,0,0,2.28,17H14.72A2.28,2.28,0,0,0,17,14.72V2.28A2.28,2.28,0,0,0,14.72,0ZM2.28,1H14.72A1.28,1.28,0,0,1,16,2.28V5.33H1V2.28A1.28,1.28,0,0,1,2.28,1ZM1,14.72V6.33H5.33V16H2.28A1.28,1.28,0,0,1,1,14.72ZM14.72,16H6.33V6.33H16v8.39A1.28,1.28,0,0,1,14.72,16Z"></path>
										</svg>
									<?php }
								endif; ?>
								<div class="ezd-item-body">
									<span class="doc-section"><?php the_title(); ?></span>
									<span class="ezd-item-type"><?php echo esc_html( $type_labels[ $ptype ] ?? ucfirst( $ptype ) ); ?></span>
								</div>
							</a>
							<?php
							if ( 'docs' === $ptype && ezd_get_opt( 'is_search_result_breadcrumb' ) && ezd_is_premium() ) {
								eazydocs_search_breadcrumbs();
							}
							?>
						</div>
					<?php endwhile; ?>
					<?php wp_reset_postdata(); ?>
				</div>
			<?php endforeach; ?>
		<?php endif;

		echo ob_get_clean();
		wp_die();
	}

	/**
	 * Doc single page
	 *
	 * @return void
	 */
	public function docs_single_content() {
		// Read-only: access to private docs is enforced below, not by the nonce.
		// A hard nonce check broke AJAX doc loading on full-page-cached sites
		// once the cached nonce outlived its 12–24h lifetime.
		check_ajax_referer( 'eazydocs-ajax', 'security', false );

		$postid     = isset( $_POST['postid'] ) ? intval( $_POST['postid'] ) : 0;

		// Validate post ID
		if ( $postid <= 0 ) {
			wp_send_json_error( [ 'message' => esc_html__( 'Invalid document ID', 'eazydocs' ) ] );
			return;
		}

		// Check private doc access
		if ( 'private' === get_post_status( $postid ) && ezd_is_premium() ) {
			// Try new settings first
			$access_type = ezd_get_opt( 'private_doc_access_type', '' );
			$has_access  = false;

			if ( ! empty( $access_type ) ) {
				// Using new settings
				if ( 'all_users' === $access_type ) {
					// All logged-in users can access
					$has_access = is_user_logged_in();
				} else {
					// Specific roles only
					$allowed_roles = ezd_get_opt( 'private_doc_allowed_roles', [ 'administrator', 'editor' ] );
					if ( ! is_array( $allowed_roles ) ) {
						$allowed_roles = [ $allowed_roles ];
					}

					$current_user_id = get_current_user_id();
					$current_user    = new \WP_User( $current_user_id );
					$current_roles   = (array) $current_user->roles;
					$matching_roles  = array_intersect( $current_roles, $allowed_roles );

					$has_access = ! empty( $matching_roles ) || current_user_can( 'manage_options' );
				}
			} else {
				// Fallback to legacy settings
				$user_group  = ezd_get_opt( 'private_doc_user_restriction' );
				$is_all_user = $user_group['private_doc_all_user'] ?? 0;

				if ( '1' === $is_all_user || 1 === $is_all_user || true === $is_all_user ) {
					$has_access = is_user_logged_in();
				} else {
					$current_user_id   = get_current_user_id();
					$current_user      = new \WP_User( $current_user_id );
					$current_roles     = (array) $current_user->roles;
					$private_doc_roles = $user_group['private_doc_roles'] ?? [];
					$matching_roles    = array_intersect( $current_roles, $private_doc_roles );

					$has_access = ! empty( $matching_roles ) || current_user_can( 'manage_options' );
				}
			}

			if ( ! $has_access ) {
				$denied_message = ezd_get_opt( 'role_visibility_denied_message', esc_html__( 'You don\'t have permission to access this document!', 'eazydocs' ) );
				wp_send_json_error( [ 'message' => esc_html( $denied_message ) ] );
				return;
			}
		}

		global $post, $wp_query;
		$wp_query       = new \WP_Query( [ 'post_type' => 'docs', 'p' => $postid ] );
		$modified       = '';
		$html           = '';

		ob_start();

		if ( $wp_query->have_posts() ) {
			while ( $wp_query->have_posts() ) {
				$wp_query->the_post();

				$modified            = get_the_modified_date( get_option( 'date_format' ) );
				$GLOBALS['wp_query'] = $wp_query;
				$GLOBALS['post']     = get_post();
				setup_postdata( $post );

				add_filter( 'is_singular', '__return_true' );

				// Instantiate Frontend from same namespace
				/**
				 * The Frontend class is not instantiated during AJAX requests (is_admin() is true),
				 * but we need its hooks (like shortcode handling) for rendering the single doc content.
				 */
				new Frontend();

				eazydocs_get_template_part( 'single-doc-content' );
			}
			wp_reset_postdata();
		}

		$html = ob_get_clean();

		return wp_send_json_success( [
			'content'         => $html,
			'modified_date'   => $modified,
		] );
	}

	/**
	 * Return child docs of a parent doc for the browse dropdown.
	 *
	 * @return void
	 */
	public function child_docs() {
		// Read-only list of published docs; see docs_single_content() on nonces.
		check_ajax_referer( 'eazydocs-ajax', 'security', false );

		$parent_id = isset( $_POST['parent_id'] ) ? intval( $_POST['parent_id'] ) : 0;

		if ( $parent_id <= 0 ) {
			wp_send_json_error( [ 'message' => 'Invalid parent ID' ] );
			return;
		}

		$children = get_posts( [
			'post_type'              => 'docs',
			'post_parent'            => $parent_id,
			'posts_per_page'         => 200,
			'orderby'                => 'menu_order',
			'order'                  => 'ASC',
			'post_status'            => 'publish',
			'no_found_rows'          => true,
			'update_post_meta_cache' => false,
			'update_post_term_cache' => false,
		] );

		if ( empty( $children ) ) {
			wp_send_json_success( [ 'html' => '', 'count' => 0 ] );
			return;
		}

		ob_start();
		foreach ( $children as $child ) {
			$url = get_permalink( $child->ID );
			?>
			<div class="search-result-item no-thumbnail">
				<a href="<?php echo esc_url( $url ); ?>" class="title">
					<div class="ezd-item-body">
						<p class="doc-section"><?php echo esc_html( $child->post_title ); ?></p>
						<span class="ezd-item-type"><?php esc_html_e( 'Docs', 'eazydocs' ); ?></span>
					</div>
				</a>
			</div>
			<?php
		}
		$html = ob_get_clean();

		wp_send_json_success( [ 'html' => $html, 'count' => count( $children ) ] );
	}
}
