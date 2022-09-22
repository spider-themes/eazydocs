<?php
// Control core classes for avoid errors
if ( class_exists( 'CSF' ) ) {

	//
	// Set a unique slug-like ID
	$prefix = 'onepage_doc_options';

	//
	// Create a metabox
	CSF::createMetabox( $prefix, array(
		'title'     => 'Onepage Docs :: Options',
		'post_type' => 'onepage-docs',
	) );

	//
	// Create a section
	CSF::createSection( $prefix, array(
		'title'  => 'Settings',
		'fields' => array(

			array(
				'id'    => 'onepage-doc-layout',
				'type'  => 'text',
				'title' => 'Doc Layout'
			),
			array(
				'id'    => 'onepage-doc-btn-1',
				'type'  => 'text',
				'title' => 'Button One Text'
			),
			array(
				'id'    => 'onepage-doc-btn-1-url',
				'type'  => 'text',
				'title' => 'Button One URL'
			),
			array(
				'id'    => 'onepage-doc-btn-2',
				'type'  => 'text',
				'title' => 'Button Two Text'
			),
			array(
				'id'    => 'onepage-doc-btn-2-url',
				'type'  => 'text',
				'title' => 'Button Two URL'
			),

			)

		)
	);
}
