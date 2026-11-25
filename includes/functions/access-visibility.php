<?php
/**
 * Cannot access directly.
 */
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Editor & Administrator access
 */
function ezd_is_admin_or_editor( $post_id = '', $action = '' ) {
	if ( empty( $post_id ) ) {
		return current_user_can( 'edit_docs' ) || current_user_can( 'manage_options' );
	}

	if ( 'delete' === $action ) {
		return current_user_can( 'delete_doc', $post_id ) || current_user_can( 'manage_options' );
	}

	return current_user_can( 'edit_doc', $post_id ) || current_user_can( 'manage_options' );
}

/**
 * Internal doc secured by user role
 * 
 * Uses new settings: private_doc_access_type, private_doc_allowed_roles
 * Falls back to legacy settings: private_doc_user_restriction for backward compatibility
 * 
 * @param int $doc_id
 */
function ezd_internal_doc_security( $doc_id = 0 ) {
	// Private doc restriction
	if ( 'private' === get_post_status( $doc_id ) ) {
		
		// Try new settings first
		$access_type = ezd_get_opt( 'private_doc_access_type', '' );
		
		if ( ! empty( $access_type ) ) {
			// Using new settings
			if ( 'all_users' === $access_type ) {
				// All logged-in users can access - just check if logged in
				if ( is_user_logged_in() ) {
					return true;
				}
			} else {
				// Specific roles only
				$allowed_roles = ezd_get_opt( 'private_doc_allowed_roles', [ 'administrator', 'editor' ] );
				if ( ! is_array( $allowed_roles ) ) {
					$allowed_roles = [ $allowed_roles ];
				}
				
				// Current user roles
				$current_user_id = get_current_user_id();
				$current_user    = new WP_User( $current_user_id );
				$current_roles   = (array) $current_user->roles;
				
				// Check if user has any allowed role
				$matching_roles = array_intersect( $current_roles, $allowed_roles );
				
				if ( ! empty( $matching_roles ) || current_user_can( 'manage_options' ) ) {
					return true;
				}
			}
		} else {
			// Fallback to legacy settings
			$user_group  = ezd_get_opt( 'private_doc_user_restriction' );
			$is_all_user = $user_group['private_doc_all_user'] ?? 0;
			
			if ( '1' === $is_all_user || 1 === $is_all_user || true === $is_all_user ) {
				// All logged-in users can access
				if ( is_user_logged_in() ) {
					return true;
				}
			} else {
				// Current user role
				$current_user_id = get_current_user_id();
				$current_user    = new WP_User( $current_user_id );
				$current_roles   = (array) $current_user->roles;

				// All selected roles
				$private_doc_roles = $user_group['private_doc_roles'] ?? [];
				$matching_roles    = array_intersect( $current_roles, $private_doc_roles );

				if ( ! empty( $matching_roles ) || current_user_can( 'manage_options' ) ) {
					return true;
				}
			}
		}
		
		// Access denied - show the locked content area (with login button for
		// logged-out users) inside the normal single-doc layout.
		if ( is_singular( array( 'docs', 'api_docs' ) ) ) {
			if ( class_exists( '\eazyDocsPro\Frontend\Login_Popup' ) ) {
				// Trusted, internally-escaped markup from the Pro popup helper.
				$gated_id = get_the_ID();
				echo '<div class="ezd-xl-col-7 ezd-lg-col-6 ezd-grid-column-full doc-middle-content">'
					. \eazyDocsPro\Frontend\Login_Popup::locked_block_html( [
						'post_id'      => $gated_id,
						'message'      => ezd_get_opt( 'role_visibility_denied_message', '' ),
						'button_label' => ezd_get_opt( 'private_doc_login_prompt', '' ),
						'with_title'   => true,
						'redirect'     => get_permalink( $gated_id ),
						'gate_type'    => 'private',
						'gate_post'    => $gated_id,
					] )
					. '</div>'; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
			} else {
				$denied_message = ezd_get_opt( 'role_visibility_denied_message', esc_html__( "You don't have permission to access this document!", 'eazydocs' ) );
				$output = sprintf( '<div class="ezd-lg-col-9"><span class="ezd-doc-warning-wrap"><i class="icon_lock"></i><span>%s</span></span></div>', esc_html( $denied_message ) );
				echo wp_kses_post( $output );
			}
		}
		return null;
	}
	return true;
}

