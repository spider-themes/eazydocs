<?php
/**
 * WordPress document queries and REST item data for the Docs Builder.
 */

namespace EazyDocs\Admin\Api;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class DocsBuilderDocuments {

	/**
	 * Request-scoped parent → ordered child IDs map. See get_docs_children_map().
	 *
	 * @var array<int, int[]>|null
	 */
	private $children_map = null;

	/**
	 * Get all parent (top-level) docs.
	 *
	 * @since  2.8.0
	 * @return array
	 */
	public function get_parent_docs() {
		$query = new \WP_Query(
			array(
				'post_type'      => 'docs',
				'posts_per_page' => -1,
				'post_parent'    => 0,
				'orderby'        => 'menu_order',
				'order'          => 'ASC',
				'post_status'    => array( 'publish', 'draft', 'private' ),
			)
		);

		$docs = array();

		while ( $query->have_posts() ) {
			$query->the_post();
			if ( ! ezd_can_read_doc_content( get_the_ID() ) ) {
				continue;
			}

			$docs[] = $this->build_parent_doc_item( get_the_ID() );
		}

		wp_reset_postdata();

		return $docs;
	}

	/**
	 * Read just the requested branch for an independent children request.
	 *
	 * Depth limits apply to rendered items. Keep their descendant counts
	 * accurate by collecting the entire branch, including deeper levels.
	 *
	 * @param int $parent_id Root of the requested branch.
	 * @return array
	 */
	public function get_branch_children( $parent_id ) {
		if ( null !== $this->children_map ) {
			return $this->get_children_tree( $parent_id );
		}

		global $wpdb;

		$pending = array( $parent_id );
		$seen    = array( $parent_id => true );
		$map     = array();
		while ( $pending ) {
			$parents = $pending;
			$pending = array();
			foreach ( array_chunk( $parents, 500 ) as $batch ) {
				$placeholders = implode( ', ', array_fill( 0, count( $batch ), '%d' ) );
				// phpcs:ignore WordPress.DB.DirectDatabaseQuery, WordPress.DB.PreparedSQL.InterpolatedNotPrepared -- batched indexed lookup with prepared parent IDs.
				$rows = $wpdb->get_results( $wpdb->prepare(
					"SELECT ID, post_parent FROM {$wpdb->posts}
					 WHERE post_parent IN ($placeholders)
					   AND post_type = 'docs' AND post_status NOT IN ('trash', 'auto-draft', 'inherit')
					 ORDER BY menu_order ASC, ID ASC",
					$batch
				) );
				foreach ( (array) $rows as $row ) {
					$id = (int) $row->ID;
					if ( isset( $seen[ $id ] ) ) {
						continue;
					}

					$seen[ $id ]                    = true;
					$map[ (int) $row->post_parent ][] = $id;
					$pending[]                      = $id;
				}
			}
		}

		return $this->get_children_tree( $parent_id, 1, $map );
	}

	/**
	 * Recursively build the children tree for a parent doc.
	 *
	 * @since  2.8.0
	 * @param  int $parent_id Parent post ID.
	 * @param  int $depth     Current depth (1-indexed).
	 * @param  array|null $map Optional branch-specific adjacency map.
	 * @return array
	 */
	public function get_children_tree( $parent_id, $depth = 1, $map = null ) {
		$map       = $map ?? $this->get_docs_children_map();
		$child_ids = $map[ (int) $parent_id ] ?? array();

		// On the root call, load every post row + meta in the subtree in one go so
		// the per-node helpers below hit the cache instead of the database.
		if ( 1 === (int) $depth && $child_ids ) {
			$seen  = array( (int) $parent_id => true );
			$stack = $child_ids;
			while ( $stack ) {
				$id = array_pop( $stack );
				if ( isset( $seen[ $id ] ) ) {
					continue;
				}
				$seen[ $id ] = true;
				if ( ! empty( $map[ $id ] ) ) {
					array_push( $stack, ...$map[ $id ] );
				}
			}
			unset( $seen[ (int) $parent_id ] );
			_prime_post_caches( array_keys( $seen ), false, true );
		}

		$results = array();

		foreach ( $child_ids as $child_id ) {
			$child = get_post( $child_id );
			if ( ! $child instanceof \WP_Post || ! ezd_can_read_doc_content( $child->ID ) ) {
				continue;
			}

			$item = $this->build_child_doc_item( $child, $depth, $map );

			// Recurse if children exist and depth allows.
			$max_depth = ezd_is_premium() ? 5 : 3;
			if ( ! empty( $item['hasChildren'] ) && $depth < $max_depth ) {
				$item['children'] = $this->get_children_tree( $child->ID, $depth + 1, $map );
			}

			$results[] = $item;
		}

		return $results;
	}

