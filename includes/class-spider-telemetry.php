<?php
/**
 * Spider Themes Telemetry & Insights Client SDK.
 *
 * Lightweight, non-intrusive, WordPress.org & GDPR compliant usage tracking and deactivation feedback.
 *
 * @version 1.0.0
 * @author  Spider Themes <support@spiderthemes.net>
 * @license GPL-2.0+
 */

namespace SpiderThemes;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! class_exists( 'SpiderThemes\\Telemetry' ) ) {

	class Telemetry {

		/**
		 * Default endpoint URL.
		 */
		const DEFAULT_ENDPOINT = 'https://telemetry.spiderthemes.net';

		/**
		 * SDK Version.
		 */
		const VERSION = '1.0.0';

		/**
		 * Config array.
		 *
		 * @var array
		 */
		private $config = [];

		/**
		 * Active instances.
		 *
		 * @var array<string, Telemetry>
		 */
		private static $instances = [];

		/**
		 * Slugs that already sent an activation ping during this request.
		 *
		 * @var array<string, bool>
		 */
		private static $activation_reported = [];

		/**
		 * Initialize a plugin tracker.
		 *
		 * @param array $config Configuration options.
		 * @return Telemetry
		 */
		public static function init( array $config ) {
			$slug = sanitize_key( $config['plugin_slug'] ?? '' );
			if ( empty( $slug ) ) {
				return null;
			}

			if ( ! isset( self::$instances[ $slug ] ) ) {
				self::$instances[ $slug ] = new self( $config );
			}

			return self::$instances[ $slug ];
		}

		/**
		 * Constructor.
		 *
		 * @param array $config
		 */
		private function __construct( array $config ) {
			$this->config = wp_parse_args( $config, [
				'plugin_slug'     => '',
				'plugin_name'     => '',
				'plugin_version'  => '1.0.0',
				'plugin_file'     => '',
				'endpoint'        => self::DEFAULT_ENDPOINT,
				'optin_title'     => '',
				'optin_message'   => '',
				'privacy_url'      => 'https://spiderthemes.net/privacy-policy/',
				'consent_callback' => null,
				// When true, an opt-in that came from the consent callback is void
				// once that callback returns false. An explicit user opt-in stays.
				'consent_follows_callback' => false,
				'custom_metrics'   => null,
			] );

			$this->hooks();
		}

		/**
		 * Register WordPress hooks.
		 */
		private function hooks() {
			$slug = $this->config['plugin_slug'];

			// Admin opt-in notice
			add_action( 'admin_notices', [ $this, 'render_optin_notice' ] );
			add_action( 'wp_ajax_spiderthemes_telemetry_optin_' . $slug, [ $this, 'ajax_handle_optin' ] );

			// Weekly heartbeat cron
			$cron_hook = 'spiderthemes_telemetry_heartbeat_' . $slug;
			add_action( $cron_hook, [ $this, 'send_heartbeat' ] );

			// Schedule cron if consented
			if ( $this->has_consent() && ! wp_next_scheduled( $cron_hook ) ) {
				wp_schedule_event( time() + HOUR_IN_SECONDS, 'weekly', $cron_hook );
			}

			// Deactivation survey modal in plugins page
			add_action( 'admin_footer', [ $this, 'render_deactivation_modal' ] );
			add_action( 'wp_ajax_spiderthemes_telemetry_deactivate_' . $slug, [ $this, 'ajax_handle_deactivate' ] );
		}

		/**
		 * Track a custom event.
		 *
		 * @param string $slug
		 * @param string $event_type
		 * @param array  $extra_metrics
		 * @return bool
		 */
		public static function track_event( $slug, $event_type, array $extra_metrics = [] ) {
			$slug     = sanitize_key( $slug );
			$instance = self::$instances[ $slug ] ?? null;
			if ( ! $instance || ! $instance->has_consent() ) {
				return false;
			}
			// Plugin activation and a fresh opt-in can happen in the same request.
			if ( 'activate' === $event_type && ! empty( self::$activation_reported[ $slug ] ) ) {
				return true;
			}
			if ( 'activate' === $event_type ) {
				self::$activation_reported[ $slug ] = true;
			}
			$instance->send_track( $event_type, $extra_metrics );
			return true;
		}

