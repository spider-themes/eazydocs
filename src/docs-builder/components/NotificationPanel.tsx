/**
 * NotificationPanel component.
 *
 * Renders the notification dropdown panel for Pro users,
 * replacing the dangerouslySetInnerHTML + jQuery infinite scroll
 * approach with a proper React component using TanStack Query.
 *
 * @package EazyDocs
 * @since   2.9.0
 */
import { useState, useRef, useEffect, useCallback } from '@wordpress/element';
import { __ } from '@wordpress/i18n';
import { useNotifications, useMarkNotificationRead } from '../hooks/useNotifications';
import type { NotificationItem } from '../types';

interface NotificationPanelProps {
	notificationCount: number;
	notificationIcon: string;
}

/**
 * Renders a single notification item.
 */
const NotificationItemRow: React.FC< { item: NotificationItem } > = ( { item } ) => {
	const { mutate: markRead } = useMarkNotificationRead();
	const [ isRead, setIsRead ] = useState( item.isRead || false );

	const handleRead = () => {
		if ( ! isRead ) {
			setIsRead( true );
			markRead( {
				type: item.type,
				postId: item.postId!,
				timestamp: item.timestamp,
			} );
		}
	};

	if ( 'vote' === item.type ) {
		const isPositive = 'positive' === item.voteType;
		const voteClass = isPositive ? 'ezd-vote-positive' : 'ezd-vote-negative';
		const voteIcon = isPositive ? 'dashicons-thumbs-up' : 'dashicons-thumbs-down';
		const voteLabel = isPositive
			? __( 'Positive vote', 'eazydocs' )
			: __( 'Negative vote', 'eazydocs' );
		const readClass = isRead ? 'ezd-notification-read' : '';

		return (
			<a
				href={ item.permalink || '#' }
				className={ `ezd-notification-item ezd-item-vote ${ voteClass } ${ readClass }` }
				target="_blank"
				rel="noopener noreferrer"
				onClick={ handleRead }
				style={ isRead ? { opacity: 0.6 } : {} }
			>
				<div className="ezd-notification-avatar">
					{ item.thumbnail ? (
						<img src={ item.thumbnail } alt="" width="40" height="40" />
					) : (
						<div className="ezd-avatar-placeholder">
							<span className="dashicons dashicons-media-document"></span>
						</div>
					) }
					<span className={ `ezd-notification-type-badge ${ voteClass }` }>
						<span className={ `dashicons ${ voteIcon }` }></span>
					</span>
				</div>
				<div className="ezd-notification-content">
					<p className="ezd-notification-text">
						<strong>{ voteLabel }</strong>
						{ ' ' }
						{ __( 'on', 'eazydocs' ) }
						{ ' ' }
						<span className="ezd-doc-title">{ item.postTitle }</span>
					</p>
					<time className="ezd-notification-time">{ item.timeAgo }</time>
				</div>
			</a>
		);
	}

	// Comment type.
	const readClass = isRead ? 'ezd-notification-read' : '';
	return (
		<a
			href={ item.commentLink || '#' }
			className={ `ezd-notification-item ezd-item-comment ${ readClass }` }
			target="_blank"
			rel="noopener noreferrer"
			onClick={ handleRead }
			style={ isRead ? { opacity: 0.6 } : {} }
		>
			<div className="ezd-notification-avatar">
				{ item.avatar ? (
					<img src={ item.avatar } alt="" width="40" height="40" />
				) : (
					<div className="ezd-avatar-placeholder">
						<span className="dashicons dashicons-admin-users"></span>
					</div>
				) }
				<span className="ezd-notification-type-badge ezd-badge-comment">
					<span className="dashicons dashicons-format-chat"></span>
				</span>
			</div>
			<div className="ezd-notification-content">
				<p className="ezd-notification-text">
					<strong>{ item.author }</strong>
					{ ' ' }
					{ __( 'commented on', 'eazydocs' ) }
					{ ' ' }
					<span className="ezd-doc-title">{ item.postTitle }</span>
				</p>
				<time className="ezd-notification-time">{ item.timeAgo }</time>
			</div>
		</a>
	);
};

/**
 * Skeleton loader for notification items.
 */
