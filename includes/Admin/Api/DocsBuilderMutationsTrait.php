<?php
/**
 * Document mutation and publishing permission handlers for Docs_Builder_Controller.
 *
 * @package EazyDocs\Admin\Api
 */

namespace EazyDocs\Admin\Api;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

trait DocsBuilderMutationsTrait {
	/**
	 * Update doc visibility via REST API.
	 *
	 * @since  2.9.0
	 * @param  \WP_REST_Request $request REST request.
	 * @return \WP_REST_Response|\WP_Error
	 */
	public function update_visibility( $request ) {
		$doc_id     = absint( $request->get_param( 'doc_id' ) );
		$visibility = sanitize_text_field( $request->get_param( 'visibility' ) );
		$password   = sanitize_text_field( $request->get_param( 'password' ) );
		$roles      = $request->get_param( 'roles' );
		$guest      = (bool) $request->get_param( 'allowGuests' );
		$apply_children = (bool) $request->get_param( 'applyToChildren' );

		if ( ! $doc_id || 'docs' !== get_post_type( $doc_id ) ) {
			return new \WP_Error( 'invalid_doc', __( 'Invalid document.', 'eazydocs' ), array( 'status' => 400 ) );
		}

		$allowed = array( 'publish', 'private', 'protected' );
		if ( ! in_array( $visibility, $allowed, true ) ) {
			return new \WP_Error( 'invalid_visibility', __( 'Invalid visibility option.', 'eazydocs' ), array( 'status' => 400 ) );
		}

		// Build the URL for the legacy visibility handler.
		$nonce = wp_create_nonce( (string) $doc_id );
		$url   = add_query_arg(
			array(
				'doc_visibility'       => $doc_id,
				'_wpnonce'             => $nonce,
				'doc_visibility_type'  => $visibility,
				'doc_password_input'   => str_replace( '#', ';hash;', $password ),
			),
			admin_url( 'admin.php' )
		);

		if ( 'private' === $visibility && is_array( $roles ) && ! empty( $roles ) ) {
			$url = add_query_arg( 'role_visibility', implode( ',', array_map( 'sanitize_text_field', $roles ) ), $url );
		}

		if ( 'private' === $visibility && $guest ) {
			$url = add_query_arg( 'role_visibility_guest', '1', $url );
		}

		if ( $apply_children ) {
			$url = add_query_arg( 'apply_roles_to_children', '1', $url );
		}

		return new \WP_REST_Response(
			array(
				'success'  => true,
				'redirect' => $url,
			),
			200
		);
	}

	/**
	 * Duplicate a doc via REST API.
	 *
	 * Performs the duplication server-side using the Pro helper
	 * functions and returns the new doc ID so the React frontend
	 * can stay on the same page and refresh its query cache.
	 *
	 * @since  2.9.0
	 * @param  \WP_REST_Request $request REST request.
	 * @return \WP_REST_Response|\WP_Error
	 */
	public function duplicate_doc( $request ) {
		$doc_id = absint( $request->get_param( 'doc_id' ) );

		if ( ! $doc_id || 'docs' !== get_post_type( $doc_id ) ) {
			return new \WP_Error( 'invalid_doc', __( 'Invalid document.', 'eazydocs' ), array( 'status' => 400 ) );
		}

		// Pro's autoloaded class also defines the helpers. Plain REST
		// permalinks do not instantiate that class during plugin startup.
		if ( ! function_exists( '\eazyDocsPro\Duplicator\ezd_duplicate_single_doc' ) ) {
			class_exists( '\eazyDocsPro\Duplicator\EazyDocs_Duplicate' );
		}

		if ( ! function_exists( '\eazyDocsPro\Duplicator\ezd_duplicate_single_doc' ) ) {
			return new \WP_Error(
				'pro_required',
				__( 'EazyDocs Pro is required for document duplication.', 'eazydocs' ),
				array( 'status' => 400 )
			);
		}

		$original_post = get_post( $doc_id );

		if ( ! $original_post || is_wp_error( $original_post ) ) {
			return new \WP_Error( 'invalid_doc', __( 'Invalid document.', 'eazydocs' ), array( 'status' => 400 ) );
		}

		$rand          = wp_rand( 1, 9999 );
		$new_parent_id = \eazyDocsPro\Duplicator\ezd_duplicate_single_doc( $original_post, $original_post->post_parent, $rand );

		if ( ! $new_parent_id || is_wp_error( $new_parent_id ) ) {
			return new \WP_Error( 'duplicate_failed', __( 'Failed to duplicate the document.', 'eazydocs' ), array( 'status' => 500 ) );
		}

		// Recursively duplicate children.
		if ( function_exists( '\eazyDocsPro\Duplicator\ezd_duplicate_doc_children_recursive' ) ) {
			\eazyDocsPro\Duplicator\ezd_duplicate_doc_children_recursive( $original_post->ID, $new_parent_id, $rand );
		}

		return new \WP_REST_Response(
			array(
				'success'  => true,
				'newId'    => $new_parent_id,
				'parentId' => (int) $original_post->post_parent,
			),
			200
		);
	}

