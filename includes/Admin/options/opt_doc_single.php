<?php

// Single Doc Fields
CSF::createSection( $prefix, array(
	'id'    => 'single_doc',
	'title' => esc_html__( 'Doc Single', 'eazydocs' ),
	'icon'  => 'fas fa-plus-circle',
) );


//
// Single Doc > General
//
CSF::createSection( $prefix, array(
	'parent' => 'single_doc',
	'title'  => esc_html__( 'General', 'eazydocs' ),
	'icon'   => '',
	'fields' => array(

		array(
			'id'      => 'docs_single_layout',
			'type'    => 'image_select',
			'title'   => esc_html__( 'Select Layout', 'eazydocs' ),
			'options' => array(
				'both_sidebar'  => EAZYDOCS_IMG . '/customizer/both_sidebar.jpg',
				'left_sidebar'  => EAZYDOCS_IMG . '/customizer/sidebar_left.jpg',
				'right_sidebar' => EAZYDOCS_IMG . '/customizer/sidebar_right.jpg',
			),
			'default' => 'both_sidebar',
			'class'   => 'single-layout-img-wrap eazydocs-pro-notice active-theme',
		),

		array(
			'id'      => 'docs_page_width',
			'type'    => 'select',
			'title'   => esc_html__( 'Page Width', 'eazydocs' ),
			'options' => [
				'boxed'      => esc_html__( 'Boxed', 'eazydocs' ),
				'full-width' => esc_html__( 'Full Width', 'eazydocs' ),
			],
			'default' => 'boxed'
		),

		array(
			'id'          => 'content-bg',
			'type'        => 'color',
			'title'       => esc_html__( 'Background Color', 'eazydocs' ),
			'output'      => 'body.single-docs',
			'output_mode' => 'background-color',
		),

		array(
			'id'         => 'is_featured_image',
			'type'       => 'switcher',
			'title'      => esc_html__( 'Featured Image', 'eazydocs' ),
			'subtitle'   => esc_html__( 'Show the Featured Image on the top of the doc content area.', 'eazydocs' ),
			'text_on'    => esc_html__( 'Show', 'eazydocs' ),
			'text_off'   => esc_html__( 'Hide', 'eazydocs' ),
			'text_width' => 92,
			'default'    => false,
		),

		array(
			'title'      => esc_html__( 'Ajax Loading', 'eazydocs' ),
			'subtitle'   => esc_html__( 'Load the doc single page without refreshing the page.', 'eazydocs' ),
			'id'         => 'is_doc_ajax',
			'type'       => 'switcher',
			'text_on'    => esc_html__( 'Enabled', 'eazydocs' ),
			'text_off'   => esc_html__( 'Disabled', 'eazydocs' ),
			'text_width' => 90,
			'default'    => false,
			'class'      => 'eazydocs-pro-notice active-theme-docy active-theme-docly active-theme-ama',
		),

		// Meta Information
		array(
			'type'       => 'heading',
			'content'    => esc_html__( 'Meta Information', 'eazydocs' ),
		),

		array(
			'id'      => 'enable-reading-time',
			'type'    => 'switcher',
			'title'   => esc_html__( 'Estimated Reading Time', 'eazydocs' ),
			'default' => true // or false
		),

		array(
			'id'      => 'enable-views',
			'type'    => 'switcher',
			'title'   => esc_html__( 'Views', 'eazydocs' ),
			'default' => true // or false
		),

		array(
			'id'      		=> 'enable-unique-views',
			'type'    		=> 'switcher',
			'title'   		=> esc_html__( 'Unique Views', 'eazydocs' ),
			'subtitle'		=> esc_html__( 'Unique views will stop counting views from reload pages.', 'eazydocs' ),
			'default' 		=> false, // or false
			'class'   		=> 'eazydocs-pro-notice',
			'dependency'	=> array(
				array( 'enable-views', '==', 'true' ),
			)
		),

		// Excerpt settings
		array(
			'type'  => 'heading',
			'content' => esc_html__( 'Excerpt', 'eazydocs' ),
		),

		array(
			'id'       => 'is_excerpt',
			'type'     => 'switcher',
			'title'    => esc_html__( 'Show Excerpt', 'eazydocs' ),
			'subtitle' => esc_html__( 'Show excerpt on doc single page.', 'eazydocs' ),
			'default'  => true,
		),

		array(
			'id'         => 'excerpt_label',
			'type'       => 'text',
			'title'      => esc_html__( 'Excerpt Label', 'eazydocs' ),
			'subtitle'   => esc_html__( 'Excerpt label on doc single page.', 'eazydocs' ),
			'default'    => esc_html__( 'Summary: ', 'eazydocs' ),
			'dependency' => array( 'is_excerpt', '==', 'true' ),
		),

		array(
			'title'    => esc_html__( 'Section Excerpt', 'eazydocs' ),
			'subtitle' => esc_html__( 'Define here the Doc section excerpt limit in word count to show. Use -1 to show the full excerpt.', 'eazydocs' ),
			'desc'     => esc_html__( 'Note: If the excerpt leaves empty, the excerpt will be automatically taken from the doc post content.', 'eazydocs' ),
			'id'       => 'doc_sec_excerpt_limit',
			'type'     => 'slider',
			'default'  => 12,
			"min"      => 1,
			"step"     => 1,
			"max"      => 100,
		),

		// Articles
		array(
			'type'       => 'heading',
			'content'    => esc_html__( 'Articles', 'eazydocs' ),
		),

		array(
			'id'         => 'is_articles',
			'type'       => 'switcher',
			'title'      => esc_html__( 'Show Articles', 'eazydocs' ),
			'subtitle'   => esc_html__( 'Show the child pages of the current doc.', 'eazydocs' ),
			'default'    => true,
		),

		array(
			'id'         => 'articles_title',
			'type'       => 'text',
			'title'      => esc_html__( 'Articles Title', 'eazydocs' ),
			'default'    => esc_html__( 'Articles', 'eazydocs' ),
			'dependency' => array( 'is_articles', '==', 'true' ),
		),

		// Doc Footer Elements
		array(
			'type'       => 'heading',
			'content'    => esc_html__( 'Doc Footer Elements', 'eazydocs' ),
		),

		array(
			'id'         => 'enable-comment',
			'type'       => 'switcher',
			'title'      => esc_html__( 'Comment', 'eazydocs' ),
			'text_on'    => esc_html__( 'Enabled', 'eazydocs' ),
			'text_off'   => esc_html__( 'Disabled', 'eazydocs' ),
			'text_width' => 92,
			'default'    => true // or false
		),

		array(
			'id'      => 'enable-next-prev-links',
			'type'    => 'switcher',
			'title'   => esc_html__( 'Next & Previous Link', 'eazydocs' ),
			'default' => false, // or false
			'class'   => 'eazydocs-pro-notice'
		),

		array(
			'id'      => 'eazydocs-enable-credit',
			'type'    => 'switcher',
			'title'   => esc_html__( 'Credit', 'eazydocs' ),
			'default' => true,
		),

		array(
			'id'            => 'eazydocs-credit-text',
			'type'          => 'wp_editor',
			'title'         => esc_html__( 'Credit Text', 'eazydocs' ),
			'tinymce'       => true,
			'quicktags'     => false,
			'media_buttons' => false,
			'height'        => '80px',
			'dependency'    => array(
				array( 'eazydocs-enable-credit', '==', 'true' )
			),
			'default'       => 'Powered By <a href="https://wordpress.org/plugins/eazydocs/">EazyDocs</a>',
		),
	)
) );

