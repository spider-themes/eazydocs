<?php 
/**
 * Cannot access directly.
 */
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

// OnePage Doc Options
CSF::createSection( $prefix, array(
    'title'     => esc_html__( 'OnePage Doc', 'eazydocs' ),
    'id'        => 'ezd_onepage_doc',
    'icon'      => 'dashicons dashicons-book',
    'fields'    => array(
        
        array(
            'id'          => 'onepage_sidebar',
            'type'        => 'subheading',
            'title'       => esc_html__( 'Sidebar', 'eazydocs' ),
            'subtitle'    => esc_html__( 'Fullscreen layout left sidebar settings', 'eazydocs' )
        ),

        array(
            'id'          => 'heading_color',
            'type'        => 'color',
            'title'       => esc_html__( 'Heading Color', 'eazydocs' ),
            'default'     => '#fff',
            'output'      => '.single-onepage-docs .documentation_area_sticky .doc-title',
            'output_mode' => 'color',
            'class'       => 'eazydocs-pro-notice active-theme-docy active-theme-docly'
        ),

        array(
            'id'          => 'menu_color',
            'type'        => 'color',
            'title'       => esc_html__( 'Menu Color', 'eazydocs' ),
            'default'     => '#fff',
            'output'      => '.single-onepage-docs .documentation_area_sticky .nav-sidebar.one-page-doc-nav-wrap .nav-item:not(.active) a',
            'output_mode' => 'color',
            'class'       => 'eazydocs-pro-notice active-theme-docy active-theme-docly'
        ),

        array(
            'id'          => 'active_bg_color',
            'type'        => 'color',
            'title'       => esc_html__( 'Menu Background', 'eazydocs' ),
            'default'     => '#2A3D4B',
            'output'      => '.fullscreen-layout.onepage_doc_area .nav-sidebar.one-page-doc-nav-wrap .nav-item.active',
            'output_mode' => 'background-color',
            'class'       => 'eazydocs-pro-notice active-theme-docy active-theme-docly'
        ),

        array(
            'id'          => 'bg_color',
            'type'        => 'color',
            'title'       => esc_html__( 'Sidebar Background', 'eazydocs' ),
            'default'     => '#0866ff',
            'output'      => '.documentation_area_sticky .one-page-docs-sidebar-wrap',
            'output_mode' => 'background-color',
            'class'       => 'eazydocs-pro-notice active-theme-docy active-theme-docly'
        )
    )
) );