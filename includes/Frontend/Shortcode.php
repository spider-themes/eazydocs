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

        $opt  = get_option( 'eazydocs_settings' );

        $defaults = [
            'col'     => $opt['docs-column'] ?? '3',
            'include' => 'any',
            'exclude' => '',
            'docs_number'   => -1,
            'articles_number' => 10,
            'more'    => __( 'View Details', 'eazydocs' ),
        ];

        $args     = wp_parse_args( $args, $defaults );
        $arranged = [];

            switch ( $args['col'] ) {
                case 1:
                    $args['col'] = '12';
                    break;

                case 2:
                    $args['col'] = '6';
                    break;

                case 3:
                    $args['col'] = '4';
                    break;

                case 4:
                    $args['col'] = '3';
                    break;

                case 6:
                    $args['col'] = '2';
                    break;
            }


        // Parent Docs
        $parent_args = [
            'post_type'   => 'docs',
            'parent'      => 0,
            'sort_column' => 'menu_order',
            'number' => (int) $args['docs_number'] ?? $opt['docs-number']
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
            foreach ( $parent_docs as $root ) {
                $sections = get_children( [
                    'post_parent'    => $root->ID,
                    'post_type'      => 'docs',
                    'posts_per_page' => (int) $args['articles_number'] ?? $opt['articles_number'],
                    'post_status'    => 'publish',
                    'orderby'        => 'menu_order',
                    'order'          => $opt['docs-order'] ?? 'ASC',
                ] );

                $arranged[] = [
                    'doc'      => $root,
                    'sections' => $sections,
                ];
            }
        }

        // call the template
        eazydocs_get_template( 'shortcode.php', [
            'docs' => $arranged,
            'col'  => (int) $args['col'],
            'more' => $args['more'],
        ] );
	}
}