/**
 * Pro action button components.
 *
 * Replaces dangerouslySetInnerHTML rendering of Pro action links
 * (duplicate, visibility, sidebar) with proper React components
 * that use structured data from the REST API.
 *
 * @package EazyDocs
 * @since   2.9.0
 */
import { useCallback, useEffect, useState, render } from '@wordpress/element';
import { __ } from '@wordpress/i18n';
import { showProAlert } from '../utils/pro-alert';
import { useDuplicateDoc, useUpdateVisibility, useUpdateSidebar } from '../hooks/useProActions';
import { useRoute } from '../../hooks/use-route';
import type {
	ProActions,
	ProDuplicateAction,
	ProVisibilityAction,
	ProSidebarAction,
	BuilderUrls,
	RoleVisibilityConfig,
} from '../types';

declare const window: Window & {
	Swal?: any;
};

interface ProActionsButtonsProps {
	docId: number;
	proActions: ProActions;
	isPremium: boolean;
	urls: BuilderUrls;
	roleVisibility?: RoleVisibilityConfig;
	context: 'parent' | 'child';
}

interface VisibilityModalValue {
	docStatus: 'publish' | 'private' | 'protected';
	password: string;
	roles: string[];
	allowGuests: boolean;
	applyRolesToChildren: boolean;
}

declare const eazydocs_local_object: any;

interface SidebarModalValue {
	leftType: string;
	leftContent: string;
	rightType: string;
	rightContent: string;
}

interface ReusableBlockOption {
	id: string;
	title: string;
}

const getReusableBlockOptions = (): ReusableBlockOption[] => {
	const options = eazydocs_local_object?.reusable_blocks_options;
	if ( ! Array.isArray( options ) ) {
		return [];
	}

	return options
		.map( ( option: any ) => ( {
			id: String( option?.id || '' ),
			title: String( option?.title || '' ),
		} ) )
		.filter( ( option: ReusableBlockOption ) => '' !== option.id && '' !== option.title );
};

