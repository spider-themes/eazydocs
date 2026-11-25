<?php
/**
 * Cannot access directly.
 */
if ( ! defined( 'ABSPATH' ) ) {
	exit;
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
