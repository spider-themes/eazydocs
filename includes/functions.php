<?php
/**
 * Get admin template part
 */
function eazydocs_get_admin_template_part( $template ) {
	$file = EAZYDOCS_PATH . "/includes/admin/templates/$template.php";
	load_template( $file, false );
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
 * @param $template
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
 * Get the value of a settings field.
 *
 * @param string $option settings field name
 * @param string $section the section name this field belongs to
 * @param string $default default text if it's not found
 *
 * @return mixed
 */
function eazydocs_get_option( $option, $section, $default = '' ) {
	$options = get_option( $section );

	if ( isset( $options[ $option ] ) ) {
		return $options[ $option ];
	}

	return $default;
}


/**
 * estimated reading time
 **/
function eazydocs_reading_time() {
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
function eazydocs_list_pages( $args = '' ) {
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
	);

	$r = wp_parse_args( $args, $defaults );

	if ( ! in_array( $r['item_spacing'], array( 'preserve', 'discard' ), true ) ) {
		// invalid value, fall back to default.
		$r['item_spacing'] = $defaults['item_spacing'];
	}

	$output       = '';
	$current_page = 0;

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
	 * @param array $r An array of page-listing arguments.
	 * @param array $pages List of WP_Post objects returned by `get_pages()`
	 *
	 * @since 1.5.1
	 * @since 4.4.0 `$pages` added as arguments.
	 *
	 * @see eazydocs_list_pages()
	 *
	 */
	if ( $r['echo'] ) {
		echo apply_filters( 'eazydocs_list_pages', $output, $r, $pages );;
	} else {
		return apply_filters( 'eazydocs_list_pages', $output, $r, $pages );;
	}
}

if ( ! function_exists( 'eazydocs_get_breadcrumb_item' ) ) {
	/**
	 * Schema.org breadcrumb item wrapper for a link.
	 *
	 * @param string $label
	 * @param string $permalink
	 * @param int $position
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
             '. esc_html( $label ) . '</li>';
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
		$home_text  = eazydocs_get_option( 'breadcrumb-home-text', 'eazydocs_settings' );
		$front_page = ! empty ( $home_text ) ? esc_html( $home_text ) : esc_html__( 'Home', 'eazydocs-pro' );

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


		$docs_page_title = eazydocs_get_option( 'docs-page-title', 'eazydocs_settings' );
		$docs_page_title = ! empty ( $docs_page_title ) ? esc_html( $docs_page_title ) : esc_html__( 'Docs', 'eazydocs' );

		$docs_home = eazydocs_get_option( 'docs-slug', 'eazydocs_settings' );

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

		$docs_page_title = eazydocs_get_option( 'docs-page-title', 'eazydocs_settings' );
		$docs_page_title = ! empty ( $docs_page_title ) ? esc_html( $docs_page_title ) : esc_html__( 'Docs', 'eazydocs' );
		$docs_home = eazydocs_get_option( 'docs-slug', 'eazydocs_settings' );

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
				$html .= $breadcrumbs[ $i ];
			}
		}

		$html .= ' ' . $args['before'] . get_the_title() . $args['after'];
		$html .= '</ol>';
		echo apply_filters( 'eazydocs_breadcrumbs_html', $html, $args );
	}
}

if( ! function_exists('docs_root_title') ){
	
	/**
	 * Docs Search breadcrumb.
	 *
	 * @return void
	 */
	function docs_root_title() {
		global $post;
		$home_text  = eazydocs_get_option( 'breadcrumb-home-text', 'eazydocs_settings' );
		$front_page = ! empty ( $home_text ) ? esc_html( $home_text ) : esc_html__( 'Home', 'eazydocs-pro' );

		$html = '';
		$args = apply_filters( 'eazydocs_breadcrumbs', [
			'delimiter' => '',
			'before'    => '<li class="breadcrumb-item active">',
			'after'     => '</li>',
		] );

		$breadcrumb_position = 1; 

		$is_parents = get_ancestors( $post->ID, 'docs' );
		$is_parent = $is_parents[0];
		if( $is_parent == 0 ){
			$parent_id = $post->ID;
		}else{
			$parent_id = $is_parent;
		}
		
		$html .= '<ol class="breadcrumb eazydocs-breadcrumb-root-title '. $parent_id .'" itemscope itemtype="http://schema.org/BreadcrumbList">';
		$html .= $args['delimiter'];


		$docs_page_title = eazydocs_get_option( 'docs-page-title', 'eazydocs_settings' );
		$docs_page_title = ! empty ( $docs_page_title ) ? esc_html( $docs_page_title ) : esc_html__( 'Docs', 'eazydocs' );

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
 * @param string $field Name of the key
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

	return $b > $a;
}

/**
 * Visible EazyDocs Menu in classic mode
 * Tag submenu in Tag screen
 **/
add_action( 'admin_footer', function () { ?>
    <script>
        // EazyDocs screen URL
        eazyDocsClassic = "edit.php?post_type=docs";
        // Tag screen URL
        eazyDocsTag = "edit-tags.php?taxonomy=doc_tag&post_type=docs";

        // EazyDocs menu active when it's EazyDocs screen
        if (window.location.href.indexOf(eazyDocsTag) > -1) {
            jQuery('.toplevel_page_eazydocs').removeClass('wp-not-current-submenu').addClass('wp-has-current-submenu wp-menu-open').find('li').has('a[href*="edit-tags.php"]').addClass('current');
        }

        // Tag Sub menu active when it's Tag screen
        if (window.location.href.indexOf(eazyDocsClassic) > -1) {
            jQuery('.toplevel_page_eazydocs').removeClass('wp-not-current-submenu').addClass('wp-has-current-submenu wp-menu-open').find('li.wp-first-item').addClass('current');
        }
    </script>
<?php } );

/**
 * Get all docs without parent
 * @return string
 */
function eazydocs_pro_doc_list() {
	$args      = array(
		'posts_per_page' => -1,
		'post_type'      => array( 'docs' ),
		'post_parent'    => 0
	);
	$docs      = get_posts( $args );
	$doc_items = '';

	    $doc_item_count = 0;
		foreach ( $docs as $doc ) {
			if ( ! get_page_by_path( $doc->post_name, OBJECT, 'onepage-docs' ) ) {
				$doc_item_count++;
				$doc_items .= '<option name="' . $doc->post_title . '">' . $doc->post_title . '</option>';
			}
		}
		if( $doc_item_count === 0){
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
 

	$post_status = get_post_status($doc_id);
	$one_page_docs = get_posts( [
		'post_type' => 'onepage-docs',
		'post_status' => 'publish',
		'name'      => $post_name,
	] );

	if( $post_status != 'draft' ) :
		if ( count( $one_page_docs ) < 1 ) :
			?>
			<button class="button button-info one-page-doc" id="one-page-doc" name="submit" data-url="<?php echo admin_url( 'admin.php/One_Page.php' ); ?>?parentID=<?php echo $doc_id; ?>&single_doc_title=<?php echo $one_page_title; ?>">
				<?php esc_html_e( 'Make OnePage Doc', 'eazydocs-pro' ); ?>
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
 * @param $color
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


function chrEncode( $data ) {
	$data = str_replace( '????????', '&#39;', $data );
	$data = str_replace( '????', '??', $data );
	$data = str_replace( '?????', '-', $data );
	$data = str_replace( '-??', '&#34;', $data );
	$data = str_replace( '???????', '&#34;', $data );
	$data = str_replace( '????', '??', $data );
	$data = str_replace( '????', '??', $data );
	$data = str_replace( '???????', '...', $data );
	$data = str_replace( '-??', '...', $data );
	$data = str_replace( '????????', '???', $data );
	$data = str_replace( '???????s', '???', $data );
	$data = str_replace( '-??s', '???', $data );
	$data = str_replace( '???????', '&#39;', $data );
	$data = str_replace( '-??', '&#39;', $data );
	$data = str_replace( '-???', '-', $data );
	$data = str_replace( '????', '??', $data );
	$data = str_replace( '??????', '(', $data );
	$data = str_replace( '???????', ')', $data );
	$data = str_replace( '???????', '&bull;', $data );
	$data = str_replace( '-??', '&bull;', $data );
	$data = str_replace( '???????????', '&bull;', $data );
	$data = str_replace( '????', '&reg;', $data );
	$data = str_replace( '???????', '&trade;', $data );
	$data = str_replace( '????', '??', $data );
	$data = str_replace( '?????s', '??', $data );
	$data = str_replace( '\\\"', '&quot;', $data );
	$data = str_replace( "\r", '', $data );
	$data = str_replace( "\\r", '', $data );
	$data = str_replace( "\n", '', $data );
	$data = str_replace( "\\n", '', $data );
	$data = str_replace( "\\\'", '', $data );
	$data = str_replace( "\\", "", $data );

	return $data;
}


function sidebar_selectbox() {
	global $wp_registered_sidebars;
	$sidebars = '';
	foreach ( $wp_registered_sidebars as $wp_registered_sidebar ) {
		$sidebars .= '<option value="' . $wp_registered_sidebar['id'] . '">' . $wp_registered_sidebar['name'] . '</option>';
	}

	return $sidebars;
}

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
        <b>Doc Layout</b><br/>
        <input type="text" disabled name="ezd_doc_layout" value="<?php echo get_post_meta( get_the_ID(), 'ezd_doc_layout', true ); ?>" class="widefat"/>
    </p> <br>

    <p class="ezd_left_content_heading"> Left Side Content</p>

    <p><b>Content Type</b><br/>
        <input type="text" disabled name="ezd_doc_content_type" value="<?php echo get_post_meta( get_the_ID(), 'ezd_doc_content_type', true ); ?>" class="widefat"/>
    </p>

    <p class="ezd_left_content_heading"> Right Side Content</p>

    <p><b>Content Type</b><br/>
        <input type="text" disabled name="ezd_doc_content_type_right" value="<?php echo get_post_meta( get_the_ID(), 'ezd_doc_content_type_right', true ); ?>" class="widefat"/>
    </p>
    <p><b>Content Box</b><br/>
        <textarea disabled name="ezd_doc_content_box_right" id="" cols="30" rows="3" class="widefat"><?php echo get_post_meta( get_the_ID(), 'ezd_doc_content_box_right', true ); ?></textarea>
    </p>
<?php }

add_action( 'save_post', function ( $post_id ) {
	// Doc Options
	$std_comment_id = $_POST['ezd_doc_layout'] ?? '';
	$ezd_doc_content_type = $_POST['ezd_doc_content_type'] ?? '';
	$ezd_doc_content_type_right = $_POST['ezd_doc_content_type_right'] ?? '';
	$ezd_doc_content_box_right = $_POST['ezd_doc_content_box_right'] ?? '';
	update_post_meta( $post_id, 'ezd_doc_layout', $std_comment_id );
	update_post_meta( $post_id, 'ezd_doc_content_type', $ezd_doc_content_type );
	update_post_meta( $post_id, 'ezd_doc_content_type_right', $ezd_doc_content_type_right );
	update_post_meta( $post_id, 'ezd_doc_content_box_right', $ezd_doc_content_box_right );
} );

add_image_size('ezd_searrch_thumb16x16','16','16', true);