const VisibilityModalContent: React.FC< {
	initialValue: VisibilityModalValue;
	hasRoleVisibility: boolean;
	roleVisibility?: RoleVisibilityConfig;
	onChange: ( value: VisibilityModalValue ) => void;
} > = ( { initialValue, hasRoleVisibility, roleVisibility, onChange } ) => {
	const [ docStatus, setDocStatus ] = useState< VisibilityModalValue[ 'docStatus' ] >( initialValue.docStatus );
	const [ password, setPassword ] = useState( initialValue.password );
	const [ roles, setRoles ] = useState< string[] >( initialValue.roles );
	const [ allowGuests, setAllowGuests ] = useState( initialValue.allowGuests );
	const [ applyRolesToChildren, setApplyRolesToChildren ] = useState( initialValue.applyRolesToChildren );

	useEffect( () => {
		onChange( {
			docStatus,
			password,
			roles,
			allowGuests,
			applyRolesToChildren,
		} );
	}, [ docStatus, password, roles, allowGuests, applyRolesToChildren, onChange ] );

	const handleRoleToggle = ( slug: string ): void => {
		setRoles( ( prev: string[] ) => (
			prev.includes( slug )
				? prev.filter( ( roleSlug: string ) => roleSlug !== slug )
				: [ ...prev, slug ]
		) );
	};

	return (
		<div className="docs_visibility_wrapper">
			<p>{ __( 'The selected visibility option will apply to the child docs as well.', 'eazydocs' ) }</p>
			<div className="docs_visibility_field_wrap">
				<label htmlFor="ezd_status_public" style={ { marginBottom: 10, fontWeight: 500, fontSize: 16 } }>{ __( 'Select Doc Visibility', 'eazydocs' ) }</label>
				<br />
				<input
					type="radio"
					id="ezd_status_public"
					name="ezd_doc_status"
					value="publish"
					checked={ docStatus === 'publish' }
					onChange={ () => setDocStatus( 'publish' ) }
				/>
				<label htmlFor="ezd_status_public">{ __( 'Public', 'eazydocs' ) }</label>
				<input
					type="radio"
					id="ezd_status_private"
					name="ezd_doc_status"
					value="private"
					checked={ docStatus === 'private' }
					onChange={ () => setDocStatus( 'private' ) }
				/>
				<label htmlFor="ezd_status_private">{ __( 'Private', 'eazydocs' ) }</label>
				<input
					type="radio"
					id="ezd_status_protected"
					name="ezd_doc_status"
					value="protected"
					checked={ docStatus === 'protected' }
					onChange={ () => setDocStatus( 'protected' ) }
				/>
				<label htmlFor="ezd_status_protected">{ __( 'Password protected', 'eazydocs' ) }</label>
				<input
					type="text"
					id="ezd_password_input"
					name="ezd_password_input"
					value={ password }
					placeholder={ __( 'Insert Password', 'eazydocs' ) }
					style={ { display: docStatus === 'protected' ? '' : 'none' } }
					onChange={ ( e ) => setPassword( e.target.value ) }
				/>

				{ hasRoleVisibility && (
					<div
						className="ezd_role_visibility_wrap"
						style={ {
							display: docStatus === 'private' ? '' : 'none',
							marginTop: '15px',
							padding: '15px',
							background: '#f9f9f9',
							borderRadius: '8px',
							border: '1px solid #e0e0e0',
						} }
					>
						<div className="ezd_role_visibility_header" style={ { marginBottom: '12px' } }>
							<label style={ { fontWeight: 600, color: '#1e1e1e', display: 'flex', alignItems: 'center', gap: '6px' } }>
								<span className="dashicons dashicons-groups" style={ { color: '#2271b1' } }></span>
								{ __( 'Role-Based Access', 'eazydocs' ) }
							</label>
							<p style={ { margin: '5px 0 0', fontSize: '12px', color: '#666' } }>
								{ __( 'Restrict this doc to specific user roles. Leave unchecked for all logged-in users.', 'eazydocs' ) }
							</p>
						</div>

						<div
							className="ezd_role_checkboxes"
							style={ {
								maxHeight: '150px',
								overflowY: 'auto',
								padding: '10px',
								background: '#fff',
								border: '1px solid #ddd',
								borderRadius: '4px',
							} }
						>
							{ roleVisibility?.roles?.map( ( role ) => (
								<label key={ role.slug } style={ { display: 'block', marginBottom: '6px', cursor: 'pointer' } }>
									<input
										type="checkbox"
										name="ezd_role_visibility[]"
										value={ role.slug }
										style={ { marginRight: '6px' } }
										checked={ roles.includes( role.slug ) }
										onChange={ () => handleRoleToggle( role.slug ) }
									/>
									{ role.name }
								</label>
							) ) }
						</div>

						<div style={ { marginTop: '10px' } }>
							<label style={ { display: 'flex', alignItems: 'center', gap: '6px', cursor: 'pointer' } }>
								<input
									type="checkbox"
									id="ezd_role_visibility_guest"
									value="1"
									checked={ allowGuests }
									onChange={ ( e ) => setAllowGuests( e.target.checked ) }
								/>
								<span>{ __( 'Allow guests (not logged in)', 'eazydocs' ) }</span>
							</label>
						</div>

						<div style={ { marginTop: '10px' } }>
							<label style={ { display: 'flex', alignItems: 'center', gap: '6px', cursor: 'pointer' } }>
								<input
									type="checkbox"
									id="ezd_apply_to_children_roles"
									value="1"
									checked={ applyRolesToChildren }
									onChange={ ( e ) => setApplyRolesToChildren( e.target.checked ) }
								/>
								<span>{ __( 'Apply roles to all child docs', 'eazydocs' ) }</span>
							</label>
						</div>
					</div>
				) }
			</div>
		</div>
	);
};

