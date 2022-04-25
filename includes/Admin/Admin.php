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

		$capabilites    = 'manage_options';
		$cz_capabilites = 'manage_options';
		$sz_capabilites = 'manage_options';

		if ( function_exists( 'eazydocspro_get_option' ) ) {

			$access    = eazydocspro_get_option( 'docs-write-access', 'eazydocs_settings' );
			$cz_access = eazydocspro_get_option( 'customizer-edit-access', 'eazydocs_settings' );
			$sz_access = eazydocspro_get_option( 'settings-edit-access', 'eazydocs_settings' );

			$all_roles = ! empty( $access ) ? implode( ',', $access ) : '';
			$cz_roles  = ! empty( $cz_access ) ? implode( ',', $cz_access ) : '';
			$sz_roles  = ! empty( $sz_access ) ? implode( ',', $sz_access ) : '';

			$cz_roled = explode( ',', $cz_roles );
			$sz_roled = explode( ',', $sz_roles );

			$user         = wp_get_current_user();
			$current_user = $user->roles[0];
			$all_roled    = explode( ',', $all_roles );
			if ( in_array( $current_user, $all_roled ) ) {
				switch ( $current_user ) {
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
		}

		add_menu_page( __( 'EazyDocss', 'eazyDocs' ), __( 'EazyDocs', 'eazyDocs' ), $capabilites, 'eazydocs', [ $this, 'eazydocs_page' ], 'dashicons-media-document', 10 );

		if ( class_exists( 'EazyDocsPro' ) ) {
			if ( in_array( $current_user, $cz_roled ) ) {
				switch ( $current_user ) {
					case 'administrator':
						$cz_capabilites = 'manage_options';
						break;

					case 'editor':
						$cz_capabilites = 'publish_pages';
						break;

					case 'author':
						$cz_capabilites = 'publish_posts';
						break;
				}
			} else {
				$cz_capabilites = 'manage_options';
			}

			add_submenu_page( 'eazydocs', __( 'Customize', 'eazydocs' ), __( 'Customize', 'eazydocs' ), $cz_capabilites, '/customize.php?autofocus[panel]=docs-page&autofocus[section]=docs-archive-page' );

			if ( in_array( $current_user, $sz_roled ) ) {
				switch ( $current_user ) {
					case 'administrator':
						$sz_capabilites = 'manage_options';
						break;

					case 'editor':
						$sz_capabilites = 'publish_pages';
						break;

					case 'author':
						$sz_capabilites = 'publish_posts';
						break;
				}
			} else {
				$sz_capabilites = 'manage_options';
			}

			add_submenu_page( 'eazydocs', __( 'Settings', 'eazydocs' ), __( 'Settings', 'eazydocs' ), $sz_capabilites, 'eazydocs-settings' );
		}
	}

	/**
	 * Docs page
	 */
	public function eazydocs_page() {
		include __DIR__ . '/admin-template.php';
	}
}