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
import { serializeTree, findNodePosition, findNodeById, isDescendant, removeNode, insertNode, filterDocTree } from '../utils/tree-utils';
import { promptForDocTitle, showCreateSuccess, showCreateError } from '../utils/prompt';
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

const ChildDocsComponent: React.FC< ChildDocsProps > = ( { parent, children, isActive, capabilities, isPremium, urls, roleVisibility } ) => {
	const { query, updateQuery } = useRoute();
	const activeFilter = useMemo( () => query.filter || 'all', [ query.filter ] );
	const createSection = useCreateSection();
	const reorderDocs = useReorderDocs();
	const { searchValue, setSearchValue } = useSearch();
	const { showToast } = useToast();

	// Whether a search term is currently active.
	const isSearching = searchValue.trim().length > 0;

	// Whether any non-default filter/search is narrowing the list.
	const isFiltering = isSearching || activeFilter !== 'all';

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

		// Default: at every level only the first item is open; all others collapsed.
		const ids = new Set< number >();

		const collapseAllDescendants = ( items: DocChild[] ): void => {
			items.forEach( ( item ) => {
				if ( item.canAddChild ) {
					ids.add( item.id );
				}
				collapseAllDescendants( item.children );
			} );
		};

		const applyFirstOpenRule = ( items: DocChild[] ): void => {
			items.forEach( ( item, index ) => {
				if ( index === 0 ) {
					// First item stays open; recurse into its children with the same rule.
					applyFirstOpenRule( item.children );
				} else {
					// Non-first items are collapsed along with all their descendants.
					if ( item.canAddChild ) {
						ids.add( item.id );
					}
					collapseAllDescendants( item.children );
				}
			} );
		};

		applyFirstOpenRule( children );
		return ids;
	} );

	// Persist collapsed state to localStorage whenever it changes.
	useEffect( () => {
		try {
			localStorage.removeItem( `ezd-collapsed-v2-${ parent.id }` );
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
	const handleFilter = useCallback( ( e: React.MouseEvent | React.KeyboardEvent, filter: string ): void => {
		e.preventDefault();
		updateQuery( { filter: filter === 'all' ? '' : filter } );
	}, [ updateQuery ] );

	/**
	 * Filter the full doc tree by status and search value.
	 *
	 * Operates at every depth: a node is kept when it matches the active
	 * status filter AND the search term, or when one of its descendants
	 * does (so the ancestor stays visible). This lets search find deeply
	 * nested docs instead of only root-level items.
	 */
	const getFilteredChildren = useCallback( (): DocChild[] => {
		if ( activeFilter === 'all' && ! isSearching ) {
			return displayChildren;
		}

		const lower = searchValue.trim().toLowerCase();
		const statusKey = activeFilter.replace( '.', '' );

		return filterDocTree( displayChildren, ( node ) => {
			const statusMatch =
				activeFilter === 'all' ||
				( node.hasPassword ? 'protected' : node.status ) === statusKey;
			const searchMatch = ! lower || node.title.toLowerCase().indexOf( lower ) > -1;
			return statusMatch && searchMatch;
		} );
	}, [ displayChildren, activeFilter, searchValue, isSearching ] );

	// Derive the toggle state from the actual collapsed set so the button
	// label always reflects reality (even when restored from localStorage).
	const allExpanded = useMemo( () => collapsedIds.size === 0, [ collapsedIds ] );

	/**
	 * Handle Expand/Collapse All toggle.
	 */
	const handleExpandToggle = ( e: React.MouseEvent< HTMLButtonElement > ): void => {
		e.preventDefault();

		if ( ! allExpanded ) {
			// Expand all: clear the collapsed set.
			setCollapsedIds( new Set() );
		} else {
			// Collapse all: add every item that can have children.
			const ids = new Set< number >();
			const collectIds = ( items: DocChild[] ) => {
				items.forEach( ( item ) => {
					ids.add( item.id );
					collectIds( item.children );
				} );
			};
			collectIds( displayChildren );
			setCollapsedIds( ids );
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
	const handleDragStart = useCallback( ( event: DragStartEvent ): void => {
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
	}, [] );

	/**
	 * Cross-container tracking: compute the exact drop position indicator.
	 *
	 * Moving nodes in handleDragOver causes layout reflow which shifts hover
	 * targets, creating a flickering feedback loop.  Instead we show a line
	 * indicator at the exact drop position and defer the actual move to
	 * handleDragEnd.
	 */
	const handleDragOver = useCallback( ( event: any ): void => {
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
	}, [ displayChildren ] );

	const handleDragCancel = useCallback( (): void => {
		setIsDragging( false );
		document.body.classList.remove( 'ezd-is-dragging' );
		setActiveDragItem( null );
		setDropIndicator( null );
		setLocalChildren( null );
	}, [] );

	const handleDragEnd = useCallback( ( event: DragEndEvent ): void => {
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
	}, [ displayChildren, dropIndicator, reorderDocs, showToast ] );

	const handleAddSection = useCallback( async ( e: React.MouseEvent< HTMLButtonElement > ): Promise< void > => {
		e.preventDefault();

		// Guard against double submissions while a create is in flight.
		if ( createSection.isPending ) {
			return;
		}

		const prompt = await promptForDocTitle();
		if ( ! prompt ) {
			return;
		}

		createSection.mutate(
			{
				parentId: parent.id,
				title: prompt.title,
				nonce: parent.sectionNonce,
				postStatus: prompt.status,
			},
			{
				onSuccess: () => {
					showCreateSuccess(
						'draft' === prompt.status
							? __( 'Section saved as draft.', 'eazydocs' )
							: __( 'Section created successfully.', 'eazydocs' )
					);
				},
				onError: () => {
					showCreateError( __( 'Failed to create section.', 'eazydocs' ) );
				},
			}
		);
	}, [ createSection, parent.id, parent.sectionNonce ] );

	/**
	 * Clear the active search term and status filter at once.
	 */
	const handleClearFilters = useCallback( (): void => {
		setSearchValue( '' );
		updateQuery( { filter: '' } );
	}, [ setSearchValue, updateQuery ] );

	const filters: FilterItem[] = useMemo( () => [
		{ key: 'all', label: __( 'All Docs', 'eazydocs' ), icon: 'media-document', className: 'easydocs-btn-black-light' },
		{ key: '.publish', label: __( 'Public', 'eazydocs' ), icon: 'admin-site-alt3', className: 'easydocs-btn-green-light' },
		{ key: '.private', label: __( 'Private', 'eazydocs' ), icon: 'privacy', className: 'easydocs-btn-blue-light' },
		{ key: '.protected', label: __( 'Protected', 'eazydocs' ), icon: 'lock', className: 'easydocs-btn-orange-light' },
		{ key: '.draft', label: __( 'Draft', 'eazydocs' ), icon: 'edit-page', className: 'easydocs-btn-gray-light' },
	], [] );

	// Filter root items
	const rootItems = useMemo( () => getFilteredChildren(), [ getFilteredChildren ] );
	const sortableIds = useMemo( () => rootItems.map( ( item ) => item.id ), [ rootItems ] );

	// While searching, expand every branch so matches are never hidden behind
	// a collapsed ancestor. The user's manual collapsed state is preserved and
	// restored automatically once the search is cleared.
	const emptyCollapsed = useMemo( () => new Set< number >(), [] );
	const effectiveCollapsedIds = isSearching ? emptyCollapsed : collapsedIds;

	// The active doc has no sections at all (as opposed to none matching a filter).
	const sectionIsEmpty = displayChildren.length === 0;

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
						data-state={ allExpanded ? 'expanded' : 'collapsed' }
						title={ allExpanded
							? __( 'Collapse all sections', 'eazydocs' )
							: __( 'Expand all sections', 'eazydocs' ) }
						onClick={ handleExpandToggle }
					>
						<span
							className={ `dashicons ${ allExpanded ? 'dashicons-arrow-up-alt2' : 'dashicons-arrow-down-alt2' }` }
							aria-hidden="true"
						></span>
						<span className="btn-text">
							{ allExpanded
								? __( 'Collapse All', 'eazydocs' )
								: __( 'Expand All', 'eazydocs' ) }
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
									rootParentId={ parent.id }
									isPremium={ isPremium }
									capabilities={ capabilities }
									urls={ urls }
									roleVisibility={ roleVisibility }
									collapsedIds={ effectiveCollapsedIds }
									onToggleCollapse={ handleToggleCollapse }
									dropIndicator={ dropIndicator }
									isDragActive={ activeDragItem !== null }
								/>
							</React.Fragment>
						) ) }
						{ dropIndicator && dropIndicator.parentId === 0 && dropIndicator.index === rootItems.length && (
							<DropIndicatorLine />
						) }

						{ rootItems.length === 0 && isFiltering && (
							<div className="ezd-builder-empty ezd-builder-empty--no-results">
								<span className="dashicons dashicons-search ezd-builder-empty__icon" aria-hidden="true"></span>
								<p className="ezd-builder-empty__title">
									{ isSearching
										? __( 'No docs match your search.', 'eazydocs' )
										: __( 'No docs match this filter.', 'eazydocs' ) }
								</p>
								<button
									type="button"
									className="ezd-builder-empty__action"
									onClick={ handleClearFilters }
								>
									{ __( 'Clear search & filters', 'eazydocs' ) }
								</button>
							</div>
						) }

						{ sectionIsEmpty && ! isFiltering && (
							<div className="ezd-builder-empty ezd-builder-empty--no-sections">
								<span className="dashicons dashicons-portfolio ezd-builder-empty__icon" aria-hidden="true"></span>
								<p className="ezd-builder-empty__title">
									{ __( 'This doc has no sections yet.', 'eazydocs' ) }
								</p>
								<p className="ezd-builder-empty__desc">
									{ __( 'Add your first section to start organising articles under this doc.', 'eazydocs' ) }
								</p>
							</div>
						) }
					</div>
				</SortableContext>
				<DragOverlay dropAnimation={{ duration: 250, easing: 'ease' }}>
					{ activeDragItem ? (
						<SortableDocItem
							doc={ activeDragItem.doc }
							depth={ activeDragItem.depth }
							parentId={ activeDragItem.parentId }
							rootParentId={ parent.id }
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
					aria-label={ __( 'Add section', 'eazydocs' ) }
					onClick={ handleAddSection }
					disabled={ createSection.isPending }
					aria-busy={ createSection.isPending }
				>
					<span className="ezd-add-sub-lesson-icon" aria-hidden="true">+</span>
					{ createSection.isPending ? __( 'Adding…', 'eazydocs' ) : __( 'Add Section', 'eazydocs' ) }
				</button>
			) }
		</div>
	);
};

const ChildDocs = React.memo( ChildDocsComponent, ( prevProps, nextProps ) => {
	return prevProps.parent === nextProps.parent
		&& prevProps.children === nextProps.children
		&& prevProps.isActive === nextProps.isActive
		&& prevProps.capabilities === nextProps.capabilities
		&& prevProps.isPremium === nextProps.isPremium
		&& prevProps.urls === nextProps.urls
		&& prevProps.roleVisibility === nextProps.roleVisibility;
} );

export default ChildDocs;