const SidebarModalContent: React.FC< {
	initialValue: SidebarModalValue;
	onChange: ( value: SidebarModalValue ) => void;
} > = ( { initialValue, onChange } ) => {
	const reusableBlockOptions = getReusableBlockOptions();
	const [ activeTab, setActiveTab ] = useState< 'left' | 'right' >( 'left' );
	const [ leftType, setLeftType ] = useState( initialValue.leftType || 'string_data' );
	const [ leftContent, setLeftContent ] = useState( initialValue.leftContent );
	const [ rightType, setRightType ] = useState( initialValue.rightType || 'string_data_right' );
	const [ rightContent, setRightContent ] = useState( initialValue.rightContent );

	useEffect( () => {
		onChange( {
			leftType,
			leftContent,
			rightType,
			rightContent,
		} );
	}, [ leftType, leftContent, rightType, rightContent, onChange ] );

	return (
		<div className="create_onepage_doc_area">
			<div className="ezd_content_btn_wrap">
				<div
					className={ `left_btn_link${ 'left' === activeTab ? ' ezd_left_active' : '' }` }
					onClick={ () => setActiveTab( 'left' ) }
				>
					{ __( 'Left Sidebar', 'eazydocs' ) }
				</div>
				<div
					className={ `right_btn_link${ 'right' === activeTab ? ' ezd_right_active' : '' }` }
					onClick={ () => setActiveTab( 'right' ) }
				>
					{ __( 'Right Sidebar', 'eazydocs' ) }
				</div>
			</div>

			{ /* Left Sidebar Tab */ }
			<div className={ `ezd_left_content${ 'left' === activeTab ? ' ezd_left_content_active' : '' }` }>
				<div className="ezd_docs_content_type_wrap">
					<label htmlFor="ezd_docs_content_type">{ __( 'Content Type:', 'eazydocs' ) }</label>
					<input
						type="radio"
						id="widget_data"
						name="ezd_docs_content_type"
						value="widget_data"
						checked={ 'widget_data' === leftType }
						onChange={ () => setLeftType( 'widget_data' ) }
					/>
					<label htmlFor="widget_data">{ __( 'Reusable Blocks', 'eazydocs' ) }</label>
					<input
						type="radio"
						id="string_data"
						name="ezd_docs_content_type"
						value="string_data"
						checked={ 'string_data' === leftType || ( 'widget_data' !== leftType && '' !== leftType ) }
						onChange={ () => setLeftType( 'string_data' ) }
					/>
					<label htmlFor="string_data">{ __( 'Normal Content', 'eazydocs' ) }</label>
				</div>

				<div
					className="ezd_shortcode_content_wrap"
					style={ { display: 'widget_data' === leftType ? 'none' : 'block' } }
				>
					<label htmlFor="ezd-shortcode-content">{ __( 'Content (Optional)', 'eazydocs' ) }</label>
					<br />
					<textarea
						name="ezd-shortcode-content"
						id="ezd-shortcode-content"
						rows={ 5 }
						className="widefat"
						value={ leftContent }
						onChange={ ( e ) => setLeftContent( e.target.value ) }
					/>
					<span className="ezd-text-support">
						{ __( '*The field will support text and html formats.', 'eazydocs' ) }
					</span>
				</div>

				<div
					className="ezd_widget_content_wrap"
					style={ { display: 'widget_data' === leftType ? 'block' : 'none', marginTop: '20px' } }
				>
					<label htmlFor="left_side_sidebar">{ __( 'Select a Reusable Block (Optional)', 'eazydocs' ) }</label>
					<br />
					<select
						name="ezd_sidebar_select_data"
						id="left_side_sidebar"
						className="widefat"
						value={ leftContent }
						onChange={ ( e ) => setLeftContent( e.target.value ) }
					>
						{ reusableBlockOptions.length > 0 ? (
							reusableBlockOptions.map( ( option: ReusableBlockOption ) => (
								<option key={ option.id } value={ option.id }>
									{ option.title }
								</option>
							) )
						) : (
							<option value="">{ __( 'No block found!', 'eazydocs' ) }</option>
						) }
					</select>
					{ eazydocs_local_object?.manage_reusable_blocks_url && (
						<p className="ezd-text-support">
							<a href={ eazydocs_local_object.manage_reusable_blocks_url } target="_blank" rel="noopener noreferrer">
								{ __( 'Manage Reusable blocks', 'eazydocs' ) }
							</a>
						</p>
					) }
				</div>
			</div>

			{ /* Right Sidebar Tab */ }
			<div className={ `ezd_right_content${ 'right' === activeTab ? ' ezd_left_content_active' : '' }` }>
				<div className="ezd_docs_content_type_wrap">
					<label htmlFor="ezd_docs_content_type_right" style={ { display: "block" } }>{ __( 'Content Type:', 'eazydocs' ) }</label>
					<input
						type="radio"
						id="widget_data_right"
						name="ezd_docs_content_type_right"
						value="widget_data_right"
						checked={ 'widget_data_right' === rightType }
						onChange={ () => setRightType( 'widget_data_right' ) }
					/>
					<label htmlFor="widget_data_right">{ __( 'Reusable Blocks', 'eazydocs' ) }</label>
					<input
						type="radio"
						id="string_data_right"
						name="ezd_docs_content_type_right"
						value="string_data_right"
						checked={
							'string_data_right' === rightType ||
							( 'widget_data_right' !== rightType && 'shortcode_right' !== rightType && '' !== rightType )
						}
						onChange={ () => setRightType( 'string_data_right' ) }
					/>
					<label htmlFor="string_data_right">{ __( 'Normal Content', 'eazydocs' ) }</label>
					<input
						type="radio"
						id="shortcode_right"
						name="ezd_docs_content_type_right"
						value="shortcode_right"
						checked={ 'shortcode_right' === rightType }
						onChange={ () => setRightType( 'shortcode_right' ) }
					/>
					<label htmlFor="shortcode_right">{ __( 'Doc Sidebar', 'eazydocs' ) }</label>

					{ 'shortcode_right' === rightType && (
						<div className="ezd-doc-sidebar-intro" style={ { marginTop: '10px', fontSize: '13px', lineHeight: '1.5' } }>
							{ __( 'To show the doc sidebar data, go to', 'eazydocs' ) } <strong>{ __( 'Appearance', 'eazydocs' ) }</strong>{ ' ' }
							{ __( 'then', 'eazydocs' ) } <strong>{ __( 'Widgets', 'eazydocs' ) }</strong>{ ' ' }
							{ __( 'and add your content inside', 'eazydocs' ) } <strong>{ __( 'Doc Right Sidebar', 'eazydocs' ) }</strong>{ ' ' }
							{ __( 'location. If you cannot find the location in Widgets, go to', 'eazydocs' ) } <strong>{ __( 'EazyDocs', 'eazydocs' ) }</strong>{ ' -> ' }
							<strong>{ __( 'Settings', 'eazydocs' ) }</strong>{ '. ' }
							{ __( 'Then go to', 'eazydocs' ) } <strong>{ __( 'Doc Single', 'eazydocs' ) }</strong>{ ' -> ' }
							<strong>{ __( 'Right Sidebar', 'eazydocs' ) }</strong>{ ' ' }
							{ __( 'and enable', 'eazydocs' ) } <strong>{ __( '"Widgets Area"', 'eazydocs' ) }</strong>.
						</div>
					) }
				</div>

				<div
					className="ezd_shortcode_content_wrap_right"
					style={ { display: 'string_data_right' === rightType || ( 'widget_data_right' !== rightType && 'shortcode_right' !== rightType && '' !== rightType ) ? 'block' : 'none' } }
				>
					<label htmlFor="ezd-shortcode-content-right">{ __( 'Content (Optional)', 'eazydocs' ) }</label>
					<br />
					<textarea
						name="ezd-shortcode-content-right"
						id="ezd-shortcode-content-right"
						rows={ 5 }
						className="widefat"
						value={ rightContent }
						onChange={ ( e ) => setRightContent( e.target.value ) }
					/>
					<span className="ezd-text-support">
						{ __( '*The field will support text and html formats.', 'eazydocs' ) }
					</span>
				</div>

				<div
					className="ezd_widget_content_wrap_right"
					style={ { display: 'widget_data_right' === rightType ? 'block' : 'none' } }
				>
					<label htmlFor="right_side_sidebar">{ __( 'Select a Reusable Block (Optional)', 'eazydocs' ) }</label>
					<br />
					<select
						name="ezd_sidebar_select_data_right"
						id="right_side_sidebar"
						className="widefat"
						value={ rightContent }
						onChange={ ( e ) => setRightContent( e.target.value ) }
					>
						{ reusableBlockOptions.length > 0 ? (
							reusableBlockOptions.map( ( option: ReusableBlockOption ) => (
								<option key={ option.id } value={ option.id }>
									{ option.title }
								</option>
							) )
						) : (
							<option value="">{ __( 'No block found!', 'eazydocs' ) }</option>
						) }
					</select>
					{ eazydocs_local_object?.manage_reusable_blocks_url && (
						<p className="ezd-text-support">
							<a href={ eazydocs_local_object.manage_reusable_blocks_url } target="_blank" rel="noopener noreferrer">
								{ __( 'Manage Reusable blocks', 'eazydocs' ) }
							</a>
						</p>
					) }
				</div>
			</div>
		</div>
	);
};

