/**
 * ParentDocs sidebar component.
 *
 * Renders the left sidebar list of parent docs with @dnd-kit
 * for React-native drag-and-drop reordering.
 *
 * @package EazyDocs
 * @since   2.9.0
 */
import { useState, useEffect, useMemo, useCallback, useRef, memo } from '@wordpress/element';
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
import { useDeleteDoc, useReorderDocs, useRenameDoc } from '../hooks/useBuilderData';
import { useSearch } from '../hooks/useSearch';
import { useToast } from '../hooks/useToast';
import { arrayMove } from '../utils/tree-utils';
import { confirmDelete, showDeleteSuccess, showDeleteError } from '../utils/prompt';
import ProActionsButtons from './ProActionsButtons';
import RenameInput from './RenameInput';
import { DragHandleIcon } from './icons';
import type { ParentDoc, DocChild, Capabilities, BuilderUrls, RoleVisibilityConfig } from '../types';

interface ParentDocsProps {
	parentDocs: ParentDoc[];
	activeTab: number | null;
	onTabChange: ( docId: number ) => void;
	capabilities: Capabilities;
	isPremium: boolean;
	urls: BuilderUrls;
	roleVisibility: RoleVisibilityConfig;
	/** Full children tree keyed by parent id – used for deep search matching. */
	childrenMap: Record< number, DocChild[] >;
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
	/** Count of descendant docs matching the active search (0 when not searching). */
	searchMatchCount?: number;
	onNavClick: ( e: React.MouseEvent< HTMLLIElement >, docId: number ) => void;
	onNavKeyDown: ( e: React.KeyboardEvent< HTMLLIElement >, docId: number ) => void;
	onDelete: ( e: React.MouseEvent< HTMLAnchorElement >, doc: ParentDoc ) => void;
	onBulkToggle: ( e: React.MouseEvent< HTMLSpanElement > | React.KeyboardEvent< HTMLSpanElement >, docId: number ) => void;
}

