<?php
/**
 * Documentation Update Subscriptions
 * Allow users to subscribe for notifications when docs are updated.
 */
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}


// Subscriptions
CSF::createSection( $prefix, array(
	'id'     => 'subscriptions_opt',
	'title'  => esc_html__( 'Update Notifications', 'eazydocs' ),
	'icon'   => 'dashicons dashicons-email',
	'fields' => array(
		array(
			'type'  => 'heading',
			'title' => esc_html__( 'Subscription Settings', 'eazydocs' ),
			'subtitle' => esc_html__( 'Let readers subscribe to receive email notifications when documentation is updated.', 'eazydocs' )
		),

		array(
			'id'         => 'subscriptions',
			'type'       => 'switcher',
			'title'      => esc_html__( 'Enable Subscriptions', 'eazydocs' ),
			'subtitle'   => esc_html__( 'Show a subscribe button that allows users to opt-in for update notifications.', 'eazydocs' ),
			'text_on'    => esc_html__( 'Enabled', 'eazydocs' ),
			'text_off'   => esc_html__( 'Disabled', 'eazydocs' ),
			'default'    => false,
			'class'      => 'eazydocs-promax-notice',
			'text_width' => 95
		),
		
		array(
			'id'            => 'subscriptions_tab',
			'type'          => 'tabbed',
			'title'         => esc_html__( 'Form Customization', 'eazydocs' ),
			'subtitle'      => esc_html__( 'Customize the subscribe and unsubscribe form text and messages.', 'eazydocs' ),
			'dependency' 	=> array( 'subscriptions', '==', 'true' ),
			'class'     	=> 'eazydocs-promax-notice',
			'tabs'          => array(
			  array(
				'title'     =>  esc_html__( 'Subscribe Form', 'eazydocs' ),
				'fields'    => array(
					array(
						'id'         => 'subscriptions_btn',
						'type'       => 'text',
						'title'      => esc_html__( 'Subscribe Button', 'eazydocs' ),
						'subtitle'   => esc_html__( 'Text displayed on the subscription trigger button.', 'eazydocs' ),
						'default'    => esc_html__( 'Subscribe for updates', 'eazydocs' )
					),
					
					array(
						'id'         => 'subscriptions_heading',
						'type'       => 'text',
						'title'      => esc_html__( 'Form Heading', 'eazydocs' ),
						'subtitle'   => esc_html__( 'Title shown at the top of the subscription form.', 'eazydocs' ),
						'default'    => esc_html__( 'Subscribe', 'eazydocs' ),
					),
					
					array(
						'id'         => 'subscriptions_name_label',
						'type'       => 'text',
						'title'      => esc_html__( 'Name Field Label', 'eazydocs' ),
						'default'    => esc_html__( 'Name', 'eazydocs' ),
					),
					array(
						'id'         => 'subscriptions_name_placeholder',
						'type'       => 'text',
						'title'      => esc_html__( 'Name Placeholder', 'eazydocs' ),
						'default'    => esc_html__( 'Enter your name', 'eazydocs' )
					),

					array(
						'id'         => 'subscriptions_email_label',
						'type'       => 'text',
						'title'      => esc_html__( 'Email Field Label', 'eazydocs' ),
						'default'    => esc_html__( 'Email', 'eazydocs' ),
					),
					array(
						'id'         => 'subscriptions_email_placeholder',
						'type'       => 'text',
						'title'      => esc_html__( 'Email Placeholder', 'eazydocs' ),
						'default'    => esc_html__( 'Enter your email', 'eazydocs' )
					), 
					
					array(
						'id'         => 'subscriptions_submit_btn',
						'type'       => 'text',
						'title'      => esc_html__( 'Submit Button', 'eazydocs' ),
						'default'    => esc_html__( 'Subscribe', 'eazydocs' ),
					),
	
					array(
						'id'         => 'subscriptions_cancel_btn',
						'type'       => 'text',
						'title'      => esc_html__( 'Cancel Button', 'eazydocs' ),
						'default'    => esc_html__( 'Cancel', 'eazydocs' ),
					),
	
					// messages				 
					array(
						'id'         => 'subscriptions_success',
						'type'       => 'text',
						'title'      => esc_html__( 'Success Message', 'eazydocs' ),
						'subtitle'   => esc_html__( 'Message shown after successful subscription.', 'eazydocs' ),
						'default'    => esc_html__( 'Confirmation email sent successfully!', 'eazydocs' ),
					),
					
					array(
						'id'         => 'subscriptions_email_exist',
						'type'       => 'text',
						'title'      => esc_html__( 'Duplicate Email Message', 'eazydocs' ),
						'subtitle'   => esc_html__( 'Message shown when email is already subscribed.', 'eazydocs' ),
						'default'    => esc_html__( 'Already email exists.', 'eazydocs' ),
					),
				
					array(
						'id'         => 'subscriptions_special_character',
						'type'       => 'text',
						'title'      => esc_html__( 'Invalid Characters Message', 'eazydocs' ),
						'subtitle'   => esc_html__( 'Message shown when invalid characters are detected.', 'eazydocs' ),
						'default'    => esc_html__( 'Special characters not allowed!', 'eazydocs' ),
					),
					
				)
			  ),
			  array(
				'title'     =>  esc_html__( 'Unsubscribe Form', 'eazydocs' ),
				'fields'    => array(
				  
					array(
						'id'         => 'unsubscriptions_btn',
						'type'       => 'text',
						'title'      => esc_html__( 'Unsubscribe Button', 'eazydocs' ),
						'default'    => esc_html__( 'Unsubscribe', 'eazydocs' ),
					),
					
					array(
						'id'         => 'unsubscriptions_heading',
						'type'       => 'text',
						'title'      => esc_html__( 'Form Heading', 'eazydocs' ),
						'default'    => esc_html__( 'Unsubscribe', 'eazydocs' ),
					),
					
					array(
						'id'         => 'unsubscriptions_desc',
						'type'       => 'textarea',
						'title'      => esc_html__( 'Confirmation Message', 'eazydocs' ),
						'subtitle'   => esc_html__( 'Message asking user to confirm unsubscription.', 'eazydocs' ),
						'default'    => esc_html__( "Are you sure you'd like to stop receiving updates", 'eazydocs' ),
					),
					
					array(
						'id'         => 'unsubscriptions_post_title',
						'type'       => 'checkbox',
						'title'      => esc_html__( 'Show Document Title', 'eazydocs' ),
						'text_on'    => esc_html__( 'Yes', 'eazydocs' ),
						'text_off'   => esc_html__( 'No', 'eazydocs' ),
						'label'	 	 => esc_html__( 'Include the document title in the confirmation message', 'eazydocs' ),
						'default'    => true,
					),
					
					array(
						'id'         => 'unsubscriptions_submit_btn',
						'type'       => 'text',
						'title'      => esc_html__( 'Confirm Button', 'eazydocs' ),
						'default'    => esc_html__( 'Confirm', 'eazydocs' ),
					),
	
					array(
						'id'         => 'unsubscriptions_cancel_btn',
						'type'       => 'text',
						'title'      => esc_html__( 'Cancel Button', 'eazydocs' ),
						'default'    => esc_html__( 'Cancel', 'eazydocs' ),
					),

				)
			  ),
			)
		)
	)
) );