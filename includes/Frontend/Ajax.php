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
		$type     = in_array( $_POST['type'], [ 'positive', 'negative' ] ) ? sanitize_text_field( $_POST['type'] ) : false;

		// check previous response
		if ( in_array( $post_id, $previous ) ) {
			$message = sprintf( $template, 'danger', esc_html__( 'Sorry, you\'ve already recorded your feedback!', 'eazydocs' ) );
			wp_send_json_error( $message );
		}

		// seems new
		if ( $type ) {
			$count 		= (int) get_post_meta( $post_id, $type, true );
			$timestamp 	= current_time( 'mysql' );

			update_post_meta( $post_id, $type, $count + 1 );

			if ( 'positive' === $type ) {
				$voters = get_post_meta( $post_id, 'positive_voter', true );
				$voters = is_array( $voters ) ? $voters : [];

				if ( ! in_array( get_current_user_id(), $voters ) ) {
					$voters[] = get_current_user_id();
					update_post_meta( $post_id, 'positive_voter', $voters );
				}

				update_post_meta( $post_id, 'positive_time', $timestamp );
			} else {
				$voters = get_post_meta( $post_id, 'negative_voter', true );
				$voters = is_array( $voters ) ? $voters : [];

				if ( ! in_array( get_current_user_id(), $voters ) ) {
					$voters[] = get_current_user_id();
					update_post_meta( $post_id, 'negative_voter', $voters );
				}

				update_post_meta( $post_id, 'negative_time', $timestamp );
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
	function eazydocs_search_results() {
		check_ajax_referer('eazydocs-ajax', 'security');
		global $wpdb;

		$keyword     = isset($_POST['keyword']) ? sanitize_text_field($_POST['keyword']) : '';
		$search_mode = ezd_is_premium() ? ezd_get_opt( 'search_by', 'title_and_content' ) : 'title_and_content';

		// Sentinel: Prevent unauthorized access to private docs
		$can_read_private = current_user_can( 'read_private_docs' ) || current_user_can( 'read_private_posts' );
		$post_status      = $can_read_private ? [ 'publish', 'private', 'protected' ] : [ 'publish', 'protected' ];

		if ( empty( $keyword ) ) {
			wp_send_json_error( [ 'message' => 'No keyword provided' ] );
		}

		// --- SEARCH LOGIC ---

		// Exact title matches
		$exact_ids = $wpdb->get_col($wpdb->prepare("
			SELECT ID FROM {$wpdb->posts}
			WHERE post_type = 'docs'
			AND post_status IN ('" . implode("','", $post_status) . "')
			AND post_title = %s
		", $keyword));

		// Partial title matches (excluding exact)
		$partial_ids = $wpdb->get_col($wpdb->prepare("
			SELECT ID FROM {$wpdb->posts}
			WHERE post_type = 'docs'
			AND post_status IN ('" . implode("','", $post_status) . "')
			AND post_title LIKE %s
		", '%' . $wpdb->esc_like($keyword) . '%'));
		$partial_ids = array_diff($partial_ids, $exact_ids);

		//  Content matches (only if mode allows)
		$content_ids = [];
		if ( $search_mode === 'title_and_content' ) {
			$content_ids = $wpdb->get_col($wpdb->prepare("
				SELECT ID FROM {$wpdb->posts}
				WHERE post_type = 'docs'
				AND post_status IN ('" . implode("','", $post_status) . "')
				AND post_content LIKE %s
			", '%' . $wpdb->esc_like($keyword) . '%'));
			$content_ids = array_diff($content_ids, $exact_ids, $partial_ids);
		}

		// Combine: exact → partial → content
		$final_ids = array_merge( $exact_ids, $partial_ids, $content_ids );
		if ( empty( $final_ids ) ) {
			$final_ids = [ 0 ];
		}

		// Add tag matches (appended after)
		$getTags  = get_terms(['taxonomy' => 'doc_tag', 'hide_empty' => false]);
		$checkTags = wp_list_pluck($getTags, 'name');
		if ( in_array($keyword, $checkTags, true) ) {
			$tag_posts = new WP_Query([
				'post_type'      => 'docs',
				'posts_per_page' => -1,
				'post_status'    => $post_status,
				'tax_query'      => [[
					'taxonomy' => 'doc_tag',
					'field'    => 'name',
					'terms'    => $keyword,
				]],
			]);
			$merged_ids = array_unique(array_merge($final_ids, wp_list_pluck($tag_posts->posts, 'ID')));
			$final_ids  = $merged_ids;
		}

		// Maintain order priority: exact → partial → content → tag
		$args = [
			'post_type'      => 'docs',
			'posts_per_page' => -1,
			'post_status'    => $post_status,
			'post__in'       => $final_ids,
			'orderby'        => [
				'post__in'    => 'ASC',
				'menu_order'  => 'ASC',
				'date'        => get_option('posts_order') === 'asc' ? 'ASC' : 'DESC',
				'title'       => 'ASC',
			],
		];

		$posts = new WP_Query($args);

		// --- LOG SEARCH KEYWORD ---
		$keyword_for_db = trim(strtolower($keyword));
		$wp_eazydocs_search_keyword = $wpdb->prefix . 'eazydocs_search_keyword';
		$wp_eazydocs_search_log     = $wpdb->prefix . 'eazydocs_search_log';

		$keyword_table_exists = $wpdb->get_var($wpdb->prepare("SHOW TABLES LIKE %s", $wp_eazydocs_search_keyword)) === $wp_eazydocs_search_keyword;
		$log_table_exists     = $wpdb->get_var($wpdb->prepare("SHOW TABLES LIKE %s", $wp_eazydocs_search_log)) === $wp_eazydocs_search_log;

		if ( $keyword_table_exists && $log_table_exists ) {
			$wpdb->insert( $wp_eazydocs_search_keyword, [ 'keyword' => $keyword_for_db ], [ '%s' ] );
			$keyword_id = $wpdb->insert_id;

			if ( $keyword_id ) {
				$wpdb->insert(
					$wp_eazydocs_search_log,
					[
						'keyword_id'      => $keyword_id,
						'count'           => $posts->post_count,
						'not_found_count' => $posts->post_count ? 0 : 1,
						'created_at'      => current_time('mysql'),
					],
					['%d', '%d', '%d', '%s']
				);
			}
		}
		?>
		<script>
			document.addEventListener('DOMContentLoaded', function() {
				document.querySelectorAll('.search-result-item').forEach(function(item) {
					item.addEventListener('click', function(e) {
						if (e.target.tagName === 'A' || e.target.closest('a')) return;
						let url = this.getAttribute('data-url');
						if (url) window.location.href = url;
					});
				});
			});
		</script>
		<?php
		// --- OUTPUT RESULTS (unchanged) ---
		if ( $posts->have_posts() ) :
			while ( $posts->have_posts() ) : $posts->the_post();
				$no_thumbnail = ezd_get_opt('is_search_result_thumbnail') == false ? 'no-thumbnail' : '';
				?>
				<div class="search-result-item <?php echo esc_attr($no_thumbnail); ?>" data-url="<?php the_permalink(); ?>">
					<a href="<?php the_permalink(); ?>" class="title">
						<?php if (ezd_get_opt('is_search_result_thumbnail')) :
							if (has_post_thumbnail() && ezd_is_premium() ) {
								the_post_thumbnail('ezd_searrch_thumb16x16');
							} else { ?>
								<svg width="16px" aria-labelledby="title" viewBox="0 0 17 17" fill="currentColor" class="block h-full w-auto" role="img">
									<title id="title">Building Search UI</title>
									<path d="M14.72,0H2.28A2.28,2.28,0,0,0,0,2.28V14.72A2.28,2.28,0,0,0,2.28,17H14.72A2.28,2.28,0,0,0,17,14.72V2.28A2.28,2.28,0,0,0,14.72,0ZM2.28,1H14.72A1.28,1.28,0,0,1,16,2.28V5.33H1V2.28A1.28,1.28,0,0,1,2.28,1ZM1,14.72V6.33H5.33V16H2.28A1.28,1.28,0,0,1,1,14.72ZM14.72,16H6.33V6.33H16v8.39A1.28,1.28,0,0,1,14.72,16Z"></path>
								</svg>
							<?php }
						endif; ?>
						<span class="doc-section"><?php the_title(); ?></span>
						<svg viewBox="0 0 24 24" fill="none" color="white" stroke="white" width="16px" stroke-width="2" stroke-linecap="round"
							stroke-linejoin="round" class="block h-auto w-16">
							<polyline points="9 10 4 15 9 20"></polyline>
							<path d="M20 4v7a4 4 0 0 1-4 4H4"></path>
						</svg>
					</a>
					<?php 
					if (ezd_get_opt('is_search_result_breadcrumb') && ezd_is_premium() ){
						eazydocs_search_breadcrumbs();
					}
					?>
				</div>
				<?php
			endwhile;
			else :
				?>
				<div><h5 class="error title"><?php esc_html_e('No result found!', 'eazydocs'); ?></h5></div>
				<?php
		endif;

		wp_reset_postdata();
		
		die();
	}

	/**
	 * Doc single page
	 */
	function docs_single_content() {
		// Verify nonce for security
		check_ajax_referer('eazydocs-ajax', 'security');

		$postid 		= isset($_POST['postid']) ? intval($_POST['postid']) : 0;

		// Validate post ID
		if ($postid <= 0) {
			wp_send_json_error(array('message' => esc_html__('Invalid document ID', 'eazydocs')));
			return;
		}

		// Check private doc access
		if ( 'private' === get_post_status( $postid ) && ezd_is_premium() ) {
			// Try new settings first
			$access_type = ezd_get_opt( 'private_doc_access_type', '' );
			$has_access  = false;
			
			if ( ! empty( $access_type ) ) {
				// Using new settings
				if ( $access_type === 'all_users' ) {
					// All logged-in users can access
					$has_access = is_user_logged_in();
				} else {
					// Specific roles only
					$allowed_roles   = ezd_get_opt( 'private_doc_allowed_roles', array( 'administrator', 'editor' ) );
					if ( ! is_array( $allowed_roles ) ) {
						$allowed_roles = array( $allowed_roles );
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
				
				if ( $is_all_user === '1' || $is_all_user === 1 || $is_all_user === true ) {
					$has_access = is_user_logged_in();
				} else {
					$current_user_id   = get_current_user_id();
					$current_user      = new \WP_User( $current_user_id );
					$current_roles     = (array) $current_user->roles;
					$private_doc_roles = $user_group['private_doc_roles'] ?? array();
					$matching_roles    = array_intersect( $current_roles, $private_doc_roles );
					
					$has_access = ! empty( $matching_roles ) || current_user_can( 'manage_options' );
				}
			}
			
			if ( ! $has_access ) {
				$denied_message = ezd_get_opt( 'role_visibility_denied_message', esc_html__( 'You don\'t have permission to access this document!', 'eazydocs' ) );
				wp_send_json_error( array( 'message' => esc_html( $denied_message ) ) );
				return;
			}
		}

		global $post, $wp_query;
		$wp_query 		= new \WP_Query( array( 'post_type' => 'docs', 'p' => $postid ) );
		$modified 		= '';
		$html 			= '';

		ob_start();

		if ( $wp_query->have_posts() ) { 
			while ( $wp_query->have_posts() ) {
				$wp_query->the_post();

				$modified 			 = get_the_modified_date( get_option( 'date_format' ) );
				$GLOBALS['wp_query'] = $wp_query;
				$GLOBALS['post']     = get_post();
				setup_postdata( $post );

				add_filter( 'is_singular', '__return_true' );

				// Instantiate Frontend from same namespace
				new Frontend();

				eazydocs_get_template_part( 'single-doc-content' );
			}
			wp_reset_postdata();
		}

		$html = ob_get_clean();

		return wp_send_json_success( array(
			'content'         => $html,
			'modified_date'   => $modified
		) );
	}
}
