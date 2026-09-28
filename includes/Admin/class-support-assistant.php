<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

/**
 * Support Assistant
 *
 * Embeds the Spider Themes helpdesk docs assistant (EazyDocs cross-domain
 * embed) on EazyDocs admin pages, scoped to the EazyDocs docs.
 *
 * Privacy:
 * - The toggle styles, script and icons are bundled; only the iframe points
 *   to the helpdesk.
 * - The iframe is loaded the first time the admin opens the chat, not on
 *   every page view.
 * - The admin's email and name are passed (to pre-fill the helpdesk lead
 *   form) only when the site opted in to Freemius and the current user is
 *   the account that gave that consent. Otherwise the chat still works and
 *   the admin types their email into the lead form.
 *
 * Can be turned off in EazyDocs → Settings → General Settings.
 */
class EazyDocs_Support_Assistant {

	const IFRAME_URL  = 'https://helpdesk.spider-themes.net/iframe-assistant/';
	const PRODUCT_CTX = 'EazyDocs';
	const DOCS_SCOPE  = 5217;

	private static $instance;

	/**
	 * Whether the embed should be printed on the current screen.
	 *
	 * @var bool
	 */
	private $is_active = false;

	/**
	 * Get class instance
	 *
	 * @return self
	 */
	public static function get_instance(): self {
		if ( null === self::$instance ) {
			self::$instance = new self();
		}
		return self::$instance;
	}

	private function __construct() {
		add_action( 'admin_enqueue_scripts', [ $this, 'enqueue_assets' ] );
		add_action( 'admin_footer', [ $this, 'render_embed' ] );
	}

	/**
	 * Whether the current admin screen belongs to EazyDocs.
	 *
	 * @return bool
	 */
	private function is_plugin_screen(): bool {
		$page = isset( $_GET['page'] ) ? sanitize_key( wp_unslash( $_GET['page'] ) ) : ''; // phpcs:ignore WordPress.Security.NonceVerification.Recommended

		return 0 === strpos( $page, 'eazydocs' ) || 0 === strpos( $page, 'ezd' );
	}

	/**
	 * Whether "Hide Support Chat" is turned on in the settings.
	 *
	 * @return bool
	 */
	private function is_hidden_in_settings(): bool {
		return (bool) ezd_get_opt( 'hide_support_assistant', false );
	}

