<?php
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
 * Check if the pro plugin and plan is active
 *
 * @return bool|void
 */
function ezd_is_premium() {
	if ( class_exists('EZD_EazyDocsPro') && eaz_fs()->can_use_premium_code() ) {
		return true;
	}
}

/**
 * Unlock the plugin with themes
 *
 * @return bool|void
 */
function ezd_unlock_themes() {
	$current_theme = get_template();
	if ( $current_theme == 'docy' || $current_theme == 'docly' || ezd_is_premium() ) {
		return true;
	}
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
 * Get the container class
 * @return string
 */
function ezd_container() {
	return ezd_get_opt('docs_page_width') == 'full-width' ? 'ezd-container-fluid' : 'ezd-container ezd-custom-container';
}

/**
 * Get admin template part
 */
function eazydocs_get_admin_template_part( $template ) {
	$file = EAZYDOCS_PATH . "/includes/admin/templates/$template.php";
	load_template( $file, false );
}

/**
 * Get Page by title
 * @param $title
 * @param $post_type
 *
 * @return int[]|WP_Post[]
 */
function ezd_get_page_by_title( $title, $post_type = 'page' ) {
	return get_posts(
		array(
			'post_type'              => $post_type,
			'title'                  => $title,
			'post_status'            => 'all',
			'numberposts'            => 1,
			'update_post_term_cache' => false,
			'update_post_meta_cache' => false,
			'orderby'                => 'post_date ID',
			'order'                  => 'ASC',
		),
	);
}

/**
 * Get template part implementation for eazydocs.
 * Looks at the theme directory first
 *
 * @param $template
 */
function eazydocs_get_template_part( $template ) {
	// Get the slug
	$template_slug = rtrim( $template, '.php' );
	$template      = $template_slug . '.php';

	// Check if a custom template exists in the theme folder, if not, load the plugin template file
	if ( $theme_file = locate_template( array( 'eazydocs/' . $template ) ) ) {
		$file = $theme_file;
	} else {
		//here path to '/single-paper.php'
		$file = EAZYDOCS_PATH . "/templates/" . $template;
	}
	//create a new filter so the devs can filter this

	if ( $file ) {
		load_template( $file, false );
	}
}


/**
 * Get template part implementation for eazydocs.
 * Looks at the theme directory first
 *
 * @param       $template
 * @param array $args
 */
function eazydocs_get_template( $template_name, $args = [] ) {
	$ezd_obj = EazyDocs::init();

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

	if ( file_exists( $template ) ) {
		include $template;
	}
}

/**
 * Estimated reading time
 **/
function ezd_reading_time() {
	$content     = get_post_field( 'post_content', get_the_ID() );
	$word_count  = str_word_count( strip_tags( $content ) );
	$readingtime = ceil( $word_count / 200 );
	if ( $readingtime == 1 ) {
		$timer = esc_html__( " minute", 'eazydocs' );
	} else {
		$timer = esc_html__( " minutes", 'eazydocs' );
	}
	$totalreadingtime = $readingtime . $timer;
	echo esc_html( $totalreadingtime );
}

/**
 * @param string $args
 *
 * @return mixed|void
 */
function ezd_list_pages( $args = '' ) {
	$defaults = array(
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
		'post_status'  => ['publish', 'private']
	);

	$r = wp_parse_args( $args, $defaults );

	if ( ! in_array( $r['item_spacing'], array( 'preserve', 'discard' ), true ) ) {
		// invalid value, fall back to default.
		$r['item_spacing'] = $defaults['item_spacing'];
	}

	$output       		= '';
	$current_page		= 0;

	// sanitize, mostly to keep spaces out
	$r['exclude'] = preg_replace( '/[^0-9,]/', '', $r['exclude'] );

	// Allow plugins to filter an array of excluded pages (but don't put a nullstring into the array)
	$exclude_array = ( $r['exclude'] ) ? explode( ',', $r['exclude'] ) : array();

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
		echo apply_filters( 'ezd_list_pages', $output, $r, $pages );;
	} else {
		return apply_filters( 'ezd_list_pages', $output, $r, $pages );;
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
		return '<li class="breadcrumb-item" itemprop="itemListElement" itemscope itemtype="http://schema.org/ListItem">
            <a itemprop="item" href="' . esc_attr( $permalink ) . '">
            <span itemprop="name">' . esc_html( $label ) . '</span></a>
            <meta itemprop="position" content="' . $position . '" />
        </li>';
	}

	function eazydocs_get_breadcrumb_root_title( $label ) {
		return '<li class="breadcrumb-item" itemprop="itemListElement" itemscope itemtype="http://schema.org/ListItem">
             ' . esc_html( $label ) . '</li>';
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
		$home_text  = ezd_get_opt( 'breadcrumb-home-text' );
		$front_page = ! empty( $home_text ) ? esc_html( $home_text ) : esc_html__( 'Home', 'eazydocs' );

		$html = '';
		$args = apply_filters( 'eazydocs_breadcrumbs', [
			'delimiter' => '',
			'home'      => $front_page,
			'before'    => '<li class="breadcrumb-item active">',
			'after'     => '</li>',
		] );

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

		if ( 'docs' == $post->post_type && $post->post_parent ) {
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

		$html .= ' ' . $args['before'] . get_the_title() . $args['after'];

		$html .= '</ol>';

		echo apply_filters( 'eazydocs_breadcrumbs_html', $html, $args );
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

		$docs_page_title = ezd_get_opt( 'docs-page-title', 'eazydocs_settings' );
		$docs_page_title = ! empty( $docs_page_title ) ? esc_html( $docs_page_title ) : esc_html__( 'Docs', 'eazydocs' );
		$docs_home       = ezd_get_opt( 'docs-slug' );

		if ( $docs_home ) {
			++ $breadcrumb_position;
			$html .= $args['delimiter'];
		}

		if ( 'docs' == $post->post_type && $post->post_parent ) {
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

		$html .= ' ' . $args['before'] . get_the_title() . $args['after'];
		$html .= '</ol>';
		echo apply_filters( 'eazydocs_breadcrumbs_html', $html, $args );
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
		$is_parent  = $is_parents[0];
		if ( $is_parent == 0 ) {
			$parent_id = $post->ID;
		} else {
			$parent_id = $is_parent;
		}

		$html .= '<ol class="breadcrumb eazydocs-breadcrumb-root-title ' . $parent_id . '" itemscope itemtype="http://schema.org/BreadcrumbList">';
		$html .= $args['delimiter'];


		$docs_page_title = ezd_get_opt( 'docs-page-title' );
		$docs_page_title = ! empty( $docs_page_title ) ? esc_html( $docs_page_title ) : esc_html__( 'Docs', 'eazydocs' );

		if ( 'docs' == $post->post_type && $post->post_parent ) {
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

		echo apply_filters( 'eazydocs_breadcrumbs_html', $html, $args );
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
 * @since 1.0.1 eazyDocs
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
	$found  = array();

	// Fallback to global post_content
	if ( empty( $text ) && is_singular() ) {
		$text = eazydocs_get_global_post_field( 'post_content', 'raw' );
	}

	// Skip if empty, or string doesn't contain the eazydocs shortcode prefix
	if ( ! empty( $text ) && ( false !== strpos( $text, '[eazydocs' ) ) ) {

		// Get possible shortcodes
		$codes = array( 'eazydocs', 'eazydocs_tab' );

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
function date_sort( $a, $b ) {
	return strtotime( $b ) - strtotime( $a );
}

/**
 * @param $a
 * @param $b
 *
 * @return bool
 */
function main_date_sort( $a, $b ) {
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
	$args      = array(
		'posts_per_page' => - 1,
		'post_type'      => array( 'docs' ),
		'post_parent'    => 0
	);
	$docs      = get_posts( $args );
	$doc_items = '';

	$doc_item_count = 0;
	foreach ( $docs as $doc ) {
		if ( ! get_page_by_path( $doc->post_name, OBJECT, 'onepage-docs' ) ) {
			$doc_item_count ++;
			$doc_items .= '<option _wpnonce="'.wp_create_nonce($doc->ID).'" value="' . $doc->ID . '">' . $doc->post_title . '</option>';
		}
	}
	if ( $doc_item_count === 0 ) {
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


	$post_status   = get_post_status( $doc_id );
	$one_page_docs = get_posts( [
		'post_type'   => 'onepage-docs',
		'post_status' => 'publish',
		'name'        => $post_name,
	] );

	if ( $post_status != 'draft' ) :
		if ( count( $one_page_docs ) < 1 ) :
			?>
			<button class="button button-info one-page-doc" id="one-page-doc" name="submit" data-url="<?php echo admin_url( 'admin.php' ); ?>?parentID=<?php echo $doc_id; ?>&single_doc_title=<?php echo $one_page_title; ?>&make_onepage=yes&_wpnonce=<?php echo wp_create_nonce($doc_id); ?>">
				<?php esc_html_e( 'Make OnePage Doc', 'eazydocs' ); ?>
			</button>
			<?php
		else :
			foreach ( $one_page_docs as $single_docs ) :
				?>
				<a class="button button-info view-page-doc" id="view-page-doc" href="<?php echo get_permalink( $single_docs ); ?>" target="_blank">
					<?php esc_html_e( 'View OnePage Doc', 'eazydocs' ); ?>
				</a>
				<?php
			endforeach;
		endif;
	endif;
}

/**
 * Convert hexdec color string to rgb(a) string
 *
 * @param       $color
 * @param false $opacity
 * Convert hexdec color string to rgb(a) string
 *
 * @return string
 */
function ezd_hex2rgba( $color, $opacity = false ) {
	$default = 'rgb(0,0,0)';

	//Return default if no color provided
	if ( empty( $color ) ) {
		return $default;
	}

	//Sanitize $color if "#" is provided
	if ( $color[0] == '#' ) {
		$color = substr( $color, 1 );
	}

	//Check if color has 6 or 3 characters and get values
	if ( strlen( $color ) == 6 ) {
		$hex = array( $color[0] . $color[1], $color[2] . $color[3], $color[4] . $color[5] );
	} elseif ( strlen( $color ) == 3 ) {
		$hex = array( $color[0] . $color[0], $color[1] . $color[1], $color[2] . $color[2] );
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
 * Encode special characters
 *
 * @param $data
 *
 * @return array|string|string[]
 */
function ezd_chrEncode( $data ) {
	$data = str_replace( 'â€™', '&#39;', $data );
	$data = str_replace( 'Ã©', 'é', $data );
	$data = str_replace( 'â€', '-', $data );
	$data = str_replace( '-œ', '&#34;', $data );
	$data = str_replace( 'â€œ', '&#34;', $data );
	$data = str_replace( 'Ãª', 'ê', $data );
	$data = str_replace( 'Ã¶', 'ö', $data );
	$data = str_replace( 'â€¦', '...', $data );
	$data = str_replace( '-¦', '...', $data );
	$data = str_replace( 'â€“', '–', $data );
	$data = str_replace( 'â€²s', '’', $data );
	$data = str_replace( '-²s', '’', $data );
	$data = str_replace( 'â€˜', '&#39;', $data );
	$data = str_replace( '-˜', '&#39;', $data );
	$data = str_replace( '-“', '-', $data );
	$data = str_replace( 'Ã¨', 'è', $data );
	$data = str_replace( 'ï¼ˆ', '(', $data );
	$data = str_replace( 'ï¼‰', ')', $data );
	$data = str_replace( 'â€¢', '&bull;', $data );
	$data = str_replace( '-¢', '&bull;', $data );
	$data = str_replace( 'Â§ï‚§', '&bull;', $data );
	$data = str_replace( 'Â®', '&reg;', $data );
	$data = str_replace( 'â„¢', '&trade;', $data );
	$data = str_replace( 'Ã±', 'ñ', $data );
	$data = str_replace( 'Å‘s', 'ő', $data );
	$data = str_replace( '\\\"', '&quot;', $data );
	$data = str_replace( "\r", '', $data );
	$data = str_replace( "\\r", '', $data );
	$data = str_replace( "\n", '', $data );
	$data = str_replace( "\\n", '', $data );
	$data = str_replace( "\\\'", '', $data );
	$data = str_replace( "\\", "", $data );

	return $data;
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
	$wp_registered_blocks = get_posts( [
		'post_type' => 'wp_block'
	] );
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


function get_reusable_blocks_right() {
	$wp_registered_blocks = get_posts( [
		'post_type' => 'wp_block'
	] );
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

function manage_reusable_blocks() {
	$admin_url = admin_url( 'edit.php?post_type=wp_block' );
	$message   = sprintf( __( '<p class="ezd-text-support"> <a href="%s" target="_blank">Manage Reusable blocks</a></p>', 'eazydocs' ), $admin_url );

	return $message;
}

/**
 * Get all registered sidebars
 *
 * @return string
 */
function edit_sidebar_selectbox() {
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
    <b>Doc Layout</b><br />
    <input type="text" disabled name="ezd_doc_layout"
        value="<?php echo get_post_meta( get_the_ID(), 'ezd_doc_layout', true ); ?>" class="widefat" />
</p> <br>

<p class="ezd_left_content_heading"> Left Side Content</p>

<p><b>Content Type</b><br />
    <input type="text" disabled name="ezd_doc_content_type"
        value="<?php echo get_post_meta( get_the_ID(), 'ezd_doc_content_type', true ); ?>" class="widefat" />
</p>
<p><b>Content Box</b><br />
    <textarea name="ezd_doc_left_sidebar" disabled cols="30" rows="3"
        class="widefat"> <?php echo get_post_meta( get_the_ID(), "ezd_doc_left_sidebar", true ); ?> </textarea>
</p>

<p class="ezd_left_content_heading"> Right Side Content</p>

<p><b>Content Type</b><br />
    <input type="text" disabled name="ezd_doc_content_type_right"
        value="<?php echo get_post_meta( get_the_ID(), 'ezd_doc_content_type_right', true ); ?>" class="widefat" />
</p>
<p><b>Content Box</b><br />
    <textarea disabled name="ezd_doc_content_box_right" id="" cols="30" rows="3"
        class="widefat"><?php echo get_post_meta( get_the_ID(), 'ezd_doc_content_box_right', true ); ?></textarea>
</p>
<?php
}

add_action( 'save_post', function ( $post_id ) {
	// Doc Options
	$std_comment_id             = $_POST['ezd_doc_layout'] ?? '';
	$ezd_doc_content_type       = $_POST['ezd_doc_content_type'] ?? '';
	$ezd_doc_content_type_right = $_POST['ezd_doc_content_type_right'] ?? '';
	$ezd_doc_content_box_right  = $_POST['ezd_doc_content_box_right'] ?? '';
	update_post_meta( $post_id, 'ezd_doc_layout', $std_comment_id );
	update_post_meta( $post_id, 'ezd_doc_content_type', $ezd_doc_content_type );
	update_post_meta( $post_id, 'ezd_doc_content_type_right', $ezd_doc_content_type_right );
	update_post_meta( $post_id, 'ezd_doc_content_box_right', $ezd_doc_content_box_right );
} );

add_image_size( 'ezd_searrch_thumb16x16', '16', '16', true );
add_image_size( 'ezd_searrch_thumb50x50', '50', '50', true );

// Doc password form
function ezd_password_form( $output, $post = 0 ) {
	$protected_form          = get_option( 'eazydocs_settings' );
	$protected_form_switcher = $protected_form['protected_doc_form'] ?? '';
	$protected_form_title    = ! empty( $protected_form['protected_form_title'] ) ? $protected_form['protected_form_title'] : __( 'Enter Password & Read this Doc', 'eazydocs' );
	$protected_form_subtitle = ! empty( $protected_form['protected_form_subtitle'] ) ? $protected_form['protected_form_subtitle'] : __( 'This content is password protected. To view it please enter your password below:', 'eazydocs' );
	if ( ! empty( $protected_form_switcher == 'eazydocs-form' ) ) :
		?>
<div class="card ezd-password-wrap">
    <div class="card-body p-0 ezd-password-head">
        <div class="text-center p-4">
            <?php
					if ( has_post_thumbnail() ) :
						?>
            <a href="<?php the_permalink(); ?>" class="logo logo-admin">
                <?php the_post_thumbnail( 'ezd_searrch_thumb50x50', [ 'class' => 'mb-3' ] ); ?>
            </a>
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
        <form action="<?php echo esc_url( site_url( 'wp-login.php?action=postpass', 'login_post' ) ); ?>" method="post"
            class="form-horizontal auth-form">
            <div class="form-group mb-2">
                <label class="form-label" for="ezd_password">
                    <?php esc_html_e( 'Password', 'eazydocs' ); ?>
                </label>
                <div class="input-group mb-3">
                    <input name="post_password" required id="ezd_password" class="form-control" type="password"
                        placeholder="Enter password" />
                </div>
            </div>
            <!--end form-group-->

            <div class="form-group mb-0 row">
                <div class="col-12">
                    <button class="btn btn-primary w-100 waves-effect waves-light" type="submit">
                        <?php esc_html_e( 'Unlock', 'eazydocs' ); ?>
                        <i class="fas fa-sign-in-alt ms-1"></i>
                    </button>
                </div>
                <!--end col-->
            </div>
            <!--end form-group-->
        </form>
        <!--end form-->
    </div>
</div>
<?php
	else :
		return $output;
	endif;
}

add_filter( 'the_password_form', 'ezd_password_form', 20 );

/**
 * EazyDocs Admin pages
 * If any of the admin pages match the current page, return true.
 *
 * @return bool|void
 */
function ezydocs_admin_pages() {
	$admin_page = $_GET['page'] ?? '';
	$post_type  = $_GET['post_type'] ?? '';

	if ( $admin_page == 'eazydocs' || $admin_page == 'eazydocs-settings'
         || $admin_page == 'ezd-user-feedback'
         || $admin_page == 'ezd-analytics'
	     || $admin_page == 'ezd-user-feedback-archived'
	     || $admin_page == 'ezd-onepage-presents'
	     || $post_type == 'onepage-docs'
	     || strstr( $_SERVER['REQUEST_URI'], 'wp-admin/post-new.php' )
	     || strstr( $_SERVER['REQUEST_URI'], 'wp-admin/post.php' )
	) {
		return true;
	}
}

function ezydocs_frontend_assets() {
	if ( is_singular( 'docs' ) || is_singular( 'onepage-docs' ) || is_page_template( 'page-onepage.php' ) ) {
		return true;
	}
}

/**
 * EazyDocs Shortcodes
 *
 * @return bool|void
 */
function ezydocspro_shortcodes_assets() {
	global $post;
	$post_content_check = $post->post_content ?? '';
	if ( has_shortcode( $post_content_check, 'ezd_login_form' ) || has_shortcode( $post_content_check, 'reference' ) ) {
		return true;
	}
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
		array(
			'post_type'   => $post_type,
			'numberposts' => - 1,
			'post_status' => [ 'publish', 'private' ],
			'parent'      => 0,
		)
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
	echo wp_trim_words( wpautop( get_the_excerpt( $settings_key ) ), $limit, '' );
}

/**
 * Arrow Left Right
 *
 * @return void
 */
function ezd_arrow() {
	$arrow_icon = is_rtl() ? 'arrow_left' : 'arrow_right';
	echo esc_attr( $arrow_icon );
}

/**
 * Elementor Title Tag Options
 *
 * @return array
 */
function ezd_el_title_tags() {
	return [
		'h1'   => __( 'H1', 'eazydocs' ),
		'h2'   => __( 'H2', 'eazydocs' ),
		'h3'   => __( 'H3', 'eazydocs' ),
		'h4'   => __( 'H4', 'eazydocs' ),
		'h5'   => __( 'H5', 'eazydocs' ),
		'h6'   => __( 'H6', 'eazydocs' ),
		'div'  => __( 'Div', 'eazydocs' ),
		'span' => __( 'Span', 'eazydocs' ),
		'p'    => __( 'Paragraph', 'eazydocs' ),
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
		echo wp_get_attachment_image( $settings_key['id'], 'full', '', array( 'class' => $class ) );
	} elseif ( ! empty( $settings_key['url'] ) && empty( $settings_key['id'] ) ) {
		$class = ! empty( $class ) ? "class='$class'" : '';
		$attss = '';
		//echo print_r($atts);
		if ( ! empty( $atts ) ) {
			foreach ( $atts as $k => $att ) {
				$attss .= "$k=" . "'$att'";
			}
		}
		echo "<img src='{$settings_key['url']}' $class alt='$alt' $attss>";
	}
}

/**
 * Docs Layout Options based on license for Elementor Widget
 *
 * @return array[]
 */
function ezd_docs_layout_option() {
	if ( ezd_is_premium() ) {
		$options = [
			'1' => [
				'title' => __( 'Docs without tab', 'eazydocs' ),
				'icon'  => 'free-doc-tab'
			],
			'2' => [
				'title' => __( 'Tabbed with doc lists', 'eazydocs' ),
				'icon'  => 'docs-1',
			],
			'3' => [
				'title' => __( 'Flat tabbed docs', 'eazydocs' ),
				'icon'  => 'docs-2',
			],
			'4' => [
				'title' => __( 'Boxed Style', 'eazydocs' ),
				'icon'  => 'docs-3',
			],
			'5' => [
				'title' => __( 'Book Chapters / Tutorials', 'eazydocs' ),
				'icon'  => 'docs-4',
			],
			'6' => [
				'title' => __( 'Collaborative Docs', 'eazydocs' ),
				'icon'  => 'docs-5',
			]
		];
	} else {
		$options = [
			'1' => [
				'title' => __( 'Docs without tab', 'eazydocs' ),
				'icon'  => 'free-doc-tab',
			],
			'2' => [
				'title' => __( 'Tabbed with doc lists', 'eazydocs' ),
				'icon'  => 'docs-1 ezd-free-docs',
			],
			'3' => [
				'title' => __( 'Flat tabbed docs', 'eazydocs' ),
				'icon'  => 'docs-2 ezd-free-docs',
			],
			'4' => [
				'title' => __( 'Boxed Style', 'eazydocs' ),
				'icon'  => 'docs-3 ezd-free-docs',
			],
			'5' => [
				'title' => __( 'Book Chapters / Tutorials', 'eazydocs' ),
				'icon'  => 'docs-4 ezd-free-docs',
			],
			'6' => [
				'title' => __( 'List Style', 'eazydocs' ),
				'icon'  => 'docs-5 ezd-free-docs',
			]
		];
	}

	return $options;
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
	$query = $wpdb->prepare( "SELECT post_parent FROM $wpdb->posts WHERE post_type='docs' AND post_status='publish' AND ID = %d", $page_id );

	// Execute the query
	$parent = $wpdb->get_var( $query );

	if ( $parent == 0 ) {
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
	return get_children( array(
		'post_parent' => $post_id,
		'post_type'   => 'docs',
		'orderby'     => 'menu_order',
		'order'       => 'ASC',
	) );
}

/**
 * Get all shortcodes from content
 *
 * @param string $content
 *
 * @return string all shortcodes from content
 */
function ezd_all_shortcodes( $content ) {
	$return = array();
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
 * Add script in customize_controls_print_footer_scripts hook
 */
function ezd_customizer_script() {
    ?>
<script type="text/javascript">
jQuery(document).ready(function() {
    // Add .is_ezd_premium class to body
    if ( eazydocs_local_object.is_ezd_premium == "yes" ) {
        jQuery("body").addClass("ezd-premium");
    }
});
</script>
<?php
}
add_action( 'customize_controls_print_footer_scripts', 'ezd_customizer_script' );

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
	$elementor_templates = get_posts( array(
		'post_type' 		=> 'elementor_library',
		'posts_per_page' 	=> -1,
		'status' 			=> 'publish'
	) );
	
	$elementor_templates_array = array();
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
	if ( is_single() && get_post_type() == 'docs' && ! empty( ezd_get_opt('single_doc_layout') == 'el-template' ) &&  ! empty( ezd_get_opt('single_layout_id') ) && $current_theme == 'docly' ) {
		$classes[] = 'disable-docly-header';
    }
    return $classes;
}
add_filter('body_class', 'ezd_single_banner');

/**
 * Editor & Administrator access
 */
function ezd_is_admin_or_editor( $post_id = '', $action = '' ) {
	if ( $action == 'delete' && get_current_user_id() == get_post_field( 'post_author', $post_id ) ) {
		return true;
	} elseif ( $action == 'edit' && current_user_can('edit_posts') ) {
		return true;
	} elseif ( current_user_can('manage_options') ) {
		return true;
	}
	return false;
}

/**
 * Internal doc secured by user role
 * @param int $doc_id
 */
function ezd_internal_doc_security( $doc_id =  0 ) {
	// Private doc restriction
	if ( get_post_status( $doc_id ) == 'private' ) {

		$user_group = ezd_get_opt('private_doc_user_restriction');
		if ( $user_group['private_doc_all_user'] == 0 ) {

			// current user role
			$current_user_id    = get_current_user_id();
			$current_user       = new WP_User( $current_user_id );
			$current_roles      = ( array ) $current_user->roles;
			
			// All selected roles
			$private_doc_roles = $user_group['private_doc_roles'];
			$matching_roles 	= array_intersect($current_roles, $private_doc_roles);

			if ( empty( $matching_roles )) {
				if ( is_singular( 'docs' ) ) {				
					$message = esc_html__("You don't have permission to access this document!", 'eazydocs');
					$output = sprintf('<div class="ezd-lg-col-9"><span class="ezd-doc-warning-wrap"><i class="icon_lock"></i><span>%s</span></span></div>', $message);
					echo $output;
				}
				return null;
			}
		}
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

	// Check if the current user has the 'delete_posts' capability
	if ( current_user_can($action.'_posts') && $docID ) {
		// Check if the current user is the author of the post
		$post_author_id = (int) get_post_field('post_author', $docID);

		if ($current_user_id === $post_author_id || current_user_can('manage_options') ) {
			return true;
		} else {
			echo "<p style='$inline_styles'>You don't have permission to $action this post.</p>";
		}
	} else {
		// User does not have delete_posts capability
		echo "<p style='$inline_styles'>You don't have sufficient permission to perform this action.</p>";
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