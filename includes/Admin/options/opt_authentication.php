<?php
/**
 * Authentication Parent Section
 * Groups the sign-in related settings (Login & Sign-up Popup, Google Sign-In)
 * under a single "Authentication" tab for clearer navigation.
 */
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

CSF::createSection( $prefix, array(
	'id'    => 'ezd_authentication',
	'title' => esc_html__( 'Authentication', 'eazydocs' ),
	'icon'  => 'dashicons dashicons-admin-users',
) );
