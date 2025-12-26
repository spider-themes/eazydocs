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
	}

	/**
	 * Register scripts and styles
	 **/
	public function dashboard_scripts() {
		// Doc Builder Assets
		if ( ezd_admin_pages('eazydocs-builder') ) {
			wp_enqueue_script( 'ezd-accordion', EAZYDOCS_ASSETS . '/js/admin/accordion.min.js', array( 'jquery' ), EAZYDOCS_VERSION, true );
			wp_enqueue_script( 'ezd-nestable', EAZYDOCS_ASSETS . '/js/admin/jquery.nestable.js', array('jquery'), EAZYDOCS_VERSION, true );
			wp_enqueue_script( 'ezd-drag-drop-enhanced', EAZYDOCS_ASSETS . '/js/admin/drag-drop-enhanced.js', array('jquery', 'ezd-nestable'), EAZYDOCS_VERSION, true );
		}

		if ( ezd_admin_pages( ['eazydocs-builder', 'ezd-analytics'] )) {
			wp_enqueue_script( 'mixitup', EAZYDOCS_VEND . '/mixitup/mixitup.min.js', array( 'jquery' ), '2.1.11', true );
			wp_enqueue_script( 'mixitup-multifilter', EAZYDOCS_ASSETS . '/js/admin/mixitup-multifilter.js', array( 'jquery' ), '2.1.11', true );			
			wp_enqueue_script( 'modernizr', EAZYDOCS_ASSETS . '/js/admin/modernizr-3.11.2.min.js', array( 'jquery' ), '3.11.2', true );
			wp_enqueue_script( 'tabby-polyfills', EAZYDOCS_ASSETS . '/js/admin/tabby.polyfills.min.js', array( 'jquery' ), '12.0.3', true );
		}

		if ( ezd_admin_pages( ['eazydocs-builder', 'ezd-analytics', 'eazydocs'] ) ) {
			wp_enqueue_script( 'ezd-admin-custom', EAZYDOCS_ASSETS . '/js/admin/custom.js', array( 'jquery' ), EAZYDOCS_VERSION, true );
		}

		if ( ezd_admin_pages( ['eazydocs-builder', 'ezd-analytics', 'eazydocs-initial-setup'] ) ) {
			wp_enqueue_style( 'nice-select', EAZYDOCS_ASSETS . '/css/admin/nice-select.css', array(), EAZYDOCS_VERSION );
			wp_enqueue_script( 'jquery-nice-select', EAZYDOCS_ASSETS . '/js/admin/jquery.nice-select.min.js', array( 'jquery' ), '1.0', true );
		}

		wp_register_style( 'sweetalert', EAZYDOCS_ASSETS . '/css/admin/sweetalert.css', array(), EAZYDOCS_VERSION );
		wp_register_script( 'sweetalert', EAZYDOCS_ASSETS . '/js/admin/sweetalert.min.js', array( 'jquery' ), EAZYDOCS_VERSION, true );

		if ( ezd_admin_pages( ['eazydocs-builder', 'eazydocs-settings', 'eazydocs', 'eazydocs-initial-setup', 'ezd-analytics', 'ezd-user-feedback'] ) || ezd_admin_post_types('onepage-docs') ) {			
			wp_enqueue_style( 'sweetalert' );
			wp_enqueue_script( 'sweetalert' );
		}

		if ( ezd_admin_pages() ) {
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
			// Custom styles and scripts
			wp_enqueue_style( 'ezd_setup_wizard', EAZYDOCS_ASSETS . '/css/admin_setup_wizard.css', array(), EAZYDOCS_VERSION );
			wp_enqueue_script( 'ezd_setup_wizard', EAZYDOCS_ASSETS . '/js/admin/setup_wizard_config.js', array( 'jquery', 'smartwizard' ), EAZYDOCS_VERSION, true );
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

		wp_enqueue_script( 'eazydocs-admin-global', EAZYDOCS_ASSETS . '/js/admin/admin-global.js', array( 'jquery' ), EAZYDOCS_VERSION, true );

		// Localize the script with new data
		$ajax_url              = admin_url( 'admin-ajax.php' );
		$wpml_current_language = apply_filters( 'wpml_current_language', null );
		if ( ! empty( $wpml_current_language ) ) {
			$ajax_url = add_query_arg( 'wpml_lang', $wpml_current_language, $ajax_url );
		}
		wp_localize_script(
			'jquery',
			'eazydocs_local_object',
			array(
				'ajaxurl'                   => $ajax_url,
				'EAZYDOCS_FRONT_CSS'        => EAZYDOCS_FRONT_CSS,
				'EAZYDOCS_ASSETS'           => EAZYDOCS_ASSETS,
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
				'ezd_get_conditional_items' => ezd_get_conditional_items()
			)
		);
	}
}