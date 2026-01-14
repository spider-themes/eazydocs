/**
 * Edit component for Tabbed Docs block
 * Provides enhanced UI/UX with featured image support in tabs
 * Pro features are locked with "Upgrade to Pro" notices for free users
 * Pro options show in dropdowns with badges and work in demo mode in editor
 *
 * @package EazyDocs
 */

import { __ } from '@wordpress/i18n';
import {
	useBlockProps,
	InspectorControls,
	ColorPalette,
} from '@wordpress/block-editor';
import {
	PanelBody,
	ToggleControl,
	RangeControl,
	SelectControl,
	TextControl,
	FormTokenField,
	__experimentalNumberControl as NumberControl,
	RadioControl,
	Spinner,
	__experimentalToggleGroupControl as ToggleGroupControl,
	__experimentalToggleGroupControlOption as ToggleGroupControlOption,
	__experimentalDivider as Divider,
	BaseControl,
	ExternalLink,
	Notice,
} from '@wordpress/components';
import { useSelect } from '@wordpress/data';
import { useState, useEffect } from '@wordpress/element';

// Import editor styles
import './editor.scss';

/**
 * Check if Pro is active via localized script data
 */
const isProActive = () => {
	return (
		window.eazydocs_local_object?.is_ezd_pro_block === 'yes' ||
		window.ezdBlockData?.is_ezd_pro_block === 'yes'
	);
};

/**
 * Get the dynamic pricing page URL from localized script data
 */
const getPricingUrl = () => {
	return (
		window.eazydocs_local_object?.ezd_pricing_url ||
		window.ezdBlockData?.ezd_pricing_url ||
		'/wp-admin/admin.php?page=eazydocs-pricing'
	);
};

/**
 * Pro style values that require Pro license
 */
const proTabStyles = ['pill', 'boxed', 'underline', 'gradient', 'glass'];
const proCardStyles = ['flat', 'minimal', 'glass', 'gradient-border'];
const proLayoutTypes = ['masonry'];

/**
 * Check if a value is a Pro-only option
 */
const isProOption = (value, proList) => proList.includes(value);

/**
 * Pro Badge Component - Inline for dropdown options
 */
const ProBadge = ({ inline = false }) => (
	<span className={`ezd-pro-badge ${inline ? 'ezd-pro-badge-inline' : ''}`}>
		<svg
			xmlns="http://www.w3.org/2000/svg"
			width="10"
			height="10"
			viewBox="0 0 24 24"
			fill="currentColor"
		>
			<path d="M12 2L15.09 8.26L22 9.27L17 14.14L18.18 21.02L12 17.77L5.82 21.02L7 14.14L2 9.27L8.91 8.26L12 2Z" />
		</svg>
		{__('Pro', 'eazydocs')}
	</span>
);

/**
 * Pro Demo Notice - Shows when a Pro option is selected in demo mode
 */
const ProDemoNotice = () => (
	<Notice
		status="warning"
		isDismissible={false}
		className="ezd-pro-demo-notice"
	>
		<div className="ezd-pro-demo-notice-content">
			<strong>{__('Pro Feature Preview', 'eazydocs')}</strong>
			<p>
				{__('This style will not apply on the frontend.', 'eazydocs')}
			</p>
			<a className="ezd-upgrade-link" href={getPricingUrl()}>
				{__('Upgrade to Pro', 'eazydocs')} →
			</a>
		</div>
	</Notice>
);

/**
 * Pro Watermark - Large centered PRO text overlay with upgrade button
 */
const ProWatermark = () => (
	<div className="ezd-pro-watermark">
		<a className="ezd-pro-watermark-btn" href={getPricingUrl()}>
			{__('Upgrade to Pro', 'eazydocs')} →
		</a>
	</div>
);

/**
 * Upgrade Notice - For panel descriptions (not overlay)
 */
const UpgradeNotice = ({ feature }) => (
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
			<strong>{__('Pro Feature', 'eazydocs')}</strong>
			<p>{feature}</p>
			<a className="ezd-upgrade-link" href={getPricingUrl()}>
				{__('Upgrade to Pro', 'eazydocs')} →
			</a>
		</div>
	</div>
);

/**
 * Locked Control Wrapper - Grayed out with PRO watermark (like screenshot)
 */
