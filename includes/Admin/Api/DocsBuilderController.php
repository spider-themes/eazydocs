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


require_once __DIR__ . '/DocsBuilderNotificationsTrait.php';
require_once __DIR__ . '/DocsBuilderMutationsTrait.php';

/**
 * Class Doc_Builder_Controller
 *
 * @package EazyDocs\Admin\Api
 */
class Docs_Builder_Controller {


	use DocsBuilderNotificationsTrait;
	use DocsBuilderMutationsTrait;
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

	/** @var DocsBuilderDocuments|null */
	private $documents;

	private function documents() {
		if ( null === $this->documents ) {
			$this->documents = new DocsBuilderDocuments();
		}

		return $this->documents;
	}

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
					return $this->can_access_pro_notifications();
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

		// Rename a doc (inline title edit).
		register_rest_route(
			self::NAMESPACE,
			'/docs-builder/rename',
			array(
				'methods'             => 'POST',
				'callback'            => array( $this, 'rename_doc' ),
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
					return $this->can_access_pro_notifications();
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
		if ( current_user_can( 'manage_options' ) ) {
			return true;
		}

		$docs_capability = apply_filters( 'eazydocs_docs_capability', 'edit_docs' );
		if ( current_user_can( $docs_capability ) ) {
			return true;
		}

		$user         = wp_get_current_user();
		$user_roles   = ! empty( $user->roles ) ? (array) $user->roles : array();
		$author_roles = ezd_get_doc_author_roles();
		if ( array_intersect( $user_roles, $author_roles ) && current_user_can( 'edit_posts' ) ) {
			return true;
		}

		return false;
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
		$user_roles           = ! empty( $user->roles ) ? (array) $user->roles : array();
		$user_role            = reset( $user_roles ) ?: '';
		$settings_edit_access = ezd_get_opt( 'settings-edit-access' );

		if ( ! is_array( $settings_edit_access ) ) {
			$settings_edit_access = array( 'administrator' );
		}

		if ( ! function_exists( 'is_plugin_active' ) ) {
			include_once ABSPATH . 'wp-admin/includes/plugin.php';
		}
		$antimanual_active = function_exists( 'is_plugin_active' ) && ( is_plugin_active( 'antimanual/antimanual.php' ) || is_plugin_active( 'antimanual-pro/antimanual.php' ) );

		$is_admin       = current_user_can( 'manage_options' );
		$author_roles   = ezd_get_doc_author_roles();
		$is_author_role = ! empty( array_intersect( $user_roles, $author_roles ) );
		$can_publish    = current_user_can( 'publish_docs' ) || $is_admin || ( $is_author_role && current_user_can( 'publish_posts' ) );
		$can_edit       = current_user_can( 'edit_docs' ) || $is_admin || $is_author_role;

		$data = array(
			'capabilities'      => array(
				'canPublishDocs'   => $can_publish,
				'canManageOptions' => $is_admin,
				'canEditDocs'      => $can_edit,
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
				'assetsUrl'         => esc_url( EZD_ASSETS ),
				'settingsIcon'      => esc_url( EZD_IMG ) . '/admin/admin-settings.svg',
				'notificationIcon'  => esc_url( EZD_IMG ) . '/admin/notification.svg',
				'proIcon'           => esc_url( EZD_IMG ) . '/admin/pro-icon.png',
				'folderOpenIcon'    => esc_url( EZD_IMG ) . '/icon/folder-open.png',
			),
			'nonces'            => array(
				'parentDoc'    => wp_create_nonce( 'parent_doc_nonce' ),
				'adminNonce'   => wp_create_nonce( 'eazydocs-admin-nonce' ),
				'notification' => wp_create_nonce( 'ezd_notification_nonce' ),
				'rest'         => wp_create_nonce( 'wp_rest' ),
				'restUrl'      => esc_url_raw( rest_url() ),
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
		$parent_docs = $this->documents()->get_parent_docs();
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
		$children_map = array();
		$parent_id    = absint( $request->get_param( 'parent_id' ) );

		if ( $parent_id > 0 ) {
			if ( 'docs' !== get_post_type( $parent_id ) || ! ezd_can_read_doc_content( $parent_id ) ) {
				return new \WP_Error( 'forbidden', __( 'You do not have permission to read this document.', 'eazydocs' ), [ 'status' => 403 ] );
			}
			$children_map[ $parent_id ] = $this->documents()->get_branch_children( $parent_id );
			return new \WP_REST_Response( $children_map, 200 );
		}

		$parent_docs = $this->documents()->get_parent_docs();

		foreach ( $parent_docs as $parent ) {
			$children_map[ $parent['id'] ] = $this->documents()->get_children_tree( $parent['id'] );
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

		$notification_count = $this->get_unread_notification_count();

		$data = array(
			'trashCount'        => isset( $trash_count->trash ) ? (int) $trash_count->trash : 0,
			'notificationCount' => $notification_count,
		);

		return new \WP_REST_Response( $data, 200 );
	}

	/**
	 * Return every piece of data the React builder needs.
	 *
	 * @since  2.8.0
	 * @param  \WP_REST_Request $request REST request.
	 * @return \WP_REST_Response
	 */
	public function get_builder_data( $request ) {
		$parent_docs  = $this->documents()->get_parent_docs();
		$children_map = array();
		$active_doc   = absint( $request->get_param( 'active_doc' ) );
		$active_doc_exists = false;

		foreach ( $parent_docs as $parent ) {
			if ( (int) $parent['id'] === $active_doc ) {
				$active_doc_exists = true;
				break;
			}
		}

		if ( ! $active_doc_exists && ! empty( $parent_docs ) ) {
			$active_doc = (int) $parent_docs[0]['id'];
		}

		if ( $active_doc > 0 ) {
			$children_map[ $active_doc ] = $this->documents()->get_children_tree( $active_doc );
		}

		$trash_count = wp_count_posts( 'docs' );

		// Current user capabilities.
		$user                 = wp_get_current_user();
		$user_roles           = ! empty( $user->roles ) ? (array) $user->roles : array();
		$user_role            = reset( $user_roles ) ?: '';
		$settings_edit_access = ezd_get_opt( 'settings-edit-access' );

		if ( ! is_array( $settings_edit_access ) ) {
			$settings_edit_access = array( 'administrator' );
		}

		// Check if Antimanual is active.
		if ( ! function_exists( 'is_plugin_active' ) ) {
			include_once ABSPATH . 'wp-admin/includes/plugin.php';
		}
		$antimanual_active = function_exists( 'is_plugin_active' ) && ( is_plugin_active( 'antimanual/antimanual.php' ) || is_plugin_active( 'antimanual-pro/antimanual.php' ) );

		// Get unread notification count for Pro users.
		$notification_count = $this->get_unread_notification_count();

		// Get role visibility configuration.
		$role_visibility_config = $this->get_role_visibility_config();

		$is_admin       = current_user_can( 'manage_options' );
		$author_roles   = ezd_get_doc_author_roles();
		$is_author_role = ! empty( array_intersect( $user_roles, $author_roles ) );
		$can_publish    = current_user_can( 'publish_docs' ) || $is_admin || ( $is_author_role && current_user_can( 'publish_posts' ) );
		$can_edit       = current_user_can( 'edit_docs' ) || $is_admin || $is_author_role;

		$data = array(
			'parentDocs'        => $parent_docs,
			'childrenMap'       => $children_map,
			'capabilities'      => array(
				'canPublishDocs'   => $can_publish,
				'canManageOptions' => $is_admin,
				'canEditDocs'      => $can_edit,
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
				'assetsUrl'         => esc_url( EZD_ASSETS ),
				'settingsIcon'      => esc_url( EZD_IMG ) . '/admin/admin-settings.svg',
				'notificationIcon'  => esc_url( EZD_IMG ) . '/admin/notification.svg',
				'proIcon'           => esc_url( EZD_IMG ) . '/admin/pro-icon.png',
				'folderOpenIcon'    => esc_url( EZD_IMG ) . '/icon/folder-open.png',
			),
			'nonces'            => array(
				'parentDoc'    => wp_create_nonce( 'parent_doc_nonce' ),
				'adminNonce'   => wp_create_nonce( 'eazydocs-admin-nonce' ),
				'notification' => wp_create_nonce( 'ezd_notification_nonce' ),
				'rest'         => wp_create_nonce( 'wp_rest' ),
				'restUrl'      => esc_url_raw( rest_url() ),
			),
			'currentTheme'      => get_template(),
		);

		return new \WP_REST_Response( $data, 200 );
	}

	/**
	 * Get the current nesting depth for a doc item.
	 *
	 * Direct children of a parent doc have depth 1.
	 *
	 * @since  2.9.0
	 * @param  int $post_id Post ID.
	 * @return int
	 */
	private function get_doc_depth( $post_id ) {
		$ancestors = get_post_ancestors( $post_id );
		return max( 1, count( $ancestors ) );
	}

	/**
	 * Get the top-level parent doc ID for a nested doc.
	 *
	 * @since  2.9.0
	 * @param  int $post_id Post ID.
	 * @return int
	 */
	private function get_root_parent_id( $post_id ) {
		$ancestors = get_post_ancestors( $post_id );
		return ! empty( $ancestors ) ? (int) end( $ancestors ) : (int) $post_id;
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

}
