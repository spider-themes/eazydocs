/**
 * Custom hook for the "Create Doc with AI" feature.
 *
 * Replaces the jQuery trigger (`window.jQuery('#ezd-create-doc-with-ai').trigger('click')`)
 * with a React-friendly custom event system.
 *
 * @package EazyDocs
 * @since   2.9.0
 */
import { useCallback } from '@wordpress/element';

/**
 * Custom event name for AI doc creation.
 */
const AI_CREATE_EVENT = 'ezd:ai-create-doc';

/**
 * Hook to handle AI doc creation interactions.
 *
 * @param {boolean} antimanualActive Whether the Antimanual plugin is active.
 * @return {{ triggerAiCreate: () => void }} Trigger function.
 */
export const useAiCreate = ( antimanualActive: boolean ) => {
	const triggerAiCreate = useCallback( (): void => {
		if ( antimanualActive ) {
			// Antimanual is active – navigation is handled by the <a> tag.
			return;
		}

		// Dispatch a custom DOM event for the AI popup handler.
		// The admin-global.js script listens for clicks on #ezd-create-doc-with-ai,
		// so we dispatch a click event on that element if it exists.
		const aiButton = document.getElementById( 'ezd-create-doc-with-ai' );
		if ( aiButton ) {
			// Use native click to trigger any external listeners.
			aiButton.dispatchEvent( new MouseEvent( 'click', { bubbles: true, cancelable: true } ) );
		}
	}, [ antimanualActive ] );

	return { triggerAiCreate };
};
