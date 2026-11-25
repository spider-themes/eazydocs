<?php
/**
 * Notification endpoints and unread counters for Docs_Builder_Controller.
 *
 * @package EazyDocs\Admin\Api
 */

namespace EazyDocs\Admin\Api;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

trait DocsBuilderNotificationsTrait {
	/**
	 * Mark a specific notification item as read.
	 *
	 * @since  2.9.0
	 * @param  \WP_REST_Request $request REST request.
	 * @return \WP_REST_Response
	 */
	public function mark_notification_read( $request ) {
		$type    = sanitize_text_field( $request->get_param( 'type' ) );
		$post_id = absint( $request->get_param( 'postId' ) );
		$timestamp = absint( $request->get_param( 'timestamp' ) );

		if ( ! $type || ! $post_id ) {
			return new \WP_Error( 'missing_data', 'Missing required data', array( 'status' => 400 ) );
		}

		if ( 'vote' === $type ) {
			// For votes, we log that this post's vote at this timestamp was read.
			$read_votes = get_user_meta( get_current_user_id(), '_ezd_read_votes', true );
			if ( ! is_array( $read_votes ) ) {
				$read_votes = array();
			}
			$key = $post_id . '_' . $timestamp;
			if ( ! in_array( $key, $read_votes, true ) ) {
				$read_votes[] = $key;
				update_user_meta( get_current_user_id(), '_ezd_read_votes', $read_votes );
			}
		} elseif ( 'comment' === $type ) {
			// For comments, post_id is actually the comment ID in the notification context.
			$comment_id = $post_id;
			update_comment_meta( $comment_id, '_ezd_is_read', 1 );
		}

		return new \WP_REST_Response( array( 'success' => true ), 200 );
	}

	/**
	 * Get the unread notification count for the Docs Builder header.
	 *
	 * Counts unread vote and comment notifications using the same read-state
	 * sources as the builder notification list.
	 *
	 * @since  2.9.0
	 * @return int
	 */
	private function get_unread_notification_count() {
		if ( ! $this->can_access_pro_notifications() ) {
			return 0;
		}

		$unread_count = 0;
		$read_votes   = get_user_meta( get_current_user_id(), '_ezd_read_votes', true );

		if ( ! is_array( $read_votes ) ) {
			$read_votes = array();
		}

		// Only the two vote timestamps are needed, so read them straight from
		// postmeta instead of loading every published doc (content, meta, terms).
		global $wpdb;
		// phpcs:ignore WordPress.DB.DirectDatabaseQuery
		$vote_rows = $wpdb->get_results(
			"SELECT pm.post_id, pm.meta_value
			 FROM {$wpdb->postmeta} pm
			 INNER JOIN {$wpdb->posts} p ON p.ID = pm.post_id
			 WHERE pm.meta_key IN ('positive_time', 'negative_time')
			   AND pm.meta_value <> ''
			   AND p.post_type = 'docs'
			   AND p.post_status = 'publish'"
		);

		$read_lookup = array_flip( $read_votes );
		foreach ( (array) $vote_rows as $row ) {
			if ( ! isset( $read_lookup[ $row->post_id . '_' . strtotime( $row->meta_value ) ] ) ) {
				++$unread_count;
			}
		}

		$comments = get_comments(
			array(
				'post_status' => 'publish',
				'post_type'   => array( 'docs' ),
				'parent'      => 0,
				'order'       => 'desc',
				'number'      => 100,
			)
		);

		foreach ( $comments as $comment ) {
			if ( ! (bool) get_comment_meta( $comment->comment_ID, '_ezd_is_read', true ) ) {
				++$unread_count;
			}
		}

		return $unread_count;
	}

	/**
	 * Check whether the current request can access Pro notification features.
	 *
	 * Keeps the Docs Builder notification feature gated the same way as before:
	 * only when Pro is active and the Pro notification helpers are available.
	 *
	 * @since  2.9.0
	 * @return bool
	 */
	private function can_access_pro_notifications() {
		return ezd_is_premium()
			&& current_user_can( 'manage_options' )
			&& function_exists( 'eazydocs_voted' )
			&& function_exists( 'ezd_comment_count' );
	}

