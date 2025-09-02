<?php

// Create a section
CSF::createSection( $prefix, array(
  'id'     => 'reporting_opt',
  'title'  => __( 'Email Reporting', 'eazydocs-pro' ),
  'icon'   => 'dashicons dashicons-email-alt',
  'fields' => array(

    array(
      'id'      => 'reporting_enabled',
      'type'    => 'switcher',
      'title'   => __( 'Enabled / Disabled', 'eazydocs-pro' ),
      'label'   => __( 'Enable or Disable Email Reporting', 'eazydocs-pro' ),
	  'class'      => 'eazydocs-promax-notice',
      'default' => false
    ),

    array(
      'id'      => 'reporting_frequency',
      'type'    => 'select',
      'title'   => __( 'Reporting Frequency', 'eazydocs-pro' ),
	  'dependency' => array(
			array( 'reporting_enabled', '==', 'true' ),
	   ),
      'options' => array(
        'daily'   => __( 'Once Daily', 'eazydocs-pro' ),
        'weekly'  => __( 'Once Weekly', 'eazydocs-pro' ),
        'monthly' => __( 'Once Monthly', 'eazydocs-pro' ),
      ),
	  'class'      => 'eazydocs-promax-notice',
      'default' => 'daily',
    ),
	
	array(
		'id'		  => 'reporting_day',
		'type'        => 'select',
		'title'       => __( 'Reporting Day', 'eazydocs-pro' ),
		'desc'	  => __( 'This is only applicable for the "Weekly" report', 'eazydocs-pro' ),
		'multiple'    => false,
		'dependency' => array(
				array( 'reporting_enabled', '==', 'true' ),
				array( 'reporting_frequency', '==', 'weekly' ),
		),
		'options' => array(
			'sunday'    => __( 'Sunday', 'eazydocs-pro' ),
			'monday'	=> __( 'Monday', 'eazydocs-pro' ),
			'tuesday'	=> __( 'Tuesday', 'eazydocs-pro' ),
			'wednesday'	=> __( 'Wednesday', 'eazydocs-pro' ),
			'thursday'	=> __( 'Thursday', 'eazydocs-pro' ),
			'friday'	=> __( 'Friday', 'eazydocs-pro' ),
			'saturday'	=> __( 'Saturday', 'eazydocs-pro' ),
		),
	    'class'      => 'eazydocs-promax-notice',
		'default' => array( 'monday' )
	),
	
	array(
		'id'          => 'reporting_data',
		'type'        => 'select',
		'title'       => __( 'Select Reporting Data', 'eazydocs-pro' ),
		'chosen'      => true,
		'multiple'    => true,
		'placeholder' => __( 'Select an option', 'eazydocs-pro' ),
	    'dependency' => array(
			array( 'reporting_enabled', '==', 'true' ),
	     ),
		'options' => array(
			'views'    => __( 'Views', 'eazydocs-pro' ),
			'searches'  => __( 'Searches', 'eazydocs-pro' ),
			'reactions'    => __( 'Reactions', 'eazydocs-pro' ),
			'docs'    => __( 'Docs', 'eazydocs-pro' ),
		),
	    'class'      => 'eazydocs-promax-notice',
		'default' => array( 'views','searches','reactions','docs' )
	),

    array(
      'id'      => 'reporting_email',
      'type'    => 'text',
      'title'   => __( 'Reporting Email', 'eazydocs-pro' ),
      'default' => 'delweratjk@gmail.com',
	  'dependency' => array(
		 array( 'reporting_enabled', '==', 'true' ),
	   ),
	  'class'      => 'eazydocs-promax-notice'
    ),

    array(
      'id'      => 'reporting_heading',
      'type'    => 'text',
      'title'   => __( 'Reporting Email Subject', 'eazydocs-pro' ),
      'default' => __( 'Documentation Analytics Summary', 'eazydocs-pro' ),
	  'dependency' => array(
		 array( 'reporting_enabled', '==', 'true' ),
	   ),
	  'class'      => 'eazydocs-promax-notice'
    ),

    array(
      'id'      => 'reporting_description',
      'type'    => 'textarea',
      'title'   => __( 'Reporting Email Description', 'eazydocs-pro' ),
      'default' => __( 'Comprehensive analytics for your website documentation', 'eazydocs-pro' ),
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
      	'title'   => __( 'Reporting Test', 'eazydocs-pro' ),
		'id'       => 'reporting_sample',
		'type'     => 'content',
		'content' => '<button class="button button-info ezd-analytics-sample-report">Test Report</button>',
	    'class'      => 'eazydocs-promax-notice'
	)

  )
) );
