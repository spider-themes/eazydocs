<?php

namespace EazyDocs\Admin;

/**
 * Cannot access directly.
 */
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

// Include admin helper functions
require_once __DIR__ . '/admin-helpers.php';

/**
 * Class Admin
 *
 * @package EazyDocs\Admin
 */
class Admin {
	/**
	 * Admin constructor.
	 */
	function __construct() {
		add_action( 'admin_menu', [ $this, 'eazyDocs_menu' ] );
		add_filter( 'admin_body_class', [ $this, 'body_class' ] );
		add_filter( 'get_edit_post_link', [ $this, 'one_page_docs_edit_content' ], 10, 3 );

		add_action( 'wp_ajax_eaz_nestable_docs', [ $this, 'nestable_callback' ] );
		add_action( 'wp_ajax_eaz_parent_nestable_docs', [ $this, 'parent_nestable_callback' ] );
		add_action( 'wp_ajax_ezd_install_advanced_accordion', [ $this, 'install_advanced_accordion' ] );
		add_action( 'wp_ajax_install_plugin', [ $this, 'install_plugin_ajax' ] );
		add_filter( 'display_post_states', [ $this, 'ezd_post_states' ], 10, 2 );
	}

	/**
	 * Register Menu
	 */
	public function eazyDocs_menu() {
		$capabilites    	= 'manage_options';
		$cz_capabilites 	= 'manage_options';
		$sz_capabilites 	= 'manage_options';
		$is_customizer 		= ezd_get_opt('customizer_visibility');

		$user_id           	= get_current_user_id(); // get the current user's ID
		$user              	= get_userdata( $user_id );
		$current_user_role 	= '';
		$default_roles     	= [ 'administrator', 'editor', 'author', 'contributor', 'subscriber' ];

		$current_rols 		= $user->caps;
		$current_rols 		= array_keys( $current_rols );
		$matched_roles     	= array_intersect( $default_roles, $current_rols );
		$current_user_role 	= reset( $matched_roles );

		$access    = ezd_get_opt( 'docs-write-access', 'administrator' );
		$sz_access = ezd_get_opt( 'settings-edit-access', 'manage_options' );

		$all_roles = '';
		$sz_roles  = '';

		if ( is_array( $access ) ) {
			$all_roles = ! empty( $access ) ? implode( ',', $access ) : '';
		}
		$all_roled = explode( ',', $all_roles );

		if ( is_array( $sz_access ) ) {
			$sz_roles = ! empty( $sz_access ) ? implode( ',', $sz_access ) : '';
		}
		$sz_roled = explode( ',', $sz_roles );

		if ( ! function_exists( 'wp_get_current_user' ) ) {
			include( ABSPATH . "wp-includes/pluggable.php" );
		}

		if ( in_array( $current_user_role, $all_roled ) ) {
			switch ( $current_user_role ) {
				case 'administrator':
					$capabilites = 'manage_options';
					break;

				case 'editor':
					$capabilites = 'publish_pages';
					break;

				case 'author':
					$capabilites = 'publish_posts';
					break;

				case 'contributor':
					$capabilites = 'edit_posts';
					break;

				case 'subscriber':
					$capabilites = 'read';
					break;
			}
		} else {
			$capabilites = 'manage_options';
		}
		
		$ezd_menu_title = ezd_get_opt( 'docs_menu_title' ) ?: ( class_exists( 'EZD_EazyDocsPro' ) ? esc_html__( 'EazyDocs Pro', 'eazydocs' ) : esc_html__( 'EazyDocs', 'eazydocs' ) );
		
		add_menu_page( $ezd_menu_title, $ezd_menu_title, $capabilites, 'eazydocs', [ $this, 'eazydocs_page' ], 'dashicons-media-document', 10 );
		add_submenu_page( 'eazydocs', esc_html__( 'Docs Builder', 'eazydocs' ), esc_html__( 'Docs Builder', 'eazydocs' ), $capabilites, 'eazydocs' );

		if ( ezd_is_premium() ) {
			if ( $is_customizer ) {
				add_submenu_page( 'eazydocs', esc_html__( 'Customize', 'eazydocs' ), esc_html__( 'Customize', 'eazydocs' ), 'manage_options', '/customize.php?autofocus[panel]=docs-page&autofocus[section]=docs-archive-page' );
			}
		}

		add_submenu_page( 'eazydocs', esc_html__( 'Tags', 'eazydocs' ), esc_html__( 'Tags', 'eazydocs' ), 'manage_options', '/edit-tags.php?taxonomy=doc_tag&post_type=docs' );

		$current_theme = get_template();
		if ( $current_theme == 'docy' || $current_theme == 'docly' || ezd_is_premium() ) {
			add_submenu_page( 'eazydocs', esc_html__( 'OnePage Docs', 'eazydocs' ), esc_html__( 'OnePage Docs', 'eazydocs' ), 'manage_options', '/edit.php?post_type=onepage-docs' );
		} else {
			add_submenu_page( 'eazydocs', esc_html__( 'OnePage Doc', 'eazydocs' ), esc_html__( 'OnePage Doc', 'eazydocs' ), 'manage_options', 'ezd-onepage-presents', [ $this, 'ezd_onepage_presents' ] );
		}

		if ( ezd_is_premium() ) {
			do_action( 'ezd_pro_admin_menu' );
		} else {
			add_submenu_page( 'eazydocs', esc_html__( 'Users Feedback', 'eazydocs' ), esc_html__( 'Users Feedback', 'eazydocs' ), $capabilites, 'ezd-user-feedback', [ $this, 'ezd_feedback_presents' ] );
			add_submenu_page( 'eazydocs', esc_html__( 'Analytics', 'eazydocs' ), esc_html__( 'Analytics', 'eazydocs' ), $capabilites, 'ezd-analytics', [ $this, 'ezd_analytics_presents' ] );
		}

		if ( ! is_plugin_active( 'advanced-accordion-block/advanced-accordion-block.php' ) || ! is_plugin_active( 'advanced-accordion-block-pro/advanced-accordion-block.php' ) ) {
			add_submenu_page( 'eazydocs', esc_html__( 'FAQ Builder', 'eazydocs' ), esc_html__( 'FAQ Builder', 'eazydocs' ), $capabilites, 'ezd-faq-builder', [ $this, 'ezd_faq_builder' ] );
		}

		add_submenu_page( 'eazydocs', esc_html__( 'Setup Wizard', 'eazydocs' ), esc_html__( 'Setup Wizard', 'eazydocs' ), 'manage_options', 'eazydocs-initial-setup', [ $this, 'ezd_setup_wizard' ] );
		
		add_submenu_page( 'eazydocs', esc_html__( 'Migration', 'eazydocs' ), esc_html__( 'Migration', 'eazydocs' ), 'manage_options', 'eazydocs-migration', [ $this, 'ezd_docs_migration' ] );
	}

