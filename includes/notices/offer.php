<?php
/**
 * EazyDocs Pro Upgrade Offer Notice
 *
 * A premium upgrade notice with improved UI/UX, countdown timer,
 * feature highlights, and conversion-optimized copywriting.
 *
 * @package EazyDocs
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Get the number of docs created by the user
 *
 * @return int Number of docs
 */
function ezd_get_docs_count() {
	$docs = wp_count_posts( 'docs' );
	return isset( $docs->publish ) ? (int) $docs->publish : 0;
}

/**
 * Get personalized message based on user's usage
 *
 * @return array Message data with headline and subheadline
 */
function ezd_get_personalized_message() {
	$docs_count    = ezd_get_docs_count();
	$install_days  = ezd_get_install_days();

	// Personalized messaging based on usage
	if ( $docs_count >= 10 ) {
		return array(
			'headline'    => sprintf(
				/* translators: %d: number of docs */
				esc_html__( 'You\'ve created %d docs! Ready to supercharge your documentation?', 'eazydocs' ),
				$docs_count
			),
			'subheadline' => esc_html__( 'Unlock advanced features like advanced Collaboration, Analytics & Role-based Access', 'eazydocs' ),
			'cta'         => esc_html__( 'Upgrade & Save 30%', 'eazydocs' ),
		);
	} elseif ( $install_days >= 30 ) {
		return array(
			'headline'    => esc_html__( 'You\'ve been with us for a month! Here\'s a special thank you.', 'eazydocs' ),
			'subheadline' => esc_html__( 'Get 30% off EazyDocs Pro as our way of saying thanks!', 'eazydocs' ),
			'cta'         => esc_html__( 'Claim Your Reward', 'eazydocs' ),
		);
	} else {
		return array(
			'headline'    => esc_html__( 'Unlock the Full Power of EazyDocs', 'eazydocs' ),
			'subheadline' => esc_html__( 'Join 3,000+ happy customers who upgraded to Pro', 'eazydocs' ),
			'cta'         => esc_html__( 'Get 30% Off Now', 'eazydocs' ),
		);
	}
}

/**
 * Get the number of days since plugin installation
 *
 * @return int Number of days
 */
function ezd_get_install_days() {
	$installed_time = get_option( 'eazyDocs_installed' );
	if ( ! $installed_time ) {
		return 0;
	}
	return (int) floor( ( time() - $installed_time ) / DAY_IN_SECONDS );
}

/**
 * Check if the notice should be shown based on snooze
 *
 * @return bool Whether to show the notice
 */
function ezd_should_show_offer_notice() {
	$user_id      = get_current_user_id();
	$snooze_until = get_user_meta( $user_id, 'ezd_offer_snooze_until', true );

	if ( $snooze_until && time() < (int) $snooze_until ) {
		return false;
	}

	return true;
}

/**
 * Display the upgrade offer notice
 */
