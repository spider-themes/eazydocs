<?php
/**
 * Cannot access directly.
 */
if ( ! defined( 'ABSPATH' ) ) {
	die;
}

/**
 * Set a unique slug-like ID
 */
$prefix = 'eazydocs_settings';

/**
 * Settings options
 */
$capability = ezd_get_opt( 'settings-edit-access' );

// Check the last element exist of the array
if ($capability === 'manage_options' || $capability === 'publish_pages' || $capability === 'publish_posts') {
	$menu_capability = $capability;
} else {
    $menu_capability = "manage_options";
}

CSF::createOptions( $prefix, array(
	'framework_title'    => esc_html__( 'EazyDocs', 'eazydocs' ) . ' <small> v' . EAZYDOCS_VERSION . '</small>',
	'menu_title'         => esc_html__( 'Settings', 'eazydocs' ),
	'show_bar_menu' 	 => false,
	'menu_slug'          => 'eazydocs-settings',
	'menu_type'          => 'submenu',
	'menu_capability' 	 => $menu_capability,
	'menu_parent'        => 'eazydocs',
	'show_in_customizer' => ezd_get_opt( 'customizer_visibility' ),
) );

/**
 * Define settings directory.
 */
define( 'EZD_SETTINGS_PATH', plugin_dir_path( __FILE__ ) );

/**
 * Include Files [ SECTIONS ].
*/

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
include EZD_SETTINGS_PATH . 'opt_backup.php';

// Additoinal fields
do_action('eazydocs_additoinal_csf_fields', $prefix);