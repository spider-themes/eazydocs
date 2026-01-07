<?php
/**
 * Setup Wizard Main Template
 *
 * @package EazyDocs
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$slugType              = ezd_is_premium() ? ezd_get_opt( 'docs-url-structure' ) : 'custom-slug';
$custom_slug           = ezd_is_premium() ? ezd_get_opt( 'docs-type-slug' ) : 'docs';
$brand_color           = ezd_get_opt( 'brand_color' );
$docs_single_layout    = ezd_is_premium() ? ezd_get_opt( 'docs_single_layout' ) : 'both_sidebar';
$docs_page_width       = ezd_get_opt( 'docs_page_width' );
$customizer_visibility = ezd_get_opt( 'customizer_visibility' );
$docs_archive_page     = ezd_get_opt( 'docs-slug' );

$wizard_steps = array(
	1 => array(
		'title' => esc_html__( 'Welcome', 'eazydocs' ),
		'icon'  => 'welcome-learn-more',
	),
	2 => array(
		'title' => esc_html__( 'Basic Setup', 'eazydocs' ),
		'icon'  => 'admin-settings',
	),
	3 => array(
		'title' => esc_html__( 'Layout', 'eazydocs' ),
		'icon'  => 'layout',
	),
	4 => array(
		'title' => esc_html__( 'Plugins', 'eazydocs' ),
		'icon'  => 'admin-plugins',
	),
	5 => array(
		'title' => esc_html__( 'Finish', 'eazydocs' ),
		'icon'  => 'yes-alt',
	),
);
?>
<div class="wrap ezd-setup-page">
	<!-- Modern Header -->
	<div class="ezd-setup-wizard-wrapper">
		<div class="ezd-setup-wizard-header">
			<div class="ezd-setup-logo">
				<img src="<?php echo esc_url( EAZYDOCS_IMG . '/eazydocs-favicon.png' ); ?>" alt="<?php echo esc_attr__( 'EazyDocs icon', 'eazydocs' ); ?>"/>
				<span class="ezd-logo-text"><?php esc_html_e( 'EazyDocs', 'eazydocs' ); ?></span>
				<span class="ezd-setup-badge"><?php esc_html_e( 'Setup Wizard', 'eazydocs' ); ?></span>
			</div>

			<div class="ezd-setup-header-actions">
				<a href="https://eazydocs.spider-themes.net/changelog/" target="_blank" class="ezd-header-link">
					<span class="dashicons dashicons-megaphone"></span>
					<span><?php esc_html_e( "What's New", 'eazydocs' ); ?></span>
				</a>
				<a href="<?php echo esc_url( admin_url( 'admin.php?page=eazydocs' ) ); ?>" class="ezd-skip-btn">
					<span><?php esc_html_e( 'Skip Setup', 'eazydocs' ); ?></span>
					<span class="dashicons dashicons-arrow-right-alt"></span>
				</a>
			</div>
		</div>
	</div>

	<!-- Setup Wizard Container -->
	<div id="ezd-setup-wizard-wrap">
		<!-- Progress Steps -->
		<div class="ezd-wizard-progress">
			<div class="ezd-progress-bar">
				<div class="ezd-progress-fill"></div>
			</div>
			<ul class="ezd-progress-steps">
				<?php foreach ( $wizard_steps as $step_num => $step ) : ?>
					<li class="ezd-progress-step <?php echo 1 === $step_num ? 'active' : ''; ?>" data-step="<?php echo esc_attr( $step_num ); ?>">
						<div class="ezd-step-indicator">
							<span class="ezd-step-number"><?php echo esc_html( $step_num ); ?></span>
							<span class="ezd-step-check dashicons dashicons-yes"></span>
						</div>
						<span class="ezd-step-title"><?php echo esc_html( $step['title'] ); ?></span>
					</li>
				<?php endforeach; ?>
			</ul>
		</div>

		<!-- Hidden Navigation for Smart Wizard -->
		<div class="sw-toolbar">
			<ul class="nav sr-only">
				<li><a class="nav-link" href="#step-1"></a></li>
				<li><a class="nav-link" href="#step-2"></a></li>
				<li><a class="nav-link" href="#step-3"></a></li>
				<li><a class="nav-link" href="#step-4"></a></li>
				<li><a class="nav-link" href="#step-5"></a></li>
			</ul>
		</div>

		<!-- Step Content -->
		<div class="tab-content">
			<?php
			require 'step1_welcome.php';
			require 'step2_settings.php';
			require 'step3_settings.php';
			require 'step4_install_plugins.php';
			?>

			<!-- Step 5: Finish -->
			<div id="step-5" class="tab-pane ezd-finish-step" role="tabpanel" style="display:none">
				<div class="ezd-finish-content">
					<div class="ezd-success-animation">
						<div class="ezd-success-circle">
							<span class="dashicons dashicons-yes-alt"></span>
						</div>
						<div class="ezd-confetti-container" id="confetti-container"></div>
					</div>

					<h2><?php esc_html_e( 'Setup Complete!', 'eazydocs' ); ?></h2>
					<p class="ezd-finish-subtitle"><?php esc_html_e( 'Your documentation site is ready. Here\'s what you can do next:', 'eazydocs' ); ?></p>

					<div class="ezd-next-steps-grid">
						<a href="<?php echo esc_url( admin_url( 'admin.php?page=eazydocs-builder' ) ); ?>" class="ezd-next-step-card ezd-primary-action">
							<div class="ezd-card-icon">
								<span class="dashicons dashicons-plus-alt"></span>
							</div>
							<div class="ezd-card-content">
								<h4><?php esc_html_e( 'Create Your First Doc', 'eazydocs' ); ?></h4>
								<p><?php esc_html_e( 'Start building your documentation now', 'eazydocs' ); ?></p>
							</div>
							<span class="dashicons dashicons-arrow-right-alt2"></span>
						</a>

						<a href="<?php echo esc_url( admin_url( 'admin.php?page=eazydocs-settings' ) ); ?>" class="ezd-next-step-card">
							<div class="ezd-card-icon">
								<span class="dashicons dashicons-admin-generic"></span>
							</div>
							<div class="ezd-card-content">
								<h4><?php esc_html_e( 'Advanced Settings', 'eazydocs' ); ?></h4>
								<p><?php esc_html_e( 'Customize more options', 'eazydocs' ); ?></p>
							</div>
							<span class="dashicons dashicons-arrow-right-alt2"></span>
						</a>

						<a href="https://helpdesk.spider-themes.net/docs/eazydocs-wordpress-plugin/" target="_blank" class="ezd-next-step-card">
							<div class="ezd-card-icon">
								<span class="dashicons dashicons-book"></span>
							</div>
							<div class="ezd-card-content">
								<h4><?php esc_html_e( 'View Documentation', 'eazydocs' ); ?></h4>
								<p><?php esc_html_e( 'Learn how to use EazyDocs', 'eazydocs' ); ?></p>
							</div>
							<span class="dashicons dashicons-arrow-right-alt2"></span>
						</a>

						<?php if ( ! ezd_is_premium() ) : ?>
							<a href="https://eazydocs.spider-themes.net/pricing" target="_blank" class="ezd-next-step-card ezd-pro-card">
								<div class="ezd-card-icon">
									<span class="dashicons dashicons-star-filled"></span>
								</div>
								<div class="ezd-card-content">
									<h4><?php esc_html_e( 'Upgrade to Pro', 'eazydocs' ); ?></h4>
									<p><?php esc_html_e( 'Unlock all premium features', 'eazydocs' ); ?></p>
								</div>
								<span class="dashicons dashicons-arrow-right-alt2"></span>
							</a>
						<?php endif; ?>
					</div>

					<div class="ezd-finish-actions">
						<button type="button" id="finish-btn" class="ezd-btn ezd-btn-primary ezd-btn-lg">
							<span class="dashicons dashicons-yes"></span>
							<?php esc_html_e( 'Finish & Go to Dashboard', 'eazydocs' ); ?>
						</button>
					</div>
				</div>
			</div>
		</div>

		<!-- Navigation Footer -->
		<div class="toolbar-bottom ezd-wizard-footer">
			<div class="ezd-footer-left">
				<button class="sw-btn sw-btn-prev ezd-btn ezd-btn-outline disabled">
					<span class="dashicons dashicons-arrow-left-alt"></span>
					<?php esc_html_e( 'Previous', 'eazydocs' ); ?>
				</button>
			</div>
			<div class="ezd-footer-center">
				<span class="ezd-step-counter">
					<?php /* translators: %1$d: current step, %2$d: total steps */ ?>
					<span class="current-step">1</span> / 5
				</span>
			</div>
			<div class="ezd-footer-right">
				<button class="sw-btn sw-btn-next ezd-btn ezd-btn-primary">
					<?php esc_html_e( 'Next', 'eazydocs' ); ?>
					<span class="dashicons dashicons-arrow-right-alt"></span>
				</button>
			</div>
		</div>
	</div>

	<!-- Quick Tips Panel -->
	<div class="ezd-setup-tips">
		<div class="ezd-tips-header">
			<span class="dashicons dashicons-lightbulb"></span>
			<h4><?php esc_html_e( 'Quick Tips', 'eazydocs' ); ?></h4>
			<button type="button" class="ezd-tips-toggle" aria-expanded="true" aria-label="<?php esc_attr_e( 'Toggle Quick Tips', 'eazydocs' ); ?>">
				<span class="dashicons dashicons-arrow-down-alt2"></span>
			</button>
		</div>
		<ul class="ezd-tips-list">
			<li data-step="1">
				<span class="dashicons dashicons-info"></span>
				<?php esc_html_e( 'Watch the video tutorial to get started quickly with EazyDocs.', 'eazydocs' ); ?>
			</li>
			<li data-step="2">
				<span class="dashicons dashicons-info"></span>
				<?php esc_html_e( 'Choose a brand color that matches your website theme for consistency.', 'eazydocs' ); ?>
			</li>
			<li data-step="3">
				<span class="dashicons dashicons-info"></span>
				<?php esc_html_e( 'The layout can be changed later in the settings page.', 'eazydocs' ); ?>
			</li>
			<li data-step="4">
				<span class="dashicons dashicons-info"></span>
				<?php esc_html_e( 'You can skip plugin installation now and install them later.', 'eazydocs' ); ?>
			</li>
			<li data-step="5">
				<span class="dashicons dashicons-info"></span>
				<?php esc_html_e( 'All settings can be modified from the EazyDocs settings page.', 'eazydocs' ); ?>
			</li>
		</ul>
	</div>
</div>