function ezd_offer_notice() {
	$is_dev_mode = defined( 'DEVELOPER_MODE' ) && DEVELOPER_MODE;

	if ( is_user_logged_in() && ! $is_dev_mode ) {
		$user_id   = get_current_user_id();
		$dismissed = get_user_meta( $user_id, 'ezd_offer_dismissed', true );

		if ( '1' === $dismissed ) {
			return;
		}

		if ( ! ezd_should_show_offer_notice() ) {
			return;
		}
	}

	$message     = ezd_get_personalized_message();
	$coupon_code = 'DASH30';
	$pricing_url = 'https://eazydocs.spider-themes.net/pricing/';

	// Pro features to highlight
	$pro_features = array(
		array(
			'icon'  => 'dashicons-chart-bar',
			'title' => esc_html__( 'Advanced Analytics', 'eazydocs' ),
			'desc'  => esc_html__( 'Track page views, search queries & user engagement in real-time', 'eazydocs' ),
		),
		array(
			'icon'  => 'dashicons-lock',
			'title' => esc_html__( 'Access Control', 'eazydocs' ),
			'desc'  => esc_html__( 'Restrict documentation access by user roles & membership levels', 'eazydocs' ),
		),
		array(
			'icon'  => 'dashicons-groups',
			'title' => esc_html__( 'Advanced Collaboration', 'eazydocs' ),
			'desc'  => esc_html__( 'Enable team members to edit, review & manage docs together', 'eazydocs' ),
		),
		array(
			'icon'  => 'dashicons-email-alt',
			'title' => esc_html__( 'Email Reports', 'eazydocs' ),
			'desc'  => esc_html__( 'Receive weekly performance insights and analytics reports via email', 'eazydocs' ),
		),
	);
	?>
	<div class="ezd-offer-notice" id="ezd-offer-notice">
		<!-- Decorative Background Elements -->
		<div class="ezd-offer-bg-pattern"></div>
		<div class="ezd-offer-glow"></div>

		<!-- Close & Snooze Buttons -->
		<div class="ezd-offer-actions">
			<button type="button" class="ezd-offer-snooze" data-action="snooze" title="<?php esc_attr_e( 'Remind me in 3 days', 'eazydocs' ); ?>">
				<span class="dashicons dashicons-clock"></span>
				<span class="ezd-offer-snooze-text"><?php esc_html_e( 'Later', 'eazydocs' ); ?></span>
			</button>
			<button type="button" class="ezd-offer-dismiss" data-action="dismiss" title="<?php esc_attr_e( 'Dismiss forever', 'eazydocs' ); ?>">
				<span class="dashicons dashicons-no-alt"></span>
			</button>
		</div>

		<div class="ezd-offer-container">
			<!-- Left: Badge & Headline -->
			<div class="ezd-offer-header">
				<div class="ezd-offer-badge">
					<span class="ezd-offer-badge-icon">🎉</span>
					<span class="ezd-offer-badge-text"><?php esc_html_e( 'Limited Time Offer', 'eazydocs' ); ?></span>
				</div>
				<h3 class="ezd-offer-headline"><?php echo esc_html( $message['headline'] ); ?></h3>
				<p class="ezd-offer-subheadline"><?php echo esc_html( $message['subheadline'] ); ?></p>
			</div>

			<!-- Center: Pro Features -->
			<div class="ezd-offer-features">
				<?php foreach ( $pro_features as $feature ) : ?>
					<div class="ezd-offer-feature">
						<span class="dashicons <?php echo esc_attr( $feature['icon'] ); ?>"></span>
						<div class="ezd-offer-feature-text">
							<strong><?php echo esc_html( $feature['title'] ); ?></strong>
							<span><?php echo esc_html( $feature['desc'] ); ?></span>
						</div>
					</div>
				<?php endforeach; ?>
			</div>

			<!-- Right: Discount & CTA -->
			<div class="ezd-offer-cta-section">
				<div class="ezd-offer-discount">
					<span class="ezd-offer-discount-value">30%</span>
					<span class="ezd-offer-discount-label"><?php esc_html_e( 'OFF', 'eazydocs' ); ?></span>
				</div>

				<div class="ezd-offer-coupon">
					<span class="ezd-offer-coupon-label"><?php esc_html_e( 'Use code:', 'eazydocs' ); ?></span>
					<div class="ezd-offer-coupon-box">
						<code class="ezd-offer-coupon-code" id="ezd-coupon-code"><?php echo esc_html( $coupon_code ); ?></code>
						<button type="button" class="ezd-offer-copy-btn" id="ezd-copy-btn" title="<?php esc_attr_e( 'Copy coupon code', 'eazydocs' ); ?>">
							<span class="dashicons dashicons-admin-page"></span>
						</button>
						<span class="ezd-offer-copy-success" id="ezd-copy-success">
							<span class="dashicons dashicons-yes-alt"></span>
							<?php esc_html_e( 'Copied!', 'eazydocs' ); ?>
						</span>
					</div>
				</div>

				<a href="<?php echo esc_url( add_query_arg( 'coupon', $coupon_code, $pricing_url ) ); ?>" 
				   class="ezd-offer-cta-btn" 
				   target="_blank"
				   rel="noopener noreferrer">
					<?php echo esc_html( $message['cta'] ); ?>
					<span class="dashicons dashicons-arrow-right-alt"></span>
				</a>

				<p class="ezd-offer-guarantee">
					<span class="dashicons dashicons-shield"></span>
					<?php esc_html_e( '30-day money-back guarantee', 'eazydocs' ); ?>
				</p>
			</div>
		</div>
	</div>

	<script>
	document.addEventListener('DOMContentLoaded', function() {
		const notice = document.getElementById('ezd-offer-notice');
		const copyBtn = document.getElementById('ezd-copy-btn');
		const couponCode = document.getElementById('ezd-coupon-code');
		const copySuccess = document.getElementById('ezd-copy-success');
		const snoozeBtn = notice.querySelector('.ezd-offer-snooze');
		const dismissBtn = notice.querySelector('.ezd-offer-dismiss');

		// Copy coupon code
		if (copyBtn && couponCode) {
			copyBtn.addEventListener('click', function() {
				navigator.clipboard.writeText(couponCode.textContent).then(function() {
					copySuccess.classList.add('show');
					copyBtn.classList.add('copied');
					setTimeout(function() {
						copySuccess.classList.remove('show');
						copyBtn.classList.remove('copied');
					}, 2000);
				}).catch(function(err) {
					// Fallback for older browsers
					const textArea = document.createElement('textarea');
					textArea.value = couponCode.textContent;
					document.body.appendChild(textArea);
					textArea.select();
					document.execCommand('copy');
					document.body.removeChild(textArea);
					copySuccess.classList.add('show');
					setTimeout(function() {
						copySuccess.classList.remove('show');
					}, 2000);
				});
			});
		}

		// Handle dismiss/snooze actions
		function handleAction(action) {
			notice.style.opacity = '0';
			notice.style.transform = 'translateY(-20px)';
			
			setTimeout(function() {
				notice.style.display = 'none';
			}, 300);

			fetch('<?php echo esc_url( admin_url( 'admin-ajax.php' ) ); ?>', {
				method: 'POST',
				credentials: 'same-origin',
				headers: {
					'Content-Type': 'application/x-www-form-urlencoded'
				},
				body: 'action=ezd_handle_offer_notice&notice_action=' + action + '&nonce=<?php echo esc_js( wp_create_nonce( 'ezd-offer-notice' ) ); ?>'
			});
		}

		if (snoozeBtn) {
			snoozeBtn.addEventListener('click', function() {
				handleAction('snooze');
			});
		}

		if (dismissBtn) {
			dismissBtn.addEventListener('click', function() {
				handleAction('dismiss');
			});
		}

		// Animate in on load
		setTimeout(function() {
			notice.classList.add('ezd-offer-visible');
		}, 100);
	});
	</script>
	<?php
}

