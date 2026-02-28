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

	const style: React.CSSProperties = {
		transform: CSS.Transform.toString( transform ),
		transition,
		opacity: isDragging ? 0.35 : 1,
		position: 'relative' as const,
        marginBottom: '10px',
		boxShadow: 'none'
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
                style={{ cursor: 'pointer', padding: '12px 16px', background: isOver ? '#f1f5f9' : '' }}
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
                    display: isCollapsed || isDragging ? 'none' : 'block', // Collapsed during dragging!
                    padding: '0 24px',
                }}
            >
                <SortableContext items={ childIds } strategy={ verticalListSortingStrategy }>
                    <div className="ezd-section-list">
                        { doc.children && doc.children.map( ( child ) => (
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
