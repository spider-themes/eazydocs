<?php
/**
 * REST API controller for the Docs Builder React page.
 *
 * Provides all data needed to render the builder UI as a single JSON response
 * so the React front-end can hydrate entirely from one request.
 *
 * @package EazyDocs\Admin\Api
 * @since   2.8.0
 */

namespace EazyDocs\Admin\Api;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Class Doc_Builder_Controller
 *
 * @package EazyDocs\Admin\Api
 */
class Docs_Builder_Controller {

	/**
	 * REST namespace.
	 *
	 * @var string
	 */
	const NAMESPACE = 'eazydocs/v1';

	/**
	 * Replacements for special characters in doc titles.
	 *
	 * @var array
	 */
	private static $replacements = array( 'ezd_ampersand' => '&', 'ezd_hash' => '#', 'ezd_plus' => '+' );

	/**
	 * Register the REST routes.
	 *
	 * @since  2.8.0
	 * @return void
	 */
	public function register_routes() {
		register_rest_route(
			self::NAMESPACE,
			'/docs-builder',
			array(
				'methods'             => 'GET',
				'callback'            => array( $this, 'get_builder_data' ),
				'permission_callback' => array( $this, 'check_permission' ),
			)
		);

		// Settings.
		register_rest_route(
			self::NAMESPACE,
			'/docs-builder/settings',
			array(
				'methods'             => 'GET',
				'callback'            => array( $this, 'get_settings_data' ),
				'permission_callback' => array( $this, 'check_permission' ),
			)
		);

		// Parent docs.
		register_rest_route(
			self::NAMESPACE,
			'/docs-builder/parents',
			array(
				'methods'             => 'GET',
				'callback'            => array( $this, 'get_parent_docs_data' ),
				'permission_callback' => array( $this, 'check_permission' ),
			)
		);

		// Child docs map (sections and sub-docs).
		register_rest_route(
			self::NAMESPACE,
			'/docs-builder/children',
			array(
				'methods'             => 'GET',
				'callback'            => array( $this, 'get_child_docs_data' ),
				'permission_callback' => array( $this, 'check_permission' ),
			)
		);

		// Notification and item counts.
		register_rest_route(
			self::NAMESPACE,
			'/docs-builder/counts',
			array(
				'methods'             => 'GET',
				'callback'            => array( $this, 'get_counts_data' ),
				'permission_callback' => array( $this, 'check_permission' ),
			)
		);

		// Mark notification item as read.
		register_rest_route(
			self::NAMESPACE,
			'/docs-builder/mark-read',
			array(
				'methods'             => 'POST',
				'callback'            => array( $this, 'mark_notification_read' ),
				'permission_callback' => function () {
					return current_user_can( 'manage_options' );
				},
			)
		);

		// Create parent doc.
		register_rest_route(
			self::NAMESPACE,
			'/docs-builder/create-parent',
			array(
				'methods'             => 'POST',
				'callback'            => array( $this, 'create_parent_doc' ),
				'permission_callback' => array( $this, 'check_publish_permission' ),
			)
		);

		// Create section under parent.
		register_rest_route(
			self::NAMESPACE,
			'/docs-builder/create-section',
			array(
				'methods'             => 'POST',
				'callback'            => array( $this, 'create_section' ),
				'permission_callback' => array( $this, 'check_publish_permission' ),
			)
		);

		// Create child doc under section.
		register_rest_route(
			self::NAMESPACE,
			'/docs-builder/create-child',
			array(
				'methods'             => 'POST',
				'callback'            => array( $this, 'create_child' ),
				'permission_callback' => array( $this, 'check_publish_permission' ),
			)
		);

		// Delete (trash) a doc.
		register_rest_route(
			self::NAMESPACE,
			'/docs-builder/delete',
			array(
				'methods'             => 'POST',
				'callback'            => array( $this, 'delete_doc' ),
				'permission_callback' => array( $this, 'check_permission' ),
			)
		);

		// Notifications (paginated JSON).
		register_rest_route(
			self::NAMESPACE,
			'/docs-builder/notifications',
			array(
				'methods'             => 'GET',
				'callback'            => array( $this, 'get_notifications' ),
				'permission_callback' => function () {
					return current_user_can( 'manage_options' );
				},
			)
		);

		// Visibility update (AJAX replacement).
		register_rest_route(
			self::NAMESPACE,
			'/docs-builder/update-visibility',
			array(
				'methods'             => 'POST',
				'callback'            => array( $this, 'update_visibility' ),
				'permission_callback' => function () {
					return current_user_can( 'manage_options' );
				},
			)
		);

		// Duplicate doc.
		register_rest_route(
			self::NAMESPACE,
			'/docs-builder/duplicate',
			array(
				'methods'             => 'POST',
				'callback'            => array( $this, 'duplicate_doc' ),
				'permission_callback' => function () {
					return current_user_can( 'manage_options' );
				},
			)
		);

		// Update sidebar.
		register_rest_route(
			self::NAMESPACE,
			'/docs-builder/update-sidebar',
			array(
				'methods'             => 'POST',
				'callback'            => array( $this, 'update_sidebar' ),
				'permission_callback' => function () {
					return current_user_can( 'manage_options' );
				},
			)
		);
	}

