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
import { __ } from '@wordpress/i18n';
import Header from './components/Header';
import ParentDocs from './components/ParentDocs';
import ChildDocs from './components/ChildDocs';
import EmptyState from './components/EmptyState';
import BuilderSkeleton from './components/BuilderSkeleton';
import { useSettingsQuery, useParentDocsQuery, useChildDocsQuery, useCountsQuery } from './hooks/useBuilderData';
import type { BuilderData } from './types';
import { SearchProvider } from './hooks/useSearch';
import { ToastProvider } from './hooks/useToast';
import { useRoute } from '../hooks/use-route';

const App: React.FC = () => {
	const { query, updateQuery } = useRoute();

	const { data: settings, isLoading: isLoadingSettings, isError: isErrorSettings } = useSettingsQuery();
	const { data: parentDocs, isLoading: isLoadingParents, isError: isErrorParents } = useParentDocsQuery();
	const { data: childrenMap, isLoading: isLoadingChildren, isError: isErrorChildren } = useChildDocsQuery();
	const { data: counts, isLoading: isLoadingCounts, isError: isErrorCounts } = useCountsQuery();

	const isLoading = isLoadingSettings || isLoadingParents || isLoadingChildren || isLoadingCounts;
	const isError = isErrorSettings || isErrorParents || isErrorChildren || isErrorCounts;

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
		updateQuery( { active_doc: String( docId ) } );
	}, [ updateQuery ] );

	if ( isLoading ) {
		return <BuilderSkeleton />;
	}

	if ( isError || ! data ) {
		return (
			<div className="ezd_docs_builder">
				<p>{ __( 'Failed to load builder data.', 'eazydocs' ) }</p>
			</div>
		);
	}

	const hasDocs = data.parentDocs.length > 0;

	return (
		<SearchProvider>
			<ToastProvider>
				<div className="ezd_docs_builder">
					{ hasDocs ? (
						<>
							<Header data={ data } onTabChange={ handleTabChange } />
							<main>
								<div className="easydocs-sidebar-menu">
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
										<div className="easydocs-tab-content" style={ { flex: 8 } }>
											{ data.parentDocs.map( ( parent ) => (
												<ChildDocs
													key={ parent.id }
													parent={ parent }
													children={ data.childrenMap[ parent.id ] || [] }
													isActive={ parent.id === activeTab }
													capabilities={ data.capabilities }
													isPremium={ data.isPremium }
													urls={ data.urls }
													currentTheme={ data.currentTheme }
													roleVisibility={ data.roleVisibility }
												/>
											) ) }
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
