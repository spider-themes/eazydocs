<?php
/**
 * Document ancestry, child queries, and navigation tree cache.
 *
 * The global ezd_* functions in this module also serve templates and Pro.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Get doc parent id by current id
 */
function ezd_get_doc_parent_id( $doc_id = 0 ) {
	$doc_id = absint( $doc_id ?: get_the_ID() );
	if ( ! $doc_id ) {
		return 0;
	}

	$ancestors = get_post_ancestors( $doc_id );

	return ! empty( $ancestors ) ? (int) end( $ancestors ) : $doc_id;
}

if ( ! function_exists( 'get_root_parent_id' ) ) {
	/**
	 * Backward compatibility alias for ezd_get_doc_parent_id.
	 *
	 * Declared on 'plugins_loaded' to allow legacy versions of EazyDocs Pro
	 * (<= 2.14.0) that declare get_root_parent_id() without a function_exists() check
	 * to load safely without triggering a fatal "Cannot redeclare get_root_parent_id()" error.
	 *
	 * @param int $page_id Post ID.
	 * @return int Root parent ID.
	 */
	if ( did_action( 'plugins_loaded' ) ) {
		function get_root_parent_id( $page_id ) {
			return ezd_get_doc_parent_id( $page_id );
		}
	} else {
		add_action(
			'plugins_loaded',
			function () {
				if ( ! function_exists( 'get_root_parent_id' ) ) {
					function get_root_parent_id( $page_id ) {
						return ezd_get_doc_parent_id( $page_id );
					}
				}
			},
			-1
		);
	}
}

/**
 * Every descendant doc ID of a doc, at any depth and in any non-trashed status.
 *
 * Used when trashing a doc tree. One ID→parent query, walked in memory; only
 * 'docs' rows, so media attached to a doc is never swept up with it.
 *
 * @param int $doc_id Root doc ID.
 * @return int[] Descendant IDs (root excluded), parents before children.
 */
function ezd_get_doc_tree_ids( $doc_id ) {
	global $wpdb;

	// phpcs:ignore WordPress.DB.DirectDatabaseQuery -- one-off, write-path lookup.
	$rows = $wpdb->get_results(
		"SELECT ID, post_parent FROM {$wpdb->posts} WHERE post_type = 'docs' AND post_status NOT IN ('trash', 'auto-draft')"
	);

	$children = [];
	foreach ( (array) $rows as $row ) {
		$children[ (int) $row->post_parent ][] = (int) $row->ID;
	}

	$ids   = [];
	$queue = $children[ (int) $doc_id ] ?? [];
	$seen  = [ (int) $doc_id => true ];

	while ( $queue ) {
		$id = array_shift( $queue );
		if ( isset( $seen[ $id ] ) ) {
			continue;
		}
		$seen[ $id ] = true;
		$ids[]       = $id;

		foreach ( $children[ $id ] ?? [] as $child_id ) {
			$queue[] = $child_id;
		}
	}

	return $ids;
}

/**
 * Fetch the direct children of several parent docs in ONE query.
 *
 * Templates that render a doc tree level by level used to call get_children()
 * once per node. Call this once per depth level instead and look children up
 * by parent ID. Children keep get_children()'s menu_order ASC ordering.
 *
 * @param int[] $parent_ids Parent doc IDs.
 * @param array $args       Extra get_posts() args (e.g. post_status).
 * @return array<int, WP_Post[]> Children grouped by parent ID.
 */
function ezd_get_doc_children_map( $parent_ids, $args = [] ) {
	return ezd_get_children_grouped( $parent_ids, $args );
}

/**
 * Count every descendant (children, grandchildren, …) of a doc.
 *
 * Loads the ID → parent map of all docs once per request (per status set) and
 * walks it in memory. Replaces calling get_pages( [ 'child_of' => $id ] ) for
 * each doc card, which re-fetched the whole docs table for every card.
 *
 * @param int      $doc_id   Root doc ID.
 * @param string[] $statuses Post statuses to count. Default: ezd_doc_listing_statuses().
 * @return int
 */
