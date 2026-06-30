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
		<h2><?php esc_html_e( 'Design Your Docs', 'eazydocs' ); ?></h2>
		<p class="ezd-step-description"><?php esc_html_e( 'Shape how your documentation looks — layout, width, brand color and dark mode. Watch the live preview update as you go.', 'eazydocs' ); ?></p>
		<button type="button" class="ezd-restore-defaults" title="<?php esc_attr_e( 'Reset layout, width and theme to the recommended defaults', 'eazydocs' ); ?>">
			<span class="dashicons dashicons-image-rotate"></span>
			<?php esc_html_e( 'Restore defaults', 'eazydocs' ); ?>
		</button>
	</div>

	<div class="ezd-layout-content">
		<!-- Live Layout Preview -->
		<?php
		// Dark-mode accent: falls back to the light brand color until the admin
		// opts into a custom one, so the dark preview always shows a real accent.
		$preview_brand      = $brand_color ? $brand_color : '#007FFF';
		$preview_brand_dark = ( '1' === $is_dark_accent_color && $brand_color_dark ) ? $brand_color_dark : $preview_brand;
		?>
		<div class="ezd-layout-live-preview" data-layout="<?php echo esc_attr( $docs_single_layout ); ?>" data-width="<?php echo esc_attr( $docs_page_width ); ?>" data-theme="light" data-dark-accent="<?php echo esc_attr( $is_dark_accent_color ? '1' : '0' ); ?>" style="--ezd-preview-brand: <?php echo esc_attr( $preview_brand ); ?>; --ezd-preview-brand-dark: <?php echo esc_attr( $preview_brand_dark ); ?>">
			<!-- Preview controls: brand colour + light/dark appearance -->
			<div class="ezd-preview-controls">
				<label class="ezd-preview-brand-control">
					<span class="ezd-preview-brand-swatch"></span>
					<span class="ezd-preview-brand-text"><?php esc_html_e( 'Brand color', 'eazydocs' ); ?></span>
					<input type="color" class="ezd-preview-brand-input" value="<?php echo esc_attr( $preview_brand ); ?>" aria-label="<?php esc_attr_e( 'Brand color', 'eazydocs' ); ?>">
				</label>
				<!-- Dark accent: only meaningful while previewing dark, shown then. -->
				<label class="ezd-preview-brand-control ezd-preview-dark-accent-control">
					<span class="ezd-preview-brand-swatch ezd-preview-dark-accent-swatch"></span>
					<span class="ezd-preview-brand-text"><?php esc_html_e( 'Dark accent', 'eazydocs' ); ?></span>
					<input type="color" class="ezd-preview-dark-accent-input" value="<?php echo esc_attr( $preview_brand_dark ); ?>" aria-label="<?php esc_attr_e( 'Dark mode accent color', 'eazydocs' ); ?>">
				</label>
				<div class="ezd-preview-mode-toggle" role="group" aria-label="<?php esc_attr_e( 'Preview appearance', 'eazydocs' ); ?>">
					<button type="button" class="ezd-mode-btn active" data-mode="light" aria-pressed="true">
						<span class="dashicons dashicons-sticky"></span>
						<?php esc_html_e( 'Light', 'eazydocs' ); ?>
					</button>
					<button type="button" class="ezd-mode-btn" data-mode="dark" aria-pressed="false">
						<span class="dashicons dashicons-admin-appearance"></span>
						<?php esc_html_e( 'Dark', 'eazydocs' ); ?>
					</button>
				</div>
			</div>
			<div class="ezd-preview-browser">
				<div class="ezd-preview-bar">
					<span></span><span></span><span></span>
				</div>
				<div class="ezd-preview-viewport">
					<div class="ezd-preview-page">
						<div class="ezd-preview-topbar"></div>
						<div class="ezd-preview-body">
							<aside class="ezd-preview-sidebar ezd-preview-sidebar-left" aria-hidden="true">
								<span></span><span></span><span></span><span></span><span></span>
							</aside>
							<main class="ezd-preview-main" aria-hidden="true">
								<span class="ezd-preview-title"></span>
								<span></span><span></span><span></span><span class="short"></span>
								<span></span><span></span><span class="short"></span>
							</main>
							<aside class="ezd-preview-sidebar ezd-preview-sidebar-right" aria-hidden="true">
								<span></span><span></span><span></span>
							</aside>
						</div>
					</div>
				</div>
			</div>
			<p class="ezd-preview-caption">
				<span class="dashicons dashicons-visibility"></span>
				<?php esc_html_e( 'Live preview — updates instantly as you change the options', 'eazydocs' ); ?>
			</p>
		</div>

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
							<img src="<?php echo esc_url( EZD_IMG . 'customizer/both_sidebar.jpg' ); ?>" alt="<?php esc_attr_e( 'Both Sidebars', 'eazydocs' ); ?>" />
						</div>
						<span class="ezd-layout-name">
							<?php esc_html_e( 'Both Sidebars', 'eazydocs' ); ?>
							<span class="ezd-layout-recommended"><?php esc_html_e( 'Recommended', 'eazydocs' ); ?></span>
						</span>
						<span class="ezd-layout-desc"><?php esc_html_e( 'Left: Navigation | Right: TOC', 'eazydocs' ); ?></span>
						<span class="ezd-check-icon">
							<span class="dashicons dashicons-yes"></span>
						</span>
					</label>

					<label for="left_sidebar" class="ezd-layout-option <?php echo 'left_sidebar' === $docs_single_layout ? 'active' : ''; ?>">
						<input type="radio" id="left_sidebar" value="left_sidebar" name="docs_single_layout" <?php checked( $docs_single_layout, 'left_sidebar' ); ?>>
						<div class="ezd-layout-preview">
							<img src="<?php echo esc_url( EZD_IMG . 'customizer/sidebar_left.jpg' ); ?>" alt="<?php esc_attr_e( 'Left Sidebar', 'eazydocs' ); ?>" />
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
							<img src="<?php echo esc_url( EZD_IMG . 'customizer/sidebar_right.jpg' ); ?>" alt="<?php esc_attr_e( 'Right Sidebar', 'eazydocs' ); ?>" />
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

		<!-- Dark Mode -->
		<div class="ezd-setting-card ezd-toggle-card ezd-dark-mode-card<?php echo '1' === $is_dark_switcher ? ' is-enabled' : ''; ?>">
			<div class="ezd-setting-header">
				<div class="ezd-setting-icon">
					<span class="dashicons dashicons-admin-appearance"></span>
				</div>
				<div class="ezd-setting-title">
					<h3><?php esc_html_e( 'Dark Mode Switcher', 'eazydocs' ); ?></h3>
					<p><?php esc_html_e( 'Add a light/dark toggle to your docs. Turning this on switches the preview to dark so you can see it.', 'eazydocs' ); ?></p>
				</div>
				<div class="ezd-toggle-switch">
					<input type="checkbox" id="dark-mode-switcher" name="is_dark_switcher" value="1" <?php checked( $is_dark_switcher, '1' ); ?>>
					<label for="dark-mode-switcher" class="ezd-toggle-label"></label>
				</div>
			</div>

			<!-- Default appearance: revealed only while the switcher is enabled. -->
			<div class="ezd-setting-body ezd-dark-default-body">
				<span class="ezd-dark-default-label"><?php esc_html_e( 'Default appearance for new visitors', 'eazydocs' ); ?></span>
				<div class="ezd-dark-default-options" role="radiogroup" aria-label="<?php esc_attr_e( 'Default appearance for new visitors', 'eazydocs' ); ?>">
					<?php
					$dark_default_choices = array(
						'light'  => array( 'sticky', __( 'Light', 'eazydocs' ) ),
						'dark'   => array( 'admin-appearance', __( 'Dark', 'eazydocs' ) ),
						'system' => array( 'desktop', __( 'Follow system', 'eazydocs' ) ),
					);
					foreach ( $dark_default_choices as $value => $choice ) :
						?>
						<label class="ezd-dark-default-option<?php echo $ezd_dark_default === $value ? ' active' : ''; ?>">
							<input type="radio" name="ezd_dark_default" value="<?php echo esc_attr( $value ); ?>" <?php checked( $ezd_dark_default, $value ); ?>>
							<span class="dashicons dashicons-<?php echo esc_attr( $choice[0] ); ?>"></span>
							<span><?php echo esc_html( $choice[1] ); ?></span>
						</label>
					<?php endforeach; ?>
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