<?php
/**
 * Cannot access directly.
 */
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Sanitize nested objects for use in the admin panel
 *
 * @param array $items Array of items to sanitize.
 * @return array Sanitized array of items.
 */
function ezd_sanitize_nested_objects( $items ) {
	$sanitized = [];

	foreach ( $items as $item ) {
		if ( ! isset( $item->id ) ) {
			continue;
		}

		$sanitized_item = (object) [
			'id' => intval( $item->id )
		];

		if ( isset( $item->children ) && is_array( $item->children ) ) {
			$sanitized_item->children = ezd_sanitize_nested_objects( $item->children );
		}

		$sanitized[] = $sanitized_item;
	}

	return $sanitized;
}


/**
 * Most-searched keywords for the search banner's "popular keywords" list.
 *
 * Replaces an uncached, unbounded `GROUP BY keyword` over the whole keyword log
 * (one row per search ever made) that ran on every page showing the banner,
 * and whose output had no cap when the limit setting was empty.
 *
 * @param int  $limit             Max keywords to return.
 * @param bool $exclude_not_found Skip keywords that have never returned a result.
 * @return string[]
 */
function ezd_get_popular_search_keywords( $limit = 6, $exclude_not_found = false ) {
	$limit     = max( 1, min( 50, (int) $limit ?: 6 ) );
	$cache_key = 'ezd_popular_kw_' . $limit . ( $exclude_not_found ? '_found' : '' );
	$keywords  = get_transient( $cache_key );

	if ( is_array( $keywords ) ) {
		return $keywords;
	}

	global $wpdb;
	$keyword_table = $wpdb->prefix . 'eazydocs_search_keyword';
	$log_table     = $wpdb->prefix . 'eazydocs_search_log';

	if ( $exclude_not_found ) {
		// phpcs:ignore WordPress.DB.DirectDatabaseQuery, WordPress.DB.PreparedSQL.InterpolatedNotPrepared -- table names are prefixed constants.
		$keywords = $wpdb->get_col( $wpdb->prepare(
			"SELECT k.keyword FROM {$keyword_table} k
			 INNER JOIN {$log_table} l ON l.keyword_id = k.id
			 GROUP BY k.keyword
			 HAVING SUM(l.not_found_count) < COUNT(*)
			 ORDER BY COUNT(*) DESC
			 LIMIT %d",
			$limit
		) );
	} else {
		// phpcs:ignore WordPress.DB.DirectDatabaseQuery, WordPress.DB.PreparedSQL.InterpolatedNotPrepared -- table name is a prefixed constant.
		$keywords = $wpdb->get_col( $wpdb->prepare(
			"SELECT keyword FROM {$keyword_table} GROUP BY keyword ORDER BY COUNT(*) DESC LIMIT %d",
			$limit
		) );
	}

	$keywords = array_values( array_filter( array_map( 'strval', (array) $keywords ), 'strlen' ) );
	set_transient( $cache_key, $keywords, HOUR_IN_SECONDS );

	return $keywords;
}

/**
 * Active language codes from Polylang or WPML (empty array if neither is active).
 *
 * @return string[]
 */
function ezd_active_language_codes() {
	static $codes = null;
	if ( null !== $codes ) {
		return $codes;
	}

	$codes = [];
	if ( function_exists( 'pll_languages_list' ) ) {
		$codes = (array) pll_languages_list( [ 'fields' => 'slug' ] );
	}
	if ( empty( $codes ) ) {
		$wpml = apply_filters( 'wpml_active_languages', null );
		if ( is_array( $wpml ) ) {
			$codes = array_keys( $wpml );
		}
	}

	$codes = array_values( array_filter( array_map( 'sanitize_key', $codes ) ) );
	return $codes;
}

/**
 * Whether a known multilingual plugin (WPML or Polylang) is active with >1 language.
 *
 * @return bool
 */
function ezd_is_multilingual() {
	return count( ezd_active_language_codes() ) > 1;
}

/**
 * Current request language code, or '' when the site is not multilingual.
 *
 * @return string
 */
function ezd_current_language() {
	if ( function_exists( 'pll_current_language' ) ) {
		$lang = pll_current_language( 'slug' );
		if ( $lang ) {
			return sanitize_key( $lang );
		}
	}
	$wpml = apply_filters( 'wpml_current_language', null );
	return $wpml ? sanitize_key( $wpml ) : '';
}

