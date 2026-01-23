<?php
/**
 * Restricted Docs Settings
 * 
 * This file contains all settings related to Private and Password Protected documentation.
 * 
 * @package EazyDocs
 */

// Cannot access directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

// Get Pro status for conditional display
$is_pro    = ezd_is_premium();
//
// Restricted Docs Parent Section
//
CSF::createSection( $prefix, array(
	'id'    => 'restricted_docs',
	'title' => esc_html__( 'Restricted Docs', 'eazydocs' ),
	'icon'  => 'dashicons dashicons-privacy',
) );

//
// Private Doc Settings
//
CSF::createSection( $prefix, array(
	'id'     => 'private_doc_settings',
	'parent' => 'restricted_docs',
	'title'  => esc_html__( 'Private Doc', 'eazydocs' ),
	'icon'   => '',
	'fields' => array(

		// ═══════════════════════════════════════════════════════════════
		// INTRODUCTION SECTION
		// ═══════════════════════════════════════════════════════════════
		array(
			'id'      => 'private_doc_intro',
			'type'    => 'content',
			'content' => '
				<div class="ezd-settings-intro">
					<div class="ezd-settings-intro__inner">
						<div class="ezd-settings-intro__icon">
							<span class="dashicons dashicons-lock"></span>
						</div>
						<div class="ezd-settings-intro__content">
							<h2>' . esc_html__( 'Private Documentation', 'eazydocs' ) . '</h2>
							<p>' . esc_html__( 'Protect your sensitive documentation by requiring users to log in before viewing. Perfect for internal knowledge bases, member-only content, customer documentation, and confidential guides.', 'eazydocs' ) . '</p>
							<div class="ezd-settings-intro__features">
								<span><span class="dashicons dashicons-yes-alt"></span>' . esc_html__( 'Login Required', 'eazydocs' ) . '</span>
								<span><span class="dashicons dashicons-yes-alt"></span>' . esc_html__( 'Role-Based Access', 'eazydocs' ) . '</span>
								<span><span class="dashicons dashicons-yes-alt"></span>' . esc_html__( 'Custom Redirects', 'eazydocs' ) . '</span>
								<span><span class="dashicons dashicons-yes-alt"></span>' . esc_html__( 'Per-Doc Control', 'eazydocs' ) . '</span>
							</div>
						</div>
					</div>
				</div>
			',
		),

		// ═══════════════════════════════════════════════════════════════
		// HOW TO USE - QUICK START GUIDE
		// ═══════════════════════════════════════════════════════════════
		array(
			'id'      => 'private_doc_howto',
			'type'    => 'content',
			'content' => '
				<div class="ezd-settings-quickstart">
					<h3 class="ezd-settings-quickstart__title">
						<span class="dashicons dashicons-lightbulb"></span>
						' . esc_html__( 'Quick Start Guide', 'eazydocs' ) . '
					</h3>
					<div class="ezd-settings-quickstart__steps">
						<div class="ezd-settings-quickstart__step">
							<div class="ezd-settings-quickstart__step-label">' . esc_html__( 'STEP 1', 'eazydocs' ) . '</div>
							<div class="ezd-settings-quickstart__step-text">' . esc_html__( 'Configure settings below to define how private docs behave.', 'eazydocs' ) . '</div>
						</div>
						<div class="ezd-settings-quickstart__step ezd-settings-quickstart__step--step-2">
							<div class="ezd-settings-quickstart__step-label">' . esc_html__( 'STEP 2', 'eazydocs' ) . '</div>
							<div class="ezd-settings-quickstart__step-text">' . esc_html__( 'Go to Docs Builder and click the Visibility icon on any doc.', 'eazydocs' ) . '</div>
						</div>
						<div class="ezd-settings-quickstart__step ezd-settings-quickstart__step--step-3">
							<div class="ezd-settings-quickstart__step-label">' . esc_html__( 'STEP 3', 'eazydocs' ) . '</div>
							<div class="ezd-settings-quickstart__step-text">' . esc_html__( 'Select "Private" and optionally set role restrictions.', 'eazydocs' ) . '</div>
						</div>
					</div>
				</div>
			',
		),


		// ═══════════════════════════════════════════════════════════════
		// BASIC SETTINGS HEADING
		// ═══════════════════════════════════════════════════════════════
		array(
			'id'    => 'private_doc_basic_heading',
			'type'  => 'heading',
			'title' => esc_html__( 'Basic Settings', 'eazydocs' ),
		),

		// Unauthorized Access Behavior
		array(
			'id'       => 'private_doc_mode',
			'type'     => 'button_set',
			'title'    => esc_html__( 'Unauthorized Access Behavior', 'eazydocs' ),
			'subtitle' => esc_html__( 'What happens when a non-logged-in user tries to access a private doc?', 'eazydocs' ),
			'desc'     => esc_html__( 'Choose how to handle unauthorized visitors. Redirecting to a login page provides a better user experience than showing an error.', 'eazydocs' ),
			'options'  => array(
				'login' => esc_html__( 'Redirect to Login Page', 'eazydocs' ),
				'none'  => esc_html__( 'Show 404 Error', 'eazydocs' ),
			),
			'default'  => 'login',
			'class'    => 'eazydocs-pro-notice',
		),

		// Login Page Selection
		array(
			'id'          => 'private_doc_login_page',
			'type'        => 'select',
			'title'       => esc_html__( 'Login Page', 'eazydocs' ),
			'subtitle'    => esc_html__( 'Select the page where users will be redirected to log in.', 'eazydocs' ),
			'desc'        => sprintf(
				/* translators: %s: shortcode */
				esc_html__( 'Use the %s shortcode to display the login form on any page.', 'eazydocs' ),
				'<code>[ezd_login_form]</code>'
			),
			'placeholder' => esc_html__( 'Select a page...', 'eazydocs' ),
			'options'     => 'pages',
			'class'       => 'eazydocs-pro-notice',
			'dependency'  => array(
				array( 'private_doc_mode', '==', 'login' ),
			),
			'query_args'  => array(
				'posts_per_page' => -1,
			),
			'chosen'      => true,
			'ajax'        => true,
		),

		// Redirect After Login
		array(
			'id'       => 'private_doc_redirect_back',
			'type'     => 'switcher',
			'title'    => esc_html__( 'Redirect Back After Login', 'eazydocs' ),
			'subtitle' => esc_html__( 'After logging in, take users back to the doc they were trying to view.', 'eazydocs' ),
			'desc'     => esc_html__( 'Provides a seamless experience by remembering the original destination.', 'eazydocs' ),
			'default'  => true,
			'text_on'  => esc_html__( 'Yes', 'eazydocs' ),
			'text_off' => esc_html__( 'No', 'eazydocs' ),
			'class'    => 'eazydocs-pro-notice',
			'dependency' => array(
				array( 'private_doc_mode', '==', 'login' ),
			),
		),

		// ═══════════════════════════════════════════════════════════════
		// ACCESS CONTROL HEADING
		// ═══════════════════════════════════════════════════════════════
		array(
			'id'         => 'private_doc_access_heading',
			'type'       => 'heading',
			'title'      => esc_html__( 'Access Control', 'eazydocs' ),
			'dependency' => array(
				array( 'private_doc_mode', '==', 'login' ),
			),
		),

		// Info about access control
		array(
			'id'         => 'private_doc_access_info',
			'type'       => 'content',
			'content'    => '
				<div class="ezd-settings-info ezd-settings-info--warning">
					<span class="dashicons dashicons-info"></span>
					<div>
						<strong>' . esc_html__( 'Global vs Per-Doc Control', 'eazydocs' ) . '</strong>
						<p>' . esc_html__( 'Settings here apply to ALL private docs as a default. You can override these on individual docs using the Visibility popup in the Docs Builder.', 'eazydocs' ) . '</p>
					</div>
				</div>
			',
			'dependency' => array(
				array( 'private_doc_mode', '==', 'login' ),
			),
		),

		// Who Can Access
		array(
			'id'         => 'private_doc_access_type',
			'type'       => 'radio',
			'title'      => esc_html__( 'Who Can Access Private Docs?', 'eazydocs' ),
			'subtitle'   => esc_html__( 'Define the default access rule for all private documentation.', 'eazydocs' ),
			'options'    => array(
				'all_users'      => esc_html__( 'All Logged-In Users — Anyone with an account can view', 'eazydocs' ),
				'specific_roles' => esc_html__( 'Specific User Roles — Only selected roles can view', 'eazydocs' ),
			),
			'default'    => 'all_users',
			'class'      => 'eazydocs-pro-notice',
			'dependency' => array(
				array( 'private_doc_mode', '==', 'login' ),
			),
		),

		// Role Selection
		array(
			'id'          => 'private_doc_allowed_roles',
			'type'        => 'select',
			'title'       => esc_html__( 'Allowed User Roles', 'eazydocs' ),
			'subtitle'    => esc_html__( 'Select which user roles can access private docs by default.', 'eazydocs' ),
			'desc'        => esc_html__( 'Users with these roles will be able to view private documentation. Administrators always have access.', 'eazydocs' ),
			'placeholder' => esc_html__( 'Select roles...', 'eazydocs' ),
			'options'     => function_exists( 'eazydocs_user_role_names' ) && $is_pro ? eazydocs_user_role_names() : array(
				'administrator' => esc_html__( 'Administrator', 'eazydocs' ),
				'editor'        => esc_html__( 'Editor', 'eazydocs' ),
				'author'        => esc_html__( 'Author', 'eazydocs' ),
				'contributor'   => esc_html__( 'Contributor', 'eazydocs' ),
				'subscriber'    => esc_html__( 'Subscriber', 'eazydocs' ),
			),
			'default'     => array( 'administrator', 'editor' ),
			'chosen'      => true,
			'ajax'        => true,
			'multiple'    => true,
			'class'       => 'eazydocs-pro-notice',
			'dependency'  => array(
				array( 'private_doc_mode', '==', 'login' ),
				array( 'private_doc_access_type', '==', 'specific_roles' ),
			),
		),

		// ═══════════════════════════════════════════════════════════════
		// PER-DOC ROLE VISIBILITY (PRO MAX)
		// ═══════════════════════════════════════════════════════════════
		array(
			'id'         => 'private_doc_perdoc_heading',
			'type'       => 'heading',
			'title'      => esc_html__( 'Per-Doc Role Visibility', 'eazydocs' ),
			'dependency' => array(
				array( 'private_doc_mode', '==', 'login' ),
			),
		),

		// Pro Max Feature Description
		array(
			'id'         => 'private_doc_perdoc_info',
			'type'       => 'content',
			'content'    => '
				<div class="ezd-settings-info ezd-settings-info--pro">
					<h4>
						<span class="dashicons dashicons-superhero-alt"></span>
						' . esc_html__( 'Advanced Per-Doc Control', 'eazydocs' ) . '
					</h4>
					<p>' . esc_html__( 'Set unique role requirements for individual docs. Perfect when different docs need different access levels — like restricting HR docs to HR roles, or limiting API docs to developers.', 'eazydocs' ) . '</p>
					<div class="ezd-settings-info__example">
						<strong>' . esc_html__( 'Example:', 'eazydocs' ) . '</strong> ' . esc_html__( 'Your global setting allows all logged-in users. But for your "Admin Guides" doc, you set it to only allow Administrators. Only Admins can see that specific doc.', 'eazydocs' ) . '
					</div>
				</div>
			',
			'dependency' => array(
				array( 'private_doc_mode', '==', 'login' ),
			),
		),

		// Enable Per-Doc Role Control
		array(
			'id'         => 'role_visibility_enable',
			'type'       => 'switcher',
			'title'      => esc_html__( 'Enable Per-Doc Role Control', 'eazydocs' ),
			'subtitle'   => esc_html__( 'Allow setting different role requirements on individual private docs.', 'eazydocs' ),
			'desc'       => esc_html__( 'When enabled, the Visibility popup will show role selection options for private docs.', 'eazydocs' ),
			'default'    => true,
			'text_on'    => esc_html__( 'Enabled', 'eazydocs' ),
			'text_off'   => esc_html__( 'Disabled', 'eazydocs' ),
			'text_width' => 90,
			'class'      => 'eazydocs-pro-notice',
			'dependency' => array(
				array( 'private_doc_mode', '==', 'login' ),
			),
		),

		// Access Denied Behavior for Role Mismatch
		array(
			'id'         => 'role_visibility_redirect_mode',
			'type'       => 'radio',
			'title'      => esc_html__( 'Role Mismatch Behavior', 'eazydocs' ),
			'subtitle'   => esc_html__( 'What happens when a logged-in user lacks the required role for a specific doc?', 'eazydocs' ),
			'desc'       => esc_html__( 'This only applies when a doc has specific role requirements that the user doesn\'t meet.', 'eazydocs' ),
			'options'    => array(
				'login'  => esc_html__( 'Redirect to Login Page — User can login with a different account', 'eazydocs' ),
				'custom' => esc_html__( 'Redirect to Custom Page — Show an "Access Denied" page', 'eazydocs' ),
				'404'    => esc_html__( 'Show 404 Error — Pretend the doc doesn\'t exist', 'eazydocs' ),
			),
			'default'    => 'login',
			'class'      => 'eazydocs-pro-notice',
			'dependency' => array(
				array( 'private_doc_mode', '==', 'login' ),
				array( 'role_visibility_enable', '==', 'true' ),
			),
		),

		// Custom Access Denied Page
		array(
			'id'          => 'role_visibility_custom_page',
			'type'        => 'select',
			'title'       => esc_html__( 'Access Denied Page', 'eazydocs' ),
			'subtitle'    => esc_html__( 'Select a custom page to show when access is denied.', 'eazydocs' ),
			'desc'        => esc_html__( 'Create a page with a friendly message explaining why access was denied and what to do next.', 'eazydocs' ),
			'placeholder' => esc_html__( 'Select a page...', 'eazydocs' ),
			'options'     => 'pages',
			'chosen'      => true,
			'ajax'        => true,
			'query_args'  => array(
				'posts_per_page' => -1,
			),
			'class'       => 'eazydocs-pro-notice',
			'dependency'  => array(
				array( 'private_doc_mode', '==', 'login' ),
				array( 'role_visibility_enable', '==', 'true' ),
				array( 'role_visibility_redirect_mode', '==', 'custom' ),
			),
		),

		// Inherit from Parent
		array(
			'id'         => 'role_visibility_inherit_default',
			'type'       => 'switcher',
			'title'      => esc_html__( 'Child Docs Inherit Parent Roles', 'eazydocs' ),
			'subtitle'   => esc_html__( 'Automatically apply parent doc\'s role restrictions to child docs.', 'eazydocs' ),
			'desc'       => esc_html__( 'When enabled, you only need to set roles on the parent doc — all child docs will inherit those restrictions.', 'eazydocs' ),
			'default'    => true,
			'text_on'    => esc_html__( 'Yes', 'eazydocs' ),
			'text_off'   => esc_html__( 'No', 'eazydocs' ),
			'class'      => 'eazydocs-pro-notice',
			'dependency' => array(
				array( 'private_doc_mode', '==', 'login' ),
				array( 'role_visibility_enable', '==', 'true' ),
			),
		),

		// ═══════════════════════════════════════════════════════════════
		// DISPLAY & NAVIGATION SETTINGS
		// ═══════════════════════════════════════════════════════════════
		array(
			'id'         => 'private_doc_display_heading',
			'type'       => 'heading',
			'title'      => esc_html__( 'Display & Navigation', 'eazydocs' ),
			'dependency' => array(
				array( 'private_doc_mode', '==', 'login' ),
			),
		),

		// Show Lock Icon
		array(
			'id'         => 'role_visibility_show_lock_icon',
			'type'       => 'switcher',
			'title'      => esc_html__( 'Show Lock Icon', 'eazydocs' ),
			'subtitle'   => esc_html__( 'Display a lock icon next to private docs in the sidebar navigation.', 'eazydocs' ),
			'desc'       => esc_html__( 'Helps users identify which docs require login at a glance.', 'eazydocs' ),
			'default'    => true,
			'text_on'    => esc_html__( 'Show', 'eazydocs' ),
			'text_off'   => esc_html__( 'Hide', 'eazydocs' ),
			'text_width' => 75,
			'class'      => 'eazydocs-pro-notice',
			'dependency' => array(
				array( 'private_doc_mode', '==', 'login' ),
			),
		),

		// Hide from Navigation for Unauthorized
		array(
			'id'         => 'role_visibility_hide_from_nav',
			'type'       => 'switcher',
			'title'      => esc_html__( 'Hide Restricted Docs from Navigation', 'eazydocs' ),
			'subtitle'   => esc_html__( 'Completely hide private docs from navigation for users who can\'t access them.', 'eazydocs' ),
			'desc'       => esc_html__( 'When disabled, private docs appear with a lock icon but are not clickable for unauthorized users.', 'eazydocs' ),
			'default'    => false,
			'text_on'    => esc_html__( 'Hide', 'eazydocs' ),
			'text_off'   => esc_html__( 'Show', 'eazydocs' ),
			'text_width' => 75,
			'class'      => 'eazydocs-pro-notice',
			'dependency' => array(
				array( 'private_doc_mode', '==', 'login' ),
			),
		),

		// Exclude from Search
		array(
			'id'         => 'role_visibility_exclude_search',
			'type'       => 'switcher',
			'title'      => esc_html__( 'Exclude from Search Results', 'eazydocs' ),
			'subtitle'   => esc_html__( 'Hide private docs from search results for unauthorized users.', 'eazydocs' ),
			'desc'       => esc_html__( 'Prevents users from finding private docs through the search feature.', 'eazydocs' ),
			'default'    => true,
			'text_on'    => esc_html__( 'Yes', 'eazydocs' ),
			'text_off'   => esc_html__( 'No', 'eazydocs' ),
			'class'      => 'eazydocs-pro-notice',
			'dependency' => array(
				array( 'private_doc_mode', '==', 'login' ),
			),
		),

		// ═══════════════════════════════════════════════════════════════
		// MESSAGES & CUSTOMIZATION
		// ═══════════════════════════════════════════════════════════════
		array(
			'id'         => 'private_doc_messages_heading',
			'type'       => 'heading',
			'title'      => esc_html__( 'Messages & Customization', 'eazydocs' ),
			'dependency' => array(
				array( 'private_doc_mode', '==', 'login' ),
			),
		),

		// Access Denied Message
		array(
			'id'         => 'role_visibility_denied_message',
			'type'       => 'textarea',
			'title'      => esc_html__( 'Access Denied Message', 'eazydocs' ),
			'subtitle'   => esc_html__( 'Message shown to users who don\'t have access to a private doc.', 'eazydocs' ),
			'desc'       => esc_html__( 'This message appears when a user tries to access a doc they\'re not authorized to view.', 'eazydocs' ),
			'default'    => esc_html__( 'This documentation is private. Please log in with an authorized account to view this content.', 'eazydocs' ),
			'class'      => 'eazydocs-pro-notice',
			'dependency' => array(
				array( 'private_doc_mode', '==', 'login' ),
			),
		),

		// Login Prompt Message
		array(
			'id'         => 'private_doc_login_prompt',
			'type'       => 'text',
			'title'      => esc_html__( 'Login Prompt Text', 'eazydocs' ),
			'subtitle'   => esc_html__( 'Text shown on the login button or link.', 'eazydocs' ),
			'default'    => esc_html__( 'Log In to View', 'eazydocs' ),
			'class'      => 'eazydocs-pro-notice',
			'dependency' => array(
				array( 'private_doc_mode', '==', 'login' ),
			),
		),

	),
) );

