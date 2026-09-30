<?php
/**
 * Cannot access directly.
 */
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

$title_tag = ! empty( $settings['title_tag'] ) ? $settings['title_tag'] : 'h2';
?>

<div class="ezd-grid ezd-column-<?php echo esc_attr( $ppp_column ); ?> topic_list_inner">
	<?php
	$delay        = 0.2;
	$section_ids  = ! empty( $sections ) ? wp_list_pluck( $sections, 'ID' ) : array();
	$grouped_all  = ! empty( $section_ids ) ? ezd_get_children_grouped(
		$section_ids,
		array(
			'post_status' => 'publish',
			'orderby'     => 'menu_order',
			'order'       => 'ASC',
		)
	) : array();
	$ppp_limit    = ! empty( $settings['ppp_doc_items'] ) ? (int) $settings['ppp_doc_items'] : -1;

	foreach ( $sections as $section ) :
		$all_doc_items = $grouped_all[ $section->ID ] ?? array();
		$doc_items     = ( $ppp_limit > 0 && count( $all_doc_items ) > $ppp_limit ) ? array_slice( $all_doc_items, 0, $ppp_limit ) : $all_doc_items;
		?>
        <div class="topic_list_item box-item wow fadeIn" data-wow-delay="0.2s">
			<?php
			if ( ! empty( $section->post_title ) ) :
				?>
                <a href="<?php the_permalink( $section->ID ); ?>" class="topic-title">
                    <h4 class="ct-heading-text">
						<?php echo get_the_post_thumbnail( $section->ID, 'full' ); ?>
						<?php echo wp_kses_post( $section->post_title ); ?>
                    </h4>
                    <span class="ezd-badge ezd-circle"> <?php echo count( $all_doc_items ) ?> </span>
                </a>
			<?php
			endif;

			if ( ! empty( $doc_items ) ) : ?>
                <ul class="article-list navbar-nav ezd-list-unstyled">
					<?php
					foreach ( $doc_items as $doc_item ) :
						?>
                        <li>
                            <a class="ct-content-text" href="<?php the_permalink( $doc_item->ID ) ?>">
                                <i class="icon_document_alt"></i>
								<?php echo wp_kses_post( $doc_item->post_title ) ?>
                            </a>
                        </li>
					<?php
					endforeach;
					?>
                </ul>
			    <?php
			endif;

			if ( ! empty( $settings['read_more'] ) ) : ?>
                <a href="<?php the_permalink( $section->ID ); ?>" class="text_btn dark_btn">
					<?php echo wp_kses_post( $settings['read_more'] ) ?>
                    <i class="<?php echo ezd_arrow() ?>"></i>
                </a>
			<?php endif; ?>
        </div>
	    <?php
	endforeach;
	?>
</div>