	/**
	 * Ordered parent → child IDs map of every non-trashed doc, built once per request.
	 *
	 * Mirrors get_children()'s default 'any' status (everything except trash and
	 * auto-draft) and menu_order ordering. The builder previously ran get_children()
	 * per node plus two more queries per node (one of them get_pages(), which
	 * loads the whole docs table), which made it O(n²) on large libraries.
	 *
	 * @return array<int, int[]>
	 */
	private function get_docs_children_map() {
		if ( null !== $this->children_map ) {
			return $this->children_map;
		}

		global $wpdb;

		// phpcs:ignore WordPress.DB.DirectDatabaseQuery -- request-scoped, memoised above.
		$rows = $wpdb->get_results(
			"SELECT ID, post_parent FROM {$wpdb->posts}
			 WHERE post_type = 'docs' AND post_status NOT IN ('trash', 'auto-draft', 'inherit')
			 ORDER BY menu_order ASC, ID ASC"
		);

		$this->children_map = array();
		foreach ( (array) $rows as $row ) {
			$this->children_map[ (int) $row->post_parent ][] = (int) $row->ID;
		}

		return $this->children_map;
	}

	/**
	 * Build a parent doc payload for the React builder.
	 *
	 * @since  2.9.0
	 * @param  int $post_id Post ID.
	 * @return array
	 */
	public function build_parent_doc_item( $post_id ) {
		$post_obj = get_post( $post_id );

		if ( ! $post_obj instanceof \WP_Post ) {
			return array();
		}

		$post_status = get_post_status( $post_id );
		$status_info = $this->get_status_info( $post_status, $post_obj );

		return array(
			'id'           => $post_id,
			'title'        => html_entity_decode( $post_obj->post_title ),
			'permalink'    => get_permalink( $post_id ),
			'editLink'     => get_edit_post_link( $post_id, 'raw' ),
			'status'       => $post_status,
			'statusIcon'   => $status_info['icon'],
			'statusText'   => $status_info['text'],
			'hasPassword'  => ! empty( $post_obj->post_password ),
			'childCount'   => $this->count_descendants_from_map( $post_id ),
			'canEdit'      => ezd_is_admin_or_editor( $post_id, 'edit' ),
			'canDelete'    => ezd_is_admin_or_editor( $post_id, 'delete' ),
			'deleteNonce'  => wp_create_nonce( 'ezd_delete_doc_' . $post_id ),
			'sectionNonce' => wp_create_nonce( 'ezd_create_section_' . $post_id ),
			'proActions'   => $this->get_pro_actions_data( $post_id ),
		);
	}

