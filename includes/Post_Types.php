<?php
/**
 * Cannot access directly.
 */
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

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
       	add_action( 'init', [ $this, 'register_badge' ] );
	}

	/**
	 * Register the post type.
	 *
	 * @return void
	 */
	public function register_post_type() {
		$slug     = ezd_docs_slug();
		$rewrite  = [];
		
		if ( $slug ) {
			$rewrite = [
				'slug'       => $slug,
				'with_front' => true,
				'pages'      => true,
				'feeds'      => true,
			];
		}
		
		$labels = [
			'name'               => _x( 'Docs', 'Post Type General Name', 'eazydocs' ),
			'singular_name'      => _x( 'Doc', 'Post Type Singular Name', 'eazydocs' ),
			'menu_name'          => esc_html__( 'EazyDocs', 'eazydocs' ),
			'parent_item_colon'  => esc_html__( 'Parent Doc', 'eazydocs' ),
			'all_items'          => esc_html__( 'All Docs', 'eazydocs' ),
			'view_item'          => esc_html__( 'View Doc', 'eazydocs' ),
			'add_new_item'       => esc_html__( 'Add Doc', 'eazydocs' ),
			'add_new'            => esc_html__( 'Add New', 'eazydocs' ),
			'edit_item'          => esc_html__( 'Edit Doc', 'eazydocs' ),
			'update_item'        => esc_html__( 'Update Doc', 'eazydocs' ),
			'search_items'       => esc_html__( 'Search Doc', 'eazydocs' ),
			'not_found'          => esc_html__( 'Not Doc found', 'eazydocs' ),
			'not_found_in_trash' => esc_html__( 'Not found in Trash', 'eazydocs' ),
		];
		
		$args = [
			'labels'              => $labels,
			'supports'            => [ 'title', 'editor', 'thumbnail', 'revisions', 'page-attributes', 'comments', 'author', 'excerpt', 'blocks' ],
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
			'map_meta_cap'        => true,
			'taxonomies'          => [ 'doc_tag' ]
		];

		if ( current_user_can( 'read_private_docs' ) ) {
			$args['capability_type'] = 'doc';
			$args['capabilities'] = [
				'read_private_posts' => 'read_private_docs'
			];
		}
		
		if ( current_user_can('edit_docs') ) {
			$args['capability_type'] = [ 'doc', 'docs' ];
			$args['capabilities'] = [
				'edit_post'             => 'edit_doc',
				'edit_posts'            => 'edit_docs',
				'edit_others_posts'     => 'edit_others_docs',
				'edit_private_posts'    => 'edit_private_docs',
				'edit_published_posts'  => 'edit_published_docs',
				'publish_posts'         => 'publish_docs',
			];
		}
		
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
			'menu_name'                  => esc_html__( 'Tags', 'eazydocs' ),
			'all_items'                  => esc_html__( 'All Tags', 'eazydocs' ),
			'parent_item'                => esc_html__( 'Parent Tag', 'eazydocs' ),
			'parent_item_colon'          => esc_html__( 'Parent Tag:', 'eazydocs' ),
			'new_item_name'              => esc_html__( 'New Tag', 'eazydocs' ),
			'add_new_item'               => esc_html__( 'Add New Item', 'eazydocs' ),
			'edit_item'                  => esc_html__( 'Edit Tag', 'eazydocs' ),
			'update_item'                => esc_html__( 'Update Tag', 'eazydocs' ),
			'view_item'                  => esc_html__( 'View Tag', 'eazydocs' ),
			'separate_items_with_commas' => esc_html__( 'Separate items with commas', 'eazydocs' ),
			'add_or_remove_items'        => esc_html__( 'Add or remove items', 'eazydocs' ),
			'choose_from_most_used'      => esc_html__( 'Choose from the most used', 'eazydocs' ),
			'popular_items'              => esc_html__( 'Popular Tags', 'eazydocs' ),
			'search_items'               => esc_html__( 'Search Tags', 'eazydocs' ),
			'not_found'                  => esc_html__( 'Not Found', 'eazydocs' ),
			'no_terms'                   => esc_html__( 'No items', 'eazydocs' ),
			'items_list'                 => esc_html__( 'Tags list', 'eazydocs' ),
			'items_list_navigation'      => esc_html__( 'Tags list navigation', 'eazydocs' ),
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
		
	 
	public function register_badge() { 
		if ( ezd_is_premium() ) {
			$badge_labels = [
				'name'                       => _x( 'Badge', 'Taxonomy General Name', 'eazydocs' ),
				'singular_name'              => _x( 'Badge', 'Taxonomy Singular Name', 'eazydocs' ),
				'menu_name'                  => esc_html__( 'Badge', 'eazydocs' ),
				'all_items'                  => esc_html__( 'All Badges', 'eazydocs' ),
				'parent_item'                => esc_html__( 'Parent Badge', 'eazydocs' ),
				'parent_item_colon'          => esc_html__( 'Parent Badge:', 'eazydocs' ),
				'new_item_name'              => esc_html__( 'New Badge', 'eazydocs' ),
				'add_new_item'               => esc_html__( 'Add New Badge', 'eazydocs' ),
				'edit_item'                  => esc_html__( 'Edit Badge', 'eazydocs' ),
				'update_item'                => esc_html__( 'Update Badge', 'eazydocs' ),
				'view_item'                  => esc_html__( 'View Badge', 'eazydocs' ),
				'separate_items_with_commas' => esc_html__( 'Separate Badges with commas', 'eazydocs' ),
				'add_or_remove_items'        => esc_html__( 'Add or remove Badges', 'eazydocs' ),
				'choose_from_most_used'      => esc_html__( 'Choose from the most used', 'eazydocs' ),
				'popular_items'              => esc_html__( 'Popular Badges', 'eazydocs' ),
				'search_items'               => esc_html__( 'Search Badges', 'eazydocs' ),
				'not_found'                  => esc_html__( 'Not Found', 'eazydocs' ),
				'no_terms'                   => esc_html__( 'No Badges', 'eazydocs' ),
				'items_list'                 => esc_html__( 'Badges list', 'eazydocs' ),
				'items_list_navigation'      => esc_html__( 'Badges list navigation', 'eazydocs' ),
				'back_to_items'              => esc_html__( 'Back to Badges', 'eazydocs' ),
				'item_updated'               => esc_html__( 'Badge updated', 'eazydocs' ),
			];

			$rewrite_badge = [
				'slug'         => 'doc-badge',
				'with_front'   => true,
				'hierarchical' => true,
			];

			$badge_args = [
				'labels'            => $badge_labels,
				'hierarchical'      => true,
				'public'            => true,
				'show_ui'           => true,
				'show_admin_column' => true,
				'show_in_nav_menus' => true,
				'show_tagcloud'     => true,
				'show_in_rest'      => true,
				'rewrite'           => $rewrite_badge
			];
			register_taxonomy( 'doc_badge', [ 'docs' ], $badge_args );
		}
	}
}
new Docs();