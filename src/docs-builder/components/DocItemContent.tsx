/**
 * DocItemContent – renders the inline hover action icons for a doc item
 * (add child, visibility, view, duplicate, trash).
 *
 * Previously this component carried three render modes (`full`,
 * `header-actions`, `inline-actions`) but only `inline-actions` was ever
 * mounted; the others have been removed to keep a single source of truth.
 *
 * @package EazyDocs
 * @since   2.9.0
 */
import { __ } from '@wordpress/i18n';
import ProActionsButtons from './ProActionsButtons';
import { useDeleteDoc, useCreateChild } from '../hooks/useBuilderData';
import { showProAlert } from '../utils/pro-alert';
import {
	promptForDocTitle,
	showCreateSuccess,
	showCreateError,
	confirmDelete,
	showDeleteSuccess,
	showDeleteError,
} from '../utils/prompt';
import type {
	DocChild,
	Capabilities,
	BuilderUrls,
	RoleVisibilityConfig,
} from '../types';

interface DocItemContentProps {
	doc: DocChild;
	rootParentId: number;
	isPremium: boolean;
	capabilities: Capabilities;
	urls: BuilderUrls;
	roleVisibility?: RoleVisibilityConfig;
}

const DocItemContent: React.FC< DocItemContentProps > = ( {
	doc,
	rootParentId,
	isPremium,
	capabilities,
	urls,
	roleVisibility,
} ) => {
	const deleteDoc = useDeleteDoc();
	const createChild = useCreateChild();

	// Determine if this doc can have sub-children.
	const canAddSub = doc.canAddChild;

	/**
	 * Handle delete – confirm, then move the doc to trash.
	 */
	const handleDelete = async ( e: React.MouseEvent< HTMLAnchorElement > ): Promise< void > => {
		e.preventDefault();
		e.stopPropagation();

		if ( ! ( await confirmDelete() ) ) {
			return;
		}

		deleteDoc.mutate(
			{
				docId: doc.id,
				nonce: doc.deleteNonce,
			},
			{
				onSuccess: () => showDeleteSuccess(),
				onError: () =>
					showDeleteError( __( 'Failed to delete the document.', 'eazydocs' ) ),
			}
		);
	};

	/**
	 * Handle add child doc.
	 */
	const handleAddChild = async ( e: React.MouseEvent< HTMLAnchorElement > ): Promise< void > => {
		e.preventDefault();
		e.stopPropagation();

		// Guard against double submissions while a create is in flight.
		if ( createChild.isPending ) {
			return;
		}

		const prompt = await promptForDocTitle();
		if ( ! prompt ) {
			return;
		}

		createChild.mutate(
			{
				parentId: doc.id,
				rootParentId,
				title: prompt.title,
				nonce: doc.childNonce,
				postStatus: prompt.status,
			},
			{
				onSuccess: () => {
					showCreateSuccess(
						'draft' === prompt.status
							? __( 'Document saved as draft.', 'eazydocs' )
							: __( 'Document created successfully.', 'eazydocs' )
					);
				},
				onError: () => {
					showCreateError( __( 'Failed to create the document.', 'eazydocs' ) );
				},
			}
		);
	};

	return (
		<>
			{ doc.canEdit && (
				<>
					{ canAddSub && (
						<a
							href="#"
							className="ezd-inline-action"
							aria-label={ __( 'Add new doc under this doc', 'eazydocs' ) }
							title={ __( 'Add new doc under this doc', 'eazydocs' ) }
							onClick={ handleAddChild }
						>
							<span className="dashicons dashicons-plus-alt2"></span>
						</a>
					) }

					{ isPremium &&
					capabilities.canManageOptions &&
					doc.proActions.visibility ? (
						<span className="ezd-inline-action">
							<ProActionsButtons
								docId={ doc.id }
								proActions={ {
									...doc.proActions,
									duplicate: null,
									sidebar: null,
								} }
								isPremium={ isPremium }
								urls={ urls }
								roleVisibility={ roleVisibility }
								context="child"
							/>
						</span>
					) : null }
				</>
			) }

			<a
				href={ doc.permalink }
				target="_blank"
				rel="noopener noreferrer"
				className="ezd-inline-action"
				aria-label={ __( 'View this doc item in new tab', 'eazydocs' ) }
				title={ __( 'View this doc item in new tab', 'eazydocs' ) }
			>
				<span className="dashicons dashicons-external"></span>
			</a>

			{ isPremium && doc.proActions.duplicate ? (
				<span className="ezd-inline-action">
					<ProActionsButtons
						docId={ doc.id }
						proActions={ {
							...doc.proActions,
							visibility: null,
							sidebar: null,
						} }
						isPremium={ isPremium }
						urls={ urls }
						roleVisibility={ roleVisibility }
						context="child"
					/>
				</span>
			) : (
				! isPremium && (
					<a
						href="#"
						className="ezd-inline-action eazydocs-pro-notice"
						aria-label={ __( 'Duplicate this doc with the child docs.', 'eazydocs' ) }
						title={ __( 'Duplicate this doc with the child docs.', 'eazydocs' ) }
						onClick={ ( e: React.MouseEvent ) => {
							e.preventDefault();
							e.stopPropagation();
							showProAlert( urls.pricing, urls.assetsUrl );
						} }
					>
						<span className="dashicons dashicons-admin-page"></span>
					</a>
				)
			) }

			{ doc.canDelete && (
				<a
					href="#"
					className="ezd-inline-action ezd-inline-action-delete"
					aria-label={ __( 'Move to Trash', 'eazydocs' ) }
					title={ __( 'Move to Trash', 'eazydocs' ) }
					onClick={ handleDelete }
				>
					<span className="dashicons dashicons-trash"></span>
				</a>
			) }
		</>
	);
};

export default DocItemContent;
