<?php

//
// General Fields
//
CSF::createSection( $prefix, array(
	'id'     => 'general_fields',
	'title'  => esc_html__( 'Docs General', 'eazydocs' ),
	'icon'   => 'fas fa-plus-circle',
	'fields' => array(

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
			'id'         => 'docs-url-structure',
			'type'       => 'select',
			'title'      => esc_html__( 'Doc Root URL Slug', 'eazydocs' ),
			'subtitle'   => esc_html__( 'Select the Docs URL Structure. This will be used to generate the Docs URL.', 'eazydocs' ),			
			'desc'   	=> sprintf( __( '<b>Note:</b> To apply this settings, After changing the URL structure here, go to %s Settings > Permalinks %s and click on the Save Changes button.', 'eazydocs' ), '<a href="'.admin_url('/options-permalink.php').'" target="_blank">', '</a>' ),
			'options'    => array(
				'custom-slug' 	=> esc_html__( 'Custom slug', 'eazydocs' ),
				'post-name' 	=> esc_html__( 'No slug', 'eazydocs' ),
			),
			'default'    => 'custom-slug',
			'class'      => 'eazydocs-pro-notice docs-url-structure',
			'multiple'   => false,
			'ajax'     	 => true,
			'attributes' => array(
				'style'  => 'width:250px',
			),
			'after' 	=> esc_html__('Ignore the plain and numeric permalink structure', 'eazydocs'),
		),

		array(
			'id'      => 'docs-type-slug',
			'type'    => 'text',
			'title'   => esc_html__( 'Root slug', 'eazydocs' ),
			'subtitle' => esc_html__( 'Make sure to keep Docs Root Slug in the Single Docs Permalink. You are not able to keep it blank.', 'eazydocs' ),
			'default'  => esc_html__( 'docs', 'eazydocs' ),	
			'desc'     => sprintf( __( '<b>Note:</b> After changing the slug, go to %s Settings > Permalinks %s and click on the Save Changes button.', 'eazydocs' ), '<a href="'.admin_url('/options-permalink.php').'" target="_blank">', '</a>' ),
			'dependency' => array( 'docs-url-structure', '==', 'custom-slug' ),
			'validate' => 'ezd_slug_validate',
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