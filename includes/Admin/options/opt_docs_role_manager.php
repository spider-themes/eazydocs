<?php

//
// Docs role manager Fields
//
CSF::createSection( $prefix, array(
	'id'     => 'role_manager_fields',
	'title'  => esc_html__( 'Docs Role Manager', 'eazydocs' ),
	'icon'   => 'fas fa-plus-circle',
	'fields' => [
		array(
			'id'       => 'docs-write-access',
			'type'     => 'select',
			'title'    => esc_html__( 'Who Can Create Docs?', 'eazydocs' ),
			'subtitle' => esc_html__( 'Allow users to view & create Docs from the Doc Builder UI in the admin dashboard.', 'eazydocs' ),
			'options'  => [
				'administrator' => __( 'Administrator', 'eazydocs' ),
				'editor'        => __( 'Editor', 'eazydocs' ),
				'author'        => __( 'Author', 'eazydocs' ),
				'contributor'   => __( 'Contributor', 'eazydocs' ),
				'subscriber'    => __( 'Subscriber', 'eazydocs' ),
			],
			'chosen'   => true,
			'multiple' => true,
			'default'  => 'administrator',
			'class'    => 'eazydocs-pro-notice'
		),

		array(
			'id'       => 'settings-edit-access',
			'type'     => 'select',
			'title'    => esc_html__( 'Who Can Edit Settings?', 'eazydocs' ),
			'subtitle' => esc_html__( 'Allow users to update options from settings.', 'eazydocs' ),
			'options'  => [
				'manage_options' 	=> __( 'Administrator', 'eazydocs' ),
				'publish_pages'     => __( 'Administrator + Editor', 'eazydocs' ),
				'publish_posts'     => __( 'Administrator + Editor + Author', 'eazydocs' ),
			],
			'chosen'   => true,
			'default'  => 'manage_options',
			'multiple' => false,
			'class'    => 'eazydocs-pro-notice'
		),

		array(
			'id'       => 'customizer-edit-access',
			'type'     => 'select',
			'title'    => esc_html__( 'Who Can Edit Customizer?', 'eazydocs' ),
			'subtitle' => esc_html__( 'Allow users to customize Docs from customizer settings.', 'eazydocs' ),
			'options'  => [
				'administrator' => __( 'Administrator', 'eazydocs' ),
				'editor'        => __( 'Editor', 'eazydocs' ),
				'author'        => __( 'Author', 'eazydocs' ),
			],
			'chosen'   => true,
			'default'  => 'administrator',
			'multiple' => true,
			'class'    => 'eazydocs-pro-notice'
		)
	]
) );