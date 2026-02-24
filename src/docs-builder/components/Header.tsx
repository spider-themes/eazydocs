/**
 * Header component for the Docs Builder.
 *
 * Fully React-based rendering – no dangerouslySetInnerHTML,
 * no direct DOM manipulation, no jQuery triggers.
 *
 * @package EazyDocs
 * @since   2.8.0
 */
import { useRef } from '@wordpress/element';
import { __ } from '@wordpress/i18n';
import { useCreateParentDoc } from '../hooks/useBuilderData';
import { useSearch } from '../hooks/useSearch';
import { useAiCreate } from '../hooks/useAiCreate';
import { showNotificationProAlert } from '../utils/pro-alert';
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
	const searchInputRef = useRef< HTMLInputElement >( null );
	const createParentDoc = useCreateParentDoc();
	const { triggerAiCreate } = useAiCreate( antimanualActive );

	/**
	 * Handle "Add Doc" button click – show SweetAlert prompt.
	 */
	const handleAddDoc = ( e: React.MouseEvent< HTMLButtonElement > ): void => {
		e.preventDefault();

		if ( typeof window.Swal !== 'undefined' ) {
			window.Swal.fire( {
				title: eazydocs_local_object.create_prompt_title,
				input: 'text',
				showCancelButton: true,
				inputAttributes: {
					name: 'new_doc',
				},
				preConfirm: ( value: string ) => {
					if ( ! value ) {
						return false;
					}

					// Show loading state on the modal button.
					window.Swal.showLoading();

					return new Promise( ( resolve, reject ) => {
						createParentDoc.mutate(
							{
								title: value,
								nonce: nonces.parentDoc,
							},
							{
								onSuccess: ( response ) => {
									resolve( response );
								},
								onError: ( error ) => {
									reject( error );
								},
							}
						);
					} );
				},
				allowOutsideClick: () => ! window.Swal.isLoading(),
			} ).then( ( result: any ) => {
				if ( result.isConfirmed && result.value ) {
					const response = result.value;

					// Switch to the newly created doc tab.
					if ( response?.data?.id && onTabChange ) {
						onTabChange( response.data.id );
					}

					if ( typeof window.Swal !== 'undefined' ) {
						window.Swal.fire( {
							title: __( 'Success!', 'eazydocs' ),
							text: __( 'Documentation created successfully.', 'eazydocs' ),
							icon: 'success',
							timer: 1500,
							showConfirmButton: false,
						} );
					}
				}
			} ).catch( () => {
				if ( typeof window.Swal !== 'undefined' ) {
					window.Swal.fire( {
						title: __( 'Error', 'eazydocs' ),
						text: __( 'Failed to create documentation.', 'eazydocs' ),
						icon: 'error',
					} );
				}
			} );
		}
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
			<div className="ezd-container-fluid">
				<div className="row alignment-center justify-content-between ml-0">
					<div className="navbar-left d-flex alignment-center">
						<div className="easydocs-logo-area">
							<a
								href="#"
								onClick={ ( e ) => e.preventDefault() }
							>
								{ __( 'Documentations', 'eazydocs' ) }
							</a>
						</div>

						{ capabilities.canPublishDocs && (
							<button
								type="button"
								id="parent-doc"
								className="easydocs-btn filled easydocs-btn-sm easydocs-btn-round"
								onClick={ handleAddDoc }
							>
								<span className="dashicons dashicons-plus-alt2"></span>
								{ __( 'Add Doc', 'eazydocs' ) }
							</button>
						) }

						{ antimanualActive ? (
							<a
								id="ezd-create-doc-with-ai"
								href={ urls.antimanualDocs }
								className="easydocs-btn easydocs-btn-ai-gold easydocs-btn-sm easydocs-btn-round"
								style={ { marginLeft: '10px' } }
								role="button"
							>
								🪄 { __( 'Create Doc with AI', 'eazydocs' ) }
							</a>
						) : (
							<button
								type="button"
								id="ezd-create-doc-with-ai"
								className="easydocs-btn easydocs-btn-ai-gold easydocs-btn-sm easydocs-btn-round"
								style={ { marginLeft: '10px' } }
								onClick={ handleAiCreate }
							>
								🪄 { __( 'Create Doc with AI', 'eazydocs' ) }
							</button>
						) }
					</div>

					<form action="#" method="POST" className="easydocs-search-form">
						<div className="search-icon">
							<span className="dashicons dashicons-search"></span>
						</div>
						<input
							type="search"
							name="keyword"
							className="form-control"
							id="easydocs-search"
							placeholder={ __( 'Search for', 'eazydocs' ) }
							value={ searchValue }
							onChange={ handleSearch }
							ref={ searchInputRef }
						/>
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
											<span className="easydocs-badge"> { trashCount } </span>
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
			</div>
		</header>
	);
};

export default Header;
