<?php
/**
 * Appearance Parent Section
 * Groups all visual/theming settings (Dark Mode, Theme Customizer,
 * One-Page Layout) under a single "Appearance" tab for clearer navigation.
 */
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

CSF::createSection( $prefix, array(
	'id'    => 'ezd_appearance',
	'title' => esc_html__( 'Appearance', 'eazydocs' ),
	'icon'  => 'dashicons dashicons-admin-appearance',
) );