	/**
	 * Docs page
	 */
	public function eazydocs_page() {
		include __DIR__ . '/admin-template.php';
	}

	/**
	 * @param $classes
	 *
	 * @return string
	 */
	public function body_class( $classes ) {
		$current_theme = get_template();
		$classes       .= ' ' . $current_theme;
		switch ( $current_theme ) {
			case 'docy':
				$classes .= ' ' . trim( get_option( 'docy_purchase_code_status' ) );
				break;
			case 'docly':
				$classes .= ' ' . trim( get_option( 'docly_purchase_code_status' ) );
				break;
			default:
				$classes .= '';
		}

		if ( eaz_fs()->is_paying_or_trial() || ezd_is_premium() ) {
			$classes .= ' ezd-premium';
		}

		if ( eaz_fs()->is_plan( 'promax' ) == "yes" ) {
			$classes .= ' ezd-promax';
		}

		if ( empty( eaz_fs()->is_plan( 'promax' ) ) ) {
			$classes .= ' ezd_no_promax';
		}

		return $classes;
	}

	/**
	 * OnePage Doc Pro Notice
	 *
	 * @return void
	 */
	public function ezd_onepage_presents() {
		?>
        <div class="wrap">
            <div class="ezd-blank_state">
				<?php // PHPCS - No need to escape an SVG image from the Elementor assets/images folder. 
				?>
                <img src="<?php echo esc_url( EAZYDOCS_IMG . '/icon/crown.svg' ); ?>" alt="<?php esc_attr_e( 'crown icon', 'eazydocs' ); ?>" width="250px"/>
                <h3> <?php echo esc_html__( 'Add Your OnePage Doc', 'eazydocs' ); ?> </h3>
                <p class="big-p"> <?php esc_html_e( 'Onepage documentation format will generate all the pages of a Doc as sections in a single page which is scrollable by sections. Visitors can find the all guides on a single page and they can navigate through the different sections very fast.',
						'eazydocs' ); ?> </p>
				<?php // PHPCS - No need to escape a URL. The query arg is sanitized. 
				?>
                <div class="button-inline">
                    <a class="button button-primary ezd-btn ezd-btn-pro btn-lg" href="<?php echo esc_url( admin_url( 'admin.php?page=eazydocs-pricing' ) ); ?>">
						<?php esc_html_e( 'Go Pro', 'eazydocs' ); ?>
                    </a>
                    <a class="button button-secondary ezd-btn btn-lg" target="_blank" href="https://wordpress-plugins.spider-themes.net/eazydocs-pro/doc/rogan-documentation/"
                       title="<?php esc_attr_e( 'View Classic Frontend Demo', 'eazydocs' ); ?>">
						<?php esc_html_e( 'Classic Layout Demo', 'eazydocs' ); ?>
                    </a>
                    <a class="button button-secondary ezd-btn btn-lg" target="_blank" href="https://wordpress-plugins.spider-themes.net/eazydocs-pro/doc/banca-wordpress-theme/"
                       title="<?php esc_attr_e( 'View Fullscreen Frontend Demo', 'eazydocs' ); ?>">
		                <?php esc_html_e( 'Fullscreen Layout Demo', 'eazydocs' ); ?>
                    </a>
                </div>
            </div>
        </div><!-- /.wrap -->
		<?php
	}

