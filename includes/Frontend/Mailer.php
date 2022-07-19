<?php
    add_action( 'wp_ajax_eazydocs_feedback_email', 'eazydocs_feedback_email' );    //execute when wp logged in
    add_action( 'wp_ajax_nopriv_eazydocs_feedback_email', 'eazydocs_feedback_email'); //execute when logged out
	
	/**
	 * Send email feedback on a document.
	 *
	 */
	function eazydocs_feedback_email() {

        if( isset($_POST['email']) ) {
		$options        = get_option( 'eazydocs_settings' );
		$admin_email    = ! empty ( $options['feedback-admin-email'] ) ? $options['feedback-admin-email']  : get_option('admin_email');
 
		$author  = ! empty ( $_POST['name'] ) ? sanitize_text_field( $_POST['name'] ) : '';
		$subject = ! empty ( $_POST['subject'] ) ? sanitize_text_field( $_POST['subject'] ) : '';
		$email   = ! empty ( $_POST['email'] ) ? sanitize_email( $_POST['email'] ) : '';
		$message = ! empty ( $_POST['message'] ) ? sanitize_text_field( $_POST['message'] ) : '';
		$doc_id  = ! empty ( $_POST['doc_id'] ) ? intval( $_POST['doc_id'] ) : 0;

		if ( ! is_user_logged_in() ) {
			$email = ! empty ( $_POST['email'] ) ? sanitize_email( $_POST['email'] ) : '';

			if ( ! $email ) {
				wp_send_json_error( __( 'Please enter a valid email address.', 'eazydocs' ) );
			}
		} else {
			$email = wp_get_current_user()->user_email;
		}

		if ( empty ( $subject ) ) {
			wp_send_json_error( __( 'Please provide a subject line.', 'eazydocs' ) );
		}

		if ( ! isset ( $message ) ) {
			wp_send_json_error( __( 'Please provide the message details.', 'eazydocs' ) );
		}

		$wp_email = 'wordpress@' . preg_replace( '#^www\.#', '', strtolower( $_SERVER[ 'SERVER_NAME' ] ) );
		$blogname = wp_specialchars_decode( get_option( 'blogname' ), ENT_QUOTES );
		$document = get_post( $doc_id );

		$email_to = $admin_email;
		$subject  = sprintf( __( '[%1$s] New Doc Feedback: "%2$s"', 'eazydocs' ), $blogname, $subject );

		$email_body = sprintf( __( 'New feedback on your doc "%s"', 'eazydocs' ), apply_filters( 'eazydocs_translate_text', $document->post_title ) ) . "\r\n";
		$email_body .= sprintf( __( 'Author: %1$s', 'eazydocs' ), $author ) . "\r\n";
		$email_body .= sprintf( __( 'Email: %s', 'eazydocs' ), $email ) . "\r\n";
		$email_body .= sprintf( __( 'Feedback: %s', 'eazydocs' ), "\r\n" . $message ) . "\r\n\r\n";
		$email_body .= sprintf( __( 'Doc Permalink: %s', 'eazydocs' ), get_permalink( $document ) ) . "\r\n";
		$email_body .= sprintf( __( 'Edit Doc: %s', 'eazydocs' ), admin_url( 'post.php?action=edit&post=' . $doc_id ) ) . "\r\n";

		$from     = "From: \"${author}\" <${wp_email}>";
		$reply_to = "Reply-To: \"${email}\" <${email}>";

		$message_headers = "${from}\n"
		                   . 'Content-Type: text/plain; charset ="' . get_option( 'blog_charset' ) . "\"\n";
		$message_headers .= $reply_to . "\n";

		$email_to        = apply_filters( 'eazydocs_email_feedback_to', $email_to, $doc_id, $document );
		$subject         = apply_filters( 'eazydocs_email_feedback_subject', $subject, $doc_id, $document, $_POST );
		$email_body      = apply_filters( 'eazydocs_email_feedback_body', $email_body, $doc_id, $document, $_POST );
		$message_headers = apply_filters( 'eazydocs_email_feedback_headers', $message_headers, $doc_id, $document, $_POST );
		wp_mail( $email_to, wp_specialchars_decode( $subject ), $email_body, $message_headers );
		wp_die();
	}
	}