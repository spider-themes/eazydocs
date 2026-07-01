/**
 * Header component for the Docs Builder.
 *
 * Fully React-based rendering – no dangerouslySetInnerHTML,
 * no direct DOM manipulation, no jQuery triggers.
 *
 * @package EazyDocs
 * @since   2.8.0
 */
import { useRef, useMemo } from '@wordpress/element';
import { __, _n } from '@wordpress/i18n';
import { useCreateParentDoc } from '../hooks/useBuilderData';
import type { DocChild } from '../types';
import { useSearch } from '../hooks/useSearch';
import { useAiCreate } from '../hooks/useAiCreate';
import { showNotificationProAlert } from '../utils/pro-alert';
import { promptForDocTitle, showCreateSuccess, showCreateError } from '../utils/prompt';
import NotificationPanel from './NotificationPanel';
import type { BuilderData } from '../types';

declare const eazydocs_local_object: any;

interface HeaderProps {
	data: BuilderData;
	onTabChange?: ( docId: number ) => void;
}

const Header: React.FC< HeaderProps > = ( { data, onTabChange } ) => {
	const { capabilities, isPremium, antimanualActive, trashCount, urls, nonces } = data;
	const { searchValue, setSearchValue } = useSearch();

	// Aggregate doc stats across every parent and descendant so the header
	// gives an at-a-glance sense of the knowledge base size and status mix.
	const stats = useMemo( () => {
		let total = 0;
		let drafts = 0;
		let priv = 0;

		const tally = ( status: string ): void => {
			total += 1;
			if ( 'draft' === status ) {
				drafts += 1;
			} else if ( 'private' === status ) {
				priv += 1;
			}
		};

		const visit = ( nodes: DocChild[] ): void => {
			nodes.forEach( ( node ) => {
				tally( node.status );
				if ( node.children.length ) {
					visit( node.children );
				}
			} );
		};

		data.parentDocs.forEach( ( parent ) => tally( parent.status ) );
		Object.values( data.childrenMap ).forEach( ( nodes ) => visit( nodes ) );

		return { total, drafts, priv };
	}, [ data.parentDocs, data.childrenMap ] );
	const searchInputRef = useRef< HTMLInputElement >( null );
	const createParentDoc = useCreateParentDoc();
	const { triggerAiCreate } = useAiCreate( antimanualActive );

	/**
	 * Handle "Add Doc" button click – prompt for a title then create the doc.
	 */
	const handleAddDoc = async ( e: React.MouseEvent< HTMLButtonElement > ): Promise< void > => {
		e.preventDefault();

		// Guard against double submissions while a create is in flight.
		if ( createParentDoc.isPending ) {
			return;
		}

		const prompt = await promptForDocTitle();
		if ( ! prompt ) {
			return;
		}

		createParentDoc.mutate(
			{
				title: prompt.title,
				nonce: nonces.parentDoc,
				postStatus: prompt.status,
			},
			{
				onSuccess: ( response ) => {
					// Switch to the newly created doc tab.
					if ( response?.data?.id && onTabChange ) {
						onTabChange( response.data.id );
					}

					showCreateSuccess(
						'draft' === prompt.status
							? __( 'Documentation saved as draft.', 'eazydocs' )
							: __( 'Documentation created successfully.', 'eazydocs' )
					);
				},
				onError: () => {
					showCreateError( __( 'Failed to create documentation.', 'eazydocs' ) );
				},
			}
		);
	};

	/**
	 * Handle search input – update search context.
	 */
	const handleSearch = ( e: React.ChangeEvent< HTMLInputElement > ): void => {
		setSearchValue( e.target.value );
	};

	/**
	 * Handle the "Create Doc with AI" button click.
	 */
	const handleAiCreate = ( e: React.MouseEvent< HTMLButtonElement > ): void => {
		if ( antimanualActive ) {
			return;
		}
		e.preventDefault();
		triggerAiCreate();
	};

	return (
		<header className="easydocs-header-area">
			<div className="row alignment-center justify-content-between ml-0">
				<div className="navbar-left d-flex alignment-center">
					<div className="easydocs-logo-area">
						<span className="easydocs-builder-title">
							{ __( 'Documentations', 'eazydocs' ) }
						</span>
						{ stats.total > 0 && (
							<span className="ezd-builder-stats" aria-label={ __( 'Documentation totals', 'eazydocs' ) }>
								<span className="ezd-builder-stat ezd-builder-stat--total">
									<strong>{ stats.total }</strong>
									{ _n( 'doc', 'docs', stats.total, 'eazydocs' ) }
								</span>
								{ stats.drafts > 0 && (
									<span className="ezd-builder-stat ezd-builder-stat--draft">
										<strong>{ stats.drafts }</strong>
										{ _n( 'draft', 'drafts', stats.drafts, 'eazydocs' ) }
									</span>
								) }
								{ stats.priv > 0 && (
									<span className="ezd-builder-stat ezd-builder-stat--private">
										<strong>{ stats.priv }</strong>
										{ __( 'private', 'eazydocs' ) }
									</span>
								) }
							</span>
						) }
					</div>

					{ capabilities.canPublishDocs && (
						<button
							type="button"
							id="parent-doc"
							className="easydocs-btn filled easydocs-btn-sm easydocs-btn-round"
							onClick={ handleAddDoc }
							disabled={ createParentDoc.isPending }
							aria-busy={ createParentDoc.isPending }
						>
							<span className={ `dashicons ${ createParentDoc.isPending ? 'dashicons-update ezd-spin' : 'dashicons-plus-alt2' }` }></span>
							{ createParentDoc.isPending ? __( 'Adding…', 'eazydocs' ) : __( 'Add Doc', 'eazydocs' ) }
						</button>
					) }

					{ antimanualActive ? (
						<a
							id="ezd-create-doc-with-ai"
							href={ urls.antimanualDocs }
							className="easydocs-btn easydocs-btn-sm easydocs-btn-round ezd-docs-builder-ai-btn"
							role="button"
						>
							<svg width="16" height="16" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
								<path d="M12 2L13.1 8.9L20 10L13.1 11.1L12 18L10.9 11.1L4 10L10.9 8.9L12 2Z" fill="currentColor" />
							</svg>
							{ __( 'Create Doc with AI', 'eazydocs' ) }
						</a>
					) : (
						<button
							type="button"
							id="ezd-create-doc-with-ai"
							className="easydocs-btn easydocs-btn-sm easydocs-btn-round ezd-docs-builder-ai-btn"
							onClick={ handleAiCreate }
						>
							<svg width="16" height="16" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
								<path d="M12 2L13.1 8.9L20 10L13.1 11.1L12 18L10.9 11.1L4 10L10.9 8.9L12 2Z" fill="currentColor" />
							</svg>
							{ __( 'Create Doc with AI', 'eazydocs' ) }
						</button>
					) }
				</div>

				<form action="#" method="POST" className="easydocs-search-form" onSubmit={ ( e ) => e.preventDefault() }>
					<div className="search-icon">
						<span className="dashicons dashicons-search"></span>
					</div>
					<label htmlFor="easydocs-search" className="screen-reader-text">
						{ __( 'Search documentation', 'eazydocs' ) }
					</label>
					<input
						type="search"
						name="keyword"
						className="form-control"
						id="easydocs-search"
						placeholder={ __( 'Search documentation…', 'eazydocs' ) }
						aria-label={ __( 'Search documentation', 'eazydocs' ) }
						value={ searchValue }
						onChange={ handleSearch }
						ref={ searchInputRef }
					/>
					{ searchValue && (
						<button
							type="button"
							className="easydocs-search-clear"
							aria-label={ __( 'Clear search', 'eazydocs' ) }
							title={ __( 'Clear search', 'eazydocs' ) }
							onClick={ () => {
								setSearchValue( '' );
								searchInputRef.current?.focus();
							} }
						>
							<span className="dashicons dashicons-no-alt"></span>
						</button>
					) }
				</form>

				<div className="navbar-right">
					<ul className="d-flex justify-content-end">
						{ ( capabilities.canManageOptions || capabilities.canEditDocs ) && (
							<li>
								<div className="easydocs-settings">
									{ capabilities.canEditDocs && (
										<div className="header-notify-icons">
											<a
												href={ urls.classicUi }
												title={ __( 'Go to Classic UI', 'eazydocs' ) }
											>
												{ __( 'Classic UI', 'eazydocs' ) }
											</a>
										</div>
									) }

									{ capabilities.hasSettingsAccess && (
										<div
											className="header-notify-icon"
											title={ __( 'Central settings page', 'eazydocs' ) }
										>
											<a href={ urls.settings }>
												<img
													src={ urls.settingsIcon }
													alt={ __( 'Settings Icon', 'eazydocs' ) }
												/>
											</a>
										</div>
									) }

									<div
										className="header-notify-icon ezd-trashicon"
										title={ __( 'View, manage, restore the trashed docs', 'eazydocs' ) }
									>
										<a href={ urls.trash }>
											<span className="dashicons dashicons-trash"></span>
										</a>
										{ trashCount > 0 && (
											<span className="easydocs-badge"> { trashCount } </span>
										) }
									</div>
								</div>
							</li>
						) }

						{ capabilities.canManageOptions && (
							isPremium ? (
								<NotificationPanel
									notificationCount={ data.notificationCount }
									notificationIcon={ urls.notificationIcon }
								/>
							) : (
								<li
									className="easydocs-notification pro-notification-alert"
									title={ __( 'Notifications', 'eazydocs' ) }
									onClick={ ( e: React.MouseEvent ) => {
										e.preventDefault();
										showNotificationProAlert( urls.assetsUrl, urls.pricing );
									} }
									role="button"
									tabIndex={ 0 }
									onKeyDown={ ( e: React.KeyboardEvent ) => {
										if ( 13 === e.which || 32 === e.which ) {
											e.preventDefault();
											showNotificationProAlert( urls.assetsUrl, urls.pricing );
										}
									} }
								>
									<div className="header-notify-icon">
										<img
											className="notify-icon"
											src={ urls.notificationIcon }
											alt={ __( 'Notify Icon', 'eazydocs' ) }
										/>
										<img
											className="settings-pro-icon"
											src={ urls.proIcon }
											alt={ __( 'Pro Icon', 'eazydocs' ) }
										/>
									</div>
								</li>
							)
						) }
					</ul>
				</div>
			</div>
		</header>
	);
};

export default Header;