<?php
$private_doc_mode       = ezd_is_premium() ? ezd_get_opt( 'private_doc_mode' ) : 'none';
$show_badge             = $show_badge ?? true; // Status badge toggle (defaults on).
$show_lock              = $show_lock ?? true;  // Lock icon toggle (defaults on).
$is_subscription 		= ezd_get_opt( 'subscriptions', false );
$is_btn_show 			= ezd_get_opt('docs-view-all-btn');
$is_masonry             = '';
// Check pro plugin class exists
if ( ezd_is_premium() ) {
	$is_masonry = $layout == 'masonry' ? ' ezd-masonry' : 'ezd-grid';
	if ( empty ( $col ) ) {
		$col = apply_filters( 'before_docs_column_wrapper', $col );
	}
}

if ( $docs ) :
	?>
    <div class="eazydocs_shortcode">
        <div <?php echo esc_attr( do_action( 'eazydocs_masonry_wrap', $layout, $col ) ); ?> class="ezd-grid ezd-masonry ezd-column-<?php echo esc_attr( $col .' '. $is_masonry ); ?>">
			<?php
			$i = 1;
			foreach ( $docs as $main_doc ) :
				$doc_counter = get_pages( [
					'child_of'    => $main_doc['doc']->ID,
					'post_type'   => 'docs',
					'orderby'     => 'menu_order',
					'order'       => 'asc',
					'post_status' => array( 'publish', 'private' )
				] );

				global $post;

				$col_wrapper = $i == 1;
				?>

                <div class="ezd-col-width">
                    <div class="categories_guide_item <?php echo esc_attr( ezd_doc_status_classes( $main_doc['doc']->ID ) ); ?> wow fadeInUp">
						<?php
						// Shared private / password-protected corner lock (honours the
						// global Settings → Restricted Docs → Card Design toggles).
						ezd_render_doc_indicators( $main_doc['doc']->ID, $show_lock );
						?>
                        <div class="doc-top ezd-d-flex ezd-align-items-start<?php echo $img_size === 'full' ? ' ezd-img-full' : ''; ?>">
							<?php 
							echo wp_get_attachment_image( get_post_thumbnail_id( $main_doc['doc']->ID ), $img_size );
							 ?>
                            <a class="doc_tag_title" href="<?php the_permalink( $main_doc['doc']->ID ); ?>">
								<?php 
								if ( ! empty( $main_doc['doc']->post_title ) ) : 
									?>
                                    <h4 class="title">
										<?php echo wp_kses_post( $main_doc['doc']->post_title ); ?>
                                    </h4>
										<?php echo ezd_doc_status_badge( $main_doc['doc']->ID, $show_badge ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
									<?php
								endif;
								if ( $show_topic == true ) : ?>
                                    <span class="ezd-badge">
                                        <?php echo count( $doc_counter ) > 0 ? count( $doc_counter ) : '0'; ?>
                                        <?php echo esc_html( $topic_label ); ?>
                                    </span>
									<?php 
								endif; 
								?>
                            </a>
                        </div>
						<?php
						if ( $main_doc['sections'] ) :
							?>
                            <ul class="ezd-list-unstyled article_list">
								<?php
								foreach ( $main_doc['sections'] as $item ) :
									?>
                                    <li>
                                        <a href="<?php the_permalink( $item->ID ); ?>">
											<?php echo esc_html( $item->post_title ); ?>
											<?php if ( function_exists('ezdpro_badge') && ezd_is_premium() ) echo ezdpro_badge( $item->ID ); ?>
                                        </a>
                                    </li>
								    <?php
								endforeach;
								?>
                            </ul>
						    <?php
						endif;
						?>
						<div class="ezd-doc-btn-wrap <?php if ( $is_subscription == '1' ) { echo 'has-subscription'; } ?>">

							<?php
							$has_children = count( $doc_counter ) > 0;

							if ( ( ! $has_children && ! empty( $more ) && ! empty( $is_btn_show ) ) || 
								( $has_children && ! empty( $more ) ) ) :
								?>
								<a href="<?php the_permalink( $main_doc['doc']->ID ); ?>" class="doc_border_btn">
									<?php echo esc_html( $more ); ?> <i class="arrow_right"></i>
								</a>
								<?php 
							endif;

							/**
							 * Subscription
							 */
							do_action( 'eazydocs_docs_subscription', ezd_get_doc_parent_id( $main_doc['doc']->ID ), 'ezd-block-subscribe' );
							do_action( 'eazydocs_suscription_modal_form', ezd_get_doc_parent_id( $main_doc['doc']->ID ) );
							?>
						</div>

                    </div>
                </div>
			    <?php
			endforeach;
			?>
        </div>
    </div>
    <?php
endif;