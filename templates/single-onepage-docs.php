<?php

$options        = get_option( 'eazydocs_settings' );
$preset_layout  = $options['onepage_layout'];

if( $preset_layout == 'other-layout' ) {
	eazydocs_get_template_part( 'onepage/other-layout' );
} else {
	eazydocs_get_template_part( 'onepage-default' );
}