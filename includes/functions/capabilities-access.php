<?php
/**
 * Cannot access directly.
 */
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Assigns or removes the 'read_private_docs' capability to user roles
 * based on the EazyDocs private doc settings.
 * 
 * Uses new settings: private_doc_access_type, private_doc_allowed_roles
 * Falls back to legacy settings: private_doc_user_restriction for backward compatibility
 */
function ezd_read_private_docs_cap_to_user() {
    // Try new settings first
    $access_type = ezd_get_opt( 'private_doc_access_type', '' );
    
    if ( ! empty( $access_type ) ) {
        // Using new settings
        if ( 'all_users' === $access_type ) {
            // All logged-in users can access
			$get_users_role = function_exists( 'eazydocs_user_role_names' ) ? array_values( array_keys( eazydocs_user_role_names() ) ) : [];
        } else {
            // Specific roles only
            $get_users_role = ezd_get_opt( 'private_doc_allowed_roles', [ 'administrator', 'editor' ] );
            if ( ! is_array( $get_users_role ) ) {
                $get_users_role = [ $get_users_role ];
            }
        }
    } else {
        // Fallback to legacy settings for backward compatibility
        $user_group  = ezd_get_opt( 'private_doc_user_restriction' );
        $is_all_user = $user_group['private_doc_all_user'] ?? 0;

        if ( '1' === $is_all_user || 1 === $is_all_user || true === $is_all_user ) {
			$get_users_role = function_exists( 'eazydocs_user_role_names' ) ? array_values( array_keys( eazydocs_user_role_names() ) ) : [];
        } else {
            $get_users_role = $user_group['private_doc_roles'] ?? [];
            if ( ! is_array( $get_users_role ) ) {
                $get_users_role = [ $get_users_role ];
            }
        }
    }

    global $wp_roles;
    if ( ! isset( $wp_roles ) ) {
        $wp_roles = new WP_Roles();
    }

    $get_users_role = array_map( 'strval', (array) $get_users_role );

    foreach ( $wp_roles->roles as $role_key => $role_data ) {
        $role = get_role( $role_key );
        if ( ! $role ) {
            continue;
        }

        $should_have = in_array( $role_key, $get_users_role, true );
        $has_cap     = ! empty( $role->capabilities['read_private_docs'] );

        // Only touch the role (which writes to the wp_user_roles option) when
        // the desired state actually differs from what is stored.
        if ( $should_have && ! $has_cap ) {
            $role->add_cap( 'read_private_docs' );
        } elseif ( ! $should_have && $has_cap ) {
            $role->remove_cap( 'read_private_docs' );
        }
    }
}

/**
 * Resolve the user roles that are allowed to author documentation.
 *
 * This is the single source of truth shared by the capability sync
 * (ezd_docs_cap_to_user) and the admin-menu access check in Admin.php, so the
 * granted capabilities and the menu visibility can never drift apart. It merges
 * the "Documentation Authors" (User Permissions) and "Allowed User Roles"
 * (Docs Collaboration) settings, because both grant doc-editing capabilities.
 *
 * @return string[] Role slugs allowed to author docs.
 */
function ezd_get_doc_author_roles() {
	$write_access_roles  = ezd_get_opt( 'docs-write-access' );
	$collaboration_roles = ezd_get_opt( 'ezd_add_editable_roles' );

	$write_access_roles  = is_array( $write_access_roles ) ? $write_access_roles : array_filter( [ $write_access_roles ] );
	$collaboration_roles = is_array( $collaboration_roles ) ? $collaboration_roles : array_filter( [ $collaboration_roles ] );

	$active_roles = array_values( array_unique( array_merge( $write_access_roles, $collaboration_roles ) ) );

	return ! empty( $active_roles ) ? $active_roles : [ 'administrator', 'editor', 'author' ];
}

/**
 * Build the list of assignable user roles for the permission selectors.
 *
 * Sources roles dynamically so custom roles registered by the site (or other
 * plugins) appear consistently across User Permissions and Docs Collaboration,
 * instead of a hard-coded list of the five default roles. Falls back to the
 * default roles if the editable-roles API is unavailable.
 *
 * @return array<string,string> Role slug => translated label.
 */
