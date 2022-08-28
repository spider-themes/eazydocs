<?php
/**
 * Block configuration file.
 */

add_action( 'carbon_fields_register_fields', 'eaz_attach_theme_options' );

function eaz_attach_theme_options() {
	require __DIR__ . '/docs-archive.php';
}
