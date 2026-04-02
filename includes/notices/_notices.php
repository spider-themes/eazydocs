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

add_action( 'admin_init', function() {
    if ( class_exists( 'Remote_Notice_Client' ) ) {
        Remote_Notice_Client::init( 'Eazydocs', [
            'api_url'        => 'https://manage.spider-themes.net/wp-json/noticepilot/v1/content/eazydocs',
            'plugin_version' => EZD_VERSION,
            'is_pro'         => ezd_is_premium(),
        ]);
    }
});