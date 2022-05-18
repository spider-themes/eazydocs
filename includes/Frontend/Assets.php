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
        $opt = get_option( 'eazydocs_settings' );
        $is_doc_ajax = $opt['is_doc_ajax'] ?? '';
		wp_enqueue_script( 'jquery' );
        wp_register_style( 'font-awesome-5', 'https://cdn.jsdelivr.net/npm/@fortawesome/fontawesome-free@5.15.4/css/all.min.css' );
        wp_register_style( 'mCustomScrollbar', EAZYDOCS_VEND . '/mcustomscrollbar/jquery.mCustomScrollbar.min.css' );
        wp_register_style( 'bootstrap-select', EAZYDOCS_VEND . '/bootstrap-select/bootstrap-select.min.css', '', '1.13.18' );

        wp_register_script( 'bootstrap', EAZYDOCS_VEND . '/bootstrap/bootstrap.bundle.min.js', array( 'jquery' ), '5.1.3', true );
        wp_register_script( 'mCustomScrollbar', EAZYDOCS_VEND . '/mcustomscrollbar/jquery.mCustomScrollbar.concat.min.js', array( 'jquery' ), '3.1.13', true );
        wp_register_script( 'anchor', EAZYDOCS_ASSETS . '/js/frontend/anchor.min.js' );
        wp_register_script( 'bootstrap-select', EAZYDOCS_VEND . '/bootstrap-select/bootstrap-select.min.js', array('jquery','bootstrap'), '1.13.18', true );

        if ( is_page_template('page-onepage.php') ) {
            wp_enqueue_style( 'mCustomScrollbar' );
            wp_enqueue_script( 'mCustomScrollbar' );
        }

		if ( is_single() && get_post_type() == 'docs' ) {

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
			wp_enqueue_script( 'eazydocs-single', EAZYDOCS_ASSETS . '/js/frontend/docs-single.js', array('jquery') );
		}
		if ( is_single() && get_post_type() == 'docs' || shortcode_exists( 'eazydocs' ) ) {
			wp_enqueue_style( 'bootstrap', EAZYDOCS_VEND . '/bootstrap/bootstrap.min.css' );
			wp_enqueue_style( 'elegant-icon', EAZYDOCS_VEND . '/elegant-icon/style.css' );
			wp_enqueue_script( 'bootstrap' );
		}
        // Global Scripts
        wp_enqueue_script( 'eazydocs-global', EAZYDOCS_ASSETS . '/js/frontend/global.js' );
	}

	public function enqueue_scripts_after() {
        wp_register_style( 'eazydocs-frontend', EAZYDOCS_ASSETS . '/css/frontend.css' );
		if ( is_single() && get_post_type() == 'docs' || shortcode_exists( 'eazydocs' ) ) {
			wp_enqueue_style( 'eazydocs-frontend' );
			wp_enqueue_style( 'eazydocs-responsive', EAZYDOCS_ASSETS . '/css/frontend/responsive.css' );
		}
	}
}