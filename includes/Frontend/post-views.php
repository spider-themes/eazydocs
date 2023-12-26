<?php
/**
 * Get post views
 * @return string
 */
 function eazydoocs_set_post_view() {

    if (is_single() && get_post_type() == 'docs') {
        global $wpdb;

        $count   = get_post_meta(get_the_ID(), 'post_views_count', true);
        $defVal  = 0;
        if ($count == '') {
            update_post_meta(get_the_ID(), 'post_views_count', 1);

            $wpdb->insert(
                $wpdb->prefix . 'eazydocs_view_log',
                array(
                    'post_id'    => get_the_ID(),
                    'count'      => 1,
                    'created_at' => date('d M, Y', current_time('timestamp', 1)),
                ),
                array(
                    '%d',
                    '%d',
                    '%s',
                )
            );

        } else {
            update_post_meta(get_the_ID(), 'post_views_count', $count + 1);

            // Get the current date in the site's timezone
            $current_date = date('d M, Y', current_time('timestamp', 1));

          
            $wpdb->insert(
                $wpdb->prefix . 'eazydocs_view_log',
                array(
                    'post_id'    => get_the_ID(),
                    'count'      => 1,
                    'created_at' => $current_date,
                ),
                array(
                    '%d',
                    '%d',
                    '%s',
                )
            );
        }
        wp_reset_postdata();
    }
 }

function eazydocs_get_post_view() {
   return get_post_meta(get_the_ID(), 'post_views_count', true);
}