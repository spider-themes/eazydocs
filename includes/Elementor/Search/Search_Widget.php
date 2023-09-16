<?php
namespace EazyDocs\Elementor\Search;

use Elementor\Repeater;
use Elementor\Widget_Base;
use Elementor\Controls_Manager;
use Elementor\Core\Schemes\Color;
use Elementor\Core\Schemes\Typography;
use Elementor\Group_Control_Typography;
use Elementor\Group_Control_Text_Shadow;
use WP_Query;
use WP_Post;

class Search_Widget extends Widget_Base {
    public function get_name() {
		return 'ezd_search_form';
	}

	public function get_title() {
		return esc_html__( 'EazyDocs Search', 'eazydocs' );
	}

	public function get_icon() {
		return 'eicon-search';
	}

	public function get_categories() {
		return [ 'eazydocs' ];
	}

    public function get_script_depends() {
        return [ 'eazydocs-el-widgets' ];
    }

    public function get_style_depends (){
        return [ 'ezd-el-widgets', 'font-awesome' ];
    }
    
	public function get_keywords() {
		return [ 'search' ];
	}
 
	protected function register_controls() {

        /** ============ Search Form ============ **/
        $this->start_controls_section(
            'search_form_sec',
            [
                'label' => esc_html__( 'Form', 'eazydocs' ),
            ]
        );

        $this->add_control(
            'placeholder',
            [
                'label' => esc_html__( 'Placeholder', 'eazydocs' ),
                'type' => Controls_Manager::TEXT,
                'label_block' => true,
                'default' => 'Search for Topics....',
            ]
        );

        $this->add_control(
            'btn-divider',
            [
                'label' => esc_html__( 'Button', 'eazydocs' ),
                'type'      => \Elementor\Controls_Manager::HEADING,
                'separator' => 'before',
            ]
        );

	    $this->add_control(
		    'submit_btn_icon',
		    [
			    'label' => esc_html__( 'Submit Button Icon', 'eazydocs' ),
			    'type' => \Elementor\Controls_Manager::ICONS,
			    'default' => [
				    'value' => 'fas fa-search',
				    'library' => 'solid',
			    ],
		    ]
	    );

        $this->add_control(
            'btn-position',
            [
                'label' => esc_html__( 'Position', 'eazydocs' ),
                'type' => \Elementor\Controls_Manager::SLIDER,
                'size_units' => [ 'px', '%' ],
                'range' => [
                    'px' => [
                        'min' => 0,
                        'max' => 100,
                        'step' => 1,
                    ],
                    '%' => [
                        'min' => 0,
                        'max' => 100,
                    ],
                ],
                'default' => [
                    'unit' => 'px',
                ],
                'selectors' => [
                    '{{WRAPPER}} .search_form_wrap .search_submit_btn' => 'right: {{SIZE}}{{UNIT}} !important;',
                ],
            ]
        );

        $this->end_controls_section();

        $this->start_controls_section(
            'ezd_search_keywords_sec',
            [
                'label' => esc_html__( 'Keywords', 'eazydocs' ),
            ]
        );

        $this->add_control(
            'is_ezd_search_keywords', [
                'label' => esc_html__( 'Keywords', 'eazydocs' ),
                'type' => \Elementor\Controls_Manager::SWITCHER,
                'return_value' => 'yes',
                'default' => 'yes',
                'separator' => 'before',
            ]
        );

        $this->add_control(
            'ezd_search_keywords_label',
            [
                'label' => esc_html__( 'Keywords Label', 'eazydocs' ),
                'type' => Controls_Manager::TEXT,
                'label_block' => true,
                'default' => 'Popular:',
                'condition' => [
                    'is_ezd_search_keywords' => 'yes'
                ]
            ]
        );

	    $this->add_responsive_control(
		    'ezd_search_keywords_align',
		    [
			    'label' => __( 'Alignment', 'eazydocs' ),
			    'type' => Controls_Manager::CHOOSE,
			    'options' => [
				    'start' => [
					    'title' => __( 'Left', 'eazydocs' ),
					    'icon' => 'eicon-h-align-left',
				    ],
				    'center' => [
					    'title' => __( 'Center', 'eazydocs' ),
					    'icon' => 'eicon-h-align-center',
				    ],
				    'end' => [
					    'title' => __( 'Right', 'eazydocs' ),
					    'icon' => 'eicon-h-align-right',
				    ],
			    ],
			    'prefix_class' => 'elementor%s-align-',
		    ]
	    );

        $keywords = new \Elementor\Repeater();

        $keywords->add_control(
            'title', [
                'label' => __( 'Title', 'eazydocs' ),
                'type' => \Elementor\Controls_Manager::TEXT,
                'label_block' => true,
            ]
        );

        $this->add_control(
            'ezd_search_keywords_repeater',
            [
                'label' => __( 'Repeater List', 'eazydocs' ),
                'type' => \Elementor\Controls_Manager::REPEATER,
                'fields' => $keywords->get_controls(),
                'default' => [
                    [
                        'title' => __( 'Keyword #1', 'eazydocs' ),
                    ],
                    [
                        'title' => __( 'Keyword #2', 'eazydocs' ),
                    ],
                ],
                'title_field' => '{{{ title }}}',
                'prevent_empty' => false,
                'condition' => [
                    'is_ezd_search_keywords' => 'yes'
                ]
            ]
        );

        $this->end_controls_section();		
        
         /**
         * Style Keywords
         * Global
         */
        include ('style-control.php');
        
    }

    protected function render() {
		$settings       = $this->get_settings();
        $title_tag = !empty($settings['title_tag']) ? $settings['title_tag'] : 'h2';
        
        include( "ezd-search.php" );

	}
}