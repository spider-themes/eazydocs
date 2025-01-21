<?php
namespace eazyDocs\Admin;

/**
 * Class Assets
 * @package EazyDocs\Admin
 */
class Assets {

	/**
	 * Assets constructor.
	 */
	public function __construct() {
		if ( ezd_admin_pages() || ezd_admin_post_types() ) {
			add_action( 'admin_enqueue_scripts', [ $this, 'dashboard_scripts' ] );
		}
		add_action( 'admin_enqueue_scripts', [ $this, 'global_scripts' ] );
		add_action( 'enqueue_block_editor_assets', [ $this, 'enqueue_block_editor_assets' ] );
	}

	/**
	 * Register scripts and styles
	 **/
	public function dashboard_scripts() {
		// Doc Builder Assets
		if ( ezd_admin_pages('eazydocs') ) {
			wp_enqueue_script( 'ezd-accordion', EAZYDOCS_ASSETS . '/js/admin/accordion.min.js', array( 'jquery' ), '', true );
			wp_enqueue_script( 'ezd-nestable', EAZYDOCS_ASSETS . '/js/admin/jquery.nestable.js', array('jquery'), true, true );
			wp_enqueue_script( 'ezd-nestable-script', EAZYDOCS_ASSETS . '/js/admin/nestable-script.js', array('jquery'), true, true );
		}

		if ( ezd_admin_pages( ['eazydocs', 'ezd-analytics'] )) {
			wp_enqueue_script( 'mixitup', EAZYDOCS_VEND . '/mixitup/mixitup.min.js', array( 'jquery' ), '2.1.11', true );
			wp_enqueue_script( 'mixitup-multifilter', EAZYDOCS_ASSETS . '/js/admin/mixitup-multifilter.js', array( 'jquery' ), '', true );
		}

		if ( ezd_admin_pages( ['eazydocs', 'ezd-analytics'] ) ) {
			wp_enqueue_script( 'modernizr', EAZYDOCS_ASSETS . '/js/admin/modernizr-3.11.2.min.js', array( 'jquery' ), '', true );
			wp_enqueue_script( 'tabby-polyfills', EAZYDOCS_ASSETS . '/js/admin/tabby.polyfills.min.js', array( 'jquery' ), '', true );
			wp_enqueue_script( 'ezd-custom', EAZYDOCS_ASSETS . '/js/admin/custom.js', array( 'jquery' ), EAZYDOCS_VERSION, true );
		}

		if ( ezd_admin_pages( ['eazydocs', 'ezd-analytics', 'eazydocs-initial-setup'] ) ) {
			wp_enqueue_style( 'nice-select', EAZYDOCS_ASSETS . '/css/admin/nice-select.css' );
			wp_enqueue_script( 'jquery-nice-select', EAZYDOCS_ASSETS . '/js/admin/jquery.nice-select.min.js', array( 'jquery' ), '', true );
		}

		wp_register_style( 'sweetalert', EAZYDOCS_ASSETS . '/css/admin/sweetalert.css' );
		wp_register_script( 'sweetalert', EAZYDOCS_ASSETS . '/js/admin/sweetalert.min.js', array( 'jquery' ), '', true );

		if ( ezd_admin_pages( ['eazydocs', 'eazydocs-settings', 'eazydocs-initial-setup', 'ezd-user-feedback'] ) || ezd_admin_post_types('onepage-docs') ) {			
			wp_enqueue_style( 'sweetalert' );
			wp_enqueue_script( 'sweetalert' );
		}

		if ( ezd_admin_post_types('onepage-docs')  ) {
			wp_enqueue_script( 'ezd-admin-onepage', EAZYDOCS_ASSETS . '/js/admin/one_page.js', array( 'jquery' ), EAZYDOCS_VERSION );
		}

		// Enqueue scripts and styles for initial setup page
		if ( ezd_admin_pages('eazydocs-initial-setup') ) {
			wp_enqueue_style( 'wp-color-picker' );
			wp_enqueue_script( 'wp-color-picker' );
			wp_enqueue_script( 'smartwizard', EAZYDOCS_ASSETS . '/js/admin/jquery.smartWizard.min.js', array('jquery'), true, true );
		}

		wp_enqueue_style( 'ezd-main', EAZYDOCS_ASSETS . '/css/admin.css', array(), EAZYDOCS_VERSION );
		wp_enqueue_style( 'ezd-custom', EAZYDOCS_ASSETS . '/css/admin/custom.css' );
	}

	public function enqueue_block_editor_assets() {

		$post_id 	= isset($_GET['post']) ? intval($_GET['post']) : null;
		// Define the blocks you want to check for
		$ezd_blocks = [ 'eazydocs-pro/eazy-docs' ];
	
		// Check if the post contains any of the target blocks
		if ($post_id) {
			$post_content = get_post($post_id)->post_content;
			foreach ($ezd_blocks as $block) {
				if ( has_block($block, $post_content) ) {
					// Enqueue your styles and scripts
					wp_enqueue_style('sweetalert', EAZYDOCS_ASSETS . '/css/admin/sweetalert.css');
					wp_enqueue_script('sweetalert', EAZYDOCS_ASSETS . '/js/admin/sweetalert.min.js', ['jquery'], '', true);
					wp_enqueue_style( 'elegant-icon', EAZYDOCS_ASSETS.'/vendors/elegant-icon/style.css' );
					wp_enqueue_style( 'ezd-docs-widgets', EAZYDOCS_ASSETS.'/css/ezd-docs-widgets.css' );
					break; // Exit loop once a matching block is found
				}
			}
		}
	}

	/**
	 * Enqueue global scripts
	 * Load on all pages of WordPress dashboard
	 */
	public function global_scripts() {
		wp_enqueue_script( 'ezd-notify-review', EAZYDOCS_ASSETS . '/js/admin/review.js' );
		wp_enqueue_style( 'eazydocs-admin-global', EAZYDOCS_ASSETS . '/css/admin-global.css', '', EAZYDOCS_VERSION );
		wp_enqueue_script( 'eazydocs-admin-global', EAZYDOCS_ASSETS . '/js/admin/admin-global.js', array( 'jquery' ), EAZYDOCS_VERSION );

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
				'one_page_prompt_sidebar'   => sidebar_selectbox(),
				'one_page_doc_sidebar_edit' => edit_sidebar_selectbox(),
				'edit_one_page_url'         => admin_url( 'admin.php?edit_docs=yes&' ),
				'get_reusable_block'        => get_reusable_blocks(),
				'get_reusable_blocks_right' => get_reusable_blocks_right(),
				'manage_reusable_blocks'    => manage_reusable_blocks(),
				'is_ezd_premium'            => eaz_fs()->is_paying_or_trial() ? 'yes' : '',
				'is_ezd_pro_block'          => ezd_is_premium() ? 'yes' : '',
				'ezd_get_conditional_items' => ezd_get_conditional_items(),
				'ezd_plugin_url'    		=> EAZYDOCS_URL,

			)
		);
	}
}