/**
 * Get the IDs of docs built with Elementor, cached.
 *
 * This list is only consumed by the AJAX doc loader on single doc pages, yet the
 * underlying unbounded meta query previously ran on every front-end page load via
 * the asset localizer. Caching it for 12 hours (flushed on doc save/delete) keeps
 * large doc libraries from re-running the query on unrelated requests.
 *
 * @return int[] Doc post IDs edited with Elementor.
 */
function ezd_get_elementor_doc_ids() {
	if ( ! class_exists( '\Elementor\Plugin' ) ) {
		return [];
	}

	$cache_key = 'ezd_elementor_doc_ids';
	$doc_ids   = get_transient( $cache_key );

	if ( false === $doc_ids ) {
		$doc_ids = get_posts(
			[
				'post_type'   => 'docs',
				'post_status' => 'publish',
				'numberposts' => -1,
				'fields'      => 'ids',
				'meta_key'    => '_elementor_edit_mode',
				'meta_value'  => 'builder',
			]
		);

		$doc_ids = array_map( 'absint', (array) $doc_ids );
		set_transient( $cache_key, $doc_ids, 12 * HOUR_IN_SECONDS );
	}

	return $doc_ids;
}

/**
 * Flush the cached Elementor doc IDs when a doc is saved or deleted.
 *
 * @param int $post_id Post ID.
 */
function ezd_flush_elementor_doc_ids_cache( $post_id ) {
	if ( 'docs' === get_post_type( $post_id ) ) {
		delete_transient( 'ezd_elementor_doc_ids' );
	}
}
add_action( 'save_post', 'ezd_flush_elementor_doc_ids_cache' );
add_action( 'delete_post', 'ezd_flush_elementor_doc_ids_cache' );

/**
 * Raise memory and execution limits for the one-page doc render / "print to PDF".
 *
 * The one-page layout renders an entire doc tree in a single synchronous request,
 * and with Elementor active it renders every node's builder output. On large trees
 * this can exhaust PHP's default memory/time limits and return a 500 mid-render.
 * Both limits are filterable, and wp_raise_memory_limit() never lowers a value that
 * is already higher.
 *
 * @return void
 */
function ezd_raise_onepage_render_limits() {
	// Respects WP_MAX_MEMORY_LIMIT and the {$context}_memory_limit filter.
	wp_raise_memory_limit( 'ezd_onepage' );

	// Give a large export room to finish instead of timing out part-way through.
	$time_limit = (int) apply_filters( 'ezd_onepage_time_limit', 300 );
	if ( $time_limit > 0 && function_exists( 'set_time_limit' ) ) {
		@set_time_limit( $time_limit );
	}
}

/**
 * Render a single doc's body for the one-page view, cached in the object cache.
 *
 * Centralises the rendering logic that was previously duplicated at every depth of
 * the one-page templates. Elementor's get_builder_content() is expensive, so the
 * rendered markup is stored in the object cache; on hosts with a persistent backend
 * (Redis/Memcached) it is reused across requests. The entry is flushed on doc save.
 *
 * The cache is only used for logged-out visitors. the_content can render
 * user-specific markup (login-gated shortcodes, draft previews, per-user nonces),
 * so caching a single rendering by post ID alone could leak it across users on a
 * persistent backend; logged-in requests always render fresh.
 *
 * The caller is responsible for escaping the return value (e.g. with wp_kses_post()),
 * preserving the previous template behaviour.
 *
 * @param int|WP_Post $doc Post ID or object.
 * @return string Rendered doc body HTML.
 */
function ezd_get_onepage_doc_content( $doc ) {
	$post = get_post( $doc );
	if ( ! $post ) {
		return '';
	}

	$use_cache = ! is_user_logged_in();

	if ( $use_cache ) {
		$cached = wp_cache_get( $post->ID, 'ezd_onepage_content' );
		if ( false !== $cached ) {
			return $cached;
		}
	}

	if ( did_action( 'elementor/loaded' ) ) {
		$builder = \Elementor\Plugin::instance()->frontend->get_builder_content( $post->ID );
		$content = ! empty( $builder ) ? $builder : apply_filters( 'the_content', $post->post_content );
	} else {
		$content = apply_filters( 'the_content', $post->post_content );
	}

	if ( $use_cache ) {
		wp_cache_set( $post->ID, $content, 'ezd_onepage_content' );
	}

	return $content;
}

