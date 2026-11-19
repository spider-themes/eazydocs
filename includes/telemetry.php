<?php
/**
 * Anonymous usage reporting for EazyDocs.
 *
 * Counts docs, views, and searches. Never stores doc text, search keywords,
 * feedback messages, names, or email addresses.
 *
 * @package eazydocs
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$ezd_telemetry_sdk = __DIR__ . '/class-spider-telemetry.php';
if ( file_exists( $ezd_telemetry_sdk ) ) {
	require_once $ezd_telemetry_sdk;
}

/**
 * Whether Freemius usage sharing is on for EazyDocs.
 *
 * The opt-in click is the decision. Email confirmation only finishes the
 * Freemius account, so a pending activation still counts.
 *
 * @return bool
 */
function ezd_fs_usage_allowed() {
	if ( ! function_exists( 'eaz_fs' ) ) {
		return false;
	}
	$fs = eaz_fs();
	if ( method_exists( $fs, 'is_tracking_allowed' ) && $fs->is_tracking_allowed() ) {
		return true;
	}
	return method_exists( $fs, 'is_pending_activation' ) && $fs->is_pending_activation();
}

/**
 * Whether the settings switch should show usage sharing as on.
 *
 * A missing choice stays off. Freemius tracking counts only when the site
 * has not opted out.
 *
 * @return bool
 */
function ezd_share_usage_enabled() {
	if ( get_option( 'spiderthemes_telemetry_eazydocs_optout', false ) ) {
		return false;
	}
	if ( get_option( 'spiderthemes_telemetry_eazydocs_optin', false ) ) {
		return true;
	}
	return ezd_fs_usage_allowed();
}

/**
 * Whether a settings switch value is on.
 *
 * @param mixed $value Stored or posted value.
 * @return bool
 */
function ezd_telemetry_is_on( $value ) {
	return in_array( $value, array( true, 1, '1', 'true', 'on' ), true );
}

/**
 * Show the live consent choice on the settings screen without storing it
 * inside the EazyDocs settings array.
 *
 * @param mixed $value Settings option.
 * @return mixed
 */
function ezd_telemetry_settings_display( $value ) {
	if ( ! is_admin() || ! is_array( $value ) ) {
		return $value;
	}
	if ( empty( $_GET['page'] ) || 'eazydocs-settings' !== $_GET['page'] ) {
		return $value;
	}
	$value['share_anonymous_usage'] = ezd_share_usage_enabled() ? '1' : '0';
	return $value;
}
add_filter( 'option_eazydocs_settings', 'ezd_telemetry_settings_display' );

/**
 * Apply an explicit Share usage change, then keep it out of the settings array.
 *
 * Saving any other setting leaves consent untouched.
 *
 * @param mixed $data Settings being saved.
 * @return mixed
 */
function ezd_telemetry_on_settings_save( $data ) {
	if ( ! is_array( $data ) || ! array_key_exists( 'share_anonymous_usage', $data ) ) {
		return $data;
	}
	$posted = ezd_telemetry_is_on( $data['share_anonymous_usage'] );
	unset( $data['share_anonymous_usage'] );

	if ( $posted === ezd_share_usage_enabled() || ! class_exists( 'SpiderThemes\\Telemetry' ) ) {
		return $data;
	}
	if ( $posted ) {
		delete_option( 'spiderthemes_telemetry_eazydocs_optout' );
		\SpiderThemes\Telemetry::grant_consent( 'eazydocs', 'user' );
	} else {
		\SpiderThemes\Telemetry::revoke_consent( 'eazydocs' );
	}
	return $data;
}
add_filter( 'csf_eazydocs_settings_save', 'ezd_telemetry_on_settings_save', 20 );

add_action( 'init', 'ezd_boot_telemetry', 1 );

/**
 * Register the telemetry client after translations are available.
 *
 * @return void
 */
