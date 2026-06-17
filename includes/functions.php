<?php
/**
 * Cannot access directly.
 */
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

/**
 * Get the value of a settings field.
 *
 * @param string $option  settings field name
 * @param string $section the section name this field belongs to
 * @param string $default default text if it's not found
 *
 * @return mixed
 */
function ezd_get_opt( $option, $default = '' ) {
	$options = get_option( 'eazydocs_settings' );

	if ( isset( $options[ $option ] ) ) {
		return $options[ $option ];
	}

	return $default;
}

/**
 * Marker prefix used to identify EazyDocs-encrypted secrets at rest.
 */
const EZD_ENC_PREFIX = 'ezd_enc::';

/**
 * Derive the symmetric encryption key from the site's secret auth salts.
 *
 * Using wp_salt() keeps the key out of the database and the codebase. The key
 * is hashed to a fixed 32-byte length for AES-256.
 *
 * @return string 32-byte binary key.
 */
function ezd_encryption_key() {
	return hash( 'sha256', wp_salt( 'secure_auth' ), true );
}

/**
 * Encrypt a sensitive value (e.g. an API secret) for storage at rest.
 *
 * Returns a prefixed, base64-encoded "IV + ciphertext" string so the value can
 * be recognised and decrypted later. Empty values and already-encrypted values
 * are returned unchanged to keep saves idempotent.
 *
 * @param string $value Plaintext value to encrypt.
 * @return string Encrypted, prefixed value (or the original input when empty/already encrypted).
 */
function ezd_encrypt( $value ) {
	if ( ! is_string( $value ) || '' === $value ) {
		return $value;
	}

	// Already encrypted — do not double-encrypt on re-save.
	if ( 0 === strpos( $value, EZD_ENC_PREFIX ) ) {
		return $value;
	}

	if ( ! function_exists( 'openssl_encrypt' ) ) {
		return $value; // Graceful fallback when OpenSSL is unavailable.
	}

	$iv         = random_bytes( 16 );
	$ciphertext = openssl_encrypt( $value, 'aes-256-cbc', ezd_encryption_key(), OPENSSL_RAW_DATA, $iv );

	if ( false === $ciphertext ) {
		return $value;
	}

	return EZD_ENC_PREFIX . base64_encode( $iv . $ciphertext );
}

/**
 * Decrypt a value previously encrypted with ezd_encrypt().
 *
 * Values without the EazyDocs marker prefix (e.g. legacy plaintext secrets) are
 * returned as-is so existing configurations keep working until re-saved.
 *
 * @param string $value Stored value to decrypt.
 * @return string Decrypted plaintext (or the original input when not encrypted).
 */
function ezd_decrypt( $value ) {
	if ( ! is_string( $value ) || 0 !== strpos( $value, EZD_ENC_PREFIX ) ) {
		return $value;
	}

	if ( ! function_exists( 'openssl_decrypt' ) ) {
		return '';
	}

	$payload = base64_decode( substr( $value, strlen( EZD_ENC_PREFIX ) ), true );

	if ( false === $payload || strlen( $payload ) <= 16 ) {
		return '';
	}

	$iv         = substr( $payload, 0, 16 );
	$ciphertext = substr( $payload, 16 );
	$plaintext  = openssl_decrypt( $ciphertext, 'aes-256-cbc', ezd_encryption_key(), OPENSSL_RAW_DATA, $iv );

	return ( false === $plaintext ) ? '' : $plaintext;
}

/**
 * CSF sanitize callback: encrypt a secret field value before it is saved.
 *
 * @param string $value Submitted field value.
 * @return string Encrypted value safe for storage.
 */
function ezd_sanitize_encrypted_secret( $value ) {
	return ezd_encrypt( sanitize_text_field( $value ) );
}

/**
 * Prime the post meta cache with backward compatibility.
 *
 * @param array|int|WP_Post $post_ids Post IDs or post objects.
 * @return void
 */
function ezd_update_post_meta_cache( $post_ids ) {
	if ( empty( $post_ids ) ) {
		return;
	}

	if ( is_object( $post_ids ) && isset( $post_ids->ID ) ) {
		$post_ids = [ $post_ids->ID ];
	} elseif ( is_array( $post_ids ) ) {
		$first = reset( $post_ids );
		if ( is_object( $first ) && isset( $first->ID ) ) {
			$post_ids = wp_list_pluck( $post_ids, 'ID' );
		}
	}

	if ( empty( $post_ids ) ) {
		return;
	}

	if ( function_exists( 'update_post_meta_cache' ) ) {
		update_post_meta_cache( $post_ids );
		return;
	}

	if ( function_exists( 'update_postmeta_cache' ) ) {
		update_postmeta_cache( $post_ids );
		return;
	}

	if ( function_exists( 'update_meta_cache' ) ) {
		update_meta_cache( 'post', $post_ids );
	}
}

/**
 * Get post-meta value or theme option value.
 *
 * This function first attempts to retrieve a post-meta value. If the post meta
 * is not set or is empty, it falls back to the theme option value.
 *
 * @param string $option_id
 * @param string|null $default The default value to return if both meta and option are not set.
 * @return mixed The post meta value, theme option value, or default value.
 */

function ezd_meta_apply( $option_id, $default = '' ) {
	// Get post meta and theme option values
	$meta_value   = get_post_meta( get_the_ID(), $option_id, true );
	$option_value = ezd_get_opt( $option_id, $default );

	// Check if meta value is an array and empty
	$is_meta_arr_empty = is_array($meta_value) && empty(array_filter($meta_value));
	if ( 'default' === $meta_value || '' === $meta_value || null === $meta_value || $is_meta_arr_empty ) {
		return $option_value;
	}

	// Return meta if it's a valid non-empty value
	return $meta_value;
}

/**
 * Normalize optional sidebar content before rendering.
 *
 * Treat placeholder quoted-empty strings (`""`/`''`) as empty so they are not
 * printed in left/right sidebar optional content blocks.
 *
 * @param mixed $content Raw sidebar content from post meta.
 * @return string
 */
function ezd_get_renderable_sidebar_content( $content ) {
	if ( ! is_scalar( $content ) ) {
		return '';
	}

	$content = trim( (string) $content );
	if ( '' === $content ) {
		return '';
	}

	$charset = get_bloginfo( 'charset' );
	$content = trim( html_entity_decode( $content, ENT_QUOTES, $charset ?: 'UTF-8' ) );

	if ( in_array( $content, [ '""', "''" ], true ) ) {
		return '';
	}

	return $content;
}

/**
 * Check if the pro plugin and plan is active.
 *
 * @return bool True if premium features are active, false otherwise.
 */
function ezd_is_premium() {
	if ( eaz_fs()->can_use_premium_code() && class_exists('EZD_EazyDocsPro')) {
		return true;
	}
}

/**
 * Unlock the plugin with themes
 *
 * @return bool|void
 */
function ezd_unlock_themes( ...$themes ) {
    // Flatten and normalize
    $allowed_themes = array_map( 'strtolower', array_map( 'trim', $themes ) );
    $current_theme = strtolower( get_template() );
    return in_array( $current_theme, $allowed_themes, true ) || ezd_is_premium();
}

/**
 * Check if footnotes are unlocked
 * Condition: Promax Active OR (Docy Theme OR Docly Theme)
 *
 * @return bool
 */
function ezd_is_footnotes_unlocked() {
	$current_theme = strtolower( get_template() );
	return ezd_is_promax() || in_array( $current_theme, [ 'docy', 'docly' ], true );
}

/**
 * Check if the pro plugin and plan is active
 *
 * @return bool|void
 */
function ezd_is_promax() {
	if ( class_exists( 'EZD_EazyDocsPro' ) && eaz_fs()->can_use_premium_code() && eaz_fs()->is_plan( 'promax' ) ) {
		return true;
	}
}

/**
 * Check if footnotes are unlocked
 * Condition: Promax Active OR (Docy Theme OR Docly Theme)
 *
 * @return bool
 */
function eazydocs_is_footnotes_unlocked() {
	$current_theme = strtolower( get_template() );
	return ezd_is_promax() || in_array( $current_theme, [ 'docy', 'docly' ], true );
}

/**
 * Check if a plugin has been installed for specific number of days
 *
 * @param string $plugin_path The plugin path (e.g. 'woocommerce/woocommerce.php')
 * @param int    $days        Number of days to check against
 * @return bool  True if plugin is installed for specified days, false otherwise
 */
function ezd_is_plugin_installed_for_days( $days, $plugin_slug='eazydocs' ) {
	// Get the installation timestamp of the plugin
	$installed_time = get_option( $plugin_slug . '_installed' );

	// Ensure it's a valid timestamp
	if ( ! is_numeric( $installed_time ) || $installed_time <= 0 ) {
		return false;
	}

	// Convert days to seconds
	$required_time = (int) $days * DAY_IN_SECONDS;

	// Get the current UTC time
	$current_time = time();

	// Check if the plugin has been installed for the required duration
	return ( $current_time - $installed_time ) >= $required_time; 
}

/**
 * Get the container class
 * @return string
 */
function ezd_container() {
	return 'full-width' === ezd_get_opt( 'docs_page_width' ) ? 'ezd-container-fluid' : 'ezd-container ezd-custom-container';
}

/**
 * Get admin template part.
 *
 * @param string $template The template name to load.
 * @return void
 */
function eazydocs_get_admin_template_part( $template ) {
	$file = EZD_PATH . "/includes/admin/templates/$template.php";
	load_template( $file, false );
}

/**
 * Get Page by title.
 *
 * @param string $title     Page title.
 * @param string $post_type Optional. Post type. Default 'page'.
 *
 * @return int[]|WP_Post[] Array of post objects or IDs.
 */
function ezd_get_page_by_title( $title, $post_type = 'page' ) {
	return get_posts(
		[
			'post_type'              => $post_type,
			'title'                  => $title,
			'post_status'            => 'all',
			'numberposts'            => 1,
			'update_post_term_cache' => false,
			'update_post_meta_cache' => false,
			'orderby'                => 'post_date ID',
			'order'                  => 'ASC',
		],
	);
}

/**
 * Get template part implementation for EazyDocs.
 * Looks at the theme directory first.
 *
 * @param string $template The template filename.
 * @return void
 */
function eazydocs_get_template_part( $template ) {
	// Sanitize template name to prevent directory traversal attacks
	// Remove any directory traversal sequences and null bytes
	$template = str_replace( [ '..', "\0" ], '', $template );
	
	// Get the slug
	$template_slug = rtrim( $template, '.php' );
	$template      = $template_slug . '.php';

	// Validate that template name only contains safe characters (alphanumeric, hyphens, underscores, and forward slashes for subdirectories)
	if ( ! preg_match( '/^[a-zA-Z0-9_\-\/]+\.php$/', $template ) ) {
		return;
	}

	// Check if a custom template exists in the theme folder, if not, load the plugin template file
	if ( $theme_file = locate_template( [ 'eazydocs/' . $template ] ) ) {
		$file = $theme_file;
	} else {
		//here path to '/single-paper.php'
		$file = EZD_PATH . "/templates/" . $template;
	}

	// Verify the file exists and is within the expected directory
	if ( $file && file_exists( $file ) ) {
		$real_file = realpath( $file );
		$real_templates_dir = realpath( EZD_PATH . '/templates' );
		
		// Ensure the resolved file path is within the templates directory or theme directory
		if ( $real_file && ( 
			0 === strpos( $real_file, $real_templates_dir ) ||
			0 === strpos( $real_file, get_template_directory() ) ||
			0 === strpos( $real_file, get_stylesheet_directory() )
		) ) {
			load_template( $file, false );
		}
	}
}

/**
 * Get template part implementation for EazyDocs.
 * Looks at the theme directory first.
 *
 * @param string $template_name The template filename.
 * @param array  $args          Optional. Arguments to pass to the template. Default empty array.
 * @return void
 */
function eazydocs_get_template( $template_name, $args = [] ) {
	$ezd_obj = EazyDocs::init();

	// Sanitize template name to prevent directory traversal attacks
	$template_name = str_replace( [ '..', "\0" ], '', $template_name );
	
	// Validate that template name only contains safe characters
	if ( ! preg_match( '/^[a-zA-Z0-9_\-\/]+\.php$/', $template_name ) ) {
		return;
	}

	if ( $args && is_array( $args ) ) {
		extract( $args );
	}

	$template = locate_template( [
		$ezd_obj->theme_dir_path . $template_name,
		$template_name,
	] );

	if ( ! $template ) {
		$template = $ezd_obj->template_path() . $template_name;
	}

	// Verify the file exists and is within the expected directory
	if ( file_exists( $template ) ) {
		$real_template = realpath( $template );
		$real_templates_dir = realpath( $ezd_obj->template_path() );
		
		// Ensure the resolved file path is within the templates directory or theme directory
		if ( $real_template && ( 
			0 === strpos( $real_template, $real_templates_dir ) ||
			0 === strpos( $real_template, get_template_directory() ) ||
			0 === strpos( $real_template, get_stylesheet_directory() )
		) ) {
			include $template;
		}
	}
}

/**
 * Estimated reading time.
 *
 * Calculates and outputs the estimated reading time based on word count.
 *
 * @return void
 */
function ezd_reading_time() {
    $content     = get_post_field( 'post_content', get_the_ID() );
    $word_count  = str_word_count( wp_strip_all_tags( $content ) );
    $wpm         = max( 1, absint( ezd_get_opt( 'reading_time_wpm', 200 ) ) );
    $readingtime = (int) ceil( $word_count / $wpm );

    if ( 1 === $readingtime ) {
        $timer = esc_html__( " minute", 'eazydocs' );
    } else {
        $timer = esc_html__( " minutes", 'eazydocs' );
    }

    $totalreadingtime = $readingtime . $timer;
    echo esc_html( $totalreadingtime );
}


