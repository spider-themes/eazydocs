<?php
/**
 * Cannot access directly.
 */
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

require_once __DIR__ . '/deactivate-other-doc-plugins.php';
require_once __DIR__ . '/gutenberg-info.php';
require_once __DIR__ . '/asking-for-review.php';
require_once __DIR__ . '/offer.php';
require_once __DIR__ . '/class-remote-notice-client.php';

// Disable notices when Pro is active
add_action( 'admin_init', function() {
    if (function_exists('ezd_is_premium') && ezd_is_premium() ) {
        Remote_Notice_Client::disable( 'Eazydocs' );
        return;
    }
    
    Remote_Notice_Client::init( 'Eazydocs', [
        'api_url' => 'https://manage.spider-themes.net/wp-json/html-notice-widget/v1/content/eazydocs',
    ]);
});