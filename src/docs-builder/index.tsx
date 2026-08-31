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
import apiFetch from '@wordpress/api-fetch';
import App from './App';

declare global {
	interface Window {
		wpApiSettings?: {
			root?: string;
			nonce?: string;
			versionString?: string;
		};
		ezdDocsBuilderData?: {
			root?: string;
			nonce?: string;
			adminUrl?: string;
		};
	}
}

// Ensure apiFetch root URL and REST nonce middlewares are registered.
const restRoot = window.ezdDocsBuilderData?.root || window.wpApiSettings?.root;
const restNonce = window.ezdDocsBuilderData?.nonce || window.wpApiSettings?.nonce;

if ( restRoot && typeof apiFetch.createRootURLMiddleware === 'function' ) {
	apiFetch.use( apiFetch.createRootURLMiddleware( restRoot ) );
}
if ( restNonce && typeof apiFetch.createNonceMiddleware === 'function' ) {
	apiFetch.use( apiFetch.createNonceMiddleware( restNonce ) );
}

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
