<?php
/**
 * Single Documentation Page Settings
 * Configure layout, appearance, and features for individual doc pages.
 */
if (!defined('ABSPATH')) {
	exit;
}


// Single Doc Fields
CSF::createSection($prefix, array(
	'id' => 'single_doc',
	'title' => esc_html__('Single Doc Page', 'eazydocs'),
	'icon' => 'dashicons dashicons-media-document',
));


//
// Single Doc > General
//
CSF::createSection($prefix, array(
	'parent' => 'single_doc',
	'title' => esc_html__('Layout & Display', 'eazydocs'),
	'icon' => '',
	'fields' => array(
		array(
			'id' => 'docs_single_layout',
			'type' => 'image_select',
			'title' => esc_html__('Page Layout', 'eazydocs'),
			'subtitle' => esc_html__('Choose the sidebar arrangement for your documentation pages.', 'eazydocs'),
			'options' => array(
				'both_sidebar' => EAZYDOCS_IMG . '/customizer/both_sidebar.jpg',
				'left_sidebar' => EAZYDOCS_IMG . '/customizer/sidebar_left.jpg',
				'right_sidebar' => EAZYDOCS_IMG . '/customizer/sidebar_right.jpg',
			),
			'default' => 'both_sidebar',
			'class' => 'single-layout-img-wrap eazydocs-pro-notice active-theme',
		),

		array(
			'id' => 'docs_page_width',
			'type' => 'select',
			'title' => esc_html__('Content Width', 'eazydocs'),
			'subtitle' => esc_html__('Set the maximum width of the documentation content area.', 'eazydocs'),
			'options' => [
				'boxed' => esc_html__('Boxed (Centered)', 'eazydocs'),
				'full-width' => esc_html__('Full Width', 'eazydocs'),
			],
			'default' => 'boxed'
		),

		array(
			'id' => 'content-bg',
			'type' => 'color',
			'title' => esc_html__('Page Background', 'eazydocs'),
			'subtitle' => esc_html__('Background color for the documentation content area.', 'eazydocs'),
			'output' => 'body.single-docs .doc_documentation_area',
			'output_mode' => 'background-color',
		),

		array(
			'id' => 'is_featured_image',
			'type' => 'switcher',
			'title' => esc_html__('Featured Image', 'eazydocs'),
			'subtitle' => esc_html__('Display the featured image at the top of each documentation page.', 'eazydocs'),
			'text_on' => esc_html__('Show', 'eazydocs'),
			'text_off' => esc_html__('Hide', 'eazydocs'),
			'default' => false,
			'text_width' => 72,
		),

		array(
			'title' => esc_html__('Ajax Page Loading', 'eazydocs'),
			'subtitle' => esc_html__('Load documentation pages dynamically without full page refresh for faster navigation.', 'eazydocs'),
			'id' => 'is_doc_ajax',
			'type' => 'switcher',
			'text_on' => esc_html__('Enabled', 'eazydocs'),
			'text_off' => esc_html__('Disabled', 'eazydocs'),
			'text_width' => 94,
			'default' => false,
			'class' => 'eazydocs-pro-notice active-theme-docy active-theme-docly active-theme-ama',
		),

		ezd_csf_switcher_field([
			'id' => 'is_doc_tag',
			'title' => esc_html__('Document Tags', 'eazydocs'),
			'subtitle' => esc_html__('Display associated tags at the bottom of each documentation page.', 'eazydocs'),
			'text_width' => 70,
			'default' => true,
		]),

		// Meta Information
		array(
			'type' => 'heading',
			'content' => esc_html__('Header Information', 'eazydocs'),
			'subtitle' => esc_html__('Configure the metadata displayed in the documentation header area.', 'eazydocs'),
		),

		ezd_csf_switcher_field([
			'id' => 'is_parent_doc',
			'title' => esc_html__('Parent Doc Badge', 'eazydocs'),
			'subtitle' => esc_html__('Show a link badge to the parent documentation category.', 'eazydocs'),
			'default' => false
		]),

		array(
			'id' => 'parent_doc_bg',
			'type' => 'background',
			'title' => esc_html__('Badge Background', 'eazydocs'),
			'subtitle' => esc_html__('Background styling for the parent doc badge.', 'eazydocs'),
			'output' => '.single-docs .shortcode_title .ezd-doc-badge',
			'dependency' => array('is_parent_doc', '==', 'true')
		),

		array(
			'id' => 'parent_doc_color',
			'type' => 'color',
			'title' => esc_html__('Badge Text Color', 'eazydocs'),
			'subtitle' => esc_html__('Text color for the parent doc badge.', 'eazydocs'),
			'output' => '.single-docs .shortcode_title .ezd-doc-badge',
			'dependency' => array('is_parent_doc', '==', 'true')
		),

		ezd_csf_switcher_field([
			'id' => 'is_doc_title',
			'title' => esc_html__('Document Title', 'eazydocs'),
			'subtitle' => esc_html__('Display the document title prominently at the top of the content area.', 'eazydocs'),
			'default' => true,
		]),

		ezd_csf_switcher_field([
			'id' => 'enable-reading-time',
			'title' => esc_html__('Reading Time Estimate', 'eazydocs'),
			'subtitle' => esc_html__('Show estimated time required to read the document.', 'eazydocs'),
			'default' => true
		]),

		ezd_csf_switcher_field([
			'id' => 'enable-views',
			'title' => esc_html__('View Counter', 'eazydocs'),
			'subtitle' => esc_html__('Display the number of times this document has been viewed.', 'eazydocs'),
			'default' => true
		]),

		ezd_csf_switcher_field([
			'id' => 'enable-unique-views',
			'title' => esc_html__('Unique Visitors Only', 'eazydocs'),
			'subtitle' => esc_html__('Count only unique visitors, ignoring page refreshes from the same user.', 'eazydocs'),
			'default' => false,
			'class' => 'eazydocs-pro-notice',
			'dependency' => array(
				array('enable-views', '==', 'true'),
			)
		]),

		// Excerpt settings
		array(
			'type' => 'heading',
			'content' => esc_html__('Summary / Excerpt', 'eazydocs'),
		),

		ezd_csf_switcher_field([
			'id' => 'is_excerpt',
			'title' => esc_html__('Document Summary', 'eazydocs'),
			'subtitle' => esc_html__('Display a brief summary or excerpt at the top of the document.', 'eazydocs'),
			'default' => true,
		]),

		array(
			'id' => 'excerpt_label',
			'type' => 'text',
			'title' => esc_html__('Summary Label', 'eazydocs'),
			'subtitle' => esc_html__('Text label shown before the document summary.', 'eazydocs'),
			'default' => esc_html__('Summary: ', 'eazydocs'),
			'dependency' => array('is_excerpt', '==', 'true'),
		),

		array(
			'id' => 'is_full_excerpt',
			'type' => 'switcher',
			'title' => esc_html__('Full Summary', 'eazydocs'),
			'subtitle' => esc_html__('Show the complete excerpt without truncation.', 'eazydocs'),
			'default' => false,
			'text_on' => esc_html__('Yes', 'eazydocs'),
			'text_off' => esc_html__('No', 'eazydocs'),
			'dependency' => array('is_excerpt', '==', 'true'),
		),

		array(
			'title' => esc_html__('Summary Word Limit', 'eazydocs'),
			'subtitle' => esc_html__('Maximum number of words to display in the summary.', 'eazydocs'),
			'desc' => esc_html__('If no excerpt is set, it will be automatically generated from the document content.', 'eazydocs'),
			'id' => 'doc_sec_excerpt_limit',
			'type' => 'slider',
			'default' => 12,
			"min" => 1,
			"step" => 1,
			"max" => 500,
			'dependency' => array(
				array('is_excerpt', '==', 'true'),
				array('is_full_excerpt', '==', 'false'),
			)
		),

		// Articles
		array(
			'type' => 'heading',
			'content' => esc_html__('Child Articles', 'eazydocs'),
		),

		array(
			'id' => 'is_articles',
			'type' => 'switcher',
			'title' => esc_html__('Related Articles List', 'eazydocs'),
			'subtitle' => esc_html__('Display a list of child documentation pages below the current document.', 'eazydocs'),
			'default' => true,
		),

		array(
			'id' => 'articles_title',
			'type' => 'text',
			'title' => esc_html__('Section Heading', 'eazydocs'),
			'subtitle' => esc_html__('Title for the related articles section.', 'eazydocs'),
			'default' => esc_html__('Articles', 'eazydocs'),
			'dependency' => array('is_articles', '==', 'true'),
		),

		// Doc Footer Elements
		array(
			'type' => 'heading',
			'content' => esc_html__('Page Footer', 'eazydocs'),
		),

		array(
			'id' => 'enable-comment',
			'type' => 'switcher',
			'title' => esc_html__('Comments Section', 'eazydocs'),
			'subtitle' => esc_html__('Allow visitors to leave comments and questions on documentation pages.', 'eazydocs'),
			'text_on' => esc_html__('Enabled', 'eazydocs'),
			'text_off' => esc_html__('Disabled', 'eazydocs'),
			'text_width' => 92,
			'default' => true
		),

		array(
			'id' => 'enable-next-prev-links',
			'type' => 'switcher',
			'title' => esc_html__('Navigation Links', 'eazydocs'),
			'subtitle' => esc_html__('Show "Previous" and "Next" article links for sequential reading.', 'eazydocs'),
			'default' => false,
			'class' => 'eazydocs-pro-notice'
		),

		array(
			'id' => 'eazydocs-enable-credit',
			'type' => 'switcher',
			'title' => esc_html__('Footer Credit', 'eazydocs'),
			'subtitle' => esc_html__('Display a credit line at the bottom of documentation pages.', 'eazydocs'),
			'default' => true,
		),

		array(
			'id' => 'eazydocs-credit-text',
			'type' => 'wp_editor',
			'title' => esc_html__('Credit Text', 'eazydocs'),
			'subtitle' => esc_html__('Customize the footer credit message. HTML links are supported.', 'eazydocs'),
			'tinymce' => true,
			'quicktags' => false,
			'media_buttons' => false,
			'height' => '80px',
			'dependency' => array(
				array('eazydocs-enable-credit', '==', 'true')
			),
			'default' => 'Powered By <a href="https://wordpress.org/plugins/eazydocs/">EazyDocs</a>',
		),
	)
));

