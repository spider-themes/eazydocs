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

		if ( isset ( $_GET['Doc_Delete'] ) && ! empty ( $_GET['DeleteID'] ) && ! empty ( $_GET['Doc_Delete'] == 'yes' ) ) {
			echo 'DeleteID: ' . $_GET['DeleteID'] . '<br>';
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

		} elseif ( isset ( $_GET['Section_Delete'] ) && ! empty ( $_GET['ID'] ) && ! empty ( $_GET['Section_Delete'] == 'yes' )) {
			
			$posts                  = sanitize_text_field( $_GET['ID'] );
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
		} elseif ( isset ( $_GET['Last_Child_Delete'] ) && ! empty ( $_GET['ID'] ) && ! empty ( $_GET['Last_Child_Delete'] == 'yes' )) {
			$last_doc_id = sanitize_text_field( $_GET['ID'] );
			wp_trash_post( $last_doc_id, true );
			header( "Location:" . admin_url( 'admin.php?page=eazydocs' ) );
		}
	}
}