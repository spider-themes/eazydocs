<?php
namespace eazyDocs\Admin;

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

		if ( ! empty ( $_GET['ID'] ) ) {

			$id = sanitize_text_field( $_GET['ID'] );

			$doc_ids = explode( ',', $id );

			$doc_ids_int = array_map( 'intval', $doc_ids );

			foreach ( $doc_ids_int as $item ) {
				wp_delete_post( $item, true );
			}

			header( "Location:" . admin_url( 'admin.php?page=eazydocs' ) );
		}

	}
}