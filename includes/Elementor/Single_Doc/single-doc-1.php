<?php
/**
 * Cannot access directly.
 */
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}
?>

<div class="ezd-grid ezd-column-<?php echo esc_attr( $ppp_column ); ?>">
	<?php
	foreach ( $sections as $section ) :
		$doc_items = ezd_get_doc_items( $section->ID, $settings );
		?>
        <div class="categories_guide_item box-item wow fadeInUp single-doc-layout-one">
            <div class="doc-top ezd-d-flex ezd-align-items-start">
				<?php echo wp_get_attachment_image( get_post_thumbnail_id( $section->ID ) ); ?>
                <a class="doc_tag_title" href="<?php the_permalink( $section->ID ); ?>">
					<?php ezd_render_section_title( $section, 'title ct-heading-text', 'Topics' ); ?>
                </a>
            </div>
			<?php ezd_render_doc_items_list( $doc_items, 'ezd-list-unstyled tag_list', 'ct-content-text' ); ?>
			<?php
			if ( ! empty( $settings['read_more'] ) ) :
				ezd_render_read_more_btn( get_permalink( $section->ID ), $settings['read_more'], 'doc_border_btn', '<i class="' . ezd_arrow() . '"></i>' );
			endif; ?>
        </div>
	<?php
	endforeach;
	?>
</div>

<?php
if ( $settings['section_btn'] == 'yes' && ! empty( $settings['section_btn_txt'] ) ) : ?>
    <div class="text-center">
        <a href="<?php echo esc_url( $settings['section_btn_url'] ); ?>" class="action_btn all_doc_btn wow fadeinUp">
			<?php echo esc_html( $settings['section_btn_txt'] ) ?><i class="<?php echo ezd_arrow() ?>"></i>
        </a>
    </div>
<?php endif; ?>