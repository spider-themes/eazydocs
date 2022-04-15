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
		wp_enqueue_script( 'jquery' );
		if ( is_single() && get_post_type() == 'docs' ) {

			// Localize the script with new data
			$ajax_url              = admin_url( 'admin-ajax.php' );
			$wpml_current_language = apply_filters( 'wpml_current_language', null );
			if ( ! empty( $wpml_current_language ) ) {
				$ajax_url = add_query_arg( 'wpml_lang', $wpml_current_language, $ajax_url );
			}

			wp_localize_script( 'jquery', 'eazydocs_local_object',
				array(
					'ajaxurl'               => $ajax_url,
					'EAZYDOCS_FRONT_CSS'    => EAZYDOCS_FRONT_CSS,
                    'nonce'                 => wp_create_nonce( 'eazydocs-ajax' ),
				)
			);

			wp_register_style( 'print', EAZYDOCS_ASSETS . '/css/frontend/print.css' );
            wp_enqueue_style( 'mCustomScrollbar', EAZYDOCS_VEND . '/mcustomscrollbar/jquery.mCustomScrollbar.min.css' );
			wp_enqueue_style( 'rvfs', EAZYDOCS_VEND . '/font-size/css/rvfs.css' );
            // Scripts
            wp_enqueue_script( 'mCustomScrollbar', EAZYDOCS_VEND . '/mcustomscrollbar/jquery.mCustomScrollbar.concat.min.js', array( 'jquery' ), '3.1.13', true );
			wp_enqueue_script( 'rv-jquery-fontsize', EAZYDOCS_VEND . '/font-size/js/rv-jquery-fontsize-2.0.3.js' );
			wp_enqueue_script( 'printThis', EAZYDOCS_ASSETS . '/js/frontend/printThis.js' );
			wp_enqueue_script( 'anchor', EAZYDOCS_ASSETS . '/js/frontend/anchor.js' );
			wp_enqueue_script( 'eazydocs-single', EAZYDOCS_ASSETS . '/js/frontend/docs-single.js' );
		}
		if ( is_single() && get_post_type() == 'docs' || shortcode_exists( 'eazydocs' ) ) {
			wp_enqueue_style( 'bootstrap', EAZYDOCS_VEND . '/bootstrap/css/bootstrap.min.css' );
			wp_enqueue_style( 'elegant-icon', EAZYDOCS_VEND . '/elegant-icon/style.css' );
			wp_enqueue_script( 'bootstrap', EAZYDOCS_VEND . '/bootstrap/js/bootstrap.min.js' );
			wp_enqueue_script( 'popper', EAZYDOCS_VEND . '/bootstrap/js/popper.min.js' );
		}
	}

	public function enqueue_scripts_after() {
		if ( is_single() && get_post_type() == 'docs' || shortcode_exists( 'eazydocs' ) ) {
			wp_enqueue_style( 'frontend', EAZYDOCS_ASSETS . '/css/frontend.css' );
		}
	}
}