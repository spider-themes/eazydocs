<?php
/**
 * Search Overview Widget
 * Shows search statistics with donut chart
 *
 * @package EazyDocs
 */

global $wpdb;
// Sentinel Fix: Use deterministic table name with $wpdb->prefix instead of relying on SHOW TABLES output.
$search_log_table    = $wpdb->prefix . 'eazydocs_search_log';
$total_failed_search = $wpdb->get_var( "SELECT count(id) FROM {$search_log_table} WHERE not_found_count > 0" );

if ( empty( $total_failed_search ) ) {
	$total_failed_search = 0;
}

// Get total search count from wp_eazydocs_search_log table and check if empty then set 0.
$total_search = $wpdb->get_var( "SELECT count(id) FROM {$search_log_table}" );
if ( empty( $total_search ) ) {
	$total_search = 0;
}

// Calculate success rate.
$success_rate    = $total_search > 0 ? round( ( ( $total_search - $total_failed_search ) / $total_search ) * 100 ) : 0;
$successful      = $total_search - $total_failed_search;

// Detect empty / zero search data.
$is_empty_data = ( 0 === $total_search && 0 === $total_failed_search );
?>

<div class="ezd-card">
	<div class="ezd-card-header">
		<h2 class="ezd-card-title">
			<span class="dashicons dashicons-search"></span>
			<?php esc_html_e( 'Search Overview', 'eazydocs' ); ?>
		</h2>
		<?php if ( ! $is_empty_data ) : ?>
			<span class="ezd-success-rate <?php echo $success_rate >= 70 ? 'ezd-rate-good' : ( $success_rate >= 50 ? 'ezd-rate-fair' : 'ezd-rate-poor' ); ?>">
				<?php echo esc_html( $success_rate ); ?>% <?php esc_html_e( 'success', 'eazydocs' ); ?>
			</span>
		<?php endif; ?>
	</div>
	<div class="ezd-search-chart-container">
		<div id="ezd-search-chart"></div>
	</div>

	<?php if ( ! $is_empty_data ) : ?>
		<div class="ezd-search-stats">
			<div class="ezd-search-stat">
				<span class="ezd-search-stat-dot" style="background: #6366f1;"></span>
				<span class="ezd-search-stat-label"><?php esc_html_e( 'Total', 'eazydocs' ); ?></span>
				<span class="ezd-search-stat-value"><?php echo esc_html( $total_search ); ?></span>
			</div>
			<div class="ezd-search-stat">
				<span class="ezd-search-stat-dot" style="background: #10b981;"></span>
				<span class="ezd-search-stat-label"><?php esc_html_e( 'Successful', 'eazydocs' ); ?></span>
				<span class="ezd-search-stat-value"><?php echo esc_html( $successful ); ?></span>
			</div>
			<div class="ezd-search-stat">
				<span class="ezd-search-stat-dot" style="background: #ef4444;"></span>
				<span class="ezd-search-stat-label"><?php esc_html_e( 'Failed', 'eazydocs' ); ?></span>
				<span class="ezd-search-stat-value"><?php echo esc_html( $total_failed_search ); ?></span>
			</div>
		</div>
	<?php endif; ?>
</div>

<style>
	.ezd-success-rate {
		padding: 0.25rem 0.625rem;
		border-radius: 6px;
		font-size: 0.75rem;
		font-weight: 600;
	}
	.ezd-rate-good {
		background: #ecfdf5;
		color: #10b981;
	}
	.ezd-rate-fair {
		background: #fffbeb;
		color: #f59e0b;
	}
	.ezd-rate-poor {
		background: #fef2f2;
		color: #ef4444;
	}
	.ezd-search-stats {
		display: flex;
		justify-content: center;
		gap: 1.5rem;
		padding-top: 1rem;
		border-top: 1px solid #f1f5f9;
		margin-top: 0.5rem;
	}
	.ezd-search-stat {
		display: flex;
		align-items: center;
		gap: 0.375rem;
		font-size: 0.75rem;
	}
	.ezd-search-stat-dot {
		width: 8px;
		height: 8px;
		border-radius: 50%;
	}
	.ezd-search-stat-label {
		color: #64748b;
	}
	.ezd-search-stat-value {
		font-weight: 600;
		color: #1e293b;
	}
</style>

<script>
	var is_empty = <?php echo $is_empty_data ? 'true' : 'false'; ?>;

	var options;

	if (is_empty) {
		// Show EMPTY chart (full gray donut).
		options = {
			series: [1],
			chart: {
				width: '100%',
				height: 260,
				type: 'donut',
				fontFamily: 'inherit',
			},
			labels: ['<?php esc_html_e( 'No Search Data', 'eazydocs' ); ?>'],
			colors: ['#f1f5f9'],
			dataLabels: { enabled: false },
			legend: { show: false },
			stroke: { show: false },
			tooltip: { enabled: false }
		};
	} else {
		// Show NORMAL chart.
		options = {
			series: [
				<?php echo esc_js( $successful ); ?>,
				<?php echo esc_js( $total_failed_search ); ?>
			],
			chart: {
				width: '100%',
				height: 260,
				type: 'donut',
				fontFamily: 'inherit',
				animations: {
					enabled: true,
					easing: 'easeinout',
					speed: 800
				}
			},
			labels: ['<?php esc_html_e( 'Successful', 'eazydocs' ); ?>', '<?php esc_html_e( 'Failed', 'eazydocs' ); ?>'],
			colors: ['#10b981', '#ef4444'],
			dataLabels: { enabled: false },
			legend: { show: false },
			stroke: {
				show: true,
				width: 3,
				colors: ['#fff']
			},
			plotOptions: {
				pie: {
					donut: {
						size: '75%',
						labels: {
							show: true,
							name: {
								show: true,
								fontSize: '14px',
								fontWeight: 500,
								color: '#64748b'
							},
							value: {
								show: true,
								fontSize: '28px',
								fontWeight: 700,
								color: '#0f172a',
								formatter: function (val) {
									return val;
								}
							},
							total: {
								show: true,
								label: '<?php esc_html_e( 'Total', 'eazydocs' ); ?>',
								fontSize: '13px',
								fontWeight: 500,
								color: '#64748b',
								formatter: function (w) {
									return w.globals.seriesTotals.reduce((a, b) => a + b, 0);
								}
							}
						}
					}
				}
			},
			tooltip: {
				enabled: true,
				y: {
					formatter: function(val) {
						return val + ' <?php esc_html_e( 'searches', 'eazydocs' ); ?>';
					}
				}
			}
		};
	}

	var search_chart = new ApexCharts(document.querySelector("#ezd-search-chart"), options);
	search_chart.render();
</script>