<?php
/**
 * Get admin template part
 */
function eazydocs_get_admin_template_part( $template ) {
    $file = EAZYDOCS_PATH."/includes/admin/templates/$template.php";
    load_template( $file, false );
}

/**
 * Get template part implementation for eazydocs.
 * Looks at the theme directory first
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

    if ( !$template ) {
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
}

if ( ! function_exists( 'eazydocs_breadcrumbs' ) ) {
	/**
	 * Docs breadcrumb.
	 *
	 * @return void
	 */
	function eazydocs_breadcrumbs() {
		global $post;
		$home_text       = eazydocs_get_option( 'breadcrumb-home-text', 'eazydocs_settings' );
		$front_page      = ! empty ( $home_text ) ? esc_html( $home_text ) : esc_html__( 'Home', 'eazydocs-pro' );

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


		$docs_page_title = eazydocs_get_option( 'docs-page-title', 'eazydocs_settings' );
		$docs_page_title = ! empty ( $docs_page_title ) ? esc_html( $docs_page_title ) : esc_html__( 'Docs', 'eazydocs' );

		$docs_home = eazydocs_get_option( 'docs-slug', 'eazydocs_settings' );

        if ( $docs_home ) {
            ++$breadcrumb_position;

            $html .= eazydocs_get_breadcrumb_item( $docs_page_title, get_permalink( $docs_home ), $breadcrumb_position );
            $html .= $args['delimiter'];
        }

        if ( 'docs' == $post->post_type && $post->post_parent ) {
            $parent_id   = $post->post_parent;
            $breadcrumbs = [];

            while ( $parent_id ) {
                ++$breadcrumb_position;

                $page          = get_post( $parent_id );
                $breadcrumbs[] = eazydocs_get_breadcrumb_item( get_the_title( $page->ID ), get_permalink( $page->ID ), $breadcrumb_position );
                $parent_id     = $page->post_parent;
            }

            $breadcrumbs = array_reverse( $breadcrumbs );

            for ( $i = 0; $i < count( $breadcrumbs ); ++$i ) {
                $html .= $breadcrumbs[$i];
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
 * @since 1.0.1 eazyDocs
 *
 * @param string $field Name of the key
 * @param string $context How to sanitize - raw|edit|db|display|attribute|js
 * @return string Field value
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
 * @since 1.0.1
 *
 * @param string $text
 * @return bool
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
        $codes = array('eazydocs', 'eazydocs_tab');

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
function date_sort($a, $b) {
	return strtotime($b) - strtotime($a);
}

/**
 * @param $a
 * @param $b
 *
 * @return bool
 */
function main_date_sort($a, $b) {
	$date1 = DateTime::createFromFormat('d/m/Y', $a);
	$date2 = DateTime::createFromFormat('d/m/Y', $b);
	return $b > $a;
}

/**
 * Visible EazyDocs Menu in classic mode
 * Tag submenu in Tag screen
 **/
add_action('admin_footer', function(){ ?>
	<script>
        // EazyDocs screen URL
        eazyDocsClassic = "edit.php?post_type=docs";
        // Tag screen URL
        eazyDocsTag = "edit-tags.php?taxonomy=doc_tag&post_type=docs";

        // EazyDocs menu active when it's EazyDocs screen
        if(window.location.href.indexOf(eazyDocsTag) > -1) {
            jQuery('.toplevel_page_eazydocs').removeClass('wp-not-current-submenu').addClass('wp-has-current-submenu wp-menu-open').find('li').has('a[href*="edit-tags.php"]').addClass('current');
        }

        // Tag Sub menu active when it's Tag screen
        if(window.location.href.indexOf(eazyDocsClassic) > -1) {
            jQuery('.toplevel_page_eazydocs').removeClass('wp-not-current-submenu').addClass('wp-has-current-submenu wp-menu-open').find('li.wp-first-item').addClass('current');
        }
	</script>
<?php });

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
	foreach ( $docs as $doc ) {

		if( ! get_page_by_path( $doc->post_name, OBJECT, 'onepage-docs' ) ) {
			$doc_items .= '<option name="' . $doc->post_title . '">' . $doc->post_title . '</option>';
		}
	}

	return $doc_items;
}

/**
 * @param $doc_id
 */
function eazydocs_one_page($doc_id){
	$one_page_title = get_the_title($doc_id);
	$docs           = get_post($doc_id);
	$post_name      = $docs->post_name;

	$one_page_docs = get_posts([
		'post_type'  => 'onepage-docs',
		'name'      => $post_name,
	]);

	if ( count( $one_page_docs ) < 1 ) :
		?>
        <button class="button button-info one-page-doc" id="one-page-doc" name="submit" data-url="<?php echo admin_url( 'admin.php/One_Page.php' ); ?>?parentID=<?php echo $doc_id; ?>&single_doc_title=<?php echo $one_page_title; ?>">
			<?php esc_html_e( 'Make OnePage Doc', 'eazydocs-pro' ); ?>
        </button>
	<?php
	else :
		foreach( $one_page_docs as $single_docs ) :
			?>
            <a class="button button-info view-page-doc" id="view-page-doc" href="<?php echo get_permalink($single_docs); ?>" target="_blank">
				<?php esc_html_e( 'View OnePage Doc', 'eazydocs' ); ?>
            </a>
		<?php
		endforeach;
	endif;
}

/**
 * @param $color
 * @param false $opacity
 * Convert hexdec color string to rgb(a) string
 * @return string
 */
function ezd_hex2rgba($color, $opacity = false) {

    $default = 'rgb(0,0,0)';

    //Return default if no color provided
    if(empty($color))
        return $default;

    //Sanitize $color if "#" is provided
    if ($color[0] == '#' ) {
        $color = substr( $color, 1 );
    }

    //Check if color has 6 or 3 characters and get values
    if (strlen($color) == 6) {
        $hex = array( $color[0] . $color[1], $color[2] . $color[3], $color[4] . $color[5] );
    } elseif ( strlen( $color ) == 3 ) {
        $hex = array( $color[0] . $color[0], $color[1] . $color[1], $color[2] . $color[2] );
    } else {
        return $default;
    }

    //Convert hexadec to rgb
    $rgb =  array_map('hexdec', $hex);

    //Check if opacity is set(rgba or rgb)
    if($opacity){
        if(abs($opacity) > 1)
            $opacity = 1.0;
        $output = 'rgba('.implode(",",$rgb).','.$opacity.')';
    } else {
        $output = implode(",",$rgb);
    }

    //Return rgb(a) color string
    return $output;
}


function chrEncode($data) {
	$data = str_replace('â€™', '&#39;' ,$data);
	$data = str_replace('Ã©', 'é' ,$data);
	$data = str_replace('â€', '-' ,$data);
	$data = str_replace('-œ', '&#34;' ,$data);
	$data = str_replace('â€œ', '&#34;' ,$data);
	$data = str_replace('Ãª', 'ê' ,$data);
	$data = str_replace('Ã¶', 'ö' ,$data);
	$data = str_replace('â€¦', '...' ,$data);
	$data = str_replace('-¦', '...' ,$data);
	$data = str_replace('â€“', '–' ,$data);
	$data = str_replace('â€²s', '’' ,$data);
	$data = str_replace('-²s', '’' ,$data);
	$data = str_replace('â€˜', '&#39;' ,$data);
	$data = str_replace('-˜', '&#39;' ,$data);
	$data = str_replace('-“', '-' ,$data);
	$data = str_replace('Ã¨', 'è' ,$data);
	$data = str_replace('ï¼ˆ', '(' ,$data);
	$data = str_replace('ï¼‰', ')' ,$data);
	$data = str_replace('â€¢', '&bull;' ,$data);
	$data = str_replace('-¢', '&bull;' ,$data);
	$data = str_replace('Â§ï‚§', '&bull;' ,$data);
	$data = str_replace('Â®', '&reg;' ,$data);
	$data = str_replace('â„¢', '&trade;' ,$data);
	$data = str_replace('Ã±', 'ñ' ,$data);
	$data = str_replace('Å‘s', 'ő' ,$data);
	$data = str_replace('\\\"', '&quot;' ,$data);
	$data = str_replace("\r", '' ,$data);
	$data = str_replace("\\r", '' ,$data);
	$data = str_replace("\n", '' ,$data);
	$data = str_replace("\\n", '' ,$data);
	$data = str_replace("\\\'", '' ,$data);
	$data = str_replace("\\", "" ,$data);

	return $data;
}