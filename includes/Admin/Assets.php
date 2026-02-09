<?php
namespace EazyDocs\Admin;

/**
 * Cannot access directly.
 */
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

/**
 * Class Assets
 * @package EazyDocs\Admin
 */
class Assets {

	/**
	 * Assets constructor.
	 */
	public function __construct() {
		add_action( 'admin_enqueue_scripts', [ $this, 'global_scripts' ] );
		if ( ezd_admin_pages() || ezd_admin_post_types() ) {
			add_action( 'admin_enqueue_scripts', [ $this, 'dashboard_scripts' ] );
		}
		add_action( 'enqueue_block_editor_assets', [ $this, 'enqueue_block_editor_assets' ] );
		add_action( 'customize_controls_enqueue_scripts', [ $this, 'customizer_scripts' ] );
	}

	/**
	 * Register scripts and styles
	 **/
	public function dashboard_scripts() {
		// Doc Builder Assets (only on Doc Builder page)
		if ( ezd_admin_pages('eazydocs-builder') ) {
			wp_enqueue_script( 'ezd-accordion', EAZYDOCS_ASSETS . '/js/admin/accordion.min.js', array( 'jquery' ), EAZYDOCS_VERSION, true );
			wp_enqueue_script( 'ezd-nestable', EAZYDOCS_ASSETS . '/js/admin/jquery.nestable.js', array('jquery'), EAZYDOCS_VERSION, true );
			
			// Doc Builder specific JavaScript (tab handling, doc CRUD, search, drag-drop, etc.)
			wp_enqueue_script( 'ezd-doc-builder', EAZYDOCS_ASSETS . '/js/admin/doc-builder.js', array( 'jquery', 'mixitup', 'ezd-nestable' ), EAZYDOCS_VERSION, true );
		}

		// MixItUp - needed on both Doc Builder (notification filtering) and Analytics (data filtering)
		if ( ezd_admin_pages( ['eazydocs-builder', 'ezd-analytics'] ) ) {
			wp_enqueue_script( 'mixitup', EAZYDOCS_VEND . '/mixitup/mixitup.min.js', array( 'jquery' ), '2.1.11', true );
		}

		// Analytics page also needs multifilter, modernizr, and tabby polyfills
		if ( ezd_admin_pages( 'ezd-analytics' ) ) {
			wp_enqueue_script( 'mixitup-multifilter', EAZYDOCS_ASSETS . '/js/admin/mixitup-multifilter.js', array( 'jquery', 'mixitup' ), '2.1.11', true );			
			wp_enqueue_script( 'modernizr', EAZYDOCS_ASSETS . '/js/admin/modernizr-3.11.2.min.js', array( 'jquery' ), '3.11.2', true );
			wp_enqueue_script( 'tabby-polyfills', EAZYDOCS_ASSETS . '/js/admin/tabby.polyfills.min.js', array( 'jquery' ), '12.0.3', true );
		}

		// Dashboard page only (NOT on Doc Builder or Analytics to prevent conflicts)
		if ( ezd_admin_pages( 'eazydocs' ) ) {
			wp_enqueue_script( 'ezd-admin-custom', EAZYDOCS_ASSETS . '/js/admin/custom.js', array( 'jquery' ), EAZYDOCS_VERSION, true );
		}

		// NiceSelect - only needed on Dashboard and Setup pages (not Doc Builder or Analytics)
		if ( ezd_admin_pages( ['eazydocs', 'eazydocs-initial-setup'] ) ) {
			wp_enqueue_style( 'nice-select', EAZYDOCS_ASSETS . '/css/admin/nice-select.css', array(), EAZYDOCS_VERSION );
			wp_enqueue_script( 'jquery-nice-select', EAZYDOCS_ASSETS . '/js/admin/jquery.nice-select.min.js', array( 'jquery' ), '1.0', true );
		}

		// ApexCharts - only needed on Dashboard and Analytics for charts (not Doc Builder)
		if ( ezd_admin_pages( ['eazydocs', 'ezd-analytics'] ) ) {
			wp_deregister_style('csf-fa5');
			wp_deregister_style('csf-fa5-v4-shims');
        	wp_enqueue_script( 'apexchart', EAZYDOCS_ASSETS . '/js/apexchart.js', array( 'jquery' ), EAZYDOCS_VERSION, false );
		}

		if ( ezd_admin_pages( ['eazydocs-builder'] ) || ezd_admin_post_types('onepage-docs') ) {
			wp_enqueue_script( 'ezd-admin-onepage', EAZYDOCS_ASSETS . '/js/admin/one_page.js', array( 'jquery' ), EAZYDOCS_VERSION, true );
		}

		wp_enqueue_style( 'ezd-main', EAZYDOCS_ASSETS . '/css/admin.css', array(), EAZYDOCS_VERSION );
		wp_enqueue_style( 'ezd-custom', EAZYDOCS_ASSETS . '/css/admin/custom.css', array(), EAZYDOCS_VERSION );

		// Enqueue scripts and styles for initial setup page
		if ( ezd_admin_pages('eazydocs-initial-setup') ) {
			// Deregister unnecessary styles and scripts
			wp_deregister_style( 'ezd-main' );
			wp_deregister_style( 'ezd-custom' );
			wp_deregister_style( 'eazydocs-admin-global' );
			wp_deregister_script( 'eazydocs-admin-global' );
			// Frameworks
			wp_enqueue_style( 'wp-color-picker' );
			wp_enqueue_script( 'wp-color-picker' );
			wp_enqueue_script( 'smartwizard', EAZYDOCS_ASSETS . '/js/admin/jquery.smartWizard.min.js', array('jquery'), true, true );
			// Custom styles and scripts (use filemtime for cache busting during development)
			$setup_wizard_css_ver = filemtime( EAZYDOCS_PATH . '/assets/css/admin_setup_wizard.css' );
			$setup_wizard_js_ver  = filemtime( EAZYDOCS_PATH . '/assets/js/admin/setup_wizard_config.js' );
			wp_enqueue_style( 'ezd_setup_wizard', EAZYDOCS_ASSETS . '/css/admin_setup_wizard.css', array(), $setup_wizard_css_ver );
			wp_enqueue_script( 'ezd_setup_wizard', EAZYDOCS_ASSETS . '/js/admin/setup_wizard_config.js', array( 'jquery', 'smartwizard' ), $setup_wizard_js_ver, true );
		}
	}

