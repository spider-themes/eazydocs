<?php
/**
 * Step 4: Plugin Installation
 *
 * @package EazyDocs
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

// Include necessary WordPress functions for plugin management.
if ( ! function_exists( 'is_plugin_active' ) ) {
	include_once ABSPATH . 'wp-admin/includes/plugin.php';
}

$plugins = array(
	array(
		'slug'        => 'elementor',
		'img'         => 'elementor-logo.png',
		'title'       => esc_html__( 'Elementor', 'eazydocs' ),
		'description' => esc_html__( 'Required for EazyDocs Elementor widgets. Perfect for building custom documentation layouts.', 'eazydocs' ),
		'status'      => 'required',
		'status_text' => esc_html__( 'Required', 'eazydocs' ),
	),
	array(
		'slug'        => 'advanced-accordion-block',
		'img'         => 'AAGB-logo.svg',
		'title'       => esc_html__( 'Advanced Accordion Block', 'eazydocs' ),
		'description' => esc_html__( 'Create beautiful FAQs and accordion content in the block editor.', 'eazydocs' ),
		'status'      => 'recommended',
		'status_text' => esc_html__( 'Recommended', 'eazydocs' ),
	),
	array(
		'img'         => 'antimanual-logo.png',
		'title'       => esc_html__( 'Antimanual', 'eazydocs' ),
		'slug'        => 'antimanual',
		'description' => esc_html__( 'Create comprehensive user guides and documentation with Antimanual.', 'eazydocs' ),
		'status'      => 'optional',
		'status_text' => esc_html__( 'Optional', 'eazydocs' ),
	),
	array(
		'img'         => 'changeloger-logo.png',
		'title'       => esc_html__( 'Changeloger', 'eazydocs' ),
		'slug'        => 'changeloger',
		'description' => esc_html__( 'Publish beautiful changelogs directly in your documentation.', 'eazydocs' ),
		'status'      => 'optional',
		'status_text' => esc_html__( 'Optional', 'eazydocs' ),
	),
);
?>
<div id="step-4" class="tab-pane ezd-plugins-step" role="tabpanel" style="display:none">
	<div class="ezd-step-header">
		<div class="ezd-step-icon">
			<span class="dashicons dashicons-admin-plugins"></span>
		</div>
		<h2><?php esc_html_e( 'Recommended Plugins', 'eazydocs' ); ?></h2>
		<p class="ezd-step-description"><?php esc_html_e( 'These plugins enhance your documentation experience. Choose the ones that fit your needs.', 'eazydocs' ); ?></p>
	</div>

	<div class="ezd-plugins-content">
		<div class="ezd-plugins-grid">
			<?php
			foreach ( $plugins as $plugin ) :
				$plugin_file  = $plugin['slug'] . '/' . $plugin['slug'] . '.php';
				$is_active    = is_plugin_active( $plugin_file );
				$is_installed = file_exists( WP_PLUGIN_DIR . '/' . $plugin['slug'] );

				$button_text  = $is_active ? esc_html__( 'Activated', 'eazydocs' ) : ( $is_installed ? esc_html__( 'Activate', 'eazydocs' ) : esc_html__( 'Install', 'eazydocs' ) );
				$btn_icon     = $is_active ? 'dashicons-yes' : ( $is_installed ? 'dashicons-update' : 'dashicons-download' );
				$button_class = $is_active ? 'button-disabled' : 'button-action';
				$button_attr  = $is_active ? 'disabled' : sprintf( 'data-plugin=%s data-action=%s', esc_attr( $plugin['slug'] ), $is_installed ? 'activate' : 'install' );
				$card_class   = $is_active ? 'is-active' : '';
				?>
				<div class="ezd-plugin-card <?php echo esc_attr( $card_class ); ?>" data-status="<?php echo esc_attr( $plugin['status'] ); ?>">
					<div class="ezd-plugin-status ezd-status-<?php echo esc_attr( $plugin['status'] ); ?>">
						<?php echo esc_html( $plugin['status_text'] ); ?>
					</div>

					<?php if ( $is_active ) : ?>
						<div class="ezd-plugin-active-badge">
							<span class="dashicons dashicons-yes-alt"></span>
						</div>
					<?php endif; ?>

					<div class="ezd-plugin-header">
						<div class="ezd-plugin-icon">
							<img src="<?php echo esc_url( EAZYDOCS_IMG . '/admin/' . $plugin['img'] ); ?>" alt="<?php echo esc_attr( $plugin['title'] ); ?>" />
						</div>
						<h4 class="ezd-plugin-title"><?php echo esc_html( $plugin['title'] ); ?></h4>
					</div>

					<p class="ezd-plugin-description"><?php echo esc_html( $plugin['description'] ); ?></p>

					<div class="ezd-plugin-actions">
						<a href="https://wordpress.org/plugins/<?php echo esc_attr( $plugin['slug'] ); ?>/" target="_blank" class="ezd-btn ezd-btn-link">
							<span class="dashicons dashicons-info-outline"></span>
							<?php esc_html_e( 'Details', 'eazydocs' ); ?>
						</a>
						<button class="ezd-btn <?php echo esc_attr( $button_class ); ?>" <?php echo $button_attr; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>>
							<span class="dashicons <?php echo esc_attr( $btn_icon ); ?>"></span>
							<span class="button-text"><?php echo esc_html( $button_text ); ?></span>
						</button>
					</div>
				</div>
			<?php endforeach; ?>
		</div>
	</div>
</div>