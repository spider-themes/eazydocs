<?php
/**
 * EazyDocs Cron Job Handler
 *
 * Handles scheduled fetching of HTML content from the HTML Notice Widget API
 * Runs daily to fetch and store the latest content
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! function_exists( 'ezd_init_remote_content_cron' ) ) {
	add_action( 'admin_init', 'ezd_init_remote_content_cron' );

	function ezd_init_remote_content_cron() {
		if ( ! wp_next_scheduled( 'ezd_fetch_remote_html_notice_content' ) ) {
			wp_schedule_event( time(), 'daily', 'ezd_fetch_remote_html_notice_content' );
		}
	}
}

if ( ! function_exists( 'ezd_fetch_remote_html_notice_content' ) ) {
	add_action( 'ezd_fetch_remote_html_notice_content', 'ezd_fetch_remote_html_notice_content' );

	function ezd_fetch_remote_html_notice_content() {
		$api_url = 'https://spider-themes.net/wp-json/html-notice-widget/v1/content/eazydocs';

		ezd_log_cron_activity( 'Starting fetch from API: ' . $api_url );

		$response = wp_remote_get(
			$api_url,
			array(
				'timeout'   => 15,
				'sslverify' => false,
			)
		);

		if ( is_wp_error( $response ) ) {
			$error_message = $response->get_error_message();
			ezd_log_cron_activity( 'API Request Error: ' . $error_message, 'error' );
			return;
		}

		$http_code = wp_remote_retrieve_response_code( $response );

		if ( 200 !== $http_code ) {
			ezd_log_cron_activity( 'API returned HTTP ' . $http_code, 'error' );
			return;
		}

		$body = wp_remote_retrieve_body( $response );

		$data = json_decode( $body, true );

		if ( ! is_array( $data ) ) {
			ezd_log_cron_activity( 'Invalid JSON response from API', 'error' );
			return;
		}

		if ( ! isset( $data['success'] ) || ! $data['success'] ) {
			$error_msg = isset( $data['message'] ) ? $data['message'] : 'Unknown error';
			ezd_log_cron_activity( 'API indicated failure: ' . $error_msg, 'error' );
			return;
		}

		$html_content = isset( $data['content'] ) ? $data['content'] : '';
		$site_info = isset( $data['site'] ) ? $data['site'] : array();

		if ( empty( $html_content ) ) {
			ezd_log_cron_activity( 'No content returned from API', 'warning' );
			return;
		}

		$site_id = isset( $site_info['id'] ) ? $site_info['id'] : 'unknown';
		$product = isset( $site_info['product'] ) ? $site_info['product'] : 'unknown';

		ezd_store_remote_html_notice_content( $product, $html_content, $site_id );

		ezd_log_cron_activity( 'Successfully fetched content for product: ' . $product );
	}
}

if ( ! function_exists( 'ezd_store_remote_html_notice_content' ) ) {
	function ezd_store_remote_html_notice_content( $product, $html_content, $site_id ) {
		$content_key = sanitize_key( $product ) . '_offer_html_notice';
		$site_id_key = sanitize_key( $product ) . '_offer_site_id';
		$switcher_key = sanitize_key( $product ) . '_offer_html_switcher';
		$fetched_time_key = sanitize_key( $product ) . '_offer_fetched_time';
		$dismiss_key = sanitize_key( $product ) . '_offer_dismissed';

		update_option( $content_key, $html_content );

		update_option( $site_id_key, sanitize_text_field( $site_id ) );

		if ( ! get_option( $switcher_key ) ) {
			update_option( $switcher_key, 1 );
		}

		update_option( $fetched_time_key, current_time( 'mysql' ) );

		if ( ! get_option( $dismiss_key ) ) {
			update_option( $dismiss_key, 0 );
		}
	}
}

if ( ! function_exists( 'ezd_log_cron_activity' ) ) {
	function ezd_log_cron_activity( $message, $status = 'info' ) {
		if ( ! defined( 'WP_DEBUG' ) || ! WP_DEBUG ) {
			return;
		}

		$log_message = '[EazyDocs Cron - ' . strtoupper( $status ) . '] ' . $message;
		error_log( $log_message );

		$logs = get_option( 'ezd_cron_logs', array() );

		array_unshift( $logs, array(
			'message'   => $message,
			'status'    => $status,
			'timestamp' => current_time( 'mysql' ),
		) );

		$logs = array_slice( $logs, 0, 20 );

		update_option( 'ezd_cron_logs', $logs );
	}
}

if ( ! function_exists( 'ezd_get_cron_logs' ) ) {
	function ezd_get_cron_logs() {
		return get_option( 'ezd_cron_logs', array() );
	}
}

if ( ! function_exists( 'ezd_trigger_manual_fetch' ) ) {
	function ezd_trigger_manual_fetch() {
		do_action( 'ezd_fetch_remote_html_notice_content' );
		return 'Fetch triggered successfully';
	}
}

if ( ! function_exists( 'ezd_get_html_notice_content' ) ) {
	function ezd_get_html_notice_content( $product ) {
		$content_key = sanitize_key( $product ) . '_offer_html_notice';
		return get_option( $content_key, '' );
	}
}

if ( ! function_exists( 'ezd_get_html_notice_switcher' ) ) {
	function ezd_get_html_notice_switcher( $product ) {
		$switcher_key = sanitize_key( $product ) . '_offer_html_switcher';
		return (bool) get_option( $switcher_key, 1 );
	}
}

if ( ! function_exists( 'ezd_get_html_notice_fetched_time' ) ) {
	function ezd_get_html_notice_fetched_time( $product ) {
		$fetched_time_key = sanitize_key( $product ) . '_offer_fetched_time';
		return get_option( $fetched_time_key, '' );
	}
}

if ( ! function_exists( 'ezd_is_html_notice_dismissed' ) ) {
	function ezd_is_html_notice_dismissed( $product ) {
		$dismiss_key = sanitize_key( $product ) . '_offer_dismissed';
		return (bool) get_option( $dismiss_key, 0 );
	}
}

if ( ! function_exists( 'ezd_toggle_html_notice_dismiss' ) ) {
	function ezd_toggle_html_notice_dismiss( $product ) {
		$dismiss_key = sanitize_key( $product ) . '_offer_dismissed';
		$current_status = (bool) get_option( $dismiss_key, 0 );
		update_option( $dismiss_key, ! $current_status );
	}
}

if ( ! function_exists( 'ezd_is_html_notice_temporarily_dismissed' ) ) {
	function ezd_is_html_notice_temporarily_dismissed( $product ) {
		$dismiss_key = sanitize_key( $product ) . '_offer_dismissed_time';
		$dismiss_time = get_option( $dismiss_key, 0 );

		if ( empty( $dismiss_time ) || ! is_numeric( $dismiss_time ) ) {
			return false;
		}

		$current_time = current_time( 'timestamp' );
		$one_week = 7 * 24 * 60 * 60;

		return ( $current_time - intval( $dismiss_time ) ) < $one_week;
	}
}

if ( ! function_exists( 'ezd_unschedule_remote_content_cron' ) ) {
	add_action( 'eazydocs_plugin_deactivate', 'ezd_unschedule_remote_content_cron' );

	function ezd_unschedule_remote_content_cron() {
		$timestamp = wp_next_scheduled( 'ezd_fetch_remote_html_notice_content' );
		if ( $timestamp ) {
			wp_unschedule_event( $timestamp, 'ezd_fetch_remote_html_notice_content' );
			ezd_log_cron_activity( 'Cron job unscheduled on plugin deactivation' );
		}
	}
}