		/**
		 * Get instance by slug.
		 *
		 * @param string $slug
		 * @return Telemetry|null
		 */
		public static function get_instance( $slug ) {
			return self::$instances[ $slug ] ?? null;
		}

		/**
		 * Grant consent programmatically.
		 *
		 * @param string $slug   Plugin slug.
		 * @param string $source `user` for the Share usage switch or the opt-in banner.
		 *                       `freemius` when tracking is allowed and this only mirrors that choice.
		 * @return void
		 */
		public static function grant_consent( $slug, $source = 'user' ) {
			$slug   = sanitize_key( $slug );
			$source = ( 'freemius' === $source ) ? 'freemius' : 'user';
			if ( get_option( 'spiderthemes_telemetry_' . $slug . '_optout', false ) ) {
				return;
			}
			$optin_key  = 'spiderthemes_telemetry_' . $slug . '_optin';
			$source_key = 'spiderthemes_telemetry_' . $slug . '_optin_source';
			$already    = (bool) get_option( $optin_key, false );

			// admin_init calls this on every wp-admin load once Freemius allows tracking.
			// Only the transition into opt-in is an activation. Later calls keep the weekly heartbeat scheduled.
			// A Freemius sync must not downgrade an explicit Share usage opt-in.
			if ( ! $already ) {
				update_option( $optin_key, 1, false );
				update_option( $source_key, $source, false );
				update_option( 'spiderthemes_telemetry_' . $slug . '_decided', time(), false );
			} elseif ( 'user' === $source ) {
				update_option( $source_key, 'user', false );
			}

			$instance = self::$instances[ $slug ] ?? null;
			if ( ! $instance || ! $instance->has_consent() ) {
				return;
			}

			$cron_hook = 'spiderthemes_telemetry_heartbeat_' . $slug;
			if ( ! wp_next_scheduled( $cron_hook ) ) {
				wp_schedule_event( time() + HOUR_IN_SECONDS, 'weekly', $cron_hook );
			}

			if ( $already || ! empty( self::$activation_reported[ $slug ] ) ) {
				return;
			}

			self::$activation_reported[ $slug ] = true;
			$instance->send_track( 'activate' );
		}

		/**
		 * Revoke consent programmatically.
		 *
		 * @param string $slug
		 * @return void
		 */
		public static function revoke_consent( $slug ) {
			$slug = sanitize_key( $slug );
			update_option( 'spiderthemes_telemetry_' . $slug . '_optout', 1, false );
			update_option( 'spiderthemes_telemetry_' . $slug . '_decided', time(), false );
			delete_option( 'spiderthemes_telemetry_' . $slug . '_optin' );
			delete_option( 'spiderthemes_telemetry_' . $slug . '_optin_source' );
			$cron_hook = 'spiderthemes_telemetry_heartbeat_' . $slug;
			wp_clear_scheduled_hook( $cron_hook );
		}

		/**
		 * Check if user has opted in.
		 *
		 * @return bool
		 */
		public function has_consent() {
			if ( defined( 'SPIDER_DISABLE_TELEMETRY' ) && SPIDER_DISABLE_TELEMETRY ) {
				return false;
			}
			if ( defined( 'ATML_DISABLE_TELEMETRY' ) && ATML_DISABLE_TELEMETRY ) {
				return false;
			}
			$const_name = strtoupper( str_replace( '-', '_', $this->config['plugin_slug'] ) ) . '_DISABLE_TELEMETRY';
			if ( defined( $const_name ) && constant( $const_name ) ) {
				return false;
			}
			if ( false === apply_filters( 'spiderthemes_telemetry_enabled', true, $this->config['plugin_slug'] ) ) {
				return false;
			}
			$slug = $this->config['plugin_slug'];
			if ( get_option( 'spiderthemes_telemetry_' . $slug . '_optout', false ) ) {
				return false;
			}

			if ( (bool) get_option( 'spiderthemes_telemetry_' . $slug . '_optin', false ) ) {
				$source = (string) get_option( 'spiderthemes_telemetry_' . $slug . '_optin_source', '' );
				// Opt-ins written before this flag existed were copied from Freemius.
				$follows_callback = 'user' !== $source && ! empty( $this->config['consent_follows_callback'] );
				if ( $follows_callback && ! $this->consent_callback_allows() ) {
					return false;
				}
				return true;
			}
			return $this->consent_callback_allows();
		}