/**
 * Single Doc > Search Banner
 */
CSF::createSection( $prefix, array(
	'parent' => 'single_doc',
	'title'  => esc_html__( 'Search Banner', 'eazydocs' ),
	'icon'   => '',
	'fields' => array(
		array(
			'type'  => 'subheading',
			'title' => esc_html__( 'Search Banner Settings', 'eazydocs' ),
		),

		array(
			'id'       => 'search_banner_layout',
			'type'     => 'select',
			'title'    => esc_html__( 'Search Banner layout', 'eazydocs' ),
			'options'  => [
				'default'     => esc_html__( 'Default', 'eazydocs' ),
				'el-template' => esc_html__( 'Elementor Template', 'eazydocs' ),
			],
			'default'  => 'default',
			'subtitle' => __( 'Select how the header of the doc detail page will be displayed', 'eazydocs' ),
		),

		array(
			'id'         => 'single_layout_id',
			'type'       => 'select',
			'title'      => esc_html__( 'Select Elementor Template', 'eazydocs' ),
			'options'    => ezd_get_elementor_templates(),
			'dependency' => array( 'search_banner_layout', '==', 'el-template' ),
			'subtitle'   => __( 'How to create Elementor template <a target="__blank" href="https://shorturl.at/filGI">See guide</a>', 'eazydocs' ),
		),

		array(
			'id'         => 'is_search_banner',
			'type'       => 'switcher',
			'title'      => esc_html__( 'Search Banner', 'eazydocs' ),
			'text_on'    => esc_html__( 'Show', 'eazydocs' ),
			'text_off'   => esc_html__( 'Hide', 'eazydocs' ),
			'default'    => true,
			'dependency' => array( 'search_banner_layout', '==', 'default' ),
			'text_width' => 72
		),

		array(
			'id'         => 'doc_banner_bg',
			'type'       => 'background',
			'title'      => esc_html__( 'Background', 'eazydocs' ),
			'output'     => '.ezd_search_banner.has_bg_dark',
			'dependency' => array(
				array( 'is_search_banner', '==', 'true' ),
				array( 'search_banner_layout', '==', 'default' ),
			),
		),

		array(
			'id'         => 'search_banner_padding',
			'type'       => 'spacing',
			'title'      => esc_html__( 'Padding', 'eazydocs' ),
			'output'     => '.ezd_search_banner',
			'dependency' => array(
				array( 'is_search_banner', '==', 'true' ),
				array( 'search_banner_layout', '==', 'default' ),
			),
			'default'    => array(
				'unit' => 'px',
			),
		),

		//Search Keywords
		array(
			'type'       => 'subheading',
			'title'      => esc_html__( 'Search Keywords', 'eazydocs' ),
			'dependency' => array(
				array( 'is_search_banner', '==', 'true' ),
				array( 'search_banner_layout', '==', 'default' ),
			),
		),

		array(
			'id'         => 'is_keywords',
			'type'       => 'switcher',
			'title'      => esc_html__( 'Keywords', 'eazydocs' ),
			'text_on'    => esc_html__( 'Enabled', 'eazydocs' ),
			'text_off'   => esc_html__( 'Disabled', 'eazydocs' ),
			'default'    => false,
			'text_width' => 96,
			'dependency' => array(
				array( 'is_search_banner', '==', 'true' ),
				array( 'search_banner_layout', '==', 'default' ),
			),
			'class'      => 'eazydocs-pro-notice'
		),

		array(
			'id'         => 'keywords_label',
			'type'       => 'text',
			'title'      => esc_html__( 'Keywords Label', 'eazydocs' ),
			'default'    => esc_html__( 'Popular Searches:', 'eazydocs' ),
			'dependency' => array(
				array( 'is_search_banner', '==', 'true' ),
				array( 'is_keywords', '==', 'true' ),
				array( 'search_banner_layout', '==', 'default' ),
			),
			'class'      => 'eazydocs-pro-notice'
		),

		array(
			'id'          		=> 'keywords_label_color',
			'type'        		=> 'color',
			'title'       		=> esc_html__( 'Label Color', 'eazydocs' ),
			'output_mode' 		=> 'color',
			'output'      		=> '.ezd_search_keywords .label',
			'output_important'	=> true,
			'dependency'  		=> array(
				array( 'is_search_banner', '==', 'true' ),
				array( 'is_keywords', '==', 'true' ),
				array( 'search_banner_layout', '==', 'default' ),
			),
			'class'       => 'eazydocs-pro-notice'
		),

		// keyword by dynamic || static select
		array(
			'id'         => 'keywords_by',
			'type'       => 'select',
			'title'      => esc_html__( 'Keywords By', 'eazydocs' ),
			'subtitle'   => esc_html__( 'Select your preferred keywords type.', 'eazydocs' ),
			'desc'       => esc_html__( 'Static keywords are predefined, while dynamic keywords are generated by queries from website visitors', 'eazydocs' ),
			'options'    => array(
				'static'	=> esc_html__( 'Static', 'eazydocs' ),
				'dynamic'  	=> esc_html__( 'Dynamic (Sort by popular)', 'eazydocs' ),
			),
			'default'    => 'static',
			'dependency' => array(
				array( 'is_keywords', '==', 'true' ),
				array( 'is_search_banner', '==', 'true' ),
				array( 'search_banner_layout', '==', 'default' ),
			),
			'class'      => 'eazydocs-pro-notice'
		),

		array(
			'id'         => 'keywords_limit',
			'type'       => 'slider',
			'title'      => esc_html__( 'Keywords Limit', 'eazydocs' ),
			'subtitle'   => esc_html__( 'Set the number of keywords to show.', 'eazydocs' ),
			'default'    => 6,
			'min'        => 1,
			'max'        => 200,
			'step'       => 1,
			'dependency' => array(
				array( 'is_search_banner', '==', 'true' ),
				array( 'is_keywords', '==', 'true' ),
				array( 'search_banner_layout', '==', 'default' ),
				array( 'keywords_by', '==', 'dynamic' ),
			),
			'class'      => 'eazydocs-pro-notice',
		),

		// not found keywords exclude checkbox
		array(
			'id'         => 'is_exclude_not_found',
			'type'       => 'switcher',
			'title'      => esc_html__( 'Exclude Not Found Keywords', 'eazydocs' ),
			'subtitle'   => esc_html__( 'Exclude the keywords that are not found in the search results.', 'eazydocs' ),
			'text_on'    => esc_html__( 'Yes', 'eazydocs' ),
			'text_off'   => esc_html__( 'No', 'eazydocs' ),
			'default'    => false,
			'text_width' => 70,
			'dependency' => array(
				array( 'is_search_banner', '==', 'true' ),
				array( 'is_keywords', '==', 'true' ),
				array( 'search_banner_layout', '==', 'default' ),
				array( 'keywords_by', '==', 'dynamic' ),
			),
			'class'      => 'eazydocs-pro-notice',
		),
		

		array(
			'id'         => 'keywords',
			'type'       => 'repeater',
			'title'      => esc_html__( 'Keywords', 'eazydocs' ),
			'dependency' => array(
				array( 'is_search_banner', '==', 'true' ),
				array( 'is_keywords', '==', 'true' ),
				array( 'search_banner_layout', '==', 'default' ),
				array( 'keywords_by', '==', 'static' ),
			),
			'fields'     => array(
				array(
					'id'    => 'title',
					'type'  => 'text',
					'title' => esc_html__( 'Keyword', 'eazydocs' )
				),
			),
			'default'    => array(
				array(
					'title' => esc_html__('Keyword #1', 'eazydocs'),
				),
				array(
                    'title' => esc_html__('Keyword #2', 'eazydocs'),
				),
			),
			'class'      => 'eazydocs-pro-notice',
            'button_title'     => esc_html__( 'Add New', 'eazydocs' ),
		),

		array(
			'id'          => 'keywords_color',
			'type'        => 'color',
			'title'       => esc_html__( 'Keywords Color', 'eazydocs' ),
			'output_mode' => 'color',
			'output'      => '.ezd_search_banner .header_search_keyword ul li a',
			'dependency'  => array(
				array( 'is_search_banner', '==', 'true' ),
				array( 'is_keywords', '==', 'true' ),
				array( 'search_banner_layout', '==', 'default' ),
			),
			'class'       => 'eazydocs-pro-notice'
		),
		
		array(
			'type'       => 'subheading',
			'title'      => esc_html__( 'Ajax Search Results', 'eazydocs' ),
			'subtitle'   => esc_html__( 'The Search Results settings is global. This settings will be applied to all Ajax Doc Search Results in the plugin.', 'eazydocs' ),
			'dependency' => array(
				array( 'is_search_banner', '==', 'true' ),
				array( 'search_banner_layout', '==', 'default' ),
			),
		),
		
		array(
			'id'         => 'is_search_result_breadcrumb',
			'type'       => 'switcher',
			'title'      => esc_html__( 'Breadcrumb', 'eazydocs' ),
			'subtitle'   => esc_html__( 'Show / Hide the breadcrumbs in search results', 'eazydocs' ),
			'text_on'    => esc_html__( 'Show', 'eazydocs' ),
			'text_off'   => esc_html__( 'Hide', 'eazydocs' ),
			'default'    => false,
			'text_width' => 70,
			'dependency' => array(
				array( 'is_search_banner', '==', 'true' ),
				array( 'search_banner_layout', '==', 'default' ),
			),
			'class'      => 'eazydocs-pro-notice'
		),

		array(
			'id'         => 'is_search_result_thumbnail',
			'type'       => 'switcher',
			'title'      => esc_html__( 'Thumbnail', 'eazydocs' ),
			'subtitle'   => esc_html__( 'Show / Hide the thumbnail in search results', 'eazydocs' ),
			'text_on'    => esc_html__( 'Show', 'eazydocs' ),
			'text_off'   => esc_html__( 'Hide', 'eazydocs' ),
			'default'    => false,
			'text_width' => 70,
			'dependency' => array(
				array( 'is_search_banner', '==', 'true' ),
				array( 'search_banner_layout', '==', 'default' ),
			),
			'class'      => 'eazydocs-pro-notice'
		)
	)
) );

