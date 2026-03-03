/**
 * ChildDocs component – renders child doc tree for a parent doc tab.
 *
 * Uses @dnd-kit for React-native drag-and-drop reordering instead of
 * jQuery Nestable. Maintains the same serialization format for the
 * PHP backend and uses the existing useReorderDocs hook.
 *
 * @package EazyDocs
 * @since   2.9.0
 */
import React from 'react';
import { useMemo, useEffect, useCallback, useState } from '@wordpress/element';
import { __ } from '@wordpress/i18n';
import {
	DndContext,
	closestCenter,
	KeyboardSensor,
	PointerSensor,
	useSensor,
	useSensors,
	DragOverlay,
} from '@dnd-kit/core';
import type { DragStartEvent, DragEndEvent } from '@dnd-kit/core';
import {
	SortableContext,
	sortableKeyboardCoordinates,
	verticalListSortingStrategy,
} from '@dnd-kit/sortable';
import SortableDocItem from './SortableDocItem';
import { useCreateSection, useReorderDocs } from '../hooks/useBuilderData';
import { useSearch } from '../hooks/useSearch';
import { useToast } from '../hooks/useToast';
import { useRoute } from '../../hooks/use-route';
import { serializeTree, findNodePosition, findNodeById, isDescendant, removeNode, insertNode } from '../utils/tree-utils';
import DropIndicatorLine from './DropIndicatorLine';
import type { DropIndicator } from './DropIndicatorLine';
import type { ParentDoc, DocChild, Capabilities, BuilderUrls, RoleVisibilityConfig } from '../types';

declare const eazydocs_local_object: any;

interface ChildDocsProps {
	parent: ParentDoc;
	children: DocChild[];
	isActive: boolean;
	capabilities: Capabilities;
	isPremium: boolean;
	urls: BuilderUrls;
	currentTheme: string;
	roleVisibility: RoleVisibilityConfig;
}

interface FilterItem {
	key: string;
	label: string;
	icon: string;
	className: string;
}