/**
 * List pages with specific arguments.
 *
 * @param string|array $args Optional. Array or string of arguments to retrieve pages.
 *                           Default empty string.
 * @return string|void HTML content of the pages list if 'echo' is false, void otherwise.
 */
function ezd_list_pages( $args = '' ) {
	// Sentinel: Prevent unauthorized access to private docs
	$can_read_private = current_user_can( 'read_private_docs' ) || current_user_can( 'read_private_posts' );
	$post_status      = $can_read_private ? [ 'publish', 'private' ] : [ 'publish' ];

	$defaults = [
		'depth'        => 0,
		'show_date'    => '',
		'date_format'  => get_option( 'date_format' ),
		'child_of'     => 0,
		'exclude'      => '',
		'title_li'     => esc_html__( 'Pages', 'eazydocs' ),
		'echo'         => 1,
		'authors'      => '',
		'sort_column'  => 'menu_order',
		'link_before'  => '',
		'link_after'   => '',
		'item_spacing' => 'preserve',
		'walker'       => '',
		'post_status'  => $post_status
	];

	$r = wp_parse_args( $args, $defaults );

	if ( ! in_array( $r['item_spacing'], [ 'preserve', 'discard' ], true ) ) {
		// invalid value, fall back to default.
		$r['item_spacing'] = $defaults['item_spacing'];
	}

	$output       		= '';
	$current_page		= 0;

	// sanitize, mostly to keep spaces out
	$r['exclude'] = preg_replace( '/[^0-9,]/', '', $r['exclude'] );

	// Allow plugins to filter an array of excluded pages (but don't put a nullstring into the array)
	$exclude_array = ( $r['exclude'] ) ? explode( ',', $r['exclude'] ) : [];

	/**
	 * Filters the array of pages to exclude from the pages list.
	 *
	 * @param array $exclude_array An array of page IDs to exclude.
	 *
	 * @since 2.1.0
	 *
	 */
	$r['exclude'] = implode( ',', apply_filters( 'wp_list_pages_excludes', $exclude_array ) );

	// Query pages.
	$r['hierarchical'] = 0;
	$pages             = get_pages( $r );

	if ( ! empty( $pages ) ) {
		if ( $r['title_li'] ) {
			$output .= '<li class="pagenav">' . $r['title_li'] . '<ul>';
		}
		global $wp_query;
		if ( is_page() || is_attachment() || $wp_query->is_posts_page ) {
			$current_page = get_queried_object_id();
		} elseif ( is_singular() ) {
			$queried_object = get_queried_object();
			if ( is_post_type_hierarchical( $queried_object->post_type ) ) {
				$current_page = $queried_object->ID;
			}
		}

		$output .= walk_page_tree( $pages, $r['depth'], $current_page, $r );

		if ( $r['title_li'] ) {
			$output .= '</ul></li>';
		}
	}

	/**
	 * Filters the HTML output of the pages to list.
	 *
	 * @param string $output HTML output of the pages list.
	 * @param array  $r      An array of page-listing arguments.
	 * @param array  $pages  List of WP_Post objects returned by `get_pages()`
	 *
	 * @since 1.5.1
	 * @since 4.4.0 `$pages` added as arguments.
	 *
	 * @see   ezd_list_pages()
	 *
	 */
	if ( $r['echo'] ) {
		echo wp_kses_post( apply_filters( 'ezd_list_pages', $output, $r, $pages ) );
	} else {
		return wp_kses_post( apply_filters( 'ezd_list_pages', $output, $r, $pages ) );
	}
}

if ( ! function_exists( 'eazydocs_get_breadcrumb_item' ) ) {
	/**
	 * Schema.org breadcrumb item wrapper for a link.
	 *
	 * @param string $label
	 * @param string $permalink
	 * @param int    $position
	 *
	 * @return string
	 */
	function eazydocs_get_breadcrumb_item( $label, $permalink, $position = 1 ) {
		// Breadcrumbs are plain-text navigation: strip any markup (e.g. a doc's
		// featured-image/icon) so only the title text is shown.
		$label = wp_strip_all_tags( $label );

		return '<li class="breadcrumb-item" itemprop="itemListElement" itemscope itemtype="http://schema.org/ListItem">
            <a itemprop="item" href="' . esc_url( $permalink ) . '" target="_top">
            <span itemprop="name">' . esc_html( $label ) . '</span></a>
            <meta itemprop="position" content="' . esc_attr($position) . '" />
        </li>';
	}

	function eazydocs_get_breadcrumb_root_title( $label ) {
		return '<li class="breadcrumb-item" itemprop="itemListElement" itemscope itemtype="http://schema.org/ListItem">
             ' . esc_html( wp_strip_all_tags( $label ) ) . '</li>';
	}
}

if ( ! function_exists( 'eazydocs_breadcrumbs' ) ) {
	/**
	 * Docs breadcrumb.
	 *
	 * @return void
	 */
	function eazydocs_breadcrumbs() {
		global $post;
		$home_text  = ezd_get_opt('breadcrumb-home-text');
		$front_page = ! empty( $home_text ) ? esc_html( $home_text ) : esc_html__( 'Home', 'eazydocs' );

		$html = '';
		$args = apply_filters( 'eazydocs_breadcrumbs', [
			'delimiter' => '',
			'home'      => $front_page,
			'before'    => '<li class="breadcrumb-item active">',
			'after'     => '</li>',
		]);

		$breadcrumb_position = 1;

		$html .= '<ol class="breadcrumb" itemscope itemtype="http://schema.org/BreadcrumbList">';
		$html .= eazydocs_get_breadcrumb_item( $args['home'], home_url( '/' ), $breadcrumb_position );
		$html .= $args['delimiter'];

		$docs_page_title = ezd_get_opt( 'docs-page-title' );
		$docs_page_title = ! empty( $docs_page_title ) ? esc_html( $docs_page_title ) : esc_html__( 'Docs', 'eazydocs' );

		$docs_home = ezd_get_opt( 'docs-slug' );

		if ( $docs_home ) {
			++ $breadcrumb_position;

			$html .= eazydocs_get_breadcrumb_item( $docs_page_title, get_permalink( $docs_home ), $breadcrumb_position );
			$html .= $args['delimiter'];
		}

		if ( 'docs' === $post->post_type && $post->post_parent ) {
			$parent_id   = $post->post_parent;
			$breadcrumbs = [];

			while ( $parent_id ) {
				++ $breadcrumb_position;
				$page          = get_post( $parent_id );
				$breadcrumbs[] = eazydocs_get_breadcrumb_item( get_the_title( $page->ID ), get_permalink( $page->ID ), $breadcrumb_position );
				$parent_id     = $page->post_parent;
			}

			$breadcrumbs = array_reverse( $breadcrumbs );

			for ( $i = 0; $i < count( $breadcrumbs ); ++ $i ) {
				$html .= $breadcrumbs[ $i ];
				$html .= ' ' . $args['delimiter'] . ' ';
			}
		}

		$html .= ' ' . $args['before'] . esc_html( wp_strip_all_tags( get_the_title() ) ) . $args['after'];

		$html .= '</ol>';

		echo wp_kses_post(apply_filters( 'eazydocs_breadcrumbs_html', $html, $args ));
	}
}

/**
 * Doc Search Breadcrumbs
 */
if ( ! function_exists( 'eazydocs_search_breadcrumbs' ) ) {
	/**
	 * Docs Search breadcrumb.
	 *
	 * @return void
	 */
	function eazydocs_search_breadcrumbs() {
		global $post;

		$html = '';
		$args = apply_filters( 'eazydocs_breadcrumbs', [
			'delimiter' => '',
			'before'    => '<li class="breadcrumb-item active">',
			'after'     => '</li>',
		] );

		$breadcrumb_position = 1;

		$html .= '<ol class="breadcrumb eazydocs-search-wrapper" itemscope itemtype="http://schema.org/BreadcrumbList">';
		$html .= $args['delimiter'];

		$docs_page_title = ezd_get_opt( 'docs-page-title', esc_html__( 'Docs', 'eazydocs' ) );
		$docs_home       = ezd_get_opt( 'docs-slug' );

		if ( $docs_home ) {
			++ $breadcrumb_position;
			$html .= $args['delimiter'];
		}

		if ( 'docs' === $post->post_type && $post->post_parent ) {
			$parent_id   = $post->post_parent;
			$breadcrumbs = [];

			while ( $parent_id ) {
				++ $breadcrumb_position;
				$page          = get_post( $parent_id );
				$breadcrumbs[] = eazydocs_get_breadcrumb_item( get_the_title( $page->ID ), get_permalink( $page->ID ), $breadcrumb_position );
				$parent_id     = $page->post_parent;
			}

			$breadcrumbs = array_reverse( $breadcrumbs );

			for ( $i = 0; $i < 2; ++ $i ) {
				$html .= $breadcrumbs[ $i ] ?? '';
			}
		}

		$html .= ' ' . $args['before'] . esc_html( wp_strip_all_tags( get_the_title() ) ) . $args['after'];
		$html .= '</ol>';
		echo wp_kses_post( apply_filters( 'eazydocs_breadcrumbs_html', $html, $args ) );
	}
}

if ( ! function_exists( 'docs_root_title' ) ) {

	/**
	 * Docs Search breadcrumb.
	 *
	 * @return void
	 */
	function docs_root_title() {
		global $post;
		$home_text  = ezd_get_opt( 'breadcrumb-home-text' );
		$front_page = ! empty( $home_text ) ? esc_html( $home_text ) : esc_html__( 'Home', 'eazydocs' );

		$html = '';
		$args = apply_filters( 'eazydocs_breadcrumbs', [
			'delimiter' => '',
			'before'    => '<li class="breadcrumb-item active">',
			'after'     => '</li>',
		] );

		$breadcrumb_position = 1;

		$is_parents = get_ancestors( $post->ID, 'docs' );
		$is_parent  = ! empty( $is_parents ) ? $is_parents[0] : 0;
		if ( 0 === $is_parent ) {
			$parent_id = $post->ID;
		} else {
			$parent_id = $is_parent;
		}

		$html .= '<ol class="breadcrumb eazydocs-breadcrumb-root-title ' . $parent_id . '" itemscope itemtype="http://schema.org/BreadcrumbList">';
		$html .= $args['delimiter'];


		$docs_page_title = ezd_get_opt( 'docs-page-title' );
		$docs_page_title = ! empty( $docs_page_title ) ? esc_html( $docs_page_title ) : esc_html__( 'Docs', 'eazydocs' );

		if ( 'docs' === $post->post_type && $post->post_parent ) {
			$parent_id   = $post->post_parent;
			$breadcrumbs = [];

			while ( $parent_id ) {
				++ $breadcrumb_position;
				$page          = get_post( $parent_id );
				$breadcrumbs[] = eazydocs_get_breadcrumb_root_title( get_the_title( $page->ID ) );
				$parent_id     = $page->post_parent;
			}

			$breadcrumbs = array_reverse( $breadcrumbs );

			for ( $i = 0; $i < 1; ++ $i ) {
				$html .= $breadcrumbs[ $i ];
				$html .= ' ' . $args['delimiter'] . ' ';
			}
		}

		$html .= ' ' . $args['before'] . get_the_title() . $args['after'];

		$html .= '</ol>';

		echo wp_kses_post( apply_filters( 'eazydocs_breadcrumbs_html', $html, $args ) );
	}
}

/**
 * Get the unfiltered value of a global $post's key
 *
 * Used most frequently when editing a forum/topic/reply
 *
 * @param string $field   Name of the key
 * @param string $context How to sanitize - raw|edit|db|display|attribute|js
 *
 * @return string Field value
 * @since 1.0.1 EazyDocs
 *
 */
function eazydocs_get_global_post_field( $field = 'ID', $context = 'edit' ) {
	// Get the post, and maybe get a field from it
	$post   = get_post();
	$retval = isset( $post->{$field} )
		? sanitize_post_field( $field, $post->{$field}, $post->ID, $context )
		: '';

	// Filter & return
	return apply_filters( 'eazydocs_get_global_post_field', $retval, $post, $field, $context );
}

/**
 * Check if text contains a EazyDocs shortcode.
 *
 * Loops through registered EazyDocs shortcodes and keeps track of which ones
 * were used in a blob of text. If no text is passed, the current global post
 * content is assumed.
 *
 * A preliminary strpos() is performed before looping through each shortcode, to
 * prevent unnecessarily processing.
 *
 * @param string $text
 *
 * @return bool
 * @since 1.0.1
 *
 */
function eazydocs_has_shortcode( $text = '' ) {
	// Default return value
	$retval = false;
	$found  = [];

	// Fallback to global post_content
	if ( empty( $text ) && is_singular() ) {
		$text = eazydocs_get_global_post_field( 'post_content', 'raw' );
	}

	// Skip if empty, or string doesn't contain the eazydocs shortcode prefix
	if ( ! empty( $text ) && ( false !== strpos( $text, '[eazydocs' ) ) ) {

		// Get possible shortcodes
		$codes = [ 'eazydocs', 'eazydocs_tab' ];

		// Loop through codes
		foreach ( $codes as $code ) {

			// Looking for shortcode in text
			if ( has_shortcode( $text, $code ) ) {
				$retval  = true;
				$found[] = $code;
			}
		}
	}

	// Filter & return
	return (bool) apply_filters( 'eazydocs_has_shortcode', $retval, $found, $text );
}

/**
 * @param $a
 * @param $b
 *
 * @return false|int
 */
function ezd_date_sort( $a, $b ) {
	return strtotime( $b ) - strtotime( $a );
}

/**
 * @param $a
 * @param $b
 *
 * @return bool
 */
