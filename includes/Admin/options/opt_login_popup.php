<?php
/**
 * Login & Sign-up Popup customization.
 *
 * Lets admins tailor the copy and look of the frontend login/sign-up popup
 * (rendered by EazyDocs Pro) without touching code. Values are stored in the
 * shared `eazydocs_settings` option and read on the frontend with
 * eazydocspro_get_option().
 */
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

// New sign-ups depend on WordPress' membership setting; surface a hint when off.
$ezd_lp_can_register = (bool) get_option( 'users_can_register' );
$ezd_lp_reg_note     = $ezd_lp_can_register
	? ''
	: '<div style="display:flex;align-items:flex-start;gap:10px;padding:12px 16px;border-radius:8px;background:#fff4e5;border:1px solid #ffd8a8;color:#a15c07;margin-top:4px;">'
		. '<span class="dashicons dashicons-info-outline"></span>'
		. '<span>' . sprintf(
			/* translators: %s: Settings → General link. */
			esc_html__( 'The sign-up form only appears to visitors when new registrations are allowed. Enable %s to let people create accounts.', 'eazydocs' ),
			'<a href="' . esc_url( admin_url( 'options-general.php' ) ) . '" target="_blank"><strong>' . esc_html__( 'Settings → General → Anyone can register', 'eazydocs' ) . '</strong></a>'
		) . '</span></div>';

CSF::createSection( $prefix, array(
	'id'     => 'ezd_login_popup',
	'parent' => 'ezd_authentication',
	'title'  => esc_html__( 'Login & Sign-up Popup', 'eazydocs' ),
	'icon'   => 'dashicons dashicons-id-alt',
	'fields' => [

		array(
			'id'       => 'login_popup_heading',
			'type'     => 'heading',
			'title'    => esc_html__( 'Login & Sign-up Popup', 'eazydocs' ),
			'subtitle' => esc_html__( 'Customize the text and appearance of the login, password-reset and sign-up popup shown on gated documentation.', 'eazydocs' ),
		),

		array(
			'id'       => 'login_popup_logo',
			'type'     => 'media',
			'title'    => esc_html__( 'Popup Logo', 'eazydocs' ),
			'subtitle' => esc_html__( 'Optional image shown at the top of the popup. Leave empty to hide.', 'eazydocs' ),
			'library'  => 'image',
			'preview'  => true,
		),

		array(
			'id'       => 'login_popup_notice',
			'type'     => 'text',
			'title'    => esc_html__( 'Notice Text', 'eazydocs' ),
			'subtitle' => esc_html__( 'The message shown in the highlighted bar at the top of the popup.', 'eazydocs' ),
			'default'  => esc_html__( 'You must log in to continue.', 'eazydocs' ),
		),

		array(
			'id'          => 'login_popup_accent_color',
			'type'        => 'color',
			'title'       => esc_html__( 'Accent Color', 'eazydocs' ),
			'subtitle'    => esc_html__( 'Color used for the buttons and links inside the popup. Leave empty to use your primary brand color.', 'eazydocs' ),
			'default'     => '',
		),

		array(
			'id'       => 'login_popup_login_label',
			'type'     => 'text',
			'title'    => esc_html__( 'Log In Button Label', 'eazydocs' ),
			'default'  => esc_html__( 'Log In', 'eazydocs' ),
		),

		ezd_csf_switcher_field( array(
			'id'       => 'login_popup_show_forgot',
			'title'    => esc_html__( 'Forgot Password Link', 'eazydocs' ),
			'subtitle' => esc_html__( 'Show the in-popup "Forgotten account?" password-reset link.', 'eazydocs' ),
			'default'  => true,
		) ),

		ezd_csf_switcher_field( array(
			'id'       => 'login_popup_show_signup',
			'title'    => esc_html__( 'Sign-up Link', 'eazydocs' ),
			'subtitle' => esc_html__( 'Show the in-popup sign-up form so visitors can create an account.', 'eazydocs' ),
			'default'  => true,
		) ),

		array(
			'id'         => 'login_popup_signup_note',
			'type'       => 'content',
			'content'    => $ezd_lp_reg_note,
			'dependency' => array(
				[ 'login_popup_show_signup', '==', 'true' ],
			),
		),

		array(
			'id'         => 'login_popup_signup_label',
			'type'       => 'text',
			'title'      => esc_html__( 'Sign Up Button Label', 'eazydocs' ),
			'default'    => esc_html__( 'Sign Up', 'eazydocs' ),
			'dependency' => array(
				[ 'login_popup_show_signup', '==', 'true' ],
			),
		),
	],
) );