const NotificationSkeletonItem: React.FC = () => (
	<div className="ezd-notification-item ezd-notification-skeleton" aria-hidden="true">
		<div className="ezd-notification-avatar">
			<div className="ezd-skeleton-line" style={ { width: '40px', height: '40px', borderRadius: '50%' } }></div>
		</div>
		<div className="ezd-notification-content" style={ { flex: 1 } }>
			<div className="ezd-skeleton-line" style={ { width: '80%', height: '14px', marginBottom: '6px' } }></div>
			<div className="ezd-skeleton-line" style={ { width: '40%', height: '12px' } }></div>
		</div>
	</div>
);

type FilterType = 'all' | 'comment' | 'vote';

const NotificationPanel: React.FC< NotificationPanelProps > = ( { notificationCount, notificationIcon } ) => {
	const [ isOpen, setIsOpen ] = useState< boolean >( false );
	const [ activeFilter, setActiveFilter ] = useState< FilterType >( 'all' );
	const panelRef = useRef< HTMLLIElement >( null );
	const listContainerRef = useRef< HTMLDivElement >( null );

	const {
		data,
		isLoading,
		isFetchingNextPage,
		hasNextPage,
		fetchNextPage,
	} = useNotifications( activeFilter, isOpen );

	// Toggle notification panel.
	const handleToggle = useCallback( (): void => {
		setIsOpen( ( prev ) => ! prev );
	}, [] );

	// Close on outside click.
	useEffect( () => {
		const handleClickOutside = ( e: MouseEvent ): void => {
			if ( panelRef.current && ! panelRef.current.contains( e.target as Node ) ) {
				setIsOpen( false );
			}
		};

		if ( isOpen ) {
			document.addEventListener( 'mousedown', handleClickOutside );
		}

		return () => document.removeEventListener( 'mousedown', handleClickOutside );
	}, [ isOpen ] );

	// Infinite scroll handler.
	const handleScroll = useCallback( (): void => {
		const container = listContainerRef.current;
		if ( ! container || ! hasNextPage || isFetchingNextPage ) {
			return;
		}

		const scrollTop = container.scrollTop;
		const scrollHeight = container.scrollHeight;
		const clientHeight = container.clientHeight;

		if ( scrollTop + clientHeight >= scrollHeight - 100 ) {
			fetchNextPage();
		}
	}, [ hasNextPage, isFetchingNextPage, fetchNextPage ] );

	// Gather all items from pages.
	const allItems = data?.pages.flatMap( ( page ) => page.items ) || [];

	const filters: Array< { key: FilterType; label: string; icon: string } > = [
		{ key: 'all', label: __( 'All', 'eazydocs' ), icon: 'dashicons-list-view' },
		{ key: 'comment', label: __( 'Comments', 'eazydocs' ), icon: 'dashicons-format-chat' },
		{ key: 'vote', label: __( 'Votes', 'eazydocs' ), icon: 'dashicons-thumbs-up' },
	];

	return (
		<li
			className="easydocs-notification ezd-notification-redesigned"
			title={ __( 'Notifications', 'eazydocs' ) }
			ref={ panelRef }
		>
			<div
				className="header-notify-icon"
				onClick={ ( e ) => {
					e.stopPropagation();
					e.nativeEvent.stopImmediatePropagation();
					handleToggle();
				} }
				role="button"
				tabIndex={ 0 }
				onKeyDown={ ( e: React.KeyboardEvent ) => {
					if ( 13 === e.which || 32 === e.which ) {
						e.preventDefault();
						e.stopPropagation();
						e.nativeEvent.stopImmediatePropagation();
						handleToggle();
					}
				} }
			>
				<svg className="notify-icon-svg" width="20" height="20" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
					<path d="M12 22C13.1 22 14 21.1 14 20H10C10 21.1 10.9 22 12 22ZM18 16V11C18 7.93 16.36 5.36 13.5 4.68V4C13.5 3.17 12.83 2.5 12 2.5C11.17 2.5 10.5 3.17 10.5 4V4.68C7.63 5.36 6 7.92 6 11V16L4 18V19H20V18L18 16Z" fill="currentColor" />
				</svg>
			</div>

			{ notificationCount > 0 && (
				<span className="easydocs-badge ezd-notification-count">
					{ notificationCount > 99 ? '99+' : notificationCount }
				</span>
			) }

			{ isOpen && (
				<div className="easydocs-dropdown notification-dropdown ezd-notification-panel is-active" onClick={ ( e ) => e.stopPropagation() }>
					{ /* Header */ }
					<div className="ezd-notification-header">
						<div className="ezd-notification-header-left">
							<h4 className="ezd-notification-title">
								{ __( 'Notifications', 'eazydocs' ) }
							</h4>
							{ notificationCount > 0 && (
								<span className="ezd-notification-header-badge">
									{ notificationCount } { __( 'new', 'eazydocs' ) }
								</span>
							) }
						</div>
					</div>

					{ notificationCount > 0 ? (
						<>
							{ /* Filter tabs */ }
							<div className="ezd-notification-filters">
								{ filters.map( ( filter ) => (
									<button
										key={ filter.key }
										type="button"
										className={ `ezd-filter-tab${ activeFilter === filter.key ? ' is-active' : '' }` }
										data-filter={ filter.key }
										onClick={ ( e ) => {
											e.stopPropagation();
											e.nativeEvent.stopImmediatePropagation();
											setActiveFilter( filter.key );
										} }
									>
										<span className="ezd-filter-icon">
											<span className={ `dashicons ${ filter.icon }` }></span>
										</span>
										<span className="ezd-filter-text">{ filter.label }</span>
									</button>
								) ) }
							</div>

							{ /* List container */ }
							<div
								className="ezd-notification-list-container"
								ref={ listContainerRef }
								onScroll={ handleScroll }
							>
								{ isLoading ? (
									<div className="ezd-notification-list">
										{ Array.from( { length: 4 } ).map( ( _, i ) => (
											<NotificationSkeletonItem key={ i } />
										) ) }
									</div>
								) : allItems.length > 0 ? (
									<div className="ezd-notification-list">
										{ allItems.map( ( item, index ) => (
											<NotificationItemRow
												key={ `${ item.type }-${ item.timestamp }-${ index }` }
												item={ item }
											/>
										) ) }
									</div>
								) : (
									<div className="ezd-notification-empty">
										<div className="ezd-empty-icon">
											<span className="dashicons dashicons-bell" style={ { fontSize: '48px', width: '48px', height: '48px', color: '#ddd' } }></span>
										</div>
										<h5 className="ezd-empty-title">
											{ __( 'No notifications found', 'eazydocs' ) }
										</h5>
									</div>
								) }

								{ isFetchingNextPage && (
									<div className="ezd-notification-loader" style={ { display: 'flex' } }>
										<div className="ezd-loader-spinner"></div>
										<span>{ __( 'Loading more...', 'eazydocs' ) }</span>
									</div>
								) }

								{ ! hasNextPage && allItems.length > 0 && ! isLoading && (
									<div className="ezd-notification-end" style={ { display: 'flex' } }>
										<span className="dashicons dashicons-yes-alt"></span>
										<span>{ __( "You're all caught up!", 'eazydocs' ) }</span>
									</div>
								) }
							</div>
						</>
					) : (
						<div className="ezd-notification-empty">
							<div className="ezd-empty-icon">
								<svg width="48" height="48" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
									<path d="M12 22C13.1 22 14 21.1 14 20H10C10 21.1 10.9 22 12 22ZM18 16V11C18 7.93 16.36 5.36 13.5 4.68V4C13.5 3.17 12.83 2.5 12 2.5C11.17 2.5 10.5 3.17 10.5 4V4.68C7.63 5.36 6 7.92 6 11V16L4 18V19H20V18L18 16Z" fill="#ddd" />
								</svg>
							</div>
							<h5 className="ezd-empty-title">
								{ __( 'No notifications yet', 'eazydocs' ) }
							</h5>
							<p className="ezd-empty-text">
								{ __( "When you get notifications, they'll show up here.", 'eazydocs' ) }
							</p>
						</div>
					) }
				</div>
			) }
		</li>
	);
};

export default NotificationPanel;
