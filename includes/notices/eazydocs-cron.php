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

		// Handle new API structure with contents array
		$contents  = isset( $data['contents'] ) ? $data['contents'] : array();
		$site_info = isset( $data['site'] ) ? $data['site'] : array();

		$site_id = isset( $site_info['id'] ) ? $site_info['id'] : 'unknown';
		$product = isset( $site_info['product'] ) ? $site_info['product'] : 'eazydocs';

		// If no contents returned from API, clear the stored data
		if ( empty( $contents ) ) {
			ezd_log_cron_activity( 'No contents returned from API - clearing stored notices', 'warning' );
			ezd_clear_remote_html_notice_contents( $product );
			return;
		}

		// Store all contents and clean up stale dismiss keys
		ezd_store_remote_html_notice_contents( $product, $contents, $site_id );

		ezd_log_cron_activity( 'Successfully fetched ' . count( $contents ) . ' contents for product: ' . $product );
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

if ( ! function_exists( 'ezd_store_remote_html_notice_contents' ) ) {
	/**
	 * Store remote HTML notice contents.
	 *
	 * @param string $product  Product identifier.
	 * @param array  $contents Array of content items from API.
	 * @param string $site_id  Site identifier.
	 */
	function ezd_store_remote_html_notice_contents( $product, $contents, $site_id ) {
		$product          = sanitize_key( $product );
		$contents_key     = $product . '_offer_html_contents';
		$site_id_key      = $product . '_offer_site_id';
		$switcher_key     = $product . '_offer_html_switcher';
		$fetched_time_key = $product . '_offer_fetched_time';

		// Get old contents to clean up stale dismiss keys
		$old_contents    = get_option( $contents_key, array() );
		$old_content_ids = array();
		if ( is_array( $old_contents ) ) {
			foreach ( $old_contents as $old_content ) {
				if ( isset( $old_content['id'] ) ) {
					$old_content_ids[] = sanitize_key( $old_content['id'] );
				}
			}
		}

		// Get new content IDs
		$new_content_ids = array();
		foreach ( $contents as $content ) {
			if ( isset( $content['id'] ) ) {
				$new_content_ids[] = sanitize_key( $content['id'] );
			}
		}

		// Remove dismiss keys for contents that no longer exist in API
		$stale_ids = array_diff( $old_content_ids, $new_content_ids );
		foreach ( $stale_ids as $stale_id ) {
			$stale_dismiss_key = $product . '_content_' . $stale_id . '_dismissed';
			delete_option( $stale_dismiss_key );
		}

		// Store the full contents array
		update_option( $contents_key, $contents );
		update_option( $site_id_key, sanitize_text_field( $site_id ) );

		if ( false === get_option( $switcher_key ) ) {
			update_option( $switcher_key, 1 );
		}

		update_option( $fetched_time_key, current_time( 'mysql' ) );

		// Initialize dismiss status for each content if not exists
		foreach ( $contents as $content ) {
			if ( isset( $content['id'] ) ) {
				$content_dismiss_key = $product . '_content_' . sanitize_key( $content['id'] ) . '_dismissed';
				if ( false === get_option( $content_dismiss_key ) ) {
					update_option( $content_dismiss_key, 0 );
				}
			}
		}
	}
}

if ( ! function_exists( 'ezd_clear_remote_html_notice_contents' ) ) {
	/**
	 * Clear all stored remote HTML notice contents and related options.
	 *
	 * This function is called when the API returns empty results,
	 * ensuring that old/stale notices are removed from display.
	 *
	 * @param string $product Product identifier.
	 */
	function ezd_clear_remote_html_notice_contents( $product ) {
		$product          = sanitize_key( $product );
		$contents_key     = $product . '_offer_html_contents';
		$fetched_time_key = $product . '_offer_fetched_time';

		// Get existing contents to clean up their dismiss keys
		$existing_contents = get_option( $contents_key, array() );
		if ( is_array( $existing_contents ) ) {
			foreach ( $existing_contents as $content ) {
				if ( isset( $content['id'] ) ) {
					$content_dismiss_key = $product . '_content_' . sanitize_key( $content['id'] ) . '_dismissed';
					delete_option( $content_dismiss_key );
				}
			}
		}

		// Clear the contents - set to empty array
		update_option( $contents_key, array() );

		// Update fetched time to indicate last check
		update_option( $fetched_time_key, current_time( 'mysql' ) );

		ezd_log_cron_activity( 'Cleared all stored notices for product: ' . $product );
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
	function ezd_trigger_manual_fetch(): string {
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

if ( ! function_exists( 'ezd_get_html_notice_contents' ) ) {
	function ezd_get_html_notice_contents( $product ) {
		$contents_key = sanitize_key( $product ) . '_offer_html_contents';
		return get_option( $contents_key, array() );
	}
}

if ( ! function_exists( 'ezd_get_non_dismissed_contents' ) ) {
	function ezd_get_non_dismissed_contents( $product ) {
		$contents = ezd_get_html_notice_contents( $product );
		$non_dismissed = array();

		foreach ( $contents as $content ) {
			if ( isset( $content['id'] ) && ! ezd_is_content_dismissed( $product, $content['id'] ) ) {
				$non_dismissed[] = $content;
			}
		}

		return $non_dismissed;
	}
}

if ( ! function_exists( 'ezd_is_content_dismissed' ) ) {
	function ezd_is_content_dismissed( $product, $content_id ) {
		$content_dismiss_key = sanitize_key( $product ) . '_content_' . sanitize_key( $content_id ) . '_dismissed';
		return (bool) get_option( $content_dismiss_key, 0 );
	}
}

if ( ! function_exists( 'ezd_dismiss_content' ) ) {
	function ezd_dismiss_content( $product, $content_id ) {
		$content_dismiss_key = sanitize_key( $product ) . '_content_' . sanitize_key( $content_id ) . '_dismissed';
		update_option( $content_dismiss_key, 1 );
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

