/**
 * TanStack Query hook for fetching notifications.
 *
 * Replaces the jQuery-based infinite scroll and dangerouslySetInnerHTML
 * approach with a proper React infinite query pattern.
 *
 * @package EazyDocs
 * @since   2.9.0
 */
import { useInfiniteQuery, useMutation, useQueryClient } from '@tanstack/react-query';
import apiFetch from '@wordpress/api-fetch';
import type { NotificationResponse } from '../types';
import { COUNTS_QUERY_KEY } from './useBuilderData';

/** Cache key for notifications. */
export const NOTIFICATIONS_QUERY_KEY = [ 'docs-builder-notifications' ] as const;

/**
 * Fetch a page of notifications from the REST API.
 *
 * @param {number} page     Page number to fetch.
 * @param {number} perPage  Items per page.
 * @param {string} filter   Filter type: 'all', 'comment', or 'vote'.
 * @return {Promise<NotificationResponse>} Notification response.
 */
const fetchNotifications = (
	page: number,
	perPage: number,
	filter: string
): Promise< NotificationResponse > => {
	return apiFetch< NotificationResponse >( {
		path: `/eazydocs/v1/docs-builder/notifications?page=${ page }&per_page=${ perPage }&filter=${ filter }`,
	} );
};

/**
 * Hook: infinite query for paginated notifications.
 *
 * @param {string}  filter  Active filter type.
 * @param {boolean} enabled Whether the query should run.
 * @return TanStack infinite query result.
 */
export const useNotifications = ( filter: string = 'all', enabled: boolean = true ) => {
	return useInfiniteQuery< NotificationResponse >( {
		queryKey: [ ...NOTIFICATIONS_QUERY_KEY, filter ],
		queryFn: ( { pageParam = 1 } ) => fetchNotifications( pageParam as number, 10, filter ),
		getNextPageParam: ( lastPage ) => {
			if ( lastPage.hasMore ) {
				return lastPage.page + 1;
			}
			return undefined;
		},
		initialPageParam: 1,
		enabled,
		staleTime: 60_000,
		refetchOnWindowFocus: false,
	} );
};

/**
 * Hook: mutate a notification item as read.
 */
export const useMarkNotificationRead = () => {
	const queryClient = useQueryClient();

	return useMutation( {
		mutationFn: async ( params: { type: string; postId: number; timestamp?: number } ) => {
			const response = await apiFetch<{ success: boolean }>( {
				path: '/eazydocs/v1/docs-builder/mark-read',
				method: 'POST',
				data: params,
			} );
			return response;
		},
		onSuccess: async () => {
			await queryClient.invalidateQueries( { queryKey: COUNTS_QUERY_KEY } );
		},
	} );
};
