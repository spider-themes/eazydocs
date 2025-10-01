<?php
/**
 * Cannot access directly.
 */
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

//
// Restricted Docs Fields
//
CSF::createSection( $prefix, array(
	'id'    => 'restricted_docs',
	'title' => esc_html__( 'Restricted Docs', 'eazydocs' ),
	'icon'  => 'dashicons dashicons-privacy',
) );

// Private Doc
CSF::createSection( $prefix, array(
	'id'     => 'private_doc_settings',
	'parent' => 'restricted_docs',
	'title'  => esc_html__( 'Private Doc', 'eazydocs' ),
	'icon'   => '',
	'fields' => array(
		array(
			'id'    => 'private_doc_visibility',
			'type'  => 'heading',
			'title' => esc_html__( 'Private Doc', 'eazydocs' )
		),

		array(
			'id'      => 'private_doc_mode',
			'type'    => 'select',
			'title'   => esc_html__( 'Visibility Mode', 'eazydocs' ),
			'subtitle'   => esc_html__( 'Select what to show when a logged out user try to visit a private doc URL.', 'eazydocs' ),
			'options' => [
				'login' => esc_html__( 'Login Page', 'eazydocs' ),
				'none'  => esc_html__( '404 Error', 'eazydocs' ),
			],
			'default' => 'none',
			'class'   => 'eazydocs-pro-notice'
		),

		array(
			'id'          => 'private_doc_login_page',
			'type'        => 'select',
			'placeholder' => 'Select page',
			'title'       => esc_html__( 'Select Page', 'eazydocs' ),
			'subtitle'    => esc_html__( 'Select Doc login page', 'eazydocs' ),
			'desc'        => esc_html__( 'If you want to change this page, use this shortcode [ezd_login_form] to display the login form on your desired page.', 'eazydocs' ),
			'options'     => 'pages',
			'class'       => 'eazydocs-pro-notice',
			'dependency'  => array(
				array( 'private_doc_mode', '==', 'login' ),
			),
			'query_args'  => array(
				'posts_per_page' => - 1,
			),
			'chosen'      => true,
			'ajax'        => true,
		),
		
		array(
			'id'         => 'private_doc_user_restriction',
			'type'      => 'fieldset',
			'title'      => esc_html__( 'Restrict Access to', 'eazydocs' ),
			'subtitle'   => esc_html__( 'Select who can view your private docs.', 'eazydocs' ),
			'dependency'  => array(
				array( 'private_doc_mode', '==', 'login' ),
			),
			'fields'    => array(
				array(
					'id'         => 'private_doc_all_user',
					'type'       => 'checkbox',
					'title'      => null,
					'text_on'    => esc_html__( 'Yes', 'eazydocs' ),
					'text_off'   => esc_html__( 'No', 'eazydocs' ),
					'label'	 	 => esc_html__( 'All logged in users', 'eazydocs' ),
					'default'    => false,
					'class'      => 'eazydocs-pro-notice',
				),
				array(
					'id'         => 'private_doc_roles',
					'type'       => 'select',
					'title'      => esc_html__( 'User Roles', 'eazydocs' ),
					'desc'   	 => esc_html__( 'Only selected User Roles will be able to view your Knowledge Base', 'eazydocs' ),
					'options'    => function_exists( 'eazydocs_user_role_names' ) && ezd_is_premium() ? eazydocs_user_role_names() : array(
						'administrator' => esc_html__( 'Administrator', 'eazydocs' ),
						'editor'        => esc_html__( 'Editor', 'eazydocs' ),
						'author'        => esc_html__( 'Author', 'eazydocs' ),
						'contributor'   => esc_html__( 'Contributor', 'eazydocs' ),
						'subscriber'    => esc_html__( 'Subscriber', 'eazydocs' ),
					),
					'default'    => 'administrator',
					'chosen'      => true,
					'ajax'        => true,
					'multiple'    => true,
					'class'      => 'eazydocs-pro-notice',
					'dependency' => array(
						array( 'private_doc_all_user', '==', 'false' ),
					)
				)
			)
		)
	)
) );