	/**
	 * Permission check – mirrors the menu capability.
	 *
	 * @since  2.8.0
	 * @return bool
	 */
	public function check_permission() {
		$docs_capability = apply_filters( 'eazydocs_docs_capability', 'edit_docs' );
		return current_user_can( $docs_capability );
	}

	/**
	 * Get Settings Data for Docs Builder.
	 *
	 * @since  2.9.0
	 * @param  \WP_REST_Request $request REST request.
	 * @return \WP_REST_Response
	 */
	public function get_settings_data( $request ) {
		$user                 = wp_get_current_user();
		$user_roles           = ! empty( $user->roles ) ? $user->roles : array();
		$user_role            = array_shift( $user_roles );
		$settings_edit_access = ezd_get_opt( 'settings-edit-access' );

		if ( ! is_array( $settings_edit_access ) ) {
			$settings_edit_access = array( 'administrator' );
		}

		if ( ! function_exists( 'is_plugin_active' ) ) {
			include_once ABSPATH . 'wp-admin/includes/plugin.php';
		}
		$antimanual_active = function_exists( 'is_plugin_active' ) && ( is_plugin_active( 'antimanual/antimanual.php' ) || is_plugin_active( 'antimanual-pro/antimanual.php' ) );

		$data = array(
			'capabilities'      => array(
				'canPublishDocs'   => current_user_can( 'publish_docs' ),
				'canManageOptions' => current_user_can( 'manage_options' ),
				'canEditDocs'      => current_user_can( 'edit_docs' ),
				'hasSettingsAccess' => is_array( $settings_edit_access ) && in_array( $user_role, $settings_edit_access, true ),
			),
			'isPremium'         => ezd_is_premium(),
			'antimanualActive'  => $antimanual_active,
			'roleVisibility'    => $this->get_role_visibility_config(),
			'urls'              => array(
				'adminUrl'          => admin_url(),
				'classicUi'         => admin_url( 'edit.php?post_type=docs' ),
				'settings'          => admin_url( 'admin.php?page=eazydocs-settings' ),
				'trash'             => admin_url( 'edit.php?post_status=trash&post_type=docs' ),
				'antimanualDocs'    => admin_url( 'admin.php?page=atml-docs' ),
				'pricing'           => admin_url( 'admin.php?page=eazydocs-pricing' ),
				'assetsUrl'         => esc_url( EAZYDOCS_ASSETS ),
				'settingsIcon'      => esc_url( EAZYDOCS_IMG ) . '/admin/admin-settings.svg',
				'notificationIcon'  => esc_url( EAZYDOCS_IMG ) . '/admin/notification.svg',
				'proIcon'           => esc_url( EAZYDOCS_IMG ) . '/admin/pro-icon.png',
				'folderOpenIcon'    => esc_url( EAZYDOCS_IMG ) . '/icon/folder-open.png',
			),
			'nonces'            => array(
				'parentDoc'    => wp_create_nonce( 'parent_doc_nonce' ),
				'adminNonce'   => wp_create_nonce( 'eazydocs-admin-nonce' ),
				'notification' => wp_create_nonce( 'ezd_notification_nonce' ),
			),
			'currentTheme'      => get_template(),
		);

		return new \WP_REST_Response( $data, 200 );
	}

	/**
	 * Get Parent Docs for Docs Builder.
	 *
	 * @since  2.9.0
	 * @param  \WP_REST_Request $request REST request.
	 * @return \WP_REST_Response
	 */
	public function get_parent_docs_data( $request ) {
		$parent_docs = $this->get_parent_docs();
		return new \WP_REST_Response( $parent_docs, 200 );
	}

