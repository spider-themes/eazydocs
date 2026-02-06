<?php
namespace EazyDocs\Admin;

/**
 * Cannot access directly.
 */
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

/**
 * Class Create_Post
 * @package EazyDocs\Admin
 */
class Create_Post {
	private static $replacements = ['ezd_ampersand' => '&', 'ezd_hash' => '#', 'ezd_plus' => '+'];

	/**
	 * Create_Post constructor.
	 */
	public function __construct() {
		add_action( 'admin_init', [ $this, 'handle_doc_creation' ] );
	}

	/**
	 * Unified handler for all doc creation actions
	 */
	public function handle_doc_creation() {
		// Check permissions first
		if ( ! current_user_can( 'publish_docs' ) ) {
			return;
		}

		// Handle parent doc creation
		if ( $this->verify_action( 'Create_doc', 'parent_doc_nonce', 'parent_title' ) ) {
			$title = $this->sanitize_title( $_GET['parent_title'] );
			$query = new \WP_Query( [ 'post_type' => 'docs', 'post_parent' => 0 ] );
			$this->create_post( $title, 0, $query->found_posts + 2 );
		}

		// Handle new doc creation
		if ( $this->verify_action( 'Create_doc', 'create_new_doc_nonce', 'new_doc' ) ) {
			$title = $this->sanitize_title( $_GET['new_doc'] );
			$this->create_post( $title, 0, 1 );
		}

		// Handle section creation
		if ( isset( $_GET['Create_Section'], $_GET['parentID'], $_GET['_wpnonce'], $_GET['is_section'] ) && 
			 sanitize_text_field( wp_unslash( $_GET['Create_Section'] ) ) === 'yes' &&
			 wp_verify_nonce( sanitize_text_field( wp_unslash( $_GET['_wpnonce'] ) ), 'ezd_create_section_nonce_' . sanitize_text_field( wp_unslash( $_GET['parentID'] ) ) ) ) {
			
			$parent_id = absint( wp_unslash( $_GET['parentID'] ) );
			$title = $this->sanitize_title( $_GET['is_section'] );
			$children = get_children( [ 'post_parent' => $parent_id, 'post_type' => 'docs' ] );
			$status = ezd_is_premium() ? get_post_status( $parent_id ) : 'publish';
			$this->create_post( $title, $parent_id, count( $children ) + 2, $status, sanitize_title( $title ) );
		}

		// Handle child creation
		if ( isset( $_GET['Create_Child'], $_GET['childID'], $_GET['_wpnonce'], $_GET['child'] ) && 
			 sanitize_text_field( wp_unslash( $_GET['Create_Child'] ) ) === 'yes' &&
			 wp_verify_nonce( sanitize_text_field( wp_unslash( $_GET['_wpnonce'] ) ), 'ezd_create_child_nonce_' . sanitize_text_field( wp_unslash( $_GET['childID'] ) ) ) ) {
			
			$child_id = absint( wp_unslash( $_GET['childID'] ) );
			$title = $this->sanitize_title( $_GET['child'] );
			$children = get_children( [ 'post_parent' => $child_id, 'post_type' => 'docs' ] );
			$status = ezd_is_premium() ? get_post_status( $child_id ) : 'publish';
			$this->create_post( $title, $child_id, count( $children ) + 2, $status, sanitize_title( $title ) );
		}
	}

	/**
	 * Verify action is valid
	 */
	private function verify_action( $action_param, $nonce_action, $title_param ) {
		return isset( $_GET[ $action_param ], $_GET['_wpnonce'], $_GET[ $title_param ] ) &&
			   sanitize_text_field( wp_unslash( $_GET[ $action_param ] ) ) === 'yes' &&
			   wp_verify_nonce( sanitize_text_field( wp_unslash( $_GET['_wpnonce'] ) ), $nonce_action );
	}

	/**
	 * Sanitize and decode title
	 */
	private function sanitize_title( $title ) {
		$title = sanitize_text_field( wp_unslash( $title ) );
		$title = htmlspecialchars( $title );
		return str_replace( array_keys( self::$replacements ), array_values( self::$replacements ), $title );
	}

	/**
	 * Create a doc post
	 */
	private function create_post( $title, $parent_id = 0, $menu_order = 1, $status = 'publish', $slug = '' ) {
		$post_data = [
			'post_title'   => $title,
			'post_parent'  => $parent_id,
			'post_content' => '',
			'post_type'    => 'docs',
			'post_status'  => $status,
			'menu_order'   => $menu_order,
		];

		if ( $parent_id === 0 ) {
			$post_data['post_author'] = get_current_user_id();
		}

		if ( ! empty( $slug ) ) {
			$post_data['post_name'] = $slug;
		}

		$post_id = wp_insert_post( $post_data );

		if ( ! is_wp_error( $post_id ) ) {
			wp_update_post( [ 'ID' => $post_id ] );
		}
		wp_safe_redirect( admin_url( 'admin.php?page=eazydocs-builder' . ( $parent_id === 0 ? '&new_doc_id=' . $post_id : '' ) ) );

		exit;
	}
}