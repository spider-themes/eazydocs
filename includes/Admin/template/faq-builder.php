<?php
/**
 * FAQ Builder Page Template
 * Presentation about Advanced Accordion Block plugin with installation guide
 *
 * @package EazyDocs\Admin
 */

// Cannot access directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$plugin_dir = WP_PLUGIN_DIR . '/advanced-accordion-block';
$is_installed = file_exists( $plugin_dir );
$is_active = is_plugin_active( 'advanced-accordion-block/advanced-accordion-block.php' );
?>

<div class="wrap ezd-faq-builder-wrapper">
		<!-- Header -->
	<div class="ezd-faq-header">
		<img src="https://ps.w.org/advanced-accordion-block/assets/icon-256x256.png" alt="Advanced Accordion Block Logo" class="ezd-faq-logo">
		<h1><?php esc_html_e( 'Build FAQs with Advanced Accordion Block', 'eazydocs' ); ?></h1>
		<p><?php esc_html_e( 'Transform your documentation with interactive, user-friendly accordions. Install our recommended plugin and start creating engaging FAQs in minutes.', 'eazydocs' ); ?></p>
	</div>

	<!-- Overview Section -->
	<div class="ezd-section">
		<h2><span class="ezd-section-icon">✨</span><?php esc_html_e( 'Why Accordions Matter for Your Documentation', 'eazydocs' ); ?></h2>
		<p><?php esc_html_e( 'Accordions transform how users interact with your documentation by organizing content into collapsible sections. This reduces cognitive load, minimizes scrolling, and helps visitors find exactly what they need—faster. With the Advanced Accordion Block, you can create stunning, responsive accordions that seamlessly integrate with your EazyDocs setup.', 'eazydocs' ); ?></p>

		<div class="video-container">
			<iframe src="https://www.youtube.com/embed/UQDLpqro9yU" title="Advanced Accordion Block Overview" allowfullscreen></iframe>
		</div>
	</div>

    <!-- CTA Section -->
	<div class="ezd-cta-section">
		<h2><?php esc_html_e( 'Ready to Elevate Your Documentation?', 'eazydocs' ); ?></h2>
		<p><?php esc_html_e( 'Install the Advanced Accordion Block now and start creating professional, interactive accordions in minutes. It\'s completely free and takes just one click.', 'eazydocs' ); ?></p>
		<div class="ezd-cta-buttons">
			<button id="ezd-install-accordion-plugin" class="ezd-btn-installer">
				<span class="button-text"><?php esc_html_e( '🚀 Install for Free', 'eazydocs' ); ?></span>
				<span class="spinner" style="display: none;"></span>
			</button>
			<a href="https://wordpress.org/plugins/advanced-accordion-block/" target="_blank" rel="noopener noreferrer" class="ezd-btn-secondary">
				<?php esc_html_e( '📖 View on WordPress.org', 'eazydocs' ); ?>
			</a>
		</div>
		<div class="ezd-installer-response"></div>
	</div>

	<!-- Key Features Section -->
	<div class="ezd-section">
		<h2><span class="ezd-section-icon">🏆</span><?php esc_html_e( 'Powerful Features That Make a Difference', 'eazydocs' ); ?></h2>
		<div class="ezd-features-grid">
			<div class="ezd-feature-card">
				<h3><?php esc_html_e( '🔌 Seamless Integration', 'eazydocs' ); ?></h3>
				<p><?php esc_html_e( 'Built as a native Gutenberg block, it integrates perfectly with WordPress and EazyDocs—no coding required.', 'eazydocs' ); ?></p>
			</div>

			<div class="ezd-feature-card">
				<h3><?php esc_html_e( '🎨 Highly Customizable', 'eazydocs' ); ?></h3>
				<p><?php esc_html_e( 'Unlimited styling options to match your brand identity. Control colors, spacing, icons, typography, and more.', 'eazydocs' ); ?></p>
			</div>

			<div class="ezd-feature-card">
				<h3><?php esc_html_e( '📚 Nested Accordions', 'eazydocs' ); ?></h3>
				<p><?php esc_html_e( 'Create multi-level accordions to organize complex documentation in a logical, hierarchical structure.', 'eazydocs' ); ?></p>
			</div>

			<div class="ezd-feature-card">
				<h3><?php esc_html_e( '⚡ Lightning Fast', 'eazydocs' ); ?></h3>
				<p><?php esc_html_e( 'Optimized for performance with minimal impact on load times. Your documentation stays fast and responsive.', 'eazydocs' ); ?></p>
			</div>

			<div class="ezd-feature-card">
				<h3><?php esc_html_e( '📱 Fully Responsive', 'eazydocs' ); ?></h3>
				<p><?php esc_html_e( 'Perfect display on all devices—from mobile phones to large desktop screens. Looks great everywhere.', 'eazydocs' ); ?></p>
			</div>

			<div class="ezd-feature-card">
				<h3><?php esc_html_e( '♿ Accessibility Ready', 'eazydocs' ); ?></h3>
				<p><?php esc_html_e( 'Keyboard navigation support ensures everyone can access your content, meeting WCAG accessibility standards.', 'eazydocs' ); ?></p>
			</div>
		</div>
	</div>

	<!-- Screenshots Section -->
	<div class="ezd-section">
		<h2><span class="ezd-section-icon">🖼️</span><?php esc_html_e( 'See It in Action', 'eazydocs' ); ?></h2>
		<p><?php esc_html_e( 'Explore real examples of beautiful accordions created with Advanced Accordion Block:', 'eazydocs' ); ?></p>
		<div class="ezd-screenshots">
			<div class="ezd-screenshot-item">
				<a href="https://ps.w.org/advanced-accordion-block/assets/screenshot-1.png" target="_blank" rel="noopener noreferrer">
					<img src="https://ps.w.org/advanced-accordion-block/assets/screenshot-1.png" alt="Default Group Accordion View">
				</a>
				<p><?php esc_html_e( 'Clean & Modern Design', 'eazydocs' ); ?></p>
			</div>
			<div class="ezd-screenshot-item">
				<a href="https://ps.w.org/advanced-accordion-block/assets/screenshot-2.png" target="_blank" rel="noopener noreferrer">
					<img src="https://ps.w.org/advanced-accordion-block/assets/screenshot-2.png" alt="Pre-Built Patterns">
				</a>
				<p><?php esc_html_e( 'Pre-built Patterns', 'eazydocs' ); ?></p>
			</div>
			<div class="ezd-screenshot-item">
				<a href="https://ps.w.org/advanced-accordion-block/assets/screenshot-3.png" target="_blank" rel="noopener noreferrer">
					<img src="https://ps.w.org/advanced-accordion-block/assets/screenshot-3.png" alt="Advanced Features">
				</a>
				<p><?php esc_html_e( 'Advanced Features', 'eazydocs' ); ?></p>
			</div>
			<div class="ezd-screenshot-item">
				<a href="https://ps.w.org/advanced-accordion-block/assets/screenshot-4.png" target="_blank" rel="noopener noreferrer">
					<img src="https://ps.w.org/advanced-accordion-block/assets/screenshot-4.png" alt="Pattern Design 2">
				</a>
				<p><?php esc_html_e( 'Engaging Layouts', 'eazydocs' ); ?></p>
			</div>
		</div>
	</div>
</div>
