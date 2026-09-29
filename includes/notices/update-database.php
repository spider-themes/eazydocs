<?php
/**
 * Cannot access directly.
 */
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

/**
 * Analytics schema version. Bump when the analytics tables or their indexes change.
 */
const EZD_ANALYTICS_DB_VERSION = 2;

/**
 * Handle the "Update Database" button from the missing-tables admin notice.
 *
 * Previously this ran for anyone who added ?eazydocs_table_create to an admin
 * URL (including admin-ajax.php for logged-out visitors), used a schema that
 * differed from the activation schema, and echoed its notice while the plugin
 * file was being included — before any headers were sent.
 */
add_action( 'admin_init', 'ezd_analytics_db_update_request' );

function ezd_analytics_db_update_request() {
	if ( ! isset( $_GET['eazydocs_table_create'] ) ) {
		return;
	}

	if ( ! current_user_can( 'manage_options' ) ) {
		return;
	}

	check_admin_referer( 'ezd_analytics_db_update' );

	eazydocs()->create_analytics_db_tables();
	ezd_analytics_db_add_indexes();
	update_option( 'ezd_analytics_db_version', EZD_ANALYTICS_DB_VERSION );

	delete_transient( 'ezd_analytics_tables_ready' );
	delete_transient( 'ezd_view_log_table_ready' );
	delete_transient( 'ezd_search_tables_check' );

	add_action( 'admin_notices', 'ezd_analytics_db_update_success_notice' );
}

/**
 * Show notice after the database was updated.
 */
function ezd_analytics_db_update_success_notice() {
	?>
	<div class="notice notice-success is-dismissible">
		<p><?php esc_html_e( 'EazyDocs database updated successfully.', 'eazydocs' ); ?></p>
	</div>
	<?php
}

/**
 * One-off upgrade for existing installs: add the indexes the analytics queries
 * need. Without them every dashboard/analytics query (views and searches by
 * date, per doc) scanned the entire log tables, which grow by one row per doc
 * view and per search.
 */
add_action( 'admin_init', 'ezd_maybe_upgrade_analytics_db' );

function ezd_maybe_upgrade_analytics_db() {
	if ( (int) get_option( 'ezd_analytics_db_version', 0 ) >= EZD_ANALYTICS_DB_VERSION ) {
		return;
	}

	// Mark first so a failure can never turn this into a per-request retry loop.
	update_option( 'ezd_analytics_db_version', EZD_ANALYTICS_DB_VERSION );

	ezd_analytics_db_add_indexes();
}

/**
 * Add any missing indexes to the analytics tables.
 *
 * Checks the existing indexes by their leading column so an equivalent index
 * (e.g. the one InnoDB creates for the keyword_id foreign key) is not duplicated.
 *
 * @return void
 */
function ezd_analytics_db_add_indexes() {
	global $wpdb;

	$indexes = [
		$wpdb->prefix . 'eazydocs_view_log'       => [
			'post_created' => '(post_id, created_at)',
			'created_at'   => '(created_at)',
		],
		$wpdb->prefix . 'eazydocs_search_log'     => [
			'keyword_id' => '(keyword_id)',
			'created_at' => '(created_at)',
		],
		$wpdb->prefix . 'eazydocs_search_keyword' => [
			'keyword' => '(keyword(191))',
		],
	];

	foreach ( $indexes as $table => $table_indexes ) {
		// phpcs:ignore WordPress.DB.DirectDatabaseQuery -- schema introspection.
		if ( $table !== $wpdb->get_var( $wpdb->prepare( 'SHOW TABLES LIKE %s', $table ) ) ) {
			continue;
		}

		// phpcs:ignore WordPress.DB.DirectDatabaseQuery, WordPress.DB.PreparedSQL.InterpolatedNotPrepared -- table name is a trusted prefix constant.
		$existing = $wpdb->get_results( "SHOW INDEX FROM {$table}" );

		$leading_columns = [];
		$key_names       = [];
		foreach ( (array) $existing as $row ) {
			$key_names[ $row->Key_name ] = true;
			if ( 1 === (int) $row->Seq_in_index ) {
				$leading_columns[ $row->Column_name ] = true;
			}
		}

		// Older builds created a UNIQUE KEY `id` on top of PRIMARY KEY (id): a
		// duplicate index every insert had to maintain. Drop it only when the
		// primary key is there to keep the auto-increment column indexed.
		if ( isset( $key_names['PRIMARY'], $key_names['id'] ) ) {
			// phpcs:ignore WordPress.DB.DirectDatabaseQuery, WordPress.DB.PreparedSQL.InterpolatedNotPrepared -- trusted identifiers.
			$wpdb->query( "ALTER TABLE {$table} DROP INDEX id" );
		}

		foreach ( $table_indexes as $name => $columns ) {
			$leading = strtok( trim( $columns, '()' ), ',(' );

			if ( isset( $key_names[ $name ] ) || isset( $leading_columns[ $leading ] ) ) {
				continue;
			}

			// phpcs:ignore WordPress.DB.DirectDatabaseQuery, WordPress.DB.PreparedSQL.InterpolatedNotPrepared -- identifiers are hard-coded above.
			$wpdb->query( "ALTER TABLE {$table} ADD INDEX {$name} {$columns}" );
		}
	}
}
