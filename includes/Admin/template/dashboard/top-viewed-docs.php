<div class="ezd-card">
	<div class="ezd-card-header">
		<h2 class="ezd-card-title">
			<span class="dashicons dashicons-visibility"></span>
			<?php esc_html_e( 'Top Viewed Docs', 'eazydocs' ); ?>
		</h2>
		<a href="<?php echo esc_url( admin_url( 'admin.php?page=ezd-analytics#analytics-helpful' ) ); ?>" class="ezd-view-all-link">
			<?php esc_html_e( 'View All', 'eazydocs' ); ?>
			<span class="dashicons dashicons-arrow-right-alt"></span>
		</a>
	</div>
	<?php
	$args = array(
		'post_type'      => 'docs',
		'posts_per_page' => 5,
		'meta_key'       => 'post_views_count',
		'orderby'        => 'meta_value_num',
		'order'          => 'DESC',
	);
	$top_products = new WP_Query( $args );
	?>
	<ul class="ezd-activity-list">
		<?php
		if ( $top_products->have_posts() ) :
			$rank = 1;
			while ( $top_products->have_posts() ) :
				$top_products->the_post();
				$views = get_post_meta( get_the_ID(), 'post_views_count', true );
				$views_display = $views >= 1000 ? round( $views / 1000, 1 ) . 'k' : $views;
				?>
				<li class="ezd-activity-item">
					<div class="ezd-activity-icon-wrapper ezd-icon-bg-blue">
						<span class="ezd-rank-number"><?php echo esc_html( $rank ); ?></span>
					</div>
					<div class="ezd-activity-content">
						<p class="ezd-activity-text">
							<a target="_blank" href="<?php the_permalink(); ?>"><?php the_title(); ?></a>
						</p>
						<p class="ezd-activity-time">
							<span class="dashicons dashicons-visibility ezd-state-view-icon"></span>
							<?php
							echo esc_html( $views_display );
							esc_html_e( ' views', 'eazydocs' );
							?>
						</p>
					</div>
				</li>
				<?php
				++$rank;
			endwhile;
			wp_reset_postdata();
		else :
			?>
			<li class="ezd-activity-item ezd-activity-empty">
				<div class="ezd-empty-state">
					<span class="dashicons dashicons-visibility"></span>
					<span><?php esc_html_e( 'No viewed docs found yet.', 'eazydocs' ); ?></span>
				</div>
			</li>
			<?php
		endif;
		?>
	</ul>
</div>

<style>
	.ezd-rank-number {
		font-size: 14px;
		font-weight: 700;
		color: inherit;
	}
</style>