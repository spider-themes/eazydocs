<?php
namespace eazyDocs\Frontend;

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
		$opt = get_option( 'eazydocs_settings' );

		wp_enqueue_script( 'jquery' );
		wp_register_style( 'font-awesome-5', 'https://cdn.jsdelivr.net/npm/@fortawesome/fontawesome-free@5.15.4/css/all.min.css' );
		// Register
		wp_register_script( 'mixitup', EAZYDOCS_VEND.'/mixitup/mixitup.min.js', array( 'jquery' ), '2.1.11', true );
		wp_register_script( 'anchor', EAZYDOCS_ASSETS.'/js/frontend/anchor.js', array( 'jquery' ), '5.1.3', true );
		wp_register_script( 'scrollspy', EAZYDOCS_ASSETS.'/js/frontend/scrollspy-gumshoe.js', array( 'jquery' ), '5.1.2', true );
		wp_register_script( 'eazydocs-el-widgets', EAZYDOCS_ASSETS.'/js/frontend/elementor-widgets.js' );

		wp_register_style( 'elegant-icon', EAZYDOCS_ASSETS.'/vendors/elegant-icon/style.css' );
		wp_register_style( 'ezd-el-widgets', EAZYDOCS_ASSETS.'/css/ezd-el-widgets.css' );

		$dynamic_cssd = ":root { --ezd_brand_color: " . ezd_get_opt( 'brand_color' ) . "; }";
		wp_add_inline_style( 'eazydocs-blocks', $dynamic_cssd );

		if ( ezydocspro_shortcodes_assets() ) {
			wp_enqueue_style( 'eazydocs-shortcodes', EAZYDOCS_ASSETS . '/css/shortcodes.css' );
			wp_enqueue_script( 'eazydocs-shortcodes', EAZYDOCS_ASSETS . '/js/shortcodes.js' );
		}

		if ( ezydocs_frontend_assets() ) {
			// Scripts
			wp_enqueue_script( 'printThis', EAZYDOCS_ASSETS . '/js/frontend/printThis.js' );

			wp_enqueue_script( 'anchor' );
			wp_enqueue_script('scrollspy');
			wp_enqueue_script( 'bootstrap-toc-js', EAZYDOCS_ASSETS . '/js/frontend/bootstrap-toc.min.js', array( 'jquery' ) );
			wp_enqueue_script( 'eazydocs-single', EAZYDOCS_ASSETS . '/js/frontend/docs-single.js', array( 'jquery' ), EAZYDOCS_VERSION );
			wp_register_script( 'eazydocs-onepage', EAZYDOCS_ASSETS . '/js/frontend/onepage.js', array( 'jquery' ), EAZYDOCS_VERSION );

			$is_dark_switcher = $opt['is_dark_switcher'] ?? '';

			if ( $is_dark_switcher == '1' ) {
				wp_enqueue_style( 'eazydocs-dark-mode', EAZYDOCS_ASSETS . '/css/frontend_dark-mode.css' );
			}

			wp_enqueue_style( 'eazydocs-frontend', EAZYDOCS_ASSETS . '/css/frontend.css', EAZYDOCS_VERSION );

			if ( is_rtl() ) {
				wp_enqueue_style( 'eazydocs-rtl', EAZYDOCS_ASSETS . '/css/rtl.css', array( 'eazydocs-frontend' ) );
			}
		}

		// Enqueue on onepage doc
		if ( is_singular( 'onepage-docs' ) || is_page_template( 'page-onepage.php' ) ) {
			wp_enqueue_style( 'ezd-onepage', EAZYDOCS_ASSETS . '/css/onepage.css' );
		}

		// Localize the script with new data
		$ajax_url              = admin_url( 'admin-ajax.php' );
		$wpml_current_language = apply_filters( 'wpml_current_language', null );

		if ( ! empty( $wpml_current_language ) ) {
			$ajax_url = add_query_arg( 'wpml_lang', $wpml_current_language, $ajax_url );
		}

		wp_localize_script( 'jquery', 'eazydocs_local_object',
			array(
				'ajaxurl'            => $ajax_url,
				'EAZYDOCS_FRONT_CSS' => EAZYDOCS_FRONT_CSS,
				'nonce'              => wp_create_nonce( 'eazydocs-ajax' ),
				'is_doc_ajax'        => ezd_get_opt( 'is_doc_ajax' ),
				'ezd_layout_container' => ezd_container(),
			)
		);

		wp_register_style( 'eazydocs-frontend-global', EAZYDOCS_ASSETS . '/css/frontend-global.css' );

		// Global Scripts
		wp_register_style( 'elegant-icon', EAZYDOCS_VEND . '/elegant-icon/style.css' );
		if ( self::global_scope() ) {
			wp_enqueue_style( 'elegant-icon' );
			wp_enqueue_style( 'eazydocs-frontend-global' );

			// Dynamic CSS
			$dynamic_css = '';
			if ( ! empty( $opt['brand_color'] ) ) {
				$brand_rgb   = ezd_hex2rgba( $opt['brand_color'] );
				$dynamic_css .= ".doc_switch input[type=checkbox] { border: 1px solid rgba($brand_rgb, 0.3); background: rgba($brand_rgb, 0.25) }";
				$dynamic_css .= ".categories_guide_item .doc_border_btn { border: 1px solid rgba($brand_rgb, 0.2); background: rgba($brand_rgb, 0.05) }";
				$dynamic_css .= "#eazydocs_feedback .action_btn{ background: rgba($brand_rgb, .9); }";
				$dynamic_css .= ".nav-sidebar .nav-item.current_page_item > .doc-link, .doc-btm ul.card_tagged li a:hover, .categories_guide_item a.doc_tag_title span.badge { background: rgba($brand_rgb, .1) }";
				$dynamic_css .= ".nav-sidebar .nav-item .dropdown_nav li:not(.has_child).current_page_item { background: rgba($brand_rgb, .1) }";
				$dynamic_css .= ".nav-sidebar .nav-item .dropdown_nav li:not(.has_child).current_page_item:hover { background: rgba($brand_rgb, .2) }";
				$dynamic_css .= ".documentation_item .media-body .title:hover { text-decoration-color: rgba($brand_rgb, 0.25)}";
			}

			wp_add_inline_style( 'eazydocs-frontend-global', $dynamic_css );
			wp_enqueue_script( 'eazydocs-global', EAZYDOCS_ASSETS . '/js/frontend/global.js', array( 'jquery' ), EAZYDOCS_VERSION );
		}
	}

	public function enqueue_scripts_after() {
		if ( is_singular('docs') || get_post_type() == 'onepage-docs' || is_page_template( 'page-onepage.php' ) ) {
			wp_enqueue_style( 'eazydocs-responsive', EAZYDOCS_ASSETS . '/css/frontend/ezd-responsive.css' );
		}
		if ( is_singular('docs') && ezd_get_opt( 'is_doc_ajax' ) == '1' && ezd_unlock_themes() ) {
			wp_enqueue_script( 'eazydocs-ajax-loading', EAZYDOCS_ASSETS . '/js/frontend/ajax.js', array( 'jquery' ), EAZYDOCS_VERSION );
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