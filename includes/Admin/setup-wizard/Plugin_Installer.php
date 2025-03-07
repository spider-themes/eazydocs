<?php
/**
 * Plugin Installer class - responsible for installing multiple selected plugins.
 */
add_action( 'wp_ajax_ezd_install_selected_plugins', 'ezd_install_selected_plugins_callback' );

function ezd_install_selected_plugins_callback() {
    if ( ! current_user_can( 'install_plugins' ) ) {
        wp_send_json_error( __( 'Permission denied.', 'eazydocs' ) );
    }

    if ( empty( $_POST['slugs' ] ) || ! is_array( $_POST['slugs' ] ) ) {
        wp_send_json_error( __( 'Invalid plugin selection.', 'eazydocs' ) );
    }

    include_once ABSPATH . 'wp-admin/includes/plugin-install.php';
    include_once ABSPATH . 'wp-admin/includes/class-wp-upgrader.php';
    include_once ABSPATH . 'wp-admin/includes/plugin.php';

    $installed_plugins = [];
    $activated_plugins = [];

    foreach ( $_POST['slugs'] as $plugin_slug ) {
        $plugin_slug     = sanitize_text_field( $plugin_slug );
        $plugin_basename = $plugin_slug . '/' . $plugin_slug . '.php';

        // Check if the plugin is already installed
        $plugins         = get_plugins();
        $is_installed    = array_key_exists( $plugin_basename, $plugins );

        if ( $is_installed ) {
            // If already installed, just activate
            if ( ! is_plugin_active( $plugin_basename ) ) {
                activate_plugin( $plugin_basename );
                $activated_plugins[] = $plugin_slug;
            }
        } else {
            // Fetch plugin info
            $api = plugins_api( 'plugin_information', [
                'slug'   => $plugin_slug, 
                'fields' => [
                    'sections' => false 
                ]
            ] );

            if ( is_wp_error( $api ) ) {
                wp_send_json_error(['message' => __( 'Failed to fetch plugin info: ', 'eazydocs' ) . $api->get_error_message() ] );
            }

            // Install the plugin
            $upgrader = new Plugin_Upgrader(new Automatic_Upgrader_Skin() );
            $install_result = $upgrader->install( $api->download_link);

            if ( is_wp_error( $install_result) || !$install_result) {
                wp_send_json_error(['message' => __( 'Installation failed for ', 'eazydocs' ) . $plugin_slug]);
            }

            // Activate the plugin after installation
            if ( ! is_plugin_active( $plugin_basename ) ) {
                activate_plugin( $plugin_basename );
            }
            
            $installed_plugins[] = $plugin_slug;
        }
    }

    wp_send_json_success([
        'message'   => __( 'Plugins processed successfully.', 'eazydocs' ),
        'installed' => $installed_plugins,
        'activated' => $activated_plugins
    ]);
}