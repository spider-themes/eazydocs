/**
 * DocItem component – renders a single child doc item with nested children.
 *
 * Uses proper React components for Pro actions instead of
 * dangerouslySetInnerHTML and reinitProHandlers.
 *
 * @package EazyDocs
 * @since   2.8.0
 */
import { __ } from '@wordpress/i18n';
import ProgressBar from './ProgressBar';
import ProActionsButtons from './ProActionsButtons';
import { useDeleteDoc, useCreateChild } from '../hooks/useBuilderData';
import { showProAlert } from '../utils/pro-alert';
import type { DocChild, Capabilities, BuilderUrls, RoleVisibilityConfig } from '../types';

declare const eazydocs_local_object: any;

interface DocItemProps {
	doc: DocChild;
	depth: number;
	parentId: number;
	isPremium: boolean;
	capabilities: Capabilities;
	urls: BuilderUrls;
	roleVisibility?: RoleVisibilityConfig;
}

const DocItem: React.FC< DocItemProps > = ( { doc, depth, parentId, isPremium, capabilities, urls, roleVisibility } ) => {
	const hasChildren = doc.children && doc.children.length > 0;
	const deleteDoc = useDeleteDoc();
	const createChild = useCreateChild();

	// Build CSS classes matching the PHP output.
	const statusClass = doc.hasPassword ? 'protected' : doc.status;
	const childrenClass = hasChildren ? ' dd3-have-children dd3-has-children' : ' dd3-have-no-children';
	const depthClass = `depth-${ depth }`;

	let itemClasses = `dd-item dd3-item ${ depthClass } easydocs-accordion-item accordion mix ${ statusClass }${ childrenClass }`;

	if ( depth === 1 ) {
		itemClasses += ` dd-item-parent child-${ doc.id }`;
	} else if ( depth === 2 ) {
		itemClasses += ` dd-item-child child-of-${ doc.id }`;
	} else if ( depth === 3 ) {
		itemClasses += ' child-of-child child-one';
	} else if ( depth === 4 ) {
		itemClasses += ' child-of-child-of-child child-two';
	}

	// Determine if this doc can have sub-children (for the "+" button).
	const canAddSub = doc.canAddChild;

	// Determine section title style.
	const isPremiumAtDepth = ( ! isPremium && depth === 3 ) ? '' : 'has-child';
	const isSectionTitle = isPremiumAtDepth && hasChildren ? 'ez-section-title ' : '';

	/**
	 * Handle delete section doc.
	 */
	const handleDelete = ( e: React.MouseEvent< HTMLAnchorElement > ): void => {
		e.preventDefault();
		e.stopPropagation();

		if ( typeof window.Swal !== 'undefined' ) {
			window.Swal.fire( {
				title: eazydocs_local_object.delete_prompt_title,
				text: eazydocs_local_object.no_revert_title,
				icon: 'question',
				showCancelButton: true,
				confirmButtonColor: '#d33',
				cancelButtonColor: '#3085d6',
				confirmButtonText: 'Yes, delete it!',
			} ).then( ( result: any ) => {
				if ( result.value ) {
					deleteDoc.mutate(
						{
							docId: doc.id,
							nonce: doc.deleteNonce,
						},
						{
							onSuccess: () => {
								if ( typeof window.Swal !== 'undefined' ) {
									( window.Swal as any ).fire( {
										title: __( 'Deleted!', 'eazydocs' ),
										text: __( 'The document has been moved to trash.', 'eazydocs' ),
										icon: 'success',
										toast: true,
										position: 'top-end',
										timer: 1500,
										showConfirmButton: false,
									} );
								}
							},
							onError: () => {
								if ( typeof window.Swal !== 'undefined' ) {
									window.Swal.fire( {
										title: __( 'Error', 'eazydocs' ),
										text: __( 'Failed to delete the document.', 'eazydocs' ),
										icon: 'error',
									} );
								}
							},
						}
					);
				}
			} );
		}
	};

	/**
	 * Handle add child doc.
	 */
	const handleAddChild = ( e: React.MouseEvent< HTMLAnchorElement > ): void => {
		e.preventDefault();
		e.stopPropagation();

		if ( typeof window.Swal !== 'undefined' ) {
			window.Swal.fire( {
				title: eazydocs_local_object.create_prompt_title,
				input: 'text',
				showCancelButton: true,
				inputAttributes: {
					name: 'child_title',
				},
				preConfirm: ( value: string ) => {
					if ( ! value ) {
						return false;
					}

					// Show loading state on the modal button.
					window.Swal.showLoading();

					return new Promise( ( resolve, reject ) => {
						createChild.mutate(
							{
								parentId: doc.id,
								title: value,
								nonce: doc.childNonce,
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
					if ( typeof window.Swal !== 'undefined' ) {
						( window.Swal as any ).fire( {
							title: __( 'Success!', 'eazydocs' ),
							text: __( 'Document created successfully.', 'eazydocs' ),
							icon: 'success',
							toast: true,
							position: 'top-end',
							timer: 1500,
							showConfirmButton: false,
						} );
					}
				}
			} ).catch( () => {
				if ( typeof window.Swal !== 'undefined' ) {
					window.Swal.fire( {
						title: __( 'Error', 'eazydocs' ),
						text: __( 'Failed to create the document.', 'eazydocs' ),
						icon: 'error',
					} );
				}
			} );
		}
	};

	// Build edit link.
	const editLink = doc.canEdit ? doc.editLink : '#';
	const editTarget = doc.canEdit ? '_blank' : '_self';

	return (
		<li className={ itemClasses } data-id={ doc.id }>
			{ /* Drag handle */ }
			{ capabilities.canManageOptions && (
				<div
					className="dd-handle dd3-handle"
					role="button"
					aria-label={ __( 'Drag to reorder this documentation item', 'eazydocs' ) }
					tabIndex={ 0 }
					title={ __( 'Drag to reorder', 'eazydocs' ) }
				>
					<svg className="dd-handle-icon" width="16" height="16" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg" aria-hidden="true">
						<circle cx="9" cy="5" r="2" fill="currentColor" />
						<circle cx="15" cy="5" r="2" fill="currentColor" />
						<circle cx="9" cy="12" r="2" fill="currentColor" />
						<circle cx="15" cy="12" r="2" fill="currentColor" />
						<circle cx="9" cy="19" r="2" fill="currentColor" />
						<circle cx="15" cy="19" r="2" fill="currentColor" />
					</svg>
					<span className="screen-reader-text">{ __( 'Drag to reorder', 'eazydocs' ) }</span>
				</div>
			) }

			{ /* Content area */ }
			<div className="dd3-content">
				<div
					className={ `accordion-title expand--child ${ isSectionTitle }${ hasChildren ? isPremiumAtDepth : '' }` }
					title={ __( 'Click on the title to edit the page', 'eazydocs' ) }
				>
					{ /* Left content */ }
					<div className={ `left-content left-content-${ depth }` }>
						<h4>
							<a
								href={ editLink }
								target={ editTarget }
								rel="noopener noreferrer"
								onClick={ ( e ) => {
									if ( ! doc.canEdit ) {
										e.preventDefault();
									}
								} }
							>
								{ doc.title }
							</a>

							{ /* Visibility badges */ }
							{ doc.visibility.isPrivate && (
								<span
									className="ezd-visibility-badge ezd-visibility-private"
									title={ __( 'Private Doc - Visible to logged-in users only', 'eazydocs' ) }
								>
									<span className="dashicons dashicons-lock"></span>
								</span>
							) }

							{ doc.visibility.isPrivate && doc.visibility.hasRoleVisibility && (
								<span
									className="ezd-visibility-badge ezd-visibility-role"
									title={ `${ __( 'Role-Based Access:', 'eazydocs' ) } ${ doc.visibility.rolesList }` }
								>
									<span className="dashicons dashicons-groups"></span>
								</span>
							) }

							{ doc.visibility.isDraft && (
								<span
									className="ezd-visibility-badge ezd-visibility-draft"
									title={ __( 'Draft', 'eazydocs' ) }
								>
									<span className="dashicons dashicons-edit"></span>
								</span>
							) }

							{ doc.visibility.isProtected && (
								<span
									className="ezd-visibility-badge ezd-visibility-protected"
									title={ __( 'Password Protected', 'eazydocs' ) }
								>
									<span className="dashicons dashicons-admin-network"></span>
								</span>
							) }

							{ doc.childCount > 0 && (
								<span className="count ezd-badge">
									{ doc.childCount }
								</span>
							) }
						</h4>

						<ul className="actions">
							{ doc.canEdit && (
								<>
									{ isPremium && doc.proActions.duplicate ? (
										<li className="duplicate">
											<ProActionsButtons
												docId={ doc.id }
												proActions={ { ...doc.proActions, visibility: null, sidebar: null } }
												isPremium={ isPremium }
												urls={ urls }
												roleVisibility={ roleVisibility }
												context="child"
											/>
										</li>
									) : (
										! isPremium && (
											<li className="duplicate">
												<a
													href="#"
													className="eazydocs-pro-notice"
													aria-label={ __( 'Duplicate this doc with the child docs.', 'eazydocs' ) }
													title={ __( 'Duplicate this doc with the child docs.', 'eazydocs' ) }
													onClick={ ( e: React.MouseEvent ) => {
														e.preventDefault();
														e.stopPropagation();
														showProAlert( urls.pricing );
													} }
												>
													<span className="dashicons dashicons-admin-page"></span>
												</a>
											</li>
										)
									) }

									{ canAddSub && (
										<li>
											<a
												href="#"
												className="child-doc"
												aria-label={ __( 'Add new doc under this doc', 'eazydocs' ) }
												title={ __( 'Add new doc under this doc', 'eazydocs' ) }
												onClick={ handleAddChild }
											>
												<span className="dashicons dashicons-plus-alt2"></span>
											</a>
										</li>
									) }

									{ isPremium && capabilities.canManageOptions && doc.proActions.visibility ? (
										<li className="visibility">
											<ProActionsButtons
												docId={ doc.id }
												proActions={ { ...doc.proActions, duplicate: null, sidebar: null } }
												isPremium={ isPremium }
												urls={ urls }
												roleVisibility={ roleVisibility }
												context="child"
											/>
										</li>
									) : null }
								</>
							) }

							<li>
								<a
									href={ doc.permalink }
									target="_blank"
									rel="noopener noreferrer"
									aria-label={ __( 'View this doc item in new tab', 'eazydocs' ) }
									title={ __( 'View this doc item in new tab', 'eazydocs' ) }
								>
									<span className="dashicons dashicons-external"></span>
								</a>
							</li>

							{ doc.canDelete && (
								<li className="delete">
									<a
										href="#"
										className="section-delete"
										aria-label={ __( 'Move to Trash', 'eazydocs' ) }
										title={ __( 'Move to Trash', 'eazydocs' ) }
										onClick={ handleDelete }
									>
										<span className="dashicons dashicons-trash"></span>
									</a>
								</li>
							) }
						</ul>
					</div>

					{ /* Right content – progress bar */ }
					<div className="right-content">
						<ProgressBar positive={ doc.positive } negative={ doc.negative } />
					</div>
				</div>
			</div>

			{ /* Nested children */ }
			{ hasChildren && (
				<ol className="dd-list">
					{ doc.children.map( ( child ) => (
						<DocItem
							key={ child.id }
							doc={ child }
							depth={ depth + 1 }
							parentId={ doc.id }
							isPremium={ isPremium }
							capabilities={ capabilities }
							urls={ urls }
							roleVisibility={ roleVisibility }
						/>
					) ) }
				</ol>
			) }
		</li>
	);
};

export default DocItem;
