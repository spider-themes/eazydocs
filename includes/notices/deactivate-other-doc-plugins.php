<?php
/**
 * Cannot access directly.
 */
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

/**
 * Notice
 * Deactivate the wedocs
 *
 * @return void
 */
add_action( 'admin_notices', function () {
	if ( is_plugin_active( 'wedocs/wedocs.php' ) ) :
		?>
        <div class="notice notice-warning eaz-notice">
            <p>
				<?php esc_html_e( 'We have detected another Knowledge Base Plugin installed in this site.', 'eazydocs' ); ?> <br>
				<?php esc_html_e( 'For EazyDocs to work efficiently, you need to migrate the data and deactivate that plugin to avoid conflict.', 'eazydocs' ); ?>
            </p>
            <p>
                <a href="?deactivate=wedocs" class="button-primary button-large red-bg">
					<?php esc_html_e( 'Deactivate weDocs', 'eazydocs' ); ?>
                </a>
            </p>
        </div>
	<?php
	endif;
} );

/**
 * Notice
 * Deactivate the BetterDocs
 *
 * @return void
 */
add_action( 'admin_notices', function () {
	if ( is_plugin_active( 'betterdocs/betterdocs.php' ) ) :
		?>
        <div class="notice notice-warning eaz-notice">
            <p>
				<?php esc_html_e( 'We have detected another Knowledge Base Plugin installed in this site.', 'eazydocs' ); ?> <br>
				<?php esc_html_e( 'For EazyDocs to work efficiently, you need to migrate the data and deactivate that plugin to avoid conflict.', 'eazydocs' ); ?>
            </p>
            <p>
                <a href="<?php echo esc_url( admin_url( 'admin.php?page=eazydocs-migration' ) ); ?>" class="button-primary button-large">
					<?php esc_html_e( 'Migrate to EazyDocs', 'eazydocs' ); ?>
                </a>
                <a href="?deactivate=betterdocs" class="button-primary button-large red-bg">
					<?php esc_html_e( 'Deactivate BetterDocs', 'eazydocs' ); ?>
                </a>
            </p>
        </div>
	<?php
	endif;
} );

/**
 * Deactivate Other Knowledge-base plugins
 */
if ( isset( $_GET['deactivate'] ) && ! empty( $_GET['deactivate'] ) ) {	
	$plugin = sanitize_text_field( $_GET['deactivate'] );
	add_action( 'admin_init', "eazydocs_deactivate_other_plugin" );
	function eazydocs_deactivate_other_plugin() {

		// Check if the current user has the capability to activate plugins
		if ( ! current_user_can( 'activate_plugins' ) ) {
			return;
		}

		$plugin = ! empty ( $_GET['deactivate'] ) ? sanitize_text_field( $_GET['deactivate'] ) : '';
		deactivate_plugins( "$plugin/$plugin.php" );
		$url = admin_url( 'plugins.php' );
		wp_safe_redirect( $url );
	}
}