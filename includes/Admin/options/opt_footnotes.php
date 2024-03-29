<?php

// Footnotes
CSF::createSection( $prefix, array(
	'id'     => 'ezd_footnotes',
	'title'  => esc_html__( 'Footnotes', 'eazydocs' ),
	'icon'   => 'fas fa-plus-circle',
	'fields' => [
		array(
			'id'         => 'is_footnotes_heading',
			'type'       => 'switcher',
			'title'      => esc_html__( 'Footnotes Heading', 'eazydocs' ),
			'text_on'    => esc_html__( 'Show', 'eazydocs' ),
			'text_off'   => esc_html__( 'Hide', 'eazydocs' ),
			'text_width' => 72,
			'default'    => true,
			'class'      => 'eazydocs-pro-notice active-theme-docy active-theme-docly active-theme-ama'
		),
		
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
			'type'    	=> 'radio',
			'options' 	=> [
				'collapsed'    	=> esc_html__( 'Collapsed', 'eazydocs' ),
				'expanded' 		=> esc_html__( 'Expanded', 'eazydocs' ),
			],
			'subtitle'      => esc_html__( 'Select how the footnote will look normally', 'eazydocs' ),
			'default' => 'collapsed',
			'dependency' => array(
				array( 'is_footnotes_heading', '==', 'true' ),
			)
		),

		array(
			'id'       => 'footnotes_column',
			'type'     => 'select',
			'title'    => esc_html__( 'Footnotes Column', 'eazydocs' ),
			'options'  => [
				'1' => __( '1 Column', 'eazydocs' ),
				'2' => __( '2 Column', 'eazydocs' ),
				'3' => __( '3 Column', 'eazydocs' ),
				'4' => __( '4 Column', 'eazydocs' ),
				'5' => __( '5 Column', 'eazydocs' ),
				'6' => __( '6 Column', 'eazydocs' ),
			],
			'chosen'   => true,
			'multiple' => false,
			'default'  => '1',
			'class'      => 'eazydocs-pro-notice active-theme-docy active-theme-docly active-theme-ama'
		),

		array(
			'id'         => 'ezdocs_footnote_shortcode1',
			'type'       => 'text',
			'title'      => esc_html__( 'Footnote Shortcode', 'eazydocs' ),
			'subtitle'   => sprintf( esc_html__( 'Use this shortcode to display footnotes. %s Learn how to create Footnotes %s', 'eazydocs' ), '<a href="https://tinyurl.com/2ewlorze" target="_blank">', '</a>' ),
			'desc'       => esc_html__( 'See the shortcode example with the available attributes', 'eazydocs' ) . '<br><code>[reference number="1"]Tooltip Content[/reference]</code>',
			'default'    => '[reference]',
			'attributes' => array(
				'readonly' => 'readonly',
			),
			'class'      => 'eazydocs-pro-notice active-theme-docy active-theme-docly active-theme-ama'
		),
	]
) );