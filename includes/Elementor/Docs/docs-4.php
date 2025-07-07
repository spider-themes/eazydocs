<?php
$is_masonry     = $settings['masonry'] ?? '';
$masonry_layout = $is_masonry == 'yes' ? 'ezd-column-3 ezd-masonry' : 'ezd-grid ezd-grid-cols-12';
$masonry_attr   = $is_masonry == 'yes' ? 'ezd-massonry-col="3"' : '';
?>
<div class="question_menu docs3" id="Arrow_slides-<?php echo esc_attr( $this->get_id() ) ?>">
    <div class="tabs_sliders">
        <span class="scroller-btn left"><i class="arrow_carrot-left"></i></span>
        <ul class="nav nav-tabs mb-5 ezd-tab-menu slide_nav_tabs">
			<?php
			$slug_type = $settings['docs_slug_format'] ?? '';
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
					$post_title_slug = get_post_field( 'post_name', $doc_id );
					$doc_name        = explode( ' ', get_the_title( $doc_id ) );

					if ( $slug_type == 1 ) {
						$atts = "href='#doc3-{$post_title_slug}'";
					} else {
						$atts = "href='#doc3-{$widget_id}-{$doc_id}'";
					}

					?>
                    <li class="nav-item">
                        <a data-rel="<?php echo esc_attr($post_title_slug); ?>"
                           class="nav-link ezd_tab_title<?php echo esc_attr( $active ) ?>">
							<?php
							echo get_the_post_thumbnail( $doc_id, 'docy_16x16' );
							if ( $settings['is_tab_title_first_word'] == 'yes' ) {
								echo wp_kses_post( $doc_name[0] );
							} else {
								echo wp_kses_post( $doc_item->post_title );
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
						if ( $slug_type == 1 ) {
							$href = "href='#doc3-{$doc->post_name}'";
						} else {
							$href = "href='#doc3-{$widget_id}-{$doc->ID}'";
						}
						?>
                        <li class="nav-item">
                            <a data-rel="doc3-<?php echo esc_attr( $this->get_id() ) ?>-<?php echo esc_attr($doc->post_name); ?>"
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
        <span class="scroller-btn right"><i class="arrow_carrot-right"></i></span>
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

			if ( $slug_type == 1 ) {
				$doc_id = $main_doc['doc']->post_name;
			} else {
				$doc_id = "{$widget_id}-{$main_doc['doc']->ID}";
			}
			?>
            <div class="doc_tab_pane ezd-tab-box <?php echo esc_attr( $active ); ?>"
                 id="doc3-<?php echo esc_attr( $this->get_id() ) ?>-<?php echo esc_attr( $doc_id ) ?>">
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
									$doc_items = get_children( array(
										'post_parent'    => $section->ID,
										'post_type'      => 'docs',
										'post_status'    => 'publish',
										'orderby'        => 'menu_order',
										'order'          => 'ASC',
										'posts_per_page' => ! empty( $settings['ppp_doc_items'] ) ? $settings['ppp_doc_items'] : - 1,
									) );
									foreach ( $doc_items as $doc_item ) :
										?>
                                        <li>
                                            <a href="<?php the_permalink( $doc_item->ID ) ?>" class="ezd_item_list_title">
												<?php echo wp_kses_post( $doc_item->post_title ) ?>
                                            </a>
                                        </li>
									<?php
									endforeach;
									?>
                                </ul>
								<?php
								if ( ! empty( $settings['read_more'] ) ) : ?>
                                    <a href="<?php the_permalink( $section->ID ); ?>" class="text_btn dark_btn ezd_btn">
										<?php echo esc_html( $settings['read_more'] ) ?>
                                        <i class="<?php ezd_arrow() ?>"></i>
                                    </a>
								    <?php
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