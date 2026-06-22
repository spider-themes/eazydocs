<?php
/**
 * Dashboard Stat Cards
 * Four non-overlapping KPIs with week-over-week trend context.
 *
 * @package EazyDocs
 *
 * @var array $ezd_dashboard Shared dashboard data payload.
 */

defined( 'ABSPATH' ) || exit;

$kpis = $ezd_dashboard['kpis'];

/**
 * Render a small trend badge for a KPI card.
 *
 * @param int|null $delta            Percentage change, or null when there is no baseline.
 * @param bool     $positive_is_good Whether an increase is a good thing (views) or bad (failed searches).
 * @return string Escaped HTML.
 */
if ( ! function_exists( 'ezd_kpi_trend_badge' ) ) {
	function ezd_kpi_trend_badge( $delta, $positive_is_good = true ) {
		if ( null === $delta ) {
			return '';
		}

		$is_up   = $delta > 0;
		$is_flat = 0 === (int) $delta;
		$good    = $positive_is_good ? ( $delta >= 0 ) : ( $delta <= 0 );
		$tone    = $is_flat ? 'flat' : ( $good ? 'up' : 'down' );
		$icon    = $is_flat ? 'minus' : ( $is_up ? 'arrow-up-alt' : 'arrow-down-alt' );
		$sign    = $is_up ? '+' : '';

		return sprintf(
			'<span class="ezd-stat-card__trend ezd-trend--%1$s" title="%5$s"><span class="dashicons dashicons-%2$s" aria-hidden="true"></span>%3$s%4$d%%</span>',
			esc_attr( $tone ),
			esc_attr( $icon ),
			esc_html( $sign ),
			(int) $delta,
			esc_attr__( 'Compared to the previous 7 days', 'eazydocs' )
		);
	}
}
?>
<div class="ezd-stat-grid">
	<!-- Total Docs -->
	<a href="<?php echo esc_url( admin_url( 'admin.php?page=eazydocs-builder' ) ); ?>" class="ezd-stat-card ezd-stat-card--docs">
		<div class="ezd-stat-card__icon">
			<span class="dashicons dashicons-media-document"></span>
		</div>
		<div class="ezd-stat-card__content">
			<span class="ezd-stat-card__label"><?php esc_html_e( 'Total Docs', 'eazydocs' ); ?></span>
			<span class="ezd-stat-card__value"><?php echo esc_html( ezd_format_number( $kpis['total_docs'] ) ); ?></span>
			<span class="ezd-stat-card__meta">
				<?php
				if ( $kpis['new_docs'] > 0 ) {
					echo '<span class="ezd-stat-card__trend ezd-trend--up"><span class="dashicons dashicons-arrow-up-alt" aria-hidden="true"></span>' . esc_html( sprintf( '+%d', $kpis['new_docs'] ) ) . '</span>';
					esc_html_e( 'this week', 'eazydocs' );
				} else {
					esc_html_e( 'No new docs this week', 'eazydocs' );
				}
				?>
			</span>
		</div>
		<span class="ezd-stat-card__arrow"><span class="dashicons dashicons-arrow-right-alt2"></span></span>
	</a>

	<!-- Total Views -->
	<a href="<?php echo esc_url( admin_url( 'admin.php?page=ezd-analytics&more_state=analytics-views' ) ); ?>" class="ezd-stat-card ezd-stat-card--views">
		<div class="ezd-stat-card__icon">
			<span class="dashicons dashicons-visibility"></span>
		</div>
		<div class="ezd-stat-card__content">
			<span class="ezd-stat-card__label"><?php esc_html_e( 'Total Views', 'eazydocs' ); ?></span>
			<span class="ezd-stat-card__value"><?php echo esc_html( ezd_format_number( $kpis['total_views'] ) ); ?></span>
			<span class="ezd-stat-card__meta">
				<?php
				echo wp_kses_post( ezd_kpi_trend_badge( $kpis['views_delta'], true ) );
				esc_html_e( 'vs last week', 'eazydocs' );
				?>
			</span>
		</div>
		<span class="ezd-stat-card__arrow"><span class="dashicons dashicons-arrow-right-alt2"></span></span>
	</a>

	<!-- Helpful Rate -->
	<a href="<?php echo esc_url( admin_url( 'admin.php?page=ezd-analytics&more_state=analytics-feedback' ) ); ?>" class="ezd-stat-card ezd-stat-card--positive">
		<div class="ezd-stat-card__icon">
			<span class="dashicons dashicons-thumbs-up"></span>
		</div>
		<div class="ezd-stat-card__content">
			<span class="ezd-stat-card__label"><?php esc_html_e( 'Helpful Rate', 'eazydocs' ); ?></span>
			<span class="ezd-stat-card__value"><?php echo esc_html( $kpis['helpful_rate'] ); ?><small>%</small></span>
			<span class="ezd-stat-card__meta">
				<?php
				printf(
					/* translators: %s: total number of feedback votes */
					esc_html__( 'from %s votes', 'eazydocs' ),
					esc_html( ezd_format_number( $kpis['total_votes'] ) )
				);
				?>
			</span>
		</div>
		<span class="ezd-stat-card__arrow"><span class="dashicons dashicons-arrow-right-alt2"></span></span>
	</a>

	<!-- Failed Searches -->
	<a href="<?php echo esc_url( admin_url( 'admin.php?page=ezd-analytics&more_state=analytics-search' ) ); ?>" class="ezd-stat-card ezd-stat-card--negative">
		<div class="ezd-stat-card__icon">
			<span class="dashicons dashicons-search"></span>
		</div>
		<div class="ezd-stat-card__content">
			<span class="ezd-stat-card__label"><?php esc_html_e( 'Failed Searches', 'eazydocs' ); ?></span>
			<span class="ezd-stat-card__value"><?php echo esc_html( ezd_format_number( $kpis['failed_total'] ) ); ?></span>
			<span class="ezd-stat-card__meta">
				<?php
				echo wp_kses_post( ezd_kpi_trend_badge( $kpis['failed_delta'], false ) );
				esc_html_e( 'vs last week', 'eazydocs' );
				?>
			</span>
		</div>
		<span class="ezd-stat-card__arrow"><span class="dashicons dashicons-arrow-right-alt2"></span></span>
	</a>
</div>