	/**
	 * Get paginated notifications as JSON.
	 *
	 * @since  2.9.0
	 * @param  \WP_REST_Request $request REST request.
	 * @return \WP_REST_Response
	 */
	public function get_notifications( $request ) {
		if ( ! $this->can_access_pro_notifications() ) {
			return new \WP_REST_Response(
				array(
					'items'   => array(),
					'hasMore' => false,
					'total'   => 0,
					'page'    => 1,
				),
				200
			);
		}

		$page     = absint( $request->get_param( 'page' ) ) ?: 1;
		$per_page = min( 50, absint( $request->get_param( 'per_page' ) ) ?: 10 );
		$filter   = sanitize_text_field( $request->get_param( 'filter' ) ) ?: 'all';

		$allowed_filters = array( 'all', 'comment', 'vote' );
		if ( ! in_array( $filter, $allowed_filters, true ) ) {
			$filter = 'all';
		}

		$total_items = array();

		$read_votes = get_user_meta( get_current_user_id(), '_ezd_read_votes', true );
		if ( ! is_array( $read_votes ) ) {
			$read_votes = array();
		}

		// Get votes if filter allows.
		if ( 'all' === $filter || 'vote' === $filter ) {
			// Only docs that actually received a vote (previously every doc was
			// loaded and then filtered in PHP).
			$args = array(
				'post_type'              => 'docs',
				'posts_per_page'         => -1,
				'post_status'            => array( 'publish' ),
				'no_found_rows'          => true,
				'update_post_term_cache' => false,
				'meta_query'             => array(
					'relation' => 'OR',
					array(
						'key'     => 'positive_time',
						'compare' => 'EXISTS',
					),
					array(
						'key'     => 'negative_time',
						'compare' => 'EXISTS',
					),
				),
			);

			$docs = get_posts( $args );
			update_postmeta_cache( wp_list_pluck( $docs, 'ID' ) );

			foreach ( $docs as $post ) {
				$positive_time = get_post_meta( $post->ID, 'positive_time', true );
				if ( ! empty( $positive_time ) ) {
					$time = strtotime( $positive_time );
					$is_read = in_array( $post->ID . '_' . $time, $read_votes, true );
					$total_items[] = array(
						'type'      => 'vote',
						'voteType'  => 'positive',
						'postId'    => $post->ID,
						'postTitle' => html_entity_decode( $post->post_title ),
						'permalink' => get_permalink( $post->ID ),
						'thumbnail' => get_the_post_thumbnail_url( $post->ID, 'thumbnail' ),
						'timestamp' => $time,
						'timeAgo'   => human_time_diff( $time, time() ) . __( ' ago', 'eazydocs' ),
						'isRead'    => $is_read,
					);
				}

				$negative_time = get_post_meta( $post->ID, 'negative_time', true );
				if ( ! empty( $negative_time ) ) {
					$time = strtotime( $negative_time );
					$is_read = in_array( $post->ID . '_' . $time, $read_votes, true );
					$total_items[] = array(
						'type'      => 'vote',
						'voteType'  => 'negative',
						'postId'    => $post->ID,
						'postTitle' => html_entity_decode( $post->post_title ),
						'permalink' => get_permalink( $post->ID ),
						'thumbnail' => get_the_post_thumbnail_url( $post->ID, 'thumbnail' ),
						'timestamp' => $time,
						'timeAgo'   => human_time_diff( $time, time() ) . __( ' ago', 'eazydocs' ),
						'isRead'    => $is_read,
					);
				}
			}
		}

		// Get comments if filter allows.
		if ( 'all' === $filter || 'comment' === $filter ) {
			$comments = get_comments(
				array(
					'post_status' => 'publish',
					'post_type'   => array( 'docs' ),
					'parent'      => 0,
					'order'       => 'desc',
					'number'      => 100,
				)
			);

			foreach ( $comments as $comment ) {
				$is_read = (bool) get_comment_meta( $comment->comment_ID, '_ezd_is_read', true );
				$total_items[] = array(
					'type'          => 'comment',
					'commentId'     => (int) $comment->comment_ID,
					'author'        => $comment->comment_author,
					'avatar'        => get_avatar_url( $comment, array( 'size' => 40 ) ),
					'postId'        => (int) $comment->comment_post_ID,
					'postTitle'     => html_entity_decode( get_the_title( $comment->comment_post_ID ) ),
					'commentLink'   => get_comment_link( $comment ),
					'timestamp'     => strtotime( $comment->comment_date ),
					'timeAgo'       => human_time_diff( strtotime( $comment->comment_date ), time() ) . __( ' ago', 'eazydocs' ),
					'isRead'        => $is_read,
				);
			}
		}

		// Sort by timestamp descending.
		usort(
			$total_items,
			function ( $a, $b ) {
				return $b['timestamp'] - $a['timestamp'];
			}
		);

		$total   = count( $total_items );
		$offset  = ( $page - 1 ) * $per_page;
		$items   = array_slice( $total_items, $offset, $per_page );
		$hasMore = ( $offset + $per_page ) < $total;

		return new \WP_REST_Response(
			array(
				'items'   => $items,
				'hasMore' => $hasMore,
				'total'   => $total,
				'page'    => $page,
			),
			200
		);
	}
}
