<?php
/**
 * Info notice about Gutenberg blocks (no Elementor required)
 * Cannot access directly.
 */
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

add_action( 'admin_notices', 'ezd_gutenberg_info_notice' );
add_action( 'wp_ajax_ezd_dismiss_gutenberg_info', 'ezd_dismiss_gutenberg_info' );

/**
 * Display Gutenberg info notice
 */
function ezd_gutenberg_info_notice() {
	// Only show this notice on EazyDocs admin pages
	if ( ! ezd_admin_pages() ) {
		return;
	}

	// Check if user has dismissed this notice
	$dismissed = get_user_meta( get_current_user_id(), 'ezd_gutenberg_info_dismissed', true );
	if ( $dismissed ) {
		return;
	}

	// Only show if user has been using the plugin for less than 7 days
	$ezd_installed = get_option( 'eazyDocs_installed' );
	if ( $ezd_installed ) {
		$current_time = current_time( 'timestamp' );
		$days_since_install = ( $current_time - $ezd_installed ) / ( 24 * 60 * 60 );
		
		// Only show this notice within first 7 days
		if ( $days_since_install > 7 ) {
			return;
		}
	}
	?>
	<div class="notice notice-info is-dismissible" id="ezd_gutenberg_info" style="padding: 18px 20px; background: #fff; border-left: 4px solid #2271b1; border-radius: 4px; box-shadow: 0 4px 12px rgba(0,0,0,0.05); margin-top: 20px; border-top: none; border-right: none; border-bottom: none;">
		<div style="display: flex; align-items: start;">
			<div style="margin-right: 20px; background: #f0f6fb; padding: 12px; border-radius: 8px; flex-shrink: 0;">
				<img src="<?php echo esc_url(EAZYDOCS_IMG . '/eazydocs-logo.png') ?>" style="width: 38px; height: auto; display: block;" />
			</div>
			<div style="padding-top: 2px;">
				<h3 style="margin: 0 0 6px 0; font-size: 16px; font-weight: 600; color: #1d2327;">
					<?php esc_html_e( 'Build Stunning Docs with Gutenberg!', 'eazydocs' ); ?>
				</h3>
				<p style="margin: 0; font-size: 14px; line-height: 1.5; color: #515c6d; max-width: 750px;">
					<?php esc_html_e( 'EazyDocs is fully optimized for the WordPress Block Editor. You have everything you need to create professional help centers and documentation landing pages right out of the box—no third-party page builders like Elementor required.', 'eazydocs' ); ?>
				</p>
				<div style="margin-top: 12px;">
					<a href="https://helpdesk.spider-themes.net/docs/eazydocs-wordpress-plugin/gutenberg-blocks/" target="_blank" style="color: #2271b1; text-decoration: none; font-weight: 500; display: inline-flex; align-items: center; gap: 4px;">
						<?php esc_html_e( 'See Gutenberg Blocks in Action', 'eazydocs' ); ?>
						<span class="dashicons dashicons-external" style="font-size: 15px; width: 15px; height: 15px; margin-top: 2px;"></span>
					</a>
				</div>
			</div>
		</div>
	</div>

	<script>
		jQuery(document).ready(function ($) {
			// Handle dismiss button click
			$('#ezd_gutenberg_info').on('click', '.notice-dismiss', function () {
				$.ajax({
					url: eazydocs_local_object.ajaxurl,
					type: 'POST',
					data: {
						action: 'ezd_dismiss_gutenberg_info',
						nonce: eazydocs_local_object.nonce,
					}
				});
			});
		});
	</script>
	<?php
}

/**
 * Handle AJAX request to dismiss the Gutenberg info notice
 */
function ezd_dismiss_gutenberg_info() {
	if ( isset( $_POST['nonce'] ) && wp_verify_nonce( $_POST['nonce'], 'eazydocs-admin-nonce' ) ) {
		update_user_meta( get_current_user_id(), 'ezd_gutenberg_info_dismissed', true );
		wp_send_json_success();
	}
	wp_send_json_error();
}
