<?php
get_header();
wp_enqueue_script('eazydocs-onepage');

$opt                = get_option( 'eazydocs_settings' );
global $post;
$post_slug          = $post->post_name;
$post_id            = get_page_by_path($post_slug, OBJECT, array( 'docs' ) );

$children           = wp_list_pages( array(
	'title_li'      => '',
	'order'         => 'menu_order',
	'child_of'      => $post_id->ID,
	'echo'          => false,
	'post_type'     => 'docs',
	'walker'        => new Walker_Onepage_Fullscren(),
) );
?>
    <section class="documentation_area_sticky doc_documentation_area onepage_doc_area page_wrapper fullscreen-layout" id="sticky_doc">
        <div class="overlay_bg"></div>
        <div class="container-fluid p-lg-5">
            <div class="row doc-container">
                <div class="col-xl-3 col-lg-3 doc_mobile_menu doc-sidebar sticky-top sticky-lg-top left-column">
                    <aside class=" one-page-docs-sidebar-wrap">
                        <div class="open_icon" id="left">
                            <i class="arrow_carrot-right"></i>
                            <i class="arrow_carrot-left"></i>
                        </div>

						<?php echo get_the_post_thumbnail($post_id->ID, 'full');

						if ( $children ) :
							?>
                            <nav class="scroll op-docs-sidebar">
                                <ul class="list-unstyled nav-sidebar doc-nav one-page-doc-nav-wrap" id="eazydocs-toc">
									<?php
									echo wp_list_pages(array(
										'title_li' => '',
										'order' => 'menu_order',
										'child_of' => $post_id->ID,
										'echo' => false,
										'post_type' => 'docs',
										'walker' => new Walker_Onepage_Fullscren(),
										'depth' => 2
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
                <div class="col-xl-7 col-lg-6 col-md-9 middle-content">
                    <div class="documentation_info" id="post">
						<?php
						$sections = get_children( array(
							'post_parent'    => $post_id->ID,
							'post_type'      => 'docs',
							'post_status'    => 'publish',
							'orderby'        => 'menu_order',
							'order'          => 'ASC',
							'posts_per_page' =>  -1,
						));

						$i = 0;
						$sec_serial = 0;
						foreach ( $sections as $doc_item ) {
							$child_sections = get_children( array(
								'post_parent'    => $doc_item->ID,
								'post_type'      => 'docs',
								'post_status'    => 'publish',
								'orderby'        => 'menu_order',
								'order'          => 'ASC',
								'posts_per_page' => -1,
							));
							$sec_serial++;
							?>
                            <article class="documentation_body doc-section onepage-doc-sec" id="<?php echo sanitize_title($doc_item->post_title) ?>" itemscope itemtype="http://schema.org/Article">
								<?php if ( !empty($doc_item->post_title) ) : ?>
                                    <div class="shortcode_title doc-sec-title">
                                        <h2> <?php
											echo $sec_serial.'. ';
											echo esc_html($doc_item->post_title) ?> </h2>
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
								<?php
								$child_serial = 0;
								foreach ( $child_sections as $child_section ) :
									$child_serial++;
									?>
                                    <div class="child-doc onepage-doc-sec" id="<?php echo sanitize_title($child_section->post_title) ?>">
                                        <div class="shortcode_title depth-one ">
                                            <h2> <?php
												echo $sec_serial.'.'.$child_serial.'. ';
												echo $child_section->post_title ?> </h2>
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
                                    </div>
								<?php
								endforeach;
								?>
                            </article>
							<?php
							++$i;
						}
						?>
                    </div>
                </div>
                <div class="col-xl-2 col-lg-3 col-md-3 doc_right_mobile_menu">
                    <div class="open_icon" id="right">
                        <i class="arrow_carrot-left"></i>
                        <i class="arrow_carrot-right"></i>
                    </div>
                    <div class="doc_rightsidebar scroll one-page-docs-right-sidebar">
                        <div class="pageSideSection">
							<?php
							$is_os_dropdown = '1';
							if ( $is_os_dropdown == '1' ) :
								wp_enqueue_style( 'bootstrap-select' );
								wp_enqueue_script( 'bootstrap-select' );
								?>
                                <select id="mySelect">
                                    <option value="windows" data-content="<i class='fab fa-windows'></i> Windows"> <?php esc_html_e( 'Windows', 'eazydocs' ); ?> </option>
                                    <option value="ios" data-content="<i class='fab fa-apple'></i> IOS"> <?php esc_html_e( 'IOS', 'eazydocs' ); ?> </option>
                                </select>
							<?php
							endif;
							?>
                            <div id="font-switcher" class="d-flex justify-content-between align-items-center">
                                <div id="rvfs-controllers" class="fontsize-controllers group">
                                    <div class="btn-group">
                                        <button id="switcher-small" class="rvfs-decrease btn" title="<?php esc_attr_e('Decrease font size', 'docy'); ?>">A-</button>
                                        <button id="switcher-default" class="rvfs-reset btn" title="<?php esc_attr_e('Default font size', 'docy'); ?>">A</button>
                                        <button id="switcher-large" class="rvfs-increase btn" title="<?php esc_attr_e('Increase font size', 'docy'); ?>">A+</button>
                                    </div>
                                </div>
                                <a href="#" class="print"><i class="icon_printer"></i></a>
                            </div>

							<?php
							$is_dark_switcher = $opt['is_dark_switcher'] ?? '';
							if ( $is_dark_switcher == '1' ) : ?>
                                <div class="doc_switch d-flex align-items-center">
                                    <label for="ezd_dark_switch" class="tab-btn tab-btns light-mode"><i class="icon_lightbulb_alt"></i></label>
                                    <input type="checkbox" name="ezd_dark_switch" id="ezd_dark_switch" class="tab_switcher">
                                    <label for="ezd_dark_switch" class="tab-btn dark-mode"><i class="far fa-moon"></i></label>
                                </div>
							<?php endif; ?>

                            <div class="onepage-sidebar doc_sidebar">
								<?php
								$content_type  = get_post_meta( get_the_ID(), 'ezd_doc_content_type_right', true );
								$ezd_shortcode  = get_post_meta( get_the_ID(), 'ezd_doc_content_box_right', true );
								if ( $content_type == 'string_data_right' ) {
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
    </section>
<?php
get_footer();