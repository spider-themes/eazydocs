<?php
/**
 * Cannot access directly.
 */
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

$private_doc_mode       = ezd_is_premium() ? ezd_get_opt( 'private_doc_mode' ) : 'none';
$ppp_column             = ! empty( $settings['ppp_column'] ) ? $settings['ppp_column'] : '3';
$is_masonry     		= $settings['masonry'] ?? '';
$masonry_layout 		= $is_masonry == 'yes' ? ' ezd-masonry' : '';
$masonry_attr   		= $is_masonry == 'yes' ? 'ezd-massonry-col="' . esc_attr( $ppp_column ) . '"' : '';
$layout 				= $is_masonry == 'yes' ? 'masonry' : 'grid';

// Restricted docs visibility / display toggles.
$show_private   = ezd_setting_enabled( $settings, 'md_show_private_docs' );
$show_protected = ezd_setting_enabled( $settings, 'md_show_protected_docs' );
$show_badge     = ezd_setting_enabled( $settings, 'md_show_status_badge' );
$show_lock      = ezd_setting_enabled( $settings, 'md_show_lock_icon' );
$doc_statuses   = ezd_doc_listing_statuses( $show_private );

// Check pro plugin class exists
if ( ezd_is_premium() ) {
	$layout = ezd_get_opt( 'docs-archive-layout', $layout ); // id of field
}
?>

<div class="eazydocs_shortcode">
    <div class="<?php if ( $layout === 'grid' ) { echo 'ezd-grid'; } ?>  ezd-column-<?php echo esc_attr( $ppp_column .' '. $masonry_layout ); ?>"  <?php echo wp_kses_post( $masonry_attr ); ?>>
		<?php
		// Ensure $doc_exclude is an array of integers
		$exclude_ids = array_map( 'intval', (array) $doc_exclude );

		$parent_args = new WP_Query( [
			'post_type'      => 'docs',
			'posts_per_page' => $doc_number,
			'post_status'    => $doc_statuses,
			'orderby'        => $order_by ?? 'menu_order',
			'order'          => $doc_order ?? 'ASC',
			'post_parent'    => 0,
			'post__not_in'   => ! empty( $empty_doc_ids ) ? $empty_doc_ids : [],
		]);

		if ( ! empty( $exclude_ids ) && !empty($parent_args->posts)) {
			// Filter posts in PHP instead of using post__not_in
			$parent_args->posts = array_values(array_filter($parent_args->posts, function($post) use ( $exclude_ids ) {
				return ! in_array( (int) $post->ID, $exclude_ids, true );
			}));
		}

		// Drop password-protected (and, when disabled, private) parents per the toggles.
		$parent_args->posts = ezd_filter_doc_visibility( $parent_args->posts, $show_private, $show_protected );

		// Update post_count after filtering
		$parent_args->post_count = count($parent_args->posts);


		// arrange the docs
		if ( $parent_args->have_posts() ) :
			$parent_ids        = ! empty( $parent_args->posts ) ? wp_list_pluck( $parent_args->posts, 'ID' ) : [];
			$descendant_counts = ! empty( $parent_ids ) ? ezd_get_docs_descendant_counts( $parent_ids, $doc_statuses ) : [];
			$grouped_sections  = ! empty( $parent_ids ) ? ezd_get_children_grouped(
				$parent_ids,
				array(
					'post_status' => $doc_statuses,
					'orderby'     => $order_by ?? 'menu_order',
					'order'       => $child_order,
					'numberposts' => ! empty( $settings['doc_items_articles'] ) ? $settings['doc_items_articles'] : 14,
				)
			) : [];

			while ( $parent_args->have_posts() ) : $parent_args->the_post();
				$current_doc_id = get_the_ID();
				$sections       = $grouped_sections[ $current_doc_id ] ?? array();
				$sections       = ezd_filter_doc_visibility( $sections, $show_private, $show_protected );
				$child_count    = $descendant_counts[ $current_doc_id ] ?? 0;

				// Skip docs with no child docs when "Hide Empty Docs" is enabled.
				if ( ! empty( $hide_empty ) && empty( $child_count ) ) {
					continue;
				}

				?>
                <div class="ezd-col-width">
                    <div class="categories_guide_item <?php echo esc_attr( ezd_doc_status_classes( $current_doc_id ) ); ?> wow fadeInUp">
						<?php ezd_render_doc_indicators( $current_doc_id, $show_lock ); ?>

                        <div class="doc-top ezd-d-flex ezd-align-items-start">
                            <a class="doc_tag_title" href="<?php the_permalink(); ?>">
                                <h4 class="title ezd_item_title"> <?php the_title(); ?> </h4>
								<?php echo ezd_doc_status_badge( $current_doc_id, $show_badge ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
                                <span class="ezd-badge">
									<?php 
									echo esc_html( $child_count );
									echo ' ' . esc_html( $topics_label ); 
									?>
								</span>
                            </a>
                        </div>

						<?php
						if ( $sections ) :
							ezd_render_doc_items_list( $sections, 'ezd-list-unstyled article_list' );
						endif;

						// Read More + Subscribe share a flex row so Subscribe sits to the right (space-between).
						$has_subscription = ( $settings['show_subscription'] ?? '' ) === 'yes';
						$has_read_more    = ! empty( $sections ) && ! empty( $read_more );

						if ( $has_read_more || $has_subscription ) :
							?>
							<div class="ezd-doc-btn-wrap<?php echo $has_subscription ? ' has-subscription' : ''; ?>">
								<?php
								if ( $has_read_more ) {
									ezd_render_read_more_btn( get_permalink(), $read_more, 'doc_border_btn ezd_btn', '<i class="arrow_right"></i>' );
								}
								// Subscribe button (reuses the Pro subscription feature).
								ezd_render_doc_subscription( $settings, get_the_ID() );
								?>
							</div>
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

<?php
if ( $is_masonry == 'yes' ) {
    ezd_render_masonry_script();
}
?>