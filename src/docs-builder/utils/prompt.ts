/**
 * Shared title-prompt helper for the Docs Builder.
 *
 * Centralises the "create doc / section / child" prompt so every entry
 * point behaves identically and degrades gracefully when SweetAlert is
 * unavailable (previously the create flows silently did nothing).
 *
 * @package EazyDocs
 * @since   2.9.0
 */
import { __ } from '@wordpress/i18n';

declare const eazydocs_local_object: any;

/**
 * Access the SweetAlert instance without tightening the shared global type.
 *
 * @return {any} The Swal instance, or undefined when the library is absent.
 */
const getSwal = (): any => ( window as any ).Swal;

export interface DocTitlePromptResult {
	title: string;
	status: 'publish' | 'draft';
}

/**
 * Prompt the user for a new doc title and the desired publish status.
 *
 * @return {Promise<DocTitlePromptResult|null>} The trimmed title and status,
 *                                              or null when cancelled / empty.
 */
export const promptForDocTitle = async (): Promise< DocTitlePromptResult | null > => {
	const promptTitle =
		( typeof eazydocs_local_object !== 'undefined' && eazydocs_local_object?.create_prompt_title ) ||
		__( 'Enter a title', 'eazydocs' );

	// Preferred path – SweetAlert modal with Publish / Draft / Cancel.
	const Swal = getSwal();
	if ( typeof Swal !== 'undefined' ) {
		const result = await Swal.fire( {
			title: promptTitle,
			input: 'text',
			showDenyButton: true,
			returnInputValueOnDeny: true,
			confirmButtonText: __( 'Publish', 'eazydocs' ),
			denyButtonText: __( 'Save as Draft', 'eazydocs' ),
			showCancelButton: true,
			inputAttributes: {
				name: 'new_doc',
			},
			inputValidator: ( value: string ) =>
				! value || ! value.trim() ? __( 'Please enter a title.', 'eazydocs' ) : undefined,
		} );

		if ( ! result.isConfirmed && ! result.isDenied ) {
			return null;
		}

		const title = ( ( result.value as string ) || '' ).trim();
		if ( ! title ) {
			return null;
		}

		return { title, status: result.isDenied ? 'draft' : 'publish' };
	}

	// Fallback – native prompt so creation still works without SweetAlert.
	// eslint-disable-next-line no-alert
	const fallback = window.prompt( promptTitle );
	const title = fallback ? fallback.trim() : '';
	if ( ! title ) {
		return null;
	}

	return { title, status: 'publish' };
};

/**
 * Lightweight success notification used after create actions.
 *
 * Falls back to a no-op when SweetAlert is unavailable.
 *
 * @param {string} text Message to display.
 */
export const showCreateSuccess = ( text: string ): void => {
	const Swal = getSwal();
	if ( typeof Swal === 'undefined' ) {
		return;
	}

	Swal.fire( {
		title: __( 'Success!', 'eazydocs' ),
		text,
		icon: 'success',
		timer: 1500,
		showConfirmButton: false,
	} );
};

/**
 * Lightweight error notification used when a create action fails.
 *
 * @param {string} text Message to display.
 */
export const showCreateError = ( text: string ): void => {
	const Swal = getSwal();
	if ( typeof Swal === 'undefined' ) {
		// eslint-disable-next-line no-alert
		window.alert( text );
		return;
	}

	Swal.fire( {
		title: __( 'Error', 'eazydocs' ),
		text,
		icon: 'error',
	} );
};
