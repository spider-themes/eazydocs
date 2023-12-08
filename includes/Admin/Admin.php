<?php
namespace eazyDocs\Admin;

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

		add_filter( 'admin_body_class', [ $this, 'admin_body_class' ] );
		add_action( 'wp_ajax_eaz_nestable_docs', [ $this, 'nestable_callback' ] );
		add_action( 'wp_ajax_eaz_parent_nestable_docs', [ $this, 'parent_nestable_callback' ] );
	}

	/**
	 * Register Menu
	 */
	public function eazyDocs_menu() {
		$capabilites    	= 'manage_options';
		$cz_capabilites 	= 'manage_options';
		$sz_capabilites 	= 'manage_options';

		$ezd_options   		= get_option( 'eazydocs_settings' );
		$is_customizer 		= $ezd_options['customizer_visibility'] ?? 'disabled';
 
		$user_id 			= get_current_user_id(); // get the current user's ID
		$user 				= get_userdata( $user_id );
		$current_user_role 	= '';
		$default_roles 		= ['administrator', 'editor', 'author', 'contributor', 'subscriber'];	 

		$current_rols 		= $user->caps;
		$current_rols 		= array_keys($current_rols);

		$matched_roles 		= array_intersect($default_roles, $current_rols);		
		$current_user_role 	= reset($matched_roles);


        $access    = ezd_get_opt( 'docs-write-access', 'eazydocs_settings' );
        $cz_access = ezd_get_opt( 'customizer-edit-access', 'eazydocs_settings' );
        $sz_access = ezd_get_opt( 'settings-edit-access', 'eazydocs_settings' );

        $all_roles = '';
        $cz_roles  = '';
        $sz_roles  = '';

        if ( is_array( $access ) ) {
            $all_roles = ! empty( $access ) ? implode( ',', $access ) : '';
        }
        $all_roled = explode( ',', $all_roles );

        if ( is_array( $cz_access ) ) {
            $cz_roles = ! empty( $cz_access ) ? implode( ',', $cz_access ) : '';
        }

        $cz_roled = explode( ',', $cz_roles );

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


		if ( class_exists('EazyDocsPro') ) {
			$ezd_menu_title = __( 'EazyDocs Pro', 'eazydocs' );
		} else {
			$ezd_menu_title = __( 'EazyDocs', 'eazydocs' );
		}

		add_menu_page( $ezd_menu_title, $ezd_menu_title, $capabilites, 'eazydocs', [ $this, 'eazydocs_page' ],
			'dashicons-media-document', 10 );
		add_submenu_page( 'eazydocs', __( 'Docs Builder', 'eazydocs' ), __( 'Docs Builder', 'eazydocs' ), $capabilites, 'eazydocs' );

		if ( ezd_is_premium() ) {
			if ( in_array( $current_user_role, $cz_roled ) ) {
				switch ( $current_user_role ) {
					case 'administrator':
						$cz_capabilites = 'manage_options';
						break;

					case 'editor':
						$cz_capabilites = 'publish_pages';
						break;

					case 'author':
						$cz_capabilites = 'publish_posts';
						break;
				}
			} else {
				$cz_capabilites = 'manage_options';
			}
			if ( $is_customizer == 'enable' ) {
				add_submenu_page( 'eazydocs', __( 'Customize', 'eazydocs' ), __( 'Customize', 'eazydocs' ), $cz_capabilites,
					'/customize.php?autofocus[panel]=docs-page&autofocus[section]=docs-archive-page' );
			}
		}

		add_submenu_page( 'eazydocs', __( 'Tags', 'eazydocs' ), __( 'Tags', 'eazydocs' ), 'manage_options', '/edit-tags.php?taxonomy=doc_tag&post_type=docs' );

		$current_theme = get_template();
		if ( $current_theme == 'docy' || $current_theme == 'docly' || ezd_is_premium() ) {
			add_submenu_page( 'eazydocs', __( 'OnePage Docs', 'eazydocs' ), __( 'OnePage Docs', 'eazydocs' ), 'manage_options',
				'/edit.php?post_type=onepage-docs' );
		} else {
			add_submenu_page( 'eazydocs', __( 'OnePage Doc', 'eazydocs' ), __( 'OnePage Doc', 'eazydocs' ), 'manage_options', 'ezd-onepage-presents',
				[ $this, 'ezd_onepage_presents' ] );
		}

		if ( ezd_is_premium() ) {
			do_action( 'ezd_pro_admin_menu' );
		} else {
			add_submenu_page( 'eazydocs', __( 'Users Feedback', 'eazydocs' ), __( 'Users Feedback', 'eazydocs' ), $capabilites, 'ezd-user-feedback',
				[ $this, 'ezd_feedback_presents' ] );
			add_submenu_page( 'eazydocs', __( 'Analytics', 'eazydocs' ), __( 'Analytics', 'eazydocs' ), $capabilites, 'ezd-analytics',
				[ $this, 'ezd_analytics_presents' ] );
		}
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

		if ( eaz_fs()->is_paying_or_trial() || eaz_fs()->is_premium() ) {
			$classes .= ' ezd-premium';
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
                <img src="<?php echo EAZYDOCS_IMG . '/icon/crown.svg'; ?>" alt="<?php esc_attr_e( 'crown icon', 'eazydocs' ); ?>" width="250px"/>
                <h3> <?php echo esc_html__( 'Add Your OnePage Doc', 'eazydocs' ); ?> </h3>
                <p class="big-p"> <?php esc_html_e( 'Onepage documentation format will generate all the pages of a Doc as sections in a single page which is scrollable by sections. Visitors can find the all guides on a single page and they can navigate through the different sections very fast.',
						'eazydocs' ); ?> </p>
				<?php // PHPCS - No need to escape a URL. The query arg is sanitized. 
				?>
                <div class="button-inline">
                    <a class="button button-primary ezd-btn ezd-btn-pro btn-lg" href="<?php echo admin_url( 'admin.php?page=eazydocs-pricing' ); ?>">
						<?php esc_html_e( 'Go Pro', 'eazydocs' ); ?>
                    </a>
                    <a class="button button-secondary ezd-btn btn-lg" target="_blank" href="https://wordpress-theme.spider-themes.net/docy/docy-documentation/"
                       title="<?php esc_attr_e( 'View Frontend Demo', 'eazydocs' ); ?>">
						<?php esc_html_e( 'View Demo', 'eazydocs' ); ?>
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
                <img src="<?php echo EAZYDOCS_IMG . '/icon/crown.svg'; ?>" alt="<?php esc_attr_e( 'crown icon', 'eazydocs' ); ?>" width="250px"/>
                <h3 class="title"> <?php echo esc_html__( 'Users Feedback', 'eazydocs' ); ?> </h3>
                <p class="big-p"> <?php esc_html_e( 'You can get the Doc Feedbacks listed in this page to review.', 'eazydocs' ); ?> </p>
                <div class="button-inline">
                    <a class="button button-primary ezd-btn ezd-btn-pro btn-lg" href="<?php echo admin_url( 'admin.php?page=eazydocs-pricing' ); ?>">
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
                <img src="<?php echo EAZYDOCS_IMG . '/icon/crown.svg'; ?>" alt="<?php esc_attr_e( 'crown icon', 'eazydocs' ); ?>" width="250px"/>
                <h3 class="title"> <?php echo esc_html__( 'EazyDocs Analytics', 'eazydocs' ); ?> </h3>
                <p class="big-p"> <?php esc_html_e( 'Analytics page is available in the EazyDocs Premium Promax Plan', 'eazydocs' ); ?> </p>
                <div class="button-inline">
                    <a class="button button-primary ezd-btn ezd-btn-pro btn-lg" href="<?php echo admin_url( 'admin.php?page=eazydocs-pricing' ); ?>">
						<?php esc_html_e( 'Get Promax Plan', 'eazydocs' ); ?>
                    </a>
                </div>
            </div>
        </div><!-- /.wrap -->
		<?php
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

			$ezd_onepage_nonce	= '&_wpnonce='.wp_create_nonce($post_ID);
			$link               = $link . $ezd_onepage_nonce . $doc_layout . $content_type . $content_null . $content_type_right . $ezd_contents_right;
		}

		return $link;
	}

	public function admin_body_class( $admin_body ) {
		$ezd_admin_classe = explode( ' ', $admin_body );
		if ( empty( eaz_fs()->is_plan( 'promax' ) ) ) {
			$ezd_admin_classe = array_merge( $ezd_admin_classe, [
				'ezd_no_promax'
			] );
		}

		return implode( ' ', array_unique( $ezd_admin_classe ) );
	}

	/**
	 ** Nestable Callback function
	 **/
	public function nestable_callback() {
		$nestedArray = json_decode( stripslashes( $_POST['data'] ) );
		$i           = 0;
		$c           = 0;
		$c_of        = 0;
		foreach ( $nestedArray as $value ) {
			$i ++;
			wp_update_post( [
				'ID'          => $value->id,
				'menu_order'  => $i,
				'post_parent' => eaz_get_nestable_parent_id( $value->id )
			], true );

			if ( is_array( $value->children ) ) {
				foreach ( $value->children as $child ) {
					$c ++;
					wp_update_post( [
						'ID'          => $child->id,
						'menu_order'  => $c,
						'post_parent' => $value->id
					], true );
					if ( is_array( $child->children ) ) {
						foreach ( $child->children as $of_child ) {
							$c_of ++;
							wp_update_post(
								[
									'ID'          => $of_child->id,
									'menu_order'  => $c_of,
									'post_parent' => $child->id
								],
								true
							);
						}
					}
				}
			}
		}

		wp_send_json_success( $nestedArray );
	}

	public function parent_nestable_callback() {
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
}