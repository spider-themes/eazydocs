<?php
/**
 * EazyDocs Offer Notices - HTML Notice Widget Display
 *
 * Handles the display of fetched HTML content as admin notices
 * with dismiss functionality
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

if ( ! function_exists( 'ezd_display_html_notice' ) ) {
    add_action( 'admin_notices', 'ezd_display_html_notice' );

    function ezd_display_html_notice() {
        $product = 'eazydocs';

        // Check if notices are globally disabled
        $is_enabled = ezd_get_html_notice_switcher( $product );
        if ( ! $is_enabled ) {
            return;
        }

        // Get all non-dismissed contents
        $contents = ezd_get_non_dismissed_contents( $product );

        if ( empty( $contents ) ) {
            return;
        }

        $fetched_time = ezd_get_html_notice_fetched_time( $product );

        // Display each content as individual notice
        foreach ( $contents as $content ) {
            if ( ! isset( $content['id'] ) || ! isset( $content['content'] ) ) {
                continue;
            }

            $content_id    = sanitize_key( $content['id'] );
            $content_title = isset( $content['title'] ) ? $content['title'] : 'Notice';
            $html_content  = $content['content'];
            $nonce         = wp_create_nonce( 'ezd_dismiss_content_' . $content_id );

            ?>
            <div id="ezd-html-notice-<?php echo esc_attr( $content_id ); ?>" class="notice notice-info ezd-html-notice-wrapper"
                 data-product="<?php echo esc_attr( $product ); ?>" data-content-id="<?php echo esc_attr( $content_id ); ?>"
                 data-nonce="<?php echo esc_attr( $nonce ); ?>">
                <div class="ezd-html-notice-content">
                    <?php echo $html_content; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
                </div>
                <button type="button" class="notice-dismiss"><span class="screen-reader-text">Dismiss this notice.</span></button>
            </div>

            <style>
                #ezd-html-notice-<?php echo esc_attr( $content_id ); ?> {
                    margin: 20px 0 !important;
                    padding: 0 !important;
                    background: transparent !important;
                    border: 0 !important;
                    position: relative;
                    box-shadow: 0;
                }

                #ezd-html-notice-<?php echo esc_attr( $content_id ); ?> .notice-dismiss {
                    position: absolute !important;
                    top: 0 !important;
                    right: 1px !important;
                    border: none !important;
                    margin: 0 !important;
                    padding: 9px !important;
                    background: none !important;
                    color: #787c82 !important;
                    cursor: pointer !important;
                }

                #ezd-html-notice-<?php echo esc_attr( $content_id ); ?> .notice-dismiss:hover {
                    color: #c92c2c !important;
                }
            </style>

            <script>
                (function () {
                    var notice = document.getElementById('ezd-html-notice-<?php echo esc_js( $content_id ); ?>');
                    if (!notice) return;

                    var closeBtn = notice.querySelector('.notice-dismiss');
                    if (closeBtn) {
                        closeBtn.addEventListener('click', function (e) {
                            e.preventDefault();
                            var product = notice.getAttribute('data-product');
                            var contentId = notice.getAttribute('data-content-id');
                            var nonce = notice.getAttribute('data-nonce');

                            var xhr = new XMLHttpRequest();
                            xhr.open('POST', '<?php echo esc_js( admin_url( 'admin-ajax.php' ) ); ?>', true);
                            xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded; charset=UTF-8');
                            xhr.onload = function () {
                                if (xhr.status === 200) {
                                    notice.style.display = 'none';
                                }
                            };

                            var data = 'action=ezd_dismiss_html_content&product=' + encodeURIComponent(product) + '&content_id=' + encodeURIComponent(contentId) + '&nonce=' + encodeURIComponent(nonce);
                            xhr.send(data);

                        });
                    }
                })();
            </script>
            <?php
        }
    }
}

if ( ! function_exists( 'ezd_handle_dismiss_html_notice' ) ) {
    add_action( 'wp_ajax_ezd_dismiss_html_notice', 'ezd_handle_dismiss_html_notice' );

    function ezd_handle_dismiss_html_notice() {
        if ( ! isset( $_POST['nonce'] ) || ! wp_verify_nonce( $_POST['nonce'], 'ezd_dismiss_notice' ) ) {
            wp_send_json_error( array( 'message' => 'Nonce verification failed' ) );
        }

        if ( ! current_user_can( 'manage_options' ) ) {
            wp_send_json_error( array( 'message' => 'Unauthorized' ) );
        }

        $product = isset( $_POST['product'] ) ? sanitize_text_field( wp_unslash( $_POST['product'] ) ) : '';

        if ( empty( $product ) ) {
            wp_send_json_error( array( 'message' => 'Invalid product' ) );
        }

        $dismiss_key = sanitize_key( $product ) . '_offer_dismissed_time';
        update_option( $dismiss_key, current_time( 'timestamp' ) );

        wp_send_json_success( array( 'message' => 'Notice dismissed for 1 week' ) );
    }
}

if ( ! function_exists( 'ezd_handle_dismiss_html_content' ) ) {
    add_action( 'wp_ajax_ezd_dismiss_html_content', 'ezd_handle_dismiss_html_content' );

    function ezd_handle_dismiss_html_content() {
        $content_id = isset( $_POST['content_id'] ) ? sanitize_text_field( wp_unslash( $_POST['content_id'] ) ) : '';
        $product    = isset( $_POST['product'] ) ? sanitize_text_field( wp_unslash( $_POST['product'] ) ) : '';
        $nonce      = isset( $_POST['nonce'] ) ? sanitize_text_field( wp_unslash( $_POST['nonce'] ) ) : '';

        if ( ! wp_verify_nonce( $nonce, 'ezd_dismiss_content_' . $content_id ) ) {
            wp_send_json_error( array( 'message' => 'Nonce verification failed' ) );
        }

        if ( ! current_user_can( 'manage_options' ) ) {
            wp_send_json_error( array( 'message' => 'Unauthorized' ) );
        }

        if ( empty( $product ) || empty( $content_id ) ) {
            wp_send_json_error( array( 'message' => 'Invalid parameters' ) );
        }

        // Dismiss this specific content permanently
        if ( function_exists( 'ezd_dismiss_content' ) ) {
            ezd_dismiss_content( $product, $content_id );
        }

        wp_send_json_success( array( 'message' => 'Content dismissed permanently' ) );
    }
}


if ( ! function_exists( 'ezd_reset_all_notice_dismisses' ) ) {
    function ezd_reset_all_notice_dismisses() {
        $products = array( 'eazydocs' );

        foreach ( $products as $product ) {
            $dismiss_key = sanitize_key( $product ) . '_offer_dismissed_time';
            delete_option( $dismiss_key );
        }
    }
}