	/**
	 * Build a child doc payload for the React builder.
	 *
	 * @since  2.9.0
	 * @param  \WP_Post $post  Post object.
	 * @param  int      $depth Depth level.
	 * @param  array|null $map Optional branch-specific adjacency map.
	 * @return array
	 */
	public function build_child_doc_item( $post, $depth, $map = null ) {
		$post_status = $post->post_status;
		if ( ! empty( $post->post_password ) ) {
			$post_status = 'protected';
		}

		$map          = $map ?? $this->get_docs_children_map();
		$has_children = ! empty( $map[ (int) $post->ID ] );

		$can_add_sub = true;
		if ( ! ezd_is_premium() && 3 === (int) $depth ) {
			$can_add_sub = false;
		}
		if ( ezd_is_premium() && 5 === (int) $depth ) {
			$can_add_sub = false;
		}

		return array(
			'id'          => $post->ID,
			'title'       => html_entity_decode( $post->post_title ),
			'permalink'   => get_permalink( $post->ID ),
			'editLink'    => admin_url( 'post.php' ) . '?post=' . $post->ID . '&action=edit',
			'status'      => $post_status,
			'hasPassword' => ! empty( $post->post_password ),
			'hasChildren' => $has_children,
			'childCount'  => $this->count_descendants_from_map( $post->ID, $map ),
			'positive'    => (int) get_post_meta( $post->ID, 'positive', true ),
			'negative'    => (int) get_post_meta( $post->ID, 'negative', true ),
			'visibility'  => $this->get_visibility_info( $post->ID ),
			'canEdit'     => ezd_is_admin_or_editor( $post->ID, 'edit' ),
			'canDelete'   => ezd_is_admin_or_editor( $post->ID, 'delete' ),
			'canAddChild' => $can_add_sub,
			'deleteNonce' => wp_create_nonce( 'ezd_delete_doc_' . $post->ID ),
			'childNonce'  => wp_create_nonce( 'ezd_create_child_' . $post->ID ),
			'depth'       => $depth,
			'children'    => array(),
			'proActions'  => $this->get_pro_actions_data( $post->ID ),
		);
	}

	/**
	 * Count descendants of a doc from the in-memory children map.
	 *
	 * @param int $post_id Doc ID.
	 * @param array|null $map Optional branch-specific adjacency map.
	 * @return int
	 */
	private function count_descendants_from_map( $post_id, $map = null ) {
		$map     = $map ?? $this->get_docs_children_map();
		$post_id = (int) $post_id;

		if ( empty( $map[ $post_id ] ) ) {
			return 0;
		}

		$count = 0;
		$stack = $map[ $post_id ];
		$seen  = array( $post_id => true );

		while ( $stack ) {
			$id = array_pop( $stack );
			if ( isset( $seen[ $id ] ) ) {
				continue;
			}
			$seen[ $id ] = true;
			++$count;

			if ( ! empty( $map[ $id ] ) ) {
				array_push( $stack, ...$map[ $id ] );
			}
		}

		return $count;
	}

	/**
	 * Get status icon and text.
	 *
	 * @since  2.8.0
	 * @param  string   $status   Post status.
	 * @param  \WP_Post $post_obj Post object.
	 * @return array
	 */
	private function get_status_info( $status, $post_obj ) {
		if ( ! empty( $post_obj->post_password ) ) {
			return array(
				'icon' => 'lock',
				'text' => __( 'Password Protected Doc', 'eazydocs' ),
			);
		}

		$map = array(
			'publish' => array(
				'icon' => 'admin-site-alt3',
				'text' => __( 'Public Doc', 'eazydocs' ),
			),
			'private' => array(
				'icon' => 'privacy',
				'text' => __( 'Private Doc', 'eazydocs' ),
			),
			'draft'   => array(
				'icon' => 'edit-page',
				'text' => __( 'Draft Doc', 'eazydocs' ),
			),
		);

		return isset( $map[ $status ] ) ? $map[ $status ] : $map['publish'];
	}

	/**
	 * Get visibility badge info for a doc.
	 *
	 * @since  2.8.0
	 * @param  int $post_id Post ID.
	 * @return array
	 */
	private function get_visibility_info( $post_id ) {
		$result = array(
			'isPrivate'        => false,
			'isDraft'          => false,
			'isProtected'      => false,
			'hasRoleVisibility' => false,
			'rolesList'        => '',
		);

		$status = get_post_status( $post_id );
		$post   = get_post( $post_id );

		if ( 'private' === $status ) {
			$result['isPrivate'] = true;

			// Check for role-based visibility (PRO MAX feature).
			if ( function_exists( 'ezd_is_promax' ) && ezd_is_promax() ) {
				$role_visibility_roles = get_post_meta( $post_id, 'ezd_role_visibility', true );
				if ( ! empty( $role_visibility_roles ) && is_array( $role_visibility_roles ) ) {
					$result['hasRoleVisibility'] = true;
					$roles_list = implode( ', ', array_slice( $role_visibility_roles, 0, 3 ) );
					if ( count( $role_visibility_roles ) > 3 ) {
						$roles_list .= '...';
					}
					$result['rolesList'] = $roles_list;
				}
			}
		} elseif ( 'draft' === $status ) {
			$result['isDraft'] = true;
		}

		if ( ! empty( $post->post_password ) ) {
			$result['isProtected'] = true;
		}

		return $result;
	}

