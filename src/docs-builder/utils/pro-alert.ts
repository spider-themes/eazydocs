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
export const showProAlert = ( pricingUrl: string ): void => {
	if ( typeof window.Swal === 'undefined' ) {
		return;
	}

	const isValid = document.body.classList.contains( 'valid' );

	window.Swal.fire( {
		title: 'Opps...',
		html: isValid
			? `This is a Premium feature. You need to <a href="${ pricingUrl }"><strong class="upgrade-link">Upgrade&nbsp;&nbsp;➤</strong></a> to the Premium plan to use this feature`
			: `This is a PRO feature. You need to <a href="${ pricingUrl }"><strong class="upgrade-link">Upgrade&nbsp;&nbsp;➤</strong></a> to the Premium Version to use this feature`,
		icon: 'warning',
		buttons: [ false, 'Close' ],
		dangerMode: true,
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
		title: 'Notification is a Premium feature',
		html: `<span class="pro-notification-body-text">You need to Upgrade the Premium Version to use this feature</span><video height="400px" autoplay="autoplay" loop="loop" src="${ assetsUrl }/videos/noti.mp4"></video>`,
		icon: false,
		buttons: false,
		dangerMode: true,
		showCloseButton: true,
		confirmButtonText: `<a href="${ pricingUrl }">Upgrade to Premium</a>`,
		footer: '<a href="https://eazydocs.spider-themes.net/" target="_blank"> Learn More </a>',
		customClass: {
			title: 'upgrade-premium-heading',
			confirmButton: 'upgrade-premium-button',
			footer: 'notification-pro-footer-wrap',
		},
		confirmButtonColor: '#f1bd6c',
		Borderless: true,
	} );
};
