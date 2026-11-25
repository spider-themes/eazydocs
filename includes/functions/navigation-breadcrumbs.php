<?php
/**
 * Cannot access directly.
 */
if ( ! defined( 'ABSPATH' ) ) {
	exit;
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

		if ( in_array( $post->post_type, array( 'docs', 'api_docs' ), true ) && $post->post_parent ) {
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
		'posts_per_page'         => - 1,
		'post_type'              => [ 'docs' ],
		'post_parent'            => 0,
		'no_found_rows'          => true,
		'update_post_meta_cache' => false,
		'update_post_term_cache' => false,
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

	// Slugs that already have a OnePage doc, fetched once instead of running
	// get_page_by_path() for every parent doc in the loop.
	$onepage_slugs = [];
	if ( ! empty( $parent_ids ) ) {
		$onepage_slugs = array_flip(
			$wpdb->get_col(
				"SELECT post_name FROM {$wpdb->posts} WHERE post_type = 'onepage-docs'"
			)
		);
	}

	$nonce = wp_create_nonce( 'ezd_make_onepage' );

	foreach ( $docs as $doc ) {
		if ( ! isset( $onepage_slugs[ $doc->post_name ] ) ) {
			$doc_item_count ++;
			$child_count = $child_counts[ $doc->ID ] ?? 0;
			$label       = $doc->post_title . ' (' . $child_count . ')';
			$doc_items  .= '<option _wpnonce="' . esc_attr( $nonce ) . '" value="' . esc_attr( $doc->ID ) . '" data-child-count="' . esc_attr( $child_count ) . '">' . esc_html( $label ) . '</option>';
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
