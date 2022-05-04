<?php
namespace eazyDocs\Admin;

/**
 * Class Assets
 * @package EazyDocs\Admin
 */
class Assets {

	/**
	 * Assets constructor.
	 */
	public function __construct() {
       $current_url = !empty($_GET["page"]) ? admin_url( "admin.php?page=".sanitize_text_field( $_GET["page"] ) ) : '';
        $target_url = admin_url( '/admin.php?page=eazydocs' );

        if ( $current_url == $target_url ) {
            add_action('admin_enqueue_scripts', [$this, 'eazydocs_dashboard_scripts']);
        }

        add_action('admin_enqueue_scripts', [$this, 'eazydocs_global_scripts']);
	}

	/**
	 * Register scripts and styles
	 */
	public function eazydocs_dashboard_scripts() {
		wp_enqueue_style( 'normalize', EAZYDOCS_ASSETS . '/css/admin/normalize.css' );
		wp_enqueue_style( 'nice-select', EAZYDOCS_ASSETS . '/css/admin/nice-select.css' );
		wp_enqueue_style( 'eazydocs-custom', EAZYDOCS_ASSETS . '/css/admin/custom.css' );
		wp_enqueue_style( 'sweetalert', EAZYDOCS_ASSETS . '/css/admin/sweetalert.css' );
		wp_enqueue_style( 'eazyDocs-main', EAZYDOCS_ASSETS . '/css/admin.css' );

		wp_enqueue_script( 'modernizr', EAZYDOCS_ASSETS . '/js/admin/modernizr-3.11.2.min.js', array('jquery'), true, true );
		wp_enqueue_script( 'jquery-ui', EAZYDOCS_ASSETS . '/js/admin/jquery-ui.js', array('jquery'), true, true );
		wp_enqueue_script( 'mixitup', EAZYDOCS_ASSETS . '/js/admin/mixitup.min.js', array('jquery'), true, true );
		wp_enqueue_script( 'mixitup-multifilter', EAZYDOCS_ASSETS . '/js/admin/mixitup-multifilter.js', array('jquery'), true, true );
		wp_enqueue_script( 'jquery-nice-select', EAZYDOCS_ASSETS . '/js/admin/jquery.nice-select.min.js', array('jquery'), true, true );
		wp_enqueue_script( 'tabby-polyfills', EAZYDOCS_ASSETS . '/js/admin/tabby.polyfills.min.js', array('jquery'), true, true );
		wp_enqueue_script( 'Sortable', EAZYDOCS_ASSETS . '/js/admin/Sortable.min.js', array('jquery'), true, true );
		wp_enqueue_script( 'eazyDocs-accordion', EAZYDOCS_ASSETS . '/js/admin/accordion.min.js', array('jquery'), true, true );
		wp_enqueue_script( 'sweetalert', EAZYDOCS_ASSETS . '/js/admin/sweetalert.min.js', array('jquery'), true, true );
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
                'create_prompt_title'   => esc_html__( 'Enter Docs Title', 'eazydocs' ),
                'delete_prompt_title'   => esc_html__( 'Are you sure to delete?', 'eazydocs' ),
                'no_revert_title'       => esc_html__( "You won't be able to revert this!", "eazydocs" ),
                'clone_prompt_title'    => esc_html__( "Are you sure to clone", "eazydocs" ),
                'nonce'                 => wp_create_nonce( 'eazydocs-admin-nonce' ),
            )
        );
		wp_enqueue_script( 'eazyDocs-custom', EAZYDOCS_ASSETS . '/js/admin/custom.js', array('jquery'), true, true );
		wp_enqueue_script( 'eazyDocs-main', EAZYDOCS_ASSETS . '/js/admin/main.js', array('jquery'), true, true );
	}

	/**
	 * Enqueue global scripts and styles by EazyDocs on WordPress dashboard
	 */
	public function eazydocs_global_scripts() {
        wp_enqueue_style( 'sweetalert', EAZYDOCS_ASSETS . '/css/admin/sweetalert.css' );
        wp_enqueue_style( 'eazydocs-admin-global', EAZYDOCS_ASSETS . '/css/admin-global.css' );

        wp_enqueue_script( 'sweetalert', EAZYDOCS_ASSETS . '/js/admin/sweetalert.min.js', array('jquery'), true, true );
		wp_enqueue_script( 'eazydocs-admin-global', EAZYDOCS_ASSETS . '/js/admin/admin-global.js' );
	}
}