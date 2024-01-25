<?php
/**
 * Get post views and update view count for the current user/visitor
 */
function eazydocs_set_post_view() {
    
    if ( is_single() && get_post_type() == 'docs' ) {
        global $wpdb;

        // Get the post ID
        $post_id = get_the_ID();

        // Check if the user/visitor has already viewed this post
        $viewed_posts = isset( $_COOKIE['eazydocs_viewed_posts'] ) ? json_decode( stripslashes( $_COOKIE['eazydocs_viewed_posts'] ), true ) : array();

        if ( ezd_get_opt('enable-views') == 1 && ezd_get_opt('enable-unique-views') == 1 ) {

            // Increment post views count
            if ( ! in_array( $post_id, $viewed_posts ) ) {

                $count = get_post_meta( $post_id, 'post_views_count', true );
                $count = $count ? $count : 0;
                update_post_meta( $post_id, 'post_views_count', $count + 1 );

                // Update the viewed posts cookie
                $viewed_posts[] = $post_id;
                setcookie( 'eazydocs_viewed_posts', json_encode( $viewed_posts ), time() + 3600 * 24, '/' );

                // Insert view log
                $wpdb->insert(
                    $wpdb->prefix . 'eazydocs_view_log',
                    array(
                        'post_id'    => $post_id,
                        'count'      => 1,
                        'created_at' => current_time( 'mysql', 1 ),
                    ),
                    array(
                        '%d',
                        '%d',
                        '%s',
                    )
                );

            }
        } else {
            
            $count = get_post_meta( $post_id, 'post_views_count', true );
            $count = $count ? $count : 0;
            update_post_meta( $post_id, 'post_views_count', $count + 1 );

            if ( $count == '') {

                $wpdb->insert(
                    $wpdb->prefix . 'eazydocs_view_log',
                    array(
                        'post_id'    => $post_id,
                        'count'      => 1,
                        'created_at' => current_time('mysql', 1)
                    ),
                    array(
                        '%d',
                        '%d',
                        '%s',
                    )
                );
            }           

        }
    }
}

/**
 * Get post views
 */
function eazydocs_get_post_view() {
    $get_views = get_post_meta( get_the_ID(), 'post_views_count', true );
    $views     = $get_views . esc_html__( ' views', 'eazydocs' );
    return $views;
}