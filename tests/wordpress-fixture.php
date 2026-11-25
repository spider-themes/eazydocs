<?php
/**
 * Seed an installed, disposable WordPress site for the browser smoke test.
 * Run with: wp --path=/path/to/disposable-wordpress eval-file tests/wordpress-fixture.php
 */
if ( 'cli' !== PHP_SAPI || ! defined( 'DB_NAME' ) || 'ezd_audit' !== DB_NAME ) {
	exit( 1 );
}

if ( ! post_type_exists( 'docs' ) ) {
	throw new RuntimeException( 'Activate EazyDocs before creating fixtures.' );
}

foreach ( [ 'Fixture Alpha', 'Fixture Bravo', 'Fixture Charlie' ] as $order => $title ) {
	$id = wp_insert_post(
		[
			'post_title'   => $title,
			'post_content' => $title . ' public fixture content.',
			'post_status'  => 'publish',
			'post_type'    => 'docs',
			'post_author'  => get_user_by( 'login', 'ezd_test_admin' )->ID,
			'menu_order'   => $order + 1,
		],
		true
	);
	if ( is_wp_error( $id ) ) {
		throw new RuntimeException( $id->get_error_message() );
	}
	if ( 0 === $order ) {
		echo get_permalink( $id ) . PHP_EOL;
	}
}
