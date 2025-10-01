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

/**
 * Cannot access directly.
 */
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

class Search_Widget extends Widget_Base {
    public function get_name() {
		return 'ezd_search_form';
	}

	public function get_title() {
		return esc_html__( '(EazyDocs) Search', 'eazydocs' );
	}

	public function get_icon() {
		return 'eicon-search';
	}

	public function get_categories() {
		return [ 'eazydocs' ];
	}

    public function get_style_depends (){
        return [ 'ezd-frontend-global', 'elegant-icon' ];
    }

	    
	public function get_keywords() {
		return [ 'search', 'find', 'docs' ];
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
		    'form-width',
		    [
		        'label' => esc_html__( 'Form Width', 'eazydocs' ),
		        'type' => \Elementor\Controls_Manager::SLIDER,
		        'size_units' => [ 'px', '%' ],
		        'range' => [
		            'px' => [
		                'min' => 300,
		                'max' => 1000,
		                'step' => 2,
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
		            '{{WRAPPER}} form.ezd_search_form' => 'max-width: {{SIZE}}{{UNIT}};',
		        ],
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
				    'value' => 'icon_search',
				    'library' => 'elegant-icon',
			    ],
		    ]
	    );

        // button position left or right. Choose field
		$this->add_control(
		    'btn-position',
		    [
		        'label' => esc_html__( 'Button Position', 'eazydocs' ),
		        'type' => \Elementor\Controls_Manager::CHOOSE,
		        'options' => [
			        'left' => [
				        'title' => esc_html__( 'Left', 'eazydocs' ),
				        'icon' => 'eicon-h-align-left',
			        ],
			        'right' => [
				        'title' => esc_html__( 'Right', 'eazydocs' ),
				        'icon' => 'eicon-h-align-right',
			        ],
		        ],
		        'default' => 'right',
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
			    'label' => esc_html__( 'Alignment', 'eazydocs' ),
			    'type' => Controls_Manager::CHOOSE,
			    'options' => [
				    'start' => [
					    'title' => esc_html__( 'Left', 'eazydocs' ),
					    'icon' => 'eicon-h-align-left',
				    ],
				    'center' => [
					    'title' => esc_html__( 'Center', 'eazydocs' ),
					    'icon' => 'eicon-h-align-center',
				    ],
				    'end' => [
					    'title' => esc_html__( 'Right', 'eazydocs' ),
					    'icon' => 'eicon-h-align-right',
				    ]
			    ]
		    ]
	    );

        // keyword by dynamic || static select
		$this->add_control(
            'keywords_by',
            [
			'type'          => \Elementor\Controls_Manager::SELECT,
			'label'         => esc_html__( 'Keywords By', 'eazydocs' ),
			'description'   => esc_html__( 'Static keywords are predefined, while dynamic keywords are generated by queries from website visitors', 'eazydocs' ),
			'options' => array(
				'static'	=> esc_html__( 'Static', 'eazydocs' ),
				'dynamic'  	=> esc_html__( 'Dynamic (Sort by popular)', 'eazydocs' ),
			),
			'default'   => 'static',
			'condition' => array(
                'is_ezd_search_keywords' => 'yes'
			)
            ]
        );

        $keywords = new \Elementor\Repeater();

        $keywords->add_control(
            'title', [
                'label' => esc_html__( 'Title', 'eazydocs' ),
                'type' => \Elementor\Controls_Manager::TEXT,
                'label_block' => true
            ]
        );

        $this->add_control(
            'ezd_search_keywords_repeater',
            [
                'label' => esc_html__( 'Keywords', 'eazydocs' ),
                'type' => \Elementor\Controls_Manager::REPEATER,
                'fields' => $keywords->get_controls(),
                'default' => [
                    [
                        'title' => esc_html__( 'Keyword #1', 'eazydocs' ),
                    ],
                    [
                        'title' => esc_html__( 'Keyword #2', 'eazydocs' ),
                    ],
                ],
                'title_field' => '{{{ title }}}',
                'prevent_empty' => false,
                'condition' => [
                    'is_ezd_search_keywords' => 'yes',
                    'keywords_by' => 'static'
                ]
            ]
        );
        
		$this->add_control(
            'keywords_limit',
            [
			'type'       => \Elementor\Controls_Manager::NUMBER,
			'label'      => esc_html__( 'Keywords Limit', 'eazydocs' ),
			'description'   => esc_html__( 'Set the number of keywords to show.', 'eazydocs' ),
			'default'    => 6,
			'min'        => 1,
			'max'        => 200,
			'step'       => 1,
			'condition' => array(
                'keywords_by' => 'dynamic',
			),
			'class'      => 'eazydocs-pro-notice',
            ]
		);

		// not found keywords exclude checkbox
		$this->add_control(
            'is_exclude_not_found',
            [
			'type'       => \Elementor\Controls_Manager::SWITCHER,
			'label'      => esc_html__( 'Exclude Not Found Keywords', 'eazydocs' ),
			'description'   => esc_html__( 'Exclude the keywords that are not found in the search results.', 'eazydocs' ),
            'return_value' => 'yes',
			'default'    => 'no',
			'text_width' => 70,
			'condition' => array(
                'keywords_by' => 'dynamic',
			),
			'class'      => 'eazydocs-pro-notice',
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
        
        include( "ezd-search.php" );
	}
}