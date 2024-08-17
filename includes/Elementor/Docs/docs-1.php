<?php
$opt                    = get_option( 'eazydocs_settings' );
$topics_count           = $opt['topics_count'] ?? '1';
$topics                 = $opt['topics_text'] ?? esc_html__( 'Topics', 'eazydocs' );
$private_doc_mode       = $opt['private_doc_mode'] ?? '';
$private_doc_login_page = $opt['private_doc_login_page'] ?? '';
$ppp_column             = ! empty( $settings['ppp_column'] ) ? $settings['ppp_column'] : '3';

// Child docs per page
$layout = 'grid';

// Check pro plugin class exists
if ( ezd_is_premium() ) {
	$layout = $opt['docs-archive-layout'] ?? $layout; // id of field
}
?>

<div class="eazydocs_shortcode">
    <div class="ezd-grid ezd-column-<?php echo esc_attr( $ppp_column ); ?>">
		<?php
		$exclude_id = $doc_exclude ?? '';

		$parent_args = new WP_Query( [
			'post_type'      => 'docs',
			'posts_per_page' => $doc_number,
			'post_status'    => array( 'publish', 'private' ),
			'orderby'        => 'menu_order',
			'order'          => $doc_order ?? 'ASC',
			'post_parent'    => 0,
			'post__not_in'   => $exclude_id,
		] );

		// arrange the docs
		if ( $parent_args->have_posts() ) :
			while ( $parent_args->have_posts() ) : $parent_args->the_post();
				$sections = get_children( [
					'post_parent' => get_the_ID(),
					'post_type'   => 'docs',
					'numberposts' => 4,
					'post_status' => array( 'publish', 'private' ),
					'orderby'     => 'menu_order',
					'order'       => 'ASC',
				] );

				global $post;
				$get_child_docs = get_pages( array(
					'child_of'    => get_the_ID(),
					'post_type'   => 'docs',
					'post_status' => array( 'publish', 'private' ),
				) );
				$private_bg     = get_post_status() == 'private' ? 'bg-warning' : '';
				$private_bg_op  = get_post_status() == 'private' ? 'style="--bs-bg-opacity: .4;"' : '';
				$protected_bg   = ! empty( $post->post_password ) ? 'bg-dark' : '';
				?>
                <div class="ezd-col-width">
                    <div class="categories_guide_item <?php echo esc_attr( $private_bg . $protected_bg ); ?> wow fadeInUp" <?php echo wp_kses_post($private_bg_op); ?>>
						<?php
						if ( get_post_status() == 'private' ) {
							$pd_txt = esc_attr__( 'Private Doc', 'eazydocs' );
							echo '<div class="private" title="' . $pd_txt . '"><i class="icon_lock"></i></div>';
						}
						if ( ! empty( $post->post_password ) ) {
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
                            <a class="doc_tag_title" href="<?php the_permalink(); ?>">
                                <h4 class="title ezd_item_title"> <?php the_title(); ?> </h4>
                                <span class="ezd-badge">
							<?php echo count( $get_child_docs );
							esc_html_e( ' Topics', 'eazydocs' ); ?>
						</span>
                            </a>
                        </div>
						<?php
						if ( $sections ) :
							?>
                            <ul class="ezd-list-unstyled article_list">
								<?php
								foreach ( $sections as $section ) :
									?>
                                    <li>
                                        <a href="<?php echo get_permalink( $section ); ?>" class="ezd_item_list_title">
											<?php echo wp_kses_post( $section->post_title ); ?>
                                        </a>
                                    </li>
								<?php
								endforeach;
								?>
                            </ul>
                            <a href="<?php the_permalink(); ?>" class="doc_border_btn ezd_btn">
								<?php echo esc_html( $read_more ); ?>
                                <i class="arrow_right"></i>
                            </a>
						    <?php
						endif;
						?>
                    </div>
                </div>
			<?php
			endwhile;
		endif;
		?>

    </div>
</div>