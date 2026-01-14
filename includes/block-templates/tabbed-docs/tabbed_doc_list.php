<?php
/**
 * Enhanced Tabbed Doc List preset template for Tabbed Docs block
 * Matches editor design with doc count badges, icons, and card styles
 * Uses ezd- prefixed classes for style isolation
 * Pro features are only enabled when EazyDocs Pro is active
 *
 * @package EazyDocs
 */

// Check if Pro is active
$is_pro_active = function_exists( 'ezd_is_premium' ) && ezd_is_premium();

// Attributes with ezd prefix naming convention
$tab_style       = isset( $attributes['tabStyle'] ) ? 'ezd-tab-style-' . esc_attr( $attributes['tabStyle'] ) : 'ezd-tab-style-default';
$card_style      = isset( $attributes['cardStyle'] ) ? 'ezd-card-style-' . esc_attr( $attributes['cardStyle'] ) : 'ezd-card-style-elevated';
$show_doc_count  = isset( $attributes['showDocCount'] ) ? $attributes['showDocCount'] : true;
$show_tab_icon   = isset( $attributes['showTabIcon'] ) ? $attributes['showTabIcon'] : false;
$enable_hover    = isset( $attributes['enableHoverAnimation'] ) ? $attributes['enableHoverAnimation'] : true;
$compact_mode    = isset( $attributes['compactMode'] ) ? $attributes['compactMode'] : false;
$animation_speed = isset( $attributes['animationSpeed'] ) ? 'ezd-animation-' . esc_attr( $attributes['animationSpeed'] ) : 'ezd-animation-normal';
$col             = isset( $attributes['col'] ) ? intval( $attributes['col'] ) : 2;
$docs_layout     = isset( $attributes['docs_layout'] ) ? $attributes['docs_layout'] : 'grid';
$show_last_updated = isset( $attributes['showLastUpdated'] ) ? $attributes['showLastUpdated'] : false;

// Advanced Styling Attributes - Only apply if Pro is active
$card_padding     = $is_pro_active && isset( $attributes['cardPadding'] ) ? intval( $attributes['cardPadding'] ) : 32;
$card_gap         = $is_pro_active && isset( $attributes['cardGap'] ) ? intval( $attributes['cardGap'] ) : 24;
$border_radius    = $is_pro_active && isset( $attributes['borderRadius'] ) ? intval( $attributes['borderRadius'] ) : 16;
$show_title_line  = $is_pro_active && isset( $attributes['showTitleLine'] ) ? $attributes['showTitleLine'] : true;
$button_style     = $is_pro_active && isset( $attributes['buttonStyle'] ) ? 'ezd-button-' . esc_attr( $attributes['buttonStyle'] ) : 'ezd-button-filled';
$icon_style       = $is_pro_active && isset( $attributes['iconStyle'] ) ? 'ezd-icon-' . esc_attr( $attributes['iconStyle'] ) : 'ezd-icon-rounded';
$shadow_intensity = $is_pro_active && isset( $attributes['shadowIntensity'] ) ? 'ezd-shadow-' . esc_attr( $attributes['shadowIntensity'] ) : 'ezd-shadow-medium';

// Validate tab style for free users (only allow basic styles)
$free_tab_styles = array( 'default', 'rounded' );
$selected_tab_style = isset( $attributes['tabStyle'] ) ? $attributes['tabStyle'] : 'default';
if ( ! $is_pro_active && ! in_array( $selected_tab_style, $free_tab_styles, true ) ) {
	$tab_style = 'ezd-tab-style-default';
}

// Validate card style for free users (only allow basic styles)
$free_card_styles = array( 'elevated', 'bordered' );
$selected_card_style = isset( $attributes['cardStyle'] ) ? $attributes['cardStyle'] : 'elevated';
if ( ! $is_pro_active && ! in_array( $selected_card_style, $free_card_styles, true ) ) {
	$card_style = 'ezd-card-style-elevated';
}

// Generate unique block ID for this instance
$block_unique_id = wp_unique_id( 'ezd-tabbed-list-' );

// Build class string with ezd prefix
$wrapper_classes = array(
	'ezd-tabbed-docs',
	'ezd-tabbed-docs-block',
	$tab_style,
	$card_style,
	$animation_speed,
	$shadow_intensity,
	$button_style,
	$icon_style,
);

