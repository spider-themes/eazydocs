<?php
/**
 * Cannot access directly.
 */
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

/**
 * Plugin Installer class - responsible for installing multiple selected plugins.
 */
add_action( 'wp_ajax_ezd_plugin_action', function () {
	// Verify nonce for security
	check_ajax_referer( 'eazydocs-admin-nonce', 'security' );

	if ( ! current_user_can( 'install_plugins' ) ) {
		wp_send_json_error( [ 'message' => esc_html__( 'Permission denied.', 'eazydocs' ) ] );
	}

	$plugin_slug = isset( $_POST['plugin'] ) ? sanitize_text_field( wp_unslash( $_POST['plugin'] ) ) : '';
	$task        = isset( $_POST['task'] ) ? sanitize_text_field( wp_unslash( $_POST['task'] ) ) : '';

	if ( ! $plugin_slug || ! $task ) {
		wp_send_json_error( [ 'message' => esc_html__( 'Invalid request.', 'eazydocs' ) ] );
	}

	if ( $task === 'install' ) {
		include_once ABSPATH . 'wp-admin/includes/class-wp-upgrader.php';
		include_once ABSPATH . 'wp-admin/includes/plugin-install.php';
		include_once ABSPATH . 'wp-admin/includes/plugin.php';

		$api = plugins_api( 'plugin_information', [ 'slug' => $plugin_slug, 'fields' => [ 'sections' => false ] ] );
		if ( is_wp_error( $api ) ) {
			wp_send_json_error( [ 'message' => esc_html__( 'Plugin info not found.', 'eazydocs' ) ] );
		}

		$upgrader       = new Plugin_Upgrader( new Automatic_Upgrader_Skin() );
		$install_result = $upgrader->install( $api->download_link );

		if ( is_wp_error( $install_result ) || ! $install_result ) {
			wp_send_json_error( [ 'message' => esc_html__( 'Plugin installation failed.', 'eazydocs' ) ] );
		}

		// Clean plugin cache so WP recognizes it immediately
		wp_clean_plugins_cache();
		wp_send_json_success();
	}

	if ( $task === 'activate' ) {
		include_once ABSPATH . 'wp-admin/includes/plugin.php';

		$plugin_file = $plugin_slug . '/' . $plugin_slug . '.php';
		if ( ! file_exists( WP_PLUGIN_DIR . '/' . $plugin_file ) ) {
			wp_send_json_error( [ 'message' => esc_html__( 'Plugin file not found.', 'eazydocs' ) ] );
		}

		$activate = activate_plugin( $plugin_file );
		if ( is_wp_error( $activate ) ) {
			wp_send_json_error( [ 'message' => $activate->get_error_message() ] );
		}

		wp_send_json_success();
	}

	wp_send_json_error( [ 'message' => esc_html__( 'Unknown action.', 'eazydocs' ) ] );
} );