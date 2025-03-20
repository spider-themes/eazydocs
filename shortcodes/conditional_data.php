<?php
add_shortcode( 'conditional_data', function( $atts, $content ) {
	ob_start();
 
	$atts = shortcode_atts( array(
		'dependency' => '',
	), $atts );

	$dependency = !empty($atts['dependency']) ? sanitize_text_field($atts['dependency']) : '';

	if ( !empty($content) ) :
		?>
		<span class="ezd-con-<?php echo esc_attr($dependency); ?>">
			<?php echo do_shortcode( wp_kses_post($content) ); ?>
		</span>
		<?php
	endif;

	$html = ob_get_clean();
	return $html;
});