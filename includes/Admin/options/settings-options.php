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
	'title'  => esc_html__( 'General', 'eazydocs' ),
	'icon'   => 'fas fa-plus-circle',
	'fields' => array(
		array(
			'id'         => 'docs-slug',
			'type'       => 'select',
			'title'      => esc_html__( 'Docs Page', 'eazydocs-pro' ),
			'options'    => 'pages',
			'class'      => 'docs-page-wrap',
			'multiple'   => false,
			'desc'       => sprintf( wp_kses_post( __( 'Home page for docs page. Preferably use <code>[eazydocs]</code> shortcode or design your own', 'eazydocs-pro' ) ) ),
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
			'desc'    => esc_html__( 'You can change the doc post type slug from here. The default slug is docs. After changing the slug, go to Settings > Permalinks and click on the Save Changes button.', 'eazydocs' )
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
			'title'      => esc_html__( 'Select Layout', 'eazydocs-pro' ),
			'options'    => array(
				'3' => EAZYDOCS_IMG . '/customizer/4.svg',
				'4' => EAZYDOCS_IMG . '/customizer/3.svg',
				'6' => EAZYDOCS_IMG . '/customizer/2.svg',
			),
			'attributes' => [
				'width' => '100px'
			],
			'default'    => '4'
		),
		array(
			'id'      => 'docs-view-more',
			'type'    => 'text',
			'title'   => esc_html__( 'View More Button', 'eazydocs-pro' ),
			'default' => esc_html__( 'View More', 'eazydocs-pro' )
		),

		array(
			'id'      => 'docs-order',
			'type'    => 'select',
			'title'   => esc_html__( 'Docs Order', 'eazydocs' ),
			'options' => array(
				'desc' => esc_html__( 'Descending', 'eazydocs' ),
				'asc'  => esc_html__( 'Ascending', 'eazydocs' ),
			),
			'default' => 'desc',
		),

		array(
			'id'       => 'docs-number',
			'type'     => 'text',
			'title'    => esc_html__( 'Number of Docs', 'eazydocs' ),
			'subtitle' => esc_html__( 'Number of Docs to show', 'eazydocs' ),
			'default'  => 5,
		),

		array(
			'id'      => 'articles_number',
			'type'    => 'text',
			'title'   => esc_html__( 'Number of Articles', 'eazydocs' ),
			'desc'    => esc_html__( 'Number of Articles to show under each Docs.', 'eazydocs' ),
			'default' => 4,
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
) );

