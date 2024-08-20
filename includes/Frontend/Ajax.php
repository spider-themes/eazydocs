<?php

namespace eazyDocs\Frontend;

use JetBrains\PhpStorm\NoReturn;
use WP_Query;

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
		check_ajax_referer( 'eazydocs-ajax' );

		$template = '<div class="eazydocs-alert alert-%s">%s</div>';
		$previous = isset( $_COOKIE['eazydocs_response'] ) ? explode( ',', htmlspecialchars( $_COOKIE['eazydocs_response'] ) ) : [];
		$post_id  = intval( $_POST['post_id'] );
		$type     = in_array( $_POST['type'], [ 'positive', 'negative' ] ) ? sanitize_text_field( $_POST['type'] ) : false;

		// check previous response
		if ( in_array( $post_id, $previous ) ) {
			$message = sprintf( $template, 'danger', __( 'Sorry, you\'ve already recorded your feedback!', 'eazydocs' ) );
			wp_send_json_error( $message );
		}

		// seems new
		if ( $type ) {
			$count = (int) get_post_meta( $post_id, $type, true );

			$timestamp = current_time( 'mysql' );

			update_post_meta( $post_id, $type, $count + 1 );

			if ( $type == 'positive' ) {
				update_post_meta( $post_id, 'positive_time', $timestamp );
			} else {
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
 
		$keyword = sanitize_text_field($_POST['keyword']);

		// Search by keyword
		$args = [
			'post_type'      	=> 'docs',
			'posts_per_page' 	=> -1,
			'post_status'    	=> ['publish', 'private'],    
			's' 				=> $keyword, // Include keyword search
		];
		
		$posts = new WP_Query($args);
		
		// Search by tag
		$getTags 	= get_terms([ 'taxonomy' => 'doc_tag', 'hide_empty' => false, 'object_ids' => get_posts([ 'post_type' => 'docs', 'posts_per_page' => -1, 'fields' => 'ids', ]) ]);
		$checkTags 	= [];

		if ( ! empty( $getTags ) && !is_wp_error( $getTags ) ) {
			foreach ($getTags as $tag) {
				$checkTags[] =  $tag->name;
			}
		}

		$postsByTags = [];

		if ( array_search( $keyword, $checkTags ) !== false ) {
			$args = [
				'post_type'      => 'docs',
				'posts_per_page' => -1,
				'post_status'    => ['publish', 'private'],
				'tax_query'      => [
					[
						'taxonomy' => 'doc_tag',
						'field'    => 'name',
						'terms'    => $keyword,
					],
				],
			];

			$postsByTags 	= new WP_Query($args);
			$merged_posts 	= array_merge($posts->posts, $postsByTags->posts);
			$merged_posts 	= array_unique($merged_posts, SORT_REGULAR);

			$posts = new WP_Query([
				'post_type'      => 'docs',
				'posts_per_page' => -1,
				'post__in'       => wp_list_pluck($merged_posts, 'ID'),
				'orderby'        => 'post__in', // Maintain order
			]);
		}
		
		// store search keyword data in wp_eazydocs_search_keywords table wordpress
		$keyword = $_POST['keyword'] ?? '';
		$keyword = sanitize_text_field( $keyword );
		$keyword = trim( $keyword );
		$keyword = strtolower( $keyword );

		if ( $posts->have_posts() ):
			// save $keyword in wp_eazydocs_search_keywords table
			global $wpdb;

			$wp_eazydocs_search_keyword = $wpdb->prefix . 'eazydocs_search_keyword';

			// Suppress direct query warning for the insert operation
			// @codingStandardsIgnoreLine WordPress.DB.DirectDatabaseQuery.DirectQuery
			$wpdb->insert(
				$wp_eazydocs_search_keyword,
				array(
					'keyword' => $keyword,
				)
			);

			// Save eazydocs_search_keyword id in wp_eazydocs_search_log table keyword_id and store count, created_at
			$wp_eazydocs_search_log = $wpdb->prefix . 'eazydocs_search_log';

			// Suppress direct query warning for the insert operation
			// @codingStandardsIgnoreLine WordPress.DB.DirectDatabaseQuery.DirectQuery
			$wpdb->insert(
				$wp_eazydocs_search_log,
				array(
					'keyword_id' => $wpdb->insert_id,
					'count'      => 1,
					'created_at' => current_time( 'mysql' ),
				)
			);

			while ( $posts->have_posts() ) : $posts->the_post();
				$no_thumbnail 		= ezd_get_opt('is_search_result_thumbnail') == false ? 'no-thumbnail' :  '';
				?>
                <div class="search-result-item <?php echo esc_attr( $no_thumbnail ); ?>" onclick="document.location='<?php echo get_the_permalink( get_the_ID() ); ?>'">
                    <a href="<?php echo esc_url(get_the_permalink( get_the_ID() )) ?>" class="title">
						
						<?php						
						if ( ezd_get_opt('is_search_result_thumbnail') ) :
							if ( has_post_thumbnail() ) :
								the_post_thumbnail( 'ezd_searrch_thumb16x16' );
								else :
								?>
								<svg width="16px" aria-labelledby="title" viewBox="0 0 17 17" fill="currentColor" class="block h-full w-auto" role="img">
									<title id="title">Building Search UI</title>
									<path d="M14.72,0H2.28A2.28,2.28,0,0,0,0,2.28V14.72A2.28,2.28,0,0,0,2.28,17H14.72A2.28,2.28,0,0,0,17,14.72V2.28A2.28,2.28,0,0,0,14.72,0ZM2.28,1H14.72A1.28,1.28,0,0,1,16,2.28V5.33H1V2.28A1.28,1.28,0,0,1,2.28,1ZM1,14.72V6.33H5.33V16H2.28A1.28,1.28,0,0,1,1,14.72ZM14.72,16H6.33V6.33H16v8.39A1.28,1.28,0,0,1,14.72,16Z"></path>
								</svg>
								<?php 
							endif;
						endif;						
						?>

                        <span class="doc-section">
                            <?php the_title(); ?>
                        </span>

                        <svg viewBox="0 0 24 24" fill="none" color="white" stroke="white" width="16px" stroke-width="2" stroke-linecap="round"
                             stroke-linejoin="round" class="block h-auto w-16">
                            <polyline points="9 10 4 15 9 20"></polyline>
                            <path d="M20 4v7a4 4 0 0 1-4 4H4"></path>
                        </svg>

                    </a>
					<?php 
					if ( ezd_get_opt('is_search_result_breadcrumb') ) {
						eazydocs_search_breadcrumbs(); 
					}
					?>
                </div>
			    <?php
			endwhile;
			wp_reset_postdata();
		else:
			// save eazydocs_search_keyword id in wp_eazydocs_search_log table keyword_id and store count, created_at
			global $wpdb;
			$wp_eazydocs_search_keyword = $wpdb->prefix . 'eazydocs_search_keyword';

			// Suppress direct query warning for the insert operation
			// @codingStandardsIgnoreLine WordPress.DB.DirectDatabaseQuery.DirectQuery
			$wpdb->insert(
				$wp_eazydocs_search_keyword,
				array(
					'keyword' => $keyword,
				)
			);

			// Save eazydocs_search_keyword id in wp_eazydocs_search_log table keyword_id and store count, created_at
			$wp_eazydocs_search_log = $wpdb->prefix . 'eazydocs_search_log';

			// Suppress direct query warning for the insert operation
			// @codingStandardsIgnoreLine WordPress.DB.DirectDatabaseQuery.DirectQuery
			$wpdb->insert(
				$wp_eazydocs_search_log,
				array(
					'keyword_id'      => $wpdb->insert_id,
					'count'           => 0,
					'not_found_count' => 1,
					'created_at'      => current_time( 'mysql' ),
				)
			);

			?>
            <div>
                <h5 class="error title"> <?php esc_html_e( 'No result found!', 'eazydocs' ); ?> </h5>
            </div>
		<?php
		endif;
		die();
	}

	/**
	 * Doc single page
	 */
	function docs_single_content() {
		$postid    = intval( $_POST['postid'] );
		$the_query = new WP_Query( array( 'post_type' => 'docs', 'p' => $postid ) );

		while ( $the_query->have_posts() ) : $the_query->the_post();
			eazydocs_get_template_part( 'single-doc-content' );
		endwhile;
		wp_reset_postdata();
		wp_die(); // this is required to terminate immediately and return a proper response
	}

}