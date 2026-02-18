<?php
/**
 * Cannot access directly.
 */
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

add_action( 'wp_ajax_eazydocs_feedback_email', 'eazydocs_feedback_email' );    //execute when wp logged in
add_action( 'wp_ajax_nopriv_eazydocs_feedback_email', 'eazydocs_feedback_email' ); //execute when logged out

/**
 * Send email feedback on a document.
 *
 */
function eazydocs_feedback_email() {

	check_ajax_referer( 'eazydocs-ajax', 'security' );

	// Sentinel: Rate limit check (10 minutes)
	$user_ip       = ! empty( $_SERVER['REMOTE_ADDR'] ) ? sanitize_text_field( wp_unslash( $_SERVER['REMOTE_ADDR'] ) ) : '127.0.0.1';
	$transient_key = 'ezd_feedback_limit_' . md5( $user_ip );
	if ( get_transient( $transient_key ) ) {
		wp_send_json_error( esc_html__( 'Please wait before sending another feedback.', 'eazydocs' ) );
	}
	set_transient( $transient_key, true, 10 * MINUTE_IN_SECONDS );

	if ( isset( $_POST['email'] ) ) {
		$admin_email      = ezd_get_opt( 'feedback-admin-email', get_option( 'admin_email' ) );
		$author           = ! empty ( $_POST['name'] ) ? sanitize_text_field( wp_unslash( $_POST['name'] ) ) : '';
		$author           = str_replace( [ "\r", "\n" ], '', $author ); // Prevent header injection
		$subject          = ! empty ( $_POST['subject'] ) ? sanitize_text_field( wp_unslash( $_POST['subject'] ) ) : '';
		$feedback_subject = ! empty ( $_POST['subject'] ) ? sanitize_text_field( wp_unslash( $_POST['subject'] ) ) : '';
		$email            = ! empty ( $_POST['email'] ) ? sanitize_email( wp_unslash( $_POST['email'] ) ) : '';
		$message          = ! empty ( $_POST['message'] ) ? sanitize_text_field( wp_unslash( $_POST['message'] ) ) : '';
		$doc_id           = ! empty ( $_POST['doc_id'] ) ? intval( $_POST['doc_id'] ) : 0;

		if ( ! is_user_logged_in() ) {
			$email = ! empty( $_POST['email'] ) ? sanitize_email( wp_unslash( $_POST['email'] ) ) : '';

			if ( ! $email ) {
				wp_send_json_error( esc_html__( 'Please enter a valid email address.', 'eazydocs' ) );
			}
		} else {
			$email = wp_get_current_user()->user_email;
		}

		if ( empty ( $subject ) ) {
			wp_send_json_error( esc_html__( 'Please provide a subject line.', 'eazydocs' ) );
		}

		if ( ! isset ( $message ) ) {
			wp_send_json_error( esc_html__( 'Please provide the message details.', 'eazydocs' ) );
		}
		
		$wp_email = 'wordpress@' . preg_replace( '#^www\.#', '', strtolower( sanitize_text_field( wp_unslash( $_SERVER['SERVER_NAME'] ?? '' ) ) ) );

		$blogname = wp_specialchars_decode( get_option( 'blogname' ), ENT_QUOTES );
		$document = get_post( $doc_id );

		$email_to = $admin_email;
		/* translators: 1: Site name, 2: Subject line */
		$subject = sprintf( __( '[%1$s] New Doc Feedback: "%2$s"', 'eazydocs' ), $blogname, $subject );
		/* translators: %s: Document title */
		$email_body = sprintf( __( 'New feedback on your doc "%s"', 'eazydocs' ),
				apply_filters( 'eazydocs_translate_text', $document->post_title ) ) . "\r\n";
		/* translators: %s: Author name */
		$email_body .= sprintf( esc_html__( 'Author: %s', 'eazydocs' ), $author ) . "\r\n";
		/* translators: %s: Email address */
		$email_body .= sprintf( esc_html__( 'Email: %s', 'eazydocs' ), $email ) . "\r\n";
		/* translators: %s: Feedback message */
		$email_body .= sprintf( esc_html__( 'Feedback: %s', 'eazydocs' ), "\r\n" . $message ) . "\r\n\r\n";
		/* translators: %s: Document permalink */
		$email_body .= sprintf( esc_html__( 'Doc Permalink: %s', 'eazydocs' ), get_permalink( $document ) ) . "\r\n";
		/* translators: %s: Edit document URL */
		$email_body .= sprintf( esc_html__( 'Edit Doc: %s', 'eazydocs' ), admin_url( 'post.php?action=edit&post=' . $doc_id ) ) . "\r\n";

		$from     = "From: \"{$author}\" <{$wp_email}>";
		$reply_to = "Reply-To: \"{$email}\" <{$email}>";

		$message_headers = "{$from}\n"
		                   . 'Content-Type: text/plain; charset ="' . get_option( 'blog_charset' ) . "\"\n";
		$message_headers .= $reply_to . "\n";

		$email_to        = apply_filters( 'eazydocs_email_feedback_to', $email_to, $doc_id, $document );
		$subject         = apply_filters( 'eazydocs_email_feedback_subject', $subject, $doc_id, $document, $_POST );
		$email_body      = apply_filters( 'eazydocs_email_feedback_body', $email_body, $doc_id, $document, $_POST );
		$message_headers = apply_filters( 'eazydocs_email_feedback_headers', $message_headers, $doc_id, $document, $_POST );
		wp_mail( $email_to, wp_specialchars_decode( $subject ), $email_body, $message_headers );

		$args = [
			'post_type'    => 'ezd_feedback',
			'post_title'   => $feedback_subject . ' - ' . $author,
			'post_content' => $message,
			'post_status'  => 'publish'

		];

		$feedback = wp_insert_post( $args, $wp_error = '' );

		if ( $feedback != 0 ) {

			if ( ! empty( $doc_id ) ) {
				update_post_meta( $feedback, 'ezd_feedback_id', $doc_id );
			}

			if ( ! empty( $author ) ) {
				update_post_meta( $feedback, 'ezd_feedback_name', $author );
			}

			if ( ! empty( $email ) ) {
				update_post_meta( $feedback, 'ezd_feedback_email', $email );
			}

			if ( ! empty( $feedback_subject ) ) {
				update_post_meta( $feedback, 'ezd_feedback_subject', $feedback_subject );
			}

		}
		wp_send_json_success( __( 'Your feedback has been submitted successfully.', 'eazydocs' ) );
	}
}

