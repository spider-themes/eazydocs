/**
 * SortableDocItem – wraps DocItemContent with @dnd-kit sortable functionality.
 *
 * Renders section cards for depth-1 items (with number badge, collapse toggle,
 * header actions) and simpler child rows for deeper items.
 *
 * @package EazyDocs
 * @since   2.9.0
 */
import { useSortable } from '@dnd-kit/sortable';
import { CSS } from '@dnd-kit/utilities';
import { __ } from '@wordpress/i18n';
import DocItemContent from './DocItemContent';
import type { DocChild, Capabilities, BuilderUrls, RoleVisibilityConfig } from '../types';

interface SortableDocItemProps {
	doc: DocChild;
	depth: number;
	parentId: number;
	isPremium: boolean;
	capabilities: Capabilities;
	urls: BuilderUrls;
	roleVisibility?: RoleVisibilityConfig;
	collapsedIds: Set< number >;
	onToggleCollapse: ( id: number ) => void;
	orderIndex?: number;
}

const SortableDocItem: React.FC< SortableDocItemProps > = ( {
	doc,
	depth,
	parentId,
	isPremium,
	capabilities,
	urls,
	roleVisibility,
	collapsedIds,
	onToggleCollapse,
	orderIndex,
} ) => {
	const {
		attributes,
		listeners,
		setNodeRef,
		setActivatorNodeRef,
		transform,
		transition,
		isDragging,
	} = useSortable( {
		id: doc.id,
		data: {
			type: 'child-doc',
			doc,
			depth,
			parentId,
		},
	} );

	const style: React.CSSProperties = {
		transform: CSS.Transform.toString( transform ),
		transition,
		opacity: isDragging ? 0.35 : 1,
		position: 'relative' as const,
	};

	const hasChildren = doc.children && doc.children.length > 0;
	const isCollapsed = collapsedIds.has( doc.id );

	/**
	 * Render a depth-1 "section card".
	 *
	 * Each section is a white card with:
	 * - Header: drag handle · collapse chevron · numbered badge · title · + / trash icons
	 * - Body: list of child items (depth-2+)
	 */
	if ( depth === 1 ) {
		const sectionClasses = [
			'ezd-section-card',
			isDragging ? 'dd-is-dragging' : '',
			isCollapsed ? 'ezd-section-collapsed' : '',
		].filter( Boolean ).join( ' ' );

		return (
			<div
				ref={ setNodeRef }
				className={ sectionClasses }
				data-id={ doc.id }
				style={ style }
			>
				{ /* Section header */ }
				<div className="ezd-section-header">
					<div className="ezd-section-header-left">
						{ /* Drag handle */ }
						{ capabilities.canManageOptions && (
							<div
								ref={ setActivatorNodeRef }
								className="ezd-section-drag-handle"
								aria-label={ __( 'Drag to reorder this section', 'eazydocs' ) }
								title={ __( 'Drag to reorder', 'eazydocs' ) }
								{ ...attributes }
								{ ...listeners }
							>
								<svg className="dd-handle-icon" width="14" height="14" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg" aria-hidden="true">
									<circle cx="9" cy="5" r="2" fill="currentColor" />
									<circle cx="15" cy="5" r="2" fill="currentColor" />
									<circle cx="9" cy="12" r="2" fill="currentColor" />
									<circle cx="15" cy="12" r="2" fill="currentColor" />
									<circle cx="9" cy="19" r="2" fill="currentColor" />
									<circle cx="15" cy="19" r="2" fill="currentColor" />
								</svg>
							</div>
						) }

						{ /* Collapse toggle */ }
						{ hasChildren && (
							<button
								type="button"
								className="ezd-section-collapse-btn"
								aria-label={ isCollapsed ? __( 'Expand section', 'eazydocs' ) : __( 'Collapse section', 'eazydocs' ) }
								onClick={ () => onToggleCollapse( doc.id ) }
							>
								<svg
									className={ `ezd-section-chevron${ isCollapsed ? ' ezd-chevron-collapsed' : '' }` }
									width="16"
									height="16"
									viewBox="0 0 24 24"
									fill="none"
									xmlns="http://www.w3.org/2000/svg"
									aria-hidden="true"
								>
									<path d="M6 9l6 6 6-6" stroke="currentColor" strokeWidth="2" strokeLinecap="round" strokeLinejoin="round" />
								</svg>
							</button>
						) }

						{ /* Numbered badge */ }
						{ typeof orderIndex === 'number' && (
							<span className="ezd-section-number">
								{ orderIndex }
							</span>
						) }

						{ /* Title */ }
						<h3 className="ezd-section-title-text">
							{ doc.canEdit ? (
								<a href={ doc.editLink } target="_blank" rel="noopener noreferrer">
									{ doc.title }
								</a>
							) : (
								doc.title
							) }
						</h3>

						{ /* Header actions (moved next to title) */ }
						<div className="ezd-section-header-actions">
							<DocItemContent
								doc={ doc }
								depth={ depth }
								parentId={ parentId }
								isPremium={ isPremium }
								capabilities={ capabilities }
								urls={ urls }
								roleVisibility={ roleVisibility }
								renderMode="inline-actions"
							/>
						</div>
					</div>
				</div>

				{ /* Section body – children */ }
				{ hasChildren && ! isCollapsed && (
					<div className="ezd-section-body">
						{ doc.children.map( ( child ) => (
							<SortableDocItem
								key={ child.id }
								doc={ child }
								depth={ depth + 1 }
								parentId={ doc.id }
								isPremium={ isPremium }
								capabilities={ capabilities }
								urls={ urls }
								roleVisibility={ roleVisibility }
								collapsedIds={ collapsedIds }
								onToggleCollapse={ onToggleCollapse }
							/>
						) ) }
					</div>
				) }
			</div>
		);
	}

	/**
	 * Render a depth-2+ child item row.
	 *
	 * Simple row: drag handle · status dot · title · (hover actions)
	 */
	const statusClass = doc.hasPassword ? 'protected' : doc.status;
	const childClasses = [
		'ezd-child-row',
		`ezd-child-depth-${ depth }`,
		`ezd-status-${ statusClass }`,
		isDragging ? 'dd-is-dragging' : '',
		hasChildren ? 'ezd-has-children' : '',
		isCollapsed ? 'ezd-child-collapsed' : '',
	].filter( Boolean ).join( ' ' );

	return (
		<div
			ref={ setNodeRef }
			className={ childClasses }
			data-id={ doc.id }
			style={ style }
		>
			<div className="ezd-child-row-inner">
				{ /* Drag handle */ }
				{ capabilities.canManageOptions && (
					<div
						ref={ setActivatorNodeRef }
						className="ezd-child-drag-handle"
						aria-label={ __( 'Drag to reorder', 'eazydocs' ) }
						title={ __( 'Drag to reorder', 'eazydocs' ) }
						{ ...attributes }
						{ ...listeners }
					>
						<svg className="dd-handle-icon" width="12" height="12" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg" aria-hidden="true">
							<circle cx="9" cy="5" r="2" fill="currentColor" />
							<circle cx="15" cy="5" r="2" fill="currentColor" />
							<circle cx="9" cy="12" r="2" fill="currentColor" />
							<circle cx="15" cy="12" r="2" fill="currentColor" />
							<circle cx="9" cy="19" r="2" fill="currentColor" />
							<circle cx="15" cy="19" r="2" fill="currentColor" />
						</svg>
					</div>
				) }

				{ /* Status dot */ }
				<span className={ `ezd-status-dot ezd-dot-${ statusClass }` } aria-hidden="true"></span>

				{ /* Title */ }
				<span className="ezd-child-title">
					{ doc.canEdit ? (
						<a href={ doc.editLink } target="_blank" rel="noopener noreferrer">
							{ doc.title }
						</a>
					) : (
						doc.title
					) }
				</span>

				{ /* Hover actions */ }
				<div className="ezd-child-actions">
					<DocItemContent
						doc={ doc }
						depth={ depth }
						parentId={ parentId }
						isPremium={ isPremium }
						capabilities={ capabilities }
						urls={ urls }
						roleVisibility={ roleVisibility }
						renderMode="inline-actions"
					/>
				</div>
			</div>

			{ /* Nested children */ }
			{ hasChildren && ! isCollapsed && (
				<div className="ezd-child-nested">
					{ doc.children.map( ( child ) => (
						<SortableDocItem
							key={ child.id }
							doc={ child }
							depth={ depth + 1 }
							parentId={ doc.id }
							isPremium={ isPremium }
							capabilities={ capabilities }
							urls={ urls }
							roleVisibility={ roleVisibility }
							collapsedIds={ collapsedIds }
							onToggleCollapse={ onToggleCollapse }
						/>
					) ) }
				</div>
			) }
		</div>
	);
};

export default SortableDocItem;