function ezd_assignable_role_options() {
	// Pro ships a richer role-name helper; keep using it when available.
	if ( function_exists( 'eazydocs_user_role_names' ) && ezd_is_premium() ) {
		return eazydocs_user_role_names();
	}

	if ( ! function_exists( 'get_editable_roles' ) ) {
		require_once ABSPATH . 'wp-admin/includes/user.php';
	}

	$roles = [];
	if ( function_exists( 'get_editable_roles' ) ) {
		foreach ( get_editable_roles() as $slug => $details ) {
			$roles[ $slug ] = translate_user_role( $details['name'] );
		}
	}

	if ( ! empty( $roles ) ) {
		return $roles;
	}

	return [
		'administrator' => esc_html__( 'Administrator', 'eazydocs' ),
		'editor'        => esc_html__( 'Editor', 'eazydocs' ),
		'author'        => esc_html__( 'Author', 'eazydocs' ),
		'contributor'   => esc_html__( 'Contributor', 'eazydocs' ),
		'subscriber'    => esc_html__( 'Subscriber', 'eazydocs' ),
	];
}

/**
 * Assigns or removes the 'add or edit_docs' capability to user roles
 */
function ezd_docs_cap_to_user() {
	$active_roles = ezd_get_doc_author_roles();

	$author_caps = [
		'edit_doc',
		'edit_docs',
		'publish_docs',
		'delete_doc',
		'delete_docs',
		'edit_published_docs',
		'delete_published_docs'
	];

	$manager_caps = [
		'edit_others_docs',
		'delete_others_docs',
		'edit_private_docs',
		'read_private_docs',
		'delete_private_docs',
	];

	// Get all roles
	global $wp_roles;
	if ( ! isset( $wp_roles ) ) {
		$wp_roles = new WP_Roles();
	}

	foreach ( $wp_roles->roles as $role_key => $role_data ) {
		$role = get_role( $role_key );
		if ( ! $role ) {
			continue;
		}

		if ( in_array( $role_key, $active_roles, true ) ) {
			// Grant Author capabilities to all active roles
			foreach ( $author_caps as $cap ) {
				ezd_set_role_cap( $role, $cap, true );
			}

			// Grant Manager capabilities only to roles that can normally edit others' posts
			$grant_manager = $role->has_cap( 'edit_others_posts' );
			foreach ( $manager_caps as $cap ) {
				// read_private_docs belongs to the Private Docs setting
				// (ezd_read_private_docs_cap_to_user): managers get it, but it
				// is never revoked here.
				if ( 'read_private_docs' === $cap && ! $grant_manager ) {
					continue;
				}
				ezd_set_role_cap( $role, $cap, $grant_manager );
			}
		} else {
			// Remove all documentation capabilities from inactive roles, except
			// read_private_docs: revoking it here undid the "who can read
			// private docs" setting and started a per-request add/remove fight
			// with EazyDocs Pro's private-docs sync.
			foreach ( array_merge( $author_caps, $manager_caps ) as $cap ) {
				if ( 'read_private_docs' === $cap ) {
					continue;
				}
				ezd_set_role_cap( $role, $cap, false );
			}
		}
	}
}

/**
 * Add or remove a single capability on a role, but only when it would change
 * the stored value. WP_Role::add_cap()/remove_cap() persist the whole
 * wp_user_roles option on every call, so skipping no-op writes turns the
 * capability sync from dozens of option writes into zero when nothing changed.
 *
 * @param WP_Role $role  Role object to modify.
 * @param string  $cap   Capability name.
 * @param bool    $grant Whether the role should have the capability.
 */
function ezd_set_role_cap( $role, $cap, $grant ) {
	$has_cap = ! empty( $role->capabilities[ $cap ] );

	if ( $grant && ! $has_cap ) {
		$role->add_cap( $cap );
	} elseif ( ! $grant && $has_cap ) {
		$role->remove_cap( $cap );
	}
}

/**
 * A fingerprint of every setting (and the registered role list) that affects
 * the documentation capability map. When this changes, the capabilities need
 * re-syncing; when it does not, the heavy add_cap/remove_cap loop can be skipped.
 *
 * @return string MD5 signature.
 */
