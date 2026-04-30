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

		$post_id  = intval( $_POST['post_id'] );
		$type     = in_array( $_POST['type'], [ 'positive', 'negative' ], true ) ? sanitize_text_field( $_POST['type'] ) : false;

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

				// Schedule notification when negative count reaches a multiple of the threshold (e.g., 3, 6, 9)
				$new_count = $count + 1;
				if ( $new_count > 0 && 0 === ( $new_count % 3 ) ) {
					wp_schedule_single_event( time(), 'ezd_negative_feedback_notification', [ $post_id, $new_count ] );
				}
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
		check_ajax_referer( 'eazydocs-ajax', 'security' );
		global $wpdb;

		$keyword     = isset( $_POST['keyword'] ) ? sanitize_text_field( $_POST['keyword'] ) : '';
		$search_mode = ezd_is_premium() ? ezd_get_opt( 'search_by', 'title_and_content' ) : 'title_and_content';

		$can_read_private = current_user_can( 'read_private_docs' ) || current_user_can( 'read_private_posts' );
		$post_status      = $can_read_private ? [ 'publish', 'private', 'protected' ] : [ 'publish', 'protected' ];

		if ( empty( $keyword ) ) {
			wp_send_json_error( [ 'message' => 'No keyword provided' ] );
		}

		$selected_type = isset( $_POST['post_type'] ) ? sanitize_text_field( $_POST['post_type'] ) : 'all';
		if ( ! in_array( $selected_type, [ 'all', 'docs', 'page', 'post' ], true ) ) {
			$selected_type = 'all';
		}
		$search_types = $selected_type === 'all' ? [ 'docs', 'page', 'post' ] : [ $selected_type ];

		$normalized_keyword = strtolower( trim( $keyword ) );
		$status_in          = "'" . implode( "','", $post_status ) . "'";
		$results_by_type    = [];

		foreach ( $search_types as $ptype ) {
			$cache_key = 'ezd_search_ids_' . md5( $normalized_keyword . '_' . $search_mode . '_' . $ptype . '_' . ( $can_read_private ? 'priv' : 'pub' ) );
			$ids       = get_transient( $cache_key );

			if ( false === $ids ) {
				$exact_ids = $wpdb->get_col( $wpdb->prepare(
					"SELECT ID FROM {$wpdb->posts} WHERE post_type = %s AND post_status IN ({$status_in}) AND post_title = %s",
					$ptype, $keyword
				) );

				$partial_ids = $wpdb->get_col( $wpdb->prepare(
					"SELECT ID FROM {$wpdb->posts} WHERE post_type = %s AND post_status IN ({$status_in}) AND post_title LIKE %s",
					$ptype, '%' . $wpdb->esc_like( $keyword ) . '%'
				) );
				$partial_ids = array_diff( $partial_ids, $exact_ids );

				$content_ids = [];
				if ( 'title_and_content' === $search_mode ) {
					$content_ids = $wpdb->get_col( $wpdb->prepare(
						"SELECT ID FROM {$wpdb->posts} WHERE post_type = %s AND post_status IN ({$status_in}) AND post_content LIKE %s",
						$ptype, '%' . $wpdb->esc_like( $keyword ) . '%'
					) );
					$content_ids = array_diff( $content_ids, $exact_ids, $partial_ids );
				}

				$ids = array_merge( $exact_ids, $partial_ids, $content_ids );

				if ( 'docs' === $ptype && get_term_by( 'name', $keyword, 'doc_tag' ) ) {
					$tag_query = new WP_Query( [
						'post_type'      => 'docs',
						'posts_per_page' => -1,
						'post_status'    => $post_status,
						'tax_query'      => [ [ 'taxonomy' => 'doc_tag', 'field' => 'name', 'terms' => $keyword ] ],
					] );
					$ids = array_unique( array_merge( $ids, wp_list_pluck( $tag_query->posts, 'ID' ) ) );
					wp_reset_postdata();
				}

				set_transient( $cache_key, $ids, 5 * MINUTE_IN_SECONDS );
			}

			if ( ! empty( $ids ) ) {
				$query = new WP_Query( [
					'post_type'      => $ptype,
					'posts_per_page' => 10,
					'post_status'    => $post_status,
					'post__in'       => $ids,
					'orderby'        => [ 'post__in' => 'ASC', 'title' => 'ASC' ],
				] );
				if ( $query->have_posts() ) {
					$results_by_type[ $ptype ] = $query;
				}
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

		$total_found = 0;
		foreach ( $results_by_type as $q ) {
			$total_found += $q->found_posts;
		}

		if ( $tables_exist ) {
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
			'docs' => __( 'Docs', 'eazydocs' ),
			'page' => __( 'Page', 'eazydocs' ),
			'post' => __( 'Post', 'eazydocs' ),
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
									if ( has_post_thumbnail() && ezd_is_premium() ) {
										the_post_thumbnail( 'ezd_searrch_thumb16x16' );
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
		// Verify nonce for security
		check_ajax_referer( 'eazydocs-ajax', 'security' );

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
		check_ajax_referer( 'eazydocs-ajax', 'security' );

		$parent_id = isset( $_POST['parent_id'] ) ? intval( $_POST['parent_id'] ) : 0;

		if ( $parent_id <= 0 ) {
			wp_send_json_error( [ 'message' => 'Invalid parent ID' ] );
			return;
		}

		$children = get_posts( [
			'post_type'      => 'docs',
			'post_parent'    => $parent_id,
			'posts_per_page' => -1,
			'orderby'        => 'menu_order',
			'order'          => 'ASC',
			'post_status'    => 'publish',
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
