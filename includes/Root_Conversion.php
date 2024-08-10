<?php
if ( ! ezd_is_premium() ) {
    return;
}

/**
 * Post link filter
*/

add_filter( 'post_type_link', function( $post_link, $post ){
	if ( $post->post_type === 'docs' && $post->post_status === 'publish' ) {
        $ancestors = get_post_ancestors($post);
        $ancestors = array_reverse($ancestors);
        $slug = '';

        foreach ( $ancestors as $ancestor ) {
            $ancestor_post = get_post($ancestor);
            $slug .= $ancestor_post->post_name . '/';
        }

        $post_link = home_url( '/' . $slug . $post->post_name . '/' );
    }
    return $post_link;
}, 10, 2 );


/**
 * Rewrite rule for docs
*/

add_action('init', function() {
	add_rewrite_rule('^([^/]+)/([^/]+)/?$', 'index.php?docs=$matches[2]', 'top');
    add_rewrite_rule('^([^/]+)/?$', 'index.php?docs=$matches[1]', 'top');
	add_rewrite_rule('^([^/]+)/([^/]+)/?$', 'index.php?docs=$matches[2]', 'top');
	add_rewrite_rule('^([^/]+)/([^/]+)/([^/]+)/?$', 'index.php?docs=$matches[3]', 'top');
});


/**
 * Add hierarchical support for docs
*/

add_action( 'wp_head', function() {
    if ( is_singular('docs') ) {
        global $wp_post_types;

        if ( isset( $wp_post_types['docs'] ) ) {
            $wp_post_types['docs']->hierarchical =  true;

            // Re-register the post type with the modified rewrite rules
            register_post_type( 'docs', $wp_post_types['docs'] );

            // Optionally flush rewrite rules (use with caution)
            flush_rewrite_rules();
        }
    }
});