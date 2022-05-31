<?php if ( ! defined( 'ABSPATH' )  ) { die; } // Cannot access directly.

//
// Set a unique slug-like ID
//
$prefix = 'eazydocs_customizer';

//
// Create customize options
//
CSF::createCustomizeOptions( $prefix );


// EazyDocs Page
CSF::createSection( $prefix, array(
	'id'       => 'docs-page',
	'title'    => esc_html__( 'EazyDocs', 'eazydocs-pro' ),
	'priority' => 1,
) );

// Docs Archive Page
CSF::createSection( $prefix, array(
	'id'       => 'docs-archive-page',
	'parent'   => 'docs-page',
	'title'    => esc_html__( 'Docs Archive', 'eazydocs-pro' ),
	'fields'   => array(
		array(
			'id'      => 'docs-column',
			'type'    => 'image_select',
			'class'   => 'docs-layout-img-wrap',
			'title'   => esc_html__( 'Select Layout', 'eazydocs-pro' ),
			'options' => array(
				'3' => EAZYDOCS_IMG . '/customizer/4.svg',
				'4' => EAZYDOCS_IMG . '/customizer/3.svg',
				'6' => EAZYDOCS_IMG . '/customizer/2.svg',
			),
			'attributes' => [
				'width'  => '100px'
			],
			'default' => '4'
		),
		array(
			'id'      => 'docs-view-more',
			'type'    => 'text',
			'title'   => esc_html__( 'View More Button', 'eazydocs-pro' ),
			'default' => esc_html__('View More', 'eazydocs-pro')
		)
	)
) );


// Single Doc
CSF::createSection( $prefix, array(
	'id'       => 'docs-single-page',
	'parent'   => 'docs-page',
	'title'    => esc_html__( 'Single Doc', 'eazydocs-pro' ),
	'fields'   => array(
        array(
            'id'            => 'doc_elements',
            'type'          => 'accordion',
            'title'         => esc_html__( 'Design Elements', 'eazydocs-pro' ),
            'accordions'    => array(
                // Doc Layout
                array(
                    'title'         => esc_html__( 'Doc Layout', 'eazydocs-pro' ),
                    'fields'        => array(
                        array(
                            'id'        => 'docs_single_layout',
                            'type'      => 'image_select',
                            'class'     => 'single-layout-img-wrap',
                            'title'     => esc_html__('Select Layout', 'eazydocs-pro'),
                            'options'   => array(
                                'both_sidebar'  => EAZYDOCS_IMG . '/customizer/both_sidebar.jpg',
                                'left_sidebar'  => EAZYDOCS_IMG . '/customizer/sidebar_left.jpg',
                                'right_sidebar' => EAZYDOCS_IMG . '/customizer/sidebar_right.jpg',
                            ),
                            'default'   => 'both_sidebar',
                            'attributes' => [
                                'width'  => '90px'
                            ],
                            'class'     => 'eazydocs-pro-notice',

                        ),
                        array(
                            'id'      => 'docs_page_width',
                            'type'    => 'select',
                            'title'   => esc_html__( 'Page Layout', 'eazydocs-pro' ),
                            'options' => [
                                'boxed'         => esc_html__( 'Boxed', 'eazydocs-pro' ),
                                'full-width'    => esc_html__( 'Full Width', 'eazydocs-pro' ),
                            ],
                            'default' => 'boxed'
                        ),
                    )
                ),

                // Search Banner
                array(
                    'title'         => esc_html__( 'Search Banner', 'eazydocs' ),
                    'fields'        => array(

	                    array(
		                    'id'    => 'doc_banner_bg',
		                    'type'  => 'background',
		                    'title' => esc_html__( 'Background', 'eazydocs' ),
		                    'output' => '.ezd_search_banner.has_bg_dark.has_cs_bg',
		                    'class'         => 'eazydocs-pro-notice'
	                    ),
                        array(
                            'id'            => 'keywords_label_color',
                            'type'          => 'color',
                            'title'         => esc_html__( 'Keywords Label Color', 'eazydocs-pro' ),
                            'output_mode'   => 'color',
                            'output'        => '.ezd_search_banner.has_bg_dark .header_search_keyword .label',
                            'class'         => 'eazydocs-pro-notice'
                        ),
                        array(
                            'id'            => 'keywords_color',
                            'type'          => 'color',
                            'title'         => esc_html__( 'Keywords Color', 'eazydocs-pro' ),
                            'output_mode'   => 'color',
                            'output'        => '.ezd_search_banner.has_bg_dark .header_search_keyword ul li a',
                            'class'         => 'eazydocs-pro-notice'
                        ),
                    )
                ),

                // Left Sidebar
                array(
                    'title'         => esc_html__( 'Sidebar', 'eazydocs-pro' ),
                    'fields'        => array(
                        array(
                            'id'            => 'docs-sidebar-bg',
                            'type'          => 'color',
                            'title'         => esc_html__( 'Background Color', 'eazydocs-pro' ),
                            'output_mode'   => 'background-color',
                            'output'        => '.doc_left_sidebarlist:before,.doc_left_sidebarlist:after',
                        ),
                        array(
                            'id'        => 'docs-sidebar-padding',
                            'type'      => 'spacing',
                            'top'       => false,
                            'bottom'    => false,
                            'title'     => esc_html__( 'Sidebar Padding', 'eazydocs-pro' ),
                            'output'    => '.doc_left_sidebarlist',
                        ),
                    )
                ),

                // Content Area
                array(
                    'title'         => esc_html__( 'Body Area', 'eazydocs-pro' ),
                    'fields'        => array(
                        array(
                            'id'            => 'content-bg',
                            'type'          => 'color',
                            'title'         => esc_html__('Background Color', 'eazydocs-pro'),
                            'output'        => 'body.single-docs',
                            'output_mode'   => 'background-color',
                        ),
                    )
                )
            )
        ),
	)
) );


// Assistant
CSF::createSection( $prefix, array(
	'id'     => 'docs-assistant',
	'parent' => 'docs-page',
	'class'     => 'eazydocs-pro-notice',
	'title'  => esc_html__( 'Assistant', 'eazydocs-pro' ),
	'fields' => array(

		array(
			'id'         => 'assistant_visibility',
			'type'       => 'switcher',
			'title'      => esc_html__( 'Visibility', 'eazydocs-pro' ),
			'text_on'    => esc_html__( 'Show', 'eazydocs-pro' ),
			'text_off'   => esc_html__( 'Hide', 'eazydocs-pro' ),
			'text_width' => 70,
			'default'    => false,
			'class'     => 'eazydocs-pro-notice'
		),

		array(
			'id'          => 'assistant_bg',
			'type'        => 'color',
			'title'       => esc_html__( 'Background Color', 'eazydocs-pro' ),
			'output'      => '.chat-toggle a',
			'output_mode' => 'background-color',
			'dependency'  => array(
				array( 'assistant_visibility', '==', 'true' )
			),
			'class'     => 'eazydocs-pro-notice'
		),

		array(
			'id'     => 'assistant_spacing',
			'type'   => 'spacing',
			'title'  => 'Position',
			'output' => '.chat-toggle,.chatbox-wrapper',
			'output_mode' => 'margin', // or margin, relative
			'dependency'  => array(
				array( 'assistant_visibility', '==', 'true' )
			),
			'class'     => 'eazydocs-pro-notice'
		)

	)
) );