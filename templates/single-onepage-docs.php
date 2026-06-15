<?php
$layout = get_post_meta( get_the_ID(), 'ezd_doc_layout', true );

// Fall back to the site-wide default layout when this document has none set.
if ( empty( $layout ) ) {
	$layout = ezd_get_opt( 'onepage_default_layout', 'classic-onepage-layout' );
}

if ( $layout == 'fullscreen-layout' ) {
	eazydocs_get_template_part( 'onepage/fullscreen-layout' );
} else {
	eazydocs_get_template_part( 'onepage/default-layout' );
}
