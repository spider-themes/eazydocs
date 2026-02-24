/**
 * WordPress global type declarations.
 *
 * @package EazyDocs
 * @since   2.8.0
 */

declare module '@wordpress/element' {
	export const useState: typeof import('react').useState;
	export const useEffect: typeof import('react').useEffect;
	export const useCallback: typeof import('react').useCallback;
	export const useRef: typeof import('react').useRef;
	export const createRoot: ( container: Element ) => {
		render: ( element: React.ReactNode ) => void;
	};
}

declare module '@wordpress/i18n' {
	export function __( text: string, domain?: string ): string;
	export function _n( single: string, plural: string, number: number, domain?: string ): string;
	export function sprintf( format: string, ...args: Array<string | number> ): string;
}

declare module '@wordpress/api-fetch' {
	interface ApiFetchOptions {
		path: string;
		method?: string;
		data?: Record<string, unknown>;
	}
	export default function apiFetch<T = unknown>( options: ApiFetchOptions ): Promise<T>;
}

/**
 * EazyDocs localized object shape.
 */
interface EazydocsLocalObject {
	ajaxurl: string;
	nonce: string;
	create_prompt_title: string;
	delete_prompt_title: string;
	no_revert_title: string;
	reusable_blocks_options?: Array< {
		id: string;
		title: string;
	} >;
	manage_reusable_blocks_url?: string;
}

/**
 * SweetAlert2 result and fire options.
 */
interface SwalResult {
	value?: string;
	isConfirmed?: boolean;
}

interface SwalOptions {
	title?: string;
	text?: string;
	icon?: string;
	input?: string;
	showCancelButton?: boolean;
	showConfirmButton?: boolean;
	confirmButtonColor?: string;
	cancelButtonColor?: string;
	confirmButtonText?: string;
	cancelButtonText?: string;
	inputAttributes?: Record<string, string>;
	customClass?: Record<string, string>;
	timer?: number;
	preConfirm?: ( value: string ) => unknown;
	allowOutsideClick?: boolean | ( () => boolean );
}

interface SwalInstance {
	fire: ( options: SwalOptions ) => Promise<SwalResult>;
	showLoading: () => void;
	isLoading: () => boolean;
	getConfirmButton: () => HTMLButtonElement | null;
}

/**
 * jQuery Nestable plugin extension.
 */
interface JQueryNestable {
	nestable: ( optionsOrAction: Record<string, unknown> | string ) => JQueryNestable;
	on: ( event: string, handler: ( ...args: unknown[] ) => void ) => JQueryNestable;
	trigger: ( event: string ) => JQueryNestable;
	closest: ( selector: string ) => JQueryNestable;
	length: number;
}

interface JQueryStatic {
	fn: {
		nestable?: unknown;
	};
	ajax: ( options: Record<string, unknown> ) => void;
	( selector: string | Element ): JQueryNestable;
}

declare global {
	const eazydocs_local_object: EazydocsLocalObject;

	interface Window {
		jQuery: JQueryStatic;
		Swal: SwalInstance;
		JSON: JSON;
	}
}

export {};