/**
 * Single Doc > Breadcrumbs Fields
 */
CSF::createSection( $prefix, array(
	'parent' => 'single_doc',
	'title'  => esc_html__( 'Breadcrumbs', 'eazydocs' ),
	'icon'   => '',
	'fields' => array(
		array(
			'id'         => 'docs-breadcrumb',
			'type'       => 'switcher',
			'title'      => esc_html__( 'Show/Hide Breadcrumb', 'eazydocs' ),
			'subtitle'   => esc_html__( 'Toggle this switch to Show/Hide the Breadcrumb bar.', 'eazydocs' ),
			'text_on'    => esc_html__( 'Show', 'eazydocs' ),
			'text_off'   => esc_html__( 'Hide', 'eazydocs' ),
			'text_width' => 70,
			'default'    => true, // or false
		),

		array(
			'id'         => 'breadcrumb-home-text',
			'type'       => 'text',
			'title'      => esc_html__( 'Frontpage Name', 'eazydocs' ),
			'default'    => esc_html__( 'Home', 'eazydocs' ),
			'dependency' => array(
				'docs-breadcrumb',
				'==',
				'true'
			),
		),

		array(
			'id'         => 'docs-page-title',
			'type'       => 'text',
			'title'      => esc_html__( 'Docs Archive Page Title', 'eazydocs' ),
			'default'    => esc_html__( 'Docs', 'eazydocs' ),
			'dependency' => array(
				'docs-breadcrumb',
				'==',
				'true'
			),
		),

		array(
			'id'         => 'breadcrumb-update-text',
			'type'       => 'text',
			'title'      => esc_html__( 'Updated Text', 'eazydocs' ),
			'default'    => esc_html__( 'Updated on', 'eazydocs' ),
			'dependency' => array(
				'docs-breadcrumb',
				'==',
				'true'
			)
		),
	)
) );

