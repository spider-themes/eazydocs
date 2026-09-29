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

			if ( 'yes' === $doc_delete && wp_verify_nonce( $nonce, 'ezd_delete_doc_' . $delete_id ) ) {
				$this->trash_tree( intval( $delete_id ) );
			}
		}

		// Case 2: Section Delete
		elseif (
			isset( $_GET['Section_Delete'], $_GET['ID'], $_GET['_wpnonce'] )
		) {
			$section_delete = sanitize_text_field( wp_unslash( $_GET['Section_Delete'] ) );
			$section_id     = sanitize_text_field( wp_unslash( $_GET['ID'] ) );
			$nonce          = sanitize_text_field( wp_unslash( $_GET['_wpnonce'] ) );

			if ( 'yes' === $section_delete && wp_verify_nonce( $nonce, 'ezd_delete_doc_' . $section_id ) ) {
				$this->trash_tree( intval( $section_id ) );
			}
		}

		// Case 3: Last Child Delete
		elseif (
			isset( $_GET['Last_Child_Delete'], $_GET['ID'], $_GET['_wpnonce'] )
		) {
			$last_child_delete = sanitize_text_field( wp_unslash( $_GET['Last_Child_Delete'] ) );
			$child_id          = sanitize_text_field( wp_unslash( $_GET['ID'] ) );
			$nonce             = sanitize_text_field( wp_unslash( $_GET['_wpnonce'] ) );

			if ( 'yes' === $last_child_delete && wp_verify_nonce( $nonce, 'ezd_delete_doc_' . $child_id ) ) {
				$this->trash_tree( intval( $child_id ) );
			}
		}
	}

	/**
	 * Trash a doc and every descendant doc, then return to the builder.
	 *
	 * The previous code collected children with get_children() without a
	 * post_type (so images attached to a doc were trashed along with it) and
	 * stopped three levels down, leaving deeper docs published under a trashed
	 * parent.
	 *
	 * @param int $doc_id Doc to trash.
	 * @return void
	 */
	private function trash_tree( $doc_id ) {
		$doc = get_post( $doc_id );

		if ( ! $doc || 'docs' !== $doc->post_type || ! ezd_perform_edit_delete_actions( 'delete', $doc_id ) ) {
			return;
		}

		$ids = array_merge( [ $doc_id ], ezd_get_doc_tree_ids( $doc_id ) );

		foreach ( $ids as $id ) {
			wp_trash_post( $id );
		}

		wp_safe_redirect( admin_url( 'admin.php?page=eazydocs-builder' ) );
		exit;
	}
}
