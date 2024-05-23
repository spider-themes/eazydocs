<?php
/**
 * Plugin Name: EazyDocs
 * Description: A powerful & beautiful documentation, knowledge base builder plugin.
 * Plugin URI: https://spider-themes.net/eazydocs
 * Author: spider-themes
 * Author URI: https://spider-themes.net/eazydocs
 * Version: 2.4.2
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
		// Create a helper function for easy SDK access.
		function eaz_fs() {
			global $eaz_fs;

			if ( ! isset( $eaz_fs ) ) {
				// Include Freemius SDK.
				require_once dirname( __FILE__ ) . '/includes/fs/start.php';

				$eaz_fs = fs_dynamic_init(
					[
						'id'              => '10290',
						'slug'            => 'eazydocs',
						'premium_slug'    => 'eazydocs-pro',
						'type'            => 'plugin',
						'public_key'      => 'pk_8474e4208f0893a7b28c04faf5045',
						'is_premium'      => false,
						'is_premium_only' => false,
						'has_addons'      => false,
						'has_paid_plans'  => true,
						'trial'           => [
							'days'               => 14,
							'is_require_payment' => true,
						],
						'menu'            => [
							'slug'       => 'eazydocs',
							'contact'    => false,
							'support'    => false,
							'first-path' => 'admin.php?page=eazydocs'
						],
					]
				);
			}

			return $eaz_fs;
		}

		// Init Freemius.
		eaz_fs()->add_filter( 'deactivate_on_activation', '__return_false' );
		eaz_fs()->add_filter( 'hide_freemius_powered_by', '__return_true' );

		// Signal that SDK was initiated.
		do_action( 'eaz_fs_loaded' );
	}
}

use eazyDocs\Post_Types;

// Make sure the same class is not loaded.
if ( ! class_exists( 'EazyDocs' ) ) {

	require_once __DIR__ . '/vendor/autoload.php';

	/**
	 * Class EazyDocs
	 */
	class EazyDocs {

		// Default constants
		const version = '2.4.1';
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
			add_action( 'init', [ $this, 'i18n' ] );
			add_action( 'init', [ $this, 'init_hooked' ] );
			add_action( 'plugins_loaded', [ $this, 'init_plugin' ] );

			if ( eaz_fs()->is_plan( 'promax' ) ) {
				add_action( 'admin_notices', [ $this, 'database_not_found' ] );
			}
			
			// Added Documentation links to plugin row meta
			add_filter('plugin_row_meta',[ $this,  'eazydocs_row_meta' ], 10, 2);
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
			// Notices
			require_once __DIR__ . '/includes/notices/deactivate-other-doc-plugins.php';
			require_once __DIR__ . '/includes/notices/asking-for-review.php';

			if ( eaz_fs()->is_plan( 'promax' ) ) {
				require_once __DIR__ . '/includes/notices/update-database.php';

				// Remove docs slug from URLs
				$docs_url 			=  ezd_get_opt('docs-url-structure', 'custom-slug');
				$permalink 			= get_option('permalink_structure');
		
				if ( $docs_url == 'post-name' ) {					
					if ( empty ( $permalink == '' || $permalink == '/archives/%post_id%' ) ) {					 
						require_once __DIR__ . '/includes/Root_Conversion.php';
					}
				}
			}

			require_once __DIR__ . '/includes/sidebars.php';
			require_once __DIR__ . '/includes/Frontend/Ajax.php';
			require_once __DIR__ . '/includes/Frontend/Mailer.php';
			require_once __DIR__ . '/includes/Post_Types.php';
			require_once __DIR__ . '/includes/One_page_doc_type.php';
			require_once __DIR__ . '/includes/Frontend/Shortcode.php';
			require_once __DIR__ . '/includes/Frontend/post-views.php';
			require_once __DIR__ . '/includes/Frontend/search-counts.php';
			require_once __DIR__ . '/includes/Walker_Docs_Onepage.php';
			require_once __DIR__ . '/includes/Walker_Docs_Onepage_Fullscreen.php';

			// Options
			require_once __DIR__ . '/vendor/csf/classes/setup.class.php';
			require_once __DIR__ . '/includes/Admin/options/settings-options.php';

			if ( ezd_is_premium() ) {
				require_once __DIR__ . '/includes/Admin/options/taxonomy-options.php';
			}

			if ( ezd_unlock_themes() ) {
				require_once __DIR__ . '/shortcodes/reference.php';
				require_once __DIR__ . '/shortcodes/conditional_data.php';
			}

			// Blocks
			require_once __DIR__ . '/blocks.php';
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
				new eazyDocs\Admin\Admin();
				new eazyDocs\Admin\Create_Post();
				new eazyDocs\Admin\Delete_Post();
				new eazyDocs\Admin\Assets();
				new eazyDocs\One_Page();
				new eazyDocs\Edit_OnePage();
			} elseif ( ! is_admin() ) {
				new eazyDocs\Frontend\Frontend();
				new eazyDocs\Frontend\Assets();
				new eazyDocs\Frontend\Shortcode();
			}
			new eazyDocs\Elementor\Widgets();
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
			update_option( 'EazyDocs_version', EAZYDOCS_VERSION );

			// Insert the documentation page into the database if not exists
			if ( ! ezd_get_page_by_title( 'Documentation' ) ) {
				// Create page object
				$docs_page = [
					'post_title'   => wp_strip_all_tags( 'Documentation' ),
					'post_content' => '[eazydocs]',
					'post_status'  => 'publish',
					'post_author'  => 1,
					'post_type'    => 'page',
				];
				wp_insert_post( $docs_page );
			}

            if ( eaz_fs()->is_plan( 'promax' ) ) {
	            $this->create_analytics_db_tables();
            }
		}

		/**
		 * Create database table if not exists
		 * Insert search keywords table and search key logs table into the database if not exists
		 */
		public function create_analytics_db_tables() {
			global $wpdb;

			$charset_collate = $wpdb->get_charset_collate();
			$search_keyword  = $wpdb->prefix . 'eazydocs_search_keyword';
			$search_logs     = $wpdb->prefix . 'eazydocs_search_log';
			$view_logs       = $wpdb->prefix . 'eazydocs_view_log';

			// Use table names directly in SQL since these are not user inputs
			$sql = "CREATE TABLE {$search_keyword} (
                id bigint(9) NOT NULL AUTO_INCREMENT,
                keyword varchar(255) NOT NULL,
                UNIQUE KEY id (id)
            ) {$charset_collate};";

			$sql2 = "CREATE TABLE {$search_logs} (
                id bigint(9) NOT NULL AUTO_INCREMENT,
                keyword_id bigint(255) NOT NULL references {$search_keyword}(id), 
                count mediumint(255) NOT NULL,
                not_found_count mediumint(9) NOT NULL,
                created_at datetime NOT NULL,
                UNIQUE KEY id (id)
            ) {$charset_collate};";

			$sql3 = "CREATE TABLE {$view_logs} (
                id bigint(9) NOT NULL AUTO_INCREMENT,
                post_id bigint(255) NOT NULL,
                count mediumint(255) NOT NULL,
                created_at datetime NOT NULL,
                UNIQUE KEY id (id)
            ) {$charset_collate};";

			require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );
			dbDelta( $sql );
			dbDelta( $sql2 );
			dbDelta( $sql3 );

			// Check table exists using WordPress functions instead of raw SQL
			if ( $wpdb->get_var( $wpdb->prepare( "SHOW TABLES LIKE %s", $search_keyword ) ) !== $search_keyword
			     || $wpdb->get_var( $wpdb->prepare( "SHOW TABLES LIKE %s", $search_logs ) ) !== $search_logs
			     || $wpdb->get_var( $wpdb->prepare( "SHOW TABLES LIKE %s", $view_logs ) ) !== $view_logs
			) {
				// Send notification to user
				$this->database_not_found();
			}
		}

		/**
		 * Database not found
		 */
		function database_not_found() {
			global $wpdb;
			$table_name = $wpdb->prefix . 'eazydocs_search_keyword';
			$table_name2 = $wpdb->prefix . 'eazydocs_search_log';
			$table_name3 = $wpdb->prefix . 'eazydocs_view_log';

			// Use $wpdb->prepare to safely include table names in SQL queries
			if ( $wpdb->get_var( $wpdb->prepare( "SHOW TABLES LIKE %s", $table_name ) ) != $table_name
			     || $wpdb->get_var( $wpdb->prepare( "SHOW TABLES LIKE %s", $table_name2 ) ) != $table_name2
			     || $wpdb->get_var( $wpdb->prepare( "SHOW TABLES LIKE %s", $table_name3 ) ) != $table_name3
			) {
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