const SortableParentItemComponent: React.FC< SortableParentItemProps > = ( {
	doc,
	isActive,
	isPremium,
	capabilities,
	urls,
	roleVisibility,
	openBulk,
	searchMatchCount = 0,
	onNavClick,
	onNavKeyDown,
	onDelete,
	onBulkToggle,
} ) => {
	const renameDoc = useRenameDoc();
	const [ isRenaming, setIsRenaming ] = useState( false );

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
			<div
				className="title"
				onDoubleClick={ ( e ) => {
					if ( doc.canEdit ) {
						e.stopPropagation();
						setIsRenaming( true );
					}
				} }
			>
				<span
					title={ docStatus }
					className={ `dashicons dashicons-${ postFormat }` }
				></span>
				{ isRenaming ? (
					<RenameInput
						initialTitle={ doc.title }
						className="ezd-rename-input--parent"
						onCommit={ ( title ) => {
							setIsRenaming( false );
							renameDoc.mutate( { docId: doc.id, title } );
						} }
						onCancel={ () => setIsRenaming( false ) }
					/>
				) : (
					doc.title
				) }
			</div>
			<div className="total-page">
				{ searchMatchCount > 0 && (
					<span
						className="ezd-nav-match-badge"
						title={ __( 'Matches inside this doc', 'eazydocs' ) }
					>
						{ searchMatchCount }
					</span>
				) }
				<span>
					{ doc.childCount > 0 ? doc.childCount : '' }
				</span>
			</div>
			<div className="link link-wrapper">
				{ capabilities.canManageOptions && (
					<span
						ref={ setActivatorNodeRef }
						className="dd-handle dd3-handle"
						aria-label={ __( 'Drag to reorder', 'eazydocs' ) }
						title={ __( 'Drag, or focus and press Space then arrow keys, to reorder', 'eazydocs' ) }
						{ ...attributes }
						{ ...listeners }
					>
						<DragHandleIcon size={ 16 } />
					</span>
				) }

				{ doc.canEdit && ! isRenaming && (
					<a
						href="#"
						className="link rename"
						aria-label={ __( 'Rename this doc', 'eazydocs' ) }
						title={ __( 'Rename', 'eazydocs' ) }
						onClick={ ( e ) => {
							e.preventDefault();
							e.stopPropagation();
							setIsRenaming( true );
						} }
					>
						<span className="dashicons dashicons-edit"></span>
					</a>
				) }

				{ doc.canEdit && (
					<a
						href={ doc.editLink }
						className="link edit"
						target="_blank"
						rel="noopener noreferrer"
						aria-label={ __( 'Edit content in the editor', 'eazydocs' ) }
						title={ __( 'Edit content', 'eazydocs' ) }
						onClick={ ( e ) => e.stopPropagation() }
					>
						<span className="dashicons dashicons-welcome-write-blog"></span>
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

const SortableParentItem = memo( SortableParentItemComponent, ( prevProps, nextProps ) => {
	const wasBulkOpen = prevProps.openBulk === prevProps.doc.id;
	const isBulkOpen = nextProps.openBulk === nextProps.doc.id;

	return prevProps.doc === nextProps.doc
		&& prevProps.isActive === nextProps.isActive
		&& prevProps.isPremium === nextProps.isPremium
		&& prevProps.capabilities === nextProps.capabilities
		&& prevProps.urls === nextProps.urls
		&& prevProps.roleVisibility === nextProps.roleVisibility
		&& prevProps.searchMatchCount === nextProps.searchMatchCount
		&& prevProps.onNavClick === nextProps.onNavClick
		&& prevProps.onNavKeyDown === nextProps.onNavKeyDown
		&& prevProps.onDelete === nextProps.onDelete
		&& prevProps.onBulkToggle === nextProps.onBulkToggle
		&& wasBulkOpen === isBulkOpen;
} );

/**
 * Count how many descendant docs (at any depth) match the search term.
 */
const countDeepMatches = ( nodes: DocChild[], lower: string ): number => {
	let count = 0;
	nodes.forEach( ( node ) => {
		if ( node.title.toLowerCase().indexOf( lower ) > -1 ) {
			count += 1;
		}
		if ( node.children.length ) {
			count += countDeepMatches( node.children, lower );
		}
	} );
	return count;
};

const ParentDocs: React.FC< ParentDocsProps > = ( { parentDocs, activeTab, onTabChange, capabilities, isPremium, urls, roleVisibility, childrenMap } ) => {
	const [ openBulk, setOpenBulk ] = useState< number | null >( null );
	const deleteDoc = useDeleteDoc();
	const reorderDocs = useReorderDocs();
	const { searchValue } = useSearch();
	const { showToast } = useToast();
	const [ activeDragItem, setActiveDragItem ] = useState<{ id: string | number; doc: ParentDoc } | null>( null );
	const parentNestableRef = useRef< HTMLDivElement | null >( null );

	// Local state for optimistic reorder.
	const [ localDocs, setLocalDocs ] = useState< ParentDoc[] | null >( null );
	const displayDocs = localDocs || parentDocs;

	// Reset local state when server data changes.
	useEffect( () => {
		setLocalDocs( null );
	}, [ parentDocs ] );

	// Filter parent docs by search value. A parent is kept when its own title
	// matches OR any descendant matches, so searching for a nested doc no longer
	// empties the sidebar. matchCounts drives the per-doc "matches inside" badge.
	const { filteredDocs, matchCounts } = useMemo( () => {
		const term = searchValue.trim().toLowerCase();
		if ( ! term ) {
			return { filteredDocs: displayDocs, matchCounts: {} as Record< number, number > };
		}

		const counts: Record< number, number > = {};
		const kept = displayDocs.filter( ( doc ) => {
			const titleMatch = doc.title.toLowerCase().indexOf( term ) > -1;
			const deep = countDeepMatches( childrenMap[ doc.id ] || [], term );
			counts[ doc.id ] = deep;
			return titleMatch || deep > 0;
		} );

		return { filteredDocs: kept, matchCounts: counts };
	}, [ displayDocs, searchValue, childrenMap ] );

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
	const handleNavClick = useCallback( ( e: React.MouseEvent< HTMLLIElement >, docId: number ): void => {
		// Don't switch tab if clicking on action links.
		if ( ( e.target as HTMLElement ).closest( 'a, button, [role="button"], .link-wrapper' ) ) {
			return;
		}
		onTabChange( docId );
	}, [ onTabChange ] );

	/**
	 * Handle keyboard activation on nav items.
	 */
	const handleNavKeyDown = useCallback( ( e: React.KeyboardEvent< HTMLLIElement >, docId: number ): void => {
		if ( e.which !== 13 && e.which !== 32 ) {
			return;
		}
		const target = e.target as HTMLElement;
		if ( target !== e.currentTarget && target.matches( 'a, button, [role="button"], input, select, textarea' ) ) {
			return;
		}
		e.preventDefault();
		onTabChange( docId );
	}, [ onTabChange ] );

	/**
	 * Handle delete parent doc.
	 */
	const handleDelete = useCallback( async ( e: React.MouseEvent< HTMLAnchorElement >, doc: ParentDoc ): Promise< void > => {
		e.preventDefault();
		e.stopPropagation();

		if ( ! ( await confirmDelete() ) ) {
			return;
		}

		deleteDoc.mutate(
			{
				docId: doc.id,
				nonce: doc.deleteNonce,
			},
			{
				onSuccess: () => showDeleteSuccess(),
				onError: () => showDeleteError( __( 'Failed to delete the document.', 'eazydocs' ) ),
			}
		);
	}, [ deleteDoc ] );

	/**
	 * Toggle bulk options dropdown.
	 */
	const handleBulkToggle = useCallback( ( e: React.MouseEvent< HTMLSpanElement > | React.KeyboardEvent< HTMLSpanElement >, docId: number ): void => {
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
	}, [ openBulk ] );

	/**
	 * Handle drag start.
	 */
	const handleDragStart = useCallback( ( event: DragStartEvent ): void => {
		document.body.classList.add( 'ezd-is-dragging' );
		if ( event.active.data.current ) {
			setActiveDragItem( {
				id: event.active.id,
				doc: event.active.data.current.doc,
			} );
		}
	}, [] );

	/**
	 * Handle drag end – reorder parent docs.
	 */
	const handleDragEnd = useCallback( ( event: DragEndEvent ): void => {
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
	}, [ displayDocs, reorderDocs, showToast ] );

	// Close bulk options when clicking outside.
	const handleClickOutside = useCallback( (): void => {
		setOpenBulk( null );
	}, [] );

	// Close bulk options when clicking outside.
	useEffect( () => {
		document.addEventListener( 'click', handleClickOutside );
		return () => document.removeEventListener( 'click', handleClickOutside );
	}, [] );

	const sortableIds = filteredDocs.map( ( doc ) => doc.id );;

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
				<div
					ref={ parentNestableRef }
					className={ `dd parent-nestable tab-menu ezd-parent-nestable-scroll ${ count > 12 ? '' : 'short' }` }
				>
					<ol
						className="easydocs-navbar sortabled dd-list"
						role="tablist"
						aria-orientation="vertical"
						aria-label={ __( 'Documentation', 'eazydocs' ) }
					>
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
								searchMatchCount={ searchValue ? ( matchCounts[ doc.id ] || 0 ) : 0 }
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
