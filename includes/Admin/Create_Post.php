<?php
namespace eazyDocs\Admin;
use ElementorPro\Modules\DynamicTags\Tags\Post_ID;

/**
 * Class Create_Post
 * @package eazyDocs\Admin
 */
class Create_Post {
	/**
	 * Create_Post constructor.
	 */
	public function __construct() {
		add_action( 'admin_init', [ $this, 'create_parent_doc' ] );
		add_action( 'admin_init', [ $this, 'create_new_doc' ] );
		add_action( 'admin_init', [ $this, 'create_section_doc' ] );
		add_action( 'admin_init', [ $this, 'create_child_doc' ] );
	}

    /**
 * Create parent Doc post
 */
	public function create_parent_doc() {
		if (
			isset( $_GET['parent_title'], $_GET['Create_doc'], $_GET['_wpnonce'] )
			&& sanitize_text_field( wp_unslash( $_GET['Create_doc'] ) ) === 'yes'
			&& wp_verify_nonce( sanitize_text_field( wp_unslash( $_GET['_wpnonce'] ) ), 'parent_doc_nonce' )
		) {
			$title_raw  = sanitize_text_field( wp_unslash( $_GET['parent_title'] ) );
			$title      = htmlspecialchars( $title_raw );
			$str        = ['ezd_ampersand', 'ezd_hash', 'ezd_plus'];
			$rplc       = ['&', '#', '+'];
			$title_text = str_replace( $str, $rplc, $title );

			$query = new \WP_Query([
				'post_type'   => 'docs',
				'post_parent' => 0
			]);
			$order = $query->found_posts + 2;

			$parent_doc = [
				'post_title'   => $title_text,
				'post_parent'  => 0,
				'post_content' => '',
				'post_type'    => 'docs',
				'post_status'  => 'publish',
				'post_author'  => get_current_user_id(),
				'menu_order'   => $order,
			];

			wp_insert_post( $parent_doc );
			wp_safe_redirect( admin_url( 'admin.php?page=eazydocs' ) );
			exit;
		}
	}

	/**
	 * Create new Doc post
	 */
	public function create_new_doc() {
		if (
			isset( $_GET['new_doc'], $_GET['Create_doc'], $_GET['_wpnonce'] )
			&& sanitize_text_field( wp_unslash( $_GET['Create_doc'] ) ) === 'yes'
			&& wp_verify_nonce( sanitize_text_field( wp_unslash( $_GET['_wpnonce'] ) ), 'create_new_doc_nonce' )
		) {
			$doc_title_raw = sanitize_text_field( wp_unslash( $_GET['new_doc'] ) );
			$doc_title     = htmlspecialchars( $doc_title_raw );
			$str           = ['ezd_ampersand', 'ezd_hash', 'ezd_plus'];
			$rplc          = ['&', '#', '+'];
			$doc_title_text = str_replace( $str, $rplc, $doc_title );

			$new_doc = [
				'post_title'   => $doc_title_text,
				'post_parent'  => 0,
				'post_content' => '',
				'post_type'    => 'docs',
				'post_status'  => 'publish',
				'menu_order'   => 1,
			];

			wp_insert_post( $new_doc );
			wp_safe_redirect( admin_url( 'admin.php?page=eazydocs' ) );
			exit;
		}
	}

	/**
	 * Create section doc post
	*/
	public function create_section_doc() {
		if (
			isset( $_GET['is_section'], $_GET['Create_Section'], $_GET['parentID'], $_GET['_wpnonce'] )
			&& sanitize_text_field( wp_unslash( $_GET['Create_Section'] ) ) === 'yes'
			&& wp_verify_nonce( sanitize_text_field( wp_unslash( $_GET['_wpnonce'] ) ), sanitize_text_field( wp_unslash( $_GET['parentID'] ) ) )
		) {
			$parentID       = absint( wp_unslash( $_GET['parentID'] ) );
			$section_title  = sanitize_text_field( wp_unslash( $_GET['is_section'] ) );
			$section_title  = htmlspecialchars( $section_title );
			$section_slug   = sanitize_title( $section_title );

			$str              = ['ezd_ampersand', 'ezd_hash', 'ezd_plus'];
			$rplc             = ['&', '#', '+'];
			$section_title_text = str_replace( $str, $rplc, $section_title );

			$parent_items = get_children([
				'post_parent' => $parentID,
				'post_type'   => 'docs'
			]);

			$order = count( $parent_items ) + 2;
			$post_status = ezd_is_premium() ? get_post_status( $parentID ) : 'publish';

			$section_doc = [
				'post_title'   => $section_title_text,
				'post_name'    => $section_slug,
				'post_parent'  => $parentID,
				'post_content' => '',
				'post_type'    => 'docs',
				'post_status'  => $post_status,
				'menu_order'   => $order,
			];

			$section_doc_id = wp_insert_post( $section_doc );
			if ( $section_doc_id && ! is_wp_error( $section_doc_id ) ) {
				wp_update_post([ 'ID' => $section_doc_id ]);
			}

			wp_safe_redirect( admin_url( 'admin.php?page=eazydocs' ) );
			exit;
		}
	}

	/**
	 * Create child doc post
	 */
	public function create_child_doc() {
		if (
			isset( $_GET['child'], $_GET['Create_Child'], $_GET['childID'], $_GET['_wpnonce'] )
			&& sanitize_text_field( wp_unslash( $_GET['Create_Child'] ) ) === 'yes'
			&& wp_verify_nonce( sanitize_text_field( wp_unslash( $_GET['_wpnonce'] ) ), sanitize_text_field( wp_unslash( $_GET['childID'] ) ) )
		) {
			$child_id        = absint( wp_unslash( $_GET['childID'] ) );
			$child_title     = sanitize_text_field( wp_unslash( $_GET['child'] ) );
			$child_title     = htmlspecialchars( $child_title );
			$child_slug      = sanitize_title( $child_title );

			$str               = ['ezd_ampersand', 'ezd_hash', 'ezd_plus'];
			$rplc              = ['&', '#', '+'];
			$child_title_text  = str_replace( $str, $rplc, $child_title );

			$child_items = get_children([
				'post_parent' => $child_id,
				'post_type'   => 'docs',
			]);

			$order = count( $child_items ) + 2;
			$post_status = ezd_is_premium() ? get_post_status( $child_id ) : 'publish';

			$child_doc = [
				'post_title'   => $child_title_text,
				'post_name'    => $child_slug,
				'post_parent'  => $child_id,
				'post_content' => '',
				'post_type'    => 'docs',
				'post_status'  => $post_status,
				'menu_order'   => $order,
			];

			$child_doc_id = wp_insert_post( $child_doc );
			if ( $child_doc_id && ! is_wp_error( $child_doc_id ) ) {
				wp_update_post([ 'ID' => $child_doc_id ]);
			}

			wp_safe_redirect( admin_url( 'admin.php?page=eazydocs' ) );
			exit;
		}
	}
}