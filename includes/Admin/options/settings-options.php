<?php if ( ! defined( 'ABSPATH' ) ) {
	die;
} // Cannot access directly.

//
// Set a unique slug-like ID
//
$prefix = 'eazydocs_settings';

//
// Create options
//
CSF::createOptions( $prefix, array(
	'menu_title'         => esc_html__( 'Settings', 'eazydocs' ),
	'menu_slug'          => 'eazydocs-settings',
	'show_in_customizer' => true
) );

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
			'title'      => esc_html__( 'Docs Page', 'eazydocs' ),
			'options'    => 'pages',
			'class'      => 'docs-page-wrap',
			'multiple'   => false,
			'desc'       => sprintf( wp_kses_post( __( 'Home page for docs page. Preferably use <code>[eazydocs]</code> shortcode or design your own', 'eazydocs' ) ) ),
			'query_args' => array(
				'posts_per_page' => - 1,
			),
			'chosen'     => true,
			'ajax'       => true,
		),

		array(
			'id'      => 'docs-type-slug',
			'type'    => 'text',
			'title'   => esc_html__( 'Docs Slug', 'eazydocs' ),
			'default' => esc_html__( 'docs', 'eazydocs' ),
			'desc'    => esc_html__( 'After changing the slug, go to Settings > Permalinks and click on the Save Changes button.', 'eazydocs' )
		),

		array(
			'id'          => 'brand_color',
			'type'        => 'color',
			'title'       => esc_html__( 'Frontend Brand Color', 'eazydocs' ),
			'default'     => '#4c4cf1',
			'output'      => ':root',
			'output_mode' => '--ezd_brand_color',
		)
	)
) );

//
// Docs Archive Fields
//
CSF::createSection( $prefix, array(
	'id'     => 'docs_archive',
	'title'  => esc_html__( 'Docs Archive', 'eazydocs' ),
	'icon'   => 'fas fa-plus-circle',
	'fields' => array(

        array(
            'id'         => 'docs-column',
            'type'       => 'image_select',
            'class'      => 'docs-layout-img-wrap',
            'title'      => esc_html__( 'Docs Columns', 'eazydocs' ),
            'subtitle'   => esc_html__( 'This option will set the default value of column attribute of ', 'eazydocs' ) . '<code>[eazydocs]</code> shortcode.',
            'options'    => array(
                '4' => EAZYDOCS_IMG . '/customizer/4.svg',
                '3' => EAZYDOCS_IMG . '/customizer/3.svg',
                '2' => EAZYDOCS_IMG . '/customizer/2.svg',
            ),
            'attributes' => [
                'width' => '100px'
            ],
            'default'    => '3'
        ),

		array(
			'id'      => 'docs-view-more',
			'type'    => 'text',
			'title'   => esc_html__( 'View More Button', 'eazydocs' ),
			'default' => esc_html__( 'View More', 'eazydocs' )
		),

        array(
            'id'         => 'topics_count',
            'type'       => 'switcher',
            'title'      => esc_html__( 'Topics Count', 'eazydocs' ),
            'text_on'    => esc_html__( 'Show', 'eazydocs' ),
            'text_off'   => esc_html__( 'Hide', 'eazydocs' ),
            'text_width' => 72,
            'default'    => true // or false
        ),

		array(
			'id'      => 'topics_text',
			'type'    => 'text',
			'title'   => esc_html__( 'Topics Count Text', 'eazydocs' ),
			'default' => esc_html__( 'Topics', 'eazydocs' ),
            'dependency' => array(
                array( 'topics_count', '==', 'true' )
            )
		),

		array(
			'id'        => 'docs-order',
			'type'      => 'select',
			'title'     => esc_html__( 'Child Docs Order', 'eazydocs' ),
			'options'   => array(
				'DESC'      => esc_html__( 'Descending', 'eazydocs' ),
				'ASC'       => esc_html__( 'Ascending', 'eazydocs' ),
			),
			'default' => 'ASC',
		),

		array(
			'id'       => 'docs-number',
			'type'     => 'number',
			'title'    => esc_html__( 'Number of Docs', 'eazydocs' ),
			'subtitle' => esc_html__( 'Number of Main Docs to show', 'eazydocs' ),
			'default'  => 6,
		),

		array(
			'id'        => 'show_articles',
			'type'      => 'number',
			'title'     => esc_html__( 'Number of Articles', 'eazydocs' ),
			'subtitle'  => esc_html__( 'Number of Articles to show under each Docs.', 'eazydocs' ),
			'default'   => 4,
		),

		array(
			'id'      => 'docs-archive-layout',
			'type'    => 'radio',
			'title'   => esc_html__( 'Docs Layout', 'eazydocs' ),
			'options' => array(
				'grid'    => esc_html__( 'Grid', 'eazydocs' ),
				'masonry' => esc_html__( 'Masonry', 'eazydocs' ),
			),
			'default' => 'grid',
			'class'   => 'eazydocs-pro-notice'
		),
		
		array(
			'id'      => 'docs-order-by',
			'type'    => 'select',
			'title'   => esc_html__( 'Docs Order By', 'eazydocs' ),
			'options' => array(
				'none'          => esc_html__( 'No Order', 'eazydocs' ),
				'ID'            => esc_html__( 'Post ID', 'eazydocs' ),
				'author'        => esc_html__( 'Post Author', 'eazydocs' ),
				'title'         => esc_html__( 'Title', 'eazydocs' ),
				'date'          => esc_html__( 'Date', 'eazydocs' ),
				'modified'      => esc_html__( 'Last Modified Date', 'eazydocs' ),
				'rand'          => esc_html__( 'Random', 'eazydocs' ),
				'comment_count' => esc_html__( 'Comment Count', 'eazydocs' ),
				'menu_order'    => esc_html__( 'Menu Order', 'eazydocs' ),
			),
			'default' => 'menu_order',
			'class'   => 'eazydocs-pro-notice'
		),
	)
));


