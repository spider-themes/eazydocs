<?php
namespace eazyDocs\Frontend;

class Ajax {
    public function __construct() {
        // feedback
        add_action( 'wp_ajax_eazydocs_handle_feedback', [ $this, 'handle_feedback' ] );
        add_action( 'wp_ajax_nopriv_eazydocs_handle_feedback', [ $this, 'handle_feedback' ] );
    }

    /**
     * Store feedback for an article.
     * @return void
     */
    public function handle_feedback() {
        check_ajax_referer( 'eazydocs-ajax' );

        $template = '<div class="eazydocs-alert alert-%s">%s</div>';
        $previous = isset( $_COOKIE['eazydocs_response'] ) ? explode( ',', $_COOKIE['eazydocs_response'] ) : [];
        $post_id  = intval( $_POST['post_id'] );
        $type     = in_array( $_POST['type'], [ 'positive', 'negative' ] ) ? $_POST['type'] : false;

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

        $message = sprintf( $template, 'success', __( 'Thanks for your feedback!', 'eazydocs' ) );
        wp_send_json_success($message);
    }
}