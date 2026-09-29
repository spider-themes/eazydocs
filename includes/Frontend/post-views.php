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
    // dbDelta() is expensive (it introspects and diffs the schema). The table is
    // created on plugin activation, so here we only re-verify occasionally rather
    // than on every request that reaches this helper.
    if ( get_transient( 'ezd_view_log_table_ready' ) ) {
        return;
    }

    global $wpdb;

    $table_name = $wpdb->prefix . 'eazydocs_view_log';

    // A cheap existence probe instead of dbDelta() on a front-end request. Only
    // when the table is really missing do we (re)create the analytics schema,
    // using the same definition as activation so the two never drift apart.
    // phpcs:ignore WordPress.DB.DirectDatabaseQuery -- schema introspection.
    if ( $table_name !== $wpdb->get_var( $wpdb->prepare( 'SHOW TABLES LIKE %s', $table_name ) ) ) {
        eazydocs()->create_analytics_db_tables();
    }

    set_transient( 'ezd_view_log_table_ready', 1, WEEK_IN_SECONDS );
}

/**
 * Increment a doc's view counter and write one row to the view log.
 *
 * @param int $post_id Doc ID.
 * @return void
 */
function ezd_record_doc_view( $post_id ) {
	global $wpdb;

	$count = (int) get_post_meta( $post_id, 'post_views_count', true );
	update_post_meta( $post_id, 'post_views_count', $count + 1 );

	// @codingStandardsIgnoreLine WordPress.DB.DirectDatabaseQuery.DirectQuery
	$wpdb->insert(
		$wpdb->prefix . 'eazydocs_view_log',
		[
			'post_id'    => $post_id,
			'count'      => 1,
			'created_at' => current_time( 'mysql', 1 ),
		],
		[ '%d', '%d', '%s' ]
	);
}

add_action('wp', 'eazydocs_set_post_view');
function eazydocs_set_post_view() {

	// Only track views on single doc pages. Bailing early keeps this off the
	// critical path for every other front-end request.
	if ( is_single() && 'docs' === get_post_type() ) {

		ezd_ensure_eazydocs_view_log_table_exists();

		$post_id = (int) get_the_ID();

		// Check if views tracking is enabled, unique views are enabled, and the user has premium access.
		if ( '1' === ezd_get_opt( 'enable-views' ) && ( ezd_is_premium() ? '1' === ezd_get_opt( 'enable-unique-views' ) : false ) ) {

			// Retrieve viewed posts from cookies. The cookie is visitor-controlled:
			// anything that doesn't decode to an array (tampered, truncated) used to
			// reach in_array() and fatal the page on PHP 8.
			$viewed_posts = isset( $_COOKIE['eazydocs_viewed_posts'] ) ? json_decode( sanitize_text_field( wp_unslash( $_COOKIE['eazydocs_viewed_posts'] ) ), true ) : [];
			$viewed_posts = is_array( $viewed_posts ) ? array_map( 'intval', $viewed_posts ) : [];

			// Increment post views count if post has not been viewed
			if ( ! in_array( $post_id, $viewed_posts, true ) ) {
				ezd_record_doc_view( $post_id );

				// Add this post to the list of viewed posts and update the cookie
				// Keep the most recent 100 IDs so the cookie can't grow without bound.
				$viewed_posts[] = $post_id;
				$viewed_posts   = array_slice( $viewed_posts, -100 );
				setcookie( 'eazydocs_viewed_posts', wp_json_encode( $viewed_posts ), time() + DAY_IN_SECONDS, '/' );
			}
		} else {
			// Increment the post view count for non-unique views or if views are not enabled
			ezd_record_doc_view( $post_id );
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