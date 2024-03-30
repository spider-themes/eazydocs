<?php

// Subscriptions
CSF::createSection( $prefix, array(
	'id'     => 'subscriptions_opt',
	'title'  => esc_html__( 'Docs Subscriptions', 'eazydocs' ), 
	'icon'   => 'fas fa-plus-circle',
	'fields' => array(
		array(
			'type'  => 'heading',
			'title' => esc_html__( 'Subscriptions Options', 'eazydocs' )
		),

		array(
			'id'         => 'subscriptions',
			'type'       => 'switcher',
			'title'      => esc_html__( 'Subscribe Feature', 'eazydocs' ),
			'subtitle'   => esc_html__( 'Enable to show the subscription form in the single doc page.', 'eazydocs' ),
			'text_on'    => esc_html__( 'Enabled', 'eazydocs' ),
			'text_off'   => esc_html__( 'Disabled', 'eazydocs' ),
			'default'    => false,
			'class'      => 'eazydocs-promax-notice',
			'text_width' => 95
		),
		
		array(
			'id'            => 'subscriptions_tab',
			'type'          => 'tabbed',
			'title'         => esc_html__( 'Customize', 'eazydocs' ),
			'subtitle'      => esc_html__( 'Customize the subscription form here.', 'eazydocs' ),
			'dependency' 	=> array( 'subscriptions', '==', 'true' ),
			'class'     	=> 'eazydocs-promax-notice',
			'tabs'          => array(
			  array(
				'title'     =>  esc_html__( 'Subscribe', 'eazydocs' ),
				'fields'    => array(
					array(
						'id'         => 'subscriptions_btn',
						'type'       => 'text',
						'title'      => esc_html__( 'Button', 'eazydocs' ),
						'default'    => esc_html__( 'Subscribe for updates', 'eazydocs' )
					),
					
					array(
						'id'         => 'subscriptions_heading',
						'type'       => 'text',
						'title'      => esc_html__( 'Heading', 'eazydocs' ),
						'default'    => esc_html__( 'Subscribe', 'eazydocs' ),
					),
					
					array(
						'id'         => 'subscriptions_name_label',
						'type'       => 'text',
						'title'      => esc_html__( 'Input :: Name', 'eazydocs' ),
						'default'    => esc_html__( 'Name', 'eazydocs' ),
					),
					array(
						'id'         => 'subscriptions_name_placeholder',
						'type'       => 'text',
						'title'      => ' ',
						'default'    => esc_html__( 'Enter your name', 'eazydocs' )
					),

					array(
						'id'         => 'subscriptions_email_label',
						'type'       => 'text',
						'title'      => esc_html__( 'Input :: Email', 'eazydocs' ),
						'default'    => esc_html__( 'Email', 'eazydocs' ),
					),
					array(
						'id'         => 'subscriptions_email_placeholder',
						'type'       => 'text',
						'title'      => ' ',
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
						'title'      => esc_html__( 'Success', 'eazydocs' ),
						'default'    => esc_html__( 'Confirmation email sent successfully!', 'eazydocs' ),
					),
					
					array(
						'id'         => 'subscriptions_email_exist',
						'type'       => 'text',
						'title'      => esc_html__( 'Email exist', 'eazydocs' ),
						'default'    => esc_html__( 'Already email exists.', 'eazydocs' ),
					),
				
					array(
						'id'         => 'subscriptions_special_character',
						'type'       => 'text',
						'title'      => esc_html__( 'Special Character', 'eazydocs' ),
						'default'    => esc_html__( 'Special characters not allowed!', 'eazydocs' ),
					),
					
				)
			  ),
			  array(
				'title'     =>  esc_html__( 'Unsubscribe', 'eazydocs' ),
				'fields'    => array(
				  
					array(
						'id'         => 'unsubscriptions_btn',
						'type'       => 'text',
						'title'      => esc_html__( 'Button', 'eazydocs' ),
						'default'    => esc_html__( 'Unsubscribe', 'eazydocs' ),
					),
					
					array(
						'id'         => 'unsubscriptions_heading',
						'type'       => 'text',
						'title'      => esc_html__( 'Heading', 'eazydocs' ),
						'default'    => esc_html__( 'Unsubscribe', 'eazydocs' ),
					),
					
					array(
						'id'         => 'unsubscriptions_desc',
						'type'       => 'textarea',
						'title'      => esc_html__( 'Description', 'eazydocs' ),
						'default'    => esc_html__( "Are you sure you'd like to stop receiving updates", 'eazydocs' ),
					),
					
					array(
						'id'         => 'unsubscriptions_post_title',
						'type'       => 'checkbox',
						'title'      => esc_html__( 'Post title', 'eazydocs' ),
						'text_on'    => esc_html__( 'Yes', 'eazydocs' ),
						'text_off'   => esc_html__( 'No', 'eazydocs' ),
						'label'	 	 => esc_html__( 'Include post title in the description', 'eazydocs' ),
						'default'    => true,
					),
					
					array(
						'id'         => 'unsubscriptions_submit_btn',
						'type'       => 'text',
						'title'      => esc_html__( 'Submit Button', 'eazydocs' ),
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