	public function enqueue_block_editor_assets() {
		 // Enqueue initial assets for the editor
		 wp_enqueue_script(
			'ezd-block-insert-handler',
			EAZYDOCS_ASSETS . '/js/block-insert-handler.js',
			['wp-data', 'wp-blocks', 'wp-edit-post'],
			EAZYDOCS_VERSION,
			true
		);

		// Enqueue SweetAlert for ProMax notices in toolbar
		if ( get_post_type() === 'docs' ) {
			wp_enqueue_style( 'sweetalert', EAZYDOCS_ASSETS . '/css/admin/sweetalert.css', array(), EAZYDOCS_VERSION );
			wp_enqueue_script( 'sweetalert', EAZYDOCS_ASSETS . '/js/admin/sweetalert.min.js', array( 'jquery' ), EAZYDOCS_VERSION, true );
		}
	
		wp_localize_script('ezd-block-insert-handler', 'ezdAssets', [
			'styles' => [
				EAZYDOCS_ASSETS . '/vendors/elegant-icon/style.css',
				EAZYDOCS_ASSETS . '/css/ezd-docs-widgets.css',
			],
			'scripts' => [
				EAZYDOCS_ASSETS . '/js/admin/sweetalert.min.js',
			],
			'ezd_img_dir' 		=> EAZYDOCS_IMG
		]);
	}
 