if ( $enable_hover ) {
	$wrapper_classes[] = 'ezd-hover-enabled';
}

if ( $compact_mode ) {
	$wrapper_classes[] = 'ezd-compact';
}

if ( ! $show_title_line ) {
	$wrapper_classes[] = 'ezd-no-title-line';
}

// Build custom inline styles - using actual CSS variable names for proper cascade
$custom_styles = '';

// Primary color from attributes - Only apply if Pro is active
$primary_color = $is_pro_active && isset( $attributes['primaryColor'] ) ? $attributes['primaryColor'] : '';
if ( ! empty( $primary_color ) ) {
	$custom_styles .= '--ezd-primary: ' . esc_attr( $primary_color ) . ';';
}

if ( $card_padding !== 32 ) {
	$custom_styles .= '--ezd-card-padding: ' . $card_padding . 'px;';
}
if ( $card_gap !== 24 ) {
	$custom_styles .= '--ezd-grid-gap: ' . $card_gap . 'px;';
}
if ( $border_radius !== 16 ) {
	$custom_styles .= '--ezd-card-radius: ' . $border_radius . 'px;';
}

// Layout class with ezd prefix - Masonry is Pro only
$selected_layout = isset( $attributes['docs_layout'] ) ? $attributes['docs_layout'] : 'grid';
// Fall back to grid if user selected masonry but doesn't have Pro
if ( ! $is_pro_active && 'masonry' === $selected_layout ) {
	$selected_layout = 'grid';
}
$layout_class = 'grid' === $selected_layout
	? 'ezd-grid ezd-column-' . $col . ' ezd-topic-list-inner'
	: 'ezd-masonry-wrap ezd-masonry-col-' . $col . ' ezd-topic-list-inner';

$parent_args = new WP_Query(
	array(
		'post_type'      => 'docs',
		'post_status'    => is_user_logged_in() ? array( 'publish', 'private' ) : 'publish',
		'orderby'        => $attributes['orderBy'] ?? 'menu_order',
		'posts_per_page' => $attributes['show_docs'] ?? -1,
		'order'          => $attributes['parent_docs_order'] ?? 'desc',
		'post_parent'    => 0,
		'post__not_in'   => $attributes['exclude'] ?? array(),
		'post__in'       => $attributes['include'] ?? array(),
	)
);

/**
 * Get article count for a parent doc (including nested children)
 *
 * @param int $parent_id Parent document ID.
 * @return int Total article count.
 */