add_action( 'eazydocs_negative_feedback_notification', 'ezd_send_negative_feedback_email', 10, 2 );

/**
 * Send email notification to admin on high negative feedback
 *
 * @param int $post_id The post ID
 * @param int $count   The negative feedback count
 */
function ezd_send_negative_feedback_email( $post_id, $count ) {
	$admin_email = ezd_get_opt( 'feedback-admin-email', get_option( 'admin_email' ) );

	if ( ! is_email( $admin_email ) ) {
		return;
	}

	$post = get_post( $post_id );
	if ( ! $post ) {
		return;
	}

	$subject = sprintf( __( '[%s] Alert: High Negative Feedback on Doc', 'eazydocs' ), get_bloginfo( 'name' ) );

	$post_title = wp_strip_all_tags( $post->post_title );
	$message    = sprintf( __( 'The document "%s" has received %d negative feedback votes.', 'eazydocs' ), $post_title, $count ) . "\r\n\r\n";
	$message   .= sprintf( __( 'View Document: %s', 'eazydocs' ), get_permalink( $post_id ) ) . "\r\n";
	$message   .= sprintf( __( 'Edit Document: %s', 'eazydocs' ), admin_url( 'post.php?post=' . $post_id . '&action=edit' ) ) . "\r\n";

	$to      = apply_filters( 'ezd_negative_feedback_email_to', $admin_email, $post_id, $count, $post );
	$subject = apply_filters( 'ezd_negative_feedback_email_subject', $subject, $post_id, $count, $post );
	$message = apply_filters( 'ezd_negative_feedback_email_body', $message, $post_id, $count, $post );
	$headers = apply_filters( 'ezd_negative_feedback_email_headers', array(), $post_id, $count, $post );

	$result = wp_mail( $to, wp_specialchars_decode( $subject ), $message, $headers );

	if ( ! $result ) {
		error_log( sprintf( 'EazyDocs: Failed to send negative feedback notification email for post %d to %s', $post_id, $to ) );
	}
}