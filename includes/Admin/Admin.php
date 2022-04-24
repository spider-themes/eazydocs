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

		$capabilites        = 'manage_options';
		$access             = eazydocspro_get_option( 'docs-write-access', 'eazydocs_settings' );
		$all_roles          = implode(',' , $access);
		$user               = wp_get_current_user();
		$current_user       = $user->roles[0];
		$all_roled     = explode(',',$all_roles);
		if (in_array($current_user, $all_roled)) {
			switch ($current_user ){
				case 'administrator':
				$capabilites = 'manage_options';
				break;

				case 'editor':
				$capabilites = 'publish_pages';
				break;

				case 'author':
				$capabilites = 'publish_posts';
				break;

				case 'contributor':
				$capabilites = 'edit_posts';
				break;

				case 'subscriber':
				$capabilites = 'read';
				break;
			}
		} else {
			$capabilites = 'manage_options';
		}

		add_menu_page( __( 'EazyDocss', 'eazyDocs' ), __( 'EazyDocs', 'eazyDocs' ), $capabilites, 'eazydocs', [ $this, 'eazydocs_page' ], 'dashicons-media-document', 10 );
		if ( class_exists( 'EazyDocsPro' ) ) {
			add_submenu_page( 'eazydocs', __( 'Customize', 'eazydocs' ), __( 'Customize', 'eazydocs' ), $capabilites, '/customize.php?autofocus[panel]=docs-page&autofocus[section]=docs-archive-page' );
		}

	}

	/**
	 * Docs page
	 */
	public function eazydocs_page() {
		include __DIR__ . '/admin-template.php';
	}

}