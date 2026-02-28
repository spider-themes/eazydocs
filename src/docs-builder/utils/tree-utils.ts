/**
 * Tree utilities for the @dnd-kit drag-and-drop system.
 *
 * Provides functions for flattening nested doc trees to get
 * sortable IDs, moving items within a flat list, and serializing
 * back into the nested format expected by the PHP backend.
 *
 * @package EazyDocs
 * @since   2.9.0
 */
import type { DocChild } from '../types';

/**
 * Item in the serialized format expected by the PHP backend.
 */
export interface SerializedItem {
	id: number;
	children: SerializedItem[];
}

/**
 * Flatten a DocChild tree to an array of IDs for the top-level sortable context.
 *
 * Only returns the IDs at the immediate level (not deeply nested children),
 * since each level has its own SortableContext.
 *
 * @param {DocChild[]} items - The items at the current level.
 * @return {number[]} Array of item IDs.
 */
export function getItemIds( items: DocChild[] ): number[] {
	return items.map( ( item ) => item.id );
}

/**
 * Reorder items at the current level by moving an item from one index to another.
 *
 * @param {DocChild[]} items    - The current array of items.
 * @param {number}     oldIndex - The index the item was at.
 * @param {number}     newIndex - The index the item should move to.
 * @return {DocChild[]} New array with the item moved.
 */
export function arrayMove< T >( items: T[], oldIndex: number, newIndex: number ): T[] {
	const result = [ ...items ];
	const [ removed ] = result.splice( oldIndex, 1 );
	result.splice( newIndex, 0, removed );
	return result;
}

/**
 * Serialize a DocChild array into the format expected by the PHP backend.
 *
 * The PHP handler expects: [{ id, children: [{ id, children: [...] }] }]
 * This matches the jQuery Nestable `serialize()` output.
 *
 * @param {DocChild[]} items - The items to serialize.
 * @return {SerializedItem[]} Serialized array.
 */
export function serializeTree( items: DocChild[] ): SerializedItem[] {
	return items.map( ( item ) => ( {
		id: item.id,
		children: item.children && item.children.length > 0
			? serializeTree( item.children )
			: [],
	} ) );
}

export type FlatDocChild = DocChild & { parentId: number; depth: number; };

/**
 * Flatten a DocChild tree to a 1D array, tracking depth and parentId.
 * This allows @dnd-kit to render a single flat SortableContext so
 * any item can be dragged between different parents and depths.
 *
 * @param {DocChild[]} items    - The items to flatten.
 * @param {number}     parentId - The parent ID of the current level.
 * @param {number}     depth    - The depth of the current level.
 * @return {FlatDocChild[]} Flat array of items.
 */
export function flattenTree( items: DocChild[], parentId = 0, depth = 1 ): FlatDocChild[] {
	return items.reduce< FlatDocChild[] >( ( acc, item ) => {
		acc.push( { ...item, parentId, depth } as FlatDocChild );
		if ( item.children && item.children.length > 0 ) {
			acc.push( ...flattenTree( item.children, item.id, depth + 1 ) );
		}
		return acc;
	}, [] );
}

/**
 * Filter the flattened tree to hide items whose ancestors are collapsed.
 */
export function getVisibleFlatItems( flatItems: FlatDocChild[], collapsedIds: Set<number> ): FlatDocChild[] {
	const visible: FlatDocChild[] = [];
	for ( const item of flatItems ) {
		// To be visible, no ancestor can be collapsed.
		let isVisible = true;
		let currentParentId = item.parentId;
		
		while ( currentParentId && currentParentId !== 0 ) {
			if ( collapsedIds.has( currentParentId ) ) {
				isVisible = false;
				break;
			}
			// Find the parent item to check its parent.
			const parent = flatItems.find( i => i.id === currentParentId );
			currentParentId = parent ? parent.parentId : 0;
		}

		if ( isVisible ) {
			visible.push( item );
		}
	}
	return visible;
}

/**
 * Build a nested tree back from a flattened array and serialize it.
 * Not required for serializeTree directly since serializeTree takes the nested tree.
 * But we need to rebuild the tree when order changes in the flat array!
 */
export function buildTree( flatItems: FlatDocChild[] ): DocChild[] {
	const rootItems: DocChild[] = [];
	const lookup: Record< number, DocChild > = {};

	flatItems.forEach( ( item ) => {
		lookup[ item.id ] = { ...item, children: [] };
	} );

	flatItems.forEach( ( item ) => {
		if ( item.parentId && item.parentId !== 0 && lookup[ item.parentId ] ) {
			lookup[ item.parentId ].children!.push( lookup[ item.id ] );
		} else {
			rootItems.push( lookup[ item.id ] );
		}
	} );

	return rootItems;
}

/**
 * Calculate the projected depth and parentId based on dragging position.
 */
