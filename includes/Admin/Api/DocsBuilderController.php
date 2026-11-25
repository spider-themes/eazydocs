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

	/**
	 * Get the unread notification count for the Docs Builder header.
	 *
	 * Counts unread vote and comment notifications using the same read-state
	 * sources as the builder notification list.
	 *
	 * @since  2.9.0
	 * @return int
	 */
	private function get_unread_notification_count() {
		if ( ! $this->can_access_pro_notifications() ) {
			return 0;
		}

		$unread_count = 0;
		$read_votes   = get_user_meta( get_current_user_id(), '_ezd_read_votes', true );

		if ( ! is_array( $read_votes ) ) {
			$read_votes = array();
		}

		// Only the two vote timestamps are needed, so read them straight from
		// postmeta instead of loading every published doc (content, meta, terms).
		global $wpdb;
		// phpcs:ignore WordPress.DB.DirectDatabaseQuery
		$vote_rows = $wpdb->get_results(
			"SELECT pm.post_id, pm.meta_value
			 FROM {$wpdb->postmeta} pm
			 INNER JOIN {$wpdb->posts} p ON p.ID = pm.post_id
			 WHERE pm.meta_key IN ('positive_time', 'negative_time')
			   AND pm.meta_value <> ''
			   AND p.post_type = 'docs'
			   AND p.post_status = 'publish'"
		);

		$read_lookup = array_flip( $read_votes );
		foreach ( (array) $vote_rows as $row ) {
			if ( ! isset( $read_lookup[ $row->post_id . '_' . strtotime( $row->meta_value ) ] ) ) {
				++$unread_count;
			}
		}

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
			if ( ! (bool) get_comment_meta( $comment->comment_ID, '_ezd_is_read', true ) ) {
				++$unread_count;
			}
		}

		return $unread_count;
	}

	/**
	 * Check whether the current request can access Pro notification features.
	 *
	 * Keeps the Docs Builder notification feature gated the same way as before:
	 * only when Pro is active and the Pro notification helpers are available.
	 *
	 * @since  2.9.0
	 * @return bool
	 */
	private function can_access_pro_notifications() {
		return ezd_is_premium()
			&& current_user_can( 'manage_options' )
			&& function_exists( 'eazydocs_voted' )
			&& function_exists( 'ezd_comment_count' );
	}

	/**
	 * Get paginated notifications as JSON.
	 *
	 * @since  2.9.0
	 * @param  \WP_REST_Request $request REST request.
	 * @return \WP_REST_Response
	 */
	public function get_notifications( $request ) {
		if ( ! $this->can_access_pro_notifications() ) {
			return new \WP_REST_Response(
				array(
					'items'   => array(),
					'hasMore' => false,
					'total'   => 0,
					'page'    => 1,
				),
				200
			);
		}

		$page     = absint( $request->get_param( 'page' ) ) ?: 1;
		$per_page = min( 50, absint( $request->get_param( 'per_page' ) ) ?: 10 );
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
			// Only docs that actually received a vote (previously every doc was
			// loaded and then filtered in PHP).
			$args = array(
				'post_type'              => 'docs',
				'posts_per_page'         => -1,
				'post_status'            => array( 'publish' ),
				'no_found_rows'          => true,
				'update_post_term_cache' => false,
				'meta_query'             => array(
					'relation' => 'OR',
					array(
						'key'     => 'positive_time',
						'compare' => 'EXISTS',
					),
					array(
						'key'     => 'negative_time',
						'compare' => 'EXISTS',
					),
				),
			);

			$docs = get_posts( $args );
			update_postmeta_cache( wp_list_pluck( $docs, 'ID' ) );

			foreach ( $docs as $post ) {
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
