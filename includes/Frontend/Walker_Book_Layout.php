<?php
namespace eazyDocs\Frontend;

use Walker_Page;

/**
 * eazyDocs Docs Walker
 */
class Walker_Book_Layout extends Walker_Page {
    /**
     * What the class handles.
     *
     * @since 2.1.0
     * @var string
     *
     * @see Walker::$tree_type
     */
    public $tree_type = 'page';

    /**
     * Database fields to use.
     *
     * @since 2.1.0
     * @var array
     *
     * @see Walker::$db_fields
     * @todo Decouple this.
     */
    public $db_fields = array(
        'parent' => 'post_parent',
        'id'     => 'ID',
    );

    public static $parent_item = false;
    public static $parent_item_class = '';

    public function start_lvl( &$output, $depth = 0, $args = array() ) {
        $indent = str_repeat("\t", $depth);
        $output .= '<span class="icon"><i class="arrow_carrot-down"></i></span>'."\n$indent<ul class='dropdown_nav'>\n";

        if ( $args['has_children'] && $depth == 0 ) {
            $classes = isset($parent_item->ID) ? array( 'page_item', 'extra-class', 'page-item-' . self::$parent_item->ID ) : '';

            if ( self::$parent_item_class ) {
                $classes[] = self::$parent_item_class;
            }
        }
    }

    /**
     * Outputs the end of the current level in the tree after elements are output.
     *
     * @since 2.1.0
     *
     * @see Walker::end_lvl()
     *
     * @param string $output Used to append additional content (passed by reference).
     * @param int    $depth  Optional. Depth of page. Used for padding. Default 0.
     * @param array  $args   Optional. Arguments for outputting the end of the current level.
     *                       Default empty array.
     */
    public function end_lvl( &$output, $depth = 0, $args = array() ) {
        if ( isset( $args['item_spacing'] ) && 'preserve' === $args['item_spacing'] ) {
            $t = "\t";
            $n = "\n";
        } else {
            $t = '';
            $n = '';
        }
        $indent  = str_repeat( $t, $depth );
        $output .= "{$indent}</ul>{$n}";
    }