export function getProjectedState(
	flatItems: FlatDocChild[],
	activeIndex: number,
	overIndex: number,
	dragOffset: number,
	indentationWidth = 24
) {
	const activeItem = flatItems[ activeIndex ];
	// Simulate the array move to see where it lands
	const newItems = [ ...flatItems ];
	const [ removed ] = newItems.splice( activeIndex, 1 );
	newItems.splice( overIndex, 0, removed );

	const previousItem = newItems[ overIndex - 1 ];
	const nextItem = newItems[ overIndex + 1 ];

	let projectedDepth = activeItem.depth + Math.round( dragOffset / indentationWidth );

	// Constrain depth based on surrounding items.
	const maxDepth = previousItem ? previousItem.depth + 1 : 1;
	const minDepth = nextItem ? nextItem.depth : 1;

	if ( projectedDepth >= maxDepth ) {
		projectedDepth = maxDepth;
	} else if ( projectedDepth < minDepth ) {
		projectedDepth = minDepth;
	}

	let parentId = 0;
	if ( projectedDepth === 1 ) {
		parentId = 0;
	} else if ( previousItem ) {
		// Find the closest previous item at depth: projectedDepth - 1
		for ( let i = overIndex - 1; i >= 0; i-- ) {
			if ( newItems[ i ].depth === projectedDepth - 1 ) {
				parentId = newItems[ i ].id;
				break;
			}
		}
	}

	return { depth: projectedDepth, parentId };
}

/**
 * Perform a flat move using the projection calculation to support nested indentation.
 */
export function reorderFlatTree(
	flatItems: FlatDocChild[],
	activeId: number,
	overId: number,
	dragOffset: number = 0
): FlatDocChild[] {
	const activeIndex = flatItems.findIndex( i => i.id === activeId );
	const overIndex = flatItems.findIndex( i => i.id === overId );

	if ( activeIndex === -1 || overIndex === -1 ) {
		return flatItems;
	}

	const projected = getProjectedState( flatItems, activeIndex, overIndex, dragOffset, 24 );

	const cloned = [ ...flatItems ];
	const activeItem = cloned[ activeIndex ];

	const newItem = {
		...activeItem,
		parentId: projected.parentId,
		depth: projected.depth,
	};

	const [ removed ] = cloned.splice( activeIndex, 1 );
	cloned.splice( overIndex, 0, newItem as FlatDocChild );

	return cloned;
}

export interface NodePosition {
	parentId: number;
	index: number;
	node: DocChild;
}

/**
 * Find the position of a node in the nested tree.
 */
export function findNodePosition( nodes: DocChild[], id: number, parentId: number = 0 ): NodePosition | null {
	for ( let i = 0; i < nodes.length; i++ ) {
		if ( nodes[ i ].id === id ) {
			return { parentId, index: i, node: nodes[ i ] };
		}
		if ( nodes[ i ].children && nodes[ i ].children.length > 0 ) {
			const pos = findNodePosition( nodes[ i ].children, id, nodes[ i ].id );
			if ( pos ) {
				return pos;
			}
		}
	}
	return null;
}

/**
 * Check if targetId is a descendant of sourceId.
 */
export function isDescendant( nodes: DocChild[], sourceId: number, targetId: number ): boolean {
	if ( sourceId === targetId ) return true;
	const sourcePos = findNodePosition( nodes, sourceId );
	if ( ! sourcePos ) return false;

	const searchDescendants = ( children: DocChild[] ): boolean => {
		for ( const child of children ) {
			if ( child.id === targetId ) return true;
			if ( child.children && searchDescendants( child.children ) ) return true;
		}
		return false;
	};

	return sourcePos.node.children ? searchDescendants( sourcePos.node.children ) : false;
}

/**
 * Remove a node from the tree.
 */
export function removeNode( nodes: DocChild[], id: number ): { updated: DocChild[], removed: DocChild | null } {
	const updated = [ ...nodes ];
	for ( let i = 0; i < updated.length; i++ ) {
		if ( updated[ i ].id === id ) {
			const removed = updated.splice( i, 1 )[ 0 ];
			return { updated, removed };
		}
		if ( updated[ i ].children && updated[ i ].children.length > 0 ) {
			const result = removeNode( updated[ i ].children, id );
			if ( result.removed ) {
				updated[ i ] = { ...updated[ i ], children: result.updated };
				return { updated, removed: result.removed };
			}
		}
	}
	return { updated, removed: null };
}

/**
 * Insert a node into the tree at the specified parent and index.
 */
export function insertNode( nodes: DocChild[], parentId: number, index: number, node: DocChild ): DocChild[] {
	if ( parentId === 0 ) {
		const updated = [ ...nodes ];
		updated.splice( index, 0, node );
		return updated;
	}
	return nodes.map( n => {
		if ( n.id === parentId ) {
			const updatedChildren = n.children ? [ ...n.children ] : [];
			updatedChildren.splice( index, 0, node );
			return { ...n, children: updatedChildren };
		}
		if ( n.children && n.children.length > 0 ) {
			return { ...n, children: insertNode( n.children, parentId, index, node ) };
		}
		return n;
	} );
}

/**
 * Reorder a node within the same parent's children array.
 */
export function updateNodeOrder( nodes: DocChild[], parentId: number, oldIndex: number, newIndex: number ): DocChild[] {
	if ( parentId === 0 ) {
		return arrayMove( nodes, oldIndex, newIndex );
	}
	return nodes.map( n => {
		if ( n.id === parentId ) {
			return { ...n, children: arrayMove( n.children || [], oldIndex, newIndex ) };
		}
		if ( n.children && n.children.length > 0 ) {
			return { ...n, children: updateNodeOrder( n.children, parentId, oldIndex, newIndex ) };
		}
		return n;
	} );
}