/**
 * Duplicate button for Pro users.
 */
const DuplicateButton: React.FC< {
	action: ProDuplicateAction;
	docId: number;
	context: 'parent' | 'child';
} > = ( { action, docId, context } ) => {
	const duplicateMutation = useDuplicateDoc();
	const { updateQuery } = useRoute();

	const handleClick = useCallback( ( e: React.MouseEvent ): void => {
		e.preventDefault();
		e.stopPropagation();

		if ( typeof window.Swal === 'undefined' ) {
			return;
		}

		window.Swal.fire( {
			title: __( 'Duplicate Doc', 'eazydocs' ),
			text: __( 'A duplicate copy of this doc (including its child) will be created. And a unique ID number will be appended on every cloned doc. The cloned doc will be drafted by default.', 'eazydocs' ),
			icon: 'question',
			showCancelButton: true,
			confirmButtonColor: '#3085d6',
			cancelButtonColor: '#d33',
			confirmButtonText: __( 'Yes', 'eazydocs' ),
		} ).then( ( result: any ) => {
			if ( result.value ) {
				// Show loading state.
				window.Swal.fire( {
					title: __( 'Duplicating…', 'eazydocs' ),
					text: __( 'Please wait while the document is being duplicated.', 'eazydocs' ),
					allowOutsideClick: false,
					allowEscapeKey: false,
					showConfirmButton: false,
					didOpen: () => {
						window.Swal.showLoading();
					},
				} );

				duplicateMutation.mutate( docId, {
					onSuccess: ( data ) => {
						// Navigate to the duplicated doc.
						if ( 0 === data.parentId ) {
							// Duplicated a parent doc – switch to the new parent tab.
							updateQuery( { active_doc: String( data.newId ) } );
						}
						// For child docs, the parent tab stays the same.

						if ( typeof window.Swal !== 'undefined' ) {
							( window.Swal as any ).fire( {
								title: __( 'Duplicated!', 'eazydocs' ),
								text: __( 'The document has been duplicated successfully.', 'eazydocs' ),
								icon: 'success',
								toast: true,
								position: 'top-end',
								timer: 2000,
								showConfirmButton: false,
							} );
						}
					},
					onError: () => {
						if ( typeof window.Swal !== 'undefined' ) {
							window.Swal.fire( {
								title: __( 'Error', 'eazydocs' ),
								text: __( 'Failed to duplicate the document. Please try again.', 'eazydocs' ),
								icon: 'error',
							} );
						}
					},
				} );
			}
		} );
	}, [ docId, duplicateMutation, updateQuery ] );

	return (
		<a
			href="#"
			className="docs-duplicate"
			title={ __( 'Duplicate this doc with the child docs.', 'eazydocs' ) }
			onClick={ handleClick }
		>
			<span className="dashicons dashicons-admin-page"></span>
			{ 'parent' === context && (
				<span className="duplicate-title">{ __( 'Duplicate', 'eazydocs' ) }</span>
			) }
		</a>
	);
};

