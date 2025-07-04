<!DOCTYPE html>
<html <?php language_attributes(); ?>>

<head>
    <!-- Charset Meta -->
    <meta charset="<?php bloginfo( 'charset' ); ?>">
    <!-- For IE -->
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <!-- For Responsive Device -->
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

	<?php wp_head(); ?>
</head>

<body <?php body_class();
if ( function_exists( 'docy_has_scrollspy' ) ) {
	docy_has_scrollspy();
} ?>>
<?php
if ( function_exists( 'wp_body_open' ) ) {
	wp_body_open();
}

wp_enqueue_script( 'eazydocs-onepage' );

$widget_sidebar = ezd_get_opt( 'is_widget_sidebar' );

global $post;
$post_slug = $post->post_name;
$post_id   = get_page_by_path( $post_slug, OBJECT, array( 'docs' ) );

$children = wp_list_pages( array(
	'title_li'  => '',
	'order'     => 'menu_order',
	'child_of'  => $post_id->ID ?? 0,
	'echo'      => false,
	'post_type' => 'docs',
	'walker'    => new Walker_Onepage_Fullscren(),
	'depth'     => 4
) );
?>
<section class="documentation_area_sticky doc_documentation_area onepage_doc_area fullscreen-layout" id="sticky_doc">
    <div class="overlay_bg"></div>
    <div class="ezd-container-fluid p-lg-5">
        <div class="ezd-grid ezd-grid-cols-12 doc-container">
            <div class="ezd-xl-col-3 ezd-lg-col-3 ezd-grid-column-full doc_mobile_menu doc-sidebar sticky-top ezd-sticky-lg-top left-column">
                <aside class=" one-page-docs-sidebar-wrap">
                    <div class="open_icon" id="mobile-left-toggle">
                        <i class="arrow_carrot-right"></i>
                        <i class="arrow_carrot-left"></i>
                    </div>

					<?php
					echo get_the_post_thumbnail( $post_id->ID ?? 0, 'full' );
					?>
                    <h3 class="doc-title">
						<?php echo get_post_field( 'post_title', $post_id->ID ?? 0, 'display' ); ?>
                    </h3>
					<?php

					if ( $children ) :
						?>
                        <nav class="scroll op-docs-sidebar">
                            <ul class="ezd-list-unstyled nav-sidebar fullscreen-layout-onepage-sidebar doc-nav one-page-doc-nav-wrap" id="eazydocs-toc">
								<?php
								echo wp_kses_post(wp_list_pages( array(
									'title_li'  => '',
									'order'     => 'menu_order',
									'child_of'  => $post_id->ID ?? 0,
									'echo'      => false,
									'post_type' => 'docs',
									'walker'    => new Walker_Onepage_Fullscren(),
									'depth'     => 4
								) ));
								?>
                            </ul>
                        </nav>
					<?php
					endif;

					$parent_doc_id_left = get_the_ID();
					$content_type_left  = get_post_meta( $parent_doc_id_left, 'ezd_doc_content_type', true );
					$ezd_shortcode_left = get_post_meta( $parent_doc_id_left, 'ezd_doc_left_sidebar', true );
					$is_valid_post_id   = is_null( get_post( $ezd_shortcode_left ) ) ? 'No' : 'Yes';

					if ( $content_type_left == 'string_data' && ! empty ( $ezd_shortcode_left ) ) {
						echo do_shortcode( html_entity_decode( $ezd_shortcode_left ) );
					} else {
						if ( $content_type_left == 'widget_data' && ! empty( $is_valid_post_id ) ) {
							$wp_blocks = new WP_Query( [
								'post_type' => 'wp_block',
								'p'         => $ezd_shortcode_left
							] );
							if ( $wp_blocks->have_posts() ) {
								while ( $wp_blocks->have_posts() ) : $wp_blocks->the_post();
									the_content();
								endwhile;
								wp_reset_postdata();
							}
						}
					}

					?>
                </aside>
            </div>
            <div class="ezd-xl-col-7 ezd-lg-col-6 ezd-md-col-9 ezd-grid-column-full middle-content">
                <div class="documentation_info ezd-container" id="post">
					<?php
					$sections = get_children( array(
						'post_parent'    => $post_id->ID ?? 0,
						'post_type'      => 'docs',
						'post_status'    => 'publish',
						'orderby'        => 'menu_order',
						'order'          => 'ASC',
						'posts_per_page' => - 1,
					) );

					$i          = 0;
					$sec_serial = 0;
					foreach ( $sections as $doc_item ) {
						$child_sections = get_children( array(
							'post_parent'    => $doc_item->ID,
							'post_type'      => 'docs',
							'post_status'    => 'publish',
							'orderby'        => 'menu_order',
							'order'          => 'ASC',
							'posts_per_page' => - 1,
						) );
						$sec_serial ++;
						$get_title = sanitize_title( $doc_item->post_title );
						if ( preg_match( '#[0-9]#', $get_title ) ) {
							$get_title = 'ezd-' . sanitize_title( $doc_item->post_title );
						}
						?>
                        <article class="documentation_body doc-section onepage-doc-sec" id="<?php echo esc_attr( $get_title ) ?>" itemscope itemtype="http://schema.org/Article">
							<?php if ( ! empty( $doc_item->post_title ) ) : ?>
                                <div class="shortcode_title doc-sec-title">
                                    <h2> <?php echo wp_kses_post($sec_serial . '. ' . $doc_item->post_title); ?> </h2>
                                </div>
							<?php endif; ?>
                            <div class="doc-content">
								<?php
								if ( did_action( 'elementor/loaded' ) ) {
									$parent_content = \Elementor\Plugin::instance()->frontend->get_builder_content( $doc_item->ID );
									echo ! empty( $parent_content ) ? $parent_content : apply_filters( 'the_content', $doc_item->post_content );
								} else {
									echo apply_filters( 'the_content', $doc_item->post_content );
								}
								?>
                            </div>
							<?php
							$child_serial = 0;
							foreach ( $child_sections as $child_section ) :
								$child_serial ++;
								$get_child_title = sanitize_title( $child_section->post_title );
								if ( preg_match( '#[0-9]#', $get_child_title ) ) {
									$get_child_title = 'ezd-' . sanitize_title( $child_section->post_title );
								}
								?>
                                <div class="child-doc onepage-doc-sec" id="<?php echo esc_attr( $get_child_title ) ?>">
                                    <div class="shortcode_title depth-two">
                                        <h3>
											<?php
											echo esc_html($sec_serial . '.' . $child_serial . ' ');
											echo wp_kses_post($child_section->post_title);
											?>
                                        </h3>
                                    </div>
                                    <div class="doc-content">
										<?php
										if ( did_action( 'elementor/loaded' ) ) {
											$child_content = \Elementor\Plugin::instance()->frontend->get_builder_content( $child_section->ID );
											echo ! empty( $child_content ) ? $child_content : apply_filters( 'the_content', $child_section->post_content );
										} else {
											echo apply_filters( 'the_content', $child_section->post_content );
										}
										?>
                                    </div>
                                </div>
								<?php
								$last_depth        = get_children( array(
									'post_parent'    => $child_section->ID,
									'post_type'      => 'docs',
									'post_status'    => 'publish',
									'orderby'        => 'menu_order',
									'order'          => 'ASC',
									'posts_per_page' => - 1,
								) );
								$last_depth_serial = 0;
								foreach ( $last_depth as $last_depth_doc ) :
									$last_depth_serial ++;
									$get_last_child_title = sanitize_title( $last_depth_doc->post_title );
									if ( preg_match( '#[0-9]#', $get_last_child_title ) ) {
										$get_last_child_title = 'ezd-' . sanitize_title( $last_depth_doc->post_title );
									}
									?>
                                    <div class="child-doc onepage-doc-sec" id="<?php echo esc_attr( $get_last_child_title ) ?>">
                                        <div class="shortcode_title depth-three">
                                            <h4>
												<?php
												echo esc_html($sec_serial . '.' . $child_serial . '.' . $last_depth_serial . ' ');
												echo wp_kses_post($last_depth_doc->post_title);
												?>
                                            </h4>
                                        </div>
                                        <div class="doc-content">
											<?php
											if ( did_action( 'elementor/loaded' ) ) {
												$child_content = \Elementor\Plugin::instance()->frontend->get_builder_content( $last_depth_doc->ID );
												echo ! empty( $child_content ) ? $child_content : apply_filters( 'the_content', $last_depth_doc->post_content );
											} else {
												echo apply_filters( 'the_content', $last_depth_doc->post_content );
											}
											?>
                                        </div>
                                    </div>

								<?php								
								$last_depth_fiveth = get_children( array(
									'post_parent'    => $last_depth_doc->ID,
									'post_type'      => 'docs',
									'post_status'    => 'publish',
									'orderby'        => 'menu_order',
									'order'          => 'ASC',
									'posts_per_page' => - 1,
								) );
								$last_depth_serial = 0;
								foreach ( $last_depth_fiveth as $last_depth_doc ) :
									$last_depth_serial ++;
									$get_last_child_title = sanitize_title( $last_depth_doc->post_title );
									if ( preg_match( '#[0-9]#', $get_last_child_title ) ) {
										$get_last_child_title = 'ezd-' . sanitize_title( $last_depth_doc->post_title );
									}
									?>
                                    <div class="child-doc onepage-doc-sec" id="<?php echo esc_attr( $get_last_child_title ) ?>">
                                        <div class="shortcode_title depth-three">
                                            <h4>
												<?php
												echo esc_html($sec_serial . '.' . $child_serial . '.' . $last_depth_serial . ' ');
												echo wp_kses_post($last_depth_doc->post_title);
												?>
                                            </h4>
                                        </div>
                                        <div class="doc-content">
											<?php
											if ( did_action( 'elementor/loaded' ) ) {
												$child_content = \Elementor\Plugin::instance()->frontend->get_builder_content( $last_depth_doc->ID );
												echo ! empty( $child_content ) ? $child_content : apply_filters( 'the_content', $last_depth_doc->post_content );
											} else {
												echo apply_filters( 'the_content', $last_depth_doc->post_content );
											}
											?>
                                        </div>
                                    </div>
								<?php
								endforeach;
							endforeach;
							
							endforeach;
							?>
                        </article>
						<?php
						++ $i;
					}
					?>
                </div>
            </div>

	        <?php
	        /**
             * OnePage Right Sidebar Template
             */
	        eazydocs_get_template_part( 'onepage/right-sidebar' );
	        ?>

        </div>
    </div>
</section>

<?php wp_footer(); ?>
</body>

</html>