<?php
/**
 * Use namespace to avoid conflict
 */
namespace EazyDocs\Elementor\Docs;

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

use Elementor\Repeater;
use Elementor\Widget_Base;
use Elementor\Controls_Manager;
use Elementor\Group_Control_Typography;
use Elementor\Group_Control_Text_Shadow;
use WP_Query;
use WP_Post;

class Doc_Widget extends Widget_Base {
	public function get_name() {
		return 'ezd_docs';
	}

	public function get_title() {
		return esc_html__( '(EazyDocs) MultiDocs', 'eazydocs' );
	}

	public function get_icon() {
		return 'eicon-document-file';
	}

	public function get_categories() {
		return [ 'eazydocs' ];
	}

    public function get_style_depends (){
        return [ 'ezd-docs-widgets', 'elegant-icon' ];
    }

	public function get_script_depends() {
		return [ 'ezd-script-handle', 'scrollspy' ];
	}
    
	public function get_keywords() {
		return [ 'eazydocs', 'docs', 'documentations', 'knowledge base', 'knowledgebase', 'kb', 'eazydocs' ];
	}

    /*
     * Tab ID Format
     */
    public function tab_id_format( $doc_id, $prefix ) {
        $settings = $this->get_settings_for_display();
        $slug_type = $settings['docs_slug_format'] ?? '';
        $widget_id = $this->get_id();

	    $post_title_slug = get_post_field( 'post_name', $doc_id );

        if ( $slug_type == 1 ) {
            $tab_id = $prefix.'-'.$post_title_slug;
        } else {
            $tab_id = $prefix.'-'.$widget_id.$doc_id;
        }

        echo esc_attr($tab_id);
    }

	/**
	 * Name: register_controls()
	 * Desc: Register controls for these widgets
	 * Params: no params
	 * Return: @void
	 * Author: spider-themes
	 */
	protected function register_controls() {
		$this->elementor_content_control();
		$this->elementor_style_control();
	}

