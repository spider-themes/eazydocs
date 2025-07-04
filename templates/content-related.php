<?php
$related_docs_switch = ezd_get_opt( 'related-docs' );
$related_docs        = $related_docs_switch == '0' ? 'ezd-d-none' : '1';

$viewed_docs_switch = ezd_get_opt( 'viewed-docs' );
$viewed_docs        = $viewed_docs_switch == '0' ? 'ezd-d-none' : '1';
$related_title      = ezd_get_opt( 'related-docs-title', esc_html__( 'Related Articles', 'eazydocs' ) );
$related_visible    = ezd_get_opt( 'related-visible-docs', '4' );
$related_see_more   = ezd_get_opt( 'related-docs-more-btn', esc_html__( 'See More', 'eazydocs' ) );

$reviewed_title     = ezd_get_opt( 'viewed-docs-title', esc_html__( 'Recently Viewed Articles', 'eazydocs' ) );
$viewed_visible     = ezd_get_opt( 'viewed-visible-docs', '4' );
$viewed_see_more    = ezd_get_opt( 'view-docs-more-btn', esc_html__( 'See More', 'eazydocs' ) );
$docs_visibility    = ( $related_docs_switch == 0 && $viewed_docs_switch == 0 ) ? 'ezd-d-none' : '1';

if ( $docs_visibility == '1' ) :
	?>
    <div class="ezd-grid ezd-grid-cols-12 topic_item_tabs inner_tab_list related-recent-docs <?php echo esc_attr( $docs_visibility ); ?>">
        <?php
            /*** Related Docs ***/
            do_action( 'eazydocs_related_articles', $related_title, $related_docs, $related_visible, $related_see_more );

            /*** Recently Viewed Docs ***/
            do_action( 'eazydocs_viewed_articles', $reviewed_title, $viewed_docs, $viewed_visible, $viewed_see_more );
            ?>
    </div>
<?php
endif;