<?php
/**
 * Dashboard Stats Cards Template
 * Modern redesigned stat cards with enhanced UI/UX
 *
 * @package EazyDocs
 */

global $wpdb;

// Get total docs count.
$total_docs = (int) $wpdb->get_var( " SELECT COUNT(*) FROM {$wpdb->posts} WHERE post_type = 'docs' AND post_status = 'publish' " );

// Get total views.
$get_views   = $wpdb->get_var( "SELECT SUM(meta_value+0) FROM $wpdb->postmeta WHERE meta_key='post_views_count'" );
$total_views = $get_views >= 1000 ? round( $get_views / 1000, 1 ) . 'k' : ( $get_views ?: '0' );

// Get pending comments count.
$pending_comments = (int) $wpdb->get_var( "SELECT COUNT(*) FROM {$wpdb->comments} WHERE comment_approved = '0' AND comment_post_ID IN (SELECT ID FROM {$wpdb->posts} WHERE post_type = 'docs')" );
?>
<div class="ezd-stat-grid">
	<!-- Total Docs Card -->
	<a href="<?php echo esc_url( admin_url( 'admin.php?page=eazydocs-builder' ) ); ?>" class="ezd-stat-card ezd-stat-card--docs">
		<div class="ezd-stat-card__icon">
			<span class="dashicons dashicons-media-document"></span>
		</div>
		<div class="ezd-stat-card__content">
			<span class="ezd-stat-card__label"><?php esc_html_e( 'Total Docs', 'eazydocs' ); ?></span>
			<span class="ezd-stat-card__value"><?php echo esc_html( $total_docs ); ?></span>
		</div>
		<span class="ezd-stat-card__arrow">
			<span class="dashicons dashicons-arrow-right-alt2"></span>
		</span>
	</a>

	<!-- Total Views Card -->
	<a href="<?php echo esc_url( admin_url( 'admin.php?page=ezd-analytics&more_state=analytics-views' ) ); ?>" class="ezd-stat-card ezd-stat-card--views">
		<div class="ezd-stat-card__icon">
			<span class="dashicons dashicons-visibility"></span>
		</div>
		<div class="ezd-stat-card__content">
			<span class="ezd-stat-card__label"><?php esc_html_e( 'Total Views', 'eazydocs' ); ?></span>
			<span class="ezd-stat-card__value"><?php echo esc_html( $total_views ); ?></span>
		</div>
		<span class="ezd-stat-card__arrow">
			<span class="dashicons dashicons-arrow-right-alt2"></span>
		</span>
	</a>

	<!-- Positive Votes Card -->
	<a href="<?php echo esc_url( admin_url( 'admin.php?page=ezd-analytics&more_state=analytics-feedback' ) ); ?>" class="ezd-stat-card ezd-stat-card--positive">
		<div class="ezd-stat-card__icon">
			<span class="dashicons dashicons-thumbs-up"></span>
		</div>
		<div class="ezd-stat-card__content">
			<span class="ezd-stat-card__label"><?php esc_html_e( 'Positive Votes', 'eazydocs' ); ?></span>
			<span class="ezd-stat-card__value"><?php echo esc_html( $total_liked ); ?><small>%</small></span>
		</div>
		<span class="ezd-stat-card__arrow">
			<span class="dashicons dashicons-arrow-right-alt2"></span>
		</span>
	</a>

	<!-- Negative Votes Card -->
	<a href="<?php echo esc_url( admin_url( 'admin.php?page=ezd-analytics&more_state=analytics-feedback' ) ); ?>" class="ezd-stat-card ezd-stat-card--negative">
		<div class="ezd-stat-card__icon">
			<span class="dashicons dashicons-thumbs-down"></span>
		</div>
		<div class="ezd-stat-card__content">
			<span class="ezd-stat-card__label"><?php esc_html_e( 'Negative Votes', 'eazydocs' ); ?></span>
			<span class="ezd-stat-card__value"><?php echo esc_html( $total_disliked ); ?><small>%</small></span>
		</div>
		<span class="ezd-stat-card__arrow">
			<span class="dashicons dashicons-arrow-right-alt2"></span>
		</span>
	</a>
</div>

<!-- Quick Actions Bar -->
<div class="ezd-quick-actions">
	<div class="ezd-quick-actions-header">
		<h3 class="ezd-quick-actions-title">
			<span class="dashicons dashicons-superhero"></span>
			<?php esc_html_e( 'Quick Actions', 'eazydocs' ); ?>
		</h3>
	</div>
	<div class="ezd-quick-actions-grid">
		<?php if ( current_user_can( 'edit_posts' ) ) : ?>
			<a href="<?php echo esc_url( admin_url( 'post-new.php?post_type=docs' ) ); ?>" class="ezd-quick-action-item">
				<span class="ezd-qa-icon ezd-qa-icon-blue">
					<span class="dashicons dashicons-plus"></span>
				</span>
				<span class="ezd-qa-text"><?php esc_html_e( 'New Article', 'eazydocs' ); ?></span>
			</a>
		<?php endif; ?>

		<a href="<?php echo esc_url( admin_url( 'admin.php?page=eazydocs-builder' ) ); ?>" class="ezd-quick-action-item">
			<span class="ezd-qa-icon ezd-qa-icon-purple">
				<span class="dashicons dashicons-screenoptions"></span>
			</span>
			<span class="ezd-qa-text"><?php esc_html_e( 'Manage Docs', 'eazydocs' ); ?></span>
		</a>

		<a href="<?php echo esc_url( admin_url( 'admin.php?page=ezd-analytics&more_state=analytics-search' ) ); ?>" class="ezd-quick-action-item">
			<span class="ezd-qa-icon ezd-qa-icon-orange">
				<span class="dashicons dashicons-chart-line"></span>
			</span>
			<span class="ezd-qa-text"><?php esc_html_e( 'Search Insights', 'eazydocs' ); ?></span>
		</a>

		<a href="<?php echo esc_url( admin_url( 'admin.php?page=eazydocs-settings' ) ); ?>" class="ezd-quick-action-item">
			<span class="ezd-qa-icon ezd-qa-icon-gray">
				<span class="dashicons dashicons-admin-settings"></span>
			</span>
			<span class="ezd-qa-text"><?php esc_html_e( 'Settings', 'eazydocs' ); ?></span>
		</a>

		<?php if ( $pending_comments > 0 ) : ?>
			<a href="<?php echo esc_url( admin_url( 'edit-comments.php?comment_status=moderated' ) ); ?>" class="ezd-quick-action-item ezd-qa-has-badge">
				<span class="ezd-qa-icon ezd-qa-icon-red">
					<span class="dashicons dashicons-admin-comments"></span>
				</span>
				<span class="ezd-qa-text"><?php esc_html_e( 'Comments', 'eazydocs' ); ?></span>
				<span class="ezd-qa-badge"><?php echo esc_html( $pending_comments ); ?></span>
			</a>
		<?php endif; ?>

		<a href="<?php echo esc_url( admin_url( 'admin.php?page=eazydocs-settings#tab=import-export' ) ); ?>" class="ezd-quick-action-item">
			<span class="ezd-qa-icon ezd-qa-icon-teal">
				<span class="dashicons dashicons-database-export"></span>
			</span>
			<span class="ezd-qa-text"><?php esc_html_e( 'Import/Export', 'eazydocs' ); ?></span>
		</a>
	</div>
</div>