function ezd_count_doc_descendants( $doc_id, $statuses = null ) {
	static $maps = [];

	$statuses = $statuses ?: ezd_doc_listing_statuses();
	$map_key  = implode( ',', $statuses );

	if ( ! isset( $maps[ $map_key ] ) ) {
		global $wpdb;

		$holders = implode( ', ', array_fill( 0, count( $statuses ), '%s' ) );
		// phpcs:ignore WordPress.DB.DirectDatabaseQuery, WordPress.DB.PreparedSQL.InterpolatedNotPrepared -- placeholders built above.
		$rows = $wpdb->get_results(
			$wpdb->prepare(
				"SELECT ID, post_parent FROM {$wpdb->posts} WHERE post_type = 'docs' AND post_status IN ({$holders})",
				$statuses
			)
		);

		$children = [];
		foreach ( (array) $rows as $row ) {
			$children[ (int) $row->post_parent ][] = (int) $row->ID;
		}
		$maps[ $map_key ] = $children;
	}

	$children = $maps[ $map_key ];
	$count    = 0;
	$stack    = $children[ (int) $doc_id ] ?? [];
	$seen     = [];

	while ( $stack ) {
		$id = array_pop( $stack );
		if ( isset( $seen[ $id ] ) ) {
			continue; // Guard against corrupt parent loops.
		}
		$seen[ $id ] = true;
		++$count;

		if ( ! empty( $children[ $id ] ) ) {
			array_push( $stack, ...$children[ $id ] );
		}
	}

	return $count;
}

/**
 * Get all descendant IDs by a parent ID
 *
 * @param int    $parent_id   The parent post ID.
 * @param string $post_type   The post type to query (default: 'docs').
 * @param string $post_status The post status to query (default: 'publish').
 *
 * @return array An array of all descendant post IDs.
 */
function ezd_get_all_descendant_ids( $parent_id, $post_type = 'docs', $post_status = 'publish' ) {
    global $wpdb;

    $all_ids = [];

    // Get immediate children IDs
    $children = $wpdb->get_col( $wpdb->prepare(
        "SELECT ID FROM {$wpdb->posts} WHERE post_parent = %d AND post_type = %s AND post_status = %s",
        $parent_id,
        $post_type,
        $post_status
    ));

    if ( ! empty( $children ) ) {
        foreach ( $children as $child_id ) {
            // Add this child ID
            $all_ids[] = $child_id;
            // Recursively get grandchildren and deeper descendants
            $descendants = ezd_get_all_descendant_ids( $child_id, $post_type, $post_status );
            if ( ! empty( $descendants ) ) {
                $all_ids = array_merge( $all_ids, $descendants );
            }
        }
    }

    return $all_ids;
}

/**
 * Transient key for the flat docs tree, segmented per language so a cached tree
 * from one language is never served on another.
 *
 * @param string $post_type
 * @return string
 */
function ezd_docs_tree_cache_key( $post_type ) {
	$lang = ezd_current_language();
	return 'ezd_docs_tree_flat_' . $post_type . ( $lang ? '_' . $lang : '' );
}

/**
 * Build the flat, ordered list of doc IDs for a post type.
 *
 * On multilingual sites suppress_filters is disabled so WPML/Polylang scope the
 * query to the current language instead of mixing every translation into one tree.
 *
 * @param string $post_type
 * @return int[]
 */
