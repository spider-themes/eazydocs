<?php 

// Backup Options
CSF::createSection( $prefix, array(
    'title'     => esc_html__( 'Backup', 'eazydocs' ),
    'id'        => 'ezd_backup',
    'icon'      => 'dashicons dashicons-database-export',
    'fields'    => array(
        array(
            'id'        => 'ezd_export_import',
            'type'      => 'backup',
            'title'     => esc_html__('Backup', 'eazydocs'),
        ),
    )
) );