    /**
     * Outputs the beginning of the current element in the tree.
     *
     * @see Walker::start_el()
     * @since 2.1.0
     *
     * @param string  $output       Used to append additional content. Passed by reference.
     * @param WP_Post $page         Page data object.
     * @param int     $depth        Optional. Depth of page. Used for padding. Default 0.
     * @param array   $args         Optional. Array of arguments. Default empty array.
     * @param int     $current_page Optional. Page ID. Default 0.
     */
    public function start_el( &$output, $page, $depth = 0, $args = array(), $current_page = 0 ) {
        if ( isset( $args['item_spacing'] ) && 'preserve' === $args['item_spacing'] ) {
            $t = "\t";
            $n = "\n";
        } else {
            $t = '';
            $n = '';
        }
        if ( $depth ) {
            $indent = str_repeat( $t, $depth );
        } else {
            $indent = '';
        }

        $css_class = array();

        $has_post_thumb = !has_post_thumbnail($page->ID) ? 'no_icon' : '';

        if ( isset( $args['pages_with_children'][ $page->ID ] ) || $depth == 0 ) {
            $css_class = array( 'nav-item', $has_post_thumb, 'page-item-' . $page->ID);
        }

        if ( ! empty( $current_page ) ) {
            $_current_page = get_post( $current_page );
            if ( $_current_page && in_array( $page->ID, $_current_page->ancestors ) ) {
                $css_class[] = 'current_page_ancestor active';
            }
            if ( $page->ID == $current_page ) {
                $css_class[] = 'current_page_item active';
            } elseif ( $_current_page && $page->ID == $_current_page->post_parent ) {
                $css_class[] = 'current_page_parent';
            }
        } elseif ( $page->ID == get_option( 'page_for_posts' ) ) {
            $css_class[] = 'current_page_parent';
        }

        /**
         * Filters the list of CSS classes to include with each page item in the list.
         *
         * @since 2.8.0
         *
         * @see wp_list_pages()
         *
         * @param string[] $css_class    An array of CSS classes to be applied to each list item.
         * @param WP_Post  $page         Page data object.
         * @param int      $depth        Depth of page, used for padding.
         * @param array    $args         An array of arguments.
         * @param int      $current_page ID of the current page.
         */
        $css_classes = implode( ' ', apply_filters( 'page_css_class', $css_class, $page, $depth, $args, $current_page ) );
        $css_classes = $css_classes ? ' class="' . esc_attr( $css_classes ) . '"' : '';

        if ( '' === $page->post_title ) {
            /* translators: %d: ID of a post */
            $page->post_title = sprintf( esc_html__( '#%d (no title)', 'eazydocs' ), $page->ID );
        }

        $thumb = '';
        if ( $depth == 0 ) {
            $folder = '<img src="' . EAZYDOCS_IMG . '/icon/folder-closed.png" alt="' . esc_attr__('folder icon closed', 'eazydocs') . '"> <img src="' . EAZYDOCS_IMG . '/icon/folder-open.png" alt="' . esc_attr__('folder icon', 'eazydocs') . '">';
            $thumb = has_post_thumbnail($page->ID) ? get_the_post_thumbnail($page->ID) : $folder;
        }

        $args['link_before'] = empty( $args['link_before'] ) ? $thumb : $args['link_before'];
        $args['link_after']  = empty( $args['link_after'] ) ? '' : $args['link_after'];

        $atts                = array();
	    $atts['href']        = "#".sanitize_title(get_the_title($page->ID));
        $atts['data-postid'] = $page->ID;
	    $atts['class']   = 'nav-link';
        if ( $page->ID       == $current_page ) {
            $atts['class']   = 'active';
        }
        if ( isset( $args['pages_with_children'][ $page->ID ] ) || $depth == 0 ) {
            $atts['class']    = 'nav-link';
	        $atts['href']     = get_permalink( $page->ID );
        }
        $atts['aria-current'] = ( $page->ID == $current_page ) ? 'page' : '';

        /**
         * Filters the HTML attributes applied to a page menu item's anchor element.
         *
         * @since 4.8.0
         *
         * @param array $atts {
         *     The HTML attributes applied to the menu item's `<a>` element, empty strings are ignored.
         *
         *     @type string $href         The href attribute.
         *     @type string $aria_current The aria-current attribute.
         * }
         * @param WP_Post $page         Page data object.
         * @param int     $depth        Depth of page, used for padding.
         * @param array   $args         An array of arguments.
         * @param int     $current_page ID of the current page.
         */
        $atts = apply_filters( 'page_menu_link_attributes', $atts, $page, $depth, $args, $current_page );

        $attributes = '';
        foreach ( $atts as $attr => $value ) {
            if ( ! empty( $value ) ) {
                $value      = ( 'href' === $attr ) ? esc_url( $value ) : esc_attr( $value );
                $attributes .= ' ' . $attr . '="' . $value . '"';
            }
            /*if ( isset( $args['pages_with_children'][ $page->ID ] ) || $depth == 0 ) {
                $has_child_class = isset( $args['pages_with_children'][ $page->ID ] ) ? 'cat-item' : '';
                $attributes .= "class='nav-link'";
            }*/
        }

        $output .= $indent . sprintf(
                '<li%s><a%s>%s%s%s</a>',
                // '<li%s><a%s>%s%s%s</a>',
                $css_classes,
                $attributes,
                $args['link_before'],
                /** This filter is documented in wp-includes/post-template.php */
                apply_filters( 'the_title', $page->post_title, $page->ID ),

                $args['link_after']
            );

        if ( ! empty( $args['show_date'] ) ) {
            if ( 'modified' == $args['show_date'] ) {
                $time = $page->post_modified;
            } else {
                $time = $page->post_date;
            }

            $date_format = empty( $args['date_format'] ) ? '' : $args['date_format'];
            $output     .= ' ' . mysql2date( $date_format, $time );
        }
    }

    /**
     * Outputs the end of the current element in the tree.
     *
     * @since 2.1.0
     *
     * @see Walker::end_el()
     *
     * @param string  $output Used to append additional content. Passed by reference.
     * @param WP_Post $page   Page data object. Not used.
     * @param int     $depth  Optional. Depth of page. Default 0 (unused).
     * @param array   $args   Optional. Array of arguments. Default empty array.
     */
    public function end_el( &$output, $page, $depth = 0, $args = array() ) {
        if ( isset( $args['item_spacing'] ) && 'preserve' === $args['item_spacing'] ) {
            $t = "\t";
            $n = "\n";
        } else {
            $t = '';
            $n = '';
        }
        $output .= "</li>{$n}";
    }
}