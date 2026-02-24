/**
 * Skeleton loader components for the Docs Builder.
 *
 * Replaces the generic spinner with content-aware skeleton
 * loaders for a better perceived performance.
 *
 * @package EazyDocs
 * @since   2.9.0
 */
import { __ } from '@wordpress/i18n';

/**
 * Individual skeleton line element.
 */
const SkeletonLine: React.FC< { width?: string; height?: string } > = ( {
	width = '100%',
	height = '14px',
} ) => (
	<div
		className="ezd-skeleton-line"
		style={ { width, height } }
		aria-hidden="true"
	></div>
);

/**
 * Skeleton for the header area.
 */
const HeaderSkeleton: React.FC = () => (
	<header className="easydocs-header-area">
		<div className="ezd-container-fluid">
			<div className="row alignment-center justify-content-between ml-0">
				<div className="navbar-left d-flex alignment-center">
					<div className="easydocs-logo-area">
						<SkeletonLine width="140px" height="20px" />
					</div>
					<div style={ { marginLeft: '15px' } }>
						<SkeletonLine width="100px" height="32px" />
					</div>
				</div>
				<div style={ { width: '240px' } }>
					<SkeletonLine width="100%" height="36px" />
				</div>
				<div className="navbar-right">
					<div className="d-flex" style={ { gap: '12px' } }>
						<SkeletonLine width="60px" height="24px" />
						<SkeletonLine width="24px" height="24px" />
						<SkeletonLine width="24px" height="24px" />
					</div>
				</div>
			</div>
		</div>
	</header>
);

/**
 * Skeleton for the sidebar parent docs list.
 */
const SidebarSkeleton: React.FC = () => (
	<div className="dd parent-nestable tab-menu short" style={ { flex: 3 } }>
		<ol className="easydocs-navbar" style={ { listStyle: 'none', padding: 0 } }>
			{ Array.from( { length: 5 } ).map( ( _, i ) => (
				<li
					key={ i }
					className="easydocs-navitem"
					style={ {
						padding: '12px 16px',
						display: 'flex',
						alignItems: 'center',
						gap: '10px',
					} }
				>
					<SkeletonLine width="16px" height="16px" />
					<SkeletonLine width={ `${ 60 + Math.random() * 40 }%` } height="16px" />
				</li>
			) ) }
		</ol>
	</div>
);

/**
 * Skeleton for the child docs content area.
 */
const ContentSkeleton: React.FC = () => (
	<div className="easydocs-tab-content" style={ { flex: 8 } }>
		<div className="easydocs-tab tab-active" style={ { padding: '20px' } }>
			<div className="easydocs-filter-container" style={ { marginBottom: '20px' } }>
				<div className="d-flex" style={ { gap: '8px' } }>
					{ Array.from( { length: 5 } ).map( ( _, i ) => (
						<SkeletonLine key={ i } width="100px" height="32px" />
					) ) }
				</div>
			</div>
			<div style={ { display: 'flex', flexDirection: 'column', gap: '8px' } }>
				{ Array.from( { length: 6 } ).map( ( _, i ) => (
					<div
						key={ i }
						style={ {
							padding: '14px 16px',
							borderRadius: '6px',
							display: 'flex',
							alignItems: 'center',
							gap: '12px',
						} }
					>
						<SkeletonLine width="16px" height="16px" />
						<SkeletonLine width={ `${ 40 + Math.random() * 30 }%` } height="16px" />
						<div style={ { marginLeft: 'auto' } }>
							<SkeletonLine width="80px" height="8px" />
						</div>
					</div>
				) ) }
			</div>
		</div>
	</div>
);

/**
 * Full page skeleton loader for the Docs Builder.
 */
const BuilderSkeleton: React.FC = () => (
	<div className="ezd_docs_builder ezd-skeleton-loading" role="status" aria-label={ __( 'Loading documentation builder...', 'eazydocs' ) }>
		<HeaderSkeleton />
		<main>
			<div className="easydocs-sidebar-menu">
				<div className="tab-container" style={ { display: 'flex' } }>
					<SidebarSkeleton />
					<ContentSkeleton />
				</div>
			</div>
		</main>
	</div>
);

export default BuilderSkeleton;
