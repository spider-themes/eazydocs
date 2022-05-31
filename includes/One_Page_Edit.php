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
				$redirect = 'edit.php?post_type=one-page-docs';
			} else {
				$redirect = 'admin.php?page=eazydocs';
			}

			$page_id      = $_GET['doc_id'] ?? '';
			$page_content = $_GET['edit_content'] ?? '';

			$edit_data = array(
				'ID'           => $page_id,
				'post_content' => $page_content
			);
			wp_update_post( $edit_data );
			wp_safe_redirect( admin_url( $redirect ) );
		}
	}
}