	public function ezd_feedback_presents() {
		?>
        <div class="wrap">
            <div class="ezd-blank_state">
				<?php // PHPCS - No need to escape an SVG image from the Elementor assets/images folder. 
				?>
                <img src="<?php echo esc_url( EAZYDOCS_IMG . '/icon/crown.svg' ); ?>" alt="<?php esc_attr_e( 'crown icon', 'eazydocs' ); ?>" width="250px"/>
                <h3 class="title"> <?php echo esc_html__( 'Users Feedback', 'eazydocs' ); ?> </h3>
                <p class="big-p"> <?php esc_html_e( 'You can get the Doc Feedbacks listed in this page to review.', 'eazydocs' ); ?> </p>
                <div class="button-inline">
                    <a class="button button-primary ezd-btn ezd-btn-pro btn-lg" href="<?php echo esc_url( admin_url( 'admin.php?page=eazydocs-pricing' ) ); ?>">
						<?php esc_html_e( 'Get Pro Plan', 'eazydocs' ); ?>
                    </a>
                </div>
            </div>
        </div><!-- /.wrap -->
		<?php
	}

	public function ezd_analytics_presents() {
		?>
        <div class="wrap">
            <div class="ezd-blank_state">
				<?php // PHPCS - No need to escape an SVG image from the Elementor assets/images folder. 
				?>
                <img src="<?php echo esc_url( EAZYDOCS_IMG . '/icon/crown.svg' ); ?>" alt="<?php esc_attr_e( 'crown icon', 'eazydocs' ); ?>" width="250px"/>
                <h3 class="title"> <?php echo esc_html__( 'EazyDocs Analytics', 'eazydocs' ); ?> </h3>
                <p class="big-p"> <?php esc_html_e( 'Analytics page is available in the EazyDocs Premium Promax Plan', 'eazydocs' ); ?> </p>
                <div class="button-inline">
                    <a class="button button-primary ezd-btn ezd-btn-pro btn-lg" href="<?php echo esc_url( admin_url( 'admin.php?page=eazydocs-pricing' ) ); ?>">
						<?php esc_html_e( 'Get Promax Plan', 'eazydocs' ); ?>
                    </a>
                </div>
            </div>
        </div><!-- /.wrap -->
		<?php
	}

	public function ezd_setup_wizard() {
		require_once __DIR__ . '/setup-wizard/setup.php';
	}

