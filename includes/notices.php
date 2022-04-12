<?php
/**
 * Notice
 * Deactivate the wedocs
 * @return void
 */
add_action( 'admin_notices', function () {
    if ( is_plugin_active('wedocs/wedocs.php') ) :
        ?>
        <div class="notice notice-warning">
            <p>
                <?php esc_html_e( 'We have detected another Knowledge Base Plugin installed in this site.', 'eazydocs' ); ?> <br>
	            <?php esc_html_e( 'For EazyDocs to work efficiently, you need to migrate the data and deactivate that plugin to avoid conflict.', 'eazydocs' ); ?>
            </p>
            <p>
                <a href="?deactivate=wedocs" class="button-primary button-large">
	                <?php esc_html_e( 'Deactivate weDocs', 'eazydocs' ); ?>
                </a>
            </p>
        </div>
    <?php
    endif;
});

/**
 * Notice
 * Deactivate the BetterDocs
 * @return void
 */
add_action( 'admin_notices', function () {
    if ( is_plugin_active('betterdocs/betterdocs.php') ) :
        ?>
        <div class="notice notice-warning">
            <p>
	            <?php esc_html_e( 'We have detected another Knowledge Base Plugin installed in this site.', 'eazydocs' ); ?> <br>
	            <?php esc_html_e( 'For EazyDocs to work efficiently, you need to migrate the data and deactivate that plugin to avoid conflict.', 'eazydocs' ); ?>
            </p>
            <p>
                <a href="?deactivate=betterdocs" class="button-primary button-large">
	                <?php esc_html_e( 'Deactivate BetterDocs', 'eazydocs' ); ?>
                </a>
            </p>
        </div>
        <?php
    endif;
});

/**
 * Deactivate Other Knowledge-base plugins
 */
if ( isset($_GET['deactivate']) && !empty($_GET['deactivate']) ) {

	$plugin = ! empty ( $_GET['deactivate'] ) ? sanitize_text_field( $_GET['deactivate'] ) : '';
    add_action( 'admin_init', "eazydocs_deactivate_other_plugin" );
    function eazydocs_deactivate_other_plugin() {
	    $plugin = ! empty ( $_GET['deactivate'] ) ? sanitize_text_field( $_GET['deactivate'] ) : '';
        deactivate_plugins("$plugin/$plugin.php");
    }
}