/**
 * Visibility button for Pro users.
 */
const VisibilityButton: React.FC< {
	action: ProVisibilityAction;
	roleVisibility?: RoleVisibilityConfig;
	context: 'parent' | 'child';
	docId: number;
} > = ( { action, roleVisibility, context, docId } ) => {
	const updateVisibilityMutation = useUpdateVisibility();

	const handleClick = useCallback( ( e: React.MouseEvent ): void => {
		e.preventDefault();
		e.stopPropagation();

		if ( typeof window.Swal === 'undefined' ) {
			return;
		}

		const hasRoleVisibility = roleVisibility?.enabled || false;
		const container = document.createElement( 'div' );
		let currentValue: VisibilityModalValue = {
			docStatus: action.currentVisibility,
			password: action.currentPassword || '',
			roles: action.roleVisibilityRoles || [],
			allowGuests: !! action.roleVisibilityGuest,
			applyRolesToChildren: false,
		};

		window.Swal.fire( {
			title: __( 'Visibility Options', 'eazydocs' ),
			html: container,
			showCancelButton: true,
			confirmButtonText: __( 'Update', 'eazydocs' ),
			width: hasRoleVisibility ? '500px' : 'auto',
			didOpen: () => {
				render(
					<VisibilityModalContent
						initialValue={ currentValue }
						hasRoleVisibility={ hasRoleVisibility }
						roleVisibility={ roleVisibility }
						onChange={ ( value ) => {
							currentValue = value;
						} }
					/>,
					container
				);
			},
			willClose: () => {
				render( <></>, container );
			},
			preConfirm: () => {
				if ( ! currentValue.docStatus ) {
					return false;
				}
				if ( 'protected' === currentValue.docStatus ) {
					if ( ! currentValue.password && 'protected' !== action.currentVisibility ) {
						window.Swal.showValidationMessage( __( 'Please enter a password.', 'eazydocs' ) );
						return false;
					}
				}

				return {
					ezd_doc_status: currentValue.docStatus,
					password: currentValue.password,
					roles: currentValue.roles,
					allowGuests: currentValue.allowGuests,
					applyRolesToChildren: currentValue.applyRolesToChildren,
				};
			},
		} ).then( ( result: any ) => {
			if ( result.isConfirmed ) {
				window.Swal.fire( {
					title: __( 'Updating…', 'eazydocs' ),
					text: __( 'Please wait while the visibility is being updated.', 'eazydocs' ),
					allowOutsideClick: false,
					allowEscapeKey: false,
					showConfirmButton: false,
					didOpen: () => {
						window.Swal.showLoading();
					},
				} );

				updateVisibilityMutation.mutate( {
					docId: docId,
					visibility: result.value.ezd_doc_status,
					password: result.value.password,
					roles: result.value.roles,
					allowGuests: result.value.allowGuests,
					applyToChildren: result.value.applyRolesToChildren,
				} );
			}
		} );
	}, [ action, roleVisibility, docId, updateVisibilityMutation ] );

	return (
		<a
			href={ action.url }
			target="_blank"
			rel="noopener noreferrer"
			className="docs-visibility"
			title={ __( 'Docs visibility', 'eazydocs' ) }
			onClick={ handleClick }
		>
			<span className="dashicons dashicons-visibility"></span>
			{ 'parent' === context && (
				<span className="visibility-title">{ __( 'Visibility', 'eazydocs' ) }</span>
			) }
		</a>
	);
};

