/**
 * SortableDocItem – wraps DocItemContent with @dnd-kit sortable functionality.
 *
 * Renders section cards for depth-1 items (with number badge, collapse toggle,
 * header actions) and simpler child rows for deeper items.
 *
 * @package EazyDocs
 * @since   2.9.0
 */
import React from 'react';
import { useSortable, SortableContext, verticalListSortingStrategy } from '@dnd-kit/sortable';
import { useDroppable } from '@dnd-kit/core';
import { CSS } from '@dnd-kit/utilities';
import { __ } from '@wordpress/i18n';
import DocItemContent from './DocItemContent';
import DropIndicatorLine from './DropIndicatorLine';
import type { DropIndicator } from './DropIndicatorLine';
import type { DocChild, Capabilities, BuilderUrls, RoleVisibilityConfig } from '../types';

/**
 * VotePieChart – Small filled pie chart showing positive vs negative votes.
 *
 * Renders filled SVG path slices (green for positive, red for negative).
 * Tooltip shows the raw counts on hover.
 *
 * @param {Object}  props
 * @param {number}  props.positive – Number of positive votes.
 * @param {number}  props.negative – Number of negative votes.
 * @since 2.9.0
 */
const VotePieChart: React.FC< { positive: number; negative: number } > = ( { positive, negative } ) => {
	const total = positive + negative;

	if ( total === 0 ) {
		return null;
	}

	const size   = 20;
	const cx     = size / 2;
	const cy     = size / 2;
	const r      = size / 2 - 1;

	const positiveRatio = Math.min( 0.9999, Math.max( 0.0001, positive / total ) );
	const angle         = positiveRatio * 2 * Math.PI;

	// Start from the top (12 o'clock = -90°).
	const startAngle = -Math.PI / 2;
	const endAngle   = startAngle + angle;

	const x1 = cx + r * Math.cos( startAngle );
	const y1 = cy + r * Math.sin( startAngle );
	const x2 = cx + r * Math.cos( endAngle );
	const y2 = cy + r * Math.sin( endAngle );

	const largeArc = angle > Math.PI ? 1 : 0;

	// Positive slice (green): top → clockwise by positiveRatio.
	const positivePath = `M ${ cx } ${ cy } L ${ x1 } ${ y1 } A ${ r } ${ r } 0 ${ largeArc } 1 ${ x2 } ${ y2 } Z`;
	// Negative slice (red): fills the remainder.
	const negativePath = `M ${ cx } ${ cy } L ${ x2 } ${ y2 } A ${ r } ${ r } 0 ${ 1 - largeArc } 1 ${ x1 } ${ y1 } Z`;

	// Tooltip text (counts only, no percentage).
	const tooltipText = `vote: ${ positive } positive, ${ negative } negative`;

	return (
		<div
			className="ezd-vote-pie"
			title={ tooltipText }
			aria-label={ tooltipText }
		>
			<svg
				width={ size }
				height={ size }
				viewBox={ `0 0 ${ size } ${ size }` }
				aria-hidden="true"
			>
				{ /* Background disc */ }
				<circle cx={ cx } cy={ cy } r={ r } fill="#e2e8f0" />
				{ /* Positive slice (green) */ }
				<path d={ positivePath } fill="#22c55e" />
				{ /* Negative slice (red) */ }
				{ negative > 0 && <path d={ negativePath } fill="#ef4444" /> }
			</svg>
		</div>
	);
};

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
	/** Drop position indicator for moves. */
	dropIndicator?: DropIndicator | null;
	/** When true, a drag is in progress – suppress sortable transforms. */
	isDragActive?: boolean;
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
	dropIndicator = null,
	isDragActive = false,
} ) => {
	const {
		attributes,
		listeners,
		setNodeRef: setSortableNodeRef,
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

    // This droppable allows us to drop items directly into this section's children list.
	const { setNodeRef: setDroppableNodeRef, isOver } = useDroppable( {
		id: `container-${ doc.id }`,
		data: {
			type: 'container',
			parentId: doc.id,
		},
	} );

	const isDropTarget = dropIndicator !== null && dropIndicator.parentId === doc.id;

	// Suppress @dnd-kit's built-in transforms for ALL items during drag – the
	// DragOverlay handles the visual cursor-following while the original item
	// stays in place at reduced opacity.
	const style: React.CSSProperties = {
		transform: isDragActive ? undefined : CSS.Transform.toString( transform ),
		transition: isDragActive ? undefined : transition,
		opacity: isDragging ? 0.35 : 1,
		position: 'relative' as const,
		boxShadow: 'none',
	};


	const hasChildren = doc.children && doc.children.length > 0;
	// Always treat as collapsible to allow dropping inside even if currently empty.
	const isCollapsed = collapsedIds.has( doc.id );

	const sectionClasses = [
		'ezd-section-card',
		isDragging ? 'dd-is-dragging' : '',
		isCollapsed ? 'ezd-section-collapsed' : '',
	].filter( Boolean ).join( ' ' );

    const childIds = doc.children ? doc.children.map( child => child.id ) : [];

	return (
		<div
			ref={ setSortableNodeRef }
			className={ sectionClasses }
			data-id={ doc.id }
			style={ style }
		>
			{ /* Section header */ }
			<div 
                className="ezd-section-header"
                onClick={ () => onToggleCollapse( doc.id ) }
                style={{ cursor: 'pointer', padding: '12px 16px' }}
            >
				<div className="ezd-section-header-left" style={{ display: 'flex', alignItems: 'center' }}>
					{ /* Drag handle */ }
					{ capabilities.canManageOptions && (
						<div
							ref={ setActivatorNodeRef }
							className="ezd-section-drag-handle"
							aria-label={ __( 'Drag to reorder this section', 'eazydocs' ) }
							title={ __( 'Drag to reorder', 'eazydocs' ) }
                            onClick={ (e) => e.stopPropagation() }
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

					{ /* Title */ }
					<h3 className="ezd-section-title-text" style={{ flex: 'none', display: 'flex', alignItems: 'center', gap: '8px' }}>
						{ doc.canEdit ? (
							<a 
                                href={ doc.editLink } 
                                target="_blank" 
                                rel="noopener noreferrer"
                                onClick={ (e) => e.stopPropagation() }
                            >
								{ doc.title }
							</a>
						) : (
							doc.title
						) }

						{ /* Subsections total count */ }
						{ doc.childCount > 0 && (
							<span className="ezd-section-number">
								{ doc.childCount }
							</span>
						) }

						{ /* Post status badge – hidden for published docs */ }
						{ doc.status !== 'publish' && ( () => {
							const statusMap: Record< string, { icon: string; label: string } > = {
								draft:     { icon: 'edit-page',      label: __( 'Draft', 'eazydocs' ) },
								private:   { icon: 'privacy',        label: __( 'Private', 'eazydocs' ) },
								protected: { icon: 'lock',           label: __( 'Protected', 'eazydocs' ) },
							};
							const entry = statusMap[ doc.status ];
							if ( ! entry ) {
								return null;
							}
							return (
								<span
									className={ `ezd-section-status-badge ezd-status-${ doc.status }` }
									title={ entry.label }
									aria-label={ entry.label }
								>
									<span className={ `dashicons dashicons-${ entry.icon }` } aria-hidden="true"></span>
								</span>
							);
						} )() }
					</h3>

					{ /* Header actions moved right next to the title */ }
					<div className="ezd-section-header-actions" onClick={ (e) => e.stopPropagation() }>
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

				<div style={{ flex: 1 }}></div>

				{ /* Vote pie chart */ }
				<VotePieChart positive={ doc.positive } negative={ doc.negative } />

                { /* Collapse toggle moved to the far right */ }
                <button
                    type="button"
                    className="ezd-section-collapse-btn"
                    aria-label={ isCollapsed ? __( 'Expand section', 'eazydocs' ) : __( 'Collapse section', 'eazydocs' ) }
                    onClick={ (e) => { e.stopPropagation(); onToggleCollapse( doc.id ); } }
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
			</div>

            { /* The children box */ }
            <div 
                ref={ setDroppableNodeRef } 
                className="ezd-section-children-box"
                style={{ 
                    display: isCollapsed ? 'none' : 'block',
                    padding: '0 24px',
                }}
            >
                <SortableContext items={ childIds } strategy={ verticalListSortingStrategy }>
                    <div className="ezd-section-list">
                        { doc.children && doc.children.map( ( child, index ) => (
                            <React.Fragment key={ child.id }>
                                { isDropTarget && dropIndicator && dropIndicator.index === index && (
                                    <DropIndicatorLine />
                                ) }
                                <SortableDocItem
                                    doc={ child }
                                    depth={ depth + 1 }
                                    parentId={ doc.id }
                                    isPremium={ isPremium }
                                    capabilities={ capabilities }
                                    urls={ urls }
                                    roleVisibility={ roleVisibility }
                                    collapsedIds={ collapsedIds }
                                    onToggleCollapse={ onToggleCollapse }
                                    dropIndicator={ dropIndicator }
                                    isDragActive={ isDragActive }
                                />
                            </React.Fragment>
                        ) ) }
                        { isDropTarget && dropIndicator && dropIndicator.index === ( doc.children?.length ?? 0 ) && (
                            <DropIndicatorLine />
                        ) }

                        { !hasChildren && (
                            <div className="ezd-empty-dropzone" style={{
                                padding: '24px',
                                textAlign: 'center',
                                border: '2px dashed #cbd5e1',
                                borderRadius: '6px',
                                color: '#64748b',
                                fontSize: '14px',
                                marginTop: '12px'
                            }}>
                                { __( 'Drop items here to make them a sub-section', 'eazydocs' ) }
                            </div>
                        ) }
                    </div>
                </SortableContext>
            </div>
		</div>
	);
};

export default SortableDocItem;
