<?php
if ( ! defined( 'ABSPATH' ) ) {
	die;
} // Cannot access directly.

//
// Set a unique slug-like ID
//
$prefix = 'eazydocs_settings';

//
// Create options
//

$ezd_options = get_option( 'eazydocs_settings' );

$edit_access = [];
$edit_access = ezd_get_opt( 'settings-edit-access', 'eazydocs_settings' );


$all_roles = '';
if ( is_array( $edit_access ) ) {
	$all_roles = ! empty ( $edit_access ) ? implode( ',', $edit_access ) : '';
}

if ( ! empty ( $all_roles ) ) {
	$all_roled = explode( ',', $all_roles );

	if ( ! function_exists( 'wp_get_current_user' ) ) {
		include( ABSPATH . "wp-includes/pluggable.php" );
	}

	$user              = wp_get_current_user();
	$userdata          = get_user_by( 'id', $user->ID );
	$current_user_role = $userdata->roles[0] ?? '';

	$capabilites = 'manage_options';

	if ( in_array( $current_user_role, $all_roled ) ) {
		switch ( $current_user_role ) {
			case 'administrator':
				$capabilites = 'manage_options';
				break;

			case 'editor':
				$capabilites = 'publish_pages';
				break;

			case 'author':
				$capabilites = 'publish_posts';
				break;
		}
	}
} else {
	$capabilites = 'manage_options';
}
CSF::createOptions( $prefix, array(
	'framework_title'    => esc_html__( 'EazyDocs', 'eazydocs' ) . ' <small> v' . EAZYDOCS_VERSION . '</small>',
	'menu_title'         => esc_html__( 'Settings', 'eazydocs' ),
	'menu_slug'          => 'eazydocs-settings',
	'menu_type'          => 'submenu',
	'menu_capability' 	 => $capabilites,
	'menu_parent'        => 'eazydocs',
	'show_in_customizer' => ezd_get_opt( 'customizer_visibility' ),
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
			'title'      => esc_html__( 'Docs Archive Page', 'eazydocs' ),
			'subtitle'   => esc_html__( 'Select the Docs Archive page. This page will be used to show the Docs.', 'eazydocs' ),
			'desc'       => esc_html__( 'You can create this page with using [eazydocs] shortcode or EazyDocs Gutenberg blocks or EazyDocs Elementor widgets.', 'eazydocs' ),
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
			'title'      => esc_html__( 'Root URL Format', 'eazydocs' ),
			'subtitle'   => esc_html__( 'Select the Docs URL Structure. This will be used to generate the Docs URL.', 'eazydocs' ),			
			'desc'   	=> sprintf( __( '<b>Note:</b> After changing the URL structure, go to %s Settings > Permalinks %s and click on the Save Changes button.', 'eazydocs' ), '<a href="'.admin_url('/options-permalink.php').'" target="_blank">', '</a>' ),
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
			'after' 	=> esc_html__('Ignore the plain and numaric permalink', 'eazydocs'),
		),

		array(
			'id'      => 'docs-type-slug',
			'type'    => 'text',
			'title'   => esc_html__( 'Root slug', 'eazydocs' ),
			'subtitle' => esc_html__( 'Make sure to keep Docs Root Slug in the Single Docs Permalink. You are not able to keep it blank.', 'eazydocs' ),
			'default'  => esc_html__( 'docs', 'eazydocs' ),	
			'desc'     => sprintf( __( '<b>Note:</b> After changing the slug, go to %s Settings > Permalinks %s and click on the Save Changes button.', 'eazydocs' ), '<a href="'.admin_url('/options-permalink.php').'" target="_blank">', '</a>' ),
			'dependency' => array( 'docs-url-structure', '==', 'custom-slug' ),
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

// Dark Mode Fields
CSF::createSection( $prefix, array(
    'id'     => 'dark_mode',
    'title'  => esc_html__( 'Dark Mode', 'eazydocs' ),
    'icon'   => 'fas fa-adjust',
    'fields' => array(

        array(
            'title'         => esc_html__( 'Dark Mode Switcher', 'eazydocs' ),
            'subtitle'      => esc_html__( 'By show/hiding the Dark Mode Switcher, you are enable/disabling the Dark mode feature on the Doc single page.', 'eazydocs' ),
            'id'            => 'is_dark_switcher',
            'type'          => 'switcher',
            'text_on'       => esc_html__( 'Show', 'eazydocs' ),
            'text_off'      => esc_html__( 'Hide', 'eazydocs' ),
            'text_width'    => 72,
            'default'       => false,
            'class'         => 'eazydocs-pro-notice active-theme-docly'
        ),

        array(
            'title'         => esc_html__( 'Accent color', 'eazydocs' ),
            'subtitle'      => esc_html__( 'Different accent color on Dark Mode.', 'eazydocs' ),
            'id'            => 'is_dark_accent_color',
            'type'          => 'switcher',
            'text_on'       => esc_html__( 'Yes', 'eazydocs' ),
            'text_off'      => esc_html__( 'No', 'eazydocs' ),
            'text_width'    => 92,
            'default'       => false,
            'class'         => 'eazydocs-pro-notice active-theme-docly',
            'dependency'    => array( 'is_dark_switcher', '==', '1' ),
        ),

        array(
            'id'            => 'ezd_brand_color_dark',
            'type'          => 'color',
            'title'         => esc_html__( 'Brand Color on Dark Mode', 'eazydocs' ),
            'subtitle'      => esc_html__( 'Accent Color for dark mode on Frontend. You can choose a different color the Dark mode from here.', 'eazydocs' ),
            'output'        => ':root',
            'output_mode'   => '--ezd_brand_color_dark',
            'dependency'    => array(
				'is_dark_switcher', '==', '1',
				'is_dark_accent_color', '==', '1',
            ),
        ),

    )
) );



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

		// unique views
		array(
			'id'      => 'enable-unique-views',
			'type'    => 'switcher',
			'title'   => esc_html__( 'Unique Views', 'eazydocs' ),
			'default' => false, // or false
			'dependency' => array(
				array( 'enable-views', '==', 'true' ),
			)
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
			'subtitle'   => esc_html__( 'Load the doc single page without refreshing the page.', 'eazydocs' ),
			'id'         => 'is_doc_ajax',
			'type'       => 'switcher',
			'text_on'    => esc_html__( 'Enabled', 'eazydocs' ),
			'text_off'   => esc_html__( 'Disabled', 'eazydocs' ),
			'text_width' => 90,
			'default'    => false,
			'class'      => 'eazydocs-pro-notice active-theme-docy active-theme-docly active-theme-ama',
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
			'type'  => 'heading',
			'title' => esc_html__( 'Excerpt', 'eazydocs' ),
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
			'id'          => 'keywords_label_color',
			'type'        => 'color',
			'title'       => esc_html__( 'Label Color', 'eazydocs' ),
			'output_mode' => 'color',
			'output'      => '.ezd_search_keywords .label',
			'dependency'  => array(
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

//
// Restricted Docs Fields
//
CSF::createSection( $prefix, array(
	'id'    => 'restricted_docs',
	'title' => esc_html__( 'Restricted Docs', 'eazydocs' ),
	'icon'  => 'fas fa-plus-circle',
) );

// Private Doc
CSF::createSection( $prefix, array(
	'id'     => 'private_doc_settings',
	'parent' => 'restricted_docs',
	'title'  => esc_html__( 'Private Doc', 'eazydocs' ),
	'icon'   => '',
	'fields' => array(
		array(
			'id'    => 'private_doc_visibility',
			'type'  => 'heading',
			'title' => esc_html__( 'Private Doc', 'eazydocs' )
		),
		array(
			'id'      => 'private_doc_mode',
			'type'    => 'select',
			'title'   => esc_html__( 'Visibility Mode', 'eazydocs' ),
			'subtitle'   => esc_html__( 'Select what to show when a logged out user try to visit a private doc URL.', 'eazydocs' ),
			'options' => [
				'login' => esc_html__( 'Login Page', 'eazydocs' ),
				'none'  => esc_html__( '404 Error', 'eazydocs' ),
			],
			'default' => 'none',
			'class'   => 'eazydocs-pro-notice'
		),
		array(
			'id'          => 'private_doc_login_page',
			'type'        => 'select',
			'placeholder' => 'Select page',
			'title'       => esc_html__( 'Select Page', 'eazydocs' ),
			'subtitle'    => esc_html__( 'Select Doc login page', 'eazydocs' ),
			'desc'        => esc_html__( 'If you want to change this page, use this shortcode [ezd_login_form] to display the login form on your desired page.', 'eazydocs' ),
			'options'     => 'pages',
			'class'       => 'eazydocs-pro-notice',
			'dependency'  => array(
				array( 'private_doc_mode', '==', 'login' ),
			),
			'query_args'  => array(
				'posts_per_page' => - 1,
			),
			'chosen'      => true,
			'ajax'        => true,
		),
		
		array(
			'id'         => 'private_doc_user_restriction',
			'type'      => 'fieldset',
			'title'      => esc_html__( 'Restrict Access to', 'eazydocs' ),
			'subtitle'   => esc_html__( 'Select who can view your private docs.', 'eazydocs' ),
			'fields'    => array(
				array(
					'id'         => 'private_doc_all_user',
					'type'       => 'checkbox',
					'title'      => null,
					'text_on'    => esc_html__( 'Yes', 'eazydocs' ),
					'text_off'   => esc_html__( 'No', 'eazydocs' ),
					'label'	 	 => esc_html__( 'All logged in users', 'eazydocs' ),
					'default'    => false,
					'class'      => 'eazydocs-pro-notice',
				),
				array(
					'id'         => 'private_doc_roles',
					'type'       => 'select',
					'title'      => esc_html__( 'User Roles', 'eazydocs' ),
					'desc'   => esc_html__( 'Only selected User Roles will be able to view your Knowledge Base', 'eazydocs' ),
					'options'    => [
						'administrator' => esc_html__( 'Administrator', 'eazydocs' ),
						'editor'        => esc_html__( 'Editor', 'eazydocs' ),
						'author'        => esc_html__( 'Author', 'eazydocs' ),
						'contributor'   => esc_html__( 'Contributor', 'eazydocs' ),
						'subscriber'    => esc_html__( 'Subscriber', 'eazydocs' ),
					],
					'default'    => 'administrator',
					'chosen'      => true,
					'ajax'        => true,
					'multiple'    => true,
					'class'      => 'eazydocs-pro-notice',
					'dependency' => array(
						array( 'private_doc_all_user', '==', 'false' ),
					)
				)
			)
		)
	)
) );

// Protected Doc
CSF::createSection( $prefix, array(
	'id'     => 'protected_doc_settings',
	'parent' => 'restricted_docs',
	'title'  => esc_html__( 'Protected Doc', 'eazydocs' ),
	'icon'   => '',
	'fields' => array(
		array(
			'id'    => 'protected_doc_heading',
			'type'  => 'heading',
			'title' => esc_html__( 'Protected Doc', 'eazydocs' )
		),
		array(
			'id'      => 'protected_doc_form',
			'type'    => 'select',
			'title'   => esc_html__( 'Password Form', 'eazydocs' ),
			'options' => [
				'eazydocs-form' => esc_html__( 'EazyDocs Form', 'eazydocs' ),
				'default'       => esc_html__( 'Default', 'eazydocs' ),
			],
			'default' => 'eazydocs-form'
		),
		array(
			'id'         => 'protected_doc_form_info',
			'type'       => 'subheading',
			'title'      => esc_html__( 'Form', 'eazydocs' ),
			'dependency' => array(
				array( 'protected_doc_form', '==', 'eazydocs-form' ),
			)
		),
		array(
			'id'          => 'protected_form_head_color',
			'type'        => 'color',
			'title'       => esc_html__( 'Header Color', 'eazydocs' ),
			'dependency'  => array(
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
			'id'          => 'protected_form_title_color',
			'type'        => 'color',
			'title'       => esc_html__( 'Title Color', 'eazydocs' ),
			'dependency'  => array(
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
			'id'          => 'protected_form_subtitle_color',
			'type'        => 'color',
			'title'       => esc_html__( 'Sub Title Color', 'eazydocs' ),
			'dependency'  => array(
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
			'id'          => 'protected_form_btn_bgcolor',
			'type'        => 'color',
			'title'       => esc_html__( 'Button Text Color', 'eazydocs' ),
			'dependency'  => array(
				array( 'protected_doc_form', '==', 'eazydocs-form' ),
			),
			'output'      => '.ezd-password-wrap .ezd-password-body form button',
			'output_mode' => 'color',
		),
		array(
			'id'          => 'protected_form_btn_textcolor',
			'type'        => 'color',
			'title'       => esc_html__( 'Button Background Color', 'eazydocs' ),
			'dependency'  => array(
				array( 'protected_doc_form', '==', 'eazydocs-form' ),
			),
			'output'      => '.ezd-password-wrap .ezd-password-body form button',
			'output_mode' => 'background-color',
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
	'title'  => esc_html__( 'Customizer', 'eazydocs' ),
	'icon'   => 'fas fa-plus-circle',
	'fields' => [
		array(
			'id'         => 'customizer_visibility',
			'type'       => 'switcher',
			'title'      => esc_html__( 'Options Visibility on Customizer', 'eazydocs' ),
			'text_on'    => esc_html__( 'Enabled', 'eazydocs' ),
			'text_off'   => esc_html__( 'Disabled', 'eazydocs' ),
			'text_width' => 100,
		),

		array(
			'type'       => 'content',
			'content'    => sprintf( '<a href="' . $archive_url . '" target="_blank" id="get_docs_archive">' . esc_html__( 'Docs Archive', 'eazydocs' )
			                         . '</a> <a href="' . $single_url . '" target="_blank" id="get_docs_single">' . esc_html__( 'Single Doc', 'eazydocs' )
			                         . '</a>' ),
			'dependency' => array(
				array( 'customizer_visibility', '==', true ),
			),
		)
	]
) );


//
// Footnotes
//
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
			'subtitle'   => sprintf(
				__( 'Use this shortcode to display the Docs. Learn more about the shortcode and the attributes %s here %s.', 'eazydocs' ),
				'<a href="https://tinyurl.com/24zm4oj3" target="_blank">', '</a>'
			),
			'desc'       => esc_html__( 'See the shortcode with the available attributes', 'eazydocs' )
			                . '<br><code>[eazydocs col="3" include="" exclude="" show_docs="" show_articles="" more="View More"]</code>',
			'default'    => '[eazydocs]',
			'attributes' => array(
				'readonly' => 'readonly',
			),
		),

		array(
			'id'         => 'conditional_data_shortcode',
			'type'       => 'text',
			'title'      => esc_html__( 'Conditional Dropdown', 'eazydocs' ),
			'subtitle'   => sprintf( esc_html__( 'Know the usage of this shortcode %s here %s', 'eazydocs' ),
				'<a href="https://tinyurl.com/24d9rw72" target="_blank">', '</a>' ),
			'default'    => '[conditional_data]',
			'attributes' => array(
				'readonly' => 'readonly',
			),
		),
		array(
			'id'         => 'ezdocs_login_shortcode',
			'type'       => 'text',
			'title'      => esc_html__( 'Docs Login', 'eazydocs' ),
			'subtitle'   => esc_html__( 'Use this shortcode to display login form.', 'eazydocs' ),
			'desc'       => esc_html__( 'See the shortcode example with the available attributes', 'eazydocs' )
			                . '<br><code>[ezd_login_form login_title="You must log in to continue."  login_subtitle="Login to ' . get_bloginfo()
			                . '" login_btn="Log In" login_forgot_btn="Forgotten account?"]</code>',
			'default'    => '[ezd_login_form]',
			'attributes' => array(
				'readonly' => 'readonly',
			),
			'class'      => 'eazydocs-pro-notice'
		),
		array(
			'id'         => 'ezdocs_footnote_shortcode',
			'type'       => 'text',
			'title'      => esc_html__( 'Footnote Shortcode', 'eazydocs' ),
			'subtitle'   => sprintf( esc_html__( 'Use this shortcode to display footnotes. %s Learn how to create Footnotes %s', 'eazydocs' ),
				'<a href="https://tinyurl.com/2ewlorze" target="_blank">', '</a>' ),
			'desc'       => esc_html__( 'See the shortcode example with the available attributes', 'eazydocs' )
			                . '<br><code>[reference number="1"]Tooltip Content[/reference]</code>',
			'default'    => '[reference]',
			'attributes' => array(
				'readonly' => 'readonly',
			),
			'class'      => 'eazydocs-pro-notice active-theme-docy active-theme-docly active-theme-ama'
		),
		array(
			'id'         => 'ezdocs_embed_post_shortcode',
			'type'       => 'text',
			'title'      => esc_html__( 'Embed Post Shortcode', 'eazydocs' ),
			'subtitle'   => sprintf( esc_html__( 'Use this shortcode to display a doc inside another doc. Know the usage of this shortcode %s here %s',
				'eazydocs' ), '<a href="https://tinyurl.com/bde27yn4" target="_blank">', '</a>' ),
			'desc'       => esc_html__( 'See the shortcode with the available attributes.', 'eazydocs' )
			                . '<br><code>[embed_post id="POST_ID" limit="no" thumbnail="yes"]</code> <br>',
			'default'    => '[embed_post]',
			'attributes' => array(
				'readonly' => 'readonly',
			),
			'class'      => 'eazydocs-pro-notice'
		)
	]
) );


//
// Docs Collaboration
//
CSF::createSection( $prefix, array(
	'id'     => 'contributor_fields',
	'title'  => esc_html__( 'Docs Collaboration', 'eazydocs' ),
	'icon'   => 'fas fa-plus-circle',
	'fields' => [
		array(
			'id'         => 'is_doc_contribution',
			'type'       => 'switcher',
			'title'      => esc_html__( 'Collaboration Feature', 'eazydocs' ),
			'subtitle'   => esc_html__( 'Collaboration buttons on the doc Right Sidebar.', 'eazydocs' ),
			'desc'       => esc_html__( 'By enabling this feature, you are allowing other people to contribute the docs. This will also let you manage the contributors from the Doc post editor.',
				'eazydocs' ),
			'text_on'    => esc_html__( 'Enabled', 'eazydocs' ),
			'text_off'   => esc_html__( 'Disabled', 'eazydocs' ),
			'text_width' => 92,
			'default'    => false,
			'class'      => 'eazydocs-promax-notice'
		),

		array(
			'id'          => 'docs_frontend_login_page',
			'type'        => 'select',
			'placeholder' => 'Select page',
			'title'       => esc_html__( 'Login Page', 'eazydocs' ),
			'subtitle'    => esc_html__( 'Type in the field to select the login page. User would require to login to edit/add docs from frontend.', 'eazydocs' ),
			'desc'        => esc_html__( 'This page is required to select to show/enable the Add/Edit doc buttons. You can use the shortcode [ezd_login_form] for the login page.', 'eazydocs' ),
			'options'     => 'pages',
			'class'       => 'eazydocs-promax-notice',
			'chosen'      => true,
			'ajax'        => true,
			'query_args'  => array(
				'posts_per_page' => - 1,
			),
			'dependency'  => array(
				array( 'is_doc_contribution', '==', 'true' )
			)
		),

		array(
			'id'         => 'ezd_add_doc_heading',
			'type'       => 'heading',
			'title'      => esc_html__( 'Add Doc', 'eazydocs' ),
			'dependency' => array(
				array( 'is_doc_contribution', '==', 'true' ),
				array( 'docs_frontend_login_page', '!=', '' ),
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
				array( 'is_doc_contribution', '==', 'true' ),
				array( 'docs_frontend_login_page', '!=', '' ),
			)
		),

		array(
			'id'         => 'frontend_add_btn_text',
			'type'       => 'text',
			'title'      => esc_html__( 'Button', 'eazydocs' ),
			'default'    => esc_html__( 'Add Doc', 'eazydocs' ),
			'class'      => 'eazydocs-promax-notice',
			'dependency' => array(
				array( 'is_doc_contribution', '==', 'true' ),
				array( 'docs_frontend_login_page', '!=', '' ),
				array( 'frontend_add_switcher', '==', 'true' ),
			)
		),

		array(
			'id'         => 'frontend_edit_doc',
			'type'       => 'heading',
			'title'      => esc_html__( 'Edit Doc', 'eazydocs' ),
			'dependency' => array(
				array( 'is_doc_contribution', '==', 'true' ),
				array( 'docs_frontend_login_page', '!=', '' ),
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
				array( 'docs_frontend_login_page', '!=', '' ),
			)
		),

		array(
			'id'         => 'frontend_edit_btn_text',
			'type'       => 'text',
			'title'      => esc_html__( 'Button', 'eazydocs' ),
			'default'    => esc_html__( 'Edit Doc', 'eazydocs' ),
			'class'      => 'eazydocs-promax-notice',
			'dependency' => array(
				array( 'is_doc_contribution', '==', 'true' ),
				array( 'frontend_edit_switcher', '==', 'true' ),
				array( 'docs_frontend_login_page', '!=', '' ),
			)
		),

		array(
			'id'         => 'docs_contributor_meta',
			'type'       => 'heading',
			'title'      => esc_html__( 'Meta Content', 'eazydocs' ),
			'dependency' => array(
				array( 'is_doc_contribution', '==', 'true' ),
				array( 'docs_frontend_login_page', '!=', '' ),
			)
		),

		array(
			'id'         => 'contributor_meta_visibility',
			'type'       => 'switcher',
			'title'      => esc_html__( 'Enable / Disable', 'eazydocs' ),
			'text_on'    => esc_html__( 'Show', 'eazydocs' ),
			'text_off'   => esc_html__( 'Hide', 'eazydocs' ),
			'class'      => 'eazydocs-promax-notice',
			'text_width' => 70,
			'default'    => true,
			'dependency' => array(
				array( 'is_doc_contribution', '==', 'true' ),
				array( 'docs_frontend_login_page', '!=', '' ),
			)
		),

		array(
			'id'         => 'contributor_meta_title',
			'type'       => 'text',
			'title'      => esc_html__( 'Title', 'eazydocs' ),
			'default'    => esc_html__( 'Contributors', 'eazydocs' ),
			'class'      => 'eazydocs-promax-notice',
			'dependency' => array(
				array( 'is_doc_contribution', '==', 'true' ),
				array( 'contributor_meta_visibility', '==', true ),
				array( 'docs_frontend_login_page', '!=', '' ),
			)
		),

		array(
			'id'         => 'contributor_meta_dropdown_title',
			'type'       => 'text',
			'title'      => esc_html__( 'Dropdown Heading', 'eazydocs' ),
			'default'    => esc_html__( 'Manage Contributors', 'eazydocs' ),
			'class'      => 'eazydocs-promax-notice',
			'dependency' => array(
				array( 'is_doc_contribution', '==', 'true' ),
				array( 'contributor_meta_visibility', '==', true ),
				array( 'docs_frontend_login_page', '!=', '' ),
			)
		),

		array(
			'id'         => 'contributor_meta_search',
			'type'       => 'switcher',
			'title'      => esc_html__( 'Search', 'eazydocs' ),
			'subtitle'   => esc_html__( 'Search through the existing users', 'eazydocs' ),
			'text_on'    => esc_html__( 'Show', 'eazydocs' ),
			'text_off'   => esc_html__( 'Hide', 'eazydocs' ),
			'class'      => 'eazydocs-promax-notice',
			'text_width' => 70,
			'default'    => true,
			'dependency' => array(
				array( 'is_doc_contribution', '==', 'true' ),
				array( 'docs_frontend_login_page', '!=', '' ),
				array( 'contributor_meta_visibility', '==', true )
			)
		),

		array(
			'id'         => 'contributor_load_more',
			'type'       => 'switcher',
			'title'      => esc_html__( 'Load More By Ajax', 'eazydocs' ),
			'subtitle'   => esc_html__( 'Enable / Disable the Load more users with scrolling', 'eazydocs' ),
			'text_on'    => esc_html__( 'Show', 'eazydocs' ),
			'text_off'   => esc_html__( 'Hide', 'eazydocs' ),
			'class'      => 'eazydocs-promax-notice',
			'text_width' => 70,
			'default'    => false,
			'dependency' => array(
				array( 'is_doc_contribution', '==', 'true' ),
				array( 'docs_frontend_login_page', '!=', '' ),
				array( 'contributor_meta_visibility', '==', true )
			)
		),

		array(
			'id'         => 'contributor_load_more_text',
			'type'       => 'text',
			'title'      => esc_html__( 'Load More Text', 'eazydocs' ),
			'default'    => esc_html__( 'Loading', 'eazydocs' ),
			'class'      => 'eazydocs-promax-notice',
			'dependency' => array(
				array( 'is_doc_contribution', '==', 'true' ),
				array( 'docs_frontend_login_page', '!=', '' ),
				array( 'contributor_meta_visibility', '==', true ),
				array( 'contributor_load_more', '==', true )
			)
		),

		array(
			'id'         => 'contributor_load_per_scroll',
			'type'       => 'number',
			'title'      => esc_html__( 'Load Per Scroll', 'eazydocs' ),
			'default'    => esc_html__( '3', 'eazydocs' ),
			'class'      => 'eazydocs-promax-notice',
			'dependency' => array(
				array( 'is_doc_contribution', '==', 'true' ),
				array( 'docs_frontend_login_page', '!=', '' ),
				array( 'contributor_meta_visibility', '==', true ),
				array( 'contributor_load_more', '==', true )
			)
		),

		array(
			'id'         => 'contributor_to_add',
			'type'       => 'number',
			'title'      => esc_html__( 'Show User Number', 'eazydocs' ),
			'subtitle'   => esc_html__( 'Number of users that can be added', 'eazydocs' ),
			'class'      => 'eazydocs-promax-notice',
			'default'    => 3,
			'dependency' => array(
				array( 'is_doc_contribution', '==', 'true' ),
				array( 'docs_frontend_login_page', '!=', '' ),
				array( 'contributor_meta_visibility', '==', true ),
				array( 'contributor_load_more', '!=', true )
			)
		)
	]
) );

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
			'title'    => esc_html__( 'Who Can Create Docs?', 'eazydocs' ),
			'subtitle' => esc_html__( 'Allow users to view & create Docs from the Doc Builder UI in the admin dashboard.', 'eazydocs' ),
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
			'subtitle' => esc_html__( 'Allow users to update options from settings.', 'eazydocs' ),
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
			'subtitle' => esc_html__( 'Allow users to customize Docs from customizer settings.', 'eazydocs' ),
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
			'id'         => 'assistant_visibility_by',
			'type'       => 'button_set',
			'title'      => esc_html__( 'Display Location', 'eazydocs' ),
			'subtitle' 	 => esc_html__( 'Set your assistant where should be appears.', 'eazydocs' ),
			'class'      => 'eazydocs-pro-notice',
			'options'    => array(
			  'global' 		=>  esc_html__( 'Everywhere', 'eazydocs' ),
			  'pages'  		=>  esc_html__( 'Pages', 'eazydocs' ),
			  'post_type' 	=>  esc_html__( 'Post Type', 'eazydocs' ),
			),
			'default'    => 'global',
			'dependency' => array(
				array( 'assistant_visibility', '==', 'true' )
			),
		),
		
		array(
			'id'         => 'assistant_pages',
			'type'       => 'select',
			'title'      => esc_html__( 'Select Pages', 'eazydocs' ),
			'subtitle' 	 => esc_html__( 'Select pages where should be appears.', 'eazydocs' ),
			'options'    => 'pages',
			'class'      => 'eazydocs-pro-notice',
			'chosen'     => true,
			'multiple'   => true,
			'dependency' => array(
				array( 'assistant_visibility_by', '==', 'pages' ),
				array( 'assistant_visibility', '==', 'true' )
			)
		),
		
		array(
			'id'         => 'assistant_post_types',
			'type'       => 'select',
			'title'      => esc_html__( 'Select Post Types', 'eazydocs' ),
			'subtitle' 	 => esc_html__( 'Pick your preferred post types where should be appears.', 'eazydocs' ),
			'options'    => 'post_types',
			'class'      => 'eazydocs-pro-notice',
			'chosen'     => true,
			'multiple'   => true,
			'dependency' => array(
				array( 'assistant_visibility_by', '==', 'post_type' ),
				array( 'assistant_visibility', '==', 'true' )
			)
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
				array( 'assistant_visibility', '==', '1' )
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
			'title'      => esc_html__( 'Tab Settings', 'eazydocs' ),
			'dependency' => array(
				array( 'assistant_visibility', '==', 'true' )
			),
			'tabs'       => array(
				array(
					'title'  => esc_html__( 'Knowledge Base', 'eazydocs' ),
					'fields' => array(
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
							'default'    => true,
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
							'default'    => true,
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
					'title'  => esc_html__( 'Contact', 'eazydocs' ),
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
					'title'  => esc_html__( 'Color', 'eazydocs' ),
					'fields' => array(
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
					'title'  => esc_html__( 'Position', 'eazydocs' ),
					'fields' => array(
						array(
							'id'    => 'assistant_position_heading',
							'type'  => 'heading',
							'title' => esc_html__( 'Position', 'eazydocs' ),
						),

						array(
							'id'          => 'assistant_spacing_vertical',
							'type'        => 'slider',
							'title'       => esc_html__( 'Vertical Position', 'eazydocs' ),
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
							'title'       => esc_html__( 'Horizontal Position', 'eazydocs' ),
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


// Subscriptions
CSF::createSection( $prefix, array(
	'id'     => 'subscriptions_opt',
	'title'  => esc_html__( 'Docs Subscriptions', 'eazydocs' ), 
	'icon'   => 'fas fa-plus-circle',
	'fields' => array(
		
		array(
			'type'  => 'heading',
			'title' => esc_html__( 'Subscriptions Options', 'eazydocs' )
		),

		array(
			'id'         => 'subscriptions',
			'type'       => 'switcher',
			'title'      => esc_html__( 'Enable / Disable', 'eazydocs' ),
			'subtitle'   => esc_html__( 'Enable to show the subscription form in the single doc page.', 'eazydocs' ),
			'text_on'    => esc_html__( 'Show', 'eazydocs' ),
			'text_off'   => esc_html__( 'Hide', 'eazydocs' ),
			'default'    => false,
			'class'      => 'eazydocs-promax-notice',
			'text_width' => 72
		),
		
		array(
			'id'            => 'subscriptions_tab',
			'type'          => 'tabbed',
			'title'         => esc_html__( 'Customize', 'eazydocs' ),
			'subtitle'      => esc_html__( 'Customize the subscription form here.', 'eazydocs' ),
			'dependency' 	=> array( 'subscriptions', '==', 'true' ),
			'class'     	=> 'eazydocs-promax-notice',
			'tabs'          => array(
			  array(
				'title'     =>  esc_html__( 'Subscribe', 'eazydocs' ),
				'fields'    => array(
					array(
						'id'         => 'subscriptions_btn',
						'type'       => 'text',
						'title'      => esc_html__( 'Button', 'eazydocs' ),
						'default'    => esc_html__( 'Subscribe for updates', 'eazydocs' )
					),
					
					array(
						'id'         => 'subscriptions_heading',
						'type'       => 'text',
						'title'      => esc_html__( 'Heading', 'eazydocs' ),
						'default'    => esc_html__( 'Subscribe', 'eazydocs' ),
					),
					
					array(
						'id'         => 'subscriptions_name_label',
						'type'       => 'text',
						'title'      => esc_html__( 'Input :: Name', 'eazydocs' ),
						'default'    => esc_html__( 'Name', 'eazydocs' ),
					),
					array(
						'id'         => 'subscriptions_name_placeholder',
						'type'       => 'text',
						'title'      => ' ',
						'default'    => esc_html__( 'Enter your name', 'eazydocs' )
					),

					array(
						'id'         => 'subscriptions_email_label',
						'type'       => 'text',
						'title'      => esc_html__( 'Input :: Email', 'eazydocs' ),
						'default'    => esc_html__( 'Email', 'eazydocs' ),
					),
					array(
						'id'         => 'subscriptions_email_placeholder',
						'type'       => 'text',
						'title'      => ' ',
						'default'    => esc_html__( 'Enter your email', 'eazydocs' )
					), 
					
					array(
						'id'         => 'subscriptions_submit_btn',
						'type'       => 'text',
						'title'      => esc_html__( 'Submit Button', 'eazydocs' ),
						'default'    => esc_html__( 'Subscribe', 'eazydocs' ),
					),
	
					array(
						'id'         => 'subscriptions_cancel_btn',
						'type'       => 'text',
						'title'      => esc_html__( 'Cancel Button', 'eazydocs' ),
						'default'    => esc_html__( 'Cancel', 'eazydocs' ),
					),
	
					// messages				 
					array(
						'id'         => 'subscriptions_success',
						'type'       => 'text',
						'title'      => esc_html__( 'Success', 'eazydocs' ),
						'default'    => esc_html__( 'Confirmation email sent successfully!', 'eazydocs' ),
					),
					
					array(
						'id'         => 'subscriptions_email_exist',
						'type'       => 'text',
						'title'      => esc_html__( 'Email exist', 'eazydocs' ),
						'default'    => esc_html__( 'Already email exists.', 'eazydocs' ),
					),
				
					array(
						'id'         => 'subscriptions_special_character',
						'type'       => 'text',
						'title'      => esc_html__( 'Special Character', 'eazydocs' ),
						'default'    => esc_html__( 'Special characters not allowed!', 'eazydocs' ),
					),
					
				)
			  ),
			  array(
				'title'     =>  esc_html__( 'Unsubscribe', 'eazydocs' ),
				'fields'    => array(
				  
					array(
						'id'         => 'unsubscriptions_btn',
						'type'       => 'text',
						'title'      => esc_html__( 'Button', 'eazydocs' ),
						'default'    => esc_html__( 'Unsubscribe', 'eazydocs' ),
					),
					
					array(
						'id'         => 'unsubscriptions_heading',
						'type'       => 'text',
						'title'      => esc_html__( 'Heading', 'eazydocs' ),
						'default'    => esc_html__( 'Unsubscribe', 'eazydocs' ),
					),
					
					array(
						'id'         => 'unsubscriptions_desc',
						'type'       => 'textarea',
						'title'      => esc_html__( 'Description', 'eazydocs' ),
						'default'    => esc_html__( "Are you sure you'd like to stop receiving updates", 'eazydocs' ),
					),
					
					array(
						'id'         => 'unsubscriptions_post_title',
						'type'       => 'checkbox',
						'title'      => esc_html__( 'Post title', 'eazydocs' ),
						'text_on'    => esc_html__( 'Yes', 'eazydocs' ),
						'text_off'   => esc_html__( 'No', 'eazydocs' ),
						'label'	 	 => esc_html__( 'Include post title in the description', 'eazydocs' ),
						'default'    => true,
					),
					
					array(
						'id'         => 'unsubscriptions_submit_btn',
						'type'       => 'text',
						'title'      => esc_html__( 'Submit Button', 'eazydocs' ),
						'default'    => esc_html__( 'Confirm', 'eazydocs' ),
					),
	
					array(
						'id'         => 'unsubscriptions_cancel_btn',
						'type'       => 'text',
						'title'      => esc_html__( 'Cancel Button', 'eazydocs' ),
						'default'    => esc_html__( 'Cancel', 'eazydocs' ),
					),

				)
			  ),
			)
		)
	)
) );