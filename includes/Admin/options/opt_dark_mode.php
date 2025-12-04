<?php
/**
 * Dark Mode Settings
 * Configure dark theme appearance for your documentation.
 */
if (!defined('ABSPATH')) {
    exit;
}


// Dark Mode Fields
CSF::createSection($prefix, array(
    'id' => 'dark_mode',
    'title' => esc_html__('Dark Mode', 'eazydocs'),
    'icon' => 'dashicons dashicons-star-half',
    'fields' => array(
        array(
            'title' => esc_html__('Enable Dark Mode Toggle', 'eazydocs'),
            'subtitle' => esc_html__('Allow visitors to switch between light and dark themes. The toggle button appears on documentation pages.', 'eazydocs'),
            'id' => 'is_dark_switcher',
            'type' => 'switcher',
            'text_on' => esc_html__('Show', 'eazydocs'),
            'text_off' => esc_html__('Hide', 'eazydocs'),
            'text_width' => 72,
            'default' => false,
            'class' => 'eazydocs-pro-notice active-theme-docly'
        ),

        array(
            'title' => esc_html__('Custom Dark Mode Accent', 'eazydocs'),
            'subtitle' => esc_html__('Use a different accent color when dark mode is active. Useful for improving contrast and visibility.', 'eazydocs'),
            'id' => 'is_dark_accent_color',
            'type' => 'switcher',
            'text_on' => esc_html__('Yes', 'eazydocs'),
            'text_off' => esc_html__('No', 'eazydocs'),
            'text_width' => 70,
            'default' => false,
            'class' => 'eazydocs-pro-notice active-theme-docly',
            'dependency' => array('is_dark_switcher', '==', '1'),
        ),

        array(
            'id' => 'ezd_brand_color_dark',
            'type' => 'color',
            'title' => esc_html__('Dark Mode Brand Color', 'eazydocs'),
            'subtitle' => esc_html__('Choose an accent color optimized for dark backgrounds. Lighter colors typically work better.', 'eazydocs'),
            'output' => ':root',
            'output_mode' => '--ezd_brand_color_dark',
            'dependency' => array(
                'is_dark_switcher',
                '==',
                '1',
                'is_dark_accent_color',
                '==',
                '1',
            ),
        ),

    )
));