<?php
/**
 * API Documentation settings (Pro Max).
 * Configure defaults, menu visibility, and frontend display for API Docs.
 */
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

CSF::createSection( $prefix, array(
	'id'     => 'api_docs_fields',
	'title'  => esc_html__( 'API Documentation', 'eazydocs' ),
	'icon'   => 'dashicons dashicons-rest-api',
	'fields' => array(

		array(
			'id'      => 'api_docs_intro',
			'type'    => 'content',
			'content' => '
				<div class="ezd-settings-intro">
					<div class="ezd-settings-intro__inner">
						<div class="ezd-settings-intro__icon">
							<span class="dashicons dashicons-rest-api"></span>
						</div>
						<div class="ezd-settings-intro__content">
							<h2>' . esc_html__( 'API Documentation', 'eazydocs' ) . '</h2>
							<p>' . esc_html__( 'Create a dedicated API reference with collections, endpoints, and copy-ready code examples.', 'eazydocs' ) . '</p>
						</div>
					</div>
				</div>
			',
		),

		array(
			'id'    => 'api_docs_menu_heading',
			'type'  => 'heading',
			'title' => esc_html__( 'Admin Menu', 'eazydocs' ),
		),

		ezd_csf_switcher_field( array(
			'id'       => 'enable_api_docs_menu',
			'title'    => esc_html__( 'API Docs Menu', 'eazydocs' ),
			'subtitle' => esc_html__( 'Add All API Docs and Add New under the EasyDocs menu. Hiding the menu does not delete existing API Docs.', 'eazydocs' ),
			'default'  => false,
			'class'    => 'eazydocs-promax-notice',
		) ),

		array(
			'id'    => 'api_docs_defaults_heading',
			'type'  => 'heading',
			'title' => esc_html__( 'New API Doc Defaults', 'eazydocs' ),
		),

		array(
			'id'          => 'default_base_url',
			'type'        => 'text',
			'title'       => esc_html__( 'Base URL', 'eazydocs' ),
			'subtitle'    => esc_html__( 'Pre-filled as the API root on new docs. Endpoint paths are appended to this URL.', 'eazydocs' ),
			'placeholder' => 'https://api.example.com/v1',
			'default'     => 'https://api.example.com/v1',
			'sanitize'    => 'esc_url_raw',
			'class'       => 'eazydocs-promax-notice',
		),

		array(
			'id'          => 'default_api_version',
			'type'        => 'text',
			'title'       => esc_html__( 'Version', 'eazydocs' ),
			'subtitle'    => esc_html__( 'Pre-filled version label on new docs, such as v1. Display only — it does not change URLs.', 'eazydocs' ),
			'placeholder' => 'v1',
			'default'     => 'v1',
			'sanitize'    => 'sanitize_text_field',
			'class'       => 'eazydocs-promax-notice',
		),

		array(
			'id'       => 'default_auth',
			'type'     => 'select',
			'title'    => esc_html__( 'Authentication', 'eazydocs' ),
			'subtitle' => esc_html__( 'Auth type selected on new docs. You can explain how to send credentials on each API Doc.', 'eazydocs' ),
			'options'  => array(
				'none'    => esc_html__( 'None', 'eazydocs' ),
				'api_key' => esc_html__( 'API Key', 'eazydocs' ),
				'bearer'  => esc_html__( 'Bearer Token', 'eazydocs' ),
				'basic'   => esc_html__( 'Basic Auth', 'eazydocs' ),
				'oauth2'  => esc_html__( 'OAuth 2.0', 'eazydocs' ),
				'custom'  => esc_html__( 'Custom', 'eazydocs' ),
			),
			'default'  => 'none',
			'chosen'   => true,
			'class'    => 'eazydocs-promax-notice',
		),

		array(
			'id'    => 'api_docs_display_heading',
			'type'  => 'heading',
			'title' => esc_html__( 'Display', 'eazydocs' ),
		),

		ezd_csf_switcher_field( array(
			'id'       => 'default_show_method_badges',
			'title'    => esc_html__( 'Method Badges', 'eazydocs' ),
			'subtitle' => esc_html__( 'Show GET, POST, and other method badges in the left navigation and beside each path.', 'eazydocs' ),
			'default'  => true,
			'class'    => 'eazydocs-promax-notice',
		) ),

		ezd_csf_switcher_field( array(
			'id'       => 'default_show_try_it_placeholder',
			'title'    => esc_html__( 'Implementation Panel', 'eazydocs' ),
			'subtitle' => esc_html__( 'Show the right-hand code panel. This is documentation only and does not call the live API.', 'eazydocs' ),
			'default'  => true,
			'class'    => 'eazydocs-promax-notice',
		) ),

		array(
			'id'       => 'default_example_language',
			'type'     => 'select',
			'title'    => esc_html__( 'Default Language', 'eazydocs' ),
			'subtitle' => esc_html__( 'Language tab selected first. Visitors can still switch between cURL, JavaScript, and PHP.', 'eazydocs' ),
			'options'  => array(
				'curl'       => 'cURL',
				'javascript' => 'JavaScript',
				'php'        => 'PHP',
			),
			'default'  => 'curl',
			'chosen'   => true,
			'class'    => 'eazydocs-promax-notice',
		),
	),
) );