	/**
	 * Name: elementor_content_control()
	 * Desc: Register the Content Tab output on the Elementor editor.
	 * Params: no params
	 * Return: @void
	 * Author: spider-themes
	 */
	public function elementor_content_control() {
		// ---Start Document Setting
		$this->start_controls_section(
			'doc_design_sec', [
				'label' => esc_html__( 'Preset Skin', 'eazydocs' ),
			]
		);

		$this->add_control(
			'doc-widget-skin', [
				'label'   => esc_html__( 'Skins', 'eazydocs' ),
				'type'    => Controls_Manager::CHOOSE,
				'options' => ezd_docs_layout_option(),
				'toggle'  => false,
				'default' => '1',
			]
		);

		$this->end_controls_section();

		// --- Layout Options
		$this->start_controls_section(
			'layout', [
				'label' => esc_html__( 'Layout Settings', 'eazydocs' ),
			]
		);

		$this->add_control(
			'ppp_column', [
				'label'       => esc_html__( 'Column', 'eazydocs' ),
				'description' => esc_html__( 'Number of column to show', 'eazydocs' ),
				'type'        => Controls_Manager::SELECT,
				'options'	  => [
					'1' 	  => esc_html__( '1 Column', 'eazydocs' ),
					'2' 	  => esc_html__( '2 Column', 'eazydocs' ),
					'3' 	  => esc_html__( '3 Column', 'eazydocs' ),
					'4' 	  => esc_html__( '4 Column', 'eazydocs' ),
					'5' 	  => esc_html__( '5 Column', 'eazydocs' ),
					'6' 	  => esc_html__( '6 Column', 'eazydocs' ),
				],
				'default'     => '3',
				'condition'   => [
					'doc-widget-skin' => [ '1' ]
				]
			]
		);

		$this->add_control(
			'doc-widget-tab-alignment', [
				'label'   => esc_html__( 'Tab Alignment', 'eazydocs' ),
				'type'    => Controls_Manager::CHOOSE,
				'options' => [
					'left' => [
						'title' => esc_html__( 'Left', 'eazydocs' ),
						'icon'  => 'eicon-h-align-left',
					],
					'center' => [
						'title' => esc_html__( 'center', 'eazydocs' ),
						'icon'  => 'eicon-h-align-center',
					],
					'right' => [
						'title' => esc_html__( 'Right', 'eazydocs' ),
						'icon'  => 'eicon-h-align-right',
					]
				],
				'toggle'  => false,
				'default' => 'center',
				'description' => esc_html__( 'Choose whether you want to position the tab buttons left, right or center from here.', 'eazydocs' ),
				'selectors' => [
					'{{WRAPPER}} .doc_tag_area .doc_tag' => 'justify-content: {{VALUE}};',
					'{{WRAPPER}} .h_doc_documentation_area .documentation_tab' => 'justify-content: {{VALUE}};',
					'{{WRAPPER}} .question_menu.docs3 .nav' => 'justify-content: {{VALUE}};',
					'{{WRAPPER}} .doc4-nav-bar .book-chapter-nav' => 'justify-content: {{VALUE}};',
				],
				'condition' => [
					'doc-widget-skin' => [ '2', '3', '4', '5' ]
				]
			]
		);

		$this->add_control(
			'masonry', [
				'label'             => esc_html__( 'Masonry', 'eazydocs' ),
				'type'              => \Elementor\Controls_Manager::SWITCHER,
				'doc-widget-skin'   => [ '4', '5' ]
			]
		);

		$this->add_control(
			'show_contributors', [
				'label'       => esc_html__( 'Show Contributors', 'eazydocs' ),
				'description' => esc_html__( 'The number of contributors to show.', 'eazydocs' ),
				'type'        => Controls_Manager::NUMBER,
				'default'     => 4,
				'min'         => 1,
				'max'         => 20,
				'condition'   => [
					'doc-widget-skin' => [ '6' ]
				],
			]
		);

		$this->end_controls_section();

		// --- Filter Options
		$this->start_controls_section(
			'document_filter', [
				'label' => esc_html__( 'Filter Options', 'eazydocs' ),
			]
		);

        $this->add_control(
			'docs_slug_format', [
				'label'     => esc_html__( 'Tab ID Format', 'eazydocs' ),
				'description'   => esc_html__( 'If the slug ID does not work then you should pick the number ID.', 'eazydocs' ),
				'type'      => Controls_Manager::SELECT,
				'options'   => [
					'1'     => esc_html__( 'Slug ID', 'eazydocs' ),
					'2'     => esc_html__( 'Number ID', 'eazydocs' ),
				],
				'condition' => [
					'doc-widget-skin' => [ '2', '3', '4', '5' ]
				],
				'default'   => '1'
			]
		);

		$this->add_control(
			'exclude', [
				'label'    => esc_html__( 'Exclude Docs', 'eazydocs' ),
				'type'     => Controls_Manager::SELECT2,
				'options'  => ezd_get_posts(),
				'multiple' => true
			]
		);

		$this->add_control(
			'show_section_count', [
				'label'       => esc_html__( 'Show Section Count', 'eazydocs' ),
				'description' => esc_html__( 'The number of sections to show under every documentation tab. Leave empty or give value -1 to show all sections.', 'eazydocs' ),
				'type'        => Controls_Manager::NUMBER,
				'default'     => 6,
				'condition'   => [
					'doc-widget-skin' => [ '2', '3', '4', '5' ]
				]
			]
		);

		$this->add_control(
			'active_doc', [
				'label'       => esc_html__( 'Active Doc', 'eazydocs' ),
				'description' => esc_html__( 'Select the active Doc tab by default.', 'eazydocs' ),
				'type'        => Controls_Manager::SELECT,
				'options'     => ezd_get_posts(),
				'condition'   => [
					'doc-widget-skin' => [ '2', '3', '5' ]
				]
			]
		);

		$this->add_control(
			'ppp_doc_items', [
				'label'       => esc_html__( 'Show Doc Item', 'eazydocs' ),
				'description' => esc_html__( 'The number of doc items to under every doc sections. Leave empty or give value -1 to show all sections.', 'eazydocs' ),
				'type'        => Controls_Manager::NUMBER,
				'default'     => -1,
				'condition'   => [
					'is_custom_order' => '',
					'doc-widget-skin' => [ '1', '2', '3', '4', '5' ]
				],
			]
		);

        $this->add_control(
            'doc_items_articles', [
                'label'       => esc_html__( 'Articles', 'eazydocs' ),
                'description' => esc_html__( 'Number of articles to show under every sections', 'eazydocs' ),
                'type'        => Controls_Manager::NUMBER,
                'default'     => 5,
                'condition'   => [
                    'doc-widget-skin' => [ '1' ]
                ]
            ]
        );

		$this->add_control(
			'main_doc_excerpt', [
				'label'       => esc_html__( 'Main Doc Excerpt', 'eazydocs' ),
				'description' => esc_html__( 'Excerpt word limit of main documentation. If the excerpt got empty, this will get from the post content.', 'eazydocs' ),
				'type'        => Controls_Manager::NUMBER,
				'default'     => 15,
				'condition'   => [
					'doc-widget-skin' => [ '2', '4' ]
				]
			]
		);

		$this->add_control(
			'doc_sec_excerpt', [
				'label'       => esc_html__( 'Doc Section Excerpt', 'eazydocs' ),
				'description' => esc_html__( 'Excerpt word limit of the documentation sections. If the excerpt got empty, this will get from the post content.', 'eazydocs' ),
				'type'        => Controls_Manager::NUMBER,
				'default'     => 8,
				'condition'   => [
					'doc-widget-skin' => '2'
				]
			]
		);
		
		$this->add_control(
			'is_custom_order', [
				'label'        => esc_html__( 'Custom Order', 'eazydocs' ),
				'description'  => esc_html__( 'Order the Doc tabs as you want.', 'eazydocs' ),
				'type'         => \Elementor\Controls_Manager::SWITCHER,
				'return_value' => 'yes',
				'default'      => '',
				'separator'    => 'before',
				'condition'    => [
					'doc-widget-skin' => [ '2', '3', '5' ]
				]
			]
		);
		
		$this->add_control(
			'order_by', [
				'label'     => esc_html__( 'Order By', 'eazydocs' ),
				'type'      => Controls_Manager::SELECT,
				'options'   => [
					'title'  	 => esc_html__( 'Title', 'eazydocs' ),
					'author'	 => esc_html__( 'Post Author', 'eazydocs' ),
					'date'		 => esc_html__( 'Date', 'eazydocs' ),
					'id'		 => esc_html__( 'Post ID', 'eazydocs' ),
					'modified'	 => esc_html__( 'Last Modified Date', 'eazydocs' ),
					'rand'		 => esc_html__( 'Random', 'eazydocs' ),
					'menu_order' => esc_html__( 'Menu Order', 'eazydocs' ),
				],
				'default'   => 'menu_order',
				'condition' => [
					'is_custom_order' => ''
				]
			]
		);
		
		$this->add_control(
			'order', [
				'label'     => esc_html__( 'Parent Docs Order', 'eazydocs' ),
				'type'      => Controls_Manager::SELECT,
				'options'   => [
					'ASC'  	=> esc_html__( 'Ascending', 'eazydocs' ),
					'DESC' 	=> esc_html__( 'Descending', 'eazydocs' ),
				],
				'default'   => 'ASC',
				'condition' => [
					'is_custom_order' => ''
				]
			]
		);
		
		$this->add_control(
			'child_order', [
				'label'     => esc_html__( 'Child Docs Order', 'eazydocs' ),
				'type'      => Controls_Manager::SELECT,
				'options'   => [
					'ASC'  	=> esc_html__( 'Ascending', 'eazydocs' ),
					'DESC' 	=> esc_html__( 'Descending', 'eazydocs' ),
				],
				'default'   => 'ASC',
				'condition' => [
					'is_custom_order' => ''
				]
			]
		);

		$doc = new Repeater();

		$doc->add_control(
			'doc', [
				'label'       => esc_html__( 'Doc', 'eazydocs' ),
				'type'        => Controls_Manager::SELECT,
				'options'     => ezd_get_posts(),
			]
		);

		$this->add_control(
			'docs', [
				'label'         => esc_html__( 'Tabs Items', 'eazydocs' ),
				'type'          => Controls_Manager::REPEATER,
				'fields'        => $doc->get_controls(),
				'title_field'   => '{{{ doc }}}',
				'prevent_empty' => false,
				'condition'     => [
					'is_custom_order' => 'yes'
				]
			]
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'labels', [
				'label' => esc_html__( 'Labels', 'eazydocs' ),
			]
		);

		$this->add_control(
			'is_tab_title_first_word', [
				'label'        => esc_html__( 'Tab Title First Word', 'eazydocs' ),
				'description'  => esc_html__( 'Show the first word of the doc in Tab Title.', 'eazydocs' ),
				'type'         => \Elementor\Controls_Manager::SWITCHER,
				'return_value' => 'yes',
				'condition'   => [
					'doc-widget-skin' => [ '2', '3', '4', '5' ]
				]
			]
		);

		$this->add_control(
			'read_more', [
				'label'       => esc_html__( 'Read More Text', 'eazydocs' ),
				'type'        => Controls_Manager::TEXT,
				'label_block' => true,
				'default'     =>  esc_html__( 'View All', 'eazydocs' ),
				'condition'   => [
					'doc-widget-skin' => [ '1', '2', '3' ]
				]
			]
		);

		$this->add_control(
			'topics_label', [
				'label'       => esc_html__( 'Topics Label', 'eazydocs' ),
				'type'        => Controls_Manager::TEXT,
				'label_block' => true,
				'default'     => esc_html__( 'Topics', 'eazydocs' ),
				'condition'   => [
					'doc-widget-skin' => [ '1' ]
				]
			]
		);

		$this->add_control(
			'book_chapter_prefix', [
				'label'     => esc_html__( 'Book Chapters / Tutorials Prefix', 'eazydocs' ),
				'type'      => \Elementor\Controls_Manager::TEXT,
				'condition' => [
					'doc-widget-skin' => [ '4', '5' ]
				]
			]
		);

		$this->end_controls_section(); // End Controls Section
	}


