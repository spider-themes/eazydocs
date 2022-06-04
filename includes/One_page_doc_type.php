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
		add_filter( 'post_type_link', [$this, 'remove_onepage_slug_permalink'], 10, 3 );
		add_action( 'pre_get_posts', [$this, 'update_onepage_slug_permalink'] );
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
		$rewrite = [
			'slug'       => $slug,
			'with_front' => true,
			'pages'      => true,
			'feeds'      => true,
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
			'rewrite'             => $rewrite,
			'map_meta_cap'        => true
		];

		register_post_type( $this->post_type, apply_filters( 'eazydocs_post_type', $args ) );
	}

	/**
	 * @param $post_link
	 * @param $post
	 * @param $leavename
	 *
	 * @return array|mixed|string|string[]
	 */
	public function remove_onepage_slug_permalink( $post_link, $post, $leavename ) {

		if ( 'onepage-docs' != $post->post_type || 'publish' != $post->post_status ) {
			return $post_link;
		}

		$post_link = str_replace( '/' . $post->post_type . '/', '/', $post_link );

		return $post_link;
	}

	/**
	 * @param $query
	 */
	function update_onepage_slug_permalink( $query ) {

		// Bail if this is not the main query.
		if ( ! $query->is_main_query() ) {
			return;
		}

		// Bail if this query doesn't match our very specific rewrite rule.
		if ( ! isset( $query->query['page'] ) || 2 !== count( $query->query ) ) {
			return;
		}

		// Bail if we're not querying based on the post name.
		if ( empty( $query->query['name'] ) ) {
			return;
		}

		// Add CPT to the list of post types WP will include when it queries based on the post name.
		$query->set( 'post_type', array( 'onepage-docs' ) );
	}

}
new One_Page_Docs();