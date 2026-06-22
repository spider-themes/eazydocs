<?php
/**
 * Performance Overview Widget
 *
 * Renders three honest, per-day series (Views, Searches, Failed Searches)
 * for the last 7 or 30 days. Both ranges are precomputed (cached) in
 * ezd_get_dashboard_data() so switching tabs needs no extra queries and the
 * initial render always matches the active tab.
 *
 * @package EazyDocs
 *
 * @var array $ezd_dashboard Shared dashboard data payload.
 */

defined( 'ABSPATH' ) || exit;

$ezd_week  = $ezd_dashboard['week'] ?? [ 'labels' => [], 'views' => [], 'searches' => [], 'failed' => [] ];
$ezd_month = $ezd_dashboard['month'] ?? [ 'labels' => [], 'views' => [], 'searches' => [], 'failed' => [] ];
?>
<div class="ezd-card ezd-grid-col-lg-2">
	<div class="ezd-card-header">
		<h2 class="ezd-card-title">
			<span class="dashicons dashicons-chart-area"></span>
			<?php esc_html_e( 'Performance Overview', 'eazydocs' ); ?>
		</h2>
		<div class="ezd-stat-filter-container">
			<div class="ezd-stat-filter" role="tablist" aria-label="<?php esc_attr_e( 'Performance time range', 'eazydocs' ); ?>">
				<button type="button" class="is-active" role="tab" aria-selected="true" data-range="week">
					<?php esc_html_e( 'This Week', 'eazydocs' ); ?>
				</button>
				<button type="button" role="tab" aria-selected="false" data-range="month">
					<?php esc_html_e( 'Last 30 Days', 'eazydocs' ); ?>
				</button>
			</div>
		</div>
	</div>

	<div class="ezd-chart-container">
		<div id="OvervIewChart"></div>
	</div>
</div>

<script>
	( function () {
		// Precomputed, correctly bucketed daily series for both ranges.
		var ezdOverviewData = {
			week: {
				labels: <?php echo wp_json_encode( $ezd_week['labels'] ); ?>,
				views: <?php echo wp_json_encode( $ezd_week['views'] ); ?>,
				searches: <?php echo wp_json_encode( $ezd_week['searches'] ); ?>,
				failed: <?php echo wp_json_encode( $ezd_week['failed'] ); ?>
			},
			month: {
				labels: <?php echo wp_json_encode( $ezd_month['labels'] ); ?>,
				views: <?php echo wp_json_encode( $ezd_month['views'] ); ?>,
				searches: <?php echo wp_json_encode( $ezd_month['searches'] ); ?>,
				failed: <?php echo wp_json_encode( $ezd_month['failed'] ); ?>
			}
		};

		var seriesNames = {
			views: '<?php echo esc_js( __( 'Views', 'eazydocs' ) ); ?>',
			searches: '<?php echo esc_js( __( 'Searches', 'eazydocs' ) ); ?>',
			failed: '<?php echo esc_js( __( 'Failed Searches', 'eazydocs' ) ); ?>'
		};

		function buildSeries( range ) {
			var d = ezdOverviewData[ range ];
			return [
				{ name: seriesNames.views, data: d.views },
				{ name: seriesNames.searches, data: d.searches },
				{ name: seriesNames.failed, data: d.failed }
			];
		}

		var options = {
			chart: {
				height: 320,
				type: 'area',
				fontFamily: 'inherit',
				toolbar: { show: false },
				zoom: { enabled: false },
				animations: { enabled: true, easing: 'easeinout', speed: 800 }
			},
			colors: [ '#3b82f6', '#6366f1', '#ef4444' ],
			dataLabels: { enabled: false },
			series: buildSeries( 'week' ),
			fill: {
				type: 'gradient',
				gradient: { shadeIntensity: 1, opacityFrom: 0.4, opacityTo: 0.05, stops: [ 0, 100 ] }
			},
			stroke: { curve: 'smooth', width: 3 },
			xaxis: {
				categories: ezdOverviewData.week.labels,
				axisBorder: { show: false },
				axisTicks: { show: false },
				labels: { style: { colors: '#64748b', fontSize: '11px' } }
			},
			yaxis: {
				labels: {
					style: { colors: '#64748b', fontSize: '11px' },
					formatter: function ( val ) { return Math.round( val ); }
				}
			},
			grid: {
				borderColor: '#f1f5f9',
				strokeDashArray: 4,
				xaxis: { lines: { show: false } }
			},
			legend: {
				position: 'top',
				horizontalAlign: 'right',
				fontSize: '13px',
				fontWeight: 500,
				markers: { radius: 12, width: 10, height: 10 },
				itemMargin: { horizontal: 12 }
			},
			tooltip: {
				theme: 'light',
				x: { show: true },
				y: { formatter: function ( val ) { return val; } }
			},
			markers: { size: 0, hover: { size: 6 } },
			noData: { text: '<?php echo esc_js( __( 'No activity recorded yet.', 'eazydocs' ) ); ?>' }
		};

		var Overviewchart = new ApexCharts( document.querySelector( '#OvervIewChart' ), options );
		Overviewchart.render();

		// Accessible tab switching for the time-range filter.
		var tabs = document.querySelectorAll( '.ezd-stat-filter [data-range]' );
		tabs.forEach( function ( tab ) {
			tab.addEventListener( 'click', function () {
				var range = this.getAttribute( 'data-range' );

				tabs.forEach( function ( t ) {
					t.classList.remove( 'is-active' );
					t.setAttribute( 'aria-selected', 'false' );
				} );
				this.classList.add( 'is-active' );
				this.setAttribute( 'aria-selected', 'true' );

				Overviewchart.updateOptions( {
					xaxis: { categories: ezdOverviewData[ range ].labels }
				} );
				Overviewchart.updateSeries( buildSeries( range ) );
			} );
		} );
	}() );
</script>