/**
 * Single Doc > Search Banner
 */
CSF::createSection($prefix, array(
	'parent' => 'single_doc',
	'title' => esc_html__('Search Header', 'eazydocs'),
	'icon' => '',
	'fields' => array(
		array(
			'type' => 'subheading',
			'title' => esc_html__('Search Banner Configuration', 'eazydocs'),
			'subtitle' => esc_html__('Customize the search header that appears above documentation pages.', 'eazydocs'),
		),

		array(
			'id' => 'search_banner_layout',
			'type' => 'select',
			'title' => esc_html__('Banner Type', 'eazydocs'),
			'options' => [
				'default' => esc_html__('Built-in Banner', 'eazydocs'),
				'el-template' => esc_html__('Custom Elementor Template', 'eazydocs'),
			],
			'default' => 'default',
			'subtitle' => esc_html__('Choose between the default search banner or a custom Elementor template.', 'eazydocs'),
		),

		array(
			'id' => 'single_layout_id',
			'type' => 'select',
			'title' => esc_html__('Elementor Template', 'eazydocs'),
			'subtitle' => esc_html__('Select a saved Elementor template. <a target="_blank" href="https://shorturl.at/filGI">Learn how to create templates</a>', 'eazydocs'),
			'options' => ezd_get_elementor_templates(),
			'dependency' => array('search_banner_layout', '==', 'el-template'),
		),

		ezd_csf_switcher_field([
			'id' => 'is_search_banner',
			'title' => esc_html__('Show Search Banner', 'eazydocs'),
			'subtitle' => esc_html__('Display the search banner header on documentation pages.', 'eazydocs'),
			'default' => true,
			'dependency' => array('search_banner_layout', '==', 'default'),
			'text_width' => 72
		]),

		array(
			'id' => 'is_search_submit',
			'type' => 'switcher',
			'title' => esc_html__('Enter Key Search', 'eazydocs'),
			'subtitle' => esc_html__('Allow users to submit search by pressing Enter or clicking the search icon.', 'eazydocs'),
			'text_on' => esc_html__('Enable', 'eazydocs'),
			'text_off' => esc_html__('Disabled', 'eazydocs'),
			'default' => true,
			'dependency' => array(
				array('is_search_banner', '==', 'true'),
				array('search_banner_layout', '==', 'default')
			),
			'text_width' => 85
		),

		array(
			'id' => 'doc_banner_bg',
			'type' => 'background',
			'title' => esc_html__('Banner Background', 'eazydocs'),
			'subtitle' => esc_html__('Background color or image for the search banner area.', 'eazydocs'),
			'output' => '.ezd_search_banner.has_bg_dark',
			'dependency' => array(
				array('is_search_banner', '==', 'true'),
				array('search_banner_layout', '==', 'default'),
			)
		),

		array(
			'id' => 'search_banner_padding',
			'type' => 'spacing',
			'title' => esc_html__('Banner Spacing', 'eazydocs'),
			'subtitle' => esc_html__('Adjust the internal padding of the search banner.', 'eazydocs'),
			'output' => 'body .ezd_search_banner',
			'dependency' => array(
				array('is_search_banner', '==', 'true'),
				array('search_banner_layout', '==', 'default'),
			),
			'default' => array(
				'unit' => 'px',
			),
		),

		//Search Keywords
		array(
			'type' => 'subheading',
			'title' => esc_html__('Search Keywords', 'eazydocs'),
			'dependency' => array(
				array('is_search_banner', '==', 'true'),
				array('search_banner_layout', '==', 'default'),
			),
		),

		array(
			'id' => 'is_keywords',
			'type' => 'switcher',
			'title' => esc_html__('Keywords', 'eazydocs'),
			'text_on' => esc_html__('Enabled', 'eazydocs'),
			'text_off' => esc_html__('Disabled', 'eazydocs'),
			'default' => false,
			'text_width' => 96,
			'dependency' => array(
				array('is_search_banner', '==', 'true'),
				array('search_banner_layout', '==', 'default'),
			),
			'class' => 'eazydocs-pro-notice'
		),

		array(
			'id' => 'keywords_label',
			'type' => 'text',
			'title' => esc_html__('Keywords Label', 'eazydocs'),
			'default' => esc_html__('Popular Searches:', 'eazydocs'),
			'dependency' => array(
				array('is_search_banner', '==', 'true'),
				array('is_keywords', '==', 'true'),
				array('search_banner_layout', '==', 'default'),
			),
			'class' => 'eazydocs-pro-notice'
		),

		array(
			'id' => 'keywords_label_color',
			'type' => 'color',
			'title' => esc_html__('Label Color', 'eazydocs'),
			'output_mode' => 'color',
			'output' => '.ezd_search_keywords .label',
			'output_important' => true,
			'dependency' => array(
				array('is_search_banner', '==', 'true'),
				array('is_keywords', '==', 'true'),
				array('search_banner_layout', '==', 'default'),
			),
			'class' => 'eazydocs-pro-notice'
		),

		// keyword by dynamic || static select
		array(
			'id' => 'keywords_by',
			'type' => 'select',
			'title' => esc_html__('Keywords By', 'eazydocs'),
			'subtitle' => esc_html__('Select your preferred keywords type.', 'eazydocs'),
			'desc' => esc_html__('Static keywords are predefined, while dynamic keywords are generated by queries from website visitors', 'eazydocs'),
			'options' => array(
				'static' => esc_html__('Static', 'eazydocs'),
				'dynamic' => esc_html__('Dynamic (Sort by popular)', 'eazydocs'),
			),
			'default' => 'static',
			'dependency' => array(
				array('is_keywords', '==', 'true'),
				array('is_search_banner', '==', 'true'),
				array('search_banner_layout', '==', 'default'),
			),
			'class' => 'eazydocs-pro-notice'
		),

		array(
			'id' => 'keywords_limit',
			'type' => 'slider',
			'title' => esc_html__('Keywords Limit', 'eazydocs'),
			'subtitle' => esc_html__('Set the number of keywords to show.', 'eazydocs'),
			'default' => 6,
			'min' => 1,
			'max' => 200,
			'step' => 1,
			'dependency' => array(
				array('is_search_banner', '==', 'true'),
				array('is_keywords', '==', 'true'),
				array('search_banner_layout', '==', 'default'),
				array('keywords_by', '==', 'dynamic'),
			),
			'class' => 'eazydocs-pro-notice',
		),

		// not found keywords exclude checkbox
		array(
			'id' => 'is_exclude_not_found',
			'type' => 'switcher',
			'title' => esc_html__('Exclude Not Found Keywords', 'eazydocs'),
			'subtitle' => esc_html__('Exclude the keywords that are not found in the search results.', 'eazydocs'),
			'text_on' => esc_html__('Yes', 'eazydocs'),
			'text_off' => esc_html__('No', 'eazydocs'),
			'default' => false,
			'text_width' => 70,
			'dependency' => array(
				array('is_search_banner', '==', 'true'),
				array('is_keywords', '==', 'true'),
				array('search_banner_layout', '==', 'default'),
				array('keywords_by', '==', 'dynamic'),
			),
			'class' => 'eazydocs-pro-notice',
		),

		array(
			'id' => 'keywords',
			'type' => 'repeater',
			'title' => esc_html__('Keywords', 'eazydocs'),
			'dependency' => array(
				array('is_search_banner', '==', 'true'),
				array('is_keywords', '==', 'true'),
				array('search_banner_layout', '==', 'default'),
				array('keywords_by', '==', 'static'),
			),
			'fields' => array(
				array(
					'id' => 'title',
					'type' => 'text',
					'title' => esc_html__('Keyword', 'eazydocs')
				),
			),
			'default' => array(
				array(
					'title' => esc_html__('Keyword #1', 'eazydocs'),
				),
				array(
					'title' => esc_html__('Keyword #2', 'eazydocs'),
				),
			),
			'class' => 'eazydocs-pro-notice',
			'button_title' => esc_html__('Add New', 'eazydocs'),
		),

		array(
			'id' => 'keywords_color',
			'type' => 'color',
			'title' => esc_html__('Keywords Color', 'eazydocs'),
			'output_mode' => 'color',
			'output' => '.ezd_search_banner .header_search_keyword ul li a',
			'dependency' => array(
				array('is_search_banner', '==', 'true'),
				array('is_keywords', '==', 'true'),
				array('search_banner_layout', '==', 'default'),
			),
			'class' => 'eazydocs-pro-notice'
		),

		array(
			'type' => 'subheading',
			'title' => esc_html__('Ajax Search Results', 'eazydocs'),
			'subtitle' => esc_html__('The Search Results settings is global. This settings will be applied to all Ajax Doc Search Results in the plugin.', 'eazydocs'),
			'dependency' => array(
				array('is_search_banner', '==', 'true'),
				array('search_banner_layout', '==', 'default'),
			)
		),

		ezd_csf_switcher_field([
			'id' => 'is_search_result_breadcrumb',
			'title' => esc_html__('Breadcrumb', 'eazydocs'),
			'subtitle' => esc_html__('Show / Hide the breadcrumbs in search results', 'eazydocs'),
			'default' => false,
			'text_width' => 70,
			'dependency' => array(
				array('is_search_banner', '==', 'true'),
				array('search_banner_layout', '==', 'default'),
			),
			'class' => 'eazydocs-pro-notice'
		]),

		ezd_csf_switcher_field([
			'id' => 'is_search_result_thumbnail',
			'title' => esc_html__('Thumbnail', 'eazydocs'),
			'subtitle' => esc_html__('Show / Hide the thumbnail in search results', 'eazydocs'),
			'default' => false,
			'text_width' => 70,
			'dependency' => array(
				array('is_search_banner', '==', 'true'),
				array('search_banner_layout', '==', 'default'),
			),
			'class' => 'eazydocs-pro-notice'
		]),

		array(
			'id' => 'search_by',
			'type' => 'select',
			'title' => esc_html__('Search Mode', 'eazydocs'),
			'desc' => sprintf(
				esc_html__(
					'%1$sSelect how you want the search to work.%2$s %3$s
					• %1$sBy Title Only:%2$s Shows results that match your search keywords in titles. %3$s
					• %1$sBy Title and Content:%2$s First shows title matches, then includes results that match inside the content.',
					'eazydocs'
				),
				'<b>',
				'</b>',
				'<br />'
			),
			'options' => array(
				'title_only' => esc_html__('Search in Titles Only', 'eazydocs'),
				'title_and_content' => esc_html__('Search in Titles and Content', 'eazydocs'),
			),

			'default' => 'title_only',
			'dependency' => array(
				array('is_search_banner', '==', 'true'),
				array('search_banner_layout', '==', 'default'),
			),
			'class' => 'eazydocs-pro-notice',
		)
	)
));