function ezd_docs_capabilities_signature() {
	$relevant = [
		'docs-write-access'            => ezd_get_opt( 'docs-write-access' ),
		'ezd_add_editable_roles'       => ezd_get_opt( 'ezd_add_editable_roles' ),
		'private_doc_access_type'      => ezd_get_opt( 'private_doc_access_type' ),
		'private_doc_allowed_roles'    => ezd_get_opt( 'private_doc_allowed_roles' ),
		'private_doc_user_restriction' => ezd_get_opt( 'private_doc_user_restriction' ),
		'roles'                        => array_keys( wp_roles()->roles ),
		'version'                      => defined( 'EZD_VERSION' ) ? EZD_VERSION : '',
		// Bump when the sync logic itself changes so every site re-syncs once
		// (2: read_private_docs is no longer revoked by the author-caps sync).
		'logic'                        => 2,
	];

	return md5( maybe_serialize( $relevant ) );
}

/**
 * Reconcile EazyDocs role capabilities with the current permission settings.
 *
 * Replaces the previous approach of running the capability grant/revoke loop on
 * every `init` (which wrote the wp_user_roles option on each page load). Now the
 * sync runs only on settings save, on activation, or once after the relevant
 * settings change — tracked by a lightweight signature so the cheap check can
 * safely run on admin_init without touching the database when nothing changed.
 *
 * @param bool $force Run the sync even if the signature is unchanged.
 */
function ezd_sync_docs_capabilities( $force = false ) {
	$signature = ezd_docs_capabilities_signature();

	if ( ! $force && get_option( 'ezd_docs_caps_signature' ) === $signature ) {
		return;
	}

	// Order matters: both touch read_private_docs. Run the private-docs sync
	// first and the author sync last (matching the original init priority) so
	// manager roles keep read_private_docs as before.
	ezd_read_private_docs_cap_to_user();
	ezd_docs_cap_to_user();

	update_option( 'ezd_docs_caps_signature', $signature, false );
}

// Reconcile once per admin or rest load only when settings actually changed, and
// immediately after the settings screen is saved.
// Wrapped in closures on purpose: rest_api_init passes the WP_REST_Server as the
// first argument, which landed in $force and forced a full grant/revoke pass
// (dozens of wp_user_roles writes) on every REST request and every admin screen
// that preloads REST data.
add_action( 'admin_init', function () {
	ezd_sync_docs_capabilities();
} );
add_action( 'rest_api_init', function () {
	ezd_sync_docs_capabilities();
} );
add_action( 'csf_eazydocs_settings_saved', function () {
	ezd_sync_docs_capabilities( true );
} );

/**
 * Dynamically map and grant EazyDocs capabilities to users based on their roles and core capabilities.
 *
 * Ensures Administrators (manage_options) and authorized documentation authors
 * always have the required capabilities even if custom capabilities were not physically
 * synced into the wp_user_roles database option, or were altered by role manager plugins.
 *
 * @param array    $allcaps All capabilities of the user.
 * @param array    $caps    Required primitive capabilities being checked.
 * @param array    $args    Arguments passed to current_user_can() [0 => cap, 1 => user_id, 2 => post_id/arg].
 * @param \WP_User $user    User object.
 * @return array Modified capabilities.
 */
