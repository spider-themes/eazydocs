<?php
/**
 * Plugin Name: EazyDocs
 * Description: Powerful & beautiful documentation, knowledge base builder plugin.
 * Plugin URI: https://spider-themes.net/eazydocs
 * Author: spider-themes
 * Author URI: https://spider-themes.net/eazydocs
 * Version: 2.7.2
 * Requires at least: 5.0
 * Requires PHP: 7.4
 * Text Domain: eazydocs
 * License: GPL2 or later
 * License URI: https://www.gnu.org/licenses/gpl-2.0.html
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit();
}

if ( function_exists( 'eaz_fs' ) ) {
	eaz_fs()->set_basename( false, __FILE__ );
} else {
	// DO NOT REMOVE THIS IF, IT IS ESSENTIAL FOR THE `function_exists` CALL ABOVE TO PROPERLY WORK.

	if ( ! function_exists( 'eaz_fs' ) ) {

		// Retrieve the Eazydocs settings option
		$opt 			= get_option('eazydocs_settings', []);
		// Check if the setup wizard has been completed (defaulting to an empty string if not set)
		$setup_wizard 	= $opt['setup_wizard_completed'] ?? '';

		// Check if the setup wizard is not completed and ezd_get_setup_wizard option is set
		if ( get_option( 'ezd_get_setup_wizard' ) && ! empty( $setup_wizard ) ) {
			// Remove the activation flag if the setup wizard is not completed
			delete_option( 'ezd_get_setup_wizard' );
		}

		// Create a helper function for easy SDK access.
		function eaz_fs() {
			global $eaz_fs;

			if ( ! isset( $eaz_fs ) ) {
				// Include Freemius SDK.
				require_once dirname( __FILE__ ) . '/vendor/fs/start.php';

				$eaz_fs = fs_dynamic_init(
					[
						'id'                => '10290',
						'slug'              => 'eazydocs',
						'premium_slug'      => 'eazydocs-pro',
						'type'              => 'plugin',
						'public_key'        => 'pk_8474e4208f0893a7b28c04faf5045',
						'is_premium'        => false,
						'is_premium_only'   => false,
						'has_addons'        => false,
						'has_paid_plans'    => true,
						'trial'             => [
							'days'               => 14,
							'is_require_payment' => true,
						],
						'menu'              => [
							'slug'          => 'eazydocs',
							'contact'       => false,
							'support'       => false,
							'first-path'    => get_option('ezd_get_setup_wizard') ? 'admin.php?page=eazydocs-initial-setup' : 'admin.php?page=eazydocs'
						],
                        'parallel_activation' => array(
                                'enabled'                  => true,
                                'premium_version_basename' => 'eazydocs-pro/eazydocs.php',
                        ),
					]
				);
			}

			return $eaz_fs;
		}

		// Add filter to hide the Freemius badge from the plugin page.
		eaz_fs()->add_filter( 'hide_freemius_powered_by', '__return_true' );

		// Signal that SDK was initiated.
		do_action( 'eaz_fs_loaded' );
	}
}


use EazyDocs\Post_Types;

// Make sure the same class is not loaded.
if ( ! class_exists( 'EazyDocs' ) ) {

	require_once __DIR__ . '/vendor/autoload.php';

	/**
	 * Class EazyDocs
	 */
	class EazyDocs {

		// Default constants
		const version = '2.7.2';
		public $plugin_path;
		public $theme_dir_path;
		public static $dir = '';

		/**
		 * Constructor.
		 *
		 * Initialize the EazyDocs plugin
		 *
		 * @access public
		 */
		public function __construct() {
			$this->define_constants();
			// Include core files in action hook.
			$this->core_includes();

			register_activation_hook( __FILE__, [ $this, 'activate' ] );
			add_action( 'init', [ $this, 'init_hooked' ] );
			add_action( 'plugins_loaded', [ $this, 'init_plugin' ] );
			add_action( 'after_setup_theme', [ $this, 'load_csf_files' ], 20 );

			if ( eaz_fs()->is_plan( 'promax' ) ) {
				add_action( 'admin_notices', [ $this, 'update_database' ] );
			}

			// Added Documentation links to plugin row meta
			add_filter('plugin_row_meta',[ $this,  'eazydocs_row_meta' ], 10, 2);

			/**
			 * Removes admin notices on the EazyDocs pages.
			 *
			 * @return void
			 */
			add_action( 'admin_head', function () {
				// Check if the current screen is for your plugin page
				if ( ezd_admin_pages() ) {
					// Remove admin notices
					remove_all_actions( 'admin_notices' );
					remove_all_actions( 'all_admin_notices' );

					// Re-add a specific notice
                    if ( !ezd_is_premium() && ezd_is_plugin_installed_for_days(12) && (!isset($_GET['page']) || $_GET['page'] !== 'eazydocs-initial-setup') ) {
	                    add_action('admin_notices', 'ezd_offer_notice');
                    }
				}
			});

		}

		// get the instance of the EazyDocs class
		public static function get_instance() {
			static $instance = null;

			if ( null === $instance ) {
				$instance = new EazyDocs();
			}

			return $instance;
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
			// Notices
			require_once __DIR__ . '/includes/notices/_notices.php';

			if ( eaz_fs()->is_plan( 'promax' ) ) {
				require_once __DIR__ . '/includes/notices/update-database.php';
			}

			require_once __DIR__ . '/includes/sidebars.php';
			require_once __DIR__ . '/includes/Frontend/Ajax.php';
			require_once __DIR__ . '/includes/Frontend/Mailer.php';
			require_once __DIR__ . '/includes/Post_Types.php';
			require_once __DIR__ . '/includes/One_Page_Docs.php';
			require_once __DIR__ . '/includes/One_Page.php';
			require_once __DIR__ . '/includes/Frontend/Shortcode.php';
			require_once __DIR__ . '/includes/Frontend/post-views.php';
			require_once __DIR__ . '/includes/Frontend/search-counts.php';
			require_once __DIR__ . '/includes/Walker_Docs_Onepage.php';
			require_once __DIR__ . '/includes/Walker_Docs_Onepage_Fullscreen.php';
			require_once __DIR__ . '/includes/Admin/setup-wizard/Plugin_Installer.php';

			if ( ezd_unlock_themes('docy','docly','ama') ) {
				require_once __DIR__ . '/shortcodes/reference.php';
			}
			require_once __DIR__ . '/shortcodes/conditional_data.php';

			// Include and Register the Shortcode for Displaying Single Docs Content
			require_once __DIR__ . '/shortcodes/ezd-view-docs.php';

			if ( ezd_is_premium() ) {
				// Remove docs slug from URLs
				$docs_url 			= ezd_get_opt('docs-url-structure', 'custom-slug');
				$permalink 			= get_option('permalink_structure');

				if ( $docs_url == 'post-name' ) {
					if ( empty ( $permalink == '' || $permalink == '/archives/%post_id%' ) ) {
						require_once __DIR__ . '/includes/Root_Conversion.php';
					}
				}
			}

			// Blocks
			require_once __DIR__ . '/blocks.php';
		}

		/**
		 * Include CSF files include
		 */
		public function load_csf_files(){
			// Load CSF framework (needed on both frontend and admin)
			require __DIR__ . '/vendor/csf/classes/setup.class.php';
			require __DIR__ . '/includes/Admin/options/settings-options.php';
			if ( ezd_is_premium() ) {
				require_once __DIR__ . '/includes/Admin/options/taxonomy-options.php';
			}
		}

		/**
		 * Define constants
		 */
		public function define_constants() {
			if ( ! defined( 'EAZYDOCS_VERSION' ) ) {
				define( 'EAZYDOCS_VERSION', self::version );
			}
			if ( ! defined( 'EAZYDOCS_FILE' ) ) {
				define( 'EAZYDOCS_FILE', __FILE__ );
			}
			if ( ! defined( 'EAZYDOCS_PATH' ) ) {
				define( 'EAZYDOCS_PATH', __DIR__ );
			}
			if ( ! defined( 'EAZYDOCS_URL' ) ) {
				define( 'EAZYDOCS_URL', plugins_url( '', EAZYDOCS_FILE ) );
			}
			if ( ! defined( 'EAZYDOCS_ASSETS' ) ) {
				define( 'EAZYDOCS_ASSETS', EAZYDOCS_URL . '/assets' );
			}
			if ( ! defined( 'EAZYDOCS_FRONT_CSS' ) ) {
				define( 'EAZYDOCS_FRONT_CSS', EAZYDOCS_URL . '/assets/css/frontend' );
			}
			if ( ! defined( 'EAZYDOCS_IMG' ) ) {
				define( 'EAZYDOCS_IMG', EAZYDOCS_URL . '/assets/images' );
			}
			if ( ! defined( 'EAZYDOCS_VEND' ) ) {
				define( 'EAZYDOCS_VEND', EAZYDOCS_URL . '/assets/vendors' );
			}
		}

		/**
		 * Initializes a singleton instances
		 *
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
		 * Initializes the plugin based on the locations
		 *
		 * @return void
		 */
		public function init_plugin() {
			$this->theme_dir_path = apply_filters( 'eazydocs_theme_dir_path', 'eazydocs/' );
			if ( is_admin() ) {
				new EazyDocs\Admin\Admin();
				new EazyDocs\Admin\Create_Post();
				new EazyDocs\Admin\Delete_Post();
				new EazyDocs\Admin\Assets();
				new EazyDocs\One_Page();
				new EazyDocs\Edit_OnePage();
			} elseif ( ! is_admin() ) {
				new EazyDocs\Frontend\Frontend();
				new EazyDocs\Frontend\Assets();
				new EazyDocs\Frontend\Shortcode();
			}
			new EazyDocs\Elementor\Widgets();

			if ( ezd_get_opt( 'is_google_login' ) ) {
				// Load Google Login functionality
				new EazyDocs\Google_Login();
			}
		}

		public function init_hooked() {
			new EazyDocs\Frontend\Ajax();
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
			update_option( 'EazyDocs_version', EAZYDOCS_VERSION );

			// Insert the documentation page into the database if not exists
			if ( ! ezd_get_page_by_title( 'Documentation' ) ) {
				// Create page object
				$current_user_id = get_current_user_id();
				if ( ! $current_user_id ) {
					$current_user_id = 1; // fallback if no user is logged in
				}
				$docs_page = [
					'post_title'   => wp_strip_all_tags( 'Documentation' ),
					'post_content' => '[eazydocs]',
					'post_status'  => 'publish',
					'post_author'  => $current_user_id,
					'post_type'    => 'page',
				];
				wp_insert_post( $docs_page );
			}

			if ( eaz_fs()->is_plan( 'promax' ) ) {
				$this->create_analytics_db_tables();
			}

			// Update the option when the setup wizard is activated
			update_option('ezd_get_setup_wizard', true);

		}

		/**
		 * Create database table if not exists
		 * Insert search keywords table and search key logs table into the database if not exists
		 */
		public function create_analytics_db_tables() {
			global $wpdb;

			$charset_collate   = $wpdb->get_charset_collate();
			$search_keyword    = $wpdb->prefix . 'eazydocs_search_keyword';
			$search_logs       = $wpdb->prefix . 'eazydocs_search_log';
			$view_logs         = $wpdb->prefix . 'eazydocs_view_log';

			// SQL statements to create tables.
			$sql = "CREATE TABLE {$search_keyword} (
				id BIGINT(20) UNSIGNED NOT NULL AUTO_INCREMENT,
				keyword VARCHAR(255) NOT NULL,
				PRIMARY KEY (id)
			) {$charset_collate};";

			$sql2 = "CREATE TABLE {$search_logs} (
				id BIGINT(20) UNSIGNED NOT NULL AUTO_INCREMENT,
				keyword_id BIGINT(20) UNSIGNED NOT NULL,
				count MEDIUMINT(8) UNSIGNED NOT NULL,
				not_found_count MEDIUMINT(8) UNSIGNED NOT NULL,
				created_at DATETIME NOT NULL,
				PRIMARY KEY (id),
				FOREIGN KEY (keyword_id) REFERENCES {$search_keyword}(id) ON DELETE CASCADE
			) {$charset_collate};";

			$sql3 = "CREATE TABLE {$view_logs} (
				id BIGINT(20) UNSIGNED NOT NULL AUTO_INCREMENT,
				post_id BIGINT(20) UNSIGNED NOT NULL,
				count MEDIUMINT(8) UNSIGNED NOT NULL,
				created_at DATETIME NOT NULL,
				PRIMARY KEY (id)
			) {$charset_collate};";

			// Load the required upgrade file.
			require_once ABSPATH . 'wp-admin/includes/upgrade.php';

			// Execute the table creation queries.
			dbDelta( $sql );
			dbDelta( $sql2 );
			dbDelta( $sql3 );
		}

		/**
		 * Database not found
		 */
		function update_database() {
			global $wpdb;
			$table_name = $wpdb->prefix . 'eazydocs_search_keyword';
			$table_name2 = $wpdb->prefix . 'eazydocs_search_log';
			$table_name3 = $wpdb->prefix . 'eazydocs_view_log';

			// Suppress direct query warnings since checking table existence requires direct queries
			// @codingStandardsIgnoreLine WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching
			$keyword_exists = $wpdb->get_var( $wpdb->prepare( "SHOW TABLES LIKE %s", $table_name ) );
			// @codingStandardsIgnoreLine WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching
			$logs_exists = $wpdb->get_var( $wpdb->prepare( "SHOW TABLES LIKE %s", $table_name2 ) );
			// @codingStandardsIgnoreLine WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching
			$view_exists = $wpdb->get_var( $wpdb->prepare( "SHOW TABLES LIKE %s", $table_name3 ) );

			if ( $keyword_exists != $table_name || $logs_exists != $table_name2 || $view_exists != $table_name3 ) {
				?>
                <div class="notice notice-error is-dismissible eazydocs_table_error">
                    <p><?php esc_html_e( 'EazyDocs database needs an update. Please click the Update button to update your database.', 'eazydocs' ); ?></p>
                    <form method="get">
                        <input type="hidden" name="eazydocs_table_create" value="1">
                        <input type="submit" class="button button-primary" value="<?php esc_html_e( 'Update Database', 'eazydocs' ); ?>">
                    </form>
                </div>
				<?php
			}
		}

		/**
		 * Get the plugin url.
		 *
		 * @return string
		 */
		public function plugin_url() {
			if ( $this->plugin_url ) {
				return $this->plugin_url;
			}

			return $this->plugin_url = untrailingslashit( plugins_url( '/', __FILE__ ) );
		}

		/**
		 * Get the plugin path.
		 *
		 * @return string
		 */
		public function plugin_path() {
			if ( $this->plugin_path ) {
				return $this->plugin_path;
			}

			return $this->plugin_path = untrailingslashit( plugin_dir_path( __FILE__ ) );
		}

		/**
		 * Get the template path.
		 *
		 * @return string
		 */
		public function template_path() {
			return $this->plugin_path() . '/templates/';
		}

		/**
		 * Documentation links to plugin row meta
		 */
		public function eazydocs_row_meta($links, $file) {
			// Check if this is your plugin
			if (plugin_basename(__FILE__) === $file) {
				// Add your custom links
				$plugin_links = array(
					'<a href="https://helpdesk.spider-themes.net/docs/eazydocs-wordpress-plugin/" target="_blank">Documentation</a>'
				);
				// Merge the custom links with the existing links
				$links = array_merge($links, $plugin_links);
			}
			return $links;
		}
		// end
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