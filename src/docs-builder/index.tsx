/**
 * EazyDocs Docs Builder – React entry point.
 *
 * Wraps the App in QueryClientProvider so TanStack Query
 * is available throughout the component tree.
 *
 * @package EazyDocs
 * @since   2.8.0
 */
import { createRoot } from '@wordpress/element';
import { QueryClient, QueryClientProvider } from '@tanstack/react-query';
import App from './App';

const queryClient = new QueryClient( {
	defaultOptions: {
		queries: {
			retry: 1,
			refetchOnWindowFocus: false,
		},
	},
} );

document.addEventListener( 'DOMContentLoaded', () => {
	const container = document.getElementById( 'ezd-docs-builder-root' );
	if ( container ) {
		const root = createRoot( container );
		root.render(
			<QueryClientProvider client={ queryClient }>
				<App />
			</QueryClientProvider>
		);
	}
} );
