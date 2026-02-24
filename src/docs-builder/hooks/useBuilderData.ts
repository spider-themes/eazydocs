/**
 * TanStack Query hooks for the Docs Builder.
 *
 * Provides useQuery / useMutation wrappers around the
 * eazydocs/v1/docs-builder REST endpoints so the React
 * tree can fetch, mutate, and refetch without full-page
 * reloads.
 *
 * @package EazyDocs
 * @since   2.8.0
 */
import { useQuery, useMutation, useQueryClient } from '@tanstack/react-query';
import apiFetch from '@wordpress/api-fetch';
import type { BuilderData, ParentDoc, DocChild } from '../types';

/** Cache keys for separated queries. */
export const SETTINGS_QUERY_KEY = [ 'docs-settings' ] as const;
export const PARENTS_QUERY_KEY = [ 'docs-parents' ] as const;
export const CHILDREN_QUERY_KEY = [ 'docs-children' ] as const;
export const COUNTS_QUERY_KEY = [ 'docs-counts' ] as const;

/**
 * Hook: fetch settings data.
 */
export const useSettingsQuery = () => {
	return useQuery<Omit<BuilderData, 'parentDocs' | 'childrenMap' | 'trashCount' | 'notificationCount'>>({
		queryKey: SETTINGS_QUERY_KEY,
		queryFn: () => apiFetch( { path: '/eazydocs/v1/docs-builder/settings' } ),
		staleTime: 300_000,
		refetchOnWindowFocus: false,
	});
};

/**
 * Hook: fetch parent docs array.
 */
export const useParentDocsQuery = () => {
	return useQuery<ParentDoc[]>({
		queryKey: PARENTS_QUERY_KEY,
		queryFn: () => apiFetch( { path: '/eazydocs/v1/docs-builder/parents' } ),
		staleTime: 60_000,
		refetchOnWindowFocus: false,
	});
};

/**
 * Hook: fetch child docs map.
 */
export const useChildDocsQuery = () => {
	return useQuery<{ [key: number]: DocChild[] }>({
		queryKey: CHILDREN_QUERY_KEY,
		queryFn: () => apiFetch( { path: '/eazydocs/v1/docs-builder/children' } ),
		staleTime: 60_000,
		refetchOnWindowFocus: false,
	});
};

/**
 * Hook: fetch notification and trash counts on a polling interval.
 */
export const useCountsQuery = () => {
	return useQuery<{ trashCount: number; notificationCount: number }>({
		queryKey: COUNTS_QUERY_KEY,
		queryFn: () => apiFetch( { path: '/eazydocs/v1/docs-builder/counts' } ),
		refetchInterval: 30_000, // Poll every 30s in the background
		staleTime: 10_000,
	});
};

/* ------------------------------------------------------------------ */
/*  Mutations                                                         */
/* ------------------------------------------------------------------ */

interface CreateDocParams {
	title: string;
	nonce: string;
}

/**
 * Hook: create a new parent doc via AJAX.
 *
 * On success the builder query is invalidated so the sidebar
 * updates without a page reload.
 */
export const useCreateParentDoc = () => {
	const queryClient = useQueryClient();

	return useMutation( {
		mutationFn: async ( params: CreateDocParams ) => {
			let value = params.title.replaceAll( '&', 'ezd_ampersand' );
			value = value.replaceAll( '#', 'ezd_hash' );
			value = value.replaceAll( '+', 'ezd_plus' );

			const response = await apiFetch<{ success: boolean; data: { id: number; redirect: string } }>( {
				path: '/eazydocs/v1/docs-builder/create-parent',
				method: 'POST',
				data: {
					title: value,
					_wpnonce: params.nonce,
				},
			} );

			return response;
		},
		onSuccess: async () => {
			await queryClient.invalidateQueries( { queryKey: PARENTS_QUERY_KEY } );
		},
	} );
};

interface DeleteDocParams {
	docId: number;
	nonce: string;
}

/**
 * Hook: trash a doc (parent or child) via AJAX.
 */
export const useDeleteDoc = () => {
	const queryClient = useQueryClient();

	return useMutation( {
		mutationFn: async ( params: DeleteDocParams ) => {
			const response = await apiFetch<{ success: boolean }>( {
				path: '/eazydocs/v1/docs-builder/delete',
				method: 'POST',
				data: {
					doc_id: params.docId,
					_wpnonce: params.nonce,
				},
			} );

			return response;
		},
		onSuccess: async () => {
			await queryClient.invalidateQueries( { queryKey: PARENTS_QUERY_KEY } );
			await queryClient.invalidateQueries( { queryKey: CHILDREN_QUERY_KEY } );
			await queryClient.invalidateQueries( { queryKey: COUNTS_QUERY_KEY } );
		},
	} );
};

interface CreateSectionParams {
	parentId: number;
	title: string;
	nonce: string;
}

/**
 * Hook: create a new section under a parent doc.
 */
export const useCreateSection = () => {
	const queryClient = useQueryClient();

	return useMutation( {
		mutationFn: async ( params: CreateSectionParams ) => {
			let value = params.title.replaceAll( '&', 'ezd_ampersand' );
			value = value.replaceAll( '#', 'ezd_hash' );
			value = value.replaceAll( '+', 'ezd_plus' );

			const response = await apiFetch<{ success: boolean; data: { id: number } }>( {
				path: '/eazydocs/v1/docs-builder/create-section',
				method: 'POST',
				data: {
					parent_id: params.parentId,
					title: value,
					_wpnonce: params.nonce,
				},
			} );

			return response;
		},
		onSuccess: async () => {
			await queryClient.invalidateQueries( { queryKey: CHILDREN_QUERY_KEY } );
		},
	} );
};

interface CreateChildParams {
	parentId: number;
	title: string;
	nonce: string;
}

/**
 * Hook: create a child doc under a section.
 */
export const useCreateChild = () => {
	const queryClient = useQueryClient();

	return useMutation( {
		mutationFn: async ( params: CreateChildParams ) => {
			let value = params.title.replaceAll( '&', 'ezd_ampersand' );
			value = value.replaceAll( '#', 'ezd_hash' );
			value = value.replaceAll( '+', 'ezd_plus' );

			const response = await apiFetch<{ success: boolean; data: { id: number } }>( {
				path: '/eazydocs/v1/docs-builder/create-child',
				method: 'POST',
				data: {
					parent_id: params.parentId,
					title: value,
					_wpnonce: params.nonce,
				},
			} );

			return response;
		},
		onSuccess: async () => {
			await queryClient.invalidateQueries( { queryKey: CHILDREN_QUERY_KEY } );
		},
	} );
};

interface ReorderParams {
	data: string;
	action: string;
}

/**
 * Hook: reorder docs (parent or child nestable).
 */
export const useReorderDocs = () => {
	const queryClient = useQueryClient();

	return useMutation( {
		mutationFn: async ( params: ReorderParams ) => {
			const formData = new FormData();
			formData.append( 'action', params.action );
			formData.append( 'data', params.data );
			formData.append( 'security', ( window as any ).eazydocs_local_object?.nonce || '' );

			const response = await apiFetch<any>( {
				url: ( window as any ).eazydocs_local_object?.ajaxurl || '/wp-admin/admin-ajax.php',
				method: 'POST',
				body: formData,
			} as any );

			return response;
		},
		onSuccess: async () => {
			await queryClient.invalidateQueries( { queryKey: PARENTS_QUERY_KEY } );
			await queryClient.invalidateQueries( { queryKey: CHILDREN_QUERY_KEY } );
		},
	} );
};
