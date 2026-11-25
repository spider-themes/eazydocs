<?php
namespace EazyDocs\Elementor\Docs;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

use Elementor\Repeater;
use Elementor\Controls_Manager;
use Elementor\Group_Control_Typography;
use Elementor\Group_Control_Text_Shadow;

trait Doc_Widget_Style_Controls {
	/**
	 * Name: elementor_style_control()
	 * Desc: Register the Style Tab output on the Elementor editor.
	 * Params: no params
	 * Return: @void
	 * Author: spider-themes
	 */
	public function elementor_style_control()
	{


		//============================ Tab Style ============================//
		$this->start_controls_section(
			'style_tab_title',
			[
				'label' => esc_html__('Tab Title', 'eazydocs'),
				'tab' => Controls_Manager::TAB_STYLE,
				'condition' => [
					'doc-widget-skin' => ['2', '3', '4', '5']
				]
			]
		);

		$this->add_group_control(
			\Elementor\Group_Control_Typography::get_type(),
			[
				'name' => 'tab_title_typo',
				'selector' => '{{WRAPPER}} .ezd_tab_title,
				               {{WRAPPER}} .book-chapter-nav .nav-item a',
			]
		);

		$this->add_responsive_control(
			'tab_title_padding',
			[
				'label' => esc_html__('Padding', 'eazydocs'),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => ['px', '%', 'em'],
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
			'style_tab_title_normal',
			[
				'label' => esc_html__('Normal', 'eazydocs'),
			]
		);

		$this->add_control(
			'normal_tab_title_text_color',
			[
				'label' => esc_html__('Text Color', 'eazydocs'),
				'type' => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .ezd_tab_title' => 'color: {{VALUE}}',
					'{{WRAPPER}} .book-chapter-nav .nav-item a' => 'color: {{VALUE}}',
				)
			]
		);

		$this->add_control(
			'normal_tab_title_border_color',
			[
				'label' => esc_html__('Border Color', 'eazydocs'),
				'type' => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .ezd_tab_title' => 'border-color: {{VALUE}};',
					'{{WRAPPER}} .book-chapter-nav::after' => 'border-color: {{VALUE}};',
				),
				'condition' => [
					'doc-widget-skin' => ['2',]
				]
			]
		);

		$this->add_group_control(
			\Elementor\Group_Control_Background::get_type(),
			[
				'name' => 'normal_tab_title_bg_color',
				'types' => ['classic', 'gradient'],
				'exclude' => ['image'],
				'selector' => '{{WRAPPER}} .ezd_tab_title',
				'condition' => [
					'doc-widget-skin' => ['2', '4'],
					'doc-widget-skin!' => ['5']
				]
			]
		);

		$this->end_controls_tab(); //End Normal Tab Title

		//start hover Tab Title
		$this->start_controls_tab(
			'style_tab_title_hover',
			[
				'label' => esc_html__('Hover', 'eazydocs'),
			]
		);

