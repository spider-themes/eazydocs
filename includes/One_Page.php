<?php
namespace EazyDocs;

/**
 * Cannot access directly.
 */
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

/**
 * Class Single_Duplicate
 * @package EZD_EazyDocsPro\Duplicator
 */
class One_Page {
	public function __construct() {
		add_action( 'admin_init', [ $this, 'doc_one_page' ] );
	}

	function doc_one_page() {
		
		if ( isset( $_GET['make_onepage'] ) && $_GET['make_onepage'] == 'yes' && isset( $_GET['parentID'] ) && !empty( $_GET['parentID'] ) ) {
			
			$layout               	= sanitize_text_field( $_GET['layout'] ?? '' );
			$ezd_doc_content_type 	= sanitize_text_field( $_GET['content_type'] ?? '' );
			$left_side_sidebar    	= sanitize_text_field( $_GET['left_side_sidebar'] ?? '' );
			$content_type         	= sanitize_text_field( $_GET['shortcode_right'] ?? '' );
			$page_contents_right 	= esc_textarea( $_GET['shortcode_content_right'] ?? '' );
			 
			if ( $content_type == 'widget_data_right' ) {
				$shortcode_content_right = sanitize_text_field( $_GET['right_side_sidebar'] ?? '' );
			} else {
				$page_content_right      = substr( ezd_chrEncode( $page_contents_right ), 6 );
				$shortcode_content_right = substr_replace( $page_content_right, "", - 6 );
				$shortcode_content_right = str_replace( 'style@',"style=", $shortcode_content_right );
				$shortcode_content_right = str_replace( ';hash;',"#", $shortcode_content_right );
				$shortcode_content_right = str_replace( 'style&equals;',"style", $shortcode_content_right );
			}

			$page_title 				 = get_the_title( $_GET['parentID'] ?? '' );		 
			$page_contents 				 = esc_textarea( $_GET['shortcode_content'] ?? '' );

			if ( $ezd_doc_content_type == 'widget_data' ) {
				$shortcode_content 	= $left_side_sidebar;
			} else {
				$page_content      	= substr( ezd_chrEncode( $page_contents ), 6 );
				$shortcode_content 	= substr_replace( $page_content, "", - 6 );
				$shortcode_content 	= str_replace( 'style@',"style=", $shortcode_content );
				$shortcode_content 	= str_replace( ';hash;',"#", $shortcode_content );
				$shortcode_content 	= str_replace( 'style&equals;',"style", $shortcode_content );
			}

			/**
			 *  Current permalink structure
			 */
			$current_permalink 	= get_option( 'permalink_structure' );
			$is_parent_id 		= sanitize_text_field( $_GET['parentID'] ?? '' );

			if ( ! empty ( $is_parent_id ) ) {
				$post_slug 	= get_post_field( 'post_name', $is_parent_id);
			} else {
				$post 		= ezd_get_page_by_title( $page_title, 'docs' );
				$post_slug 	= $post[0]->post_name ?? '';
			}

			if ( empty ( $_GET['self_doc'] ) ) {
				$redirect 	= 'admin.php?page=eazydocs';
			} else {
				$redirect 	= 'edit.php?post_type=onepage-docs';
			}
			
			$one_page_doc = array(
				'post_title'   => wp_strip_all_tags( $page_title ),
				'post_status'  => 'publish',
				'post_author'  => 1,
				'post_type'    => 'onepage-docs',
				'post_name'    => $post_slug
			);

			$post_id      = wp_insert_post( $one_page_doc, $wp_error = '' );

			if ( $post_id != 0 ) {

				if ( 'onepage-docs' != get_post_type( $post_id ) ) {
					return;
				}
				
				if ( ! empty( $layout ) ) {
					update_post_meta( $post_id, 'ezd_doc_layout', $layout );
				}

				if ( ! empty( $ezd_doc_content_type ) ) {
					update_post_meta( $post_id, 'ezd_doc_content_type', $ezd_doc_content_type );
				}
				
				if ( ! empty( $shortcode_content ) ) {
					update_post_meta( $post_id, 'ezd_doc_left_sidebar', $shortcode_content );
				}
				
				if ( ! empty( $content_type ) ) {
					update_post_meta( $post_id, 'ezd_doc_content_type_right', $content_type );
				}

				if ( ! empty( $shortcode_content_right ) ) {
					update_post_meta( $post_id, 'ezd_doc_content_box_right', $shortcode_content_right );
				}
				
			}

			global $wp_rewrite;
			$wp_rewrite->set_permalink_structure( $current_permalink );
			$wp_rewrite->flush_rules();
			
			wp_safe_redirect( admin_url( $redirect ) );
		}
	}
}