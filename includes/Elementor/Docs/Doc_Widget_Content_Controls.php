<?php
namespace EazyDocs\Elementor\Docs;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

use Elementor\Repeater;
use Elementor\Controls_Manager;
use Elementor\Group_Control_Typography;
use Elementor\Group_Control_Text_Shadow;

trait Doc_Widget_Content_Controls {
	/**
	 * Name: elementor_content_control()
	 * Desc: Register the Content Tab output on the Elementor editor.
	 * Params: no params
	 * Return: @void
	 * Author: spider-themes
	 */
	public function elementor_content_control()
	{
		// ---Start Document Setting
		$this->start_controls_section(
			'doc_design_sec',
			[
				'label' => esc_html__('Preset Skin', 'eazydocs'),
			]
		);

		$this->add_control(
			'doc-widget-skin',
			[
				'label' => esc_html__('Skins', 'eazydocs'),
				'type' => Controls_Manager::VISUAL_CHOICE,
				'options' => $this->docs_layout_option(),
				'toggle' => false,
				'default' => '1',
				'label_block' => true,
			]
		);

		$this->end_controls_section();

		// --- Layout Options
		$this->start_controls_section(
			'layout',
			[
				'label' => esc_html__('Layout Settings', 'eazydocs'),
			]
		);

		$this->add_control(
			'ppp_column',
			[
				'label' => esc_html__('Column', 'eazydocs'),
				'description' => esc_html__('Number of column to show', 'eazydocs'),
				'type' => Controls_Manager::SELECT,
				'options' => [
					'1' => esc_html__('1 Column', 'eazydocs'),
					'2' => esc_html__('2 Column', 'eazydocs'),
					'3' => esc_html__('3 Column', 'eazydocs'),
					'4' => esc_html__('4 Column', 'eazydocs'),
					'5' => esc_html__('5 Column', 'eazydocs'),
					'6' => esc_html__('6 Column', 'eazydocs'),
				],
				'default' => '3',
				'condition' => [
					'doc-widget-skin' => ['1', '7']
				]
			]
		);

		$this->add_control(
			'doc-widget-tab-alignment',
			[
				'label' => esc_html__('Tab Alignment', 'eazydocs'),
				'type' => Controls_Manager::CHOOSE,
				'options' => [
					'left' => [
						'title' => esc_html__('Left', 'eazydocs'),
						'icon' => 'eicon-h-align-left',
					],
					'center' => [
						'title' => esc_html__('center', 'eazydocs'),
						'icon' => 'eicon-h-align-center',
					],
					'right' => [
						'title' => esc_html__('Right', 'eazydocs'),
						'icon' => 'eicon-h-align-right',
					]
				],
				'toggle' => false,
				'default' => 'center',
				'description' => esc_html__('Choose whether you want to position the tab buttons left, right or center from here.', 'eazydocs'),
				'selectors' => [
					'{{WRAPPER}} .doc_tag_area .doc_tag' => 'justify-content: {{VALUE}};',
					'{{WRAPPER}} .h_doc_documentation_area .documentation_tab' => 'justify-content: {{VALUE}};',
					'{{WRAPPER}} .question_menu.docs3 .nav' => 'justify-content: {{VALUE}};',
					'{{WRAPPER}} .doc4-nav-bar .book-chapter-nav' => 'justify-content: {{VALUE}};',
				],
				'condition' => [
					'doc-widget-skin' => ['2', '3', '4', '5']
				]
			]
		);

		$this->add_control(
			'masonry',
			[
				'label' => esc_html__('Masonry', 'eazydocs'),
				'description' => esc_html__('Enable masonry layout for docs (responsive grid with dynamic column heights).', 'eazydocs'),
				'type' => \Elementor\Controls_Manager::SWITCHER,
				'condition' => [
					'doc-widget-skin' => ['2', '4', '5']
				],
			]
		);

		$this->add_control(
			'show_contributors',
			[
				'label' => esc_html__('Show Contributors', 'eazydocs'),
				'description' => esc_html__('The number of contributors to show.', 'eazydocs'),
				'type' => Controls_Manager::NUMBER,
				'default' => 4,
				'min' => 1,
				'max' => 20,
				'condition' => [
					'doc-widget-skin' => ['6']
				],
			]
		);

		$this->end_controls_section();

		// --- Filter Options
		$this->start_controls_section(
			'document_filter',
			[
				'label' => esc_html__('Filter Options', 'eazydocs'),
			]
		);

		$this->add_control(
			'docs_slug_format',
			[
				'label' => esc_html__('Tab ID Format', 'eazydocs'),
				'description' => esc_html__('If the slug ID does not work then you should pick the number ID.', 'eazydocs'),
				'type' => Controls_Manager::SELECT,
				'options' => [
					'1' => esc_html__('Slug ID', 'eazydocs'),
					'2' => esc_html__('Number ID', 'eazydocs'),
				],
				'condition' => [
					'doc-widget-skin' => ['2', '3', '4', '5']
				],
				'default' => '1'
			]
		);

		$this->add_control(
			'exclude',
			[
				'label' => esc_html__('Exclude Docs', 'eazydocs'),
				'type' => Controls_Manager::SELECT2,
				'options' => ezd_get_posts(),
				'multiple' => true
			]
		);

		$this->add_control(
			'show_section_count',
			[
				'label' => esc_html__('Show Section Count', 'eazydocs'),
				'description' => esc_html__('The number of sections to show under every documentation tab. Leave empty or give value -1 to show all sections.', 'eazydocs'),
				'type' => Controls_Manager::NUMBER,
				'default' => 6,
				'condition' => [
					'doc-widget-skin' => ['2', '3', '4', '5']
				]
			]
		);

		$this->add_control(
			'active_doc',
			[
				'label' => esc_html__('Active Doc', 'eazydocs'),
				'description' => esc_html__('Select the active Doc tab by default.', 'eazydocs'),
				'type' => Controls_Manager::SELECT,
				'options' => ezd_get_posts(),
				'condition' => [
					'doc-widget-skin' => ['2', '3', '5']
				]
			]
		);

		$this->add_control(
			'ppp_doc_items',
			[
				'label' => esc_html__('Show Doc Item', 'eazydocs'),
				'description' => esc_html__('The number of doc items to under every doc sections. Leave empty or give value -1 to show all sections.', 'eazydocs'),
				'type' => Controls_Manager::NUMBER,
				'default' => -1,
				'condition' => [
					'is_custom_order' => '',
					'doc-widget-skin' => ['1', '2', '3', '4', '5']
				],
			]
		);

		$this->add_control(
			'doc_items_articles',
			[
				'label' => esc_html__('Articles', 'eazydocs'),
				'description' => esc_html__('Number of articles to show under every sections', 'eazydocs'),
				'type' => Controls_Manager::NUMBER,
				'default' => 5,
				'condition' => [
					'doc-widget-skin' => ['1', '7']
				]
			]
		);

		$this->add_control(
			'main_doc_excerpt',
			[
				'label' => esc_html__('Main Doc Excerpt', 'eazydocs'),
				'description' => esc_html__('Excerpt word limit of main documentation. If the excerpt got empty, this will get from the post content.', 'eazydocs'),
				'type' => Controls_Manager::NUMBER,
				'default' => 15,
				'condition' => [
					'doc-widget-skin' => ['2', '4']
				]
			]
		);

		$this->add_control(
			'doc_sec_excerpt',
			[
				'label' => esc_html__('Doc Section Excerpt', 'eazydocs'),
				'description' => esc_html__('Excerpt word limit of the documentation sections. If the excerpt got empty, this will get from the post content.', 'eazydocs'),
				'type' => Controls_Manager::NUMBER,
				'default' => 8,
				'condition' => [
					'doc-widget-skin' => '2'
				]
			]
		);

		$this->add_control(
			'is_custom_order',
			[
				'label' => esc_html__('Custom Order', 'eazydocs'),
				'description' => esc_html__('Order the Doc tabs as you want.', 'eazydocs'),
				'type' => \Elementor\Controls_Manager::SWITCHER,
				'return_value' => 'yes',
				'default' => '',
				'separator' => 'before',
				'condition' => [
					'doc-widget-skin' => ['2', '3', '5']
				]
			]
		);

		$this->add_control(
			'order_by',
			[
				'label' => esc_html__('Order By', 'eazydocs'),
				'type' => Controls_Manager::SELECT,
				'options' => [
					'title' => esc_html__('Title', 'eazydocs'),
					'author' => esc_html__('Post Author', 'eazydocs'),
					'date' => esc_html__('Date', 'eazydocs'),
					'id' => esc_html__('Post ID', 'eazydocs'),
					'modified' => esc_html__('Last Modified Date', 'eazydocs'),
					'rand' => esc_html__('Random', 'eazydocs'),
					'menu_order' => esc_html__('Menu Order', 'eazydocs'),
				],
				'default' => 'menu_order',
				'condition' => [
					'is_custom_order' => ''
				]
			]
		);

		$this->add_control(
			'order',
			[
				'label' => esc_html__('Parent Docs Order', 'eazydocs'),
				'type' => Controls_Manager::SELECT,
				'options' => [
					'ASC' => esc_html__('Ascending', 'eazydocs'),
					'DESC' => esc_html__('Descending', 'eazydocs'),
				],
				'default' => 'ASC',
				'condition' => [
					'is_custom_order' => ''
				]
			]
		);

		$this->add_control(
			'child_order',
			[
				'label' => esc_html__('Child Docs Order', 'eazydocs'),
				'type' => Controls_Manager::SELECT,
				'options' => [
					'ASC' => esc_html__('Ascending', 'eazydocs'),
					'DESC' => esc_html__('Descending', 'eazydocs'),
				],
				'default' => 'ASC',
				'condition' => [
					'is_custom_order' => ''
				]
			]
		);

		$doc = new Repeater();

		$doc->add_control(
			'doc',
			[
				'label' => esc_html__('Doc', 'eazydocs'),
				'type' => Controls_Manager::SELECT,
				'options' => ezd_get_posts(),
			]
		);

		$this->add_control(
			'docs',
			[
				'label' => esc_html__('Tabs Items', 'eazydocs'),
				'type' => Controls_Manager::REPEATER,
				'fields' => $doc->get_controls(),
				'title_field' => '{{{ doc }}}',
				'prevent_empty' => false,
				'condition' => [
					'is_custom_order' => 'yes'
				]
			]
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'labels',
			[
				'label' => esc_html__('Labels', 'eazydocs'),
			]
		);

		$this->add_control(
			'is_tab_title_first_word',
			[
				'label' => esc_html__('Tab Title First Word', 'eazydocs'),
				'description' => esc_html__('Show the first word of the doc in Tab Title.', 'eazydocs'),
				'type' => \Elementor\Controls_Manager::SWITCHER,
				'return_value' => 'yes',
				'condition' => [
					'doc-widget-skin' => ['2', '3', '4', '5']
				]
			]
		);

		$this->add_control(
			'read_more',
			[
				'label' => esc_html__('Read More Text', 'eazydocs'),
				'type' => Controls_Manager::TEXT,
				'label_block' => true,
				'default' => esc_html__('View All', 'eazydocs'),
				'condition' => [
					'doc-widget-skin' => ['1', '2', '3', '7']
				]
			]
		);

		$this->add_control(
			'show_subscription',
			[
				'label' => esc_html__('Show Subscribe Button', 'eazydocs'),
				'description' => esc_html__('Let visitors subscribe to a doc for update notifications. Requires the EazyDocs Pro subscription feature.', 'eazydocs'),
				'type' => Controls_Manager::SWITCHER,
				'return_value' => 'yes',
				'default' => '',
				'condition' => [
					'doc-widget-skin' => ['1', '3', '7']
				]
			]
		);

		$this->add_control(
			'subscription_text',
			[
				'label' => esc_html__('Subscribe Button Text', 'eazydocs'),
				'type' => Controls_Manager::TEXT,
				'label_block' => true,
				'default' => '',
				'placeholder' => esc_html__('Subscribe', 'eazydocs'),
				'condition' => [
					'doc-widget-skin' => ['1', '3', '7'],
					'show_subscription' => 'yes'
				]
			]
		);

		$this->add_control(
			'md_show_article_count',
			[
				'label' => esc_html__( 'Show Article Count', 'eazydocs' ),
				'type' => Controls_Manager::SWITCHER,
				'default' => 'yes',
				'return_value' => 'yes',
				'condition' => [
					'doc-widget-skin' => ['7'],
				],
			]
		);

		$this->add_control(
			'md_show_read_time',
			[
				'label' => esc_html__( 'Show Avg Read Time', 'eazydocs' ),
				'type' => Controls_Manager::SWITCHER,
				'default' => 'yes',
				'return_value' => 'yes',
				'condition' => [
					'doc-widget-skin' => ['7'],
				],
			]
		);

		$this->add_control(
			'md_hide_empty_docs',
			[
				'label' => esc_html__( 'Hide Empty Docs', 'eazydocs' ),
				'description' => esc_html__( 'Hide docs that have no child docs. Applies to all skins.', 'eazydocs' ),
				'type' => Controls_Manager::SWITCHER,
				'default' => '',
				'return_value' => 'yes',
			]
		);

		// Restricted Docs controls live in their own section (see below).

		$this->add_control(
			'topics_label',
			[
				'label' => esc_html__('Topics Label', 'eazydocs'),
				'type' => Controls_Manager::TEXT,
				'label_block' => true,
				'default' => esc_html__('Topics', 'eazydocs'),
				'condition' => [
					'doc-widget-skin' => ['1']
				]
			]
		);

		$this->add_control(
			'book_chapter_prefix',
			[
				'label' => esc_html__('Book Chapters / Tutorials Prefix', 'eazydocs'),
				'type' => \Elementor\Controls_Manager::TEXT,
				'condition' => [
					'doc-widget-skin' => ['4', '5']
				]
			]
		);

		$this->end_controls_section(); // End Controls Section

		/**
		 * Restricted Docs — grouped in their own section. Visibility toggles are
		 * per-widget; the card design (colours, style, badge/lock) is global and
		 * managed in EazyDocs → Settings → Restricted Docs → Card Design.
		 */
		$this->start_controls_section(
			'restricted_docs_section',
			[
				'label' => esc_html__( 'Restricted Docs', 'eazydocs' ),
			]
		);

		$this->add_control(
			'md_restricted_docs_notice',
			[
				'type'            => \Elementor\Controls_Manager::RAW_HTML,
				'raw'             => sprintf(
					/* translators: %1$s: opening link tag, %2$s: closing link tag. */
					esc_html__( 'Card colours, style and badge/lock visibility for restricted docs are set globally for the whole site. %1$sCustomize in Settings → Restricted Docs → Card Design%2$s', 'eazydocs' ),
					'<a href="' . esc_url( admin_url( 'admin.php?page=eazydocs-settings#tab=restricted-docs/card-design' ) ) . '" target="_blank" rel="noopener">',
					' →</a>'
				),
				'content_classes' => 'elementor-panel-alert elementor-panel-alert-info',
			]
		);

		$this->add_control(
			'md_show_private_docs',
			[
				'label'        => esc_html__( 'Show Private Docs', 'eazydocs' ),
				'description'  => esc_html__( 'List private docs (only shown to users allowed to read them).', 'eazydocs' ),
				'type'         => Controls_Manager::SWITCHER,
				'default'      => 'yes',
				'return_value' => 'yes',
			]
		);

		$this->add_control(
			'md_show_protected_docs',
			[
				'label'        => esc_html__( 'Show Password Protected Docs', 'eazydocs' ),
				'description'  => esc_html__( 'List docs that are protected with a password.', 'eazydocs' ),
				'type'         => Controls_Manager::SWITCHER,
				'default'      => 'yes',
				'return_value' => 'yes',
			]
		);

		$this->add_control(
			'md_show_status_badge',
			[
				'label'        => esc_html__( 'Show Status Badges', 'eazydocs' ),
				'description'  => esc_html__( 'Show the "Private" / "Protected" badge on restricted docs.', 'eazydocs' ),
				'type'         => Controls_Manager::SWITCHER,
				'default'      => 'yes',
				'return_value' => 'yes',
			]
		);

		$this->add_control(
			'md_show_lock_icon',
			[
				'label'        => esc_html__( 'Show Lock Icons', 'eazydocs' ),
				'description'  => esc_html__( 'Show the lock icon overlay on restricted docs.', 'eazydocs' ),
				'type'         => Controls_Manager::SWITCHER,
				'default'      => 'yes',
				'return_value' => 'yes',
			]
		);

		$this->end_controls_section(); // End Restricted Docs Section
	}
}
