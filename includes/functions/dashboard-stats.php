<?php
/**
 * Cannot access directly.
 */
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Prime the post meta cache with backward compatibility.
 *
 * @param array|int|WP_Post $post_ids Post IDs or post objects.
 * @return void
 */
function ezd_update_post_meta_cache( $post_ids ) {
	if ( empty( $post_ids ) ) {
		return;
	}

	if ( is_object( $post_ids ) && isset( $post_ids->ID ) ) {
		$post_ids = [ $post_ids->ID ];
	} elseif ( is_array( $post_ids ) ) {
		$first = reset( $post_ids );
		if ( is_object( $first ) && isset( $first->ID ) ) {
			$post_ids = wp_list_pluck( $post_ids, 'ID' );
		}
	}

	if ( empty( $post_ids ) ) {
		return;
	}

	if ( function_exists( 'update_post_meta_cache' ) ) {
		update_post_meta_cache( $post_ids );
		return;
	}

	if ( function_exists( 'update_postmeta_cache' ) ) {
		update_postmeta_cache( $post_ids );
		return;
	}

	if ( function_exists( 'update_meta_cache' ) ) {
		update_meta_cache( 'post', $post_ids );
	}
}

/**
 * Prime post meta for doc lists returned by get_pages().
 *
 * Core get_pages() queries with update_post_meta_cache disabled, so every later
 * meta read — our sidebar walker (thumbnail, secondary title, visibility) and
 * any third-party 'get_pages' filter — costs one query per doc. Loading the
 * meta for the whole list up front turns that N+1 into a single query.
 *
 * @param WP_Post[] $pages       Pages returned by get_pages().
 * @param array     $parsed_args get_pages() arguments.
 * @return WP_Post[]
 */
function ezd_prime_doc_pages_meta( $pages, $parsed_args = [] ) {
	$post_type = $parsed_args['post_type'] ?? '';

	if ( ! empty( $pages ) && in_array( $post_type, [ 'docs', 'onepage-docs', 'api_docs' ], true ) ) {
		ezd_update_post_meta_cache( wp_list_pluck( $pages, 'ID' ) );
	}

	return $pages;
}
add_filter( 'get_pages', 'ezd_prime_doc_pages_meta', -999, 2 );

/**
 * Format a number into a compact, human-readable string (e.g. 1.2k, 3.4M).
 *
 * Used across the dashboard so view counts, vote totals and search figures
 * are displayed consistently.
 *
 * @param int|float $number   The raw number.
 * @param int       $decimals Maximum decimals for the shortened value.
 * @return string
 */
function ezd_format_number( $number, $decimals = 1 ) {
	$number = (float) $number;

	if ( $number >= 1000000 ) {
		return rtrim( rtrim( number_format( $number / 1000000, $decimals ), '0' ), '.' ) . 'M';
	}

	if ( $number >= 1000 ) {
		return rtrim( rtrim( number_format( $number / 1000, $decimals ), '0' ), '.' ) . 'k';
	}

	return (string) (int) $number;
}

/**
 * Calculate the percentage change between two values.
 *
 * @param int|float $old Previous period value.
 * @param int|float $new Current period value.
 * @return int|null Whole-number percentage change, or null when no baseline exists.
 */
function ezd_percent_change( $old, $new ) {
	$old = (float) $old;
	$new = (float) $new;

	if ( $old <= 0 ) {
		// No baseline: report growth only when something new appeared.
		return $new > 0 ? 100 : null;
	}

	return (int) round( ( ( $new - $old ) / $old ) * 100 );
}

/**
 * Build correctly bucketed daily time-series for the dashboard chart.
 *
 * Returns three honest, per-day series sourced from the logging tables:
 * - views    : SUM(count) from the view log (view increments per day).
 * - searches : COUNT(*) of search events per day.
 * - failed   : SUM(not_found_count) of searches that returned nothing.
 *
 * Feedback is intentionally omitted: the schema only stores cumulative vote
 * totals and the last vote time, so a truthful per-day feedback line is not
 * possible. Arrays are ordered oldest -> newest and aligned with `labels`.
 *
 * @param int $days Number of days to include (e.g. 7 or 30).
 * @return array{labels:string[],views:int[],searches:int[],failed:int[]}
 */