		/**
		 * Whether the external consent callback currently allows tracking.
		 *
		 * A missing or throwing callback does not allow tracking.
		 *
		 * @return bool
		 */
		private function consent_callback_allows() {
			if ( empty( $this->config['consent_callback'] ) || ! is_callable( $this->config['consent_callback'] ) ) {
				return false;
			}
			try {
				return (bool) call_user_func( $this->config['consent_callback'] );
			} catch ( \Throwable $e ) {
				return false;
			}
		}

		/**
		 * Check if user has already made a decision (opted in or skipped).
		 *
		 * @return bool
		 */
		public function has_decided() {
			if ( $this->has_consent() ) {
				return true;
			}
			return get_option( 'spiderthemes_telemetry_' . $this->config['plugin_slug'] . '_decided', false ) !== false;
		}

		/**
		 * Generate a persistent anonymized site hash.
		 *
		 * @return string
		 */
		public function get_site_hash() {
			$hash = get_option( 'spiderthemes_telemetry_site_hash' );
			if ( ! $hash ) {
				$hash = hash( 'sha256', home_url() . wp_salt( 'auth' ) );
				update_option( 'spiderthemes_telemetry_site_hash', $hash, false );
			}
			return $hash;
		}

		/**
		 * Render opt-in banner if not yet decided.
		 */
		public function render_optin_notice() {
			if ( ! current_user_can( 'manage_options' ) || $this->has_decided() ) {
				return;
			}

			$slug  = $this->config['plugin_slug'];
			$name  = esc_html( $this->config['plugin_name'] ?: $slug );
			$title = esc_html( $this->config['optin_title'] ?: sprintf( __( 'Help us improve %s', 'spiderthemes' ), $name ) );
			$msg   = esc_html( $this->config['optin_message'] ?: sprintf(
				__( 'Opt in to share anonymous diagnostic and feature usage data. This helps us ensure compatibility and build what you need next. No sensitive data is ever collected.', 'spiderthemes' ),
				$name
			) );
			$priv  = esc_url( $this->config['privacy_url'] );
			$nonce = wp_create_nonce( 'spiderthemes_optin_' . $slug );
			?>
			<div class="notice notice-info spiderthemes-telemetry-notice" id="spiderthemes-notice-<?php echo esc_attr( $slug ); ?>" style="border-left-color: #10b981; padding: 12px 16px; margin: 15px 0;">
				<div style="display: flex; align-items: flex-start; justify-content: space-between; gap: 16px;">
					<div>
						<h3 style="margin: 0 0 6px 0; font-size: 14px; font-weight: 700; color: #0f172a;">
							<?php echo $title; ?>
						</h3>
						<p style="margin: 0 0 10px 0; font-size: 13px; color: #475569; max-width: 780px;">
							<?php echo $msg; ?>
							<a href="<?php echo $priv; ?>" target="_blank" rel="noopener noreferrer" style="color: #059669; text-decoration: underline;"><?php esc_html_e( 'Privacy Policy', 'spiderthemes' ); ?></a>
						</p>
						<div style="display: flex; gap: 10px; align-items: center;">
							<button type="button" class="button button-primary" onclick="spiderTelemetryDecision('<?php echo esc_js( $slug ); ?>', 'allow', '<?php echo esc_js( $nonce ); ?>')" style="background: #10b981; border-color: #059669; font-weight: 600;">
								<?php esc_html_e( 'Allow & Continue', 'spiderthemes' ); ?>
							</button>
							<button type="button" class="button button-secondary" onclick="spiderTelemetryDecision('<?php echo esc_js( $slug ); ?>', 'skip', '<?php echo esc_js( $nonce ); ?>')">
								<?php esc_html_e( 'Skip', 'spiderthemes' ); ?>
							</button>
						</div>
					</div>
				</div>
			</div>
			<script>
				function spiderTelemetryDecision(slug, action, nonce) {
					var el = document.getElementById('spiderthemes-notice-' + slug);
					if (el) el.style.opacity = '0.5';
					var data = new FormData();
					data.append('action', 'spiderthemes_telemetry_optin_' + slug);
					data.append('decision', action);
					data.append('nonce', nonce);
					fetch(ajaxurl, { method: 'POST', body: data })
						.then(function() {
							if (el) el.remove();
						});
				}
			</script>
			<?php
		}