/**
 * Single Doc > Breadcrumbs Fields
 */
CSF::createSection($prefix, array(
	'parent' => 'single_doc',
	'title' => esc_html__('Breadcrumbs', 'eazydocs'),
	'icon' => '',
	'fields' => array(
		ezd_csf_switcher_field([
			'id' => 'docs-breadcrumb',
			'title' => esc_html__('Show/Hide Breadcrumb', 'eazydocs'),
			'subtitle' => esc_html__('Toggle this switch to Show/Hide the Breadcrumb bar.', 'eazydocs'),
			'text_width' => 70,
			'default' => true,
		]),

		array(
			'id' => 'breadcrumb-home-text',
			'type' => 'text',
			'title' => esc_html__('Frontpage Name', 'eazydocs'),
			'default' => esc_html__('Home', 'eazydocs'),
			'dependency' => array(
				'docs-breadcrumb',
				'==',
				'true'
			),
		),

		array(
			'id' => 'docs-page-title',
			'type' => 'text',
			'title' => esc_html__('Docs Archive Page Title', 'eazydocs'),
			'default' => esc_html__('Docs', 'eazydocs'),
			'dependency' => array(
				'docs-breadcrumb',
				'==',
				'true'
			),
		),

		array(
			'id' => 'breadcrumb-update-text',
			'type' => 'text',
			'title' => esc_html__('Updated Text', 'eazydocs'),
			'default' => esc_html__('Updated on', 'eazydocs'),
			'dependency' => array(
				'docs-breadcrumb',
				'==',
				'true'
			)
		),
	)
));

