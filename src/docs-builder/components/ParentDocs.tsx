/**
 * ParentDocs sidebar component.
 *
 * Renders the left sidebar list of parent docs with @dnd-kit
 * for React-native drag-and-drop reordering.
 *
 * @package EazyDocs
 * @since   2.9.0
 */
import { useState, useEffect, useMemo } from '@wordpress/element';
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
	useSortable,
} from '@dnd-kit/sortable';
import { CSS } from '@dnd-kit/utilities';
import { useDeleteDoc, useReorderDocs } from '../hooks/useBuilderData';
import { useSearch } from '../hooks/useSearch';
import { useToast } from '../hooks/useToast';
import { arrayMove } from '../utils/tree-utils';
import ProActionsButtons from './ProActionsButtons';
import type { ParentDoc, Capabilities, BuilderUrls, RoleVisibilityConfig } from '../types';

declare const eazydocs_local_object: any;

interface ParentDocsProps {
	parentDocs: ParentDoc[];
	activeTab: number | null;
	onTabChange: ( docId: number ) => void;
	capabilities: Capabilities;
	isPremium: boolean;
	urls: BuilderUrls;
	roleVisibility: RoleVisibilityConfig;
}

/**
 * A single sortable parent doc nav item.
 */
interface SortableParentItemProps {
	doc: ParentDoc;
	isActive: boolean;
	isPremium: boolean;
	capabilities: Capabilities;
	urls: BuilderUrls;
	roleVisibility: RoleVisibilityConfig;
	openBulk: number | null;
	onNavClick: ( e: React.MouseEvent< HTMLLIElement >, docId: number ) => void;
	onNavKeyDown: ( e: React.KeyboardEvent< HTMLLIElement >, docId: number ) => void;
	onDelete: ( e: React.MouseEvent< HTMLAnchorElement >, doc: ParentDoc ) => void;
	onBulkToggle: ( e: React.MouseEvent< HTMLSpanElement > | React.KeyboardEvent< HTMLSpanElement >, docId: number ) => void;
}

const SortableParentItem: React.FC< SortableParentItemProps > = ( {
	doc,
	isActive,
	isPremium,
	capabilities,
	urls,
	roleVisibility,
	openBulk,
	onNavClick,
	onNavKeyDown,
	onDelete,
	onBulkToggle,
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
			type: 'parent-doc',
			doc,
		},
	} );

	const style: React.CSSProperties = {
		transform: CSS.Transform.toString( transform ),
		transition,
		opacity: isDragging ? 0.4 : 1,
		position: 'relative' as const,
	};

	// Determine status icon.
	let postFormat = doc.statusIcon;
	let docStatus = doc.statusText;

	if ( doc.hasPassword ) {
		postFormat = 'lock';
		docStatus = __( 'Password Protected Doc', 'eazydocs' );
	}

	return (
		<li
			ref={ setNodeRef }
			key={ doc.id }
			className={ `easydocs-navitem dd-item dd3-item${ isActive ? ' active' : '' }${ isDragging ? ' dd-is-dragging' : '' }` }
			data-rel={ `tab-${ doc.id }` }
			data-id={ doc.id }
			role="tab"
			tabIndex={ 0 }
			aria-selected={ isActive ? 'true' : 'false' }
			aria-controls={ `tab-${ doc.id }` }
			onClick={ ( e ) => onNavClick( e, doc.id ) }
			onKeyDown={ ( e ) => onNavKeyDown( e, doc.id ) }
			style={ style }
		>
			<div className="title">
				<span
					title={ docStatus }
					className={ `dashicons dashicons-${ postFormat }` }
				></span>
				{ doc.title }
			</div>
			<div className="total-page">
				<span>
					{ doc.childCount > 0 ? doc.childCount : '' }
				</span>
			</div>
			<div className="link link-wrapper">
				{ isPremium && capabilities.canManageOptions && (
					<span
						ref={ setActivatorNodeRef }
						className="dd-handle dd3-handle"
						aria-label={ __( 'Drag to reorder', 'eazydocs' ) }
						title={ __( 'Drag to reorder', 'eazydocs' ) }
						{ ...attributes }
						{ ...listeners }
					>
						<svg className="dd-handle-icon" width="16" height="16" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg" aria-hidden="true">
							<circle cx="9" cy="5" r="2" fill="currentColor" />
							<circle cx="15" cy="5" r="2" fill="currentColor" />
							<circle cx="9" cy="12" r="2" fill="currentColor" />
							<circle cx="15" cy="12" r="2" fill="currentColor" />
							<circle cx="9" cy="19" r="2" fill="currentColor" />
							<circle cx="15" cy="19" r="2" fill="currentColor" />
						</svg>
					</span>
				) }

				{ doc.canEdit && (
					<a
						href={ doc.editLink }
						className="link edit"
						target="_blank"
						rel="noopener noreferrer"
						aria-label={ __( 'Edit this doc', 'eazydocs' ) }
						title={ __( 'Edit this doc', 'eazydocs' ) }
						onClick={ ( e ) => e.stopPropagation() }
					>
						<span className="dashicons dashicons-edit"></span>
					</a>
				) }

				<a
					href={ doc.permalink }
					className="link external-link"
					target="_blank"
					rel="noopener noreferrer"
					data-id={ `tab-${ doc.id }` }
					aria-label={ __( 'View this doc item in new tab', 'eazydocs' ) }
					title={ __( 'View this doc item in new tab', 'eazydocs' ) }
					onClick={ ( e ) => e.stopPropagation() }
				>
					<span className="dashicons dashicons-external"></span>
				</a>

				{ doc.canDelete && (
					<a
						href="#"
						className="link delete parent-delete"
						aria-label={ __( 'Move to Trash', 'eazydocs' ) }
						title={ __( 'Move to Trash', 'eazydocs' ) }
						onClick={ ( e ) => onDelete( e, doc ) }
					>
						<span className="dashicons dashicons-trash"></span>
					</a>
				) }

				{ capabilities.canManageOptions && (
					<span
						className={ `ezd-admin-bulk-options link${ openBulk === doc.id ? ' active' : '' }` }
						id={ `bulk-options-${ doc.id }` }
						role="button"
						tabIndex={ 0 }
						aria-label={ __( 'More options', 'eazydocs' ) }
						aria-expanded={ openBulk === doc.id }
						onClick={ ( e ) => onBulkToggle( e, doc.id ) }
						onKeyDown={ ( e ) => onBulkToggle( e, doc.id ) }
					>
						<span className={ `dashicons dashicons-arrow-down-alt2${ openBulk === doc.id ? ' arrow-active' : '' }` } aria-hidden="true"></span>
						<span className="ezd-admin-bulk-actions">
							<ProActionsButtons
								docId={ doc.id }
								proActions={ doc.proActions }
								isPremium={ isPremium }
								urls={ urls }
								roleVisibility={ roleVisibility }
								context="parent"
							/>
						</span>
					</span>
				) }
			</div>
		</li>
	);
};

