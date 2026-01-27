<?php
namespace EazyDocs\Frontend;

/**
 * Cannot access directly.
 */
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

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
            'img_size'             => $args['img_size'] ?? 'ezd_searrch_thumb50x50',
        ];

        $args       = wp_parse_args( $args, $defaults );
        $arranged   = [];

        // Normalize numeric limits after parsing shortcode attributes.
        $args['show_docs']     = is_numeric( $args['show_docs'] ) ? (int) $args['show_docs'] : -1;
        $args['show_articles'] = is_numeric( $args['show_articles'] ) ? (int) $args['show_articles'] : 5;

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
        $parent_ids = wp_list_pluck( $parent_docs, 'ID' );
        $all_children = [];

        if ( ! empty( $parent_ids ) ) {
            $orderby = [
                'post_parent'                => 'ASC',
                $args['parent_docs_order']   => strtoupper( $args['child_docs_order'] ),
            ];

            $all_children = get_posts( [
                'post_parent__in' => $parent_ids,
                'post_type'       => 'docs',
                'posts_per_page'  => -1,
                'post_status'     => [ 'publish', 'private' ],
                'orderby'         => $orderby,
            ] );
        }

        // Group children by parent
        $children_by_parent = [];
        foreach ( $all_children as $child ) {
            $children_by_parent[ $child->post_parent ][] = $child;
        }

        foreach ( $parent_docs as $root ) {
            $sections = $children_by_parent[ $root->ID ] ?? [];

            // Slice if show_articles is set (and not -1)
            if ( $args['show_articles'] !== -1 && count( $sections ) > $args['show_articles'] ) {
                $sections = array_slice( $sections, 0, $args['show_articles'] );
            }

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
            'img_size'    => $args['img_size'],
        ] );
    }
}