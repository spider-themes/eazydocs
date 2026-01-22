<?php
/**
 * Failed Searches Quick View Widget
 * Shows recent failed searches with quick action to create docs
 *
 * @package EazyDocs
 */

global $wpdb;

// Get table names.
$keyword_table = $wpdb->prefix . 'eazydocs_search_keyword';
$log_table     = $wpdb->prefix . 'eazydocs_search_log';

// Check if tables exist.
$keyword_table_exists = $wpdb->get_var( $wpdb->prepare( 'SHOW TABLES LIKE %s', $keyword_table ) ) === $keyword_table;
$log_table_exists     = $wpdb->get_var( $wpdb->prepare( 'SHOW TABLES LIKE %s', $log_table ) ) === $log_table;

$failed_searches = array();
$total_failed    = 0;

if ( $keyword_table_exists && $log_table_exists ) {
	// Get resolved keyword IDs to exclude.
	$resolved_ids     = get_option( 'ezd_resolved_search_keywords', array() );
	$exclude_resolved = '';
	if ( ! empty( $resolved_ids ) ) {
		$ids_placeholder  = implode( ',', array_map( 'absint', $resolved_ids ) );
		$exclude_resolved = " AND k.id NOT IN ({$ids_placeholder})";
	}

	// Get top failed search keywords by joining both tables.
	// phpcs:ignore WordPress.DB.PreparedSQL.InterpolatedNotPrepared
	$failed_searches = $wpdb->get_results(
		"SELECT k.id AS keyword_id, k.keyword, SUM(l.not_found_count) as total_failed, MAX(l.created_at) as last_searched
		FROM {$log_table} l
		INNER JOIN {$keyword_table} k ON l.keyword_id = k.id
		WHERE l.not_found_count > 0 {$exclude_resolved}
		GROUP BY k.id, k.keyword
		ORDER BY total_failed DESC, last_searched DESC
		LIMIT 20"
	);

	// phpcs:ignore WordPress.DB.PreparedSQL.InterpolatedNotPrepared
	$total_failed = (int) $wpdb->get_var(
		"SELECT COUNT(DISTINCT k.id) 
		FROM {$log_table} l
		INNER JOIN {$keyword_table} k ON l.keyword_id = k.id
		WHERE l.not_found_count > 0 {$exclude_resolved}"
	);
}

// Set context variables for the shared component.
$context = 'dashboard';
$limit   = 4;

// Include the shared component.
include __DIR__ . '/../components/failed-searches-list.php';
