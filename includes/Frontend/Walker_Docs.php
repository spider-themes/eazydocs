<?php
namespace EazyDocs\Frontend;

use Walker_Page;

/**
 * Cannot access directly.
 */
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * EazyDocs Docs Walker
 */
class Walker_Docs extends Walker_Page {
	/**
	 * What the class handles.
	 *
	 * @since 2.1.0
	 * @var string
	 *
	 * @see   Walker::$tree_type
	 */
	public $tree_type = 'page';

	/**
	 * Database fields to use.
	 *
	 * @since 2.1.0
	 * @var array
	 *
	 * @see   Walker::$db_fields
	 */
	public $db_fields = array(
		'parent' => 'post_parent',
		'id'     => 'ID',
	);

	public static $parent_item       = false;
	public static $parent_item_class = '';

	private function get_item_spacing( $args ) {
		return isset( $args['item_spacing'] ) && 'preserve' === $args['item_spacing'] ? array( "\t", "\n" ) : array( '', '' );
	}

	public function start_lvl( &$output, $depth = 0, $args = array() ) {
		$indent  = str_repeat( "\t", $depth );
		$output .= '<span class="icon"><i class="arrow_carrot-down"></i></span> </div>' . "\n$indent<ul class='dropdown_nav'>\n";

		if ( $args['has_children'] && $depth == 0 ) {
			$classes = isset( self::$parent_item->ID ) ? array( 'page_item', 'extra-class', 'page-item-' . self::$parent_item->ID ) : '';

			if ( self::$parent_item_class ) {
				$classes[] = self::$parent_item_class;
			}
		}
	}

	/**
	 * Outputs the end of the current level in the tree after elements are output.
	 *
	 * @param string $output Used to append additional content (passed by reference).
	 * @param int    $depth  Optional. Depth of page. Used for padding. Default 0.
	 * @param array  $args   Optional. Arguments for outputting the end of the current level.
	 *                       Default empty array.
	 *
	 * @see   Walker::end_lvl()
	 *
	 * @since 2.1.0
	 */
	public function end_lvl( &$output, $depth = 0, $args = array() ) {
		list( $t, $n ) = $this->get_item_spacing( $args );
		$indent        = str_repeat( $t, $depth );
		$output       .= "{$indent}</ul>{$n}";
	}

	/**
	 * Outputs the beginning of the current element in the tree.
	 *
	 * @param string  $output       Used to append additional content. Passed by reference.
	 * @param WP_Post $page         Page data object.
	 * @param int     $depth        Optional. Depth of page. Used for padding. Default 0.
	 * @param array   $args         Optional. Array of arguments. Default empty array.
	 * @param int     $current_page Optional. Page ID. Default 0.
	 *
	 * @since 2.1.0
	 *
	 * @see   Walker::start_el()
	 */
	public function start_el( &$output, $page, $depth = 0, $args = array(), $current_page = 0 ) {

		$icon_type = ezd_get_opt( 'doc_sec_icon_type', false );

		list( $t, $n ) = $this->get_item_spacing( $args );
		$indent        = $depth ? str_repeat( $t, $depth ) : '';

		$has_post_thumb = ! has_post_thumbnail( $page->ID ) ? 'no_icon' : '';
		$has_child      = isset( $args['pages_with_children'][ $page->ID ] ) ? 'has_child' : '';

		$css_class = array( 'nav-item', $has_post_thumb, $has_child, 'page-item-' . $page->ID );

		// Add post status class
		$css_class[] = 'post-status-' . $page->post_status;

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
		 * @param string[] $css_class    An array of CSS classes to be applied to each list item.
		 * @param WP_Post  $page         Page data object.
		 * @param int      $depth        Depth of page, used for padding.
		 * @param array    $args         An array of arguments.
		 * @param int      $current_page ID of the current page.
		 *
		 * @see   wp_list_pages()
		 *
		 * @since 2.8.0
		 */
		$css_classes = implode( ' ', apply_filters( 'page_css_class', $css_class, $page, $depth, $args, $current_page ) );
		$css_classes = $css_classes ? ' class="' . esc_attr( $css_classes ) . '"' : '';

		if ( '' === $page->post_title ) {
			/* translators: %d: ID of a post */
			$page->post_title = sprintf( esc_html__( '#%d (no title)', 'eazydocs' ), $page->ID );
		}

		$thumb = '';
		if ( $depth == 0 ) {
			$doc_sec_icon_open = ezd_get_opt( 'doc_sec_icon_open' );
			$folder_open       = is_array( $doc_sec_icon_open ) && ! empty( $doc_sec_icon_open['url'] )
				? $doc_sec_icon_open['url']
				: EAZYDOCS_IMG . '/icon/folder-open.png';

			$doc_sec_icon  = ezd_get_opt( 'doc_sec_icon' );
			$folder_closed = is_array( $doc_sec_icon ) && ! empty( $doc_sec_icon['url'] ) ? $doc_sec_icon['url'] : EAZYDOCS_IMG . '/icon/folder-closed.png';

			$folder = "<img class='closed' src='$folder_closed' alt='" . esc_attr__( 'Folder icon closed', 'eazydocs' )
						. "'> <img class='open' src='$folder_open' alt='" . esc_attr__( 'Folder open icon', 'eazydocs' ) . "'>";
			$thumb  = $icon_type && has_post_thumbnail( $page->ID ) ? get_the_post_thumbnail( $page->ID ) : $folder;
		}

		$args['link_before'] = empty( $args['link_before'] ) ? $thumb : $args['link_before'];

		// Build link_after with badge and visibility lock
		$link_after = function_exists( 'ezdpro_badge' ) ? ezdpro_badge( $page->ID ) : '';

		// Add visibility lock icon for private/role-restricted docs
		$link_after .= $this->get_visibility_lock_icon( $page );

		$args['link_after'] = $link_after;

		$atts                = array();
		$atts['href']        = get_permalink( $page->ID );
		$atts['data-postid'] = $page->ID;
		if ( $page->ID == $current_page ) {
			$atts['class'] = 'active';
		}
		$doc_link = '';
		if ( isset( $args['pages_with_children'][ $page->ID ] ) || $depth == 0 ) {
			$atts['class'] = 'nav-link';
			$doc_link      = '<div class="doc-link">';
		}
		$atts['aria-current'] = ( $page->ID == $current_page ) ? 'page' : '';

		/**
		 * Filters the HTML attributes applied to a page menu item's anchor element.
		 *
		 * @param array   $atts         {
		 *                              The HTML attributes applied to the menu item's `<a>` element, empty strings are ignored.
		 *
		 * @type string   $href         The href attribute.
		 * @type string   $aria_current The aria-current attribute.
		 *                              }
		 *
		 * @param WP_Post $page         Page data object.
		 * @param int     $depth        Depth of page, used for padding.
		 * @param array   $args         An array of arguments.
		 * @param int     $current_page ID of the current page.
		 *
		 * @since 4.8.0
		 */
		$atts = apply_filters( 'page_menu_link_attributes', $atts, $page, $depth, $args, $current_page );

		$attributes = '';
		foreach ( $atts as $attr => $value ) {
			if ( ! empty( $value ) ) {
				$value       = ( 'href' === $attr ) ? esc_url( $value ) : esc_attr( $value );
				$attributes .= ' ' . $attr . '="' . $value . '"';
			}
		}

		$post_title = ezd_is_premium() && ( $secondary = get_post_meta( $page->ID, 'ezd_doc_secondary_title', true ) ) ? esc_html( $secondary )
			: apply_filters( 'the_title', $page->post_title, $page->ID );

		$output .= $indent . sprintf(
			'<li%s> %s <a%s>%s%s%s</a>',
			$css_classes,
			$doc_link,
			$attributes,
			$args['link_before'],
			$post_title,
			$args['link_after']
		);
	}