function ezd_boot_telemetry() {
	if ( ! class_exists( 'SpiderThemes\\Telemetry' ) ) {
		return;
	}

	\SpiderThemes\Telemetry::init( array(
		'plugin_slug'              => 'eazydocs',
		'plugin_name'              => 'EazyDocs',
		'plugin_version'           => defined( 'EZD_VERSION' ) ? EZD_VERSION : '2.15.1',
		'plugin_file'              => defined( 'EZD_FILE' ) ? EZD_FILE : dirname( __DIR__ ) . '/eazydocs.php',
		'optin_title'              => __( 'Help us improve EazyDocs', 'eazydocs' ),
		'optin_message'            => __( 'Help make EazyDocs better by sharing anonymous documentation counts. Doc text, search keywords, and email addresses are never collected.', 'eazydocs' ),
		'consent_callback'         => 'ezd_fs_usage_allowed',
		'consent_follows_callback' => true,
		'custom_metrics'           => 'ezd_telemetry_metrics',
	) );
}

/**
 * Whether an analytics table exists.
 *
 * @param string $table Full table name.
 * @return bool
 */
function ezd_telemetry_table_ready( $table ) {
	static $ready = array();
	if ( isset( $ready[ $table ] ) ) {
		return $ready[ $table ];
	}
	global $wpdb;
	$found            = $wpdb->get_var( $wpdb->prepare( 'SHOW TABLES LIKE %s', $wpdb->esc_like( $table ) ) );
	$ready[ $table ]  = ( $found === $table );
	return $ready[ $table ];
}

/**
 * Aggregate one number from an analytics table over the last 30 days.
 *
 * @param string $table Full table name.
 * @param string $expression Fixed SQL aggregate.
 * @return int
 */
function ezd_telemetry_since( $table, $expression ) {
	if ( ! ezd_telemetry_table_ready( $table ) ) {
		return 0;
	}
	global $wpdb;
	$since = gmdate( 'Y-m-d H:i:s', time() - ( 30 * DAY_IN_SECONDS ) );
	// phpcs:ignore WordPress.DB.PreparedSQL.InterpolatedNotPrepared -- table and aggregate are fixed strings.
	$value = $wpdb->get_var( $wpdb->prepare( "SELECT {$expression} FROM {$table} WHERE created_at >= %s", $since ) );
	return (int) $value;
}

/**
 * Count published posts of one type.
 *
 * @param string $post_type Post type.
 * @param string $extra Fixed SQL condition.
 * @return int
 */
function ezd_telemetry_posts( $post_type, $extra = '' ) {
	global $wpdb;
	// phpcs:ignore WordPress.DB.PreparedSQL.InterpolatedNotPrepared -- post type is a fixed string and extra is a fixed condition.
	return (int) $wpdb->get_var( $wpdb->prepare( "SELECT COUNT(*) FROM {$wpdb->posts} WHERE post_type = %s AND post_status = 'publish' {$extra}", $post_type ) );
}

/**
 * Sum a numeric meta value on docs only.
 *
 * @param string $meta_key Meta key.
 * @return int
 */
function ezd_telemetry_doc_meta_sum( $meta_key ) {
	global $wpdb;
	$value = $wpdb->get_var(
		$wpdb->prepare(
			"SELECT SUM(pm.meta_value + 0) FROM {$wpdb->postmeta} pm INNER JOIN {$wpdb->posts} p ON p.ID = pm.post_id WHERE pm.meta_key = %s AND p.post_type = 'docs'",
			$meta_key
		)
	);
	return (int) $value;
}

/**
 * Whether a product setting is enabled, using the same default the settings screen uses.
 *
 * @param string $key Settings key.
 * @param bool   $default Field default.
 * @return int
 */
function ezd_telemetry_flag( $key, $default = false ) {
	if ( ! function_exists( 'ezd_get_opt' ) ) {
		return $default ? 1 : 0;
	}
	$value = ezd_get_opt( $key, null );
	if ( null === $value || '' === $value ) {
		return $default ? 1 : 0;
	}
	return ezd_telemetry_is_on( $value ) ? 1 : 0;
}

/**
 * Metrics sent with an EazyDocs heartbeat.
 *
 * @return array<string, mixed>
 */
