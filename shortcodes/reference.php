<?php
add_shortcode( 'reference', function ( $atts, $content ) {
	ob_start();
	$atts = shortcode_atts( array(
		'number' => '1',
	), $atts );
	if ( ! ezd_unlock_themes() ) {
		return false;
	}
	?>

    <span ezd-note-serial="<?php echo esc_attr($atts['number']) ?>" id="serial-id-<?php echo esc_attr( $atts['number'] ); ?>" class="ezd-footnotes-link-item" data-bs-original-title="<?php echo esc_attr($content); ?>">
        <i onclick="location.href='#note-name-<?php echo esc_attr( $atts['number'] ); ?>'">
            [<?php echo esc_html($atts['number'] ?? ''); ?>]
        </i>
        <span><?php echo wp_kses_post( $content ); ?></span>
    </span>

	<?php
	return ob_get_clean();
} );