//
// Single Doc > Feedback Area
//
CSF::createSection( $prefix, array(
	'parent' => 'single_doc',
	'title'  => esc_html__( 'Feedback Options', 'eazydocs' ),
	'icon'   => '',
	'fields' => array(
		array(
			'id'         => 'docs-feedback',
			'type'       => 'switcher',
			'title'      => esc_html__( 'Feedback Area', 'eazydocs' ),
			'text_on'    => esc_html__( 'Show', 'eazydocs' ),
			'text_off'   => esc_html__( 'Hide', 'eazydocs' ),
			'text_width' => 70,
			'default'    => true,
		),

		array(
			'type'       => 'heading',
			'content'    => esc_html__( 'Feedback Area Options', 'eazydocs' ),
			'dependency' => array(
				'docs-feedback',
				'==',
				'true'
			),
		),

		array(
			'id'         => 'message-feedback',
			'type'       => 'switcher',
			'title'      => esc_html__( 'Message Feedback', 'eazydocs' ),
			'text_on'    => esc_html__( 'Show', 'eazydocs' ),
			'text_off'   => esc_html__( 'Hide', 'eazydocs' ),
			'default'    => true,
			'text_width' => 70,
			'dependency' => array(
				'docs-feedback',
				'==',
				'true',
			)
		),

		array(
			'id'         => 'still-stuck',
			'type'       => 'text',
			'title'      => esc_html__( 'Still Stuck', 'eazydocs' ),
			'default'    => esc_html__( 'Still stuck?', 'eazydocs' ),
			'dependency' => array(
				array( 'docs-feedback', '==', 'true' ),
				array( 'message-feedback', '==', 'true' ),
			)
		),

		array(
			'id'         => 'feedback-link-text',
			'type'       => 'text',
			'title'      => esc_html__( 'Help form link text', 'eazydocs' ),
			'default'    => esc_html__( 'How can we help?', 'eazydocs' ),
			'dependency' => array(
				array( 'docs-feedback', '==', 'true' ),
				array( 'message-feedback', '==', 'true' ),
			)
		),

		array(
			'id'         => 'feedback-admin-email',
			'type'       => 'text',
			'title'      => esc_html__( 'Email Address', 'eazydocs' ),
			'default'    => get_option( 'admin_email' ),
			'dependency' => array(
				array( 'docs-feedback', '==', 'true' ),
				array( 'message-feedback', '==', 'true' ),
			)
		),

		array(
			'type'       => 'subheading',
			'content'    => esc_html__( 'Feedback Modal', 'eazydocs' ),
			'dependency' => array(
				array( 'docs-feedback', '==', 'true' ),
				array( 'message-feedback', '==', 'true' ),
			)
		),

		array(
			'id'         => 'feedback-form-title',
			'type'       => 'text',
			'title'      => esc_html__( 'Form Title', 'eazydocs' ),
			'default'    => esc_html__( 'How can we help?', 'eazydocs' ),
			'dependency' => array(
				array( 'docs-feedback', '==', 'true' ),
				array( 'message-feedback', '==', 'true' ),
			)
		),

		array(
			'id'         => 'feedback-form-desc',
			'type'       => 'textarea',
			'title'      => esc_html__( 'Form Subtitle', 'eazydocs' ),
			'dependency' => array(
				array( 'docs-feedback', '==', 'true' ),
				array( 'message-feedback', '==', 'true' ),
			)
		),

		array(
			'type'       => 'heading',
			'title'      => esc_html__( 'Voting Feedback', 'eazydocs' ),
			'dependency' => array(
				array( 'docs-feedback', '==', 'true' ),
			)
		),

		array(
			'id'         => 'helpful_feedback',
			'type'       => 'switcher',
			'title'      => esc_html__( 'Helpful feedback', 'eazydocs' ),
			'default'    => true,
			'dependency' => array(
				'docs-feedback',
				'==',
				'true',
			)
		),

		array(
			'id'         => 'feedback-label',
			'type'       => 'text',
			'title'      => esc_html__( 'Feedback Label', 'eazydocs' ),
			'default'    => esc_html__( 'Was this page helpful?', 'eazydocs' ),
			'dependency' => array(
				array( 'docs-feedback', '==', 'true' ),
				array( 'helpful_feedback', '==', 'true' ),
			)
		),
		array(
			'id'         => 'feedback_count',
			'type'       => 'switcher',
			'title'      => esc_html__( 'Feedback Count', 'eazydocs' ),
			'default'    => true,
			'dependency' => array(
				'docs-feedback',
				'==',
				'true',
			)
		),

		// Feedback on Selected Text
		array(
			'type'       => 'heading',
			'content'    => esc_html__( 'Feedback on Selected Text Options', 'eazydocs' ),
		),

		array(
			'id'         => 'enable-selected-comment',
			'type'       => 'switcher',
			'title'      => esc_html__( 'Feedback on Selected Text', 'eazydocs' ),
			'subtitle'   => esc_html__( 'Enable the feature to allow users to comment on selected text.', 'eazydocs' ),
			'desc'       => esc_html__( 'Note: if enabled, a switcher will appear in the doc meta area to allow visitors to turn On/Off the feature.', 'eazydocs' ),
			'text_on'    => esc_html__( 'Enabled', 'eazydocs' ),
			'text_off'   => esc_html__( 'Disabled', 'eazydocs' ),
			'class'      => 'eazydocs-promax-notice',
			'text_width' => 95,
			'default'    => false
		),

		array(
			'id'         => 'selected-comment-meta-title',
			'type'       => 'text',
			'title'      => esc_html__( 'Frontend Switcher Level', 'eazydocs' ),
			'subtitle'   => esc_html__( 'This title will be shown on the frontend to On/Off the feature.', 'eazydocs' ),
			'default'    => esc_html__( 'Feedback', 'eazydocs' ),
			'class'      => 'eazydocs-promax-notice',
			'dependency' => array( 'enable-selected-comment', '==', 'true' ),
		),

		array(
			'id'         => 'selected-comment-button-text',
			'type'       => 'text',
			'title'      => esc_html__( 'Button Text', 'eazydocs' ),
			'class'      => 'eazydocs-promax-notice',
			'default'    => esc_html__( 'Comment', 'eazydocs' ),
			'dependency' => array( 'enable-selected-comment', '==', 'true' ),
		),

		array(
			'id'         => 'selected-comment-roles',
			'type'       => 'select',
			'title'      => esc_html__( 'Who can view comments?', 'eazydocs' ),
			'options'    => 'roles',
			'default'    => 'administrator',
			'class'      => 'eazydocs-promax-notice',
			'dependency' => array( 'enable-selected-comment', '==', 'true' ),
			'multiple'   => true,
			'chosen'     => true
		),
	)
) );

