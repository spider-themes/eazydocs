<?php
/**
 ** Give Notice
 **/
function ezd_offer_notice() {
	if ( is_user_logged_in() ) {
		$user_id   = get_current_user_id();
		$dismissed = get_user_meta( $user_id, 'ezd_offer_dismissed', true );

		if ( '1' === $dismissed ) {
			return; // Don't show the notice if it has been dismissed by this user
		}
	}

	?>
    <div class="ezd-offer-wrap">
        <div class="ezd-offer">
            <button class="dismiss-btn">&times;</button>
            <div class="ezd-col">
                <span class="dashicons dashicons-megaphone"></span>
                <div class="ezd-col-text">
                    <p><strong> Upgrade to EazyDocs Pro </strong></p>
                    <p> Massive discount </p>
                </div>
            </div>
            <div class="ezd-col">
                <img src="<?php echo esc_url(EAZYDOCS_IMG) ?>/icon/coupon.svg"
                     alt="<?php echo esc_attr_x( 'Coupon', 'coupon', 'eazydocs' ); ?>" class="coupon-icon">
                <div class="ezd-col-text">
                    <p><strong> Up to 40% Off </strong></p>
                    <p> This is limited time offer! </p>
                </div>
            </div>
            <div class="ezd-col">
                <img src="<?php echo esc_url(EAZYDOCS_IMG) ?>/icon/cursor-hand.svg"
                     alt="<?php echo esc_attr_x( 'Coupon', 'coupon', 'eazydocs' ); ?>" class="coupon-icon">
                <div class="ezd-col-text">
                    <p><strong> Grab the deal </strong></p>
                    <p> Before it expires! </p>
                </div>
            </div>
            <div class="ezd-col">
                <div class="ezd-col-box">
                    <label for="coupon">Coupon Code:</label>
                    <div class="coupon-container">
                        <input type="text" value="DASH40" id="coupon" class="coupon" readonly>
                        <span class="copy-message">Coupon copied.</span>
                        <button class="copy-btn">Copy</button>
                    </div>
                </div>
            </div>
            <div class="ezd-col">
                <a href="https://spider-themes.net/eazydocs/pricing/" class="buy-btn" target="_blank"> Claim Discount </a>
            </div>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const copyBtn = document.querySelector('.ezd-offer .copy-btn');
            const couponInput = document.querySelector('.ezd-offer .coupon');
            const copyMessage = document.querySelector('.ezd-offer .copy-message');
            const dismissBtn = document.querySelector('.ezd-offer .dismiss-btn');
            const offerWrap = document.querySelector('.ezd-offer-wrap');

            copyBtn.addEventListener('click', function () {
                navigator.clipboard.writeText(couponInput.value).then(() => {
                    copyMessage.style.display = 'inline';
                    setTimeout(() => {
                        copyMessage.style.display = 'none';
                    }, 1000);
                }).catch(err => {
                    console.error('Could not copy text: ', err);
                });
            });

            dismissBtn.addEventListener('click', function () {
                offerWrap.style.display = 'none';

                // Make an AJAX request to save the dismissal for the logged-in user
                fetch('<?php echo esc_url(admin_url( 'admin-ajax.php' )); ?>', {
                    method: 'POST',
                    credentials: 'same-origin',
                    headers: {
                        'Content-Type': 'application/x-www-form-urlencoded'
                    },
                    body: 'action=ezd_dismiss_offer_notice&nonce=<?php echo esc_js( wp_create_nonce( "ezd-dismiss-notice" ) ); ?>'
                }).then(response => response.json()).then(data => {
                    if (!data.success) {
                        console.error('Error dismissing the notice:', data.message);
                    }
                }).catch(err => {
                    console.error('Error dismissing the offer:', err);
                });
            });
        });
    </script>
	<?php
}

add_action( 'wp_ajax_ezd_dismiss_offer_notice', 'ezd_dismiss_offer_notice' );

function ezd_dismiss_offer_notice() {
	check_ajax_referer( 'ezd-dismiss-notice', 'nonce' );

	if ( is_user_logged_in() ) {
		$user_id = get_current_user_id();
		update_user_meta( $user_id, 'ezd_offer_dismissed', '1' );

		wp_send_json_success( array( 'message' => 'Notice dismissed for this user.' ) );
	} else {
		wp_send_json_error( array( 'message' => 'User not logged in.' ) );
	}

	wp_die();
}
