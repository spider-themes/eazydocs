<?php
/**
 * Analytics Email Reports
 * Configure automated email reports for documentation performance.
 */
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}


// Create a section
CSF::createSection( $prefix, array(
  'id'     => 'reporting_opt',
  'title'  => __( 'Email Reports', 'eazydocs' ),
  'icon'   => 'dashicons dashicons-email-alt',
  'fields' => array(

    array(
      'id'      => 'reporting_enabled',
      'type'    => 'switcher',
      'title'   => __( 'Automated Reports', 'eazydocs' ),
      'subtitle'   => __( 'Receive periodic email summaries of your documentation analytics and performance metrics.', 'eazydocs' ),
	  'class'      => 'eazydocs-promax-notice',
      'default' => false
    ),

    array(
      'id'      => 'reporting_frequency',
      'type'    => 'select',
      'title'   => __( 'Report Frequency', 'eazydocs' ),
      'subtitle'   => __( 'Choose how often you want to receive analytics reports.', 'eazydocs' ),
	  'dependency' => array(
			array( 'reporting_enabled', '==', 'true' ),
	   ),
      'options' => array(
        'daily'   => __( 'Daily', 'eazydocs' ),
        'weekly'  => __( 'Weekly', 'eazydocs' ),
        'monthly' => __( 'Monthly', 'eazydocs' ),
      ),
	  'class'      => 'eazydocs-promax-notice',
      'default' => 'daily',
    ),
	
	array(
		'id'		  => 'reporting_day',
		'type'        => 'select',
		'title'       => __( 'Weekly Report Day', 'eazydocs' ),
		'subtitle'	  => __( 'Select which day of the week to send weekly reports.', 'eazydocs' ),
		'multiple'    => false,
		'dependency' => array(
				array( 'reporting_enabled', '==', 'true' ),
				array( 'reporting_frequency', '==', 'weekly' ),
		),
		'options' => array(
			'sunday'    => __( 'Sunday', 'eazydocs' ),
			'monday'	=> __( 'Monday', 'eazydocs' ),
			'tuesday'	=> __( 'Tuesday', 'eazydocs' ),
			'wednesday'	=> __( 'Wednesday', 'eazydocs' ),
			'thursday'	=> __( 'Thursday', 'eazydocs' ),
			'friday'	=> __( 'Friday', 'eazydocs' ),
			'saturday'	=> __( 'Saturday', 'eazydocs' ),
		),
	    'class'      => 'eazydocs-promax-notice',
		'default' => array( 'monday' )
	),
	
	array(
		'id'          => 'reporting_data',
		'type'        => 'select',
		'title'       => __( 'Report Contents', 'eazydocs' ),
		'subtitle'    => __( 'Select which metrics to include in your reports.', 'eazydocs' ),
		'chosen'      => true,
		'multiple'    => true,
		'placeholder' => __( 'Select metrics...', 'eazydocs' ),
	    'dependency' => array(
			array( 'reporting_enabled', '==', 'true' ),
	     ),
		'options' => array(
			'views'    => __( 'Page Views', 'eazydocs' ),
			'searches'  => __( 'Search Queries', 'eazydocs' ),
			'reactions'    => __( 'User Reactions', 'eazydocs' ),
			'docs'    => __( 'Documentation Stats', 'eazydocs' ),
		),
	    'class'      => 'eazydocs-promax-notice',
		'default' => array( 'views','searches','reactions','docs' )
	),

    array(
      'id'      => 'reporting_email',
      'type'    => 'text',
      'title'   => __( 'Recipient Email', 'eazydocs' ),
      'subtitle'   => __( 'Enter the email address where reports should be sent.', 'eazydocs' ),
      'default' => get_option( 'admin_email' ),
	  'dependency' => array(
		 array( 'reporting_enabled', '==', 'true' ),
	   ),
	  'class'      => 'eazydocs-promax-notice'
    ),

    array(
      'id'      => 'reporting_heading',
      'type'    => 'text',
      'title'   => __( 'Email Subject Line', 'eazydocs' ),
      'subtitle'   => __( 'Customize the subject line for report emails.', 'eazydocs' ),
      'default' => __( 'Documentation Analytics Summary', 'eazydocs' ),
	  'dependency' => array(
		 array( 'reporting_enabled', '==', 'true' ),
	   ),
	  'class'      => 'eazydocs-promax-notice'
    ),

    array(
      'id'      => 'reporting_description',
      'type'    => 'textarea',
      'title'   => __( 'Email Introduction', 'eazydocs' ),
      'subtitle'   => __( 'Add a brief introduction message at the top of report emails.', 'eazydocs' ),
      'default' => __( 'Comprehensive analytics for your website documentation', 'eazydocs' ),
	  'dependency' => array(
		 array( 'reporting_enabled', '==', 'true' ),
	   ),
	   'attributes' => [
		'rows' 		 => '3',
		'style' => 'min-height:unset'
	   ],
	  'class'      => 'eazydocs-promax-notice',
	),

	array(
      	'title'   => __( 'Send Test Report', 'eazydocs' ),
      	'subtitle'   => __( 'Send a sample report to verify your email settings are working correctly.', 'eazydocs' ),
		'id'       => 'reporting_sample',
		'type'     => 'content',
		'content' => '<button class="button button-info ezd-analytics-sample-report">Send Test Email</button>',
	    'class'      => 'eazydocs-promax-notice',
		'dependency' => array(
			array( 'reporting_enabled', '==', 'true' )
		)
	)

  )
) );
