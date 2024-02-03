<?php
if ( ! defined( 'ABSPATH' ) ) {
	die;
} // Cannot access directly.

//
// Set a unique slug-like ID
//
$prefix = 'eazydocs_settings';

//
// Create options
//

$ezd_options = get_option( 'eazydocs_settings' );

$edit_access = [];
$edit_access = ezd_get_opt( 'settings-edit-access', 'eazydocs_settings' );


$all_roles = '';
if ( is_array( $edit_access ) ) {
	$all_roles = ! empty ( $edit_access ) ? implode( ',', $edit_access ) : '';
}

if ( ! empty ( $all_roles ) ) {
	$all_roled = explode( ',', $all_roles );

	if ( ! function_exists( 'wp_get_current_user' ) ) {
		include( ABSPATH . "wp-includes/pluggable.php" );
	}

	$user              = wp_get_current_user();
	$userdata          = get_user_by( 'id', $user->ID );
	$current_user_role = $userdata->roles[0] ?? '';

	$capabilites = 'manage_options';

	if ( in_array( $current_user_role, $all_roled ) ) {
		switch ( $current_user_role ) {
			case 'administrator':
				$capabilites = 'manage_options';
				break;

			case 'editor':
				$capabilites = 'publish_pages';
				break;

			case 'author':
				$capabilites = 'publish_posts';
				break;
		}
	}
} else {
	$capabilites = 'manage_options';
}
CSF::createOptions( $prefix, array(
	'framework_title'    => esc_html__( 'EazyDocs', 'eazydocs' ) . ' <small> v' . EAZYDOCS_VERSION . '</small>',
	'menu_title'         => esc_html__( 'Settings', 'eazydocs' ),
	'menu_slug'          => 'eazydocs-settings',
	'menu_type'          => 'submenu',
	'menu_capability' 	 => $capabilites,
	'menu_parent'        => 'eazydocs',
	'show_in_customizer' => ezd_get_opt( 'customizer_visibility' ),
) );

	// Widgets Settings.
	define( 'EZD_SETTINGS_PATH', plugin_dir_path( __FILE__ ) );

	include EZD_SETTINGS_PATH . 'opt_docs_general.php';
	include EZD_SETTINGS_PATH . 'opt_dark_mode.php';
	include EZD_SETTINGS_PATH . 'opt_doc_single.php';
	include EZD_SETTINGS_PATH . 'opt_restricted_docs.php';
	include EZD_SETTINGS_PATH . 'opt_customizer.php';
	include EZD_SETTINGS_PATH . 'opt_footnotes.php';
	include EZD_SETTINGS_PATH . 'opt_docs_shortcodes.php';
	include EZD_SETTINGS_PATH . 'opt_docs_collaboration.php';
	include EZD_SETTINGS_PATH . 'opt_docs_role_manager.php';
	include EZD_SETTINGS_PATH . 'opt_docs_assistant.php';
	include EZD_SETTINGS_PATH . 'opt_docs_subscription.php';