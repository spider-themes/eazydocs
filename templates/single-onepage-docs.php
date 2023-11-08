<?php
$layout = get_post_meta( get_the_ID(), 'ezd_doc_layout', true );

if ( $layout == 'fullscreen-layout' ) {
	eazydocs_get_template_part( 'onepage/fullscreen-layout' );
} elseif ( $layout == 'classic-onepage-layout' ) {
	eazydocs_get_template_part( 'onepage/default-layout' );
} else {
	eazydocs_get_template_part( 'onepage/default-layout' );
}