/**
 * Handle offer notice AJAX actions (dismiss/snooze)
 */
add_action( 'wp_ajax_ezd_handle_offer_notice', 'ezd_handle_offer_notice_ajax' );

/**
 * Process the dismiss or snooze action
 */
function ezd_handle_offer_notice_ajax() {
	check_ajax_referer( 'ezd-offer-notice', 'nonce' );

	if ( ! is_user_logged_in() ) {
		wp_send_json_error( array( 'message' => 'User not logged in.' ) );
	}

	$user_id = get_current_user_id();
	$action  = isset( $_POST['notice_action'] ) ? sanitize_text_field( wp_unslash( $_POST['notice_action'] ) ) : '';

	if ( 'dismiss' === $action ) {
		update_user_meta( $user_id, 'ezd_offer_dismissed', '1' );
		wp_send_json_success( array( 'message' => 'Notice dismissed permanently.' ) );
	} elseif ( 'snooze' === $action ) {
		// Snooze for 3 days
		$snooze_until = time() + ( 3 * DAY_IN_SECONDS );
		update_user_meta( $user_id, 'ezd_offer_snooze_until', $snooze_until );
		wp_send_json_success( array( 'message' => 'Notice snoozed for 3 days.' ) );
	} else {
		wp_send_json_error( array( 'message' => 'Invalid action.' ) );
	}

	wp_die();
}

// Keep backward compatibility - remove old action if it exists
remove_action( 'wp_ajax_ezd_dismiss_offer_notice', 'ezd_dismiss_offer_notice' );