const ParentDocs: React.FC< ParentDocsProps > = ( { parentDocs, activeTab, onTabChange, capabilities, isPremium, urls, roleVisibility } ) => {
	const [ openBulk, setOpenBulk ] = useState< number | null >( null );
	const deleteDoc = useDeleteDoc();
	const reorderDocs = useReorderDocs();
	const { searchValue } = useSearch();
	const { showToast } = useToast();
	const [ activeDragItem, setActiveDragItem ] = useState<{ id: string | number; doc: ParentDoc } | null>( null );

	// Local state for optimistic reorder.
	const [ localDocs, setLocalDocs ] = useState< ParentDoc[] | null >( null );
	const displayDocs = localDocs || parentDocs;

	// Reset local state when server data changes.
	useEffect( () => {
		setLocalDocs( null );
	}, [ parentDocs ] );

	// Filter parent docs by search value.
	const filteredDocs = useMemo( () => {
		if ( ! searchValue ) {
			return displayDocs;
		}
		const lower = searchValue.toLowerCase();
		return displayDocs.filter( ( doc ) => doc.title.toLowerCase().indexOf( lower ) > -1 );
	}, [ displayDocs, searchValue ] );

	const count = filteredDocs.length;

	/**
	 * Configure sensors.
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
	 * Handle clicking on a parent doc nav item.
	 */
	const handleNavClick = ( e: React.MouseEvent< HTMLLIElement >, docId: number ): void => {
		// Don't switch tab if clicking on action links.
		if ( ( e.target as HTMLElement ).closest( 'a, button, [role="button"], .link-wrapper' ) ) {
			return;
		}
		onTabChange( docId );
	};

	/**
	 * Handle keyboard activation on nav items.
	 */
	const handleNavKeyDown = ( e: React.KeyboardEvent< HTMLLIElement >, docId: number ): void => {
		if ( e.which !== 13 && e.which !== 32 ) {
			return;
		}
		const target = e.target as HTMLElement;
		if ( target !== e.currentTarget && target.matches( 'a, button, [role="button"], input, select, textarea' ) ) {
			return;
		}
		e.preventDefault();
		onTabChange( docId );
	};

	/**
	 * Handle delete parent doc.
	 */
	const handleDelete = ( e: React.MouseEvent< HTMLAnchorElement >, doc: ParentDoc ): void => {
		e.preventDefault();
		e.stopPropagation();

		if ( typeof window.Swal !== 'undefined' ) {
			window.Swal.fire( {
				title: eazydocs_local_object.delete_prompt_title,
				text: eazydocs_local_object.no_revert_title,
				icon: 'question',
				showCancelButton: true,
				confirmButtonColor: '#d33',
				cancelButtonColor: '#3085d6',
				confirmButtonText: 'Yes, delete it!',
			} ).then( ( result: any ) => {
				if ( result.value ) {
					deleteDoc.mutate(
						{
							docId: doc.id,
							nonce: doc.deleteNonce,
						},
						{
							onSuccess: () => {
								if ( typeof window.Swal !== 'undefined' ) {
									window.Swal.fire( {
										title: __( 'Deleted!', 'eazydocs' ),
										text: __( 'The document has been moved to trash.', 'eazydocs' ),
										icon: 'success',
										timer: 1500,
										showConfirmButton: false,
									} );
								}
							},
							onError: () => {
								if ( typeof window.Swal !== 'undefined' ) {
									window.Swal.fire( {
										title: __( 'Error', 'eazydocs' ),
										text: __( 'Failed to delete the document.', 'eazydocs' ),
										icon: 'error',
									} );
								}
							},
						}
					);
				}
			} );
		}
	};

	/**
	 * Toggle bulk options dropdown.
	 */
	const handleBulkToggle = ( e: React.MouseEvent< HTMLSpanElement > | React.KeyboardEvent< HTMLSpanElement >, docId: number ): void => {
		if ( ( e.target as HTMLElement ).closest( '.ezd-admin-bulk-actions' ) ) {
			return;
		}
		if ( e.type === 'keydown' && ( e as React.KeyboardEvent ).which !== 13 && ( e as React.KeyboardEvent ).which !== 32 ) {
			return;
		}
		if ( e.type === 'keydown' ) {
			e.preventDefault();
		}
		e.stopPropagation();
		setOpenBulk( openBulk === docId ? null : docId );
	};

	/**
	 * Handle drag start.
	 */
	const handleDragStart = ( event: DragStartEvent ): void => {
		document.body.classList.add( 'ezd-is-dragging' );
		if ( event.active.data.current ) {
			setActiveDragItem( {
				id: event.active.id,
				doc: event.active.data.current.doc,
			} );
		}
	};

	/**
	 * Handle drag end – reorder parent docs.
	 */
	const handleDragEnd = ( event: DragEndEvent ): void => {
		document.body.classList.remove( 'ezd-is-dragging' );
		setActiveDragItem( null );

		const { active, over } = event;

		if ( ! over || active.id === over.id ) {
			return;
		}

		const activeId = active.id as number;
		const overId = over.id as number;

		const oldIndex = displayDocs.findIndex( ( d ) => d.id === activeId );
		const newIndex = displayDocs.findIndex( ( d ) => d.id === overId );

		if ( oldIndex === -1 || newIndex === -1 ) {
			return;
		}

		const newDocs = arrayMove( displayDocs, oldIndex, newIndex );

		// Optimistic update.
		setLocalDocs( newDocs );

		// Serialize parent docs (flat list — just id, no children).
		const serialized = newDocs.map( ( doc ) => ( { id: doc.id, children: [] } ) );

		reorderDocs.mutate(
			{
				data: window.JSON.stringify( serialized ),
				action: 'eaz_parent_nestable_docs',
			},
			{
				onSuccess: () => showToast( __( 'Order saved', 'eazydocs' ) ),
				onError: () => {
					showToast( __( 'Failed to save order', 'eazydocs' ), 'error' );
					// Revert optimistic update.
					setLocalDocs( null );
				},
			}
		);
	};

	// Close bulk options when clicking outside.
	const handleClickOutside = (): void => {
		setOpenBulk( null );
	};

	// Close bulk options when clicking outside.
	useEffect( () => {
		document.addEventListener( 'click', handleClickOutside );
		return () => document.removeEventListener( 'click', handleClickOutside );
	}, [] );

	const sortableIds = filteredDocs.map( ( doc ) => doc.id );

	return (
		<DndContext
			sensors={ sensors }
			collisionDetection={ closestCenter }
			onDragStart={ handleDragStart }
			onDragEnd={ handleDragEnd }
		>
			<SortableContext
				items={ sortableIds }
				strategy={ verticalListSortingStrategy }
			>
				<div className={ `dd parent-nestable tab-menu ${ count > 12 ? '' : 'short' }` } style={ { flex: 3 } }>
					<ol className="easydocs-navbar sortabled dd-list">
						{ filteredDocs.map( ( doc ) => (
							<SortableParentItem
								key={ doc.id }
								doc={ doc }
								isActive={ doc.id === activeTab }
								isPremium={ isPremium }
								capabilities={ capabilities }
								urls={ urls }
								roleVisibility={ roleVisibility }
								openBulk={ openBulk }
								onNavClick={ handleNavClick }
								onNavKeyDown={ handleNavKeyDown }
								onDelete={ handleDelete }
								onBulkToggle={ handleBulkToggle }
							/>
						) ) }
					</ol>
				</div>
			</SortableContext>
			<DragOverlay dropAnimation={{ duration: 250, easing: 'ease' }}>
				{ activeDragItem ? (
					<SortableParentItem
						doc={ activeDragItem.doc }
						isActive={ activeDragItem.doc.id === activeTab }
						isPremium={ isPremium }
						capabilities={ capabilities }
						urls={ urls }
						roleVisibility={ roleVisibility }
						openBulk={ null }
						onNavClick={ () => {} }
						onNavKeyDown={ () => {} }
						onDelete={ () => {} }
						onBulkToggle={ () => {} }
					/>
				) : null }
			</DragOverlay>
		</DndContext>
	);
};

export default ParentDocs;
