<?php
/**
 * System Status Widget
 * Shows EazyDocs plugin status and useful links
 *
 * @package EazyDocs
 */

// Get plugin version.
$plugin_data = get_plugin_data( EAZYDOCS_FILE );
$plugin_version = $plugin_data['Version'] ?? '1.0.0';

// Check if Pro is active.
$is_pro_active = defined( 'EAZYDOCS_PRO_VERSION' );
$pro_version = defined( 'EAZYDOCS_PRO_VERSION' ) ? EAZYDOCS_PRO_VERSION : null;

// Get PHP version.
$php_version = phpversion();

// Get WordPress version.
$wp_version = get_bloginfo( 'version' );

// Check for docs page.
$docs_page_id = get_option( 'eazydocs_docs_page' );
$docs_page_url = $docs_page_id ? get_permalink( $docs_page_id ) : null;

// Get theme info.
$theme = wp_get_theme();
?>

<div class="ezd-card ezd-system-status-card">
	<div class="ezd-card-header">
		<h2 class="ezd-card-title">
			<span class="dashicons dashicons-admin-tools"></span>
			<?php esc_html_e( 'System Status', 'eazydocs' ); ?>
		</h2>
	</div>

	<div class="ezd-system-status-content">
		<!-- Plugin Status -->
		<div class="ezd-status-row">
			<span class="ezd-status-label"><?php esc_html_e( 'EazyDocs Version', 'eazydocs' ); ?></span>
			<span class="ezd-status-value">
				<span class="ezd-version-badge"><?php echo esc_html( $plugin_version ); ?></span>
			</span>
		</div>

		<?php if ( $is_pro_active ) : ?>
			<div class="ezd-status-row">
				<span class="ezd-status-label"><?php esc_html_e( 'Pro Version', 'eazydocs' ); ?></span>
				<span class="ezd-status-value">
					<span class="ezd-version-badge ezd-badge-pro"><?php echo esc_html( $pro_version ); ?></span>
				</span>
			</div>
		<?php else : ?>
			<div class="ezd-status-row">
				<span class="ezd-status-label"><?php esc_html_e( 'Pro Version', 'eazydocs' ); ?></span>
				<span class="ezd-status-value">
					<a href="<?php echo esc_url( admin_url( 'admin.php?page=eazydocs-pricing' ) ); ?>" class="ezd-upgrade-link">
						<span class="dashicons dashicons-star-filled"></span>
						<?php esc_html_e( 'Upgrade', 'eazydocs' ); ?>
					</a>
				</span>
			</div>
		<?php endif; ?>

		<div class="ezd-status-row">
			<span class="ezd-status-label"><?php esc_html_e( 'WordPress', 'eazydocs' ); ?></span>
			<span class="ezd-status-value"><?php echo esc_html( $wp_version ); ?></span>
		</div>

		<div class="ezd-status-row">
			<span class="ezd-status-label"><?php esc_html_e( 'PHP Version', 'eazydocs' ); ?></span>
			<span class="ezd-status-value"><?php echo esc_html( $php_version ); ?></span>
		</div>

		<div class="ezd-status-row">
			<span class="ezd-status-label"><?php esc_html_e( 'Active Theme', 'eazydocs' ); ?></span>
			<span class="ezd-status-value"><?php echo esc_html( $theme->get( 'Name' ) ); ?></span>
		</div>

		<?php if ( $docs_page_url ) : ?>
			<div class="ezd-status-row">
				<span class="ezd-status-label"><?php esc_html_e( 'Docs Page', 'eazydocs' ); ?></span>
				<span class="ezd-status-value">
					<a href="<?php echo esc_url( $docs_page_url ); ?>" target="_blank" class="ezd-docs-link">
						<span class="dashicons dashicons-external"></span>
						<?php esc_html_e( 'View', 'eazydocs' ); ?>
					</a>
				</span>
			</div>
		<?php endif; ?>
	</div>

	<!-- Quick Links -->
	<div class="ezd-system-links">
		<h4 class="ezd-system-links-title"><?php esc_html_e( 'Resources', 'eazydocs' ); ?></h4>
		<div class="ezd-system-links-grid">
			<a href="https://helpdesk.spider-themes.net/docs/eazydocs/" target="_blank" class="ezd-system-link">
				<span class="dashicons dashicons-book"></span>
				<?php esc_html_e( 'Documentation', 'eazydocs' ); ?>
			</a>
			<a href="https://spider-themes.net/support/" target="_blank" class="ezd-system-link">
				<span class="dashicons dashicons-sos"></span>
				<?php esc_html_e( 'Support', 'eazydocs' ); ?>
			</a>
			<a href="https://spider-themes.net/eazydocs/changelog/" target="_blank" class="ezd-system-link">
				<span class="dashicons dashicons-list-view"></span>
				<?php esc_html_e( 'Changelog', 'eazydocs' ); ?>
			</a>
			<a href="https://spider-themes.net/eazydocs/#features" target="_blank" class="ezd-system-link">
				<span class="dashicons dashicons-star-empty"></span>
				<?php esc_html_e( 'Features', 'eazydocs' ); ?>
			</a>
		</div>
	</div>
</div>