//
// Doc Left Sidebar Fields
//
CSF::createSection( $prefix, array(
	'parent' => 'single_doc',
	'title'  => esc_html__( 'Left Sidebar', 'eazydocs' ),
	'icon'   => '',
	'fields' => array(
		array(
			'id'      => 'docs_content_layout',
			'type'    => 'radio',
			'title'   => esc_html__( 'Docs Navigation Layout', 'eazydocs' ),
			'options' => [
				'badge_base'    => esc_html__( 'Collapsed with Icons', 'eazydocs' ),
				'category_base' => esc_html__( 'Extended Docs', 'eazydocs' ),
			],
			'default' => 'badge_base',
			'class'   => 'eazydocs-pro-notice',
		),

		array(
			'id'         => 'toggle_visibility',
			'type'       => 'switcher',
			'title'      => esc_html__( 'Sidebar Toggle', 'eazydocs' ),
			'subtitle'   => esc_html__( 'Collapse and Expand the left Sidebar with a Toggle button.', 'eazydocs' ),
			'text_on'    => esc_html__( 'Show', 'eazydocs' ),
			'text_off'   => esc_html__( 'Hide', 'eazydocs' ),
			'text_width' => 72,
			'default'    => true,
		),

		array(
			'id'         => 'search_visibility',
			'type'       => 'switcher',
			'title'      => esc_html__( 'Filter Form', 'eazydocs' ),
			'subtitle'   => esc_html__( 'Filter the left sidebar doc items by typing latter.', 'eazydocs' ),
			'text_on'    => esc_html__( 'Show', 'eazydocs' ),
			'text_off'   => esc_html__( 'Hide', 'eazydocs' ),
			'text_width' => 72,
			'default'    => true,
		),

		array(
			'id'         => 'search_mark_word',
			'type'       => 'switcher',
			'title'      => esc_html__( 'Mark Words', 'eazydocs' ),
			'subtitle'   => esc_html__( 'Highlight the typed keyword in the docs.', 'eazydocs' ),
			'text_on'    => esc_html__( 'Enable', 'eazydocs' ),
			'text_off'   => esc_html__( 'Disable', 'eazydocs' ),
			'text_width' => 80,
			'default'    => false,
			'class'      => 'eazydocs-pro-notice',
		),

		array(
			'title'    => esc_html__( 'Doc Section Icon', 'eazydocs' ),
			'subtitle' => esc_html__( "This is the Doc's default icon. If you don't use icon for the article section individually, this icon will be shown.",
				'eazydocs' ),
			'id'       => 'doc_sec_icon',
			'type'     => 'media',
			'default'  => array(
				'url' => EAZYDOCS_IMG . '/icon/folder-closed.png'
			),
			'class'    => 'eazydocs-pro-notice active-theme'
		),

		array(
			'title'    => esc_html__( 'Doc Section Icon Open', 'eazydocs' ),
			'subtitle' => esc_html__( "This is the Doc's default icon. If you don't use icon for the article section individually, this icon will be shown on open states of the Doc sections.",
				'eazydocs' ),
			'id'       => 'doc_sec_icon_open',
			'type'     => 'media',
			'default'  => array(
				'url' => EAZYDOCS_IMG . '/icon/folder-open.png'
			),
			'class'    => 'eazydocs-pro-notice active-theme'
		),

		array(
			'id'     => 'action_btn_typo',
			'type'   => 'typography',
			'title'  => esc_html__( 'Doc Title Typography', 'eazydocs' ),
			'output' => '.doc_left_sidebarlist .doc-title',
		),

		array(
			'id'          => 'docs-sidebar-bg',
			'type'        => 'color',
			'title'       => esc_html__( 'Background Color', 'eazydocs' ),
			'output_mode' => 'background-color',
			'output'      => '.doc_left_sidebarlist:before,.doc_left_sidebarlist:after',
		)
	)
) );

