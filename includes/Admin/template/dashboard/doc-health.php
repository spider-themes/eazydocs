<?php
/**
 * Documentation Health Score Widget
 * Shows the overall health and quality of documentation
 *
 * @package EazyDocs
 */

global $wpdb;

// Calculate documentation health metrics.
$total_docs   = (int) $wpdb->get_var( "SELECT COUNT(*) FROM {$wpdb->posts} WHERE post_type = 'docs' AND post_status = 'publish'" );
$docs_no_views = (int) $wpdb->get_var( 
	"SELECT COUNT(*) FROM {$wpdb->posts} p 
	LEFT JOIN {$wpdb->postmeta} pm ON p.ID = pm.post_id AND pm.meta_key = 'post_views_count'
	WHERE p.post_type = 'docs' AND p.post_status = 'publish' AND (pm.meta_value IS NULL OR pm.meta_value = '0')"
);

// Get docs without feedback.
$docs_with_feedback = (int) $wpdb->get_var(
	"SELECT COUNT(DISTINCT p.ID) FROM {$wpdb->posts} p 
	INNER JOIN {$wpdb->postmeta} pm ON p.ID = pm.post_id 
	WHERE p.post_type = 'docs' AND p.post_status = 'publish' AND pm.meta_key IN ('positive', 'negative') AND pm.meta_value > 0"
);

// Get failed search ratio.
$search_log_table = $wpdb->prefix . 'eazydocs_search_log';
$total_search     = (int) $wpdb->get_var( "SELECT count(id) FROM {$search_log_table}" );
$failed_search    = (int) $wpdb->get_var( "SELECT count(id) FROM {$search_log_table} WHERE not_found_count > 0" );

// Get docs older than 90 days without updates.
$stale_docs = (int) $wpdb->get_var(
	$wpdb->prepare(
		"SELECT COUNT(*) FROM {$wpdb->posts} WHERE post_type = 'docs' AND post_status = 'publish' AND post_modified < %s",
		gmdate( 'Y-m-d', strtotime( '-90 days' ) )
	)
);

// Calculate health score (0-100).
$health_score = 100;
$health_issues = array();

// Penalty for stale content.
if ( $total_docs > 0 && $stale_docs > 0 ) {
	$stale_percentage = ( $stale_docs / $total_docs ) * 100;
	$health_score -= min( 20, $stale_percentage / 2 );
	if ( $stale_percentage > 20 ) {
		$health_issues[] = array(
			'type'    => 'warning',
			'icon'    => 'dashicons-clock',
			'message' => sprintf(
				/* translators: %d: number of stale docs */
				__( '%d articles haven\'t been updated in 90+ days', 'eazydocs' ),
				$stale_docs
			),
			'action'  => admin_url( 'edit.php?post_type=docs&orderby=modified&order=asc' ),
			'action_text' => __( 'Review', 'eazydocs' ),
		);
	}
}

// Penalty for low engagement.
if ( $total_docs > 0 && $docs_no_views > 0 ) {
	$no_views_percentage = ( $docs_no_views / $total_docs ) * 100;
	$health_score -= min( 15, $no_views_percentage / 3 );
	if ( $no_views_percentage > 30 ) {
		$health_issues[] = array(
			'type'    => 'info',
			'icon'    => 'dashicons-visibility',
			'message' => sprintf(
				/* translators: %d: number of docs with no views */
				__( '%d articles have no views yet', 'eazydocs' ),
				$docs_no_views
			),
			'action'  => null,
			'action_text' => null,
		);
	}
}

// Penalty for failed searches.
if ( $total_search > 0 && $failed_search > 0 ) {
	$failed_percentage = ( $failed_search / $total_search ) * 100;
	$health_score -= min( 25, $failed_percentage / 2 );
	if ( $failed_percentage > 10 ) {
		$health_issues[] = array(
			'type'    => 'error',
			'icon'    => 'dashicons-search',
			'message' => sprintf(
				/* translators: %d: percentage of failed searches */
				__( '%d%% of searches aren\'t finding results', 'eazydocs' ),
				round( $failed_percentage )
			),
			'action'  => admin_url( 'admin.php?page=ezd-analytics&more_state=analytics-search' ),
			'action_text' => __( 'View Insights', 'eazydocs' ),
		);
	}
}

// Bonus for having feedback.
if ( $total_docs > 0 && $docs_with_feedback > 0 ) {
	$feedback_coverage = ( $docs_with_feedback / $total_docs ) * 100;
	if ( $feedback_coverage > 50 ) {
		$health_score += min( 10, $feedback_coverage / 10 );
	}
}

$health_score = max( 0, min( 100, round( $health_score ) ) );

