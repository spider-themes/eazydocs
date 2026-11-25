<?php
/**
 * Seed or measure a controlled Docs Builder workload on a disposable site.
 *
 * Usage: php tests/benchmark-docs-builder.php WP_LOAD seed 100|1000|10000
 *        php tests/benchmark-docs-builder.php WP_LOAD bootstrap|branch
 */

if ( 'cli' !== PHP_SAPI || ! isset( $argv[2] ) ) {
	exit( 1 );
}

require $argv[1];

if ( 'ezd_audit' !== DB_NAME || 'bench_' !== $GLOBALS['wpdb']->prefix || ! post_type_exists( 'docs' ) ) {
	fwrite( STDERR, "Use an activated EazyDocs fixture with database ezd_audit and prefix bench_.\n" );
	exit( 1 );
}

global $wpdb;
$admin = get_user_by( 'login', 'ezd_test_admin' );
wp_set_current_user( $admin->ID );

if ( 'seed' === $argv[2] ) {
	$target = (int) ( $argv[3] ?? 0 );
	$roots  = get_option( 'ezd_benchmark_roots', [] );
	$total  = array_sum( (array) wp_count_posts( 'docs' ) );
	if ( ! in_array( $target, [ 100, 1000, 10000 ], true ) || $target <= $total || ( $total && ! $roots ) ) {
		throw new RuntimeException( 'Seed an empty site in ascending order: 100, 1000, 10000.' );
	}

	// Split each 100 posts into one top-level doc and a nested 99-doc subtree.
	// Empty posts are enough to exercise Builder serialization and tree lookups.
	$section = 0;
	for ( $index = $total; $index < $target; ++$index ) {
		$offset = $index % 100;
		$parent = 0 === $offset ? 0 : ( 1 === $offset % 10 ? end( $roots ) : $section );
		$id     = wp_insert_post(
			[
				'post_type'   => 'docs',
				'post_status' => 'publish',
				'post_author' => $admin->ID,
				'post_title'  => 'Benchmark doc ' . ( $index + 1 ),
				'post_parent' => $parent,
				'menu_order'  => $offset,
			],
			true,
			false
		);
		if ( is_wp_error( $id ) ) {
			throw new RuntimeException( $id->get_error_message() );
		}
		if ( 0 === $offset ) {
			$roots[] = $id;
		} elseif ( 1 === $offset % 10 ) {
			$section = $id;
		}
	}

	update_option( 'ezd_benchmark_roots', $roots, false );
	printf( "Seeded %d docs\n", $target );
	exit;
}

$roots = get_option( 'ezd_benchmark_roots', [] );
if ( ! $roots || ! in_array( $argv[2], [ 'bootstrap', 'branch' ], true ) ) {
	throw new RuntimeException( 'Seed docs first, then select bootstrap or branch.' );
}

$branch  = 'branch' === $argv[2];
$request = new WP_REST_Request( 'GET', '/eazydocs/v1/docs-builder' . ( $branch ? '/children' : '' ) );
$request->set_param( $branch ? 'parent_id' : 'active_doc', (int) end( $roots ) );

// Register the routes before collecting metrics. Each CLI invocation starts
// with a fresh request-local object cache and a new REST controller.
rest_get_server();
$start_queries = get_num_queries();
$start_memory  = memory_get_usage();
if ( function_exists( 'memory_reset_peak_usage' ) ) {
	memory_reset_peak_usage();
}
$start    = hrtime( true );
$response = rest_do_request( $request );
$bytes    = strlen( wp_json_encode( $response->get_data() ) );
$elapsed  = ( hrtime( true ) - $start ) / 1e6;
$peak     = memory_get_peak_usage() - $start_memory;
$queries  = get_num_queries() - $start_queries;

if ( 200 !== $response->get_status() ) {
	fwrite( STDERR, wp_json_encode( $response->get_data() ) . PHP_EOL );
	exit( 1 );
}

printf(
	"%s\n",
	wp_json_encode(
		[
			'docs'         => count( $roots ) * 100,
			'request'      => $argv[2],
			'queries'      => $queries,
			'bytes'        => $bytes,
			'peak_kib'     => round( $peak / 1024, 1 ),
			'milliseconds' => round( $elapsed, 2 ),
		]
	)
);