//
// Doc Right Sidebar Fields
//
CSF::createSection( $prefix, array(
	'parent' => 'single_doc',
	'title'  => esc_html__( 'Right Sidebar', 'eazydocs' ),
	'icon'   => '',
	'fields' => array(
		array(
			'type'  => 'heading',
			'title' => esc_html__( 'Sharing Doc', 'eazydocs' ),
		),

		array(
			'id'         => 'is_social_links',
			'type'       => 'switcher',
			'title'      => esc_html__( 'Share Button', 'eazydocs' ),
			'text_on'    => esc_html__( 'Show', 'eazydocs' ),
			'text_off'   => esc_html__( 'Hide', 'eazydocs' ),
			'text_width' => 72,
			'default'    => true
		),

		array(
			'id'         => 'share_btn_label',
			'type'       => 'text',
			'title'      => esc_html__( 'Share Button Label', 'eazydocs' ),
			'default'    => esc_html__( 'Share this Doc', 'eazydocs' ),
			'dependency' => array(
				array( 'is_copy_link', '==', '1' ),
				array( 'is_social_links', '==', '1' ),
			)
		),

		array(
			'id'         => 'is_copy_link',
			'type'       => 'switcher',
			'title'      => esc_html__( 'Copy Link Button', 'eazydocs' ),
			'text_on'    => esc_html__( 'Show', 'eazydocs' ),
			'text_off'   => esc_html__( 'Hide', 'eazydocs' ),
			'dependency' => array( 'is_social_links', '==', '1' ),
			'text_width' => 72,
			'default'    => true,
		),

		array(
			'id'         => 'copy_link_label',
			'type'       => 'text',
			'title'      => esc_html__( 'Copy Link Label', 'eazydocs' ),
			'default'    => esc_html__( 'Or copy link', 'eazydocs' ),
			'dependency' => array(
				array( 'is_copy_link', '==', '1' ),
				array( 'is_social_links', '==', '1' ),
			)
		),

		array(
			'id'         => 'copy_link_text_success',
			'type'       => 'text',
			'title'      => esc_html__( 'Success Message', 'eazydocs' ),
			'default'    => esc_html__( 'URL copied to clipboard', 'eazydocs' ),
			'dependency' => array(
				array( 'is_copy_link', '==', '1' ),
				array( 'is_social_links', '==', '1' ),
			)
		),

		array(
			'type'  => 'heading',
			'title' => esc_html__( 'Tools', 'eazydocs' ),
		),

		array(
			'id'         => 'font-size-switcher',
			'type'       => 'switcher',
			'title'      => esc_html__( 'Font Size Switcher', 'eazydocs' ),
			'text_on'    => esc_html__( 'Show', 'eazydocs' ),
			'text_off'   => esc_html__( 'Hide', 'eazydocs' ),
			'text_width' => 72,
			'default'    => true,
		),

		array(
			'id'         => 'pr-icon-switcher',
			'type'       => 'switcher',
			'title'      => esc_html__( 'Print Article', 'eazydocs' ),
			'text_on'    => esc_html__( 'Show', 'eazydocs' ),
			'text_off'   => esc_html__( 'Hide', 'eazydocs' ),
			'text_width' => 72,
			'default'    => true,
		),

		// Features
		array(
			'type'  => 'heading',
			'title' => esc_html__( 'Features', 'eazydocs' ),
		),

		array(
			'id'        => 'toc_switcher',
			'type'      => 'switcher',
			'title'     => esc_html__( 'Table on Contents (TOC)', 'eazydocs' ),
			'desc'      => esc_html__( 'EazyDocs will automatically create a structured Table Of Contents(TOC) while you are writing your documentation.', 'eazydocs' ),
			'default'   => true,
		),

		array(
			'id'         => 'toc_heading',
			'type'       => 'text',
			'title'      => esc_html__( 'TOC Heading', 'eazydocs' ),
			'default'    => esc_html__( 'CONTENTS', 'eazydocs' ),
			'dependency' => array( 'toc_switcher', '==', '1' ),
		),

		array(
			'id'         => 'toc_auto_numbering',
			'type'       => 'switcher',
			'desc'      => esc_html__( 'Enable to add numbers before each table of content item.', 'eazydocs' ),
			'title'      => esc_html__( 'Auto Numbering', 'eazydocs' ),
			'text_on'    => esc_html__( 'Show', 'eazydocs' ),
			'text_off'   => esc_html__( 'Hide', 'eazydocs' ),
			'text_width' => 72,
			'dependency' => array( 'toc_switcher', '==', '1' ),
		),

		array(
			'title'      => esc_html__( 'Widgets Area', 'eazydocs' ),
			'desc'       => esc_html__( "Enable to register a Sidebar Widgets area named 'Doc Right Sidebar' in Appearance > Widgets.", 'eazydocs' ),
			'id'         => 'is_widget_sidebar',
			'type'       => 'switcher',
			'text_on'    => esc_html__( 'Enabled', 'eazydocs' ),
			'text_off'   => esc_html__( 'Disabled', 'eazydocs' ),
			'text_width' => 92,
			'default'    => false,
			'class'      => 'eazydocs-pro-notice active-theme-docly'
		),

		// Conditional Dropdown
		array(
			'title' => esc_html__( 'Conditional Dropdown', 'eazydocs' ),
			'type'  => 'heading'
		),

		array(
			'title'      => esc_html__( 'Conditional Dropdown', 'eazydocs' ),
			'desc'       => __( 'You can display conditional contents using the [conditional_data] shortcode in documentation based on the dropdown value. See the shortcode usage tutorial <a href="https://tinyurl.com/yd46mfax" target="_blank">here</a>.', 'eazydocs' ),
			'id'         => 'is_conditional_dropdown',
			'type'       => 'switcher',
			'text_on'    => esc_html__( 'Enabled', 'eazydocs' ),
			'text_off'   => esc_html__( 'Disabled', 'eazydocs' ),
			'text_width' => 92,
			'default'    => false,
			'class'      => 'eazydocs-pro-notice active-theme'
		),

		array(
			'title'      => esc_html__( 'Dropdown Options', 'eazydocs' ),
			'id'         => 'condition_options',
			'type'       => 'repeater',
			'fields'     => array(
				array(
					'title' => esc_html__( 'Title', 'eazydocs' ),
					'id'    => 'title',
					'type'  => 'text',
				),
				array(
					'title' => esc_html__( 'Icon', 'eazydocs' ),
					'id'    => 'icon',
					'type'  => 'icon',
				),
			),
			'dependency' => array( 'is_conditional_dropdown', '==', '1' ),
			'class'      => 'eazydocs-pro-notice active-theme',
            'default'   => array(
                array(
                    'title' => esc_html__('Windows', 'eazydocs'),
                    'icon' => 'fab fa-windows',
                ),
                array(
                    'title' => esc_html__('iOs', 'eazydocs'),
                    'icon' => 'fab fa-apple',
                ),
                array(
                    'title' => esc_html__('Linux', 'eazydocs'),
                    'icon' => 'fab fa-linux',
                ),
            ),
            'button_title'     => esc_html__( 'Add New', 'eazydocs' ),
		),
	)
) );