	/**
	 * Name: elementor_style_control()
	 * Desc: Register the Style Tab output on the Elementor editor.
	 * Params: no params
	 * Return: @void
	 * Author: spider-themes
	 */
	public function elementor_style_control() {


		//============================ Tab Style ============================//
		$this->start_controls_section(
			'style_tab_title', [
				'label' => esc_html__( 'Tab Title', 'eazydocs' ),
				'tab'   => Controls_Manager::TAB_STYLE,
				'condition' => [
					'doc-widget-skin' => [ '2', '3', '4', '5' ]
				]
			]
		);

		$this->add_group_control(
			\Elementor\Group_Control_Typography::get_type(), [
				'name' => 'tab_title_typo',
				'selector' => '{{WRAPPER}} .ezd_tab_title,
				               {{WRAPPER}} .book-chapter-nav .nav-item a',
			]
		);

		$this->add_responsive_control(
			'tab_title_padding',[
				'label' => esc_html__( 'Padding', 'eazydocs' ),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%', 'em' ],
				'selectors' => [
					'{{WRAPPER}} .ezd_tab_title' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
					'{{WRAPPER}} .book-chapter-nav .nav-item a' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		// Tab Title Normal/Active State
		$this->start_controls_tabs(
			'style_tab_title_tabs'
		);

		//=== Normal Tab Title
		$this->start_controls_tab(
			'style_tab_title_normal', [
				'label' => esc_html__( 'Normal', 'eazydocs' ),
			]
		);

		$this->add_control(
			'normal_tab_title_text_color', [
				'label' => esc_html__( 'Text Color', 'eazydocs' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .ezd_tab_title' => 'color: {{VALUE}}',
					'{{WRAPPER}} .book-chapter-nav .nav-item a' => 'color: {{VALUE}}',
				)
			]
		);

		$this->add_control(
			'normal_tab_title_border_color', [
				'label' => esc_html__( 'Border Color', 'eazydocs' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .ezd_tab_title' => 'border-color: {{VALUE}};',
					'{{WRAPPER}} .book-chapter-nav::after' => 'border-color: {{VALUE}};',
				),
				'condition' => [
					'doc-widget-skin' => [ '2', ]
				]
			]
		);

		$this->add_group_control(
			\Elementor\Group_Control_Background::get_type(), [
				'name' => 'normal_tab_title_bg_color',
				'types' => [ 'classic', 'gradient' ],
				'exclude' => [ 'image'],
				'selector' => '{{WRAPPER}} .ezd_tab_title',
				'condition' => [
					'doc-widget-skin' => [ '2', '4' ],
					'doc-widget-skin!' => [ '5' ]
				]
			]
		);

		$this->end_controls_tab(); //End Normal Tab Title

        //start hover Tab Title
        $this->start_controls_tab(
            'style_tab_title_hover', [
                'label' => esc_html__( 'Hover', 'eazydocs' ),
            ]
        );

        $this->add_control(
            'hover_tab_title_text_color', [
                'label' => esc_html__( 'Text Color', 'eazydocs' ),
                'type' => Controls_Manager::COLOR,
                'selectors' => array(
                    '{{WRAPPER}} .ezd_tab_title:hover' => 'color: {{VALUE}}',
                    '{{WRAPPER}} .book-chapter-nav .nav-item a:hover' => 'color: {{VALUE}}',
                )
            ]
        );

        $this->add_group_control(
            \Elementor\Group_Control_Background::get_type(), [
                'name' => 'hover_tab_title_bg_colors',
                'types' => [ 'classic', 'gradient' ],
                'exclude' => [ 'image'],
                'selector' => '{{WRAPPER}} .ezd_tab_title:hover',
                'condition' => [
                    'doc-widget-skin' => [ '2', '4' ],
                    'doc-widget-skin!' => [ '5' ]
                ]
            ]
        );

        $this->end_controls_tab(); //End hover Tab Title


		//=== Active Tab Title
		$this->start_controls_tab(
			'style_tab_title_active', [
				'label' => esc_html__( 'Active', 'eazydocs' ),
			]
		);

		$this->add_control(
			'active_tab_title_text_color', [
				'label' => esc_html__( 'Text Color', 'eazydocs' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .ezd_tab_title.active, {{WRAPPER}} .ezd_tab_title:hover' => 'color: {{VALUE}};',
					'{{WRAPPER}} .book-chapter-nav .nav-item.active a' => 'color: {{VALUE}};',
				)
			]
		);

		$this->add_control(
			'active_tab_title_border_color', [
				'label' => esc_html__( 'Border Color', 'eazydocs' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .ezd_tab_title.active, {{WRAPPER}} .ezd_tab_title:hover' => 'border-color: {{VALUE}};',
					'{{WRAPPER}} .book-chapter-nav .nav-item.active' => 'border-color: {{VALUE}};',
				),
				'condition' => [
					'doc-widget-skin' => [ '2','5' ]
				]
			]
		);

		$this->add_group_control(
			\Elementor\Group_Control_Background::get_type(), [
				'name' => 'active_tab_title_bg_color',
				'types' => [ 'classic', 'gradient' ],
				'exclude' => [ 'image'],
				'selector' => '{{WRAPPER}} .ezd_tab_title.active, {{WRAPPER}} .ezd_tab_title:hover',
				'condition' => [
					'doc-widget-skin' => [ '2', '4' ],
					'doc-widget-skin!' => [ '5' ]
				]
			]
		);

		$this->end_controls_tab(); // End Active Tab Title

		$this->end_controls_tabs(); // End Tab Title Style Tabs

		$this->end_controls_section(); // End Tab Title Style


		//============================ Style Contents ============================//
		$this->start_controls_section(
			'style_contents', [
				'label' => esc_html__( 'Contents', 'eazydocs' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			]
		);

		//=== Item Parent Title
		$this->add_control(
			'item_title_parent_heading', [
				'label' => esc_html__( 'Item Parent Title', 'eazydocs' ),
				'type' => Controls_Manager::HEADING,
				'separator' => 'before',
				'condition' => [
					'doc-widget-skin' => [ '3', '5' ]
				]
			]
		);

		$this->add_group_control(
			\Elementor\Group_Control_Typography::get_type(), [
				'name' => 'item_parent_title_typo',
				'selector' => '{{WRAPPER}} .ezd_item_parent_title,
			                   {{WRAPPER}} .docs4-heading h3',
				'condition' => [
					'doc-widget-skin' => [ '3', '5' ]
				]
			]
		);

		$this->add_control(
			'item_parent_title_color', [
				'label' => esc_html__( 'Text Color', 'eazydocs' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .ezd_item_parent_title' => 'color: {{VALUE}};',
					'{{WRAPPER}} .docs4-heading h3' => 'color: {{VALUE}};',
				),
				'condition' => [
					'doc-widget-skin' => [ '3', '5' ]
				]
			]
		); // End Item Parent Title

		//=== Item Parent description
		$this->add_control(
			'item_title_parent_desc', [
				'label' => esc_html__( 'Item Parent Description', 'eazydocs' ),
				'type' => Controls_Manager::HEADING,
				'separator' => 'before',
				'condition' => [
					'doc-widget-skin' => [ '5' ]
				]
			]
		);

		$this->add_group_control(
			\Elementor\Group_Control_Typography::get_type(), [
				'name' => 'parent_desc_typo',
				'selector' => '{{WRAPPER}} .docs4-heading p',
				'condition' => [
					'doc-widget-skin' => [ '5' ]
				]
			]
		);

		$this->add_control(
			'parent_desc_color', [
				'label' => esc_html__( 'Text Color', 'eazydocs' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .docs4-heading p' => 'color: {{VALUE}};',
				),
				'condition' => [
					'doc-widget-skin' => [ '5' ]
				]
			]
		); // End Item Parent Description

		//=== Item Title
		$this->add_control(
			'item_title_heading', [
				'label' => esc_html__( 'Item Title', 'eazydocs' ),
				'type' => Controls_Manager::HEADING,
				'separator' => 'before',
			]
		);

		$this->add_group_control(
			\Elementor\Group_Control_Typography::get_type(), [
				'name' => 'item_title_typo',
				'selector' => '{{WRAPPER}} .ezd_item_title,
				               {{WRAPPER}} .doc4-section-title h4',
			]
		);

		$this->add_control(
			'item_title_color', [
				'label' => esc_html__( 'Text Color', 'eazydocs' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .ezd_item_title' => 'color: {{VALUE}};',
					'{{WRAPPER}} .doc4-section-title h4' => 'color: {{VALUE}};',
				),
			]
		);

		$this->add_control(
			'item_title_hover_color', [
				'label' => esc_html__( 'Text Hover Color', 'eazydocs' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .ezd_item_title:hover' => 'color: {{VALUE}}; text-decoration-color: {{VALUE}};',
					'{{WRAPPER}} .doc4-section-title h4:hover' => 'color: {{VALUE}};',
				),
			]
		); // End Item Title


		//=== Item List Title
		$this->add_control(
			'item_list_title_heading', [
				'label' => esc_html__( 'Item Title List', 'eazydocs' ),
				'type' => Controls_Manager::HEADING,
				'separator' => 'before',
				'condition' => [
					'doc-widget-skin' => [ '1', '2', '4', '5' ],
					'doc-widget-skin!' => [ '6' ]
				]
			]
		);

		$this->add_group_control(
			\Elementor\Group_Control_Typography::get_type(), [
				'name' => 'item_list_title_typo',
				'selector' => '{{WRAPPER}} .ezd_item_list_title,
				               {{WRAPPER}} .topic_list_item ul li a,
				               {{WRAPPER}} .topic_list_item ul li a .chapter_counter',
				'condition' => [
					'doc-widget-skin' => [ '1', '2', '4', '5' ],
					'doc-widget-skin!' => [ '6' ]
				]
			]
		);

		$this->add_control(
			'item_list_title_color', [
				'label' => esc_html__( 'Text Color', 'eazydocs' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .ezd_item_list_title' => 'color: {{VALUE}};',
					'{{WRAPPER}} .topic_list_item ul li a' => 'color: {{VALUE}};',
					'{{WRAPPER}} .topic_list_item ul li a .chapter_counter' => 'color: {{VALUE}};',
				),
				'condition' => [
					'doc-widget-skin' => [ '1', '2', '4', '5' ],
					'doc-widget-skin!' => ['6']
				]
			]
		);

		$this->add_control(
			'item_list_title_hover_color', [
				'label' => esc_html__( 'Text Hover Color', 'eazydocs' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .article_list li a.ezd_item_list_title:hover' => 'color: {{VALUE}}',
					'{{WRAPPER}} .topic_list_item ul li a:hover' => 'color: {{VALUE}}',
					'{{WRAPPER}} .topic_list_item ul li a:hover .chapter_counter' => 'color: {{VALUE}}',
                ],
				'condition' => [
					'doc-widget-skin' => [ '1', '2', '4', '5' ],
					'doc-widget-skin!' => ['6']
				]
			]
		);// End Item Title


		//=== Item Contents
		$this->add_control(
			'item_content_heading', [
				'label' => esc_html__( 'Item Contents', 'eazydocs' ),
				'type' => Controls_Manager::HEADING,
				'separator' => 'before',
				'condition' => [
					'doc-widget-skin' => ['3','6']
				]
			]
		);

		$this->add_group_control(
			\Elementor\Group_Control_Typography::get_type(), [
				'name' => 'item_content_typo',
				'selector' => '{{WRAPPER}} .ezd_item_content',
				'condition' => [
					'doc-widget-skin' => ['3','6']
				]
			]
		);

		$this->add_control(
			'item_content_color', [
				'label' => esc_html__( 'Text Color', 'eazydocs' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .ezd_item_content' => 'color: {{VALUE}};',
				),
				'condition' => [
					'doc-widget-skin' => ['3','6']
				]
			]
		); // End Item Contents


		$this->end_controls_section(); // End Contents Style


		//============================ Style Button ============================//
		$this->start_controls_section(
			'style_buttons', [
				'label' => esc_html__( 'Button', 'eazydocs' ),
				'tab'   => Controls_Manager::TAB_STYLE,
				'condition' => [
					'doc-widget-skin' => [ '1', '2', '3', '4' ]
				]
			]
		);

		$this->add_group_control(
			\Elementor\Group_Control_Typography::get_type(), [
				'name' => 'item_btn_typo',
				'selector' => '{{WRAPPER}} .ezd_btn',
			]
		);

		// Tab Title Normal/Hover State
		$this->start_controls_tabs(
			'style_btn_tabs'
		);


		//=== Normal Button
		$this->start_controls_tab(
			'style_btn_normal', [
				'label' => esc_html__( 'Normal', 'eazydocs' ),
			]
		);

		$this->add_control(
			'btn_text_normal_color', [
				'label' => esc_html__( 'Text Color', 'eazydocs' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .ezd_btn' => 'color: {{VALUE}};',
				),
			]
		);

		$this->add_group_control(
			\Elementor\Group_Control_Background::get_type(), [
				'name' => 'btn_bg_normal_color',
				'types' => [ 'classic', 'gradient' ],
				'exclude' => [ 'image' ],
				'selector' => '{{WRAPPER}} .ezd_btn',
				'condition' => [
					'doc-widget-skin' => '1'
				]
			]
		);

		$this->end_controls_tab(); // End Normal Button

		//=== Hover Button
		$this->start_controls_tab(
			'style_btn_hover', [
				'label' => esc_html__( 'Hover', 'eazydocs' ),
			]
		);

		$this->add_control(
			'btn_text_hover_color', [
				'label' => esc_html__( 'Text Color', 'eazydocs' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .ezd_btn:hover' => 'color: {{VALUE}};',
				),
			]
		);

		$this->add_control(
			'btn_border_hover_color', [
				'label' => esc_html__( 'Border Color', 'eazydocs' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .ezd_btn:hover' => 'border-color: {{VALUE}};',
				),
				'condition' => [
					'doc-widget-skin' => '1'
				]
			]
		);

		$this->add_group_control(
			\Elementor\Group_Control_Background::get_type(), [
				'name' => 'btn_bg_hover_color',
				'types' => [ 'classic', 'gradient' ],
				'exclude' => [ 'image' ],
				'selector' => '{{WRAPPER}} .ezd_btn:hover',
				'condition' => [
					'doc-widget-skin' => '1'
				]
			]
		);

		$this->end_controls_tab(); // End Hover Button

		$this->end_controls_tabs(); // End Tab Title Normal/Hover State

		$this->add_responsive_control(
			'btn_padding', [
				'label' => esc_html__( 'Padding', 'eazydocs' ),
				'type' => \Elementor\Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%', 'em', 'rem', 'custom' ],
				'selectors' => [
					'{{WRAPPER}} .ezd_btn' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
				'separator' => 'before',
				'condition' => [
					'doc-widget-skin' => '1'
				]
			]
		);

		$this->add_group_control(
			\Elementor\Group_Control_Border::get_type(), [
				'name' => 'btn_border',
				'selector' => '{{WRAPPER}} .ezd_btn',
				'condition' => [
					'doc-widget-skin' => '1'
				]
			]
		);

		$this->end_controls_section(); // End Button Style


	}


	/**
	 * Name: elementor_render()
	 * Desc: Render the widget output on the frontend.
	 * Params: no params
	 * Return: @void
	 * Author: spider-themes
	 */
	protected function render() {
		$settings       = $this->get_settings();
		$doc_number     = $settings['ppp_doc_items'] ?? -1;
		$read_more      = $settings['read_more'] ?? '';
		$doc_order     	= $settings['order'] ?? 'asc'; // 'asc' or 'desc'
		$child_order   	= $settings['child_order'] ?? 'asc';
		$order_by      	= $settings['order_by'] ?? 'menu_order'; // e.g., 'post_title', 'menu_order', 'post_date'
		$doc_exclude 	= $settings['exclude'] ?? '';               
		$topics_label 	= ! empty( $settings['topics_label'] ) ? $settings['topics_label'] : esc_html__( 'Topics', 'eazydocs' );

		// Map Elementor 'orderby' options to get_pages 'sort_column'
		$valid_sort_columns = [
			'ID',
			'post_title',
			'menu_order',
			'post_date',
			'post_modified',
		];

		$sort_column = in_array($order_by, $valid_sort_columns) ? $order_by : 'menu_order';

		$args = array(
			'post_type'   => 'docs',
			'parent'      => 0,
			'sort_column' => $sort_column,
			'sort_order'  => $doc_order,
		);

		if ( ! empty( $doc_exclude ) ) {
			$args['exclude'] = $doc_exclude;
		}

		$parent_docs = get_pages( $args );


		/**
		 * Get the doc sections
		 */
		if ( $parent_docs ) {
			foreach ( $parent_docs as $root ) {
				$sections = get_children( array(
					'post_parent'    => $root->ID,
					'post_type'      => 'docs',
					'post_status'    => 'publish',
					'orderby'    	 => $order_by,
					'order' 		 => $child_order,
					'posts_per_page' => ! empty( $settings['show_section_count'] ) ? $settings['show_section_count'] : - 1,
				));

				$docs[]   = array(
					'doc'      => $root,
					'sections' => $sections,
				);

			}
		}

        if ( ezd_unlock_themes('docy','docly') ) {
		    include( "docs-{$settings['doc-widget-skin']}.php" );
        } else {
            include( "docs-1.php" );
        }

        ?>

    <script type="text/javascript">
    ;
    (function($) {
        "use strict";

        $(document).ready(function() {

            // === Tabs Slider
            var tabId = "#Arrow_slides-<?php echo esc_js($this->get_id()) ?>";
            var tabSliderContainers = $(tabId + " .tabs_sliders");

            tabSliderContainers.each(function() {
                let tabWrapWidth = $(this).outerWidth();
                let totalWidth = 0;

                let slideArrowBtn = $(tabId + " .scroller-btn");
                let slideBtnLeft = $(tabId + " .scroller-btn.left");
                let slideBtnRight = $(tabId + " .scroller-btn.right");
                let navWrap = $(tabId + " .slide_nav_tabs");
                let navWrapItem = $(tabId + " .slide_nav_tabs li");

                navWrapItem.each(function() {
                    totalWidth += $(this).outerWidth();
                });

                if (totalWidth > tabWrapWidth) {
                    slideArrowBtn.removeClass("inactive");
                } else {
                    slideArrowBtn.addClass("inactive");
                }

                if (navWrap.scrollLeft() === 0) {
                    slideBtnLeft.addClass("inactive");
                } else {
                    slideBtnLeft.removeClass("inactive");
                }

                slideBtnRight.on("click", function() {
                    navWrap.animate({
                        scrollLeft: "+=200px"
                    }, 300);
                    console.log(navWrap.scrollLeft() + " px");
                });

                slideBtnLeft.on("click", function() {
                    navWrap.animate({
                        scrollLeft: "-=200px"
                    }, 300);
                });

                scrollerHide(navWrap, slideBtnLeft, slideBtnRight);
            });

            function scrollerHide(navWrap, slideBtnLeft, slideBtnRight) {
                let scrollLeftPrev = 0;
                navWrap.scroll(function() {
                    let $elem = $(this);
                    let newScrollLeft = $elem.scrollLeft(),
                        width = $elem.outerWidth(),
                        scrollWidth = $elem.get(0).scrollWidth;
                    if (scrollWidth - newScrollLeft === width) {
                        slideBtnRight.addClass("inactive");
                    } else {
                        slideBtnRight.removeClass("inactive");
                    }
                    if (newScrollLeft === 0) {
                        slideBtnLeft.addClass("inactive");
                    } else {
                        slideBtnLeft.removeClass("inactive");
                    }
                    scrollLeftPrev = newScrollLeft;
                });
            }

            // custom tab js
            $('.ezd-tab-menu li a').on('click', function(e) {
                e.preventDefault();

                // Remove active class from all tabs within the same menu
                $(this).closest('.ezd-tab-menu').find('li a').removeClass('active');

                // Add active class to the clicked tab
                $(this).addClass('active');

                var target = $(this).attr('data-rel');

                $('#' + target)
                    .addClass('active')
                    .siblings('.ezd-tab-box')
                    .removeClass('active');

                return false;
            });

        });
    })(jQuery);
    </script>
    <?php

	}
}

