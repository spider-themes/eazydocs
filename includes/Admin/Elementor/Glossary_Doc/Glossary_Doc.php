<?php
namespace EazyDocs\Admin\Elementor\Glossary_Doc;

use Elementor\Widget_Base;
use Elementor\Controls_Manager;
use Elementor\Core\Schemes\Color;
use Elementor\Core\Schemes\Typography;
use Elementor\Group_Control_Typography;
use Elementor\Group_Control_Text_Shadow;
use WP_Query;
use WP_Post;

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Class Glossary_doc
 * @package DocyCore\Widgets
 */
class Glossary_Doc extends Widget_Base {

	public function get_name() {
		return 'docy_glossary_doc';
	}

	public function get_title() {
		return __( 'Glossary Doc', 'docy-core' );
	}

	public function get_icon() {
		return 'eicon-document-file';
	}

	public function get_categories() {
		return [ 'docy-elements' ];
	}

    public function get_script_depends() {
        return [ 'mixitup', 'eazydocs-el-widgets'];
    }

	protected function register_controls() {

		// ---Start Document Setting
		// $this->start_controls_section(
		// 	'doc_design_sec', [
		// 		'label' => __( 'Preset Skin', 'docy-core' ),
		// 	]
		// );

		// $this->add_control(
		// 	'style', [
		// 		'label'   => esc_html__( 'Skins', 'docy-core' ),
		// 		'type'    => Controls_Manager::CHOOSE,
		// 		'options' => [
		// 			'1' => [
		// 				'title' => __( 'Light', 'coro-core' ),
		// 				'icon'  => 'single-doc1',
		// 			],
		// 			'2' => [
		// 				'title' => __( 'Creative', 'coro-core' ),
		// 				'icon'  => 'single-doc2',
		// 			],
		// 			'3' => [
		// 				'title' => __( 'Box', 'coro-core' ),
		// 				'icon'  => 'single-doc3',
		// 			],
		// 			'4' => [
		// 				'title' => __( 'Topic Boxes', 'coro-core' ),
		// 				'icon'  => 'single-doc4',
		// 			],
		// 			'5' => [
		// 				'title' => __( 'Docs Boxes', 'coro-core' ),
		// 				'icon'  => 'single-doc5',
		// 			],
		// 		],
		// 		'toggle'  => false,
		// 		'default' => '1',
		// 	]
		// );

		// $this->end_controls_section();


		/** ============ Title Section ============ **/
		$this->start_controls_section(
			'content_sec',
			[
				'label'     => esc_html__( 'Title', 'docy-core' ),
				'condition' => [
					'style' => [ '2' ]
				]
			]
		);

		$this->add_control(
			'title',
			[
				'label'       => esc_html__( 'Title Text', 'docy-core' ),
				'type'        => Controls_Manager::TEXTAREA,
				'label_block' => true,
				'default'     => 'Recommended Topics',
			]
		);

		$this->add_control(
			'title_tag', [
				'label'     => __( 'Title Tag', 'docy-core' ),
				'type'      => \Elementor\Controls_Manager::SELECT,
				'default'   => 'h2',
				'options'   => docy_el_title_tags(),
				'separator' => 'after'
			]
		);

		$this->add_control(
			'subtitle',
			[
				'label'       => esc_html__( 'Subtitle Text', 'docy-core' ),
				'type'        => Controls_Manager::TEXTAREA,
				'label_block' => true,
			]
		);

		$this->end_controls_section();


		// --- Doc ------------------------------------
		$this->start_controls_section(
			'doc_opt', [
				'label' => __( 'Doc', 'docy-core' ),
			]
		);

		$this->add_control(
			'doc', [
				'label'   => esc_html__( 'Doc', 'docy-core' ),
				'type'    => Controls_Manager::SELECT,
				'options' => docy_get_posts()
			]
		);

		$this->add_control(
			'doc_sec_excerpt', [
				'label'       => esc_html__( 'Excerpt', 'docy-core' ),
				'description' => esc_html__( 'Excerpt word limit of the documentation sections. If the excerpt got empty, this will get from the post content.', 'docy-core' ),
				'type'        => Controls_Manager::NUMBER,
				'label_block' => true,
				'default'     => 8,
				'condition'   => [
					'style' => '4'
				]
			]
		);

		$this->add_control(
			'ppp_doc_items', [
				'label'       => esc_html__( 'Articles', 'docy-core' ),
				'description' => esc_html__( 'Number of articles to show under every sections', 'docy-core' ),
				'type'        => Controls_Manager::NUMBER,
				'label_block' => true,
				'default'     => 4,

			]
		);

		$this->add_control(
			'order', [
				'label'   => esc_html__( 'Order', 'docy-core' ),
				'type'    => Controls_Manager::SELECT,
				'options' => [
					'ASC'  => 'ASC',
					'DESC' => 'DESC'
				],
				'default' => 'ASC'
			]
		);

		$this->end_controls_section();


		// Buttons
		$this->start_controls_section(
			'view_all_btn_opt', [
				'label' => __( 'Buttons', 'docy-core' ),
				'condition'   => [
					'style' => [ '1', '3' ]
				]
			]
		);

		$this->add_control(
			'read_more', [
				'label'       => esc_html__( 'Read More Button', 'docy-core' ),
				'type'        => Controls_Manager::TEXT,
				'label_block' => true,
				'default'     => 'Read More',
			]
		);

		$this->add_control(
			'section_btn', [
				'label'        => esc_html__( 'Section Button', 'docy-core' ),
				'type'         => \Elementor\Controls_Manager::SWITCHER,
				'return_value' => 'yes',
				'default'      => 'yes',
				'condition'   => [
					'style' => [ '1', '2' ]
				]
			]
		);

		$this->add_control(
			'section_btn_txt', [
				'label'       => esc_html__( 'Button Text', 'docy-core' ),
				'type'        => Controls_Manager::TEXT,
				'label_block' => true,
				'default'     => 'View More',
				'condition'   => [
					'section_btn' => 'yes',
					'style' => [ '1', '2' ]
				]
			]
		);

		$this->add_control(
			'section_btn_url', [
				'label'       => esc_html__( 'Button URL', 'docy-core' ),
				'type'        => Controls_Manager::TEXT,
				'label_block' => true,
				'condition'   => [
					'section_btn' => 'yes',
					'style' => [ '1', '2' ]
				]
			]
		);

		$this->add_control(
			'show_more_btn', [
				'label'       => esc_html__( 'Show More Button', 'docy-core' ),
				'type'        => Controls_Manager::TEXT,
				'label_block' => true,
				'default'     => 'View All',
				'condition'   => [
					'style' => [ '4' ]
				]
			]
		);

		$this->add_control(
			'show_less_btn', [
				'label'       => esc_html__( 'Show Less Text', 'docy-core' ),
				'type'        => Controls_Manager::TEXT,
				'label_block' => true,
				'default'     => 'Show Less',
				'condition'   => [
					'style' => [ '4' ]
				]
			]
		);

		$this->end_controls_section();


		/**
		 * Style Tab
		 * ------------------------------ Style Box ------------------------------
		 */
		$this->start_controls_section(
			'style_box', [
				'label'     => __( 'Box', 'docy-core' ),
				'tab'       => Controls_Manager::TAB_STYLE,
				'condition'   => [
					'style' => [ '1', '2', '3', '4'  ]
				]
			]
		);

		$this->add_group_control(
			\Elementor\Group_Control_Background::get_type(),
			[
				'name' => 'box-background',
				'label' => esc_html__( 'Background', 'docy-core' ),
				'types' => [ 'classic', 'gradient' ],
				'selector' => '{{WRAPPER}} .box-item',
			]
		);

		$this->add_group_control(
			\Elementor\Group_Control_Border::get_type(),
			[
				'name' => 'box-border',
				'label' => esc_html__( 'Border', 'docy-core' ),
				'selector' => '{{WRAPPER}} .box-item',
			]
		);

		$this->add_control(
			'box-padding',
			[
				'label' => esc_html__( 'Padding', 'docy-core' ),
				'type' => \Elementor\Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', 'em' ],
				'selectors' => [
					'{{WRAPPER}} .box-item' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->end_controls_section();

		/**
		 * Style Tab
		 * ------------------------------ Style Title ------------------------------
		 */
		$this->start_controls_section(
			'style_title', [
				'label'     => __( 'Title', 'docy-core' ),
				'tab'       => Controls_Manager::TAB_STYLE,
				'condition' => [
					'style' => [ '2' ]
				]
			]
		);

		$this->add_control(
			'color_title', [
				'label'     => __( 'Text Color', 'docy-core' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .title' => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(), [
				'name'     => 'typography_prefix',
				'scheme'   => Typography::TYPOGRAPHY_1,
				'selector' => '
                    {{WRAPPER}} .title'
			]
		);

		$this->add_group_control(
			Group_Control_Text_Shadow::get_type(), [
				'name'     => 'text_shadow_prefix',
				'selector' => '{{WRAPPER}} .title',
			]
		);

		$this->end_controls_section();

		//------------------------------ Style Subtitle ------------------------------
		$this->start_controls_section(
			'style_subtitle_sec', [
				'label'     => __( 'Subtitle', 'docy-core' ),
				'tab'       => Controls_Manager::TAB_STYLE,
				'condition' => [
					'style' => [ '2' ]
				]
			]
		);

		$this->add_control(
			'color_subtitle', [
				'label'     => __( 'Text Color', 'docy-core' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .subtitle' => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(), [
				'name'     => 'typography_subtitle',
				'scheme'   => Typography::TYPOGRAPHY_1,
				'selector' => '{{WRAPPER}} .subtitle',
			]
		);

		$this->add_group_control(
			Group_Control_Text_Shadow::get_type(), [
				'name'     => 'text_shadow_subtitle',
				'selector' => '{{WRAPPER}} .subtitle',
			]
		);

		$this->end_controls_section();


		/**
		 * Style Content Tab
		 * ------------------------------ Style Content ------------------------------
		 */
		$this->start_controls_section(
			'style_content', [
				'label' => __( 'Content', 'docy-core' ),
				'tab'   => Controls_Manager::TAB_STYLE,
				'condition'   => [
					'style' => [ '1', '2', '3', '4'  ]
				]
			]
		);

		$this->add_control(
			'heading_title', [
				'label' => __( 'Title', 'docy-core' ),
				'type'  => Controls_Manager::HEADING
			]
		);
		$this->add_control(
			'doc_color_title', [
				'label'     => __( 'Color', 'docy-core' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .ct-heading-text' => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(), [
				'name'     => 'title_typography_prefix',
				'scheme'   => Typography::TYPOGRAPHY_1,
				'selector' => '
                    {{WRAPPER}} .ct-heading-text'
			]
		);

		$this->add_group_control(
			Group_Control_Text_Shadow::get_type(), [
				'name'     => 'title_shadow_prefix',
				'selector' => '{{WRAPPER}} .ct-heading-text',
			]
		);

		$this->add_control(
			'heading_content', [
				'label'     => __( 'Content', 'docy-core' ),
				'type'      => Controls_Manager::HEADING,
				'separator' => 'before'
			]
		);
		$this->add_control(
			'doc_color_content', [
				'label'     => __( 'Color', 'docy-core' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .ct-content-text, {{WRAPPER}} .ct-content-text p' => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(), [
				'name'     => 'content_typography_prefix',
				'scheme'   => Typography::TYPOGRAPHY_1,
				'selector' => '
                    {{WRAPPER}} .ct-content-text, {{WRAPPER}} .ct-content-text p'
			]
		);

		$this->add_group_control(
			Group_Control_Text_Shadow::get_type(), [
				'name'     => 'content_shadow_prefix',
				'selector' => '{{WRAPPER}} .ct-content-text, {{WRAPPER}} .ct-content-text p',
			]
		);

		$this->end_controls_section();


		/**
		 * Background Objects
		 */
		$this->start_controls_section(
			'style_bg_objects', [
				'label'     => esc_html__( 'Background Objects', 'docy-core' ),
				'tab'       => Controls_Manager::TAB_STYLE,
				'condition' => [
					'style' => [ '2' ]
				]
			]
		);

		$this->add_control(
			'is_bg_objects', [
				'label'        => esc_html__( 'Background Objects', 'docy-core' ),
				'type'         => \Elementor\Controls_Manager::SWITCHER,
				'return_value' => 'yes',
				'default'      => 'yes',
			]
		);

		$this->add_control(
			'bg_shape', [
				'label'       => esc_html__( 'Shape', 'docy-core' ),
				'description' => esc_html__( 'The background shape should be transparent png or svg image.', 'docy-core' ),
				'type'        => Controls_Manager::MEDIA,
				'default'     => [
					'url' => plugins_url( 'images/docbg-shap.png', __FILE__ )
				],
				'condition'   => [
					'is_bg_objects' => [ 'yes' ]
				]
			]
		);

		$this->add_control(
			'is_round1', [
				'label'        => esc_html__( 'Round Objects 01', 'docy-core' ),
				'type'         => \Elementor\Controls_Manager::SWITCHER,
				'return_value' => 'yes',
				'default'      => 'yes',
				'separator'    => 'before'
			]
		);

		$this->add_control(
			'round1_color', [
				'label'     => __( 'Round 01 Color', 'docy-core' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .doc_round.one' => 'background: {{VALUE}}',
				],
				'condition' => [
					'is_bg_objects' => 'yes',
					'is_round1'     => 'yes',
				]
			]
		);

		$this->add_control(
			'is_round2', [
				'label'        => esc_html__( 'Round Objects 02', 'docy-core' ),
				'type'         => \Elementor\Controls_Manager::SWITCHER,
				'return_value' => 'yes',
				'default'      => 'yes',
				'separator'    => 'before'
			]
		);

		$this->add_control(
			'round2_color', [
				'label'     => __( 'Round 02 Color', 'docy-core' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .doc_round.two' => 'background: {{VALUE}}',
				],
				'condition' => [
					'is_bg_objects' => 'yes',
					'is_round1'     => 'yes',
				]
			]
		);

		$this->end_controls_section();

	}

	protected function render() {
		$settings  = $this->get_settings();
		$title_tag = ! empty( $settings['title_tag'] ) ? $settings['title_tag'] : 'h2';

		/**
		 * Get the parent docs with query
		 */
        $sections = get_children( array(
            'post_parent'    => $settings['doc'],
            'post_type'      => 'docs',
            'post_status'    => 'publish',
            'orderby'        => 'menu_order',
            'order'          => $settings['order'],
            'posts_per_page' => -1,
        ) );

        // include( "inc/Glossary-Doc/glossary-doc-1.php" );
		// / get theme name
		$theme = wp_get_theme();
		if ( ezd_is_premium() || $theme == 'docy' || $theme == 'Docy' ) {
			// include( "glossary-doc-{$settings['doc-widget-single-search']}.php" );
			include( "glossary-doc-1.php" );
		} else {
			include( "glossary-doc-1.php" );
		}
	}
}