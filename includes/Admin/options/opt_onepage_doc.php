<?php 
/**
 * One-Page Documentation Settings
 * Configure the single-page documentation layout and styling.
 */
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

// OnePage Doc Options
CSF::createSection( $prefix, array(
    'title'     => esc_html__( 'One-Page Layout', 'eazydocs' ),
    'id'        => 'ezd_onepage_doc',
    'icon'      => 'dashicons dashicons-book',
    'fields'    => array(
        
        array(
            'id'          => 'onepage_sidebar',
            'type'        => 'subheading',
            'title'       => esc_html__( 'Navigation Sidebar', 'eazydocs' ),
            'subtitle'    => esc_html__( 'Customize the fullscreen layout navigation sidebar appearance.', 'eazydocs' )
        ),

        array(
            'id'          => 'heading_color',
            'type'        => 'color',
            'title'       => esc_html__( 'Title Color', 'eazydocs' ),
            'subtitle'    => esc_html__( 'Color for the documentation title in the sidebar.', 'eazydocs' ),
            'default'     => '#fff',
            'output'      => '.single-onepage-docs .documentation_area_sticky .doc-title',
            'output_mode' => 'color',
            'class'       => 'eazydocs-pro-notice active-theme-docy active-theme-docly'
        ),

        array(
            'id'          => 'menu_color',
            'type'        => 'color',
            'title'       => esc_html__( 'Menu Link Color', 'eazydocs' ),
            'subtitle'    => esc_html__( 'Color for navigation menu items in their default state.', 'eazydocs' ),
            'default'     => '#fff',
            'output'      => '.single-onepage-docs .documentation_area_sticky .nav-sidebar.one-page-doc-nav-wrap .nav-item:not(.active) a',
            'output_mode' => 'color',
            'class'       => 'eazydocs-pro-notice active-theme-docy active-theme-docly'
        ),

        array(
            'id'          => 'active_bg_color',
            'type'        => 'color',
            'title'       => esc_html__( 'Active Menu Highlight', 'eazydocs' ),
            'subtitle'    => esc_html__( 'Background color for the currently active navigation item.', 'eazydocs' ),
            'default'     => '#2A3D4B',
            'output'      => '.fullscreen-layout.onepage_doc_area .nav-sidebar.one-page-doc-nav-wrap .nav-item.active',
            'output_mode' => 'background-color',
            'class'       => 'eazydocs-pro-notice active-theme-docy active-theme-docly'
        ),

        array(
            'id'          => 'bg_color',
            'type'        => 'color',
            'title'       => esc_html__( 'Sidebar Background', 'eazydocs' ),
            'subtitle'    => esc_html__( 'Background color for the entire navigation sidebar.', 'eazydocs' ),
            'default'     => '#0866ff',
            'output'      => '.documentation_area_sticky .one-page-docs-sidebar-wrap',
            'output_mode' => 'background-color',
            'class'       => 'eazydocs-pro-notice active-theme-docy active-theme-docly'
        )
    )
) );