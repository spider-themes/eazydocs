<?php
namespace eazyDocs;

/**
 * Class Single_Duplicate
 * @package eazyDocsPro\Duplicator
 */
class One_Page {
	public function __construct() {
		add_action( 'admin_init', [ $this, 'doc_one_page' ] );
	}

	function doc_one_page() {

		if ( ! empty ( $_GET['single_doc_title'] ) ) {
			
			$layout               = $_GET['layout'] ?? '';
			$ezd_doc_content_type = $_GET['content_type'] ?? '';
			$left_side_sidebar    = $_GET['left_side_sidebar'] ?? '';
			$content_type         = $_GET['shortcode_right'] ?? '';

			$page_contents_right = $_GET['shortcode_content_right'] ?? '';
			$page_contents_right = esc_textarea( $page_contents_right );

			if( $content_type == 'widget_data_right'){
				$shortcode_content_right   = $_GET['right_side_sidebar'] ?? '';
			}else{
				$page_content_right      = substr( chrEncode( $page_contents_right ), 6 );
				$shortcode_content_right = substr_replace( $page_content_right, "", - 6 );
				$shortcode_content_right = str_replace('style@',"style=", $shortcode_content_right);
				$shortcode_content_right = str_replace(';hash;',"#", $shortcode_content_right);
				$shortcode_content_right = str_replace('style&equals;',"style", $shortcode_content_right);
			}

			$page_title = $_GET['single_doc_title'] ?? '';
			$page_title = sanitize_text_field( $page_title );

			$page_contents = $_GET['shortcode_content'] ?? '';
			$page_contents = esc_textarea( $page_contents );

			if ( $ezd_doc_content_type == 'widget_data' ) {
				$shortcode_content = $left_side_sidebar;
			} else {
				$page_content      = substr( chrEncode( $page_contents ), 6 );
				$shortcode_content = substr_replace( $page_content, "", - 6 );
				$shortcode_content = str_replace('style@',"style=", $shortcode_content);
				$shortcode_content   = str_replace(';hash;',"#", $shortcode_content);
				$shortcode_content   = str_replace('style&equals;',"style", $shortcode_content);
			}

			/**
			 *  Current permalink structure
			 */
			$current_permalink = get_option( 'permalink_structure' );

			$post = get_page_by_title( $_GET['single_doc_title'], OBJECT, 'docs' );

			if ( empty ( $_GET['self_doc'] ) ) {
				$redirect = 'admin.php?page=eazydocs';
			} else {
				$redirect = 'edit.php?post_type=onepage-docs';
			}

			if ( ! get_page_by_title( $page_title, OBJECT, 'onepage-docs' ) ) {
				// Create page object
				$one_page_doc = array(
					'post_title'   => wp_strip_all_tags( $page_title ),
					'post_status'  => 'publish',
					'post_author'  => 1,
					'post_type'    => 'onepage-docs',
					'post_name'    => $post->post_name
				);
				$post_id      = wp_insert_post( $one_page_doc, $wp_error = '' );
				if ( $post_id != 0 ) {
					update_post_meta( $post_id, 'ezd_doc_layout', $layout );
					update_post_meta( $post_id, 'ezd_doc_content_type', $ezd_doc_content_type );
					update_post_meta( $post_id, 'ezd_doc_left_sidebar', $shortcode_content );

					update_post_meta( $post_id, 'ezd_doc_content_type_right', $content_type );
					update_post_meta( $post_id, 'ezd_doc_content_box_right', $shortcode_content_right );
				}
				global $wp_rewrite;
				$wp_rewrite->set_permalink_structure( $current_permalink );
				$wp_rewrite->flush_rules();
			}
			wp_safe_redirect( admin_url( $redirect ) );
		}
	}
}