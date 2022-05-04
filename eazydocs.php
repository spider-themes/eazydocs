<?php
/**
 * Plugin Name: EazyDocs
 * Description: A powerful & beautiful documentation builder plugin.
 * Plugin URI: https://wordpress-theme.spider-themes.net/docy/docs/
 * Author: spider-themes
 * Author URI: #
 * Version: 1.1.0
 * Requires at least: 5.0
 * Requires PHP: 7.2
 * Text Domain: easydocs
 * License: GPL2 or later
 * License URI: https://www.gnu.org/licenses/gpl-2.0.html
 */

use eazyDocs\Post_Types;

if ( ! defined( 'ABSPATH' ) ) {
	exit();
}

if ( ! function_exists( 'eaz_fs' ) ) {
	// Create a helper function for easy SDK access.
	function eaz_fs() {
		global $eaz_fs;

		if ( ! isset( $eaz_fs ) ) {
			// Include Freemius SDK.
			require_once dirname(__FILE__) . '/freemius/start.php';

			$eaz_fs = fs_dynamic_init( array(
				'id'                  => '10290',
				'slug'                => 'eazydocs',
				'premium_slug'        => 'eazydocs-pro',
				'type'                => 'plugin',
				'public_key'          => 'pk_8474e4208f0893a7b28c04faf5045',
				'is_premium'          => false,
				'is_premium_only'     => false,
				'has_addons'          => false,
				'has_paid_plans'      => true,
				'menu'                => array(
					'slug'           => 'eazydocs',
					'first-path'     => 'admin.php?page=eazydocs',
					'contact'        => false,
					'support'        => false,
				),
				// Set the SDK to work in a sandbox mode (for development & testing).
				// IMPORTANT: MAKE SURE TO REMOVE SECRET KEY BEFORE DEPLOYMENT.
				'secret_key'          => 'sk_BWE>Guw~IF@(8czeEjdF->!6MZE+N',
			) );
		}

		return $eaz_fs;
	}

	// Init Freemius.
	eaz_fs();
	// Signal that SDK was initiated.
	do_action( 'eaz_fs_loaded' );
}

// Make sure the same class is not loaded.
if ( ! class_exists( 'EazyDocs' ) ) {

	require_once __DIR__ . '/vendor/autoload.php';

	/**
	 * Class EazyDocs
	 */
	class EazyDocs {

		/**
		 * EazyDocs Version
		 *
		 * Holds the version of the plugin.
		 *
		 * @var string The plugin version.
		 */
		const version = '1.0.0';

		/**
		 * Constructor.
		 *
		 * Initialize the EazyDocs plugin
		 *
		 * @access public
		 */
		public function __construct() {
			$this->define_constants();
			$this->core_includes();

			register_activation_hook( __FILE__, [ $this, 'activate' ] );
			add_action( 'init', [ $this, 'i18n' ] );
			add_action( 'init', [ $this, 'init_hooked' ] );
			add_action( 'plugins_loaded', [ $this, 'init_plugin' ] );
		}

		/**
		 * Load Textdomain
		 *
		 * Load plugin localization files.
		 *
		 * @access public
		 */
		public function i18n() {
			load_plugin_textdomain( 'eazydocs', false, plugin_basename( dirname( __FILE__ ) ) . '/languages' );
		}

		/**
		 * Include Files
		 *
		 * Load core files required to run the plugin.
		 *
		 * @access public
		 */
		public function core_includes() {
			require_once __DIR__ . '/includes/functions.php';
			require_once __DIR__ . '/includes/notices.php';
			require_once __DIR__ . '/includes/Frontend/Ajax.php';
			require_once __DIR__ . '/includes/Frontend/Mailer.php';
			require_once __DIR__ . '/includes/Post_Types.php';
			require_once __DIR__ . '/includes/Frontend/Shortcode.php';
			require_once __DIR__ . '/includes/Frontend/post-views.php';
			require_once __DIR__ . '/vendor/codestar-framework/codestar-framework.php';

			if ( ! class_exists('EazyDocsPro')) {
                require_once __DIR__ . '/includes/Admin/options/customize-docs.php';
				require_once __DIR__ .'/includes/Admin/options/settings-options.php';
			}
		}

		/**
		 * Define constants
		 */
		public function define_constants() {
			define( 'EAZYDOCS_VERSION', self::version );
			define( 'EAZYDOCS_FILE', __FILE__ );
			define( 'EAZYDOCS_PATH', __DIR__ );
			define( 'EAZYDOCS_URL', plugins_url( '', EAZYDOCS_FILE ) );
			define( 'EAZYDOCS_ASSETS', EAZYDOCS_URL . '/assets' );
			define( 'EAZYDOCS_FRONT_CSS', EAZYDOCS_URL . '/assets/css/frontend' );
			define( 'EAZYDOCS_IMG', EAZYDOCS_URL . '/assets/images' );
			define( 'EAZYDOCS_VEND', EAZYDOCS_URL . '/assets/vendors' );
		}

		/**
		 * Initializes a singleton instances
		 * @return void
		 */
		public static function init() {
			static $instance = false;
			if ( ! $instance ) {
				$instance = new self();
			}

			return $instance;
		}

		/**
		 * Initializes the plugin
		 * @return void
		 */
		public function init_plugin() {
			$this->theme_dir_path = apply_filters( 'eazydocs_theme_dir_path', 'eazydocs/' );
			if ( is_admin() ) {
				new eazyDocs\Admin\Admin();
				new eazyDocs\Admin\Create_Post();
				new eazyDocs\Admin\Delete_Post();
				new eazyDocs\Admin\Assets();
			} else {
				new eazyDocs\Frontend\Frontend();
				new eazyDocs\Frontend\Assets();
				new eazyDocs\Frontend\Shortcode();
				new eazyDocs\Frontend\Mailer();
			}
		}

		public function init_hooked() {
			new eazyDocs\Frontend\Ajax();
		}

		/**
		 * Do stuff upon plugin activation
		 */
		public function activate() {
			//Insert the installation time into the database
			$installed = get_option( 'eazyDocs_installed' );
			if ( ! $installed ) {
				update_option( 'eazyDocs_installed', time() );
			}
			update_option( 'EazyDocs_version', 'EAZYDOCS_VERSION' );

			// Insert the documentation page into the database if not exists
			if( ! get_page_by_title( 'Documentation', OBJECT, 'page' ) ){
				// Create page object
				$docs_page = array(
					'post_title'   => wp_strip_all_tags( 'Documentation' ),
					'post_content' => '[eazydocs]',
					'post_status'  => 'publish',
					'post_author'  => 1,
					'post_type'    => 'page',
				);
				wp_insert_post( $docs_page );
			}
		}
	}
}

/**
 * @return EazyDocs|false
 */
if ( ! function_exists( 'eazydocs' ) ) {
	/**
	 * Load eazydocs
	 *
	 * Main instance of eazydocs
	 *
	 */
	function eazydocs() {
		return EazyDocs::init();
	}

	/**
	 * Kick of the plugin
	 */
	eazydocs();
}