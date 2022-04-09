<?php
/**
 * Get post views
 * @return string
 */
function eazydocs_get_post_view() {
	$count = get_post_meta( get_the_ID(), 'post_views_count', true );
	return "$count ".esc_html__('views', 'docly');
}

/**
 * Set post view meta
 * @return void
 */
function eazydocs_set_post_view() {
	$key = 'post_views_count';
	$post_id = get_the_ID();
	$count = (int) get_post_meta( $post_id, $key, true );
    $count++;
	update_post_meta( $post_id, $key, $count );
}

/**
 * View post views in post column
 */
add_filter( 'manage_doc_posts_columns', function () {
    $columns['post_views'] = 'Views';
    return $columns;
});

add_action( 'manage_doc_posts_custom_column', function ( $column ) {
    if ( $column === 'post_views') {
        echo eazydocs_get_post_view();
    }
});