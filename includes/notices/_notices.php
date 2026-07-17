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

/**
 * NoticePilot — remote admin-notice campaigns (SDK v1.6.1).
 *
 * Product id .......... 'Eazydocs' (used for every SDK call below — keep consistent).
 * Hub endpoint ........ manage.spider-themes.net → /content/eazydocs
 *
 * This lives in the free EazyDocs plugin, which is always active (EazyDocs Pro
 * requires it), so this single integration covers both free and Pro users —
 * ezd_is_premium() reports which one is running.
 */
add_action( 'plugins_loaded', function () {
    if ( ! class_exists( 'Noticepilot_Remote_Notice_Client' ) ) {
        return;
    }

    Noticepilot_Remote_Notice_Client::init( 'Eazydocs', [
        'api_url'          => 'https://manage.spider-themes.net/wp-json/noticepilot/v1/content/eazydocs',

        // How often to pull campaigns, and who may see them.
        'schedule'         => 'daily',            // hourly | twicedaily | daily
        'capability'       => 'manage_options',

        // Audience targeting: campaigns can target by version rule + free/Pro.
        'plugin_version'   => EZD_VERSION,
        'is_pro'           => ezd_is_premium(),

        // Frequency / dismissal behaviour (keeps notices tasteful — wp.org guideline 11).
        'max_notices'      => 2,                  // never stack more than 2 at once
        'dismiss_duration' => WEEK_IN_SECONDS,    // a dismissal sticks for a week
        'snooze_duration'  => WEEK_IN_SECONDS,    // "Remind me later" cooldown

        // Analytics consent (wp.org guideline 7): keep beacons OFF until the user
        // opts in. We reuse EazyDocs' existing Freemius tracking opt-in as the
        // consent signal (synced just below), so there is no second consent prompt.
        'require_consent'  => true,

        // Deactivation feedback is intentionally left OFF: Freemius already shows
        // its own deactivation survey for EazyDocs, so enabling the SDK prompt too
        // would give the user a duplicate modal on deactivate.
        // 'deactivation_feedback' => true,
        // 'plugin_file'           => 'eazydocs/eazydocs.php',
    ] );
} );

/**
 * Keep NoticePilot analytics consent in sync with the user's Freemius
 * tracking choice. Because init() runs with require_consent => true, no
 * impression/click/dismissal/goal beacon fires until this grants consent.
 */
add_action( 'admin_init', function () {
    if ( ! class_exists( 'Noticepilot_Remote_Notice_Client' ) || ! function_exists( 'eaz_fs' ) ) {
        return;
    }

    // is_tracking_allowed() is true once the user opts in to Freemius tracking.
    if ( method_exists( eaz_fs(), 'is_tracking_allowed' ) && eaz_fs()->is_tracking_allowed() ) {
        Noticepilot_Remote_Notice_Client::grant_consent( 'Eazydocs' );
    } else {
        Noticepilot_Remote_Notice_Client::revoke_consent( 'Eazydocs' );
    }
} );

/**
 * Conversion goal: report the Pro upgrade once, attributed to the campaign /
 * variant that most recently drove it. This is what lets the hub measure real
 * conversion rate (and pick A/B winners), not just clicks.
 */
add_action( 'admin_init', function () {
    if ( ! class_exists( 'Noticepilot_Remote_Notice_Client' ) || ! function_exists( 'ezd_is_premium' ) ) {
        return;
    }

    if ( ezd_is_premium() && ! get_option( 'ezd_np_goal_upgraded' ) ) {
        // track_goal() returns true only when the beacon actually fired (consent
        // granted + a campaign was seen). Mark it recorded only then, so a user
        // who opts in later still gets the conversion attributed.
        if ( Noticepilot_Remote_Notice_Client::track_goal( 'Eazydocs', 'upgraded_to_pro' ) ) {
            update_option( 'ezd_np_goal_upgraded', 1, false );
        }
    }
} );

/**
 * Smart-trigger metric: report the live number of published docs. NoticePilot
 * only stores the number — WE do the counting. On the hub you can then trigger
 * a campaign at a milestone (e.g. nudge for Pro once "docs_created" >= 10).
 *
 * Fires on every docs save; reporting the current total is idempotent, so
 * updates and re-saves simply re-report the same accurate count.
 */
add_action( 'save_post_docs', function ( $post_id, $post ) {
    if ( ! class_exists( 'Noticepilot_Remote_Notice_Client' ) ) {
        return;
    }
    if ( wp_is_post_revision( $post_id ) || wp_is_post_autosave( $post_id ) ) {
        return;
    }
    if ( 'publish' !== $post->post_status ) {
        return;
    }

    $count = (int) wp_count_posts( 'docs' )->publish;
    Noticepilot_Remote_Notice_Client::set_metric( 'Eazydocs', 'docs_created', $count );
}, 10, 2 );