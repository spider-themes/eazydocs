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
			'name'               => _x( 'One Page Docs', 'Post Type General Name', 'eazydocs' ),
			'singular_name'      => _x( 'One Page Doc', 'Post Type Singular Name', 'eazydocs' ),
			'menu_name'          => __( 'EazyDocs One Page', 'eazydocs' ),
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
			'show_in_admin_bar'   => true,
			'menu_icon'           => 'dashicons-media-document',
			'can_export'          => true,
			'has_archive'         => false,
			'exclude_from_search' => false,
			'publicly_queryable'  => true,
			'show_in_rest'        => true,
			'rewrite' => array( 'slug' => '/', 'with_front' => false ),
			'map_meta_cap'        => true
		];

		register_post_type( $this->post_type, apply_filters( 'eazydocs_post_type', $args ) );
	}

}
new One_Page_Docs();