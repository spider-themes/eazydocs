/**
 * DropIndicatorLine – visual line showing where a dragged item will be placed.
 *
 * Renders a horizontal indigo line with a small circle at the leading edge.
 *
 * @package EazyDocs
 * @since   2.9.0
 */
import React from 'react';

/**
 * Shape of the drop indicator state shared between ChildDocs and SortableDocItem.
 */
export interface DropIndicator {
	parentId: number;
	index: number;
}

/**
 * The visual drop indicator line component.
 */
const DropIndicatorLine: React.FC = () => (
	<div className="ezd-drop-indicator" aria-hidden="true">
		<div className="ezd-drop-indicator-dot"></div>
		<div className="ezd-drop-indicator-line"></div>
	</div>
);

export default DropIndicatorLine;
