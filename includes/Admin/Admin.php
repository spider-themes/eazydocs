<?php

namespace eazyDocs\Admin;

/**
 * Class Admin
 * @package EazyDocs\Admin
 */
class Admin {

	/**
	 * Admin constructor.
	 */
	function __construct() {
		add_action( 'admin_menu', [ $this, 'eazyDocs_menu' ] );
	}

	/**
	 * Register Menu
	 */
	public function eazyDocs_menu() {
		add_menu_page( __( 'EazyDocs', 'eazyDocs' ), __( 'EazyDocs', 'eazyDocs' ), 'manage_options', 'eazydocs', [ $this, 'eazydocs_page' ], 'dashicons-media-document', 10 );
		add_submenu_page( 'eazydocs', __( 'EazyDocs', 'eazyDocs' ), __( 'EazyDocs', 'eazyDocs' ), 'manage_options', 'eazydocs', [ $this, 'eazydocs_page' ] );
	}

	/**
	 * Docs page
	 */
	public function eazydocs_page() {
		include __DIR__ . '/admin-template.php';
	}

}