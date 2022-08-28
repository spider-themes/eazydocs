<?php
use Carbon_Fields\Block;
use Carbon_Fields\Field;

function add_docs() {
	$docs = new WP_Query(
		[
			'post_type'      => 'docs',
			'post_parent'    => 0,
			'posts_per_page' => -1,
		]
	);

	while ( $docs->have_posts() ) :
		$docs->the_post();
		$doclist[ get_the_ID() ] = get_the_title();
	endwhile;
	wp_reset_postdata();

	return $doclist;
}

$doclist = add_docs();


Block::make( __( 'Docs Archive', 'eazydocs' ) )
	->add_fields(
		[
			Field::make( 'select', 'cols', __( 'Choose The number of columns', 'eazydocs' ) )
			->set_options(
				[
					'4' => 4,
					'3' => 3,
					'2' => 2,
					'1' => 1,
				]
			),

			Field::make( 'multiselect', 'include', __( 'Include Docs', 'eazydocs' ) )
			->add_options(
				add_docs()
			),

			Field::make( 'multiselect', 'exclude', __( 'Exclude Docs', 'eazydocs' ) )
			->add_options(
				add_docs()
			),
			Field::make( 'text', 'show_docs', __( 'Show Docs ( number of docs to show, input -1 for all the docs. default is 4 )', 'eazydocs' ) ),
			Field::make( 'text', 'show_article', __( 'Show Articles ( number of articles to show, input -1 for all the articles. default is 5 )', 'eazydocs' ) ),
			Field::make( 'text', 'more_text', __( 'View More button text', 'eazydocs' ) ),
		]
	)
	->set_description( __( 'EazyDocs Document Archive', 'eazydocs' ) )
	->set_category( 'eazydocs', __( 'EazyDocs', 'eazydocs' ), 'document' )
	->set_icon( 'archive' )
	->set_keywords( [ __( 'archive', 'eazydocs' ), __( 'docs', 'eazydocs' ) ] )
	->set_render_callback(
		function ( $fields, $attributes, $inner_blocks ) {
			$cols             = $fields['cols'];
			$include          = implode( ',', $fields['include'] );
			$exclude          = implode( ',', $fields['exclude'] );
			$show_docs        = ! empty( $fields['show_docs'] ) ? (int) $fields['show_docs'] : 4;
			$show_articles    = ! empty( $fields['show_article'] ) ? (int) $fields['show_article'] : 5;
			$view_more_button = ! empty( $fields['more_text'] ) ? $fields['more_text'] : __( 'View Details', 'eazydocs' );

			$shortcode = "[eazydocs col='{$cols}' include='{$include}' exclude='{$exclude}' show_docs='{$show_docs}' show_articles='{$show_articles}' more='{$view_more_button}']";
			echo do_shortcode( $shortcode );
		}
	);
