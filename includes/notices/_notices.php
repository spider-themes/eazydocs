<?php
/**
 * Cannot access directly.
 */
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

require_once __DIR__ . '/deactivate-other-doc-plugins.php';
require_once __DIR__ . '/asking-for-review.php';
require_once __DIR__ . '/offer.php';
if ( ! ezd_is_premium() ) {
	require_once __DIR__ . '/eazydocs-cron.php';
	require_once __DIR__ . '/eazydocs-offer-notices.php';
}