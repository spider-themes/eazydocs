<?php
/**
 * Shortcode: [ezd_search]
 *
 * Renders the EazyDocs search banner on any page, post, classic widget, or
 * other area that runs shortcodes. Uses the same template, stylesheet, and
 * AJAX live search as the built-in documentation banner.
 *
 * Example:
 * [ezd_search]
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

add_shortcode( 'ezd_search', 'ezd_search_shortcode' );

/**
 * Render the standalone search bar.
 *
 * @param array|string $atts Shortcode attributes. None are used.
 * @return string
 */
function ezd_search_shortcode( $atts = [] ) {
	shortcode_atts( [], $atts, 'ezd_search' );

	ezd_enqueue_search_shortcode_assets();

	$GLOBALS['ezd_search_shortcode'] = true;

	ob_start();
	eazydocs_get_template_part( 'search-banner' );
	$html = ob_get_clean();

	// A theme copy of search-banner.php may still hide the banner when the
	// documentation setting is off. Fall back to the plugin template so the
	// shortcode always prints the search bar.
	if ( ! is_string( $html ) || false === strpos( $html, 'ezd_search_form' ) ) {
		$plugin_template = EZD_PATH . '/templates/search-banner.php';
		if ( is_readable( $plugin_template ) ) {
			ob_start();
			include $plugin_template;
			$html = ob_get_clean();
		}
	}

	unset( $GLOBALS['ezd_search_shortcode'] );

	return is_string( $html ) ? $html : '';
}

/**
 * Whether the current request should load search-shortcode assets up front.
 *
 * Covers the Classic Editor, the Shortcode block, and active classic widgets.
 * Shortcodes printed later from a theme template enqueue assets themselves.
 *
 * @return bool
 */
function ezd_is_search_shortcode_present() {
	static $present = null;

	if ( null !== $present ) {
		return $present;
	}

	$present = false;

	if ( is_singular() ) {
		$post = get_post();
		if ( $post instanceof WP_Post && has_shortcode( $post->post_content, 'ezd_search' ) ) {
			$present = true;
			return $present;
		}
	}

	$sidebars = get_option( 'sidebars_widgets', [] );
	if ( ! is_array( $sidebars ) ) {
		return $present;
	}

	$option_map = [
		'text'        => [ 'option' => 'widget_text', 'key' => 'text' ],
		'custom_html' => [ 'option' => 'widget_custom_html', 'key' => 'content' ],
		'block'       => [ 'option' => 'widget_block', 'key' => 'content' ],
	];
	$widget_settings = [];

	foreach ( $sidebars as $sidebar_id => $widget_ids ) {
		if ( 'wp_inactive_widgets' === $sidebar_id || ! is_array( $widget_ids ) ) {
			continue;
		}

		foreach ( $widget_ids as $widget_id ) {
			if ( ! is_string( $widget_id ) || ! preg_match( '/^(text|custom_html|block)-(\d+)$/', $widget_id, $matches ) ) {
				continue;
			}

			$option_name = $option_map[ $matches[1] ]['option'];
			$content_key = $option_map[ $matches[1] ]['key'];

			if ( ! isset( $widget_settings[ $option_name ] ) ) {
				$stored = get_option( $option_name, [] );
				$widget_settings[ $option_name ] = is_array( $stored ) ? $stored : [];
			}

			$instance = $widget_settings[ $option_name ][ (int) $matches[2] ] ?? null;
			if ( ! is_array( $instance ) ) {
				continue;
			}

			$content = $instance[ $content_key ] ?? '';
			if ( is_string( $content ) && has_shortcode( $content, 'ezd_search' ) ) {
				$present = true;
				return $present;
			}
		}
	}

	return $present;
}

/**
 * Load the styles and script the default search banner already uses.
 *
 * No separate stylesheet or script is added. When the shortcode renders after
 * wp_head, those existing styles are printed with the markup.
 *
 * @return void
 */
function ezd_enqueue_search_shortcode_assets() {
	if ( ! wp_style_is( 'elegant-icon-vend', 'registered' ) ) {
		wp_register_style( 'elegant-icon-vend', EZD_VEND . 'elegant-icon/style.css', [], EZD_VERSION );
	}

	if ( ! wp_style_is( 'ezd-frontend-global', 'registered' ) ) {
		wp_register_style( 'ezd-frontend-global', EZD_STYLES . 'frontend-global.css', [], EZD_VERSION );
	}

	if ( ! wp_script_is( 'eazydocs-search-banner', 'registered' ) ) {
		wp_register_script( 'eazydocs-search-banner', EZD_ASSETS . 'js/frontend/search-banner.js', [ 'jquery' ], EZD_VERSION, true );
	}

	wp_enqueue_style( 'elegant-icon-vend' );
	wp_enqueue_style( 'ezd-frontend-global' );
	wp_enqueue_script( 'jquery' );
	wp_enqueue_script( 'eazydocs-search-banner' );

	if ( did_action( 'wp_print_styles' ) ) {
		wp_print_styles( [ 'elegant-icon-vend', 'ezd-frontend-global' ] );
	}
}

/**
 * Run [ezd_search] in classic widgets that do not process shortcodes.
 *
 * The visual Text widget already runs do_shortcode. Legacy Text widgets and
 * the Custom HTML widget do not.
 *
 * @param string $text     Widget content.
 * @param array  $instance Widget instance.
 * @return string
 */
function ezd_search_render_widget_shortcode( $text, $instance = [] ) {
	if ( ! is_string( $text ) || ! has_shortcode( $text, 'ezd_search' ) ) {
		return $text;
	}

	// Visual Text widgets process shortcodes later, after wpautop.
	if ( is_array( $instance ) && ! empty( $instance['visual'] ) ) {
		return $text;
	}

	return preg_replace_callback(
		'/' . get_shortcode_regex( [ 'ezd_search' ] ) . '/',
		'do_shortcode_tag',
		$text
	);
}

add_filter( 'widget_text', 'ezd_search_render_widget_shortcode', 11, 2 );
add_filter( 'widget_custom_html_content', 'ezd_search_render_widget_shortcode', 11, 2 );
