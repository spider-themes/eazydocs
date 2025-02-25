<?php
$opt                    = get_option( 'eazydocs_settings' );
$topics                 = $opt['topics_text'] ?? esc_html__( 'Topics', 'eazydocs' );
$private_doc_mode       = $opt['private_doc_mode'] ?? '';
$private_doc_login_page = $opt['private_doc_login_page'] ?? '';
$is_subscription 		= $opt['subscriptions'] ?? false;
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

				$private_bg    = $main_doc['doc']->post_status == 'private' ? 'bg-warning' : '';
				$private_bg_op = $main_doc['doc']->post_status == 'private' ? 'style="--bs-bg-opacity: .4;"' : '';
				$protected_bg  = ! empty( $main_doc['doc']->post_password ) ? 'bg-dark' : '';

				$col_wrapper = $i == 1;
				?>

                <div class="ezd-col-width">
                    <div class="categories_guide_item <?php echo esc_attr( $private_bg . $protected_bg ); ?> wow fadeInUp" <?php echo wp_kses_post( $private_bg_op ); ?>>
						<?php
						if ( $main_doc['doc']->post_status == 'private' ) {
							$pd_txt = esc_attr__( 'Private Doc', 'eazydocs' );
							echo '<div class="private" title="' . esc_attr( $pd_txt ) . '"><i class="icon_lock"></i></div>';

						}
						if ( ! empty( $main_doc['doc']->post_password ) ) {
							?>
                            <div class="private" title="Password Protected Doc">
                                <svg width="50px" height="50px" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg" fill="#4e5668">
                                    <g>
                                        <path fill="none" d="M0 0h24v24H0z"/>
                                        <path d="M18 8h2a1 1 0 0 1 1 1v12a1 1 0 0 1-1 1H4a1 1 0 0 1-1-1V9a1 1 0 0 1 1-1h2V7a6 6 0 1 1 12 0v1zm-2 0V7a4 4 0 1 0-8 0v1h8zm-5 6v2h2v-2h-2zm-4 0v2h2v-2H7zm8 0v2h2v-2h-2z"/>
                                    </g>
                                </svg>
                            </div>
							<?php
						}
						?>
                        <div class="doc-top ezd-d-flex ezd-align-items-start">
                            <a class="doc_tag_title" href="<?php echo get_permalink( $main_doc['doc']->ID ); ?>">
								<?php if ( ! empty( $main_doc['doc']->post_title ) ) : ?>
                                    <h4 class="title">
										<?php echo wp_kses_post( $main_doc['doc']->post_title ); ?>
                                    </h4>
								<?php endif;
								if ( $show_topic == true ) : ?>
                                    <span class="ezd-badge">
                                        <?php echo count( $doc_counter ) > 0 ? count( $doc_counter ) : ''; ?>
                                        <?php echo esc_html( $topic_label ); ?>
                                    </span>
								<?php endif; ?>
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
                                        <a href="<?php echo get_permalink( $item->ID ); ?>">
											<?php echo esc_html( $item->post_title ); ?>
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
							<a href="<?php echo get_permalink( $main_doc['doc']->ID ); ?>" class="doc_border_btn">
								<?php echo esc_html( $more ); ?> <i class="arrow_right"></i>
							</a>

							<?php
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