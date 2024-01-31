<?php
/**
 * Use namespace to avoid conflict
 */
namespace EazyDocs\Elementor\Book_Chapters;


// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

use Elementor\Repeater;
use Elementor\Widget_Base;
use Elementor\Controls_Manager;
use Elementor\Group_Control_Box_Shadow;
use Elementor\Group_Control_Typography;
use WP_Query;
use WP_Post;

class Book_Chapters extends Widget_Base {

	public function get_name() {
		return 'ezd_book_chapters';
	}

	public function get_title() {
		return esc_html__( 'Book Chapters/Tutorials', 'eazydocs' );
	}

	public function get_icon() {
		return 'eicon-document-file';
	}

	public function get_categories() {
		return [ 'eazydocs' ];
	}

    public function get_style_depends (){
        return [ 'ezd-el-widgets', 'ezd-docs-widget', 'elegant-icon' ];
    }

	public function get_script_depends() {
		return [ 'ezd-script-handle', 'scrollspy' ];
	}
    
	public function get_keywords() {
		return [ 'eazydocs', 'docs', 'documentations', 'knowledge base', 'kb', 'book', 'book-chapters', 'tutorials' ];
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

		// --- Filter Options
		$this->start_controls_section(
			'document_filter', [
				'label' => __( 'Filter Options', 'eazydocs' ),
			]
		);


		$this->add_control(
			'docs_slug_format', [
				'label'     => esc_html__( 'ID Format', 'eazydocs' ),
				'type'      => Controls_Manager::SELECT,
				'options'   => [
					'1'     => 'Slug ID',
					'2'     => 'Number ID',
				],
				'default'   => '1',
				'description'   => esc_html__( 'If the slug ID does not work then you should pick the number ID.', 'eazydocs' ),
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
			]
		);

		$this->add_control(
			'ppp_doc_items', [
				'label'       => esc_html__( 'Show Doc Item', 'eazydocs' ),
				'description' => esc_html__( 'The number of doc items to under every doc sections. Leave empty or give value -1 to show all sections.', 'eazydocs' ),
				'type'        => Controls_Manager::NUMBER,
				'default'     => -1,
			]
		);

		$this->add_control(
			'main_doc_excerpt', [
				'label'       => esc_html__( 'Main Doc Excerpt', 'eazydocs' ),
				'description' => esc_html__( 'Excerpt word limit of main documentation. If the excerpt got empty, this will get from the post content.', 'eazydocs' ),
				'type'        => Controls_Manager::NUMBER,
				'default'     => 15,
			]
		);

		$this->add_control(
			'masonry', [
				'label'       => esc_html__( 'Masonry', 'eazydocs' ),
				'type'        => \Elementor\Controls_Manager::SWITCHER,
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
			'order', [
				'label'     => esc_html__( 'Order', 'eazydocs' ),
				'type'      => Controls_Manager::SELECT,
				'options'   => [
					'ASC'  => 'ASC',
					'DESC' => 'DESC'
				],
				'default'   => 'ASC',
			]
		);

		// $doc = new Repeater();

        // $doc->add_control(
        //     'doc', [
        //         'label'       => __( 'Doc', 'eazydocs' ),
        //         'type'        => Controls_Manager::SELECT,
        //         'options'     => ezd_get_posts(),
        //     ]
        // );


		$this->end_controls_section();

		$this->start_controls_section(
			'labels', [
				'label' => esc_html__( 'Labels', 'eazydocs' ),
			]
		);

		$this->add_control(
			'book_chapter_prefix',
			[
				'label'         => __( 'Prefix', 'eazydocs' ),
                'description'   => __( 'You can insert your designated prefix above the document title.', 'eazydocs' ),
				'type'          => \Elementor\Controls_Manager::TEXT,
			]
		);

		// Add a switcher control for auto-numbering
		$this->add_control(
			'prefix_auto_numbering',
			[
				'label'         => __( 'Enable Auto Numbering', 'eazydocs' ),
				'description'	=> __('Enable/Disable Auto Numbering after the prefix text', 'eazydocs'),
				'type'          => \Elementor\Controls_Manager::SWITCHER,
				'default'       => 'yes',
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
				'label' => __( 'Tab Title', 'eazydocs' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			]
		);

		$this->add_group_control(
			\Elementor\Group_Control_Typography::get_type(), [
				'name'      => 'tab_title_typo',
				'selector'  => '{{WRAPPER}} .ezd_tab_title',
			]
		);

		$this->add_control(
			'tab_title_hr', [
				'type' => \Elementor\Controls_Manager::DIVIDER,
			]
		);

		// Tab Title Normal/Active State
		$this->start_controls_tabs(
			'style_tab_title_tabs'
		);

		//=== Normal Tab Title
		$this->start_controls_tab(
			'style_tab_title_normal', [
				'label'	=> __( 'Normal', 'eazydocs' ),
			]
		);

		$this->add_control(
			'normal_tab_title_text_color', [
				'label' 	=> __( 'Color', 'eazydocs' ),
				'type' 		=> Controls_Manager::COLOR,
				'selectors'	=> array(
					'{{WRAPPER}} .book-chapter-nav .nav-item a' => 'color: {{VALUE}}',
					'{{WRAPPER}} .book-chapter-nav .nav-item a span.chapter-part' => 'color: {{VALUE}}',
				)
			]
		);

		$this->end_controls_tab(); //End Normal Tab Title

		//=== Active Tab Title
		$this->start_controls_tab(
			'style_tab_title_active', [
				'label' => __( 'Active', 'eazydocs' ),
			]
		);

		$this->add_control(
			'active_tab_title_text_color', [
				'label' 	=> __( 'Color', 'eazydocs' ),
				'type' 		=> Controls_Manager::COLOR,
				'selectors'	=> array(
					'{{WRAPPER}} .book-chapter-nav .nav-item.active a' => 'color: {{VALUE}};',
					'{{WRAPPER}} .book-chapter-nav .nav-item.active span.chapter-part' => 'color: {{VALUE}};',
				)
			]
		);

		$this->add_control(
			'active_tab_title_border_color', [
				'label' 	=> __( 'Border Color', 'eazydocs' ),
				'type' 		=> Controls_Manager::COLOR,
				'selectors'	=> array(
					'{{WRAPPER}} .book-chapter-nav .nav-item.active' => 'border-color: {{VALUE}};',
				)
			]
		);

		$this->end_controls_tab(); // End Active Tab Title

		$this->end_controls_tabs(); // End Tab Title Style Tabs

		$this->end_controls_section(); // End Tab Title Style


		//============================ Style Contents ============================//
		$this->start_controls_section(
			'style_contents', [
				'label'	=> __( 'Contents', 'eazydocs' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			]
		);

		//=== Docs Title 
		$this->add_control(
			'docs_title_heading', [
				'label'	=> __( 'Docs Title', 'eazydocs' ),
				'type' 	=> Controls_Manager::HEADING,
			]
		);
		$this->add_control(
			'docs_title_color',
			[
				'label'		=> esc_html__( 'Color', 'eazydocs' ),
				'type' 		=> Controls_Manager::COLOR,
				'selectors'	=> [
					'{{WRAPPER}} .docs4-heading h3' => 'color: {{VALUE}}',
				],
			]
		);

		$this->add_group_control(
			\Elementor\Group_Control_Typography::get_type(),
			[
				'name' 		=> 'docs_typography',
				'selector'	=> '{{WRAPPER}} .docs4-heading h3',
			]
		); 
		//=== Docs Title End

		//=== Docs Excerpt 
		$this->add_control(
			'docs_excerpt', [
				'label' 	=> __( 'Docs Excerpt', 'eazydocs' ),
				'type' 		=> Controls_Manager::HEADING,
				'separator'	=> 'before',
			]
		);
		$this->add_control(
			'docs_excerpt_color',
			[
				'label' 	=> esc_html__( 'Color', 'eazydocs' ),
				'type' 		=> Controls_Manager::COLOR,
				'selectors'	=> [
					'{{WRAPPER}} .docs4-heading p' => 'color: {{VALUE}}',
				],
			]
		);

		$this->add_group_control(
			\Elementor\Group_Control_Typography::get_type(),
			[
				'name' 		=> 'docs_expert_typography',
				'selector' 	=> '{{WRAPPER}} .docs4-heading p',
				'separator'	=> 'before',
			]
		); 
		//=== Docs Excerpt End

		//=== Item Title
		$this->add_control(
			'item_title_heading', [
				'label' 	=> __( 'Item Title', 'eazydocs' ),
				'type' 		=> Controls_Manager::HEADING,
				'separator'	=> 'before',
			]
		);

		$this->add_control(
			'item_title_color', [
				'label' 	=> __( 'Color', 'eazydocs' ),
				'type' 		=> Controls_Manager::COLOR,
				'selectors'	=> array(
					'{{WRAPPER}} .ezd_item_title' => 'color: {{VALUE}};',
				),
			]
		);

		$this->add_group_control(
			\Elementor\Group_Control_Typography::get_type(), [
				'name' 		=> 'item_title_typo',
				'selector'	=> '{{WRAPPER}} .ezd_item_title',
			]
		);  
		// End Item Title

		//=== Item List Title
		$this->add_control(
			'item_list_title_heading', [
				'label' 	=> __( 'Item Title List', 'eazydocs' ),
				'type' 		=> Controls_Manager::HEADING,
				'separator'	=> 'before',
			]
		);

		$this->add_control(
			'item_list_title_color', [
				'label' 	=> __( 'Color', 'eazydocs' ),
				'type' 		=> Controls_Manager::COLOR,
				'selectors'	=> array(
					'{{WRAPPER}} .ezd_item_list_title' 		=> 'color: {{VALUE}};',
					'{{WRAPPER}} .ezd_item_list_title span'	=> 'color: {{VALUE}};',
				),
			]
		);

		$this->add_control(
			'item_list_title_hover_color', [
				'label' 	=> __( 'Hover Color', 'eazydocs' ),
				'type' 		=> Controls_Manager::COLOR,
				'selectors'	=> array(
					'{{WRAPPER}} .ezd_item_list_title:hover' 		=> 'color: {{VALUE}};',
					'{{WRAPPER}} .ezd_item_list_title span:hover'	=> 'color: {{VALUE}};',
				),
			]
		);

		$this->add_group_control(
			\Elementor\Group_Control_Typography::get_type(), [
				'name' 		=> 'item_list_title_typo',
				'selector'	=> '{{WRAPPER}} .ezd_item_list_title',
			]
		);
		// End Item Title

		$this->end_controls_section(); // End Contents Style
		
		//============================ Style Item Box ============================//
		$this->start_controls_section(
			'item_box_style', [
				'label' => __( 'Item Box', 'eazydocs' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			]
		);

		// Background Hover and active tab
		$this->start_controls_tabs(
			'box_bg_tab'
		);
		
		// start normal tab
		$this->start_controls_tab(
			'box_bg_normal',
			[
				'label' => esc_html__( 'Normal', 'eazydocs' ),
			]
		);

		$this->add_group_control(
			\Elementor\Group_Control_Background::get_type(),
			[
				'name' 		=> 'item_box_bg',
				'label' 	=> esc_html__( 'Background', 'eazydocs' ),
				'types' 	=> [ 'classic', 'gradient' ],
				'exclude' 	=> [ 'image' ],
				'selector'	=> '{{WRAPPER}} .topic_list_item',
			]
		);

		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(), [
			  'name'     => 'item_box_noraml_shadow',
			  'selector' => '{{WRAPPER}} .topic_list_item',
			]
		);
		
		$this->end_controls_tab(); //End Normal tab

		// start hover tab
		$this->start_controls_tab(
			'box_bg_hover',
			[
				'label' => esc_html__( 'Hover', 'eazydocs' ),
			]
		);

		$this->add_group_control(
			\Elementor\Group_Control_Background::get_type(),
			[
				'name' 		=> 'item_box_bg_hover',
				'label' 	=> esc_html__( 'Background', 'eazydocs' ),
				'types' 	=> [ 'classic', 'gradient' ],
				'exclude' 	=> [ 'image' ],
				'selector'	=> '{{WRAPPER}} .topic_list_item:hover',
			]
		);

		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(), [
			  'name'     => 'item_box_hover_shadow',
			  'selector' => '{{WRAPPER}} .topic_list_item:hover',
			]
		);
		
		$this->end_controls_tab(); //End hover tab
		
		$this->end_controls_tabs();

		$this->add_group_control(
			\Elementor\Group_Control_Border::get_type(),
			[
				'name' 		=> 'item_box-border',
				'label'		=> esc_html__( 'Border', 'eazydocs' ),
				'selector'	=> '{{WRAPPER}} .topic_list_item',
			]
		);

		$this->add_responsive_control(
			'item_box-border_radius',
			[
				'label' 		=> esc_html__( 'Border Radius', 'eazydocs' ),
				'type' 			=> \Elementor\Controls_Manager::DIMENSIONS,
				'size_units'	=> [ 'px', 'em' ],
				'selectors' 	=> [
					'{{WRAPPER}} .topic_list_item' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->add_responsive_control(
			'item_box-margin',
			[
				'label' 		=> esc_html__( 'Margin', 'eazydocs' ),
				'type' 			=> \Elementor\Controls_Manager::DIMENSIONS,
				'size_units'	=> [ 'px', 'em' ],
				'selectors' 	=> [
					'{{WRAPPER}} .topic_list_item' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->add_responsive_control(
			'item_box-padding',
			[
				'label' 		=> esc_html__( 'Padding', 'eazydocs' ),
				'type' 			=> \Elementor\Controls_Manager::DIMENSIONS,
				'size_units'	=> [ 'px', 'em' ],
				'selectors' 	=> [
					'{{WRAPPER}} .topic_list_item' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->end_controls_section(); // End Item Box Style


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
		include "book-chapters.php" ;
	}
}