function ezd_main_date_sort( $a, $b ) {
	$date1 = DateTime::createFromFormat( 'd/m/Y', $a );
	$date2 = DateTime::createFromFormat( 'd/m/Y', $b );

	return $b > $a ? 1 : - 1;
}

/**
 * Visible EazyDocs Menu in classic mode
 * Tag submenu in Tag screen
 **/
add_action( 'admin_footer', function () {
	?>
    <script>
    // EazyDocs screen URL
    eazyDocsClassic = "edit.php?post_type=docs";
    // Tag screen URL
    eazyDocsTag = "edit-tags.php?taxonomy=doc_tag&post_type=docs";

    // EazyDocs menu active when it's EazyDocs screen
    if (window.location.href.indexOf(eazyDocsTag) > -1) {
        jQuery('.toplevel_page_eazydocs').removeClass('wp-not-current-submenu').addClass(
            'wp-has-current-submenu wp-menu-open').find('li').has('a[href*="taxonomy=doc_tag"]').addClass('current');
    }

    // Tag Sub menu active when it's Tag screen
    if (window.location.href.indexOf(eazyDocsClassic) > -1) {
        jQuery('.toplevel_page_eazydocs').removeClass('wp-not-current-submenu').addClass(
            'wp-has-current-submenu wp-menu-open').find('li.wp-first-item').addClass('current');
    }
    </script>
<?php } );

/**
 * Get all docs without parent
 *
 * @return string
 */
function eazydocs_pro_doc_list() {
	$args = [
		'posts_per_page' => - 1,
		'post_type'      => [ 'docs' ],
		'post_parent'    => 0
	];
	$docs      		= get_posts( $args );
	$doc_item_count = 0;
	$doc_items 		= '<option value="">' . esc_html__( 'Select a doc', 'eazydocs' ) . '</option>';

	// Batch-fetch direct child counts for all parent docs in one grouped query
	// (avoids an N+1 lookup inside the loop below).
	$child_counts = [];
	$parent_ids   = wp_list_pluck( $docs, 'ID' );
	if ( ! empty( $parent_ids ) ) {
		global $wpdb;
		$placeholders = implode( ', ', array_fill( 0, count( $parent_ids ), '%d' ) );
		$rows = $wpdb->get_results(
			$wpdb->prepare(
				"SELECT post_parent, COUNT(*) AS total
				 FROM {$wpdb->posts}
				 WHERE post_type = 'docs'
				   AND post_status = 'publish'
				   AND post_parent IN ($placeholders)
				 GROUP BY post_parent",
				...$parent_ids
			)
		);
		foreach ( $rows as $row ) {
			$child_counts[ (int) $row->post_parent ] = (int) $row->total;
		}
	}

	foreach ( $docs as $doc ) {
		if ( ! get_page_by_path( $doc->post_name, OBJECT, 'onepage-docs' ) ) {
			$doc_item_count ++;
			$child_count = $child_counts[ $doc->ID ] ?? 0;
			$label       = $doc->post_title . ' (' . $child_count . ')';
			$doc_items  .= '<option _wpnonce="' . esc_attr( wp_create_nonce( 'ezd_make_onepage' ) ) . '" value="' . esc_attr( $doc->ID ) . '" data-child-count="' . esc_attr( $child_count ) . '">' . esc_html( $label ) . '</option>';
		}
	}
	if ( 0 === $doc_item_count ) {
		$doc_items = '<option id="no-more-doc-available" value="no-more-doc-available">No doc available!</option>';
	}

	return $doc_items;
}

/**
 * @param $doc_id
 */
function eazydocs_one_page( $doc_id ) {
	$one_page_title = get_the_title( $doc_id );
	$docs           = get_post( $doc_id );
	$post_name      = $docs->post_name;
	$post_status    = get_post_status( $doc_id );

	$one_page_docs = get_posts( [
		'post_type'   => 'onepage-docs',
		'post_status' => 'publish',
		'name'        => $post_name,
	] );

	if ( 'draft' !== $post_status ) :
		if ( count( $one_page_docs ) < 1 ) :

			// Generate a secure URL with nonce
			$onepage_url = wp_nonce_url(
				add_query_arg(
					[
						'parentID'         => $doc_id,
						'single_doc_title' => $one_page_title,
						'make_onepage'     => 'yes',
					],
					admin_url( 'admin.php' )
				),
				'ezd_make_onepage' // must match wp_verify_nonce action name
			);
			?>
			<button
				class="button button-info one-page-doc"
				id="one-page-doc"
				name="submit"
				data-url="<?php echo esc_url( $onepage_url ); ?>">
				<?php esc_html_e( 'Make OnePage Doc', 'eazydocs' ); ?>
			</button>
			<?php
		else :
			foreach ( $one_page_docs as $single_docs ) :
				?>
				<a
					class="button button-info view-page-doc"
					id="view-page-doc"
					href="<?php echo esc_url( get_permalink( $single_docs ) ); ?>"
					target="_blank">
					<?php esc_html_e( 'View OnePage Doc', 'eazydocs' ); ?>
				</a>
				<?php
			endforeach;
		endif;
	endif;
}

/**
 * Convert hexdec color string to rgb(a) string.
 *
 * @deprecated 2.9.0 Use CSS custom properties instead: var(--ezd_brand_color_XX) where XX is the opacity percentage.
 *                   CSS color-mix() function is now used in SCSS to generate dynamic RGBA colors
 *                   that automatically inherit from the --ezd_brand_color CSS variable.
 *                   This function is kept for backward compatibility only.
 *
 * @param string $color   The hex color value (with or without #).
 * @param float  $opacity Optional. The opacity value (0-1). Default false.
 *
 * @return string RGB or RGBA color string.
 */
function ezd_hex2rgba( $color, $opacity = false ) {
	$default = 'rgb(0,0,0)';

	//Return default if no color provided
	if ( empty( $color ) ) {
		return $default;
	}

	//Sanitize $color if "#" is provided
	if ( '#' === $color[0] ) {
		$color = substr( $color, 1 );
	}

	//Check if color has 6 or 3 characters and get values
	if ( 6 === strlen( $color ) ) {
		$hex = [ $color[0] . $color[1], $color[2] . $color[3], $color[4] . $color[5] ];
	} elseif ( 3 === strlen( $color ) ) {
		$hex = [ $color[0] . $color[0], $color[1] . $color[1], $color[2] . $color[2] ];
	} else {
		return $default;
	}

	//Convert hexadec to rgb
	$rgb = array_map( 'hexdec', $hex );

	//Check if opacity is set(rgba or rgb)
	if ( $opacity ) {
		if ( abs( $opacity ) > 1 ) {
			$opacity = 1.0;
		}
		$output = 'rgba(' . implode( ",", $rgb ) . ',' . $opacity . ')';
	} else {
		$output = implode( ",", $rgb );
	}

	//Return rgb(a) color string
	return $output;
}

/**
 * Determine whether a hex color is visually dark.
 *
 * Used to decide whether light (white) text is legible over a background color.
 * Uses the perceived-brightness (YIQ) formula. Empty or unparseable values
 * (e.g. an rgba() string) are treated as dark so existing dark styling is preserved.
 *
 * @param string $hex       Hex color, with or without leading "#", 3 or 6 digits.
 * @param int    $threshold Brightness cutoff (0-255); below this is "dark". Default 140.
 *
 * @return bool True when the color is dark (or cannot be parsed).
 */
function ezd_is_dark_color( $hex, $threshold = 140 ) {
	$hex = ltrim( (string) $hex, '#' );

	if ( 3 === strlen( $hex ) ) {
		$hex = $hex[0] . $hex[0] . $hex[1] . $hex[1] . $hex[2] . $hex[2];
	}

	if ( 6 !== strlen( $hex ) || ! ctype_xdigit( $hex ) ) {
		return true; // Unknown format — assume dark to preserve default styling.
	}

	$r = hexdec( substr( $hex, 0, 2 ) );
	$g = hexdec( substr( $hex, 2, 2 ) );
	$b = hexdec( substr( $hex, 4, 2 ) );

	// Perceived brightness (YIQ).
	$brightness = ( ( $r * 299 ) + ( $g * 587 ) + ( $b * 114 ) ) / 1000;

	return $brightness < $threshold;
}

/**
 * Get all registered sidebars
 *
 * @return string
 */
function sidebar_selectbox() {
	global $wp_registered_sidebars;
	$sidebars = '';
	foreach ( $wp_registered_sidebars as $wp_registered_sidebar ) {
		$sidebars .= '<option value="' . $wp_registered_sidebar['id'] . '">' . $wp_registered_sidebar['name'] . '</option>';
	}

	return $sidebars;
}

/**
 * Get all registered reusable blocks
 *
 * @return string
 */
function get_reusable_blocks() {
	$wp_registered_blocks = get_posts(
		array(
			'post_type'      => array( 'wp_block', 'wp_pattern' ),
			'post_status'    => array( 'publish', 'private' ),
			'posts_per_page' => -1,
			'orderby'        => 'title',
			'order'          => 'ASC',
		)
	);
	if ( ! empty ( $wp_registered_blocks ) ) {
		$sidebars = '';
		foreach ( $wp_registered_blocks as $wp_registered_block ) {
			$sidebars .= '<option value="' . $wp_registered_block->ID . '">' . $wp_registered_block->post_title . '</option>';
		}
		$return_output
			= '<label for="ezd-shortcode"> Select a Reusable Block (Optional) </label><br><select name="ezd_sidebar_select_data" id="left_side_sidebar" class="widefat">'
			  . $sidebars . '</select>';

		return $return_output;
	} else {
		return $return_output
			= '<label for="ezd-shortcode"> Select a Reusable Block (Optional) </label><br><select name="ezd_sidebar_select_data" id="left_side_sidebar" class="widefat"><option>No block found!</option></select>';
	}
}


/**
 * Get all registered reusable blocks for the right sidebar.
 *
 * @return string HTML option tags for the select box.
 */
function get_reusable_blocks_right() {
	$wp_registered_blocks = get_posts(
		array(
			'post_type'      => array( 'wp_block', 'wp_pattern' ),
			'post_status'    => array( 'publish', 'private' ),
			'posts_per_page' => -1,
			'orderby'        => 'title',
			'order'          => 'ASC',
		)
	);
	if ( ! empty( $wp_registered_blocks ) ) {
		$sidebars = '';

		foreach ( $wp_registered_blocks as $wp_registered_block ) {
			$sidebars .= '<option value="' . $wp_registered_block->ID . '">' . $wp_registered_block->post_title . '</option>';
		}

		$return_output
			= '<label for="ezd-shortcode"> Select a Reusable Block (Optional) </label><br><select  name="ezd_sidebar_select_data_right" id="right_side_sidebar" class="widefat">'
			  . $sidebars . '</select>';

		return $return_output;
	} else {
		return $return_output
			= '<label for="ezd-shortcode"> Select a Reusable Block (Optional) </label><br><select name="ezd_sidebar_select_data_right" id="right_side_sidebar" class="widefat"><option>No block found!</option></select>';
	}
}

/**
 * Get reusable blocks as structured options for React UIs.
 *
 * @return array<int, array{id: string, title: string}>
 */
function ezd_get_reusable_blocks_options() {
	$wp_registered_blocks = get_posts(
		array(
			'post_type'      => array( 'wp_block', 'wp_pattern' ),
			'post_status'    => array( 'publish', 'private' ),
			'posts_per_page' => -1,
			'orderby'        => 'title',
			'order'          => 'ASC',
		)
	);

	if ( empty( $wp_registered_blocks ) ) {
		return array();
	}

	return array_map(
		static function ( $wp_registered_block ) {
			$title = isset( $wp_registered_block->post_title ) ? wp_strip_all_tags( $wp_registered_block->post_title ) : '';

			return array(
				'id'    => (string) $wp_registered_block->ID,
				'title' => '' !== $title ? $title : esc_html__( '(Untitled)', 'eazydocs' ),
			);
		},
		$wp_registered_blocks
	);
}

/**
 * Get the link to manage reusable blocks.
 *
 * @return string HTML link to the reusable blocks admin page.
 */
function ezd_manage_reusable_blocks() {
	$admin_url = admin_url( 'edit.php?post_type=wp_block' );
	/* translators: %s: URL for managing reusable blocks */
	$message = sprintf(
		'<p class="ezd-text-support"><a href="%s" target="_blank">%s</a></p>',
		esc_url( $admin_url ),
		esc_html__( 'Manage Reusable blocks', 'eazydocs')
	);

	return $message;
}

/**
 * Get all registered sidebars
 *
 * @return string
 */
function ezd_edit_sidebar_selectbox() {
	global $wp_registered_sidebars;
	global $post;
	$edit_sidebars = '';
	foreach ( $wp_registered_sidebars as $wp_registered_sidebar ) {
		$edit_sidebars .= '<option value="' . $wp_registered_sidebar['id'] . '">' . $wp_registered_sidebar['name'] . '</option>';
	}

	return $edit_sidebars;
}

//CUSTOM META BOX
add_action( 'add_meta_boxes', function () {
	add_meta_box( 'EZD OnePage Options', 'EZD OnePage Options', 'ezd_onepage_docs', 'onepage-docs' );
} );

