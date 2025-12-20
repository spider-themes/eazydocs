<?php
/**
 * EazyDocs Offer Notices - HTML Notice Widget Display
 *
 * Handles the display of fetched HTML content as admin notices
 * with dismiss functionality
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! function_exists( 'ezd_display_html_notice' ) ) {
	add_action( 'admin_notices', 'ezd_display_html_notice' );

	function ezd_display_html_notice() {
		$product = 'eazydocs';

		if ( ezd_is_html_notice_temporarily_dismissed( $product ) ) {
			return;
		}

		$is_enabled = ezd_get_html_notice_switcher( $product );

		if ( ! $is_enabled ) {
			return;
		}

		$html_content = ezd_get_html_notice_content( $product );

		if ( empty( $html_content ) ) {
			return;
		}

		$fetched_time = ezd_get_html_notice_fetched_time( $product );
		$nonce = wp_create_nonce( 'ezd_dismiss_notice' );

		?>
		<div id="ezd-html-notice-<?php echo esc_attr( $product ); ?>" class="notice notice-info ezd-html-notice-wrapper" data-product="<?php echo esc_attr( $product ); ?>" data-nonce="<?php echo esc_attr( $nonce ); ?>">
			<div class="ezd-html-notice-content">
				<?php echo $html_content; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
			</div>
            <button type="button" class="notice-dismiss"><span class="screen-reader-text">Dismiss this notice.</span></button>
		</div>

		<style>
		#ezd-html-notice-<?php echo esc_attr( $product ); ?> {
			margin: 20px 0 !important;
            padding: 0;
            background: transparent;
            border: 0;
            position: relative;
		}

		#ezd-html-notice-<?php echo esc_attr( $product ); ?> .ezd-html-notice-content p {
			margin: 5px 0;
			line-height: 1.6;
		}

		#ezd-html-notice-<?php echo esc_attr( $product ); ?> a {
			text-decoration: none;
			color: #0073aa;
			font-weight: 500;
		}

		#ezd-html-notice-<?php echo esc_attr( $product ); ?> a:hover {
			text-decoration: underline;
		}

		#ezd-html-notice-<?php echo esc_attr( $product ); ?> strong {
			color: #000;
			font-weight: 600;
		}

		#ezd-html-notice-<?php echo esc_attr( $product ); ?> i {
			color: #0073aa;
			margin-right: 8px;
		}
		</style>

		<script>
		(function() {
			var notice = document.getElementById( 'ezd-html-notice-<?php echo esc_js( $product ); ?>' );
			if ( ! notice ) return;

			var closeBtn = notice.querySelector( '.notice-dismiss' );
			if ( closeBtn ) {
				closeBtn.addEventListener( 'click', function( e ) {
					e.preventDefault();
					var product = notice.getAttribute( 'data-product' );
					var nonce = notice.getAttribute( 'data-nonce' );

					var xhr = new XMLHttpRequest();
					xhr.open( 'POST', '<?php echo esc_js( admin_url( 'admin-ajax.php' ) ); ?>', true );
					xhr.setRequestHeader( 'Content-Type', 'application/x-www-form-urlencoded; charset=UTF-8' );
					xhr.onload = function() {
						if ( xhr.status === 200 ) {
							notice.style.display = 'none';
						}
					};

					var data = 'action=ezd_dismiss_html_notice&product=' + encodeURIComponent( product ) + '&nonce=' + encodeURIComponent( nonce );
					xhr.send( data );
				});
			}
		})();
		</script>
		<?php
	}
}

if ( ! function_exists( 'ezd_handle_dismiss_html_notice' ) ) {
	add_action( 'wp_ajax_ezd_dismiss_html_notice', 'ezd_handle_dismiss_html_notice' );

	function ezd_handle_dismiss_html_notice() {
		if ( ! isset( $_POST['nonce'] ) || ! wp_verify_nonce( $_POST['nonce'], 'ezd_dismiss_notice' ) ) {
			wp_send_json_error( array( 'message' => 'Nonce verification failed' ) );
		}

		if ( ! current_user_can( 'manage_options' ) ) {
			wp_send_json_error( array( 'message' => 'Unauthorized' ) );
		}

		$product = isset( $_POST['product'] ) ? sanitize_text_field( wp_unslash( $_POST['product'] ) ) : '';

		if ( empty( $product ) ) {
			wp_send_json_error( array( 'message' => 'Invalid product' ) );
		}

		$dismiss_key = sanitize_key( $product ) . '_offer_dismissed_time';
		update_option( $dismiss_key, current_time( 'timestamp' ) );

		if ( function_exists( 'ezd_log_cron_activity' ) ) {
			ezd_log_cron_activity( 'Notice dismissed by user for product: ' . $product );
		}

		wp_send_json_success( array( 'message' => 'Notice dismissed for 1 week' ) );
	}
}


if ( ! function_exists( 'ezd_reset_all_notice_dismisses' ) ) {
	function ezd_reset_all_notice_dismisses() {
		$products = array( 'eazydocs' );

		foreach ( $products as $product ) {
			$dismiss_key = sanitize_key( $product ) . '_offer_dismissed_time';
			delete_option( $dismiss_key );
		}
	}
}

