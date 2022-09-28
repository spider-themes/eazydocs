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

        // define constants
        $this->define_constants();

        // block initialization
        add_action( 'init', [ $this, 'blocks_init' ] );


        add_action( 'enqueue_block_editor_assets', [ $this, 'editor_scripts' ] );

        // blocks category
        if( version_compare( $GLOBALS['wp_version'], '5.7', '<' ) ) {
            add_filter( 'block_categories', [ $this, 'register_block_category' ], 10, 2 );
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
     * Define the plugin constants
     */
    private function define_constants() {
        define( 'BOILERPLATE_VERSION', '1.0.0' );
        define( 'BOILERPLATE_URL', plugin_dir_url( __FILE__ ) );
        define( 'BOILERPLATE_LIB_URL', BOILERPLATE_URL . 'lib/' );
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
        // register single block
        $this->register_block( 'shortcode' );
    }

    /**
     * Register Block Category
     */
    public function register_block_category( $categories, $post ) {
        return array_merge(
            array(
                array(
                    'slug'  => 'eazydocs',
                    'title' => __( 'EazyDocs', 'eazydocs' ),
                ),
            ),
            $categories,
        );
    }

    /**
     * Load editor scripts and styles
     * @return void
     */
    public function editor_scripts() {
        wp_enqueue_style( 'ezd-editor', EAZYDOCS_ASSETS.'/css/ezd-block-editor.css', );
    }
}

/**
 * Kickoff
 */
EAZYDOCS_BLOCKS_CLASS::init();
