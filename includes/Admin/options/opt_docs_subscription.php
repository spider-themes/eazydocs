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
	'parent' => 'email_settings',
	'title'  => esc_html__( 'Update Notifications', 'eazydocs' ),
	'icon'   => '',
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
				'title'     =>  esc_html__( 'Email Design', 'eazydocs' ),
				'icon'      =>  'dashicons dashicons-email-alt',
				'fields'    => array(
					array(
						'type'    => 'subheading',
						'content' => esc_html__( 'Email Branding', 'eazydocs' ),
					),
					array(
						'id'         => 'email_logo_url',
						'type'       => 'upload',
						'title'      => esc_html__( 'Email Logo', 'eazydocs' ),
						'subtitle'   => esc_html__( 'Logo displayed in email header. Recommended size: 200x50px.', 'eazydocs' ),
						'library'    => 'image',
						'preview'    => true,
					),
					array(
						'id'         => 'email_footer_text',
						'type'       => 'textarea',
						'title'      => esc_html__( 'Footer Message', 'eazydocs' ),
						'subtitle'   => esc_html__( 'Custom message shown at the bottom of emails.', 'eazydocs' ),
						'default'    => esc_html__( 'Thank you for being a valued subscriber.', 'eazydocs' ),
					),
					
					array(
						'type'    => 'subheading',
						'content' => esc_html__( 'Email Content Options', 'eazydocs' ),
					),
					array(
						'id'         => 'email_show_excerpt',
						'type'       => 'switcher',
						'title'      => esc_html__( 'Show Article Excerpt', 'eazydocs' ),
						'subtitle'   => esc_html__( 'Display a preview of the article content in the email.', 'eazydocs' ),
						'text_on'    => esc_html__( 'Show', 'eazydocs' ),
						'text_off'   => esc_html__( 'Hide', 'eazydocs' ),
						'default'    => true,
						'text_width' => 80
					),
					array(
						'id'         => 'email_show_featured_image',
						'type'       => 'switcher',
						'title'      => esc_html__( 'Show Featured Image', 'eazydocs' ),
						'subtitle'   => esc_html__( 'Include the article\'s featured image in the email.', 'eazydocs' ),
						'text_on'    => esc_html__( 'Show', 'eazydocs' ),
						'text_off'   => esc_html__( 'Hide', 'eazydocs' ),
						'default'    => true,
						'text_width' => 80
					),
					array(
						'id'         => 'email_show_related',
						'type'       => 'switcher',
						'title'      => esc_html__( 'Show Related Articles', 'eazydocs' ),
						'subtitle'   => esc_html__( 'Suggest other articles from the same documentation.', 'eazydocs' ),
						'text_on'    => esc_html__( 'Show', 'eazydocs' ),
						'text_off'   => esc_html__( 'Hide', 'eazydocs' ),
						'default'    => true,
						'text_width' => 80
					),
					
					array(
						'type'    => 'subheading',
						'content' => esc_html__( 'Social Links (Optional)', 'eazydocs' ),
					),
					array(
						'id'         => 'social_twitter',
						'type'       => 'text',
						'title'      => esc_html__( 'Twitter/X URL', 'eazydocs' ),
						'placeholder'=> 'https://twitter.com/yourhandle',
					),
					array(
						'id'         => 'social_facebook',
						'type'       => 'text',
						'title'      => esc_html__( 'Facebook URL', 'eazydocs' ),
						'placeholder'=> 'https://facebook.com/yourpage',
					),
					array(
						'id'         => 'social_linkedin',
						'type'       => 'text',
						'title'      => esc_html__( 'LinkedIn URL', 'eazydocs' ),
						'placeholder'=> 'https://linkedin.com/company/yourcompany',
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