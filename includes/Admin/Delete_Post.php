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
				wp_trash_post( $deletes, true );
			}
			header( "Location:" . admin_url( 'admin.php?page=eazydocs' ) );

		} elseif ( ! empty ( $_GET['ID'] ) ) {
			echo $id                  = sanitize_text_field( $_GET['ID'] );			
			$doc_parent_id              = $id . ',';

			/**
			 * Section Docs
			 **/
			$doc_parent                 = get_children( [
				'post_parent'       => $id
			] );
			$doc_sec_ids                = '';
			$doc_child_sec_ids          = '';
			$doc_child_ids              = '';
			foreach ( $doc_parent as $doc_section ) {
				$doc_sec_ids            .= $doc_section->ID . ',';
				$doc_sec_child           = get_children( [
					'post_parent' => $doc_section->ID
				] );
				foreach ( $doc_sec_child as $doc_child_sec ) {
					$doc_child_sec_ids    .= $doc_child_sec->ID . ',';

					$doc_child = get_children( [
						'post_parent'  => $doc_child_sec->ID
					] );
					foreach ( $doc_child as $doc_childs ) {
						$doc_child_ids     .= $doc_childs->ID . ',';
					}
				}
			}

			$doc_delete_ids = $doc_parent_id . $doc_sec_ids . $doc_child_sec_ids . $doc_child_ids;
			$doc_doc_ids                  = explode( ',', $doc_delete_ids );
			$doc_doc_ids_int              = array_map( 'intval', $doc_doc_ids );
			foreach ( $doc_doc_ids_int as $doc_deletes ) {
				wp_trash_post( $doc_deletes, true );
			}
			header( "Location:" . admin_url( 'admin.php?page=eazydocs' ) );
			
		}
	}
}