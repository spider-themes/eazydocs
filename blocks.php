<?php
/**
 * @package Zero Configuration with @wordpress/create-block
 *  [boilerplate] && [BOILERPLATE] ===> Prefix
 */

// Stop Direct Access
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

/**
 * Blocks Final Class
 */

final class EAZYDOCS_BLOCKS_CLASS {
    public function __construct() {
        // block initialization
        add_action( 'init', [ $this, 'blocks_init' ] );
        add_action( 'current_screen', [ $this, 'register_toolbar_block' ] );

        //add_action( 'enqueue_block_assets', [ $this, 'editor_scripts' ] );

        // blocks category
        if ( version_compare( $GLOBALS['wp_version'], '5.7', '<' ) ) {
            add_filter( 'block_categories_all', [ $this, 'register_block_category' ], 10, 2 );
        } else {
            add_filter( 'block_categories_all', [ $this, 'register_block_category' ], 10, 2 );
        }
    }

    /**
     * Initialize the plugin
     */
    public static function init(){
        static $instance = false;
        if( ! $instance ) {
            $instance = new self();
        }
        return $instance;
    }

    /**
     * Blocks Registration
     */
    public function register_block( $name, $options = array() ) {
        register_block_type( __DIR__ . '/build/' . $name, $options );
    }

    /**
     * Blocks Initialization
     */
    public function blocks_init() {
        // Always registered
        $this->register_block( 'shortcode' );

        $this->register_block( 'search-banner', array(
            'render_callback' => [ $this, 'search_banner_block_render' ]
        ));
    }

    /**
     * Register Toolbar Block
     */
    public function register_toolbar_block( $screen ) {
        if ( isset( $screen->post_type ) && $screen->post_type === 'docs' && $screen->base === 'post' && ezd_unlock_themes('docy','docly') ) {
            $this->register_block( 'eazydocs-toolbar' );
        }
    }

    /**
     * Enqueue editor scripts
     */
    function search_banner_block_render( $attributes ) {
	    wp_register_style( 'ezd-search-block', EAZYDOCS_URL.'/build/search-banner/style-index.css', array(), EAZYDOCS_VERSION );
        return require_once __DIR__ . '/includes/block-templates/search-banner.php';
    }

    /**
     * Register Block Category
     */
    public function register_block_category( $categories, $post ) {
        return array_merge(
            array(
                array(
                    'slug'  => 'eazydocs',
                    'title' => esc_html__( 'EazyDocs', 'eazydocs' ),
                ),
            ),
            $categories,
        );
    }
}

/**
 * Kickoff
 */
EAZYDOCS_BLOCKS_CLASS::init();