//
// Doc Left Sidebar Fields
//
CSF::createSection($prefix, array(
	'parent' => 'single_doc',
	'title' => esc_html__('Left Sidebar', 'eazydocs'),
	'icon' => '',
	'fields' => array(
		array(
			'id' => 'docs_content_layout',
			'type' => 'radio',
			'title' => esc_html__('Docs Navigation Layout', 'eazydocs'),
			'options' => [
				'badge_base' => esc_html__('Collapsed with Icons', 'eazydocs'),
				'category_base' => esc_html__('Extended Docs', 'eazydocs'),
			],
			'default' => 'badge_base',
			'class' => 'eazydocs-pro-notice',
		),

		array(
			'id' => 'docs_to_view',
			'type' => 'radio',
			'title' => esc_html__('Docs to view', 'eazydocs'),
			'subtitle' => esc_html__('Select All Docs to display all the top label docs or choose Self Docs to show child docs of the current doc.', 'eazydocs'),
			'options' => [
				'all_docs' => esc_html__('All Docs (Archive)', 'eazydocs'),
				'self_docs' => esc_html__('Single (Self) Doc', 'eazydocs'),
			],
			'default' => 'self_docs',
			'class' => 'eazydocs-pro-notice',
		),

		array(
			'id' => 'toggle_visibility',
			'type' => 'switcher',
			'title' => esc_html__('Sidebar Toggle', 'eazydocs'),
			'subtitle' => esc_html__('Collapse and Expand the left Sidebar with a Toggle button.', 'eazydocs'),
			'text_on' => esc_html__('Show', 'eazydocs'),
			'text_off' => esc_html__('Hide', 'eazydocs'),
			'text_width' => 72,
			'default' => true,
		),

		ezd_csf_switcher_field([
			'id' => 'search_visibility',
			'title' => esc_html__('Filter Form', 'eazydocs'),
			'subtitle' => esc_html__('Filter the left sidebar doc items by typing latter.', 'eazydocs'),
			'text_width' => 72,
			'default' => true,
		]),

		array(
			'id' => 'search_mark_word',
			'type' => 'switcher',
			'title' => esc_html__('Mark Words', 'eazydocs'),
			'subtitle' => esc_html__('Highlight the typed keyword in the docs.', 'eazydocs'),
			'text_on' => esc_html__('Enable', 'eazydocs'),
			'text_off' => esc_html__('Disable', 'eazydocs'),
			'text_width' => 85,
			'default' => false,
			'class' => 'eazydocs-pro-notice',
		),

		ezd_csf_switcher_field([
			'id' => 'doc_sec_icon_type',
			'title' => esc_html__('Featured Image', 'eazydocs'),
			'subtitle' => esc_html__('Enable this switcher to use featured image for the Doc sections icon.', 'eazydocs'),
			'default' => false,
		]),

		array(
			'title' => esc_html__('Doc Section Icon', 'eazydocs'),
			'subtitle' => esc_html__(
				"This is the Doc's default icon. If you don't use icon for the article section individually, this icon will be shown.",
				'eazydocs'
			),
			'id' => 'doc_sec_icon',
			'type' => 'media',
			'default' => array(
				'url' => EAZYDOCS_IMG . '/icon/folder-closed.png'
			)
		),

		array(
			'title' => esc_html__('Doc Section Icon Open', 'eazydocs'),
			'subtitle' => esc_html__(
				"This is the Doc's default icon. If you don't use icon for the article section individually, this icon will be shown on open states of the Doc sections.",
				'eazydocs'
			),
			'id' => 'doc_sec_icon_open',
			'type' => 'media',
			'default' => array(
				'url' => EAZYDOCS_IMG . '/icon/folder-open.png'
			)
		),

		array(
			'id' => 'action_btn_typo',
			'type' => 'typography',
			'title' => esc_html__('Doc Title Typography', 'eazydocs'),
			'output' => '.doc_left_sidebarlist .doc-title',
		),

		array(
			'id' => 'docs-sidebar-bg',
			'type' => 'color',
			'title' => esc_html__('Background Color', 'eazydocs'),
			'output_mode' => 'background-color',
			'output' => '.doc_left_sidebarlist:before,.doc_left_sidebarlist:after',
		)
	)
));

