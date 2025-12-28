<?php
namespace EazyDocs\Frontend;

/**
 * Cannot access directly.
 */
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

class Assets {
	/**
	 * Assets constructor.
	 **/
	public function __construct() {
		add_action( 'wp_enqueue_scripts', [ $this, 'enqueue_scripts' ] );
		add_action( 'wp_enqueue_scripts', [ $this, 'enqueue_scripts_after' ], 999 );
	}

	/**
	 * Scripts enqueue
	 */
	public static function enqueue_scripts() {
		
		wp_enqueue_script( 'jquery' );

		// Register
		wp_register_script( 'mixitup', EAZYDOCS_VEND.'/mixitup/mixitup.min.js', array( 'jquery' ), '2.1.11', true );
		wp_register_script( 'anchor', EAZYDOCS_ASSETS.'/js/frontend/anchor.js', array( 'jquery' ), '5.1.3', true );
		wp_register_script( 'scrollspy', EAZYDOCS_ASSETS.'/js/frontend/scrollspy-gumshoe.js', array( 'jquery' ), '5.1.2', true );
		wp_register_script( 'eazydocs-el-widgets', EAZYDOCS_ASSETS.'/js/frontend/elementor-widgets.js', array(), EAZYDOCS_VERSION, true );

		wp_register_style( 'elegant-icon', EAZYDOCS_ASSETS.'/vendors/elegant-icon/style.css', array(), EAZYDOCS_VERSION );
		wp_register_style( 'ezd-docs-widgets', EAZYDOCS_ASSETS.'/css/ezd-docs-widgets.css', array(), EAZYDOCS_VERSION );

		$dynamic_cssd = ":root { --ezd_brand_color: " . ezd_get_opt( 'brand_color' ) . "; }";
		wp_add_inline_style( 'eazydocs-blocks', $dynamic_cssd );

		if ( ezd_has_shortcode( ['ezd_login_form', 'reference'] ) || has_ezd_mark_text_class() ) {
			wp_enqueue_style( 'eazydocs-shortcodes', EAZYDOCS_ASSETS . '/css/shortcodes.css', array(), EAZYDOCS_VERSION );
		}

		if ( ezd_has_shortcode( ['eazydocs'] ) ) {
			wp_enqueue_style( 'ezd-docs-widgets' );
		}

		if ( ezd_frontend_pages() ) {
			// Scripts
			wp_enqueue_script( 'printThis', EAZYDOCS_ASSETS . '/js/frontend/printThis.js', array(), EAZYDOCS_VERSION, true );

			wp_enqueue_script( 'anchor' );
			wp_enqueue_script('scrollspy');
			wp_enqueue_script( 'bootstrap-toc-js', EAZYDOCS_ASSETS . '/js/frontend/bootstrap-toc.min.js', array( 'jquery' ), '1.0.1', true );
			wp_enqueue_script( 'eazydocs-single', EAZYDOCS_ASSETS . '/js/frontend/docs-single.js', array( 'jquery' ), EAZYDOCS_VERSION, true );
			wp_register_script( 'eazydocs-onepage', EAZYDOCS_ASSETS . '/js/frontend/onepage.js', array( 'jquery' ), EAZYDOCS_VERSION, true );

			$is_dark_switcher = ezd_is_premium() ? ezd_get_opt( 'is_dark_switcher' ) : false;

			if ( $is_dark_switcher == '1' && is_singular( ['docs', 'onepage-docs'] ) ) {
				wp_enqueue_style( 'eazydocs-dark-mode', EAZYDOCS_ASSETS . '/css/frontend_dark-mode.css', array(), EAZYDOCS_VERSION );
			}

			wp_enqueue_style( 'eazydocs-frontend', EAZYDOCS_ASSETS . '/css/frontend.css', array(), EAZYDOCS_VERSION );
		}

		if ( is_rtl() ) {
			if ( ezd_frontend_pages() ) {
				wp_enqueue_style( 'eazydocs-rtl', EAZYDOCS_ASSETS . '/css/rtl.css', ['eazydocs-frontend'], EAZYDOCS_VERSION);
			} else {
				wp_enqueue_style( 'eazydocs-rtl', EAZYDOCS_ASSETS . '/css/rtl.css', [], EAZYDOCS_VERSION);
			}
		}

		// Enqueue on onepage doc
		if ( is_singular( 'onepage-docs' ) || is_page_template( 'page-onepage.php' ) ) {
			wp_enqueue_style( 'ezd-onepage', EAZYDOCS_ASSETS . '/css/onepage.css', array(), EAZYDOCS_VERSION );
		}

		// Localize the script with new data
		$ajax_url              = admin_url( 'admin-ajax.php' );
		$wpml_current_language = apply_filters( 'wpml_current_language', null );

		if ( ! empty( $wpml_current_language ) ) {
			$ajax_url = add_query_arg( 'wpml_lang', $wpml_current_language, $ajax_url );
		}

		$elementor_docs = [];
		if ( class_exists( '\Elementor\Plugin' ) ) {
			$elementor_docs = get_posts(
				[
					'post_type'   => 'docs',
					'post_status' => 'publish',
					'numberposts' => -1,
					'fields'      => 'ids',
					'meta_key'    => '_elementor_edit_mode',
					'meta_value'  => 'builder',
				]
			);
		}

		wp_localize_script( 'jquery', 'eazydocs_local_object',
			array(
				'ajaxurl'            => $ajax_url,
				'EAZYDOCS_FRONT_CSS' => EAZYDOCS_FRONT_CSS,
				'nonce'              => wp_create_nonce( 'eazydocs-ajax' ),
				'is_doc_ajax'        => ezd_is_premium() ? ezd_get_opt( 'is_doc_ajax' ) : false,
				'ezd_layout_container' => ezd_container(),
				'ezd_search_submit'   => ezd_get_opt('is_search_submit', true),
				'ezd_dark_switcher'   => ezd_get_opt('is_dark_switcher', true),
				'elementor_docs'     => $elementor_docs,
			)
		);

		wp_register_style( 'ezd-frontend-global', EAZYDOCS_ASSETS . '/css/frontend-global.css', array(), EAZYDOCS_VERSION );

		// Global Scripts
		wp_register_style( 'elegant-icon', EAZYDOCS_VEND . '/elegant-icon/style.css', array(), EAZYDOCS_VERSION );

		if ( self::global_scope() ) {
			wp_enqueue_style( 'elegant-icon' );
			wp_enqueue_style( 'ezd-frontend-global' );

			// Dynamic CSS
			$dynamic_css = '';
			if ( ezd_get_opt( 'brand_color' ) ) {
				$brand_rgb   = ezd_hex2rgba( ezd_get_opt( 'brand_color' ) );
				// Border and background color
				$dynamic_css .= ".categories_guide_item .doc_border_btn { border: 1px solid rgba($brand_rgb, 0.2); background: rgba($brand_rgb, 0.05) }";
				$dynamic_css .= ".doc_switch input[type=checkbox] { border: 1px solid rgba($brand_rgb, 0.3); background: rgba($brand_rgb, 0.25) }";
				// background 0.1
				$dynamic_css .= ".nav-sidebar .nav-item .dropdown_nav li:not(.has_child).current_page_item, .nav-sidebar .nav-item.current_page_item > .doc-link, .doc-btm ul.card_tagged li a:hover, .categories_guide_item a.doc_tag_title span.badge { background: rgba($brand_rgb, .1) }";
				// background 0.2
				$dynamic_css .= ".nav-sidebar .nav-item .dropdown_nav li:not(.has_child).current_page_item:hover { background: rgba($brand_rgb, .2) }";
				// background 0.6
				$dynamic_css .= ".single-docs .shortcode_title .ezd-doc-badge, .categories_guide_item.bg-dark a.doc_border_btn { background: rgba($brand_rgb, .6) }";
				// background 0.7
				$dynamic_css .= ".single-docs .shortcode_title .ezd-doc-badge:hover { background: rgba($brand_rgb, .7) }";
				// background 0.9
				$dynamic_css .= "#eazydocs_feedback .action_btn { background: rgba($brand_rgb, .9); }";
				$dynamic_css .= ".documentation_item .media-body .title:hover { text-decoration-color: rgba($brand_rgb, 0.25)}";
			}

			wp_add_inline_style( 'ezd-frontend-global', $dynamic_css );
			wp_enqueue_script( 'eazydocs-global', EAZYDOCS_ASSETS . '/js/frontend/global.js', array( 'jquery' ), EAZYDOCS_VERSION, true );
		}
	}

