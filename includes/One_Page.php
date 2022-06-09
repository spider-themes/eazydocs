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

		/**
		 *  Current permalink structure
		 */
		$current_permalink = get_option( 'permalink_structure' );

		if ( ! empty ( $_GET['single_doc_title'] ) ) {

			$post =  get_page_by_title( $_GET['single_doc_title'], OBJECT, 'docs' );

			if ( isset ( $_GET['self_doc'] ) ) {
				$redirect = 'edit.php?post_type=onepage-docs';
			} else {
				$redirect = 'admin.php?page=eazydocs';
			}

			$page_title   = sanitize_text_field( $_GET['single_doc_title'] ) ?? '';
			$page_content = sanitize_text_field( $_GET['content'] ) ?? '';

			if ( ! get_page_by_title( $page_title, OBJECT, 'onepage-docs' ) ) {
				// Create page object
				$one_page_doc = array(
					'post_title'   => wp_strip_all_tags( $page_title ),
					'post_content' =>  $page_content,
					'post_status'  => 'publish',
					'post_author'  => 1,
					'post_type'    => 'onepage-docs',
					'post_name'    => $post->post_name
				);
				wp_insert_post( $one_page_doc );

				global $wp_rewrite;
				$wp_rewrite->set_permalink_structure($current_permalink);
				$wp_rewrite->flush_rules();

			}
			wp_safe_redirect( admin_url( $redirect ) );
		}
	}
}

