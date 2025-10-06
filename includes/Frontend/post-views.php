<?php
/**
 * Cannot access directly.
 */
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

/**
 * Get post views and update view count for the current user/visitor
 */
function ezd_ensure_eazydocs_view_log_table_exists() {
    global $wpdb;

    $table_name = $wpdb->prefix . 'eazydocs_view_log';
    
    // Use dbDelta without manually checking the table
    $charset_collate = $wpdb->get_charset_collate();

    $sql = "CREATE TABLE {$table_name} (
        id BIGINT(20) NOT NULL AUTO_INCREMENT,
        post_id BIGINT(20) UNSIGNED NOT NULL,
        count MEDIUMINT(8) UNSIGNED NOT NULL,
        created_at DATETIME NOT NULL,
        UNIQUE KEY id (id)
    ) {$charset_collate};";

    require_once ABSPATH . 'wp-admin/includes/upgrade.php';
    dbDelta( $sql );
}

add_action('wp', 'eazydocs_set_post_view');
function eazydocs_set_post_view() {

	ezd_ensure_eazydocs_view_log_table_exists();

	if ( is_single() && get_post_type() === 'docs' ) {

		global $wpdb;
		$post_id = get_the_ID();

		// Check if views tracking is enabled, unique views are enabled, and the user has premium access.
		if ( ezd_get_opt( 'enable-views' ) === '1' && ezd_get_opt( 'enable-unique-views' ) === '1' && ezd_is_premium() ) {

			// Retrieve viewed posts from cookies
			$viewed_posts = isset( $_COOKIE['eazydocs_viewed_posts'] ) ? json_decode( sanitize_text_field( wp_unslash( $_COOKIE['eazydocs_viewed_posts'] ) ), true ) : array();

			// Increment post views count if post has not been viewed
			if ( ! in_array( $post_id, $viewed_posts, true ) ) {

				// Update the post's view count meta
				$count = get_post_meta( $post_id, 'post_views_count', true );
				$count = $count ? $count : 0;
				update_post_meta( $post_id, 'post_views_count', $count + 1 );

				// Add this post to the list of viewed posts and update the cookie
				$viewed_posts[] = $post_id;
				setcookie( 'eazydocs_viewed_posts', json_encode( $viewed_posts ), time() + 3600 * 24, '/' );

				// Insert view log into the eazydocs_view_log table
				// @codingStandardsIgnoreLine WordPress.DB.DirectDatabaseQuery.DirectQuery
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
			// Increment the post view count for non-unique views or if views are not enabled
			$count = get_post_meta( $post_id, 'post_views_count', true );
			$count = $count ? $count : 0;
			update_post_meta( $post_id, 'post_views_count', $count + 1 );

			// Insert view log into the eazydocs_view_log table
			// @codingStandardsIgnoreLine WordPress.DB.DirectDatabaseQuery.DirectQuery
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
	}

}

/**
 * Get post views
 */
function eazydocs_get_post_view() {
    $get_views = get_post_meta( get_the_ID(), 'post_views_count', true );
    $views     = $get_views . ' ' . esc_html__( 'views', 'eazydocs' );
    return $views;
}