<?php
/**
 * Get post views
 * @return string
 */
 
function eazydocs_set_post_view() {
 
    global $post;
    $post_id    = $post->ID ?? '';
    $post_views = get_post_meta($post_id, 'post_views_count', true);
    
    if ($post_views == '') {
        $post_views = 0;
    }
    $post_views++;
    
    update_post_meta($post_id, 'post_views_count', $post_views);
    
}
add_action('wp_head', 'eazydocs_set_post_view');

function eazydocs_get_post_view() {
    $get_views  = get_post_meta( get_the_ID(), 'post_views_count', true );
    $views      = $get_views . esc_html__( ' views', 'eazydocs' );
    return $views;
}

function eazydocs_add_views_column($columns) {
    $columns['post_views'] = esc_html__('Views', 'eazydocs');
    return $columns;
}
add_filter('manage_docs_posts_columns', 'eazydocs_add_views_column');

function eazydocs_display_views($column_name) {
    $get_views      = '';
    if ( $column_name == 'post_views' ) {
        $get_views  = get_post_meta( get_the_ID(), 'post_views_count', true );
        $get_views  = ! empty ( $get_views )  ? $get_views : 0;
        echo $get_views . esc_html__( ' views', 'eazydocs' );
    }
}
add_action('manage_docs_posts_custom_column', 'eazydocs_display_views' );