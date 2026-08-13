<?php
/**
 * API Documentation settings (Pro Max).
 * Configure defaults and menu visibility for API Docs.
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
							<p>' . esc_html__( 'Publish structured API reference docs with collections, endpoints, parameters, requests, responses, and code examples — separate from Standard Docs.', 'eazydocs' ) . '</p>
							<div class="ezd-settings-intro__features">
								<span><span class="dashicons dashicons-category"></span>' . esc_html__( 'Collections', 'eazydocs' ) . '</span>
								<span><span class="dashicons dashicons-rest-api"></span>' . esc_html__( 'Endpoints', 'eazydocs' ) . '</span>
								<span><span class="dashicons dashicons-editor-code"></span>' . esc_html__( 'Code examples', 'eazydocs' ) . '</span>
							</div>
						</div>
					</div>
				</div>
			',
		),

		array(
			'id'    => 'api_docs_defaults_heading',
			'type'  => 'heading',
			'title' => esc_html__( 'Defaults for New API Docs', 'eazydocs' ),
		),

		ezd_csf_switcher_field( array(
			'id'       => 'enable_api_docs_menu',
			'title'    => esc_html__( 'Show API Docs Menu', 'eazydocs' ),
			'subtitle' => esc_html__( 'Display All API Docs and Add New under the EasyDocs admin menu.', 'eazydocs' ),
			'default'  => true,
			'class'    => 'eazydocs-promax-notice',
		) ),

		array(
			'id'          => 'default_base_url',
			'type'        => 'text',
			'title'       => esc_html__( 'Default Base URL', 'eazydocs' ),
			'subtitle'    => esc_html__( 'Pre-filled when creating a new API Doc project.', 'eazydocs' ),
			'placeholder' => 'https://api.example.com/v1',
			'default'     => 'https://api.example.com/v1',
			'class'       => 'eazydocs-promax-notice',
		),

		array(
			'id'          => 'default_api_version',
			'type'        => 'text',
			'title'       => esc_html__( 'Default API Version', 'eazydocs' ),
			'subtitle'    => esc_html__( 'Pre-filled version label for new API Docs (e.g. v1).', 'eazydocs' ),
			'placeholder' => 'v1',
			'default'     => 'v1',
			'class'       => 'eazydocs-promax-notice',
		),

		array(
			'id'       => 'default_auth',
			'type'     => 'select',
			'title'    => esc_html__( 'Default Authentication', 'eazydocs' ),
			'subtitle' => esc_html__( 'Default auth type applied to new API Docs.', 'eazydocs' ),
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
	),
) );
