<?php
namespace EazyDocs\Admin;

/**
 * Cannot access directly.
 */
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

/**
 * Class Delete_Post
 * @package EazyDocs\Admin
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

		// Case 1: Full Doc Delete
		if (
			isset( $_GET['Doc_Delete'], $_GET['DeleteID'], $_GET['_wpnonce'] )
		) {
			$doc_delete = sanitize_text_field( wp_unslash( $_GET['Doc_Delete'] ) );
			$delete_id  = sanitize_text_field( wp_unslash( $_GET['DeleteID'] ) );
			$nonce      = sanitize_text_field( wp_unslash( $_GET['_wpnonce'] ) );

			if ( $doc_delete === 'yes' && wp_verify_nonce( $nonce, $delete_id ) ) {
				$posts     = intval( $delete_id );
				$parent_id = $posts . ',';

				$parent        = get_children( [ 'post_parent' => $posts ] );
				$sec_ids       = '';
				$child_sec_ids = '';
				$child_ids     = '';

				foreach ( $parent as $section ) {
					$sec_ids .= $section->ID . ',';

					$sec_child = get_children( [ 'post_parent' => $section->ID ] );
					foreach ( $sec_child as $child_sec ) {
						$child_sec_ids .= $child_sec->ID . ',';

						$child = get_children( [ 'post_parent' => $child_sec->ID ] );
						foreach ( $child as $childs ) {
							$child_ids .= $childs->ID . ',';
						}
					}
				}

				$delete_ids  = $parent_id . $sec_ids . $child_sec_ids . $child_ids;
				$doc_ids     = explode( ',', $delete_ids );
				$doc_ids_int = array_filter( array_map( 'intval', $doc_ids ) );

				if ( ezd_perform_edit_delete_actions( 'delete', $posts ) ) {
					foreach ( $doc_ids_int as $deletes ) {
						if ( get_post( $deletes ) ) {
							wp_trash_post( $deletes, true );
						}
					}
					wp_safe_redirect( admin_url( 'admin.php?page=eazydocs' ) );
					exit;
				}
			}
		}

		// Case 2: Section Delete
		elseif (
			isset( $_GET['Section_Delete'], $_GET['ID'], $_GET['_wpnonce'] )
		) {
			$section_delete = sanitize_text_field( wp_unslash( $_GET['Section_Delete'] ) );
			$section_id     = sanitize_text_field( wp_unslash( $_GET['ID'] ) );
			$nonce          = sanitize_text_field( wp_unslash( $_GET['_wpnonce'] ) );	

			if ( $section_delete === 'yes' && wp_verify_nonce( $nonce, $section_id ) ) {
				$posts     = intval( $section_id );
				$parent_id = $posts . ',';

				$parent        = get_children( [ 'post_parent' => $posts ] );
				$sec_ids       = '';
				$child_sec_ids = '';
				$child_ids     = '';

				foreach ( $parent as $section ) {
					$sec_ids .= $section->ID . ',';

					$sec_child = get_children( [ 'post_parent' => $section->ID ] );
					foreach ( $sec_child as $child_sec ) {
						$child_sec_ids .= $child_sec->ID . ',';

						$child = get_children( [ 'post_parent' => $child_sec->ID ] );
						foreach ( $child as $childs ) {
							$child_ids .= $childs->ID . ',';
						}
					}
				}
				
				$delete_ids  = $parent_id . $sec_ids . $child_sec_ids . $child_ids;
				$doc_ids     = explode( ',', $delete_ids );
				$doc_ids_int = array_filter( array_map( 'intval', $doc_ids ) );
				
				if ( ezd_perform_edit_delete_actions( 'delete', $posts ) ) { 
					foreach ( $doc_ids_int as $deletes ) {
						if ( get_post( $deletes ) ) {
							wp_trash_post( $deletes, true );
						}
					}
					wp_safe_redirect( admin_url( 'admin.php?page=eazydocs' ) );
					exit;
				}
			}
		}

		// Case 3: Last Child Delete
		elseif (
			isset( $_GET['Last_Child_Delete'], $_GET['ID'], $_GET['_wpnonce'] )
		) {
			$last_child_delete = sanitize_text_field( wp_unslash( $_GET['Last_Child_Delete'] ) );
			$child_id          = sanitize_text_field( wp_unslash( $_GET['ID'] ) );
			$nonce             = sanitize_text_field( wp_unslash( $_GET['_wpnonce'] ) );

			if ( $last_child_delete === 'yes' && wp_verify_nonce( $nonce, $child_id ) ) {
				$last_doc_id = intval( $child_id );

				if ( ezd_perform_edit_delete_actions( 'delete', $last_doc_id ) ) {
					if ( get_post( $last_doc_id ) ) {
						wp_trash_post( $last_doc_id, true );
					}
					wp_safe_redirect( admin_url( 'admin.php?page=eazydocs' ) );
					exit;
				}
			}
		}
	}
}