function ezd_build_docs_tree_flat_ids( $post_type ) {
	// Fetch the whole tree in ONE ordered query and walk it in memory. The
	// previous version issued a separate query for every doc in the library.
	$args = [
		'post_type'              => $post_type,
		'post_status'            => 'publish',
		'orderby'                => 'menu_order title',
		'order'                  => 'ASC',
		'fields'                 => 'id=>parent',
		'numberposts'            => -1,
		'no_found_rows'          => true,
		'update_post_meta_cache' => false,
		'update_post_term_cache' => false,
	];

	if ( ezd_is_multilingual() ) {
		$args['suppress_filters'] = false;
	}

	$posts = get_posts( $args );
	if ( empty( $posts ) || ! is_array( $posts ) ) {
		return [];
	}

	// 'id=>parent' returns an ordered [ ID => post_parent ] map. Group children
	// under their parent, preserving the query order.
	$children = [];
	foreach ( $posts as $id => $parent_id ) {
		// When WordPress query cache / object cache (e.g. Redis) is active,
		// WP_Query's 'id=>parent' returns keys prefixed with 'post_parent:'.
		if ( is_object( $parent_id ) ) {
			$doc_id    = isset( $parent_id->ID ) ? (int) $parent_id->ID : 0;
			$parent_id = isset( $parent_id->post_parent ) ? (int) $parent_id->post_parent : 0;
		} else {
			if ( is_string( $id ) && strpos( $id, 'post_parent:' ) === 0 ) {
				$id = substr( $id, 12 );
			}
			$doc_id    = (int) trim( (string) $id );
			$parent_id = (int) trim( (string) $parent_id );
		}

		if ( $doc_id <= 0 ) {
			continue;
		}

		// Prevent direct self-parenting loops.
		if ( $parent_id === $doc_id ) {
			$parent_id = 0;
		}

		$children[ $parent_id ][] = $doc_id;
	}

	$ordered_ids = [];
	$stack       = array_reverse( $children[0] ?? [] );
	$visited     = [];

	// Iterative depth-first walk (pre-order): parent first, then its children.
	// Guard against circular hierarchies and duplicate visits.
	while ( ! empty( $stack ) ) {
		$id = array_pop( $stack );

		if ( isset( $visited[ $id ] ) ) {
			continue;
		}
		$visited[ $id ] = true;
		$ordered_ids[]  = $id;

		if ( ! empty( $children[ $id ] ) ) {
			foreach ( array_reverse( $children[ $id ] ) as $child_id ) {
				if ( ! isset( $visited[ $child_id ] ) ) {
					$stack[] = $child_id;
				}
			}
		}
	}

	return $ordered_ids;
}

/**
 * Get cached flat document tree (array of IDs in order)
 *
 * @param string $post_type
 * @return array
 */
function ezd_get_docs_tree_flat_cached( $post_type ) {
	$cache_key   = ezd_docs_tree_cache_key( $post_type );
	$ordered_ids = get_transient( $cache_key );

	if ( false === $ordered_ids ) {
		$ordered_ids = ezd_build_docs_tree_flat_ids( $post_type );
		set_transient( $cache_key, $ordered_ids, 12 * HOUR_IN_SECONDS );
	}

	return $ordered_ids;
}

/**
 * Delete the flat docs-tree cache for a post type across every active language.
 *
 * @param string $post_type
 */
function ezd_delete_docs_tree_cache_all_langs( $post_type ) {
	delete_transient( 'ezd_docs_tree_flat_' . $post_type );
	foreach ( ezd_active_language_codes() as $code ) {
		delete_transient( 'ezd_docs_tree_flat_' . $post_type . '_' . $code );
	}
}

/**
 * Clear the docs tree cache on post update/delete
 *
 * @param int $post_id
 * @param WP_Post $post
 */
function ezd_clear_docs_tree_cache( $post_id, $post ) {
	if ( in_array( $post->post_type, [ 'docs', 'onepage-docs' ], true ) ) {
		ezd_delete_docs_tree_cache_all_langs( $post->post_type );
	}
}
add_action( 'save_post', 'ezd_clear_docs_tree_cache', 10, 2 );
add_action( 'delete_post', 'ezd_clear_docs_tree_cache', 10, 2 );

/**
 * Get previous and next IDs from an array of IDs
 *
 * @param int $current_post_id
 *
 * @return array
 */
function ezd_prev_next_docs( $current_post_id ) {
	$post_type   = get_post_type( $current_post_id );
	$ordered_ids = ezd_get_docs_tree_flat_cached( $post_type );

	// Find current index and prev/next IDs. A doc that is not in the published
	// tree (draft preview, private doc) has no neighbours; without this guard
	// array_search() returned false and the first docs were shown as "next".
	$current_index = array_search( (int) $current_post_id, array_map( 'intval', (array) $ordered_ids ), true );
	if ( false === $current_index ) {
		return [
			'prev'    => null,
			'current' => $current_post_id,
			'next'    => null,
		];
	}

	$prev_id = $ordered_ids[ $current_index - 1 ] ?? null;
	$next_id = $ordered_ids[ $current_index + 1 ] ?? null;

	return [
		'prev'    => $prev_id,
		'current' => $current_post_id,
		'next'    => $next_id,
	];
}

