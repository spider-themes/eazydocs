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
			<div class="ezd-pro-alert__logo-stack">
				<svg class="ezd-pro-alert__logo-icon" stroke="currentColor" fill="currentColor" stroke-width="0" viewBox="0 0 640 512" height="1em" width="1em" xmlns="http://www.w3.org/2000/svg"><path d="M528 448H112c-8.8 0-16 7.2-16 16v32c0 8.8 7.2 16 16 16h416c8.8 0 16-16 16-16v-32c0-8.8-7.2-16-16-16zm64-320c-26.5 0-48 21.5-48 48 0 7.1 1.6 13.7 4.4 19.8L476 239.2c-15.4 9.2-35.3 4-44.2-11.6L350.3 85C361 76.2 368 63 368 48c0-26.5-21.5-48-48-48s-48 21.5-48 48c0 15 7 28.2 17.7 37l-81.5 142.6c-8.9 15.6-28.9 20.8-44.2 11.6l-72.3-43.4c2.7-6 4.4-12.7 4.4-19.8 0-26.5-21.5-48-48-48S0 149.5 0 176s21.5 48 48 48c2.6 0 5.2-.4 7.7-.8L128 416h384l72.3-192.8c2.5.4 5.1.8 7.7.8 26.5 0 48-21.5 48-48s-21.5-48-48-48z"></path></svg>
				<img class="ezd-pro-alert__logo" src="${ assetsUrl }/images/eazydocs-logo.png" alt="EazyDocs Premium" />
			</div>
		`;
	} else {
		// Fallback if no assetsUrl is provided.
		logoHtml = `
			<div class="ezd-pro-alert__logo-stack">
				<span class="dashicons dashicons-star-filled ezd-pro-alert__fallback-icon"></span>
			</div>
		`;
	}

	window.Swal.fire( {
		html: `
			<div class="ezd-pro-alert">
				<div class="ezd-pro-alert__media">
					<span class="ezd-pro-alert__badge">
						${ logoHtml }
					</span>
				</div>
				<h2 class="ezd-pro-alert__title">
					Premium Feature
				</h2>
				<p class="ezd-pro-alert__text">
					${ isValid ? 'This is a powerful Premium feature.' : 'This feature is exclusively available in our PRO version.' }
					<br />
					Upgrade your plan to instantly access this and many more advanced tools.
				</p>
				<a href="${ pricingUrl }" class="ezd-pro-alert__cta-link">
					Upgrade to Premium <span class="dashicons dashicons-arrow-right-alt ezd-pro-alert__cta-icon"></span>
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
 * @param {string} assetsUrl  The EZD_ASSETS URL for the video.
 * @param {string} pricingUrl URL to the pricing page.
 */
export const showNotificationProAlert = ( assetsUrl: string, pricingUrl: string ): void => {
	if ( typeof window.Swal === 'undefined' ) {
		return;
	}

	window.Swal.fire( {
		title: 'Notification is a Premium feature',
		html: `
			<div class="ezd-pro-alert ezd-pro-alert--notification">
				<div class="ezd-pro-alert__text">
					<span class="ezd-pro-alert__body-text">Upgrade to our Premium Version to unlock powerful notifications and take full control of your docs.</span>
				</div>
				<div class="ezd-pro-alert__video-frame">
					<video class="ezd-pro-alert__video" width="100%" height="auto" autoplay="autoplay" loop="loop" muted>
						<source src="${ assetsUrl }/videos/noti.mp4" type="video/mp4">
					</video>
				</div>
			</div>
		`,
		icon: false,
		buttons: false,
		dangerMode: true,
		showCloseButton: true,
		confirmButtonText: `
			<a href="${ pricingUrl }" class="ezd-pro-alert__cta-link">
				Upgrade to Premium <span class="dashicons dashicons-arrow-right-alt ezd-pro-alert__cta-icon"></span>
			</a>
		`,
		footer: '<a href="https://eazydocs.spider-themes.net/" target="_blank">Explore All Premium Features →</a>',
		customClass: {
			popup: 'ezd-premium-swal-popup',
			title: 'ezd-builder-premium-heading',
			confirmButton: 'ezd-builder-premium-button',
			footer: 'ezd-builder-premium-footer',
		},
		confirmButtonColor: 'inherit',
	} );
	};
