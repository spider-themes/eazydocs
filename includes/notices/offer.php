<?php
/**
 ** Give Notice
 **/
function ezd_offer_notice() {
	if ( isset( $_COOKIE['ezd_offer_dismissed'] ) && '1' === $_COOKIE['ezd_offer_dismissed'] ) {
		return; // Don't show the notice if it has been dismissed
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
                <img src="<?php echo EAZYDOCS_IMG ?>/icon/coupon.svg"
                     alt="<?php echo esc_attr_x( 'Coupon', 'coupon', 'eazydocs' ); ?>" class="coupon-icon">
                <div class="ezd-col-text">
                    <p><strong> Up to 40% Off </strong></p>
                    <p> This is limited time offer! </p>
                </div>
            </div>
            <div class="ezd-col">
                <img src="<?php echo EAZYDOCS_IMG ?>/icon/cursor-hand.svg"
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
            const copyBtn = document.querySelector('.copy-btn');
            const couponInput = document.querySelector('.coupon');
            const copyMessage = document.querySelector('.copy-message');
            const dismissBtn = document.querySelector('.dismiss-btn');
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

                // Set a cookie to prevent the notice from showing again
                document.cookie = "ezd_offer_dismissed=1; path=/; max-age=" + (60 * 60 * 24 * 30); // Cookie valid for 30 days
            });
        });
    </script>
	<?php
}