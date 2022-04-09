<?php
/**
 * WordPress settings API demo class
 *
 * @author SpiderDevs
 */
if ( ! class_exists( 'SpiderDevs_Settings_API_Test' ) ):
	class SpiderDevs_Settings_API_Test {

		private $settings_api;

		function __construct() {
			$this->settings_api = new SpiderDevs_Settings_API;

			add_action( 'admin_init', array( $this, 'admin_init' ) );
			add_action( 'admin_menu', array( $this, 'admin_menu' ) );
		}

		function admin_init() {
			//set the settings
			$this->settings_api->set_sections( $this->get_settings_sections() );
			$this->settings_api->set_fields( $this->get_settings_fields() );

			//initialize settings
			$this->settings_api->admin_init();
		}

		function admin_menu() {
			add_menu_page( __( 'EazyDocs', 'eazyDocs' ), __( 'EazyDocs', 'eazyDocs' ), 'manage_options', 'eazydocs', [ $this, 'eazydocs_page' ], 'dashicons-media-document', 10 );
			add_submenu_page( 'eazydocs', __( 'Settings', 'eazyDocs' ), __( 'Settings', 'eazyDocs' ), 'manage_options', 'eazydocs-settings', [ $this, 'plugin_page' ] );
		}

		public function eazydocs_page() {
			include EAZYDOCS_PATH . '/includes/Admin/admin-template.php';
		}

		function get_settings_sections() {
			$sections = array(
				array(
					'id'    => 'eazydocs_basics',
					'title' => __( 'Settings', 'eazydocs' )
				)
			);

			return $sections;
		}

		/**
		 * Returns all the settings fields
		 *
		 * @return array settings fields
		 */
		function get_settings_fields() {
			$settings_fields = array(
				'eazydocs_basics' => array(
					array(
						'name'    => 'docs_home',
						'label'   => esc_html__( 'Docs Home', 'eazydocs' ),
						'desc'    => sprintf( __( 'Home page for docs page. Preferably use <code>[eazydocs]</code> <a href="%s" target="_blank">shortcode</a> or design your own.', 'eazydocs' ), '#' ),
						'type'    => 'select',
						'default' => 'no',
						'options' => $this->get_pages()
					),
					array(
						'name'  => 'email_feedback',
						'label' => esc_html__( 'Email feedback', 'eazydocs' ),
						'desc'  => esc_html__( 'Enable Email feedback form', 'eazydocs' ),
						'type'  => 'checkbox',
						'default' => 'on'
					),
					array(
						'name'        => 'email_address',
						'label'       => esc_html__( 'Email Address', 'eazydocs' ),
						'desc'        => esc_html__( 'The email address where the feedbacks should sent to', 'eazydocs' ),
						'placeholder' => esc_html__( 'Text Input placeholder', 'eazydocs' ),
						'type'        => 'text',
						'default'     => get_option( 'admin_email' ),
					),
					array(
						'name'    => 'helpful_feedback',
						'label'   => esc_html__( 'Helpful feedback', 'eazydocs' ),
						'desc'    => esc_html__( 'Enable helpful feedback links', 'eazydocs' ),
						'type'    => 'checkbox',
						'default' => 'on'
					),
					array(
						'name'  => 'comment_visibility',
						'label' => esc_html__( 'Comments', 'eazydocs' ),
						'desc'  => esc_html__( 'Allow Comments', 'eazydocs' ),
						'type'  => 'checkbox'
					),
					array(
						'name'  => 'article_print',
						'label' => esc_html__( 'Print article', 'eazydocs' ),
						'desc'  => esc_html__( 'Enable article printing', 'eazydocs' ),
						'type'  => 'checkbox'
					)
				)
			);

			return $settings_fields;
		}

		function plugin_page() {
			echo '<div class="wrap">';

			$this->settings_api->show_navigation();
			$this->settings_api->show_forms();

			echo '</div>';
		}

		/**
		 * Get all the pages
		 *
		 * @return array page names with key value pairs
		 */
		public function get_pages() {
			$pages_options = [ '' => __( '&mdash; Select Page &mdash;', 'eazydocs' ) ];
			$pages         = get_pages( [
				'numberposts' => - 1,
			] );

			if ( $pages ) {
				foreach ( $pages as $page ) {
					$pages_options[ $page->ID ] = $page->post_title;
				}
			}

			return $pages_options;
		}

	}
endif;