const LockedControl = ({ children, feature, isPro = false }) => {
	if (isPro || isProActive()) {
		return children;
	}

	return (
		<div className="ezd-locked-control">
			<div className="ezd-locked-overlay">
				<ProWatermark />
			</div>
			<div className="ezd-locked-content">{children}</div>
		</div>
	);
};

/**
 * TabIcon Component - Fetches and displays featured image for a tab
 */
const TabIcon = ({ mediaId }) => {
	const media = useSelect(
		(select) => {
			if (!mediaId) return null;
			return select('core').getMedia(mediaId, { context: 'view' });
		},
		[mediaId]
	);

	if (!media) return null;

	return (
		<span className="ezd-tab-icon">
			<img
				src={media.source_url}
				alt={media.alt_text || ''}
				className="ezd-tab-icon-img"
			/>
		</span>
	);
};

/**
 * Helper function to extract doc IDs from form token values
 */
const doc_ids = (tokens) => {
	return tokens
		.map((token) => {
			const match = token.match(/^(\d+)/);
			return match ? parseInt(match[1], 10) : null;
		})
		.filter((id) => id !== null);
};

/**
 * Create dropdown options with Pro badges
 */
const createOptionsWithProBadge = (freeOptions, proOptions, isPro) => {
	const allOptions = [...freeOptions];

	proOptions.forEach((option) => {
		allOptions.push({
			...option,
			label: isPro ? option.label : `${option.label} ⭐ Pro`,
		});
	});

	return allOptions;
};

/**
 * Edit component
 */
