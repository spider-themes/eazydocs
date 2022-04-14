<?php
namespace eazyDocs\Frontend;

class Frontend {
	public function __construct() {
		add_filter( 'single_template', [ $this, 'template_loader' ], 20, 99 );
		add_filter( 'body_class', [ $this, 'eazydocs_body_class' ] );
	}

	/**
	 * Returns template file
	 *
	 * @since 1.0.0
	 */
	public function template_loader( $template ) {
		$file = '';

		if ( is_single() && get_post_type() == 'docs' ) {
			$single_template = 'single-docs.php';
			// Check if a custom template exists in the theme folder, if not, load the plugin template file
			if ( $theme_file = locate_template( array( 'eazydocs/' . $single_template ) ) ) {
				$file = $theme_file;
			} else {
				$file = EAZYDOCS_PATH . '/templates' .'//'. $single_template;
			}
		}
		return apply_filters( 'eazydocs_template_' . $template, $file );
	}

	public function eazydocs_body_class( $classes ) {
		if(is_singular('docs')) {
			$classes[] = '"data-spy="scroll" data-target="#navbar-example3"';
		}
		return $classes;
	}
}