		$this->add_control(
			'hover_tab_title_text_color',
			[
				'label' => esc_html__('Text Color', 'eazydocs'),
				'type' => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .ezd_tab_title:hover' => 'color: {{VALUE}}',
					'{{WRAPPER}} .book-chapter-nav .nav-item a:hover' => 'color: {{VALUE}}',
				)
			]
		);

		$this->add_group_control(
			\Elementor\Group_Control_Background::get_type(),
			[
				'name' => 'hover_tab_title_bg_colors',
				'types' => ['classic', 'gradient'],
				'exclude' => ['image'],
				'selector' => '{{WRAPPER}} .ezd_tab_title:hover',
				'condition' => [
					'doc-widget-skin' => ['2', '4'],
					'doc-widget-skin!' => ['5']
				]
			]
		);

		$this->end_controls_tab(); //End hover Tab Title


		//=== Active Tab Title
		$this->start_controls_tab(
			'style_tab_title_active',
			[
				'label' => esc_html__('Active', 'eazydocs'),
			]
		);

		$this->add_control(
			'active_tab_title_text_color',
			[
				'label' => esc_html__('Text Color', 'eazydocs'),
				'type' => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .ezd_tab_title.active, {{WRAPPER}} .ezd_tab_title:hover' => 'color: {{VALUE}};',
					'{{WRAPPER}} .book-chapter-nav .nav-item.active a' => 'color: {{VALUE}};',
				)
			]
		);

		$this->add_control(
			'active_tab_title_border_color',
			[
				'label' => esc_html__('Border Color', 'eazydocs'),
				'type' => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .ezd_tab_title.active, {{WRAPPER}} .ezd_tab_title:hover' => 'border-color: {{VALUE}};',
					'{{WRAPPER}} .book-chapter-nav .nav-item.active' => 'border-color: {{VALUE}};',
				),
				'condition' => [
					'doc-widget-skin' => ['2', '5']
				]
			]
		);

		$this->add_group_control(
			\Elementor\Group_Control_Background::get_type(),
			[
				'name' => 'active_tab_title_bg_color',
				'types' => ['classic', 'gradient'],
				'exclude' => ['image'],
				'selector' => '{{WRAPPER}} .ezd_tab_title.active, {{WRAPPER}} .ezd_tab_title:hover',
				'condition' => [
					'doc-widget-skin' => ['2', '4'],
					'doc-widget-skin!' => ['5']
				]
			]
		);

		$this->end_controls_tab(); // End Active Tab Title

		$this->end_controls_tabs(); // End Tab Title Style Tabs

		$this->end_controls_section(); // End Tab Title Style


		//============================ Style Contents ============================//
		$this->start_controls_section(
			'style_contents',
			[
				'label' => esc_html__('Contents', 'eazydocs'),
				'tab' => Controls_Manager::TAB_STYLE,
			]
		);

		//=== Item Parent Title
		$this->add_control(
			'item_title_parent_heading',
			[
				'label' => esc_html__('Item Parent Title', 'eazydocs'),
				'type' => Controls_Manager::HEADING,
				'separator' => 'before',
				'condition' => [
					'doc-widget-skin' => ['3', '5']
				]
			]
		);

		$this->add_group_control(
			\Elementor\Group_Control_Typography::get_type(),
			[
				'name' => 'item_parent_title_typo',
				'selector' => '{{WRAPPER}} .ezd_item_parent_title,
			                   {{WRAPPER}} .docs4-heading h3',
				'condition' => [
					'doc-widget-skin' => ['3', '5']
				]
			]
		);

		$this->add_control(
			'item_parent_title_color',
			[
				'label' => esc_html__('Text Color', 'eazydocs'),
				'type' => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .ezd_item_parent_title' => 'color: {{VALUE}};',
					'{{WRAPPER}} .docs4-heading h3' => 'color: {{VALUE}};',
				),
				'condition' => [
					'doc-widget-skin' => ['3', '5']
				]
			]
		); // End Item Parent Title

		//=== Item Parent description
		$this->add_control(
			'item_title_parent_desc',
			[
				'label' => esc_html__('Item Parent Description', 'eazydocs'),
				'type' => Controls_Manager::HEADING,
				'separator' => 'before',
				'condition' => [
					'doc-widget-skin' => ['5']
				]
			]
		);

		$this->add_group_control(
			\Elementor\Group_Control_Typography::get_type(),
			[
				'name' => 'parent_desc_typo',
				'selector' => '{{WRAPPER}} .docs4-heading p',
				'condition' => [
					'doc-widget-skin' => ['5']
				]
			]
		);

		$this->add_control(
			'parent_desc_color',
			[
				'label' => esc_html__('Text Color', 'eazydocs'),
				'type' => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .docs4-heading p' => 'color: {{VALUE}};',
				),
				'condition' => [
					'doc-widget-skin' => ['5']
				]
			]
		); // End Item Parent Description

		//=== Item Title
		$this->add_control(
			'item_title_heading',
			[
				'label' => esc_html__('Item Title', 'eazydocs'),
				'type' => Controls_Manager::HEADING,
				'separator' => 'before',
			]
		);

		$this->add_group_control(
			\Elementor\Group_Control_Typography::get_type(),
			[
				'name' => 'item_title_typo',
				'selector' => '{{WRAPPER}} .ezd_item_title,
				               {{WRAPPER}} .doc4-section-title h4',
			]
		);

		$this->add_control(
			'item_title_color',
			[
				'label' => esc_html__('Text Color', 'eazydocs'),
				'type' => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .ezd_item_title' => 'color: {{VALUE}};',
					'{{WRAPPER}} .doc4-section-title h4' => 'color: {{VALUE}};',
				),
			]
		);

		$this->add_control(
			'item_title_hover_color',
			[
				'label' => esc_html__('Text Hover Color', 'eazydocs'),
				'type' => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .ezd_item_title:hover' => 'color: {{VALUE}}; text-decoration-color: {{VALUE}};',
					'{{WRAPPER}} .doc4-section-title h4:hover' => 'color: {{VALUE}};',
				),
			]
		); // End Item Title


		//=== Item List Title
		$this->add_control(
			'item_list_title_heading',
			[
				'label' => esc_html__('Item Title List', 'eazydocs'),
				'type' => Controls_Manager::HEADING,
				'separator' => 'before',
				'condition' => [
					'doc-widget-skin' => ['1', '2', '4', '5', '7'],
				]
			]
		);

		$this->add_group_control(
			\Elementor\Group_Control_Typography::get_type(),
			[
				'name' => 'item_list_title_typo',
				'selector' => '{{WRAPPER}} .ezd_item_list_title,
				               {{WRAPPER}} .topic_list_item ul li a,
				               {{WRAPPER}} .topic_list_item ul li a .chapter_counter,
				               {{WRAPPER}} .ezd-docs-card__articles li a',
				'condition' => [
					'doc-widget-skin' => ['1', '2', '4', '5', '7'],
				]
			]
		);

		$this->add_control(
			'item_list_title_color',
			[
				'label' => esc_html__('Text Color', 'eazydocs'),
				'type' => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .ezd_item_list_title' => 'color: {{VALUE}};',
					'{{WRAPPER}} .topic_list_item ul li a' => 'color: {{VALUE}};',
					'{{WRAPPER}} .topic_list_item ul li a .chapter_counter' => 'color: {{VALUE}};',
					'{{WRAPPER}} .ezd-docs-card__articles li a' => 'color: {{VALUE}};',
				),
				'condition' => [
					'doc-widget-skin' => ['1', '2', '4', '5', '7'],
				]
			]
		);

		$this->add_control(
			'item_list_title_hover_color',
			[
				'label' => esc_html__('Text Hover Color', 'eazydocs'),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .article_list li a.ezd_item_list_title:hover' => 'color: {{VALUE}}',
					'{{WRAPPER}} .topic_list_item ul li a:hover' => 'color: {{VALUE}}',
					'{{WRAPPER}} .topic_list_item ul li a:hover .chapter_counter' => 'color: {{VALUE}}',
					'{{WRAPPER}} .ezd-docs-card__articles li a:hover' => 'color: {{VALUE}}',
				],
				'condition' => [
					'doc-widget-skin' => ['1', '2', '4', '5', '7'],
				]
			]
		);// End Item Title


		//=== Item Contents
		$this->add_control(
			'item_content_heading',
			[
				'label' => esc_html__('Item Contents', 'eazydocs'),
				'type' => Controls_Manager::HEADING,
				'separator' => 'before',
				'condition' => [
					'doc-widget-skin' => ['3', '6']
				]
			]
		);

		$this->add_group_control(
			\Elementor\Group_Control_Typography::get_type(),
			[
				'name' => 'item_content_typo',
				'selector' => '{{WRAPPER}} .ezd_item_content',
				'condition' => [
					'doc-widget-skin' => ['3', '6']
				]
			]
		);

		$this->add_control(
			'item_content_color',
			[
				'label' => esc_html__('Text Color', 'eazydocs'),
				'type' => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .ezd_item_content' => 'color: {{VALUE}};',
				),
				'condition' => [
					'doc-widget-skin' => ['3', '6']
				]
			]
		); // End Item Contents


		$this->end_controls_section(); // End Contents Style


		//============================ Style Button ============================//
		$this->start_controls_section(
			'style_buttons',
			[
				'label' => esc_html__('Button', 'eazydocs'),
				'tab' => Controls_Manager::TAB_STYLE,
				'condition' => [
					'doc-widget-skin' => ['1', '2', '3', '4', '7']
				]
			]
		);

		$this->add_group_control(
			\Elementor\Group_Control_Typography::get_type(),
			[
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
			'style_btn_normal',
			[
				'label' => esc_html__('Normal', 'eazydocs'),
			]
		);

		$this->add_control(
			'btn_text_normal_color',
			[
				'label' => esc_html__('Text Color', 'eazydocs'),
				'type' => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .ezd_btn' => 'color: {{VALUE}};',
				),
			]
		);

		$this->add_group_control(
			\Elementor\Group_Control_Background::get_type(),
			[
				'name' => 'btn_bg_normal_color',
				'types' => ['classic', 'gradient'],
				'exclude' => ['image'],
				'selector' => '{{WRAPPER}} .ezd_btn',
				'condition' => [
					'doc-widget-skin' => ['1', '7']
				]
			]
		);

		$this->end_controls_tab(); // End Normal Button

		//=== Hover Button
		$this->start_controls_tab(
			'style_btn_hover',
			[
				'label' => esc_html__('Hover', 'eazydocs'),
			]
		);

		$this->add_control(
			'btn_text_hover_color',
			[
				'label' => esc_html__('Text Color', 'eazydocs'),
				'type' => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .ezd_btn:hover' => 'color: {{VALUE}};',
				),
			]
		);

		$this->add_control(
			'btn_border_hover_color',
			[
				'label' => esc_html__('Border Color', 'eazydocs'),
				'type' => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .ezd_btn:hover' => 'border-color: {{VALUE}};',
				),
				'condition' => [
					'doc-widget-skin' => ['1', '7']
				]
			]
		);

		$this->add_group_control(
			\Elementor\Group_Control_Background::get_type(),
			[
				'name' => 'btn_bg_hover_color',
				'types' => ['classic', 'gradient'],
				'exclude' => ['image'],
				'selector' => '{{WRAPPER}} .ezd_btn:hover',
				'condition' => [
					'doc-widget-skin' => ['1', '7']
				]
			]
		);

		$this->end_controls_tab(); // End Hover Button

		$this->end_controls_tabs(); // End Tab Title Normal/Hover State

		$this->add_responsive_control(
			'btn_padding',
			[
				'label' => esc_html__('Padding', 'eazydocs'),
				'type' => \Elementor\Controls_Manager::DIMENSIONS,
				'size_units' => ['px', '%', 'em', 'rem', 'custom'],
				'selectors' => [
					'{{WRAPPER}} .ezd_btn' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
				'separator' => 'before',
				'condition' => [
					'doc-widget-skin' => ['1', '7']
				]
			]
		);

		$this->add_group_control(
			\Elementor\Group_Control_Border::get_type(),
			[
				'name' => 'btn_border',
				'selector' => '{{WRAPPER}} .ezd_btn',
				'condition' => [
					'doc-widget-skin' => ['1', '7']
				]
			]
		);

		$this->end_controls_section(); // End Button Style


		//============================ Style Card Grid: Box Style ============================//
		$this->start_controls_section(
			'style_card7_card',
			[
				'label' => esc_html__( 'Card Grid: Box Style', 'eazydocs' ),
				'tab'   => Controls_Manager::TAB_STYLE,
				'condition' => [ 'doc-widget-skin' => ['7'] ],
			]
		);

		$this->add_group_control(
			\Elementor\Group_Control_Background::get_type(),
			[
				'name'    => 'card7_bg',
				'types'   => ['classic', 'gradient'],
				'exclude' => ['image'],
				'selector' => '{{WRAPPER}} .ezd-docs-card',
			]
		);

		$this->add_responsive_control(
			'card7_padding',
			[
				'label'      => esc_html__( 'Padding', 'eazydocs' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => ['px', '%', 'em', 'rem'],
				'selectors'  => [
					'{{WRAPPER}} .ezd-docs-card' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->add_group_control(
			\Elementor\Group_Control_Border::get_type(),
			[
				'name'     => 'card7_border',
				'selector' => '{{WRAPPER}} .ezd-docs-card',
			]
		);

		$this->add_responsive_control(
			'card7_border_radius',
			[
				'label'      => esc_html__( 'Border Radius', 'eazydocs' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => ['px', '%', 'em'],
				'selectors'  => [
					'{{WRAPPER}} .ezd-docs-card' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->add_group_control(
			\Elementor\Group_Control_Box_Shadow::get_type(),
			[
				'name'     => 'card7_shadow',
				'selector' => '{{WRAPPER}} .ezd-docs-card',
			]
		);

		$this->add_responsive_control(
			'card7_column_gap',
			[
				'label'      => esc_html__( 'Column Gap', 'eazydocs' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => ['px', 'em', 'rem'],
				'range'      => [ 'px' => [ 'min' => 0, 'max' => 80 ] ],
				'separator'  => 'before',
				'selectors'  => [
					'{{WRAPPER}} .ezd-card-grid-7' => 'column-gap: {{SIZE}}{{UNIT}};',
				],
			]
		);

		$this->add_responsive_control(
			'card7_row_gap',
			[
				'label'      => esc_html__( 'Row Gap', 'eazydocs' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => ['px', 'em', 'rem'],
				'range'      => [ 'px' => [ 'min' => 0, 'max' => 80 ] ],
				'selectors'  => [
					'{{WRAPPER}} .ezd-card-grid-7' => 'row-gap: {{SIZE}}{{UNIT}};',
				],
			]
		);

		$this->add_control(
			'card7_sep_color',
			[
				'label'     => esc_html__( 'Separator Color', 'eazydocs' ),
				'type'      => Controls_Manager::COLOR,
				'separator' => 'before',
				'selectors' => [
					'{{WRAPPER}} .ezd-docs-card__sep' => 'border-top-color: {{VALUE}};',
					'{{WRAPPER}} .ezd-docs-card__articles li' => 'border-bottom-color: {{VALUE}};',
				],
			]
		);

		$this->end_controls_section(); // End Card Grid: Box Style


		//============================ Style Card Grid: Meta Text ============================//
		$this->start_controls_section(
			'style_card7_meta',
			[
				'label' => esc_html__( 'Card Grid: Meta Text', 'eazydocs' ),
				'tab'   => Controls_Manager::TAB_STYLE,
				'condition' => [ 'doc-widget-skin' => ['7'] ],
			]
		);

		// ── Article count badge ──────────────────────────
		$this->add_control(
			'card7_count_heading',
			[
				'label' => esc_html__( 'Article Count Badge', 'eazydocs' ),
				'type'  => Controls_Manager::HEADING,
			]
		);

		$this->add_group_control(
			\Elementor\Group_Control_Typography::get_type(),
			[
				'name'     => 'card7_count_typo',
				'selector' => '{{WRAPPER}} .ezd-docs-card__info .ezd-badge',
			]
		);

		$this->add_control(
			'card7_count_color',
			[
				'label'     => esc_html__( 'Badge Text Color', 'eazydocs' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .ezd-docs-card__info .ezd-badge' => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'card7_count_bg',
			[
				'label'     => esc_html__( 'Badge Background', 'eazydocs' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .ezd-docs-card__info .ezd-badge' => 'background-color: {{VALUE}};',
				],
			]
		);

		// ── Read time ────────────────────────────────────
		$this->add_control(
			'card7_readtime_heading',
			[
				'label'     => esc_html__( 'Read Time Text', 'eazydocs' ),
				'type'      => Controls_Manager::HEADING,
				'separator' => 'before',
			]
		);

		$this->add_group_control(
			\Elementor\Group_Control_Typography::get_type(),
			[
				'name'     => 'card7_meta_typo',
				'selector' => '{{WRAPPER}} .ezd-docs-card__readtime',
			]
		);

		$this->add_control(
			'card7_meta_color',
			[
				'label'     => esc_html__( 'Text Color', 'eazydocs' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .ezd-docs-card__readtime' => 'color: {{VALUE}};',
				],
			]
		);

		$this->end_controls_section(); // End Card Grid: Meta Text
	}
}