	/**
	 * FAQ Builder Page
	 */
	public function ezd_faq_builder() {
		wp_enqueue_style( 'ezd-faq-builder', EAZYDOCS_ASSETS . '/css/admin/faq-builder.css', [], EAZYDOCS_VERSION );
		wp_enqueue_script( 'ezd-faq-builder', EAZYDOCS_ASSETS . '/js/admin/faq-builder.js', [ 'jquery' ], EAZYDOCS_VERSION, true );
		
		// Localize script with necessary data
		wp_localize_script( 'ezd-faq-builder', 'ezd_faq_builder', [
			'nonce' => wp_create_nonce( 'ezd_install_accordion_nonce' ),
			'ajaxurl' => admin_url( 'admin-ajax.php' )
		] );
		
		require_once __DIR__ . '/template/faq-builder.php';
	}

	/**
	 * @param $link
	 * @param $post_ID
	 * @param $content
	 *
	 * @return mixed|string
	 */
	public function one_page_docs_edit_content( $link, $post_ID ) {
		if ( 'onepage-docs' == get_post_type( $post_ID ) ) {
			$is_content = get_post_meta( $post_ID, 'ezd_doc_left_sidebar', true );

			$ezd_doc_layout = get_post_meta( $post_ID, 'ezd_doc_layout', true );
			$doc_layout     = ! empty( $ezd_doc_layout ) ? '&doc_layout=' . $ezd_doc_layout : null;

			$ezd_content_type = get_post_meta( $post_ID, 'ezd_doc_content_type', true );
			$content_type     = ! empty( $ezd_content_type ) ? '&content_type=' . $ezd_content_type : null;

			$is_content   = str_replace( '#', ';hash;', $is_content );
			$is_content   = str_replace( 'style&equals;', 'style@', $is_content );
			$content_null = ! empty( $is_content ) ? '&content=' . $is_content : null;

			$ezd_content_type_right = get_post_meta( $post_ID, 'ezd_doc_content_type_right', true );
			$content_type_right     = ! empty( $ezd_content_type_right ) ? '&content_type_right=' . $ezd_content_type_right : null;

			$ezd_content_right = '';
			if ( $ezd_content_type_right == 'widget_data_right' ) {
				$ezd_content_right = get_post_meta( $post_ID, 'ezd_doc_content_box_right', true );
			} else {
				$ezd_content_right = get_post_meta( $post_ID, 'ezd_doc_content_box_right', true );
			}

			$ezd_content_right  = str_replace( '#', ';hash;', $ezd_content_right );
			$ezd_content_right  = str_replace( 'style&equals;', 'style@', $ezd_content_right );
			$ezd_contents_right = ! empty( $ezd_content_right ) ? '&content_right=' . $ezd_content_right : null;

			$ezd_onepage_nonce = '&_wpnonce=' . wp_create_nonce( $post_ID );
			$link              = $link . $ezd_onepage_nonce . $doc_layout . $content_type . $content_null . $content_type_right . $ezd_contents_right;
		}

		return $link;
	}

	/**
	 ** Nestable Callback function
	 **/
	public function nestable_callback() {
		check_ajax_referer( 'eazydocs-admin-nonce', 'security' );

		if ( ! current_user_can( 'edit_posts' ) ) {
			wp_send_json_error( [ 'message' => 'Insufficient permissions.' ] );
		}

		if ( ! isset( $_POST['data'] ) ) {
			wp_send_json_error( [ 'message' => 'Missing data parameter.' ] );
		}

		$raw_data = stripslashes( $_POST['data'] ); // Keep stripslashes since you're using it in JS
		$nestedArray = json_decode( $raw_data );

		if ( ! is_array( $nestedArray ) ) {
			wp_send_json_error( [ 'message' => 'Invalid data format.' ] );
		}

		$nestedArray = ezd_sanitize_nested_objects( $nestedArray ); // ✅ sanitize all IDs

		$i    = 0;
		$c    = 0;
		$c_of = 0;
		$f_of = 0;

		foreach ( $nestedArray as $value ) {
			$i++;
			wp_update_post( [
				'ID'          => $value->id,
				'menu_order'  => $i,
				'post_parent' => eaz_get_nestable_parent_id( $value->id )
			] );

			if ( is_array( $value->children ) ) {
				foreach ( $value->children as $child ) {
					$c++;
					wp_update_post( [
						'ID'          => $child->id,
						'menu_order'  => $c,
						'post_parent' => $value->id
					] );

					if ( is_array( $child->children ) ) {
						foreach ( $child->children as $of_child ) {
							$c_of++;
							wp_update_post( [
								'ID'          => $of_child->id,
								'menu_order'  => $c_of,
								'post_parent' => $child->id
							] );

							if ( is_array( $of_child->children ) ) {
								foreach ( $of_child->children as $fourth_child ) {
									$f_of++;
									wp_update_post( [
										'ID'          => $fourth_child->id,
										'menu_order'  => $f_of,
										'post_parent' => $of_child->id
									] );
								}
							}
						}
					}
				}
			}
		}

		wp_send_json_success( $nestedArray );
	}
	
