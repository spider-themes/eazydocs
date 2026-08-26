<?php
/**
 * API Docs settings (Pro Max).
 * Parent + Archive / Single child tabs (same pattern as Single Doc Page).
 *
 * Placement rules:
 * - Archive tab  → site-wide only (no per-doc override).
 * - Single tab   → site defaults (empty meta Base URL / Version fall back here) + site-wide display options.
 * - Doc metabox  → per-doc content; Display Mode can override the site default.
 */
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

// Parent
CSF::createSection( $prefix, array(
	'id'    => 'api_docs',
	'title' => esc_html__( 'API Docs', 'eazydocs' ),
	'icon'  => 'dashicons dashicons-rest-api',
) );

// Archive — site-wide only
CSF::createSection( $prefix, array(
	'id'     => 'api_docs_archive',
	'parent' => 'api_docs',
	'title'  => esc_html__( 'Archive', 'eazydocs' ),
	'icon'   => '',
	'fields' => array(

		array(
			'id'      => 'api_docs_archive_intro',
			'type'    => 'content',
			'content' => '
				<div class="ezd-settings-intro">
					<div class="ezd-settings-intro__inner">
						<div class="ezd-settings-intro__icon">
							<span class="dashicons dashicons-portfolio"></span>
						</div>
						<div class="ezd-settings-intro__content">
							<h2>' . esc_html__( 'API Docs Archive', 'eazydocs' ) . '</h2>
							<p>' . esc_html__( 'Site-wide controls for the public API Docs archive (usually /api-docs/). These are not set per API Doc.', 'eazydocs' ) . '</p>
						</div>
					</div>
				</div>
			',
		),

		ezd_csf_switcher_field( array(
			'id'       => 'enable_api_docs_archive',
			'title'    => esc_html__( 'Enable Archive', 'eazydocs' ),
			'subtitle' => esc_html__( 'Public listing of all published API Docs (usually /api-docs/).', 'eazydocs' ),
			'default'  => true,
			'class'    => 'eazydocs-promax-notice',
		) ),

		array(
			'id'         => 'api_docs_archive_title',
			'type'       => 'text',
			'title'      => esc_html__( 'Title', 'eazydocs' ),
			'subtitle'   => esc_html__( 'Main heading on the archive page.', 'eazydocs' ),
			'default'    => esc_html__( 'API Docs', 'eazydocs' ),
			'sanitize'   => 'sanitize_text_field',
			'class'      => 'eazydocs-promax-notice',
			'dependency' => array( 'enable_api_docs_archive', '==', 'true' ),
		),

		array(
			'id'         => 'api_docs_archive_description',
			'type'       => 'textarea',
			'title'      => esc_html__( 'Description', 'eazydocs' ),
			'subtitle'   => esc_html__( 'Short intro under the title.', 'eazydocs' ),
			'default'    => esc_html__( 'Browse API references and developer documentation.', 'eazydocs' ),
			'sanitize'   => 'sanitize_textarea_field',
			'class'      => 'eazydocs-promax-notice',
			'dependency' => array( 'enable_api_docs_archive', '==', 'true' ),
		),

		array(
			'id'         => 'api_docs_archive_columns',
			'type'       => 'button_set',
			'title'      => esc_html__( 'Columns', 'eazydocs' ),
			'subtitle'   => esc_html__( 'How many API Doc cards to show in each row.', 'eazydocs' ),
			'options'    => array(
				'2' => esc_html__( '2 Columns', 'eazydocs' ),
				'3' => esc_html__( '3 Columns', 'eazydocs' ),
				'4' => esc_html__( '4 Columns', 'eazydocs' ),
			),
			'default'    => '3',
			'class'      => 'eazydocs-promax-notice',
			'dependency' => array( 'enable_api_docs_archive', '==', 'true' ),
		),
	),
) );