	/**
	 * Update sidebar settings for a doc via REST API.
	 *
	 * @since  2.9.0
	 * @param  \WP_REST_Request $request REST request.
	 * @return \WP_REST_Response|\WP_Error
	 */
	public function update_sidebar( $request ) {
		$doc_id        = absint( $request->get_param( 'doc_id' ) );

		if ( ! $doc_id || 'docs' !== get_post_type( $doc_id ) ) {
			return new \WP_Error( 'invalid_doc', __( 'Invalid document.', 'eazydocs' ), array( 'status' => 400 ) );
		}

		$left_type     = sanitize_text_field( $request->get_param( 'leftType' ) );
		$left_content  = wp_kses_post( wp_unslash( $request->get_param( 'leftContent' ) ?? '' ) );
		$right_type    = sanitize_text_field( $request->get_param( 'rightType' ) );
		$right_content = wp_kses_post( wp_unslash( $request->get_param( 'rightContent' ) ?? '' ) );

		update_post_meta( $doc_id, 'ezd_doc_left_sidebar_type', $left_type );
		update_post_meta( $doc_id, 'ezd_doc_left_sidebar', $left_content );
		update_post_meta( $doc_id, 'ezd_doc_right_sidebar_type', $right_type );
		update_post_meta( $doc_id, 'ezd_doc_right_sidebar', $right_content );

		return new \WP_REST_Response(
			array(
				'success' => true,
			),
			200
		);
	}

	/**
	 * Permission check for publishing docs.
	 *
	 * @since  2.8.0
	 * @param  \WP_REST_Request|null $request Creation request, if available.
	 * @return bool
	 */
	public function check_publish_permission( $request = null ) {
		// Editors without publishing rights may still create drafts.
		if ( $request instanceof \WP_REST_Request && 'draft' === $request->get_param( 'post_status' ) ) {
			return $this->check_permission();
		}

		if ( current_user_can( 'manage_options' ) ) {
			return true;
		}

		$docs_capability = apply_filters( 'eazydocs_publish_capability', 'publish_docs' );
		if ( current_user_can( $docs_capability ) ) {
			return true;
		}

		$user         = wp_get_current_user();
		$user_roles   = ! empty( $user->roles ) ? (array) $user->roles : array();
		$author_roles = ezd_get_doc_author_roles();
		if ( array_intersect( $user_roles, $author_roles ) && current_user_can( 'publish_posts' ) ) {
			return true;
		}

		return false;
	}

	/**
	 * Create a new parent (top-level) doc.
	 *
	 * @since  2.8.0
	 * @param  \WP_REST_Request $request REST request.
	 * @return \WP_REST_Response|\WP_Error
	 */
	public function create_parent_doc( $request ) {
		$title = sanitize_text_field( $request->get_param( 'title' ) );
		$post_status = sanitize_key( $request->get_param( 'post_status' ) );
		if ( ! in_array( $post_status, array( 'publish', 'draft' ), true ) ) {
			$post_status = 'publish';
		}

		if ( empty( $title ) ) {
			return new \WP_Error( 'missing_title', __( 'A title is required.', 'eazydocs' ), array( 'status' => 400 ) );
		}

		$title = $this->decode_special_chars( $title );

		$query = new \WP_Query(
			array(
				'post_type'   => 'docs',
				'post_parent' => 0,
			)
		);

		$post_id = wp_insert_post(
			array(
				'post_title'   => $title,
				'post_parent'  => 0,
				'post_content' => '',
				'post_type'    => 'docs',
				'post_status'  => $post_status,
				'menu_order'   => $query->found_posts + 2,
				'post_author'  => get_current_user_id(),
			)
		);

		if ( is_wp_error( $post_id ) ) {
			return new \WP_Error( 'create_failed', $post_id->get_error_message(), array( 'status' => 500 ) );
		}

		wp_update_post( array( 'ID' => $post_id ) );

		return new \WP_REST_Response(
			array(
				'success' => true,
				'data'    => array(
					'id'       => $post_id,
					'redirect' => admin_url( 'admin.php?page=eazydocs-builder&new_doc_id=' . $post_id ),
					'doc'      => $this->documents()->build_parent_doc_item( $post_id ),
				),
			),
			200
		);
	}

