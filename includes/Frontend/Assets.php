<?php
namespace EazyDocs\Frontend;

/**
 * Cannot access directly.
 */
if (!defined('ABSPATH')) {
	exit;
}

class Assets
{
	/**
	 * Assets constructor.
	 **/
	public function __construct()
	{
		add_action('wp_enqueue_scripts', [__CLASS__, 'enqueue_scripts']);
		add_action('wp_enqueue_scripts', [$this, 'enqueue_scripts_after'], 999);
	}

	/**
	 * Scripts enqueue
	 */
	public static function enqueue_scripts()
	{

		wp_enqueue_script('jquery');

		// Register
		wp_register_script('mixitup', EZD_VEND . 'mixitup/mixitup.min.js', ['jquery'], '2.1.11', true);
		wp_register_script('anchor', EZD_ASSETS . 'js/frontend/anchor.js', ['jquery'], '5.1.3', true);
		wp_register_script('scrollspy', EZD_ASSETS . 'js/frontend/scrollspy-gumshoe.js', ['jquery'], '5.1.2', true);
		wp_register_script('eazydocs-el-widgets', EZD_ASSETS . 'js/frontend/elementor-widgets.js', [], EZD_VERSION, true);

		wp_register_style('elegant-icon', EZD_ASSETS . 'vendors/elegant-icon/style.css', [], EZD_VERSION);
		wp_register_style('ezd-docs-widgets', EZD_STYLES . 'ezd-docs-widgets.css', [], EZD_VERSION);

		$dynamic_cssd = ":root { --ezd_brand_color: " . ezd_get_opt('brand_color') . "; }";
		wp_add_inline_style('eazydocs-blocks', $dynamic_cssd);

		if (ezd_has_shortcode(['ezd_login_form', 'reference']) || has_ezd_mark_text_class()) {
			wp_enqueue_style('eazydocs-shortcodes', EZD_STYLES . 'shortcodes.css', [], EZD_VERSION);
		}

		if (ezd_has_shortcode(['eazydocs'])) {
			wp_enqueue_style('ezd-docs-widgets');
		}

		if (ezd_frontend_pages()) {
			// Scripts
			wp_enqueue_script('printThis', EZD_ASSETS . 'js/frontend/printThis.js', [], EZD_VERSION, true);

			wp_enqueue_script('anchor');
			wp_enqueue_script('scrollspy');
			wp_enqueue_script('bootstrap-toc-js', EZD_ASSETS . 'js/frontend/bootstrap-toc.min.js', ['jquery'], '1.0.1', true);
			wp_enqueue_script('eazydocs-single', EZD_ASSETS . 'js/frontend/docs-single.js', ['jquery'], EZD_VERSION, true);
			wp_register_script('eazydocs-onepage', EZD_ASSETS . 'js/frontend/onepage.js', ['jquery'], EZD_VERSION, true);

			$is_dark_switcher = ezd_unlock_themes('docy', 'docly') ? ezd_get_opt('is_dark_switcher') : false;

			if ('1' === $is_dark_switcher && is_singular(['docs', 'onepage-docs'])) {
				wp_enqueue_style('eazydocs-dark-mode', EZD_STYLES . 'frontend-dark-mode.css', [], EZD_VERSION);
			}

			wp_enqueue_style('eazydocs-frontend', EZD_STYLES . 'frontend.css', [], EZD_VERSION);
		}

		if (is_rtl()) {
			if (ezd_frontend_pages()) {
				wp_enqueue_style('eazydocs-rtl', EZD_STYLES . 'rtl.css', ['eazydocs-frontend'], EZD_VERSION);
			} else {
				wp_enqueue_style('eazydocs-rtl', EZD_STYLES . 'rtl.css', [], EZD_VERSION);
			}
		}

		// Enqueue on onepage doc
		if (is_singular('onepage-docs') || is_page_template('page-onepage.php')) {
			wp_enqueue_style('ezd-onepage', EZD_STYLES . 'onepage.css', [], EZD_VERSION);
		}

		// Localize the script with new data
		$ajax_url = admin_url('admin-ajax.php');
		$wpml_current_language = apply_filters('wpml_current_language', null);

		if (!empty($wpml_current_language)) {
			$ajax_url = add_query_arg('wpml_lang', $wpml_current_language, $ajax_url);
		}

		$elementor_docs = [];
		if (class_exists('\Elementor\Plugin')) {
			$elementor_docs = get_posts(
				[
					'post_type' => 'docs',
					'post_status' => 'publish',
					'numberposts' => -1,
					'fields' => 'ids',
					'meta_key' => '_elementor_edit_mode',
					'meta_value' => 'builder',
				]
			);
		}

		wp_localize_script(
			'jquery',
			'eazydocs_local_object',
			[
				'ajaxurl' => $ajax_url,
				'EZD_STYLES' => EZD_STYLES,
				'nonce' => wp_create_nonce('eazydocs-ajax'),
				'is_doc_ajax' => ezd_is_premium() ? ezd_get_opt('is_doc_ajax') : false,
				'ezd_layout_container' => ezd_container(),
				'ezd_search_submit' => ezd_get_opt('is_search_submit', true),
				'ezd_dark_switcher' => ezd_get_opt('is_dark_switcher', true),
				'elementor_docs' => $elementor_docs,
			]
		);

		wp_register_style('ezd-frontend-global', EZD_STYLES . 'frontend-global.css', [], EZD_VERSION);

		// Global Scripts
		wp_register_style('elegant-icon-vend', EZD_VEND . 'elegant-icon/style.css', [], EZD_VERSION);

		if (self::global_scope()) {
			wp_enqueue_style('elegant-icon-vend');
			wp_enqueue_style('ezd-frontend-global');

			// Note: Dynamic RGBA brand colors are now handled via CSS custom properties in SCSS
			// Using color-mix() function with --ezd_brand_color variable (see _variables.scss)
			// No inline PHP-generated CSS needed anymore

			wp_enqueue_script('eazydocs-global', EZD_ASSETS . 'js/frontend/global.js', ['jquery'], EZD_VERSION, true);
		}
	}

	/**
	 * Scripts enqueue after.
	 *
	 * @return void
	 */
	public function enqueue_scripts_after()
	{
		if (is_singular('docs') && (ezd_is_premium() ? '1' === ezd_get_opt('is_doc_ajax') : false) && ezd_unlock_themes('docy', 'docly', 'ama')) {
			wp_enqueue_script('eazydocs-ajax-loading', EZD_ASSETS . 'js/frontend/ajax.js', ['jquery'], EZD_VERSION, true);
		}
		// if gutenberg block theme installed and single docs
		if (function_exists('block_header_area') && is_singular('docs')) {
			wp_enqueue_script('eazydocs-block', EZD_ASSETS . 'js/frontend/block.js', ['jquery'], EZD_VERSION, true);
		}
	}

	/**
	 * Check if current page is in global scope.
	 *
	 * @return bool
	 */
	private static function global_scope()
	{
		if (
			has_block('eazydocs/search-banner')
			|| in_array('eazydocs_shortcode', get_body_class())
			|| is_singular('docs')
			|| is_singular('onepage-docs')
			|| is_page_template('page-onepage.php')
		) {
			return true;
		}
	}
}