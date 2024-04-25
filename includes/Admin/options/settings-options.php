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

CSF::createOptions( $prefix, array(
	'framework_title'    => esc_html__( 'EazyDocs', 'eazydocs' ) . ' <small> v' . EAZYDOCS_VERSION . '</small>',
	'menu_title'         => esc_html__( 'Settings', 'eazydocs' ),
	'menu_slug'          => 'eazydocs-settings',
	'menu_type'          => 'submenu',
	'menu_capability' 	 => ezd_get_opt( 'settings-edit-access', 'manage_options' ),
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