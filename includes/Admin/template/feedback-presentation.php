<?php
/**
 * User Feedback Presentation Template
 *
 * This file displays a premium feature presentation for the Users Feedback page
 * when EazyDocs Pro is not active. It showcases all the features and encourages
 * users to upgrade to a premium plan.
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
	<div class="ezd-feedback-presentation">
		<!-- Hero Section -->
		<div class="ezd-fprp-hero">
			<div class="ezd-fprp-hero-bg">
				<div class="ezd-fprp-hero-glow"></div>
			</div>
			<div class="ezd-fprp-hero-content">
				<div class="ezd-fprp-badge">
					<span class="dashicons dashicons-star-filled"></span>
					<?php esc_html_e( 'Premium Feature', 'eazydocs' ); ?>
				</div>
				<h1 class="ezd-fprp-title"><?php esc_html_e( 'Users Feedback Management', 'eazydocs' ); ?></h1>
				<p class="ezd-fprp-subtitle">
					<?php esc_html_e( 'Collect, manage, and analyze user feedback to improve your documentation. Understand what users find helpful and what needs improvement.', 'eazydocs' ); ?>
				</p>
				<div class="ezd-fprp-hero-actions">
					<a href="<?php echo esc_url( admin_url( 'admin.php?page=eazydocs-pricing' ) ); ?>" class="ezd-fprp-btn ezd-fprp-btn-primary">
						<span class="dashicons dashicons-unlock"></span>
						<?php esc_html_e( 'Unlock This Feature', 'eazydocs' ); ?>
					</a>
						<a href="https://wordpress-plugins.spider-themes.net/eazydocs-pro/" target="_blank" class="ezd-fprp-btn ezd-fprp-btn-secondary">
						<span class="dashicons dashicons-external"></span>
						<?php esc_html_e( 'Learn More', 'eazydocs' ); ?>
					</a>
				</div>
			</div>
		</div>

		<!-- Demo Stats Section -->
		<div class="ezd-fprp-section">
			<h2 class="ezd-fprp-section-title">
				<span class="dashicons dashicons-chart-bar"></span>
				<?php esc_html_e( 'Feedback Analytics at a Glance', 'eazydocs' ); ?>
			</h2>
			<p class="ezd-fprp-section-desc">
				<?php esc_html_e( 'Get real-time insights into user satisfaction with powerful analytics dashboards.', 'eazydocs' ); ?>
			</p>
			<div class="ezd-fprp-demo-stats">
				<div class="ezd-fprp-stat ezd-fprp-stat-total">
					<div class="ezd-fprp-stat-icon">
						<span class="dashicons dashicons-format-chat"></span>
					</div>
					<div class="ezd-fprp-stat-content">
						<span class="ezd-fprp-stat-value">247</span>
						<span class="ezd-fprp-stat-label"><?php esc_html_e( 'Total Feedback', 'eazydocs' ); ?></span>
					</div>
				</div>
				<div class="ezd-fprp-stat ezd-fprp-stat-open">
					<div class="ezd-fprp-stat-icon">
						<span class="dashicons dashicons-visibility"></span>
					</div>
					<div class="ezd-fprp-stat-content">
						<span class="ezd-fprp-stat-value">42</span>
						<span class="ezd-fprp-stat-label"><?php esc_html_e( 'New This Week', 'eazydocs' ); ?></span>
					</div>
				</div>
				<div class="ezd-fprp-stat ezd-fprp-stat-archived">
					<div class="ezd-fprp-stat-icon">
						<span class="dashicons dashicons-archive"></span>
					</div>
					<div class="ezd-fprp-stat-content">
						<span class="ezd-fprp-stat-value">186</span>
						<span class="ezd-fprp-stat-label"><?php esc_html_e( 'Resolved', 'eazydocs' ); ?></span>
					</div>
				</div>
				<div class="ezd-fprp-stat ezd-fprp-stat-rate">
					<div class="ezd-fprp-stat-icon">
						<span class="dashicons dashicons-chart-pie"></span>
					</div>
					<div class="ezd-fprp-stat-content">
						<span class="ezd-fprp-stat-value">92%</span>
						<span class="ezd-fprp-stat-label"><?php esc_html_e( 'Satisfaction Rate', 'eazydocs' ); ?></span>
						<div class="ezd-fprp-stat-progress">
							<div class="ezd-fprp-stat-progress-bar" style="width: 92%;"></div>
						</div>
					</div>
				</div>
				<div class="ezd-fprp-demo-overlay">
					<span class="dashicons dashicons-lock"></span>
				</div>
			</div>
		</div>

		<!-- Features Grid -->
		<div class="ezd-fprp-section">
			<h2 class="ezd-fprp-section-title">
				<span class="dashicons dashicons-star-filled"></span>
				<?php esc_html_e( 'Powerful Features', 'eazydocs' ); ?>
			</h2>
			<p class="ezd-fprp-section-desc">
				<?php esc_html_e( 'Everything you need to understand and improve your documentation based on real user feedback.', 'eazydocs' ); ?>
			</p>
			<div class="ezd-fprp-features">
				<div class="ezd-fprp-feature">
					<div class="ezd-fprp-feature-icon ezd-fprp-icon-blue">
						<span class="dashicons dashicons-media-document"></span>
					</div>
					<h3><?php esc_html_e( 'Doc Feedback', 'eazydocs' ); ?></h3>
					<p><?php esc_html_e( 'Collect helpful/unhelpful votes on each documentation page. See which docs are performing well and which need attention.', 'eazydocs' ); ?></p>
				</div>
				<div class="ezd-fprp-feature">
					<div class="ezd-fprp-feature-icon ezd-fprp-icon-green">
						<span class="dashicons dashicons-editor-quote"></span>
					</div>
					<h3><?php esc_html_e( 'Text Feedback', 'eazydocs' ); ?></h3>
					<p><?php esc_html_e( 'Allow users to submit detailed text feedback with email contact. Understand specific pain points and suggestions.', 'eazydocs' ); ?></p>
				</div>
				<div class="ezd-fprp-feature">
					<div class="ezd-fprp-feature-icon ezd-fprp-icon-purple">
						<span class="dashicons dashicons-email"></span>
					</div>
					<h3><?php esc_html_e( 'Email Integration', 'eazydocs' ); ?></h3>
					<p><?php esc_html_e( 'Reply directly to user feedback via email. Build a relationship with your users and show them you care.', 'eazydocs' ); ?></p>
				</div>
				<div class="ezd-fprp-feature">
					<div class="ezd-fprp-feature-icon ezd-fprp-icon-orange">
						<span class="dashicons dashicons-filter"></span>
					</div>
					<h3><?php esc_html_e( 'Advanced Filtering', 'eazydocs' ); ?></h3>
					<p><?php esc_html_e( 'Filter feedback by status, date, doc type, and more. Quickly find what you need with powerful search.', 'eazydocs' ); ?></p>
				</div>
				<div class="ezd-fprp-feature">
					<div class="ezd-fprp-feature-icon ezd-fprp-icon-red">
						<span class="dashicons dashicons-download"></span>
					</div>
					<h3><?php esc_html_e( 'CSV Export', 'eazydocs' ); ?></h3>
					<p><?php esc_html_e( 'Export all feedback data to CSV for analysis in Excel or Google Sheets. Create reports and track trends.', 'eazydocs' ); ?></p>
				</div>
				<div class="ezd-fprp-feature">
					<div class="ezd-fprp-feature-icon ezd-fprp-icon-teal">
						<span class="dashicons dashicons-archive"></span>
					</div>
					<h3><?php esc_html_e( 'Archive Management', 'eazydocs' ); ?></h3>
					<p><?php esc_html_e( 'Archive resolved feedback to keep your workspace clean. Bulk actions make managing hundreds of items easy.', 'eazydocs' ); ?></p>
				</div>
			</div>
		</div>

		<!-- Demo Interface Preview -->
		<div class="ezd-fprp-section">
			<h2 class="ezd-fprp-section-title">
				<span class="dashicons dashicons-welcome-view-site"></span>
				<?php esc_html_e( 'Interface Preview', 'eazydocs' ); ?>
			</h2>
			<p class="ezd-fprp-section-desc">
				<?php esc_html_e( 'A beautiful, intuitive interface to manage all your user feedback in one place.', 'eazydocs' ); ?>
			</p>
			<div class="ezd-fprp-demo-card">
				<!-- Demo Tabs -->
				<div class="ezd-fprp-demo-tabs">
					<span class="ezd-fprp-demo-tab is-active">
						<span class="dashicons dashicons-media-document"></span>
						<?php esc_html_e( 'Doc Feedback', 'eazydocs' ); ?>
						<span class="ezd-fprp-tab-count">156</span>
					</span>
					<span class="ezd-fprp-demo-tab">
						<span class="dashicons dashicons-editor-quote"></span>
						<?php esc_html_e( 'Text Feedback', 'eazydocs' ); ?>
						<span class="ezd-fprp-tab-count">91</span>
					</span>
				</div>

				<!-- Demo Toolbar -->
				<div class="ezd-fprp-demo-toolbar">
					<div class="ezd-fprp-demo-filters">
						<span class="ezd-fprp-filter-pill is-active">
							<span class="ezd-fprp-dot ezd-fprp-dot-green"></span>
							<?php esc_html_e( 'Open', 'eazydocs' ); ?>
						</span>
						<span class="ezd-fprp-filter-pill">
							<span class="ezd-fprp-dot ezd-fprp-dot-gray"></span>
							<?php esc_html_e( 'Archived', 'eazydocs' ); ?>
						</span>
					</div>
					<div class="ezd-fprp-demo-search">
						<span class="dashicons dashicons-search"></span>
						<?php esc_html_e( 'Search feedback...', 'eazydocs' ); ?>
					</div>
				</div>

				<!-- Demo List -->
				<div class="ezd-fprp-demo-list">
					<div class="ezd-fprp-demo-item">
						<div class="ezd-fprp-demo-avatar">
							<span class="dashicons dashicons-admin-users"></span>
						</div>
						<div class="ezd-fprp-demo-content">
							<div class="ezd-fprp-demo-header">
								<span class="ezd-fprp-demo-title">
									<?php esc_html_e( 'Getting Started Guide', 'eazydocs' ); ?>
									<span class="ezd-fprp-demo-badge ezd-fprp-badge-green"><?php esc_html_e( 'Helpful', 'eazydocs' ); ?></span>
								</span>
								<span class="ezd-fprp-demo-time"><?php esc_html_e( '2 hours ago', 'eazydocs' ); ?></span>
							</div>
							<div class="ezd-fprp-demo-meta">
								<span><span class="dashicons dashicons-email"></span> user@example.com</span>
								<span><span class="dashicons dashicons-media-document"></span> <?php esc_html_e( 'Documentation', 'eazydocs' ); ?></span>
							</div>
							<p class="ezd-fprp-demo-text"><?php esc_html_e( 'This documentation was incredibly helpful! The step-by-step instructions made it easy to set up everything correctly.', 'eazydocs' ); ?></p>
							<div class="ezd-fprp-demo-actions">
								<span class="ezd-fprp-demo-action-btn">
									<span class="dashicons dashicons-admin-comments"></span>
									<?php esc_html_e( 'Reply', 'eazydocs' ); ?>
								</span>
								<span class="ezd-fprp-demo-action-btn">
									<span class="dashicons dashicons-archive"></span>
									<?php esc_html_e( 'Archive', 'eazydocs' ); ?>
								</span>
							</div>
						</div>
					</div>

					<div class="ezd-fprp-demo-item">
						<div class="ezd-fprp-demo-avatar ezd-fprp-avatar-green">
							<span class="dashicons dashicons-admin-users"></span>
						</div>
						<div class="ezd-fprp-demo-content">
							<div class="ezd-fprp-demo-header">
								<span class="ezd-fprp-demo-title">
									<?php esc_html_e( 'API Reference', 'eazydocs' ); ?>
									<span class="ezd-fprp-demo-badge ezd-fprp-badge-orange"><?php esc_html_e( 'Needs Review', 'eazydocs' ); ?></span>
								</span>
								<span class="ezd-fprp-demo-time"><?php esc_html_e( '5 hours ago', 'eazydocs' ); ?></span>
							</div>
							<div class="ezd-fprp-demo-meta">
								<span><span class="dashicons dashicons-email"></span> developer@company.com</span>
								<span><span class="dashicons dashicons-media-document"></span> <?php esc_html_e( 'API Docs', 'eazydocs' ); ?></span>
							</div>
							<p class="ezd-fprp-demo-text"><?php esc_html_e( 'Could you add more code examples for the authentication section? The current examples are good but more use cases would help.', 'eazydocs' ); ?></p>
							<div class="ezd-fprp-demo-actions">
								<span class="ezd-fprp-demo-action-btn">
									<span class="dashicons dashicons-admin-comments"></span>
									<?php esc_html_e( 'Reply', 'eazydocs' ); ?>
								</span>
								<span class="ezd-fprp-demo-action-btn">
									<span class="dashicons dashicons-archive"></span>
									<?php esc_html_e( 'Archive', 'eazydocs' ); ?>
								</span>
							</div>
						</div>
					</div>

					<div class="ezd-fprp-demo-item">
						<div class="ezd-fprp-demo-avatar ezd-fprp-avatar-purple">
							<span class="dashicons dashicons-admin-users"></span>
						</div>
						<div class="ezd-fprp-demo-content">
							<div class="ezd-fprp-demo-header">
								<span class="ezd-fprp-demo-title">
									<?php esc_html_e( 'Troubleshooting Tips', 'eazydocs' ); ?>
									<span class="ezd-fprp-demo-badge"><?php esc_html_e( 'Text Feedback', 'eazydocs' ); ?></span>
								</span>
								<span class="ezd-fprp-demo-time"><?php esc_html_e( 'Yesterday', 'eazydocs' ); ?></span>
							</div>
							<div class="ezd-fprp-demo-meta">
								<span><span class="dashicons dashicons-email"></span> support@customer.org</span>
								<span><span class="dashicons dashicons-media-document"></span> <?php esc_html_e( 'Help Center', 'eazydocs' ); ?></span>
							</div>
							<p class="ezd-fprp-demo-text"><?php esc_html_e( 'The troubleshooting section saved me hours of debugging! Found the solution to my caching issue right away.', 'eazydocs' ); ?></p>
							<div class="ezd-fprp-demo-actions">
								<span class="ezd-fprp-demo-action-btn">
									<span class="dashicons dashicons-admin-comments"></span>
									<?php esc_html_e( 'Reply', 'eazydocs' ); ?>
								</span>
								<span class="ezd-fprp-demo-action-btn">
									<span class="dashicons dashicons-archive"></span>
									<?php esc_html_e( 'Archive', 'eazydocs' ); ?>
								</span>
							</div>
						</div>
					</div>
				</div>

				<!-- Demo Overlay -->
				<div class="ezd-fprp-demo-full-overlay">
					<div class="ezd-fprp-demo-lock-content">
						<span class="dashicons dashicons-lock"></span>
						<p><?php esc_html_e( 'Unlock to Access', 'eazydocs' ); ?></p>
					</div>
				</div>
			</div>
		</div>

		<!-- Benefits Section -->
		<div class="ezd-fprp-section">
			<div class="ezd-fprp-benefits-section">
				<div class="ezd-fprp-benefits-content">
					<h2><?php esc_html_e( 'Why User Feedback Matters', 'eazydocs' ); ?></h2>
					<ul class="ezd-fprp-benefits-list">
						<li>
							<span class="dashicons dashicons-yes-alt"></span>
							<span><?php esc_html_e( 'Identify documentation gaps and areas that need improvement', 'eazydocs' ); ?></span>
						</li>
						<li>
							<span class="dashicons dashicons-yes-alt"></span>
							<span><?php esc_html_e( 'Reduce support tickets by improving self-service documentation', 'eazydocs' ); ?></span>
						</li>
						<li>
							<span class="dashicons dashicons-yes-alt"></span>
							<span><?php esc_html_e( 'Understand what content resonates with your users', 'eazydocs' ); ?></span>
						</li>
						<li>
							<span class="dashicons dashicons-yes-alt"></span>
							<span><?php esc_html_e( 'Build direct relationships with users through email replies', 'eazydocs' ); ?></span>
						</li>
						<li>
							<span class="dashicons dashicons-yes-alt"></span>
							<span><?php esc_html_e( 'Track satisfaction trends over time with analytics', 'eazydocs' ); ?></span>
						</li>
						<li>
							<span class="dashicons dashicons-yes-alt"></span>
							<span><?php esc_html_e( 'Prioritize content updates based on real user input', 'eazydocs' ); ?></span>
						</li>
					</ul>
				</div>
				<div class="ezd-fprp-benefits-visual">
					<div class="ezd-fprp-benefits-card">
						<div class="ezd-fprp-benefits-icon">
							<span class="dashicons dashicons-thumbs-up"></span>
						</div>
						<div class="ezd-fprp-benefits-stat">85%</div>
						<div class="ezd-fprp-benefits-label">
							<?php esc_html_e( 'of users find docs more helpful after implementing feedback-driven improvements', 'eazydocs' ); ?>
						</div>
					</div>
				</div>
			</div>
		</div>

		<!-- CTA Section -->
		<div class="ezd-fprp-section">
			<div class="ezd-fprp-cta">
				<div class="ezd-fprp-cta-decoration">
					<div class="ezd-fprp-cta-circles">
						<div class="ezd-fprp-circle ezd-fprp-circle-1"></div>
						<div class="ezd-fprp-circle ezd-fprp-circle-2"></div>
						<div class="ezd-fprp-circle ezd-fprp-circle-3"></div>
					</div>
				</div>
				<div class="ezd-fprp-cta-content">
					<h2><?php esc_html_e( 'Ready to Collect User Feedback?', 'eazydocs' ); ?></h2>
					<p><?php esc_html_e( 'Upgrade to EazyDocs Pro and start understanding your users better today.', 'eazydocs' ); ?></p>
					<div class="ezd-fprp-cta-actions">
						<a href="<?php echo esc_url( admin_url( 'admin.php?page=eazydocs-pricing' ) ); ?>" class="ezd-fprp-btn ezd-fprp-btn-cta">
							<span class="dashicons dashicons-cart"></span>
							<?php esc_html_e( 'Get EazyDocs Pro Now', 'eazydocs' ); ?>
						</a>
						<div class="ezd-fprp-cta-features">
							<span>
								<span class="dashicons dashicons-yes"></span>
								<?php esc_html_e( '14-day money-back guarantee', 'eazydocs' ); ?>
							</span>
							<span>
								<span class="dashicons dashicons-yes"></span>
								<?php esc_html_e( 'Priority email or chat support included', 'eazydocs' ); ?>
							</span>
							<span>
								<span class="dashicons dashicons-yes"></span>
								<?php esc_html_e( 'Free updates till the license expires', 'eazydocs' ); ?>
							</span>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>