/**
 * Flush the cached docs tree when a doc is saved or deleted.
 *
 * @param int $post_id Post ID.
 */
function ezd_flush_docs_tree_cache( $post_id ) {
	$post_type = get_post_type( $post_id );
	if ( 'docs' === $post_type || 'onepage-docs' === $post_type ) {
		ezd_delete_docs_tree_cache_all_langs( $post_type );
	}
}
add_action( 'save_post', 'ezd_flush_docs_tree_cache' );
add_action( 'delete_post', 'ezd_flush_docs_tree_cache' );

// Helper function to flatten the doc tree in correct order
function ezd_docs_build_tree_flat( $post_id, &$list ) {
	$list[] = $post_id;

	$args = [
		'post_type'   => get_post_type( $post_id ),
		'post_status' => 'publish',
		'post_parent' => $post_id,
		'orderby'     => 'menu_order title',
		'order'       => 'ASC',
		'fields'      => 'ids',
		'numberposts' => -1,
	];

	if ( ezd_is_multilingual() ) {
		$args['suppress_filters'] = false;
	}

	foreach ( get_posts( $args ) as $child_id ) {
		ezd_docs_build_tree_flat( $child_id, $list );
	}
}

/**
 * Efficiently batch-count direct children and grandchildren for a list of parent doc IDs.
 * Utilizes in-memory tree cache of ezd_count_doc_descendants to avoid redundant DB queries.
 *
 * @param array $parent_ids Array of parent post IDs.
 * @param array $statuses   Post statuses to count.
 * @return array Map of parent_id => total descendant count.
 */
function ezd_get_docs_descendant_counts( $parent_ids, $statuses = [] ) {
	if ( empty( $parent_ids ) ) {
		return [];
	}

	$parent_ids = array_values( array_unique( array_filter( array_map( 'absint', (array) $parent_ids ) ) ) );
	if ( empty( $parent_ids ) ) {
		return [];
	}

	$counts = [];
	foreach ( $parent_ids as $pid ) {
		$counts[ $pid ] = ezd_count_doc_descendants( $pid, $statuses );
	}

	return $counts;
}

/**
 * Batch fetch children for an array of parent post IDs, grouped by post_parent.
 * Eliminates N+1 queries when looping over parent sections or documents.
 *
 * @param array $parent_ids Array of parent post IDs.
 * @param array $args       Query arguments (post_status, orderby, order, numberposts, etc.)
 * @return array Array mapping parent_id => array of WP_Post objects.
 */
function ezd_get_children_grouped( $parent_ids, $args = [] ) {
	if ( empty( $parent_ids ) ) {
		return [];
	}

	$parent_ids = array_values( array_unique( array_filter( array_map( 'absint', (array) $parent_ids ) ) ) );
	if ( empty( $parent_ids ) ) {
		return [];
	}

	$grouped = array_fill_keys( $parent_ids, [] );

	$query_args = [
		'post_type'      => 'docs',
		'post_parent__in'=> $parent_ids,
		'posts_per_page' => -1,
		'post_status'    => $args['post_status'] ?? ( function_exists( 'ezd_doc_listing_statuses' ) ? ezd_doc_listing_statuses() : [ 'publish' ] ),
		'orderby'        => $args['orderby'] ?? 'menu_order',
		'order'          => $args['order'] ?? 'ASC',
		'no_found_rows'  => true,
	];

	if ( ! empty( $args['exclude'] ) ) {
		$query_args['post__not_in'] = (array) $args['exclude'];
	}

	$posts = get_posts( $query_args );

	$per_parent_limit = isset( $args['numberposts'] ) && (int) $args['numberposts'] > 0 ? (int) $args['numberposts'] : 0;

	foreach ( $posts as $post ) {
		$pid = (int) $post->post_parent;
		if ( isset( $grouped[ $pid ] ) ) {
			if ( $per_parent_limit > 0 && count( $grouped[ $pid ] ) >= $per_parent_limit ) {
				continue;
			}
			$grouped[ $pid ][] = $post;
		}
	}

	return $grouped;
}
