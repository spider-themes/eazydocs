<?php

CSF::createSection( $prefix, array(
	'id'     => 'design_fields',
	'title'  => esc_html__( 'Customizer', 'eazydocs' ),
	'icon'   => 'fas fa-plus-circle',
	'fields' => [
		array(
			'id'         => 'customizer_visibility',
			'type'       => 'switcher',
			'title'      => esc_html__( 'Options Visibility on Customizer', 'eazydocs' ),
			'text_on'    => esc_html__( 'Enabled', 'eazydocs' ),
			'text_off'   => esc_html__( 'Disabled', 'eazydocs' ),
			'default'	 => true,
			'text_width' => 100
		),
		array(
			'type'     => 'callback',
			'function' => 'customizer_visibility_callback',
			'dependency' => array(
				array( 'customizer_visibility', '==', true ),
			)
		)
	]
) );