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
		$current_url         = ! empty( $_GET["page"] ) ? admin_url( "admin.php?page=" . sanitize_text_field( $_GET["page"] ) ) : '';
		$target_url          = admin_url( '/admin.php?page=eazydocs' );
		$target_one_page_url = admin_url( '/admin.php?page=eazydocs-onepage' );

		if ( $current_url == $target_url ) {
			add_action( 'admin_enqueue_scripts', [ $this, 'eazydocs_dashboard_scripts' ] );
		} elseif ( $current_url == $target_one_page_url ) {
			add_action( 'admin_enqueue_scripts', [ $this, 'eazydocs_dashboard_scripts' ] );
		}

		add_action( 'admin_enqueue_scripts', [ $this, 'eazydocs_global_scripts' ] );
	}

	/**
	* Register scripts and styles
	**/
	public function eazydocs_dashboard_scripts() {
		$version = get_option( 'EazyDocs_version' );

		wp_enqueue_style( 'normalize', EAZYDOCS_ASSETS . '/css/admin/normalize.css' );
		wp_enqueue_style( 'nice-select', EAZYDOCS_ASSETS . '/css/admin/nice-select.css' );
		wp_enqueue_style( 'sweetalert', EAZYDOCS_ASSETS . '/css/admin/sweetalert.css' );
		wp_enqueue_style( 'eazyDocs-main', EAZYDOCS_ASSETS . '/css/admin.css', array(), $version );

		wp_enqueue_script( 'modernizr', EAZYDOCS_ASSETS . '/js/admin/modernizr-3.11.2.min.js', array( 'jquery' ), '', true );
		wp_enqueue_script( 'jquery-ui', EAZYDOCS_ASSETS . '/js/admin/jquery-ui.js', array( 'jquery' ), '', true );
		wp_enqueue_script( 'mixitup', EAZYDOCS_ASSETS . '/js/admin/mixitup.min.js', array( 'jquery' ), '', true );
		wp_enqueue_script( 'mixitup-multifilter', EAZYDOCS_ASSETS . '/js/admin/mixitup-multifilter.js', array( 'jquery' ), '', true );
		wp_enqueue_script( 'jquery-nice-select', EAZYDOCS_ASSETS . '/js/admin/jquery.nice-select.min.js', array( 'jquery' ), '', true );
		wp_enqueue_script( 'tabby-polyfills', EAZYDOCS_ASSETS . '/js/admin/tabby.polyfills.min.js', array( 'jquery' ), '', true );
		wp_enqueue_script( 'eazyDocs-accordion', EAZYDOCS_ASSETS . '/js/admin/accordion.min.js', array( 'jquery' ), '', true );
		wp_enqueue_script( 'sweetalert', EAZYDOCS_ASSETS . '/js/admin/sweetalert.min.js', array( 'jquery' ), '', true );

		wp_enqueue_script( 'eazyDocs-custom', EAZYDOCS_ASSETS . '/js/admin/custom.js', array( 'jquery' ), $version, true );
		wp_enqueue_script( 'eazyDocs-main', EAZYDOCS_ASSETS . '/js/admin/main.js', array( 'jquery' ), $version, true );
	}

	/**
	 * Enqueue global scripts and styles by EazyDocs on WordPress dashboard
	 */
	public function eazydocs_global_scripts() {
		ezydocs_admin_assets();
	}
}