if ( ! function_exists( 'ezd_tabbed_list_get_article_count' ) ) {
	function ezd_tabbed_list_get_article_count( $parent_id ) {
		$children = get_children(
			array(
				'post_parent' => $parent_id,
				'post_type'   => 'docs',
				'post_status' => array( 'publish', 'private' ),
			)
		);

		$count = count( $children );

		// Count grandchildren
		foreach ( $children as $child ) {
			$grandchildren = get_children(
				array(
					'post_parent' => $child->ID,
					'post_type'   => 'docs',
					'post_status' => array( 'publish', 'private' ),
				)
			);
			$count        += count( $grandchildren );
		}

		return $count;
	}
}
?>
<section class="<?php echo esc_attr( implode( ' ', $wrapper_classes ) ); ?>" id="<?php echo esc_attr( $block_unique_id ); ?>" style="<?php echo esc_attr( $custom_styles ); ?>">

	<div class="ezd-tabs-sliders">
		<span class="ezd-scroller-btn ezd-left ezd-inactive">
			<svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
				<polyline points="15 18 9 12 15 6"></polyline>
			</svg>
		</span>

		<?php if ( $parent_args->have_posts() ) : ?>
			<ul class="ezd-tab-menu ezd-slide-nav-tabs">
				<?php
				$i = 0;
				while ( $parent_args->have_posts() ) :
					$parent_args->the_post();
					++$i;
					$active        = ( 1 === $i ) ? 'ezd-active' : '';
					$article_count = $show_doc_count ? ezd_tabbed_list_get_article_count( get_the_ID() ) : 0;
					$has_thumbnail = has_post_thumbnail();
					?>
					<li class="ezd-nav-item">
						<a data-rel="<?php echo esc_attr( $block_unique_id . '-' . get_post_field( 'post_name', get_the_ID() ) ); ?>" class="ezd-nav-link <?php echo esc_attr( $active ); ?>">
							<?php if ( $show_tab_icon && $has_thumbnail ) : ?>
								<span class="ezd-tab-icon">
									<?php the_post_thumbnail( 'thumbnail', array( 'class' => 'ezd-tab-icon-img' ) ); ?>
								</span>
							<?php endif; ?>
							<span class="ezd-tab-text"><?php the_title(); ?></span>
							<?php if ( $show_doc_count && $article_count > 0 ) : ?>
								<span class="ezd-tab-count"><?php echo esc_html( $article_count ); ?></span>
							<?php endif; ?>
						</a>
					</li>
				<?php endwhile; ?>
				<?php wp_reset_postdata(); ?>
			</ul>
		<?php endif; ?>

		<span class="ezd-scroller-btn ezd-right ezd-inactive">
			<svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
				<polyline points="9 18 15 12 9 6"></polyline>
			</svg>
		</span>
	</div>

	<div class="ezd-tab-content">
		<?php
		if ( $parent_args->have_posts() ) :
			$i = 0;
			while ( $parent_args->have_posts() ) :
				$parent_args->the_post();
				++$i;
				$active = ( 1 === $i ) ? 'ezd-active' : '';

				$sections = get_children(
					array(
						'post_parent' => get_the_ID(),
						'post_type'   => 'docs',
						'numberposts' => $attributes['sectionsNumber'] ?? -1,
						'post_status' => array( 'publish', 'private' ),
						'orderby'     => $attributes['orderBy'] ?? 'menu_order',
						'order'       => $attributes['child_docs_order'] ?? 'desc',
					)
				);
				?>
				<div class="ezd-tab-pane <?php echo esc_attr( $active ); ?>" id="<?php echo esc_attr( $block_unique_id . '-' . get_post_field( 'post_name', get_the_ID() ) ); ?>">
					<?php if ( empty( $sections ) ) : ?>
						<div class="ezd-no-sections">
							<svg xmlns="http://www.w3.org/2000/svg" width="48" height="48" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5">
								<path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"></path>
								<polyline points="14 2 14 8 20 8"></polyline>
							</svg>
							<p><?php esc_html_e( 'No sections found in this documentation.', 'eazydocs' ); ?></p>
						</div>
					<?php else : ?>
						<div class="<?php echo esc_attr( $layout_class ); ?>">
							<?php
							foreach ( $sections as $section ) :
								$articles = get_children(
									array(
										'post_parent' => $section->ID,
										'post_type'   => 'docs',
										'numberposts' => $attributes['articlesNumber'] ?? -1,
										'post_status' => array( 'publish', 'private' ),
										'orderby'     => $attributes['orderBy'] ?? 'menu_order',
										'order'       => $attributes['child_docs_order'] ?? 'desc',
									)
								);
								?>
								<div class="ezd-section-card">
									<div class="ezd-doc-tag-item">
										<div class="ezd-doc-tag-title">
											<h4 class="ezd-item-title">
												<a href="<?php echo esc_url( get_permalink( $section->ID ) ); ?>">
													<?php echo wp_kses_post( $section->post_title ); ?>
												</a>
												<?php if ( count( $articles ) > 0 ) : ?>
													<span class="ezd-section-count">
														<?php
														/* translators: %d: number of articles */
														printf(
															esc_html( _n( '%d article', '%d articles', count( $articles ), 'eazydocs' ) ),
															count( $articles )
														);
														?>
													</span>
												<?php endif; ?>
											</h4>
											<div class="ezd-line"></div>
										</div>

										<?php if ( ! empty( $articles ) ) : ?>
											<ul class="ezd-tag-list">
												<?php foreach ( $articles as $article ) : ?>
													<li>
														<a href="<?php echo esc_url( get_permalink( $article->ID ) ); ?>" class="ezd-item-list-title">
															<svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
																<path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"></path>
																<polyline points="14 2 14 8 20 8"></polyline>
																<line x1="16" y1="13" x2="8" y2="13"></line>
																<line x1="16" y1="17" x2="8" y2="17"></line>
																<polyline points="10 9 9 9 8 9"></polyline>
															</svg>
															<?php echo wp_kses_post( $article->post_title ); ?>
															<?php if ( $show_last_updated ) : ?>
																<span class="ezd-article-date">
																	<?php echo esc_html( human_time_diff( strtotime( $article->post_modified ), current_time( 'timestamp' ) ) . ' ' . __( 'ago', 'eazydocs' ) ); ?>
																</span>
															<?php endif; ?>
														</a>
													</li>
												<?php endforeach; ?>
											</ul>
										<?php else : ?>
											<p class="ezd-no-articles-msg">
												<?php esc_html_e( 'No articles in this section yet.', 'eazydocs' ); ?>
											</p>
										<?php endif; ?>

										<a href="<?php echo esc_url( get_permalink( $section->ID ) ); ?>" class="ezd-text-btn ezd-dark-btn">
											<?php echo esc_html( $attributes['readMoreText'] ?? __( 'View All', 'eazydocs' ) ); ?>
											<svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
												<line x1="5" y1="12" x2="19" y2="12"></line>
												<polyline points="12 5 19 12 12 19"></polyline>
											</svg>
										</a>
									</div>
								</div>
							<?php endforeach; ?>
						</div>
					<?php endif; ?>
				</div>
			<?php endwhile; ?>
			<?php wp_reset_postdata(); ?>
		<?php endif; ?>
	</div>