function ezd_telemetry_metrics() {
	global $wpdb;

	$content = array(
		'docs'                => ezd_telemetry_posts( 'docs', 'AND post_parent = 0' ),
		'articles'            => ezd_telemetry_posts( 'docs', 'AND post_parent > 0' ),
		'onepage'             => ezd_telemetry_posts( 'onepage-docs' ),
		'views_30d'           => ezd_telemetry_since( $wpdb->prefix . 'eazydocs_view_log', 'SUM(count)' ),
		'searches_30d'        => ezd_telemetry_since( $wpdb->prefix . 'eazydocs_search_log', 'COUNT(*)' ),
		'failed_searches_30d' => ezd_telemetry_since( $wpdb->prefix . 'eazydocs_search_log', 'SUM(not_found_count)' ),
		'helpful'             => ezd_telemetry_doc_meta_sum( 'positive' ),
		'unhelpful'           => ezd_telemetry_doc_meta_sum( 'negative' ),
		'feedback_messages'   => (int) $wpdb->get_var( "SELECT COUNT(*) FROM {$wpdb->posts} WHERE post_type = 'ezd_feedback' AND post_status NOT IN ('trash', 'auto-draft')" ),
		'feedback'            => ezd_telemetry_flag( 'docs-feedback', true ),
		'assistant'           => ezd_telemetry_flag( 'assistant_visibility', false ),
		'contributions'       => ezd_telemetry_flag( 'is_doc_contribution', false ),
		'subscriptions'       => ezd_telemetry_flag( 'subscriptions', false ),
		'google_login'        => ezd_telemetry_flag( 'is_google_login', false ),
	);

	$features = array();
	foreach ( $content as $key => $count ) {
		$features[] = array(
			'feature'      => $key,
			'requests'     => (int) $count,
			'total_tokens' => 0,
		);
	}

	$is_pro = function_exists( 'ezd_is_premium' ) && ezd_is_premium();

	return array(
		'is_pro'            => (bool) $is_pro,
		'content_counts'    => $content,
		'feature_usage_30d' => $features,
	);
}

/**
 * Keep Freemius tracking and the Share usage switch in agreement.
 *
 * Share usage off wins. A Freemius opt-out clears only a mirrored opt-in.
 *
 * @return void
 */
function ezd_sync_telemetry_consent() {
	if ( ! function_exists( 'eaz_fs' ) || ! class_exists( 'SpiderThemes\\Telemetry' ) ) {
		return;
	}
	if ( get_option( 'spiderthemes_telemetry_eazydocs_optout', false ) ) {
		return;
	}

	$telemetry = \SpiderThemes\Telemetry::get_instance( 'eazydocs' );
	$fs_ready  = method_exists( eaz_fs(), 'is_tracking_allowed' ) || method_exists( eaz_fs(), 'is_pending_activation' );
	$allowed   = ezd_fs_usage_allowed();
	if ( $fs_ready && ! $allowed ) {
		$source = (string) get_option( 'spiderthemes_telemetry_eazydocs_optin_source', '' );
		if ( 'user' !== $source ) {
			delete_option( 'spiderthemes_telemetry_eazydocs_optin' );
			delete_option( 'spiderthemes_telemetry_eazydocs_optin_source' );
			wp_clear_scheduled_hook( 'spiderthemes_telemetry_heartbeat_eazydocs' );
		}
	} elseif ( $allowed ) {
		\SpiderThemes\Telemetry::grant_consent( 'eazydocs', 'freemius' );
	}

	if ( $telemetry && $telemetry->has_consent() ) {
		$stamp_key = 'spiderthemes_telemetry_eazydocs_metrics_at';
		$rev_key   = 'spiderthemes_telemetry_eazydocs_metrics_rev';
		$last_sent = (int) get_option( $stamp_key, 0 );
		$due       = ( time() - $last_sent ) >= DAY_IN_SECONDS;
		$rev       = (int) get_option( $rev_key, 0 ) < 1;
		if ( $due || $rev ) {
			update_option( $stamp_key, time(), false );
			update_option( $rev_key, 1, false );
			$telemetry->send_heartbeat();
		}
	}
}
add_action( 'admin_init', 'ezd_sync_telemetry_consent' );

/**
 * Send the Pro upgrade once, and only after the event is accepted.
 *
 * @return void
 */
function ezd_track_pro_upgrade() {
	if ( get_option( 'ezd_telemetry_pro_tracked' ) ) {
		return;
	}
	if ( ! function_exists( 'ezd_is_premium' ) || ! ezd_is_premium() ) {
		return;
	}
	if ( class_exists( 'SpiderThemes\\Telemetry' ) && \SpiderThemes\Telemetry::track_event( 'eazydocs', 'upgraded_to_pro' ) ) {
		update_option( 'ezd_telemetry_pro_tracked', 1, false );
	}
}
add_action( 'admin_init', 'ezd_track_pro_upgrade' );