	public function parent_nestable_callback() {
		check_ajax_referer( 'eazydocs-admin-nonce', 'security' );

		if ( ! current_user_can( 'edit_posts' ) ) {
			wp_send_json_error( [ 'message' => 'Insufficient permissions.' ] );
		}

		$nestedArray = json_decode( stripslashes( $_POST['data'] ) );
		$msg         = [];
		$i           = 0;
		foreach ( $nestedArray as $value ) {
			$i ++;
			$msg = $value->id;
			wp_update_post( [
				'ID'         => $value->id,
				'menu_order' => $i,
			], true );
		}

		wp_send_json_success( $msg );
	}
	
	/**
	 * Docs page
	 */
	public function ezd_docs_migration() {
		include __DIR__ . '/migration.php';
	}

	/**
	 * Install Advanced Accordion Block via AJAX
	 */
	public function install_advanced_accordion() {
		// Log the request for debugging
		error_log('EazyDocs: install_advanced_accordion called');
		
		check_ajax_referer( 'ezd_install_accordion_nonce', '_ajax_nonce' );

		if ( ! current_user_can( 'install_plugins' ) ) {
			error_log('EazyDocs: User does not have install_plugins capability');
			wp_send_json_error( [ 'message' => __( 'You do not have permission to install plugins.', 'eazydocs' ) ] );
		}

		// Check if plugin is already active
		if ( is_plugin_active( 'advanced-accordion-block/advanced-accordion-block.php' ) ) {
			error_log('EazyDocs: Plugin is already active');
			wp_send_json_success( [ 'message' => __( 'Advanced Accordion Block is already installed and active!', 'eazydocs' ) ] );
			return;
		}

		error_log('EazyDocs: Starting plugin installation for advanced-accordion-block');
		$nx_status = $this->install_plugin( 'advanced-accordion-block', 'advanced-accordion-block.php' );
		$this->fail_on_error( $nx_status );

		error_log('EazyDocs: Plugin installation successful');
		wp_send_json_success( [ 'message' => __( 'Advanced Accordion Block has been successfully installed and activated! Start creating beautiful accordions now.', 'eazydocs' ) ] );
	}

	/**
	 * Fail if plugin installation/activation fails
	 *
	 * @param Object $thing
	 *
	 * @return void
	 */
	public function fail_on_error( $thing ) {
		if ( is_wp_error( $thing ) ) {
			wp_send_json_error( [ 'message' => $thing->get_error_message() ] );
		}
	}

