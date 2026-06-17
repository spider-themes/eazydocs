<?php
get_header();

// Rendering the whole tree (and every Elementor node) in one request is heavy;
// give it room so large docs don't 500 mid-render / during "print to PDF".
ezd_raise_onepage_render_limits();

wp_enqueue_script( 'eazydocs-onepage' );
$widget_sidebar = ezd_get_opt( 'is_widget_sidebar' );
$onepage_width  = ezd_get_opt( 'onepage_content_width', 'full-width' );
global $post;
$post_slug        = $post->post_name;
$post_id          = get_page_by_path( $post_slug, OBJECT, array( 'docs' ) );
$walker           = new EazyDocs\Frontend\Walker_Docs();
$child_of_id      = $post_id->ID ?? '';
// Build the navigation list once and reuse it below (it was previously generated
// twice — once only to test for emptiness).
$children         = wp_list_pages( array(
	'title_li'  => '',
	'order'     => 'menu_order',
	'child_of'  => $child_of_id,
	'echo'      => false,
	'post_type' => 'docs',
	'walker'    => new EazyDocs_Walker_Onepage(),
	'depth'     => 4,
) );
?>

<section class="doc_documentation_area onepage_doc_area classic-onepage ezd-onepage-<?php echo esc_attr( $onepage_width ); ?>" id="sticky_doc">
	<?php // Mobile-only trigger; lives outside the off-canvas panel so it stays reachable when the panel is closed. ?>
	<button type="button" class="ezd-classic-menu-trigger" id="mobile-left-toggle" aria-controls="ezd-classic-sidebar" aria-expanded="false" aria-label="<?php esc_attr_e( 'Open documentation menu', 'eazydocs' ); ?>">
		<span class="ezd-bars" aria-hidden="true">
			<span class="ezd-bar"></span>
			<span class="ezd-bar"></span>
			<span class="ezd-bar"></span>
		</span>
		<span class="ezd-trigger-label"><?php esc_html_e( 'Contents', 'eazydocs' ); ?></span>
	</button>
	<div class="overlay_bg" id="ezd-classic-overlay"></div>
	<?php
	// Full-width page banner: the parent doc's title and excerpt alongside a set
	// of at-a-glance stat metrics, shown as a hero at the top of the page (above
	// the content grid). The banner and which metrics appear are controlled from
	// Appearance → One-Page Layout.
	$banner_title   = get_post_field( 'post_title', $child_of_id, 'display' );
	$banner_excerpt = has_excerpt( $child_of_id ) ? get_the_excerpt( $child_of_id ) : '';
	if ( ezd_get_opt( 'onepage_banner', '1' ) == '1' && ( $banner_title || $banner_excerpt ) ) :
		$banner_meta     = ezd_get_onepage_banner_meta( $child_of_id );
		$enabled_metrics = (array) ezd_get_opt( 'onepage_banner_metrics', [ 'count', 'modified', 'authors', 'reading_time' ] );

		// Build the metric items; skip empties and any the admin switched off.
		$banner_stats = [];
		if ( in_array( 'count', $enabled_metrics, true ) && $banner_meta['count'] > 0 ) {
			$banner_stats[] = [
				'icon'  => 'icon_documents_alt',
				'value' => number_format_i18n( $banner_meta['count'] ),
				'label' => _n( 'Doc', 'Docs', $banner_meta['count'], 'eazydocs' ),
			];
		}
		if ( in_array( 'modified', $enabled_metrics, true ) && $banner_meta['modified'] > 0 ) {
			$banner_stats[] = [
				'icon'  => 'icon_calendar',
				'value' => wp_date( get_option( 'date_format' ), $banner_meta['modified'] ),
				'label' => esc_html__( 'Updated', 'eazydocs' ),
			];
		}
		$banner_author_ids = (array) ( $banner_meta['author_ids'] ?? [] );
		if ( in_array( 'authors', $enabled_metrics, true ) && ! empty( $banner_author_ids ) ) {
			$banner_stats[] = [
				'type'    => 'avatars',
				'authors' => $banner_author_ids,
				'label'   => _n( 'Author', 'Authors', count( $banner_author_ids ), 'eazydocs' ),
			];
		}
		if ( in_array( 'reading_time', $enabled_metrics, true ) && $banner_meta['reading_time'] > 0 ) {
			$banner_stats[] = [
				'icon'  => 'icon_hourglass',
				/* translators: %s: estimated number of minutes to read. */
				'value' => sprintf( esc_html__( '%s min', 'eazydocs' ), number_format_i18n( $banner_meta['reading_time'] ) ),
				'label' => esc_html__( 'Read', 'eazydocs' ),
			];
		}
		?>
		<div class="ezd-onepage-banner">
			<?php // Decorative shapes — purely graphical, hidden from assistive tech. ?>
			<span class="ezd-onepage-banner-shape ezd-onepage-banner-shape--one" aria-hidden="true"></span>
			<span class="ezd-onepage-banner-shape ezd-onepage-banner-shape--two" aria-hidden="true"></span>
			<span class="ezd-onepage-banner-grid" aria-hidden="true"></span>
			<div class="ezd-onepage-banner-content ezd-d-flex ezd-align-items-center ezd-justify-content-between">
				<div class="ezd-onepage-banner-main">
					<?php
					// Badge label is configurable; an empty value hides the pill entirely.
					$banner_badge = ezd_get_opt( 'onepage_banner_badge', esc_html__( 'Documentation', 'eazydocs' ) );
					if ( '' !== trim( (string) $banner_badge ) ) :
						?>
						<span class="ezd-onepage-banner-badge" aria-hidden="true">
							<i class="icon_book_alt"></i>
							<?php echo esc_html( $banner_badge ); ?>
						</span>
					<?php endif; ?>
					<?php if ( $banner_title ) : ?>
						<h1 class="ezd-onepage-banner-title"><?php echo esc_html( $banner_title ); ?></h1>
					<?php endif; ?>
					<?php if ( $banner_excerpt ) : ?>
						<p class="ezd-onepage-banner-excerpt"><?php echo wp_kses_post( $banner_excerpt ); ?></p>
					<?php endif; ?>
				</div>
				<?php if ( ! empty( $banner_stats ) ) : ?>
					<ul class="ezd-onepage-banner-stats ezd-list-unstyled">
						<?php foreach ( $banner_stats as $stat ) : ?>
							<li class="ezd-onepage-banner-stat ezd-d-flex ezd-align-items-center<?php echo isset( $stat['type'] ) ? ' ezd-onepage-banner-stat--' . esc_attr( $stat['type'] ) : ''; ?>">
								<?php if ( isset( $stat['type'] ) && 'avatars' === $stat['type'] ) : ?>
									<span class="ezd-onepage-banner-avatars ezd-align-items-center" aria-hidden="true">
										<?php
										$shown  = array_slice( $stat['authors'], 0, 4 );
										$hidden = count( $stat['authors'] ) - count( $shown );
										foreach ( $shown as $author_id ) {
											echo get_avatar( $author_id, 56, '', '', [ 'class' => 'ezd-onepage-banner-avatar' ] );
										}
										if ( $hidden > 0 ) {
											echo '<span class="ezd-onepage-banner-avatar ezd-onepage-banner-avatar-more">+' . esc_html( number_format_i18n( $hidden ) ) . '</span>';
										}
										?>
									</span>
									<span class="ezd-onepage-banner-stat-label"><?php echo esc_html( $stat['label'] ); ?></span>
								<?php else : ?>
									<i class="<?php echo esc_attr( $stat['icon'] ); ?>" aria-hidden="true"></i>
									<span class="ezd-onepage-banner-stat-value"><?php echo esc_html( $stat['value'] ); ?></span>
									<span class="ezd-onepage-banner-stat-label"><?php echo esc_html( $stat['label'] ); ?></span>
								<?php endif; ?>
							</li>
						<?php endforeach; ?>
					</ul>
				<?php endif; ?>
			</div>
		</div>
	<?php endif; ?>
	<div class="ezd-container-fluid">
		<div class="ezd-grid ezd-grid-cols-12 doc-container">
			<div class="ezd-xl-col-3 ezd-lg-col-3 ezd-grid-column-full doc_mobile_menu doc-sidebar sticky-top ezd-sticky-lg-top left-column">
				<aside class="doc_left_sidebarlist one-page-docs-sidebar-wrap" id="ezd-classic-sidebar">
					<button type="button" class="ezd-sidebar-close" id="mobile-left-close" aria-label="<?php esc_attr_e( 'Close documentation menu', 'eazydocs' ); ?>">
						<i class="icon_close" aria-hidden="true"></i>
					</button>
					<h3 class="nav_title">
						<?php echo esc_html(get_post_field( 'post_title', $child_of_id, 'display' )); ?>
					</h3>
					<?php
					if ( $children ) :
						?>
						<nav class="scroll op-docs-sidebar">
							<ul class="ezd-list-unstyled nav-sidebar default-layout-onepage-sidebar doc-nav one-page-doc-nav-wrap" id="eazydocs-toc">
								<?php
								// Reuse the list built above instead of querying again.
								echo wp_kses( $children, ezd_kses_allowed_docs_nav_html() );
								?>
							</ul>
						</nav>
					<?php
					endif;

					$parent_doc_id_left = get_the_ID();
					$content_type_left  = get_post_meta( $parent_doc_id_left, 'ezd_doc_content_type', true );
					$ezd_shortcode_left = get_post_meta( $parent_doc_id_left, 'ezd_doc_left_sidebar', true );
					$left_sidebar_content = ezd_get_renderable_sidebar_content( $ezd_shortcode_left );
					$is_valid_post_id   = is_null( get_post( $ezd_shortcode_left ) ) ? 'No' : 'Yes';

					if ( $content_type_left == 'string_data' && ! empty( $left_sidebar_content ) ) {
						echo do_shortcode( $left_sidebar_content );
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
			<div class="ezd-xl-col-7 ezd-lg-col-6 ezd-grid-column-full middle-content">
				<div class="documentation_info ezd-container" id="post">
					<?php
					$sections = get_children( array(
						'post_parent'    => $child_of_id,
						'post_type'      => 'docs',
						'post_status'    => 'publish',
						'orderby'        => 'menu_order',
						'order'          => 'ASC',
						'posts_per_page' => - 1,
					) );

					$i = 0;
					foreach ( $sections as $doc_item ) {
						$child_sections = get_children( array(
							'post_parent'    => $doc_item->ID,
							'post_type'      => 'docs',
							'post_status'    => 'publish',
							'orderby'        => 'menu_order',
							'order'          => 'ASC',
							'posts_per_page' => - 1,
						) );
						$get_title      = sanitize_title( $doc_item->post_title );
						if ( preg_match( '#[0-9]#', $get_title ) ) {
							$get_title = 'ezd-' . sanitize_title( $doc_item->post_title );
						}
						?>
						<article class="documentation_body doc-section onepage-doc-sec" id="<?php echo esc_attr( $get_title ); ?>"
									itemscope itemtype="http://schema.org/Article">
							<?php if ( ! empty( $doc_item->post_title ) ) : ?>
								<div class="shortcode_title">
									<h2> <?php echo wp_kses_post( $doc_item->post_title ); ?> </h2>
								</div>
							<?php endif; ?>
							<div class="doc-content">
								<?php
								echo wp_kses_post( ezd_get_onepage_doc_content( $doc_item ) );
								?>
							</div>

							<?php if ( $child_sections ) : ?>
								<div class="articles-list mt-5">
									<h4 class="c_head"> <?php esc_html_e( 'Articles', 'eazydocs' ); ?> </h4>
									<ul class="article_list one-page-docs-tag-list">
										<?php
										foreach ( $child_sections as $child_section ) :
											?>
											<li>
												<a href="#<?php echo esc_attr(sanitize_title( $child_section->post_title )) ?>">
													<i class="icon_document_alt"></i>
													<?php 
													echo esc_html($child_section->post_title); 
													if ( function_exists( 'ezdpro_badge' ) && ezd_is_premium() ) {
														echo ezdpro_badge( $child_section->ID );
													}
													?>
												</a>
											</li>
										<?php
										endforeach;
										?>
									</ul>
								</div>
							<?php endif; ?>

							<?php
							foreach ( $child_sections as $child_section ) :
								$get_child_title = sanitize_title( $child_section->post_title );
								if ( preg_match( '#[0-9]#', $get_child_title ) ) {
									$get_child_title = 'ezd-' . sanitize_title( $child_section->post_title );
								}
								?>
								<div class="child-doc onepage-doc-sec" id="<?php echo esc_attr( $get_child_title ) ?>">
									<div class="shortcode_title depth-two">
										<h3> <?php echo wp_kses_post($child_section->post_title) ?> </h3>
									</div>
									<div class="doc-content">
										<?php
										echo wp_kses_post( ezd_get_onepage_doc_content( $child_section ) );
										?>
									</div>
								</div>

								<?php
								$last_depth = get_children( array(
									'post_parent'    => $child_section->ID,
									'post_type'      => 'docs',
									'post_status'    => 'publish',
									'orderby'        => 'menu_order',
									'order'          => 'ASC',
									'posts_per_page' => - 1,
								) );

								foreach ( $last_depth as $last_depth_doc ) :
									$get_last_child_title = sanitize_title( $last_depth_doc->post_title );
									if ( preg_match( '#[0-9]#', $get_last_child_title ) ) {
										$get_last_child_title = 'ezd-' . sanitize_title( $last_depth_doc->post_title );
									}
									?>
									<div class="child-doc onepage-doc-sec" id="<?php echo esc_attr( $get_last_child_title ) ?>">
										<div class="shortcode_title depth-three">
											<h4> <?php echo wp_kses_post($last_depth_doc->post_title); ?> </h4>
										</div>
										<div class="doc-content">
											<?php
											echo wp_kses_post( ezd_get_onepage_doc_content( $last_depth_doc ) );
											?>
										</div>
									</div>

									<?php
									$last_depth_extend = get_children( array(
										'post_parent'    => $last_depth_doc->ID,
										'post_type'      => 'docs',
										'post_status'    => 'publish',
										'orderby'        => 'menu_order',
										'order'          => 'ASC',
										'posts_per_page' => - 1,
									) );

									foreach ( $last_depth_extend as $last_depth_doc ) :
										$get_last_child_title = sanitize_title( $last_depth_doc->post_title );
										if ( preg_match( '#[0-9]#', $get_last_child_title ) ) {
											$get_last_child_title = 'ezd-' . sanitize_title( $last_depth_doc->post_title );
										}
										?>
										<div class="child-doc onepage-doc-sec" id="<?php echo esc_attr( $get_last_child_title ) ?>">
											<div class="shortcode_title depth-three">
												<h4> <?php echo wp_kses_post($last_depth_doc->post_title); ?> </h4>
											</div>
											<div class="doc-content">
												<?php
												echo wp_kses_post( ezd_get_onepage_doc_content( $last_depth_doc ) );
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
			// Right sidebar part template
			eazydocs_get_template_part( 'onepage/right-sidebar' );
			?>
		</div>
	</div>
</section>

<?php
get_footer();
