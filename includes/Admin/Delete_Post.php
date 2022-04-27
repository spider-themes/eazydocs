<?php
namespace eazyDocs\Admin;

/**
 * Class Delete_Post
 * @package eazyDocs\Admin
 */
class Delete_Post {

	/**
	 * Create_Post constructor.
	 */
	public function __construct() {
		add_action( 'admin_init', [ $this, 'delete_doc' ] );
	}

	/**
	 * Delete Parent Doc
	 */
	public function delete_doc() {

		if ( ! empty ( $_GET['DeleteID'] ) ) {
			$posts                  = sanitize_text_field( $_GET['DeleteID'] );
			$parent_id              = $posts . ',';

			/**
			 * Section Docs
			 **/
			$parent                 = get_children( [
				'post_parent'       => $posts
			] );
			$sec_ids                = '';
			$child_sec_ids          = '';
			$child_ids              = '';
			foreach ( $parent as $section ) {
				$sec_ids            .= $section->ID . ',';
				$sec_child           = get_children( [
					'post_parent' => $section->ID
				] );
				foreach ( $sec_child as $child_sec ) {
					$child_sec_ids    .= $child_sec->ID . ',';

					$child = get_children( [
						'post_parent'  => $child_sec->ID
					] );
					foreach ( $child as $childs ) {
						$child_ids     .= $childs->ID . ',';
					}
				}
			}

			$delete_ids = $parent_id . $sec_ids . $child_sec_ids . $child_ids;
			$doc_ids                  = explode( ',', $delete_ids );
			$doc_ids_int              = array_map( 'intval', $doc_ids );
			foreach ( $doc_ids_int as $deletes ) {
				wp_delete_post( $deletes, true );
			}
			header( "Location:" . admin_url( 'admin.php?page=eazydocs' ) );

		} elseif ( ! empty ( $_GET['ID'] ) ) {
			$id                       = sanitize_text_field( $_GET['ID'] );
			$doc_ids                  = explode( ',', $id );
			$doc_ids_int              = array_map( 'intval', $doc_ids );

			foreach ( $doc_ids_int as $item ) {
				wp_delete_post( $item, true );
			}
			header( "Location:" . admin_url( 'admin.php?page=eazydocs' ) );
		}
	}
}