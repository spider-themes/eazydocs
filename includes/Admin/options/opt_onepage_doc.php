<?php
/**
 * One-Page Documentation Settings
 * Configure the single-page documentation layout and styling.
 *
 * Field groups:
 *  1. Layout      - functional defaults (layout + content width), available on free.
 *  2. Sidebar     - navigation sidebar colors (Pro, docy/docly).
 *  3. Content     - reading column colors (Pro, docy/docly).
 *  4. Right Side  - right sidebar/tools colors (Pro, docy/docly).
 *
 * Color outputs are written for BOTH One-Page templates (classic + fullscreen)
 * by targeting the shared wrapper classes, so a change here is visible whatever
 * layout a document uses.
 */
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

// Shared gating class for the cosmetic (color) fields, mirroring the rest of
// the Appearance sections: Pro feature, applied on the docy/docly themes.
$ezd_onepage_pro = 'eazydocs-pro-notice active-theme-docy active-theme-docly';

// OnePage Doc Options
CSF::createSection( $prefix, array(
    'title'     => esc_html__( 'One-Page Layout', 'eazydocs' ),
    'id'        => 'ezd_onepage_doc',
    'parent'    => 'ezd_appearance',
    'icon'      => 'dashicons dashicons-book',
    'fields'    => array(

        // ── Layout ──────────────────────────────────────────────
        array(
            'id'       => 'onepage_layout_heading',
            'type'     => 'subheading',
            'title'    => esc_html__( 'Layout', 'eazydocs' ),
            'subtitle' => esc_html__( 'One-Page docs render an entire documentation tree on a single scrollable page. Pick the default layout below — you can still override it per document from the document editor.', 'eazydocs' ),
        ),

        array(
            'id'       => 'onepage_default_layout',
            'type'     => 'image_select',
            'title'    => esc_html__( 'Default Layout', 'eazydocs' ),
            'subtitle' => esc_html__( 'Used when a document has no layout chosen in its editor.', 'eazydocs' ),
            'options'  => array(
                'classic-onepage-layout' => EZD_IMG . 'customizer/both_sidebar.jpg',
                'fullscreen-layout'      => EZD_IMG . 'customizer/sidebar_left.jpg',
            ),
            'default'  => 'classic-onepage-layout',
        ),

        array(
            'id'       => 'onepage_content_width',
            'type'     => 'select',
            'title'    => esc_html__( 'Content Width', 'eazydocs' ),
            'subtitle' => esc_html__( 'Constrain the reading column to a centered, boxed width or let it stretch full width.', 'eazydocs' ),
            'options'  => array(
                'full-width' => esc_html__( 'Full Width', 'eazydocs' ),
                'boxed'      => esc_html__( 'Boxed (Centered)', 'eazydocs' ),
            ),
            'default'  => 'full-width',
        ),

        // ── Page Banner ─────────────────────────────────────────
        array(
            'id'          => 'onepage_banner_heading',
            'type'        => 'subheading',
            'title'       => esc_html__( 'Page Banner', 'eazydocs' ),
            'subtitle'    => esc_html__( 'A hero shown at the top of the page with the document title, excerpt and at-a-glance stat metrics.', 'eazydocs' ),
        ),

        array(
            'id'       => 'onepage_banner',
            'type'     => 'switcher',
            'title'    => esc_html__( 'Show Page Banner', 'eazydocs' ),
            'subtitle' => esc_html__( 'Display the title/excerpt hero banner at the top of One-Page docs.', 'eazydocs' ),
            'default'  => true,
            'text_on'  => esc_html__( 'On', 'eazydocs' ),
            'text_off' => esc_html__( 'Off', 'eazydocs' ),
        ),

        array(
            'id'         => 'onepage_banner_metrics',
            'type'       => 'checkbox',
            'title'      => esc_html__( 'Banner Stat Metrics', 'eazydocs' ),
            'subtitle'   => esc_html__( 'Pick which stat metric cards appear on the banner. Each is only shown when it has data.', 'eazydocs' ),
            'options'    => array(
                'count'        => esc_html__( 'Docs count', 'eazydocs' ),
                'modified'     => esc_html__( 'Last updated date', 'eazydocs' ),
                'authors'      => esc_html__( 'Authors', 'eazydocs' ),
                'reading_time' => esc_html__( 'Estimated reading time', 'eazydocs' ),
            ),
            'default'    => array( 'count', 'modified', 'authors', 'reading_time' ),
            'dependency' => array( 'onepage_banner', '==', 'true' ),
        ),

        array(
            'id'         => 'onepage_banner_badge',
            'type'       => 'text',
            'title'      => esc_html__( 'Banner Badge Label', 'eazydocs' ),
            'subtitle'   => esc_html__( 'Small pill shown above the title. Leave empty to hide it.', 'eazydocs' ),
            'default'    => esc_html__( 'Documentation', 'eazydocs' ),
            'dependency' => array( 'onepage_banner', '==', 'true' ),
        ),

        array(
            'id'          => 'onepage_banner_accent',
            'type'        => 'color',
            'title'       => esc_html__( 'Banner Gradient Start', 'eazydocs' ),
            'subtitle'    => esc_html__( 'Start color of the banner background gradient. Defaults to your brand color when left empty.', 'eazydocs' ),
            'output'      => '.single-onepage-docs .ezd-onepage-banner',
            'output_mode' => '--ezd-banner-accent',
            'class'       => $ezd_onepage_pro,
            'dependency'  => array( 'onepage_banner', '==', 'true' ),
        ),

        array(
            'id'          => 'onepage_banner_gradient_end',
            'type'        => 'color',
            'title'       => esc_html__( 'Banner Gradient End', 'eazydocs' ),
            'subtitle'    => esc_html__( 'End color the banner background gradient fades into.', 'eazydocs' ),
            'output'      => '.single-onepage-docs .ezd-onepage-banner',
            'output_mode' => '--ezd-banner-gradient-end',
            'class'       => $ezd_onepage_pro,
            'dependency'  => array( 'onepage_banner', '==', 'true' ),
        ),

        array(
            'id'          => 'onepage_banner_title_color',
            'type'        => 'color',
            'title'       => esc_html__( 'Banner Title Color', 'eazydocs' ),
            'subtitle'    => esc_html__( 'Color for the banner title text.', 'eazydocs' ),
            'output'      => '.single-onepage-docs .ezd-onepage-banner .ezd-onepage-banner-title',
            'output_mode' => 'color',
            'class'       => $ezd_onepage_pro,
            'dependency'  => array( 'onepage_banner', '==', 'true' ),
        ),

        array(
            'id'          => 'onepage_banner_text_color',
            'type'        => 'color',
            'title'       => esc_html__( 'Banner Text Color', 'eazydocs' ),
            'subtitle'    => esc_html__( 'Color for the banner excerpt/description text.', 'eazydocs' ),
            'output'      => '.single-onepage-docs .ezd-onepage-banner .ezd-onepage-banner-excerpt',
            'output_mode' => 'color',
            'class'       => $ezd_onepage_pro,
            'dependency'  => array( 'onepage_banner', '==', 'true' ),
        ),

        // ── Navigation Sidebar ──────────────────────────────────
        array(
            'id'          => 'onepage_sidebar',
            'type'        => 'subheading',
            'title'       => esc_html__( 'Navigation Sidebar', 'eazydocs' ),
            'subtitle'    => esc_html__( 'Style the sidebar that lists the document sections.', 'eazydocs' ),
        ),

        array(
            'id'          => 'bg_color',
            'type'        => 'color',
            'title'       => esc_html__( 'Sidebar Background', 'eazydocs' ),
            'subtitle'    => esc_html__( 'Background color for the entire navigation sidebar.', 'eazydocs' ),
            'output'      => '.single-onepage-docs .one-page-docs-sidebar-wrap',
            'output_mode' => 'background-color',
            'class'       => $ezd_onepage_pro,
        ),

        array(
            'id'          => 'heading_color',
            'type'        => 'color',
            'title'       => esc_html__( 'Title Color', 'eazydocs' ),
            'subtitle'    => esc_html__( 'Color for the documentation title in the sidebar.', 'eazydocs' ),
            'output'      => '.single-onepage-docs .one-page-docs-sidebar-wrap .doc-title, .single-onepage-docs .one-page-docs-sidebar-wrap .nav_title',
            'output_mode' => 'color',
            'class'       => $ezd_onepage_pro,
        ),

        array(
            'id'          => 'menu_color',
            'type'        => 'color',
            'title'       => esc_html__( 'Menu Link Color', 'eazydocs' ),
            'subtitle'    => esc_html__( 'Color for navigation menu items in their default state.', 'eazydocs' ),
            'output'      => '.single-onepage-docs .one-page-doc-nav-wrap .nav-item:not(.active) a',
            'output_mode' => 'color',
            'class'       => $ezd_onepage_pro,
        ),

        array(
            'id'          => 'menu_hover_color',
            'type'        => 'color',
            'title'       => esc_html__( 'Menu Link Hover', 'eazydocs' ),
            'subtitle'    => esc_html__( 'Color for navigation menu items on hover.', 'eazydocs' ),
            'output'      => '.single-onepage-docs .one-page-doc-nav-wrap .nav-item:not(.active) a:hover',
            'output_mode' => 'color',
            'class'       => $ezd_onepage_pro,
        ),

        array(
            'id'          => 'active_bg_color',
            'type'        => 'color',
            'title'       => esc_html__( 'Active Item Background', 'eazydocs' ),
            'subtitle'    => esc_html__( 'Background color for the currently active navigation item.', 'eazydocs' ),
            'output'      => '.single-onepage-docs .one-page-doc-nav-wrap .nav-item.active',
            'output_mode' => 'background-color',
            'class'       => $ezd_onepage_pro,
        ),

        array(
            'id'          => 'active_text_color',
            'type'        => 'color',
            'title'       => esc_html__( 'Active Item Text', 'eazydocs' ),
            'subtitle'    => esc_html__( 'Text color for the active navigation item. Keep it readable against the highlight color above.', 'eazydocs' ),
            'output'      => '.single-onepage-docs .one-page-doc-nav-wrap .nav-item.active a',
            'output_mode' => 'color',
            'class'       => $ezd_onepage_pro,
        ),

        // ── Content Area ────────────────────────────────────────
        array(
            'id'          => 'onepage_content_heading',
            'type'        => 'subheading',
            'title'       => esc_html__( 'Content Area', 'eazydocs' ),
            'subtitle'    => esc_html__( 'Style the main reading column.', 'eazydocs' ),
        ),

        array(
            'id'          => 'content_bg_color',
            'type'        => 'color',
            'title'       => esc_html__( 'Content Background', 'eazydocs' ),
            'subtitle'    => esc_html__( 'Background color for the main content column.', 'eazydocs' ),
            'default'     => '',
            'output'      => '.single-onepage-docs .middle-content',
            'output_mode' => 'background-color',
            'class'       => $ezd_onepage_pro,
        ),

        array(
            'id'          => 'sec_number_color',
            'type'        => 'color',
            'title'       => esc_html__( 'Section Number Color', 'eazydocs' ),
            'subtitle'    => esc_html__( 'Color for the auto-numbered section prefixes (e.g. 1.2.3). Applies to the Fullscreen layout.', 'eazydocs' ),
            'default'     => '',
            'output'      => '.single-onepage-docs .ezd-onepage-sec-no',
            'output_mode' => 'color',
            'class'       => $ezd_onepage_pro,
        ),

        // ── Right Sidebar ───────────────────────────────────────
        array(
            'id'          => 'onepage_right_heading',
            'type'        => 'subheading',
            'title'       => esc_html__( 'Right Sidebar', 'eazydocs' ),
            'subtitle'    => esc_html__( 'Style the right column that holds tools and widgets.', 'eazydocs' ),
        ),

        array(
            'id'          => 'right_sidebar_bg',
            'type'        => 'color',
            'title'       => esc_html__( 'Right Sidebar Background', 'eazydocs' ),
            'subtitle'    => esc_html__( 'Background color for the right sidebar.', 'eazydocs' ),
            'default'     => '',
            'output'      => '.single-onepage-docs .one-page-docs-right-sidebar',
            'output_mode' => 'background-color',
            'class'       => $ezd_onepage_pro,
        ),

        array(
            'id'          => 'right_sidebar_link',
            'type'        => 'color',
            'title'       => esc_html__( 'Right Sidebar Links', 'eazydocs' ),
            'subtitle'    => esc_html__( 'Link color inside the right sidebar.', 'eazydocs' ),
            'default'     => '',
            'output'      => '.single-onepage-docs .one-page-docs-right-sidebar a',
            'output_mode' => 'color',
            'class'       => $ezd_onepage_pro,
        ),
    )
) );