</section>

<script>
;(function($) {
	"use strict";

	$(document).ready(function() {
		var tabId = "#<?php echo esc_js( $block_unique_id ); ?>";
		var tabSliderContainers = $(tabId + " .ezd-tabs-sliders");

		tabSliderContainers.each(function() {
			let tabWrapWidth = $(this).outerWidth();
			let totalWidth = 0;

			let slideArrowBtn = $(tabId + " .ezd-scroller-btn");
			let slideBtnLeft = $(tabId + " .ezd-scroller-btn.ezd-left");
			let slideBtnRight = $(tabId + " .ezd-scroller-btn.ezd-right");
			let navWrap = $(tabId + " .ezd-slide-nav-tabs");
			let navWrapItem = $(tabId + " .ezd-slide-nav-tabs li");

			navWrapItem.each(function() {
				totalWidth += $(this).outerWidth();
			});

			if (totalWidth > tabWrapWidth) {
				slideArrowBtn.removeClass("ezd-inactive");
			} else {
				slideArrowBtn.addClass("ezd-inactive");
			}

			if (navWrap.scrollLeft() === 0) {
				slideBtnLeft.addClass("ezd-inactive");
			} else {
				slideBtnLeft.removeClass("ezd-inactive");
			}

			slideBtnRight.on("click", function() {
				navWrap.animate({ scrollLeft: "+=200px" }, 300);
			});

			slideBtnLeft.on("click", function() {
				navWrap.animate({ scrollLeft: "-=200px" }, 300);
			});

			scrollerHide(navWrap, slideBtnLeft, slideBtnRight);
		});

		function scrollerHide(navWrap, slideBtnLeft, slideBtnRight) {
			navWrap.scroll(function() {
				let $elem = $(this);
				let newScrollLeft = $elem.scrollLeft(),
					width = $elem.outerWidth(),
					scrollWidth = $elem.get(0).scrollWidth;
				if (scrollWidth - newScrollLeft === width) {
					slideBtnRight.addClass("ezd-inactive");
				} else {
					slideBtnRight.removeClass("ezd-inactive");
				}
				if (newScrollLeft === 0) {
					slideBtnLeft.addClass("ezd-inactive");
				} else {
					slideBtnLeft.removeClass("ezd-inactive");
				}
			});
		}

		// Tab switching with animation
		$(tabId + ' .ezd-tab-menu li a').on('click', function(e) {
			e.preventDefault();
			$(this).closest('.ezd-tab-menu').find('li a').removeClass('ezd-active');
			$(this).addClass('ezd-active');

			var target = $(this).attr('data-rel');
			$(tabId + ' #' + target)
				.addClass('ezd-active')
				.siblings('.ezd-tab-pane')
				.removeClass('ezd-active');

			return false;
		});
	});
})(jQuery);
</script>