	/**
	 * Install and activate a plugin
	 *
	 * @param string $slug
	 * @param string $file
	 *
	 * @return WP_Error|null
	 */
	public function install_plugin( $slug, $file ) {
		include_once ABSPATH . 'wp-admin/includes/plugin-install.php';
		include_once ABSPATH . 'wp-admin/includes/class-wp-upgrader.php';
		include_once ABSPATH . 'wp-admin/includes/class-wp-upgrader-skin.php';
		include_once ABSPATH . 'wp-admin/includes/file.php';

		// Check if we can write to plugins directory
		if ( ! is_writable( WP_PLUGIN_DIR ) ) {
			error_log('EazyDocs: Plugins directory is not writable: ' . WP_PLUGIN_DIR);
			return new WP_Error( 'filesystem_error', __( 'Plugins directory is not writable. Please check file permissions.', 'eazydocs' ) );
		}

		// Initialize filesystem
		WP_Filesystem();
		global $wp_filesystem;

		$plugin_basename = $slug . '/' . $file;

		error_log('EazyDocs: Checking if plugin exists: ' . $plugin_basename);
		
		// if exists and not activated
		if ( file_exists( WP_PLUGIN_DIR . '/' . $plugin_basename ) ) {
			error_log('EazyDocs: Plugin exists, activating...');
			return activate_plugin( $plugin_basename );
		}

		error_log('EazyDocs: Plugin does not exist, downloading and installing...');
		
		// seems like the plugin doesn't exists. Download and activate it
		$upgrader = new Plugin_Upgrader( new Automatic_Upgrader_Skin() );

		$api = plugins_api( 'plugin_information', array( 'slug' => $slug, 'fields' => array( 'sections' => false ) ) );
		
		if ( is_wp_error( $api ) ) {
			error_log('EazyDocs: plugins_api error: ' . $api->get_error_message());
			return $api;
		}
		
		if ( ! isset( $api->download_link ) ) {
			error_log('EazyDocs: No download link found in API response');
			return new WP_Error( 'no_download_link', __( 'No download link found for the plugin.', 'eazydocs' ) );
		}

		error_log('EazyDocs: Download link found: ' . $api->download_link);
		$result = $upgrader->install( $api->download_link );

		if ( is_wp_error( $result ) ) {
			error_log('EazyDocs: Installation failed: ' . $result->get_error_message());
			return $result;
		}

		error_log('EazyDocs: Installation successful, activating plugin...');
		return activate_plugin( $plugin_basename );
	}

	/**
	 * AJAX handler for plugin installation
	 */
	public function install_plugin_ajax() {
		// Verify nonce
		if ( ! wp_verify_nonce( $_POST['nonce'], 'ezd_install_accordion_nonce' ) ) {
			wp_die( __( 'Security check failed', 'eazydocs' ) );
		}

		// Check user capabilities
		if ( ! current_user_can( 'install_plugins' ) ) {
			wp_send_json_error( array( 'message' => __( 'You do not have permission to install plugins.', 'eazydocs' ) ) );
		}

		$plugin_slug = sanitize_text_field( $_POST['plugin_slug'] );
		$plugin_file = sanitize_text_field( $_POST['plugin_file'] );

		if ( empty( $plugin_slug ) || empty( $plugin_file ) ) {
			wp_send_json_error( array( 'message' => __( 'Plugin slug and file are required.', 'eazydocs' ) ) );
		}

		error_log('EazyDocs: Starting AJAX plugin installation for: ' . $plugin_slug . '/' . $plugin_file);

		$result = $this->install_plugin( $plugin_slug, $plugin_file );

		if ( is_wp_error( $result ) ) {
			error_log('EazyDocs: Plugin installation failed: ' . $result->get_error_message());
			wp_send_json_error( array( 'message' => $result->get_error_message() ) );
		} elseif ( $result === true ) {
			error_log('EazyDocs: Plugin installation successful');
			wp_send_json_success( array( 'message' => __( 'Plugin installed and activated successfully!', 'eazydocs' ) ) );
		} else {
			error_log('EazyDocs: Plugin activation failed');
			wp_send_json_error( array( 'message' => __( 'Plugin installed but activation failed.', 'eazydocs' ) ) );
		}
	}

	/**
	 * Post states added in EazyDocs pages
	 */
	public function ezd_post_states( $post_states, $post ) {
		if ( 'page' !== $post->post_type ) {
			return $post_states;
		}

		$docs_slug_page   = ezd_get_opt( 'docs-slug' );
		$login_page       = ezd_get_opt( 'private_doc_login_page' );
		$frontend_login   = ezd_get_opt( 'docs_frontend_login_page' );
		$private_mode     = ezd_get_opt( 'private_doc_mode' );
		$is_contribution  = ezd_get_opt( 'is_doc_contribution' );

		if ( $post->ID == $docs_slug_page ) {
			$post_states['docs_archive'] = __( 'Docs Archive', 'eazydocs' );
		}

		if ( $login_page === $frontend_login && $post->ID == $login_page ) {
			$post_states['docs_login'] = __( 'Docs Access', 'eazydocs' );
		} else {
			if ( $post->ID == $login_page && $private_mode === 'login' ) {
				$post_states['docs_login'] = __( 'Private Access', 'eazydocs' );
			}
			if ( $post->ID == $frontend_login && $is_contribution ) {
				$post_states['docs_collaborator'] = __( 'Collaborator Access', 'eazydocs' );
			}
		}

		return $post_states;
	}
}