// Single — editor placeholders + display defaults
CSF::createSection( $prefix, array(
	'id'     => 'api_docs_single',
	'parent' => 'api_docs',
	'title'  => esc_html__( 'Single', 'eazydocs' ),
	'icon'   => '',
	'fields' => array(

		array(
			'id'      => 'api_docs_single_intro',
			'type'    => 'content',
			'content' => '
				<div class="ezd-settings-intro">
					<div class="ezd-settings-intro__inner">
						<div class="ezd-settings-intro__icon">
							<span class="dashicons dashicons-media-code"></span>
						</div>
						<div class="ezd-settings-intro__content">
							<h2>' . esc_html__( 'Single API Doc', 'eazydocs' ) . '</h2>
							<p>' . esc_html__( 'Site-wide Display options for every API Doc. Display Mode can also be overridden per doc. Base URL and Version apply on the frontend when a doc leaves those fields empty.', 'eazydocs' ) . '</p>
						</div>
					</div>
				</div>
			',
		),

		array(
			'type'    => 'subheading',
			'content' => esc_html__( 'Defaults', 'eazydocs' ),
		),

		array(
			'id'          => 'default_base_url',
			'type'        => 'text',
			'title'       => esc_html__( 'Base URL', 'eazydocs' ),
			'subtitle'    => esc_html__( 'Used on the frontend when a doc’s Base URL is empty. Also shown as the editor placeholder.', 'eazydocs' ),
			'placeholder' => 'https://api.example.com/v1',
			'default'     => 'https://api.example.com/v1',
			'sanitize'    => 'esc_url_raw',
			'class'       => 'eazydocs-promax-notice',
		),

		array(
			'id'          => 'default_api_version',
			'type'        => 'text',
			'title'       => esc_html__( 'Version Label', 'eazydocs' ),
			'subtitle'    => esc_html__( 'Used on the frontend when a doc’s Version is empty. Also shown as the editor placeholder.', 'eazydocs' ),
			'placeholder' => 'v1',
			'default'     => 'v1',
			'sanitize'    => 'sanitize_text_field',
			'class'       => 'eazydocs-promax-notice',
		),

		array(
			'type'    => 'subheading',
			'content' => esc_html__( 'Display', 'eazydocs' ),
		),

		array(
			'id'       => 'default_api_display_format',
			'type'     => 'button_set',
			'title'    => esc_html__( 'Display Mode', 'eazydocs' ),
			'subtitle' => esc_html__( 'Site default. Each doc’s Display tab can override. Multi-page uses separate URLs for collections and endpoints; One-page lists every endpoint on one scrollable page.', 'eazydocs' ),
			'options'  => array(
				'multi'   => esc_html__( 'Multi-page', 'eazydocs' ),
				'onepage' => esc_html__( 'One-page', 'eazydocs' ),
			),
			'default'  => 'multi',
			'class'    => 'eazydocs-promax-notice',
		),

		ezd_csf_switcher_field( array(
			'id'       => 'default_show_method_badges',
			'title'    => esc_html__( 'HTTP Method Badges', 'eazydocs' ),
			'subtitle' => esc_html__( 'Site-wide. Colored GET, POST, PUT, PATCH, DELETE labels next to each endpoint title.', 'eazydocs' ),
			'default'  => true,
			'class'    => 'eazydocs-promax-notice',
		) ),

		ezd_csf_switcher_field( array(
			'id'       => 'default_show_try_it_placeholder',
			'title'    => esc_html__( 'Code Panel', 'eazydocs' ),
			'subtitle' => esc_html__( 'Site-wide. Right-side panel with request examples and response samples. Does not send live API calls.', 'eazydocs' ),
			'default'  => true,
			'class'    => 'eazydocs-promax-notice',
		) ),

		array(
			'id'          => 'default_example_language',
			'type'        => 'text',
			'title'       => esc_html__( 'Code Language', 'eazydocs' ),
			'subtitle'    => esc_html__( 'Site-wide. Opens this language tab first in the Code panel when an endpoint has a matching Code Language example (e.g. cURL, JavaScript).', 'eazydocs' ),
			'placeholder' => 'cURL',
			'default'     => 'cURL',
			'sanitize'    => 'sanitize_text_field',
			'class'       => 'eazydocs-promax-notice',
		),
	),
) );