	/**
	 * Create a new section under a parent doc.
	 *
	 * @since  2.8.0
	 * @param  \WP_REST_Request $request REST request.
	 * @return \WP_REST_Response|\WP_Error
	 */
	public function create_section( $request ) {
		$parent_id = absint( $request->get_param( 'parent_id' ) );
		$title     = sanitize_text_field( $request->get_param( 'title' ) );
		$post_status = sanitize_key( $request->get_param( 'post_status' ) );
		if ( ! in_array( $post_status, array( 'publish', 'draft' ), true ) ) {
			$post_status = 'publish';
		}

		if ( empty( $title ) ) {
			return new \WP_Error( 'missing_title', __( 'A title is required.', 'eazydocs' ), array( 'status' => 400 ) );
		}

		// Must be a doc: otherwise a section could be attached under any post type.
		if ( ! $parent_id || 'docs' !== get_post_type( $parent_id ) ) {
			return new \WP_Error( 'invalid_parent', __( 'The specified parent document does not exist.', 'eazydocs' ), array( 'status' => 400 ) );
		}

		if ( ! current_user_can( 'edit_post', $parent_id ) && ! current_user_can( 'manage_options' ) ) {
			return new \WP_Error( 'forbidden', __( 'You do not have permission to edit this document.', 'eazydocs' ), array( 'status' => 403 ) );
		}

		$title    = $this->decode_special_chars( $title );
		$children = get_children(
			array(
				'post_parent' => $parent_id,
				'post_type'   => 'docs',
			)
		);

		$status = $post_status;

		$post_id = wp_insert_post(
			array(
				'post_title'   => $title,
				'post_parent'  => $parent_id,
				'post_content' => '',
				'post_type'    => 'docs',
				'post_status'  => $status,
				'menu_order'   => count( $children ) + 2,
				'post_name'    => sanitize_title( $title ),
			)
		);

		if ( is_wp_error( $post_id ) ) {
			return new \WP_Error( 'create_failed', $post_id->get_error_message(), array( 'status' => 500 ) );
		}

		return new \WP_REST_Response(
			array(
				'success' => true,
				'data'    => array(
					'id'       => $post_id,
					'parentId' => $parent_id,
					'item'     => $this->documents()->build_child_doc_item( get_post( $post_id ), 1 ),
				),
			),
			200
		);
	}

	/**
	 * Create a child doc under a section.
	 *
	 * @since  2.8.0
	 * @param  \WP_REST_Request $request REST request.
	 * @return \WP_REST_Response|\WP_Error
	 */
	public function create_child( $request ) {
		$parent_id = absint( $request->get_param( 'parent_id' ) );
		$title     = sanitize_text_field( $request->get_param( 'title' ) );
		$post_status = sanitize_key( $request->get_param( 'post_status' ) );
		if ( ! in_array( $post_status, array( 'publish', 'draft' ), true ) ) {
			$post_status = 'publish';
		}

		if ( empty( $title ) ) {
			return new \WP_Error( 'missing_title', __( 'A title is required.', 'eazydocs' ), array( 'status' => 400 ) );
		}

		$parent_post = $parent_id > 0 ? get_post( $parent_id ) : null;

		if ( ! $parent_post || 'docs' !== $parent_post->post_type ) {
			return new \WP_Error( 'invalid_parent', __( 'The specified document does not exist.', 'eazydocs' ), array( 'status' => 400 ) );
		}

		if ( ! current_user_can( 'edit_post', $parent_id ) && ! current_user_can( 'manage_options' ) ) {
			return new \WP_Error( 'forbidden', __( 'You do not have permission to edit this document.', 'eazydocs' ), array( 'status' => 403 ) );
		}

		$title    = $this->decode_special_chars( $title );
		$children = get_children(
			array(
				'post_parent' => $parent_id,
				'post_type'   => 'docs',
			)
		);

		$status = $post_status;

		$post_id = wp_insert_post(
			array(
				'post_title'   => $title,
				'post_parent'  => $parent_id,
				'post_content' => '',
				'post_type'    => 'docs',
				'post_status'  => $status,
				'menu_order'   => count( $children ) + 2,
				'post_name'    => sanitize_title( $title ),
			)
		);

		if ( is_wp_error( $post_id ) ) {
			return new \WP_Error( 'create_failed', $post_id->get_error_message(), array( 'status' => 500 ) );
		}

		return new \WP_REST_Response(
			array(
				'success' => true,
				'data'    => array(
					'id'           => $post_id,
					'parentId'     => $parent_id,
					'rootParentId' => $this->get_root_parent_id( $parent_id ),
					'item'         => $this->documents()->build_child_doc_item( get_post( $post_id ), $this->get_doc_depth( $parent_id ) + 1 ),
				),
			),
			200
		);
	}