	public function enqueue_scripts_after() {
		if ( is_singular('docs') || get_post_type() == 'onepage-docs' || is_page_template( 'page-onepage.php' ) ) {
			wp_enqueue_style( 'eazydocs-responsive', EAZYDOCS_ASSETS . '/css/frontend/ezd-responsive.css', array(), EAZYDOCS_VERSION );
		}
		if ( is_singular('docs') && ( ezd_is_premium() ? ezd_get_opt( 'is_doc_ajax' ) == '1' : false ) && ezd_unlock_themes('docy','docly','ama') ) {
			wp_enqueue_script( 'eazydocs-ajax-loading', EAZYDOCS_ASSETS . '/js/frontend/ajax.js', array( 'jquery' ), EAZYDOCS_VERSION, true );
		}
		// if gutenberg block theme installed and single docs
		if ( function_exists( 'block_header_area' ) && is_singular('docs') ) {
			wp_enqueue_script( 'eazydocs-block', EAZYDOCS_ASSETS . '/js/frontend/block.js', array( 'jquery' ), EAZYDOCS_VERSION, true );	
		}
	}

	private static function global_scope() {
		if ( has_block( 'eazydocs/search-banner' )
		     || in_array( 'eazydocs_shortcode', get_body_class() )
		     || is_singular( 'docs' )
		     || is_singular( 'onepage-docs' )
		     || is_page_template( 'page-onepage.php' )
		) {
			return true;
		}
	}
}