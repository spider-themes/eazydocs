<?php
namespace eazyDocs;

/**
 * Class Single_Duplicate
 * @package eazyDocsPro\Duplicator
 */
class One_Page_Edit {
	public function __construct() {
		add_action( 'admin_init', [ $this, 'edit_doc_one_page' ] );
	}

	function edit_doc_one_page() {

		if ( isset ( $_GET['edit_content'] ) ) {
			if ( isset ( $_GET['edit_docs'] ) ) {
				$redirect = 'edit.php?post_type=onepage-docs';
			} else {
				$redirect = 'admin.php?page=eazydocs';
			}

			$page_id      = $_GET['doc_id'] ?? '';

			$layout = $_GET['layout'] ?? '';
			$content_type = $_GET['content_type'] ?? '';

			$page_contents =  esc_textarea($_GET['edit_content'])  ?? '';
			$page_content = substr( chrEncode( $page_contents), 6 );
			echo $page_content = substr_replace($page_content, "", -6); //Str1, Str2, str3

			$edit_data = array(
				'ID'           => $page_id,
				'post_content' => $page_content
			);
			wp_update_post( $edit_data );
			update_post_meta($page_id, 'ezd_doc_layout', $layout);
			update_post_meta($page_id, 'ezd_doc_content_type', $content_type);
			wp_safe_redirect( admin_url( $redirect ) );
		}

	}

}