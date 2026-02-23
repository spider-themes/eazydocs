<?php
/**
 * Plugin Name: EazyDocs
 * Description: Powerful & beautiful documentation, knowledge base builder plugin.
 * Plugin URI: https://eazydocs.spider-themes.net
 * Author: spider-themes
 * Author URI: https://eazydocs.spider-themes.net
 * Version: 2.10.0
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
	// Check setup wizard completion
	$opt = get_option( 'eazydocs_settings', [] );
	if ( get_option( 'ezd_get_setup_wizard' ) && ! empty( $opt['setup_wizard_completed'] ) ) {
		delete_option( 'ezd_get_setup_wizard' );
	}

	// Create SDK helper function
	function eaz_fs() {
		global $eaz_fs;

		if ( ! isset( $eaz_fs ) ) {
			require_once dirname( __FILE__ ) . '/vendor/freemius/wordpress-sdk/start.php';

			$eaz_fs = fs_dynamic_init( [
				'id'                      => '10290',
				'slug'                    => 'eazydocs',
				'premium_slug'            => 'eazydocs-pro',
				'type'                    => 'plugin',
				'public_key'              => 'pk_8474e4208f0893a7b28c04faf5045',
				'is_premium'              => false,
				'is_premium_only'         => false,
				'has_addons'              => false,
				'has_paid_plans'          => true,
				'trial'                   => [ 'days' => 14, 'is_require_payment' => true ],
				'menu'                    => [
					'slug'       => 'eazydocs',
					'contact'    => false,
					'support'    => false,
					'first-path' => get_option( 'ezd_get_setup_wizard' ) ? 'admin.php?page=eazydocs-initial-setup' : 'admin.php?page=eazydocs',
				],
				'parallel_activation'     => [
					'enabled'                  => true,
					'premium_version_basename' => 'eazydocs-pro/eazydocs.php',
				],
			] );
		}

		return $eaz_fs;
	}

	eaz_fs()->add_filter( 'hide_freemius_powered_by', '__return_true' );
	do_action( 'eaz_fs_loaded' );
}


if ( ! class_exists( 'EazyDocs' ) ) {
	require_once __DIR__ . '/vendor/autoload.php';

	class EazyDocs {

		// Default constants
		const version = '2.10.0';
		public $plugin_path;
		public $theme_dir_path;

		public function __construct() {
			$this->define_constants();
			$this->core_includes();

			register_activation_hook( __FILE__, [ $this, 'activate' ] );
			add_action( 'init', [ $this, 'init_hooked' ] );
			add_action( 'plugins_loaded', [ $this, 'init_plugin' ] );
			add_action( 'after_setup_theme', [ $this, 'load_csf_files' ], 20 );
			add_action( 'admin_notices', [ $this, 'update_database' ] );
			add_filter( 'plugin_row_meta', [ $this, 'eazydocs_row_meta' ], 10, 2 );

			add_action( 'admin_head', function () {
				// Check if we're on any EazyDocs admin page, taxonomy page, or post type page
				$is_ezd_page = ezd_admin_pages() || ezd_admin_taxonomy() || ezd_admin_post_types();

				if ( $is_ezd_page ) {
					remove_all_actions( 'admin_notices' );
					remove_all_actions( 'all_admin_notices' );

					// Allow the Antimanual integration notice on EazyDocs pages.
					if ( class_exists( '\EazyDocs\Admin\AntimanualNotice' ) ) {
						\EazyDocs\Admin\AntimanualNotice::init();
					}

					$is_dev_mode = defined( 'DEVELOPER_MODE' ) && DEVELOPER_MODE;
					if ( $is_dev_mode || ( ! ezd_is_premium() && ezd_is_plugin_installed_for_days( 12 ) && ( ! isset( $_GET['page'] ) || 'eazydocs-initial-setup' !== $_GET['page'] ) ) ) {
						add_action( 'admin_notices', 'ezd_offer_notice' );
					}

					if ( function_exists( 'ezd_gutenberg_info_notice' ) ) {
						add_action( 'admin_notices', 'ezd_gutenberg_info_notice' );
					}
				}
			});
		}

		public static function get_instance() {
			static $instance = null;
			return $instance ?: ( $instance = new self() );
		}

		public function core_includes() {
			require_once __DIR__ . '/includes/functions.php';
			require_once __DIR__ . '/includes/notices/_notices.php';
			require_once __DIR__ . '/includes/notices/update-database.php';
			
			$core_files = [
				'/includes/sidebars.php',
				'/includes/Frontend/Ajax.php',
				'/includes/Frontend/Mailer.php',
				'/includes/Post_Types.php',
				'/includes/One_Page_Docs.php',
				'/includes/One_Page.php',
				'/includes/Frontend/Shortcode.php',
				'/includes/Frontend/post-views.php',
				'/includes/Frontend/search-counts.php',
				'/includes/Walker_Docs_Onepage.php',
				'/includes/Walker_Docs_Onepage_Fullscreen.php',
				'/includes/Admin/setup-wizard/Plugin_Installer.php',
			];

			foreach ( $core_files as $file ) {
				require_once __DIR__ . $file;
			}

			if ( ezd_unlock_themes( 'docy', 'docly', 'ama' ) ) {
				require_once __DIR__ . '/shortcodes/reference.php';
			}

			require_once __DIR__ . '/shortcodes/conditional_data.php';
			require_once __DIR__ . '/shortcodes/ezd-view-docs.php';

			if ( ezd_is_premium() ) {
				$docs_url   = ezd_get_opt( 'docs-url-structure', 'custom-slug' );
				$permalink  = get_option( 'permalink_structure' );

				if ( 'post-name' === $docs_url && ! empty( $permalink ) && '/archives/%post_id%' !== $permalink ) {
					require_once __DIR__ . '/includes/Root_Conversion.php';
				}
			}

			require_once __DIR__ . '/blocks.php';
		}

		/**
		 * Include CSF files include
		 */
		public function load_csf_files(){
			// Load CSF framework (needed on both frontend and admin)
			require __DIR__ . '/includes/csf/classes/setup.class.php';
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

		public static function init() {
			static $instance = false;
			return $instance ?: ( $instance = new self() );
		}

		/**
		 * Initialize the plugin
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

		/**
		 * Initialize hooked classes
		 */
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
					'post_content' => '<!-- wp:eazydocs-pro/eazy-docs {"docTypes":"multi-doc","docPreset":"box","docSinglePreset":"box","docId":"2484"} /-->',
					'post_status'  => 'publish',
					'post_author'  => $current_user_id,
					'post_type'    => 'page',
				];
				wp_insert_post( $docs_page );
			}
			
			$this->create_analytics_db_tables();
			
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
			id bigint(20) unsigned not null auto_increment,
			keyword varchar(255) not null,
			PRIMARY KEY (id)
		) {$charset_collate};";

		$sql2 = "CREATE TABLE {$search_logs} (
			id bigint(20) unsigned not null auto_increment,
			keyword_id bigint(20) unsigned not null,
			count mediumint(8) unsigned not null,
			not_found_count mediumint(8) unsigned not null,
			created_at datetime not null,
			PRIMARY KEY (id),
			FOREIGN KEY (keyword_id) REFERENCES {$search_keyword}(id) ON DELETE CASCADE
		) {$charset_collate};";

		$sql3 = "CREATE TABLE {$view_logs} (
			id bigint(20) unsigned not null auto_increment,
			post_id bigint(20) unsigned not null,
			count mediumint(8) unsigned not null,
			created_at datetime not null,
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

			if ( $table_name !== $keyword_exists || $table_name2 !== $logs_exists || $table_name3 !== $view_exists ) {
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
		 * Get the plugin URL.
		 *
		 * @return string
		 */
		public function plugin_url() {
			return $this->plugin_url ?: ( $this->plugin_url = untrailingslashit( plugins_url( '/', __FILE__ ) ) );
		}

		/**
		 * Get the plugin path.
		 *
		 * @return string
		 */
		public function plugin_path() {
			return $this->plugin_path ?: ( $this->plugin_path = untrailingslashit( plugin_dir_path( __FILE__ ) ) );
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
		public function eazydocs_row_meta( $links, $file ) {
			if ( plugin_basename( __FILE__ ) === $file ) {
				$links[] = '<a href="https://helpdesk.spider-themes.net/docs/eazydocs-wordpress-plugin/" target="_blank">Documentation</a>';
				$links[] = '<a href="' . admin_url( 'admin.php?page=eazydocs' ) . '">Dashboard</a>';
			}
			return $links;
		}
	}
}

if ( ! function_exists( 'eazydocs' ) ) {
	function eazydocs() {
		return EazyDocs::init();
	}

	eazydocs();
}