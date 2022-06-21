<?php

$options = get_option( 'eazydocs_settings' );
$preset_layout = $options['preset_layout'];

if( $preset_layout == 'main' ) {
	eazydocs_get_template_part( 'default' );
} else {
	eazydocs_get_template_part( 'book-layout/book-layout' );
}