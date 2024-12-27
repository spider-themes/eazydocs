<?php 
// Control core classes for avoid errors
if( class_exists( 'CSF' ) ) {

  //
  // Set a unique slug-like ID
  $prefix = 'ezd_badge_settings';

  //
  // Create taxonomy options
  CSF::createTaxonomyOptions( $prefix, array(
    'taxonomy'  => 'doc_badge',
    'data_type' => 'serialize', // The type of the database save options. `serialize` or `unserialize`
  ) );

  //
  // Create a section
  CSF::createSection( $prefix, array(
    'fields' => array(

      array(
        'id'          => 'ezd-badge-color',
        'type'        => 'color',
        'title'       => esc_html__( 'Badge Text Color', 'eazydocs' ),
      ),

      array(
        'id'          => 'ezd-badge-bg',
        'type'        => 'color',
        'title'       => esc_html__( 'Badge Background Color', 'eazydocs' ),
      ),

    )
  ) );

}
 