	/**
	 * Get structured Pro action data for a given doc.
	 *
	 * Returns JSON-friendly data instead of raw HTML so React can
	 * render proper components without dangerouslySetInnerHTML.
	 *
	 * @since  2.9.0
	 * @param  int $post_id Post ID.
	 * @return array Associative array with structured pro actions data.
	 */
	private function get_pro_actions_data( $post_id ) {
		$result = array(
			'duplicate'  => null,
			'visibility' => null,
			'sidebar'    => null,
		);

		if ( ! ezd_is_premium() ) {
			return $result;
		}

		// Duplicate action data.
		$duplicate_nonce = wp_create_nonce( (string) $post_id );
		$duplicate_url   = add_query_arg(
			array(
				'action'    => 'doc_duplicate',
				'_wpnonce'  => $duplicate_nonce,
				'duplicate' => $post_id,
			),
			admin_url( 'admin.php' )
		);

		$result['duplicate'] = array(
			'url'   => esc_url( $duplicate_url ),
			'nonce' => $duplicate_nonce,
		);

		// Visibility action data.
		if ( current_user_can( 'manage_options' ) ) {
			$post              = get_post( $post_id );
			$current_visibility = 'publish';
			$current_password   = '';

			if ( $post instanceof \WP_Post ) {
				if ( 'private' === $post->post_status ) {
					$current_visibility = 'private';
				} elseif ( ! empty( $post->post_password ) ) {
					$current_visibility = 'protected';
					$current_password   = $post->post_password;
				}
			}

			$role_visibility_roles = array();
			$role_visibility_guest = false;

			if ( function_exists( 'ezd_is_premium' ) && ezd_is_premium() ) {
				$roles_meta = get_post_meta( $post_id, 'ezd_role_visibility', true );
				if ( is_array( $roles_meta ) ) {
					$role_visibility_guest = in_array( 'guest', $roles_meta, true );
					$role_visibility_roles = array_values( array_diff( $roles_meta, array( 'guest' ) ) );
				}
			}

			$visibility_nonce = wp_create_nonce( (string) $post_id );
			$visibility_url   = add_query_arg(
				array(
					'doc_visibility' => $post_id,
					'_wpnonce'       => $visibility_nonce,
				),
				admin_url( 'admin.php' )
			);

			$result['visibility'] = array(
				'url'                => esc_url( $visibility_url ),
				'nonce'              => $visibility_nonce,
				'currentVisibility'  => $current_visibility,
				'currentPassword'    => $current_password,
				'roleVisibilityRoles' => $role_visibility_roles,
				'roleVisibilityGuest' => $role_visibility_guest,
			);
		}

		// Sidebar action data.
		if ( current_user_can( 'manage_options' ) ) {
			$left_type     = get_post_meta( $post_id, 'ezd_doc_left_sidebar_type', true );
			$left_content  = get_post_meta( $post_id, 'ezd_doc_left_sidebar', true );
			$right_type    = get_post_meta( $post_id, 'ezd_doc_right_sidebar_type', true );
			$right_content = get_post_meta( $post_id, 'ezd_doc_right_sidebar', true );

			$result['sidebar'] = array(
				'leftType'     => $left_type ? $left_type : '',
				'leftContent'  => $left_content ? $left_content : '',
				'rightType'    => $right_type ? $right_type : '',
				'rightContent' => $right_content ? $right_content : '',
			);
		}

		return $result;
	}
}
