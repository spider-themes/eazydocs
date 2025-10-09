<?php
/**
 * Cannot access directly.
 */
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}
?>

<section class="doc_tag_area" id="Arrow_slides-<?php echo esc_attr( $this->get_id() ) ?>">
    <div class="tabs_sliders">
        <?php ezd_render_scroller_btn( 'left' ); ?>
        <ul class="nav nav-tabs doc_tag ezd-tab-menu slide_nav_tabs ezd-list-unstyled">
			<?php
			$widget_id = $this->get_id();

			if ( $settings['is_custom_order'] == 'yes' && ! empty( $settings['docs'] ) ) {
				$custom_docs = ! empty( $settings['docs'] ) ? $settings['docs'] : '';
				$i           = 0;
				foreach ( $custom_docs as $doc_item ) {
					$doc_id = $doc_item['doc'];
					// Active Doc
					if ( ! empty( $settings['active_doc'] ) ) {
						$active = $doc_id == $settings['active_doc'] ? ' active' : '';
					} else {
						$active = ( $i == 0 ) ? ' active' : '';
					}
					$doc_name        = explode( ' ', get_the_title( $doc_id ) );
					?>
                    <li class="nav-item">
                        <a data-rel="<?php $this->tab_id_format( $doc_id, 'doc'); ?>" class="nav-link ezd_tab_title<?php echo esc_attr( $active ) ?>">
                            <i class="icon_document_alt"></i>
							<?php
							if ( $settings['is_tab_title_first_word'] == 'yes' ) {
								echo wp_kses_post( $doc_name[0] );
							} else {
								echo wp_kses_post( get_the_title( $doc_id ) );
							}
							?>
                        </a>
                    </li>
					<?php
					++ $i;
				}
			} else {
				if ( $parent_docs ) {
					foreach ( $parent_docs as $i => $doc ) {
						// Active Doc
						if ( ! empty( $settings['active_doc'] ) ) {
							$active = $doc->ID == $settings['active_doc'] ? ' active' : '';
						} else {
							$active = ( $i == 0 ) ? ' active' : '';
						}
						$doc_name        = explode( ' ', $doc->post_title );
						?>
                        <li class="nav-item">
                            <a data-rel="<?php $this->tab_id_format( $doc->ID, 'doc'); ?>"
                               class="nav-link ezd_tab_title<?php echo esc_attr( $active ) ?>">
								<?php
								if ( $settings['is_tab_title_first_word'] == 'yes' ) {
									echo wp_kses_post( $doc_name[0] );
								} else {
									echo wp_kses_post( $doc->post_title );
								}
								?>
                            </a>
                        </li>
						<?php
					}
				}
			}
			?>
        </ul>
        <?php ezd_render_scroller_btn( 'right' ); ?>
    </div>
    <div class="ezd-tab-content">
		<?php
		$is_masonry     		= $settings['masonry'] ?? '';
		$masonry_layout 		= $is_masonry == 'yes' ? 'ezd-column-3 ezd-masonry' : 'ezd-grid ezd-grid-cols-12 ';
		$masonry_attr   		= $is_masonry == 'yes' ? 'ezd-massonry-col="3"' : '';

		foreach ( $docs as $i => $main_doc ) :
			// Active Doc
			if ( ! empty( $settings['active_doc'] ) ) {
				$active = $main_doc['doc']->ID == $settings['active_doc'] ? 'active' : '';
			} else {
				$active = ( $i == 0 ) ? 'active' : '';
			}
			?>
            <div class="doc_tab_pane ezd-tab-box <?php echo esc_attr( $active ); ?>" id="<?php $this->tab_id_format( $main_doc['doc']->ID, 'doc'); ?>">
                <div class="<?php echo esc_attr( $masonry_layout ); ?>" <?php echo wp_kses_post( $masonry_attr ); ?>>
					<?php
					if ( ! empty( $main_doc['sections'] ) ) :
						foreach ( $main_doc['sections'] as $section ) :
							?>
                            <div class="ezd-grid-column-full ezd-sm-col-6 ezd-lg-col-4">
                                <div class="doc_tag_item">
									<?php 
									if ( ! empty( $section->post_title ) ) : 
										?>
                                        <div class="doc_tag_title">
                                            <h4 class="ezd_item_title">
												<?php echo wp_kses_post( $section->post_title ); ?>
											</h4>
                                            <div class="line"></div>
                                        </div>
										<?php 
									endif;
									
									$doc_items = ezd_get_doc_items( $section->ID, $settings );

									if ( ! empty( $doc_items ) ) : 
										ezd_render_doc_items_list( $doc_items, 'ezd-list-unstyled tag_list' );
									endif;

									if ( ! empty( $settings['read_more'] ) ) : 
										ezd_render_read_more_btn( get_permalink( $section->ID ), $settings['read_more'], 'learn_btn ezd_btn', '<i class="' . ezd_arrow() . '"></i>' );
									endif; 
									?>
                                </div>
                            </div>
						<?php
						endforeach;
					endif;
					?>
                </div>
            </div>
		<?php
		endforeach;
		?>
    </div>
</section>

<?php
if ( $is_masonry == 'yes' ) {
    ezd_render_masonry_script();
}
?>