/**
 * Sidebar button for Pro parent docs.
 */
const SidebarButton: React.FC< {
	action: ProSidebarAction;
	docId: number;
} > = ( { action, docId } ) => {
	const updateSidebarMutation = useUpdateSidebar();

	const handleClick = useCallback( ( e: React.MouseEvent ): void => {
		e.preventDefault();
		e.stopPropagation();

		if ( typeof window.Swal === 'undefined' ) {
			return;
		}

		const container = document.createElement( 'div' );
		let currentValue: SidebarModalValue = {
			leftType: action.leftType || 'string_data',
			leftContent: action.leftContent || '',
			rightType: action.rightType || 'string_data_right',
			rightContent: action.rightContent || '',
		};

		window.Swal.fire( {
			title: __( 'Doc Sidebar', 'eazydocs' ),
			html: container,
			showCancelButton: true,
			confirmButtonText: __( 'Update', 'eazydocs' ),
			didOpen: () => {
				render(
					<SidebarModalContent
						initialValue={ currentValue }
						onChange={ ( value ) => {
							currentValue = value;
						} }
					/>,
					container
				);
			},
			willClose: () => {
				render( <></>, container );
			},
			preConfirm: () => currentValue,
		} ).then( ( result: any ) => {
			if ( result.isConfirmed && result.value ) {
				updateSidebarMutation.mutate( {
					docId: docId,
					leftType: result.value.leftType,
					leftContent: result.value.leftContent,
					rightType: result.value.rightType,
					rightContent: result.value.rightContent,
				} );
			}
		} );
	}, [ action, docId, updateSidebarMutation ] );

	return (
		<a
			href="#"
			className="docs-sidebar"
			title={ __( 'Docs sidebar', 'eazydocs' ) }
			onClick={ handleClick }
		>
			<span className="dashicons dashicons-welcome-widgets-menus"></span>
			<span className="sidebar-title">{ __( 'Sidebar', 'eazydocs' ) }</span>
		</a>
	);
};

