<?php
/**
 * Cannot access directly.
 */
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

/**
 * Show notice if other Knowledge Base plugins are active.
 */
add_action( 'admin_notices', function () {
    $conflicting_plugins = [
        'wedocs/wedocs.php' => [
            'name'   => 'weDocs',
            'migrate'=> false
        ],
        'betterdocs/betterdocs.php' => [
            'name'   => 'BetterDocs',
            'migrate'=> true
        ]
    ];

    foreach ( $conflicting_plugins as $plugin_file => $plugin_data ) :
        if ( is_plugin_active( $plugin_file ) ) :
            ?>
            <div class="notice notice-warning eaz-notice">
                <p>
                    <?php esc_html_e( 'We have detected another Knowledge Base Plugin installed on this site.', 'eazydocs' ); ?>
                    <br>
                    <?php esc_html_e( 'For EazyDocs to work efficiently, please migrate the data (if available) and deactivate that plugin to avoid conflicts.', 'eazydocs' ); ?>
                </p>
                <p>
                    <?php 
					if ( $plugin_data['migrate'] ) : 
						?>
                        <a href="<?php echo esc_url( admin_url( 'admin.php?page=eazydocs-migration' ) ); ?>" class="button-primary button-large">
                            <?php esc_html_e( 'Migrate to EazyDocs', 'eazydocs' ); ?>
                        </a>
                    	<?php 
					endif; 
					?>
                    <a href="<?php echo esc_url( wp_nonce_url( add_query_arg( [ 'eazydocs_deactivate' => $plugin_file ] ), 'eazydocs_deactivate_plugin' ) ); ?>" class="button-primary button-large red-bg">
                        <?php 
						// translators: %s is the name of the plugin being deactivated.
						printf( esc_html__( 'Deactivate %s', 'eazydocs' ), esc_html( $plugin_data['name'] ) ); 
						?>
                    </a>
                </p>
            </div>
            <?php
        endif;
    endforeach;
});

/**
 * Deactivate other Knowledge Base plugins securely.
 */
add_action( 'admin_init', function () {
    if ( isset( $_GET['eazydocs_deactivate'] ) && check_admin_referer( 'eazydocs_deactivate_plugin' ) ) {

        if ( ! current_user_can( 'activate_plugins' ) ) {
            return;
        }

        $plugin_file = sanitize_text_field( wp_unslash( $_GET['eazydocs_deactivate'] ) );

        if ( is_plugin_active( $plugin_file ) ) {
            deactivate_plugins( $plugin_file );

            wp_safe_redirect( add_query_arg( [
                'deactivated' => 'true',
                'plugin'      => urlencode( $plugin_file )
            ], admin_url( 'plugins.php' ) ) );
            exit;
        }
    }
});