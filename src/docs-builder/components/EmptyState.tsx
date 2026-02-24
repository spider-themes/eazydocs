/**
 * EmptyState component.
 *
 * Shown when there are no docs – uses TanStack Query mutation
 * for sample import instead of jQuery AJAX and DOM manipulation.
 *
 * @package EazyDocs
 * @since   2.8.0
 */
import { useState } from '@wordpress/element';
import { __ } from '@wordpress/i18n';
import { useQueryClient, useMutation } from '@tanstack/react-query';
import { useCreateParentDoc, PARENTS_QUERY_KEY, CHILDREN_QUERY_KEY, COUNTS_QUERY_KEY } from '../hooks/useBuilderData';
import type { BuilderData } from '../types';

declare const eazydocs_local_object: any;

interface EmptyStateProps {
	data: BuilderData;
}

const EmptyState: React.FC< EmptyStateProps > = ( { data } ) => {
	const { urls, nonces } = data;
	const createParentDoc = useCreateParentDoc();
	const queryClient = useQueryClient();
	const [ isImporting, setIsImporting ] = useState< boolean >( false );

	// TanStack mutation for sample data import.
	const importSample = useMutation( {
		mutationFn: async () => {
			const formData = new FormData();
			formData.append( 'action', 'ezd_import_sample_data' );
			formData.append( 'security', eazydocs_local_object.nonce );

			const response = await fetch( eazydocs_local_object.ajaxurl, {
				method: 'POST',
				body: formData,
				credentials: 'same-origin',
			} );

			return response.json();
		},
		onSuccess: async ( response ) => {
			setIsImporting( false );

			if ( response.success ) {
				await queryClient.invalidateQueries( { queryKey: PARENTS_QUERY_KEY } );
				await queryClient.invalidateQueries( { queryKey: CHILDREN_QUERY_KEY } );
				await queryClient.invalidateQueries( { queryKey: COUNTS_QUERY_KEY } );

				if ( typeof window.Swal !== 'undefined' ) {
					window.Swal.fire( {
						title: __( 'Success!', 'eazydocs' ),
						text: response.data.message,
						icon: 'success',
						confirmButtonColor: '#4C4CF1',
					} );
				}
			} else {
				showError( response.data?.message || __( 'Failed to import sample data.', 'eazydocs' ) );
			}
		},
		onError: () => {
			setIsImporting( false );
			showError( __( 'An error occurred while importing. Please try again.', 'eazydocs' ) );
		},
	} );

	const handleCreateFirstDoc = ( e: React.MouseEvent< HTMLAnchorElement > ): void => {
		e.preventDefault();

		if ( typeof window.Swal !== 'undefined' ) {
			window.Swal.fire( {
				title: eazydocs_local_object.create_prompt_title,
				input: 'text',
				showCancelButton: true,
				inputAttributes: {
					name: 'new_doc',
				},
			} ).then( ( result: any ) => {
				if ( result.value ) {
					createParentDoc.mutate(
						{
							title: result.value,
							nonce: nonces.parentDoc,
						},
						{
							onSuccess: () => {
								if ( typeof window.Swal !== 'undefined' ) {
									window.Swal.fire( {
										title: __( 'Success!', 'eazydocs' ),
										text: __( 'Documentation created successfully.', 'eazydocs' ),
										icon: 'success',
										timer: 1500,
										showConfirmButton: false,
									} );
								}
							},
							onError: () => {
								if ( typeof window.Swal !== 'undefined' ) {
									window.Swal.fire( {
										title: __( 'Error', 'eazydocs' ),
										text: __( 'Failed to create documentation.', 'eazydocs' ),
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

	const handleImportSample = ( e: React.MouseEvent< HTMLButtonElement > ): void => {
		e.preventDefault();

		const doImport = (): void => {
			setIsImporting( true );
			importSample.mutate();
		};

		if ( typeof window.Swal !== 'undefined' ) {
			window.Swal.fire( {
				title: __( 'Import Sample Data?', 'eazydocs' ),
				text: __( 'This will populate your knowledge base with a complete set of demo documentation items.', 'eazydocs' ),
				icon: 'question',
				showCancelButton: true,
				confirmButtonColor: '#5A50F9',
				cancelButtonColor: '#e0e0e0',
				confirmButtonText: __( 'Yes, Import Demo', 'eazydocs' ),
				cancelButtonText: __( 'Cancel', 'eazydocs' ),
				customClass: {
					container: 'ezd-swal2-container',
					popup: 'ezd-swal2-popup',
				},
			} ).then( ( result: any ) => {
				if ( result.isConfirmed ) {
					doImport();
				}
			} );
		} else {
			// eslint-disable-next-line no-alert
			if ( confirm( __( 'This will import demo documentation to help you get started. Do you want to continue?', 'eazydocs' ) ) ) {
				doImport();
			}
		}
	};

	const showError = ( msg: string ): void => {
		if ( typeof window.Swal !== 'undefined' ) {
			window.Swal.fire( {
				title: __( 'Error', 'eazydocs' ),
				text: msg,
				icon: 'error',
				confirmButtonColor: '#4C4CF1',
			} );
		} else {
			// eslint-disable-next-line no-alert
			alert( msg );
		}
	};

	return (
		<div className="eazydocs-no-content-wrapper">
			<div className="ezd-empty-state-card">
				<div className="ezd-empty-icon-box">
					<img
						src={ urls.folderOpenIcon }
						alt={ __( 'Folder Open', 'eazydocs' ) }
						className="ezd-empty-icon"
					/>
				</div>
				<h2 className="ezd-empty-title">
					{ __( 'Ready to Start Your Knowledge Base?', 'eazydocs' ) }
				</h2>
				<p className="ezd-empty-desc">
					{ __( "It looks like you haven't created any documentation yet. Get started by creating your first doc or import our sample data to see how it works.", 'eazydocs' ) }
				</p>

				<div className="ezd-empty-actions">
					<a
						className="ezd-btn-premium ezd-btn-primary-gradient"
						href="#"
						id="new-doc"
						onClick={ handleCreateFirstDoc }
					>
						<span className="dashicons dashicons-plus"></span>
						{ __( 'Create First Doc', 'eazydocs' ) }
					</a>

					<button
						type="button"
						className={ `ezd-btn-premium ezd-btn-import-sample${ isImporting ? ' is-loading' : '' }` }
						id="ezd-import-sample-data"
						onClick={ handleImportSample }
						disabled={ isImporting }
					>
						{ isImporting ? (
							<>
								<span className="dashicons dashicons-update ezd-spin"></span>
								{ ' ' }
								{ __( 'Importing...', 'eazydocs' ) }
							</>
						) : (
							<>
								<span className="dashicons dashicons-download"></span>
								{ __( 'Import Sample Data', 'eazydocs' ) }
							</>
						) }
					</button>
				</div>
			</div>
		</div>
	);
};

export default EmptyState;
