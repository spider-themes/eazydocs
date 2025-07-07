<section class="h_doc_documentation_area" id="Arrow_slides-<?php echo esc_attr( $this->get_id() ) ?>">
    <div class="tabs_sliders">
        <span class="scroller-btn left"><i class="arrow_carrot-left"></i></span>
        <ul class="nav nav-tabs documentation_tab ezd-tab-menu slide_nav_tabs ezd-list-unstyled">
			<?php
			$slug_type = $settings['docs_slug_format'] ?? '';
			$widget_id = $this->get_id();
			if ( $settings['is_custom_order'] == 'yes' && ! empty( $settings['docs'] ) ) {
				$custom_docs = $settings['docs'];
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
						$atts = "href='#doc2-{$post_title_slug}'";
					} else {
						$atts = "href='#doc2-{$widget_id}-{$doc_id}'";
					}
					?>
                    <li class="nav-item">
                        <a data-rel="<?php echo esc_attr( $post_title_slug ); ?>"
                           class="nav-link ezd_tab_title<?php echo esc_attr( $active ) ?>">
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
							$href = "href='#doc2-{$doc->post_name}'";
						} else {
							$href = "href='#doc2-{$widget_id}-{$doc->ID}'";
						}

						?>
                        <li class="nav-item">
                            <a data-rel="doc2-<?php echo esc_attr( $this->get_id() ) ?>-<?php echo esc_attr( $doc->post_name ); ?>"
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
					endforeach;
				endif;
			}
			?>
        </ul>
        <span class="scroller-btn right"><i class="arrow_carrot-right"></i></span>
    </div>
    <div class="ezd-tab-content">
		<?php
		foreach ( $docs as $i => $main_doc ) :
			// Active Doc
			if ( ! empty( $settings['active_doc'] ) ) {
				$active = $main_doc['doc']->ID == $settings['active_doc'] ? ' active' : '';
			} else {
				$active = ( $i == 0 ) ? ' active' : '';
			}

			if ( $slug_type == 1 ) {
				$doc_id = $main_doc['doc']->post_name;
			} else {
				$doc_id = "{$widget_id}-{$main_doc['doc']->ID}";
			}
			?>
            <div class="documentation_tab_pane ezd-tab-box<?php echo esc_attr( $active ); ?>"
                 id="doc2-<?php echo esc_attr( $this->get_id() ) ?>-<?php echo esc_attr( $doc_id ); ?>">
                <div class="ezd-grid ezd-grid-cols-12">
                    <div class="ezd-lg-col-4 ezd-grid-column-full">
                        <div class="documentation_text">
							<?php if ( has_post_thumbnail( $main_doc['doc']->ID ) ) : ?>
								<?php echo get_the_post_thumbnail( $main_doc['doc']->ID, 'full', array( 'class' => 'doc-logo' ) ); ?>
							<?php endif; ?>

							<?php if ( ! empty( $main_doc['doc']->post_title ) ) : ?>
                                <h4 class="ezd_item_parent_title"><?php echo wp_kses_post( $main_doc['doc']->post_title ); ?>
                                </h4>
							<?php endif; ?>

                            <p class="ezd_item_content">
								<?php
								if ( strlen( trim( $main_doc['doc']->post_excerpt ) ) != 0 ) {
									echo wp_kses_post( wp_trim_words( $main_doc['doc']->post_excerpt, $settings['main_doc_excerpt'], '' ) );
								} else {
									echo wp_kses_post( wp_trim_words( $main_doc['doc']->post_content, $settings['main_doc_excerpt'], '' ) );
								}
								?>
                            </p>


                            <a href="<?php the_permalink( $main_doc['doc']->ID ); ?>" class="learn_btn ezd_btn">
								<?php echo esc_html( $settings['read_more'] ); ?> <i class="<?php ezd_arrow() ?>"></i>
                            </a>
                        </div>
                    </div>
                    <div class="ezd-lg-col-8 ezd-grid-column-full">
                        <div class="d-items">
							<?php
							foreach ( $main_doc['sections'] as $section ) :
								?>
                                <div class="media documentation_item">
                                    <div class="icon bs-sm">
										<?php

										if ( has_post_thumbnail( $section->ID ) ) {
											echo get_the_post_thumbnail( $section->ID, 'full' );
										} else {
											$default_icon = esc_url( plugins_url( 'images/folder.png', __FILE__ ) );
											echo '<img src="' . esc_url( $default_icon ) . '" alt="' . esc_attr( $section->post_title ) . '">';
										}
										?>
                                    </div>
                                    <div class="media-body">
                                        <a href="<?php the_permalink( $section->ID ); ?>">
                                            <h5 class="title ezd_item_title">
												<?php echo wp_kses_post( $section->post_title ); ?>
                                            </h5>
                                        </a>
                                        <p class="ezd_item_content">
											<?php
											if ( strlen( trim( $section->post_excerpt ) ) != 0 ) {
												echo wp_kses_post( wp_trim_words( $section->post_excerpt, $settings['doc_sec_excerpt'], '' ) );
											} else {
												echo wp_kses_post( wp_trim_words( $section->post_content, $settings['doc_sec_excerpt'], '' ) );
											}
											?>
                                        </p>
                                    </div>
                                </div>
							<?php
							endforeach;
							?>
                        </div>
                    </div>
                </div>
            </div>
		<?php
		endforeach;
		?>
    </div>
</section>