//
// Doc Right Sidebar Fields
//
CSF::createSection($prefix, array(
	'parent' => 'single_doc',
	'title' => esc_html__('Right Sidebar', 'eazydocs'),
	'icon' => '',
	'fields' => array(
		array(
			'type' => 'heading',
			'title' => esc_html__('Sharing Doc', 'eazydocs'),
		),

		array(
			'id' => 'is_social_links',
			'type' => 'switcher',
			'title' => esc_html__('Share Button', 'eazydocs'),
			'text_on' => esc_html__('Enabled', 'eazydocs'),
			'text_off' => esc_html__('Disabled', 'eazydocs'),
			'text_width' => 92,
			'default' => true
		),

		array(
			'id' => 'share_btn_label',
			'type' => 'text',
			'title' => esc_html__('Share Button Label', 'eazydocs'),
			'default' => esc_html__('Share this Doc', 'eazydocs'),
			'dependency' => array(
				array('is_copy_link', '==', '1'),
				array('is_social_links', '==', '1'),
			)
		),

		ezd_csf_switcher_field([
			'id' => 'is_copy_link',
			'title' => esc_html__('Copy Link Button', 'eazydocs'),
			'dependency' => array('is_social_links', '==', '1'),
			'text_width' => 72,
			'default' => true,
		]),

		array(
			'id' => 'copy_link_label',
			'type' => 'text',
			'title' => esc_html__('Copy Link Label', 'eazydocs'),
			'default' => esc_html__('Or copy link', 'eazydocs'),
			'dependency' => array(
				array('is_copy_link', '==', '1'),
				array('is_social_links', '==', '1'),
			)
		),

		array(
			'id' => 'copy_link_text_success',
			'type' => 'text',
			'title' => esc_html__('Success Message', 'eazydocs'),
			'default' => esc_html__('URL copied to clipboard', 'eazydocs'),
			'dependency' => array(
				array('is_copy_link', '==', '1'),
				array('is_social_links', '==', '1'),
			)
		),

		array(
			'type' => 'heading',
			'title' => esc_html__('Tools', 'eazydocs'),
		),

		ezd_csf_switcher_field([
			'id' => 'font-size-switcher',
			'title' => esc_html__('Font Size Switcher', 'eazydocs'),
			'text_width' => 72,
			'default' => true,
		]),

		array(
			'id' => 'pr-icon-switcher',
			'type' => 'switcher',
			'title' => esc_html__('Print Article', 'eazydocs'),
			'text_on' => esc_html__('Show', 'eazydocs'),
			'text_off' => esc_html__('Hide', 'eazydocs'),
			'text_width' => 72,
			'default' => true,
		),

		array(
			'title' => esc_html__('Widgets Area', 'eazydocs'),
			'desc' => esc_html__("Enable to register a Sidebar Widgets area named 'Doc Right Sidebar' in Appearance > Widgets.", 'eazydocs'),
			'id' => 'is_widget_sidebar',
			'type' => 'switcher',
			'text_on' => esc_html__('Enabled', 'eazydocs'),
			'text_off' => esc_html__('Disabled', 'eazydocs'),
			'text_width' => 92,
			'default' => false,
			'class' => 'eazydocs-pro-notice active-theme-docly'
		),

		// TOC
		array(
			'type' => 'heading',
			'title' => esc_html__('Table on Contents (TOC)', 'eazydocs'),
		),

		array(
			'id' => 'toc_switcher',
			'type' => 'switcher',
			'title' => esc_html__('Enable TOC', 'eazydocs'),
			'desc' => esc_html__('EazyDocs will automatically create a structured Table Of Contents(TOC) while you are writing your documentation.', 'eazydocs'),
			'default' => true,
		),

		array(
			'id' => 'toc_heading',
			'type' => 'text',
			'title' => esc_html__('TOC Heading', 'eazydocs'),
			'default' => esc_html__('CONTENTS', 'eazydocs'),
			'dependency' => array('toc_switcher', '==', '1'),
		),

		ezd_csf_switcher_field([
			'id' => 'toc_auto_numbering',
			'desc' => esc_html__('Enable to add numbers before each table of content item.', 'eazydocs'),
			'title' => esc_html__('Auto Numbering', 'eazydocs'),
			'text_width' => 72,
			'dependency' => array('toc_switcher', '==', '1'),
		]),

		// Conditional Dropdown
		array(
			'title' => esc_html__('Conditional Dropdown', 'eazydocs'),
			'type' => 'heading'
		),

		array(
			'title' => esc_html__('Conditional Dropdown', 'eazydocs'),
			'desc' => esc_html__('You can display conditional contents using the [conditional_data] shortcode in documentation based on the dropdown value. See the shortcode usage tutorial <a href="https://tinyurl.com/yd46mfax" target="_blank">here</a>.', 'eazydocs'),
			'id' => 'is_conditional_dropdown',
			'type' => 'switcher',
			'text_on' => esc_html__('Enabled', 'eazydocs'),
			'text_off' => esc_html__('Disabled', 'eazydocs'),
			'text_width' => 92,
			'default' => false,
			'class' => 'eazydocs-pro-notice active-theme'
		),

		array(
			'title' => esc_html__('Dropdown Options', 'eazydocs'),
			'id' => 'condition_options',
			'type' => 'repeater',
			'fields' => array(
				array(
					'title' => esc_html__('Title', 'eazydocs'),
					'id' => 'title',
					'type' => 'text',
				),
				array(
					'title' => esc_html__('Icon', 'eazydocs'),
					'id' => 'icon',
					'type' => 'icon',
				),
			),
			'dependency' => array('is_conditional_dropdown', '==', '1'),
			'class' => 'eazydocs-pro-notice active-theme',
			'default' => array(
				array(
					'title' => esc_html__('Windows', 'eazydocs'),
					'icon' => 'icon_desktop',
				),
				array(
					'title' => esc_html__('iOs', 'eazydocs'),
					'icon' => 'icon_easel_alt',
				),
				array(
					'title' => esc_html__('Linux', 'eazydocs'),
					'icon' => 'icon_laptop',
				),
			),
			'button_title' => esc_html__('Add New', 'eazydocs'),
		),
	)
));

