<?php if ( ! defined( 'ABSPATH' ) ) {
	die;
} // Cannot access directly.

//
// Set a unique slug-like ID
//
$prefix = 'eazydocs_settings';

//
// Create options
//
CSF::createOptions( $prefix, array(
	'menu_title' => esc_html__( 'Settings', 'eazydocs' ),
	'menu_slug'  => 'eazydocs-settings',
) );

//
// General Fields
//
CSF::createSection( $prefix, array(
	'id'    => 'general_fields',
	'title' => esc_html__( 'General', 'eazydocs' ),
	'icon'  => 'fas fa-plus-circle',

	'fields' => array(

		array(
			'id'      => 'docs-slug',
			'type'    => 'select',
			'title'   => esc_html__( 'Docs Page', 'eazydocs' ),
			'options' => 'pages',
			'desc'    => sprintf( wp_kses_post( __( 'Home page for docs page. Preferably use <code>[eazydocs]</code> shortcode or design your own', 'eazydocs' ) ) )
		),

		array(
			'id'         => 'docs-feedback',
			'type'       => 'switcher',
			'title'      => esc_html__( 'Feedback Area', 'eazydocs' ),
			'text_on'    => esc_html__( 'Show', 'eazydocs' ),
			'text_off'   => esc_html__( 'Hide', 'eazydocs' ),
			'default'    => true,
			'text_width' => 70
		),

		array(
			'id'         => 'feedback-admin-email',
			'type'       => 'text',
			'title'      => esc_html__( 'Email Address', 'eazydocs' ),
			'default'    => get_option( 'admin_email' ),
			'dependency' => array(
				'docs-feedback', '==', 'true'
			),
			'desc'       => esc_html__( 'The email address where the feedbacks should sent to', 'eazydocs' )
		),

		array(
			'id'      => 'enable-comment',
			'type'    => 'switcher',
			'title'   => esc_html__( 'Comment', 'eazydocs' ),
			'default' => true // or false
		),
		array(
			'id'      => 'pr-icon-switcher',
			'type'    => 'switcher',
			'title'   => esc_html__( 'Print article', 'eazydocs' ),
			'default' => true
		),
		array(
			'id'      => 'helpful_feedback',
			'type'    => 'switcher',
			'title'   => esc_html__( 'Helpful feedback', 'eazydocs' ),
			'default' => true
		)
	)
) );