<?php
/** ============ Content Styling ============ **/
use Elementor\Controls_Manager;
use Elementor\Group_Control_Typography;
use Elementor\Core\Schemes\Typography;

$this->start_controls_section(
	'style_form', [
		'label' => esc_html__( 'Form', 'eazydocs' ),
		'tab'   => Controls_Manager::TAB_STYLE,
	]
);

$this->add_responsive_control(
    'input-padding', [
        'label'      => __( 'Padding', 'eazydocs' ),
        'type'       => Controls_Manager::DIMENSIONS,
        'size_units' => [ 'px', '%', 'em' ],
        'separator'  => 'before',
        'selectors'  => [
            '{{WRAPPER}} .search_field_wrap' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}} !important;',
        ],
    ]
);

$this->add_group_control(
    \Elementor\Group_Control_Border::get_type(),
    [
        'name' => 'input-border',
        'label' => esc_html__( 'Border', 'eazydocs' ),
        'selector' => '{{WRAPPER}} .search_field_wrap',
    ]
);

$this->add_control(
	'input_background', [
		'label'     => esc_html__( 'Background Color', 'eazydocs' ),
		'type'      => Controls_Manager::COLOR,
		'selectors' => [
			'{{WRAPPER}} .search_field_wrap' => 'background: {{VALUE}};',
		],
	]
);

$this->add_control(
	'input_foucs_background', [
		'label'     => esc_html__( 'Focus Background Color', 'eazydocs' ),
		'type'      => Controls_Manager::COLOR,
		'selectors' => [
			'{{WRAPPER}} .search_form_wrap input:focus' => 'background: {{VALUE}};',
		],
	]
);

$this->add_responsive_control(
    'border-radius', [
        'label'      => __( 'Border Radius', 'eazydocs' ),
        'type'       => Controls_Manager::DIMENSIONS,
        'size_units' => [ 'px', '%', 'em' ],
        'selectors'  => [
            '{{WRAPPER}} .search_field_wrap' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
        ],
    ]
);

$this->add_control(
	'color_placeholder', [
		'label'     => esc_html__( 'Placeholder Color', 'eazydocs' ),
		'type'      => Controls_Manager::COLOR,
        'separator'  => 'before',
		'selectors' => [
			'{{WRAPPER}} .search_form_wrap .search_field_wrap::placeholder' => 'color: {{VALUE}};',
		],
	]
);

$this->add_group_control(
	Group_Control_Typography::get_type(), [
		'name'     => 'typography_placeholder',
		'label'    => esc_html__( 'Typography', 'eazydocs' ),
		'scheme'   => Typography::TYPOGRAPHY_1,
		'selector' => '{{WRAPPER}} .search_form_wrap .search_field_wrap::placeholder',
	]
);

$this->add_control(
	'color_text', [
		'label'     => esc_html__( 'Text Color', 'eazydocs' ),
		'type'      => Controls_Manager::COLOR,
		'selectors' => [
			'{{WRAPPER}} .search_form_wrap .search_field_wrap' => 'color: {{VALUE}};',
		],
		'separator' => 'before'
	]
);

$this->add_control(
    'btn-style-divider',
    [
        'label' => esc_html__( 'Button', 'eazydocs' ),
        'type'      => \Elementor\Controls_Manager::HEADING,
        'separator' => 'before',
    ]
);

$this->add_responsive_control(
    'btn-padding', [
        'label'      => __( 'Padding', 'eazydocs' ),
        'type'       => Controls_Manager::DIMENSIONS,
        'size_units' => [ 'px', '%', 'em' ],
        'separator'  => 'before',
        'selectors'  => [
            '{{WRAPPER}} .search_form_wrap .search_submit_btn' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
        ],
    ]
);

$this->add_control(
	'color_icon', [
		'label'     => esc_html__( 'Icon/Label Color', 'eazydocs' ),
		'type'      => Controls_Manager::COLOR,
		'selectors' => [
			'{{WRAPPER}} .search_submit_btn > i' => 'color: {{VALUE}} !important;',
		],
	]
);

$this->add_control(
	'search_bg',
	[
		'label'     => __( 'Background Color', 'eazydocs' ),
		'type'      => \Elementor\Controls_Manager::COLOR,
		'separator' => 'before',
		'selectors' => [
			'{{WRAPPER}} .search_form_wrap .search_submit_btn' => 'background: {{VALUE}}',
		],
	]
);

$this->end_controls_section();

