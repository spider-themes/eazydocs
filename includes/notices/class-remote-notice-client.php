<?php
/**
 * Remote Notice Client SDK
 *
 * A reusable SDK for integrating remote HTML notices from the NoticePilot API.
 * Bundle this file with your plugin to enable remote admin notices.
 *
 * @package Noticepilot_Remote_Notice_Client
 * @version 1.1.1
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
		const SDK_VERSION = '1.1.1';

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
		 * Dismiss duration in seconds (default: 1 week)
		 *
		 * @var int
		 */
		private $dismiss_duration;

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
			$this->plugin_version   = isset( $config['plugin_version'] ) ? sanitize_text_field( $config['plugin_version'] ) : '';
			$this->is_pro           = isset( $config['is_pro'] ) ? (bool) $config['is_pro'] : false;
			
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
			return '.rnc-notice-wrapper{margin:20px 20px 20px 0!important;padding:0!important;background:transparent!important;border:0!important;position:relative;box-shadow:none}'
				. '.rnc-notice-wrapper .notice-dismiss{position:absolute!important;top:0!important;right:1px!important;border:none!important;margin:0!important;padding:9px!important;background:none!important;color:#787c82!important;cursor:pointer!important}'
				. '.rnc-notice-wrapper .notice-dismiss:hover{color:#c92c2c!important}'
				. '.rnc-notice-wrapper img{max-width:100%!important}';
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
			$dismissed_ids = $this->get_dismissed_ids();
			$cleaned       = array_values( array_intersect( $dismissed_ids, $new_ids ) );
			if ( count( $cleaned ) !== count( $dismissed_ids ) ) {
				update_option( $this->get_option_key( 'dismissed_ids' ), $cleaned, false );
			}

			update_option( $key, $contents, false );
			update_option( $this->get_option_key( 'fetched_time' ), current_time( 'mysql' ), false );
		}

		/**
		 * Clear all stored contents
		 */
		private function clear_contents() {
			delete_option( $this->get_option_key( 'contents' ) );
			delete_option( $this->get_option_key( 'fetched_time' ) );
			delete_option( $this->get_option_key( 'dismissed_ids' ) );
		}

		/**
		 * Get non-dismissed contents
		 *
		 * @return array
		 */
		private function get_non_dismissed_contents() {
			$contents      = get_option( $this->get_option_key( 'contents' ), array() );
			$dismissed_ids = $this->get_dismissed_ids();
			$result        = array();

			if ( ! is_array( $contents ) ) {
				return $result;
			}

			foreach ( $contents as $content ) {
				if ( ! isset( $content['id'] ) ) {
					continue;
				}

				if ( ! in_array( $content['id'], $dismissed_ids, true ) ) {
					$result[] = $content;
				}
			}

			return $result;
		}

		/**
		 * Get the dismissed campaign IDs array
		 *
		 * @return array List of dismissed content IDs.
		 */
		private function get_dismissed_ids() {
			$ids = get_option( $this->get_option_key( 'dismissed_ids' ), array() );
			return is_array( $ids ) ? $ids : array();
		}

		/**
		 * Add a campaign ID to the dismissed list with FIFO cleanup
		 *
		 * Stores up to 20 dismissed campaign IDs. When the limit is exceeded,
		 * the oldest entries are removed to keep the option lightweight.
		 *
		 * @param string $content_id The campaign UUID to dismiss.
		 */
		private function add_dismissed_id( $content_id ) {
			$ids = $this->get_dismissed_ids();

			// Already dismissed — nothing to do.
			if ( in_array( $content_id, $ids, true ) ) {
				return;
			}

			$ids[] = $content_id;

			// FIFO cleanup: keep only the 20 most recent dismissed IDs.
			if ( count( $ids ) > 20 ) {
				$ids = array_slice( $ids, -20 );
			}

			update_option( $this->get_option_key( 'dismissed_ids' ), $ids, false );
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

			foreach ( $contents as $content ) {
				if ( ! isset( $content['id'] ) || ! isset( $content['content'] ) ) {
					continue;
				}

				// Evaluate targeting rules before display.
				if ( ! $this->should_display_content( $content ) ) {
					continue;
				}

				$content_id   = sanitize_key( $content['id'] );
				$html_content = wp_kses( $content['content'], $this->allowed_html );
				$nonce        = wp_create_nonce( 'noticepilot_rnc_dismiss_' . $this->product . '_' . $content_id );
				$ajax_action  = 'noticepilot_rnc_dismiss_content_' . $this->product;

				?>
				<div id="rnc-notice-<?php echo esc_attr( $this->product . '-' . $content_id ); ?>"
				     class="notice notice-info rnc-notice-wrapper"
				     data-product="<?php echo esc_attr( $this->product ); ?>"
				     data-content-id="<?php echo esc_attr( $content_id ); ?>"
				     data-nonce="<?php echo esc_attr( $nonce ); ?>"
				     data-action="<?php echo esc_attr( $ajax_action ); ?>">
					<div class="rnc-notice-content">
						<?php echo $html_content; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- Already filtered through wp_kses() above. ?>
					</div>
					<button type="button" class="notice-dismiss"><span class="screen-reader-text">Dismiss this notice.</span></button>
				</div>
				<?php

				// Per-notice JS: analytics beacons + dismiss handler (printed in footer).
				$notice_js = '(function(){'
					. 'var notice=document.getElementById("rnc-notice-' . esc_js( $this->product . '-' . $content_id ) . '");'
					. 'if(!notice)return;'
					. 'var apiUrl="' . esc_js( $this->api_url ) . '";'
					. 'var trackUrl=apiUrl.replace(/\/content\/[^\/]+$/,"/analytics/track");'
					. 'var endpoint="' . esc_js( $this->product ) . '";'
					. 'var cid="' . esc_js( $content_id ) . '";'
					. 'function sendBeacon(t){if(typeof navigator.sendBeacon==="function"){'
					. 'var p=JSON.stringify({endpoint:endpoint,campaign_id:cid,event_type:t,site_url:window.location.origin});'
					. 'navigator.sendBeacon(trackUrl,new Blob([p],{type:"application/json"}));}}'
					. 'sendBeacon("impression");'
					. 'var ce=notice.querySelector(".rnc-notice-content");'
					. 'if(ce){ce.addEventListener("click",function(e){var a=e.target.closest("a");if(a){sendBeacon("click");}});}'
					. 'var cb=notice.querySelector(".notice-dismiss");'
					. 'if(cb){cb.addEventListener("click",function(e){'
					. 'e.preventDefault();sendBeacon("dismissal");'
					. 'var act=notice.getAttribute("data-action");'
					. 'var cid2=notice.getAttribute("data-content-id");'
					. 'var nc=notice.getAttribute("data-nonce");'
					. 'var x=new XMLHttpRequest();'
					. 'x.open("POST","' . esc_js( admin_url( 'admin-ajax.php' ) ) . '",true);'
					. 'x.setRequestHeader("Content-Type","application/x-www-form-urlencoded");'
					. 'x.onload=function(){if(x.status===200){notice.style.display="none";}};'
					. 'x.send("action="+encodeURIComponent(act)+"&content_id="+encodeURIComponent(cid2)+"&nonce="+encodeURIComponent(nc));'
					. '});}'
					. '})();';

				wp_enqueue_script( $script_handle );
				wp_add_inline_script( $script_handle, $notice_js );
			}
		}

		/**
		 * AJAX handler for dismissing content
		 */
		public function ajax_dismiss_content() {
			$content_id = isset( $_POST['content_id'] ) ? sanitize_text_field( wp_unslash( $_POST['content_id'] ) ) : '';
			$nonce      = isset( $_POST['nonce'] ) ? sanitize_text_field( wp_unslash( $_POST['nonce'] ) ) : '';

			if ( ! wp_verify_nonce( $nonce, 'noticepilot_rnc_dismiss_' . $this->product . '_' . $content_id ) ) {
				wp_send_json_error( array( 'message' => 'Invalid nonce' ) );
			}

			if ( ! current_user_can( $this->capability ) ) {
				wp_send_json_error( array( 'message' => 'Unauthorized' ) );
			}

			if ( empty( $content_id ) ) {
				wp_send_json_error( array( 'message' => 'Invalid content ID' ) );
			}

			$this->add_dismissed_id( $content_id );

			wp_send_json_success( array( 'message' => 'Content dismissed' ) );
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