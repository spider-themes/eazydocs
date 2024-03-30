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

    <span ezd-note-serial="<?php echo $atts['number'] ?>" id="serial-id-<?php echo esc_attr( $atts['number'] ); ?>" class="ezd-footnotes-link-item" data-bs-original-title="<?php echo esc_attr($content); ?>">
        <i onclick="location.href='#note-name-<?php echo esc_attr( $atts['number'] ); ?>'">
            [<?php echo $atts['number'] ?? ''; ?>]
        </i>
        <span><?php echo wp_kses_post( $content ); ?></span>
    </span>

	<?php
	return ob_get_clean();
} );

add_filter( 'the_content', function ( $ezd_content ) {
	$ezd_options      	= get_option( 'eazydocs_settings' );
	$is_notes_title   	= $ezd_options['is_footnotes_heading'] ?? '1';

	$footnotes_layout  	 = $ezd_options['footnotes_layout'] ?? 'collapsed';
	$is_footnotes_expand = $is_notes_title == 1 ? $footnotes_layout : '';

	$ezd_notes_footer_mt = $is_notes_title != '1' ? 'mt-30' : '';
	$notes_title_text 	= $ezd_options['footnotes_heading_text'] ?? __( 'Footnotes', 'eazydocs' );
	$footnotes_column 	= $ezd_options['footnotes_column'] ?? '1';

	$all_shortcodes 	= ezd_all_shortcodes( $ezd_content );
	$all_shortcoded 	= '';
	$shortcode_counter 	= 0;

	foreach ( $all_shortcodes as $all_shortcode ) {
		if ( has_shortcode( $all_shortcode[0], 'reference' ) ) {
			$shortcode_counter++;
			$all_shortcoded .= '<span>' . do_shortcode( $all_shortcode[0] ) . '</span>';
		}
	}

	$ezd_footnote_title = '';
	if ( ! empty( $notes_title_text ) && $is_notes_title == '1' && has_shortcode( $ezd_content, 'reference' ) ) {
		$ezd_footnote_title = sprintf(
            '<div class="ezd-footnote-title '.$is_footnotes_expand.'">
                <span class="ezd-plus-minus"> <i class="icon_plus-box"></i><i class="icon_minus-box"></i></span>
                <span class="ezd-title-txt"> %s </span>
                <span> ('.$shortcode_counter.') </span>
            </div>',
            $notes_title_text .' '
        );
	}

	$footnotes_contents = '';
	if ( has_shortcode( $ezd_content, 'reference' ) ) {
		$footnotes_contents = $ezd_footnote_title . "<div ezd-data-column='" . $footnotes_column . "' class='ezd-footnote-footer $ezd_notes_footer_mt $is_footnotes_expand'>" . $all_shortcoded . "</div>";
	}

	return $ezd_content . $footnotes_contents;
} );