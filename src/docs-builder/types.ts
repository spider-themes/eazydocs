/**
 * Shared TypeScript interfaces for the Docs Builder.
 *
 * @package EazyDocs
 * @since   2.8.0
 */

/**
 * Visibility information for a doc item.
 */
export interface DocVisibility {
	isPrivate: boolean;
	hasRoleVisibility: boolean;
	rolesList: string;
	isDraft: boolean;
	isProtected: boolean;
}

/**
 * Pro actions structured data for duplicate action.
 */
export interface ProDuplicateAction {
	url: string;
	nonce: string;
}

/**
 * Pro actions structured data for visibility action.
 */
export interface ProVisibilityAction {
	url: string;
	nonce: string;
	currentVisibility: 'publish' | 'private' | 'protected';
	currentPassword: string;
	roleVisibilityRoles: string[];
	roleVisibilityGuest: boolean;
}

/**
 * Pro actions structured data for sidebar action.
 */
export interface ProSidebarAction {
	leftType: string;
	leftContent: string;
	rightType: string;
	rightContent: string;
}

/**
 * Pro actions data (null when not premium).
 */
export interface ProActions {
	duplicate: ProDuplicateAction | null;
	visibility: ProVisibilityAction | null;
	sidebar: ProSidebarAction | null;
}

/**
 * A child doc item (nested tree node).
 */
export interface DocChild {
	id: number;
	title: string;
	status: string;
	hasPassword: boolean;
	permalink: string;
	editLink: string;
	canEdit: boolean;
	canDelete: boolean;
	canAddChild: boolean;
	deleteNonce: string;
	childNonce: string;
	childCount: number;
	positive: number;
	negative: number;
	visibility: DocVisibility;
	children: DocChild[];
	proActions: ProActions;
}

/**
 * A parent (top-level) doc item.
 */
export interface ParentDoc {
	id: number;
	title: string;
	status: string;
	hasPassword: boolean;
	permalink: string;
	editLink: string;
	canEdit: boolean;
	canDelete: boolean;
	deleteNonce: string;
	sectionNonce: string;
	childCount: number;
	statusIcon: string;
	statusText: string;
	proActions: ProActions;
}

/**
 * Capabilities for the current user.
 */
export interface Capabilities {
	canManageOptions: boolean;
	canPublishDocs: boolean;
	canEditDocs: boolean;
	hasSettingsAccess: boolean;
}

/**
 * URL references used across the builder.
 */
export interface BuilderUrls {
	adminUrl: string;
	settings: string;
	trash: string;
	classicUi: string;
	pricing: string;
	antimanualDocs: string;
	assetsUrl: string;
	settingsIcon: string;
	notificationIcon: string;
	proIcon: string;
	folderOpenIcon: string;
}

/**
 * Nonces used across the builder.
 */
export interface BuilderNonces {
	parentDoc: string;
	adminNonce: string;
	notification: string;
}

/**
 * Role visibility configuration.
 */
export interface RoleVisibilityConfig {
	enabled: boolean;
	roles: Array< { slug: string; name: string } >;
}

/**
 * Full data object returned by the REST API.
 */
export interface BuilderData {
	parentDocs: ParentDoc[];
	childrenMap: Record< number, DocChild[] >;
	capabilities: Capabilities;
	isPremium: boolean;
	antimanualActive: boolean;
	trashCount: number;
	notificationCount: number;
	roleVisibility: RoleVisibilityConfig;
	urls: BuilderUrls;
	nonces: BuilderNonces;
	currentTheme: string;
}

/**
 * Notification item from the REST API.
 */
export interface NotificationItem {
	type: 'vote' | 'comment';
	voteType?: 'positive' | 'negative';
	postId?: number;
	postTitle?: string;
	permalink?: string;
	thumbnail?: string | false;
	commentId?: number;
	author?: string;
	avatar?: string;
	commentLink?: string;
	isRead?: boolean;
	timestamp: number;
	timeAgo: string;
}

/**
 * Notification API response.
 */
export interface NotificationResponse {
	items: NotificationItem[];
	hasMore: boolean;
	total: number;
	page: number;
}

/**
 * Toast notification data for the toast system.
 */
export interface ToastData {
	id: string;
	message: string;
	type: 'success' | 'error';
}
