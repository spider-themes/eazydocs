<?php
/**
 * Shortcode: [ezd-view-docs]
 * 
 * This shortcode is designed to display the details of a single document (Docs) on a single Docs page. 
 * It ensures the document content is displayed correctly, even if the default template for viewing a document is not applied. 
 * This can happen due to theme or plugin restrictions.
 *
 * Usage:
 * Place the shortcode [ezd-view-docs] on a single Docs template to render the document's content.
 */
add_shortcode('ezd-view-docs', function() {  
    // Check if we're on a single Docs page
    if ( is_singular('docs') ) {
        ob_start();

        // Check if the eazylms plugin is active and the document has a depth of 0
        if ( function_exists( 'get_eazylms_courses_depth' ) && get_eazylms_courses_depth( get_the_ID() ) === 0 ) {
            // Try loading the template from the eazylms plugin
            $template = WP_PLUGIN_DIR . '/eazylms/eazydocs/single-docs.php';
            if ( file_exists( $template ) ) {
                load_template( $template, true );
            } else {
                echo esc_html__( 'Template not found in the eazylms plugin.', 'eazydocs' );
            }
        } else {
            // Load the default single-docs template if the condition is not met
            load_template( EAZYDOCS_PATH . '/templates/single-docs.php', true );
        }

        // Return the output buffer content
        return ob_get_clean();
    }

    // Return a message if the shortcode is not used on a single Docs page
    return esc_html__( 'This shortcode is only valid on single Docs pages.', 'eazydocs' );
});