/**
 * Whether the current visitor may read a document's actual content.
 * Unlike read_post alone, this also enforces passwords and Pro role visibility.
 * Editors can already read the source through the editor and export it.
 *
 * @param int $post_id Document ID.
 * @return bool
 */
function ezd_can_read_doc_content( $post_id ) {
	$post = $post_id > 0 ? get_post( $post_id ) : null;
	if ( ! $post || ! in_array( $post->post_type, [ 'docs', 'onepage-docs' ], true ) ) {
		return false;
	}
	if ( current_user_can( 'edit_post', $post_id ) ) {
		return true;
	}
	if ( 'publish' !== $post->post_status && ( 'private' !== $post->post_status || ! current_user_can( 'read_post', $post_id ) ) ) {
		return false;
	}
	if ( post_password_required( $post ) ) {
		return false;
	}
	return 'docs' !== $post->post_type || ! function_exists( 'ezd_user_can_view_doc' ) || ezd_user_can_view_doc( $post_id );
}

/**
 * Delete doc secured by user role security
*/
function ezd_perform_edit_delete_actions( $action = 'delete', $docID = 0 ){
	// Get the current user ID
	$current_user_id = get_current_user_id();
	$inline_styles   = "margin: 50px auto; background: #f5f3f3;padding: 10px 80px;	width: max-content;	font-size: 16px;font-weight: 500;font-family: system-ui;border-radius: 3px;	color: #363636;";

	// Check if the current user has the documentation specific capability
	if ( current_user_can( $action . '_doc', $docID ) && $docID ) {
		// Check if the current user is the author of the post
		$post_author_id = (int) get_post_field('post_author', $docID);

		// Editors hold "{action}_others_docs"; without that check the builder
		// showed them Delete/Edit buttons that then always failed.
		if ( $current_user_id === $post_author_id || current_user_can( 'manage_options' ) || current_user_can( $action . '_others_docs' ) ) {
			return true;
		} else {
			echo sprintf(
				'<p style="%1$s">%2$s%3$s%4$s</p>',
				esc_attr( $inline_styles ),
				esc_html__( "You don't have permission to ", "eazydocs" ),
				esc_html( $action ),
				esc_html__( " this post.", "eazydocs" )
			);
		}
	} else {
		// User does not have delete_posts capability
		echo '<p style="' . esc_attr( $inline_styles ) . '">' . esc_html__( 'You don\'t have sufficient permission to perform this action.', 'eazydocs' ) . '</p>';

	}
}

/**
 * Get IDs of top-level docs that have NO child docs ("empty" docs).
 *
 * Used by the "Hide Empty Docs" option so empty docs can be excluded at the
 * query level (via post__not_in / exclude), which keeps the requested number
 * of docs accurate instead of filtering after the query limit is applied.
 *
 * Runs two lightweight queries (no per-doc loops) and caches the result per
 * status set for the duration of the request.
 *
 * @param array $statuses Post statuses to consider (e.g. ['publish'] or ['publish','private']).
 * @return int[] List of empty parent doc IDs.
 */
function ezd_get_empty_doc_ids( $statuses = [ 'publish' ] ) {
	global $wpdb;

	$statuses = array_values( array_filter( array_map( 'sanitize_key', (array) $statuses ) ) );
	if ( empty( $statuses ) ) {
		$statuses = [ 'publish' ];
	}

	static $cache = [];
	$cache_key = implode( ',', $statuses );
	if ( isset( $cache[ $cache_key ] ) ) {
		return $cache[ $cache_key ];
	}

	$placeholders = implode( ', ', array_fill( 0, count( $statuses ), '%s' ) );

	// Distinct parents that have at least one child doc.
	$parents_with_children = $wpdb->get_col(
		$wpdb->prepare(
			"SELECT DISTINCT post_parent FROM {$wpdb->posts}
			 WHERE post_type = 'docs' AND post_parent > 0 AND post_status IN ($placeholders)",
			...$statuses
		)
	);
	$parents_with_children = array_map( 'absint', (array) $parents_with_children );

	// All top-level docs.
	$top_level = $wpdb->get_col(
		$wpdb->prepare(
			"SELECT ID FROM {$wpdb->posts}
			 WHERE post_type = 'docs' AND post_parent = 0 AND post_status IN ($placeholders)",
			...$statuses
		)
	);
	$top_level = array_map( 'absint', (array) $top_level );

	// Empty docs = top-level docs that are not a parent of anything.
	$empty = array_values( array_diff( $top_level, $parents_with_children ) );

	$cache[ $cache_key ] = $empty;

	return $empty;
}

