<?php
// add button for create table
if ( isset( $_GET['eazydocs_table_create'] ) ) {

	add_action( 'admin_init', "ezd_analytics_db_update_success_notice" );

	/**
	 * Show notice after database created
	 */
	function ezd_analytics_db_update_success_notice() {
		global $wpdb;
		$charset_collate = $wpdb->get_charset_collate();
		$search_keyword  = $wpdb->prefix . 'eazydocs_search_keyword';
		$search_logs     = $wpdb->prefix . 'eazydocs_search_log';
		$view_logs       = $wpdb->prefix . 'eazydocs_view_log';

		$sql = "CREATE TABLE $search_keyword (
        id bigint(9) NOT NULL AUTO_INCREMENT,
        keyword varchar(255) NOT NULL,
        UNIQUE KEY id (id)
        ) $charset_collate;";

		$sql2 = "CREATE TABLE $search_logs (
        id bigint(9) NOT NULL AUTO_INCREMENT,
        keyword_id bigint(255) NOT NULL references $search_keyword(id), 
        count mediumint(255) NOT NULL,
        not_found_count mediumint(9) NOT NULL,
        created_at datetime NOT NULL,
        UNIQUE KEY id (id)
        ) $charset_collate;";

		$sql3 = "CREATE TABLE $view_logs (
        id bigint(9) NOT NULL AUTO_INCREMENT,
        post_id bigint(255) NOT NULL,
        count mediumint(255) NOT NULL,
        created_at datetime NOT NULL,
        UNIQUE KEY id (id)
        ) $charset_collate;";

		require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );
		dbDelta( $sql );
		dbDelta( $sql2 );
		dbDelta( $sql3 );

		// if table created then database_not_found notice will be removed and return true
		if ( $sql && $sql2 && $sql3 ) {
			remove_action( 'admin_notices', 'database_not_found' );
		}

		$url = admin_url( 'admin.php?page=ezd-analytics' );
		wp_safe_redirect( $url );
	}

	?>
    <div class="notice notice-success is-dismissible">
        <p> <?php esc_html_e( 'EazyDocs database updated successfully.', 'eazydocs' ); ?> </p>
    </div>
    <!-- after eazydocs_table_create done then remove database_not_found notice in php -->
    <script>
        jQuery(document).ready(function ($) {
            $('.eazydocs_table_error').remove();
        });
    </script>
	<?php
}