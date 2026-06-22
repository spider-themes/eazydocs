<?php
/**
 * EazyDocs Dashboard
 * Main dashboard page.
 *
 * @package EazyDocs
 */

defined( 'ABSPATH' ) || exit;

// All KPI + chart data is computed once (cached for 5 minutes) and shared
// with the widgets below via the $ezd_dashboard variable.
$ezd_dashboard = ezd_get_dashboard_data();
$ezd_has_docs  = $ezd_dashboard['kpis']['total_docs'] > 0;
?>
<div class="ezd-dashboard-container">
	<?php
	// Include header.php.
	include __DIR__ . '/template/dashboard/header.php';
	?>

	<main class="ezd-main-content">
		<?php
		if ( ! $ezd_has_docs ) {
			// First-run onboarding when no docs exist yet.
			include __DIR__ . '/template/dashboard/getting-started.php';
		}

		// Stat cards (KPIs with trend deltas).
		include __DIR__ . '/template/dashboard/card-info.php';
		?>

		<!-- Row 1: Performance Overview + Documentation Health -->
		<div class="ezd-grid ezd-grid-cols-lg-3 ezd-mt-6">
			<?php
			// Performance chart spans two columns; health takes one.
			include __DIR__ . '/template/dashboard/analytics.php';
			include __DIR__ . '/template/dashboard/doc-health.php';
			?>
		</div>

		<!-- Supercharge with AI (full-width band) -->
		<div class="ezd-mt-6">
			<?php include __DIR__ . '/template/dashboard/antimanual-ai.php'; ?>
		</div>

		<!-- Row 2: Search Overview + Failed Searches + System Status -->
		<div class="ezd-grid ezd-grid-cols-lg-3 ezd-mt-6">
			<?php
			include __DIR__ . '/template/dashboard/search-data.php';
			include __DIR__ . '/template/dashboard/failed-searches.php';
			include __DIR__ . '/template/dashboard/system-status.php';
			?>
		</div>

		<!-- Row 3: Recent Activity + Top Ranked + Top Viewed -->
		<div class="ezd-grid ezd-grid-cols-lg-3 ezd-mt-6">
			<?php
			include __DIR__ . '/template/dashboard/recent-activity.php';
			include __DIR__ . '/template/dashboard/top-ranked-docs.php';
			include __DIR__ . '/template/dashboard/top-viewed-docs.php';
			?>
		</div>
	</main>

	<!-- Footer -->
	<footer class="ezd-dashboard-footer">
		<div class="ezd-footer-content">
			<p class="ezd-footer-text">
				<?php
				printf(
					/* translators: %s: EazyDocs link */
					esc_html__( 'Thank you for using %s!', 'eazydocs' ),
					'<a href="https://eazydocs.spider-themes.net/" target="_blank">EazyDocs</a>'
				);
				?>
			</p>
			<p class="ezd-footer-links">
				<a href="https://wordpress.org/support/plugin/eazydocs/reviews/#new-post" target="_blank">
					<span class="dashicons dashicons-star-filled"></span>
					<?php esc_html_e( 'Rate Us', 'eazydocs' ); ?>
				</a>
				<span class="ezd-footer-divider">|</span>
				<a href="https://wordpress.org/support/plugin/eazydocs/" target="_blank">
					<span class="dashicons dashicons-sos"></span>
					<?php esc_html_e( 'Get Support', 'eazydocs' ); ?>
				</a>
			</p>
		</div>
	</footer>
</div>
