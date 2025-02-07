<?php
if ( class_exists( 'CSF' ) ) {
    $prefix = 'eazydocs_meta';
    // Register a custom meta box for the Docs post type.
    CSF::createMetabox( $prefix, array(
        'title'     => esc_html__( 'Docs :: Options', 'eazydocs' ),
        'post_type' => 'docs',
        'priority'  => 'default',
    ) );

    // Create the fields conditionally.
    CSF::createSection( $prefix, array(
        'id'     => 'ezd_footnote_options',
        'fields' => array(
            array(
                'id'      => 'footnote_column',
                'type'    => 'number',
                'title'   => esc_html__( 'Footnote Column', 'eazydocs' ),
                'default' => 3,
                'min'     => 1,
                'max'     => 4,
			    'class'   => 'eazydocs-pro-notice active-theme-docy active-theme-docly active-theme-ama'
            )
        ),
    ) );
}