/**
 * Pro functionality alert utilities.
 *
 * Provides reusable functions to show SweetAlert alerts when
 * a user tries to use a Pro-only feature without a premium license.
 *
 * @package EazyDocs
 * @since   2.8.0
 */

declare const window: Window & {
	Swal?: any;
};

/**
 * Show a generic Pro feature alert via SweetAlert.
 *
 * Mirrors the jQuery handler in admin-global.js for .eazydocs-pro-notice elements.
 *
 * @param {string} pricingUrl URL to the pricing page.
 */
export const showProAlert = ( pricingUrl: string, assetsUrl?: string ): void => {
	if ( typeof window.Swal === 'undefined' ) {
		return;
	}

	const isValid = document.body.classList.contains( 'valid' );

	let logoHtml = '';
	if ( assetsUrl ) {
		logoHtml = `
			<div style="position: absolute; top: 0; left: 0; width: 100%; height: 100%; display: flex; align-items: center; justify-content: center;">
				<svg style="position: absolute; top: -20px; left: 50%; transform: translateX(-50%); z-index: 2;" width="32" height="32" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
					<path d="M12 2L15.09 8.26L22 9.27L17 14.14L18.18 21.02L12 17.77L5.82 21.02L7 14.14L2 9.27L8.91 8.26L12 2Z" fill="#ffb020" stroke="#f59e0b" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
				</svg>
				<img src="${ assetsUrl }/images/eazydocs-logo.png" alt="EazyDocs Premium" style="width: 52px; height: auto; position: relative; z-index: 1;" />
			</div>
		`;
	} else {
		// Fallback if no assetsUrl is provided.
		logoHtml = `
			<div style="position: absolute; top: 0; left: 0; width: 100%; height: 100%; display: flex; align-items: center; justify-content: center;">
				<span class="dashicons dashicons-star-filled" style="font-size: 44px; color: #ff9900; width: 44px; height: 44px;"></span>
			</div>
		`;
	}

	window.Swal.fire( {
		html: `
			<div style="text-align: center; padding: 20px 10px 10px;">
				<div style="margin-bottom: 24px;">
					<span style="position: relative; display: inline-flex; align-items: center; justify-content: center; width: 88px; height: 88px; background: linear-gradient(135deg, #eff6ff 0%, #bfdbfe 100%); border-radius: 50%; box-shadow: 0 4px 16px rgba(59, 130, 246, 0.2);">
						${ logoHtml }
					</span>
				</div>
				<h2 style="font-size: 24px; font-weight: 700; color: #1e2532; margin-bottom: 16px; line-height: 1.3;">
					Premium Feature Unlock
				</h2>
				<p style="font-size: 16px; color: #515872; margin-bottom: 28px; line-height: 1.6;">
					${ isValid ? 'This is a powerful Premium feature.' : 'This feature is exclusively available in our PRO version.' }
					<br />
					Upgrade your plan to instantly access this and many more advanced tools.
				</p>
				<a href="${ pricingUrl }" style="display: flex; align-items: center; justify-content: center; gap: 8px; background: linear-gradient(135deg, #6366f1 0%, #4f46e5 100%); color: #ffffff; text-decoration: none; font-weight: 600; font-size: 16px; padding: 14px 28px; border-radius: 8px; box-shadow: 0 4px 14px rgba(99, 102, 241, 0.35); transition: all 0.2s ease; width: 100%; box-sizing: border-box;">
					Upgrade to Premium <span class="dashicons dashicons-arrow-right-alt" style="margin-top: 2px;"></span>
				</a>
			</div>
		`,
		showConfirmButton: false,
		showCloseButton: true,
		customClass: {
			popup: 'ezd-premium-swal-popup',
		},
		width: 440,
	} );
};

/**
 * Show the Notification Pro alert via SweetAlert.
 *
 * Mirrors the jQuery handler in admin-global.js for .pro-notification-alert elements.
 *
 * @param {string} assetsUrl  The EAZYDOCS_ASSETS URL for the video.
 * @param {string} pricingUrl URL to the pricing page.
 */
export const showNotificationProAlert = ( assetsUrl: string, pricingUrl: string ): void => {
	if ( typeof window.Swal === 'undefined' ) {
		return;
	}

	window.Swal.fire( {
		title: '<strong style="color: #2b3044; font-size: 24px; font-weight: 700;">Notification is a Premium feature</strong>',
		html: `
			<div style="font-size: 16px; color: #515872; margin-bottom: 24px; line-height: 1.6;">
				<span class="pro-notification-body-text">Upgrade to our Premium Version to unlock powerful notifications and take full control of your docs.</span>
			</div>
			<div style="border-radius: 12px; overflow: hidden; box-shadow: 0 10px 30px rgba(0,0,0,0.1); margin-bottom: 24px;">
				<video width="100%" height="auto" autoplay="autoplay" loop="loop" muted style="display: block;">
					<source src="${ assetsUrl }/videos/noti.mp4" type="video/mp4">
				</video>
			</div>
		`,
		icon: false,
		buttons: false,
		dangerMode: true,
		showCloseButton: true,
		confirmButtonText: `
			<a href="${ pricingUrl }" style="color: #ffffff; text-decoration: none; display: flex; align-items: center; justify-content: center; gap: 8px; font-weight: 600; font-size: 16px;">
				Upgrade to Premium <span class="dashicons dashicons-arrow-right-alt" style="margin-top: 2px;"></span>
			</a>
		`,
		footer: '<a href="https://eazydocs.spider-themes.net/" target="_blank" style="color: #6366f1; text-decoration: none; font-weight: 600;">Explore All Premium Features →</a>',
		customClass: {
			popup: 'ezd-premium-swal-popup',
			title: 'upgrade-premium-heading',
			confirmButton: 'upgrade-premium-button-modern',
			footer: 'notification-pro-footer-wrap',
		},
		confirmButtonColor: 'inherit',
	} );
};
