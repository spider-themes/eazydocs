<?php
/**
 * Cannot access directly.
 */
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

$is_masonry     = $settings['masonry'] ?? '';
$masonry_layout = $is_masonry == 'yes' ? 'ezd-column-3 ezd-masonry' : 'ezd-grid ezd-grid-cols-12';
$masonry_attr   = $is_masonry == 'yes' ? 'ezd-massonry-col="3"' : '';
?>
<div class="question_menu docs3" id="Arrow_slides-<?php echo esc_attr( $this->get_id() ) ?>">
    <div class="tabs_sliders">
        <?php ezd_render_scroller_btn( 'left' ); ?>
        <ul class="nav nav-tabs mb-5 ezd-tab-menu slide_nav_tabs">
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
                        <a data-rel="<?php $this->tab_id_format( $doc_id, 'doc3'); ?>" class="nav-link ezd_tab_title<?php echo esc_attr( $active ) ?>">
							<?php
							echo get_the_post_thumbnail( $doc_id, 'docy_16x16' );
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
				if ( $parent_docs ) :
					foreach ( $parent_docs as $i => $doc ) :
						// Active Doc
						if ( ! empty( $settings['active_doc'] ) ) {
							$active = $doc->ID == $settings['active_doc'] ? ' active' : '';
						} else {
							$active = ( $i == 0 ) ? ' active' : '';
						}
						$doc_name = explode( ' ', $doc->post_title );
						?>
                        <li class="nav-item">
                            <a data-rel="<?php $this->tab_id_format( $doc->ID, 'doc3'); ?>"
                               class="nav-link ezd_tab_title<?php echo esc_attr( $active ) ?>">
								<?php
								echo get_the_post_thumbnail( $doc->ID, 'docy_16x16' );
								if ( $settings['is_tab_title_first_word'] == 'yes' ) {
									echo wp_kses_post( $doc_name[0] );
								} else {
									echo wp_kses_post( $doc->post_title );
								}
								?>
                            </a>
                        </li>
					<?php
					endforeach;
				endif;
			}
			?>
        </ul>
        <?php ezd_render_scroller_btn( 'right' ); ?>
    </div>
    <div class="topic_list_inner">
        <div class="ezd-tab-content">
			<?php
			if ( ! empty( $docs ) ) :
			foreach ( $docs

			as $i => $main_doc ) :
			// Active Doc
			if ( ! empty( $settings['active_doc'] ) ) {
				$active = $main_doc['doc']->ID == $settings['active_doc'] ? 'active' : '';
			} else {
				$active = ( $i == 0 ) ? 'active' : '';
			}
			?>
            <div class="doc_tab_pane ezd-tab-box <?php echo esc_attr( $active ); ?>"
                 id="<?php $this->tab_id_format( $main_doc['doc']->ID, 'doc3'); ?>">
                <div class="<?php echo esc_attr( $masonry_layout ); ?>" <?php echo wp_kses_post( $masonry_attr ); ?>>
				<?php
				if ( ! empty( $main_doc['sections'] ) ) :
					foreach ( $main_doc['sections'] as $section ) :
						?>
                        <div class="ezd-lg-col-4 ezd-md-col-6 ezd-grid-column-full">
                            <div class="topic_list_item">
								<?php if ( ! empty( $section->post_title ) ) : ?>
                                    <h4 class="ezd_item_title"><?php echo wp_kses_post( $section->post_title ); ?></h4>
								<?php endif; ?>
                                <ul class="navbar-nav">
									<?php
									$doc_items = ezd_get_doc_items( $section->ID, $settings );
									ezd_render_doc_items_list( $doc_items, 'navbar-nav', 'ezd_item_list_title' );
									?>
                                </ul>
								<?php
								if ( ! empty( $settings['read_more'] ) ) :
									ezd_render_read_more_btn( get_permalink( $section->ID ), $settings['read_more'], 'text_btn dark_btn ezd_btn', '<i class="' . ezd_arrow() . '"></i>' );
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
		endif;
		?>
    </div>
</div>
</div>

<script>
    ;
    (function ($) {
        'use strict';

        $(document).ready(function () {

            function ezd_docs4_masonry() {
				$('.ezd-masonry').each(function () {
					var $masonry = $(this);
					var masonryCols = $masonry.attr('ezd-massonry-col');
					var masonryColumns = parseInt(masonryCols);

					if ($(window).width() <= 1024) {
						masonryColumns = 2;
					}
					if ($(window).width() <= 768) {
						masonryColumns = 1;
					}

					var count = 0;
					var content = $masonry.children();

					var $columnsContainer = $('<div class="ezd-masonry-columns"></div>');

					content.each(function (index) {
						count = count + 1;
						$(this).addClass('ezd-masonry-sort-' + count);

						if (count === masonryColumns) {
							count = 0;
						}
					});

					for (var i = 1; i <= masonryColumns; i++) {
						$columnsContainer.append('<div class="ezd-masonry-' + i + '"></div>');
					}

					for (var i = 1; i <= masonryColumns; i++) {
						$masonry.find('.ezd-masonry-sort-' + i).appendTo($columnsContainer.find('.ezd-masonry-' + i));
					}

					$masonry.empty().append($columnsContainer);
				});
			}
            ezd_docs4_masonry();

        });
    })(jQuery);
</script>