	/**
	 * Enqueue global scripts
	 * Load on all pages of WordPress dashboard
	 */
	public function global_scripts() {
		wp_enqueue_style( 'eazydocs-admin-global', EAZYDOCS_ASSETS . '/css/admin-global.css', array(), EAZYDOCS_VERSION );
		wp_enqueue_style( 'elegant-icon', EAZYDOCS_ASSETS . '/vendors/elegant-icon/style.css', array(), EAZYDOCS_VERSION );		
		wp_register_style( 'ezd-admin-settings', EAZYDOCS_ASSETS . '/css/admin-settings.css', array(), EAZYDOCS_VERSION );

		// Settings page specific styles
		if ( ezd_admin_pages( 'eazydocs-settings' )  ) {
			wp_enqueue_style( 'ezd-admin-settings' );
		}
		
		wp_register_style( 'sweetalert', EAZYDOCS_ASSETS . '/css/admin/sweetalert.css', array(), EAZYDOCS_VERSION );
		wp_register_script( 'sweetalert', EAZYDOCS_ASSETS . '/js/admin/sweetalert.min.js', array( 'jquery' ), EAZYDOCS_VERSION, true );

		if ( ezd_admin_pages( ['eazydocs-builder', 'eazydocs-settings', 'eazydocs', 'eazydocs-initial-setup', 'ezd-analytics', 'ezd-user-feedback'] ) || ezd_admin_post_types('onepage-docs') ) {			
			wp_enqueue_style( 'sweetalert' );
			wp_enqueue_script( 'sweetalert' );
		}

		wp_enqueue_script( 'eazydocs-admin-global', EAZYDOCS_ASSETS . '/js/admin/admin-global.js', array( 'jquery' ), EAZYDOCS_VERSION, true );

		// Localize the script with new data
		$ajax_url              = admin_url( 'admin-ajax.php' );
		$wpml_current_language = apply_filters( 'wpml_current_language', null );
		if ( ! empty( $wpml_current_language ) ) {
			$ajax_url = add_query_arg( 'wpml_lang', $wpml_current_language, $ajax_url );
		}
		
		// Check if Antimanual is active
		if ( ! \function_exists( 'is_plugin_active' ) ) {
			include_once \ABSPATH . 'wp-admin/includes/plugin.php';
		}
		$antimanual_active = \function_exists( 'is_plugin_active' ) && \is_plugin_active( 'antimanual/antimanual.php' );

		// Shared "Create Doc with AI" popup HTML (single source)
		$ai_popup_html = '';
		$ai_popup_template = EAZYDOCS_PATH . '/includes/Admin/template/partials/ai-create-doc-popup.php';
		if ( file_exists( $ai_popup_template ) ) {
			$antimanual_settings_url = admin_url( 'admin.php?page=antimanual' );
			$antimanual_docs_url     = 'https://helpdesk.spider-themes.net/docs/antimanual';
			$antimanual_install_url  = add_query_arg(
				array(
					's'    => 'antimanual',
					'tab'  => 'search',
					'type' => 'term',
				),
				admin_url( 'plugin-install.php' )
			);
			$antimanual_learn_more    = 'https://antimanual.spider-themes.net';
			$antimanual_demo_url      = 'https://www.youtube.com/watch?v=X9HMPBkzDeM';
			$antimanual_video_mp4_url = 'https://antimanual.spider-themes.net/wp-content/uploads/2025/08/AI-Doc-generate.mp4';

			ob_start();
			$is_antimanual_active = $antimanual_active;
			require $ai_popup_template;
			$ai_popup_html = (string) ob_get_clean();
		}
		
		wp_localize_script(
			'jquery',
			'eazydocs_local_object',
			array(
				'ajaxurl'                   => $ajax_url,
				'EAZYDOCS_FRONT_CSS'        => EAZYDOCS_FRONT_CSS,
				'EAZYDOCS_ASSETS'           => EAZYDOCS_ASSETS,
				'antimanualActive'          => $antimanual_active,
				'aiPopupHtml'               => $ai_popup_html,
				'create_prompt_title'       => esc_html__( 'Enter Doc Title', 'eazydocs' ),
				'delete_prompt_title'       => esc_html__( 'Are you sure to delete?', 'eazydocs' ),
				'no_revert_title'           => esc_html__( "This doc will be trashed with the child docs and you will be able to restore it later from the trash!", "eazydocs" ),
				'clone_prompt_title'        => esc_html__( "Are you sure to Duplicate this doc?", "eazydocs" ),
				'nonce'                     => wp_create_nonce( 'eazydocs-admin-nonce' ),
				'one_page_prompt_docs'      => eazydocs_pro_doc_list(),
				'onepage_doc_admin_url'     => admin_url(),
				'one_page_prompt_sidebar'   => sidebar_selectbox(),
				'one_page_doc_sidebar_edit' => ezd_edit_sidebar_selectbox(),
				'edit_one_page_url'         => admin_url( 'admin.php?edit_docs=yes&' ),
				'get_reusable_block'        => get_reusable_blocks(),
				'get_reusable_blocks_right' => get_reusable_blocks_right(),
				'manage_reusable_blocks'    => ezd_manage_reusable_blocks(),
				'is_ezd_premium'            => eaz_fs()->is_paying_or_trial() ? 'yes' : '',
				'is_ezd_pro_block'          => ezd_is_premium() ? 'yes' : '',
				'ezd_get_conditional_items' => ezd_get_conditional_items(),
				'ezd_pricing_url'           => admin_url( 'admin.php?page=eazydocs-pricing' ),
				'is_footnotes_unlocked' 	=> ezd_is_footnotes_unlocked() ?'yes':'no'
			)
		);
	}

	/**
	 * Enqueue scripts and styles for customizer
	 * 
	 * @return void
	 */
	public function customizer_scripts(){
		wp_enqueue_style( 'ezd-admin-settings' );		
		wp_enqueue_style( 'sweetalert' );
		wp_enqueue_script( 'sweetalert' );

    	wp_add_inline_style( 'eazydocs-admin-global', '
			.eazydocs-pro-notice, .eazydocs-promax-notice { cursor: pointer !important; }
			.swal2-container { z-index: 999999 !important; } 
		');		
	}
	
}