<?php
/**
 * Get post views and update view count for the current user/visitor
 */

add_action('wp', 'eazydocs_set_post_view');
function eazydocs_set_post_view() {
    
    if ( is_single() && get_post_type() == 'docs' ) {

        global $wpdb;
        $post_id = get_the_ID();
        
        if ( ezd_get_opt('enable-views') == 1 && ezd_get_opt('enable-unique-views') == 1 && ezd_is_premium() ) {
            
            $viewed_posts = isset($_COOKIE['eazydocs_viewed_posts']) ? json_decode(stripslashes($_COOKIE['eazydocs_viewed_posts']), true) : [];
        
            // Increment post views count
            if ( ! in_array( $post_id, $viewed_posts ) ) {

                $count = get_post_meta( $post_id, 'post_views_count', true );
                $count = $count ? $count : 0;
                update_post_meta( $post_id, 'post_views_count', $count + 1 );

                $viewed_posts[] = $post_id;

                setcookie('eazydocs_viewed_posts', json_encode($viewed_posts), time() + 3600 * 24, '/');
                
                // Insert view log
			    // @codingStandardsIgnoreLine WordPress.DB.DirectDatabaseQuery.DirectQuery
                $wpdb->insert(
                    $wpdb->prefix . 'eazydocs_view_log',
                    array(
                        'post_id'    => $post_id,
                        'count'      => 1,
                        'created_at' => current_time( 'mysql', 1 )
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
            
			// @codingStandardsIgnoreLine WordPress.DB.DirectDatabaseQuery.DirectQuery
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

/**
 * Get post views
 */
function eazydocs_get_post_view() {
    $get_views = get_post_meta( get_the_ID(), 'post_views_count', true );
    $views     = $get_views . esc_html__( ' views', 'eazydocs' );
    return $views;
}