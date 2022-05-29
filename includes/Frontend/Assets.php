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
        wp_register_style( 'mCustomScrollbar', EAZYDOCS_VEND . '/mcustomscrollbar/jquery.mCustomScrollbar.min.css' );

        // Register Bootstrap Select
        wp_register_style( 'bootstrap-select', 'https://cdn.jsdelivr.net/npm/bootstrap-select@1.14.0-beta2/dist/css/bootstrap-select.min.css' );
        wp_register_script( 'bootstrap-select', 'https://cdn.jsdelivr.net/npm/bootstrap-select@1.14.0-beta2/dist/js/bootstrap-select.min.js', array('bootstrap') );

        wp_register_script( 'bootstrap', EAZYDOCS_VEND . '/bootstrap/bootstrap.bundle.min.js', array( 'jquery' ), '5.1.3', true );
        wp_register_script( 'mCustomScrollbar', EAZYDOCS_VEND . '/mcustomscrollbar/jquery.mCustomScrollbar.concat.min.js', array( 'jquery' ), '3.1.13', true );
        wp_register_script( 'anchor', EAZYDOCS_ASSETS . '/js/frontend/anchor.min.js' );

        if (  is_single() && get_post_type() == 'one-page-docs' ) {
            wp_enqueue_style( 'mCustomScrollbar' );
            wp_enqueue_script( 'mCustomScrollbar' );
        }

		if ( is_single() && get_post_type() == 'docs' || get_post_type() == 'one-page-docs' ) {

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

			wp_register_style( 'print', EAZYDOCS_ASSETS . '/css/frontend/print.css' );
			wp_enqueue_style( 'rvfs', EAZYDOCS_VEND . '/font-size/css/rvfs.css' );
            wp_enqueue_style( 'mCustomScrollbar' );

            // Scripts
            wp_enqueue_script( 'mCustomScrollbar' );
			wp_enqueue_script( 'rv-jquery-fontsize', EAZYDOCS_VEND . '/font-size/js/rv-jquery-fontsize-2.0.3.js' );
			wp_enqueue_script( 'printThis', EAZYDOCS_ASSETS . '/js/frontend/printThis.js' );

			wp_enqueue_script( 'anchor' );
			wp_enqueue_script( 'bootstrap-toc', EAZYDOCS_ASSETS . '/js/frontend/bootstrap-toc.min.js', array('jquery','bootstrap') );
			wp_enqueue_script( 'eazydocs-single', EAZYDOCS_ASSETS . '/js/frontend/docs-single.js', array('jquery'), $version );
			wp_register_script( 'eazydocs-onpage', EAZYDOCS_ASSETS . '/js/frontend/onpage-menu.js', array('jquery'), $version );
			wp_enqueue_script( 'bootstrap' );

            $is_dark_switcher = $opt['is_dark_switcher'] ?? '';
            if ( $is_dark_switcher == '1' ) {
                wp_enqueue_style( 'eazydocs-dark-mode', EAZYDOCS_FRONT_CSS.'/dark-mode.css' );
            }

			wp_enqueue_style( 'eazydocs-frontend', EAZYDOCS_ASSETS . '/css/frontend.css', array('bootstrap'), $version );

			if( is_rtl() ){
				wp_enqueue_style( 'eazydocs-rtl', EAZYDOCS_ASSETS . '/css/rtl.css', array('eazydocs-frontend') );
			}
		}

        // Global Scripts
        if ( in_array( 'eazydocs_shortcode', get_body_class() ) || is_singular('docs') || is_singular('one-page-docs') ) {
            wp_enqueue_style( 'bootstrap', EAZYDOCS_VEND . '/bootstrap/bootstrap.min.css' );

            wp_enqueue_style( 'elegant-icon', EAZYDOCS_VEND . '/elegant-icon/style.css' );
            wp_enqueue_style('eazydocs-frontend-global', EAZYDOCS_ASSETS . '/css/frontend-global.css');
            wp_enqueue_script('eazydocs-global', EAZYDOCS_ASSETS . '/js/frontend/global.js', array('jquery'), $version);
        }
	}

	public function enqueue_scripts_after() {
        $version = get_option('EazyDocs_version');
		if ( is_single() && get_post_type() == 'docs' || get_post_type() == 'one-page-docs' ) {
			wp_enqueue_style( 'eazydocs-responsive', EAZYDOCS_ASSETS . '/css/frontend/ezd-responsive.css' );
		}
	}
}