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

