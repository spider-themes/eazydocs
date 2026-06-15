<?php
namespace EazyDocs;

/**
 * Cannot access directly.
 */
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Class One_Page_Docs
 */
class One_Page_Docs {

	/**
	 * The post type name.
	 *
	 * @var string
	 */
	private $post_type = 'onepage-docs';

	/**
	 * Initialize the class
	 */
	public function __construct() {
		add_action( 'init', [ $this, 'register_post_type' ] );

		// Admin list-table columns: Docs count + selected Content Style.
		if ( is_admin() ) {
			add_filter( "manage_{$this->post_type}_posts_columns", [ $this, 'register_columns' ] );
			add_action( "manage_{$this->post_type}_posts_custom_column", [ $this, 'render_column' ], 10, 2 );
		}
	}

	/**
	 * Register the post type.
	 *
	 * @return void
	 */
	public function register_post_type() {

		/**
		 * Docs slug
		 * @var string
		 */
		$slug = 'onepage-docs';

		$labels = [
			'name'               => _x( 'OnePage Docs', 'Post Type General Name', 'eazydocs' ),
			'singular_name'      => _x( 'OnePage Doc', 'Post Type Singular Name', 'eazydocs' ),
			'menu_name'          => esc_html__( 'EazyDocs OnePage', 'eazydocs' ),
			'parent_item_colon'  => esc_html__( 'Parent Doc', 'eazydocs' ),
			'all_items'          => esc_html__( 'All Docs', 'eazydocs' ),
			'view_item'          => esc_html__( 'View Doc', 'eazydocs' ),
			'add_new_item'       => esc_html__( 'Add Doc', 'eazydocs' ),
			'add_new'            => esc_html__( 'Add New', 'eazydocs' ),
			'edit_item'          => false,
			'update_item'        => esc_html__( 'Update Doc', 'eazydocs' ),
			'search_items'       => esc_html__( 'Search Doc', 'eazydocs' ),
			'not_found'          => esc_html__( 'Not Doc found', 'eazydocs' ),
			'not_found_in_trash' => esc_html__( 'Not found in Trash', 'eazydocs' ),
			'capabilities'       => array( 'create_posts' => false ),

		];
		$args = [
			'labels'              => $labels,
			'supports'            => [ 'title', 'editor'],
			'hierarchical'        => true,
			'public'              => true,
			'show_ui'             => true,
			'show_in_menu'        => false,
			'show_in_nav_menus'   => true,
			'show_in_admin_bar'   => false,
			'menu_icon'           => 'dashicons-media-document',
			'can_export'          => true,
			'has_archive'         => false,
			'exclude_from_search' => false,
			'publicly_queryable'  => true,
			'show_in_rest'        => true,
            'rewrite'             => array( 'slug' => 'doc', 'with_front' => false ),
			'map_meta_cap'        => true
		];

		register_post_type( $this->post_type, apply_filters( 'eazydocs_onepage_post_type', $args ) );
	}

	/**
	 * Add the "Docs" and "Content Style" columns to the OnePage Docs list table.
	 *
	 * Inserts both columns right after the title so they read alongside the doc name.
	 *
	 * @param array $columns Existing columns keyed by id.
	 * @return array Modified columns.
	 */
	public function register_columns( $columns ) {
		$new_columns = [];

		foreach ( $columns as $key => $label ) {
			$new_columns[ $key ] = $label;

			// Drop our columns in immediately after the title column.
			if ( 'title' === $key ) {
				$new_columns['ezd_docs_count']    = esc_html__( 'Docs', 'eazydocs' );
				$new_columns['ezd_content_style'] = esc_html__( 'Content Style', 'eazydocs' );
			}
		}

		return $new_columns;
	}

	/**
	 * Render the value for the custom OnePage Docs columns.
	 *
	 * @param string $column  Column id being rendered.
	 * @param int    $post_id Current row's post ID.
	 * @return void
	 */
	public function render_column( $column, $post_id ) {
		if ( 'ezd_docs_count' === $column ) {
			$parent_id = $this->get_source_doc_id( $post_id );
			$count     = $parent_id ? $this->count_descendant_docs( $parent_id ) : 0;

			echo '<span class="ezd-docs-count">' . esc_html( number_format_i18n( $count ) ) . '</span>';
			return;
		}

		if ( 'ezd_content_style' === $column ) {
			$layout    = get_post_meta( $post_id, 'ezd_doc_layout', true );
			$inherited = empty( $layout );

			if ( $inherited ) {
				$layout = ezd_get_opt( 'onepage_default_layout', 'classic-onepage-layout' );
			}

			$label = $this->get_layout_label( $layout );

			if ( $inherited ) {
				/* translators: %s: layout name inherited from the global default. */
				$label = sprintf( esc_html__( '%s (Default)', 'eazydocs' ), $label );
			}

			echo esc_html( $label );
		}
	}

	/**
	 * Resolve the source `docs` post a OnePage Doc was generated from.
	 *
	 * Prefers the stored parent meta and falls back to matching the shared slug,
	 * mirroring how the front-end template locates the source document.
	 *
	 * @param int $post_id OnePage Doc post ID.
	 * @return int Source docs post ID, or 0 when none can be resolved.
	 */
	private function get_source_doc_id( $post_id ) {
		$parent_id = absint( get_post_meta( $post_id, 'ezd_onepage_parent_id', true ) );

		if ( $parent_id ) {
			return $parent_id;
		}

		$slug = get_post_field( 'post_name', $post_id );
		if ( $slug ) {
			$source = get_page_by_path( $slug, OBJECT, 'docs' );
			if ( $source instanceof \WP_Post ) {
				return (int) $source->ID;
			}
		}

		return 0;
	}

	/**
	 * Count every published descendant `docs` post beneath a parent doc.
	 *
	 * Builds the full parent → children map once per request (single query) so the
	 * list table doesn't fire a recursive lookup for every row.
	 *
	 * @param int $parent_id Source docs post ID.
	 * @return int Total descendant docs.
	 */
	private function count_descendant_docs( $parent_id ) {
		static $children_map = null;

		if ( null === $children_map ) {
			global $wpdb;
			$children_map = [];

			// No dynamic values, so no prepare() needed; one query for the whole tree.
			$rows = $wpdb->get_results(
				"SELECT ID, post_parent FROM {$wpdb->posts} WHERE post_type = 'docs' AND post_status = 'publish'"
			);

			foreach ( $rows as $row ) {
				$children_map[ (int) $row->post_parent ][] = (int) $row->ID;
			}
		}

		$count = 0;
		$stack = $children_map[ $parent_id ] ?? [];

		while ( $stack ) {
			$current = array_pop( $stack );
			$count++;

			if ( ! empty( $children_map[ $current ] ) ) {
				foreach ( $children_map[ $current ] as $child_id ) {
					$stack[] = $child_id;
				}
			}
		}

		return $count;
	}

	/**
	 * Map a stored layout key to a human-readable Content Style label.
	 *
	 * @param string $layout Layout meta value.
	 * @return string Translated, display-ready label.
	 */
	private function get_layout_label( $layout ) {
		$labels = [
			'classic-onepage-layout' => esc_html__( 'Classic', 'eazydocs' ),
			'fullscreen-layout'      => esc_html__( 'Fullscreen', 'eazydocs' ),
		];

		return $labels[ $layout ] ?? esc_html__( 'Classic', 'eazydocs' );
	}
}

// Initialize the class
new One_Page_Docs();