CSF::createSection( $prefix, array(
	'id'     => 'doc_related_articles',
	'parent' => 'single_doc',
	'title'  => esc_html__( 'Related Articles', 'eazydocs' ),
	'icon'   => '',
	'fields' => array(
		array(
			'type'  => 'heading',
			'title' => esc_html__( 'Related Docs Settings', 'eazydocs' )
		),

		array(
			'id'         => 'related-docs',
			'type'       => 'switcher',
			'title'      => esc_html__( 'Related Docs', 'eazydocs' ),
			'text_on'    => esc_html__( 'Show', 'eazydocs' ),
			'text_off'   => esc_html__( 'Hide', 'eazydocs' ),
			'default'    => true,
			'text_width' => 72
		),

		array(
			'id'         => 'related-docs-title',
			'type'       => 'text',
			'title'      => esc_html__( 'Title', 'eazydocs' ),
			'default'    => esc_html__( 'Related articles', 'eazydocs' ),
			'dependency' => array(
				'related-docs',
				'==',
				'true'
			)
		),

		array(
			'id'         => 'related-visible-docs',
			'type'       => 'number',
			'title'      => esc_html__( 'Docs Number', 'eazydocs' ),
			'default'    => esc_html__( '4', 'eazydocs' ),
			'dependency' => array(
				'related-docs',
				'==',
				'true'
			)
		),

		array(
			'id'         => 'related-doc-column',
			'type'       => 'select',
			'title'      => esc_html__( 'Column Width', 'eazydocs' ),
			'options'    => [
				'6'  => esc_html__( 'Half', 'eazydocs' ),
				'12' => esc_html__( 'Fullwidth', 'eazydocs' ),
			],
			'dependency' => array(
				'related-docs',
				'==',
				'true'
			),
			'default'    => '6'
		),

		array(
			'id'         => 'related-docs-more-btn',
			'type'       => 'text',
			'title'      => esc_html__( 'Button', 'eazydocs' ),
			'default'    => esc_html__( 'See More', 'eazydocs' ),
			'dependency' => array(
				'related-docs',
				'==',
				'true'
			)
		),
	)
) );


