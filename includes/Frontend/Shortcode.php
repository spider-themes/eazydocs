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
            'col'                  => ! empty( $args['col'] ) ? (int) $args['col'] : 3,
            'include'              => 'any',
            'exclude'              => '',
            'show_docs'            => ! empty( $args['show_docs'] ) ? (int) $args['show_docs'] : -1,
            'show_articles'        => ! empty( $args['show_articles'] ) ? (int) $args['show_articles'] : 5,
            'parent_docs_order'    => $args['parent_docs_order'] ?? 'menu_order',
            'parent_docs_order_by' => $args['parent_docs_order_by'] ?? 'ASC',
            'child_docs_order'     => $args['child_docs_order'] ?? 'ASC',
        ];

        $args       = wp_parse_args( $args, $defaults );
        $arranged   = [];

        // Parent Docs Query Args
        $parent_args = [
            'post_type'     => 'docs',
            'post_parent'   => 0,
            'orderby'       => $args['parent_docs_order'],
            'order'         => strtoupper( $args['parent_docs_order_by'] ),
            'post_status'   => [ 'publish', 'private' ],
            'numberposts'   => $args['show_docs']
        ];

        if ( 'any' !== $args['include'] ) {
            $parent_args['include'] = explode( ',', $args['include'] );
        }

        if ( ! empty( $args['exclude'] ) ) {
            $parent_args['exclude'] = explode( ',', $args['exclude'] );
        }

        $parent_docs = get_posts( $parent_args );

        // Optional PHP-side sort override (for premium users)
        if ( $parent_docs && ezd_is_premium() ) {
            usort( $parent_docs, function( $a, $b ) use ( $args ) {
                $key    = $args['parent_docs_order'];
                $order  = strtoupper( $args['parent_docs_order_by'] );
                $valA   = $a->$key ?? 0;
                $valB   = $b->$key ?? 0;

                if ( $valA == $valB ) return 0;

                return ( $order === 'ASC' ) ? ( $valA < $valB ? -1 : 1 ) : ( $valA > $valB ? -1 : 1 );
            });
        }

        // Fetch Child Docs (Articles)
        foreach ( $parent_docs as $root ) {
            $sections = get_children( [
                'post_parent'    => $root->ID,
                'post_type'      => 'docs',
                'numberposts'    => $args['show_articles'],
                'post_status'    => [ 'publish', 'private' ],
                'orderby'        => $args['parent_docs_order'],
                'order'          => strtoupper( $args['child_docs_order'] ),
            ] );

            $arranged[] = [
                'doc'      => $root,
                'sections' => $sections
            ];
        }

        // Load the template
        eazydocs_get_template( 'shortcode.php', [
            'docs'        => $arranged,
            'col'         => $args['col'],
            'more'        => $args['more'] ?? esc_html__( 'View Details', 'eazydocs' ),
            'show_topic'  => $args['show_topic'] ?? true,
            'topic_label' => $args['topic_label'] ?? esc_html__( 'Topics', 'eazydocs' ),
            'layout'      => $args['docs_layout'] ?? 'grid',
        ] );
    }
}