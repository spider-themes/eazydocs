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

/**
 * Find and reorder items within a nested tree by parent ID.
 *
 * Walks the tree to find the parent that contains the moved item,
 * then applies `arrayMove` at that level.
 *
 * @param {DocChild[]} tree     - The full tree.
 * @param {number}     itemId   - The ID of the item being moved.
 * @param {number}     overId   - The ID of the item being moved over.
 * @param {number}     parentId - The parent ID (container) where the move happens.
 * @return {DocChild[] | null} New tree with the item moved, or null if not found.
 */
export function reorderInTree(
	tree: DocChild[],
	itemId: number,
	overId: number,
	parentId: number
): DocChild[] | null {
	// Check if the move is at the root level.
	const rootIds = tree.map( ( item ) => item.id );
	if ( rootIds.includes( itemId ) && rootIds.includes( overId ) ) {
		const oldIndex = rootIds.indexOf( itemId );
		const newIndex = rootIds.indexOf( overId );
		return arrayMove( tree, oldIndex, newIndex );
	}

	// Otherwise, recurse into children.
	return tree.map( ( item ) => {
		if ( ! item.children || item.children.length === 0 ) {
			return item;
		}

		const childIds = item.children.map( ( c ) => c.id );
		if ( childIds.includes( itemId ) && childIds.includes( overId ) ) {
			const oldIndex = childIds.indexOf( itemId );
			const newIndex = childIds.indexOf( overId );
			return {
				...item,
				children: arrayMove( item.children, oldIndex, newIndex ),
			};
		}

		const result = reorderInTree( item.children, itemId, overId, parentId );
		if ( result ) {
			return { ...item, children: result };
		}

		return item;
	} );
}
