<?php
/**
 * Failed Searches Quick View Widget
 * Shows recent failed searches with quick action to create docs
 *
 * @package EazyDocs
 */

global $wpdb;

// Get table names.
$keyword_table = $wpdb->prefix . 'eazydocs_search_keyword';
$log_table     = $wpdb->prefix . 'eazydocs_search_log';

// Check if tables exist.
$keyword_table_exists = $wpdb->get_var( $wpdb->prepare( 'SHOW TABLES LIKE %s', $keyword_table ) ) === $keyword_table;
$log_table_exists     = $wpdb->get_var( $wpdb->prepare( 'SHOW TABLES LIKE %s', $log_table ) ) === $log_table;

$failed_searches = array();
$total_failed    = 0;

if ( $keyword_table_exists && $log_table_exists ) {
	// Get top 5 failed search keywords by joining both tables.
	$failed_searches = $wpdb->get_results(
		"SELECT k.keyword, SUM(l.not_found_count) as total_failed, MAX(l.created_at) as last_searched
		FROM {$log_table} l
		INNER JOIN {$keyword_table} k ON l.keyword_id = k.id
		WHERE l.not_found_count > 0
		GROUP BY k.keyword
		ORDER BY total_failed DESC, last_searched DESC
		LIMIT 5"
	);

	$total_failed = (int) $wpdb->get_var(
		"SELECT COUNT(DISTINCT k.keyword) 
		FROM {$log_table} l
		INNER JOIN {$keyword_table} k ON l.keyword_id = k.id
		WHERE l.not_found_count > 0"
	);
}

$has_failed_searches = ! empty( $failed_searches );
?>

<div class="ezd-card ezd-failed-searches-card">
	<div class="ezd-card-header">
		<h2 class="ezd-card-title">
			<span class="dashicons dashicons-search"></span>
			<?php esc_html_e( 'Failed Searches', 'eazydocs' ); ?>
		</h2>
		<?php if ( $has_failed_searches ) : ?>
			<span class="ezd-card-badge ezd-badge-warning"><?php echo esc_html( $total_failed ); ?></span>
		<?php endif; ?>
	</div>

	<?php if ( $has_failed_searches ) : ?>
		<div class="ezd-failed-searches-list">
			<?php foreach ( $failed_searches as $search ) : ?>
				<div class="ezd-failed-search-item">
					<div class="ezd-failed-search-info">
						<span class="ezd-failed-search-keyword"><?php echo esc_html( $search->keyword ); ?></span>
						<span class="ezd-failed-search-count">
							<span class="dashicons dashicons-chart-bar"></span>
							<?php
							printf(
								/* translators: %d: number of failed search attempts */
								esc_html__( '%d attempts', 'eazydocs' ),
								(int) $search->total_failed
							);
							?>
						</span>
					</div>
					<div class="ezd-failed-search-actions">
						<?php
						$nonce      = wp_create_nonce( 'parent_doc_nonce' );
						$create_url = admin_url( 'admin.php' ) . '?Create_doc=yes&_wpnonce=' . $nonce . '&parent_title=' . rawurlencode( $search->keyword );
						?>
						<a href="<?php echo esc_url( $create_url ); ?>"
						   class="ezd-btn-create-doc"
						   title="<?php esc_attr_e( 'Create doc for this keyword', 'eazydocs' ); ?>">
							<span class="dashicons dashicons-plus"></span>
							<?php esc_html_e( 'Create', 'eazydocs' ); ?>
						</a>
					</div>
				</div>
			<?php endforeach; ?>
		</div>

		<div class="ezd-card-footer">
			<a href="<?php echo esc_url( admin_url( 'admin.php?page=ezd-analytics&more_state=analytics-search' ) ); ?>" class="ezd-view-all-link">
				<?php esc_html_e( 'View all search analytics', 'eazydocs' ); ?>
				<span class="dashicons dashicons-arrow-right-alt"></span>
			</a>
		</div>
	<?php else : ?>
		<div class="ezd-empty-state">
			<span class="ezd-empty-icon">
				<span class="dashicons dashicons-yes-alt"></span>
			</span>
			<p class="ezd-empty-text"><?php esc_html_e( 'Great! No failed searches recorded.', 'eazydocs' ); ?></p>
			<p class="ezd-empty-subtext"><?php esc_html_e( 'Your documentation is covering user needs well.', 'eazydocs' ); ?></p>
		</div>
	<?php endif; ?>
</div>