//
// Single Doc Fields
//
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

        // Meta Information
        array(
            'type'       => 'subheading',
            'content'    => esc_html__( 'Meta Information', 'eazydocs' ),
            'dependency' => array(
                array( 'docs-feedback', '==', 'true' ),
                array( 'message-feedback', '==', 'true' ),
            )
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
            'id'          => 'content-bg',
            'type'        => 'color',
            'title'       => esc_html__( 'Background Color', 'eazydocs' ),
            'output'      => 'body.single-docs',
            'output_mode' => 'background-color',
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
			'title'      => esc_html__( 'Ajax Loading', 'eazydocs' ),
			'subtitle'   => esc_html__( 'Load doc pages via Ajax.', 'eazydocs' ),
			'id'         => 'is_doc_ajax',
			'type'       => 'switcher',
			'text_on'    => esc_html__( 'Enabled', 'eazydocs' ),
			'text_off'   => esc_html__( 'Disabled', 'eazydocs' ),
			'text_width' => 90,
			'default'    => false,
			'class'      => 'eazydocs-pro-notice active-theme-docy',
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

        array(
            'type'       => 'heading',
            'title'      => esc_html__( 'Excerpt', 'eazydocs' ),
        ),

        array(
            'id'        => 'is_excerpt',
            'type'      => 'switcher',
            'title'     => esc_html__( 'Show Excerpt', 'eazydocs' ),
            'subtitle'  => esc_html__( 'Show excerpt on doc single page.', 'eazydocs' ),
            'default'   => true,
        ),

        array(
            'id'        => 'excerpt_label',
            'type'      => 'text',
            'title'     => esc_html__( 'Excerpt Label', 'eazydocs' ),
            'subtitle'  => esc_html__( 'Excerpt label on doc single page.', 'eazydocs' ),
            'default'   => esc_html__( 'Summary: ', 'eazydocs' ),
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
			'id'         => 'is_search_banner',
			'type'       => 'switcher',
			'title'      => esc_html__( 'Search Banner', 'eazydocs' ),
			'text_on'    => esc_html__( 'Show', 'eazydocs' ),
			'text_off'   => esc_html__( 'Hide', 'eazydocs' ),
			'default'    => true,
			'text_width' => 72
		),
		
		array(
			'id'     => 'doc_banner_bg',
			'type'   => 'background',
			'title'  => esc_html__( 'Background', 'eazydocs' ),
			'output' => '.ezd_search_banner.has_bg_dark.has_cs_bg',
		),

        array(
            'id'       => 'search_banner_padding',
            'type'     => 'spacing',
            'title'    => esc_html__( 'Padding', 'eazydocs'),
            'output'   => '.ezd_search_banner',
            'default'  => array(
                'unit'   => 'px',
            ),
        ),

        //Search Keywords
        array(
			'type'  => 'subheading',
			'title' => esc_html__( 'Search Keywords', 'eazydocs' ),
		),
		
		array(
			'id'         => 'is_keywords',
			'type'       => 'switcher',
			'title'      => esc_html__( 'Keywords', 'eazydocs' ),
			'text_on'    => esc_html__( 'Enabled', 'eazydocs' ),
			'text_off'   => esc_html__( 'Disabled', 'eazydocs' ),
			'default'    => false,
			'text_width' => 90,
			'dependency' => array( 'is_search_banner', '==', 'true' ),
			'class'      => 'eazydocs-pro-notice'
		),
		
		array(
			'id'         => 'keywords_label',
			'type'       => 'text',
			'title'      => esc_html__( 'Keywords Label', 'eazydocs' ),
			'default'    => esc_html__( 'Popular Searches', 'eazydocs' ),
			'dependency' => array(
				array( 'is_search_banner', '==', 'true' ),
				array( 'is_keywords', '==', 'true' ),
			),
			'class'      => 'eazydocs-pro-notice'
		),
		
		array(
			'id'          => 'keywords_label_color',
			'type'        => 'color',
			'title'       => esc_html__( 'Label Color', 'eazydocs' ),
			'output_mode' => 'color',
			'output'      => '.ezd_search_keywords .label',
            'dependency' => array(
                array( 'is_search_banner', '==', 'true' ),
                array( 'is_keywords', '==', 'true' ),
            ),
			'class'       => 'eazydocs-pro-notice'
		),
		
		array(
			'id'         => 'keywords',
			'type'       => 'repeater',
			'title'      => esc_html__( 'Keywords', 'eazydocs' ),
			'dependency' => array(
				array( 'is_search_banner', '==', 'true' ),
				array( 'is_keywords', '==', 'true' ),
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
					'title' => 'Keyword #1',
				),
				array(
					'title' => 'Keyword #2',
				),
			),
			'class'      => 'eazydocs-pro-notice'
		),
		
		array(
			'id'          => 'keywords_color',
			'type'        => 'color',
			'title'       => esc_html__( 'Keywords Color', 'eazydocs' ),
			'output_mode' => 'color',
			'output'      => '.ezd_search_banner .header_search_keyword ul li a',
            'dependency' => array(
                array( 'is_search_banner', '==', 'true' ),
                array( 'is_keywords', '==', 'true' ),
            ),
			'class'       => 'eazydocs-pro-notice'
		)
	)
));

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
			'id'    => 'docs-page-title',
			'type'  => 'text',
			'title' => esc_html__( 'Docs Archive Page Title', 'eazydocs' ),
            'default' => 'Docs',
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
	'title'  => esc_html__( 'Feedback Area', 'eazydocs' ),
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
			'content'    => esc_html__( 'Feedback Area', 'eazydocs' ),
			'dependency' => array(
				'docs-feedback',
				'==',
				'true'
			),
			'subtitle'   => esc_html__( 'Customize the feedback modal form here.', 'eazydocs' )
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
			'type'       => 'subheading',
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
		)
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
            'id'      	=> 'docs_content_layout',
            'type'    	=> 'radio',
            'title'   	=> esc_html__( 'Docs Navigation Layout', 'eazydocs' ),
            'options' 	=> [
                'category_base'  => esc_html__( 'Collapsed with Icons', 'eazydocs' ),
                'badge_base'     => esc_html__( 'Extended Docs', 'eazydocs' ),
            ],
            'default' 	=> 'badge_base',
            'class'   	=> 'eazydocs-pro-notice',
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
            'title'      => esc_html__( 'Filter', 'eazydocs' ),
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
			'subtitle' => esc_html__( "This is the Doc's default icon. If you don't use icon for the article section individually, this icon will be shown.", 'eazydocs' ),
			'id'       => 'doc_sec_icon',
			'type'     => 'media',
			'default'  => array(
				'url' => EAZYDOCS_IMG . '/icon/folder-closed.png'
			),
			'class'    => 'eazydocs-pro-notice active-theme'
		),
		
		array(
			'title'    => esc_html__( 'Doc Section Icon Open', 'eazydocs' ),
			'subtitle' => esc_html__( "This is the Doc's default icon. If you don't use icon for the article section individually, this icon will be shown on open states of the Doc sections.", 'eazydocs' ),
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
            'type'       => 'heading',
            'title'      => esc_html__( 'Sharing Doc', 'eazydocs' ),
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
            'default'    => esc_html__( 'Share this Doc', 'eazydocs-pro' ),
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
			'type'       => 'heading',
			'title'      => esc_html__( 'Tools', 'eazydocs' ),
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
            'type'       => 'heading',
            'title'      => esc_html__( 'Features', 'eazydocs' ),
        ),

        array(
            'title'      => esc_html__( 'Dark Mode Switcher', 'eazydocs' ),
            'id'         => 'is_dark_switcher',
            'type'       => 'switcher',
            'text_on'    => esc_html__( 'Enabled', 'eazydocs' ),
            'text_off'   => esc_html__( 'Disabled', 'eazydocs' ),
            'text_width' => 92,
            'default'    => false,
            'class'      => 'eazydocs-pro-notice active-theme-docly'
        ),

        array(
            'id'         => 'toc_switcher',
            'type'       => 'switcher',
            'title'      => esc_html__( 'Table on Contents (TOC)', 'eazydocs' ),
            'subtitle'   => esc_html__( 'EazyDocs will automatically create a structured Table Of Contents(TOC) while you are writing your documentation.', 'eazydocs' ),
            'default'    => true,
        ),

        array(
            'id'         => 'toc_heading',
            'type'       => 'text',
            'title'      => esc_html__( 'TOC Heading', 'eazydocs' ),
            'default'    => esc_html__( 'CONTENTS', 'eazydocs' ),
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
            'title' => esc_html__('Conditional Dropdown', 'eazydocs'),
            'type' => 'heading'
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
            'class'      => 'eazydocs-pro-notice active-theme'
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
			'text_on'    => esc_html__( 'Show', 'eazydocs-pr' ),
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

// Private Doc
CSF::createSection( $prefix, array(
	'id'     => 'private_doc_settings',
	'parent' => 'single_doc',
	'title'  => esc_html__( 'Private Doc', 'eazydocs' ),
	'icon'   => '',
	'fields' => array(
		
		array(
			'id'    => 'private_doc_visibility',
			'type'  => 'heading',
			'title' => esc_html__( 'Private Doc', 'eazydocs' )
		),
		array(
			'id'         => 'private_doc_mode',
			'type'       => 'select',
			'title'      => esc_html__( 'Visibility Mode', 'eazydocs' ),
			'options'	 => [
				'login'	 =>  esc_html__( 'Login Required', 'eazydocs' ),
				'none'	 =>  esc_html__( 'None', 'eazydocs' ),
			],
			'default'    => 'none',
			'class'      => 'eazydocs-pro-notice'
		),
		array(
			'id'         => 'private_doc_login_page',
			'type'       => 'select',
			'placeholder' => 'Select page',
			'title'      => esc_html__( 'Select Page', 'eazydocs' ),
			'subtitle'   => esc_html__( 'Select Doc login page', 'eazydocs' ),
			'desc'		 => esc_html__( 'If you want to change this page, use this shortcode [ezd_login_form] to display the login form on your desired page.', 'eazydocs' ),
			'options'	 => 'pages',
			'class'      => 'eazydocs-pro-notice',
			'dependency' => array(
				array( 'private_doc_mode', '==', 'login' ),
			),
			'query_args' => array(
				'posts_per_page' => -1,
			),
			'chosen'     => true,
			'ajax'       => true,
		)
		
	)
) );

// Protected Doc
CSF::createSection( $prefix, array(
	'id'     => 'protected_doc_settings',
	'parent' => 'single_doc',
	'title'  => esc_html__( 'Protected Doc', 'eazydocs' ),
	'icon'   => '',
	'fields' => array(
		
		array(
			'id'    => 'protected_doc_heading',
			'type'  => 'heading',
			'title' => esc_html__( 'Protected Doc', 'eazydocs' )
		),
		array(
			'id'         => 'protected_doc_form',
			'type'       => 'select',
			'title'      => esc_html__( 'Password Form', 'eazydocs' ),
			'options'	 => [
				'eazydocs-form'	 	=>  esc_html__( 'EazyDocs Form', 'eazydocs' ),
				'default'	 		=>  esc_html__( 'Default', 'eazydocs' ),
			],
			'default'    => 'eazydocs-form'
		),
		array(
			'id'    => 'protected_doc_form_info',
			'type'  => 'subheading',
			'title' => esc_html__( 'Form', 'eazydocs' ),
			'dependency' => array(
				array( 'protected_doc_form', '==', 'eazydocs-form' ),
			)
		),
		array(
			'id'         => 'protected_form_head_color',
			'type'       => 'color',
			'title'      => esc_html__( 'Header Color', 'eazydocs' ),
			'dependency' => array(
				array( 'protected_doc_form', '==', 'eazydocs-form' ),
			),
			'output'      => '.ezd-password-wrap .ezd-password-head',
			'output_mode' => 'background-color',
		),
		array(
			'id'         => 'protected_form_title',
			'type'       => 'text',
			'title'      => esc_html__( 'Title', 'eazydocs' ),
			'dependency' => array(
				array( 'protected_doc_form', '==', 'eazydocs-form' ),
			)
		),
		array(
			'id'         => 'protected_form_title_color',
			'type'       => 'color',
			'title'      => esc_html__( 'Title Color', 'eazydocs' ),
			'dependency' => array(
				array( 'protected_doc_form', '==', 'eazydocs-form' ),
			),
			'output'      => '.ezd-password-wrap .ezd-password-head p.ezd-password-title',
			'output_mode' => 'color',
		),
		array(
			'id'         => 'protected_form_subtitle',
			'type'       => 'text',
			'title'      => esc_html__( 'Sub Title', 'eazydocs' ),
			'dependency' => array(
				array( 'protected_doc_form', '==', 'eazydocs-form' ),
			)
		),
		array(
			'id'         => 'protected_form_subtitle_color',
			'type'       => 'color',
			'title'      => esc_html__( 'Sub Title Color', 'eazydocs' ),
			'dependency' => array(
				array( 'protected_doc_form', '==', 'eazydocs-form' ),
			),
			'output'      => '.ezd-password-wrap .ezd-password-head p.ezd-password-subtitle',
			'output_mode' => 'color',
		),
		array(
			'id'         => 'protected_form_btn',
			'type'       => 'text',
			'title'      => esc_html__( 'Button', 'eazydocs' ),
			'dependency' => array(
				array( 'protected_doc_form', '==', 'eazydocs-form' ),
			)
		),
		array(
			'id'         => 'protected_form_btn_bgcolor',
			'type'       => 'color',
			'title'      => esc_html__( 'Button Text Color', 'eazydocs' ),
			'dependency' => array(
				array( 'protected_doc_form', '==', 'eazydocs-form' ),
			),
			'output'      => '.ezd-password-wrap .ezd-password-body form button',
			'output_mode' => 'color',
		),
		array(
			'id'         => 'protected_form_btn_textcolor',
			'type'       => 'color',
			'title'      => esc_html__( 'Button Background Color', 'eazydocs' ),
			'dependency' => array(
				array( 'protected_doc_form', '==', 'eazydocs-form' ),
			),
			'output'      => '.ezd-password-wrap .ezd-password-body form button',
			'output_mode' => 'background-color',
		),
		
	)
) );


// OnePage Doc
CSF::createSection( $prefix, array(
	'id'     => 'ezd-onepage-docs',
	'title'  => esc_html__( 'OnePage Doc', 'eazydocs' ),
	'class'    => 'eazydocs-pro-notice',
	'icon'   => 'fas fa-plus-circle',
	'fields' => [
		array(
			'id'       => 'onepage_layout',
			'type'     => 'select',
			'title'    => esc_html__( 'Layout', 'eazydocs' ),
			'options'  => [
				'main'              => __( 'Classic OnePage Doc', 'eazydocs' ),
				'fullscreen-layout' => __( 'Fullscreen OnePage Doc', 'eazydocs' ),
			],
			'default'  => 'main',
		),
		array(
			'id'         => 'onepage_numbering',
			'type'       => 'switcher',
			'title'      => esc_html__( 'Numbering', 'eazydocs' ),
			'text_on'    => esc_html__( 'Show', 'eazydocs' ),
			'text_off'   => esc_html__( 'Hide', 'eazydocs' ),
			'text_width' => 70,
			'default'    => false
		)
	]
) );

// Select docs page
$options      = get_option( 'eazydocs_settings' );
$doc_id       = $options['docs-slug'] ?? '';
$doc_page     = get_post_field( 'post_name', $doc_id );
$args         = array(
	'post_type'      => 'docs',
	'posts_per_page' => - 1,
	'orderby'        => 'menu_order',
	'order'          => 'asc'
);
$recent_posts = wp_get_recent_posts( $args );
$post_url     = '';
$post_count   = 0;
foreach ( $recent_posts as $recent ):
	$post_url = $recent['ID'];
	$post_count ++;
endforeach;
$docs_url = $post_count > 0 ? $post_url : $doc_id;

$archive_url = admin_url( 'customize.php?url=' ) . site_url( '/' ) . '?p=' . $doc_id . '?autofocus[panel]=docs-page&autofocus[section]=docs-archive-page';
$single_url  = admin_url( 'customize.php?url=' ) . site_url( '/' ) . '?p=' . $docs_url . '?autofocus[panel]=docs-page&autofocus[section]=docs-single-page';

CSF::createSection( $prefix, array(
	'id'     => 'design_fields',
	'title'  => esc_html__( 'Customizer', 'eazydocs' ),
	'icon'   => 'fas fa-plus-circle',
	'fields' => [
		
		array(
			'type'    => 'content',
			'content' => sprintf( '<a href="' . $archive_url . '" target="_blank" id="get_docs_archive">' . esc_html__( 'Docs Archive', 'eazydocs' ) . '</a> <a href="' . $single_url . '" target="_blank" id="get_docs_single">' . esc_html__( 'Single Doc', 'eazydocs' ) . '</a>' ),
		),
		
	]
) );

//
// Shortcode Fields
//
CSF::createSection( $prefix, array(
	'id'     => 'shortcode_fields',
	'title'  => esc_html__( 'Docs Shortcodes', 'eazydocs' ),
	'icon'   => 'fas fa-plus-circle',
	'fields' => [

		array(
			'id'         => 'eazydocs_docs_shortcode',
			'type'       => 'text',
			'title'      => esc_html__( 'Docs archive', 'eazydocs' ),
            'subtitle'       => sprintf(
                __( 'Use this shortcode to display the Docs. Learn more about the shortcode and the attributes %s here %s.', 'eazydocs' ),
                '<a href="https://tinyurl.com/24zm4oj3" target="_blank">', '</a>'
            ),
            'desc'       => esc_html__('See the shortcode with the available attributes', 'eazydocs' ).'<br><code>[eazydocs col="3" include="" exclude="" show_docs="" show_articles="" more="View More"]</code>',
            'default'    => '[eazydocs]',
            'attributes' => array(
                'readonly' => 'readonly',
            ),
		),

		array(
			'id'         => 'conditional_data_shortcode',
			'type'       => 'text',
			'title'      => esc_html__( 'Conditional Dropdown', 'eazydocs' ),
			'desc'       => __( 'Know the usage of this shortcode <a href="https://tinyurl.com/24d9rw72" target="_blank"> here </a>', 'eazydocs' ),
			'default'    => '[conditional_data]',
			'attributes' => array(
				'readonly' => 'readonly',
			),
		),
		array(
            'id'         => 'ezdocs_login_shortcode',
            'type'       => 'text',
            'title'      => esc_html__( 'Docs Login', 'eazydocs' ),
            'subtitle'       => esc_html__( 'Use this shortcode to display login form.', 'eazydocs' ),
            'desc'       => esc_html__('See the shortcode with the available attributes', 'eazydocs' ).'<br><code>[ezd_login_form login_title="You must log in to continue."  login_subtitle="Login to '.get_bloginfo().'" login_btn="Log In" login_forgot_btn="Forgotten account?"]</code>',
            'default'    => '[ezd_login_form]',
            'attributes' => array(
                'readonly' => 'readonly',
			),
			'class'      => 'eazydocs-pro-notice'
        )
	]
) );


//
// Docs Contribution
//
CSF::createSection( $prefix, array(
    'id'     => 'contributor_fields',
    'title'  => esc_html__( 'Docs Contribution', 'eazydocs' ),
    'icon'   => 'fas fa-plus-circle',
    'fields' => [
        array(
            'id'         => 'is_doc_contribution',
            'type'       => 'switcher',
            'title'      => esc_html__( 'Contribution Feature', 'eazydocs' ),
            'subtitle'   => esc_html__( 'Contribution buttons on the doc Right Sidebar.', 'eazydocs' ),
            'desc'       => esc_html__( 'By enabling this feature, you are allowing other people to contribute the docs. This will also let you manage the contributors from the Doc post editor.', 'eazydocs' ),
            'text_on'    => esc_html__( 'Enabled', 'eazydocs' ),
            'text_off'   => esc_html__( 'Disabled', 'eazydocs' ),
            'text_width' => 92,
            'default'    => false,
			'class'      => 'eazydocs-promax-notice'
        ),
        array(
            'id'    => 'ezd_add_doc_heading',
            'type'  => 'heading',
            'title' => esc_html__( 'Add Doc', 'eazydocs' ),
			'dependency' => array(
				array( 'is_doc_contribution', '==', 'true' ),
			)
			
        ),
        array(
            'id'         => 'frontend_add_switcher',
            'type'       => 'switcher',
            'title'      => esc_html__( 'Add Button', 'eazydocs' ),
            'text_on'    => esc_html__( 'Show', 'eazydocs' ),
            'text_off'   => esc_html__( 'Hide', 'eazydocs' ),
            'text_width' => 72,
            'default'    => false,
			'class'      => 'eazydocs-promax-notice',
			'dependency' => array(
				array( 'is_doc_contribution', '==', 'true' )
			)
        ),
        array(
            'id'         => 'frontend_add_btn_text',
            'type'       => 'text',
            'title'      => esc_html__( 'Button', 'eazydocs' ),
            'default'	 => esc_html__( 'Add Doc', 'eazydocs' ),
			'class'      => 'eazydocs-promax-notice',
			'dependency' => array(
				array( 'is_doc_contribution', '==', 'true' ),
				array( 'frontend_add_switcher', '==', 'true' ),
			)
        ),

        array(
            'id'    => 'frontend_edit_doc',
            'type'  => 'heading',
            'title' => esc_html__( 'Edit Doc', 'eazydocs' ),
			'dependency' => array(
				array( 'is_doc_contribution', '==', 'true' )
			)
        ),
        array(
            'id'         => 'frontend_edit_switcher',
            'type'       => 'switcher',
            'title'      => esc_html__( 'Edit Button', 'eazydocs' ),
            'text_on'    => esc_html__( 'Show', 'eazydocs' ),
            'text_off'   => esc_html__( 'Hide', 'eazydocs' ),
            'text_width' => 72,
            'default'    => false,
			'class'      => 'eazydocs-promax-notice',
			'dependency' => array(
				array( 'is_doc_contribution', '==', 'true' ),
			)
        ),
        array(
            'id'         => 'frontend_edit_btn_text',
            'type'       => 'text',
            'title'      => esc_html__( 'Button', 'eazydocs' ),
            'default'	 => esc_html__( 'Edit Doc', 'eazydocs' ),
			'class'      => 'eazydocs-promax-notice',
			'dependency' => array(
				array( 'is_doc_contribution', '==', 'true' ),
				array( 'frontend_edit_switcher', '==', 'true' ),
			)
        ),
		
        array(
            'id'         => 'docs_frontend_login_page',
            'type'       => 'select',
            'placeholder' => 'Select page',
            'title'      => esc_html__( 'Login Page', 'eazydocs' ),
            'subtitle'   => esc_html__( 'Select Doc login page', 'eazydocs' ),
			'desc'		 => esc_html__( 'If you want to change this page, use this shortcode [ezd_login_form] to display the login form on your desired page.', 'eazydocs' ),
            'options'	 => 'pages',
			'class'      => 'eazydocs-promax-notice',
			'dependency' => array(
				array( 'is_doc_contribution', '==', 'true' )
			)
        ),
		array(
            'id'    => 'docs_contributor_meta',
            'type'  => 'heading',
            'title' => esc_html__( 'Meta Content', 'eazydocs' )	
        ),
		array(
			'id'         => 'contributor_meta_visibility',
			'type'       => 'switcher',
			'title'      => esc_html__( 'Enable / Disable', 'eazydocs' ),
			'text_on'    => esc_html__( 'Show', 'eazydocs' ),
			'text_off'   => esc_html__( 'Hide', 'eazydocs' ),
			'class'      => 'eazydocs-promax-notice',
			'text_width' => 70,
			'default'	 => false
		),
		array(
            'id'    	=> 'contributor_meta_title',
            'type'  	=> 'text', 
            'title' 	=> esc_html__( 'Title', 'eazydocs' ),
            'default' 	=> esc_html__( 'Contributors', 'eazydocs' ),
			'class'      => 'eazydocs-promax-notice'
        ),
		array(
            'id'    	=> 'contributor_meta_dropdown_title',
            'type'  	=> 'text', 
            'title' 	=> esc_html__( 'Dropdown Heading', 'eazydocs' ),
            'default' 	=> esc_html__( 'Manage Contributors', 'eazydocs' ),
			'class'      => 'eazydocs-promax-notice'
        ),
		array(
			'id'         => 'contributor_meta_search',
			'type'       => 'switcher',
			'title'      => esc_html__( 'Search', 'eazydocs' ),
			'text_on'    => esc_html__( 'Show', 'eazydocs' ),
			'text_off'   => esc_html__( 'Hide', 'eazydocs' ),
			'class'      => 'eazydocs-promax-notice',
			'text_width' => 70,
			'default'	 => false
		)
    ]
));


//
// Shortcode Fields
//
CSF::createSection( $prefix, array(
	'id'     => 'role_manager_fields',
	'title'  => esc_html__( 'Docs Role Manager', 'eazydocs' ),
	'icon'   => 'fas fa-plus-circle',
	'fields' => [
		array(
			'id'       => 'docs-write-access',
			'type'     => 'select',
			'title'    => esc_html__( 'Who Can View Docs?', 'eazydocs' ),
			'subtitle'    => esc_html__( 'Allow users to view Docs.', 'eazydocs' ),
			'options'  => [
				'administrator' => __( 'Administrator', 'eazydocs' ),
				'editor'        => __( 'Editor', 'eazydocs' ),
				'author'        => __( 'Author', 'eazydocs' ),
				'contributor'   => __( 'Contributor', 'eazydocs' ),
				'subscriber'    => __( 'Subscriber', 'eazydocs' ),
			],
			'chosen'   => true,
			'multiple' => true,
			'default'  => 'administrator',
			'class'    => 'eazydocs-pro-notice'
		),
		
		array(
			'id'       => 'settings-edit-access',
			'type'     => 'select',
			'title'    => esc_html__( 'Who Can Edit Settings?', 'eazydocs' ),
			'subtitle'    => esc_html__( 'Allow users to update options from settings.', 'eazydocs' ),
			'options'  => [
				'administrator' => __( 'Administrator', 'eazydocs' ),
				'editor'        => __( 'Editor', 'eazydocs' ),
				'author'        => __( 'Author', 'eazydocs' ),
			],
			'chosen'   => true,
			'default'  => 'administrator',
			'multiple' => true,
			'class'    => 'eazydocs-pro-notice'
		),
		
		array(
			'id'       => 'customizer-edit-access',
			'type'     => 'select',
			'title'    => esc_html__( 'Who Can Edit Customizer?', 'eazydocs' ),
			'subtitle'    => esc_html__( 'Allow users to customize Docs from customizer settings.', 'eazydocs' ),
			'options'  => [
				'administrator' => __( 'Administrator', 'eazydocs' ),
				'editor'        => __( 'Editor', 'eazydocs' ),
				'author'        => __( 'Author', 'eazydocs' ),
			],
			'chosen'   => true,
			'default'  => 'administrator',
			'multiple' => true,
			'class'    => 'eazydocs-pro-notice'
		),
	]
) );

//
// Instant Answer
//
CSF::createSection( $prefix, array(
	'id'     => 'eazydocs_instant_answer',
	'title'  => esc_html__( 'Docs Assistant', 'eazydocs' ),
	'icon'   => 'fas fa-plus-circle',
	'fields' => [
		array(
			'type'  => 'heading',
			'title' => esc_html__( 'Assistant Settings', 'eazydocs' ),
		),

		array(
			'id'         => 'assistant_visibility',
			'type'       => 'switcher',
			'title'      => esc_html__( 'Docs Assistant', 'eazydocs' ),
			'text_on'    => esc_html__( 'Enabled', 'eazydocs' ),
			'text_off'   => esc_html__( 'Disabled', 'eazydocs' ),
			'class'      => 'eazydocs-pro-notice',
			'text_width' => 92,
			'default'    => false
		),

		array(
			'id'             => 'assistant_open_icon',
			'type'           => 'media',
			'title'          => esc_html__( 'Open Icon', 'eazydocs' ),
			'library'        => 'image',
			'url'            => false,
			'preview_width'  => '60',
			'preview_height' => '60',
			'class'          => 'eazydocs-pro-notice',
			'dependency'     => array(
				array( 'assistant_visibility', '==', 'true' )
			)
		),

		array(
			'id'             => 'assistant_close_icon',
			'type'           => 'media',
			'title'          => esc_html__( 'Close Icon', 'eazydocs' ),
			'library'        => 'image',
			'class'          => 'eazydocs-pro-notice',
			'url'            => false,
			'preview_width'  => '60',
			'preview_height' => '60',
			'dependency'     => array(
				array( 'assistant_visibility', '==', 'true' )
			)
		),

		array(
			'id'         => 'assistant_tab_settings',
			'type'       => 'tabbed',
			'class'      => 'eazydocs-pro-notice',
			'title'      => 'Tab Settings',
			'dependency' => array(
				array( 'assistant_visibility', '==', 'true' )
			),
			'tabs'       => array(
				array(
					'title'  => 'Knowledge Base',
					'fields' => array(
						array(
							'id'    => 'assistant_kb_heading',
							'type'  => 'heading',
							'title' => esc_html__( 'Knowledge Base Options', 'eazydocs' ),
						),
						
						array(
							'id'         => 'kb_visibility',
							'type'       => 'switcher',
							'title'      => esc_html__( 'Knowledge-base Tab', 'eazydocs' ),
							'text_on'    => esc_html__( 'Show', 'eazydocs' ),
							'text_off'   => esc_html__( 'Hide', 'eazydocs' ),
							'text_width' => 70,
							'default'    => true
						),

						array(
							'id'         => 'kb_label',
							'type'       => 'text',
							'title'      => esc_html__( 'Heading', 'eazydocs' ),
							'default'    => esc_html__( 'Knowledge Base', 'eazydocs' ),
							'dependency' => array(
								array( 'kb_visibility', '==', 'true' ),
							)
						),

						array(
							'id'         => 'assistant_search',
							'type'       => 'switcher',
							'title'      => esc_html__( 'Search', 'eazydocs' ),
							'text_on'    => esc_html__( 'Show', 'eazydocs' ),
							'text_off'   => esc_html__( 'Hide', 'eazydocs' ),
							'text_width' => 70,
							'default'    => false,
							'dependency' => array(
								array( 'kb_visibility', '==', 'true' )
							)
						),

						array(
							'id'         => 'kb_search_placeholder',
							'type'       => 'text',
							'title'      => esc_html__( 'Search Placeholder', 'eazydocs' ),
							'default'    => esc_html__( 'Search...', 'eazydocs' ),
							'dependency' => array(
								array( 'assistant_search', '==', 'true' ),
								array( 'kb_visibility', '==', 'true' )
							)
						),

						array(
							'id'         => 'assistant_breadcrumb',
							'type'       => 'switcher',
							'title'      => esc_html__( 'Breadcrumb', 'eazydocs' ),
							'text_on'    => esc_html__( 'Show', 'eazydocs' ),
							'text_off'   => esc_html__( 'Hide', 'eazydocs' ),
							'text_width' => 70,
							'default'    => false,
							'dependency' => array(
								array( 'kb_visibility', '==', 'true' )
							)
						),

						array(
							'id'         => 'docs_not_found',
							'type'       => 'text',
							'title'      => esc_html__( 'Docs not Found', 'eazydocs' ),
							'default'    => esc_html__( 'Docs not Found', 'eazydocs' ),
							'dependency' => array(
								array( 'kb_visibility', '==', 'true' )
							)
						)

					)
				),

				array(
					'title'  => 'Contact',
					'fields' => array(
						array(
							'id'         => 'contact_visibility',
							'type'       => 'switcher',
							'title'      => esc_html__( 'Contact Tab', 'eazydocs' ),
							'text_on'    => esc_html__( 'Show', 'eazydocs' ),
							'text_off'   => esc_html__( 'Hide', 'eazydocs' ),
							'text_width' => 70,
							'default'    => true
						),

						array(
							'id'         => 'contact_label',
							'type'       => 'text',
							'title'      => esc_html__( 'Heading', 'eazydocs' ),
							'default'    => esc_html__( 'Contact', 'eazydocs' ),
							'dependency' => array(
								array( 'contact_visibility', '==', 'true' ),
							)
						),

						array(
							'id'         => 'assistant_contact_mail',
							'type'       => 'text',
							'title'      => esc_html__( 'Receiver Email', 'eazydocs' ),
							'default'    => get_option( 'admin_email' ),
							'validate'   => 'csf_validate_email',
							'dependency' => array(
								array( 'contact_visibility', '==', 'true' ),
							)
						),

						array(
							'type'       => 'subheading',
							'title'      => esc_html__( 'Form Input', 'eazydocs' ),
							'dependency' => array(
								array( 'contact_visibility', '==', 'true' ),
							)
						),
						
						array(
							'id'         => 'contact_fullname',
							'type'       => 'text',
							'title'      => esc_html__( 'Full name', 'eazydocs' ),
							'default'    => esc_html__( 'Full name', 'eazydocs' ),
							'dependency' => array(
								array( 'contact_visibility', '==', 'true' ),
							)
						),
						
						array(
							'id'         => 'contact_mail',
							'type'       => 'text',
							'title'      => esc_html__( 'Email', 'eazydocs' ),
							'default'    => esc_html__( 'name@example.com', 'eazydocs' ),
							'dependency' => array(
								array( 'contact_visibility', '==', 'true' ),
							)
						),
						
						array(
							'id'         => 'contact_subject',
							'type'       => 'text',
							'title'      => esc_html__( 'Subject', 'eazydocs' ),
							'default'    => esc_html__( 'Subject', 'eazydocs' ),
							'dependency' => array(
								array( 'contact_visibility', '==', 'true' ),
							)
						),
						
						array(
							'id'         => 'contact_message',
							'type'       => 'text',
							'title'      => esc_html__( 'Message', 'eazydocs' ),
							'default'    => esc_html__( 'Write Your Message', 'eazydocs' ),
							'dependency' => array(
								array( 'contact_visibility', '==', 'true' ),
							)
						),
						
						array(
							'id'         => 'contact_submit',
							'type'       => 'text',
							'title'      => esc_html__( 'Submit Button', 'eazydocs' ),
							'default'    => esc_html__( 'Send Message', 'eazydocs' ),
							'dependency' => array(
								array( 'contact_visibility', '==', 'true' ),
							)
						),

					)
				),

				array(
					'title'  => 'Color',
					'fields' => array(
						array(
							'id'    => 'assistant_color_heading',
							'type'  => 'heading',
							'title' => esc_html__( 'Color', 'eazydocs' ),
						),
						
						array(
							'id'          => 'assistant_bg',
							'type'        => 'color',
							'title'       => esc_html__( 'Icon Color', 'eazydocs' ),
							'output_mode' => 'background-color',
						),
						
						array(
							'id'          => 'assistant_header_bg',
							'type'        => 'color',
							'title'       => esc_html__( 'Header Background', 'eazydocs' ),
							'output_mode' => 'background-color',
						),
						
						array(
							'id'          => 'assistant_body_bg',
							'type'        => 'color',
							'title'       => esc_html__( 'Background', 'eazydocs' ),
							'output'      => '.chatbox-body',
							'output_mode' => 'background-color',
						),
						
						array(
							'id'          => 'assistant_submit_bg',
							'type'        => 'color',
							'title'       => esc_html__( 'Submit Button', 'eazydocs' ),
							'output'      => '.chatbox-form input[type="submit"]',
							'output_mode' => 'background-color',
						)
					)
				),

				array(
					'title'  => 'Position',
					'fields' => array(

						array(
							'id'    => 'assistant_position_heading',
							'type'  => 'heading',
							'title' => esc_html__( 'Position', 'eazydocs' ),
						),
						
						array(
							'id'          => 'assistant_spacing_vertical',
							'type'        => 'slider',
							'title'       => 'Vertical Position',
							'min'         => 0,
							'max'         => 54,
							'step'        => 1,
							'unit'        => '%',
							'output'      => '.chat-toggle,.chatbox-wrapper',
							'output_mode' => 'margin-bottom'
						),
						
						array(
							'id'          => 'assistant_spacing_horizontal',
							'type'        => 'slider',
							'title'       => 'Horizontal Position',
							'min'         => 0,
							'max'         => 94,
							'step'        => 1,
							'unit'        => '%',
							'output'      => '.chat-toggle,.chatbox-wrapper',
							'output_mode' => 'margin-right'
						)
					)
				),

			)
		)

	]
) );