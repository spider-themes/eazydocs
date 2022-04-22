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
        add_action( 'wp_ajax_eazydocs_search_results', [ $this, 'search_data_fetch' ] );
        add_action( 'wp_ajax_nopriv_eazydocs_search_results', [ $this, 'search_data_fetch' ] );
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
            update_post_meta( $post_id, $type, $count + 1 );

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
    function search_data_fetch() {
        $the_query = new WP_Query([
            'posts_per_page' => 5,
            'post_type'      => 'docs',
            'post_parent'    => 0,
            'fields'         => 'ids',
        ]);
        $unique_parents          = array();
        $unique_sections         = array();

        if ( isset( $_GET['wpml_lang'] ) ) {
            do_action( 'wpml_switch_language', $_GET['wpml_lang'] );
        }

        if ( $the_query->have_posts() ) :
            $i = 1;
            $search_result_limit = ! empty( $opt['doc_result_limit'] ) ? $opt['doc_result_limit'] : 3;
            while ( $the_query->have_posts() ) : $the_query->the_post();
                $parent_title = get_the_title( wp_get_post_parent_id( get_the_ID() ) );
                $parent_id    = get_the_ID();
                $child_query  = new WP_Query( array( 'post_type' => 'docs', 'post_parent' => $parent_id, 'posts_per_page' => $search_result_limit ) );
                if ( $child_query->have_posts() ) :
                    while ( $child_query->have_posts() ) : $child_query->the_post();
                        $parent_of_parent_id  = get_the_ID();
                        $doc_sec_title        = get_the_title( $parent_of_parent_id );
                        $child_of_child_query = new WP_Query( array(
                            'post_type'      => 'docs',
                            's'              => $_POST['keyword'],
                            'post_parent'    => $parent_of_parent_id,
                            'posts_per_page' => -1,
                        ));
                        if ( $child_of_child_query->have_posts() ) :
                            while ( $child_of_child_query->have_posts() ) : $child_of_child_query->the_post();
                                ?>
                                <div class="search-result-item <?php echo ! in_array( $parent_title, $unique_parents ) ? 'parent-doc' : ''; ?>">
                                    <?php
                                    if ( ! in_array( $parent_title, $unique_parents ) ) :
                                        $unique_parents[] = $parent_title;
                                        ?>
                                        <div class="doc-item">
                                            <a href="<?php echo get_the_permalink( $the_query->post->post_parent ); ?>">
                                                <?php echo esc_html( $parent_title ); ?>
                                            </a>
                                        </div>
                                    <?php endif; ?>
                                    <div class="doc-list <?php echo ! in_array( $doc_sec_title, $unique_sections ) ? 'doc-sec-unique' : ''; ?>">
                                    <span class="doc-section">
                                        <?php
                                        if ( ! in_array( $doc_sec_title, $unique_sections ) ) :
                                            $unique_sections[] = $doc_sec_title;
                                            ?>
                                            <a href="<?php the_permalink(); ?>">
                                                <?php echo esc_html( $doc_sec_title ); ?>
                                            </a>
                                        <?php endif; ?>
                                    </span>
                                        <span class="doc-article">
                                        <a href="<?php the_permalink(); ?>"><?php echo get_the_title(); ?></a>
                                    </span>
                                    </div>
                                </div>
                                <?php
                            endwhile;
                        endif;
                    endwhile;
                    wp_reset_query();
                endif;
            endwhile;
        endif;
        wp_reset_query();
        die();
    }
}