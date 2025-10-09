<?php
/**
 * Cannot access directly.
 */
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}


// Footnotes
CSF::createSection( $prefix, array(
	'id'     => 'ezd_footnotes',
	'title'  => esc_html__( 'Footnotes', 'eazydocs' ),
	'icon'   => 'dashicons dashicons-text-page',
	'fields' => [
		ezd_csf_switcher_field([
			'id'         => 'is_footnotes_heading',
			'title'      => esc_html__( 'Footnotes Heading', 'eazydocs' ),
			'text_width' => 72,
			'default'    => true,
			'class'      => 'eazydocs-pro-notice active-theme-docy active-theme-docly active-theme-ama'
		]),

		array(
			'id'         => 'footnotes_heading_text',
			'type'       => 'text',
			'title'      => esc_html__( 'Footnotes Heading Text', 'eazydocs' ),
			'dependency' => array(
				array( 'is_footnotes_heading', '==', 'true' ),
			),
			'default'    => esc_html__( 'Footnotes', 'eazydocs' ),
			'class'      => 'eazydocs-pro-notice active-theme-docy active-theme-docly active-theme-ama'
		),

		array(
			'id'         => 'footnotes_layout',
			'title'      => esc_html__( 'Footnotes Layout', 'eazydocs' ),
			'type'       => 'radio',
			'options'    => [
				'collapsed' => esc_html__( 'Collapsed', 'eazydocs' ),
				'expanded'  => esc_html__( 'Expanded', 'eazydocs' ),
			],
			'subtitle'   => esc_html__( 'Select how the footnote will look normally', 'eazydocs' ),
			'default'    => 'collapsed',
			'dependency' => array(
				array( 'is_footnotes_heading', '==', 'true' ),
			)
		),

		array(
			'id'       => 'footnotes_column',
			'type'     => 'select',
			'title'    => esc_html__( 'Footnotes Column', 'eazydocs' ),
			'options'  => [
				'1' => esc_html__( '1 Column', 'eazydocs' ),
				'2' => esc_html__( '2 Column', 'eazydocs' ),
				'3' => esc_html__( '3 Column', 'eazydocs' ),
				'4' => esc_html__( '4 Column', 'eazydocs' ),
				'5' => esc_html__( '5 Column', 'eazydocs' ),
				'6' => esc_html__( '6 Column', 'eazydocs' ),
			],
			'chosen'   => true,
			'multiple' => false,
			'default'  => '1',
			'class'    => 'eazydocs-pro-notice active-theme-docy active-theme-docly active-theme-ama'
		),

		array(
			'id'         => 'ezdocs_footnote_shortcode1',
			'type'       => 'text',
			'title'      => esc_html__( 'Footnote Shortcode', 'eazydocs' ),
			/* translators: %1$s - opening link tag, %2$s - closing link tag */
			'subtitle'   => sprintf( esc_html__( 'Use this shortcode to display footnotes. %1$s Learn how to create Footnotes %2$s', 'eazydocs' ),
				'<a href="https://tinyurl.com/2ewlorze" target="_blank">', '</a>' ),
			'desc'       => esc_html__( 'See the shortcode example with the available attributes', 'eazydocs' )
			                . '<br><code>[reference number="1"]Tooltip Content[/reference]</code>',
			'default'    => '[reference]',
			'attributes' => array(
				'readonly' => 'readonly',
			),
			'class'      => 'eazydocs-pro-notice active-theme-docy active-theme-docly active-theme-ama'
		),
	]
) );

$meta = 'eazydocs_meta';
// Register a custom meta box for the Docs post type.
CSF::createMetabox( $meta, array(
	'title'     => esc_html__( 'Docs :: Options', 'eazydocs' ),
	'post_type' => 'docs',
	'data_type' => 'unserialize',
	'priority'  => 'default'
) );

// Create the fields conditionally.
CSF::createSection( $meta, array(
	'id'     => 'ezd_footnotes',
	'title'  => esc_html__( 'Footnotes', 'eazydocs' ),
	'fields' => array(
		array(
			'id'     => 'footnotes_colum_opt',
			'type'   => 'fieldset',
			'title'  => esc_html__( 'Footnotes Column', 'eazydocs' ),
			'desc'   => esc_html__( 'Select Default to use the value from the settings, or choose Custom to manually select the column number from the dropdown.',
				'eazydocs' ),
			'fields' => array(
				array(
					'id'       => 'footnotes_column_source',
					'type'     => 'select',
					'title'    => null,
					'options'  => [
						'default' => esc_html__( 'Default', 'eazydocs' ),
						'custom'  => esc_html__( 'Custom', 'eazydocs' ),
					],
					'multiple' => false,
					'default'  => 'default',
					'class'    => 'eazydocs-pro-notice active-theme-docy active-theme-docly active-theme-ama'
				),
				array(
					'id'         => 'footnotes_column',
					'type'       => 'select',
					'title'      => esc_html__( 'Set Column', 'eazydocs' ),
					'options'    => array(
						'1' => esc_html__( '1 Column', 'eazydocs' ),
						'2' => esc_html__( '2 Columns', 'eazydocs' ),
						'3' => esc_html__( '3 Columns', 'eazydocs' ),
						'4' => esc_html__( '4 Columns', 'eazydocs' ),
						'5' => esc_html__( '5 Columns', 'eazydocs' ),
						'6' => esc_html__( '6 Columns', 'eazydocs' )
					),
					'default'    => ezd_get_opt( 'footnotes_column', 3 ),
					'class'      => 'eazydocs-pro-notice active-theme-docy active-theme-docly active-theme-ama layout-inline',
					'dependency' => array(
						array( 'footnotes_column_source', '==', 'custom' ),
					)
				)
			)
		),
	)
) );