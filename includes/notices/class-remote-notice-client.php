<?php
/**
 * Remote Notice Client SDK
 *
 * A reusable SDK for integrating remote HTML notices from the HTML Notice Widget API.
 * Bundle this file with your plugin to enable remote admin notices.
 *
 * @package Remote_Notice_Client
 * @version 1.1.1
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! class_exists( 'Remote_Notice_Client' ) ) {

	/**
	 * Remote Notice Client Class
	 */
	class Remote_Notice_Client {

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
		 * @return Remote_Notice_Client|false Instance or false if disabled.
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
			);
		}

		/**
		 * Setup WordPress hooks
		 */
		private function setup_hooks() {
			// Cron.
			add_action( 'admin_init', array( $this, 'init_cron' ) );
			add_action( 'rnc_fetch_content_' . $this->product, array( $this, 'fetch_content' ) );

			// Display notices.
			add_action( 'admin_notices', array( $this, 'display_notices' ) );

			// AJAX handlers.
			add_action( 'wp_ajax_rnc_dismiss_content_' . $this->product, array( $this, 'ajax_dismiss_content' ) );
		}

		/**
		 * Initialize cron schedule
		 */
		public function init_cron() {
			$hook = 'rnc_fetch_content_' . $this->product;

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
				$this->log( 'No contents - cleared stored notices' );
				return;
			}

			$this->store_contents( $contents );
			$this->log( 'Fetched ' . count( $contents ) . ' contents' );
		}

		/**
		 * Store contents in database
		 *
		 * @param array $contents Contents array.
		 */
		private function store_contents( $contents ) {
			$key          = $this->get_option_key( 'contents' );
			$old_contents = get_option( $key, array() );
			$old_ids      = wp_list_pluck( $old_contents, 'id' );
			$new_ids      = wp_list_pluck( $contents, 'id' );

			// Clean up stale dismiss keys.
			$removed_ids = array_diff( $old_ids, $new_ids );
			foreach ( $removed_ids as $removed_id ) {
				delete_option( $this->get_option_key( 'dismissed_' . $removed_id ) );
			}

			update_option( $key, $contents, false );
			update_option( $this->get_option_key( 'fetched_time' ), current_time( 'mysql' ), false );
		}

		/**
		 * Clear all stored contents
		 */
		private function clear_contents() {
			$contents = get_option( $this->get_option_key( 'contents' ), array() );

			// Clean up all dismiss keys.
			if ( is_array( $contents ) ) {
				foreach ( $contents as $content ) {
					if ( isset( $content['id'] ) ) {
						delete_option( $this->get_option_key( 'dismissed_' . $content['id'] ) );
					}
				}
			}

			delete_option( $this->get_option_key( 'contents' ) );
			delete_option( $this->get_option_key( 'fetched_time' ) );
			delete_option( $this->get_option_key( 'temp_dismissed' ) );
		}

		/**
		 * Get non-dismissed contents
		 *
		 * @return array
		 */
		private function get_non_dismissed_contents() {
			$contents = get_option( $this->get_option_key( 'contents' ), array() );
			$result   = array();

			if ( ! is_array( $contents ) ) {
				return $result;
			}

			foreach ( $contents as $content ) {
				if ( ! isset( $content['id'] ) ) {
					continue;
				}

				$dismissed = get_option( $this->get_option_key( 'dismissed_' . $content['id'] ), false );
				if ( ! $dismissed ) {
					$result[] = $content;
				}
			}

			return $result;
		}

		/**
		 * Check if temporarily dismissed
		 *
		 * @return bool
		 */
		private function is_temporarily_dismissed() {
			$dismiss_time = get_option( $this->get_option_key( 'temp_dismissed' ), 0 );

			if ( empty( $dismiss_time ) ) {
				return false;
			}

			return ( current_time( 'timestamp' ) - intval( $dismiss_time ) ) < $this->dismiss_duration;
		}

		/**
		 * Display admin notices
		 */
		public function display_notices() {
			if ( ! current_user_can( $this->capability ) ) {
				return;
			}

			if ( $this->is_temporarily_dismissed() ) {
				return;
			}

			$contents = $this->get_non_dismissed_contents();

			if ( empty( $contents ) ) {
				return;
			}

			foreach ( $contents as $content ) {
				if ( ! isset( $content['id'] ) || ! isset( $content['content'] ) ) {
					continue;
				}

				$content_id   = sanitize_key( $content['id'] );
				$html_content = wp_kses( $content['content'], $this->allowed_html );
				$nonce        = wp_create_nonce( 'rnc_dismiss_' . $this->product . '_' . $content_id );
				$ajax_action  = 'rnc_dismiss_content_' . $this->product;

				?>
				<div id="rnc-notice-<?php echo esc_attr( $this->product . '-' . $content_id ); ?>" 
				     class="notice notice-info rnc-notice-wrapper"
				     data-product="<?php echo esc_attr( $this->product ); ?>"
				     data-content-id="<?php echo esc_attr( $content_id ); ?>"
				     data-nonce="<?php echo esc_attr( $nonce ); ?>"
				     data-action="<?php echo esc_attr( $ajax_action ); ?>">
					<div class="rnc-notice-content">
						<?php echo $html_content; // Already sanitized with wp_kses above ?>
					</div>
					<button type="button" class="notice-dismiss"><span class="screen-reader-text">Dismiss this notice.</span></button>
				</div>

				<style>
					#rnc-notice-<?php echo esc_attr( $this->product . '-' . $content_id ); ?> {
						margin: 20px 0 !important;
						padding: 0 !important;
						background: transparent !important;
						border: 0 !important;
						position: relative;
						box-shadow: none;
					}
					#rnc-notice-<?php echo esc_attr( $this->product . '-' . $content_id ); ?> .notice-dismiss {
						position: absolute !important;
						top: 0 !important;
						right: 1px !important;
						border: none !important;
						margin: 0 !important;
						padding: 9px !important;
						background: none !important;
						color: #787c82 !important;
						cursor: pointer !important;
					}
					#rnc-notice-<?php echo esc_attr( $this->product . '-' . $content_id ); ?> .notice-dismiss:hover {
						color: #c92c2c !important;
					}
				</style>

				<script>
					(function() {
						var notice = document.getElementById('rnc-notice-<?php echo esc_js( $this->product . '-' . $content_id ); ?>');
						if (!notice) return;

						var closeBtn = notice.querySelector('.notice-dismiss');
						if (closeBtn) {
							closeBtn.addEventListener('click', function(e) {
								e.preventDefault();
								
								var action = notice.getAttribute('data-action');
								var contentId = notice.getAttribute('data-content-id');
								var nonce = notice.getAttribute('data-nonce');
								
								var xhr = new XMLHttpRequest();
								xhr.open('POST', '<?php echo esc_js( admin_url( 'admin-ajax.php' ) ); ?>', true);
								xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
								xhr.onload = function() {
									if (xhr.status === 200) {
										notice.style.display = 'none';
									}
								};
								var data = 'action=' + encodeURIComponent(action) +
								           '&content_id=' + encodeURIComponent(contentId) +
								           '&nonce=' + encodeURIComponent(nonce);
								xhr.send(data);
							});
						}
					})();
				</script>
				<?php
			}
		}

		/**
		 * AJAX handler for dismissing content
		 */
		public function ajax_dismiss_content() {
			$content_id = isset( $_POST['content_id'] ) ? sanitize_text_field( wp_unslash( $_POST['content_id'] ) ) : '';
			$nonce      = isset( $_POST['nonce'] ) ? sanitize_text_field( wp_unslash( $_POST['nonce'] ) ) : '';

			if ( ! wp_verify_nonce( $nonce, 'rnc_dismiss_' . $this->product . '_' . $content_id ) ) {
				wp_send_json_error( array( 'message' => 'Invalid nonce' ) );
			}

			if ( ! current_user_can( $this->capability ) ) {
				wp_send_json_error( array( 'message' => 'Unauthorized' ) );
			}

			if ( empty( $content_id ) ) {
				wp_send_json_error( array( 'message' => 'Invalid content ID' ) );
			}

			update_option( $this->get_option_key( 'dismissed_' . $content_id ), true, false );

			wp_send_json_success( array( 'message' => 'Content dismissed' ) );
		}

		/**
		 * Get option key with product prefix
		 *
		 * @param string $key Key name.
		 * @return string
		 */
		private function get_option_key( $key ) {
			return 'rnc_' . $this->product . '_' . $key;
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
			update_option( 'rnc_' . $product . '_disabled', true, false );
			
			// Unschedule cron
			$hook      = 'rnc_fetch_content_' . $product;
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
			delete_option( 'rnc_' . $product . '_disabled' );
		}

		/**
		 * Check if the notice client is disabled for a product
		 *
		 * @param string $product Product identifier.
		 * @return bool
		 */
		public static function is_disabled( $product ) {
			$product = sanitize_key( $product );
			return (bool) get_option( 'rnc_' . $product . '_disabled', false );
		}
	}
}