/**
 * Build the CSS classes that flag a doc as private or password-protected.
 *
 * Used by every doc skin/preset so restricted docs read consistently.
 *
 * @param int $post_id Doc post ID.
 * @return string Space-separated classes (empty for public docs).
 */
function ezd_doc_status_classes( $post_id ) {
	$classes = [];

	if ( 'private' === get_post_status( $post_id ) ) {
		$classes[] = 'ezd-doc-private';
	}

	$post = get_post( $post_id );
	if ( $post && '' !== $post->post_password ) {
		$classes[] = 'ezd-doc-protected';
	}

	return implode( ' ', $classes );
}

/**
 * Read a yes/no display toggle from a widget/block settings array.
 *
 * Normalises the many truthy shapes used across Elementor switchers ('yes'),
 * block attributes (true / '1') and shortcode atts ('true') into a boolean so
 * every surface can share the same visibility helpers.
 *
 * @param array  $settings Settings / attributes array.
 * @param string $key      Setting key to read.
 * @param string $default  Value to assume when the key is missing.
 * @return bool True when the toggle is enabled.
 */
function ezd_setting_enabled( $settings, $key, $default = 'yes' ) {
	$value = isset( $settings[ $key ] ) ? $settings[ $key ] : $default;

	return in_array( $value, [ 'yes', 'true', '1', 1, true ], true );
}

/**
 * Read a global restricted-docs display toggle from the Settings page.
 *
 * Acts as a master switch for the status badge / lock icon across every
 * surface (shortcode, blocks, Elementor widgets). When off, the element is
 * hidden everywhere regardless of any per-widget toggle.
 *
 * @param string $key     Option key (e.g. 'restricted_show_badge').
 * @param bool   $default Value to assume when the option is unset.
 * @return bool True when the element should render.
 */
function ezd_global_restricted_enabled( $key, $default = true ) {
	$value = ezd_get_opt( $key, $default ? '1' : '0' );

	return in_array( $value, [ 'yes', 'true', '1', 1, true ], true );
}

/**
 * Flag the restricted-docs "Outline" card style on the front-end <body>.
 *
 * Lets one admin setting re-style every restricted doc card (shortcode,
 * blocks, Elementor) without touching markup. The matching CSS lives in
 * assets/scss/ezd-docs-widgets.scss (body.ezd-restricted--outline).
 *
 * @param array $classes Existing body classes.
 * @return array Body classes, with the outline flag appended when selected.
 */
function ezd_restricted_body_class( $classes ) {
	if ( 'outline' === ezd_get_opt( 'restricted_card_style', 'filled' ) ) {
		$classes[] = 'ezd-restricted--outline';
	}

	return $classes;
}
add_filter( 'body_class', 'ezd_restricted_body_class' );

/**
 * Resolve which post statuses a docs listing query should request.
 *
 * Password-protected docs are post_status 'publish', so they are always
 * included here and filtered out separately via ezd_filter_doc_visibility().
 * Private docs are only surfaced when the toggle is on and the current user
 * holds the plugin's read_private_docs capability — the same capability that
 * gates the single-doc view (see ezd_private_docs_access()). This is mapped
 * from the docs CPT's read_private_posts meta cap and assigned per the
 * "who can access private docs" setting. Being merely logged in is not enough,
 * which also prevents private metadata/excerpt disclosure through the
 * get_pages()-based listing paths (those skip core's per-user private filter).
 *
 * @param bool $show_private Whether private docs should be listed.
 * @return array Post statuses for the query.
 */
function ezd_doc_listing_statuses( $show_private = true ) {
	$statuses = [ 'publish' ];

	if ( $show_private && current_user_can( 'read_private_docs' ) ) {
		$statuses[] = 'private';
	}

	return $statuses;
}

/**
 * Return allowable post statuses for frontend documentation sidebar/navigation queries.
 *
 * Respects the administrator setting 'draft_doc_visibility' (default: 'hide').
 * When set to 'show', 'draft' post status is allowed in sidebar navigation.
 *
 * @return array List of post status strings.
 */
function ezd_sidebar_doc_listing_statuses() {
	$visibility = ezd_get_opt( 'draft_doc_visibility', 'hide' );
	$statuses   = [ 'publish' ];

	if ( current_user_can( 'read_private_docs' ) ) {
		$statuses[] = 'private';
	}

	if ( 'show' === $visibility ) {
		$statuses[] = 'draft';
	}

	return array_unique( $statuses );
}

