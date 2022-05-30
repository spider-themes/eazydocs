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

		if ( isset ( $_GET['single'] ) ) {
			$redirect = 'edit.php?post_type=one-page-docs';
		} else {
			$redirect = 'admin.php?page=eazydocs';
		}

		if ( ! empty ( $_GET['single_doc_title'] ) ) {

			$page_title = $_GET['single_doc_title'] ?? '';
			$page_content = $_GET['content'] ?? '';

			if ( ! get_page_by_title( $page_title, OBJECT, 'page' ) ) {
				// Create page object
				$one_page_doc = array(
					'post_title'   => wp_strip_all_tags( $page_title ),
					'post_content' => $page_content,
					'post_status'  => 'publish',
					'post_author'  => 1,
					'post_type'    => 'one-page-docs',
				);
				wp_insert_post( $one_page_doc );
			}
			wp_safe_redirect( admin_url( $redirect ) );
		}
	}
}