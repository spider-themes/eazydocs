<?php
/**
 * API Docs settings (Pro Max).
 * Configure defaults, menu visibility, and frontend display for API Docs.
 */
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

CSF::createSection( $prefix, array(
	'id'     => 'api_docs_fields',
	'title'  => esc_html__( 'API Docs', 'eazydocs' ),
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
							<h2>' . esc_html__( 'API Docs', 'eazydocs' ) . '</h2>
							<p>' . esc_html__( 'Defaults and display options for API Docs.', 'eazydocs' ) . '</p>
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
			'subtitle' => esc_html__( 'Show All API Docs and New API Doc in the EasyDocs menu.', 'eazydocs' ),
			'default'  => false,
			'class'    => 'eazydocs-promax-notice',
		) ),

		array(
			'id'    => 'api_docs_archive_heading',
			'type'  => 'heading',
			'title' => esc_html__( 'Frontend Archive', 'eazydocs' ),
		),

		ezd_csf_switcher_field( array(
			'id'       => 'enable_api_docs_archive',
			'title'    => esc_html__( 'API Docs Archive', 'eazydocs' ),
			'subtitle' => esc_html__( 'Enable the public archive at /api-docs/ that lists all published API Docs.', 'eazydocs' ),
			'default'  => true,
			'class'    => 'eazydocs-promax-notice',
		) ),

		array(
			'id'          => 'api_docs_archive_title',
			'type'        => 'text',
			'title'       => esc_html__( 'Archive Title', 'eazydocs' ),
			'subtitle'    => esc_html__( 'Heading shown on the frontend archive page.', 'eazydocs' ),
			'default'     => esc_html__( 'API Docs', 'eazydocs' ),
			'sanitize'    => 'sanitize_text_field',
			'class'       => 'eazydocs-promax-notice',
			'dependency'  => array( 'enable_api_docs_archive', '==', 'true' ),
		),

		array(
			'id'          => 'api_docs_archive_description',
			'type'        => 'textarea',
			'title'       => esc_html__( 'Archive Description', 'eazydocs' ),
			'subtitle'    => esc_html__( 'Short intro text under the archive title.', 'eazydocs' ),
			'default'     => esc_html__( 'Browse API references and developer documentation.', 'eazydocs' ),
			'sanitize'    => 'sanitize_textarea_field',
			'class'       => 'eazydocs-promax-notice',
			'dependency'  => array( 'enable_api_docs_archive', '==', 'true' ),
		),

		array(
			'id'    => 'api_docs_defaults_heading',
			'type'  => 'heading',
			'title' => esc_html__( 'New API Doc Defaults', 'eazydocs' ),
		),

		array(
			'id'          => 'default_base_url',
			'type'        => 'text',
			'title'       => esc_html__( 'Default Base URL', 'eazydocs' ),
			'subtitle'    => esc_html__( 'Pre-filled when creating a new API Doc.', 'eazydocs' ),
			'placeholder' => 'https://api.example.com/v1',
			'default'     => 'https://api.example.com/v1',
			'sanitize'    => 'esc_url_raw',
			'class'       => 'eazydocs-promax-notice',
		),

		array(
			'id'          => 'default_api_version',
			'type'        => 'text',
			'title'       => esc_html__( 'Default Version Label', 'eazydocs' ),
			'subtitle'    => esc_html__( 'Pre-filled when creating a new API Doc.', 'eazydocs' ),
			'placeholder' => 'v1',
			'default'     => 'v1',
			'sanitize'    => 'sanitize_text_field',
			'class'       => 'eazydocs-promax-notice',
		),

		array(
			'id'       => 'default_auth',
			'type'     => 'select',
			'title'    => esc_html__( 'Default Authentication Type', 'eazydocs' ),
			'subtitle' => esc_html__( 'Pre-filled when creating a new API Doc.', 'eazydocs' ),
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
			'id'    => 'api_docs_format_heading',
			'type'  => 'heading',
			'title' => esc_html__( 'Documentation Layout', 'eazydocs' ),
		),

		array(
			'id'       => 'default_api_display_format',
			'type'     => 'button_set',
			'title'    => esc_html__( 'Default Display Mode', 'eazydocs' ),
			'subtitle' => esc_html__( 'Multi-page uses separate URLs for collections and endpoints. One-page shows the full API on a single page.', 'eazydocs' ),
			'options'  => array(
				'multi'   => esc_html__( 'Multi-page', 'eazydocs' ),
				'onepage' => esc_html__( 'One-page', 'eazydocs' ),
			),
			'default'  => 'multi',
			'class'    => 'eazydocs-promax-notice',
		),

		array(
			'id'         => 'default_api_page_layout',
			'type'       => 'image_select',
			'title'      => esc_html__( 'Default Multi-page Sidebar Layout', 'eazydocs' ),
			'subtitle'   => esc_html__( 'Applies when Display Mode is Multi-page.', 'eazydocs' ),
			'options'    => array(
				'both_sidebar'  => EZD_IMG . 'customizer/both_sidebar.jpg',
				'left_sidebar'  => EZD_IMG . 'customizer/sidebar_left.jpg',
				'right_sidebar' => EZD_IMG . 'customizer/sidebar_right.jpg',
			),
			'default'    => 'both_sidebar',
			'class'      => 'eazydocs-promax-notice single-layout-img-wrap',
			'dependency' => array( 'default_api_display_format', '==', 'multi' ),
		),

		array(
			'id'         => 'default_api_onepage_layout',
			'type'       => 'image_select',
			'title'      => esc_html__( 'Default One-page Sidebar Layout', 'eazydocs' ),
			'subtitle'   => esc_html__( 'Applies when Display Mode is One-page.', 'eazydocs' ),
			'options'    => array(
				'classic-onepage-layout' => EZD_IMG . 'customizer/both_sidebar.jpg',
				'fullscreen-layout'      => EZD_IMG . 'customizer/sidebar_left.jpg',
			),
			'default'    => 'classic-onepage-layout',
			'class'      => 'eazydocs-promax-notice single-layout-img-wrap',
			'dependency' => array( 'default_api_display_format', '==', 'onepage' ),
		),

		array(
			'id'    => 'api_docs_display_heading',
			'type'  => 'heading',
			'title' => esc_html__( 'Display Options', 'eazydocs' ),
		),

		ezd_csf_switcher_field( array(
			'id'       => 'default_show_method_badges',
			'title'    => esc_html__( 'Show HTTP Method Badges', 'eazydocs' ),
			'default'  => true,
			'class'    => 'eazydocs-promax-notice',
		) ),

		ezd_csf_switcher_field( array(
			'id'       => 'default_show_try_it_placeholder',
			'title'    => esc_html__( 'Show Code Panel', 'eazydocs' ),
			'subtitle' => esc_html__( 'Shows sample code on the right. Does not send live API requests.', 'eazydocs' ),
			'default'  => true,
			'class'    => 'eazydocs-promax-notice',
		) ),

		array(
			'id'          => 'default_example_language',
			'type'        => 'text',
			'title'       => esc_html__( 'Default Code Language', 'eazydocs' ),
			'subtitle'    => esc_html__( 'Pre-filled on new API Docs. Use any language name, such as Python or Go.', 'eazydocs' ),
			'placeholder' => 'cURL',
			'default'     => 'cURL',
			'sanitize'    => 'sanitize_text_field',
			'class'       => 'eazydocs-promax-notice',
		),
	),
) );