$this->start_controls_section(
	'ezd_search_style_keywords', [
		'label' => esc_html__( 'Keywords', 'eazydocs' ),
		'tab'   => Controls_Manager::TAB_STYLE,
	]
);

$this->add_control(
	'margin_keywords', [
		'label'       => __( 'Margin', 'eazydocs' ),
		'description' => __( 'Margin around the keywords block', 'eazydocs' ),
		'type'        => Controls_Manager::DIMENSIONS,
		'size_units'  => [ 'px', '%', 'em' ],
		'selectors'   => [ '{{WRAPPER}} .header_search_keyword' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};' ],
		'separator'   => 'before',
		'default'     => [
			'unit' => 'px', // The selected CSS Unit. 'px', '%', 'em',
		],
	]
);

$this->add_control(
	'color_keywords_label', [
		'label'     => esc_html__( 'Label Color', 'eazydocs' ),
		'type'      => Controls_Manager::COLOR,
		'selectors' => [
			'{{WRAPPER}} .header_search_keyword .header-search-form__keywords-label' => 'color: {{VALUE}};',
		],
	]
);

$this->add_group_control(
	\Elementor\Group_Control_Typography::get_type(),
	[
		'name'     => 'keyword_label_typography',
		'label'    => __( 'Label Typography', 'eazydocs' ),
		'scheme'   => \Elementor\Core\Schemes\Typography::TYPOGRAPHY_1,
		'selector' => '{{WRAPPER}} .search_keyword_label',
	]
);

$this->start_controls_tabs(
    'tabs_keywords_style'
);

$this->start_controls_tab(
    'keywords_style',
    [
        'label' => esc_html__( 'Normal', 'eazydocs' ),
    ]
);

$this->add_control(
    'color_keywords', [
        'label'     => esc_html__( 'Keyword Color', 'eazydocs' ),
        'type'      => Controls_Manager::COLOR,
        'separator' => 'before',
        'selectors' => [
            '{{WRAPPER}} .header_search_keyword ul li a' => 'color: {{VALUE}};',
        ],
    ]
);

$this->add_control(
    'color_keywords_bg', [
        'label'     => esc_html__( 'Background Color', 'eazydocs' ),
        'type'      => Controls_Manager::COLOR,
        'separator' => 'after',
        'selectors' => [
            '{{WRAPPER}} .header_search_keyword ul li a' => 'background: {{VALUE}};',
        ],
    ]
);

$this->end_controls_tab();

$this->start_controls_tab(
    'keywords_style_hover',
    [
        'label' => esc_html__( 'Hover', 'eazydocs' ),
    ]
);

$this->add_control(
    'color_keywords_hover', [
        'label'     => esc_html__( 'Keyword Color', 'eazydocs' ),
        'type'      => Controls_Manager::COLOR,
        'selectors' => [
            '{{WRAPPER}} .header_search_keyword ul li a:hover' => 'color: {{VALUE}};',
        ],
    ]
);

$this->add_control(
    'color_keywords_bg_hover', [
        'label'     => esc_html__( 'Background Color', 'eazydocs' ),
        'type'      => Controls_Manager::COLOR,
        'selectors' => [
            '{{WRAPPER}} .header_search_keyword ul li a:hover' => 'background: {{VALUE}};',
        ],
    ]
);

$this->end_controls_tabs();


$this->add_group_control(
	Group_Control_Typography::get_type(), [
		'name'     => 'typography_keywords',
		'label'    => esc_html__( 'Typography', 'eazydocs' ),
		'scheme'   => Typography::TYPOGRAPHY_1,
		'selector' => '{{WRAPPER}} .header_search_keyword ul li a',
	]
);

$this->add_control(
	'keywords_padding', [
		'label'      => __( 'Padding', 'eazydocs' ),
		'type'       => Controls_Manager::DIMENSIONS,
		'size_units' => [ 'px', '%', 'em' ],
		'selectors'  => [ '{{WRAPPER}} .header_search_keyword ul li a' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};' ],
		'default'    => [
			'unit' => 'px', // The selected CSS Unit. 'px', '%', 'em',
		],
	]
);

$this->add_control(
	'border_radius', [
		'label'      => __( 'Border Radius', 'eazydocs' ),
		'type'       => Controls_Manager::DIMENSIONS,
		'size_units' => [ 'px', '%', 'em' ],
		'selectors'  => [ '{{WRAPPER}} .header_search_keyword ul li a' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};' ],
		'default'    => [
			'unit' => 'px', // The selected CSS Unit. 'px', '%', 'em',
		],
	]
);

$this->end_controls_section();