const ChildDocs: React.FC< ChildDocsProps > = ( { parent, children, isActive, capabilities, isPremium, urls, roleVisibility } ) => {
	const { query, updateQuery } = useRoute();
	const activeFilter = useMemo( () => query.filter || 'all', [ query.filter ] );
	const [ expandState, setExpandState ] = useState< 'collapsed' | 'expanded' >( 'collapsed' );
	const createSection = useCreateSection();
	const reorderDocs = useReorderDocs();
	const { searchValue } = useSearch();
	const { showToast } = useToast();

	// localStorage key scoped to this parent doc.
	const storageKey = `ezd-collapsed-${ parent.id }`;

	// Track which items are collapsed.
	const [ collapsedIds, setCollapsedIds ] = useState< Set< number > >( () => {
		// Restore from localStorage if available.
		try {
			const saved = localStorage.getItem( storageKey );
			if ( saved ) {
				const parsed = JSON.parse( saved );
				if ( Array.isArray( parsed ) ) {
					return new Set< number >( parsed );
				}
			}
		} catch {
			// Ignore parse errors – fall through to default.
		}

		// Default: all items collapsed (matching the old nestable('collapseAll') behaviour).
		const ids = new Set< number >();
		const collectIds = ( items: DocChild[] ) => {
			items.forEach( ( item ) => {
				ids.add( item.id );
				collectIds( item.children );
			} );
		};
		collectIds( children );
		return ids;
	} );

	// Persist collapsed state to localStorage whenever it changes.
	useEffect( () => {
		try {
			localStorage.setItem( storageKey, JSON.stringify( [ ...collapsedIds ] ) );
		} catch {
			// Silently ignore storage errors (e.g. quota exceeded).
		}
	}, [ collapsedIds, storageKey ] );

	// Local state for optimistic reorder.
	const [ localChildren, setLocalChildren ] = useState< DocChild[] | null >( null );

	// The displayed children array (local override during drag, or server data).
	const displayChildren = localChildren || children;

	// Reset local state when server data changes.
	useEffect( () => {
		setLocalChildren( null );
	}, [ children ] );

	// Track dragging state for body class.
	const [ isDragging, setIsDragging ] = useState( false );
	const [ activeDragItem, setActiveDragItem ] = useState<{ id: string | number; doc: DocChild; depth: number; parentId: number } | null>(null);

	// Track the exact drop position for cross-container drops (without moving nodes).
	const [ dropIndicator, setDropIndicator ] = useState< DropIndicator | null >( null );

	/**
	 * Configure sensors for @dnd-kit.
	 */
	const sensors = useSensors(
		useSensor( PointerSensor, {
			activationConstraint: {
				distance: 5,
			},
		} ),
		useSensor( KeyboardSensor, {
			coordinateGetter: sortableKeyboardCoordinates,
		} )
	);

	/**
	 * Handle filter button click.
	 */
	const handleFilter = ( e: React.MouseEvent | React.KeyboardEvent, filter: string ): void => {
		e.preventDefault();
		updateQuery( { filter: filter === 'all' ? '' : filter } );
	};

	/**
	 * Filter items by status and search value.
	 * Currently operates on the root nodes.
	 */
	const getFilteredChildren = useCallback( (): DocChild[] => {
		let filtered = displayChildren;

		// Filter by status.
		if ( activeFilter !== 'all' ) {
			filtered = filtered.filter( ( child ) => {
				const status = child.hasPassword ? 'protected' : child.status;
				return status === activeFilter.replace( '.', '' );
			} );
		}

		// Filter by search value.
		if ( searchValue ) {
			const lower = searchValue.toLowerCase();
			filtered = filtered.filter( ( child ) =>
				child.title.toLowerCase().indexOf( lower ) > -1
			);
		}

		return filtered;
	}, [ displayChildren, activeFilter, searchValue ] );

	/**
	 * Handle Expand/Collapse All toggle.
	 */
	const handleExpandToggle = ( e: React.MouseEvent< HTMLButtonElement > ): void => {
		e.preventDefault();

		if ( expandState === 'collapsed' ) {
			// Expand all: clear the collapsed set.
			setCollapsedIds( new Set() );
			setExpandState( 'expanded' );
		} else {
			// Collapse all: add all items with children.
			const ids = new Set< number >();
			const collectIds = ( items: DocChild[] ) => {
				items.forEach( ( item ) => {
					ids.add( item.id );
					collectIds( item.children );
				} );
			};
			collectIds( displayChildren );
			setCollapsedIds( ids );
			setExpandState( 'collapsed' );
		}
	};

	/**
	 * Toggle collapse state for a single item.
	 */
	const handleToggleCollapse = useCallback( ( id: number ): void => {
		setCollapsedIds( ( prev ) => {
			const next = new Set( prev );
			if ( next.has( id ) ) {
				next.delete( id );
			} else {
				next.add( id );
			}
			return next;
		} );
	}, [] );

	/**
	 * Handle drag start – add body class for cursor styling.
	 */
	const handleDragStart = ( event: DragStartEvent ): void => {
		setIsDragging( true );
		document.body.classList.add( 'ezd-is-dragging' );
		if ( event.active.data.current ) {
			setActiveDragItem( {
				id: event.active.id,
				doc: event.active.data.current.doc,
				depth: event.active.data.current.depth,
				parentId: event.active.data.current.parentId,
			} );

			// Auto collapse to hide children during drag visualization.
			setCollapsedIds( ( prev ) => new Set( [ ...prev, Number( event.active.id ) ] ) );
		}
	};

	/**
	 * Cross-container tracking: compute the exact drop position indicator.
	 *
	 * Moving nodes in handleDragOver causes layout reflow which shifts hover
	 * targets, creating a flickering feedback loop.  Instead we show a line
	 * indicator at the exact drop position and defer the actual move to
	 * handleDragEnd.
	 */
	const handleDragOver = ( event: any ): void => {
		const { active, over } = event;

		// When the cursor moves into a gap (no droppable underneath), keep the
		// last indicator visible so it never flickers to nothing.
		if ( ! over ) {
			return;
		}

		const activeId = active.id as number;
		const overId = over.id;

		// Don't react when hovering directly over the dragged item itself.
		if ( Number( overId ) === activeId ) {
			return;
		}

		let overParentId: number | null = null;
		let overIndex = 0;

		if ( String( overId ).startsWith( 'container-' ) ) {
			// Cursor is inside the children body of a section – drop inside at end.
			overParentId = parseInt( String( overId ).replace( 'container-', '' ), 10 );
			const targetNode = findNodeById( displayChildren, overParentId );
			overIndex = targetNode && targetNode.children ? targetNode.children.length : 0;
		} else {
			// Cursor is over a section card (header or collapsed card).
			// Always treat as sibling positioning: above 50% = before, below = after.
			const overPos = findNodePosition( displayChildren, overId as number );
			if ( overPos ) {
				overParentId = overPos.parentId;

				const currentY = ( event.activatorEvent as PointerEvent )?.clientY
					? ( event.activatorEvent as PointerEvent ).clientY + ( event.delta?.y ?? 0 )
					: 0;
				const overRect = over.rect;
				const overTop = overRect?.top ?? 0;

				// Use the section header height (52px) as the boundary.
				// If within header area: top 50% = before, bottom 50% = after.
				const headerHeight = 52;
				const headerMidY = overTop + headerHeight / 2;
				overIndex = currentY > headerMidY ? overPos.index + 1 : overPos.index;
			}
		}

		if ( overParentId === null ) {
			return;
		}

		// Don't allow dropping into own descendants.
		if ( isDescendant( displayChildren, activeId, overParentId ) ) {
			setDropIndicator( null );
			return;
		}

		setDropIndicator( ( prev ) => {
			if ( prev && prev.parentId === overParentId && prev.index === overIndex ) {
				return prev;
			}
			return { parentId: overParentId as number, index: overIndex };
		} );
	};

	const handleDragCancel = (): void => {
		setIsDragging( false );
		document.body.classList.remove( 'ezd-is-dragging' );
		setActiveDragItem( null );
		setDropIndicator( null );
		setLocalChildren( null );
	};

	const handleDragEnd = ( event: DragEndEvent ): void => {
		setIsDragging( false );
		document.body.classList.remove( 'ezd-is-dragging' );

		const { active, over } = event;
		const currentIndicator = dropIndicator;

		// Clear drag state immediately.
		setActiveDragItem( null );
		setDropIndicator( null );

		if ( ! over ) {
			return;
		}

		const activeId = active.id as number;
		const overId = over.id;
		let finalTree = displayChildren;

		// Use the indicator's exact parentId and index for the move.
		if ( currentIndicator !== null ) {
			const { updated, removed } = removeNode( displayChildren, activeId );
			if ( removed ) {
				// After removing the active node, sibling indices may have shifted.
				// Re-check whether the target index needs adjustment.
				const activePos = findNodePosition( displayChildren, activeId );
				let adjustedIndex = currentIndicator.index;

				// If the removed item was in the same parent and before the target,
				// decrement the index by one to stay accurate.
				if ( activePos && activePos.parentId === currentIndicator.parentId && activePos.index < currentIndicator.index ) {
					adjustedIndex = Math.max( 0, adjustedIndex - 1 );
				}

				finalTree = insertNode( updated, currentIndicator.parentId, adjustedIndex, removed );
				setLocalChildren( finalTree );
			}
		}

		// No actual change.
		if ( finalTree === displayChildren ) {
			return;
		}

		// Serialize and save to database.
		const serialized = serializeTree( finalTree );
		reorderDocs.mutate(
			{
				data: window.JSON.stringify( serialized ),
				action: 'eaz_nestable_docs',
			},
			{
				onSuccess: () => showToast( __( 'Order saved', 'eazydocs' ) ),
				onError: () => {
					showToast( __( 'Failed to save order', 'eazydocs' ), 'error' );
					setLocalChildren( null );
				},
			}
		);
	};

    const handleAddSection = ( e: React.MouseEvent< HTMLButtonElement > ): void => {
		e.preventDefault();

		if ( typeof window.Swal !== 'undefined' ) {
			window.Swal.fire( {
				title: eazydocs_local_object.create_prompt_title,
				input: 'text',
				showCancelButton: true,
				inputAttributes: {
					name: 'section',
				},
				preConfirm: ( value: string ) => {
					if ( ! value ) {
						return false;
					}

					// Show loading state on the modal button.
					window.Swal.showLoading();

					return new Promise( ( resolve, reject ) => {
						createSection.mutate(
							{
								parentId: parent.id,
								title: value,
								nonce: parent.sectionNonce,
							},
							{
								onSuccess: ( response ) => {
									resolve( response );
								},
								onError: ( error ) => {
									reject( error );
								},
							}
						);
					} );
				},
				allowOutsideClick: () => ! window.Swal.isLoading(),
			} ).then( ( result: any ) => {
				if ( result.isConfirmed && result.value ) {
					if ( typeof window.Swal !== 'undefined' ) {
						window.Swal.fire( {
							title: __( 'Success!', 'eazydocs' ),
							text: __( 'Section created successfully.', 'eazydocs' ),
							icon: 'success',
							timer: 1500,
							showConfirmButton: false,
						} );
					}
				}
			} ).catch( () => {
				if ( typeof window.Swal !== 'undefined' ) {
					window.Swal.fire( {
						title: __( 'Error', 'eazydocs' ),
						text: __( 'Failed to create section.', 'eazydocs' ),
						icon: 'error',
					} );
				}
			} );
		}
	};

	const filters: FilterItem[] = [
		{ key: 'all', label: __( 'All articles', 'eazydocs' ), icon: 'media-document', className: 'easydocs-btn-black-light' },
		{ key: '.publish', label: __( 'Public', 'eazydocs' ), icon: 'admin-site-alt3', className: 'easydocs-btn-green-light' },
		{ key: '.private', label: __( 'Private', 'eazydocs' ), icon: 'privacy', className: 'easydocs-btn-blue-light' },
		{ key: '.protected', label: __( 'Protected', 'eazydocs' ), icon: 'lock', className: 'easydocs-btn-orange-light' },
		{ key: '.draft', label: __( 'Draft', 'eazydocs' ), icon: 'edit-page', className: 'easydocs-btn-gray-light' },
	];

	// Filter root items
	const rootItems = getFilteredChildren();
	const sortableIds = rootItems.map( ( item ) => item.id );

	return (
		<div
			className={ `easydocs-tab${ isActive ? ' tab-active' : '' }` }
			id={ `tab-${ parent.id }` }
			style={ { display: isActive ? '' : 'none' } }
		>
			<div className="easydocs-filter-container">
				<ul className="single-item-filter" role="group" aria-label={ __( 'Filter documentation by status', 'eazydocs' ) }>
					{ filters.map( ( filter ) => (
						<li
							key={ filter.key }
							className={ `easydocs-btn ${ filter.className } easydocs-btn-rounded easydocs-btn-sm${ activeFilter === filter.key ? ' is-active mixitup-control-active' : '' }` }
							data-filter={ filter.key }
							role="button"
							tabIndex={ 0 }
							aria-pressed={ activeFilter === filter.key ? 'true' : 'false' }
							aria-controls={ `nestable-${ parent.id }` }
							onClick={ ( e ) => handleFilter( e, filter.key ) }
							onKeyDown={ ( e ) => {
								if ( e.which === 13 || e.which === 32 ) {
									e.preventDefault();
									handleFilter( e, filter.key );
								}
							} }
						>
							<span className={ `dashicons dashicons-${ filter.icon }` }></span>
							{ filter.label }
						</li>
					) ) }
				</ul>

				<div className="ezd-toolbar-actions">
					<button
						type="button"
						className="ezd-toggle-expand-btn"
						data-state={ expandState }
						title={ expandState === 'collapsed'
							? __( 'Expand all sections', 'eazydocs' )
							: __( 'Collapse all sections', 'eazydocs' ) }
						onClick={ handleExpandToggle }
					>
						<span
							className={ `dashicons ${ expandState === 'collapsed' ? 'dashicons-arrow-down-alt2' : 'dashicons-arrow-up-alt2' }` }
							aria-hidden="true"
						></span>
						<span className="btn-text">
							{ expandState === 'collapsed'
								? __( 'Expand All', 'eazydocs' )
								: __( 'Collapse All', 'eazydocs' ) }
						</span>
					</button>
				</div>
			</div>

			<DndContext
				sensors={ sensors }
				collisionDetection={ closestCenter }
				onDragStart={ handleDragStart }
				onDragOver={ handleDragOver }
				onDragEnd={ handleDragEnd }
				onDragCancel={ handleDragCancel }
			>
				<SortableContext
					items={ sortableIds }
					strategy={ verticalListSortingStrategy }
				>
					<div
						className="ezd-section-list nestables-child"
						id={ `nestable-${ parent.id }` }
					>
						{ rootItems.map( ( child, index ) => (
							<React.Fragment key={ child.id }>
								{ dropIndicator && dropIndicator.parentId === 0 && dropIndicator.index === index && (
									<DropIndicatorLine />
								) }
								<SortableDocItem
									doc={ child }
									depth={ 1 }
									parentId={ parent.id }
									isPremium={ isPremium }
									capabilities={ capabilities }
									urls={ urls }
									roleVisibility={ roleVisibility }
									collapsedIds={ collapsedIds }
									onToggleCollapse={ handleToggleCollapse }
									dropIndicator={ dropIndicator }
									isDragActive={ activeDragItem !== null }
								/>
							</React.Fragment>
						) ) }
						{ dropIndicator && dropIndicator.parentId === 0 && dropIndicator.index === rootItems.length && (
							<DropIndicatorLine />
						) }
					</div>
				</SortableContext>
				<DragOverlay dropAnimation={{ duration: 250, easing: 'ease' }}>
					{ activeDragItem ? (
						<SortableDocItem
							doc={ activeDragItem.doc }
							depth={ activeDragItem.depth }
							parentId={ activeDragItem.parentId }
							isPremium={ isPremium }
							capabilities={ capabilities }
							urls={ urls }
							roleVisibility={ roleVisibility }
							collapsedIds={ collapsedIds }
							onToggleCollapse={ () => {} }
						/>
					) : null }
				</DragOverlay>
			</DndContext>

			{ capabilities.canPublishDocs && (
				<button
					type="button"
					className="ezd-add-sub-lesson-btn"
					aria-label={ __( 'Add sub-lesson', 'eazydocs' ) }
					onClick={ handleAddSection }
				>
					<span className="ezd-add-sub-lesson-icon" aria-hidden="true">+</span>
					{ __( 'Add sub-lesson', 'eazydocs' ) }
				</button>
			) }
		</div>
	);
};

export default ChildDocs;
