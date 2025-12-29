<?php
/**
 * Step 3: Layout Selection
 *
 * @package EazyDocs
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
?>
<div id="step-3" class="tab-pane ezd-layout-step" role="tabpanel" style="display:none">
	<div class="ezd-step-header">
		<div class="ezd-step-icon">
			<span class="dashicons dashicons-layout"></span>
		</div>
		<h2><?php esc_html_e( 'Choose Your Layout', 'eazydocs' ); ?></h2>
		<p class="ezd-step-description"><?php esc_html_e( 'Select how your documentation pages will look. Pick a layout that suits your content best.', 'eazydocs' ); ?></p>
	</div>

	<div class="ezd-layout-content">
		<!-- Page Layout Selection -->
		<div class="ezd-setting-card ezd-layout-card">
			<div class="ezd-setting-header">
				<div class="ezd-setting-icon">
					<span class="dashicons dashicons-welcome-widgets-menus"></span>
				</div>
				<div class="ezd-setting-title">
					<h3><?php esc_html_e( 'Sidebar Layout', 'eazydocs' ); ?></h3>
					<p><?php esc_html_e( 'Choose the sidebar configuration for documentation pages.', 'eazydocs' ); ?></p>
				</div>
			</div>
			<div class="ezd-setting-body">
				<div class="page-layout-wrap ezd-layout-options">
					<label for="both_sidebar" class="ezd-layout-option <?php echo 'both_sidebar' === $docs_single_layout ? 'active' : ''; ?>">
						<input type="radio" id="both_sidebar" value="both_sidebar" name="docs_single_layout" <?php checked( $docs_single_layout, 'both_sidebar' ); ?>>
						<div class="ezd-layout-preview">
							<img src="<?php echo esc_url( EAZYDOCS_IMG . '/customizer/both_sidebar.jpg' ); ?>" alt="<?php esc_attr_e( 'Both Sidebars', 'eazydocs' ); ?>" />
						</div>
						<span class="ezd-layout-name"><?php esc_html_e( 'Both Sidebars', 'eazydocs' ); ?></span>
						<span class="ezd-layout-desc"><?php esc_html_e( 'Left: Navigation | Right: TOC', 'eazydocs' ); ?></span>
						<span class="ezd-check-icon">
							<span class="dashicons dashicons-yes"></span>
						</span>
					</label>

					<label for="left_sidebar" class="ezd-layout-option <?php echo 'left_sidebar' === $docs_single_layout ? 'active' : ''; ?>">
						<input type="radio" id="left_sidebar" value="left_sidebar" name="docs_single_layout" <?php checked( $docs_single_layout, 'left_sidebar' ); ?>>
						<div class="ezd-layout-preview">
							<img src="<?php echo esc_url( EAZYDOCS_IMG . '/customizer/sidebar_left.jpg' ); ?>" alt="<?php esc_attr_e( 'Left Sidebar', 'eazydocs' ); ?>" />
						</div>
						<span class="ezd-layout-name"><?php esc_html_e( 'Left Sidebar', 'eazydocs' ); ?></span>
						<span class="ezd-layout-desc"><?php esc_html_e( 'Navigation on left side', 'eazydocs' ); ?></span>
						<span class="ezd-check-icon">
							<span class="dashicons dashicons-yes"></span>
						</span>
					</label>

					<label for="right_sidebar" class="ezd-layout-option <?php echo 'right_sidebar' === $docs_single_layout ? 'active' : ''; ?>">
						<input type="radio" id="right_sidebar" value="right_sidebar" name="docs_single_layout" <?php checked( $docs_single_layout, 'right_sidebar' ); ?>>
						<div class="ezd-layout-preview">
							<img src="<?php echo esc_url( EAZYDOCS_IMG . '/customizer/sidebar_right.jpg' ); ?>" alt="<?php esc_attr_e( 'Right Sidebar', 'eazydocs' ); ?>" />
						</div>
						<span class="ezd-layout-name"><?php esc_html_e( 'Right Sidebar', 'eazydocs' ); ?></span>
						<span class="ezd-layout-desc"><?php esc_html_e( 'Navigation on right side', 'eazydocs' ); ?></span>
						<span class="ezd-check-icon">
							<span class="dashicons dashicons-yes"></span>
						</span>
					</label>
				</div>
			</div>
		</div>

		<!-- Page Width -->
		<div class="ezd-setting-card">
			<div class="ezd-setting-header">
				<div class="ezd-setting-icon">
					<span class="dashicons dashicons-align-wide"></span>
				</div>
				<div class="ezd-setting-title">
					<h3><?php esc_html_e( 'Page Width', 'eazydocs' ); ?></h3>
					<p><?php esc_html_e( 'Choose between boxed or full-width content area.', 'eazydocs' ); ?></p>
				</div>
			</div>
			<div class="ezd-setting-body">
				<div class="page-width-wrap ezd-width-options">
					<label for="boxed" class="ezd-width-option <?php echo 'boxed' === $docs_page_width ? 'active' : ''; ?>">
						<input type="radio" id="boxed" name="docsPageWidth" value="boxed" <?php checked( $docs_page_width, 'boxed' ); ?>>
						<span class="ezd-width-icon">
							<span class="ezd-width-preview boxed"></span>
						</span>
						<span class="ezd-width-label"><?php esc_html_e( 'Boxed', 'eazydocs' ); ?></span>
					</label>

					<label for="full-width" class="ezd-width-option <?php echo 'full-width' === $docs_page_width ? 'active' : ''; ?>">
						<input type="radio" id="full-width" name="docsPageWidth" value="full-width" <?php checked( $docs_page_width, 'full-width' ); ?>>
						<span class="ezd-width-icon">
							<span class="ezd-width-preview full-width"></span>
						</span>
						<span class="ezd-width-label"><?php esc_html_e( 'Full Width', 'eazydocs' ); ?></span>
					</label>
				</div>
			</div>
		</div>

		<!-- Live Customizer -->
		<div class="ezd-setting-card ezd-toggle-card">
			<div class="ezd-setting-header">
				<div class="ezd-setting-icon">
					<span class="dashicons dashicons-admin-customizer"></span>
				</div>
				<div class="ezd-setting-title">
					<h3><?php esc_html_e( 'Live Customizer', 'eazydocs' ); ?></h3>
					<p><?php esc_html_e( 'Enable the frontend customizer for real-time styling adjustments.', 'eazydocs' ); ?></p>
				</div>
				<div class="ezd-toggle-switch">
					<input type="checkbox" id="live-customizer" name="customizer_visibility" value="1" <?php checked( $customizer_visibility, '1' ); ?>>
					<label for="live-customizer" class="ezd-toggle-label"></label>
				</div>
			</div>
		</div>

		<?php if ( ! ezd_is_premium() ) : ?>
			<!-- Pro Features Teaser -->
			<div class="ezd-pro-teaser">
				<div class="ezd-pro-badge">
					<span class="dashicons dashicons-star-filled"></span>
					<?php esc_html_e( 'Pro', 'eazydocs' ); ?>
				</div>
				<div class="ezd-pro-content">
					<h4><?php esc_html_e( 'Unlock More Layouts with Pro', 'eazydocs' ); ?></h4>
					<p><?php esc_html_e( 'Get access to advanced layout options, floating TOC, print mode, and more.', 'eazydocs' ); ?></p>
				</div>
				<a href="https://eazydocs.spider-themes.net/pricing" target="_blank" class="ezd-btn ezd-btn-pro">
					<?php esc_html_e( 'Upgrade Now', 'eazydocs' ); ?>
				</a>
			</div>
		<?php endif; ?>
	</div>
</div>