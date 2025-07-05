<?php
    add_action( 'wp_ajax_eazydocs_feedback_email', 'eazydocs_feedback_email' );    //execute when wp logged in
    add_action( 'wp_ajax_nopriv_eazydocs_feedback_email', 'eazydocs_feedback_email'); //execute when logged out
	
	/**
	 * Send email feedback on a document.
	 *
	 */
	function eazydocs_feedback_email() {

        if ( isset( $_POST['email'] ) ) {
			$admin_email    	= ezd_get_opt( 'feedback-admin-email', get_option('admin_email') );
			$author  			= ! empty ( $_POST['name'] ) ? sanitize_text_field( $_POST['name'] ) : '';
			$subject 			= ! empty ( $_POST['subject'] ) ? sanitize_text_field( $_POST['subject'] ) : '';
			$feedback_subject 	= ! empty ( $_POST['subject'] ) ? sanitize_text_field( $_POST['subject'] ) : '';
			$email   			= ! empty ( $_POST['email'] ) ? sanitize_email( $_POST['email'] ) : '';
			$message 			= ! empty ( $_POST['message'] ) ? sanitize_text_field( $_POST['message'] ) : '';
			$doc_id  			= ! empty ( $_POST['doc_id'] ) ? intval( $_POST['doc_id'] ) : 0;

			if ( ! is_user_logged_in() ) {
				$email = ! empty ( $_POST['email'] ) ? sanitize_email( $_POST['email'] ) : '';

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

			$wp_email = 'wordpress@' . preg_replace( '#^www\.#', '', strtolower( $_SERVER[ 'SERVER_NAME' ] ) );
			$blogname = wp_specialchars_decode( get_option( 'blogname' ), ENT_QUOTES );
			$document = get_post( $doc_id );

			$email_to = $admin_email;
			$subject  = sprintf( esc_html__( '[%1$s] New Doc Feedback: "%2$s"', 'eazydocs' ), $blogname, $subject );

			$email_body = sprintf( esc_html__( 'New feedback on your doc "%s"', 'eazydocs' ), apply_filters( 'eazydocs_translate_text', $document->post_title ) ) . "\r\n";
			$email_body .= sprintf( esc_html__( 'Author: %1$s', 'eazydocs' ), $author ) . "\r\n";
			$email_body .= sprintf( esc_html__( 'Email: %s', 'eazydocs' ), $email ) . "\r\n";
			$email_body .= sprintf( esc_html__( 'Feedback: %s', 'eazydocs' ), "\r\n" . $message ) . "\r\n\r\n";
			$email_body .= sprintf( esc_html__( 'Doc Permalink: %s', 'eazydocs' ), get_permalink( $document ) ) . "\r\n";
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
				'post_type' 	=> 'ezd_feedback',
				'post_title'	=> $feedback_subject .' - '. $author,
				'post_content'	=> $message,
				'post_status'	=> 'publish'
				
			];

			$feedback = wp_insert_post($args, $wp_error = '' );

			if( $feedback != 0 ){

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
			wp_die();
		}
	}