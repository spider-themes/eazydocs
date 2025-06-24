<?php
$is_widget_sidebar = ezd_get_opt( 'is_widget_sidebar' );

// Register Widget areas
if ( $is_widget_sidebar == '1' ) {
    add_action('widgets_init', function () {
        register_sidebar(array(
            'name' => esc_html__( 'Doc Right Sidebar', 'eazydocs' ),
            'description' => esc_html__('Add widgets here for the Right Sidebar of the Doc pages', 'eazydocs'),
            'id' => 'doc_sidebar',
            'before_widget' => '<div id="%1$s" class="widget sidebar_widget %2$s">',
            'after_widget' => '</div>',
            'before_title' => '<h3 class="title">',
            'after_title' => '</h3>'
        ));
    });
}