	/**
	 * Get Child Docs Map for Docs Builder.
	 *
	 * @since  2.9.0
	 * @param  \WP_REST_Request $request REST request.
	 * @return \WP_REST_Response
	 */
	public function get_child_docs_data( $request ) {
		$parent_docs  = $this->get_parent_docs();
		$children_map = array();

		foreach ( $parent_docs as $parent ) {
			$children_map[ $parent['id'] ] = $this->get_children_tree( $parent['id'] );
		}
		return new \WP_REST_Response( $children_map, 200 );
	}

	/**
	 * Get Notification Counts and Trash Count.
	 *
	 * @since  2.9.0
	 * @param  \WP_REST_Request $request REST request.
	 * @return \WP_REST_Response
	 */
	public function get_counts_data( $request ) {
		$trash_count = wp_count_posts( 'docs' );
		
		$notification_count = 0;
		if ( ezd_is_premium() && current_user_can( 'manage_options' ) ) {
			if ( function_exists( 'eazydocs_voted' ) && function_exists( 'ezd_comment_count' ) ) {
				$notification_count = eazydocs_voted() + ezd_comment_count();
			}
		}

		$data = array(
			'trashCount'        => isset( $trash_count->trash ) ? (int) $trash_count->trash : 0,
			'notificationCount' => $notification_count,
		);

		return new \WP_REST_Response( $data, 200 );
	}

	/**
	 * Mark a specific notification item as read.
	 *
	 * @since  2.9.0
	 * @param  \WP_REST_Request $request REST request.
	 * @return \WP_REST_Response
	 */
	public function mark_notification_read( $request ) {
		$type    = sanitize_text_field( $request->get_param( 'type' ) );
		$post_id = absint( $request->get_param( 'postId' ) );
		$timestamp = absint( $request->get_param( 'timestamp' ) );

		if ( ! $type || ! $post_id ) {
			return new \WP_Error( 'missing_data', 'Missing required data', array( 'status' => 400 ) );
		}

		if ( 'vote' === $type ) {
			// For votes, we log that this post's vote at this timestamp was read.
			$read_votes = get_user_meta( get_current_user_id(), '_ezd_read_votes', true );
			if ( ! is_array( $read_votes ) ) {
				$read_votes = array();
			}
			$key = $post_id . '_' . $timestamp;
			if ( ! in_array( $key, $read_votes, true ) ) {
				$read_votes[] = $key;
				update_user_meta( get_current_user_id(), '_ezd_read_votes', $read_votes );
			}
		} elseif ( 'comment' === $type ) {
			// For comments, post_id is actually the comment ID in the notification context.
			$comment_id = $post_id;
			update_comment_meta( $comment_id, '_ezd_is_read', 1 );
		}

		return new \WP_REST_Response( array( 'success' => true ), 200 );
	}