CSF::createSection($prefix, array(
	'id' => 'doc_related_articles',
	'parent' => 'single_doc',
	'title' => esc_html__('Related Articles', 'eazydocs'),
	'icon' => '',
	'fields' => array(
		array(
			'type' => 'heading',
			'title' => esc_html__('Related Docs Settings', 'eazydocs')
		),

		ezd_csf_switcher_field([
			'id' => 'related-docs',
			'title' => esc_html__('Related Docs', 'eazydocs'),
			'default' => true,
			'text_width' => 72
		]),

		array(
			'id' => 'related-docs-title',
			'type' => 'text',
			'title' => esc_html__('Title', 'eazydocs'),
			'default' => esc_html__('Related articles', 'eazydocs'),
			'dependency' => array(
				'related-docs',
				'==',
				'true'
			)
		),

		array(
			'id' => 'related-visible-docs',
			'type' => 'number',
			'title' => esc_html__('Docs Number', 'eazydocs'),
			'default' => esc_html__('4', 'eazydocs'),
			'dependency' => array(
				'related-docs',
				'==',
				'true'
			)
		),

		array(
			'id' => 'related-doc-column',
			'type' => 'select',
			'title' => esc_html__('Column Width', 'eazydocs'),
			'options' => [
				'6' => esc_html__('Half', 'eazydocs'),
				'12' => esc_html__('Fullwidth', 'eazydocs'),
			],
			'dependency' => array(
				'related-docs',
				'==',
				'true'
			),
			'default' => '6'
		),

		array(
			'id' => 'related-docs-more-btn',
			'type' => 'text',
			'title' => esc_html__('Button', 'eazydocs'),
			'default' => esc_html__('See More', 'eazydocs'),
			'dependency' => array(
				'related-docs',
				'==',
				'true'
			)
		),
	)
));


