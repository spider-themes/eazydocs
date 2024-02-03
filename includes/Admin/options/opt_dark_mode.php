<?php

// Dark Mode Fields
CSF::createSection( $prefix, array(
    'id'     => 'dark_mode',
    'title'  => esc_html__( 'Dark Mode', 'eazydocs' ),
    'icon'   => 'fas fa-adjust',
    'fields' => array(

        array(
            'title'         => esc_html__( 'Dark Mode Switcher', 'eazydocs' ),
            'subtitle'      => esc_html__( 'By show/hiding the Dark Mode Switcher, you are enable/disabling the Dark mode feature on the Doc single page.', 'eazydocs' ),
            'id'            => 'is_dark_switcher',
            'type'          => 'switcher',
            'text_on'       => esc_html__( 'Show', 'eazydocs' ),
            'text_off'      => esc_html__( 'Hide', 'eazydocs' ),
            'text_width'    => 72,
            'default'       => false,
            'class'         => 'eazydocs-pro-notice active-theme-docly'
        ),

        array(
            'title'         => esc_html__( 'Accent color', 'eazydocs' ),
            'subtitle'      => esc_html__( 'Different accent color on Dark Mode.', 'eazydocs' ),
            'id'            => 'is_dark_accent_color',
            'type'          => 'switcher',
            'text_on'       => esc_html__( 'Yes', 'eazydocs' ),
            'text_off'      => esc_html__( 'No', 'eazydocs' ),
            'text_width'    => 92,
            'default'       => false,
            'class'         => 'eazydocs-pro-notice active-theme-docly',
            'dependency'    => array( 'is_dark_switcher', '==', '1' ),
        ),

        array(
            'id'            => 'ezd_brand_color_dark',
            'type'          => 'color',
            'title'         => esc_html__( 'Brand Color on Dark Mode', 'eazydocs' ),
            'subtitle'      => esc_html__( 'Accent Color for dark mode on Frontend. You can choose a different color the Dark mode from here.', 'eazydocs' ),
            'output'        => ':root',
            'output_mode'   => '--ezd_brand_color_dark',
            'dependency'    => array(
				'is_dark_switcher', '==', '1',
				'is_dark_accent_color', '==', '1',
            ),
        ),

    )
) );