	/**
	 * Return every piece of data the React builder needs.
	 *
	 * @since  2.8.0
	 * @param  \WP_REST_Request $request REST request.
	 * @return \WP_REST_Response
	 */
	public function get_builder_data( $request ) {
		$parent_docs  = $this->get_parent_docs();
		$children_map = array();

		foreach ( $parent_docs as $parent ) {
			$children_map[ $parent['id'] ] = $this->get_children_tree( $parent['id'] );
		}

		$trash_count = wp_count_posts( 'docs' );

		// Current user capabilities.
		$user                 = wp_get_current_user();
		$user_roles           = ! empty( $user->roles ) ? $user->roles : array();
		$user_role            = array_shift( $user_roles );
		$settings_edit_access = ezd_get_opt( 'settings-edit-access' );

		if ( ! is_array( $settings_edit_access ) ) {
			$settings_edit_access = array( 'administrator' );
		}

		// Check if Antimanual is active.
		if ( ! function_exists( 'is_plugin_active' ) ) {
			include_once ABSPATH . 'wp-admin/includes/plugin.php';
		}
		$antimanual_active = function_exists( 'is_plugin_active' ) && ( is_plugin_active( 'antimanual/antimanual.php' ) || is_plugin_active( 'antimanual-pro/antimanual.php' ) );

		// Get notification count for Pro users.
		$notification_count = 0;
		if ( ezd_is_premium() && current_user_can( 'manage_options' ) ) {
			if ( function_exists( 'eazydocs_voted' ) && function_exists( 'ezd_comment_count' ) ) {
				$notification_count = eazydocs_voted() + ezd_comment_count();
			}
		}

		// Get role visibility configuration.
		$role_visibility_config = $this->get_role_visibility_config();

		$data = array(
			'parentDocs'        => $parent_docs,
			'childrenMap'       => $children_map,
			'capabilities'      => array(
				'canPublishDocs'   => current_user_can( 'publish_docs' ),
				'canManageOptions' => current_user_can( 'manage_options' ),
				'canEditDocs'      => current_user_can( 'edit_docs' ),
				'hasSettingsAccess' => is_array( $settings_edit_access ) && in_array( $user_role, $settings_edit_access, true ),
			),
			'isPremium'         => ezd_is_premium(),
			'antimanualActive'  => $antimanual_active,
			'trashCount'        => isset( $trash_count->trash ) ? (int) $trash_count->trash : 0,
			'notificationCount' => $notification_count,
			'roleVisibility'    => $role_visibility_config,
			'urls'              => array(
				'adminUrl'          => admin_url(),
				'classicUi'         => admin_url( 'edit.php?post_type=docs' ),
				'settings'          => admin_url( 'admin.php?page=eazydocs-settings' ),
				'trash'             => admin_url( 'edit.php?post_status=trash&post_type=docs' ),
				'antimanualDocs'    => admin_url( 'admin.php?page=atml-docs' ),
				'pricing'           => admin_url( 'admin.php?page=eazydocs-pricing' ),
				'assetsUrl'         => esc_url( EAZYDOCS_ASSETS ),
				'settingsIcon'      => esc_url( EAZYDOCS_IMG ) . '/admin/admin-settings.svg',
				'notificationIcon'  => esc_url( EAZYDOCS_IMG ) . '/admin/notification.svg',
				'proIcon'           => esc_url( EAZYDOCS_IMG ) . '/admin/pro-icon.png',
				'folderOpenIcon'    => esc_url( EAZYDOCS_IMG ) . '/icon/folder-open.png',
			),
			'nonces'            => array(
				'parentDoc'    => wp_create_nonce( 'parent_doc_nonce' ),
				'adminNonce'   => wp_create_nonce( 'eazydocs-admin-nonce' ),
				'notification' => wp_create_nonce( 'ezd_notification_nonce' ),
			),
			'currentTheme'      => get_template(),
		);

		return new \WP_REST_Response( $data, 200 );
	}

	/**
	 * Get all parent (top-level) docs.
	 *
	 * @since  2.8.0
	 * @return array
	 */
	private function get_parent_docs() {
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

			$post_id     = get_the_ID();
			$post_status = get_post_status( $post_id );
			$post_obj    = get_post( $post_id );

			// Determine status icon.
			$status_info = $this->get_status_info( $post_status, $post_obj );

			// Child count.
			$child_pages = get_pages(
				array(
					'child_of'    => $post_id,
					'post_type'   => 'docs',
					'post_status' => array( 'publish', 'draft', 'private' ),
				)
			);

			// Get Pro action structured data for parent docs.
			$pro_data = $this->get_pro_actions_data( $post_id );

			$docs[] = array(
				'id'              => $post_id,
				'title'           => html_entity_decode( $post_obj->post_title ),
				'permalink'       => get_permalink(),
				'editLink'        => get_edit_post_link( $post_id, 'raw' ),
				'status'          => $post_status,
				'statusIcon'      => $status_info['icon'],
				'statusText'      => $status_info['text'],
				'hasPassword'     => ! empty( $post_obj->post_password ),
				'childCount'      => count( $child_pages ),
				'canEdit'         => ezd_is_admin_or_editor( $post_id, 'edit' ),
				'canDelete'       => ezd_is_admin_or_editor( $post_id, 'delete' ),
				'deleteNonce'     => wp_create_nonce( 'ezd_delete_doc_' . $post_id ),
				'sectionNonce'    => wp_create_nonce( 'ezd_create_section_' . $post_id ),
				'proActions'      => $pro_data,
			);
		}

		wp_reset_postdata();