	/**
	 * Enqueue the bundled toggle styles and script on EazyDocs admin pages.
	 *
	 * @return void
	 */
	public function enqueue_assets(): void {
		if ( ! current_user_can( 'manage_options' ) || ! $this->is_plugin_screen() ) {
			return;
		}

		/**
		 * Filter whether the helpdesk support assistant is shown on EazyDocs admin pages.
		 *
		 * @param bool $enabled False when "Hide Support Chat" is on in the settings.
		 */
		if ( ! apply_filters( 'ezd_support_assistant_enabled', ! $this->is_hidden_in_settings() ) ) {
			return;
		}

		$this->is_active = true;

		wp_enqueue_style( 'ezd-support-assistant', EZD_ASSETS . 'css/support-assistant.css', [], EZD_VERSION );
		wp_enqueue_script( 'ezd-support-assistant', EZD_ASSETS . 'js/support-assistant.js', [], EZD_VERSION, true );

		// Spinner shown in the panel until the helpdesk page has loaded.
		wp_add_inline_style(
			'ezd-support-assistant',
			'.ezd-support-assistant .chatbox-iframe-wraper.ezd-loading{background:#fff}
			.ezd-support-assistant .chatbox-iframe-wraper.ezd-loading::after{content:"";position:absolute;top:50%;left:50%;width:32px;height:32px;margin:-16px 0 0 -16px;border:3px solid #e2e8f0;border-top-color:#0866ff;border-radius:50%;animation:ezd-support-spin .8s linear infinite}
			@keyframes ezd-support-spin{to{transform:rotate(360deg)}}'
		);

		// Load the iframe on first open only. To make that feel instant:
		// - hovering the button opens the connection to the helpdesk (DNS/TLS
		//   only, no data is sent), which saves ~1s on a cold connection;
		// - loading starts on press rather than when the click completes.
		wp_add_inline_script(
			'ezd-support-assistant',
			"(function () {
				var root = document.querySelector('.ezd-support-assistant');
				var toggle = root && root.querySelector('.chat-toggle');
				var wrap = root && root.querySelector('.chatbox-iframe-wraper');
				var iframe = root && root.querySelector('iframe[data-src]');
				if (!toggle || !wrap || !iframe) return;
				var src = iframe.getAttribute('data-src');
				var warmed = false;
				function warm() {
					if (warmed) return;
					warmed = true;
					var link = document.createElement('link');
					link.rel = 'preconnect';
					link.href = new URL(src).origin;
					document.head.appendChild(link);
				}
				function load() {
					if (iframe.getAttribute('src')) return;
					warm();
					wrap.classList.add('ezd-loading');
					iframe.addEventListener('load', function () { wrap.classList.remove('ezd-loading'); }, { once: true });
					iframe.setAttribute('src', src);
				}
				toggle.addEventListener('pointerenter', warm);
				toggle.addEventListener('pointerdown', load);
				toggle.addEventListener('click', load);
			})();"
		);
	}

	/**
	 * The admin's email and name, only when Freemius consent covers them.
	 *
	 * Consent is given by the admin who opted in, so the current user must be
	 * that Freemius account; other admins on the same site get no pre-fill.
	 *
	 * @return array{email?: string, name?: string}
	 */
	private function get_consented_lead(): array {
		if ( ! function_exists( 'eaz_fs' ) ) {
			return [];
		}

		$fs = eaz_fs();
		if ( ! is_object( $fs ) || ! method_exists( $fs, 'is_tracking_allowed' ) || ! $fs->is_registered() || ! $fs->is_tracking_allowed() ) {
			return [];
		}

		$fs_user = $fs->get_user();
		$wp_user = wp_get_current_user();
		if ( ! is_object( $fs_user ) || empty( $fs_user->email ) || 0 !== strcasecmp( $fs_user->email, $wp_user->user_email ) ) {
			return [];
		}

		$name = trim( ( $fs_user->first ?? '' ) . ' ' . ( $fs_user->last ?? '' ) );

		return [
			'email' => sanitize_email( $fs_user->email ),
			'name'  => sanitize_text_field( '' !== $name ? $name : $wp_user->display_name ),
		];
	}

	/**
	 * Build the iframe URL: product scope, plus the admin's details when consented.
	 *
	 * @return string
	 */
	private function get_iframe_url(): string {
		$args = [
			'ctx'       => self::PRODUCT_CTX,
			'ezd_scope' => self::DOCS_SCOPE,
		];

		$lead = $this->get_consented_lead();
		if ( ! empty( $lead['email'] ) ) {
			$args['atml_email'] = $lead['email'];
			$args['atml_name']  = $lead['name'];
		}

		/**
		 * Filter the query args passed to the helpdesk assistant iframe.
		 *
		 * @param array $args Query args (ctx, ezd_scope, and atml_email/atml_name when consented).
		 */
		$args = (array) apply_filters( 'ezd_support_assistant_args', $args );
		$args = array_filter( array_map( 'strval', $args ), 'strlen' );

		return add_query_arg( array_map( 'rawurlencode', $args ), self::IFRAME_URL );
	}

	/**
	 * Print the embed markup in the admin footer.
	 *
	 * @return void
	 */
	public function render_embed(): void {
		if ( ! $this->is_active ) {
			return;
		}

		?>
		<div class="eazydocs-cross-domain-code ezd-support-assistant">
			<div class="chat-toggle">
				<img class="wp-spotlight-chat" src="<?php echo esc_url( EZD_ASSETS . 'images/support-chat.svg' ); ?>" alt="<?php esc_attr_e( 'Chat Icon', 'eazydocs' ); ?>">
				<img class="wp-spotlight-hide" src="<?php echo esc_url( EZD_ASSETS . 'images/support-close.svg' ); ?>" alt="<?php esc_attr_e( 'Close Icon', 'eazydocs' ); ?>" style="display: none;">
			</div>
			<button type="button" class="close-chat-sm"><span><?php esc_html_e( 'Hide', 'eazydocs' ); ?></span><span class="icon">❮</span></button>
			<div class="chatbox-iframe-wraper">
				<iframe data-src="<?php echo esc_url( $this->get_iframe_url() ); ?>" title="<?php esc_attr_e( 'EazyDocs Support Assistant', 'eazydocs' ); ?>" style="border: none;" frameborder="0"></iframe>
			</div>
		</div>
		<?php
	}
}
