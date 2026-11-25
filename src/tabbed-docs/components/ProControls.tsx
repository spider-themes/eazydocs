/**
 * Shared Pro gating controls, badges, and helper components for Tabbed Docs block.
 *
 * @package EazyDocs
 */

import type { ReactNode } from 'react';
import { __ } from '@wordpress/i18n';
import { Notice } from '@wordpress/components';
import { useSelect } from '@wordpress/data';

export interface SelectOption {
	label: string;
	value: string;
}

/**
 * Check if Pro is active via localized script data.
 */
export const isProActive = (): boolean => {
	return (
		window.eazydocs_local_object?.is_ezd_pro_block === 'yes' ||
		window.ezdBlockData?.is_ezd_pro_block === 'yes'
	);
};

/**
 * Get the dynamic pricing page URL from localized script data.
 */
export const getPricingUrl = (): string => {
	return (
		window.eazydocs_local_object?.ezd_pricing_url ||
		window.ezdBlockData?.ezd_pricing_url ||
		'/wp-admin/admin.php?page=eazydocs-pricing'
	);
};

/**
 * Pro style values that require Pro license.
 */
export const proTabStyles: string[] = [ 'pill', 'boxed', 'underline', 'gradient', 'glass' ];
export const proCardStyles: string[] = [ 'flat', 'minimal', 'glass', 'gradient-border' ];
export const proLayoutTypes: string[] = [ 'masonry' ];

/**
 * Check if a value is a Pro-only option.
 */
export const isProOption = ( value: string, proList: string[] ): boolean => proList.includes( value );

/**
 * Pro Badge Component - Inline for dropdown options.
 */
export const ProBadge = ( { inline = false }: { inline?: boolean } ) => (
	<span className={ `ezd-pro-badge ${ inline ? 'ezd-pro-badge-inline' : '' }` }>
		<svg
			xmlns="http://www.w3.org/2000/svg"
			width="10"
			height="10"
			viewBox="0 0 24 24"
			fill="currentColor"
		>
			<path d="M12 2L15.09 8.26L22 9.27L17 14.14L18.18 21.02L12 17.77L5.82 21.02L7 14.14L2 9.27L8.91 8.26L12 2Z" />
		</svg>
		{ __( 'Pro', 'eazydocs' ) }
	</span>
);

/**
 * Pro Demo Notice - Shows when a Pro option is selected in demo mode.
 */
export const ProDemoNotice = () => (
	<Notice
		status="warning"
		isDismissible={ false }
		className="ezd-pro-demo-notice"
	>
		<div className="ezd-pro-demo-notice-content">
			<strong>{ __( 'Pro Feature Preview', 'eazydocs' ) }</strong>
			<p>{ __( 'This style will not apply on the frontend.', 'eazydocs' ) }</p>
			<a className="ezd-upgrade-link" href={ getPricingUrl() }>
				{ __( 'Upgrade to Pro', 'eazydocs' ) } →
			</a>
		</div>
	</Notice>
);

/**
 * Pro Watermark - Large centered PRO text overlay with upgrade button.
 */
export const ProWatermark = () => (
	<div className="ezd-pro-watermark">
		<a className="ezd-pro-watermark-btn" href={ getPricingUrl() }>
			{ __( 'Upgrade to Pro', 'eazydocs' ) } →
		</a>
	</div>
);

/**
 * Upgrade Notice - For panel descriptions (not overlay).
 */
export const UpgradeNotice = ( { feature }: { feature: string } ) => (
	<div className="ezd-upgrade-notice">
		<div className="ezd-upgrade-icon">
			<svg
				xmlns="http://www.w3.org/2000/svg"
				width="16"
				height="16"
				viewBox="0 0 24 24"
				fill="none"
				stroke="currentColor"
				strokeWidth="2"
			>
				<path d="M12 22c5.523 0 10-4.477 10-10S17.523 2 12 2 2 6.477 2 12s4.477 10 10 10z" />
				<path d="M12 16v-4" />
				<path d="M12 8h.01" />
			</svg>
		</div>
		<div className="ezd-upgrade-content">
			<strong>{ __( 'Pro Feature', 'eazydocs' ) }</strong>
			<p>{ feature }</p>
			<a className="ezd-upgrade-link" href={ getPricingUrl() }>
				{ __( 'Upgrade to Pro', 'eazydocs' ) } →
			</a>
		</div>
	</div>
);

/**
 * Locked Control Wrapper - Grayed out with PRO watermark.
 */
export const LockedControl = ( {
	children,
	feature: _feature,
	isPro = false,
}: {
	children: ReactNode;
	feature?: string;
	isPro?: boolean;
} ) => {
	if ( isPro || isProActive() ) {
		return <>{ children }</>;
	}

	return (
		<div className="ezd-locked-control">
			<div className="ezd-locked-overlay">
				<ProWatermark />
			</div>
			<div className="ezd-locked-content">{ children }</div>
		</div>
	);
};

interface MediaRecord {
	source_url: string;
	alt_text?: string;
}

/**
 * TabIcon Component - Fetches and displays featured image for a tab.
 */
export const TabIcon = ( { mediaId }: { mediaId: number } ) => {
	const media = useSelect(
		( select: ( store: string ) => Record< string, ( ...args: unknown[] ) => unknown > ) => {
			if ( ! mediaId ) {
				return null;
			}
			return select( 'core' ).getMedia( mediaId, { context: 'view' } ) as MediaRecord | null;
		},
		[ mediaId ]
	);

	if ( ! media ) {
		return null;
	}

	return (
		<span className="ezd-tab-icon">
			<img
				src={ media.source_url }
				alt={ media.alt_text || '' }
				className="ezd-tab-icon-img"
			/>
		</span>
	);
};

/**
 * Extract doc IDs from form token values.
 */
export const doc_ids = ( tokens: string[] ): number[] => {
	return tokens
		.map( ( token ) => {
			const match = token.match( /^(\d+)/ );
			return match ? parseInt( match[ 1 ], 10 ) : null;
		} )
		.filter( ( id ): id is number => id !== null );
};

/**
 * Create dropdown options with Pro badges.
 */
export const createOptionsWithProBadge = (
	freeOptions: SelectOption[],
	proOptions: SelectOption[],
	isPro: boolean
): SelectOption[] => {
	const allOptions = [ ...freeOptions ];

	proOptions.forEach( ( option ) => {
		allOptions.push( {
			...option,
			label: isPro ? option.label : `${ option.label } ⭐ Pro`,
		} );
	} );

	return allOptions;
};