	/**
	 * Outputs the end of the current element in the tree.
	 *
	 * @param string  $output Used to append additional content. Passed by reference.
	 * @param WP_Post $page   Page data object. Not used.
	 * @param int     $depth  Optional. Depth of page. Default 0 (unused).
	 * @param array   $args   Optional. Array of arguments. Default empty array.
	 *
	 * @since 2.1.0
	 *
	 * @see   Walker::end_el()
	 */
	public function end_el( &$output, $page, $depth = 0, $args = array() ) {
		list( $t, $n ) = $this->get_item_spacing( $args );
		$output       .= "</li>{$n}";
	}

	/**
	 * Get visibility lock icon HTML for a page.
	 *
	 * @param WP_Post|int $page The page/doc object or post ID.
	 * @return string HTML for the lock icon or empty string.
	 */
	public function get_visibility_lock_icon( $page ) {
		// Convert ID to post object if needed
		if ( is_int( $page ) ) {
			$page = \get_post( $page );
		}

		// Check if lock icon should be shown (from settings)
		$show_lock_icon = ezd_get_opt( 'role_visibility_show_lock_icon', true );
		if ( ! $show_lock_icon ) {
			return '';
		}

		$output       = '';
		$is_private   = $page->post_status == 'private';
		$has_password = ! empty( $page->post_password );

		// Check for role-based visibility
		$has_role_visibility = false;
		if ( function_exists( 'ezd_is_promax' ) && ezd_is_promax() ) {
			$role_visibility = get_post_meta( $page->ID, 'ezd_role_visibility', true );
			if ( ! empty( $role_visibility ) && is_array( $role_visibility ) ) {
				$has_role_visibility = true;
			}
		}

		// Add lock icon for private docs
		if ( $is_private ) {
			$output .= '<span class="ezd-doc-lock ezd-lock-private" title="' . esc_attr__( 'Private Doc', 'eazydocs' ) . '">';
			$output .= '<i class="icon_lock"></i>';
			$output .= '</span>';
		}

		// Add role icon for role-restricted docs
		if ( $has_role_visibility ) {
			$output .= '<span class="ezd-doc-lock ezd-lock-role" title="' . esc_attr__( 'Role Restricted', 'eazydocs' ) . '">';
			$output .= '<i class="icon_group"></i>';
			$output .= '</span>';
		}

		// Add lock icon for password-protected docs
		if ( $has_password ) {
			$output .= '<span class="ezd-doc-lock ezd-lock-protected" title="' . esc_attr__( 'Password Protected', 'eazydocs' ) . '">';
			$output .= '<i class="icon_key"></i>';
			$output .= '</span>';
		}

		return $output;
	}
}