		return $docs;
	}

	/**
	 * Recursively build the children tree for a parent doc.
	 *
	 * @since  2.8.0
	 * @param  int $parent_id Parent post ID.
	 * @param  int $depth     Current depth (1-indexed).
	 * @return array
	 */
	private function get_children_tree( $parent_id, $depth = 1 ) {
		$children = get_children(
			array(
				'post_parent' => $parent_id,
				'post_type'   => 'docs',
				'orderby'     => 'menu_order',
				'order'       => 'ASC',
			)
		);

		// Remove the thumbnail ID if it happens to be in children.
		$thumbnail_id = get_post_thumbnail_id( $parent_id );
		if ( $thumbnail_id && isset( $children[ $thumbnail_id ] ) ) {
			unset( $children[ $thumbnail_id ] );
		}

		$results = array();

		foreach ( $children as $child ) {
			$post_status = $child->post_status;
			if ( ! empty( $child->post_password ) ) {
				$post_status = 'protected';
			}

			$sub_children = eaz_get_nestable_children( $child->ID );
			$has_children = ! empty( $sub_children );

			// Determine child count.
			$child_pages = get_pages(
				array(
					'child_of'    => $child->ID,
					'post_type'   => 'docs',
					'post_status' => array( 'publish', 'draft', 'private' ),
				)
			);

			// Positive / negative votes.
			$positive = (int) get_post_meta( $child->ID, 'positive', true );
			$negative = (int) get_post_meta( $child->ID, 'negative', true );

			// Visibility badge info.
			$visibility = $this->get_visibility_info( $child->ID );

			// Determine if adding child is possible at this depth.
			$can_add_sub = true;
			if ( ! ezd_is_premium() && 3 === $depth ) {
				$can_add_sub = false;
			}
			if ( ezd_is_premium() && 4 === $depth ) {
				$can_add_sub = false;
			}

			// Get Pro action structured data for child docs.
			$pro_data = $this->get_pro_actions_data( $child->ID );

			$item = array(
				'id'              => $child->ID,
				'title'           => html_entity_decode( $child->post_title ),
				'permalink'       => get_permalink( $child->ID ),
				'editLink'        => admin_url( 'post.php' ) . '?post=' . $child->ID . '&action=edit',
				'status'          => $post_status,
				'hasPassword'     => ! empty( $child->post_password ),
				'hasChildren'     => $has_children,
				'childCount'      => count( $child_pages ),
				'positive'        => $positive,
				'negative'        => $negative,
				'visibility'      => $visibility,
				'canEdit'         => ezd_is_admin_or_editor( $child->ID, 'edit' ),
				'canDelete'       => ezd_is_admin_or_editor( $child->ID, 'delete' ),
				'canAddChild'     => $can_add_sub,
				'deleteNonce'     => wp_create_nonce( 'ezd_delete_doc_' . $child->ID ),
				'childNonce'      => wp_create_nonce( 'ezd_create_child_' . $child->ID ),
				'depth'           => $depth,
				'children'        => array(),
				'proActions'      => $pro_data,
			);

			// Recurse if children exist and depth allows.
			$max_depth = ezd_is_premium() ? 4 : 3;
			if ( $has_children && $depth < $max_depth ) {
				$item['children'] = $this->get_children_tree( $child->ID, $depth + 1 );
			}

			$results[] = $item;
		}

		return $results;
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
				'text' => __( 'Drafted Doc', 'eazydocs' ),
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

	/**
	 * Get role visibility configuration for the React frontend.
	 *
	 * @since  2.9.0
	 * @return array
	 */
	private function get_role_visibility_config() {
		$config = array(
			'enabled' => false,
			'roles'   => array(),
		);

		if ( ! ezd_is_premium() ) {
			return $config;
		}

		// Check if role visibility is enabled in Pro.
		if ( function_exists( 'ezd_is_promax' ) && ezd_is_promax() ) {
			$config['enabled'] = true;
		}

		// Get all WordPress roles.
		global $wp_roles;
		if ( $wp_roles ) {
			foreach ( $wp_roles->role_names as $slug => $name ) {
				$config['roles'][] = array(
					'slug' => $slug,
					'name' => translate_user_role( $name ),
				);
			}
		}

		return $config;
	}

	/**
	 * Get paginated notifications as JSON.
	 *
	 * @since  2.9.0
	 * @param  \WP_REST_Request $request REST request.
	 * @return \WP_REST_Response
	 */
	public function get_notifications( $request ) {
		$page     = absint( $request->get_param( 'page' ) ) ?: 1;
		$per_page = absint( $request->get_param( 'per_page' ) ) ?: 10;
		$filter   = sanitize_text_field( $request->get_param( 'filter' ) ) ?: 'all';

		$allowed_filters = array( 'all', 'comment', 'vote' );
		if ( ! in_array( $filter, $allowed_filters, true ) ) {
			$filter = 'all';
		}

		$total_items = array();

		$read_votes = get_user_meta( get_current_user_id(), '_ezd_read_votes', true );
		if ( ! is_array( $read_votes ) ) {
			$read_votes = array();
		}

		// Get votes if filter allows.
		if ( 'all' === $filter || 'vote' === $filter ) {
			$args = array(
				'post_type'      => 'docs',
				'posts_per_page' => -1,
				'post_status'    => array( 'publish' ),
			);

			foreach ( get_posts( $args ) as $post ) {
				$positive_time = get_post_meta( $post->ID, 'positive_time', true );
				if ( ! empty( $positive_time ) ) {
					$time = strtotime( $positive_time );
					$is_read = in_array( $post->ID . '_' . $time, $read_votes, true );
					$total_items[] = array(
						'type'      => 'vote',
						'voteType'  => 'positive',
						'postId'    => $post->ID,
						'postTitle' => html_entity_decode( $post->post_title ),
						'permalink' => get_permalink( $post->ID ),
						'thumbnail' => get_the_post_thumbnail_url( $post->ID, 'thumbnail' ),
						'timestamp' => $time,
						'timeAgo'   => human_time_diff( $time, time() ) . __( ' ago', 'eazydocs' ),
						'isRead'    => $is_read,
					);
				}

				$negative_time = get_post_meta( $post->ID, 'negative_time', true );
				if ( ! empty( $negative_time ) ) {
					$time = strtotime( $negative_time );
					$is_read = in_array( $post->ID . '_' . $time, $read_votes, true );
					$total_items[] = array(
						'type'      => 'vote',
						'voteType'  => 'negative',
						'postId'    => $post->ID,
						'postTitle' => html_entity_decode( $post->post_title ),
						'permalink' => get_permalink( $post->ID ),
						'thumbnail' => get_the_post_thumbnail_url( $post->ID, 'thumbnail' ),
						'timestamp' => $time,
						'timeAgo'   => human_time_diff( $time, time() ) . __( ' ago', 'eazydocs' ),
						'isRead'    => $is_read,
					);
				}
			}
		}

		// Get comments if filter allows.
		if ( 'all' === $filter || 'comment' === $filter ) {
			$comments = get_comments(
				array(
					'post_status' => 'publish',
					'post_type'   => array( 'docs' ),
					'parent'      => 0,
					'order'       => 'desc',
					'number'      => 100,
				)
			);

			foreach ( $comments as $comment ) {
				$is_read = (bool) get_comment_meta( $comment->comment_ID, '_ezd_is_read', true );
				$total_items[] = array(
					'type'          => 'comment',
					'commentId'     => (int) $comment->comment_ID,
					'author'        => $comment->comment_author,
					'avatar'        => get_avatar_url( $comment, array( 'size' => 40 ) ),
					'postId'        => (int) $comment->comment_post_ID,
					'postTitle'     => html_entity_decode( get_the_title( $comment->comment_post_ID ) ),
					'commentLink'   => get_comment_link( $comment ),
					'timestamp'     => strtotime( $comment->comment_date ),
					'timeAgo'       => human_time_diff( strtotime( $comment->comment_date ), time() ) . __( ' ago', 'eazydocs' ),
					'isRead'        => $is_read,
				);
			}
		}

		// Sort by timestamp descending.
		usort(
			$total_items,
			function ( $a, $b ) {
				return $b['timestamp'] - $a['timestamp'];
			}
		);

		$total   = count( $total_items );
		$offset  = ( $page - 1 ) * $per_page;
		$items   = array_slice( $total_items, $offset, $per_page );
		$hasMore = ( $offset + $per_page ) < $total;

		return new \WP_REST_Response(
			array(
				'items'   => $items,
				'hasMore' => $hasMore,
				'total'   => $total,
				'page'    => $page,
			),
			200
		);
	}

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

		if ( ! $doc_id || ! get_post( $doc_id ) ) {
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

		if ( ! $doc_id || ! get_post( $doc_id ) ) {
			return new \WP_Error( 'invalid_doc', __( 'Invalid document.', 'eazydocs' ), array( 'status' => 400 ) );
		}

		// Check that the Pro duplicate helpers are available.
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
		$left_type     = sanitize_text_field( $request->get_param( 'leftType' ) );
		$left_content  = wp_kses_post( wp_unslash( $request->get_param( 'leftContent' ) ) );
		$right_type    = sanitize_text_field( $request->get_param( 'rightType' ) );
		$right_content = wp_kses_post( wp_unslash( $request->get_param( 'rightContent' ) ) );

		if ( ! $doc_id || ! get_post( $doc_id ) ) {
			return new \WP_Error( 'invalid_doc', __( 'Invalid document.', 'eazydocs' ), array( 'status' => 400 ) );
		}

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
	 * @return bool
	 */
	public function check_publish_permission() {
		return current_user_can( 'publish_docs' );
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
				'post_status'  => 'publish',
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

		if ( empty( $title ) ) {
			return new \WP_Error( 'missing_title', __( 'A title is required.', 'eazydocs' ), array( 'status' => 400 ) );
		}

		if ( ! $parent_id || ! get_post( $parent_id ) ) {
			return new \WP_Error( 'invalid_parent', __( 'The specified parent document does not exist.', 'eazydocs' ), array( 'status' => 400 ) );
		}

		if ( ! current_user_can( 'edit_post', $parent_id ) ) {
			return new \WP_Error( 'forbidden', __( 'You do not have permission to edit this document.', 'eazydocs' ), array( 'status' => 403 ) );
		}

		$title    = $this->decode_special_chars( $title );
		$children = get_children(
			array(
				'post_parent' => $parent_id,
				'post_type'   => 'docs',
			)
		);

		$status = ezd_is_premium() ? get_post_status( $parent_id ) : 'publish';

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
					'id' => $post_id,
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

		if ( empty( $title ) ) {
			return new \WP_Error( 'missing_title', __( 'A title is required.', 'eazydocs' ), array( 'status' => 400 ) );
		}

		$parent_post = $parent_id > 0 ? get_post( $parent_id ) : null;

		if ( ! $parent_post || 'docs' !== $parent_post->post_type ) {
			return new \WP_Error( 'invalid_parent', __( 'The specified document does not exist.', 'eazydocs' ), array( 'status' => 400 ) );
		}

		if ( ! current_user_can( 'edit_post', $parent_id ) ) {
			return new \WP_Error( 'forbidden', __( 'You do not have permission to edit this document.', 'eazydocs' ), array( 'status' => 403 ) );
		}

		$title    = $this->decode_special_chars( $title );
		$children = get_children(
			array(
				'post_parent' => $parent_id,
				'post_type'   => 'docs',
			)
		);

		$status = ezd_is_premium() ? get_post_status( $parent_id ) : 'publish';

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
					'id' => $post_id,
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

		if ( ! $doc_id || ! get_post( $doc_id ) ) {
			return new \WP_Error( 'invalid_doc', __( 'The specified document does not exist.', 'eazydocs' ), array( 'status' => 400 ) );
		}

		if ( ! function_exists( 'ezd_perform_edit_delete_actions' ) || ! ezd_perform_edit_delete_actions( 'delete', $doc_id ) ) {
			return new \WP_Error( 'forbidden', __( 'You do not have permission to delete this document.', 'eazydocs' ), array( 'status' => 403 ) );
		}

		// Collect all descendant IDs recursively.
		$all_ids = $this->collect_descendant_ids( $doc_id );
		array_unshift( $all_ids, $doc_id );

		$trashed = 0;

		foreach ( $all_ids as $id ) {
			if ( get_post( $id ) ) {
				wp_trash_post( $id );
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
	 * Recursively collect all descendant post IDs.
	 *
	 * @since  2.8.0
	 * @param  int $parent_id Parent post ID.
	 * @return array
	 */
	private function collect_descendant_ids( $parent_id ) {
		$ids      = array();
		$children = get_children(
			array(
				'post_parent' => $parent_id,
				'post_type'   => 'docs',
			)
		);

		foreach ( $children as $child ) {
			$ids[] = $child->ID;
			$ids   = array_merge( $ids, $this->collect_descendant_ids( $child->ID ) );
		}

		return $ids;
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
