<div class="ezd-card">
	<div class="ezd-card-header">
		<h2 class="ezd-card-title">
			<span class="dashicons dashicons-star-filled"></span>
			<?php esc_html_e( 'Top Ranked Docs', 'eazydocs' ); ?>
		</h2>
		<a href="<?php echo esc_url( admin_url( 'admin.php?page=ezd-analytics#analytics-helpful' ) ); ?>" class="ezd-view-all-link">
			<?php esc_html_e( 'View All', 'eazydocs' ); ?>
			<span class="dashicons dashicons-arrow-right-alt"></span>
		</a>
	</div>

	<?php
	// Top docs by positive votes, aggregated in a single query (only the top 5
	// docs that actually have votes are loaded into memory).
	$post_data = ezd_map_ranked_docs_for_display( ezd_get_ranked_docs_by_votes( 'positive', 5 ) );
	?>
	<ul class="ezd-activity-list">
		<?php
		if ( ! empty( $post_data ) ) :
			// Limit to top 5 posts.
			$top_posts = array_slice( $post_data, 0, 5 );
			$rank      = 1;

			foreach ( $top_posts as $post ) :
				$positive    = (int) $post['positive_time'];
				$negative    = (int) $post['negative_time'];
				$total_votes = $positive + $negative;
				$score       = $total_votes > 0 ? round( ( $positive / $total_votes ) * 100 ) : 0;
				?>
				<li class="ezd-activity-item">

					<!-- Rank badge -->
					<div class="ezd-activity-icon-wrapper <?php echo $rank <= 3 ? 'ezd-icon-bg-yellow' : 'ezd-icon-bg-blue'; ?>">
						<?php if ( 1 === $rank ) : ?>
							<span class="dashicons dashicons-awards"></span>
						<?php else : ?>
							<span class="ezd-rank-number"><?php echo esc_html( $rank ); ?></span>
						<?php endif; ?>
					</div>

					<!-- Content -->
					<div class="ezd-activity-content">
						<p class="ezd-activity-text">
							<a target="_blank" href="<?php echo esc_url( $post['post_permalink'] ); ?>">
								<?php echo esc_html( $post['post_title'] ); ?>
							</a>
						</p>

						<!-- Votes -->
						<div class="ezd-activity-meta">
							<div class="ezd-votes">
								<span class="like">
									<span class="dashicons dashicons-thumbs-up t-success"></span>
									<?php echo esc_html( $positive ); ?>
								</span>

								<span class="dislike">
									<span class="dashicons dashicons-thumbs-down t-danger"></span>
									<?php echo esc_html( $negative ); ?>
								</span>
							</div>

							<!-- Progress Bar -->
							<div class="ezd-progress">
								<?php if ( $total_votes > 0 ) : ?>
									<progress value="<?php echo esc_attr( $positive ); ?>" max="<?php echo esc_attr( $total_votes ); ?>"></progress>
								<?php else : ?>
									<span class="ezd-no-rates"><?php esc_html_e( 'No rates', 'eazydocs' ); ?></span>
								<?php endif; ?>
							</div>
						</div>
					</div>
				</li>
				<?php
				++$rank;
			endforeach;
		else :
			?>
			<li class="ezd-activity-item ezd-activity-empty">
				<div class="ezd-empty-state">
					<span class="dashicons dashicons-star-empty"></span>
					<span><?php esc_html_e( 'No ranked docs found yet.', 'eazydocs' ); ?></span>
				</div>
			</li>
			<?php
		endif;
		?>
	</ul>
</div>