<?php

/**
 * Get search count
 * @return string
 */
function eazydocs_get_search_count() {
	$count = get_post_meta( get_the_ID(), 'search_count_count', true );
	return "$count " . __( 'search count', 'eazydocs' );
}

/**
 * Set post view meta
 * @return void
 */
function eazydocs_set_search_count() {
	$key = 'search_count_count';
	$post_id = get_the_ID();
	$count = (int) get_post_meta( $post_id, $key, true );
    $count++;
	update_post_meta( $post_id, $key, $count );
}

/**
 * View post views in post column
 */
add_filter( 'manage_doc_posts_columns', function () {
    $columns['search_count'] = esc_html__( 'Search Count', 'eazydocs' );
    return $columns;
});

add_action( 'manage_doc_posts_custom_column', function ( $column ) {
    if ( $column === 'search_count') {
        echo esc_html( eazydocs_get_search_count() );
    }
});