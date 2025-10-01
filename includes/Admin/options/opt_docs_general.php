<?php
/**
 * Cannot access directly.
 */
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}


//
// General Fields
//
CSF::createSection( $prefix, array(
	'id'     => 'general_fields',
	'title'  => esc_html__( 'Docs General', 'eazydocs' ),
	'icon'   => 'dashicons dashicons-admin-settings',
	'fields' => array(
		array(
			'id'       => 'docs_menu_title',
			'type'     => 'text',
			'title'    => esc_html__( 'Dashboard Menu Title', 'eazydocs' ),
			'subtitle' => esc_html__( 'Change the Documentation menu title in WordPress dashboard', 'eazydocs' ),
			'default'  => esc_html__( 'EazyDocs', 'eazydocs' ),
		),

		array(
			'id'         => 'docs-slug',
			'type'       => 'select',
			'title'      => esc_html__( 'Docs Archive Page', 'eazydocs' ),
			'subtitle'   => esc_html__( 'This page will show on the Doc single page breadcrumb and will be used to show the Docs.', 'eazydocs' ),
			'desc'       => esc_html__( 'You can create this page with using [eazydocs] shortcode or available EazyDocs Gutenberg blocks or Elementor widgets.', 'eazydocs' ),
			'options'    => 'pages',
			'class'      => 'docs-page-wrap',
			'multiple'   => false,
			'query_args' => array(
				'posts_per_page' => -1,
			),
			'chosen'     => true,
			'ajax'       => true,
		),

		array(
			'id'         => 'docs-view-all-btn',
			'type'       => 'switcher',
			'title'      => esc_html__( 'View All / Read More Button', 'eazydocs' ),
			'subtitle'   => esc_html__( 'This switch controls the visibility of the button in the Docs Archive page.', 'eazydocs' ),
			'desc'       => esc_html__( 'If set to "Hide", the button will not appear for docs that do not have any child posts.', 'eazydocs' ),
			'text_on'    => esc_html__( 'Show', 'eazydocs' ),
			'text_off'   => esc_html__( 'Hide', 'eazydocs' ),
			'default'    => false,
			'text_width' => 72,
		),

		array(
			'id'         => 'docs-url-structure',
			'type'       => 'select',
			'title'      => esc_html__( 'Doc Root URL Slug', 'eazydocs' ),
			'subtitle'   => esc_html__( 'Select the Docs URL Structure. This will be used to generate the Docs URL.', 'eazydocs' ),
			/* translators: %1$s and %2$s are HTML link tags */
			'desc' => sprintf( __( '<b>Note:</b> To apply this settings, After changing the URL structure here, go to %1$s Settings > Permalinks %2$s and click on the Save Changes button.',
				'eazydocs' ), '<a href="' . admin_url( '/options-permalink.php' ) . '" target="_blank">', '</a>' ),
			'options'    => array(
				'custom-slug' => esc_html__( 'Custom slug', 'eazydocs' ),
				'post-name'   => esc_html__( 'No slug', 'eazydocs' ),
			),
			'default'    => 'custom-slug',
			'class'      => 'eazydocs-pro-notice docs-url-structure',
			'multiple'   => false,
			'ajax'       => true,
			'attributes' => array(
				'style' => 'width:250px',
			),
			'after'      => esc_html__( 'Ignore the plain and numeric permalink structure', 'eazydocs' ),
		),

		array(
			'id'         => 'docs-type-slug',
			'type'       => 'text',
			'title'      => esc_html__( 'Root slug', 'eazydocs' ),
			'subtitle'   => esc_html__( 'Make sure to keep Docs Root Slug in the Single Docs Permalink. You are not able to keep it blank.', 'eazydocs' ),
			'default'    => 'docs',
			'class'      => 'eazydocs-pro-notice',
			/* translators: %1$s and %2$s are HTML link tags */
			'desc' => sprintf( __( '<b>Note:</b> After changing the slug, go to %1$s Settings > Permalinks %2$s and click on the Save Changes button.',
				'eazydocs' ), '<a href="' . admin_url( '/options-permalink.php' ) . '" target="_blank">', '</a>' ),
			'dependency' => array( 'docs-url-structure', '==', 'custom-slug' ),
			'validate'   => 'ezd_slug_validate'
		),

		array(
			'id'          => 'brand_color',
			'type'        => 'color',
			'title'       => esc_html__( 'Frontend Brand Color', 'eazydocs' ),
			'default'     => '#0866ff',
			'output'      => ':root',
			'output_mode' => '--ezd_brand_color',
		)
	)
) );