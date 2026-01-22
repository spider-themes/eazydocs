<?php
/**
 * Email & Notifications Settings Parent Section
 * Groups all email and notification-related settings together.
 */
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Create parent section for Email & Notification settings
 * This groups Update Notifications and Email Reports under a single section.
 */
CSF::createSection( $prefix, array(
	'id'    => 'email_settings',
	'title' => esc_html__( 'Email Settings', 'eazydocs' ),
	'icon'  => 'dashicons dashicons-email',
) );