function ezd_filter_user_has_cap( $allcaps, $caps, $args, $user ) {
	// Logged-out visitors (ID 0) and role-less users can never gain doc caps here;
	// this filter runs dozens of times per page, so bail before any option reads.
	if ( empty( $user ) || ! ( $user instanceof \WP_User ) || empty( $user->ID ) || ( empty( $user->roles ) && empty( $allcaps['manage_options'] ) ) ) {
		return $allcaps;
	}

	// Administrators, and anyone WordPress treats as a super admin, always have
	// all doc capabilities. On a single site that is delete_users, already
	// present in $allcaps. Do not call is_super_admin() here: on a single site
	// it calls has_cap(), this filter runs again, and the request uses memory
	// until PHP stops it. On multisite, is_super_admin() only reads the super-admin list.
	$is_super_admin = is_multisite() ? is_super_admin( $user->ID ) : ! empty( $allcaps['delete_users'] );
	if ( ! empty( $allcaps['manage_options'] ) || $is_super_admin ) {
		$doc_caps = [
			'edit_doc',
			'edit_docs',
			'edit_others_docs',
			'edit_published_docs',
			'edit_private_docs',
			'publish_docs',
			'read_doc',
			'read_private_docs',
			'delete_doc',
			'delete_docs',
			'delete_others_docs',
			'delete_published_docs',
			'delete_private_docs',
		];
		foreach ( $doc_caps as $cap ) {
			$allcaps[ $cap ] = true;
		}
		return $allcaps;
	}

	// For non-administrators, check if their role is in the configured doc author roles.
	$author_roles = ezd_get_doc_author_roles();
	$user_roles   = (array) $user->roles;

	if ( array_intersect( $user_roles, $author_roles ) ) {
		// Basic author capabilities (create, edit, delete own docs).
		$allcaps['edit_doc']    = true;
		$allcaps['edit_docs']   = true;
		$allcaps['delete_doc']  = true;
		$allcaps['delete_docs'] = true;
		$allcaps['read_doc']    = true;

		// Can publish docs if they can publish posts or if role is editor/author.
		if ( ! empty( $allcaps['publish_posts'] ) || in_array( 'editor', $user_roles, true ) || in_array( 'author', $user_roles, true ) ) {
			$allcaps['publish_docs']          = true;
			$allcaps['edit_published_docs']   = true;
			$allcaps['delete_published_docs'] = true;
		}

		// Can edit others' docs if they have edit_others_posts (like Editors).
		if ( ! empty( $allcaps['edit_others_posts'] ) || in_array( 'editor', $user_roles, true ) ) {
			$allcaps['edit_others_docs']    = true;
			$allcaps['delete_others_docs']  = true;
			$allcaps['edit_private_docs']   = true;
			$allcaps['read_private_docs']   = true;
			$allcaps['delete_private_docs'] = true;
		}
	}

	return $allcaps;
}
add_filter( 'user_has_cap', 'ezd_filter_user_has_cap', 10, 4 );

/**
 * Admin bar hide for OnePage Docs
 */
add_filter('show_admin_bar', function ( $show ) {
    // Hide admin bar on singular onepage-docs
    if ( is_singular('onepage-docs') ) {
        return false;
    }

    // Leave every other page alone: forcing is_user_logged_in() here overrode the
    // user's "Show Toolbar when viewing site" preference and other plugins' filters.
    return $show;
});


/**
 * Handle private docs access based on settings.
 * Respects private_doc_mode setting: 'login' redirects to login page, 'none' shows 404.
 */
add_action( 'template_redirect', 'ezd_private_docs_access' );

function ezd_private_docs_access() {
    if ( is_singular( 'docs' ) ) {
        global $post;

        // Check if the doc is private
        if ( 'private' === get_post_status( $post->ID ) ) {

            // If user does not have permission to read private docs
            if ( ! current_user_can( 'read_private_docs' ) ) {
                
                // Get the private doc mode setting (only for pro users)
                $private_doc_mode = ezd_is_premium() ? ezd_get_opt( 'private_doc_mode', 'none' ) : 'none';
                
                // If mode is 'login', show the login popup in place (locked view)
                // instead of redirecting to a dedicated login page.
                if ( 'login' === $private_doc_mode ) {
                    if ( class_exists( '\eazyDocsPro\Frontend\Login_Popup' ) ) {
                        $redirect_back = ezd_get_opt( 'private_doc_redirect_back', true );
                        $redirect_to   = $redirect_back ? get_permalink( $post->ID ) : home_url();

                        \eazyDocsPro\Frontend\Login_Popup::request_gate( $post->ID, $redirect_to, 'private' );
                        return;
                    }

                    // Fallback to WordPress login if the popup is unavailable.
                    wp_safe_redirect( wp_login_url( get_permalink( $post->ID ) ) );
                    exit;
                }

                // Default behavior: Show 404
                global $wp_query;
                $wp_query->set_404();
                status_header( 404 );
                nocache_headers();
                include( get_query_template( '404' ) );
                exit;

            }
        }
    }
}

/**
 * Get the sanitized docs slug
 *
 * @return string The sanitized docs slug.
 */
function ezd_docs_slug() {
	if ( ! ezd_is_premium() ) {
		return '';
	}

	$docs_url	  = ezd_get_opt( 'docs-url-structure', 'custom-slug' );
	$permalink    = get_option( 'permalink_structure' );
    $custom_slug  = ezd_get_opt( 'docs-type-slug' );
    $safe_slug 	  = preg_replace( '/[^a-zA-Z0-9-_]/', '-', $custom_slug );
	
	if ( 'custom-slug' === $docs_url || '' === $permalink || '/archives/%post_id%' === $permalink ) {
		return $safe_slug ?: 'docs';
	}

	return '';
}
