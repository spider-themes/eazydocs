<?php
/**
 * API Docs settings (Pro Max).
 * Parent + Archive / Single child tabs (same pattern as Single Doc Page).
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

// Archive
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
							<p>' . esc_html__( 'Controls for the public /api-docs/ listing page only.', 'eazydocs' ) . '</p>
						</div>
					</div>
				</div>
			',
		),

		ezd_csf_switcher_field( array(
			'id'       => 'enable_api_docs_archive',
			'title'    => esc_html__( 'Enable Archive', 'eazydocs' ),
			'subtitle' => esc_html__( 'Public listing at /api-docs/ for all published API Docs.', 'eazydocs' ),
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

// Single
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
							<p>' . esc_html__( 'Defaults applied when you create a new API Doc. Each doc can still override these in its own settings.', 'eazydocs' ) . '</p>
						</div>
					</div>
				</div>
			',
		),

		array(
			'id'          => 'default_base_url',
			'type'        => 'text',
			'title'       => esc_html__( 'Base URL', 'eazydocs' ),
			'subtitle'    => esc_html__( 'Pre-filled on new API Docs.', 'eazydocs' ),
			'placeholder' => 'https://api.example.com/v1',
			'default'     => 'https://api.example.com/v1',
			'sanitize'    => 'esc_url_raw',
			'class'       => 'eazydocs-promax-notice',
		),

		array(
			'id'          => 'default_api_version',
			'type'        => 'text',
			'title'       => esc_html__( 'Version Label', 'eazydocs' ),
			'subtitle'    => esc_html__( 'Pre-filled on new API Docs.', 'eazydocs' ),
			'placeholder' => 'v1',
			'default'     => 'v1',
			'sanitize'    => 'sanitize_text_field',
			'class'       => 'eazydocs-promax-notice',
		),

		array(
			'id'       => 'default_api_display_format',
			'type'     => 'button_set',
			'title'    => esc_html__( 'Display Mode', 'eazydocs' ),
			'subtitle' => esc_html__( 'Multi-page uses separate URLs. One-page shows everything on a single page.', 'eazydocs' ),
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
			'title'      => esc_html__( 'Multi-page Layout', 'eazydocs' ),
			'subtitle'   => esc_html__( 'Sidebar layout for Multi-page docs.', 'eazydocs' ),
			'options'    => array(
				'both_sidebar' => EZD_IMG . 'customizer/both_sidebar.jpg',
				'left_sidebar' => EZD_IMG . 'customizer/sidebar_left.jpg',
			),
			'default'    => 'both_sidebar',
			'class'      => 'eazydocs-promax-notice single-layout-img-wrap',
			'dependency' => array( 'default_api_display_format', '==', 'multi' ),
		),

		array(
			'id'         => 'default_api_onepage_layout',
			'type'       => 'image_select',
			'title'      => esc_html__( 'One-page Layout', 'eazydocs' ),
			'subtitle'   => esc_html__( 'Sidebar layout for One-page docs.', 'eazydocs' ),
			'options'    => array(
				'classic-onepage-layout' => EZD_IMG . 'customizer/both_sidebar.jpg',
				'fullscreen-layout'      => EZD_IMG . 'customizer/sidebar_left.jpg',
			),
			'default'    => 'classic-onepage-layout',
			'class'      => 'eazydocs-promax-notice single-layout-img-wrap',
			'dependency' => array( 'default_api_display_format', '==', 'onepage' ),
		),

		ezd_csf_switcher_field( array(
			'id'       => 'default_show_method_badges',
			'title'    => esc_html__( 'HTTP Method Badges', 'eazydocs' ),
			'subtitle' => esc_html__( 'Show GET / POST / etc. badges by default.', 'eazydocs' ),
			'default'  => true,
			'class'    => 'eazydocs-promax-notice',
		) ),

		ezd_csf_switcher_field( array(
			'id'       => 'default_show_try_it_placeholder',
			'title'    => esc_html__( 'Code Panel', 'eazydocs' ),
			'subtitle' => esc_html__( 'Show sample code on the right by default. Does not send live requests.', 'eazydocs' ),
			'default'  => true,
			'class'    => 'eazydocs-promax-notice',
		) ),

		array(
			'id'          => 'default_example_language',
			'type'        => 'text',
			'title'       => esc_html__( 'Code Language', 'eazydocs' ),
			'subtitle'    => esc_html__( 'Default language tab label, e.g. cURL, Python, or Go.', 'eazydocs' ),
			'placeholder' => 'cURL',
			'default'     => 'cURL',
			'sanitize'    => 'sanitize_text_field',
			'class'       => 'eazydocs-promax-notice',
		),
	),
) );
