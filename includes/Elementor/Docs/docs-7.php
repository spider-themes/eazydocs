<?php
/**
 * Cannot access directly.
 */
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$ppp_column     = ! empty( $settings['ppp_column'] ) ? $settings['ppp_column'] : '3';
$articles_limit = ! empty( $settings['doc_items_articles'] ) ? absint( $settings['doc_items_articles'] ) : 4;
$show_count     = ( $settings['md_show_article_count'] ?? 'yes' ) === 'yes';
$show_rd_time   = ( $settings['md_show_read_time'] ?? 'yes' ) === 'yes';
?>

<div class="eazydocs_shortcode">
	<div class="ezd-grid ezd-column-<?php echo esc_attr( $ppp_column ); ?> ezd-card-grid-7">
		<?php
		$exclude_ids = array_map( 'absint', (array) $doc_exclude );

		$parent_args = new WP_Query(
			array(
				'post_type'      => 'docs',
				'posts_per_page' => $doc_number,
				'post_status'    => array( 'publish', 'private' ),
				'orderby'        => $order_by ?? 'menu_order',
				'order'          => $doc_order ?? 'ASC',
				'post_parent'    => 0,
			)
		);

		if ( ! empty( $exclude_ids ) && ! empty( $parent_args->posts ) ) {
			$parent_args->posts      = array_values(
				array_filter(
					$parent_args->posts,
					function ( $p ) use ( $exclude_ids ) {
						return ! in_array( absint( $p->ID ), $exclude_ids, true );
					}
				)
			);
			$parent_args->post_count = count( $parent_args->posts );
		}

		if ( $parent_args->have_posts() ) :
			while ( $parent_args->have_posts() ) :
				$parent_args->the_post();
				$doc_id = get_the_ID();

				// All recursive children — total count & avg read time.
				$all_children  = get_pages(
					array(
						'child_of'    => $doc_id,
						'post_type'   => 'docs',
						'post_status' => array( 'publish', 'private' ),
					)
				);
				$article_count = count( $all_children );

				// Direct children for the visible article list (limited by control).
				$direct_articles = get_children(
					array(
						'post_parent'    => $doc_id,
						'post_type'      => 'docs',
						'post_status'    => array( 'publish', 'private' ),
						'orderby'        => $order_by ?? 'menu_order',
						'order'          => $child_order ?? 'ASC',
						'posts_per_page' => $articles_limit,
					)
				);

				// Avg read time: total word count across all articles ÷ 200 wpm.
				$avg_read_time = 0;
				if ( $show_rd_time && ! empty( $all_children ) ) {
					$total_words = 0;
					foreach ( $all_children as $child ) {
						$total_words += str_word_count( wp_strip_all_tags( $child->post_content ) );
					}
					$avg_read_time = max( 1, (int) ceil( ( $total_words / count( $all_children ) ) / 200 ) );
				}
				?>
				<div class="ezd-docs-card">

					<?php ezd_render_doc_indicators( $doc_id ); ?>

					<div class="ezd-docs-card__head">

						<?php if ( has_post_thumbnail( $doc_id ) ) : ?>
							<div class="ezd-docs-card__icon">
								<?php echo get_the_post_thumbnail( $doc_id, array( 52, 52 ) ); ?>
							</div>
						<?php endif; ?>

						<div class="ezd-docs-card__info">
							<h3 class="ezd-docs-card__title ezd_item_title">
								<a href="<?php the_permalink(); ?>"><?php the_title(); ?></a>
							</h3>

							<?php if ( $show_count ) : ?>
								<span class="ezd-badge">
									<?php
									echo esc_html(
										$article_count . ' ' . _n( 'article', 'articles', $article_count, 'eazydocs' )
									);
									?>
								</span>
							<?php endif; ?>

							<?php if ( $show_rd_time && $avg_read_time > 0 ) : ?>
								<div class="ezd-docs-card__meta">
									<span class="ezd-docs-card__readtime">
										<?php
										echo esc_html(
											sprintf(
												/* translators: %d: average minutes to read */
												_n( '%d minute average read', '%d minutes average read', $avg_read_time, 'eazydocs' ),
												$avg_read_time
											)
										);
										?>
									</span>
								</div>
							<?php endif; ?>
						</div>

					</div><!-- /.ezd-docs-card__head -->

					<?php if ( ! empty( $direct_articles ) ) : ?>
						<div class="ezd-docs-card__sep"></div>
						<ul class="ezd-docs-card__articles ezd-list-unstyled">
							<?php foreach ( $direct_articles as $article ) : ?>
								<li>
									<a href="<?php echo esc_url( get_permalink( $article->ID ) ); ?>" class="ezd_item_list_title">
										<?php echo esc_html( $article->post_title ); ?>
										<?php if ( function_exists( 'ezdpro_badge' ) && ezd_is_premium() ) : ?>
											<?php echo ezdpro_badge( $article->ID ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
										<?php endif; ?>
									</a>
								</li>
							<?php endforeach; ?>
						</ul>
					<?php endif; ?>

					<?php if ( ! empty( $read_more ) ) : ?>
						<div class="ezd-docs-card__sep"></div>
						<a href="<?php the_permalink(); ?>" class="ezd-docs-card__browse ezd_btn">
							<?php echo esc_html( $read_more ); ?>
							<i class="<?php echo esc_attr( ezd_arrow() ); ?>"></i>
						</a>
					<?php endif; ?>

				</div><!-- /.ezd-docs-card -->
			<?php
			endwhile;
			wp_reset_postdata();
		endif;
		?>
	</div>
</div>
