<?php 
/**
 * Backup & Restore Settings
 * Export and import your EazyDocs configuration.
 */
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}


// Backup Options
CSF::createSection( $prefix, array(
    'title'     => esc_html__( 'Backup & Restore', 'eazydocs' ),
    'id'        => 'ezd_backup',
    'icon'      => 'dashicons dashicons-database-export',
    'fields'    => array(
        array(
            'id'        => 'ezd_export_import',
            'type'      => 'backup',
            'title'     => esc_html__( 'Settings Backup', 'eazydocs' ),
            'subtitle'  => esc_html__( 'Export your current settings to save a backup, or import previously saved settings to restore your configuration.', 'eazydocs' ),
        ),
    )
) );