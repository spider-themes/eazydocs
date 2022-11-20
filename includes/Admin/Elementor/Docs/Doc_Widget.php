<?php 
namespace EazyDocs\Admin\Elementor\Docs;

use Elementor\Repeater;
use Elementor\Widget_Base;
use Elementor\Controls_Manager;
use Elementor\Core\Schemes\Color;
use Elementor\Core\Schemes\Typography;
use Elementor\Group_Control_Typography;
use Elementor\Group_Control_Text_Shadow;
use WP_Query;
use WP_Post;

class Doc_Widget extends Widget_Base {
	public function get_name() {
		return 'ezd_docs';
	}

	public function get_title() {
		return esc_html__( 'EazyDocs Multi Docs', 'eazydocs' );
	}

	public function get_icon() {
		return 'eicon-document-file';
	}

	public function get_categories() {
		return [ 'eazydocs' ];
	}

    public function get_script() {
        return [ 'eazydocs', 'docs', 'documentations', 'knowledge base', 'knowledgebase', 'kb', 'eazydocs' ];
    }

    public function get_style_depends (){
        return [ 'ezd-docs-widget', 'bootstrap', 'elegant-icon' ];
    }
    
	public function get_keywords() {
		return [ 'docs' ];
	}

    public function get_script_dipeends() {
        return [ 'ezd-script-handle' ];
    }
 
	protected function register_controls() {

		$repeater = new \Elementor\Repeater();

		// ---Start Document Setting
		$this->start_controls_section(
			'doc_design_sec', [
				'label' => __( 'Preset Skin', 'eazydocs' ),
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
				'description'   => esc_html__( 'If the slug ID does not work then you should pick the number ID.', 'eazydocs' ),
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
				'label_block' => true,
				'default'     => 6,
				'condition'   => [
					'doc-widget-skin' => [ '2', '3', '4', '5' ]
				]
			]
		);

		$this->add_control(
			'active_doc',
			[
				'label'       => __( 'Active Doc', 'docly-core' ),
				'description' => __( 'Select the active Doc tab by default.', 'docly-core' ),
				'type'        => Controls_Manager::SELECT,
				'options'     => ezd_get_posts(),
				'label_block' => true,
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
				'label_block' => true,
				'default'     => -1,
				'condition'   => [
					'is_custom_order' => '',
					'doc-widget-skin' => [ '1', '2', '3', '4', '5' , '6' ]
				],
			]
		);

		$this->add_control(
			'main_doc_excerpt', [
				'label'       => esc_html__( 'Main Doc Excerpt', 'eazydocs' ),
				'description' => esc_html__( 'Excerpt word limit of main documentation. If the excerpt got empty, this will get from the post content.', 'eazydocs' ),
				'type'        => Controls_Manager::NUMBER,
				'label_block' => true,
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
				'label_block' => true,
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
				'condition' => [
					'is_custom_order' => ''
				]
			]
		);

		$this->add_control(
			'is_custom_order',
			[
				'label'        => __( 'Custom Order', 'docly-core' ),
				'description'  => __( 'Order the Doc tabs as you want.', 'docly-core' ),
				'type'         => \Elementor\Controls_Manager::SWITCHER,
				'return_value' => 'yes',
				'default'      => '',
				'separator'    => 'before',
				'condition'    => [
					'doc-widget-skin' => [ '2', '3', '5' ]
				]
			]
		);

		$doc = new Repeater();

		$doc->add_control(
			'doc',
			[
				'label'       => __( 'Doc', 'docly-core' ),
				'type'        => Controls_Manager::SELECT,
				'options'     => ezd_get_posts(),
				'label_block' => true,
			]
		);

		$this->add_control(
			'docs',
			[
				'label'         => __( 'Tabs Items', 'docly-core' ),
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
			'is_tab_title_first_word',
			[
				'label'        => __( 'Tab Title First Word', 'eazydocs' ),
				'description'  => __( 'Show the first word of the doc in Tab Title.', 'eazydocs' ),
				'type'         => \Elementor\Controls_Manager::SWITCHER,
				'return_value' => 'yes',
				'default'      => 'yes',
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
				'default'     => 'View All',
				'condition'   => [
					'doc-widget-skin' => [ '1', '2', '3' ]
				]
			]
		);

		$this->add_control(
			'book_chapter_prefix',
			[
				'label'     => __( 'Book Chapters / Tutorials Prefix', 'eazydocs' ),
				'type'      => \Elementor\Controls_Manager::TEXT,
				'condition' => [
					'doc-widget-skin' => [ '4' ]
				]
			]
		);

		$this->end_controls_section();
	}

	protected function render() {
		$settings       = $this->get_settings();
		$doc_number     = $settings['ppp_doc_items'] ?? -1;
		$read_more      = $settings['read_more'] ?? '';
		$doc_order      = $settings['order'] ?? '';
		$doc_exclude    = $settings['exclude'] ?? '';
        
		/**
		 * Get the parent docs with query
		 */
		if ( ! empty( $settings['exclude'] ) ) {
			$parent_docs = get_pages( array(
				'post_type'  => 'docs',
				'parent'     => 0,
				'sort_order' => $settings['order'],
				'exclude'    => $settings['exclude']
			));
		} else {
			$parent_docs = get_pages( array(
				'post_type'  => 'docs',
				'parent'     => 0,
				'sort_order' => $settings['order'],
			) );
		}

		/**
		 * Get the doc sections
		 */
		if ( $parent_docs ) {
			foreach ( $parent_docs as $root ) {
				
				$sections = get_children( array(
					'post_parent'    => $root->ID,
					'post_type'      => 'docs',
					'post_status'    => 'publish',
					'orderby'        => 'menu_order',
					'order'          => 'ASC',
					'posts_per_page' => ! empty( $settings['show_section_count'] ) ? $settings['show_section_count'] : - 1,
				) );

				$docs[]   = array(
					'doc'      => $root,
					'sections' => $sections,
				);

			}
		}

        if ( class_exists( 'EazyDocsPro' ) ) {
		    include( "docs-{$settings['doc-widget-skin']}.php" );
        } else {
            include( "docs-1.php" );
        }

	}
}