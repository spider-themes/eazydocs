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
import type { QueryClient } from '@tanstack/react-query';
import apiFetch from '@wordpress/api-fetch';
import type { BuilderData, ParentDoc, DocChild } from '../types';

/** Cache keys for separated queries. */
export const BUILDER_BOOTSTRAP_QUERY_KEY = [ 'docs-builder-bootstrap' ] as const;
export const SETTINGS_QUERY_KEY = [ 'docs-settings' ] as const;
export const PARENTS_QUERY_KEY = [ 'docs-parents' ] as const;
export const CHILDREN_QUERY_KEY = [ 'docs-children' ] as const;
export const COUNTS_QUERY_KEY = [ 'docs-counts' ] as const;
export const getParentChildrenQueryKey = ( parentId: number ) => [ 'docs-children-parent', parentId ] as const;

interface BuilderQueryOptions< T > {
	enabled?: boolean;
	initialData?: T;
	initialDataUpdatedAt?: number;
}

const normalizeTitle = ( value: string ): string => {
	let normalized = value.replaceAll( '&', 'ezd_ampersand' );
	normalized = normalized.replaceAll( '#', 'ezd_hash' );
	normalized = normalized.replaceAll( '+', 'ezd_plus' );
	return normalized;
};

const appendChildNode = ( items: DocChild[], targetId: number, child: DocChild ): DocChild[] => {
	return items.map( ( item ) => {
		if ( item.id === targetId ) {
			return {
				...item,
				childCount: item.childCount + 1,
				children: [ ...item.children, child ],
			};
		}

		if ( item.children.length === 0 ) {
			return item;
		}

		const updatedChildren = appendChildNode( item.children, targetId, child );
		if ( updatedChildren === item.children ) {
			return item;
		}

		return {
			...item,
			childCount: item.childCount + 1,
			children: updatedChildren,
		};
	} );
};

const updateChildrenTree = (
	childrenMap: Record<number, DocChild[]> | undefined,
	rootParentId: number,
	updater: ( nodes: DocChild[] ) => DocChild[]
): Record<number, DocChild[]> | undefined => {
	if ( ! childrenMap ) {
		return childrenMap;
	}

	return {
		...childrenMap,
		[ rootParentId ]: updater( childrenMap[ rootParentId ] || [] ),
	};
};

const getBuilderBootstrapPath = ( activeDocId?: number | null ): string => {
	if ( ! activeDocId ) {
		return '/eazydocs/v1/docs-builder';
	}

	return `/eazydocs/v1/docs-builder?active_doc=${ activeDocId }`;
};

const getChildrenPath = ( parentId?: number | null ): string => {
	if ( ! parentId ) {
		return '/eazydocs/v1/docs-builder/children';
	}

	return `/eazydocs/v1/docs-builder/children?parent_id=${ parentId }`;
};

export const fetchChildrenMap = ( parentId?: number | null ) => {
	return apiFetch<Record<number, DocChild[]>>( { path: getChildrenPath( parentId ) } );
};

export const prefetchParentChildren = async ( queryClient: QueryClient, parentId: number ) => {
	const childrenMap = await queryClient.fetchQuery<Record<number, DocChild[]>>( {
		queryKey: getParentChildrenQueryKey( parentId ),
		queryFn: () => fetchChildrenMap( parentId ),
		staleTime: 60_000,
	} );

	queryClient.setQueryData<Record<number, DocChild[]>>( CHILDREN_QUERY_KEY, ( previous = {} ) => ( {
		...previous,
		...childrenMap,
	} ) );

	return childrenMap;
};

/**
 * Hook: fetch the entire builder payload in one request for first-load bootstrap.
 */
export const useBuilderBootstrapQuery = ( activeDocId?: number | null ) => {
	return useQuery<BuilderData>({
		queryKey: BUILDER_BOOTSTRAP_QUERY_KEY,
		queryFn: () => apiFetch( { path: getBuilderBootstrapPath( activeDocId ) } ),
		staleTime: 60_000,
		refetchOnWindowFocus: false,
		retry: 1,
	});
};

/**
 * Hook: fetch settings data.
 */
export const useSettingsQuery = ( options: BuilderQueryOptions<Omit<BuilderData, 'parentDocs' | 'childrenMap' | 'trashCount' | 'notificationCount'>> = {} ) => {
	return useQuery<Omit<BuilderData, 'parentDocs' | 'childrenMap' | 'trashCount' | 'notificationCount'>>({
		queryKey: SETTINGS_QUERY_KEY,
		queryFn: () => apiFetch( { path: '/eazydocs/v1/docs-builder/settings' } ),
		staleTime: 300_000,
		refetchOnWindowFocus: false,
		enabled: options.enabled,
		initialData: options.initialData,
		initialDataUpdatedAt: options.initialDataUpdatedAt,
	});
};

/**
 * Hook: fetch parent docs array.
 */
export const useParentDocsQuery = ( options: BuilderQueryOptions<ParentDoc[]> = {} ) => {
	return useQuery<ParentDoc[]>({
		queryKey: PARENTS_QUERY_KEY,
		queryFn: () => apiFetch( { path: '/eazydocs/v1/docs-builder/parents' } ),
		staleTime: 60_000,
		refetchOnWindowFocus: false,
		enabled: options.enabled,
		initialData: options.initialData,
		initialDataUpdatedAt: options.initialDataUpdatedAt,
	});
};

