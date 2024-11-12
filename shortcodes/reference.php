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
		<span class="ezd-footnote-content">
			<?php
			// Execute the shortcode
			$output = do_shortcode( $content );

			// Replace <div> with <span> and </div> with </span>
			$output = preg_replace('/<p([^>]*)>/', '<span$1>', $output);
			$output = preg_replace('/<\/p>/', '</span>', $output);

			// Output the modified content
			echo $output;
			?>
		</span>
	</span>
	<?php
	return ob_get_clean();
} );