global $post;
function ezd_onepage_docs() {
	?>
    <p>
        <label for="ezd_doc_layout"><?php esc_html_e( 'Doc Layout', 'eazydocs' ); ?></label><br/>
        <input type="text" disabled name="ezd_doc_layout" id="ezd_doc_layout" value="<?php echo esc_attr( get_post_meta( get_the_ID(), 'ezd_doc_layout', true ) ); ?>" class="widefat"/>
    </p> <br>

    <p class="ezd_left_content_heading"> <?php esc_html_e( 'Left Side Content', 'eazydocs' ); ?></p>

    <p>
        <label for="ezd_doc_content_type"><?php esc_html_e( 'Content Type', 'eazydocs' ); ?></label><br/>
        <input type="text" disabled name="ezd_doc_content_type" id="ezd_doc_content_type" value="<?php echo esc_attr( get_post_meta( get_the_ID(), 'ezd_doc_content_type', true ) ); ?>" class="widefat"/>
    </p>

    <p>
        <label for="ezd_doc_left_sidebar"><?php esc_html_e( 'Content Box', 'eazydocs' ); ?></label><br/>
        <textarea name="ezd_doc_left_sidebar" id="ezd_doc_left_sidebar" disabled cols="30" rows="3" class="widefat">
            <?php echo esc_attr( get_post_meta( get_the_ID(), "ezd_doc_left_sidebar", true ) ); ?>
        </textarea>
    </p>

    <p class="ezd_left_content_heading"> <?php esc_html_e( 'Right Side Content', 'eazydocs' ); ?></p>

    <p>
        <label for="ezd_doc_content_type_right"><?php esc_html_e( 'Content Type', 'eazydocs' ); ?></label><br/>
        <input type="text" disabled name="ezd_doc_content_type_right" id="ezd_doc_content_type_right" value="<?php echo esc_attr( get_post_meta( get_the_ID(), 'ezd_doc_content_type_right', true ) ); ?>" class="widefat"/>
    </p>
    <p>
        <label for="ezd_doc_content_box_right"><?php esc_html_e( 'Content Box', 'eazydocs' ); ?></label><br/>
        <textarea disabled name="ezd_doc_content_box_right" id="ezd_doc_content_box_right" cols="30" rows="3" class="widefat">
            <?php echo esc_attr( get_post_meta( get_the_ID(), 'ezd_doc_content_box_right', true ) ); ?>
        </textarea>
    </p>
    <?php
}

add_action( 'save_post', function ( $post_id ) {
	// Doc Options
	if ( 'onepage-docs' !== get_post_type( $post_id ) ) {
		return;
	}

	$std_comment_id             = $_POST['ezd_doc_layout'] ?? '';
	$ezd_doc_content_type       = $_POST['ezd_doc_content_type'] ?? '';
	$ezd_doc_content_type_right = $_POST['ezd_doc_content_type_right'] ?? '';
	$ezd_doc_content_box_right  = $_POST['ezd_doc_content_box_right'] ?? '';

	if ( isset( $_POST['ezd_doc_layout'] ) ) {
		update_post_meta( $post_id, 'ezd_doc_layout', $std_comment_id );
	}

	if ( isset( $_POST['ezd_doc_content_type'] ) ) {
		update_post_meta( $post_id, 'ezd_doc_content_type', $ezd_doc_content_type );
	}

	if ( isset( $_POST['ezd_doc_content_type_right'] ) ) {
		update_post_meta( $post_id, 'ezd_doc_content_type_right', $ezd_doc_content_type_right );
	}

	if ( isset( $_POST['ezd_doc_content_box_right'] ) ) {
		update_post_meta( $post_id, 'ezd_doc_content_box_right', wp_kses_post( $ezd_doc_content_box_right ) );
	}	

} );

add_image_size( 'ezd_searrch_thumb16x16', '16', '16', true );
add_image_size( 'ezd_searrch_thumb50x50', '50', '50', true );

/**
 * Doc password form
 *
 * @param string      $output The password form HTML.
 * @param int|WP_Post $post   Optional. Post ID or WP_Post object. Default 0.
 *
 * @return string
 */
function ezd_password_form( $output, $post = 0 ) {

    // Check if post is set and is the desired custom post type
    if ( is_null( $post ) || ( 'docs' !== get_post_type( $post ) ) ) {
        // If it's not the correct post type, return the original output
        return $output;
    }

	$protected_form_switcher = ezd_is_premium() ? ezd_get_opt( 'protected_doc_form' ) : 'default';
	$protected_form_title    = ezd_is_premium() ? ezd_get_opt( 'protected_form_title', esc_html__( 'Enter Password & Read this Doc', 'eazydocs' ) ) : esc_html__( 'Enter Password & Read this Doc', 'eazydocs' );
	$protected_form_subtitle = ezd_is_premium() ? ezd_get_opt( 'protected_form_subtitle', esc_html__( 'This content is password protected. To view it please enter your password below:', 'eazydocs' ) ) : esc_html__( 'This content is password protected. To view it please enter your password below:', 'eazydocs' );

	if ( 'eazydocs-form' === $protected_form_switcher ) :
		ob_start();
		?>
		<div class="card ezd-password-wrap">
			<div class="card-body p-0 ezd-password-head">
				<div class="text-center p-4">
					<?php
					if ( has_post_thumbnail() ) :
						?>
                        <?php the_post_thumbnail( 'ezd_searrch_thumb50x50', [ 'class' => 'pw-logo' ] ); ?>
						<?php
					endif;
					?>
					<p class="mb-1 ezd-password-title">
						<?php echo esc_html( $protected_form_title ); ?>
					</p>
					<p class="mb-0 ezd-password-subtitle">
						<?php echo esc_html( $protected_form_subtitle ); ?>
					</p>
				</div>
			</div>
			<div class="card-body ezd-password-body p-4">
				<form action="<?php echo esc_url( site_url( 'wp-login.php?action=postpass', 'login_post' ) ); ?>" method="post" class="form-horizontal auth-form ezd-password-form">
					<div class="form-group mb-2">
						<label class="form-label" for="ezd_password"> <?php esc_html_e( 'Password', 'eazydocs' ); ?> </label>
						<div class="input-group mb-3">
							<input name="post_password" required id="ezd_password" class="form-control" type="password" placeholder="Enter password" />
						</div>
					</div>
					<div class="form-group mb-0 row">
						<div class="col-12">
							<button class="btn btn-primary w-100 waves-effect waves-light" type="submit"> <?php esc_html_e( 'Unlock', 'eazydocs' ); ?> <i class="fas fa-sign-in-alt ms-1"></i> </button>
						</div>
					</div>
				</form></div>
			</div>
		<?php
		return ob_get_clean();
	endif;
	return $output;
}

add_filter( 'the_password_form', 'ezd_password_form', 9999 );

/**
 * Check if current page is an EazyDocs admin page.
 *
 * @param string|array $pages Optional. Specific pages to check against. Default empty array.
 * @return bool True if on an EazyDocs admin page, false otherwise.
 */
function ezd_admin_pages( $pages = [] ) {
    // if $pages is string, convert it to an array
    if ( is_string( $pages ) ) {
        $pages = [ $pages ];
    }

    if ( empty( $pages ) ) {
        // Default admin pages of EazyDocs
	    $admin_pages = !empty($_GET['page']) ? in_array( sanitize_text_field( $_GET['page'] ), [
		    'eazydocs-builder', 'eazydocs-settings', 'ezd-user-feedback', 'ezd-user-feedback-archived',
            'ezd-analytics', 'ezd-onepage-presents', 'onepage-docs', 'eazydocs-initial-setup', 'eazydocs-account', 'eazydocs-migration', 'eazydocs-import-export', 'ezd-faq-builder', 'ezd-integrated-themes', 'eazydocs'
	    ], true ) : '';
    } else {
        // Selected admin pages of EazyDocs
	    $admin_pages = !empty($_GET['page']) ? in_array( sanitize_text_field( $_GET['page'] ), $pages, true ) : '';
    }

	if ( $admin_pages ) {
		return true;
	}
}

/**
 * Get EazyDocs post type pages in admin
 * @param $post_type
 *
 * @return true|void
 */
function ezd_admin_post_types( $post_types = [] ) {
    // if $post_types is string, convert it to an array
    if ( is_string( $post_types ) ) {
        $post_types = [ $post_types ];
    }

    if ( empty( $post_types ) ) {
        // Default post types of EazyDocs
        $admin_post_types = !empty($_GET['post_type']) ? in_array( sanitize_text_field( $_GET['post_type'] ), [
            'docs', 'onepage-docs'
        ], true ) : '';
    } else {
        // Selected post types of EazyDocs
        $admin_post_types = !empty($_GET['post_type']) ? in_array( sanitize_text_field( $_GET['post_type'] ), $post_types, true ) : '';
    }

    if ( $admin_post_types ) {
        return true;
    }
}

/**
 * Get EazyDocs taxonomy pages in admin
 * @param $tax
 *
 * @return true|void
 */
function ezd_admin_taxonomy( $tax = [] ) {
    // if $tax is string, convert it to an array
    if ( is_string( $tax ) ) {
        $tax = [ $tax ];
    }

    if ( empty( $tax ) ) {
        // Default taxonomies of EazyDocs
        $admin_tax = !empty($_GET['taxonomy']) ? in_array( sanitize_text_field( $_GET['taxonomy'] ), [
            'doc_tag', 'doc_category', 'doc_badge'
        ], true ) : '';
    } else {
        // Selected taxonomies of EazyDocs
        $admin_tax = !empty($_GET['taxonomy']) ? in_array( sanitize_text_field( $_GET['taxonomy'] ), $tax, true ) : '';
    }

    if ( $admin_tax ) {
        return true;
    }
}

/**
 * EazyDocs Frontend Assets
 *
 * @return bool|void
 */
function ezd_frontend_pages() {
	if ( is_singular('docs') || is_singular('onepage-docs') || is_page_template('page-onepage.php') ) {
		return true;
	}
}

/**
 * EazyDocs Shortcodes
 *
 * @return bool|void
 */
function ezd_has_shortcode( $shortcodes = [] ) {
	global $post;
	$post_content_check = $post->post_content ?? '';

	if ( ! empty( $shortcodes ) && is_array( $shortcodes ) ) {
		foreach ( $shortcodes as $shortcode ) {
			if ( has_shortcode( $post_content_check, $shortcode ) ) {
				return true;
			}
		}
	}

	return false; // Explicitly return false if no shortcode is found
}


/**
 * Get all posts
 *
 * @param $post_type
 *
 * @return array
 */
function ezd_get_posts( $post_type = 'docs' ) {
	$docs       = get_pages(
		[
			'post_type'   => $post_type,
			'numberposts' => - 1,
			'post_status' => [ 'publish', 'private' ],
			'parent'      => 0,
		]
	);
	$docs_array = [];
	if ( $docs ) {
		foreach ( $docs as $doc ) {
			$docs_array[ $doc->ID ] = $doc->post_title;
		}
	}

	return $docs_array;
}

function ezd_widget_excerpt( $settings_key, $limit = 10 ) {
	echo wp_kses_post( wp_trim_words( wpautop( get_the_excerpt( $settings_key ) ), $limit, '' ) );
}

/**
 * Get arrow icon based on text direction.
 *
 * @return string The arrow icon class name.
 */
function ezd_arrow() {
    $arrow_icon = is_rtl() ? 'arrow_left' : 'arrow_right';
    return esc_attr( $arrow_icon );
}


/**
 * Elementor Title Tag Options
 *
 * @return array
 */
function ezd_el_title_tags() {
	return [
		'h1'   => 'H1',
		'h2'   => 'H2',
		'h3'   => 'H3',
		'h4'   => 'H4',
		'h5'   => 'H5',
		'h6'   => 'H6',
		'div'  => 'Div',
		'span' => 'Span',
		'p'    => 'P'
	];
}

/**
 * Get Default Image Elementor
 *
 * @param string $settings_key
 * @param string $alt
 * @param string $class
 * @param array  $atts
 */
function ezd_el_image( $settings_key = '', $alt = '', $class = '', $atts = [] ) {
	if ( ! empty( $settings_key['id'] ) ) {
		echo wp_get_attachment_image( $settings_key['id'], 'full', '', [ 'class' => $class ] );
	} elseif ( ! empty( $settings_key['url'] ) && empty( $settings_key['id'] ) ) {
		$class = ! empty( $class ) ? "class='$class'" : '';
		$attss = '';
		//echo print_r($atts);
		if ( ! empty( $atts ) ) {
			foreach ( $atts as $k => $att ) {
				$attss .= esc_attr( $k ) . '="' . esc_attr( $att ) . '" ';
			}
		}
		echo '<img src="' . esc_url( $settings_key['url'] ) . '" ' . esc_attr( $class ) . ' alt="' . esc_attr( $alt ) . '" ' . esc_attr( trim( $attss ) ) . '>';
	}
}

/**
 * Get Nestable Parent ID
 *
 * @param $page_id
 *
 * @return mixed
 */
function eaz_get_nestable_parent_id( $page_id ) {
	global $wpdb;
	// Ensure that $page_id is an integer
	$page_id = intval($page_id);

	// Prepare the SQL statement using placeholders
	// @codingStandardsIgnoreLine WordPress.DB.DirectDatabaseQuery.DirectQuery
	$query = $wpdb->prepare( "SELECT post_parent FROM $wpdb->posts WHERE post_type='docs' AND  ID = %d", $page_id );

	// Execute the query
	// @codingStandardsIgnoreLine WordPress.DB.DirectDatabaseQuery.DirectQuery
	$parent = (int) $wpdb->get_var( $query );

	if ( 0 === $parent ) {
		return $page_id;
	} else {
		return eaz_get_nestable_parent_id( $parent );
	}
}

/**
 * Get all children of a page
 *
 * @param $post_id
 *
 * @return array[]|int[]|WP_Post[]
 */
function eaz_get_nestable_children( $post_id ) {
	return get_children( [
		'post_parent' => $post_id,
		'post_type'   => 'docs',
		'orderby'     => 'menu_order',
		'order'       => 'ASC',
	] );
}

/**
 * Get all shortcodes from content
 *
 * @param string $content
 *
 * @return string all shortcodes from content
 */
function ezd_all_shortcodes( $content ) {
	$return = [];
	preg_match_all( '/' . get_shortcode_regex() . '/', $content, $shortcodes, PREG_SET_ORDER );
	if ( ! empty( $shortcodes ) ) {
		foreach ( $shortcodes as $shortcode ) {
			$return[] = $shortcode;
			$return   = array_merge( $return, ezd_all_shortcodes( $shortcode[5] ) );
		}
	}

	return $return;
}

