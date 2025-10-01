<?php
/**
 * Cannot access directly.
 */
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

// Enqueue SweetAlert CSS and JS
wp_enqueue_style( 'sweetalert' );
wp_enqueue_script( 'sweetalert' );

// Ensure the function exists before calling is_plugin_active
if ( ! function_exists( 'is_plugin_active' ) ) {
    require_once ABSPATH . 'wp-admin/includes/plugin.php';
}

// Check if BetterDocs is active
$is_betterdocs_active = is_plugin_active( 'betterdocs/betterdocs.php' );
$disabled_attr        = $is_betterdocs_active ? '' : 'disabled';
?>

<div class="wrap">
    <div class="ezd-migration-wrapper">
        <div class="ezd-migration-inner">
            <img src="<?php echo esc_url( EAZYDOCS_IMG . '/bdocs-ezd.png' ); ?>" alt="<?php esc_attr_e( 'Eazydocs icon', 'eazydocs' ); ?>" />

            <h1><?php esc_html_e( 'Migrate from BetterDocs to EazyDocs', 'eazydocs' ); ?></h1>

            <div class="ezd-migration-content">
                <p>
                    <?php esc_html_e( 'This tool will help you migrate your existing documentation from', 'eazydocs' ); ?>
                    <strong>BetterDocs</strong>
                    <?php esc_html_e( 'into', 'eazydocs' ); ?>
                    <strong>EazyDocs</strong>.
                    <br>
                    <?php esc_html_e( 'During this migration:', 'eazydocs' ); ?>
                </p>

                <ul>
                    <li><?php esc_html_e( 'All', 'eazydocs' ); ?> <strong><?php esc_html_e( 'categories', 'eazydocs' ); ?></strong> <?php esc_html_e( 'will be converted into', 'eazydocs' ); ?> <strong><?php esc_html_e( 'parent Docs', 'eazydocs' ); ?></strong>.</li>
                    <li><?php esc_html_e( 'All existing Docs will be organized as', 'eazydocs' ); ?> <strong><?php esc_html_e( 'child Docs', 'eazydocs' ); ?></strong> <?php esc_html_e( 'under those parent Docs.', 'eazydocs' ); ?></li>
                    <li><?php esc_html_e( 'The URL structure will reflect the parent-child relationship (e.g.', 'eazydocs' ); ?> <code>/docs/parent-doc/child-doc/</code>).</li>
                    <li><?php esc_html_e( 'Your original category will be preserved as taxonomy terms.', 'eazydocs' ); ?></li>
                </ul>

                <span>
                    <?php esc_html_e( 'Need help?', 'eazydocs' ); ?>
                    <a href="https://helpdesk.spider-themes.net/docs/eazydocs-wordpress-plugin/getting-started/migrate-from-betterdocs-to-eazydocs" target="_blank"><?php esc_html_e( 'Read the full migration guide', 'eazydocs' ); ?></a>
                </span>
            </div>

            <button class="ezd-start-miration-btn button-primary" <?php echo esc_attr( $disabled_attr ); ?>>
                🚀 <?php esc_html_e( 'Start Migration', 'eazydocs' ); ?>
            </button>

        </div>
    </div>
</div>