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
		$current_url               = ! empty( $_GET["page"] ) ? admin_url( "admin.php?page=" . sanitize_text_field( $_GET["page"] ) ) : '';
		$doc_builder_url           = admin_url( '/admin.php?page=eazydocs' );
		$settings_url              = admin_url( '/admin.php?page=eazydocs-settings' );
		$target_onepage_url        = admin_url( '/admin.php?page=eazydocs-onepage' );
		$target_analytics_page_url = admin_url( '/admin.php?page=ezd-analytics' );

		if ( $current_url == $doc_builder_url ) {
			add_action( 'admin_enqueue_scripts', [ $this, 'eazydocs_dashboard_scripts' ] );
		} elseif ( $current_url == $target_onepage_url ) {
			add_action( 'admin_enqueue_scripts', [ $this, 'eazydocs_dashboard_scripts' ] );
		} elseif ( $current_url == $target_analytics_page_url ) {
			add_action( 'admin_enqueue_scripts', [ $this, 'eazydocs_dashboard_scripts' ] );
		}

		add_action( 'admin_enqueue_scripts', [ $this, 'eazydocs_global_scripts' ] );
	}

	/**
	 * Register scripts and styles
	 **/
	public function eazydocs_dashboard_scripts() {
		/* Stylesheets */
		wp_enqueue_style( 'nice-select', EAZYDOCS_ASSETS . '/css/admin/nice-select.css' );
		wp_enqueue_style( 'sweetalert', EAZYDOCS_ASSETS . '/css/admin/sweetalert.css' );
		wp_enqueue_style( 'eazyDocs-main', EAZYDOCS_ASSETS . '/css/admin.css', array(), EAZYDOCS_VERSION );

		/* Scripts */
		wp_enqueue_script( 'modernizr', EAZYDOCS_ASSETS . '/js/admin/modernizr-3.11.2.min.js', array( 'jquery' ), '', true );
		wp_enqueue_script( 'mixitup', EAZYDOCS_VEND . '/mixitup/mixitup.min.js', array( 'jquery' ), '2.1.11', true );
		wp_enqueue_script( 'mixitup-multifilter', EAZYDOCS_ASSETS . '/js/admin/mixitup-multifilter.js', array( 'jquery' ), '', true );
		wp_enqueue_script( 'jquery-nice-select', EAZYDOCS_ASSETS . '/js/admin/jquery.nice-select.min.js', array( 'jquery' ), '', true );
		wp_enqueue_script( 'tabby-polyfills', EAZYDOCS_ASSETS . '/js/admin/tabby.polyfills.min.js', array( 'jquery' ), '', true );
		wp_enqueue_script( 'eazyDocs-accordion', EAZYDOCS_ASSETS . '/js/admin/accordion.min.js', array( 'jquery' ), '', true );
		wp_enqueue_script( 'sweetalert', EAZYDOCS_ASSETS . '/js/admin/sweetalert.min.js', array( 'jquery' ), '', true );
		wp_enqueue_script( 'eazyDocs-custom', EAZYDOCS_ASSETS . '/js/admin/custom.js', array( 'jquery' ), EAZYDOCS_VERSION, true );
	}

	/**
	 * Enqueue global scripts
	 * and styles by EazyDocs pages on WordPress dashboard
	 */
	public function eazydocs_global_scripts() {
		wp_enqueue_script( 'ezd-notify-review', EAZYDOCS_ASSETS . '/js/admin/review.js' );
		wp_enqueue_style( 'eazydocs-admin-global', EAZYDOCS_ASSETS . '/css/admin-global.css', '', EAZYDOCS_VERSION );

		if ( ezydocs_admin_pages() == true ) {
			wp_enqueue_style( 'sweetalert', EAZYDOCS_ASSETS . '/css/admin/sweetalert.css' );
			wp_enqueue_script( 'sweetalert', EAZYDOCS_ASSETS . '/js/admin/sweetalert.min.js', array( 'jquery' ), true, true );
			wp_enqueue_script( 'eazydocs-admin-global', EAZYDOCS_ASSETS . '/js/admin/admin-global.js', array( 'jquery' ), EAZYDOCS_VERSION );
			wp_enqueue_script( 'eazydocs-admin-onepage', EAZYDOCS_ASSETS . '/js/admin/one_page.js', array( 'jquery' ), EAZYDOCS_VERSION );
			wp_enqueue_style( 'eazydocs-custom', EAZYDOCS_ASSETS . '/css/admin/custom.css' );

			wp_enqueue_script( 'eazydocs-nestable', EAZYDOCS_ASSETS . '/js/admin/jquery.nestable.js', array( 'jquery' ), true, true );
			wp_enqueue_script( 'eazydocs-nestable-script', EAZYDOCS_ASSETS . '/js/admin/nestable-script.js', array( 'jquery' ), true, true );
		}

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
			)
		);
	}
}