/**
 * Allowed HTML for docs navigation markup.
 *
 * We generate the docs sidebar tree markup ourselves (via wp_list_pages + custom Walker).
 * Some templates were using wp_kses_post(), which strips <svg> tags and causes our
 * visibility lock icons to disappear.
 *
 * @return array
 */
function ezd_kses_allowed_docs_nav_html() {
	$allowed = wp_kses_allowed_html( 'post' );

	// Allow inline SVG icons used by docs navigation.
	$allowed['svg'] = [
		'class'            => true,
		'xmlns'            => true,
		'width'            => true,
		'height'           => true,
		'viewbox'          => true,
		'viewBox'          => true,
		'fill'             => true,
		'stroke'           => true,
		'stroke-width'     => true,
		'stroke-linecap'   => true,
		'stroke-linejoin'  => true,
		'role'             => true,
		'aria-hidden'      => true,
		'focusable'        => true,
	];
	$allowed['path'] = [
		'd'               => true,
		'fill'            => true,
		'stroke'          => true,
		'stroke-width'    => true,
		'stroke-linecap'  => true,
		'stroke-linejoin' => true,
	];
	$allowed['rect'] = [
		'x'      => true,
		'y'      => true,
		'width'  => true,
		'height' => true,
		'rx'     => true,
		'ry'     => true,
		'fill'   => true,
		'stroke' => true,
	];
	$allowed['circle'] = [
		'cx'     => true,
		'cy'     => true,
		'r'      => true,
		'fill'   => true,
		'stroke' => true,
	];
	$allowed['g'] = [
		'fill'   => true,
		'stroke' => true,
	];

	return apply_filters( 'ezd_kses_allowed_docs_nav_html', $allowed );
}

add_filter( 'body_class', function ( $classes ) {
    if ( ezd_is_premium() ) {
        $classes[] = 'ezd-premium';
    }
    return $classes;
});


// check if block theme activated
function ezd_header_with_block_theme() {
	if ( function_exists( 'block_header_area' ) ) {
		// Include your template part
		block_template_part('header');
		?>
		<style>
			#page > hr,
			#page #header{
				display:none;
			}
		</style>
	<?php
	}
}

/**
 * Footer with block theme
 * @return void
 */
function ezd_footer_with_block_theme(){
	if ( function_exists( 'block_footer_area' ) ) {
		block_template_part('footer');
		?>
		<style>
			#page #footer{
				display:none;
			}
		</style>
		<?php
	}
}

/**
 * Get all templates from Elementor
 * @return array
 */
function ezd_get_elementor_templates() {
	$elementor_templates = get_posts( [
		'post_type' 		=> 'elementor_library',
		'posts_per_page' 	=> -1,
		'status' 			=> 'publish'
	] );

	$elementor_templates_array = [];
	if ( ! empty( $elementor_templates ) ) {
		foreach ( $elementor_templates as $elementor_template ) {
			$elementor_templates_array[$elementor_template->ID] = $elementor_template->post_title;
		}
	}
	return $elementor_templates_array;
}

/**
 * Get all templates from Elementor
 * @return array
 */
function ezd_single_banner($classes) {
	$current_theme = get_template();
	if ( is_single() && 'docs' === get_post_type() && 'el-template' === ezd_get_opt( 'single_doc_layout' ) && ! empty( ezd_get_opt( 'single_layout_id' ) ) && 'docly' === $current_theme ) {
		$classes[] = 'disable-docly-header';
    }
    return $classes;
}
add_filter('body_class', 'ezd_single_banner');

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
		
		// Access denied - show message
		if ( is_singular( 'docs' ) ) {
			$denied_message = ezd_get_opt( 'role_visibility_denied_message', esc_html__( "You don't have permission to access this document!", 'eazydocs' ) );
			$output = sprintf( '<div class="ezd-lg-col-9"><span class="ezd-doc-warning-wrap"><i class="icon_lock"></i><span>%s</span></span></div>', esc_html( $denied_message ) );
			echo wp_kses_post( $output );
		}
		return null;
	}
	return true;
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

		if ($current_user_id === $post_author_id || current_user_can('manage_options') ) {
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
 * Get doc parent id by current id
 */
function ezd_get_doc_parent_id( $doc_id = 0 ) {

	$parent_id = get_post_ancestors( get_the_ID() );
	$ancestors = end($parent_id);

	if ( ! empty( $ancestors ) ) {
		return $ancestors;
	} else {
		return $doc_id;
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

/**
 * Retrieve the "reference" shortcodes and their content in a post
 *
 * @return string
 *
 * @param int $post_id The ID of the post to retrieve the "reference" shortcodes from
 */
function ezd_get_footnotes_in_content($post_id) {
    // Retrieve the post by its ID
    $post = get_post($post_id);

    // Check if the post exists
    if (!$post) {
        return [];
    }

    // Get the content of the post
    $content = $post->post_content;

    // Regular expressions for [reference] shortcodes and target span elements
    $shortcode_pattern = '/\[reference([^\]]*)\](.*?)\[\/reference\]/s';
    $span_pattern = '/<span id="serial-id-(\d+)" class="ezd-footnotes-link-item" data-bs-original-title=".*?">.*?<span class="ezd-footnote-content">(.*?)<\/span><\/span>/s';

    $references = [];

    // Extract spans
    if (preg_match_all($span_pattern, $content, $span_matches, PREG_SET_ORDER)) {
        foreach ($span_matches as $match) {
            $references[] = [
                'id'      => $match[1], // Serial ID
                'content' => $match[2], // Footnote content
                'source'  => 'span',
            ];
        }
    }

    // Extract [reference] shortcodes
    if (preg_match_all($shortcode_pattern, $content, $matches, PREG_SET_ORDER)) {
        foreach ($matches as $match) {
            $attributes = $match[1];
            $shortcode_content = $match[2];

            // Extract the `number` attribute
            $number = null;
            if (preg_match('/number=["\']?(\d+)["\']?/', $attributes, $number_match)) {
                $number = $number_match[1];
            }

            // Add the reference regardless of the existence of spans
            $references[] = [
                'id'      => $number,
                'content' => $shortcode_content,
                'source'  => 'shortcode',
            ];
        }
    }

    return $references;
}


/**
 * Replace footenote number attribute
 */
function ezd_footnote_number_attribute( $content ) {
    return preg_replace('/\[reference number="##"\]/', '[reference number="1"]', $content);
}
add_filter('the_content', 'ezd_footnote_number_attribute');

/**
 * This function dynamically updates footnote content in the single 'docs'
 * It targets <span> elements with specific attributes and checks for <i> tags to add an onclick event.
 */
function ezd_update_footnotes_content($content) {
    // Apply only to single 'docs' post type
    if (is_singular('docs')) {
        // Regular expression to match the required span tag
        $pattern = '/<span id="serial-id-(\d+)" class="ezd-footnotes-link-item" data-bs-original-title="(.*?)">.*?<i(.*?)>(.*?)<\/i>.*?<span class="ezd-footnote-content">(.*?)<\/span><\/span>/s';

        $content = preg_replace_callback($pattern, function ($matches) {
            $id 				= $matches[1];
            $original_title 	= $matches[2];
            $i_attributes 		= $matches[3];
            $i_content 			= $matches[4];
            $footnote_content 	= $matches[5];

            // Check if the <i> tag already has an onclick event
            if (!strpos($i_attributes, 'onclick')) {
                // Add the onclick event
                $i_attributes .= " onclick=\"location.href='#note-name-$id'\"";
            }

            // Return the updated span content
            return sprintf(
                '<span id="serial-id-%d" class="ezd-footnotes-link-item" data-bs-original-title="%s"><i%s>%s</i><span class="ezd-footnote-content">%s</span></span>',
                $id,
                esc_attr($original_title),
                $i_attributes,
                esc_html($i_content),
                wp_kses_post($footnote_content)
            );
        }, $content);
    }

    return $content;
}
add_filter('the_content', 'ezd_update_footnotes_content');

/**
 * Customizer buttons visibility by user role
 * Used in the settings page
 */
function customizer_visibility_callback() {
	$archive_url = 'javascript:void(0)';
	$single_url  = 'javascript:void(0)';
	$target      = '_self';
	$no_access   = 'no-customizer-access';

	if ( current_user_can( 'manage_options' ) ) {
		$archive_id = ezd_get_opt( 'docs-slug' );

		$first_doc = get_posts( [
			'post_type'      => 'docs',
			'posts_per_page' => 1,
			'orderby'        => 'menu_order',
			'order'          => 'ASC',
			'post_status'    => 'publish',
		] );

		$doc_id = ! empty( $first_doc ) ? $first_doc[0]->ID : $archive_id;

		$customize_base = admin_url( 'customize.php?url=' ) . site_url( '/' );
		$archive_url    = $customize_base . '?p=' . $archive_id . '&autofocus[panel]=docs-page&autofocus[section]=docs-archive-page';
		$single_url     = $customize_base . '?p=' . $doc_id . '&autofocus[panel]=docs-page&autofocus[section]=docs-single-page';
		$no_access      = '';
		$target         = '_blank';
	}
	?>
	<a href="<?php echo esc_attr( $archive_url ); ?>" class="<?php echo esc_attr( $no_access ); ?>" target="<?php echo esc_attr( $target ); ?>" id="get_docs_archive">
		<?php echo esc_html__( 'Docs Archive', 'eazydocs' ); ?>
	</a>
	<a href="<?php echo esc_attr( $single_url ); ?>" class="<?php echo esc_attr( $no_access ); ?>" target="<?php echo esc_attr( $target ); ?>" id="get_docs_single">
		<?php echo esc_html__( 'Single Doc', 'eazydocs' ); ?>
	</a>
	<?php
}

/**
 * Setup wizard save settings
 */
function ezd_setup_wizard_save_settings() {

	check_ajax_referer( 'eazydocs-admin-nonce', 'security' );

	if ( ! current_user_can( 'manage_options' ) ) {
		wp_send_json_error( 'Unauthorized user' );
	}

	$rootslug        = isset( $_POST['rootslug'] ) ? sanitize_text_field( $_POST['rootslug'] ) : '';
	$brandColor      = isset( $_POST['brandColor'] ) ? sanitize_text_field( $_POST['brandColor'] ) : '';
	$slugType        = isset( $_POST['slugType'] ) ? sanitize_text_field( $_POST['slugType'] ) : '';
	$docSingleLayout = isset( $_POST['docSingleLayout'] ) ? sanitize_text_field( $_POST['docSingleLayout'] ) : '';
	$docsPageWidth   = isset( $_POST['docsPageWidth'] ) ? sanitize_text_field( $_POST['docsPageWidth'] ) : '';
	$live_customizer = isset( $_POST['live_customizer'] ) ? sanitize_text_field( $_POST['live_customizer'] ) : '';
	// int value
	$archivePage = isset( $_POST['archivePage'] ) ? intval( $_POST['archivePage'] ) : '';
	$options     = get_option( 'eazydocs_settings' );

	// Check if the option exists and is an array
	if ( is_array( $options ) ) {

		// Update the specific setting
		$options['docs-type-slug']         = $rootslug;
		$options['brand_color']            = $brandColor;
		$options['docs-url-structure']     = $slugType;
		$options['docs_single_layout']     = $docSingleLayout;
		$options['docs_page_width']        = $docsPageWidth;
		$options['customizer_visibility']  = $live_customizer;
		$options['docs-slug']              = $archivePage;
		$options['setup_wizard_completed'] = true;

		// Update the option in the database
		update_option( 'eazydocs_settings', $options );
	}

	wp_send_json_success( 'Settings saved' );
}

add_action( 'wp_ajax_ezd_setup_wizard_save_settings', 'ezd_setup_wizard_save_settings' );

/**
 * Add the post thumbnail to the RSS feed for 'docs'
 */
function ezd_add_thumbnail_to_rss_feed() {
    $post_id = get_the_ID();

    // Check if the post type is 'docs' and it has a thumbnail.
    if ('docs' === get_post_type($post_id) && has_post_thumbnail($post_id)) {
        printf(
            '<thumbnail url="%s" />',
            esc_url(get_the_post_thumbnail_url($post_id, 'full'))
        );
    }
}
add_action('rss2_item', 'ezd_add_thumbnail_to_rss_feed');

/*
 * Check if has selected comment class exists 
 */
function has_ezd_mark_text_class() {
    global $post;

    if ( is_singular('docs') && isset($post->post_content) ) {
        return false !== strpos($post->post_content, 'class="ezd_mark_text"');
    }

    return false;
}

/**
 * Render comments template for docs pages
 */
function ezd_get_comments_template() {
	
	// Block theme → render comments block (loads correct styles)
	if ( function_exists( 'wp_is_block_theme' ) && wp_is_block_theme() ) {
		echo do_blocks( '<!-- wp:comments /--><!-- wp:post-comments-form /-->' );
	} else {
		// Classic theme → native comments.php
		comments_template();
	}
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

    foreach ( $wp_roles->roles as $role_key => $role_data ) {
        $role = get_role( $role_key );

        if ( in_array( $role_key, $get_users_role ) ) {
            $role->add_cap( 'read_private_docs' );
        } else {
            $role->remove_cap( 'read_private_docs' );
        }
    }
}
add_action( 'init', 'ezd_read_private_docs_cap_to_user' );

/**
 * Assigns or removes the 'add or edit_docs' capability to user roles
 */
function ezd_docs_cap_to_user() {
	$collaboration_roles = ezd_get_opt( 'ezd_add_editable_roles' );
	$write_access_roles  = ezd_get_opt( 'docs-write-access' );
	$default_roles       = [ 'administrator', 'editor', 'author' ];

	if ( ! is_array( $collaboration_roles ) ) {
		$collaboration_roles = array_filter( [ $collaboration_roles ] );
	}

	if ( ! is_array( $write_access_roles ) ) {
		$write_access_roles = array_filter( [ $write_access_roles ] );
	}

	$active_roles = array_unique( array_merge( $collaboration_roles, $write_access_roles ) );
	$active_roles = ! empty( $active_roles ) ? $active_roles : $default_roles;

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
				$role->add_cap( $cap );
			}

			// Grant Manager capabilities only to roles that can normally edit others' posts
			if ( $role->has_cap( 'edit_others_posts' ) ) {
				foreach ( $manager_caps as $cap ) {
					$role->add_cap( $cap );
				}
			} else {
				foreach ( $manager_caps as $cap ) {
					$role->remove_cap( $cap );
				}
			}
		} else {
			// Remove all documentation capabilities from inactive roles
			$all_caps = array_merge( $author_caps, $manager_caps );
			foreach ( $all_caps as $cap ) {
				$role->remove_cap( $cap );
			}
		}
	}
}
add_action( 'init', 'ezd_docs_cap_to_user' );

