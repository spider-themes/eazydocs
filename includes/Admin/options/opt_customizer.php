<?php

// Select docs page
$options      = get_option( 'eazydocs_settings' );
$doc_id       = $options['docs-slug'] ?? '';
$doc_page     = get_post_field( 'post_name', $doc_id );
$args         = array(
	'post_type'      => 'docs',
	'posts_per_page' => - 1,
	'orderby'        => 'menu_order',
	'order'          => 'asc'
);
$recent_posts = wp_get_recent_posts( $args );
$post_url     = '';
$post_count   = 0;
foreach ( $recent_posts as $recent ):
	$post_url = $recent['ID'];
	$post_count ++;
endforeach;
$docs_url = $post_count > 0 ? $post_url : $doc_id;

$archive_url = admin_url( 'customize.php?url=' ) . site_url( '/' ) . '?p=' . $doc_id . '?autofocus[panel]=docs-page&autofocus[section]=docs-archive-page';
$single_url  = admin_url( 'customize.php?url=' ) . site_url( '/' ) . '?p=' . $docs_url . '?autofocus[panel]=docs-page&autofocus[section]=docs-single-page';

CSF::createSection( $prefix, array(
	'id'     => 'design_fields',
	'title'  => esc_html__( 'Customizer', 'eazydocs' ),
	'icon'   => 'fas fa-plus-circle',
	'fields' => [
		array(
			'id'         => 'customizer_visibility',
			'type'       => 'switcher',
			'title'      => esc_html__( 'Options Visibility on Customizer', 'eazydocs' ),
			'text_on'    => esc_html__( 'Enabled', 'eazydocs' ),
			'text_off'   => esc_html__( 'Disabled', 'eazydocs' ),
			'text_width' => 100,
		),

		array(
			'type'       => 'content',
			'content'    => sprintf( '<a href="' . $archive_url . '" target="_blank" id="get_docs_archive">' . esc_html__( 'Docs Archive', 'eazydocs' )
			                         . '</a> <a href="' . $single_url . '" target="_blank" id="get_docs_single">' . esc_html__( 'Single Doc', 'eazydocs' )
			                         . '</a>' ),
			'dependency' => array(
				array( 'customizer_visibility', '==', true ),
			),
		)
	]
) );