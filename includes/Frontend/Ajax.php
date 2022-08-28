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

	        $timestamp = current_time('mysql');

            update_post_meta( $post_id, $type, $count + 1);

            if( $type == 'positive') {
	            update_post_meta( $post_id, 'positive_time', $timestamp );
            }else{
	            update_post_meta( $post_id, 'negative_time', $timestamp );
            }

            array_push( $previous, $post_id );
            $cookie_val = implode( ',', $previous );

            $val = setcookie( 'eazydocs_response', $cookie_val, time() + WEEK_IN_SECONDS, COOKIEPATH, COOKIE_DOMAIN );
        }

        $message = sprintf( $template, 'success', esc_html__( 'Thanks for your feedback!', 'eazydocs' ) );
        wp_send_json_success($message);
    }

    /**
     * Ajax Search Results
     * @return void
     */
    function eazydocs_search_results() {
        $posts = new WP_Query( [
                'post_type'         => 'docs',
                's'                 => $_POST['keyword'] ?? ''
            ]
        );
      
        if ( $posts->have_posts() ): 
         
            while ( $posts->have_posts() ) : $posts->the_post();
            //echo $parent_title = get_the_title( wp_get_post_parent_id( get_the_ID() ) );
            docs_root_title();
                ?>
                <div class="search-result-item" onclick="document.location='<?php echo get_the_permalink(get_the_ID()); ?>'">
                    <?php eazydocs_search_breadcrumbs(); ?>
                    <a href="<?php echo get_the_permalink(get_the_ID()); ?>">
                        <?php
                        if ( has_post_thumbnail() ) :
                            the_post_thumbnail('ezd_searrch_thumb16x16');
                        else:
                            ?>
                            <svg width="16px" aria-labelledby="title" viewBox="0 0 17 17" fill="currentColor" class="block h-full w-auto" role="img"><title id="title">Building Search UI</title><path d="M14.72,0H2.28A2.28,2.28,0,0,0,0,2.28V14.72A2.28,2.28,0,0,0,2.28,17H14.72A2.28,2.28,0,0,0,17,14.72V2.28A2.28,2.28,0,0,0,14.72,0ZM2.28,1H14.72A1.28,1.28,0,0,1,16,2.28V5.33H1V2.28A1.28,1.28,0,0,1,2.28,1ZM1,14.72V6.33H5.33V16H2.28A1.28,1.28,0,0,1,1,14.72ZM14.72,16H6.33V6.33H16v8.39A1.28,1.28,0,0,1,14.72,16Z"></path></svg>
                        <?php endif; ?>
                        <span class="doc-section">
                            <?php the_title(); ?>
                        </span>
                        <svg viewBox="0 0 24 24" fill="none" color="white" stroke="white" width="16px" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="block h-auto w-16"><polyline points="9 10 4 15 9 20"></polyline><path d="M20 4v7a4 4 0 0 1-4 4H4"></path></svg>
                    </a>
                </div>
                <?php
            endwhile;
            wp_reset_postdata();
        else:
            ?>                        
            <div> 
                <h5 class="error title"> No result found! </h5> 
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