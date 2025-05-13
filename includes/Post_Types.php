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
       	add_action( 'init', [ $this, 'register_badge' ] );
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
        $settings_options   = get_option( 'eazydocs_settings' );
		$custom_slug 		= $settings_options['docs-type-slug'] ?? '';
		
		// Validate the slug
		$pattern 			= '/[^a-zA-Z0-9-_]/';
		$safe_slug 			= preg_replace( $pattern, '-', $custom_slug );		
        $slug               = $safe_slug ?? 'docs';
		
		// Docs URL structure
        $docs_url 			= ezd_get_opt('docs-url-structure', 'custom-slug');
		$rewrite 			= [];
		
		if ( $docs_url == 'custom-slug' || get_option('permalink_structure') === '' || get_option('permalink_structure') === '/archives/%post_id%' ) {	 
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
		
		if ( current_user_can('edit_doc') ) {
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
		
	 
	public function register_badge() { 
		if ( ezd_is_premium() ) {
			$badge_labels = [
				'name'                       => _x( 'Badge', 'Taxonomy General Name', 'eazydocs' ),
				'singular_name'              => _x( 'Badge', 'Taxonomy Singular Name', 'eazydocs' ),
				'menu_name'                  => __( 'Badge', 'eazydocs' ),
				'all_items'                  => __( 'All Badges', 'eazydocs' ),
				'parent_item'                => __( 'Parent Badge', 'eazydocs' ),
				'parent_item_colon'          => __( 'Parent Badge:', 'eazydocs' ),
				'new_item_name'              => __( 'New Badge', 'eazydocs' ),
				'add_new_item'               => __( 'Add New Badge', 'eazydocs' ),
				'edit_item'                  => __( 'Edit Badge', 'eazydocs' ),
				'update_item'                => __( 'Update Badge', 'eazydocs' ),
				'view_item'                  => __( 'View Badge', 'eazydocs' ),
				'separate_items_with_commas' => __( 'Separate Badges with commas', 'eazydocs' ),
				'add_or_remove_items'        => __( 'Add or remove Badges', 'eazydocs' ),
				'choose_from_most_used'      => __( 'Choose from the most used', 'eazydocs' ),
				'popular_items'              => __( 'Popular Badges', 'eazydocs' ),
				'search_items'               => __( 'Search Badges', 'eazydocs' ),
				'not_found'                  => __( 'Not Found', 'eazydocs' ),
				'no_terms'                   => __( 'No Badges', 'eazydocs' ),
				'items_list'                 => __( 'Badges list', 'eazydocs' ),
				'items_list_navigation'      => __( 'Badges list navigation', 'eazydocs' ),
				'back_to_items'              => __( 'Back to Badges', 'eazydocs' ),
				'item_updated'               => __( 'Badge updated', 'eazydocs' ),
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