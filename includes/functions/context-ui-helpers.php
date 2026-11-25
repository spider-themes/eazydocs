<?php
/**
 * Cannot access directly.
 */
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

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
            'docs', 'onepage-docs', 'api_docs'
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
	if (
		is_singular( 'docs' )
		|| is_singular( 'onepage-docs' )
		|| is_singular( 'api_docs' )
		|| is_post_type_archive( 'api_docs' )
		|| is_page_template( 'page-onepage.php' )
	) {
		return true;
	}
}

/**
 * Whether the Dark Mode feature is enabled for the front end.
 *
 * Mirrors the gate used when enqueuing the dark stylesheet: the toggle must be
 * switched on AND the active theme must support it (docy/docly) or premium be
 * active. Use this single helper everywhere so the switcher UI, the stylesheet,
 * and the server-side body class never drift out of sync.
 *
 * @return bool
 */
function ezd_is_dark_mode_enabled() {
	return '1' === ezd_get_opt( 'is_dark_switcher' ) && ezd_unlock_themes( 'docy', 'docly' );
}

/**
 * Resolve the admin-chosen default appearance for first-time visitors.
 *
 * @return string One of 'light', 'dark', 'system'.
 */
function ezd_dark_default_mode() {
	$mode = ezd_get_opt( 'ezd_dark_default', 'system' );
	return in_array( $mode, [ 'light', 'dark', 'system' ], true ) ? $mode : 'system';
}

/**
 * Whether the current request is an EazyDocs front-end surface that should
 * honour the dark theme (single docs, one-page docs, docs archive/taxonomy, or
 * a page rendering an EazyDocs shortcode). Kept deliberately free of
 * get_body_class() so it is safe to call from inside the body_class filter.
 *
 * @return bool
 */
function ezd_is_dark_context() {
	return ezd_frontend_pages()
		|| is_post_type_archive( 'docs' )
		|| is_tax( [ 'doc_tag', 'doc_badge' ] )
		|| eazydocs_has_shortcode();
}

/**
 * Whether the front-end dark-mode switcher button should be rendered.
 *
 * Themes that ship their own dark switcher (e.g. Docy's header toggle) can
 * return false via the `eazydocs_render_dark_switcher` filter to avoid showing
 * two competing toggles that fight over the same `body_dark` class.
 *
 * @return bool
 */
function ezd_should_render_dark_switcher() {
	return (bool) apply_filters( 'eazydocs_render_dark_switcher', ezd_is_dark_mode_enabled() );
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
	// get_pages() applies post_status directly in SQL with no per-user read
	// filtering, so gate 'private' on the read_private_docs capability rather than
	// always requesting it (prevents private doc titles leaking into pickers/lists).
	$docs       = get_pages(
		[
			'post_type'   => $post_type,
			'numberposts' => - 1,
			'post_status' => ezd_doc_listing_statuses(),
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
 * Get the arrow icon class based on text direction.
 *
 * Returns the class name so it can be echoed or concatenated inside template
 * markup, e.g. <i class="<?php echo ezd_arrow(); ?>"></i>.
 *
 * @return string
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
	// Walk up via the post object cache (one uncached query per ancestor at most)
	// rather than an uncached SELECT on every step for every item.
	$page_id = intval( $page_id );
	$seen    = [];

	while ( $page_id && ! isset( $seen[ $page_id ] ) ) {
		$seen[ $page_id ] = true;
		$post             = get_post( $page_id );
		$parent           = ( $post && 'docs' === $post->post_type ) ? (int) $post->post_parent : 0;

		if ( 0 === $parent ) {
			return $page_id;
		}

		$page_id = $parent;
	}

	return $page_id;
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
	// 'status' was a typo for 'post_status' (ignored by WP_Query). Only ID and
	// title are needed, so skip the meta/term cache priming.
	$elementor_templates = get_posts( [
		'post_type'              => 'elementor_library',
		'posts_per_page'         => 500,
		'post_status'            => 'publish',
		'no_found_rows'          => true,
		'update_post_meta_cache' => false,
		'update_post_term_cache' => false,
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