	/**
	 * Delete (trash) a doc and all its descendants.
	 *
	 * @since  2.8.0
	 * @param  \WP_REST_Request $request REST request.
	 * @return \WP_REST_Response|\WP_Error
	 */
	public function delete_doc( $request ) {
		$doc_id = absint( $request->get_param( 'doc_id' ) );

		if ( ! $doc_id || 'docs' !== get_post_type( $doc_id ) ) {
			return new \WP_Error( 'invalid_doc', __( 'The specified document does not exist.', 'eazydocs' ), array( 'status' => 400 ) );
		}

		// The permission helper echoes an HTML notice on failure; swallow it so it
		// can't corrupt this JSON response.
		ob_start();
		$can_delete = function_exists( 'ezd_perform_edit_delete_actions' ) && ezd_perform_edit_delete_actions( 'delete', $doc_id );
		ob_end_clean();

		if ( ! $can_delete ) {
			return new \WP_Error( 'forbidden', __( 'You do not have permission to delete this document.', 'eazydocs' ), array( 'status' => 403 ) );
		}

		// Collect all descendant IDs (one query; any depth).
		$all_ids = ezd_get_doc_tree_ids( $doc_id );
		array_unshift( $all_ids, $doc_id );

		// Authorize the entire operation before trashing anything. A child can
		// belong to another author even when the current user owns the root.
		foreach ( $all_ids as $id ) {
			if ( 'docs' !== get_post_type( $id ) || ! current_user_can( 'delete_post', $id ) ) {
				return new \WP_Error( 'forbidden', __( 'You do not have permission to delete every document in this tree.', 'eazydocs' ), array( 'status' => 403 ) );
			}
		}

		$trashed = 0;

		foreach ( $all_ids as $id ) {
			if ( wp_trash_post( $id ) ) {
				++$trashed;
			}
		}

		return new \WP_REST_Response(
			array(
				'success' => true,
				'data'    => array(
					'trashed' => $trashed,
				),
			),
			200
		);
	}

	/**
	 * Rename a doc's title (inline edit from the builder).
	 *
	 * Only the title is updated; the slug/permalink is intentionally left
	 * untouched so existing links never break on a rename.
	 *
	 * @since  2.12.2
	 * @param  \WP_REST_Request $request REST request.
	 * @return \WP_REST_Response|\WP_Error
	 */
	public function rename_doc( $request ) {
		$doc_id = absint( $request->get_param( 'doc_id' ) );
		$title  = sanitize_text_field( $request->get_param( 'title' ) );
		$title  = $this->decode_special_chars( $title );
		$title  = trim( $title );

		if ( '' === $title ) {
			return new \WP_Error( 'missing_title', __( 'A title is required.', 'eazydocs' ), array( 'status' => 400 ) );
		}

		$post = $doc_id > 0 ? get_post( $doc_id ) : null;

		if ( ! $post || 'docs' !== $post->post_type ) {
			return new \WP_Error( 'invalid_doc', __( 'The specified document does not exist.', 'eazydocs' ), array( 'status' => 400 ) );
		}

		if ( ! current_user_can( 'edit_post', $doc_id ) && ! current_user_can( 'manage_options' ) ) {
			return new \WP_Error( 'forbidden', __( 'You do not have permission to edit this document.', 'eazydocs' ), array( 'status' => 403 ) );
		}

		$updated = wp_update_post(
			array(
				'ID'         => $doc_id,
				'post_title' => $title,
			),
			true
		);

		if ( is_wp_error( $updated ) ) {
			return new \WP_Error( 'rename_failed', $updated->get_error_message(), array( 'status' => 500 ) );
		}

		return new \WP_REST_Response(
			array(
				'success' => true,
				'data'    => array(
					'id'    => $doc_id,
					'title' => html_entity_decode( $title ),
				),
			),
			200
		);
	}

	/**
	 * Decode special character placeholders in titles.
	 *
	 * @since  2.8.0
	 * @param  string $title Raw title with placeholders.
	 * @return string
	 */
	private function decode_special_chars( $title ) {
		return str_replace( array_keys( self::$replacements ), array_values( self::$replacements ), $title );
	}
}