/**
 * Flush the cached one-page render for a doc when it changes.
 *
 * @param int $post_id Post ID.
 */
function ezd_flush_onepage_doc_content_cache( $post_id ) {
	if ( in_array( get_post_type( $post_id ), [ 'docs', 'onepage-docs' ], true ) ) {
		wp_cache_delete( $post_id, 'ezd_onepage_content' );
	}
}
add_action( 'save_post', 'ezd_flush_onepage_doc_content_cache' );
add_action( 'delete_post', 'ezd_flush_onepage_doc_content_cache' );

/**
 * Aggregate stat metrics for the One-Page banner.
 *
 * Summarises a parent doc and all of its descendants: total doc count, most
 * recent update time, distinct author count, and estimated reading time. All
 * values come from a single batched query (after the descendant-ID lookup) and
 * the result is cached in a transient that is flushed whenever a doc is saved.
 *
 * @param int $parent_id Parent doc ID.
 * @return array{count:int,modified:int,authors:int,author_ids:int[],reading_time:int} Metric set.
 */
function ezd_get_onepage_banner_meta( $parent_id ) {
	$parent_id = absint( $parent_id );
	$empty     = [ 'count' => 0, 'modified' => 0, 'authors' => 0, 'author_ids' => [], 'reading_time' => 0 ];

	if ( ! $parent_id ) {
		return $empty;
	}

	// The version suffix lets a structure change (e.g. new keys) invalidate any
	// data cached by an older build instead of returning an incomplete shape.
	$cache_key = 'ezd_onepage_banner_meta_v2_' . $parent_id;
	$cached    = get_transient( $cache_key );
	if ( is_array( $cached ) && isset( $cached['author_ids'] ) ) {
		return $cached;
	}

	// Parent + every descendant make up the metric set.
	$ids   = ezd_get_all_descendant_ids( $parent_id );
	$ids[] = $parent_id;
	$ids   = array_values( array_unique( array_map( 'absint', $ids ) ) );

	if ( empty( $ids ) ) {
		return $empty;
	}

	global $wpdb;

	// One batched query for everything the metrics need — no per-doc lookups.
	$placeholders = implode( ', ', array_fill( 0, count( $ids ), '%d' ) );
	$rows         = $wpdb->get_results( $wpdb->prepare(
		"SELECT post_author, post_modified_gmt, post_content
		 FROM {$wpdb->posts}
		 WHERE ID IN ($placeholders)",
		...$ids
	) );

	$authors     = [];
	$latest      = 0;
	$total_words = 0;
	foreach ( $rows as $row ) {
		$authors[ (int) $row->post_author ] = true;
		$modified                           = (int) mysql2date( 'U', $row->post_modified_gmt );
		if ( $modified > $latest ) {
			$latest = $modified;
		}
		$total_words += str_word_count( wp_strip_all_tags( (string) $row->post_content ) );
	}

	$wpm        = max( 1, absint( ezd_get_opt( 'reading_time_wpm', 200 ) ) );
	$author_ids = array_map( 'intval', array_keys( $authors ) );
	$meta       = [
		// Exclude the parent itself from the "docs" count.
		'count'        => max( 0, count( $ids ) - 1 ),
		'modified'     => $latest,
		'authors'      => count( $author_ids ),
		'author_ids'   => $author_ids,
		'reading_time' => max( 1, (int) ceil( $total_words / $wpm ) ),
	];

	set_transient( $cache_key, $meta, 12 * HOUR_IN_SECONDS );

	return $meta;
}

/**
 * Flush every cached One-Page banner metric set when a doc changes.
 *
 * Metrics are keyed by ancestor, so a child edit must invalidate its parent's
 * cache too; the simplest correct approach is to clear them all on any doc save.
 *
 * @param int $post_id Post ID.
 */
function ezd_flush_onepage_banner_meta_cache( $post_id ) {
	if ( ! in_array( get_post_type( $post_id ), [ 'docs', 'onepage-docs' ], true ) ) {
		return;
	}

	global $wpdb;
	$wpdb->query(
		"DELETE FROM {$wpdb->options}
		 WHERE option_name LIKE '\_transient\_ezd\_onepage\_banner\_meta\_%'
		    OR option_name LIKE '\_transient\_timeout\_ezd\_onepage\_banner\_meta\_%'"
	);
}
add_action( 'save_post', 'ezd_flush_onepage_banner_meta_cache' );
add_action( 'delete_post', 'ezd_flush_onepage_banner_meta_cache' );

