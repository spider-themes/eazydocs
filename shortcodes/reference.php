<?php
add_shortcode( 'reference', function ( $atts, $content ) {
	ob_start();
	$atts = shortcode_atts( array(
		'number' => '1',
	), $atts );
	if ( ezd_unlock_themes() == false ) {
		return false;
	}
	?>

    <span ezd-note-serial="<?php echo $atts['number'] ?>" class="ezd-footnotes-link-item" data-bs-original-title='<?php echo $content; ?>'>
        <i onclick="location.href='#note-name-<?php echo esc_attr( $atts['number'] ); ?>'">
            [<?php echo $atts['number'] ?? ''; ?>]
        </i>
        <span> <?php echo $content; ?> </span>
    </span>

	<?php
	return ob_get_clean();
} );

add_filter( 'the_content', function ( $ezd_content ) {
	$ezd_options      = get_option( 'eazydocs_settings' );
	$is_notes_title   = $ezd_options['is_footnotes_heading'] ?? '1';
	$notes_title_text = $ezd_options['footnotes_heading_text'] ?? __( 'Footnotes', 'eazydocs' );
	$footnotes_column = $ezd_options['footnotes_column'] ?? '1';

	$all_shortcodes = ezd_all_shortcodes( $ezd_content );
	$all_shortcoded = '';
	foreach ( $all_shortcodes as $all_shortcode ) {
		$all_shortcoded .= '<span>' . do_shortcode( $all_shortcode[0] ) . '</span>';
	}

	$ezd_footnote_title = '';
	if ( ! empty( $notes_title_text ) && $is_notes_title == '1' && has_shortcode( $ezd_content, 'reference' ) ) {
		$ezd_footnote_title = sprintf( '<div class="ezd-footnote-title">%s</div>', $notes_title_text );
	}

	$footnotes_contents = '';
	if ( has_shortcode( $ezd_content, 'reference' ) ) {
		$footnotes_contents = $ezd_footnote_title . "<div ezd-data-column='" . $footnotes_column . "' class='ezd-footnote-footer'>" . $all_shortcoded
		                      . "</div>";
		return $ezd_content . $footnotes_contents;
	}

} );