<?php
$cz_options          = get_option( 'eazydocs_settings' );
$related_docs_switch = $cz_options['related-docs'] ?? '';
$related_docs        = $related_docs_switch == '0' ? 'd-none' : '1';

$viewed_docs_switch = $cz_options['viewed-docs'] ?? '';
$viewed_docs        = $viewed_docs_switch == '0' ? 'd-none' : '1';

$related_title    = $cz_options['related-docs-title'] ?? esc_html__( 'Related Articles', 'eazydocs' ); // id of field
$related_visible  = $cz_options['related-visible-docs'] ?? '4';
$related_see_more = $cz_options['related-docs-more-btn'] ?? esc_html__( 'See More', 'eazydocs' );

$reviewed_title  = $cz_options['viewed-docs-title'] ?? esc_html__( 'Recently Viewed Articles', 'eazydocs' );
$viewed_visible  = $cz_options['viewed-visible-docs'] ?? '4';
$viewed_see_more = $cz_options['view-docs-more-btn'] ?? esc_html__( 'See More', 'eazydocs' );
$docs_visibility = ( $related_docs_switch == 0 && $viewed_docs_switch == 0 ) ? 'd-none' : '1';

if ( $docs_visibility == '1' ) :
	?>
    <div class="row topic_item_tabs inner_tab_list related-recent-docs <?php echo esc_attr( $docs_visibility ); ?>">
		<?php
		/*** Related Docs ***/
		do_action( 'eazydocs_related_articles', $related_title, $related_docs, $related_visible, $related_see_more );

		/*** Recently Viewed Docs ***/
		do_action( 'eazydocs_viewed_articles', $reviewed_title, $viewed_docs, $viewed_visible, $viewed_see_more );
		?>
    </div>
    <?php
endif;