/**
 * Filter a list of doc posts by the per-widget visibility toggles.
 *
 * Removes password-protected docs when $show_protected is false and private
 * docs when $show_private is false. Accepts WP_Post objects or post IDs and
 * returns a re-indexed array of the same shape it received.
 *
 * @param array $docs           Array of WP_Post objects or post IDs.
 * @param bool  $show_private   Whether to keep private docs.
 * @param bool  $show_protected Whether to keep password-protected docs.
 * @return array Filtered, re-indexed array.
 */
function ezd_filter_doc_visibility( $docs, $show_private = true, $show_protected = true ) {
	if ( ( $show_private && $show_protected ) || empty( $docs ) ) {
		return $docs;
	}

	$filtered = array_filter(
		(array) $docs,
		function ( $doc ) use ( $show_private, $show_protected ) {
			$post = is_object( $doc ) ? $doc : get_post( $doc );

			if ( ! $post ) {
				return false;
			}

			if ( ! $show_protected && '' !== (string) $post->post_password ) {
				return false;
			}

			if ( ! $show_private && 'private' === $post->post_status ) {
				return false;
			}

			return true;
		}
	);

	// Preserve the original keys for ID-keyed arrays (e.g. sections), otherwise re-index.
	$is_assoc = array_keys( $docs ) !== range( 0, count( $docs ) - 1 );

	return $is_assoc ? $filtered : array_values( $filtered );
}

/**
 * Return the status badge markup for a private or password-protected doc.
 *
 * Password-protected takes priority when a doc is both. The markup is fully
 * controlled here (only the label is dynamic, and it is escaped), so callers
 * echo the return value directly.
 *
 * @param int  $post_id Doc post ID.
 * @param bool $show    Whether status badges should render (per-widget toggle).
 * @return string Badge HTML, or an empty string for public docs / when disabled.
 */
function ezd_doc_status_badge( $post_id, $show = true ) {
	if ( ! $show || ! ezd_global_restricted_enabled( 'restricted_show_badge' ) ) {
		return '';
	}

	$post         = get_post( $post_id );
	$is_protected = $post && '' !== $post->post_password;
	$is_private   = ( 'private' === get_post_status( $post_id ) );

	if ( $is_protected ) {
		$icon = '<svg width="12" height="12" viewBox="0 0 24 24" fill="currentColor" aria-hidden="true" focusable="false"><path d="M12 1a5 5 0 00-5 5v3H6a2 2 0 00-2 2v9a2 2 0 002 2h12a2 2 0 002-2v-9a2 2 0 00-2-2h-1V6a5 5 0 00-5-5zm3 8H9V6a3 3 0 016 0v3z"/></svg>';
		return '<span class="ezd-doc-flag ezd-doc-flag--protected">' . $icon . esc_html__( 'Protected', 'eazydocs' ) . '</span>';
	}

	if ( $is_private ) {
		$icon = '<svg width="12" height="12" viewBox="0 0 24 24" fill="currentColor" aria-hidden="true" focusable="false"><path d="M12 7c2.76 0 5 2.24 5 5 0 .65-.13 1.26-.36 1.83l2.92 2.92c1.51-1.26 2.7-2.89 3.43-4.75-1.73-4.39-6-7.5-11-7.5-1.4 0-2.74.25-3.98.7l2.16 2.16C10.74 7.13 11.35 7 12 7zM2 4.27l2.28 2.28.46.46C3.08 8.3 1.78 10.02 1 12c1.73 4.39 6 7.5 11 7.5 1.55 0 3.03-.3 4.38-.84l.42.42L19.73 22 21 20.73 3.27 3 2 4.27zM7.53 9.8l1.55 1.55c-.05.21-.08.43-.08.65 0 1.66 1.34 3 3 3 .22 0 .44-.03.65-.08l1.55 1.55c-.67.33-1.41.53-2.2.53-2.76 0-5-2.24-5-5 0-.79.2-1.53.53-2.2zm4.31-.78l3.15 3.15.02-.16c0-1.66-1.34-3-3-3l-.17.01z"/></svg>';
		return '<span class="ezd-doc-flag ezd-doc-flag--private">' . $icon . esc_html__( 'Private', 'eazydocs' ) . '</span>';
	}

	return '';
}

