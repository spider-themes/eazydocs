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
			while ( $parent_args->have_posts() ) : $parent_args->the_post();
				$sections = get_children( [
					'post_parent' => get_the_ID(),
					'post_type'   => 'docs',
					'numberposts' => 14,
					'post_status' => $doc_statuses,
					'orderby'     => $order_by ?? 'menu_order',
					'order'       => $child_order,
                    'posts_per_page' => ! empty( $settings['doc_items_articles'] ) ? $settings['doc_items_articles'] : - 1,
				] );
				$sections = ezd_filter_doc_visibility( $sections, $show_private, $show_protected );

				global $post;
				$get_child_docs = get_pages( array(
					'child_of'    => get_the_ID(),
					'post_type'   => 'docs',
					'post_status' => $doc_statuses,
				) );
				$get_child_docs = ezd_filter_doc_visibility( $get_child_docs, $show_private, $show_protected );

				// Skip docs with no child docs when "Hide Empty Docs" is enabled.
				if ( ! empty( $hide_empty ) && empty( $get_child_docs ) ) {
					continue;
				}

				?>
                <div class="ezd-col-width">
                    <div class="categories_guide_item <?php echo esc_attr( ezd_doc_status_classes( get_the_ID() ) ); ?> wow fadeInUp">
						<?php ezd_render_doc_indicators( get_the_ID(), $show_lock ); ?>

                        <div class="doc-top ezd-d-flex ezd-align-items-start">
                            <a class="doc_tag_title" href="<?php the_permalink(); ?>">
                                <h4 class="title ezd_item_title"> <?php the_title(); ?> </h4>
								<?php echo ezd_doc_status_badge( get_the_ID(), $show_badge ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
                                <span class="ezd-badge">
									<?php 
									echo count( $get_child_docs );
									echo ' ' . esc_html( $topics_label ); 
									?>
								</span>
                            </a>
                        </div>

						<?php
						if ( $sections ) :
							ezd_render_doc_items_list( $sections, 'ezd-list-unstyled article_list' );
							ezd_render_read_more_btn( get_permalink(), $read_more, 'doc_border_btn ezd_btn', '<i class="arrow_right"></i>' );
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