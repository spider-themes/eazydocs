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
 * Check the existing doc plugins
 * @return array
 */
 function eazydocs_docs_plugins() {
    include_once(ABSPATH . 'wp-admin/includes/plugin.php');
    $plugins = array();
    if ( is_plugin_active('betterdocs/betterdocs.php') ) {
        $plugins[] = array('betterdocs', 'BetterDocs');
    }
    if ( is_plugin_active('eazydocs/eazydocs.php') ) {
        $plugins[] = array('eazydocs', 'eazydocs');
    }
    if ( is_plugin_active('bsf-docs/bsf-docs.php') ) {
        $plugins[] = array('bsf-docs', 'BSF docs');
    }
    if ( is_plugin_active('documentor-lite/documentor-lite.php') ) {
        $plugins[] = array('documentor-lite', 'Documentor');
    }
    if ( is_plugin_active('echo-knowledge-base/echo-knowledge-base.php') ) {
        $plugins[] = array('echo-knowledge-base', 'Echo Knowledge Base');
    }
    if ( is_plugin_active('pressapps-knowledge-base/pressapps-knowledge-base.php') ) {
        $plugins[] = array('pressapps-knowledge-base', 'PressApps Knowledge Base');
    }
    return $plugins;
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
function docs_reading_time() {
	$content     = get_post_field( 'post_content', get_the_ID() );
	$word_count  = str_word_count( strip_tags( $content ) );
	$readingtime = ceil( $word_count / 200 );
	if ( $readingtime == 1 ) {
		$timer = esc_html__( " minute", 'docy' );
	} else {
		$timer = esc_html__( " minutes", 'docy' );
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
		'title_li'     => esc_html__( 'Pages', 'docy' ),
		'echo'         => 1,
		'authors'      => '',
		'sort_column'  => 'menu_order, post_title',
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

// Recently Viewed Docs
add_action( 'template_redirect', 'eazydocs_posts_visited' );
function eazydocs_posts_visited() {
	if ( is_single() && get_post_type() == 'docs' ) {
		$cooki    = 'eazydocs_recent_posts';
		$ft_posts = isset( $_COOKIE[ $cooki ] ) ? json_decode( $_COOKIE[ $cooki ], true ) : null;
		if ( isset( $ft_posts ) ) {
			// Remove current post in the cookie
			$ft_posts = array_diff( $ft_posts, array( get_the_ID() ) );
			// update cookie with current post
			array_unshift( $ft_posts, get_the_ID() );
		} else {
			$ft_posts = array( get_the_ID() );
		}
		setcookie( $cooki, json_encode( $ft_posts ), time() + ( DAY_IN_SECONDS * 31 ), COOKIEPATH, COOKIE_DOMAIN );
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

		$html = '';
		$args = apply_filters( 'eazydocs_breadcrumbs', [
			'delimiter' => '',
			'home'      => __( 'Home', 'eazydocs' ),
            'before'    => '<li class="breadcrumb-item active">',
            'after'     => '</li>',
		]);

        $breadcrumb_position = 1;

        $html .= '<ol class="breadcrumb" itemscope itemtype="http://schema.org/BreadcrumbList">';
        $html .= eazydocs_get_breadcrumb_item( $args['home'], home_url( '/' ), $breadcrumb_position );
        $html .= $args['delimiter'];

        $docs_home = eazydocs_get_option( 'docs_home', 'eazydocs_basics' );

        if ( $docs_home ) {
            ++$breadcrumb_position;

            $html .= eazydocs_get_breadcrumb_item( esc_html__( 'Docs', 'eazydocs' ), get_permalink( $docs_home ), $breadcrumb_position );
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