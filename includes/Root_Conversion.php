<?php
if ( ! ezd_is_premium() ) {
    return;
}

/**
 * Post link filter
*/
 
add_filter( 'post_type_link', function($link, $post){
    $post_meta = $post->ID;
    if ( 'docs' == get_post_type( $post ) ) {
        $link = str_replace( '/' . $post->post_type . '/', '/', $link );
    } 
    return $link;
}, 1, 2 );


/**
 * Rewrite rule for docs
*/

add_action('init', function() {
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