/**
 * Admin bar hide for OnePage Docs
 */
add_filter('show_admin_bar', function ( $show ) {
    // Hide admin bar on singular onepage-docs
    if ( is_singular('onepage-docs') ) {
        return false;
    }

    // Only show admin bar if user is logged in
    return is_user_logged_in();
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
                
                // If mode is 'login', redirect to login page instead of showing 404
                if ( 'login' === $private_doc_mode ) {
                    $login_page_id = ezd_get_opt( 'private_doc_login_page', '' );
                    
                    if ( ! empty( $login_page_id ) ) {
                        $login_page_url = get_permalink( $login_page_id );
                        
                        if ( $login_page_url ) {
                            // Add redirect parameters
                            $permalink_structure = get_option( 'permalink_structure' );
                            $separator = empty( $permalink_structure ) ? '&' : '?';
                            $redirect_url = $login_page_url . $separator . 'post_id=' . $post->ID . '&private_doc=yes';
                            
                            wp_safe_redirect( $redirect_url );
                            exit;
                        }
                    }
                    
                    // Fallback to WordPress login if no custom login page set
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

/**
 * Sanitize nested objects for use in the admin panel
 *
 * @param array $items Array of items to sanitize.
 * @return array Sanitized array of items.
 */
function ezd_sanitize_nested_objects( $items ) {
	$sanitized = [];

	foreach ( $items as $item ) {
		if ( ! isset( $item->id ) ) {
			continue;
		}

		$sanitized_item = (object) [
			'id' => intval( $item->id )
		];

		if ( isset( $item->children ) && is_array( $item->children ) ) {
			$sanitized_item->children = ezd_sanitize_nested_objects( $item->children );
		}

		$sanitized[] = $sanitized_item;
	}

	return $sanitized;
}


/**
 * Get all descendant IDs by a parent ID
 *
 * @param int    $parent_id   The parent post ID.
 * @param string $post_type   The post type to query (default: 'docs').
 * @param string $post_status The post status to query (default: 'publish').
 *
 * @return array An array of all descendant post IDs.
 */
function ezd_get_all_descendant_ids( $parent_id, $post_type = 'docs', $post_status = 'publish' ) {
    global $wpdb;

    $all_ids = [];

    // Get immediate children IDs
    $children = $wpdb->get_col( $wpdb->prepare(
        "SELECT ID FROM {$wpdb->posts} WHERE post_parent = %d AND post_type = %s AND post_status = %s",
        $parent_id,
        $post_type,
        $post_status
    ));

    if ( ! empty( $children ) ) {
        foreach ( $children as $child_id ) {
            // Add this child ID
            $all_ids[] = $child_id;
            // Recursively get grandchildren and deeper descendants
            $descendants = ezd_get_all_descendant_ids( $child_id, $post_type, $post_status );
            if ( ! empty( $descendants ) ) {
                $all_ids = array_merge( $all_ids, $descendants );
            }
        }
    }

    return $all_ids;
}

/**
 * Active language codes from Polylang or WPML (empty array if neither is active).
 *
 * @return string[]
 */
function ezd_active_language_codes() {
	static $codes = null;
	if ( null !== $codes ) {
		return $codes;
	}

	$codes = [];
	if ( function_exists( 'pll_languages_list' ) ) {
		$codes = (array) pll_languages_list( [ 'fields' => 'slug' ] );
	}
	if ( empty( $codes ) ) {
		$wpml = apply_filters( 'wpml_active_languages', null );
		if ( is_array( $wpml ) ) {
			$codes = array_keys( $wpml );
		}
	}

	$codes = array_values( array_filter( array_map( 'sanitize_key', $codes ) ) );
	return $codes;
}

/**
 * Whether a known multilingual plugin (WPML or Polylang) is active with >1 language.
 *
 * @return bool
 */
function ezd_is_multilingual() {
	return count( ezd_active_language_codes() ) > 1;
}

/**
 * Current request language code, or '' when the site is not multilingual.
 *
 * @return string
 */
function ezd_current_language() {
	if ( function_exists( 'pll_current_language' ) ) {
		$lang = pll_current_language( 'slug' );
		if ( $lang ) {
			return sanitize_key( $lang );
		}
	}
	$wpml = apply_filters( 'wpml_current_language', null );
	return $wpml ? sanitize_key( $wpml ) : '';
}

/**
 * Transient key for the flat docs tree, segmented per language so a cached tree
 * from one language is never served on another.
 *
 * @param string $post_type
 * @return string
 */
function ezd_docs_tree_cache_key( $post_type ) {
	$lang = ezd_current_language();
	return 'ezd_docs_tree_flat_' . $post_type . ( $lang ? '_' . $lang : '' );
}

/**
 * Build the flat, ordered list of doc IDs for a post type.
 *
 * On multilingual sites suppress_filters is disabled so WPML/Polylang scope the
 * query to the current language instead of mixing every translation into one tree.
 *
 * @param string $post_type
 * @return int[]
 */
function ezd_build_docs_tree_flat_ids( $post_type ) {
	$args = [
		'post_type'   => $post_type,
		'post_status' => 'publish',
		'post_parent' => 0,
		'orderby'     => 'menu_order title',
		'order'       => 'ASC',
		'fields'      => 'ids',
		'numberposts' => -1,
	];

	if ( ezd_is_multilingual() ) {
		$args['suppress_filters'] = false;
	}

	$ordered_ids = [];
	foreach ( get_posts( $args ) as $top_id ) {
		ezd_docs_build_tree_flat( $top_id, $ordered_ids );
	}

	return $ordered_ids;
}

/**
 * Get cached flat document tree (array of IDs in order)
 *
 * @param string $post_type
 * @return array
 */
function ezd_get_docs_tree_flat_cached( $post_type ) {
	$cache_key   = ezd_docs_tree_cache_key( $post_type );
	$ordered_ids = get_transient( $cache_key );

	if ( false === $ordered_ids ) {
		$ordered_ids = ezd_build_docs_tree_flat_ids( $post_type );
		set_transient( $cache_key, $ordered_ids, 12 * HOUR_IN_SECONDS );
	}

	return $ordered_ids;
}

/**
 * Delete the flat docs-tree cache for a post type across every active language.
 *
 * @param string $post_type
 */
function ezd_delete_docs_tree_cache_all_langs( $post_type ) {
	delete_transient( 'ezd_docs_tree_flat_' . $post_type );
	foreach ( ezd_active_language_codes() as $code ) {
		delete_transient( 'ezd_docs_tree_flat_' . $post_type . '_' . $code );
	}
}

/**
 * Clear the docs tree cache on post update/delete
 *
 * @param int $post_id
 * @param WP_Post $post
 */
function ezd_clear_docs_tree_cache( $post_id, $post ) {
	if ( in_array( $post->post_type, [ 'docs', 'onepage-docs' ], true ) ) {
		ezd_delete_docs_tree_cache_all_langs( $post->post_type );
	}
}
add_action( 'save_post', 'ezd_clear_docs_tree_cache', 10, 2 );
add_action( 'delete_post', 'ezd_clear_docs_tree_cache', 10, 2 );

/**
 * Get previous and next IDs from an array of IDs
 *
 * @param int $current_post_id
 *
 * @return array
 */
function ezd_prev_next_docs( $current_post_id ) {
	$post_type   = get_post_type( $current_post_id );
	$ordered_ids = ezd_get_docs_tree_flat_cached( $post_type );

	// Find current index and prev/next IDs
	$current_index = array_search( $current_post_id, $ordered_ids );
	$prev_id       = $ordered_ids[ $current_index - 1 ] ?? null;
	$next_id       = $ordered_ids[ $current_index + 1 ] ?? null;

	return [
		'prev'    => $prev_id,
		'current' => $current_post_id,
		'next'    => $next_id,
	];
}

/**
 * Flush the cached docs tree when a doc is saved or deleted.
 *
 * @param int $post_id Post ID.
 */
function ezd_flush_docs_tree_cache( $post_id ) {
	$post_type = get_post_type( $post_id );
	if ( 'docs' === $post_type || 'onepage-docs' === $post_type ) {
		ezd_delete_docs_tree_cache_all_langs( $post_type );
	}
}
add_action( 'save_post', 'ezd_flush_docs_tree_cache' );
add_action( 'delete_post', 'ezd_flush_docs_tree_cache' );

// Helper function to flatten the doc tree in correct order
function ezd_docs_build_tree_flat( $post_id, &$list ) {
	$list[] = $post_id;

	$args = [
		'post_type'   => get_post_type( $post_id ),
		'post_status' => 'publish',
		'post_parent' => $post_id,
		'orderby'     => 'menu_order title',
		'order'       => 'ASC',
		'fields'      => 'ids',
		'numberposts' => -1,
	];

	if ( ezd_is_multilingual() ) {
		$args['suppress_filters'] = false;
	}

	foreach ( get_posts( $args ) as $child_id ) {
		ezd_docs_build_tree_flat( $child_id, $list );
	}
}

/**
 * Get the IDs of docs built with Elementor, cached.
 *
 * This list is only consumed by the AJAX doc loader on single doc pages, yet the
 * underlying unbounded meta query previously ran on every front-end page load via
 * the asset localizer. Caching it for 12 hours (flushed on doc save/delete) keeps
 * large doc libraries from re-running the query on unrelated requests.
 *
 * @return int[] Doc post IDs edited with Elementor.
 */
function ezd_get_elementor_doc_ids() {
	if ( ! class_exists( '\Elementor\Plugin' ) ) {
		return [];
	}

	$cache_key = 'ezd_elementor_doc_ids';
	$doc_ids   = get_transient( $cache_key );

	if ( false === $doc_ids ) {
		$doc_ids = get_posts(
			[
				'post_type'   => 'docs',
				'post_status' => 'publish',
				'numberposts' => -1,
				'fields'      => 'ids',
				'meta_key'    => '_elementor_edit_mode',
				'meta_value'  => 'builder',
			]
		);

		$doc_ids = array_map( 'absint', (array) $doc_ids );
		set_transient( $cache_key, $doc_ids, 12 * HOUR_IN_SECONDS );
	}

	return $doc_ids;
}

/**
 * Flush the cached Elementor doc IDs when a doc is saved or deleted.
 *
 * @param int $post_id Post ID.
 */
function ezd_flush_elementor_doc_ids_cache( $post_id ) {
	if ( 'docs' === get_post_type( $post_id ) ) {
		delete_transient( 'ezd_elementor_doc_ids' );
	}
}
add_action( 'save_post', 'ezd_flush_elementor_doc_ids_cache' );
add_action( 'delete_post', 'ezd_flush_elementor_doc_ids_cache' );

/**
 * Raise memory and execution limits for the one-page doc render / "print to PDF".
 *
 * The one-page layout renders an entire doc tree in a single synchronous request,
 * and with Elementor active it renders every node's builder output. On large trees
 * this can exhaust PHP's default memory/time limits and return a 500 mid-render.
 * Both limits are filterable, and wp_raise_memory_limit() never lowers a value that
 * is already higher.
 *
 * @return void
 */
function ezd_raise_onepage_render_limits() {
	// Respects WP_MAX_MEMORY_LIMIT and the {$context}_memory_limit filter.
	wp_raise_memory_limit( 'ezd_onepage' );

	// Give a large export room to finish instead of timing out part-way through.
	$time_limit = (int) apply_filters( 'ezd_onepage_time_limit', 300 );
	if ( $time_limit > 0 && function_exists( 'set_time_limit' ) ) {
		@set_time_limit( $time_limit );
	}
}

/**
 * Render a single doc's body for the one-page view, cached in the object cache.
 *
 * Centralises the rendering logic that was previously duplicated at every depth of
 * the one-page templates. Elementor's get_builder_content() is expensive, so the
 * rendered markup is stored in the object cache; on hosts with a persistent backend
 * (Redis/Memcached) it is reused across requests. The entry is flushed on doc save.
 *
 * The cache is only used for logged-out visitors. the_content can render
 * user-specific markup (login-gated shortcodes, draft previews, per-user nonces),
 * so caching a single rendering by post ID alone could leak it across users on a
 * persistent backend; logged-in requests always render fresh.
 *
 * The caller is responsible for escaping the return value (e.g. with wp_kses_post()),
 * preserving the previous template behaviour.
 *
 * @param int|WP_Post $doc Post ID or object.
 * @return string Rendered doc body HTML.
 */
function ezd_get_onepage_doc_content( $doc ) {
	$post = get_post( $doc );
	if ( ! $post ) {
		return '';
	}

	$use_cache = ! is_user_logged_in();

	if ( $use_cache ) {
		$cached = wp_cache_get( $post->ID, 'ezd_onepage_content' );
		if ( false !== $cached ) {
			return $cached;
		}
	}

	if ( did_action( 'elementor/loaded' ) ) {
		$builder = \Elementor\Plugin::instance()->frontend->get_builder_content( $post->ID );
		$content = ! empty( $builder ) ? $builder : apply_filters( 'the_content', $post->post_content );
	} else {
		$content = apply_filters( 'the_content', $post->post_content );
	}

	if ( $use_cache ) {
		wp_cache_set( $post->ID, $content, 'ezd_onepage_content' );
	}

	return $content;
}

/**
 * Flush the cached one-page render for a doc when it changes.
 *
 * @param int $post_id Post ID.
 */
function ezd_flush_onepage_doc_content_cache( $post_id ) {
	if ( in_array( get_post_type( $post_id ), [ 'docs', 'onepage-docs' ], true ) ) {
		wp_cache_delete( $post_id, 'ezd_onepage_content' );
	}
}
add_action( 'save_post', 'ezd_flush_onepage_doc_content_cache' );
add_action( 'delete_post', 'ezd_flush_onepage_doc_content_cache' );

/**
 * Aggregate stat metrics for the One-Page banner.
 *
 * Summarises a parent doc and all of its descendants: total doc count, most
 * recent update time, distinct author count, and estimated reading time. All
 * values come from a single batched query (after the descendant-ID lookup) and
 * the result is cached in a transient that is flushed whenever a doc is saved.
 *
 * @param int $parent_id Parent doc ID.
 * @return array{count:int,modified:int,authors:int,author_ids:int[],reading_time:int} Metric set.
 */
function ezd_get_onepage_banner_meta( $parent_id ) {
	$parent_id = absint( $parent_id );
	$empty     = [ 'count' => 0, 'modified' => 0, 'authors' => 0, 'author_ids' => [], 'reading_time' => 0 ];

	if ( ! $parent_id ) {
		return $empty;
	}

	// The version suffix lets a structure change (e.g. new keys) invalidate any
	// data cached by an older build instead of returning an incomplete shape.
	$cache_key = 'ezd_onepage_banner_meta_v2_' . $parent_id;
	$cached    = get_transient( $cache_key );
	if ( is_array( $cached ) && isset( $cached['author_ids'] ) ) {
		return $cached;
	}

	// Parent + every descendant make up the metric set.
	$ids   = ezd_get_all_descendant_ids( $parent_id );
	$ids[] = $parent_id;
	$ids   = array_values( array_unique( array_map( 'absint', $ids ) ) );

	if ( empty( $ids ) ) {
		return $empty;
	}

	global $wpdb;

	// One batched query for everything the metrics need — no per-doc lookups.
	$placeholders = implode( ', ', array_fill( 0, count( $ids ), '%d' ) );
	$rows         = $wpdb->get_results( $wpdb->prepare(
		"SELECT post_author, post_modified_gmt, post_content
		 FROM {$wpdb->posts}
		 WHERE ID IN ($placeholders)",
		...$ids
	) );

	$authors     = [];
	$latest      = 0;
	$total_words = 0;
	foreach ( $rows as $row ) {
		$authors[ (int) $row->post_author ] = true;
		$modified                           = (int) mysql2date( 'U', $row->post_modified_gmt );
		if ( $modified > $latest ) {
			$latest = $modified;
		}
		$total_words += str_word_count( wp_strip_all_tags( (string) $row->post_content ) );
	}

	$wpm        = max( 1, absint( ezd_get_opt( 'reading_time_wpm', 200 ) ) );
	$author_ids = array_map( 'intval', array_keys( $authors ) );
	$meta       = [
		// Exclude the parent itself from the "docs" count.
		'count'        => max( 0, count( $ids ) - 1 ),
		'modified'     => $latest,
		'authors'      => count( $author_ids ),
		'author_ids'   => $author_ids,
		'reading_time' => max( 1, (int) ceil( $total_words / $wpm ) ),
	];

	set_transient( $cache_key, $meta, 12 * HOUR_IN_SECONDS );

	return $meta;
}

/**
 * Flush every cached One-Page banner metric set when a doc changes.
 *
 * Metrics are keyed by ancestor, so a child edit must invalidate its parent's
 * cache too; the simplest correct approach is to clear them all on any doc save.
 *
 * @param int $post_id Post ID.
 */
function ezd_flush_onepage_banner_meta_cache( $post_id ) {
	if ( ! in_array( get_post_type( $post_id ), [ 'docs', 'onepage-docs' ], true ) ) {
		return;
	}

	global $wpdb;
	$wpdb->query(
		"DELETE FROM {$wpdb->options}
		 WHERE option_name LIKE '\_transient\_ezd\_onepage\_banner\_meta\_%'
		    OR option_name LIKE '\_transient\_timeout\_ezd\_onepage\_banner\_meta\_%'"
	);
}
add_action( 'save_post', 'ezd_flush_onepage_banner_meta_cache' );
add_action( 'delete_post', 'ezd_flush_onepage_banner_meta_cache' );

/**
 * Get docs ranked by feedback votes via a single aggregated query.
 *
 * Replaces the previous approach of loading every doc into memory and summing
 * 'positive'/'negative' meta per post. The votes are aggregated in one GROUP BY
 * query and only docs that actually have votes are returned, ordered by the
 * requested vote type. This mirrors the direct-SQL aggregation already used by
 * the dashboard health widget.
 *
 * @param string $order_by 'positive' or 'negative'. Sort key (DESC) and HAVING filter.
 * @param int    $limit    Max rows to return. 0 for no limit.
 * @return array[] Each row: [ 'post_id' => int, 'positive' => int, 'negative' => int ].
 */
function ezd_get_ranked_docs_by_votes( $order_by = 'positive', $limit = 0 ) {
	global $wpdb;

	// Whitelist the sortable column so it is safe to interpolate directly.
	$order_by = ( 'negative' === $order_by ) ? 'negative' : 'positive';

	$sql = "SELECT p.ID AS post_id,
			COALESCE( SUM( CASE WHEN pm.meta_key = 'positive' THEN pm.meta_value ELSE 0 END ), 0 ) AS positive,
			COALESCE( SUM( CASE WHEN pm.meta_key = 'negative' THEN pm.meta_value ELSE 0 END ), 0 ) AS negative
		FROM {$wpdb->posts} p
		INNER JOIN {$wpdb->postmeta} pm ON p.ID = pm.post_id
		WHERE p.post_type = 'docs'
		  AND p.post_status = 'publish'
		  AND pm.meta_key IN ( 'positive', 'negative' )
		GROUP BY p.ID
		HAVING {$order_by} > 0
		ORDER BY {$order_by} DESC, post_id DESC";

	if ( $limit > 0 ) {
		// phpcs:ignore WordPress.DB.PreparedSQL.NotPrepared -- $order_by is whitelisted above; $limit is bound below.
		$rows = $wpdb->get_results( $wpdb->prepare( "{$sql} LIMIT %d", $limit ), ARRAY_A );
	} else {
		// phpcs:ignore WordPress.DB.PreparedSQL.NotPrepared -- $order_by is whitelisted above; query has no dynamic input.
		$rows = $wpdb->get_results( $sql, ARRAY_A );
	}

	return is_array( $rows ) ? $rows : [];
}

/**
 * Map ranked vote rows to the display array used by the dashboard/analytics panels.
 *
 * Loads the matching posts in a single query (which also primes the meta cache)
 * instead of one lookup per row, and preserves the SQL ordering of $rows.
 *
 * @param array[] $rows Rows from ezd_get_ranked_docs_by_votes().
 * @return array[]
 */
function ezd_map_ranked_docs_for_display( $rows ) {
	if ( empty( $rows ) ) {
		return [];
	}

	$post_ids = array_map(
		static function ( $row ) {
			return (int) $row['post_id'];
		},
		$rows
	);

	// Single query for the (already limited) set of ranked docs.
	$posts = get_posts(
		[
			'post_type'   => 'docs',
			'post__in'    => $post_ids,
			'numberposts' => count( $post_ids ),
			'post_status' => 'publish',
		]
	);

	$posts_by_id = [];
	foreach ( $posts as $post ) {
		$posts_by_id[ $post->ID ] = $post;
	}

	$data = [];
	foreach ( $rows as $row ) {
		$post_id = (int) $row['post_id'];
		if ( empty( $posts_by_id[ $post_id ] ) ) {
			continue;
		}

		$post     = $posts_by_id[ $post_id ];
		$data[]   = [
			'post_id'        => $post_id,
			'post_title'     => $post->post_title,
			'post_permalink' => get_permalink( $post_id ),
			'post_edit_link' => get_edit_post_link( $post_id ),
			'positive_time'  => (int) $row['positive'],
			'negative_time'  => (int) $row['negative'],
			'created_at'     => get_the_time( 'U', $post_id ),
		];
	}

	return $data;
}

/**
 * AJAX handler to migrate BetterDocs content into EazyDocs.
 *
 * Converts every doc_category into a parent doc and nests existing docs beneath
 * the parent that matches their deepest category. The run is idempotent: parent
 * docs created by an earlier migration (flagged _ezd_migrated_parent) are removed
 * and rebuilt, while user-authored docs are never deleted. Returns a count
 * summary so the UI can report exactly what changed.
 */
add_action( 'wp_ajax_ezd_migrate_to_eazydocs', 'ezd_migrate_betterdocs_to_eazydocs' );
function ezd_migrate_betterdocs_to_eazydocs() {

	check_ajax_referer( 'eazydocs-admin-nonce', 'security' );

	// Mirror the capability the Migrate tab itself is gated behind.
	$settings_cap = function_exists( 'ezd_get_opt' ) ? ezd_get_opt( 'settings-edit-access', 'manage_options' ) : 'manage_options';
	if ( ! current_user_can( $settings_cap ) && ! current_user_can( 'manage_options' ) ) {
		wp_send_json_error( [ 'message' => __( 'You do not have permission to run a migration.', 'eazydocs' ) ] );
	}

	if ( ! function_exists( 'is_plugin_active' ) ) {
		require_once ABSPATH . 'wp-admin/includes/plugin.php';
	}
	if ( ! is_plugin_active( 'betterdocs/betterdocs.php' ) ) {
		wp_send_json_error( [ 'message' => __( 'BetterDocs is not active. Please activate it first.', 'eazydocs' ) ] );
	}

	$from = isset( $_POST['migrate_from'] ) ? sanitize_text_field( wp_unslash( $_POST['migrate_from'] ) ) : '';
	if ( 'betterdocs' !== $from ) {
		wp_send_json_error( [ 'message' => __( 'Only BetterDocs migration is supported currently.', 'eazydocs' ) ] );
	}

	// A whole-library migration can be slow on shared hosting.
	wp_raise_memory_limit( 'admin' );
	if ( function_exists( 'set_time_limit' ) ) {
		@set_time_limit( 0 ); // phpcs:ignore WordPress.PHP.NoSilencedErrors.Discouraged -- disabled on some hosts.
	}

	/**
	 * Cleanup: remove only the category parent docs a previous migration created
	 * (flagged _ezd_migrated_parent) so re-running stays clean. -1 is intentional —
	 * a one-time admin migration must see the whole library.
	 */
	$old_parent_docs = get_posts( [
		'post_type'              => 'docs',
		'post_status'            => 'any',
		'posts_per_page'         => -1,
		'fields'                 => 'ids',
		'no_found_rows'          => true,
		'update_post_meta_cache' => false,
		'update_post_term_cache' => false,
		'meta_key'               => '_ezd_migrated_parent',
		'meta_value'             => 'yes',
	] );
	foreach ( $old_parent_docs as $pid ) {
		wp_delete_post( $pid, true );
	}

	// PASS 1 — one parent doc per category (recursive); returns the count created.
	$created_docs    = []; // term_id => parent_doc_id.
	$parents_created = ezd_create_parent_docs_from_terms( $created_docs, 0 );

	// PASS 2 — nest existing docs under the parent for their deepest category.
	$docs_reparented = 0;
	if ( ! empty( $created_docs ) ) {
		$created_parent_doc_ids = array_values( $created_docs );

		$posts = get_posts( [
			'post_type'              => 'docs',
			'post_status'            => 'any',
			'posts_per_page'         => -1,
			'no_found_rows'          => true,
			'update_post_meta_cache' => false,
			'post__not_in'           => $created_parent_doc_ids,
			'tax_query'              => [
				[
					'taxonomy' => 'doc_category',
					'operator' => 'EXISTS',
				],
			],
		] );

		foreach ( $posts as $post ) {
			// Leave docs that already sit under a parent untouched.
			if ( 0 !== (int) $post->post_parent ) {
				continue;
			}

			$terms = wp_get_post_terms( $post->ID, 'doc_category' );
			if ( empty( $terms ) || is_wp_error( $terms ) ) {
				continue;
			}

			// Deepest (most specific) category wins.
			$deepest_term = null;
			$max_depth    = -1;
			foreach ( $terms as $term ) {
				$depth = count( get_ancestors( $term->term_id, 'doc_category' ) );
				if ( $depth > $max_depth ) {
					$max_depth    = $depth;
					$deepest_term = $term;
				}
			}

			if ( ! $deepest_term || ! isset( $created_docs[ $deepest_term->term_id ] ) ) {
				continue;
			}

			$parent_doc_id = $created_docs[ $deepest_term->term_id ];

			wp_update_post( [
				'ID'          => $post->ID,
				'post_parent' => $parent_doc_id,
				'menu_order'  => $post->menu_order,
			] );

			// Flags for future cleanups / debugging.
			update_post_meta( $post->ID, '_ezd_migrated', 'yes' );
			update_post_meta( $post->ID, '_ezd_parent_doc', $parent_doc_id );
			update_post_meta( $post->ID, '_ezd_parent_term', $deepest_term->term_id );

			$docs_reparented++;
		}
	}

	// Build a translated, pluralised summary for the success dialog.
	$summary_parts   = [];
	/* translators: %d: number of categories converted into parent docs. */
	$summary_parts[] = sprintf( _n( '%d category converted into a parent doc.', '%d categories converted into parent docs.', $parents_created, 'eazydocs' ), $parents_created );
	/* translators: %d: number of docs nested under their category. */
	$summary_parts[] = sprintf( _n( '%d doc organised under its category.', '%d docs organised under their categories.', $docs_reparented, 'eazydocs' ), $docs_reparented );

	wp_send_json_success( [
		'message'    => __( 'Migration completed.', 'eazydocs' ),
		'categories' => $parents_created,
		'docs'       => $docs_reparented,
		'summary'    => implode( '<br>', array_map( 'esc_html', $summary_parts ) ),
	] );
}

/**
 * Recursively create a parent doc for each doc_category term.
 *
 * @param array $created_docs   Map of term_id => created parent doc ID (by reference).
 * @param int   $parent_term_id Term to start from (0 = top level).
 * @return int  Number of parent docs created within this subtree.
 */
if ( ! function_exists( 'ezd_create_parent_docs_from_terms' ) ) {
	function ezd_create_parent_docs_from_terms( &$created_docs, $parent_term_id = 0 ) {
		$created = 0;

		$categories = get_categories( [
			'taxonomy'   => 'doc_category',
			'hide_empty' => false,
			'parent'     => $parent_term_id,
		] );

		foreach ( $categories as $cat ) {
			$parent_doc_parent_id = ( $cat->parent && isset( $created_docs[ $cat->parent ] ) ) ? $created_docs[ $cat->parent ] : 0;

			$parent_doc_id = wp_insert_post( [
				'post_type'   => 'docs',
				'post_title'  => $cat->name,
				'post_name'   => $cat->slug,
				'post_status' => 'publish',
				'post_parent' => $parent_doc_parent_id,
				'meta_input'  => [
					'_ezd_migrated_parent' => 'yes',
					'_ezd_parent_term'     => $cat->term_id,
				],
			] );

			if ( is_wp_error( $parent_doc_id ) ) {
				continue;
			}

			// Keep the original taxonomy term attached to its new parent doc.
			wp_set_post_terms( $parent_doc_id, [ $cat->term_id ], 'doc_category', false );

			$created_docs[ $cat->term_id ] = $parent_doc_id;
			$created++;

			$created += ezd_create_parent_docs_from_terms( $created_docs, $cat->term_id );
		}

		return $created;
	}
}


/**
 * AJAX handler to install and activate Advanced Accordion Block plugin
 */
add_action('wp_ajax_ezd_install_advanced_accordion', 'ezd_install_advanced_accordion');
function ezd_install_advanced_accordion() {
    check_ajax_referer( 'ezd_install_accordion_nonce', 'nonce' );

    if ( ! current_user_can( 'install_plugins' ) ) {
        return;
    }

    $plugin_basename = 'advanced-accordion-block' . '/' . 'advanced-accordion-block.php';
    include_once ABSPATH . 'wp-admin/includes/plugin-install.php';
    include_once ABSPATH . 'wp-admin/includes/class-wp-upgrader.php';
    include_once ABSPATH . 'wp-admin/includes/plugin.php';
    include_once ABSPATH . 'wp-admin/includes/file.php';
    if (!function_exists('WP_Filesystem')) { require_once ABSPATH . 'wp-admin/includes/file.php'; }
    WP_Filesystem();

    if ( ! file_exists( WP_PLUGIN_DIR . '/' . $plugin_basename ) ) {
        $api = plugins_api( 'plugin_information', [ 'slug' => 'advanced-accordion-block', 'fields' => [ 'sections' => false ] ] );
        if ( ! is_wp_error( $api ) ) {
            $upgrader = new Plugin_Upgrader( new Automatic_Upgrader_Skin() );
            $upgrader->install( $api->download_link );
        }
    }

    if ( ! is_plugin_active( $plugin_basename ) ) {
        activate_plugin( $plugin_basename );
    }
}

/**
 * AJAX handler to import sample data from demo.xml
 *
 * This function imports the sample documentation data from the
 * sample-data/demo.xml file using WordPress Importer.
 *
 * @since 2.8.3
 */
add_action( 'wp_ajax_ezd_import_sample_data', 'ezd_import_sample_data' );

/**
 * Import sample data from demo.xml.
 *
 * @return void
 */
function ezd_import_sample_data() {
	// Verify nonce for security.
	check_ajax_referer( 'eazydocs-admin-nonce', 'security' );

	// Check user capabilities.
	if ( ! current_user_can( 'manage_options' ) ) {
		wp_send_json_error( [ 'message' => esc_html__( 'You do not have permission to import data.', 'eazydocs' ) ] );
	}

	// Path to the sample data XML file.
	$sample_data_file = EZD_PATH . '/sample-data/demo.xml';

	// Check if the file exists.
	if ( ! file_exists( $sample_data_file ) ) {
		wp_send_json_error( [ 'message' => esc_html__( 'Sample data file not found.', 'eazydocs' ) ] );
	}

	// Include WordPress importer files.
	if ( ! defined( 'WP_LOAD_IMPORTERS' ) ) {
		define( 'WP_LOAD_IMPORTERS', true );
	}

	// Load WordPress Importer class.
	require_once ABSPATH . 'wp-admin/includes/import.php';

	// Check if the WordPress Importer class exists.
	if ( ! class_exists( 'WP_Import' ) ) {
		// Try to load the importer plugin.
		$importer_plugin = ABSPATH . 'wp-content/plugins/wordpress-importer/wordpress-importer.php';

		if ( file_exists( $importer_plugin ) ) {
			require_once $importer_plugin;
		} else {
			// Importer not available, use manual import.
			$result = ezd_manual_import_sample_data( $sample_data_file );
			if ( is_wp_error( $result ) ) {
				wp_send_json_error( [ 'message' => $result->get_error_message() ] );
			}
			wp_send_json_success( [ 'message' => esc_html__( 'Sample data imported successfully!', 'eazydocs' ) ] );
		}
	}

	// If WP_Import class exists, use it.
	if ( class_exists( 'WP_Import' ) ) {
		$wp_import                    = new WP_Import();
		$wp_import->fetch_attachments = false;

		ob_start();
		$wp_import->import( $sample_data_file );
		ob_end_clean();

		wp_send_json_success( [ 'message' => esc_html__( 'Sample data imported successfully!', 'eazydocs' ) ] );
	} else {
		// Fallback to manual import.
		$result = ezd_manual_import_sample_data( $sample_data_file );
		if ( is_wp_error( $result ) ) {
			wp_send_json_error( [ 'message' => $result->get_error_message() ] );
		}
		wp_send_json_success( [ 'message' => esc_html__( 'Sample data imported successfully!', 'eazydocs' ) ] );
	}
}

/**
 * Manual import of sample data by parsing XML.
 *
 * @param string $file Path to the XML file.
 * @return bool|WP_Error True on success, WP_Error on failure.
 */
function ezd_manual_import_sample_data( $file ) {
	// Load the XML file.
	$xml_content = file_get_contents( $file );

	if ( empty( $xml_content ) ) {
		return new WP_Error( 'empty_file', esc_html__( 'The sample data file is empty.', 'eazydocs' ) );
	}

	// Parse XML.
	libxml_use_internal_errors( true );
	$xml = simplexml_load_string( $xml_content );

	if ( ! $xml ) {
		return new WP_Error( 'xml_parse_error', esc_html__( 'Failed to parse the sample data file.', 'eazydocs' ) );
	}

	// Get namespaces.
	$namespaces = $xml->getNamespaces( true );
	$wp         = $xml->channel->children( isset( $namespaces['wp'] ) ? $namespaces['wp'] : '' );
	$content_ns = isset( $namespaces['content'] ) ? $namespaces['content'] : '';

	// Track old ID to new ID mapping for parent relationships.
	$id_mapping = [];

	// First pass: Create all docs without parent relationships.
	foreach ( $xml->channel->item as $item ) {
		$wp_data = $item->children( isset( $namespaces['wp'] ) ? $namespaces['wp'] : '' );

		// Only import 'docs' post type.
		if ( 'docs' !== (string) $wp_data->post_type ) {
			continue;
		}

		$old_id = (int) $wp_data->post_id;
		$title  = (string) $item->title;

		// Check if a doc with the same title already exists.
		$existing = get_posts(
			[
				'post_type'   => 'docs',
				'title'       => $title,
				'post_status' => 'any',
				'numberposts' => 1,
			]
		);

		if ( ! empty( $existing ) ) {
			$id_mapping[ $old_id ] = $existing[0]->ID;
			continue;
		}

		// Get content.
		$content_data = $item->children( $content_ns );
		$post_content = isset( $content_data->encoded ) ? (string) $content_data->encoded : '';

		// Create the doc post.
		$post_data = [
			'import_id'    => $old_id, // Preserve original post ID to avoid occupying IDs other plugins rely on.
			'post_title'   => sanitize_text_field( $title ),
			'post_content' => wp_kses_post( $post_content ),
			'post_status'  => ( 'private' === (string) $wp_data->status ) ? 'publish' : sanitize_text_field( (string) $wp_data->status ),
			'post_type'    => 'docs',
			'menu_order'   => (int) $wp_data->menu_order,
			'post_parent'  => 0, // Will be updated in second pass.
		];

		$new_id = wp_insert_post( $post_data );

		if ( ! is_wp_error( $new_id ) ) {
			$id_mapping[ $old_id ] = $new_id;

			// Store the original parent ID for later.
			$original_parent = (int) $wp_data->post_parent;
			if ( $original_parent > 0 ) {
				update_post_meta( $new_id, '_ezd_temp_parent', $original_parent );
			}
		}
	}

	// Second pass: Update parent relationships.
	foreach ( $id_mapping as $old_id => $new_id ) {
		$temp_parent = get_post_meta( $new_id, '_ezd_temp_parent', true );

		if ( ! empty( $temp_parent ) && isset( $id_mapping[ $temp_parent ] ) ) {
			wp_update_post(
				[
					'ID'          => $new_id,
					'post_parent' => $id_mapping[ $temp_parent ],
				]
			);
		}

		// Clean up temp meta.
		delete_post_meta( $new_id, '_ezd_temp_parent' );
	}

	return true;
}

/**
 * EazyDocs_Article_Walker Class.
 *
 * @since 1.0.0
 */
class EazyDocs_Article_Walker extends Walker_Page {

	/**
	 * Outputs the beginning of the current element in the tree.
	 *
	 * @since 1.0.0
	 *
	 * @see Walker::start_el()
	 *
	 * @param string  $output            Used to append additional content. Passed by reference.
	 * @param WP_Post $data_object       Page data object.
	 * @param int     $depth             Optional. Depth of page. Used for padding. Default 0.
	 * @param array   $args              Optional. Array of arguments. Default empty array.
	 * @param int     $current_object_id Optional. ID of the current page. Default 0.
	 */
	public function start_el( &$output, $data_object, $depth = 0, $args = [], $current_object_id = 0 ) {
		$page      = $data_object;
		$css_class = [ 'page_item', 'page-item-' . $page->ID ];

		if ( isset( $args['pages_with_children'][ $page->ID ] ) ) {
			$css_class[] = 'page_item_has_children';
		}

		if ( ! empty( $current_object_id ) ) {
			$_current_page = get_post( $current_object_id );
			if ( $_current_page && in_array( $page->ID, $_current_page->ancestors, true ) ) {
				$css_class[] = 'current_page_ancestor';
			}
			if ( (int) $page->ID === (int) $current_object_id ) {
				$css_class[] = 'current_page_item';
			} elseif ( $_current_page && (int) $page->ID === (int) $_current_page->post_parent ) {
				$css_class[] = 'current_page_parent';
			}
		} elseif ( (int) get_option( 'page_for_posts' ) === (int) $page->ID ) {
			$css_class[] = 'current_page_parent';
		}

		$css_classes = implode( ' ', apply_filters( 'page_css_class', $css_class, $page, $depth, $args, $current_object_id ) );
		$css_classes = $css_classes ? ' class="' . esc_attr( $css_classes ) . '"' : '';

		if ( '' === $page->post_title ) {
			/* translators: %d: ID of a post */
			$page->post_title = sprintf( __( '#%d (no title)', 'eazydocs' ), $page->ID );
		}

		$args['link_before'] = empty( $args['link_before'] ) ? '' : $args['link_before'];
		$args['link_after']  = empty( $args['link_after'] ) ? '' : $args['link_after'];

		$badge_html = '';
		if ( function_exists( 'ezdpro_badge' ) && ezd_is_premium() ) {
			$badge_html = ezdpro_badge( $page->ID );
		}

		$atts                 = [];
		$atts['href']         = get_permalink( $page->ID );
		$atts['aria-current'] = ( (int) $page->ID === (int) $current_object_id ) ? 'page' : '';

		$atts = apply_filters( 'page_menu_link_attributes', $atts, $page, $depth, $args, $current_object_id );

		$attributes = '';
		foreach ( $atts as $attr => $value ) {
			if ( is_scalar( $value ) && '' !== $value && false !== $value ) {
				$value       = ( 'href' === $attr ) ? esc_url( $value ) : esc_attr( $value );
				$attributes .= ' ' . $attr . '="' . $value . '"';
			}
		}

		$output .= sprintf(
			'<li%s><a%s>%s%s%s%s</a>',
			$css_classes,
			$attributes,
			$args['link_before'],
			apply_filters( 'the_title', $page->post_title, $page->ID ),
			$args['link_after'],
			$badge_html
		);
	}
}
