<?php
/**
 * Get post views
 * @return string
 */
function eazydocs_get_post_view() {
    global $wpdb;
    $table_name = $wpdb->prefix . 'eazydocs_view_log';
    // check if table exists or not if not then show notice to admin for one time only
    if ( $wpdb->get_var( "SHOW TABLES LIKE '$table_name'" ) != $table_name ) {
        add_action( 'admin_notices', 'eazydocs_view_log_table_not_found' );
    } else {
        $post_id = get_the_ID();
        $post_type = get_post_type($post_id);
        $current_date = date( 'Y-m-d' );
        if ($post_type == 'docs') {
            //  get meta value from wp_meta post of post_views_count
            $oldCount = (int) get_post_meta( $post_id, 'post_views_count', true );
            $count = $wpdb->get_var("SELECT SUM(count) FROM $table_name WHERE post_id = '$post_id'");
            if ($count == null || $oldCount == null) {
                $count = 0;
                $oldCount = 0;
            }
            
            $count = $count + $oldCount;
            return "$count ".esc_html__( 'views', 'eazydocs' );
        }
    }
}

/**
 * Set post view meta
 * @return void
 */
function eazydocs_set_post_view() {
    global $wpdb;
    $table_name = $wpdb->prefix . 'eazydocs_view_log';
    $post_id = get_the_ID();
    $post_type = get_post_type($post_id);
    $current_date = date( 'Y-m-d' );
    if ($post_type == 'docs') {
        $count = $wpdb->get_var("SELECT count FROM $table_name WHERE post_id = '$post_id' AND created_at = '$current_date'");
        if ($count) {
            $count++;
            $wpdb->update($table_name, array('count' => $count), array('post_id' => $post_id, 'created_at' => $current_date));
        } else {
            $wpdb->insert($table_name, array('post_id' => $post_id, 'count' => 1, 'created_at' => $current_date));
        }
    }
}

/**
 * View post views in post column
 */
add_filter( 'manage_doc_posts_columns', function () {
    $columns['post_views'] = esc_html__( 'Views', 'eazydocs' );
    return $columns;
});

add_action( 'manage_doc_posts_custom_column', function ( $column ) {
    if ( $column === 'post_views') {
        echo eazydocs_get_post_view();
        
    }
});

/**
 * Show notice to admin if eazydocs_view_log table not found
 */
function eazydocs_view_log_table_not_found() {
    ?>
    <div class="notice notice-error is-dismissible">
        <p><?php _e( 'eazydocs_view_log table not found, please deactivate and activate the plugin again', 'eazydocs' ); ?></p>
    </div>
    <?php
}