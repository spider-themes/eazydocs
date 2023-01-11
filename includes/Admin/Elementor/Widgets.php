<?php
namespace EazyDocs\Admin\Elementor;

class Widgets{
    public function __construct() {
        // Register Widgets
        add_action( 'elementor/widgets/widgets_registered', [ $this, 'register_widgets' ] );

        // Register Category
        add_action( 'elementor/elements/categories_registered', [ $this, 'ezd_register_category' ] );

        // Register Scripts
        add_action( 'elementor/editor/before_enqueue_scripts', [ $this, 'ezd_elementor_editor_styles' ] );
        add_action( 'elementor/frontend/after_register_scripts', [ $this, 'ezd_register_scripts' ] );
    }

    // Register Widgets
    public function register_widgets( $widgets_manager ) {
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
                'title' => __( 'EazyDocs', 'eazydocs' ),
            ]
        );
    }

    // Register scripts
    public function ezd_register_scripts() {
        wp_register_style( 'ezd-docs-widget', EAZYDOCS_ASSETS . '/css/frontend.css' );
    }

    // Register editor styles
    public function ezd_elementor_editor_styles(){   
        wp_enqueue_style( 'ezd-docs-editor', EAZYDOCS_ASSETS . '/css/elementor/ezd-elementor-editor.css' ); 

        if ( ezd_is_premium() ) {
            wp_enqueue_style( 'ezd-docs-pro-editor', EAZYDOCS_ASSETS . '/css/elementor/ezd-pro-elementor-editor.css' );
        }

    }
   
}