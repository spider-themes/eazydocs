<?php
/**
 * Remote Notice Client SDK
 *
 * A reusable SDK for integrating remote HTML notices from the NoticePilot API.
 * Bundle this file with your plugin to enable remote admin notices.
 *
 * @package Noticepilot_Remote_Notice_Client
 * @version 1.6.1
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! class_exists( 'Noticepilot_Remote_Notice_Client' ) ) {

	/**
	 * Remote Notice Client Class
	 */
	class Noticepilot_Remote_Notice_Client {

		/**
		 * SDK version.
		 */
		const SDK_VERSION = '1.6.1';

		/**
		 * Registered instances
		 *
		 * @var array
		 */
		private static $instances = array();

		/**
		 * Product identifier
		 *
		 * @var string
		 */
		private $product;

		/**
		 * API URL
		 *
		 * @var string
		 */
		private $api_url;

		/**
		 * Cron schedule
		 *
		 * @var string
		 */
		private $schedule;

		/**
		 * Required capability
		 *
		 * @var string
		 */
		private $capability;

		/**
		 * Dismiss duration in seconds (default: 1 week, 0 = permanent)
		 *
		 * @var int
		 */
		private $dismiss_duration;

		/**
		 * Snooze duration in seconds for "Remind me later" (default: 1 week)
		 *
		 * @var int
		 */
		private $snooze_duration;

		/**
		 * Maximum notices displayed at once (frequency cap)
		 *
		 * @var int
		 */
		private $max_notices;

		/**
		 * Admin screen IDs where "plugin screens only" campaigns may render
		 *
		 * @var array
		 */
		private $screen_ids;

		/**
		 * Whether analytics beacons require explicit consent (wp.org guideline 7)
		 *
		 * @var bool
		 */
		private $require_consent;

		/**
		 * Plugin version for targeting (provided by integrating plugin)
		 *
		 * @var string
		 */
		private $plugin_version;

		/**
		 * Whether pro version is active (provided by integrating plugin)
		 *
		 * @var bool
		 */
		private $is_pro;

		/**
		 * Whether to show a deactivation-feedback modal for the host plugin.
		 *
		 * @var bool
		 */
		private $deactivation_feedback;

		/**
		 * Host plugin basename (e.g. my-plugin/my-plugin.php) for the
		 * deactivation-feedback modal to target on the Plugins screen.
		 *
		 * @var string
		 */
		private $plugin_file;

		/**
		 * Allow HTML tags for content
		 *
		 * @var array
		 */
		private $allowed_html;

		/**
		 * Initialize the client
		 *
		 * @param string $product Product identifier.
		 * @param array  $config  Configuration options.
		 * @return Noticepilot_Remote_Notice_Client|false Instance or false if disabled.
		 */
		public static function init( $product, $config = array() ) {
			$product = sanitize_key( $product );

			// Check if disabled (e.g., when Pro is active)
			if ( self::is_disabled( $product ) ) {
				return false;
			}

			if ( isset( self::$instances[ $product ] ) ) {
				return self::$instances[ $product ];
			}

			$instance                    = new self( $product, $config );
			self::$instances[ $product ] = $instance;
			$instance->setup_hooks();

			return $instance;
		}

		/**
		 * Constructor
		 *
		 * @param string $product Product identifier.
		 * @param array  $config  Configuration options.
		 */
		private function __construct( $product, $config ) {
			$this->product          = $product;
			$this->api_url          = isset( $config['api_url'] ) ? esc_url_raw( $config['api_url'] ) : '';
			$this->schedule         = isset( $config['schedule'] ) ? $config['schedule'] : 'daily';
			$this->capability       = isset( $config['capability'] ) ? $config['capability'] : 'manage_options';
			$this->dismiss_duration = isset( $config['dismiss_duration'] ) ? absint( $config['dismiss_duration'] ) : WEEK_IN_SECONDS;
			$this->snooze_duration  = isset( $config['snooze_duration'] ) ? absint( $config['snooze_duration'] ) : WEEK_IN_SECONDS;
			$this->max_notices      = isset( $config['max_notices'] ) ? max( 1, absint( $config['max_notices'] ) ) : 3;
			$this->screen_ids       = isset( $config['screen_ids'] ) && is_array( $config['screen_ids'] ) ? array_map( 'sanitize_key', $config['screen_ids'] ) : array();
			$this->require_consent  = ! empty( $config['require_consent'] );
			$this->plugin_version   = isset( $config['plugin_version'] ) ? sanitize_text_field( $config['plugin_version'] ) : '';
			$this->is_pro           = isset( $config['is_pro'] ) ? (bool) $config['is_pro'] : false;
			$this->deactivation_feedback = ! empty( $config['deactivation_feedback'] );
			$this->plugin_file      = isset( $config['plugin_file'] ) ? (string) $config['plugin_file'] : '';
			
			// Define allowed HTML tags for notices
			$this->allowed_html = array(
				'div'    => array( 'class' => array(), 'style' => array(), 'id' => array() ),
				'p'      => array( 'class' => array(), 'style' => array() ),
				'span'   => array( 'class' => array(), 'style' => array() ),
				'strong' => array( 'class' => array() ),
				'em'     => array( 'class' => array() ),
				'b'      => array(),
				'i'      => array(),
				'a'      => array( 'href' => array(), 'title' => array(), 'target' => array(), 'class' => array(), 'rel' => array() ),
				'br'     => array(),
				'h1'     => array( 'class' => array() ),
				'h2'     => array( 'class' => array() ),
				'h3'     => array( 'class' => array() ),
				'h4'     => array( 'class' => array() ),
				'ul'     => array( 'class' => array() ),
				'ol'     => array( 'class' => array() ),
				'li'     => array( 'class' => array() ),
				'img'    => array( 'src' => array(), 'alt' => array(), 'class' => array(), 'width' => array(), 'height' => array() ),
				'style'  => array( 'type' => array(), 'media' => array() ),
			);
		}

		/**
		 * Setup WordPress hooks
		 */
		private function setup_hooks() {
			// Cron. If admin_init has already fired (or is currently firing — e.g. the
			// integrating plugin called init() from within its own admin_init callback),
			// schedule immediately; otherwise hook normally. Without this, registering a
			// callback on admin_init while admin_init is mid-fire silently drops the
			// callback and the cron event is never scheduled.
			if ( did_action( 'admin_init' ) || doing_action( 'admin_init' ) ) {
				$this->init_cron();
			} else {
				add_action( 'admin_init', array( $this, 'init_cron' ) );
			}
			add_action( 'noticepilot_rnc_fetch_content_' . $this->product, array( $this, 'fetch_content' ) );

			// Enqueue inline CSS/JS via WordPress APIs.
			add_action( 'admin_enqueue_scripts', array( $this, 'enqueue_notice_assets' ) );

			// Display notices.
			add_action( 'admin_notices', array( $this, 'display_notices' ) );

			// AJAX handlers.
			add_action( 'wp_ajax_noticepilot_rnc_dismiss_content_' . $this->product, array( $this, 'ajax_dismiss_content' ) );

			// Deactivation-feedback modal (opt-in, Plugins screen only).
			if ( $this->deactivation_feedback && $this->plugin_file ) {
				add_action( 'admin_enqueue_scripts', array( $this, 'enqueue_deactivation_assets' ) );
			}

			// Record first-seen once (basis for install-age smart triggers).
			$this->ensure_first_seen();
		}

		/**
		 * Enqueue the deactivation-feedback modal assets on the Plugins screen.
		 *
		 * The modal intercepts the host plugin's Deactivate link, asks an
		 * optional (skippable) reason, posts it to the hub's /feedback endpoint
		 * via sendBeacon, then proceeds with deactivation. Never blocks.
		 *
		 * @param string $hook_suffix Current admin page.
		 */
		public function enqueue_deactivation_assets( $hook_suffix ) {
			if ( 'plugins.php' !== $hook_suffix ) {
				return;
			}
			if ( ! current_user_can( 'deactivate_plugin', $this->plugin_file ) ) {
				return;
			}
			if ( empty( $this->api_url ) ) {
				return;
			}

			$handle = 'noticepilot-rnc-deact-' . $this->product;
			wp_register_style( $handle, false );
			wp_enqueue_style( $handle );
			wp_add_inline_style( $handle, $this->get_deactivation_css() );

			wp_register_script( $handle, false, array(), self::SDK_VERSION, true );
			wp_enqueue_script( $handle );
			wp_add_inline_script( $handle, $this->get_deactivation_js() );
		}

		/**
		 * CSS for the deactivation-feedback modal.
		 *
		 * @return string
		 */
		private function get_deactivation_css() {
			return '.rnc-deact-ov{display:none;position:fixed;inset:0;z-index:100000;background:rgba(0,0,0,.5);align-items:center;justify-content:center}'
				. '.rnc-deact-ov.open{display:flex}'
				. '.rnc-deact{background:#fff;border-radius:10px;max-width:460px;width:92%;padding:22px;box-shadow:0 10px 40px rgba(0,0,0,.25);font-size:13px}'
				. '.rnc-deact h2{margin:0 0 4px;font-size:16px}'
				. '.rnc-deact__sub{margin:0 0 14px;color:#6b7280;font-size:12px}'
				. '.rnc-deact__reasons{list-style:none;margin:0 0 12px;padding:0}'
				. '.rnc-deact__reasons li{padding:6px 0}'
				. '.rnc-deact__reasons label{display:flex;gap:8px;align-items:flex-start;cursor:pointer}'
				. '.rnc-deact textarea{width:100%;margin-top:8px;border:1px solid #d1d5db;border-radius:6px;padding:8px;font-size:13px}'
				. '.rnc-deact__actions{display:flex;gap:8px;justify-content:flex-end;margin-top:14px}'
				. '.rnc-deact__actions .button-link{color:#6b7280;text-decoration:none}';
		}

		/**
		 * JS for the deactivation-feedback modal (inline, footer).
		 *
		 * @return string
		 */
		private function get_deactivation_js() {
			$reasons = array(
				'better_plugin'    => 'I found a better plugin',
				'missing_feature'  => 'It\'s missing a feature I need',
				'not_working'      => 'It stopped working',
				'temporary'        => 'It\'s a temporary deactivation',
				'no_longer_needed' => 'I no longer need it',
				'other'            => 'Other',
			);

			$feedback_url = preg_replace( '/\/content\/[^\/]+$/', '/feedback', $this->api_url );

			$li = '';
			foreach ( $reasons as $key => $label ) {
				$li .= '<li><label><input type="radio" name="rnc-reason" value="' . esc_attr( $key ) . '"> <span>' . esc_html( $label ) . '</span></label></li>';
			}

			$modal = '<div class="rnc-deact-ov" id="rnc-deact-' . esc_attr( $this->product ) . '">'
				. '<div class="rnc-deact" role="dialog" aria-modal="true">'
				. '<h2>Quick question before you go</h2>'
				. '<p class="rnc-deact__sub">Anonymous — no personal data is sent. This is optional.</p>'
				. '<ul class="rnc-deact__reasons">' . $li . '</ul>'
				. '<textarea rows="2" placeholder="Anything we could do better? (optional)"></textarea>'
				. '<div class="rnc-deact__actions">'
				. '<a href="#" class="button-link rnc-deact__skip">Skip &amp; deactivate</a>'
				. '<button type="button" class="button button-primary rnc-deact__submit">Submit &amp; deactivate</button>'
				. '</div></div></div>';

			return '(function(){'
				. 'var pf=' . wp_json_encode( $this->plugin_file ) . ';'
				. 'var ep=' . wp_json_encode( $this->product ) . ';'
				. 'var url=' . wp_json_encode( $feedback_url ) . ';'
				. 'var ver=' . wp_json_encode( $this->plugin_version ) . ';'
				. 'var row=document.querySelector(\'tr[data-plugin="\'+pf+\'"]\');'
				. 'if(!row)return;'
				. 'var link=row.querySelector(".deactivate a");'
				. 'if(!link)return;'
				. 'var wrap=document.createElement("div");wrap.innerHTML=' . wp_json_encode( $modal ) . ';'
				. 'var ov=wrap.firstChild;document.body.appendChild(ov);'
				. 'var target="";'
				. 'function go(){window.location.href=target;}'
				. 'function send(){'
				. 'var r=ov.querySelector(\'input[name="rnc-reason"]:checked\');'
				. 'var c=ov.querySelector("textarea").value||"";'
				. 'if(r&&typeof navigator.sendBeacon==="function"){'
				. 'var p=JSON.stringify({endpoint:ep,reason:r.value,comment:c,version:ver,site_url:window.location.origin});'
				. 'navigator.sendBeacon(url,new Blob([p],{type:"application/json"}));}'
				. '}'
				. 'link.addEventListener("click",function(e){e.preventDefault();target=link.href;ov.classList.add("open");});'
				. 'ov.addEventListener("click",function(e){if(e.target===ov){ov.classList.remove("open");}});'
				. 'ov.querySelector(".rnc-deact__skip").addEventListener("click",function(e){e.preventDefault();go();});'
				. 'ov.querySelector(".rnc-deact__submit").addEventListener("click",function(){send();setTimeout(go,150);});'
				. '})();';
		}

		/**
		 * Enqueue inline CSS for notice styling via WordPress APIs
		 *
		 * Registers a virtual style handle and attaches the notice CSS
		 * so it prints in the document head before notices render.
		 * Also registers the script handle used for per-notice JS.
		 */
		public function enqueue_notice_assets() {
			if ( ! current_user_can( $this->capability ) ) {
				return;
			}

			$contents = $this->get_non_dismissed_contents();

			if ( empty( $contents ) ) {
				return;
			}

			$handle = 'noticepilot-rnc-notice-' . $this->product;

			// Register and enqueue inline CSS.
			wp_register_style( $handle, false );
			wp_enqueue_style( $handle );
			wp_add_inline_style( $handle, $this->get_notice_css() );

			// Register script handle for per-notice inline JS (added in display_notices).
			wp_register_script( $handle, false, array(), self::SDK_VERSION, true );
		}

		/**
		 * Get the CSS string for notice wrapper styling
		 *
		 * @return string CSS rules.
		 */
		private function get_notice_css() {
			return
				/* Base — applies to every NoticePilot notice (styled or plain).
				   Only layout/behaviour here; it must NOT strip the border/bg so
				   the selected "Notice style" (WP notice-* classes) can show. */
				'.rnc-notice-wrapper{position:relative;}'
				. '.rnc-notice-wrapper img{max-width:100%!important;height:auto}'
				. '.rnc-notice-wrapper .notice-dismiss{position:absolute!important;top:0!important;right:1px!important;border:none!important;margin:0!important;padding:9px!important;background:none!important;color:#787c82!important;cursor:pointer!important}'
				. '.rnc-notice-wrapper .notice-dismiss:hover{color:#c92c2c!important}'
				. '.rnc-notice-wrapper .rnc-notice-snooze{display:inline-block;margin:0 0 8px;font-size:12px;color:#787c82;text-decoration:underline}'
				. '.rnc-notice-wrapper .rnc-notice-snooze:hover{color:#2271b1}'

				/* Styled variants — keep the WP notice chrome and pin the left-border
				   colour to the chosen style, so it stays correct across WP versions. */
				. '.rnc-notice-wrapper.notice{background:#fff;border-left-width:4px;box-shadow:0 1px 1px rgba(0,0,0,.04)}'

				/* "None" — strip every bit of chrome so only the author's HTML shows. */
				. '.rnc-notice-wrapper--plain{margin:10px 0!important;padding:0!important;background:transparent!important;border:0!important;box-shadow:none!important}';
		}

		/**
		 * Initialize cron schedule
		 */
		public function init_cron() {
			$hook = 'noticepilot_rnc_fetch_content_' . $this->product;

			if ( ! wp_next_scheduled( $hook ) ) {
				wp_schedule_event( time(), $this->schedule, $hook );
			}
		}

		/**
		 * Fetch content from remote API
		 */
		public function fetch_content() {
			if ( empty( $this->api_url ) ) {
				return;
			}

			$response = wp_remote_get(
				$this->api_url,
				array(
					'timeout'   => 15,
					'sslverify' => true,
				)
			);

			if ( is_wp_error( $response ) ) {
				$this->log( 'API Error: ' . $response->get_error_message(), 'error' );
				return;
			}

			$http_code = wp_remote_retrieve_response_code( $response );

			if ( 200 !== $http_code ) {
				$this->log( 'API returned HTTP ' . $http_code, 'error' );
				return;
			}

			$body = wp_remote_retrieve_body( $response );
			$data = json_decode( $body, true );

			if ( ! is_array( $data ) || ! isset( $data['success'] ) || ! $data['success'] ) {
				$this->log( 'Invalid API response', 'error' );
				return;
			}

			$contents = isset( $data['contents'] ) ? $data['contents'] : array();

			if ( empty( $contents ) ) {
				$this->clear_contents();
				return;
			}

			$this->store_contents( $contents );
		}

		/**
		 * Store contents in database
		 *
		 * @param array $contents Contents array.
		 */
		private function store_contents( $contents ) {
			$key     = $this->get_option_key( 'contents' );
			$new_ids = wp_list_pluck( $contents, 'id' );

			// Prune dismissed IDs that no longer exist on the hub.
			$dismissed_map = $this->get_dismissed_map();
			$cleaned       = array_intersect_key( $dismissed_map, array_flip( $new_ids ) );
			if ( count( $cleaned ) !== count( $dismissed_map ) ) {
				update_option( $this->get_option_key( 'dismissed_ids' ), $cleaned, false );
			}

			// Prune snoozed IDs the same way.
			$snoozed_map     = $this->get_snoozed_map();
			$snoozed_cleaned = array_intersect_key( $snoozed_map, array_flip( $new_ids ) );
			if ( count( $snoozed_cleaned ) !== count( $snoozed_map ) ) {
				update_option( $this->get_option_key( 'snoozed_ids' ), $snoozed_cleaned, false );
			}

			update_option( $key, $contents, false );
			update_option( $this->get_option_key( 'fetched_time' ), current_time( 'mysql' ), false );

			// Persist the API URL + consent requirement so track_goal() can send a
			// beacon (and honour consent) even on a request where init() hasn't run
			// and no live instance exists.
			if ( ! empty( $this->api_url ) ) {
				update_option( $this->get_option_key( 'api_url' ), $this->api_url, false );
			}
			update_option( $this->get_option_key( 'require_consent' ), $this->require_consent ? 1 : 0, false );
		}

		/**
		 * Clear all stored contents
		 */
		private function clear_contents() {
			delete_option( $this->get_option_key( 'contents' ) );
			delete_option( $this->get_option_key( 'fetched_time' ) );
			delete_option( $this->get_option_key( 'dismissed_ids' ) );
			delete_option( $this->get_option_key( 'snoozed_ids' ) );
		}

		/**
		 * Get non-dismissed, non-snoozed contents
		 *
		 * @return array
		 */
		private function get_non_dismissed_contents() {
			$contents      = get_option( $this->get_option_key( 'contents' ), array() );
			$dismissed_ids = $this->get_dismissed_ids();
			$snoozed_ids   = $this->get_active_snoozed_ids();
			$hidden_ids    = array_merge( $dismissed_ids, $snoozed_ids );
			$result        = array();

			if ( ! is_array( $contents ) ) {
				return $result;
			}

			foreach ( $contents as $content ) {
				if ( ! isset( $content['id'] ) ) {
					continue;
				}

				if ( ! in_array( $content['id'], $hidden_ids, true ) ) {
					$result[] = $content;
				}
			}

			return $result;
		}

		/**
		 * Get the dismissed campaigns map (id => dismissal timestamp)
		 *
		 * Converts the legacy plain-list format (pre-1.2.0) to the
		 * timestamped map on first read and persists the conversion.
		 *
		 * @return array Map of content ID => Unix timestamp of dismissal.
		 */
		private function get_dismissed_map() {
			$raw = get_option( $this->get_option_key( 'dismissed_ids' ), array() );

			if ( ! is_array( $raw ) ) {
				return array();
			}

			// Legacy format: numeric list of IDs — convert to id => now.
			if ( isset( $raw[0] ) ) {
				$map = array();
				foreach ( $raw as $id ) {
					if ( is_string( $id ) ) {
						$map[ $id ] = time();
					}
				}
				update_option( $this->get_option_key( 'dismissed_ids' ), $map, false );
				return $map;
			}

			return $raw;
		}

		/**
		 * Get IDs of campaigns whose dismissal is still in effect
		 *
		 * A dismissal expires after `dismiss_duration` seconds, at which
		 * point the notice may reappear. Duration 0 means dismissals are
		 * permanent.
		 *
		 * @return array List of currently dismissed content IDs.
		 */
		private function get_dismissed_ids() {
			$map = $this->get_dismissed_map();

			if ( 0 === $this->dismiss_duration ) {
				return array_keys( $map );
			}

			$active = array();
			foreach ( $map as $id => $timestamp ) {
				if ( (int) $timestamp + $this->dismiss_duration > time() ) {
					$active[] = $id;
				}
			}

			return $active;
		}

		/**
		 * Add a campaign ID to the dismissed map with FIFO cleanup
		 *
		 * Stores up to 20 dismissed campaign IDs. When the limit is exceeded,
		 * the oldest entries are removed to keep the option lightweight.
		 *
		 * @param string $content_id The campaign UUID to dismiss.
		 */
		private function add_dismissed_id( $content_id ) {
			$map = $this->get_dismissed_map();

			$map[ $content_id ] = time();

			// FIFO cleanup: keep only the 20 most recent dismissals.
			if ( count( $map ) > 20 ) {
				asort( $map );
				$map = array_slice( $map, -20, null, true );
			}

			update_option( $this->get_option_key( 'dismissed_ids' ), $map, false );
		}

		/**
		 * Get the snoozed campaigns map (id => reappear timestamp)
		 *
		 * @return array Map of content ID => Unix timestamp when it may reappear.
		 */
		private function get_snoozed_map() {
			$map = get_option( $this->get_option_key( 'snoozed_ids' ), array() );
			return is_array( $map ) ? $map : array();
		}

		/**
		 * Get IDs of campaigns currently snoozed ("Remind me later")
		 *
		 * @return array List of snoozed content IDs still within their snooze window.
		 */
		private function get_active_snoozed_ids() {
			$active = array();

			foreach ( $this->get_snoozed_map() as $id => $reappear_at ) {
				if ( (int) $reappear_at > time() ) {
					$active[] = $id;
				}
			}

			return $active;
		}

		/**
		 * Snooze a campaign for the configured snooze duration
		 *
		 * @param string $content_id The campaign UUID to snooze.
		 */
		private function add_snoozed_id( $content_id ) {
			$map = $this->get_snoozed_map();

			$map[ $content_id ] = time() + $this->snooze_duration;

			// FIFO cleanup, same policy as dismissals.
			if ( count( $map ) > 20 ) {
				asort( $map );
				$map = array_slice( $map, -20, null, true );
			}

			update_option( $this->get_option_key( 'snoozed_ids' ), $map, false );
		}

		/**
		 * Display admin notices
		 *
		 * CSS is already enqueued via enqueue_notice_assets().
		 * Per-notice JS is added via wp_add_inline_script() for footer output.
		 */
		public function display_notices() {
			if ( ! current_user_can( $this->capability ) ) {
				return;
			}

			$contents = $this->get_non_dismissed_contents();

			if ( empty( $contents ) ) {
				return;
			}

			$script_handle = 'noticepilot-rnc-notice-' . $this->product;
			// 'none' renders a bare, unstyled container (no WP notice box/padding);
			// the others map to WordPress core notice-* styles.
			$allowed_types = array( 'none', 'info', 'success', 'warning', 'error' );

			$screen_id = '';
			if ( function_exists( 'get_current_screen' ) ) {
				$screen    = get_current_screen();
				$screen_id = $screen ? $screen->id : '';
			}

			$shown = 0;

			foreach ( $contents as $content ) {
				// Frequency cap: never stack more than max_notices at once.
				if ( $shown >= $this->max_notices ) {
					break;
				}

				if ( ! isset( $content['id'] ) || ! isset( $content['content'] ) ) {
					continue;
				}

				// Evaluate targeting rules before display.
				if ( ! $this->should_display_content( $content ) ) {
					continue;
				}

				// Placement: "plugin screens only" campaigns render solely on
				// the screen IDs the integrating plugin registered.
				if ( isset( $content['placement'] ) && 'plugin_screens' === $content['placement'] ) {
					if ( empty( $this->screen_ids ) || ! in_array( $screen_id, $this->screen_ids, true ) ) {
						continue;
					}
				}

				$picked       = $this->pick_variant( $content );
				$content_id   = sanitize_key( $content['id'] );
				$variant_id   = $picked['variant_id'];
				$html_content = wp_kses( $picked['content'], $this->allowed_html );
				$notice_type  = isset( $content['notice_type'] ) && in_array( $content['notice_type'], $allowed_types, true ) ? $content['notice_type'] : 'info';
				$has_snooze   = ! empty( $content['snooze'] );
				$nonce        = wp_create_nonce( 'noticepilot_rnc_dismiss_' . $this->product . '_' . $content_id );
				$ajax_action  = 'noticepilot_rnc_dismiss_content_' . $this->product;

				// "none" → no WP notice-* box (and the wrapper already zeroes
				// padding/border/background); anything else → the core notice style.
				$wrapper_class = ( 'none' === $notice_type )
					? 'rnc-notice-wrapper rnc-notice-wrapper--plain'
					: 'notice notice-' . $notice_type . ' rnc-notice-wrapper';

				?>
				<div id="rnc-notice-<?php echo esc_attr( $this->product . '-' . $content_id ); ?>"
				     class="<?php echo esc_attr( $wrapper_class ); ?>"
				     data-product="<?php echo esc_attr( $this->product ); ?>"
				     data-content-id="<?php echo esc_attr( $content_id ); ?>"
				     data-nonce="<?php echo esc_attr( $nonce ); ?>"
				     data-action="<?php echo esc_attr( $ajax_action ); ?>">
					<div class="rnc-notice-content">
						<?php echo $html_content; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- Already filtered through wp_kses() above. ?>
					</div>
					<?php if ( $has_snooze ) : ?>
						<a href="#" class="rnc-notice-snooze">Remind me later</a>
					<?php endif; ?>
					<button type="button" class="notice-dismiss"><span class="screen-reader-text">Dismiss this notice.</span></button>
				</div>
				<?php

				$shown++;

				// Record (throttled) that this campaign+variant was shown, so a
				// later track_goal() call can attribute the conversion to it.
				$this->record_shown( $content_id, $variant_id );

				// Analytics beacons only fire when allowed: either the
				// integrating plugin doesn't require consent, or the user
				// granted it (wp.org guideline 7 — no tracking without opt-in).
				$analytics_on = $this->analytics_allowed() ? '1' : '';

				// Per-notice JS: analytics beacons + dismiss/snooze handlers (printed in footer).
				$notice_js = '(function(){'
					. 'var notice=document.getElementById("rnc-notice-' . esc_js( $this->product . '-' . $content_id ) . '");'
					. 'if(!notice)return;'
					. 'var apiUrl="' . esc_js( $this->api_url ) . '";'
					. 'var trackUrl=apiUrl.replace(/\/content\/[^\/]+$/,"/analytics/track");'
					. 'var endpoint="' . esc_js( $this->product ) . '";'
					. 'var cid="' . esc_js( $content_id ) . '";'
					. 'var vid="' . esc_js( $variant_id ) . '";'
					. 'var analyticsOn=' . ( $analytics_on ? 'true' : 'false' ) . ';'
					. 'function sendBeacon(t){if(analyticsOn&&typeof navigator.sendBeacon==="function"){'
					. 'var p=JSON.stringify({endpoint:endpoint,campaign_id:cid,variant_id:vid,event_type:t,site_url:window.location.origin});'
					. 'navigator.sendBeacon(trackUrl,new Blob([p],{type:"application/json"}));}}'
					. 'var impKey="np_imp_"+cid;'
					. 'if(typeof sessionStorage!=="undefined"){'
					. 'if(!sessionStorage.getItem(impKey)){sessionStorage.setItem(impKey,"1");sendBeacon("impression");}}'
					. 'else{sendBeacon("impression");}'
					. 'var ce=notice.querySelector(".rnc-notice-content");'
					. 'if(ce){ce.addEventListener("click",function(e){var a=e.target.closest("a");if(a){sendBeacon("click");}});}'
					. 'function hideViaAjax(snooze){'
					. 'var act=notice.getAttribute("data-action");'
					. 'var cid2=notice.getAttribute("data-content-id");'
					. 'var nc=notice.getAttribute("data-nonce");'
					. 'var x=new XMLHttpRequest();'
					. 'x.open("POST","' . esc_js( admin_url( 'admin-ajax.php' ) ) . '",true);'
					. 'x.setRequestHeader("Content-Type","application/x-www-form-urlencoded");'
					. 'x.onload=function(){if(x.status===200){notice.style.display="none";}};'
					. 'x.send("action="+encodeURIComponent(act)+"&content_id="+encodeURIComponent(cid2)+"&nonce="+encodeURIComponent(nc)+(snooze?"&snooze=1":""));}'
					. 'var cb=notice.querySelector(".notice-dismiss");'
					. 'if(cb){cb.addEventListener("click",function(e){e.preventDefault();sendBeacon("dismissal");hideViaAjax(false);});}'
					. 'var sn=notice.querySelector(".rnc-notice-snooze");'
					. 'if(sn){sn.addEventListener("click",function(e){e.preventDefault();hideViaAjax(true);});}'
					. '})();';

				wp_enqueue_script( $script_handle );
				wp_add_inline_script( $script_handle, $notice_js );
			}
		}

		/**
		 * Pick the A/B variant for this site (deterministic, stateless)
		 *
		 * When a campaign carries a `variants` array (authored via the Pro
		 * add-on on the hub), the site is assigned a stable bucket derived
		 * from its URL + the campaign ID. The same site always sees the same
		 * variant, with no cookies or extra options, and the install base
		 * splits according to the variant weights.
		 *
		 * @param array $content Campaign array from the hub.
		 * @return array { content: string, variant_id: string }
		 */
		private function pick_variant( $content ) {
			$fallback = array(
				'content'    => isset( $content['content'] ) ? $content['content'] : '',
				'variant_id' => '',
			);

			if ( empty( $content['variants'] ) || ! is_array( $content['variants'] ) ) {
				return $fallback;
			}

			$variants = array_values( $content['variants'] );
			$total    = 0;

			foreach ( $variants as $variant ) {
				$total += isset( $variant['weight'] ) ? max( 0, (int) $variant['weight'] ) : 0;
			}

			if ( $total <= 0 || empty( $variants ) ) {
				return $fallback;
			}

			// Stable bucket in [0, total): crc32 can be negative on 32-bit PHP.
			$bucket = crc32( md5( get_site_url() . $content['id'] ) ) % $total;
			if ( $bucket < 0 ) {
				$bucket += $total;
			}

			$cumulative = 0;
			foreach ( $variants as $variant ) {
				$cumulative += isset( $variant['weight'] ) ? max( 0, (int) $variant['weight'] ) : 0;
				if ( $bucket < $cumulative ) {
					return array(
						'content'    => isset( $variant['content'] ) ? $variant['content'] : $fallback['content'],
						'variant_id' => isset( $variant['id'] ) ? sanitize_key( $variant['id'] ) : '',
					);
				}
			}

			return $fallback;
		}

		/* =====================================================================
		   Conversion goals
		   ===================================================================== */

		/**
		 * Record (throttled) that a campaign+variant was shown to this site.
		 *
		 * Kept as a small map (campaign_id => [variant_id, ts]) so a later
		 * track_goal() call can attribute the conversion to the most recently
		 * shown campaign. Writes at most once per campaign per hour to avoid
		 * an option write on every admin page load.
		 *
		 * @param string $content_id Campaign UUID.
		 * @param string $variant_id Picked variant ID ('' for non-A/B).
		 */
		private function record_shown( $content_id, $variant_id ) {
			$key = $this->get_option_key( 'last_shown' );
			$map = get_option( $key, array() );
			if ( ! is_array( $map ) ) {
				$map = array();
			}

			// Throttle: skip if we already recorded this campaign within the hour.
			if ( isset( $map[ $content_id ]['ts'] ) && ( time() - (int) $map[ $content_id ]['ts'] ) < HOUR_IN_SECONDS ) {
				return;
			}

			$map[ $content_id ] = array(
				'variant_id' => (string) $variant_id,
				'ts'         => time(),
			);

			// Keep only the 10 most recently shown campaigns.
			if ( count( $map ) > 10 ) {
				uasort(
					$map,
					function ( $a, $b ) {
						return ( isset( $b['ts'] ) ? $b['ts'] : 0 ) <=> ( isset( $a['ts'] ) ? $a['ts'] : 0 );
					}
				);
				$map = array_slice( $map, 0, 10, true );
			}

			update_option( $key, $map, false );
		}

		/**
		 * Report a conversion goal, attributed to the most recently shown campaign.
		 *
		 * Call this from your plugin when a user completes a valuable action —
		 * e.g. activates a Pro license:
		 *
		 *   Noticepilot_Remote_Notice_Client::track_goal( 'my-product', 'upgraded_to_pro' );
		 *
		 * The goal is attributed (last-impression, within `window` seconds) to
		 * the campaign/variant this site most recently saw, so per-variant
		 * conversion rate (CVR) works in the hub's A/B analytics.
		 *
		 * @param string $product  Product slug.
		 * @param string $goal_key Short goal identifier (e.g. 'upgraded_to_pro').
		 * @param int    $window   Attribution window in seconds (default 30 days).
		 * @return bool True if a goal beacon was sent.
		 */
		public static function track_goal( $product, $goal_key, $window = null ) {
			$product = sanitize_key( $product );

			$instance = isset( self::$instances[ $product ] ) ? self::$instances[ $product ] : null;
			if ( ! $instance ) {
				// Build a lightweight instance so goals still fire when init()
				// hasn't run on this particular request. Consent requirement is
				// restored from the persisted flag so a goal never bypasses it.
				$instance = new self(
					$product,
					array(
						'api_url'         => get_option( 'noticepilot_rnc_' . $product . '_api_url', '' ),
						'require_consent' => (bool) get_option( 'noticepilot_rnc_' . $product . '_require_consent', false ),
					)
				);
			}

			return $instance->send_goal( $goal_key, $window );
		}

		/**
		 * Send the goal beacon for the last-shown campaign (instance helper).
		 *
		 * @param string   $goal_key Goal identifier.
		 * @param int|null $window   Attribution window in seconds.
		 * @return bool
		 */
		private function send_goal( $goal_key, $window = null ) {
			$goal_key = sanitize_key( $goal_key );
			if ( '' === $goal_key ) {
				return false;
			}

			// Respect the same consent gate as other beacons (wp.org guideline 7).
			if ( ! $this->analytics_allowed() ) {
				return false;
			}

			$api_url = $this->api_url ? $this->api_url : get_option( $this->get_option_key( 'api_url' ), '' );
			if ( empty( $api_url ) ) {
				return false;
			}

			$window = ( null === $window ) ? ( 30 * DAY_IN_SECONDS ) : absint( $window );

			$map = get_option( $this->get_option_key( 'last_shown' ), array() );
			if ( ! is_array( $map ) || empty( $map ) ) {
				return false;
			}

			// Pick the most recently shown campaign still within the window.
			$best_id = '';
			$best_ts = 0;
			$best_variant = '';
			foreach ( $map as $cid => $data ) {
				$ts = isset( $data['ts'] ) ? (int) $data['ts'] : 0;
				if ( $ts <= 0 || ( time() - $ts ) > $window ) {
					continue;
				}
				if ( $ts > $best_ts ) {
					$best_ts      = $ts;
					$best_id      = $cid;
					$best_variant = isset( $data['variant_id'] ) ? (string) $data['variant_id'] : '';
				}
			}

			if ( '' === $best_id ) {
				return false;
			}

			$track_url = preg_replace( '/\/content\/[^\/]+$/', '/analytics/track', $api_url );

			wp_remote_post(
				$track_url,
				array(
					'timeout'   => 8,
					'blocking'  => false,
					'sslverify' => true,
					'headers'   => array( 'Content-Type' => 'application/json' ),
					'body'      => wp_json_encode(
						array(
							'endpoint'    => $this->product,
							'campaign_id' => $best_id,
							'variant_id'  => $best_variant,
							'event_type'  => 'goal',
							'goal_key'    => $goal_key,
							'site_url'    => home_url(),
						)
					),
				)
			);

			return true;
		}

		/* =====================================================================
		   Smart triggers (usage-based display conditions)
		   ===================================================================== */

		/**
		 * Record a usage metric for milestone-based triggers.
		 *
		 * Call from your plugin as counts change, e.g.:
		 *   Noticepilot_Remote_Notice_Client::set_metric( 'my-product', 'forms_created', 120 );
		 *
		 * @param string $product Product slug.
		 * @param string $key     Metric key.
		 * @param int    $value   Current value.
		 */
		public static function set_metric( $product, $key, $value ) {
			$product = sanitize_key( $product );
			$key     = sanitize_key( $key );
			if ( '' === $product || '' === $key ) {
				return;
			}

			$option  = 'noticepilot_rnc_' . $product . '_metrics';
			$metrics = get_option( $option, array() );
			if ( ! is_array( $metrics ) ) {
				$metrics = array();
			}

			$metrics[ $key ] = (float) $value;
			update_option( $option, $metrics, true );
		}

		/**
		 * Ensure the first-seen timestamp exists (basis for install-age triggers).
		 */
		private function ensure_first_seen() {
			$key = $this->get_option_key( 'first_seen' );
			if ( false === get_option( $key, false ) ) {
				update_option( $key, time(), true );
			}
		}

		/**
		 * Evaluate a campaign's smart-trigger conditions (all must pass).
		 *
		 * Supported (each optional):
		 *   triggers.install_age = [ 'operator' => 'gte', 'days' => 7 ]
		 *   triggers.metric      = [ 'key' => 'forms_created', 'operator' => 'gte', 'value' => 100 ]
		 *
		 * @param array $content Campaign array.
		 * @return bool True when there are no triggers or all pass.
		 */
		private function evaluate_triggers( $content ) {
			if ( empty( $content['triggers'] ) || ! is_array( $content['triggers'] ) ) {
				return true;
			}

			$triggers = $content['triggers'];

			// Install-age condition.
			if ( ! empty( $triggers['install_age'] ) && is_array( $triggers['install_age'] ) ) {
				$days = isset( $triggers['install_age']['days'] ) ? (int) $triggers['install_age']['days'] : 0;
				$op   = isset( $triggers['install_age']['operator'] ) ? $triggers['install_age']['operator'] : 'gte';

				if ( $days > 0 ) {
					$first_seen = (int) get_option( $this->get_option_key( 'first_seen' ), time() );
					$age_days   = ( time() - $first_seen ) / DAY_IN_SECONDS;
					if ( ! $this->compare_numeric( $age_days, $op, $days ) ) {
						return false;
					}
				}
			}

			// Usage-metric condition.
			if ( ! empty( $triggers['metric'] ) && is_array( $triggers['metric'] ) ) {
				$metric_key = isset( $triggers['metric']['key'] ) ? sanitize_key( $triggers['metric']['key'] ) : '';
				$op         = isset( $triggers['metric']['operator'] ) ? $triggers['metric']['operator'] : 'gte';
				$threshold  = isset( $triggers['metric']['value'] ) ? (float) $triggers['metric']['value'] : 0;

				if ( '' !== $metric_key ) {
					$metrics = get_option( $this->get_option_key( 'metrics' ), array() );
					$current = ( is_array( $metrics ) && isset( $metrics[ $metric_key ] ) ) ? (float) $metrics[ $metric_key ] : 0;
					if ( ! $this->compare_numeric( $current, $op, $threshold ) ) {
						return false;
					}
				}
			}

			return true;
		}

		/**
		 * Numeric comparison with a named operator (lt/lte/eq/gte/gt).
		 *
		 * @param float  $left     Left operand.
		 * @param string $operator Operator key.
		 * @param float  $right    Right operand.
		 * @return bool
		 */
		private function compare_numeric( $left, $operator, $right ) {
			switch ( $operator ) {
				case 'lt':
					return $left < $right;
				case 'lte':
					return $left <= $right;
				case 'eq':
					return $left === $right;
				case 'gt':
					return $left > $right;
				case 'gte':
				default:
					return $left >= $right;
			}
		}

		/**
		 * AJAX handler for dismissing content
		 */
		public function ajax_dismiss_content() {
			$content_id = isset( $_POST['content_id'] ) ? sanitize_text_field( wp_unslash( $_POST['content_id'] ) ) : '';
			$nonce      = isset( $_POST['nonce'] ) ? sanitize_text_field( wp_unslash( $_POST['nonce'] ) ) : '';
			$is_snooze  = ! empty( $_POST['snooze'] );

			if ( ! wp_verify_nonce( $nonce, 'noticepilot_rnc_dismiss_' . $this->product . '_' . $content_id ) ) {
				wp_send_json_error( array( 'message' => 'Invalid nonce' ) );
			}

			if ( ! current_user_can( $this->capability ) ) {
				wp_send_json_error( array( 'message' => 'Unauthorized' ) );
			}

			if ( empty( $content_id ) ) {
				wp_send_json_error( array( 'message' => 'Invalid content ID' ) );
			}

			if ( $is_snooze ) {
				$this->add_snoozed_id( $content_id );
				wp_send_json_success( array( 'message' => 'Content snoozed' ) );
			}

			$this->add_dismissed_id( $content_id );

			wp_send_json_success( array( 'message' => 'Content dismissed' ) );
		}

		/* -----------------------------------------------------------------
		   Analytics consent (WordPress.org guideline 7)
		   ----------------------------------------------------------------- */

		/**
		 * Whether analytics beacons may fire on this site
		 *
		 * True when the integrating plugin doesn't require consent, or when
		 * consent was recorded via grant_consent().
		 *
		 * @return bool
		 */
		private function analytics_allowed() {
			return ! $this->require_consent || self::has_consent( $this->product );
		}

		/**
		 * Record analytics consent for a product
		 *
		 * Call this from your own opt-in flow (e.g. a settings checkbox or
		 * an activation opt-in screen). Until consent is granted, a client
		 * initialized with `require_consent => true` sends no beacons.
		 *
		 * @param string $product Product identifier.
		 */
		public static function grant_consent( $product ) {
			update_option( 'noticepilot_rnc_' . sanitize_key( $product ) . '_consent', 1, false );
		}

		/**
		 * Withdraw analytics consent for a product
		 *
		 * @param string $product Product identifier.
		 */
		public static function revoke_consent( $product ) {
			delete_option( 'noticepilot_rnc_' . sanitize_key( $product ) . '_consent' );
		}

		/**
		 * Check whether analytics consent was granted for a product
		 *
		 * @param string $product Product identifier.
		 * @return bool
		 */
		public static function has_consent( $product ) {
			return (bool) get_option( 'noticepilot_rnc_' . sanitize_key( $product ) . '_consent', false );
		}

		/**
		 * Get option key with product prefix
		 *
		 * @param string $key Key name.
		 * @return string
		 */
		private function get_option_key( $key ) {
			return 'noticepilot_rnc_' . $this->product . '_' . $key;
		}

		/**
		 * Log activity (only in debug mode)
		 *
		 * @param string $message Message.
		 * @param string $level   Log level.
		 */
		private function log( $message, $level = 'info' ) {
			if ( defined( 'WP_DEBUG' ) && WP_DEBUG ) {
				// phpcs:ignore WordPress.PHP.DevelopmentFunctions.error_log_error_log
				error_log( '[Remote Notice Client - ' . strtoupper( $level ) . '] [' . $this->product . '] ' . $message );
			}
		}

		/**
		 * Evaluate targeting rules for a content item
		 *
		 * Returns true if the content should be displayed, false if it should be hidden.
		 * Missing targeting data defaults to showing the content (backward compatible).
		 *
		 * @param array $content Content array with optional 'targeting' key.
		 * @return bool
		 */
		private function should_display_content( $content ) {
			// Smart triggers (Pro) — install-age / usage-metric gates. Evaluated
			// independently of targeting; a campaign whose triggers aren't met
			// stays cached and appears automatically once conditions are satisfied.
			if ( ! $this->evaluate_triggers( $content ) ) {
				return false;
			}

			// No targeting data — show to everyone (backward compat)
			if ( empty( $content['targeting'] ) || ! is_array( $content['targeting'] ) ) {
				return true;
			}

			$targeting = $content['targeting'];

			// 1. Pro users check
			if ( ! empty( $targeting['pro_users'] ) && 'all' !== $targeting['pro_users'] ) {
				if ( 'free_only' === $targeting['pro_users'] && $this->is_pro ) {
					return false;
				}
				if ( 'pro_only' === $targeting['pro_users'] && ! $this->is_pro ) {
					return false;
				}
			}

			// 2. Plugin version check
			if ( ! empty( $targeting['plugin_version'] ) && is_array( $targeting['plugin_version'] ) ) {
				$operator = isset( $targeting['plugin_version']['operator'] ) ? $targeting['plugin_version']['operator'] : '';
				$version  = isset( $targeting['plugin_version']['version'] ) ? $targeting['plugin_version']['version'] : '';

				if ( ! empty( $operator ) && ! empty( $version ) && ! empty( $this->plugin_version ) ) {
					$op_map = array(
						'lt'  => '<',
						'lte' => '<=',
						'eq'  => '==',
						'gte' => '>=',
						'gt'  => '>',
					);

					$php_op = isset( $op_map[ $operator ] ) ? $op_map[ $operator ] : '';
					if ( ! empty( $php_op ) && ! version_compare( $this->plugin_version, $version, $php_op ) ) {
						return false;
					}
				}
			}

			// 3. User roles check
			if ( ! empty( $targeting['user_roles'] ) && is_array( $targeting['user_roles'] ) ) {
				$current_user = wp_get_current_user();
				if ( $current_user && ! empty( $current_user->roles ) ) {
					$intersect = array_intersect( $current_user->roles, $targeting['user_roles'] );
					if ( empty( $intersect ) ) {
						return false;
					}
				}
			}

			return true;
		}

		/**
		 * Manually trigger fetch (useful for testing)
		 *
		 * @param string $product Product identifier.
		 * @return bool True if triggered, false if instance doesn't exist.
		 */
		public static function trigger_fetch( $product ) {
			$product = sanitize_key( $product );
			
			if ( ! isset( self::$instances[ $product ] ) ) {
				return false;
			}
			
			self::$instances[ $product ]->fetch_content();
			return true;
		}

		/**
		 * Clear all data for a product
		 *
		 * @param string $product Product identifier.
		 * @return bool True if cleared, false if instance doesn't exist.
		 */
		public static function clear_all( $product ) {
			$product = sanitize_key( $product );
			
			if ( ! isset( self::$instances[ $product ] ) ) {
				// Still try to clear data even if instance doesn't exist
				$temp_instance = new self( $product, array() );
				$temp_instance->clear_contents();
				return true;
			}
			
			self::$instances[ $product ]->clear_contents();
			return true;
		}

		/**
		 * Disable the notice client for a product
		 * 
		 * Use this when Pro version is active to stop showing notices
		 * and unschedule the cron job.
		 *
		 * @param string $product Product identifier.
		 */
		public static function disable( $product ) {
			$product = sanitize_key( $product );
			
			// Mark as disabled
			update_option( 'noticepilot_rnc_' . $product . '_disabled', true, false );

			// Unschedule cron
			$hook      = 'noticepilot_rnc_fetch_content_' . $product;
			$timestamp = wp_next_scheduled( $hook );
			if ( $timestamp ) {
				wp_unschedule_event( $timestamp, $hook );
			}
			
			// Clear all stored data
			if ( isset( self::$instances[ $product ] ) ) {
				self::$instances[ $product ]->clear_contents();
				unset( self::$instances[ $product ] );
			} else {
				// Clear data even if instance doesn't exist
				$temp_instance = new self( $product, array() );
				$temp_instance->clear_contents();
			}
		}

		/**
		 * Re-enable the notice client for a product
		 *
		 * @param string $product Product identifier.
		 */
		public static function enable( $product ) {
			$product = sanitize_key( $product );
			delete_option( 'noticepilot_rnc_' . $product . '_disabled' );
		}

		/**
		 * Check if the notice client is disabled for a product
		 *
		 * @param string $product Product identifier.
		 * @return bool
		 */
		public static function is_disabled( $product ) {
			$product = sanitize_key( $product );
			return (bool) get_option( 'noticepilot_rnc_' . $product . '_disabled', false );
		}
	}
}