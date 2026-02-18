<?php
/**
 * Cannot access directly.
 */
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}


/**
 * Get search count
 *
 * @param int $post_id Optional. Post ID.
 * @return string
 */
function eazydocs_get_search_count( $post_id = 0 ) {
	if ( ! $post_id ) {
		$post_id = get_the_ID();
	}
	$count = get_post_meta( $post_id, 'search_count_count', true );
	return "$count " . __( 'search count', 'eazydocs' );
}

/**
 * Set post view meta
 *
 * @return void
 */
function eazydocs_set_search_count() {
	$key     = 'search_count_count';
	$post_id = get_the_ID();
	$count   = (int) get_post_meta( $post_id, $key, true );
	$count++;
	update_post_meta( $post_id, $key, $count );
}

/**
 * View post views in post column
 */
add_filter( 'manage_doc_posts_columns', function ( $columns ) {
	$columns['search_count'] = esc_html__( 'Search Count', 'eazydocs' );
	return $columns;
});

add_action( 'manage_doc_posts_custom_column', function ( $column, $post_id ) {
	if ( 'search_count' === $column ) {
		echo esc_html( eazydocs_get_search_count( $post_id ) );
	}
}, 10, 2 );
