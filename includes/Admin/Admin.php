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

// Include Antimanual integration notice
require_once __DIR__ . '/AntimanualNotice.php';

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
		add_action( 'admin_menu', [ $this, 'reorder_eazydocs_admin_submenu' ], 999 );
		add_filter( 'admin_body_class', [ $this, 'body_class' ] );
		add_action( 'customize_controls_print_footer_scripts', [ $this, 'body_class' ], 999 );
		add_filter( 'get_edit_post_link', [ $this, 'one_page_docs_edit_content' ], 10, 3 );

		add_action( 'wp_ajax_eaz_nestable_docs', [ $this, 'nestable_callback' ] );
		add_action( 'wp_ajax_eaz_parent_nestable_docs', [ $this, 'parent_nestable_callback' ] );
		add_filter( 'display_post_states', [ $this, 'ezd_post_states' ], 10, 2 );
		
		// Initialize Antimanual notice
		AntimanualNotice::init();
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
		
		// Add Pro badge indicator for menu title
		$menu_title_with_badge = $ezd_menu_title;
		if ( class_exists( 'EZD_EazyDocsPro' ) || ezd_is_premium() ) {
			$menu_title_with_badge = $ezd_menu_title . ' <span class="update-plugins" style="background-color: #00a32a; margin-left: 5px;"><span class="plugin-count" style="background-color: #00a32a;">PRO</span></span>';
		}
		
		add_menu_page( $ezd_menu_title, $menu_title_with_badge, $capabilites, 'eazydocs', [ $this, 'eazydocs_dashboard' ], 'dashicons-media-document', 10 );

		add_submenu_page( 'eazydocs', esc_html__( 'Dashboard', 'eazydocs' ), esc_html__( 'Dashboard', 'eazydocs' ), 'manage_options', 'eazydocs' );
		add_submenu_page( 'eazydocs', esc_html__( 'Docs Builder', 'eazydocs' ), esc_html__( 'Docs Builder', 'eazydocs' ), $capabilites, 'eazydocs-builder', [ $this, 'eazydocs_builder' ] );

		$current_theme = get_template();
		if ( $current_theme == 'docy' || $current_theme == 'docly' || ezd_is_premium() ) {
			add_submenu_page( 'eazydocs', esc_html__( 'OnePage Docs', 'eazydocs' ), esc_html__( 'OnePage Docs', 'eazydocs' ), 'manage_options', '/edit.php?post_type=onepage-docs' );
		} else {
			add_submenu_page( 'eazydocs', esc_html__( 'OnePage Doc', 'eazydocs' ), esc_html__( 'OnePage Doc', 'eazydocs' ), 'manage_options', 'ezd-onepage-presents', [ $this, 'ezd_onepage_presents' ] );
		}

		add_submenu_page( 'eazydocs', esc_html__( 'Tags', 'eazydocs' ), esc_html__( 'Tags', 'eazydocs' ), 'manage_options', '/edit-tags.php?taxonomy=doc_tag&post_type=docs' );

		if ( ezd_is_premium() ) {
			if ( $is_customizer ) {
				add_submenu_page( 'eazydocs', esc_html__( 'Customize', 'eazydocs' ), esc_html__( 'Customize', 'eazydocs' ), 'manage_options', '/customize.php?autofocus[panel]=docs-page&autofocus[section]=docs-archive-page' );
			}
		}

		if ( ezd_is_premium() ) {
			do_action( 'ezd_pro_admin_menu' );
		} else {
			add_submenu_page( 'eazydocs', esc_html__( 'Users Feedback', 'eazydocs' ), esc_html__( 'Users Feedback', 'eazydocs' ), $capabilites, 'ezd-user-feedback', [ $this, 'ezd_feedback_presents' ] );
			add_submenu_page( 'eazydocs', esc_html__( 'Analytics', 'eazydocs' ), esc_html__( 'Analytics', 'eazydocs' ), $capabilites, 'ezd-analytics', [ $this, 'ezd_analytics_presents' ] );
		}
		
		// Only show FAQ Builder menu if neither the free nor pro version of Advanced Accordion Block is active
		if ( ! function_exists( 'is_plugin_active' ) ) {
			include_once ABSPATH . 'wp-admin/includes/plugin.php';
		}
		
		if ( ! is_plugin_active( 'advanced-accordion-block/advanced-accordion-block.php' ) && ! is_plugin_active( 'advanced-accordion-block-pro/advanced-accordion-block.php' ) ) {
			add_submenu_page( 'eazydocs', esc_html__( 'FAQ Builder', 'eazydocs' ), esc_html__( 'FAQ Builder', 'eazydocs' ), $capabilites, 'ezd-faq-builder', [ $this, 'ezd_faq_builder' ] );
		}

		add_submenu_page( 'eazydocs', esc_html__( 'Integrated Themes', 'eazydocs' ), esc_html__( 'Integrated Themes', 'eazydocs' ), 'manage_options', 'ezd-integrated-themes', [ $this, 'ezd_integrated_themes' ] );
		
		add_submenu_page( 'eazydocs', esc_html__( 'Setup Wizard', 'eazydocs' ), esc_html__( 'Setup Wizard', 'eazydocs' ), 'manage_options', 'eazydocs-initial-setup', [ $this, 'ezd_setup_wizard' ] );
		
		add_submenu_page( 'eazydocs', esc_html__( 'Migration', 'eazydocs' ), esc_html__( 'Migration', 'eazydocs' ), 'manage_options', 'eazydocs-migration', [ $this, 'ezd_docs_migration' ] );
	}

	/**
	 * Reorder EazyDocs submenu items into requested groups and insert separators.
	 *
	 * Groups:
	 * 1) Dashboard, Docs Builder, OnePage Docs
	 * 2) Tags, Badges
	 * 3) Users Feedback, Analytics
	 * 4) Settings, Customize, Setup Wizard
	 * 5) Migration, FAQ Builder, Integrated Themes
	 * 6) Rest
	 */
	public function reorder_eazydocs_admin_submenu() {
		global $submenu;

		if ( empty( $submenu['eazydocs'] ) || ! is_array( $submenu['eazydocs'] ) ) {
			return;
		}

		// Remove legacy/bad separators that used '#' and any previously registered separator slugs.
		$original = [];
		foreach ( $submenu['eazydocs'] as $item ) {
			$slug = isset( $item[2] ) ? (string) $item[2] : '';
			$normalized_slug = ltrim( $slug, '/' );

			if ( '#' === $slug || 0 === strpos( $normalized_slug, 'ezd-menu-sep-' ) ) {
				continue;
			}

			$original[] = $item;
		}
		$submenu['eazydocs'] = $original;

		$remaining = $submenu['eazydocs'];

		$take = function ( array $slugs ) use ( &$remaining ) {
			$normalized_targets = array_map(
				function ( $s ) {
					return ltrim( (string) $s, '/' );
				},
				$slugs
			);

			foreach ( $remaining as $key => $item ) {
				$slug = isset( $item[2] ) ? (string) $item[2] : '';
				$normalized_slug = ltrim( $slug, '/' );

				if ( in_array( $normalized_slug, $normalized_targets, true ) ) {
					unset( $remaining[ $key ] );
					return $item;
				}
			}

			return null;
		};

		$group_1 = [];
		if ( $item = $take( [ 'eazydocs' ] ) ) { $group_1[] = $item; }
		if ( $item = $take( [ 'eazydocs-builder' ] ) ) { $group_1[] = $item; }
		if ( $item = $take( [ '/edit.php?post_type=onepage-docs', 'edit.php?post_type=onepage-docs', 'ezd-onepage-presents' ] ) ) { $group_1[] = $item; }

		$group_2 = [];
		if ( $item = $take( [ '/edit-tags.php?taxonomy=doc_tag&post_type=docs', 'edit-tags.php?taxonomy=doc_tag&post_type=docs' ] ) ) { $group_2[] = $item; }
		if ( $item = $take( [ '/edit-tags.php?taxonomy=doc_badge&post_type=docs', 'edit-tags.php?taxonomy=doc_badge&post_type=docs' ] ) ) { $group_2[] = $item; }

		$group_3 = [];
		if ( $item = $take( [ 'ezd-user-feedback' ] ) ) { $group_3[] = $item; }
		if ( $item = $take( [ 'ezd-analytics' ] ) ) { $group_3[] = $item; }

		$group_4 = [];
		if ( $item = $take( [ 'eazydocs-settings' ] ) ) { $group_4[] = $item; }
		if ( $item = $take( [
			'/customize.php?autofocus[panel]=docs-page&autofocus[section]=docs-archive-page',
			'customize.php?autofocus[panel]=docs-page&autofocus[section]=docs-archive-page',
		] ) ) { $group_4[] = $item; }
		if ( $item = $take( [ 'eazydocs-initial-setup' ] ) ) { $group_4[] = $item; }

		$group_5 = [];
		if ( $item = $take( [ 'eazydocs-migration' ] ) ) { $group_5[] = $item; }
		if ( $item = $take( [ 'ezd-faq-builder' ] ) ) { $group_5[] = $item; }
		if ( $item = $take( [ 'ezd-integrated-themes' ] ) ) { $group_5[] = $item; }

		// Group 6: preserve original order for any remaining items.
		$group_6 = [];
		foreach ( $submenu['eazydocs'] as $key => $item ) {
			if ( isset( $remaining[ $key ] ) ) {
				$group_6[] = $remaining[ $key ];
			}
		}

		$groups = [ $group_1, $group_2, $group_3, $group_4, $group_5, $group_6 ];
		$new = [];
		$sep_index = 1;

		for ( $i = 0; $i < count( $groups ); $i++ ) {
			if ( empty( $groups[ $i ] ) ) {
				continue;
			}

			foreach ( $groups[ $i ] as $item ) {
				$new[] = $item;
			}

			$has_next = false;
			for ( $j = $i + 1; $j < count( $groups ); $j++ ) {
				if ( ! empty( $groups[ $j ] ) ) {
					$has_next = true;
					break;
				}
			}

			if ( $has_next ) {
				$sep_slug = 'ezd-menu-sep-' . $sep_index;
				$sep_index++;

				add_submenu_page(
					'eazydocs',
					'',
					'<span class="ezd-menu-separator" aria-hidden="true"></span>',
					'read',
					$sep_slug,
					'__return_null'
				);

				$sep_item = null;
				foreach ( $submenu['eazydocs'] as $candidate ) {
					if ( isset( $candidate[2] ) && $candidate[2] === $sep_slug ) {
						$sep_item = $candidate;
						break;
					}
				}

				if ( ! empty( $sep_item ) ) {
					$new[] = $sep_item;
				}
			}
		}

		$submenu['eazydocs'] = $new;
	}

	/**
	 * Docs page
	 */
	public function eazydocs_builder() {
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

		// If current screen is Customizer;
		if ( doing_action( 'customize_controls_print_footer_scripts' ) ) {
			?>
			<script type="text/javascript">
				document.addEventListener('DOMContentLoaded', function () {
					document.body.className += ' <?php echo esc_js( trim( $classes ) ); ?>';
				});
			</script>
			<?php
			return;
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
		// Enqueue the feedback presentation CSS.
		wp_enqueue_style(
			'ezd-feedback-presentation',
			EAZYDOCS_ASSETS . '/css/feedback-presentation.css',
			[],
			EAZYDOCS_VERSION
		);

		// Include the template file.
		require_once __DIR__ . '/template/feedback-presentation.php';
	}

	public function ezd_analytics_presents() {
		// Enqueue the analytics presentation CSS.
		wp_enqueue_style(
			'ezd-analytics-presentation',
			EAZYDOCS_ASSETS . '/css/analytics-presentation.css',
			[],
			EAZYDOCS_VERSION
		);

		// Include the template file.
		require_once __DIR__ . '/template/analytics-presentation.php';
	}

	public function ezd_setup_wizard() {
		$setup_file = __DIR__ . '/setup-wizard/setup.php';
		if ( file_exists( $setup_file ) ) {
			require_once $setup_file;
		}
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
	 * Integrated Themes Showcase Page
	 */
	public function ezd_integrated_themes() {
		wp_enqueue_style( 'ezd-integrated-themes', EAZYDOCS_ASSETS . '/css/admin/integrated-themes.css', [], EAZYDOCS_VERSION );
		require_once __DIR__ . '/template/integrated-themes.php';
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
	 * Post states added in EazyDocs pages
	 */
	public function ezd_post_states( $post_states, $post ) {
		if ( 'page' !== $post->post_type ) {
			return $post_states;
		}

		$docs_slug_page   = ezd_get_opt( 'docs-slug' );
		$login_page       = ezd_is_premium() ? ezd_get_opt( 'private_doc_login_page' ) : '';
		$frontend_login   = ezd_is_promax() ? ezd_get_opt( 'docs_frontend_login_page' ) : '';
		$private_mode     = ezd_is_premium() ? ezd_get_opt( 'private_doc_mode' ) : 'none';
		$is_contribution  = ezd_is_promax() ? ezd_get_opt( 'is_doc_contribution' ) : false;

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
	
	/**
	 * Dashboard page
	 */
	public function eazydocs_dashboard() {
		include __DIR__ . '/admin-dashboard.php';
	}
}