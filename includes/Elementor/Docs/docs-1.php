<?php
/**
 * Cannot access directly.
 */
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

$private_doc_mode       = ezd_get_opt( 'private_doc_mode' );
$private_doc_login_page = ezd_get_opt( 'private_doc_login_page' );
$ppp_column             = ! empty( $settings['ppp_column'] ) ? $settings['ppp_column'] : '3';
$is_masonry     		= $settings['masonry'] ?? '';
$masonry_layout 		= $is_masonry == 'yes' ? 'ezd-column-3 ezd-masonry' : '';
$masonry_attr   		= $is_masonry == 'yes' ? 'ezd-massonry-col="3"' : '';
$layout 				= $is_masonry == 'yes' ? 'masonry' : 'grid';

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
			'post_status'    => ['publish', 'private'],
			'orderby'        => $order_by ?? 'menu_order',
			'order'          => $doc_order ?? 'ASC',
			'post_parent'    => 0,
		]);

		if ( ! empty( $exclude_ids ) && !empty($parent_args->posts)) {
			// Filter posts in PHP instead of using post__not_in
			$parent_args->posts = array_values(array_filter($parent_args->posts, function($post) use ( $exclude_ids ) {
				return ! in_array( (int) $post->ID, $exclude_ids, true );
			}));
		}

		// Update post_count after filtering
		$parent_args->post_count = count($parent_args->posts);


		// arrange the docs
		if ( $parent_args->have_posts() ) :
			while ( $parent_args->have_posts() ) : $parent_args->the_post();
				$sections = get_children( [
					'post_parent' => get_the_ID(),
					'post_type'   => 'docs',
					'numberposts' => 14,
					'post_status' => array( 'publish', 'private' ),
					'orderby'     => $order_by ?? 'menu_order',
					'order'       => $child_order,
                    'posts_per_page' => ! empty( $settings['doc_items_articles'] ) ? $settings['doc_items_articles'] : - 1,
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
						<?php ezd_render_doc_indicators( get_the_ID() ); ?>

                        <div class="doc-top ezd-d-flex ezd-align-items-start">
                            <a class="doc_tag_title" href="<?php the_permalink(); ?>">
                                <h4 class="title ezd_item_title"> <?php the_title(); ?> </h4>
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