//
// Password Protected Doc Settings
//
CSF::createSection( $prefix, array(
	'id'     => 'protected_doc_settings',
	'parent' => 'restricted_docs',
	'title'  => esc_html__( 'Password Protected', 'eazydocs' ),
	'icon'   => '',
	'fields' => array(

		// ═══════════════════════════════════════════════════════════════
		// INTRODUCTION
		// ═══════════════════════════════════════════════════════════════
		array(
			'id'      => 'protected_doc_intro',
			'type'    => 'content',
			'content' => '
				<div class="ezd-settings-intro ezd-settings-intro--green">
					<div class="ezd-settings-intro__inner">
						<div class="ezd-settings-intro__icon">
							<span class="dashicons dashicons-admin-network"></span>
						</div>
						<div class="ezd-settings-intro__content">
							<h2>' . esc_html__( 'Password Protected Documentation', 'eazydocs' ) . '</h2>
							<p>' . esc_html__( 'Protect specific docs with a password. Perfect for sharing sensitive documentation with external parties without requiring them to create an account.', 'eazydocs' ) . '</p>
						</div>
					</div>
				</div>
			',
		),

		// ═══════════════════════════════════════════════════════════════
		// PASSWORD FORM SETTINGS
		// ═══════════════════════════════════════════════════════════════
		array(
			'id'    => 'protected_doc_heading',
			'type'  => 'heading',
			'title' => esc_html__( 'Password Form Settings', 'eazydocs' ),
		),

		// Form Style
		array(
			'id'       => 'protected_doc_form',
			'type'     => 'button_set',
			'title'    => esc_html__( 'Password Form Style', 'eazydocs' ),
			'subtitle' => esc_html__( 'Choose the style for the password entry form.', 'eazydocs' ),
			'options'  => array(
				'eazydocs-form' => esc_html__( 'EazyDocs Custom Form', 'eazydocs' ),
				'default'       => esc_html__( 'WordPress Default', 'eazydocs' ),
			),
			'default'  => 'eazydocs-form',
		),

		// ═══════════════════════════════════════════════════════════════
		// FORM CUSTOMIZATION (EazyDocs Form)
		// ═══════════════════════════════════════════════════════════════
		array(
			'id'         => 'protected_form_customize_heading',
			'type'       => 'subheading',
			'title'      => esc_html__( 'Form Customization', 'eazydocs' ),
			'dependency' => array(
				array( 'protected_doc_form', '==', 'eazydocs-form' ),
			),
		),

		// Header Color
		array(
			'id'          => 'protected_form_head_color',
			'type'        => 'color',
			'title'       => esc_html__( 'Header Background', 'eazydocs' ),
			'subtitle'    => esc_html__( 'Background color of the form header.', 'eazydocs' ),
			'default'     => '#0c213a',
			'dependency'  => array(
				array( 'protected_doc_form', '==', 'eazydocs-form' ),
			),
			'output'      => '.ezd-password-wrap .ezd-password-head',
			'output_mode' => 'background-color',
		),

		// Title Text
		array(
			'id'         => 'protected_form_title',
			'type'       => 'text',
			'title'      => esc_html__( 'Form Title', 'eazydocs' ),
			'subtitle'   => esc_html__( 'Main heading text on the password form.', 'eazydocs' ),
			'default'    => esc_html__( 'This document is password protected', 'eazydocs' ),
			'dependency' => array(
				array( 'protected_doc_form', '==', 'eazydocs-form' ),
			),
		),

		// Title Color
		array(
			'id'          => 'protected_form_title_color',
			'type'        => 'color',
			'title'       => esc_html__( 'Title Color', 'eazydocs' ),
			'default'     => '#ffffff',
			'dependency'  => array(
				array( 'protected_doc_form', '==', 'eazydocs-form' ),
			),
			'output'      => '.ezd-password-wrap .ezd-password-head p.ezd-password-title',
			'output_mode' => 'color',
		),

		// Subtitle Text
		array(
			'id'         => 'protected_form_subtitle',
			'type'       => 'text',
			'title'      => esc_html__( 'Form Subtitle', 'eazydocs' ),
			'subtitle'   => esc_html__( 'Helper text shown below the title.', 'eazydocs' ),
			'default'    => esc_html__( 'Please enter the password to view this document.', 'eazydocs' ),
			'dependency' => array(
				array( 'protected_doc_form', '==', 'eazydocs-form' ),
			),
		),

		// Subtitle Color
		array(
			'id'          => 'protected_form_subtitle_color',
			'type'        => 'color',
			'title'       => esc_html__( 'Subtitle Color', 'eazydocs' ),
			'default'     => '#a4abc5',
			'dependency'  => array(
				array( 'protected_doc_form', '==', 'eazydocs-form' ),
			),
			'output'      => '.ezd-password-wrap .ezd-password-head p.ezd-password-subtitle',
			'output_mode' => 'color',
		),

		// Button Settings Subheading
		array(
			'id'         => 'protected_form_button_heading',
			'type'       => 'subheading',
			'title'      => esc_html__( 'Button Styling', 'eazydocs' ),
			'dependency' => array(
				array( 'protected_doc_form', '==', 'eazydocs-form' ),
			),
		),

		// Button Text
		array(
			'id'         => 'protected_form_btn',
			'type'       => 'text',
			'title'      => esc_html__( 'Button Text', 'eazydocs' ),
			'default'    => esc_html__( 'Unlock Document', 'eazydocs' ),
			'dependency' => array(
				array( 'protected_doc_form', '==', 'eazydocs-form' ),
			),
		),

		// Button Text Color
		array(
			'id'          => 'protected_form_btn_bgcolor',
			'type'        => 'color',
			'title'       => esc_html__( 'Button Text Color', 'eazydocs' ),
			'default'     => '#ffffff',
			'dependency'  => array(
				array( 'protected_doc_form', '==', 'eazydocs-form' ),
			),
			'output'      => '.ezd-password-wrap .ezd-password-body form button',
			'output_mode' => 'color',
		),

		// Button Background Color
		array(
			'id'          => 'protected_form_btn_textcolor',
			'type'        => 'color',
			'title'       => esc_html__( 'Button Background Color', 'eazydocs' ),
			'default'     => '#5b86e5',
			'dependency'  => array(
				array( 'protected_doc_form', '==', 'eazydocs-form' ),
			),
			'output'      => '.ezd-password-wrap .ezd-password-body form button',
			'output_mode' => 'background-color',
		),

	),
) );