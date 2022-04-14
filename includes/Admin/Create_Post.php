<?php

namespace eazyDocs\Admin;

class Create_Post {

	/**
	 * Create_Post constructor.
	 */
	public function __construct() {
		add_action( 'admin_init', [ $this, 'create_new_doc' ] );
		add_action( 'admin_init', [ $this, 'create_section_doc' ] );
		add_action( 'admin_init', [ $this, 'create_child_doc' ] );
	}

	/**
	 * Create new post
	 */
	public function create_new_doc() {
		if ( isset ( $_GET['new_doc'] ) && ! empty ( $_GET['new_doc'] ) ) {

			$doc_title      = ! empty ( $_GET['new_doc'] ) ? sanitize_text_field( $_GET['new_doc'] ) : 0;

			// Create post object
			$post = array(
				'post_title'   => $doc_title,
				'post_parent'  => 0,
				'post_content' => '',
				'post_type'    => 'docs',
				'post_status'  => 'publish',
				'menu_order'   => 1
			);
			wp_insert_post( $post, $wp_error = '' );
			header("Location:". admin_url('admin.php?page=eazydocs'));
		}
	}

	/**
	 * Create section post
	 */
	public function create_section_doc() {
		if ( isset ( $_GET['section'] ) && ! empty ( $_GET['section'] ) ) {

			$parentID      = ! empty ( $_GET['parentID'] ) ? absint( $_GET['parentID'] ) : 0;
			$section_title = ! empty ( $_GET['section'] ) ? sanitize_text_field( $_GET['section'] ) : '';
			$parent_item   = get_children( array(
				'post_parent' => $parentID,
				'post_type'   => 'docs'
			) );

			$add   = 2;
			$order = count( $parent_item );
			$order = $order + $add;

			// Create post object
			$post = array(
				'post_title'   => $section_title,
				'post_parent'  => $parentID,
				'post_content' => '',
				'post_type'    => 'docs',
				'post_status'  => 'publish',
				'menu_order'   => $order
			);
			wp_insert_post( $post, $wp_error = '' );
			header("Location:". admin_url('admin.php?page=eazydocs'));
		}
	}

	/**
	 *  Create section post
	 */
	public function create_child_doc() {

		if ( isset ( $_GET['child'] ) && ! empty ( $_GET['child'] ) ) {

			$child_id    = ! empty ( $_GET['childID'] ) ? absint( $_GET['childID'] ) : 0;
			$child_title = ! empty ( $_GET['child'] ) ? sanitize_text_field( $_GET['child'] ) : '';

			$child_item = get_children( array(
				'post_parent' => $child_id,
				'post_type'   => 'docs'
			) );

			$add = 2;
			echo $order = count( $child_item );
			$order = $order + $add;

			// Create post object
			$post = array(
				'post_title'   => $child_title,
				'post_parent'  => $child_id,
				'post_content' => '',
				'post_type'    => 'docs',
				'post_status'  => 'publish',
				'menu_order'   => $order
			);
			wp_insert_post( $post, $wp_error = '' );
			header( "Location:" . admin_url( 'admin.php?page=eazydocs' ) );
		}
	}
}