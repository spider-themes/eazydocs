/**
 * EazyDocs Docs Builder – Root App component.
 *
 * Uses TanStack Query to fetch builder data from the REST API,
 * then renders the Header + Main (Sidebar + Content) layout.
 * Wraps tree with SearchProvider and ToastProvider for
 * React-friendly state management.
 *
 * @package EazyDocs
 * @since   2.8.0
 */
import { useMemo, useEffect, useCallback } from '@wordpress/element';
import { useQueryClient } from '@tanstack/react-query';
import { __ } from '@wordpress/i18n';
import Header from './components/Header';
import ParentDocs from './components/ParentDocs';
import ChildDocs from './components/ChildDocs';
import EmptyState from './components/EmptyState';
import BuilderSkeleton from './components/BuilderSkeleton';
import { CHILDREN_QUERY_KEY, prefetchParentChildren, useBuilderBootstrapQuery, useSettingsQuery, useParentDocsQuery, useChildDocsQuery, useCountsQuery } from './hooks/useBuilderData';
import type { BuilderData } from './types';
import type { DocChild } from './types';
import { SearchProvider } from './hooks/useSearch';
import { ToastProvider } from './hooks/useToast';
import { useRoute } from '../hooks/use-route';

const App: React.FC = () => {
	const { query, updateQuery } = useRoute();
	const queryClient = useQueryClient();
	const requestedActiveDoc = query.active_doc ? parseInt( query.active_doc, 10 ) : null;
	const bootstrapQuery = useBuilderBootstrapQuery( requestedActiveDoc );
	const bootstrapData = bootstrapQuery.data;
	const bootstrapUpdatedAt = bootstrapQuery.dataUpdatedAt || Date.now();
	const queryEnabled = ! bootstrapQuery.isPending;

	const { data: settings, isLoading: isLoadingSettings, isError: isErrorSettings } = useSettingsQuery( {
		enabled: queryEnabled,
		initialData: bootstrapData ? {
			capabilities: bootstrapData.capabilities,
			isPremium: bootstrapData.isPremium,
			antimanualActive: bootstrapData.antimanualActive,
			roleVisibility: bootstrapData.roleVisibility,
			urls: bootstrapData.urls,
			nonces: bootstrapData.nonces,
			currentTheme: bootstrapData.currentTheme,
		} : undefined,
		initialDataUpdatedAt: bootstrapUpdatedAt,
	} );
	const { data: parentDocs, isLoading: isLoadingParents, isError: isErrorParents } = useParentDocsQuery( {
		enabled: queryEnabled,
		initialData: bootstrapData?.parentDocs,
		initialDataUpdatedAt: bootstrapUpdatedAt,
	} );
	const { data: childrenMap, isLoading: isLoadingChildren, isError: isErrorChildren } = useChildDocsQuery( {
		enabled: queryEnabled,
		initialData: bootstrapData?.childrenMap,
		initialDataUpdatedAt: bootstrapUpdatedAt,
	} );
	const { data: counts, isLoading: isLoadingCounts, isError: isErrorCounts } = useCountsQuery( {
		enabled: queryEnabled,
		initialData: bootstrapData ? {
			trashCount: bootstrapData.trashCount,
			notificationCount: bootstrapData.notificationCount,
		} : undefined,
		initialDataUpdatedAt: bootstrapUpdatedAt,
	} );

	const isLoading = bootstrapQuery.isPending || isLoadingSettings || isLoadingParents || isLoadingChildren || isLoadingCounts;
	const isError = bootstrapQuery.isError || isErrorSettings || isErrorParents || isErrorChildren || isErrorCounts;

	const data = useMemo( (): BuilderData | null => {
		if ( !settings || !parentDocs || !childrenMap || !counts ) {
			return null;
		}
		return {
			...settings,
			parentDocs,
			childrenMap,
			trashCount: counts.trashCount,
			notificationCount: counts.notificationCount,
		} as BuilderData;
	}, [ settings, parentDocs, childrenMap, counts ] );

	/**
	 * Derive active tab from the URL query parameter.
	 * Falls back to the first parent doc when the query param is missing or invalid.
	 */
	const activeTab = useMemo( () => {
		if ( ! data ) {
			return null;
		}

		const activeDocFromQuery = query.active_doc;

		if ( activeDocFromQuery ) {
			const docId = parseInt( activeDocFromQuery, 10 );
			const docExists = data.parentDocs.some( ( d ) => d.id === docId );
			if ( docExists ) {
				return docId;
			}
		}

		// Fallback to first doc.
		return data.parentDocs.length > 0 ? data.parentDocs[ 0 ].id : null;
	}, [ data, query.active_doc ] );

	// Ensure the URL always has a valid `active_doc` query param.
	useEffect( () => {
		if ( ! data || null === activeTab ) {
			return;
		}

		const currentQueryDoc = query.active_doc ? parseInt( query.active_doc, 10 ) : null;

		if ( currentQueryDoc !== activeTab ) {
			updateQuery( { active_doc: String( activeTab ) } );
		}
	}, [ data, activeTab, query.active_doc, updateQuery ] );

	// Handle tab change.
	const handleTabChange = useCallback( ( docId: number ): void => {
		const currentChildrenMap = queryClient.getQueryData<Record<number, DocChild[]>>( CHILDREN_QUERY_KEY );
		if ( currentChildrenMap && Object.prototype.hasOwnProperty.call( currentChildrenMap, docId ) ) {
			updateQuery( { active_doc: String( docId ) } );
			return;
		}

		prefetchParentChildren( queryClient, docId )
			.catch( () => undefined )
			.finally( () => {
				updateQuery( { active_doc: String( docId ) } );
			} );
	}, [ queryClient, updateQuery ] );

	const hasDocs = data ? data.parentDocs.length > 0 : false;
	const activeParent = data && activeTab ? data.parentDocs.find( ( parent ) => parent.id === activeTab ) ?? null : null;

	useEffect( () => {
		if ( ! data || ! activeParent ) {
			return;
		}

		let cancelled = false;

		const prefetchRemainingTabs = async () => {
			for ( const parent of data.parentDocs ) {
				if ( cancelled || parent.id === activeParent.id ) {
					continue;
				}

				const currentChildrenMap = queryClient.getQueryData<Record<number, DocChild[]>>( CHILDREN_QUERY_KEY );
				if ( currentChildrenMap && Object.prototype.hasOwnProperty.call( currentChildrenMap, parent.id ) ) {
					continue;
				}

				try {
					await prefetchParentChildren( queryClient, parent.id );
				} catch {
					// Ignore background prefetch errors so the active tab stays responsive.
				}
			}
		};

		prefetchRemainingTabs();

		return () => {
			cancelled = true;
		};
	}, [ activeParent, data?.parentDocs, queryClient ] );

	if ( isLoading ) {
		return <BuilderSkeleton />;
	}

	if ( isError || ! data ) {
		return (
			<div className="ezd-docs-builder-root">
				<div className="ezd-builder-error">
					<p>{ __( 'Failed to load builder data.', 'eazydocs' ) }</p>
				</div>
			</div>
		);
	}

	return (
		<SearchProvider>
			<ToastProvider>
				<div className="ezd-docs-builder-root">
					{ hasDocs ? (
						<>
							<Header data={ data } onTabChange={ handleTabChange } />
							<main>
								<div className="easydocs-sidebar-menu easydocs-builder-page">
									<div className="tab-container">
										<ParentDocs
											parentDocs={ data.parentDocs }
											activeTab={ activeTab }
											onTabChange={ handleTabChange }
											capabilities={ data.capabilities }
											isPremium={ data.isPremium }
											urls={ data.urls }
											roleVisibility={ data.roleVisibility }
										/>
										<div className="easydocs-tab-content ezd-builder-content">
											{ activeParent && (
												<ChildDocs
													key={ activeParent.id }
													parent={ activeParent }
													children={ data.childrenMap[ activeParent.id ] || [] }
													isActive={ true }
													capabilities={ data.capabilities }
													isPremium={ data.isPremium }
													urls={ data.urls }
													currentTheme={ data.currentTheme }
													roleVisibility={ data.roleVisibility }
												/>
											) }
										</div>
									</div>
								</div>
							</main>
						</>
					) : (
						<EmptyState data={ data } />
					) }
				</div>
			</ToastProvider>
		</SearchProvider>
	);
};

export default App;