		/**
		 * AJAX handler for opt-in decision.
		 */
		public function ajax_handle_optin() {
			$slug = $this->config['plugin_slug'];
			check_ajax_referer( 'spiderthemes_optin_' . $slug, 'nonce' );

			if ( ! current_user_can( 'manage_options' ) ) {
				wp_send_json_error( 'Unauthorized' );
			}

			$decision = sanitize_key( $_POST['decision'] ?? 'skip' );

			if ( 'allow' === $decision ) {
				self::grant_consent( $slug, 'user' );
			} else {
				update_option( 'spiderthemes_telemetry_' . $slug . '_decided', time(), false );
				delete_option( 'spiderthemes_telemetry_' . $slug . '_optin' );
				delete_option( 'spiderthemes_telemetry_' . $slug . '_optin_source' );
			}

			wp_send_json_success();
		}

		/**
		 * Send periodic heartbeat track.
		 */
		public function send_heartbeat() {
			if ( ! $this->has_consent() ) {
				return;
			}
			$this->send_track( 'heartbeat' );
		}

		/**
		 * Dispatch track data to server.
		 *
		 * @param string $event_type
		 * @param array  $extra_metrics
		 * @return void
		 */
		public function send_track( $event_type = 'heartbeat', array $extra_metrics = [] ) {
			global $wp_version;

			$custom_metrics = [];
			if ( is_callable( $this->config['custom_metrics'] ) ) {
				try {
					$custom_metrics = (array) call_user_func( $this->config['custom_metrics'] );
				} catch ( \Throwable $e ) {
					// Guard against errors in custom metrics
				}
			}

			if ( ! empty( $extra_metrics ) ) {
				$custom_metrics = array_merge( $custom_metrics, $extra_metrics );
			}

			$theme = wp_get_theme();

			$payload = [
				'site_hash'   => $this->get_site_hash(),
				'site_url'    => home_url(),
				'plugin'      => $this->config['plugin_slug'],
				'version'     => $this->config['plugin_version'],
				'wp_version'  => $wp_version,
				'php_version' => PHP_VERSION,
				'server'      => $_SERVER['SERVER_SOFTWARE'] ?? '',
				'locale'      => get_locale(),
				'theme'       => $theme ? $theme->get( 'Name' ) : '',
				'multisite'   => is_multisite(),
				'event'       => $event_type,
				'metrics'     => $custom_metrics,
			];

			$endpoint = rtrim( $this->config['endpoint'], '/' ) . '/api/v1/track';

			wp_remote_post( $endpoint, [
				'method'      => 'POST',
				'timeout'     => 5,
				'redirection' => 2,
				'httpversion' => '1.1',
				'blocking'    => false, // Non-blocking background request
				'headers'     => [
					'Content-Type' => 'application/json; charset=utf-8',
					'Accept'       => 'application/json',
				],
				'body'        => wp_json_encode( $payload ),
				'sslverify'   => true,
			] );
		}

