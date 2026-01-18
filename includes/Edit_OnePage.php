<?php
namespace EazyDocs;

/**
 * Cannot access directly.
 */
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

/**
 * Class Edit_OnePage
 *
 * @package EZD_EazyDocsPro\Duplicator
 */
class Edit_OnePage {
    public function __construct() {
        add_action( 'admin_init', [ $this, 'edit_doc_one_page' ] );
    }

    /**
     * Helper to clean special characters
     */
    private function ezd_chrEncode( $data ) {
        if ( ! is_string( $data ) ) {
            return $data;
        }

        $replacements = [
            'â€™'   => '&#39;',
            'Ã©'    => 'é',
            'â€'    => '-',
            '-œ'    => '&#34;',
            'â€œ'   => '&#34;',
            'Ãª'    => 'ê',
            'Ã¶'    => 'ö',
            'â€¦'   => '...',
            '-¦'    => '...',
            'â€“'   => '–',
            'â€²s'  => '’',
            '-²s'   => '’',
            'â€˜'   => '&#39;',
            '-˜'    => '&#39;',
            '-“'    => '-',
            'Ã¨'    => 'è',
            'ï¼ˆ'  => '(',
            'ï¼‰'  => ')',
            'â€¢'   => '&bull;',
            '-¢'    => '&bull;',
            'Â§ï‚§' => '&bull;',
            'Â®'    => '&reg;',
            'â„¢'   => '&trade;',
            'Ã±'    => 'ñ',
            'Å‘s'   => 'ő',
            '\\"'   => '&quot;',
            "\r"    => '',
            "\\r"   => '',
            "\n"    => '',
            "\\n"   => '',
            "\\'"   => '',
            "\\"    => '',
        ];

        return strtr( $data, $replacements );
    }

    public function edit_doc_one_page() {

        if (
            ! empty($_GET['edit_docs']) &&
            ! empty($_GET['edit_onepage']) &&
            $_GET['edit_onepage'] === 'yes' &&
            ! empty($_GET['doc_id']) &&
            ! empty($_GET['_wpnonce']) &&
            wp_verify_nonce( wp_unslash($_GET['_wpnonce']), absint($_GET['doc_id']) )
        ) {
            $page_id = absint( $_GET['doc_id'] );

            // Security: Check if user can edit this specific post
            if ( ! current_user_can( 'edit_post', $page_id ) ) {
                return;
            }

            $layout             = sanitize_text_field( $_GET['layout'] ?? '' );
            $content_type       = sanitize_text_field( $_GET['content_type'] ?? '' );
            $content_type_right = sanitize_text_field( $_GET['shortcode_right'] ?? '' );

            // --- Right-side content ---
            if ( $content_type_right === 'widget_data_right' ) {
                $shortcode_content_right = $_GET['right_side_sidebar'] ?? '';
            } elseif ( $content_type_right === 'shortcode_right' ) {
                $shortcode_content_right = 'doc_sidebar';
            } else {
                $page_content_rights     = $_GET['shortcode_content_right'] ?? '';
                $page_content_right      = substr( $this->ezd_chrEncode( $page_content_rights ), 6 );
                $shortcode_content_right = substr_replace( $page_content_right, "", -6 );
                $shortcode_content_right = str_replace( ['style@',';hash;'], ['style=','#'], $shortcode_content_right );
            }

            // --- Left-side content ---
            if ( $content_type === 'widget_data' ) {
                $page_content = $_GET['left_side_sidebar'] ?? '';
            } else {
                $page_contents = $_GET['edit_content'] ?? '';
                $page_content  = substr( $this->ezd_chrEncode( $page_contents ), 6 );
                $page_content  = substr_replace( $page_content, "", -6 );
                $page_content  = str_replace( ['style@',';hash;'], ['style=','#'], $page_content );
            }

            // Only edit onepage-docs type
            if ( 'onepage-docs' !== get_post_type( $page_id ) ) {
                return;
            }

            // Security: Sanitize HTML content before saving to prevent Stored XSS
            if ( ! empty( $layout ) ) {
                update_post_meta( $page_id, 'ezd_doc_layout', $layout );
            }
            if ( ! empty( $content_type ) ) {
                update_post_meta( $page_id, 'ezd_doc_content_type', $content_type );
            }
            if ( ! empty( $page_content ) ) {
                update_post_meta( $page_id, 'ezd_doc_left_sidebar', wp_kses_post( $page_content ) );
            }
            if ( ! empty( $shortcode_content_right ) ) {
                update_post_meta( $page_id, 'ezd_doc_content_box_right', wp_kses_post( $shortcode_content_right ) );
            }
            if ( ! empty( $content_type_right ) ) {
                update_post_meta( $page_id, 'ezd_doc_content_type_right', $content_type_right );
            }

            wp_safe_redirect( admin_url( 'edit.php?post_type=onepage-docs' ) );
            exit;
        }
    }
}
