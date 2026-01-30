<?php
namespace EazyDocs;

/**
 * Cannot access directly.
 */
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

/**
 * Class One_Page
 * Creates a single "onepage" doc from a parent docs post
 */
class One_Page {
    public function __construct() {
        add_action( 'admin_init', [ $this, 'doc_one_page' ] );
    }

    /**
     * Helper: clean up encoded characters
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

    /**
     * Handle admin init action to create a onepage doc
     */
    public function doc_one_page() {
        // Only run when explicitly requested
        if ( empty( $_GET['make_onepage'] ) || wp_unslash( $_GET['make_onepage'] ) !== 'yes' ) {
            return;
        }

        // Verify nonce
        if ( empty( $_GET['_wpnonce'] ) || ! wp_verify_nonce( wp_unslash( $_GET['_wpnonce'] ), 'ezd_make_onepage' ) ) {
            wp_die( esc_html__( 'Security check failed. Please try again.', 'eazydocs' ) );
        }

        // Capability check
        if ( ! current_user_can( 'edit_docs' ) ) {
            wp_die( esc_html__( 'You do not have permission to perform this action.', 'eazydocs' ) );
        }

        // Sanitize basic inputs
        $parent_id = isset( $_GET['parentID'] ) ? absint( wp_unslash( $_GET['parentID'] ) ) : 0;
        if ( $parent_id <= 0 ) {
            wp_die( esc_html__( 'Invalid parent ID.', 'eazydocs' ) );
        }

        $layout                = isset( $_GET['layout'] ) ? sanitize_text_field( $_GET['layout'] ) : '';
        $ezd_doc_content_type  = isset( $_GET['content_type'] ) ? sanitize_text_field( $_GET['content_type'] ) : '';
        $content_type_right    = isset( $_GET['shortcode_right'] ) ? sanitize_text_field( $_GET['shortcode_right'] ) : '';

        // --- HTML content fields: no sanitize_text_field, keep full HTML/shortcodes ---
        $left_side_sidebar       = isset( $_GET['left_side_sidebar'] ) ? $this->ezd_chrEncode( $_GET['left_side_sidebar'] ) : '';
        $page_contents_raw       = isset( $_GET['shortcode_content'] ) ? $this->ezd_chrEncode( $_GET['shortcode_content'] ) : '';
        $page_contents_right_raw = isset( $_GET['shortcode_content_right'] ) ? $this->ezd_chrEncode( $_GET['shortcode_content_right'] ) : '';

        // --- Process right-side content ---
        if ( 'widget_data_right' === $content_type_right ) {
            $shortcode_content_right = $left_side_sidebar;
        } else {
            $clean_right = $page_contents_right_raw;
            if ( strlen( $clean_right ) > 12 ) {
                $clean_right = substr( $clean_right, 6, -6 );
            }
            $clean_right = str_replace( [ 'style@', ';hash;', 'style&equals;' ], [ 'style=', '#', 'style' ], $clean_right );
            $shortcode_content_right = $clean_right; // raw HTML/shortcodes
        }

        // --- Process left-side content ---
        if ( 'widget_data' === $ezd_doc_content_type ) {
            $shortcode_content = $left_side_sidebar;
        } else {
            $clean_left = $page_contents_raw;
            if ( strlen( $clean_left ) > 12 ) {
                $clean_left = substr( $clean_left, 6, -6 );
            }
            $clean_left = str_replace( [ 'style@', ';hash;', 'style&equals;' ], [ 'style=', '#', 'style' ], $clean_left );
            $shortcode_content = $clean_left; // raw HTML/shortcodes
        }

        // Titles and slugs
        $page_title = get_the_title( $parent_id );
        $post_slug  = get_post_field( 'post_name', $parent_id ) ?: sanitize_title( $page_title );

        // Decide redirect target
        $redirect = empty( $_GET['self_doc'] ) ? 'admin.php?page=eazydocs-builder' : 'edit.php?post_type=onepage-docs';

        // Create post array
        $one_page_doc = [
            'post_title'  => wp_strip_all_tags( $page_title ),
            'post_status' => 'publish',
            'post_author' => get_current_user_id() ?: 1,
            'post_type'   => 'onepage-docs',
            'post_name'   => $post_slug,
        ];

        // Insert post
        $post_id = wp_insert_post( $one_page_doc );
        if ( is_wp_error( $post_id ) || $post_id === 0 ) {
            wp_die( esc_html__( 'Failed to create OnePage document.', 'eazydocs' ) );
        }

        // Ensure correct post type
        if ( 'onepage-docs' !== get_post_type( $post_id ) ) {
            return;
        }

        // Save metadata with full HTML/shortcodes
        if ( $layout ) {
            update_post_meta( $post_id, 'ezd_doc_layout', $layout );
        }
        if ( $ezd_doc_content_type ) {
            update_post_meta( $post_id, 'ezd_doc_content_type', $ezd_doc_content_type );
        }
        if ( $shortcode_content ) {
            // Sentinel: Sanitize content to prevent Stored XSS
            update_post_meta( $post_id, 'ezd_doc_left_sidebar', wp_kses_post( $shortcode_content ) );
        }
        if ( $content_type_right ) {
            update_post_meta( $post_id, 'ezd_doc_content_type_right', $content_type_right );
        }
        if ( $shortcode_content_right ) {
            // Sentinel: Sanitize content to prevent Stored XSS
            update_post_meta( $post_id, 'ezd_doc_content_box_right', wp_kses_post( $shortcode_content_right ) );
        }

        // Store relation to original parent
        update_post_meta( $post_id, 'ezd_onepage_parent_id', $parent_id );

        // Redirect back to admin page
        wp_safe_redirect( admin_url( $redirect ) );
        exit;
    }
}
