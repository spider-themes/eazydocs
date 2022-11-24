<?php
namespace eazyDocs;

/**
 * Class Single_Duplicate
 * @package eazyDocsPro\Duplicator
 */
class Edit_OnePage {
	public function __construct() {
		add_action( 'admin_init', [ $this, 'edit_doc_one_page' ] );
	}

	function edit_doc_one_page() {

		if ( ! empty ( $_GET['edit_docs'] ) ) { 

			$page_id 				= $_GET['doc_id'] ?? '';
			$layout 				= $_GET['layout'] ?? '';
			$content_type 			= $_GET['content_type'] ?? '';
			
			$content_type_right 	= $_GET['shortcode_right'] ?? '';
			$page_content_rights 	= '';
			$page_content_right 	= '';
			
			if( $content_type_right == 'widget_data_right'){
				$shortcode_content_right   = $_GET['right_side_sidebar'] ?? '';
			} elseif( $content_type_right == 'shortcode_right'){
				$shortcode_content_right = 'doc_sidebar';
			} else {			
				$page_content_rights  	 = $_GET['shortcode_content_right'] ?? '';
				$page_content_right  	 = substr( chrEncode( $page_content_rights ), 1 );
				$shortcode_content_right = substr_replace( $page_content_right, "", -1);
				$shortcode_content_right = str_replace('style@',"style=", $shortcode_content_right);
				$shortcode_content_right = str_replace(';hash;',"#", $shortcode_content_right);
			}
			
			$page_contents 		= '';
			$page_content  		= '';

			if ( $content_type 	== 'widget_data' ) {
				$page_content 	= $_GET['left_side_sidebar'] ?? '';;
			} else {
				$page_contents 	= $_GET['edit_content'] ?? '';
				$page_content  	= substr( chrEncode( $page_contents ), 1 );
				$page_content  	= substr_replace( $page_content, "", - 1 );
				$page_content   = str_replace('style@',"style=", $page_content);
				$page_content   = str_replace(';hash;',"#", $page_content);
			}
			
			update_post_meta( $page_id, 'ezd_doc_layout', $layout );
			update_post_meta( $page_id, 'ezd_doc_content_type', $content_type );
			update_post_meta( $page_id, 'ezd_doc_left_sidebar', $page_content );
			update_post_meta( $page_id, 'ezd_doc_content_type_right', $content_type_right );
			update_post_meta( $page_id, 'ezd_doc_content_box_right', $shortcode_content_right );
			wp_safe_redirect( admin_url( 'edit.php?post_type=onepage-docs' ) );
		}
	}
}