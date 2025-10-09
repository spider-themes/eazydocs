<?php
namespace EazyDocs\Elementor;

/**
 * Cannot access directly.
 */
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

class Widgets{
    public function __construct() {
        // Register Widgets
        add_action( 'elementor/widgets/register', [ $this, 'register_widgets' ] );

        // Register Category
        add_action( 'elementor/elements/categories_registered', [ $this, 'ezd_register_category' ] );

        // Register Scripts
        add_action( 'elementor/editor/before_enqueue_scripts', [ $this, 'ezd_elementor_editor_styles' ] );
    }

    // Register Widgets
    public function register_widgets( $widgets_manager ) {
        // Include Helper Functions
        require_once( __DIR__ . '/template-helpers.php' );

        // Include Widget files
        require_once( __DIR__ . '/Docs/Doc_Widget.php' ); 
        require_once( __DIR__ . '/Search/Search_Widget.php' ); 
        require_once( __DIR__ . '/Single_Doc/Single_Doc.php' ); 
        $widgets_manager->register( new Docs\Doc_Widget() );
        $widgets_manager->register( new Search\Search_Widget() );
        $widgets_manager->register( new Single_Doc\Single_Doc() );
    }
    
    // Register category
    public function ezd_register_category( $elements_manager ) {
        $elements_manager->add_category(
            'eazydocs', [
                'title' => esc_html__( 'EazyDocs', 'eazydocs' ),
            ]
        );
    }

    // Register editor styles
    public function ezd_elementor_editor_styles(){   
        wp_enqueue_style( 'ezd-docs-editor', EAZYDOCS_ASSETS . '/css/elementor/ezd-elementor-editor.css', array(), EAZYDOCS_VERSION ); 

        if ( ezd_unlock_themes('docy','docly') ) {
            wp_enqueue_style( 'ezd-docs-pro-editor', EAZYDOCS_ASSETS . '/css/elementor/ezd-pro-elementor-editor.css', array(), EAZYDOCS_VERSION );
        }
    }
}