CSF::createSection($prefix, array(
	'id' => 'doc_viewed_articles',
	'parent' => 'single_doc',
	'title' => esc_html__('Viewed Articles', 'eazydocs'),
	'icon' => '',
	'fields' => array(
		array(
			'type' => 'heading',
			'title' => esc_html__('Recently Viewed Docs Options', 'eazydocs')
		),

		ezd_csf_switcher_field([
			'id' => 'viewed-docs',
			'title' => esc_html__('Recently Viewed Docs', 'eazydocs'),
			'default' => true,
			'text_width' => 72
		]),

		array(
			'id' => 'viewed-docs-title',
			'type' => 'text',
			'title' => esc_html__('Title', 'eazydocs'),
			'default' => esc_html__('Recently Viewed articles', 'eazydocs'),
			'dependency' => array(
				'viewed-docs',
				'==',
				'true'
			)
		),

		array(
			'id' => 'viewed-visible-docs',
			'type' => 'number',
			'title' => esc_html__('Docs Number', 'eazydocs'),
			'default' => esc_html__('4', 'eazydocs'),
			'dependency' => array(
				'viewed-docs',
				'==',
				'true'
			)
		),

		array(
			'id' => 'viewed-doc-column',
			'type' => 'select',
			'title' => esc_html__('Column Width', 'eazydocs'),
			'options' => [
				'6' => esc_html__('Half', 'eazydocs'),
				'12' => esc_html__('Fullwidth', 'eazydocs'),
			],
			'dependency' => array(
				'viewed-docs',
				'==',
				'true'
			),
			'default' => '6'
		),

		array(
			'id' => 'view-docs-more-btn',
			'type' => 'text',
			'title' => esc_html__('Button', 'eazydocs'),
			'default' => esc_html__('See More', 'eazydocs'),
			'dependency' => array(
				'viewed-docs',
				'==',
				'true'
			)
		),
	)
));