		/**
		 * Render deactivation modal in plugins.php.
		 */
		public function render_deactivation_modal() {
			global $pagenow;
			if ( 'plugins.php' !== $pagenow ) {
				return;
			}

			$slug      = $this->config['plugin_slug'];
			$plugin_basename = plugin_basename( $this->config['plugin_file'] );
			$nonce     = wp_create_nonce( 'spiderthemes_deact_' . $slug );
			$name      = esc_html( $this->config['plugin_name'] ?: $slug );
			?>
			<div id="spiderthemes-deact-modal-<?php echo esc_attr( $slug ); ?>" class="spiderthemes-modal" style="display:none; position:fixed; inset:0; z-index:999999; background:rgba(15,23,42,0.7); backdrop-filter:blur(4px); align-items:center; justify-content:center; font-family:-apple-system,BlinkMacSystemFont,'Segoe UI',Roboto,sans-serif;">
				<div style="background:#fff; border-radius:12px; width:100%; max-width:480px; padding:24px; box-shadow:0 20px 25px -5px rgba(0,0,0,0.1),0 10px 10px -5px rgba(0,0,0,0.04); position:relative;">
					<h3 style="margin:0 0 8px 0; font-size:17px; font-weight:700; color:#0f172a;">
						<?php printf( esc_html__( 'Quick feedback for %s', 'spiderthemes' ), $name ); ?>
					</h3>
					<p style="margin:0 0 16px 0; font-size:13px; color:#64748b; line-height:1.5;">
						<?php esc_html_e( 'If you have a moment, please let us know why you are deactivating. Your feedback helps us build a better experience.', 'spiderthemes' ); ?>
					</p>

					<div style="display:flex; flex-direction:column; gap:8px; margin-bottom:16px; font-size:13px; color:#334155;">
						<label style="display:flex; align-items:center; gap:8px; cursor:pointer;">
							<input type="radio" name="deact_reason_<?php echo esc_attr( $slug ); ?>" value="no_longer_needed" checked>
							<?php esc_html_e( 'I no longer need this plugin', 'spiderthemes' ); ?>
						</label>
						<label style="display:flex; align-items:center; gap:8px; cursor:pointer;">
							<input type="radio" name="deact_reason_<?php echo esc_attr( $slug ); ?>" value="found_better">
							<?php esc_html_e( 'I found a better alternative', 'spiderthemes' ); ?>
						</label>
						<label style="display:flex; align-items:center; gap:8px; cursor:pointer;">
							<input type="radio" name="deact_reason_<?php echo esc_attr( $slug ); ?>" value="could_not_setup">
							<?php esc_html_e( 'I couldn\'t get it to work as expected', 'spiderthemes' ); ?>
						</label>
						<label style="display:flex; align-items:center; gap:8px; cursor:pointer;">
							<input type="radio" name="deact_reason_<?php echo esc_attr( $slug ); ?>" value="temporary_debug">
							<?php esc_html_e( 'Temporary deactivation for testing / debugging', 'spiderthemes' ); ?>
						</label>
						<label style="display:flex; align-items:center; gap:8px; cursor:pointer;">
							<input type="radio" name="deact_reason_<?php echo esc_attr( $slug ); ?>" value="other">
							<?php esc_html_e( 'Other reason', 'spiderthemes' ); ?>
						</label>
					</div>

					<div style="margin-bottom:18px;">
						<textarea id="spiderthemes-deact-details-<?php echo esc_attr( $slug ); ?>" placeholder="<?php esc_attr_e( 'Additional comments or suggestions (optional)...', 'spiderthemes' ); ?>" style="width:100%; box-sizing:border-box; height:70px; border-radius:8px; border:1px solid #cbd5e1; padding:8px 10px; font-size:12px; resize:none;"></textarea>
					</div>

					<div style="display:flex; align-items:center; justify-content:space-between;">
						<a href="#" id="spiderthemes-deact-skip-<?php echo esc_attr( $slug ); ?>" style="font-size:12px; color:#64748b; text-decoration:none;">
							<?php esc_html_e( 'Skip & Deactivate', 'spiderthemes' ); ?>
						</a>
						<div style="display:flex; gap:8px;">
							<button type="button" class="button button-secondary" id="spiderthemes-deact-cancel-<?php echo esc_attr( $slug ); ?>">
								<?php esc_html_e( 'Cancel', 'spiderthemes' ); ?>
							</button>
							<button type="button" class="button button-primary" id="spiderthemes-deact-submit-<?php echo esc_attr( $slug ); ?>" style="background:#10b981; border-color:#059669;">
								<?php esc_html_e( 'Submit & Deactivate', 'spiderthemes' ); ?>
							</button>
						</div>
					</div>
				</div>
			</div>

			<script>
				(function() {
					var slug = '<?php echo esc_js( $slug ); ?>';
					var pluginFile = '<?php echo esc_js( $plugin_basename ); ?>';
					var nonce = '<?php echo esc_js( $nonce ); ?>';
					var deactLink = document.querySelector('tr[data-plugin="' + pluginFile + '"] .deactivate a');
					var modal = document.getElementById('spiderthemes-deact-modal-' + slug);
					if (!deactLink || !modal) return;

					var targetUrl = deactLink.href;

					deactLink.addEventListener('click', function(e) {
						e.preventDefault();
						modal.style.display = 'flex';
					});

					document.getElementById('spiderthemes-deact-cancel-' + slug).addEventListener('click', function() {
						modal.style.display = 'none';
					});

					document.getElementById('spiderthemes-deact-skip-' + slug).addEventListener('click', function(e) {
						e.preventDefault();
						window.location.href = targetUrl;
					});

					document.getElementById('spiderthemes-deact-submit-' + slug).addEventListener('click', function() {
						var selected = document.querySelector('input[name="deact_reason_' + slug + '"]:checked');
						var reason = selected ? selected.value : 'other';
						var reasonLabel = selected ? selected.parentElement.innerText.trim() : '';
						var details = document.getElementById('spiderthemes-deact-details-' + slug).value;

						var data = new FormData();
						data.append('action', 'spiderthemes_telemetry_deactivate_' + slug);
						data.append('reason_id', reason);
						data.append('reason_text', reasonLabel);
						data.append('details', details);
						data.append('nonce', nonce);

						fetch(ajaxurl, { method: 'POST', body: data })
							.finally(function() {
								window.location.href = targetUrl;
							});
					});
				})();
			</script>
			<?php
		}