/**
 * Get docs ranked by feedback votes via a single aggregated query.
 *
 * Replaces the previous approach of loading every doc into memory and summing
 * 'positive'/'negative' meta per post. The votes are aggregated in one GROUP BY
 * query and only docs that actually have votes are returned, ordered by the
 * requested vote type. This mirrors the direct-SQL aggregation already used by
 * the dashboard health widget.
 *
 * @param string $order_by 'positive' or 'negative'. Sort key (DESC) and HAVING filter.
 * @param int    $limit    Max rows to return. 0 for no limit.
 * @return array[] Each row: [ 'post_id' => int, 'positive' => int, 'negative' => int ].
 */
function ezd_get_ranked_docs_by_votes( $order_by = 'positive', $limit = 0 ) {
	global $wpdb;

	// Whitelist the sortable column so it is safe to interpolate directly.
	$order_by = ( 'negative' === $order_by ) ? 'negative' : 'positive';

	$sql = "SELECT p.ID AS post_id,
			COALESCE( SUM( CASE WHEN pm.meta_key = 'positive' THEN pm.meta_value ELSE 0 END ), 0 ) AS positive,
			COALESCE( SUM( CASE WHEN pm.meta_key = 'negative' THEN pm.meta_value ELSE 0 END ), 0 ) AS negative
		FROM {$wpdb->posts} p
		INNER JOIN {$wpdb->postmeta} pm ON p.ID = pm.post_id
		WHERE p.post_type = 'docs'
		  AND p.post_status = 'publish'
		  AND pm.meta_key IN ( 'positive', 'negative' )
		GROUP BY p.ID
		HAVING {$order_by} > 0
		ORDER BY {$order_by} DESC, post_id DESC";

	if ( $limit > 0 ) {
		// phpcs:ignore WordPress.DB.PreparedSQL.NotPrepared -- $order_by is whitelisted above; $limit is bound below.
		$rows = $wpdb->get_results( $wpdb->prepare( "{$sql} LIMIT %d", $limit ), ARRAY_A );
	} else {
		// phpcs:ignore WordPress.DB.PreparedSQL.NotPrepared -- $order_by is whitelisted above; query has no dynamic input.
		$rows = $wpdb->get_results( $sql, ARRAY_A );
	}

	return is_array( $rows ) ? $rows : [];
}

/**
 * Map ranked vote rows to the display array used by the dashboard/analytics panels.
 *
 * Loads the matching posts in a single query (which also primes the meta cache)
 * instead of one lookup per row, and preserves the SQL ordering of $rows.
 *
 * @param array[] $rows Rows from ezd_get_ranked_docs_by_votes().
 * @return array[]
 */
function ezd_map_ranked_docs_for_display( $rows ) {
	if ( empty( $rows ) ) {
		return [];
	}

	$post_ids = array_map(
		static function ( $row ) {
			return (int) $row['post_id'];
		},
		$rows
	);

	// Single query for the (already limited) set of ranked docs.
	$posts = get_posts(
		[
			'post_type'   => 'docs',
			'post__in'    => $post_ids,
			'numberposts' => count( $post_ids ),
			'post_status' => 'publish',
		]
	);

	$posts_by_id = [];
	foreach ( $posts as $post ) {
		$posts_by_id[ $post->ID ] = $post;
	}

	$data = [];
	foreach ( $rows as $row ) {
		$post_id = (int) $row['post_id'];
		if ( empty( $posts_by_id[ $post_id ] ) ) {
			continue;
		}

		$post     = $posts_by_id[ $post_id ];
		$data[]   = [
			'post_id'        => $post_id,
			'post_title'     => $post->post_title,
			'post_permalink' => get_permalink( $post_id ),
			'post_edit_link' => get_edit_post_link( $post_id ),
			'positive_time'  => (int) $row['positive'],
			'negative_time'  => (int) $row['negative'],
			'created_at'     => get_the_time( 'U', $post_id ),
		];
	}

	return $data;
}
