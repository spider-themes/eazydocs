<?php
namespace eazyDocs\Frontend;

/**
 * Shortcode.
 */
class Shortcode {

	/**
	 * Initialize the class
	 */
	public function __construct() {
		add_shortcode( 'eazydocs', [ $this, 'shortcode' ] );
	}

	/**
	 * Shortcode handler.
	 *
	 * @param array  $atts
	 * @param string $content
	 *
	 * @return string
	 */
	public function shortcode( $atts, $content = null ) {
		Assets::enqueue_scripts();

		ob_start();
		self::eazydocs( $atts );
		$content = ob_get_clean();

		return $content;
	}

	/**
	 * Generic function for displaying docs.
	 *
	 * @param array $args
	 *
	 * @return void
	 */
	public static function eazydocs( $args = [] ) {

		// call the template
		eazydocs_get_template_part('shortcode.php');
	}
}