function ezd_dashboard_daily_series( $days = 7 ) {
	global $wpdb;

	$days = max( 1, (int) $days );

	$view_table   = $wpdb->prefix . 'eazydocs_view_log';
	$search_table = $wpdb->prefix . 'eazydocs_search_log';

	$labels    = [];
	$views     = [];
	$searches  = [];
	$failed    = [];
	$positions = []; // 'Y-m-d' => index in the series arrays.

	$now = current_time( 'timestamp' );

	for ( $i = $days - 1; $i >= 0; $i-- ) {
		$ts                 = $now - ( $i * DAY_IN_SECONDS );
		$key                = wp_date( 'Y-m-d', $ts );
		$labels[]           = wp_date( 'd M', $ts );
		$views[]            = 0;
		$searches[]         = 0;
		$failed[]           = 0;
		$positions[ $key ]  = count( $labels ) - 1;
	}

	// Widen the lower bound by a day so timezone skew between the GMT view log
	// and the local search log never drops boundary rows.
	$since = gmdate( 'Y-m-d 00:00:00', $now - ( $days * DAY_IN_SECONDS ) );

	// Views per day. phpcs:ignore WordPress.DB.PreparedSQL.InterpolatedNotPrepared -- table name only.
	$view_rows = $wpdb->get_results(
		$wpdb->prepare(
			"SELECT DATE(created_at) AS d, SUM(count) AS total FROM {$view_table} WHERE created_at >= %s GROUP BY DATE(created_at)",
			$since
		)
	);
	foreach ( (array) $view_rows as $row ) {
		if ( isset( $positions[ $row->d ] ) ) {
			$views[ $positions[ $row->d ] ] = (int) $row->total;
		}
	}

	// Searches and failed searches per day. phpcs:ignore WordPress.DB.PreparedSQL.InterpolatedNotPrepared -- table name only.
	$search_rows = $wpdb->get_results(
		$wpdb->prepare(
			"SELECT DATE(created_at) AS d, COUNT(*) AS total, SUM(not_found_count) AS failed FROM {$search_table} WHERE created_at >= %s GROUP BY DATE(created_at)",
			$since
		)
	);
	foreach ( (array) $search_rows as $row ) {
		if ( isset( $positions[ $row->d ] ) ) {
			$searches[ $positions[ $row->d ] ] = (int) $row->total;
			$failed[ $positions[ $row->d ] ]   = (int) $row->failed;
		}
	}

	return [
		'labels'   => $labels,
		'views'    => $views,
		'searches' => $searches,
		'failed'   => $failed,
	];
}

/**
 * Compute the dashboard KPI cards with 7-day trend deltas.
 *
 * @return array
 */
