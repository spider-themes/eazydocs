<?php
/**
 * Class Docs
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

}
new One_Page_Docs();