export default function Edit({ attributes, setAttributes }) {
	const {
		preset,
		show_docs,
		sectionsNumber,
		articlesNumber,
		readMoreText,
		orderBy,
		parent_docs_order,
		child_docs_order,
		docs_layout,
		col,
		include,
		exclude,
		isFeaturedImage,
		excerptCharNumber,
		enableHoverAnimation,
		tabStyle,
		showDocCount,
		showTabIcon,
		cardStyle,
		animationSpeed,
		showLastUpdated,
		compactMode,
		// Advanced styling attributes
		primaryColor,
		cardPadding,
		cardGap,
		borderRadius,
		showTitleLine,
		buttonStyle,
		iconStyle,
		shadowIntensity,
	} = attributes;

	// Check Pro status
	const isPro = isProActive();

	// State for active tab in preview
	const [activeTab, setActiveTab] = useState(null);

	// Check if current selections are Pro features (for demo notice)
	const isUsingProTabStyle = !isPro && isProOption(tabStyle, proTabStyles);
	const isUsingProCardStyle = !isPro && isProOption(cardStyle, proCardStyles);
	const isUsingProLayout = !isPro && docs_layout === 'masonry';
	const isUsingProFeature =
		isUsingProTabStyle || isUsingProCardStyle || isUsingProLayout;

	// Fetch parent docs (those with parent === 0)
	const parentDocs = useSelect(
		(select) => {
			try {
				const args = {
					per_page: show_docs || -1,
					order: parent_docs_order || 'asc',
					orderby: orderBy === 'rand' ? 'title' : orderBy,
					parent: 0,
					status: ['publish', 'private'],
				};

				if (doc_ids(include || []).length > 0) {
					args.include = doc_ids(include);
				}

				// Only apply exclude if Pro is active
				if (isPro && doc_ids(exclude || []).length > 0) {
					args.exclude = doc_ids(exclude);
				}

				return select('core').getEntityRecords(
					'postType',
					'docs',
					args
				);
			} catch (error) {
				console.error('Error fetching parent docs:', error);
				return [];
			}
		},
		[show_docs, orderBy, parent_docs_order, include, exclude, isPro]
	);

	// Fetch all docs for suggestions and child docs
	const allDocs = useSelect(
		(select) => {
			return select('core').getEntityRecords('postType', 'docs', {
				per_page: -1,
				status: ['publish', 'private'],
				orderby: orderBy === 'rand' ? 'title' : orderBy,
				order: child_docs_order || 'desc',
			});
		},
		[orderBy, child_docs_order]
	);

	// Set initial active tab
	useEffect(() => {
		if (parentDocs && parentDocs.length > 0 && !activeTab) {
			setActiveTab(parentDocs[0].id);
		}
	}, [parentDocs]);

	// Get child docs for a parent
	const getChildDocs = (parentId) => {
		if (!allDocs) return [];
		return allDocs.filter((doc) => doc.parent === parentId);
	};

	// Get grandchild docs count for article count
	const getArticleCount = (parentId) => {
		if (!allDocs) return 0;
		const children = allDocs.filter((doc) => doc.parent === parentId);
		let count = children.length;
		children.forEach((child) => {
			count += allDocs.filter((doc) => doc.parent === child.id).length;
		});
		return count;
	};

	// Doc suggestions for include/exclude
	const docSuggestions = allDocs
		? allDocs
				.filter((doc) => doc.parent === 0)
				.map((doc) => `${doc.id} | ${doc.title.rendered}`)
		: [];

	// Order options
	const orderOptions = [
		{ label: __('Ascending', 'eazydocs'), value: 'asc' },
		{ label: __('Descending', 'eazydocs'), value: 'desc' },
	];

	const orderByOptions = [
		{ label: __('Title', 'eazydocs'), value: 'title' },
		{ label: __('Post Author', 'eazydocs'), value: 'author' },
		{ label: __('Date', 'eazydocs'), value: 'date' },
		{ label: __('Post ID', 'eazydocs'), value: 'id' },
		{ label: __('Last Modified Date', 'eazydocs'), value: 'modified' },
		{ label: __('Random', 'eazydocs'), value: 'rand' },
		{ label: __('Menu Order', 'eazydocs'), value: 'menu_order' },
	];

	// Free layout options
	const freeLayoutOptions = [
		{ label: __('Grid', 'eazydocs'), value: 'grid' },
	];

	// Pro layout options
	const proLayoutOptions = [
		{ label: __('Masonry', 'eazydocs'), value: 'masonry' },
	];

	// All layout options with Pro badge
	const layoutOptions = createOptionsWithProBadge(
		freeLayoutOptions,
		proLayoutOptions,
		isPro
	);

	// Free tab styles (basic options)
	const freeTabStyleOptions = [
		{ label: __('Default (Underline)', 'eazydocs'), value: 'default' },
		{ label: __('Rounded Pill', 'eazydocs'), value: 'rounded' },
	];

	// Pro tab styles (advanced options)
	const proTabStyleOptionsList = [
		{ label: __('Pill Style', 'eazydocs'), value: 'pill' },
		{ label: __('Boxed', 'eazydocs'), value: 'boxed' },
		{ label: __('Simple Underline', 'eazydocs'), value: 'underline' },
		{ label: __('Gradient', 'eazydocs'), value: 'gradient' },
		{ label: __('Glassmorphism', 'eazydocs'), value: 'glass' },
	];

	// All tab style options with Pro badges
	const tabStyleOptions = createOptionsWithProBadge(
		freeTabStyleOptions,
		proTabStyleOptionsList,
		isPro
	);

	// Free card styles
	const freeCardStyleOptions = [
		{ label: __('Elevated (Shadow)', 'eazydocs'), value: 'elevated' },
		{ label: __('Bordered', 'eazydocs'), value: 'bordered' },
	];

	// Pro card styles
	const proCardStyleOptionsList = [
		{ label: __('Flat', 'eazydocs'), value: 'flat' },
		{ label: __('Minimal', 'eazydocs'), value: 'minimal' },
		{ label: __('Glassmorphism', 'eazydocs'), value: 'glass' },
		{ label: __('Gradient Border', 'eazydocs'), value: 'gradient-border' },
	];

	// All card style options with Pro badges
	const cardStyleOptions = createOptionsWithProBadge(
		freeCardStyleOptions,
		proCardStyleOptionsList,
		isPro
	);

	const animationSpeedOptions = [
		{ label: __('Slow', 'eazydocs'), value: 'slow' },
		{ label: __('Normal', 'eazydocs'), value: 'normal' },
		{ label: __('Fast', 'eazydocs'), value: 'fast' },
	];

	const buttonStyleOptions = [
		{ label: __('Filled', 'eazydocs'), value: 'filled' },
		{ label: __('Outlined', 'eazydocs'), value: 'outlined' },
		{ label: __('Text Only', 'eazydocs'), value: 'text' },
	];

	const iconStyleOptions = [
		{ label: __('Rounded', 'eazydocs'), value: 'rounded' },
		{ label: __('Circle', 'eazydocs'), value: 'circle' },
		{ label: __('Square', 'eazydocs'), value: 'square' },
	];

	const shadowIntensityOptions = [
		{ label: __('None', 'eazydocs'), value: 'none' },
		{ label: __('Light', 'eazydocs'), value: 'light' },
		{ label: __('Medium', 'eazydocs'), value: 'medium' },
		{ label: __('Strong', 'eazydocs'), value: 'strong' },
	];

	// Build wrapper classes - Always apply selected styles for editor preview (demo mode)
	const wrapperClasses = [
		'ezd-tabbed-docs-editor',
		'ezd-tabbed-docs',
		`ezd-tab-style-${tabStyle || 'default'}`,
		`ezd-card-style-${cardStyle || 'elevated'}`,
		`ezd-animation-${animationSpeed || 'normal'}`,
		`ezd-shadow-${shadowIntensity || 'medium'}`,
		`ezd-button-${buttonStyle || 'filled'}`,
		`ezd-icon-${iconStyle || 'rounded'}`,
		enableHoverAnimation ? 'ezd-hover-enabled' : '',
		compactMode ? 'ezd-compact' : '',
		!showTitleLine ? 'ezd-no-title-line' : '',
		isUsingProFeature ? 'ezd-pro-demo-mode' : '',
	]
		.filter(Boolean)
		.join(' ');

	// Build inline styles for custom properties
	const customStyles = {
		'--ezd-primary': primaryColor || undefined,
		'--ezd-card-padding': cardPadding ? `${cardPadding}px` : undefined,
		'--ezd-grid-gap': cardGap ? `${cardGap}px` : undefined,
		'--ezd-card-radius': borderRadius ? `${borderRadius}px` : undefined,
	};

	const layoutClass =
		docs_layout === 'grid'
			? `ezd-grid ezd-column-${col} ezd-topic-list-inner`
			: `ezd-masonry-wrap ezd-masonry-col-${col} ezd-topic-list-inner`;

	// Loading state
	if (!parentDocs) {
		return (
			<div {...useBlockProps()}>
				<div className="ezd-tabbed-docs-loading">
					<Spinner />
					<p>{__('Loading documentation...', 'eazydocs')}</p>
				</div>
			</div>
		);
	}

	// Empty state
	if (parentDocs.length === 0) {
		return (
			<div {...useBlockProps()}>
				<div className="ezd-tabbed-docs-empty">
					<svg
						xmlns="http://www.w3.org/2000/svg"
						width="48"
						height="48"
						viewBox="0 0 24 24"
						fill="none"
						stroke="currentColor"
						strokeWidth="1.5"
					>
						<path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"></path>
						<polyline points="14 2 14 8 20 8"></polyline>
						<line x1="12" y1="18" x2="12" y2="12"></line>
						<line x1="9" y1="15" x2="15" y2="15"></line>
					</svg>
					<h4>{__('No Documentation Found', 'eazydocs')}</h4>
					<p>
						{__(
							'Create some documentation posts to display them here.',
							'eazydocs'
						)}
					</p>
				</div>
			</div>
		);
	}

	return (
		<>
			{/* Settings Tab - Content & Data Controls */}
			<InspectorControls>
				{/* Content Settings */}
				<PanelBody
					title={__('Content Settings', 'eazydocs')}
					initialOpen={true}
				>
					<NumberControl
						label={__('Number of Docs', 'eazydocs')}
						help={__(
							'Number of parent docs to show. Use -1 for all.',
							'eazydocs'
						)}
						value={show_docs}
						onChange={(value) =>
							setAttributes({ show_docs: value })
						}
						min={-1}
						step={1}
					/>

					<NumberControl
						label={__('Sections per Tab', 'eazydocs')}
						help={__(
							'Number of sections per tab. Use -1 for all.',
							'eazydocs'
						)}
						value={sectionsNumber}
						onChange={(value) =>
							setAttributes({ sectionsNumber: value })
						}
						min={-1}
						step={1}
					/>

					{/* Articles per Section - Pro Only */}
					<LockedControl
						feature={__(
							'Control the number of articles displayed per section.',
							'eazydocs'
						)}
						isPro={isPro}
					>
						<NumberControl
							label={
								<>
									{__('Articles per Section', 'eazydocs')}
									{!isPro && <ProBadge inline />}
								</>
							}
							help={__(
								'Number of articles per section. Use -1 for all.',
								'eazydocs'
							)}
							value={articlesNumber}
							onChange={(value) =>
								setAttributes({ articlesNumber: value })
							}
							min={-1}
							step={1}
						/>
					</LockedControl>

					<TextControl
						label={__('Read More Text', 'eazydocs')}
						value={readMoreText}
						onChange={(value) =>
							setAttributes({ readMoreText: value })
						}
					/>

					{exclude.length === 0 && (
						<FormTokenField
							__experimentalAutoSelectFirstMatch
							__experimentalExpandOnFocus
							label={__('Docs to Show', 'eazydocs')}
							help={__(
								'Select specific docs to show. Leave empty for all.',
								'eazydocs'
							)}
							suggestions={docSuggestions}
							value={include}
							onChange={(value) =>
								setAttributes({ include: value })
							}
						/>
					)}

					{/* Docs to Exclude - Pro Only */}
					{include.length === 0 && (
						<LockedControl
							feature={__(
								'Exclude specific documentation from display.',
								'eazydocs'
							)}
							isPro={isPro}
						>
							<FormTokenField
								__experimentalAutoSelectFirstMatch
								__experimentalExpandOnFocus
								label={
									<>
										{__('Docs to Exclude', 'eazydocs')}
										{!isPro && <ProBadge inline />}
									</>
								}
								help={__(
									'Select specific docs to exclude.',
									'eazydocs'
								)}
								suggestions={docSuggestions}
								value={exclude}
								onChange={(value) =>
									isPro && setAttributes({ exclude: value })
								}
							/>
						</LockedControl>
					)}
				</PanelBody>

				{/* Ordering */}
				<PanelBody
					title={__('Ordering', 'eazydocs')}
					initialOpen={false}
				>
					<SelectControl
						label={__('Order By', 'eazydocs')}
						value={orderBy}
						options={orderByOptions}
						onChange={(value) => setAttributes({ orderBy: value })}
						help={__(
							'Some options like Random may not preview correctly.',
							'eazydocs'
						)}
					/>

					<SelectControl
						label={__('Parent Docs Order', 'eazydocs')}
						value={parent_docs_order}
						options={orderOptions}
						onChange={(value) =>
							setAttributes({ parent_docs_order: value })
						}
					/>

					<SelectControl
						label={__('Child Docs Order', 'eazydocs')}
						value={child_docs_order}
						options={orderOptions}
						onChange={(value) =>
							setAttributes({ child_docs_order: value })
						}
					/>
				</PanelBody>
			</InspectorControls>

			{/* Styles Tab - Design & Appearance Controls */}
			<InspectorControls group="styles">
				{/* Layout Settings */}
				<PanelBody title={__('Layout', 'eazydocs')} initialOpen={true}>
					<RangeControl
						label={__('Columns', 'eazydocs')}
						value={col}
						onChange={(value) => setAttributes({ col: value })}
						min={1}
						max={6}
					/>

					{/* Layout Type with Pro option */}
					<SelectControl
						label={__('Layout Type', 'eazydocs')}
						value={docs_layout}
						options={layoutOptions}
						onChange={(value) =>
							setAttributes({ docs_layout: value })
						}
					/>

					{isUsingProLayout && <ProDemoNotice />}

					<ToggleControl
						label={__('Compact Mode', 'eazydocs')}
						help={__(
							'Reduce padding and spacing for compact display.',
							'eazydocs'
						)}
						checked={compactMode}
						onChange={(value) =>
							setAttributes({ compactMode: value })
						}
					/>
				</PanelBody>

				{/* Tab Style */}
				<PanelBody
					title={
						<>
							{__('Tab Style', 'eazydocs')}
						</>
					}
					initialOpen={false}
				>
					<SelectControl
						label={__('Style', 'eazydocs')}
						value={tabStyle}
						options={tabStyleOptions}
						onChange={(value) => setAttributes({ tabStyle: value })}
					/>

					{isUsingProTabStyle && <ProDemoNotice />}

					<ToggleControl
						label={__('Show Doc Count', 'eazydocs')}
						help={__(
							'Display article count badge on each tab.',
							'eazydocs'
						)}
						checked={showDocCount}
						onChange={(value) =>
							setAttributes({ showDocCount: value })
						}
					/>

					{/* Show Tab Featured Image - Pro Only */}
					<LockedControl
						feature={__(
							'Display featured images on tabs.',
							'eazydocs'
						)}
						isPro={isPro}
					>
						<ToggleControl
							label={
								<>
									{__('Show Tab Featured Image', 'eazydocs')}
									{!isPro && <ProBadge inline />}
								</>
							}
							help={__(
								'Display featured image/icon alongside tab titles.',
								'eazydocs'
							)}
							checked={showTabIcon}
							onChange={(value) =>
								isPro && setAttributes({ showTabIcon: value })
							}
						/>
					</LockedControl>
				</PanelBody>

				{/* Card Style */}
				<PanelBody
					title={
						<>
							{__('Card Style', 'eazydocs')}
						</>
					}
					initialOpen={false}
				>
					<SelectControl
						label={__('Style', 'eazydocs')}
						value={cardStyle}
						options={cardStyleOptions}
						onChange={(value) =>
							setAttributes({ cardStyle: value })
						}
					/>

					{isUsingProCardStyle && <ProDemoNotice />}

					{/* Show Featured Images - Pro Only */}
					<LockedControl
						feature={__(
							'Display featured images on section cards.',
							'eazydocs'
						)}
						isPro={isPro}
					>
						<ToggleControl
							label={
								<>
									{__('Show Featured Images', 'eazydocs')}
									{!isPro && <ProBadge inline />}
								</>
							}
							help={__(
								'Display featured images on section cards.',
								'eazydocs'
							)}
							checked={isFeaturedImage}
							onChange={(value) =>
								isPro &&
								setAttributes({ isFeaturedImage: value })
							}
						/>
					</LockedControl>

					<ToggleControl
						label={__('Show Last Updated', 'eazydocs')}
						help={__(
							'Display last updated date on articles.',
							'eazydocs'
						)}
						checked={showLastUpdated}
						onChange={(value) =>
							setAttributes({ showLastUpdated: value })
						}
					/>
				</PanelBody>

				{/* Animation */}
				<PanelBody
					title={__('Animation', 'eazydocs')}
					initialOpen={false}
				>
					<ToggleControl
						label={__('Enable Hover Animation', 'eazydocs')}
						help={__(
							enableHoverAnimation
								? 'Cards will animate on hover'
								: 'No hover animation',
							'eazydocs'
						)}
						checked={enableHoverAnimation}
						onChange={(value) =>
							setAttributes({ enableHoverAnimation: value })
						}
					/>

					<SelectControl
						label={__('Animation Speed', 'eazydocs')}
						value={animationSpeed}
						options={animationSpeedOptions}
						onChange={(value) =>
							setAttributes({ animationSpeed: value })
						}
					/>
				</PanelBody>

				{/* Advanced Styling - Pro Only */}
				<PanelBody
					title={
						<>
							{__('Advanced Styling', 'eazydocs')}
							{!isPro && <ProBadge />}
						</>
					}
					initialOpen={false}
				>
					{/* Single lock overlay for all advanced styling options */}
					<LockedControl
						feature={__('Advanced styling options', 'eazydocs')}
						isPro={isPro}
					>
						<BaseControl
							label={__('Primary Color', 'eazydocs')}
							help={__(
								'Customize the main accent color for tabs and highlights.',
								'eazydocs'
							)}
						>
							<ColorPalette
								value={primaryColor}
								onChange={(value) =>
									isPro &&
									setAttributes({ primaryColor: value })
								}
								clearable={true}
							/>
						</BaseControl>

						<Divider />

						<RangeControl
							label={__('Card Padding', 'eazydocs')}
							value={cardPadding}
							onChange={(value) =>
								isPro && setAttributes({ cardPadding: value })
							}
							min={8}
							max={64}
							step={4}
						/>

						<RangeControl
							label={__('Card Gap', 'eazydocs')}
							value={cardGap}
							onChange={(value) =>
								isPro && setAttributes({ cardGap: value })
							}
							min={8}
							max={48}
							step={4}
						/>

						<RangeControl
							label={__('Border Radius', 'eazydocs')}
							value={borderRadius}
							onChange={(value) =>
								isPro && setAttributes({ borderRadius: value })
							}
							min={0}
							max={32}
							step={2}
						/>

						<Divider />

						<ToggleControl
							label={__('Show Title Line', 'eazydocs')}
							help={__(
								'Display gradient line under section titles.',
								'eazydocs'
							)}
							checked={showTitleLine}
							onChange={(value) =>
								isPro && setAttributes({ showTitleLine: value })
							}
						/>

						<SelectControl
							label={__('Button Style', 'eazydocs')}
							value={buttonStyle}
							options={buttonStyleOptions}
							onChange={(value) =>
								isPro && setAttributes({ buttonStyle: value })
							}
						/>

						<SelectControl
							label={__('Tab Icon Style', 'eazydocs')}
							value={iconStyle}
							options={iconStyleOptions}
							onChange={(value) =>
								isPro && setAttributes({ iconStyle: value })
							}
							help={__(
								'Only visible when tab icons are enabled.',
								'eazydocs'
							)}
						/>

						<SelectControl
							label={__('Shadow Intensity', 'eazydocs')}
							value={shadowIntensity}
							options={shadowIntensityOptions}
							onChange={(value) =>
								isPro &&
								setAttributes({ shadowIntensity: value })
							}
						/>
					</LockedControl>
				</PanelBody>
			</InspectorControls>

			<div
				{...useBlockProps({
					className: wrapperClasses,
					style: customStyles,
				})}
			>
				{/* Pro Demo Mode Notice - shown above the block preview */}
				{isUsingProFeature && (
					<div className="ezd-pro-demo-banner">
						<div className="ezd-pro-demo-banner-icon">
							<svg
								xmlns="http://www.w3.org/2000/svg"
								width="18"
								height="18"
								viewBox="0 0 24 24"
								fill="currentColor"
							>
								<path d="M12 2L15.09 8.26L22 9.27L17 14.14L18.18 21.02L12 17.77L5.82 21.02L7 14.14L2 9.27L8.91 8.26L12 2Z" />
							</svg>
						</div>
						<div className="ezd-pro-demo-banner-content">
							<strong>
								{__('Pro Preview Mode', 'eazydocs')}
							</strong>
							<span>
								{__(
									'Upgrade to apply this style on the frontend.',
									'eazydocs'
								)}
							</span>
						</div>
						<a
							className="ezd-pro-demo-banner-btn"
							href={getPricingUrl()}
						>
							{__('Upgrade', 'eazydocs')}
						</a>
					</div>
				)}

				{/* Tab Navigation */}
				<div className="ezd-tabs-sliders">
					<span className="ezd-scroller-btn ezd-left ezd-inactive">
						<svg
							xmlns="http://www.w3.org/2000/svg"
							width="20"
							height="20"
							viewBox="0 0 24 24"
							fill="none"
							stroke="currentColor"
							strokeWidth="2"
						>
							<polyline points="15 18 9 12 15 6"></polyline>
						</svg>
					</span>

					<ul className="ezd-tab-menu ezd-slide-nav-tabs">
						{parentDocs.map((doc) => {
							const articleCount = showDocCount
								? getArticleCount(doc.id)
								: 0;
							const featuredMedia = doc.featured_media;

							return (
								<li className="ezd-nav-item" key={doc.id}>
									<button
										type="button"
										className={`ezd-nav-link ${
											activeTab === doc.id
												? 'ezd-active'
												: ''
										}`}
										onClick={() => setActiveTab(doc.id)}
									>
										{showTabIcon && featuredMedia > 0 && (
											<TabIcon mediaId={featuredMedia} />
										)}
										<span className="ezd-tab-text">
											{doc.title.rendered}
										</span>
										{showDocCount && articleCount > 0 && (
											<span className="ezd-tab-count">
												{articleCount}
											</span>
										)}
									</button>
								</li>
							);
						})}
					</ul>

					<span className="ezd-scroller-btn ezd-right ezd-inactive">
						<svg
							xmlns="http://www.w3.org/2000/svg"
							width="20"
							height="20"
							viewBox="0 0 24 24"
							fill="none"
							stroke="currentColor"
							strokeWidth="2"
						>
							<polyline points="9 18 15 12 9 6"></polyline>
						</svg>
					</span>
				</div>

				{/* Tab Content */}
				<div className="ezd-tab-content">
					{parentDocs.map((parentDoc) => {
						const childDocs = getChildDocs(parentDoc.id).slice(
							0,
							sectionsNumber === -1
								? undefined
								: Number(sectionsNumber)
						);

						return (
							<div
								className={`ezd-tab-pane ${
									activeTab === parentDoc.id
										? 'ezd-active'
										: ''
								}`}
								key={parentDoc.id}
							>
								{childDocs.length === 0 ? (
									<div className="ezd-no-sections">
										<svg
											xmlns="http://www.w3.org/2000/svg"
											width="48"
											height="48"
											viewBox="0 0 24 24"
											fill="none"
											stroke="currentColor"
											strokeWidth="1.5"
										>
											<path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"></path>
											<polyline points="14 2 14 8 20 8"></polyline>
										</svg>
										<p>
											{__(
												'No sections found in this documentation.',
												'eazydocs'
											)}
										</p>
									</div>
								) : (
									<div className={layoutClass}>
										{childDocs.map((section) => {
											// Apply articles limit - but always show in editor for demo
											const articlesLimit = isPro
												? articlesNumber
												: -1;
											const articles = allDocs
												? allDocs
														.filter(
															(doc) =>
																doc.parent ===
																section.id
														)
														.slice(
															0,
															articlesLimit === -1
																? undefined
																: Number(
																		articlesLimit
																	)
														)
												: [];

											return (
												<div
													key={section.id}
													className="ezd-section-card"
												>
													<div className="ezd-doc-tag-item">
														<div className="ezd-doc-tag-title">
															<h4 className="ezd-item-title">
																<a
																	href={
																		section.link
																	}
																	onClick={(
																		e
																	) =>
																		e.preventDefault()
																	}
																>
																	{
																		section
																			.title
																			.rendered
																	}
																</a>
																{articles.length >
																	0 && (
																	<span className="ezd-section-count">
																		{
																			articles.length
																		}{' '}
																		{articles.length ===
																		1
																			? __(
																					'article',
																					'eazydocs'
																				)
																			: __(
																					'articles',
																					'eazydocs'
																				)}
																	</span>
																)}
															</h4>
															<div className="ezd-line"></div>
														</div>

														{articles.length > 0 ? (
															<ul className="ezd-tag-list">
																{articles.map(
																	(
																		article
																	) => (
																		<li
																			key={
																				article.id
																			}
																		>
																			<a
																				href={
																					article.link
																				}
																				className="ezd-item-list-title"
																				onClick={(
																					e
																				) =>
																					e.preventDefault()
																				}
																			>
																				<svg
																					xmlns="http://www.w3.org/2000/svg"
																					width="16"
																					height="16"
																					viewBox="0 0 24 24"
																					fill="none"
																					stroke="currentColor"
																					strokeWidth="2"
																				>
																					<path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"></path>
																					<polyline points="14 2 14 8 20 8"></polyline>
																					<line
																						x1="16"
																						y1="13"
																						x2="8"
																						y2="13"
																					></line>
																					<line
																						x1="16"
																						y1="17"
																						x2="8"
																						y2="17"
																					></line>
																					<polyline points="10 9 9 9 8 9"></polyline>
																				</svg>
																				{
																					article
																						.title
																						.rendered
																				}
																			</a>
																		</li>
																	)
																)}
															</ul>
														) : (
															<p className="ezd-no-articles-msg">
																{__(
																	'No articles in this section yet.',
																	'eazydocs'
																)}
															</p>
														)}

														<a
															href={section.link}
															className="ezd-text-btn ezd-dark-btn"
															onClick={(e) =>
																e.preventDefault()
															}
														>
															{readMoreText ||
																__(
																	'View All',
																	'eazydocs'
																)}
															<svg
																xmlns="http://www.w3.org/2000/svg"
																width="16"
																height="16"
																viewBox="0 0 24 24"
																fill="none"
																stroke="currentColor"
																strokeWidth="2"
															>
																<line
																	x1="5"
																	y1="12"
																	x2="19"
																	y2="12"
																></line>
																<polyline points="12 5 19 12 12 19"></polyline>
															</svg>
														</a>
													</div>
												</div>
											);
										})}
									</div>
								)}
							</div>
						);
					})}
				</div>
			</div>
		</>
	);
}
