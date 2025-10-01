<?php
/**
 * Cannot access directly.
 */
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}


// Create a section
CSF::createSection( $prefix, array(
  'id'     => 'reporting_opt',
  'title'  => __( 'Email Reporting', 'eazydocs' ),
  'icon'   => 'dashicons dashicons-email-alt',
  'fields' => array(

    array(
      'id'      => 'reporting_enabled',
      'type'    => 'switcher',
      'title'   => __( 'Enabled / Disabled', 'eazydocs' ),
      'label'   => __( 'Enable or Disable Email Reporting', 'eazydocs' ),
	  'class'      => 'eazydocs-promax-notice',
      'default' => false
    ),

    array(
      'id'      => 'reporting_frequency',
      'type'    => 'select',
      'title'   => __( 'Reporting Frequency', 'eazydocs' ),
	  'dependency' => array(
			array( 'reporting_enabled', '==', 'true' ),
	   ),
      'options' => array(
        'daily'   => __( 'Once Daily', 'eazydocs' ),
        'weekly'  => __( 'Once Weekly', 'eazydocs' ),
        'monthly' => __( 'Once Monthly', 'eazydocs' ),
      ),
	  'class'      => 'eazydocs-promax-notice',
      'default' => 'daily',
    ),
	
	array(
		'id'		  => 'reporting_day',
		'type'        => 'select',
		'title'       => __( 'Reporting Day', 'eazydocs' ),
		'desc'	  => __( 'This is only applicable for the "Weekly" report', 'eazydocs' ),
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
		'title'       => __( 'Select Reporting Data', 'eazydocs' ),
		'chosen'      => true,
		'multiple'    => true,
		'placeholder' => __( 'Select an option', 'eazydocs' ),
	    'dependency' => array(
			array( 'reporting_enabled', '==', 'true' ),
	     ),
		'options' => array(
			'views'    => __( 'Views', 'eazydocs' ),
			'searches'  => __( 'Searches', 'eazydocs' ),
			'reactions'    => __( 'Reactions', 'eazydocs' ),
			'docs'    => __( 'Docs', 'eazydocs' ),
		),
	    'class'      => 'eazydocs-promax-notice',
		'default' => array( 'views','searches','reactions','docs' )
	),

    array(
      'id'      => 'reporting_email',
      'type'    => 'text',
      'title'   => __( 'Reporting Email', 'eazydocs' ),
      'default' => 'delweratjk@gmail.com',
	  'dependency' => array(
		 array( 'reporting_enabled', '==', 'true' ),
	   ),
	  'class'      => 'eazydocs-promax-notice'
    ),

    array(
      'id'      => 'reporting_heading',
      'type'    => 'text',
      'title'   => __( 'Reporting Email Subject', 'eazydocs' ),
      'default' => __( 'Documentation Analytics Summary', 'eazydocs' ),
	  'dependency' => array(
		 array( 'reporting_enabled', '==', 'true' ),
	   ),
	  'class'      => 'eazydocs-promax-notice'
    ),

    array(
      'id'      => 'reporting_description',
      'type'    => 'textarea',
      'title'   => __( 'Reporting Email Description', 'eazydocs' ),
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
      	'title'   => __( 'Reporting Test', 'eazydocs' ),
		'id'       => 'reporting_sample',
		'type'     => 'content',
		'content' => '<button class="button button-info ezd-analytics-sample-report">Test Report</button>',
	    'class'      => 'eazydocs-promax-notice',
		'dependency' => array(
			array( 'reporting_enabled', '==', 'true' )
		)
	)

  )
) );
