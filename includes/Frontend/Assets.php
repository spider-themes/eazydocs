<?php
namespace eazyDocs\Frontend;

class Assets {
    /**
     * Assets constructor.
     **/
    public function __construct() {
        add_action( 'wp_enqueue_scripts', [ $this, 'enqueue_scripts' ] );
        add_action( 'wp_enqueue_scripts', [ $this, 'enqueue_scripts_after' ], 999 );
    }

    /**
     * Scripts enqueue
     */
    public static function enqueue_scripts() {
        $opt        = get_option( 'eazydocs_settings' );
        $version    = get_option('EazyDocs_version');

        $is_doc_ajax = $opt['is_doc_ajax'] ?? '';
        wp_enqueue_script( 'jquery' );
        wp_register_style( 'font-awesome-5', 'https://cdn.jsdelivr.net/npm/@fortawesome/fontawesome-free@5.15.4/css/all.min.css' );

        // Register
        wp_register_style( 'bootstrap-select', 'https://cdn.jsdelivr.net/npm/bootstrap-select@1.14.0-beta2/dist/css/bootstrap-select.min.css' );
        wp_register_script( 'bootstrap-select', 'https://cdn.jsdelivr.net/npm/bootstrap-select@1.14.0-beta2/dist/js/bootstrap-select.min.js', array('bootstrap') );
        wp_register_script( 'bootstrap-bundle', EAZYDOCS_VEND . '/bootstrap/bootstrap.bundle.min.js', array( 'jquery' ), '5.1.3', true );
        wp_register_script( 'anchor', EAZYDOCS_ASSETS . '/js/frontend/anchor.js', array( 'jquery' ), true, true );

        wp_enqueue_style( 'eazydocs-blocks', EAZYDOCS_ASSETS . '/css/blocks.css');

        if ( is_singular('docs') || is_singular('onepage-docs') || is_page_template('page-onepage.php') ) {
            // Localize the script with new data
            $ajax_url              = admin_url( 'admin-ajax.php' );
            $wpml_current_language = apply_filters( 'wpml_current_language', null );
            if ( !empty( $wpml_current_language ) ) {
                $ajax_url = add_query_arg( 'wpml_lang', $wpml_current_language, $ajax_url );
            }

            wp_localize_script( 'jquery', 'eazydocs_local_object',
                array(
                    'ajaxurl'               => $ajax_url,
                    'EAZYDOCS_FRONT_CSS'    => EAZYDOCS_FRONT_CSS,
                    'nonce'                 => wp_create_nonce( 'eazydocs-ajax' ),
                    'is_doc_ajax'           => $is_doc_ajax
                )
            );

            wp_enqueue_style( 'rvfs', EAZYDOCS_VEND . '/font-size/css/rvfs.css' );
	        wp_enqueue_style( 'ezd-onepage', EAZYDOCS_ASSETS . '/css/onepage.css' );
            
            // Scripts
            wp_enqueue_script( 'rv-jquery-fontsize', EAZYDOCS_VEND . '/font-size/js/rv-jquery-fontsize-2.0.3.js' );
            wp_enqueue_script( 'printThis', EAZYDOCS_ASSETS . '/js/frontend/printThis.js' );

            wp_enqueue_script( 'anchor' );
            wp_enqueue_script( 'bootstrap-toc', EAZYDOCS_ASSETS . '/js/frontend/bootstrap-toc.min.js', array('jquery','bootstrap') );
            wp_enqueue_script( 'eazydocs-single', EAZYDOCS_ASSETS . '/js/frontend/docs-single.js', array('jquery'), $version );
            wp_register_script( 'eazydocs-onepage', EAZYDOCS_ASSETS . '/js/frontend/onepage.js', array('jquery'), $version );
            wp_enqueue_script( 'bootstrap-bundle' );

            $is_dark_switcher = $opt['is_dark_switcher'] ?? '';
            if ( $is_dark_switcher == '1' ) {
                wp_enqueue_style( 'eazydocs-dark-mode', EAZYDOCS_ASSETS.'/css/frontend_dark-mode.css' );
            }

            wp_enqueue_style( 'eazydocs-frontend', EAZYDOCS_ASSETS . '/css/frontend.css', array('bootstrap'), $version );

            if( is_rtl() ){
                wp_enqueue_style( 'eazydocs-rtl', EAZYDOCS_ASSETS . '/css/rtl.css', array('eazydocs-frontend') );
            }
        }

        wp_register_style( 'bootstrap', EAZYDOCS_VEND . '/bootstrap/bootstrap.min.css' );
        wp_register_style( 'eazydocs-frontend-global', EAZYDOCS_ASSETS . '/css/frontend-global.css' );

        // Global Scripts
        if ( in_array( 'eazydocs_shortcode', get_body_class() ) || is_singular('docs') || is_singular('onepage-docs') || is_page_template('page-onepage.php') ) {
            wp_enqueue_style('bootstrap');
            wp_enqueue_style( 'elegant-icon', EAZYDOCS_VEND . '/elegant-icon/style.css' );
            wp_enqueue_style( 'eazydocs-frontend-global' );

            // Dynamic CSS
            $dynamic_css = '';
            if ( !empty($opt['brand_color']) ) {
                $brand_rgb = ezd_hex2rgba($opt['brand_color']);
                $dynamic_css .= ".doc_switch input[type=checkbox] { border: 1px solid rgba($brand_rgb, 0.3); background: rgba($brand_rgb, 0.25); }";
                $dynamic_css .= ".categories_guide_item .doc_border_btn { border: 1px solid rgba($brand_rgb, 0.2); background: rgba($brand_rgb, 0.05); }";
                $dynamic_css .= "#eazydocs_feedback .action_btn{ background: rgba($brand_rgb, .9); }";
                $dynamic_css .= ".documentation_item .media-body .title:hover { text-decoration-color: rgba($brand_rgb, 0.25);}";
            }

            wp_add_inline_style( 'eazydocs-frontend-global', $dynamic_css );

            wp_enqueue_script('eazydocs-global', EAZYDOCS_ASSETS . '/js/frontend/global.js', array('jquery'), $version);
        }
    }

    public function enqueue_scripts_after() {
        $version = get_option('EazyDocs_version');
        if ( is_single() && get_post_type() == 'docs' || get_post_type() == 'onepage-docs' || is_page_template('page-onepage.php') ) {
            wp_enqueue_style( 'eazydocs-responsive', EAZYDOCS_ASSETS . '/css/frontend/ezd-responsive.css' );
        }
    }
}