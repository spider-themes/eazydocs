<?php
get_header();
$post_id            = get_page_by_title( get_the_title(get_the_ID()), OBJECT, 'docs' );
wp_enqueue_script('eazydocs-onpage');
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
		<div class="container-fluid pl-60 pr-60">
			<div class="row doc-container">
				<div class="col-xxl-2 col-xl-3 col-lg-3 doc_mobile_menu doc-sidebar sticky-top left-column">
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
							<div class="scroll">
								<ul class="list-unstyled nav-sidebar doc-nav one-page-doc-nav-wrap">
									<?php
									echo wp_list_pages(array(
										'title_li' => '',
										'order' => 'menu_order',
										'child_of' => $post_id->ID,
										'echo' => false,
										'post_type' => 'docs',
										'walker' => new EazyDocs_Walker_Onepage(),
										'depth' => 2
									));
									?>
								</ul>
							</div>
						<?php
						endif;
						?>
					</aside>
				</div>
				<div class="col-xxl-8 col-xl-6 col-lg-6 middle-content">
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
									<?php echo  apply_filters('the_content', $doc_item->post_content); ?>
								</div>

								<?php if ( $child_sections ) : ?>
									<div class="articles-list mt-5">
										<h4> <?php esc_html_e('Articles', 'docy'); ?></h4>
										<ul class="article_list tag_list one-page-docs-tag-list">
											<?php
											foreach ( $child_sections as $child_section ):
												?>
												<li>
													<a href="#<?php echo sanitize_title($child_section->post_title) ?>">
														<i class="icon_document_alt"></i><?php echo esc_html($child_section->post_title) ?>
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
											<h2> <?php echo esc_html($child_section->post_title) ?> </h2>
										</div>
										<div class="doc-content">
											<?php echo apply_filters('the_content', $child_section->post_content);
											?>
										</div>
										<div class="border_bottom"></div>
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
				<div class="col-xxl-2 col-xl-3 col-lg-3 doc_right_mobile_menu sticky-top">
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

							 <div class="onepage-sidebar doc_sidebar <?php echo esc_attr($ezd_content_none); ?>">
                                 <div class="hire-us">
                                     <?php
                                     if( ! empty( $ezd_content ) ) :
                                         ?>
                                         <p class="explanation hire expn-left p-3 small">
                                             <?php echo wp_strip_all_tags( $ezd_content ); ?>
                                         </p>
                                        <?php
                                     endif;
                                     dynamic_sidebar('doc_sidebar');
                                     ?>
                                     <a href="mailto:<?php echo get_option('admin_email'); ?>" class="action_btn btn_small"> Email Us<i class="arrow_right"></i> </a>
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