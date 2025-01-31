<?php
namespace eazyDocs\Frontend;

/**
 * Shortcode.
 */
class Shortcode {
	/**
	 * Initialize the class
	 */
	public function __construct() {
		add_shortcode( 'eazydocs', [ $this, 'shortcode' ] );
	}

	/**
	 * Shortcode handler.
	 *
	 * @param array  $atts
	 * @param string $content
	 *
	 * @return string
	 */
	public function shortcode( $atts, $content = '' ) {
		Assets::enqueue_scripts();

		ob_start();
		self::eazydocs( $atts );
		$content .= ob_get_clean();

		return $content;
	}

	/**
	 * Generic function for displaying docs.
	 *
	 * @param array $args
	 *
	 * @return void
	 */
	public static function eazydocs( $args = [] ) {
        
        $defaults = [
            'col'           => ! empty ($args['col']) ? (int) $args['col'] : 3,
            'include'       => 'any',
            'exclude'       => '',
            'show_docs'     => ! empty ( $args['show_docs'] ) ? (int) $args['show_docs'] : -1,
            'show_articles' => ! empty ( $args['show_articles'] ) ? (int) $args['show_articles'] : 5,
            'parent_docs_order' => $args['parent_docs_order'] ?? 'ID',
        ];

        $args               = wp_parse_args( $args, $defaults );
        $arranged           = [];
        
        // Parent Docs
        $parent_args = [
            'post_type'     => 'docs',
            'parent'        => 0,
            'orderby'       => $args['parent_docs_order'] ?? 'menu_order',
            'order'         => $args['parent_docs_order_by'] ?? 'desc',
            'post_status'   => array( 'publish', 'private' ),
            'number'        => ! empty ( $args['show_docs'] ) ? (int) $args['show_docs'] : -1,
        ];

        if ( 'any' != $args['include'] ) {
            $parent_args['include'] = $args['include'];
        }

        if ( !empty( $args['exclude'] ) ) {
            $parent_args['exclude'] = $args['exclude'];
        }

        $parent_docs = get_pages( $parent_args );

        // arrange the docs
        if ( $parent_docs ) {

            if ( ezd_is_premium() ) {
                usort( $parent_docs, function ($a, $b) use ($args) {
                    $parent_args = $args['parent_docs_order'];    
                    $parent_docs_order_by = $args['parent_docs_order_by'] ?? 'asc'; 
                    if ( $parent_docs_order_by == 'asc' ) {
                        return ($a->$parent_args < $b->$parent_args) ? -1 : 1;
                    } else {
                        return ($a->$parent_args > $b->$parent_args) ? -1 : 1;
                    }
                    
                });
            }
            
            foreach ( $parent_docs as $root ) {
                $sections = get_children( [
                    'post_parent'    => $root->ID,
                    'post_type'      => 'docs',
                    'numberposts'    => ! empty ( $args['show_articles'] ) ? (int) $args['show_articles'] : 5,
                    'post_status'    => array( 'publish', 'private' ),
                    'orderby'        => $args['parent_docs_order'],
                    'order'          => $args['child_docs_order'] ?? 'ASC',
                ] );

                $arranged[] = [
                    'doc'           => $root,
                    'sections'      => $sections
                ];
            }
        }

        // call the template
        eazydocs_get_template( 'shortcode.php', [
            'docs'                => $arranged,
            'col'                 => ! empty ($args['col']) ? (int) $args['col'] : 3,
            'more'                => ! empty ($args['more']) ? $args['more'] : __( 'View Details', 'eazydocs' ),
            'show_topic'          => $args['show_topic'] ?? false,
            'topic_label'         => ! empty ($args['topic_label']) ? $args['topic_label'] : __( 'Topics', 'eazydocs' ),
            'layout'              => $args['docs_layout'] ?? 'grid'            
        ] );
	}
}