		/**
		 * AJAX handler for deactivation feedback.
		 */
		public function ajax_handle_deactivate() {
			$slug = $this->config['plugin_slug'];
			check_ajax_referer( 'spiderthemes_deact_' . $slug, 'nonce' );

			if ( ! current_user_can( 'manage_options' ) ) {
				wp_send_json_error( 'Unauthorized' );
			}

			global $wp_version;

			$reason_id   = sanitize_key( $_POST['reason_id'] ?? 'other' );
			$reason_text = sanitize_text_field( $_POST['reason_text'] ?? '' );
			$details     = sanitize_textarea_field( $_POST['details'] ?? '' );

			$payload = [
				'site_hash'   => $this->get_site_hash(),
				'plugin'      => $slug,
				'version'     => $this->config['plugin_version'],
				'reason_id'   => $reason_id,
				'reason_text' => $reason_text,
				'details'     => $details,
				'wp_version'  => $wp_version,
				'php_version' => PHP_VERSION,
			];

			$endpoint = rtrim( $this->config['endpoint'], '/' ) . '/api/v1/deactivate';

			wp_remote_post( $endpoint, [
				'method'      => 'POST',
				'timeout'     => 5,
				'blocking'    => false,
				'headers'     => [
					'Content-Type' => 'application/json; charset=utf-8',
					'Accept'       => 'application/json',
				],
				'body'        => wp_json_encode( $payload ),
				'sslverify'   => true,
			] );

			wp_send_json_success();
		}
	}
}
