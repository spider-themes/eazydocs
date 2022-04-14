<?php
/**
 * Class Docs
 */
class Docs {

	/**
	 * The post type name.
	 *
	 * @var string
	 */
	private $post_type = 'docs';

	/**
	 * Initialize the class
	 */
	public function __construct() {
		add_action( 'init', [ $this, 'register_post_type' ] );
		add_action( 'init', [ $this, 'register_taxonomy' ] );

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
		$slug = 'docs';
		if ( class_exists( 'EazyDocsPro' ) ) {
			$settings_options   = get_option( 'eazydocs_settings' );
			$slug               = $settings_options['docs-type-slug'] ?? 'docs';
		}

		$labels = [
			'name'               => _x( 'Docs', 'Post Type General Name', 'eazydocs' ),
			'singular_name'      => _x( 'Doc', 'Post Type Singular Name', 'eazydocs' ),
			'menu_name'          => __( 'EazyDocs', 'eazydocs' ),
			'parent_item_colon'  => __( 'Parent Doc', 'eazydocs' ),
			'all_items'          => __( 'All Docs', 'eazydocs' ),
			'view_item'          => __( 'View Doc', 'eazydocs' ),
			'add_new_item'       => __( 'Add Doc', 'eazydocs' ),
			'add_new'            => __( 'Add New', 'eazydocs' ),
			'edit_item'          => __( 'Edit Doc', 'eazydocs' ),
			'update_item'        => __( 'Update Doc', 'eazydocs' ),
			'search_items'       => __( 'Search Doc', 'eazydocs' ),
			'not_found'          => __( 'Not Doc found', 'eazydocs' ),
			'not_found_in_trash' => __( 'Not found in Trash', 'eazydocs' ),
		];
		$rewrite = [
			'slug'       => $slug,
			'with_front' => true,
			'pages'      => true,
			'feeds'      => true,
		];
		$args = [
			'labels'              => $labels,
			'supports'            => [ 'title', 'editor', 'thumbnail', 'revisions', 'page-attributes', 'comments' ],
			'hierarchical'        => true,
			'public'              => true,
			'show_ui'             => true,
			'show_in_menu'        => false,
			'show_in_nav_menus'   => true,
			'show_in_admin_bar'   => true,
			'menu_icon'           => 'dashicons-media-document',
			'can_export'          => true,
			'has_archive'         => false,
			'exclude_from_search' => false,
			'publicly_queryable'  => true,
			'show_in_rest'        => true,
			'rewrite'             => $rewrite,
			'capability_type'     => 'post',
			'taxonomies'          => [ 'doc_tag' ],
		];

		register_post_type( $this->post_type, apply_filters( 'eazydocs_post_type', $args ) );
	}

	/**
	 * Register doc tags taxonomy.
	 *
	 * @return void
	 */
	public function register_taxonomy() {
		$labels = [
			'name'                       => _x( 'Tags', 'Taxonomy General Name', 'eazydocs' ),
			'singular_name'              => _x( 'Tag', 'Taxonomy Singular Name', 'eazydocs' ),
			'menu_name'                  => __( 'Tags', 'eazydocs' ),
			'all_items'                  => __( 'All Tags', 'eazydocs' ),
			'parent_item'                => __( 'Parent Tag', 'eazydocs' ),
			'parent_item_colon'          => __( 'Parent Tag:', 'eazydocs' ),
			'new_item_name'              => __( 'New Tag', 'eazydocs' ),
			'add_new_item'               => __( 'Add New Item', 'eazydocs' ),
			'edit_item'                  => __( 'Edit Tag', 'eazydocs' ),
			'update_item'                => __( 'Update Tag', 'eazydocs' ),
			'view_item'                  => __( 'View Tag', 'eazydocs' ),
			'separate_items_with_commas' => __( 'Separate items with commas', 'eazydocs' ),
			'add_or_remove_items'        => __( 'Add or remove items', 'eazydocs' ),
			'choose_from_most_used'      => __( 'Choose from the most used', 'eazydocs' ),
			'popular_items'              => __( 'Popular Tags', 'eazydocs' ),
			'search_items'               => __( 'Search Tags', 'eazydocs' ),
			'not_found'                  => __( 'Not Found', 'eazydocs' ),
			'no_terms'                   => __( 'No items', 'eazydocs' ),
			'items_list'                 => __( 'Tags list', 'eazydocs' ),
			'items_list_navigation'      => __( 'Tags list navigation', 'eazydocs' ),
		];

		$rewrite = [
			'slug'         => 'doc-tag',
			'with_front'   => true,
			'hierarchical' => false,
		];

		$args = [
			'labels'            => $labels,
			'hierarchical'      => false,
			'public'            => true,
			'show_ui'           => true,
			'show_admin_column' => true,
			'show_in_nav_menus' => true,
			'show_tagcloud'     => true,
			'show_in_rest'      => true,
			'rewrite'           => $rewrite,
		];

		register_taxonomy( 'doc_tag', [ 'docs' ], $args );
	}
}
new Docs();