function ezd_compute_dashboard_kpis() {
	global $wpdb;

	$now       = current_time( 'timestamp' );
	$boundary7 = gmdate( 'Y-m-d H:i:s', time() - 7 * DAY_IN_SECONDS );
	$boundary14 = gmdate( 'Y-m-d H:i:s', time() - 14 * DAY_IN_SECONDS );

	$view_table   = $wpdb->prefix . 'eazydocs_view_log';
	$search_table = $wpdb->prefix . 'eazydocs_search_log';

	// Total published docs + how many are new this week.
	$total_docs = (int) $wpdb->get_var( "SELECT COUNT(*) FROM {$wpdb->posts} WHERE post_type = 'docs' AND post_status = 'publish'" );
	$new_docs   = (int) $wpdb->get_var(
		$wpdb->prepare(
			"SELECT COUNT(*) FROM {$wpdb->posts} WHERE post_type = 'docs' AND post_status = 'publish' AND post_date >= %s",
			wp_date( 'Y-m-d H:i:s', $now - 7 * DAY_IN_SECONDS )
		)
	);

	// Total views (all-time) and the week-over-week trend from the view log.
	$total_views = (int) $wpdb->get_var( "SELECT SUM(meta_value + 0) FROM {$wpdb->postmeta} WHERE meta_key = 'post_views_count'" );
	// phpcs:ignore WordPress.DB.PreparedSQL.InterpolatedNotPrepared -- table name only.
	$views_7    = (int) $wpdb->get_var( $wpdb->prepare( "SELECT SUM(count) FROM {$view_table} WHERE created_at >= %s", $boundary7 ) );
	// phpcs:ignore WordPress.DB.PreparedSQL.InterpolatedNotPrepared -- table name only.
	$views_prev = (int) $wpdb->get_var( $wpdb->prepare( "SELECT SUM(count) FROM {$view_table} WHERE created_at >= %s AND created_at < %s", $boundary14, $boundary7 ) );

	// Helpful rate from true cumulative vote totals.
	$positive = (int) $wpdb->get_var( "SELECT SUM(meta_value + 0) FROM {$wpdb->postmeta} WHERE meta_key = 'positive'" );
	$negative = (int) $wpdb->get_var( "SELECT SUM(meta_value + 0) FROM {$wpdb->postmeta} WHERE meta_key = 'negative'" );
	$total_votes  = $positive + $negative;
	$helpful_rate = $total_votes > 0 ? (int) round( ( $positive / $total_votes ) * 100 ) : 0;

	// Failed searches (all-time) and the week-over-week trend.
	$failed_total = (int) $wpdb->get_var( "SELECT SUM(not_found_count) FROM {$search_table}" );
	// phpcs:ignore WordPress.DB.PreparedSQL.InterpolatedNotPrepared -- table name only.
	$failed_7    = (int) $wpdb->get_var( $wpdb->prepare( "SELECT SUM(not_found_count) FROM {$search_table} WHERE created_at >= %s", $boundary7 ) );
	// phpcs:ignore WordPress.DB.PreparedSQL.InterpolatedNotPrepared -- table name only.
	$failed_prev = (int) $wpdb->get_var( $wpdb->prepare( "SELECT SUM(not_found_count) FROM {$search_table} WHERE created_at >= %s AND created_at < %s", $boundary14, $boundary7 ) );

	return [
		'total_docs'    => $total_docs,
		'new_docs'      => $new_docs,
		'total_views'   => $total_views,
		'views_delta'   => ezd_percent_change( $views_prev, $views_7 ),
		'helpful_rate'  => $helpful_rate,
		'total_votes'   => $total_votes,
		'failed_total'  => $failed_total,
		'failed_delta'  => ezd_percent_change( $failed_prev, $failed_7 ),
	];
}

/**
 * Get the full dashboard data payload (KPIs + chart series) with caching.
 *
 * One transient holds everything so a dashboard load fires the aggregate
 * queries once and reuses them for five minutes. The cache is flushed when
 * docs change or feedback is recorded via ezd_flush_dashboard_cache().
 *
 * @param bool $force Bypass the cache and recompute.
 * @return array
 */
function ezd_get_dashboard_data( $force = false ) {
	$cache_key = 'ezd_dashboard_data_v1';

	if ( ! $force ) {
		$cached = get_transient( $cache_key );
		if ( is_array( $cached ) ) {
			return $cached;
		}
	}

	$data = [
		'kpis'  => ezd_compute_dashboard_kpis(),
		'week'  => ezd_dashboard_daily_series( 7 ),
		'month' => ezd_dashboard_daily_series( 30 ),
	];

	set_transient( $cache_key, $data, 5 * MINUTE_IN_SECONDS );

	return $data;
}

/**
 * Invalidate the cached dashboard data payload.
 *
 * @return void
 */
function ezd_flush_dashboard_cache() {
	delete_transient( 'ezd_dashboard_data_v1' );
	delete_transient( 'ezd_doc_health_v1' );
}
add_action( 'save_post_docs', 'ezd_flush_dashboard_cache' );
add_action( 'deleted_post', 'ezd_flush_dashboard_cache' );
add_action( 'trashed_post', 'ezd_flush_dashboard_cache' );