//
// Single Doc Fields
//
CSF::createSection( $prefix, array(
	'id'    => 'single_doc',
	'title' => esc_html__( 'Single Doc', 'eazydocs' ),
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
			'class'   => 'single-layout-img-wrap eazydocs-pro-notice',
			'title'   => esc_html__( 'Select Layout', 'eazydocs' ),
			'options' => array(
				'both_sidebar'  => EAZYDOCS_IMG . '/customizer/both_sidebar.jpg',
				'left_sidebar'  => EAZYDOCS_IMG . '/customizer/sidebar_left.jpg',
				'right_sidebar' => EAZYDOCS_IMG . '/customizer/sidebar_right.jpg',
			),
			'default' => 'both_sidebar'
		),
		array(
			'id'      => 'docs_page_width',
			'type'    => 'select',
			'title'   => esc_html__( 'Page Layout', 'eazydocs-pro' ),
			'options' => [
				'boxed'      => esc_html__( 'Boxed', 'eazydocs-pro' ),
				'full-width' => esc_html__( 'Full Width', 'eazydocs-pro' ),
			],
			'default' => 'boxed'
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
			'id'         => 'enable-comment',
			'type'       => 'switcher',
			'title'      => esc_html__( 'Comment', 'eazydocs' ),
			'text_on'    => esc_html__( 'Enabled', 'eazydocs' ),
			'text_off'   => esc_html__( 'Disabled', 'eazydocs' ),
			'text_width' => 90,
			'default'    => true // or false
		),
		array(
			'id'      => 'enable-next-prev-links',
			'type'    => 'switcher',
			'title'   => esc_html__( 'Next & Previous Link', 'eazydocs-pro' ),
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
			'title'    => esc_html__( 'Section Excerpt', 'eazydocs' ),
			'subtitle' => esc_html__( 'Define here the Doc section excerpt limit in word count to show. Use -1 to show the full excerpt.', 'eazydocs' ),
			'desc'     => esc_html__( 'Note: If the excerpt leaves empty, the excerpt will be automatically taken from the doc post content.', 'eazydocs' ),
			'id'       => 'doc_sec_excerpt_limit',
			'type'     => 'slider',
			'default'  => 8,
			"min"      => 1,
			"step"     => 1,
			"max"      => 100,
		),
		array(
			'id'      => 'eazydocs-enable-credit',
			'type'    => 'switcher',
			'title'   => esc_html__( 'Credit', 'eazydocs' ),
			'default' => true,
			'class'   => 'eazydocs-pro-notice active-theme'
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
			'default'       => sprintf( __( "%s", 'eazydocs' ), 'Powered By <a href="https://wordpress.org/plugins/eazydocs/">EazyDocs</a>' ),
			'class'         => 'eazydocs-pro-notice active-theme'
		),

		array(
			'id'          => 'content-bg',
			'type'        => 'color',
			'title'       => esc_html__( 'Background Color', 'eazydocs-pro' ),
			'output'      => 'body.single-docs',
			'output_mode' => 'background-color',
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
			'title' => esc_html__( 'Search Banner', 'eazydocs' ),
		),
		array(
			'id'         => 'is_search_banner',
			'type'       => 'switcher',
			'title'      => esc_html__( 'Enable/Disable', 'eazydocs' ),
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
			'class'  => 'eazydocs-pro-notice'
		),
		array(
			'type'  => 'subheading',
			'title' => esc_html__( 'Keywords', 'eazydocs' ),
		),
		array(
			'id'         => 'is_keywords',
			'type'       => 'switcher',
			'title'      => esc_html__( 'Enable/Disable', 'eazydocs' ),
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
			'title'       => esc_html__( 'Label Color', 'eazydocs-pro' ),
			'output_mode' => 'color',
			'output'      => '.ezd_search_keywords .label',
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
			'title'       => esc_html__( 'Keywords Color', 'eazydocs-pro' ),
			'output_mode' => 'color',
			'output'      => '.ezd_search_banner .header_search_keyword ul li a',
			'class'       => 'eazydocs-pro-notice'
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
			'title'      => esc_html__( 'Frontpage Menu', 'eazydocs' ),
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
			'title' => esc_html__( 'Docs Page Title', 'eazydocs-pro' ),
			'class' => 'eazydocs-pro-notice'
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
			'default'    => esc_html__( 'A premium WordPress theme with integrated Knowledge Base, providing 24/7 community based support', 'eazydocs' ),
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
			'id'     => 'action_btn_typo',
			'type'   => 'typography',
			'title'  => esc_html__( 'Documentation Title Typography', 'eazydocs' ),
			'output' => '.doc_left_sidebarlist .doc-title',
			'class'  => 'eazydocs-pro-notice active-theme'
		),

		array(
			'title'    => esc_html__( 'Doc Section Icon', 'docly' ),
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
			'id'          => 'docs-sidebar-bg',
			'type'        => 'color',
			'title'       => esc_html__( 'Background Color', 'eazydocs-pro' ),
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

		array(
			'title'      => esc_html__( 'Conditional Dropdown', 'eazydocs' ),
			'desc'       => __( 'You can display conditional contents using the [conditional_data] shortcode in documentation based on the dropdown value. See the shortcode usage tutorial <a href="https://tinyurl.com/yd46mfax" target="_blank">here</a>.', 'eazydocs' ),
			'id'         => 'is_conditional_dropdown',
			'type'       => 'switcher',
			'text_on'    => esc_html__( 'Show', 'eazydocs' ),
			'text_off'   => esc_html__( 'Hide', 'eazydocs' ),
			'text_width' => 72,
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

		array(
			'title'      => esc_html__( 'Dark Mode Switcher', 'eazydocs' ),
			'id'         => 'is_dark_switcher',
			'type'       => 'switcher',
			'text_on'    => esc_html__( 'Enabled', 'eazydocs' ),
			'text_off'   => esc_html__( 'Disabled', 'eazydocs' ),
			'text_width' => 90,
			'default'    => false,
			'class'      => 'eazydocs-pro-notice active-theme-docly'
		),

		array(
			'title'      => esc_html__( 'Widgets Area', 'eazydocs' ),
			'desc'       => esc_html__( "Enable to register a Sidebar Widgets area named 'Doc Right Sidebar' in Appearance > Widgets.", 'eazydocs' ),
			'id'         => 'is_widget_sidebar',
			'type'       => 'switcher',
			'text_on'    => esc_html__( 'Enabled', 'eazydocs' ),
			'text_off'   => esc_html__( 'Disabled', 'eazydocs' ),
			'text_width' => 90,
			'default'    => false,
			'class'      => 'eazydocs-pro-notice active-theme-docly'
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
			'title' => esc_html__( 'Related Docs', 'eazydocs' )
		),
		array(
			'id'         => 'related-docs',
			'type'       => 'switcher',
			'title'      => esc_html__( 'Enable/Disable', 'eazydocs' ),
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
			'title' => esc_html__( 'Recently Viewed Docs', 'eazydocs' )
		),

		array(
			'id'         => 'viewed-docs',
			'type'       => 'switcher',
			'title'      => esc_html__( 'Enable/Disable', 'eazydocs' ),
			'text_on'    => esc_html__( 'Show', 'eazydocs-pr' ),
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
	'title'  => esc_html__( 'Design', 'eazydocs' ),
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
	'title'  => esc_html__( 'Shortcodes', 'eazydocs' ),
	'icon'   => 'fas fa-plus-circle',
	'fields' => [
		array(
			'id'         => 'eazydocs_docs_shortcode',
			'type'       => 'text',
			'title'      => esc_html__( 'Docs archive', 'eazydocs' ),
			'desc'       => __( 'Know the usage of this shortcode <a href=""> here </a>', 'eazydocs' ),
			'default'    => '[eazydocs]',
			'attributes' => array(
				'readonly' => 'readonly',
			),
		),
		array(
			'id'         => 'conditional_data_shortcode',
			'type'       => 'text',
			'title'      => esc_html__( 'Conditional Dropdown', 'eazydocs' ),
			'desc'       => __( 'Know the usage of this shortcode <a href="https://tinyurl.com/yd46mfax" target="_blank"> here </a>', 'eazydocs' ),
			'default'    => '[conditional_data]',
			'attributes' => array(
				'readonly' => 'readonly',
			),
		),

		array(
			'id'         => 'direction_shortcode',
			'type'       => 'text',
			'title'      => esc_html__( 'Direction Shortcode', 'eazydocs-pro' ),
			'desc'       => __( 'Know the usage of this shortcode <a href="https://tinyurl.com/y2xxhsvx" target="_blank"> here </a>', 'eazydocs-pro' ),
			'default'    => '[direction]',
			'attributes' => array(
				'readonly' => 'readonly',
			),
			'class'      => 'eazydocs-pro-notice'
		),
	]
) );

//
// Shortcode Fields
//
CSF::createSection( $prefix, array(
	'id'     => 'role_manager_fields',
	'title'  => esc_html__( 'Role Manager', 'eazydocs' ),
	'icon'   => 'fas fa-plus-circle',
	'fields' => [

		array(
			'id'       => 'docs-write-access',
			'type'     => 'select',
			'title'    => esc_html__( 'Who Can View Docs?', 'eazydocs' ),
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
	'title'  => esc_html__( 'Assistant', 'eazydocs-pro' ),
	'icon'   => 'fas fa-plus-circle',
	'fields' => [

		array(
			'type'  => 'heading',
			'title' => esc_html__( 'Assistant', 'eazydocs-pro' ),
		),

		array(
			'id'         => 'assistant_visibility',
			'type'       => 'switcher',
			'title'      => esc_html__( 'Enable/Disable', 'eazydocs-pro' ),
			'text_on'    => esc_html__( 'Show', 'eazydocs-pro' ),
			'text_off'   => esc_html__( 'Hide', 'eazydocs-pro' ),
			'class'      => 'eazydocs-pro-notice',
			'text_width' => 70,
			'default'    => false
		),
		array(
			'id'             => 'assistant_open_icon',
			'type'           => 'media',
			'title'          => esc_html__( 'Open Icon', 'eazydocs-pro' ),
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
			'title'          => esc_html__( 'Close Icon', 'eazydocs-pro' ),
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
							'title' => esc_html__( 'Knowledge Base', 'eazydocs-pro' ),
						),
						array(
							'id'         => 'kb_visibility',
							'type'       => 'switcher',
							'title'      => esc_html__( 'Enable/Disable', 'eazydocs-pro' ),
							'text_on'    => esc_html__( 'Show', 'eazydocs-pro' ),
							'text_off'   => esc_html__( 'Hide', 'eazydocs-pro' ),
							'text_width' => 70,
							'default'    => true
						),

						array(
							'id'         => 'kb_label',
							'type'       => 'text',
							'title'      => esc_html__( 'Heading', 'eazydocs-pro' ),
							'default'    => esc_html__( 'Knowledge Base', 'eazydocs-pro' ),
							'dependency' => array(
								array( 'kb_visibility', '==', 'true' ),
							)
						),

						array(
							'id'         => 'assistant_search',
							'type'       => 'switcher',
							'title'      => esc_html__( 'Search', 'eazydocs-pro' ),
							'text_on'    => esc_html__( 'Show', 'eazydocs-pro' ),
							'text_off'   => esc_html__( 'Hide', 'eazydocs-pro' ),
							'text_width' => 70,
							'default'    => false,
							'dependency' => array(
								array( 'kb_visibility', '==', 'true' )
							)
						),

						array(
							'id'         => 'kb_search_placeholder',
							'type'       => 'text',
							'title'      => esc_html__( 'Search Placeholder', 'eazydocs-pro' ),
							'default'    => esc_html__( 'Search...', 'eazydocs-pro' ),
							'dependency' => array(
								array( 'assistant_search', '==', 'true' ),
								array( 'kb_visibility', '==', 'true' )
							)
						),

						array(
							'id'         => 'assistant_breadcrumb',
							'type'       => 'switcher',
							'title'      => esc_html__( 'Breadcrumb', 'eazydocs-pro' ),
							'text_on'    => esc_html__( 'Show', 'eazydocs-pro' ),
							'text_off'   => esc_html__( 'Hide', 'eazydocs-pro' ),
							'text_width' => 70,
							'default'    => false,
							'dependency' => array(
								array( 'kb_visibility', '==', 'true' )
							)
						),

						array(
							'id'         => 'docs_not_found',
							'type'       => 'text',
							'title'      => esc_html__( 'Docs not Found', 'eazydocs-pro' ),
							'default'    => esc_html__( 'Docs not Found', 'eazydocs-pro' ),
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
							'title'      => esc_html__( 'Enable/Disable', 'eazydocs-pro' ),
							'text_on'    => esc_html__( 'Show', 'eazydocs-pro' ),
							'text_off'   => esc_html__( 'Hide', 'eazydocs-pro' ),
							'text_width' => 70,
							'default'    => true
						),

						array(
							'id'         => 'contact_label',
							'type'       => 'text',
							'title'      => esc_html__( 'Heading', 'eazydocs-pro' ),
							'default'    => esc_html__( 'Contact', 'eazydocs-pro' ),
							'dependency' => array(
								array( 'contact_visibility', '==', 'true' ),
							)
						),

						array(
							'id'         => 'assistant_contact_mail',
							'type'       => 'text',
							'title'      => esc_html__( 'Receiver Email', 'eazydocs-pro' ),
							'default'    => get_option( 'admin_email' ),
							'validate'   => 'csf_validate_email',
							'dependency' => array(
								array( 'contact_visibility', '==', 'true' ),
							)
						),

						array(
							'type'       => 'subheading',
							'title'      => esc_html__( 'Form Input', 'eazydocs-pro' ),
							'dependency' => array(
								array( 'contact_visibility', '==', 'true' ),
							)
						),
						array(
							'id'         => 'contact_fullname',
							'type'       => 'text',
							'title'      => esc_html__( 'Full name', 'eazydocs-pro' ),
							'default'    => esc_html__( 'Full name', 'eazydocs-pro' ),
							'dependency' => array(
								array( 'contact_visibility', '==', 'true' ),
							)
						),
						array(
							'id'         => 'contact_mail',
							'type'       => 'text',
							'title'      => esc_html__( 'Email', 'eazydocs-pro' ),
							'default'    => esc_html__( 'name@example.com', 'eazydocs-pro' ),
							'dependency' => array(
								array( 'contact_visibility', '==', 'true' ),
							)
						),
						array(
							'id'         => 'contact_subject',
							'type'       => 'text',
							'title'      => esc_html__( 'Subject', 'eazydocs-pro' ),
							'default'    => esc_html__( 'Subject', 'eazydocs-pro' ),
							'dependency' => array(
								array( 'contact_visibility', '==', 'true' ),
							)
						),
						array(
							'id'         => 'contact_message',
							'type'       => 'text',
							'title'      => esc_html__( 'Message', 'eazydocs-pro' ),
							'default'    => esc_html__( 'Write Your Message', 'eazydocs-pro' ),
							'dependency' => array(
								array( 'contact_visibility', '==', 'true' ),
							)
						),
						array(
							'id'         => 'contact_submit',
							'type'       => 'text',
							'title'      => esc_html__( 'Submit Button', 'eazydocs-pro' ),
							'default'    => esc_html__( 'Send Message', 'eazydocs-pro' ),
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
							'title' => esc_html__( 'Color', 'eazydocs-pro' ),
						),
						array(
							'id'          => 'assistant_bg',
							'type'        => 'color',
							'title'       => esc_html__( 'Icon Color', 'eazydocs-pro' ),
							'output'      => '.chat-toggle a',
							'output_mode' => 'background-color',
						),
						array(
							'id'          => 'assistant_header_bg',
							'type'        => 'color',
							'title'       => esc_html__( 'Header Background', 'eazydocs-pro' ),
							'output'      => '.chatbox-header',
							'output_mode' => 'background-color',
						),
						array(
							'id'          => 'assistant_body_bg',
							'type'        => 'color',
							'title'       => esc_html__( 'Background', 'eazydocs-pro' ),
							'output'      => '.chatbox-body',
							'output_mode' => 'background-color',
						),
						array(
							'id'          => 'assistant_submit_bg',
							'type'        => 'color',
							'title'       => esc_html__( 'Submit Button', 'eazydocs-pro' ),
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
							'title' => esc_html__( 'Position', 'eazydocs-pro' ),
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