/**
 * Main Pro actions buttons component.
 *
 * Renders the correct buttons depending on premium status.
 */
const ProActionsButtons: React.FC< ProActionsButtonsProps > = ( {
	docId,
	proActions,
	isPremium,
	urls,
	roleVisibility,
	context,
} ) => {
	if ( isPremium ) {
		return (
			<>
				{ proActions.duplicate && (
					<DuplicateButton action={ proActions.duplicate } docId={ docId } context={ context } />
				) }
				{ proActions.visibility && (
					<VisibilityButton
						action={ proActions.visibility }
						roleVisibility={ roleVisibility }
						context={ context }
						docId={ docId }
					/>
				) }
				{ 'parent' === context && proActions.sidebar && (
					<SidebarButton action={ proActions.sidebar } docId={ docId } />
				) }
			</>
		);
	}

	// Free version – show Pro alert links.
	return (
		<>
			<a
				href={ urls.pricing }
				className="docs-duplicate eazydocs-pro-notice"
				aria-label={ __( 'Duplicate this doc with the child docs.', 'eazydocs' ) }
				title={ __( 'Duplicate this doc with the child docs.', 'eazydocs' ) }
				onClick={ ( e: React.MouseEvent ) => {
					e.preventDefault();
					e.stopPropagation();
					showProAlert( urls.pricing, urls.assetsUrl );
				} }
			>
				<span className="dashicons dashicons-admin-page"></span>
				{ 'parent' === context && (
					<span>{ __( 'Duplicate', 'eazydocs' ) }</span>
				) }
			</a>
			<a
				href={ urls.pricing }
				className="docs-visibility eazydocs-pro-notice"
				aria-label={ __( 'Docs visibility', 'eazydocs' ) }
				title={ __( 'Docs visibility', 'eazydocs' ) }
				onClick={ ( e: React.MouseEvent ) => {
					e.preventDefault();
					e.stopPropagation();
					showProAlert( urls.pricing, urls.assetsUrl );
				} }
			>
				<span className="dashicons dashicons-visibility"></span>
				{ 'parent' === context && (
					<span> { __( 'Visibility', 'eazydocs' ) } </span>
				) }
			</a>
			{ 'parent' === context && (
				<a
					href={ urls.pricing }
					className="docs-sidebar eazydocs-pro-notice"
					aria-label={ __( 'Docs sidebar', 'eazydocs' ) }
					title={ __( 'Docs sidebar', 'eazydocs' ) }
					onClick={ ( e: React.MouseEvent ) => {
						e.preventDefault();
						e.stopPropagation();
						showProAlert( urls.pricing, urls.assetsUrl );
					} }
				>
					<span className="dashicons dashicons-welcome-widgets-menus"></span>
					<span> { __( 'Sidebar', 'eazydocs' ) } </span>
				</a>
			) }
		</>
	);
};

export default ProActionsButtons;
