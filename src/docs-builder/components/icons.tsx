/**
 * Shared inline SVG icons for the Docs Builder.
 *
 * Centralises icons that were previously duplicated across components
 * so the visual language stays consistent in one place.
 *
 * @package EazyDocs
 * @since   2.12.2
 */

interface IconProps {
	/** Pixel size for both width and height. */
	size?: number;
}

/**
 * Six-dot drag grip used by both the parent nav and section cards.
 *
 * @param {IconProps} props Component props.
 */
export const DragHandleIcon: React.FC< IconProps > = ( { size = 16 } ) => (
	<svg
		className="dd-handle-icon"
		width={ size }
		height={ size }
		viewBox="0 0 24 24"
		xmlns="http://www.w3.org/2000/svg"
		aria-hidden="true"
	>
		<circle cx="9" cy="5" r="2" fill="currentColor" />
		<circle cx="15" cy="5" r="2" fill="currentColor" />
		<circle cx="9" cy="12" r="2" fill="currentColor" />
		<circle cx="15" cy="12" r="2" fill="currentColor" />
		<circle cx="9" cy="19" r="2" fill="currentColor" />
		<circle cx="15" cy="19" r="2" fill="currentColor" />
	</svg>
);