/**
 * Return a compact, inline lock icon for a restricted doc.
 *
 * Unlike ezd_render_doc_indicators() (an absolutely-positioned card overlay),
 * this is a small inline badge meant to sit next to a doc title in lists — used
 * by the Gutenberg blocks where the overlay markup would be mis-positioned.
 *
 * @param int  $post_id Doc post ID.
 * @param bool $show    Whether lock icons should render (per-block toggle).
 * @return string Lock icon HTML, or an empty string for public docs / when disabled.
 */
function ezd_doc_lock_icon( $post_id, $show = true ) {
	if ( ! $show || ! ezd_global_restricted_enabled( 'restricted_show_lock' ) ) {
		return '';
	}

	$post         = get_post( $post_id );
	$is_protected = $post && '' !== $post->post_password;
	$is_private   = ( 'private' === get_post_status( $post_id ) );

	if ( ! $is_protected && ! $is_private ) {
		return '';
	}

	$label = $is_protected ? esc_attr__( 'Password protected', 'eazydocs' ) : esc_attr__( 'Private', 'eazydocs' );
	$class = $is_protected ? 'ezd-doc-lock-inline--protected' : 'ezd-doc-lock-inline--private';
	$icon  = '<svg width="13" height="13" viewBox="0 0 24 24" fill="currentColor" aria-hidden="true" focusable="false"><path d="M12 1a5 5 0 00-5 5v3H6a2 2 0 00-2 2v9a2 2 0 002 2h12a2 2 0 002-2v-9a2 2 0 00-2-2h-1V6a5 5 0 00-5-5zm3 8H9V6a3 3 0 016 0v3z"/></svg>';

	return '<span class="ezd-doc-lock-inline ' . $class . '" title="' . $label . '">' . $icon . '</span>';
}

/**
 * Render the absolutely-positioned corner lock overlay for a restricted doc.
 *
 * Shared by every card-style surface (shortcode, Elementor doc skins). Lives in
 * functions.php (always loaded) because the shortcode can run on pages where the
 * Elementor template helpers are not included. Honours the global lock toggle.
 *
 * @param int  $post_id Doc post ID.
 * @param bool $show    Per-widget lock toggle.
 * @return void
 */
function ezd_render_doc_indicators( $post_id, $show = true ) {
	if ( ! $show || ! ezd_global_restricted_enabled( 'restricted_show_lock' ) ) {
		return;
	}

	if ( get_post_status( $post_id ) === 'private' ) {
		echo '<div class="private" title="' . esc_attr__( 'Private Doc', 'eazydocs' ) . '"><i class="icon_lock"></i></div>';
	}

	if ( ! empty( get_post( $post_id )->post_password ) ) {
		echo '<div class="private" title="' . esc_attr__( 'Password Protected Doc', 'eazydocs' ) . '">';
		echo '<svg width="50px" height="50px" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg" fill="#4e5668">';
		echo '<g><path fill="none" d="M0 0h24v24H0z"/><path d="M18 8h2a1 1 0 0 1 1 1v12a1 1 0 0 1-1 1H4a1 1 0 0 1-1-1V9a1 1 0 0 1 1-1h2V7a6 6 0 1 1 12 0v1zm-2 0V7a4 4 0 1 0-8 0v1h8zm-5 6v2h2v-2h-2zm-4 0v2h2v-2H7zm8 0v2h2v-2h-2z"/></g>';
		echo '</svg>';
		echo '</div>';
	}
}

/**
 * Get all conditional items
 */
 function ezd_get_conditional_items() {
	if ( ! ezd_is_promax() ) {
		return [];
	}
	$conditional_items 	= ezd_get_opt('condition_options');
	$conditional_array 	= [];

    if ( !empty( $conditional_items ) ) {
	    foreach ( $conditional_items as $key => $value ) {
		    $conditional_array[ $key ] = [
			    'id'    => $key,
			    'title' => ucwords( $value['title'] ),
			    'value' => strtolower( str_replace( ' ', '-', $value['title'] ) )
		    ];
	    }
    }

	return $conditional_array;
}

/**
 * Docs custom slug field validation
 */
if ( ! function_exists( 'ezd_slug_validate' ) ) {
    function ezd_slug_validate( $value ) {
        // Define the allowed characters: letters, numbers, hyphens, and underscores.
        $pattern = '/^[a-zA-Z0-9-_]+$/';

        // If the value contains any characters other than letters, numbers, hyphens, and underscores.
        if ( ! preg_match ( $pattern, $value ) ) {
            return esc_html__( 'Please avoid using special characters other than hyphens and underscores!', 'eazydocs' );
        }
    }
}