// Protected Doc
CSF::createSection( $prefix, array(
	'id'     => 'protected_doc_settings',
	'parent' => 'restricted_docs',
	'title'  => esc_html__( 'Protected Doc', 'eazydocs' ),
	'icon'   => '',
	'fields' => array(
		array(
			'id'    => 'protected_doc_heading',
			'type'  => 'heading',
			'title' => esc_html__( 'Protected Doc', 'eazydocs' )
		),

		array(
			'id'      => 'protected_doc_form',
			'type'    => 'select',
			'title'   => esc_html__( 'Password Form', 'eazydocs' ),
			'options' => [
				'eazydocs-form' => esc_html__( 'EazyDocs Form', 'eazydocs' ),
				'default'       => esc_html__( 'Default', 'eazydocs' ),
			],
			'default' => 'eazydocs-form'
		),

		array(
			'id'         => 'protected_doc_form_info',
			'type'       => 'subheading',
			'title'      => esc_html__( 'Form', 'eazydocs' ),
			'dependency' => array(
				array( 'protected_doc_form', '==', 'eazydocs-form' ),
			)
		),

		array(
			'id'          => 'protected_form_head_color',
			'type'        => 'color',
			'title'       => esc_html__( 'Header Color', 'eazydocs' ),
			'dependency'  => array(
				array( 'protected_doc_form', '==', 'eazydocs-form' ),
			),
			'output'      => '.ezd-password-wrap .ezd-password-head',
			'output_mode' => 'background-color',
		),

		array(
			'id'         => 'protected_form_title',
			'type'       => 'text',
			'title'      => esc_html__( 'Title', 'eazydocs' ),
			'dependency' => array(
				array( 'protected_doc_form', '==', 'eazydocs-form' ),
			)
		),

		array(
			'id'          => 'protected_form_title_color',
			'type'        => 'color',
			'title'       => esc_html__( 'Title Color', 'eazydocs' ),
			'dependency'  => array(
				array( 'protected_doc_form', '==', 'eazydocs-form' ),
			),
			'output'      => '.ezd-password-wrap .ezd-password-head p.ezd-password-title',
			'output_mode' => 'color',
		),

		array(
			'id'         => 'protected_form_subtitle',
			'type'       => 'text',
			'title'      => esc_html__( 'Sub Title', 'eazydocs' ),
			'dependency' => array(
				array( 'protected_doc_form', '==', 'eazydocs-form' ),
			)
		),

		array(
			'id'          => 'protected_form_subtitle_color',
			'type'        => 'color',
			'title'       => esc_html__( 'Sub Title Color', 'eazydocs' ),
			'dependency'  => array(
				array( 'protected_doc_form', '==', 'eazydocs-form' ),
			),
			'output'      => '.ezd-password-wrap .ezd-password-head p.ezd-password-subtitle',
			'output_mode' => 'color',
		),

		array(
			'id'         => 'protected_form_btn',
			'type'       => 'text',
			'title'      => esc_html__( 'Button', 'eazydocs' ),
			'dependency' => array(
				array( 'protected_doc_form', '==', 'eazydocs-form' ),
			)
		),

		array(
			'id'          => 'protected_form_btn_bgcolor',
			'type'        => 'color',
			'title'       => esc_html__( 'Button Text Color', 'eazydocs' ),
			'dependency'  => array(
				array( 'protected_doc_form', '==', 'eazydocs-form' ),
			),
			'output'      => '.ezd-password-wrap .ezd-password-body form button',
			'output_mode' => 'color',
		),

		array(
			'id'          => 'protected_form_btn_textcolor',
			'type'        => 'color',
			'title'       => esc_html__( 'Button Background Color', 'eazydocs' ),
			'dependency'  => array(
				array( 'protected_doc_form', '==', 'eazydocs-form' ),
			),
			'output'      => '.ezd-password-wrap .ezd-password-body form button',
			'output_mode' => 'background-color',
		),

	)
) );