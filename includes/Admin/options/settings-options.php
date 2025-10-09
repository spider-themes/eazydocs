<?php
/**
 * Cannot access directly.
 */
if ( ! defined( 'ABSPATH' ) ) {
	die;
}

/**
 * Render CSF section wrapper
 *
 * @param string $prefix CSF prefix
 * @param string $id Section ID
 * @param string $title Section title
 * @param string $icon Section icon
 * @param array $fields Section fields
 * @param string $parent Parent section ID (optional)
 */
function ezd_render_csf_section( $prefix, $id, $title, $icon, $fields, $parent = '' ) {
	$section_config = [
		'id' => $id,
		'title' => $title,
		'icon' => $icon,
		'fields' => $fields
	];

	if ( $parent ) {
		$section_config['parent'] = $parent;
	}

	CSF::createSection( $prefix, $section_config );
}

/**
 * Get common CSF field configurations
 *
 * @param string $type Field type
 * @param array $config Field configuration
 * @return array Complete field configuration
 */
function ezd_get_csf_field_config( $type, $config ) {
	$defaults = [
		'id' => '',
		'type' => $type,
		'title' => '',
		'subtitle' => '',
		'desc' => '',
		'default' => '',
		'class' => '',
	];

	// Type-specific defaults
	switch ( $type ) {
		case 'switcher':
			$defaults['text_on'] = esc_html__( 'Show', 'eazydocs' );
			$defaults['text_off'] = esc_html__( 'Hide', 'eazydocs' );
			break;

		case 'select':
			$defaults['options'] = [];
			$defaults['chosen'] = true;
			break;

		case 'image_select':
			$defaults['options'] = [];
			break;
	}

	return array_merge( $defaults, $config );
}

/**
 * Create common CSF switcher field
 *
 * @param array $config Field configuration
 * @return array Complete field configuration
 */
function ezd_csf_switcher_field( $config ) {
	return ezd_get_csf_field_config( 'switcher', array_merge( $config, [
		'text_on' => esc_html__( 'Show', 'eazydocs' ),
		'text_off' => esc_html__( 'Hide', 'eazydocs' ),
	] ) );
}

/**
 * Create common CSF select field with pages
 *
 * @param array $config Field configuration
 * @return array Complete field configuration
 */
function ezd_csf_pages_select_field( $config ) {
	return ezd_get_csf_field_config( 'select', array_merge( $config, [
		'options' => 'pages',
		'chosen' => true,
		'ajax' => true,
		'query_args' => [
			'posts_per_page' => -1,
		],
	] ) );
}

/**
 * Create common CSF text field
 *
 * @param array $config Field configuration
 * @return array Complete field configuration
 */
function ezd_csf_text_field( $config ) {
	return ezd_get_csf_field_config( 'text', $config );
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

	// Disable Font Awesome loading
	'enqueue_webfont' 	 => false,
	'async_webfont' 	 => false
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
include EZD_SETTINGS_PATH . 'opt_email_reporting.php';
include EZD_SETTINGS_PATH . 'opt_docs_role_manager.php';
include EZD_SETTINGS_PATH . 'opt_docs_assistant.php';
include EZD_SETTINGS_PATH . 'opt_docs_subscription.php';
include EZD_SETTINGS_PATH . 'opt_onepage_doc.php';
include EZD_SETTINGS_PATH . 'opt_google_login.php';

// Additoinal fields
do_action('eazydocs_additoinal_csf_fields', $prefix);

include EZD_SETTINGS_PATH . 'opt_backup.php';