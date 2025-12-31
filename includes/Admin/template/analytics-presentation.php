<?php
/**
 * Analytics Presentation Template
 *
 * This file displays a premium feature presentation for the Analytics page
 * when EazyDocs Pro/Promax is not active. It showcases all the features and
 * encourages users to upgrade to the Promax plan.
 *
 * @package EazyDocs\Admin\Template
 * @since 1.0.0
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
?>
<div class="wrap">
	<div class="ezd-analytics-presentation">
		<!-- Hero Section -->
		<div class="ezd-anp-hero">
			<div class="ezd-anp-hero-bg">
				<div class="ezd-anp-hero-pattern"></div>
				<div class="ezd-anp-hero-glow"></div>
			</div>
			<div class="ezd-anp-hero-content">
				<div class="ezd-anp-badge">
					<span class="dashicons dashicons-awards"></span>
					<?php esc_html_e( 'Promax Feature', 'eazydocs' ); ?>
				</div>
				<h1 class="ezd-anp-title"><?php esc_html_e( 'Powerful Documentation Analytics', 'eazydocs' ); ?></h1>
				<p class="ezd-anp-subtitle">
					<?php esc_html_e( 'Get deep insights into how users interact with your documentation. Track views, understand search behavior, measure satisfaction, and make data-driven improvements.', 'eazydocs' ); ?>
				</p>
				<div class="ezd-anp-hero-actions">
					<a href="<?php echo esc_url( admin_url( 'admin.php?page=eazydocs-pricing' ) ); ?>" class="ezd-anp-btn ezd-anp-btn-primary">
						<span class="dashicons dashicons-chart-bar"></span>
						<?php esc_html_e( 'Get Promax Plan', 'eazydocs' ); ?>
					</a>
					<a href="https://eazydocs.spider-themes.net/features/analytics/" target="_blank" class="ezd-anp-btn ezd-anp-btn-secondary">
						<span class="dashicons dashicons-external"></span>
						<?php esc_html_e( 'Learn More', 'eazydocs' ); ?>
					</a>
				</div>
			</div>
		</div>

		<!-- Demo Dashboard Preview -->
		<div class="ezd-anp-section">
			<h2 class="ezd-anp-section-title">
				<span class="dashicons dashicons-dashboard"></span>
				<?php esc_html_e( 'Analytics Dashboard Overview', 'eazydocs' ); ?>
			</h2>
			<p class="ezd-anp-section-desc">
				<?php esc_html_e( 'A comprehensive dashboard showing all key metrics at a glance with interactive charts.', 'eazydocs' ); ?>
			</p>

			<!-- Demo Stats Grid -->
			<div class="ezd-anp-demo-stats">
				<div class="ezd-anp-stat ezd-anp-stat-views">
					<div class="ezd-anp-stat-icon">
						<span class="dashicons dashicons-visibility"></span>
					</div>
					<div class="ezd-anp-stat-content">
						<span class="ezd-anp-stat-value">24,893</span>
						<span class="ezd-anp-stat-label"><?php esc_html_e( 'Total Views', 'eazydocs' ); ?></span>
						<span class="ezd-anp-stat-trend ezd-anp-trend-up">
							<span class="dashicons dashicons-arrow-up-alt"></span> +12.5%
						</span>
					</div>
				</div>
				<div class="ezd-anp-stat ezd-anp-stat-satisfaction">
					<div class="ezd-anp-stat-icon">
						<span class="dashicons dashicons-thumbs-up"></span>
					</div>
					<div class="ezd-anp-stat-content">
						<span class="ezd-anp-stat-value">94.2%</span>
						<span class="ezd-anp-stat-label"><?php esc_html_e( 'Satisfaction Rate', 'eazydocs' ); ?></span>
						<span class="ezd-anp-stat-trend ezd-anp-trend-up">
							<span class="dashicons dashicons-arrow-up-alt"></span> +3.1%
						</span>
					</div>
				</div>
				<div class="ezd-anp-stat ezd-anp-stat-search">
					<div class="ezd-anp-stat-icon">
						<span class="dashicons dashicons-search"></span>
					</div>
					<div class="ezd-anp-stat-content">
						<span class="ezd-anp-stat-value">87.6%</span>
						<span class="ezd-anp-stat-label"><?php esc_html_e( 'Search Success', 'eazydocs' ); ?></span>
						<span class="ezd-anp-stat-trend ezd-anp-trend-up">
							<span class="dashicons dashicons-arrow-up-alt"></span> +5.2%
						</span>
					</div>
				</div>
				<div class="ezd-anp-stat ezd-anp-stat-docs">
					<div class="ezd-anp-stat-icon">
						<span class="dashicons dashicons-media-document"></span>
					</div>
					<div class="ezd-anp-stat-content">
						<span class="ezd-anp-stat-value">156</span>
						<span class="ezd-anp-stat-label"><?php esc_html_e( 'Published Docs', 'eazydocs' ); ?></span>
						<span class="ezd-anp-stat-trend ezd-anp-trend-neutral">
							<span class="dashicons dashicons-minus"></span> 0
						</span>
					</div>
				</div>
				<div class="ezd-anp-demo-overlay">
					<span class="dashicons dashicons-lock"></span>
				</div>
			</div>

			<!-- Demo Chart Area -->
			<div class="ezd-anp-demo-chart">
				<div class="ezd-anp-chart-header">
					<div class="ezd-anp-chart-title">
						<span class="dashicons dashicons-chart-area"></span>
						<h4><?php esc_html_e( 'Performance Trends (Last 7 Days)', 'eazydocs' ); ?></h4>
					</div>
					<div class="ezd-anp-chart-legend">
						<span class="ezd-anp-legend-item ezd-anp-legend-views">
							<span class="ezd-anp-legend-dot"></span>
							<?php esc_html_e( 'Views', 'eazydocs' ); ?>
						</span>
						<span class="ezd-anp-legend-item ezd-anp-legend-feedback">
							<span class="ezd-anp-legend-dot"></span>
							<?php esc_html_e( 'Feedback', 'eazydocs' ); ?>
						</span>
						<span class="ezd-anp-legend-item ezd-anp-legend-search">
							<span class="ezd-anp-legend-dot"></span>
							<?php esc_html_e( 'Searches', 'eazydocs' ); ?>
						</span>
					</div>
				</div>
				<div class="ezd-anp-chart-visual">
					<!-- Simulated chart using SVG -->
					<svg viewBox="0 0 600 200" class="ezd-anp-svg-chart">
						<!-- Grid lines -->
						<g class="ezd-anp-grid">
							<line x1="0" y1="50" x2="600" y2="50" />
							<line x1="0" y1="100" x2="600" y2="100" />
							<line x1="0" y1="150" x2="600" y2="150" />
						</g>
						<!-- Views area -->
						<path class="ezd-anp-area ezd-anp-area-views" d="M0,180 L85,140 L170,120 L255,100 L340,80 L425,60 L510,40 L600,30 L600,200 L0,200 Z" />
						<!-- Feedback area -->
						<path class="ezd-anp-area ezd-anp-area-feedback" d="M0,170 L85,160 L170,155 L255,145 L340,140 L425,130 L510,125 L600,120 L600,200 L0,200 Z" />
						<!-- Search area -->
						<path class="ezd-anp-area ezd-anp-area-search" d="M0,175 L85,165 L170,170 L255,155 L340,150 L425,145 L510,140 L600,135 L600,200 L0,200 Z" />
						<!-- Views line -->
						<path class="ezd-anp-line ezd-anp-line-views" d="M0,180 L85,140 L170,120 L255,100 L340,80 L425,60 L510,40 L600,30" />
						<!-- Feedback line -->
						<path class="ezd-anp-line ezd-anp-line-feedback" d="M0,170 L85,160 L170,155 L255,145 L340,140 L425,130 L510,125 L600,120" />
						<!-- Search line -->
						<path class="ezd-anp-line ezd-anp-line-search" d="M0,175 L85,165 L170,170 L255,155 L340,150 L425,145 L510,140 L600,135" />
						<!-- Data points -->
						<g class="ezd-anp-points">
							<circle cx="0" cy="180" r="4" class="ezd-anp-point-views" />
							<circle cx="85" cy="140" r="4" class="ezd-anp-point-views" />
							<circle cx="170" cy="120" r="4" class="ezd-anp-point-views" />
							<circle cx="255" cy="100" r="4" class="ezd-anp-point-views" />
							<circle cx="340" cy="80" r="4" class="ezd-anp-point-views" />
							<circle cx="425" cy="60" r="4" class="ezd-anp-point-views" />
							<circle cx="510" cy="40" r="4" class="ezd-anp-point-views" />
							<circle cx="600" cy="30" r="5" class="ezd-anp-point-views" />
						</g>
					</svg>
					<div class="ezd-anp-chart-labels">
						<span><?php esc_html_e( 'Mon', 'eazydocs' ); ?></span>
						<span><?php esc_html_e( 'Tue', 'eazydocs' ); ?></span>
						<span><?php esc_html_e( 'Wed', 'eazydocs' ); ?></span>
						<span><?php esc_html_e( 'Thu', 'eazydocs' ); ?></span>
						<span><?php esc_html_e( 'Fri', 'eazydocs' ); ?></span>
						<span><?php esc_html_e( 'Sat', 'eazydocs' ); ?></span>
						<span><?php esc_html_e( 'Sun', 'eazydocs' ); ?></span>
					</div>
				</div>
				<div class="ezd-anp-chart-overlay">
					<div class="ezd-anp-chart-lock">
						<span class="dashicons dashicons-lock"></span>
						<p><?php esc_html_e( 'Interactive charts available in Promax', 'eazydocs' ); ?></p>
					</div>
				</div>
			</div>
		</div>

		<!-- Analytics Modules Grid -->
		<div class="ezd-anp-section">
			<h2 class="ezd-anp-section-title">
				<span class="dashicons dashicons-analytics"></span>
				<?php esc_html_e( 'Six Powerful Analytics Modules', 'eazydocs' ); ?>
			</h2>
			<p class="ezd-anp-section-desc">
				<?php esc_html_e( 'Each module provides deep insights into different aspects of your documentation performance.', 'eazydocs' ); ?>
			</p>

			<div class="ezd-anp-modules">
				<!-- Overview Module -->
				<div class="ezd-anp-module">
					<div class="ezd-anp-module-icon ezd-anp-icon-primary">
						<span class="dashicons dashicons-chart-area"></span>
					</div>
					<div class="ezd-anp-module-content">
						<h3><?php esc_html_e( 'Overview Dashboard', 'eazydocs' ); ?></h3>
						<p><?php esc_html_e( 'Get a bird\'s eye view of all key metrics including total views, satisfaction rate, search success, and published docs count with trend indicators.', 'eazydocs' ); ?></p>
						<ul class="ezd-anp-module-features">
							<li><span class="dashicons dashicons-yes"></span> <?php esc_html_e( 'Key performance indicators', 'eazydocs' ); ?></li>
							<li><span class="dashicons dashicons-yes"></span> <?php esc_html_e( 'Interactive trend charts', 'eazydocs' ); ?></li>
							<li><span class="dashicons dashicons-yes"></span> <?php esc_html_e( 'Quick insights & summaries', 'eazydocs' ); ?></li>
						</ul>
					</div>
				</div>

				<!-- Views Module -->
				<div class="ezd-anp-module">
					<div class="ezd-anp-module-icon ezd-anp-icon-blue">
						<span class="dashicons dashicons-visibility"></span>
					</div>
					<div class="ezd-anp-module-content">
						<h3><?php esc_html_e( 'Page Views Analytics', 'eazydocs' ); ?></h3>
						<p><?php esc_html_e( 'Track detailed page view statistics for every documentation page. Identify your most popular content and engagement patterns.', 'eazydocs' ); ?></p>
						<ul class="ezd-anp-module-features">
							<li><span class="dashicons dashicons-yes"></span> <?php esc_html_e( 'Daily/weekly/monthly views', 'eazydocs' ); ?></li>
							<li><span class="dashicons dashicons-yes"></span> <?php esc_html_e( 'Top performing docs', 'eazydocs' ); ?></li>
							<li><span class="dashicons dashicons-yes"></span> <?php esc_html_e( 'View trends over time', 'eazydocs' ); ?></li>
						</ul>
					</div>
				</div>

				<!-- Feedback Module -->
				<div class="ezd-anp-module">
					<div class="ezd-anp-module-icon ezd-anp-icon-green">
						<span class="dashicons dashicons-thumbs-up"></span>
					</div>
					<div class="ezd-anp-module-content">
						<h3><?php esc_html_e( 'Feedback Analytics', 'eazydocs' ); ?></h3>
						<p><?php esc_html_e( 'Monitor user satisfaction with helpful/not helpful vote tracking. Identify which docs need improvement based on real user feedback.', 'eazydocs' ); ?></p>
						<ul class="ezd-anp-module-features">
							<li><span class="dashicons dashicons-yes"></span> <?php esc_html_e( 'Satisfaction rate tracking', 'eazydocs' ); ?></li>
							<li><span class="dashicons dashicons-yes"></span> <?php esc_html_e( 'Positive/negative breakdown', 'eazydocs' ); ?></li>
							<li><span class="dashicons dashicons-yes"></span> <?php esc_html_e( 'Docs needing attention', 'eazydocs' ); ?></li>
						</ul>
					</div>
				</div>

				<!-- Search Module -->
				<div class="ezd-anp-module">
					<div class="ezd-anp-module-icon ezd-anp-icon-purple">
						<span class="dashicons dashicons-search"></span>
					</div>
					<div class="ezd-anp-module-content">
						<h3><?php esc_html_e( 'Search Analytics', 'eazydocs' ); ?></h3>
						<p><?php esc_html_e( 'Understand what users are searching for in your documentation. Discover content gaps and optimize for commonly searched terms.', 'eazydocs' ); ?></p>
						<ul class="ezd-anp-module-features">
							<li><span class="dashicons dashicons-yes"></span> <?php esc_html_e( 'Popular search keywords', 'eazydocs' ); ?></li>
							<li><span class="dashicons dashicons-yes"></span> <?php esc_html_e( 'Failed search tracking', 'eazydocs' ); ?></li>
							<li><span class="dashicons dashicons-yes"></span> <?php esc_html_e( 'Search success rate', 'eazydocs' ); ?></li>
						</ul>
					</div>
				</div>

				<!-- Doc Ranks Module -->
				<div class="ezd-anp-module">
					<div class="ezd-anp-module-icon ezd-anp-icon-orange">
						<span class="dashicons dashicons-star-filled"></span>
					</div>
					<div class="ezd-anp-module-content">
						<h3><?php esc_html_e( 'Doc Rankings', 'eazydocs' ); ?></h3>
						<p><?php esc_html_e( 'See which documentation pages are performing best and which need improvement. Rankings based on views and user feedback combined.', 'eazydocs' ); ?></p>
						<ul class="ezd-anp-module-features">
							<li><span class="dashicons dashicons-yes"></span> <?php esc_html_e( 'Most helpful docs', 'eazydocs' ); ?></li>
							<li><span class="dashicons dashicons-yes"></span> <?php esc_html_e( 'Least helpful docs', 'eazydocs' ); ?></li>
							<li><span class="dashicons dashicons-yes"></span> <?php esc_html_e( 'Performance scores', 'eazydocs' ); ?></li>
						</ul>
					</div>
				</div>

				<!-- Collaboration Module -->
				<div class="ezd-anp-module">
					<div class="ezd-anp-module-icon ezd-anp-icon-teal">
						<span class="dashicons dashicons-groups"></span>
					</div>
					<div class="ezd-anp-module-content">
						<h3><?php esc_html_e( 'Collaboration Metrics', 'eazydocs' ); ?></h3>
						<p><?php esc_html_e( 'Track team collaboration and contribution metrics. See who is actively contributing to your documentation and measure team productivity.', 'eazydocs' ); ?></p>
						<ul class="ezd-anp-module-features">
							<li><span class="dashicons dashicons-yes"></span> <?php esc_html_e( 'Contributor statistics', 'eazydocs' ); ?></li>
							<li><span class="dashicons dashicons-yes"></span> <?php esc_html_e( 'Edit activity tracking', 'eazydocs' ); ?></li>
							<li><span class="dashicons dashicons-yes"></span> <?php esc_html_e( 'Team performance', 'eazydocs' ); ?></li>
						</ul>
					</div>
				</div>
			</div>
		</div>

		<!-- Additional Features Section -->
		<div class="ezd-anp-section">
			<h2 class="ezd-anp-section-title">
				<span class="dashicons dashicons-admin-tools"></span>
				<?php esc_html_e( 'Advanced Features', 'eazydocs' ); ?>
			</h2>
			<p class="ezd-anp-section-desc">
				<?php esc_html_e( 'Tools and features that help you take action on your analytics data.', 'eazydocs' ); ?>
			</p>

			<div class="ezd-anp-features">
				<div class="ezd-anp-feature">
					<div class="ezd-anp-feature-icon">
						<span class="dashicons dashicons-calendar-alt"></span>
					</div>
					<h4><?php esc_html_e( 'Custom Date Ranges', 'eazydocs' ); ?></h4>
					<p><?php esc_html_e( 'Analyze data for any time period with flexible date range selection.', 'eazydocs' ); ?></p>
				</div>
				<div class="ezd-anp-feature">
					<div class="ezd-anp-feature-icon">
						<span class="dashicons dashicons-download"></span>
					</div>
					<h4><?php esc_html_e( 'Export to CSV', 'eazydocs' ); ?></h4>
					<p><?php esc_html_e( 'Download analytics data for external analysis or reporting.', 'eazydocs' ); ?></p>
				</div>
				<div class="ezd-anp-feature">
					<div class="ezd-anp-feature-icon">
						<span class="dashicons dashicons-email-alt"></span>
					</div>
					<h4><?php esc_html_e( 'Email Reports', 'eazydocs' ); ?></h4>
					<p><?php esc_html_e( 'Receive scheduled analytics reports directly in your inbox.', 'eazydocs' ); ?></p>
				</div>
				<div class="ezd-anp-feature">
					<div class="ezd-anp-feature-icon">
						<span class="dashicons dashicons-filter"></span>
					</div>
					<h4><?php esc_html_e( 'Advanced Filtering', 'eazydocs' ); ?></h4>
					<p><?php esc_html_e( 'Filter and sort data by various criteria to find exactly what you need.', 'eazydocs' ); ?></p>
				</div>
				<div class="ezd-anp-feature">
					<div class="ezd-anp-feature-icon">
						<span class="dashicons dashicons-chart-pie"></span>
					</div>
					<h4><?php esc_html_e( 'Visual Charts', 'eazydocs' ); ?></h4>
					<p><?php esc_html_e( 'Beautiful, interactive charts powered by ApexCharts library.', 'eazydocs' ); ?></p>
				</div>
				<div class="ezd-anp-feature">
					<div class="ezd-anp-feature-icon">
						<span class="dashicons dashicons-lightbulb"></span>
					</div>
					<h4><?php esc_html_e( 'Actionable Insights', 'eazydocs' ); ?></h4>
					<p><?php esc_html_e( 'Get smart recommendations based on your analytics data.', 'eazydocs' ); ?></p>
				</div>
			</div>
		</div>

		<!-- Benefits Section -->
		<div class="ezd-anp-section">
			<div class="ezd-anp-benefits-section">
				<div class="ezd-anp-benefits-content">
					<h2><?php esc_html_e( 'Why Analytics Matters', 'eazydocs' ); ?></h2>
					<ul class="ezd-anp-benefits-list">
						<li>
							<span class="dashicons dashicons-yes-alt"></span>
							<span><?php esc_html_e( 'Identify top-performing documentation and replicate success', 'eazydocs' ); ?></span>
						</li>
						<li>
							<span class="dashicons dashicons-yes-alt"></span>
							<span><?php esc_html_e( 'Discover content gaps through failed search analysis', 'eazydocs' ); ?></span>
						</li>
						<li>
							<span class="dashicons dashicons-yes-alt"></span>
							<span><?php esc_html_e( 'Reduce support tickets by improving low-rated docs', 'eazydocs' ); ?></span>
						</li>
						<li>
							<span class="dashicons dashicons-yes-alt"></span>
							<span><?php esc_html_e( 'Make data-driven decisions for content strategy', 'eazydocs' ); ?></span>
						</li>
						<li>
							<span class="dashicons dashicons-yes-alt"></span>
							<span><?php esc_html_e( 'Track team productivity and collaboration', 'eazydocs' ); ?></span>
						</li>
						<li>
							<span class="dashicons dashicons-yes-alt"></span>
							<span><?php esc_html_e( 'Demonstrate documentation ROI to stakeholders', 'eazydocs' ); ?></span>
						</li>
					</ul>
				</div>
				<div class="ezd-anp-benefits-visual">
					<div class="ezd-anp-benefits-card">
						<div class="ezd-anp-benefits-icon">
							<span class="dashicons dashicons-chart-line"></span>
						</div>
						<div class="ezd-anp-benefits-stat">3x</div>
						<div class="ezd-anp-benefits-label">
							<?php esc_html_e( 'faster identification of documentation issues through analytics-driven insights', 'eazydocs' ); ?>
						</div>
					</div>
				</div>
			</div>
		</div>

		<!-- CTA Section -->
		<div class="ezd-anp-section">
			<div class="ezd-anp-cta">
				<div class="ezd-anp-cta-decoration">
					<div class="ezd-anp-cta-circles">
						<div class="ezd-anp-circle ezd-anp-circle-1"></div>
						<div class="ezd-anp-circle ezd-anp-circle-2"></div>
						<div class="ezd-anp-circle ezd-anp-circle-3"></div>
					</div>
				</div>
				<div class="ezd-anp-cta-content">
					<h2><?php esc_html_e( 'Unlock the Full Power of Analytics', 'eazydocs' ); ?></h2>
					<p><?php esc_html_e( 'Get the Promax plan and start making data-driven improvements to your documentation today.', 'eazydocs' ); ?></p>
					<div class="ezd-anp-cta-actions">
						<a href="<?php echo esc_url( admin_url( 'admin.php?page=eazydocs-pricing' ) ); ?>" class="ezd-anp-btn ezd-anp-btn-cta">
							<span class="dashicons dashicons-superhero"></span>
							<?php esc_html_e( 'Get EazyDocs Promax', 'eazydocs' ); ?>
						</a>
						<div class="ezd-anp-cta-features">
							<span>
								<span class="dashicons dashicons-yes"></span>
								<?php esc_html_e( 'All 6 analytics modules', 'eazydocs' ); ?>
							</span>
							<span>
								<span class="dashicons dashicons-yes"></span>
								<?php esc_html_e( 'Priority support', 'eazydocs' ); ?>
							</span>
							<span>
								<span class="dashicons dashicons-yes"></span>
								<?php esc_html_e( 'Lifetime updates', 'eazydocs' ); ?>
							</span>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>
