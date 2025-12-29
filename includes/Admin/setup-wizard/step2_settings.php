<?php
/**
 * Step 2: Basic Settings
 *
 * @package EazyDocs
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
?>
<div id="step-2" class="tab-pane ezd-settings-step" role="tabpanel" style="display:none">
	<div class="ezd-step-header">
		<div class="ezd-step-icon">
			<span class="dashicons dashicons-admin-settings"></span>
		</div>
		<h2><?php esc_html_e( 'Basic Configuration', 'eazydocs' ); ?></h2>
		<p class="ezd-step-description"><?php esc_html_e( 'Configure the essential settings for your documentation. Don\'t worry, you can change these later.', 'eazydocs' ); ?></p>
	</div>

	<div class="ezd-settings-content">
		<!-- Archive Page Selection -->
		<div class="ezd-setting-card">
			<div class="ezd-setting-header">
				<div class="ezd-setting-icon">
					<span class="dashicons dashicons-admin-page"></span>
				</div>
				<div class="ezd-setting-title">
					<h3><?php esc_html_e( 'Docs Archive Page', 'eazydocs' ); ?></h3>
					<p><?php esc_html_e( 'Select the page that will display all your documentation.', 'eazydocs' ); ?></p>
				</div>
			</div>
			<div class="ezd-setting-body">
				<div class="archive-page-selection-wrap">
					<select name="docs_archive_page" id="docs_archive_page" class="ezd-select">
						<option value=""><?php esc_html_e( '— Select a page —', 'eazydocs' ); ?></option>
						<?php
						$pages = get_pages();
						foreach ( $pages as $page ) {
							$selected = ( $page->ID == $docs_archive_page ) ? 'selected' : '';
							echo '<option value="' . esc_attr( $page->ID ) . '" ' . esc_attr( $selected ) . '>' . esc_html( $page->post_title ) . '</option>';
						}
						?>
					</select>
					<span class="ezd-setting-hint">
						<span class="dashicons dashicons-info"></span>
						<?php esc_html_e( 'Use [eazydocs] shortcode, Gutenberg blocks, or Elementor widgets on this page.', 'eazydocs' ); ?>
					</span>
				</div>
			</div>
		</div>

		<!-- Brand Color -->
		<div class="ezd-setting-card">
			<div class="ezd-setting-header">
				<div class="ezd-setting-icon">
					<span class="dashicons dashicons-color-picker"></span>
				</div>
				<div class="ezd-setting-title">
					<h3><?php esc_html_e( 'Brand Color', 'eazydocs' ); ?></h3>
					<p><?php esc_html_e( 'Choose your primary brand color for buttons, links, and accents.', 'eazydocs' ); ?></p>
				</div>
			</div>
			<div class="ezd-setting-body">
				<div class="brand-color-picker-wrap">
					<input type="text" class="brand-color-picker" placeholder="#007FFF" value="<?php echo esc_attr( $brand_color ); ?>">
					<div class="ezd-color-preview" style="--preview-color: <?php echo esc_attr( $brand_color ? $brand_color : '#007FFF' ); ?>">
						<span><?php esc_html_e( 'Preview', 'eazydocs' ); ?></span>
					</div>
				</div>
			</div>
		</div>

		<!-- URL Slug -->
		<div class="ezd-setting-card">
			<div class="ezd-setting-header">
				<div class="ezd-setting-icon">
					<span class="dashicons dashicons-admin-links"></span>
				</div>
				<div class="ezd-setting-title">
					<h3><?php esc_html_e( 'Doc Root URL Slug', 'eazydocs' ); ?></h3>
					<p><?php esc_html_e( 'Set the URL structure for your documentation pages.', 'eazydocs' ); ?></p>
				</div>
			</div>
			<div class="ezd-setting-body">
				<div class="root-slug-wrap">
					<div class="ezd-slug-options">
						<label for="post-name" class="ezd-slug-option <?php echo 'post-name' === $slugType ? 'active' : ''; ?>">
							<input type="radio" id="post-name" name="slug" value="post-name" <?php checked( $slugType, 'post-name' ); ?>>
							<span class="ezd-option-label">
								<span class="ezd-option-title"><?php esc_html_e( 'Default Slug', 'eazydocs' ); ?></span>
								<span class="ezd-option-example"><?php echo esc_html( home_url( '/docs/your-doc-title/' ) ); ?></span>
							</span>
						</label>

						<label for="custom-slug" class="ezd-slug-option <?php echo 'custom-slug' === $slugType ? 'active' : ''; ?>">
							<input type="radio" id="custom-slug" name="slug" value="custom-slug" <?php checked( $slugType, 'custom-slug' ); ?>>
							<span class="ezd-option-label">
								<span class="ezd-option-title"><?php esc_html_e( 'Custom Slug', 'eazydocs' ); ?></span>
								<span class="ezd-option-example"><?php esc_html_e( 'Define your own URL structure', 'eazydocs' ); ?></span>
							</span>
						</label>
					</div>

					<div class="ezd-custom-slug-input <?php echo 'custom-slug' === $slugType ? 'active' : ''; ?>">
						<span class="ezd-slug-prefix"><?php echo esc_html( home_url( '/' ) ); ?></span>
						<input type="text" class="custom-slug-field" placeholder="docs" value="<?php echo esc_attr( $custom_slug ); ?>">
						<span class="ezd-slug-suffix">/</span>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>