CSF::createSection( $prefix, array(
	'id'     => 'doc_viewed_articles',
	'parent' => 'single_doc',
	'title'  => esc_html__( 'Viewed Articles', 'eazydocs' ),
	'icon'   => '',
	'fields' => array(
		array(
			'type'  => 'heading',
			'title' => esc_html__( 'Recently Viewed Docs Options', 'eazydocs' )
		),

		array(
			'id'         => 'viewed-docs',
			'type'       => 'switcher',
			'title'      => esc_html__( 'Recently Viewed Docs', 'eazydocs' ),
			'text_on'    => esc_html__( 'Show', 'eazydocs' ),
			'text_off'   => esc_html__( 'Hide', 'eazydocs' ),
			'default'    => true,
			'text_width' => 72
		),

		array(
			'id'         => 'viewed-docs-title',
			'type'       => 'text',
			'title'      => esc_html__( 'Title', 'eazydocs' ),
			'default'    => esc_html__( 'Recently Viewed articles', 'eazydocs' ),
			'dependency' => array(
				'viewed-docs',
				'==',
				'true'
			)
		),

		array(
			'id'         => 'viewed-visible-docs',
			'type'       => 'number',
			'title'      => esc_html__( 'Docs Number', 'eazydocs' ),
			'default'    => esc_html__( '4', 'eazydocs' ),
			'dependency' => array(
				'viewed-docs',
				'==',
				'true'
			)
		),

		array(
			'id'         => 'viewed-doc-column',
			'type'       => 'select',
			'title'      => esc_html__( 'Column Width', 'eazydocs' ),
			'options'    => [
				'6'  => esc_html__( 'Half', 'eazydocs' ),
				'12' => esc_html__( 'Fullwidth', 'eazydocs' ),
			],
			'dependency' => array(
				'viewed-docs',
				'==',
				'true'
			),
			'default'    => '6'
		),

		array(
			'id'         => 'view-docs-more-btn',
			'type'       => 'text',
			'title'      => esc_html__( 'Button', 'eazydocs' ),
			'default'    => esc_html__( 'See More', 'eazydocs' ),
			'dependency' => array(
				'viewed-docs',
				'==',
				'true'
			)
		),
	)
) );