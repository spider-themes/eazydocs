<?php
get_header();

wp_enqueue_script('eazydocs-onepage');
$opt                = get_option( 'eazydocs_settings' );
global $post;
$post_slug          = $post->post_name;
$post_id            = get_page_by_path($post_slug, OBJECT, array( 'docs' ) );
$walker             = new eazyDocs\Frontend\Walker_Docs();
$ezd_content        = get_the_content(get_the_ID());
$ezd_content_none   = ! empty( $ezd_content ) ? 'mt-5' : '';
$children           = wp_list_pages( array(
	'title_li'      => '',
	'order'         => 'menu_order',
	'child_of'      => $post_id->ID,
	'echo'          => false,
	'post_type'     => 'docs',
	'walker'        => new EazyDocs_Walker_Onepage(),
) );
?>
    <section class="doc_documentation_area onepage_doc_area page_wrapper" id="sticky_doc">
        <div class="overlay_bg"></div>
        <div class="container-fluid p-lg-5">
            <div class="row doc-container">
                <div class="col-xl-3 col-lg-3 doc_mobile_menu doc-sidebar sticky-top sticky-lg-top left-column">
                    <aside class="doc_left_sidebarlist one-page-docs-sidebar-wrap">
                        <div class="open_icon" id="left">
                            <i class="arrow_carrot-right"></i>
                            <i class="arrow_carrot-left"></i>
                        </div>
                        <h3 class="nav_title">
							<?php echo get_post_field( 'post_title', $post_id->ID, 'display' ); ?>
                        </h3>
						<?php
						if ( $children ) :
							?>
                            <nav class="scroll op-docs-sidebar">
                                <ul class="list-unstyled nav-sidebar default-layout-onepage-sidebar doc-nav one-page-doc-nav-wrap" id="eazydocs-toc">
									<?php
									echo wp_list_pages(array(
										'title_li' => '',
										'order' => 'menu_order',
										'child_of' => $post_id->ID,
										'echo' => false,
										'post_type' => 'docs',
										'walker' => new EazyDocs_Walker_Onepage(),
										'depth' => 3
									));
									?>
                                </ul>
                            </nav>
						<?php
						endif;
	                    $ezd_shortcode = get_the_content( $post_id->ID );
	                    $content_type = get_post_meta( get_the_ID(), 'ezd_doc_content_type', true );
	                    if( $content_type == 'string_data' ){
		                    echo html_entity_decode( $ezd_shortcode ) ?? '';
	                    } elseif( $content_type == 'shortcode' ){
		                    echo do_shortcode( html_entity_decode($ezd_shortcode) );
	                    } else {
		                    dynamic_sidebar( html_entity_decode($ezd_shortcode) );
	                    }
	                    ?>
                    </aside>

                </div>
                <div class="col-xl-7 col-lg-6 middle-content">
                    <div class="documentation_info" id="post">
						<?php
						$sections = get_children( array(
							'post_parent'    => $post_id->ID,
							'post_type'      => 'docs',
							'post_status'    => 'publish',
							'orderby'        => 'menu_order',
							'order'          => 'ASC',
							'posts_per_page' =>  -1,
						) );

						$i = 0;
						foreach ( $sections as $doc_item ) {
							$child_sections = get_children( array(
								'post_parent'    => $doc_item->ID,
								'post_type'      => 'docs',
								'post_status'    => 'publish',
								'orderby'        => 'menu_order',
								'order'          => 'ASC',
								'posts_per_page' => -1,
							));
							?>
                            <article class="documentation_body doc-section onepage-doc-sec" id="<?php echo sanitize_title($doc_item->post_title) ?>" itemscope itemtype="http://schema.org/Article">
								<?php if ( !empty($doc_item->post_title) ) : ?>
                                    <div class="shortcode_title">
                                        <h2> <?php echo esc_html($doc_item->post_title) ?> </h2>
                                    </div>
								<?php endif; ?>
                                <div class="doc-content">
									<?php
									if ( did_action( 'elementor/loaded' ) ) {
										$parent_content = \Elementor\Plugin::instance()->frontend->get_builder_content($doc_item->ID);
										echo !empty($parent_content) ? $parent_content : apply_filters('the_content', $doc_item->post_content);
									} else {
										echo apply_filters('the_content', $doc_item->post_content);
									}
									?>
                                </div>

								<?php if ( $child_sections ) : ?>
                                    <div class="articles-list mt-5">
                                        <h4> <?php esc_html_e('Articles', 'docy'); ?></h4>
                                        <ul class="article_list one-page-docs-tag-list">
											<?php
											foreach ( $child_sections as $child_section ) :
												?>
                                                <li>
                                                    <a href="#<?php echo sanitize_title($child_section->post_title) ?>">
                                                        <i class="icon_document_alt"></i>
														<?php echo $child_section->post_title; ?>
                                                    </a>
                                                </li>
											<?php
											endforeach;
											?>
                                        </ul>
                                    </div>
								<?php endif; ?>

                                <div class="border_bottom"></div>

								<?php
								foreach ( $child_sections as $child_section ) :
									?>
                                    <div class="child-doc onepage-doc-sec" id="<?php echo sanitize_title($child_section->post_title) ?>">
                                        <div class="shortcode_title">
                                            <h2> <?php echo $child_section->post_title ?> </h2>
                                        </div>
                                        <div class="doc-content">
											<?php
											if ( did_action( 'elementor/loaded' ) ) {
												$child_content = \Elementor\Plugin::instance()->frontend->get_builder_content($child_section->ID);
												echo !empty($child_content) ? $child_content : apply_filters('the_content', $child_section->post_content);
											} else {
												echo apply_filters('the_content', $child_section->post_content);
											}
											?>
                                        </div>
                                        <div class="border_bottom"></div>
                                    </div>
								<?php

								
								$last_depth = get_children( array(
									'post_parent'    => $child_section->ID,
									'post_type'      => 'docs',
									'post_status'    => 'publish',
									'orderby'        => 'menu_order',
									'order'          => 'ASC',
									'posts_per_page' => -1,
								));

								foreach( $last_depth as $last_depth_doc ) :
									?>
									<div class="child-doc onepage-doc-sec" id="<?php echo sanitize_title($last_depth_doc->post_title) ?>">
                                        <div class="shortcode_title">
                                            <h2> <?php echo $last_depth_doc->post_title ?> </h2>
                                        </div>
                                        <div class="doc-content">
											<?php
											if ( did_action( 'elementor/loaded' ) ) {
												$child_content = \Elementor\Plugin::instance()->frontend->get_builder_content($last_depth_doc->ID);
												echo !empty($child_content) ? $child_content : apply_filters('the_content', $last_depth_doc->post_content);
											} else {
												echo apply_filters('the_content', $last_depth_doc->post_content);
											}
											?>
                                        </div>
                                        <div class="border_bottom"></div>
                                    </div>
									<?php
								endforeach;

								endforeach;


								
							
								


								?>
                            </article>
							<?php
							++$i;
						}
						?>
                    </div>
                </div>
                <div class="col-xl-2 col-lg-3 doc_right_mobile_menu sticky-top sticky-lg-top">
                    <div class="open_icon" id="right">
                        <i class="arrow_carrot-left"></i>
                        <i class="arrow_carrot-right"></i>
                    </div>
                    <div class="doc_rightsidebar scroll one-page-docs-right-sidebar">
                        <div class="pageSideSection">
							<?php
                            /**
                             * Conditional Dropdown
                             */
                            eazydocs_get_template_part('tools/conditional-dropdown');

                            /**
                             * Font Size Switcher & Print Icon
                             */
                            eazydocs_get_template_part('tools/font-switcher');

                            /**
                             * Dark Mode switcher
                             */
                            eazydocs_get_template_part('tools/dark-mode-switcher');
							?>

                            <div class="onepage-sidebar doc_sidebar <?php echo esc_attr($ezd_content_none); ?>">
                                <div class="hire-us">
									<?php
									$content_type  = get_post_meta( get_the_ID(), 'ezd_doc_content_type_right', true );
									$ezd_shortcode  = get_post_meta( get_the_ID(), 'ezd_doc_content_box_right', true );

									if (  $content_type  == 'string_data_right' ) {
										echo html_entity_decode( $ezd_shortcode ) ?? '';
									} elseif ( $content_type == 'shortcode_right' ) {
										echo do_shortcode( html_entity_decode( $ezd_shortcode ) );
									} else {
										dynamic_sidebar( html_entity_decode( $ezd_shortcode ) );
									}
									?>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

<?php
get_footer();