<?php
/**
 * Block configuration file.
 */
define( 'EAZYDOCS_BLOCK_IMG', plugins_url( 'images/', __FILE__ ) );

add_action( 'carbon_fields_register_fields', 'eaz_attach_theme_options' );

function eaz_attach_theme_options() {
	require __DIR__ . '/docs-archive.php';
	require __DIR__ . '/search.php';
}
