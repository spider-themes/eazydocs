<?php
$options        = get_option( 'eazydocs_settings' );
$preset_layout  = $options['onepage_layout'] ?? '';
$layout = get_post_meta(get_the_ID(), 'ezd_doc_layout', true );

if( $layout == 'fullscreen-layout' ){
	eazydocs_get_template_part( 'onepage/fullscreen-layout' );
}else {
	if ( $preset_layout == 'fullscreen-layout' ) {
		eazydocs_get_template_part( 'onepage/fullscreen-layout' );
	} else {
		eazydocs_get_template_part( 'onepage/default-layout' );
	}
}