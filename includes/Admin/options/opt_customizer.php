<?php
/**
 * Theme Customizer Integration
 * Control how EazyDocs integrates with the WordPress Customizer.
 */
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}


CSF::createSection( $prefix, array(
	'id'     => 'design_fields',
	'title'  => esc_html__( 'Theme Customizer', 'eazydocs' ),
	'icon'   => 'dashicons dashicons-admin-customizer',
	'fields' => [
		array(
			'id'         => 'customizer_visibility',
			'type'       => 'switcher',
			'title'      => esc_html__( 'Customizer Integration', 'eazydocs' ),
			'subtitle'   => esc_html__( 'Show EazyDocs settings in the WordPress Theme Customizer for live preview while editing.', 'eazydocs' ),
			'text_on'    => esc_html__( 'Enabled', 'eazydocs' ),
			'text_off'   => esc_html__( 'Disabled', 'eazydocs' ),
			'default'	 => true,
			'text_width' => 100
		),
		array(
			'type'     => 'callback',
			'function' => 'customizer_visibility_callback',
			'dependency' => array(
				array( 'customizer_visibility', '==', true ),
			)
		)
	]
) );