/**
 * Hook: fetch child docs map.
 */
export const useChildDocsQuery = ( options: BuilderQueryOptions<Record<number, DocChild[]>> = {} ) => {
	return useQuery<{ [key: number]: DocChild[] }>({
		queryKey: CHILDREN_QUERY_KEY,
		queryFn: () => fetchChildrenMap(),
		staleTime: 60_000,
		refetchOnWindowFocus: false,
		enabled: options.enabled,
		initialData: options.initialData,
		initialDataUpdatedAt: options.initialDataUpdatedAt,
	});
};

/**
 * Hook: fetch notification and trash counts on a polling interval.
 */
export const useCountsQuery = ( options: BuilderQueryOptions<{ trashCount: number; notificationCount: number }> = {} ) => {
	return useQuery<{ trashCount: number; notificationCount: number }>({
		queryKey: COUNTS_QUERY_KEY,
		queryFn: () => apiFetch( { path: '/eazydocs/v1/docs-builder/counts' } ),
		refetchInterval: 30_000, // Poll every 30s in the background
		staleTime: 10_000,
		enabled: options.enabled,
		initialData: options.initialData,
		initialDataUpdatedAt: options.initialDataUpdatedAt,
	});
};

/* ------------------------------------------------------------------ */
/*  Mutations                                                         */
/* ------------------------------------------------------------------ */

interface CreateDocParams {
	title: string;
	nonce: string;
	postStatus?: 'publish' | 'draft';
}

interface CreateParentResponse {
	success: boolean;
	data: {
		id: number;
		redirect: string;
		doc: ParentDoc;
	};
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
			const response = await apiFetch<CreateParentResponse>( {
				path: '/eazydocs/v1/docs-builder/create-parent',
				method: 'POST',
				data: {
					title: normalizeTitle( params.title ),
					post_status: params.postStatus || 'publish',
					_wpnonce: params.nonce,
				},
			} );

			return response;
		},
		onSuccess: async ( response ) => {
			queryClient.setQueryData<ParentDoc[]>( PARENTS_QUERY_KEY, ( previous = [] ) => [ ...previous, response.data.doc ] );
			queryClient.setQueryData<Record<number, DocChild[]>>( CHILDREN_QUERY_KEY, ( previous = {} ) => ( {
				...previous,
				[ response.data.id ]: previous[ response.data.id ] || [],
			} ) );
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
	postStatus?: 'publish' | 'draft';
}

interface CreateSectionResponse {
	success: boolean;
	data: {
		id: number;
		parentId: number;
		item: DocChild;
	};
}

/**
 * Hook: create a new section under a parent doc.
 */
export const useCreateSection = () => {
	const queryClient = useQueryClient();

	return useMutation( {
		mutationFn: async ( params: CreateSectionParams ) => {
			const response = await apiFetch<CreateSectionResponse>( {
				path: '/eazydocs/v1/docs-builder/create-section',
				method: 'POST',
				data: {
					parent_id: params.parentId,
					title: normalizeTitle( params.title ),
					post_status: params.postStatus || 'publish',
					_wpnonce: params.nonce,
				},
			} );

			return response;
		},
		onSuccess: async ( response ) => {
			queryClient.setQueryData<Record<number, DocChild[]>>( CHILDREN_QUERY_KEY, ( previous ) => updateChildrenTree(
				previous,
				response.data.parentId,
				( nodes ) => [ ...nodes, response.data.item ]
			) );

			queryClient.setQueryData<ParentDoc[]>( PARENTS_QUERY_KEY, ( previous = [] ) => previous.map( ( parent ) =>
				parent.id === response.data.parentId
					? { ...parent, childCount: parent.childCount + 1 }
					: parent
			) );
		},
	} );
};

interface CreateChildParams {
	parentId: number;
	rootParentId: number;
	title: string;
	nonce: string;
	postStatus?: 'publish' | 'draft';
}

interface CreateChildResponse {
	success: boolean;
	data: {
		id: number;
		parentId: number;
		rootParentId: number;
		item: DocChild;
	};
}

/**
 * Hook: create a child doc under a section.
 */
export const useCreateChild = () => {
	const queryClient = useQueryClient();

	return useMutation( {
		mutationFn: async ( params: CreateChildParams ) => {
			const response = await apiFetch<CreateChildResponse>( {
				path: '/eazydocs/v1/docs-builder/create-child',
				method: 'POST',
				data: {
					parent_id: params.parentId,
					title: normalizeTitle( params.title ),
					post_status: params.postStatus || 'publish',
					_wpnonce: params.nonce,
				},
			} );

			return response;
		},
		onSuccess: async ( response ) => {
			queryClient.setQueryData<Record<number, DocChild[]>>( CHILDREN_QUERY_KEY, ( previous ) => updateChildrenTree(
				previous,
				response.data.rootParentId,
				( nodes ) => appendChildNode( nodes, response.data.parentId, response.data.item )
			) );

			queryClient.setQueryData<ParentDoc[]>( PARENTS_QUERY_KEY, ( previous = [] ) => previous.map( ( parent ) =>
				parent.id === response.data.rootParentId
					? { ...parent, childCount: parent.childCount + 1 }
					: parent
			) );
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
