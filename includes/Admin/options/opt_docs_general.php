<?php
/**
 * General Documentation Settings
 * Core configuration options for your documentation system.
 */
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}


//
// General Fields
//
ezd_render_csf_section( $prefix, 'general_fields', esc_html__( 'General Settings', 'eazydocs' ), 'dashicons dashicons-admin-settings', [
	ezd_csf_text_field([
		'id' => 'docs_menu_title',
		'title' => esc_html__( 'Admin Menu Label', 'eazydocs' ),
		'subtitle' => esc_html__( 'Customize the documentation menu name displayed in your WordPress admin sidebar.', 'eazydocs' ),
		'default' => esc_html__( 'EazyDocs', 'eazydocs' ),
	]),

	ezd_csf_pages_select_field([
		'id' => 'docs-slug',
		'title' => esc_html__( 'Documentation Archive Page', 'eazydocs' ),
		'subtitle' => esc_html__( 'Select the main page that displays all your documentation. This page will appear in breadcrumbs and serve as your docs homepage.', 'eazydocs' ),
		'desc' => esc_html__( 'Create this page using the [eazydocs] shortcode, EazyDocs Gutenberg blocks, or Elementor widgets.', 'eazydocs' ),
		'class' => 'docs-page-wrap',
		'multiple' => false,
	]),

	ezd_csf_switcher_field([
		'id' => 'docs-view-all-btn',
		'title' => esc_html__( '"View All" Button', 'eazydocs' ),
		'subtitle' => esc_html__( 'Display a "View All" or "Read More" button on the documentation archive page.', 'eazydocs' ),
		'desc' => esc_html__( 'When hidden, docs without child pages won\'t show this button.', 'eazydocs' ),
		'default' => false,
		'text_width' => 80
	]),

	array(
		'id'         => 'docs-url-structure',
		'type'       => 'select',
		'title'      => esc_html__( 'URL Structure', 'eazydocs' ),
		'subtitle'   => esc_html__( 'Choose how your documentation URLs are formatted.', 'eazydocs' ),
		/* translators: %1$s and %2$s are HTML link tags */
		'desc' => sprintf( __( '<strong>Important:</strong> After changing this setting, visit %1$sSettings → Permalinks%2$s and click "Save Changes" to apply.',
			'eazydocs' ), '<a href="' . admin_url( '/options-permalink.php' ) . '" target="_blank">', '</a>' ),
		'options'    => array(
			'custom-slug' => esc_html__( 'Custom Slug (e.g., /docs/article-name)', 'eazydocs' ),
			'post-name'   => esc_html__( 'Direct URLs (e.g., /article-name)', 'eazydocs' ),
		),
		'default'    => 'custom-slug',
		'class'      => 'eazydocs-pro-notice docs-url-structure',
		'multiple'   => false,
		'ajax'       => true,
		'attributes' => array(
			'style' => 'width:250px',
		),
		'after'      => esc_html__( 'Note: Plain and numeric permalink structures are not supported.', 'eazydocs' ),
	),

	array(
		'id'         => 'docs-type-slug',
		'type'       => 'text',
		'title'      => esc_html__( 'Documentation URL Slug', 'eazydocs' ),
		'subtitle'   => esc_html__( 'Define the base URL path for all documentation pages. Cannot be left empty.', 'eazydocs' ),
		'default'    => 'docs',
		'class'      => 'eazydocs-pro-notice',
		/* translators: %1$s and %2$s are HTML link tags */
		'desc' => sprintf( __( '<strong>Important:</strong> After changing, visit %1$sSettings → Permalinks%2$s and click "Save Changes".',
			'eazydocs' ), '<a href="' . admin_url( '/options-permalink.php' ) . '" target="_blank">', '</a>' ),
		'dependency' => array( 'docs-url-structure', '==', 'custom-slug' ),
		'validate'   => 'ezd_slug_validate'
	),

	array(
		'id'          => 'brand_color',
		'type'        => 'color',
		'title'       => esc_html__( 'Primary Brand Color', 'eazydocs' ),
		'subtitle'    => esc_html__( 'Set the main accent color used throughout your documentation for buttons, links, and highlights.', 'eazydocs' ),
		'default'     => '#0866ff',
		'output'      => ':root',
		'output_mode' => '--ezd_brand_color',
	)
]);