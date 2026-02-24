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
import { getItemIds, arrayMove, serializeTree, reorderInTree } from '../utils/tree-utils';
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

	// Track which items are collapsed.
	const [ collapsedIds, setCollapsedIds ] = useState< Set< number > >( () => {
		// Default: all items collapsed (matching the old nestable('collapseAll') behaviour).
		const ids = new Set< number >();
		const collectIds = ( items: DocChild[] ) => {
			items.forEach( ( item ) => {
				if ( item.children && item.children.length > 0 ) {
					ids.add( item.id );
					collectIds( item.children );
				}
			} );
		};
		collectIds( children );
		return ids;
	} );

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
					if ( item.children && item.children.length > 0 ) {
						ids.add( item.id );
						collectIds( item.children );
					}
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
	 * Handle "Add Section" button click.
	 */
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
		}
	};

	const handleDragCancel = (): void => {
		setIsDragging( false );
		document.body.classList.remove( 'ezd-is-dragging' );
		setActiveDragItem( null );
	};

	/**
	 * Handle drag end – reorder items and save to backend.
	 */
	const handleDragEnd = ( event: DragEndEvent ): void => {
		setIsDragging( false );
		document.body.classList.remove( 'ezd-is-dragging' );
		setActiveDragItem( null );

		const { active, over } = event;

		if ( ! over || active.id === over.id ) {
			return;
		}

		const activeId = active.id as number;
		const overId = over.id as number;
		const activeData = active.data.current;
		const overData = over.data.current;

		// Only reorder if items are at the same depth/parent.
		if ( activeData?.parentId !== overData?.parentId ) {
			return;
		}

		// Reorder in tree.
		const newTree = reorderInTree(
			displayChildren,
			activeId,
			overId,
			activeData?.parentId
		);

		if ( newTree ) {
			// Optimistic update.
			setLocalChildren( newTree );

			// Serialize and save.
			const serialized = serializeTree( newTree );
			reorderDocs.mutate(
				{
					data: window.JSON.stringify( serialized ),
					action: 'eaz_nestable_docs',
				},
				{
					onSuccess: () => showToast( __( 'Order saved', 'eazydocs' ) ),
					onError: () => {
						showToast( __( 'Failed to save order', 'eazydocs' ), 'error' );
						// Revert optimistic update.
						setLocalChildren( null );
					},
				}
			);
		}
	};

	const filters: FilterItem[] = [
		{ key: 'all', label: __( 'All articles', 'eazydocs' ), icon: 'media-document', className: 'easydocs-btn-black-light' },
		{ key: '.publish', label: __( 'Public', 'eazydocs' ), icon: 'admin-site-alt3', className: 'easydocs-btn-green-light' },
		{ key: '.private', label: __( 'Private', 'eazydocs' ), icon: 'privacy', className: 'easydocs-btn-blue-light' },
		{ key: '.protected', label: __( 'Protected', 'eazydocs' ), icon: 'lock', className: 'easydocs-btn-orange-light' },
		{ key: '.draft', label: __( 'Draft', 'eazydocs' ), icon: 'edit-page', className: 'easydocs-btn-gray-light' },
	];

	const filteredChildren = getFilteredChildren();
	const sortableIds = getItemIds( filteredChildren );

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
						{ filteredChildren.map( ( child, index ) => (
							<SortableDocItem
								key={ child.id }
								doc={ child }
								depth={ 1 }
								parentId={ parent.id }
								isPremium={ isPremium }
								capabilities={ capabilities }
								urls={ urls }
								roleVisibility={ roleVisibility }
								collapsedIds={ collapsedIds }
								onToggleCollapse={ handleToggleCollapse }
								orderIndex={ index + 1 }
							/>
						) ) }
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
