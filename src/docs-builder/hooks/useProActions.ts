/**
 * TanStack Query mutations for Pro actions (duplicate, visibility, sidebar).
 *
 * Replaces the jQuery-based handlers and dangerouslySetInnerHTML
 * approach with proper React-managed API calls.
 *
 * @package EazyDocs
 * @since   2.9.0
 */
import { useMutation, useQueryClient } from '@tanstack/react-query';
import apiFetch from '@wordpress/api-fetch';
import { __ } from '@wordpress/i18n';
import { PARENTS_QUERY_KEY, CHILDREN_QUERY_KEY } from './useBuilderData';

declare const window: Window & {
	Swal?: any;
};

interface VisibilityParams {
	docId: number;
	visibility: 'publish' | 'private' | 'protected';
	password?: string;
	roles?: string[];
	allowGuests?: boolean;
	applyToChildren?: boolean;
}

interface SidebarParams {
	docId: number;
	leftType: string;
	leftContent: string;
	rightType: string;
	rightContent: string;
}

/**
 * Hook: update doc visibility via REST API.
 */
export const useUpdateVisibility = () => {
	const queryClient = useQueryClient();

	return useMutation( {
		mutationFn: async ( params: VisibilityParams ) => {
			const response = await apiFetch< { success: boolean; redirect: string } >( {
				path: '/eazydocs/v1/docs-builder/update-visibility',
				method: 'POST',
				data: {
					doc_id: params.docId,
					visibility: params.visibility,
					password: params.password || '',
					roles: params.roles || [],
					allowGuests: params.allowGuests || false,
					applyToChildren: params.applyToChildren || false,
				},
			} );

			return response;
		},
		onSuccess: async ( response ) => {
			if ( response?.redirect ) {
				await fetch( response.redirect, { redirect: 'follow' } );
			}
			await queryClient.invalidateQueries( { queryKey: PARENTS_QUERY_KEY } );
			await queryClient.invalidateQueries( { queryKey: CHILDREN_QUERY_KEY } );

			if ( typeof window.Swal !== 'undefined' ) {
				window.Swal.fire( {
					title: __( 'Success!', 'eazydocs' ),
					text: __( 'Visibility updated successfully.', 'eazydocs' ),
					icon: 'success',
					toast: true,
					position: 'top-end',
					timer: 3000,
					showConfirmButton: false,
				} );
			}
		},
	} );
};

/**
 * Hook: duplicate a doc via REST API.
 */
export const useDuplicateDoc = () => {
	const queryClient = useQueryClient();

	return useMutation( {
		mutationFn: async ( docId: number ) => {
			const response = await apiFetch< { success: boolean; newId: number; parentId: number } >( {
				path: '/eazydocs/v1/docs-builder/duplicate',
				method: 'POST',
				data: {
					doc_id: docId,
				},
			} );

			return response;
		},
		onSuccess: async () => {
			await queryClient.invalidateQueries( { queryKey: PARENTS_QUERY_KEY } );
			await queryClient.invalidateQueries( { queryKey: CHILDREN_QUERY_KEY } );
		},
	} );
};

/**
 * Hook: update sidebar settings via REST API.
 */
export const useUpdateSidebar = () => {
	const queryClient = useQueryClient();

	return useMutation( {
		mutationFn: async ( params: SidebarParams ) => {
			const response = await apiFetch< { success: boolean } >( {
				path: '/eazydocs/v1/docs-builder/update-sidebar',
				method: 'POST',
				data: {
					doc_id: params.docId,
					leftType: params.leftType,
					leftContent: params.leftContent,
					rightType: params.rightType,
					rightContent: params.rightContent,
				},
			} );

			return response;
		},
		onSuccess: async () => {
			await queryClient.invalidateQueries( { queryKey: PARENTS_QUERY_KEY } );
			await queryClient.invalidateQueries( { queryKey: CHILDREN_QUERY_KEY } );

			if ( typeof window.Swal !== 'undefined' ) {
				window.Swal.fire( {
					title: __( 'Success!', 'eazydocs' ),
					text: __( 'Sidebar updated successfully.', 'eazydocs' ),
					icon: 'success',
					toast: true,
					position: 'top-end',
					timer: 3000,
					showConfirmButton: false,
				} );
			}
		},
		onError: () => {
			if ( typeof window.Swal !== 'undefined' ) {
				window.Swal.fire( {
					title: __( 'Error', 'eazydocs' ),
					text: __( 'Failed to update sidebar.', 'eazydocs' ),
					icon: 'error',
				} );
			}
		},
	} );
};