// Determine health level.
if ( $health_score >= 80 ) {
	$health_level = 'excellent';
	$health_label = __( 'Excellent', 'eazydocs' );
	$health_color = '#10b981';
} elseif ( $health_score >= 60 ) {
	$health_level = 'good';
	$health_label = __( 'Good', 'eazydocs' );
	$health_color = '#3b82f6';
} elseif ( $health_score >= 40 ) {
	$health_level = 'fair';
	$health_label = __( 'Fair', 'eazydocs' );
	$health_color = '#f59e0b';
} else {
	$health_level = 'poor';
	$health_label = __( 'Needs Work', 'eazydocs' );
	$health_color = '#ef4444';
}
?>

<div class="ezd-card ezd-health-card">
	<div class="ezd-card-header">
		<h2 class="ezd-card-title">
			<span class="dashicons dashicons-heart"></span>
			<?php esc_html_e( 'Documentation Health', 'eazydocs' ); ?>
		</h2>
	</div>

	<div class="ezd-health-content">
		<!-- Health Score Circle -->
		<div class="ezd-health-score-container">
			<div class="ezd-health-circle" data-score="<?php echo esc_attr( $health_score ); ?>" data-color="<?php echo esc_attr( $health_color ); ?>">
				<svg class="ezd-health-svg" viewBox="0 0 100 100">
					<circle class="ezd-health-bg" cx="50" cy="50" r="45" />
					<circle class="ezd-health-progress" cx="50" cy="50" r="45" 
						stroke-dasharray="283" 
						stroke-dashoffset="<?php echo esc_attr( 283 - ( 283 * $health_score / 100 ) ); ?>"
						style="stroke: <?php echo esc_attr( $health_color ); ?>;" />
				</svg>
				<div class="ezd-health-score-text">
					<span class="ezd-health-score-value"><?php echo esc_html( $health_score ); ?></span>
					<span class="ezd-health-score-label" style="color: <?php echo esc_attr( $health_color ); ?>;"><?php echo esc_html( $health_label ); ?></span>
				</div>
			</div>
		</div>

		<!-- Health Metrics -->
		<div class="ezd-health-metrics">
			<div class="ezd-health-metric">
				<span class="ezd-health-metric-icon ezd-icon-blue">
					<span class="dashicons dashicons-media-document"></span>
				</span>
				<div class="ezd-health-metric-content">
					<span class="ezd-health-metric-value"><?php echo esc_html( $total_docs ); ?></span>
					<span class="ezd-health-metric-label"><?php esc_html_e( 'Published Articles', 'eazydocs' ); ?></span>
				</div>
			</div>
			
			<div class="ezd-health-metric">
				<span class="ezd-health-metric-icon ezd-icon-green">
					<span class="dashicons dashicons-thumbs-up"></span>
				</span>
				<div class="ezd-health-metric-content">
					<span class="ezd-health-metric-value"><?php echo esc_html( $docs_with_feedback ); ?></span>
					<span class="ezd-health-metric-label"><?php esc_html_e( 'With Feedback', 'eazydocs' ); ?></span>
				</div>
			</div>
			
			<div class="ezd-health-metric">
				<span class="ezd-health-metric-icon ezd-icon-orange">
					<span class="dashicons dashicons-clock"></span>
				</span>
				<div class="ezd-health-metric-content">
					<span class="ezd-health-metric-value"><?php echo esc_html( $stale_docs ); ?></span>
					<span class="ezd-health-metric-label"><?php esc_html_e( 'Need Review', 'eazydocs' ); ?></span>
				</div>
			</div>
		</div>

		<!-- Health Issues -->
		<?php if ( ! empty( $health_issues ) ) : ?>
			<div class="ezd-health-issues">
				<h4 class="ezd-health-issues-title"><?php esc_html_e( 'Recommendations', 'eazydocs' ); ?></h4>
				<?php foreach ( $health_issues as $issue ) : ?>
					<div class="ezd-health-issue ezd-health-issue-<?php echo esc_attr( $issue['type'] ); ?>">
						<span class="dashicons <?php echo esc_attr( $issue['icon'] ); ?>"></span>
						<span class="ezd-health-issue-text"><?php echo esc_html( $issue['message'] ); ?></span>
						<?php if ( $issue['action'] ) : ?>
							<a href="<?php echo esc_url( $issue['action'] ); ?>" class="ezd-health-issue-action">
								<?php echo esc_html( $issue['action_text'] ); ?>
							</a>
						<?php endif; ?>
					</div>
				<?php endforeach; ?>
			</div>
		<?php else : ?>
			<div class="ezd-health-success">
				<span class="dashicons dashicons-yes-alt"></span>
				<span><?php esc_html_e( 'Great job! Your documentation is in excellent shape.', 'eazydocs' ); ?></span>
			</div>
		<?php endif; ?>
	</div>
</div>
