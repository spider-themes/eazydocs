<?php
/**
 * User Permissions & Role Management
 * Control who can create, edit, and manage documentation.
 */
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

//
// Docs role manager Fields
//
CSF::createSection( $prefix, array(
	'id'     => 'role_manager_fields',
	'title'  => esc_html__( 'User Permissions', 'eazydocs' ),
	'icon'   => 'dashicons dashicons-businessman',
	'fields' => [
		array(
			'id'       => 'docs-write-access',
			'type'     => 'select',
			'title'    => esc_html__( 'Documentation Authors', 'eazydocs' ),
			'subtitle' => esc_html__( 'Select which user roles can create and manage documentation from the Docs Builder interface.', 'eazydocs' ),
			'options'  => [
				'administrator' => esc_html__( 'Administrator', 'eazydocs' ),
				'editor'        => esc_html__( 'Editor', 'eazydocs' ),
				'author'        => esc_html__( 'Author', 'eazydocs' ),
				'contributor'   => esc_html__( 'Contributor', 'eazydocs' ),
				'subscriber'    => esc_html__( 'Subscriber', 'eazydocs' ),
			],
			'chosen'   => true,
			'multiple' => true,
			'default'  => 'administrator',
			'class'    => 'eazydocs-pro-notice'
		),

		array(
			'id'       => 'settings-edit-access',
			'type'     => 'select',
			'title'    => esc_html__( 'Settings Managers', 'eazydocs' ),
			'subtitle' => esc_html__( 'Define which user roles have access to modify EazyDocs settings.', 'eazydocs' ),
			'options'  => [
				'manage_options' 	=> esc_html__( 'Administrator Only', 'eazydocs' ),
				'publish_pages'     => esc_html__( 'Administrator & Editor', 'eazydocs' ),
				'publish_posts'     => esc_html__( 'Administrator, Editor & Author', 'eazydocs' ),
			],
			'chosen'   => true,
			'default'  => 'manage_options',
			'multiple' => false,
			'class'    => 'eazydocs-pro-notice'
		),

		array(
			'id'       => 'analytics-access',
			'type'     => 'select',
			'title'    => esc_html__( 'Analytics Viewers', 'eazydocs' ),
			'subtitle' => esc_html__( 'Choose which user roles can view documentation analytics and performance metrics.', 'eazydocs' ),
			'options'  => [
				'administrator' => esc_html__( 'Administrator', 'eazydocs' ),
				'editor'        => esc_html__( 'Editor', 'eazydocs' ),
				'author'        => esc_html__( 'Author', 'eazydocs' ),
			],
			'chosen'   => true,
			'default'  => 'administrator',
			'multiple' => true,
			'class'    => 'eazydocs-promax-notice'
		)
	]
) );