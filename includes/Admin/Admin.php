<?php
namespace eazyDocs\Admin;

/**
 * Class Admin
 * @package EazyDocs\Admin
 */
class Admin {

	/**
	 * Admin constructor.
	 */
	function __construct() {
		add_action( 'admin_menu', [ $this, 'eazyDocs_menu' ] );
        add_filter( 'admin_body_class', [ $this, 'body_class' ] );
		add_filter( 'get_edit_post_link', [$this, 'one_page_docs_edit_content'], 10, 3 );

		$optionReview = get_option('ezd_notify_review');
		if (time() >= (int)$optionReview && $optionReview !== '0'){
			add_action('admin_notices', array($this, 'ezd_notify_give_review'));
		}
		add_action('wp_ajax_ezd_notify_save_review', array($this, 'ezd_notify_save_review'));
		add_filter('admin_body_class', [$this, 'ezd_admin_body_class']);
	}
	
	/**
	 * Register Menu
	 */
	public function eazyDocs_menu() {

		$capabilites    = 'manage_options';
		$cz_capabilites = 'manage_options';
		$sz_capabilites = 'manage_options';

		if ( function_exists( 'eazydocspro_get_option' ) ) {

			$access    = eazydocspro_get_option( 'docs-write-access', 'eazydocs_settings' );
			$cz_access = eazydocspro_get_option( 'customizer-edit-access', 'eazydocs_settings' );
			$sz_access = eazydocspro_get_option( 'settings-edit-access', 'eazydocs_settings' );

			$all_roles = '';
			$cz_roles = '';
			$sz_roles = '';

			if( is_array($access)) {
				$all_roles = ! empty( $access ) ? implode( ',', $access ) : '';
			}
			if( is_array($cz_roles)) {
				$cz_roles  = ! empty( $cz_access ) ? implode( ',', $cz_access ) : '';
			}
			if( is_array($sz_roles)) {
				$sz_roles  = ! empty( $sz_access ) ? implode( ',', $sz_access ) : '';
			}

			$cz_roled = explode( ',', $cz_roles );
			$sz_roled = explode( ',', $sz_roles );

			$user         = wp_get_current_user();
			$current_user = $user->roles[0] ?? '';
			$all_roled    = explode( ',', $all_roles );
			if ( in_array( $current_user, $all_roled ) ) {
				switch ( $current_user ) {
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
		}

		add_menu_page( __( 'EazyDocs', 'eazyDocs' ), __( 'EazyDocs', 'eazyDocs' ), $capabilites, 'eazydocs', [ $this, 'eazydocs_page' ], 'dashicons-media-document', 10 );

		if ( class_exists( 'EazyDocsPro' ) ) {
			if ( in_array( $current_user, $cz_roled ) ) {
				switch ( $current_user ) {
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

			add_submenu_page( 'eazydocs', __( 'Customize', 'eazydocs' ), __( 'Customize', 'eazydocs' ), $cz_capabilites, '/customize.php?autofocus[panel]=docs-page&autofocus[section]=docs-archive-page' );

			if ( in_array( $current_user, $sz_roled ) ) {
				switch ( $current_user ) {
					case 'administrator':
						$sz_capabilites = 'manage_options';
						break;

					case 'editor':
						$sz_capabilites = 'publish_pages';
						break;

					case 'author':
						$sz_capabilites = 'publish_posts';
						break;
				}
			} else {
				$sz_capabilites = 'manage_options';
			}

			add_submenu_page( 'eazydocs', __( 'Settings', 'eazydocs' ), __( 'Settings', 'eazydocs' ), $sz_capabilites, 'eazydocs-settings' );
		}else{
			add_submenu_page( 'eazydocs', __( 'Customize', 'eazydocs' ), __( 'Customize', 'eazydocs' ), 'manage_options', '/customize.php?autofocus[panel]=docs-page&autofocus[section]=docs-archive-page' );
		}
		$current_theme = get_template();
		if ( $current_theme == 'docy' || $current_theme == 'docly' || class_exists('EazyDocsPro')) {
			add_submenu_page( 'eazydocs', __( 'OnePage Docs', 'eazydocs' ), __( 'OnePage Docs', 'eazydocs' ), 'manage_options', '/edit.php?post_type=onepage-docs' );
		}else{
			add_submenu_page( 'eazydocs', __( 'OnePage Doc', 'eazydocs' ), __( 'OnePage Doc', 'eazydocs' ), 'manage_options', 'ezd-onepage-presents', [$this, 'ezd_onepage_presents'] );
		}

		if ( class_exists('EazyDocsPro')) {
			do_action('ezd_pro_admin_menu');
		}else{
			add_submenu_page( 'eazydocs', __( 'Users Feedback', 'eazydocs' ), __( 'Users Feedback', 'eazydocs' ), $capabilites, 'ezd-user-feedback', [$this, 'ezd_feedback_presents'] );
		}
		
		add_submenu_page( 'eazydocs', __( 'Tags', 'eazydocs' ), __( 'Tags', 'eazydocs' ), $capabilites, '/edit-tags.php?taxonomy=doc_tag&post_type=docs' );
		
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
        $classes .= ' '.$current_theme;
        switch ( $current_theme ) {
            case 'docy':
                $classes .= ' '.trim( get_option('docy_purchase_code_status') );
                break;
            case 'docly':
                $classes .= ' '.trim( get_option('docly_purchase_code_status') );
                break;
            default:
                $classes .= '';
        }

        if ( eaz_fs()->is__premium_only() ) {
            $classes .= ' ezd-premium';
        }

        return $classes;
    }

    /**
     * OnePage Doc Pro Notice
     * @return void
     */
    public function ezd_onepage_presents(){
	   ?>
        <div class="wrap">
            <div class="ezd-blank_state">
                <?php // PHPCS - No need to escape an SVG image from the Elementor assets/images folder. ?>
                <img src="<?php echo EAZYDOCS_IMG.'/icon/crown.svg'; ?>" alt="<?php esc_attr_e('crown icon', 'eazydocs'); ?>" width="250px"/>
                <h2> <?php echo esc_html__( 'Add Your OnePage Doc', 'eazydocs' ); ?> </h2>
                <p class="big-p"> <?php esc_html_e('Onepage documentation format will generate all the pages of a Doc as sections in a single page which is scrollable by sections. Visitors can find the all guides on a single page and they can navigate through the different sections very fast.', 'eazydocs'); ?> </p>
                <?php // PHPCS - No need to escape a URL. The query arg is sanitized. ?>
                <div class="button-inline">
                    <a class="button button-primary ezd-btn ezd-btn-pro btn-lg" href="<?php echo admin_url('admin.php?page=eazydocs-pricing'); ?>">
                        <?php esc_html_e( 'Go Pro', 'elementor' ); ?>
                    </a>
                    <a class="button button-secondary ezd-btn btn-lg" target="_blank" href="https://wordpress-theme.spider-themes.net/docy/docy-documentation/" title="<?php esc_attr_e('View Frontend Demo', 'eazydocs'); ?>">
                        <?php esc_html_e( 'View Demo', 'elementor' ); ?>
                    </a>
                </div>
            </div>
        </div><!-- /.wrap -->
        <?php
    }

    public function ezd_feedback_presents(){
	   ?>
        <div class="wrap">
            <div class="ezd-blank_state">
                <?php // PHPCS - No need to escape an SVG image from the Elementor assets/images folder. ?>
                <img src="<?php echo EAZYDOCS_IMG.'/icon/crown.svg'; ?>" alt="<?php esc_attr_e('crown icon', 'eazydocs'); ?>" width="250px"/>
                <h2> <?php echo esc_html__( 'Users Feedback', 'eazydocs' ); ?> </h2>
                <p class="big-p"> <?php esc_html_e( 'You can get the Doc Feedbacks listed in this page to review.', 'eazydocs'); ?> </p>
                <?php // PHPCS - No need to escape a URL. The query arg is sanitized. ?>
                <div class="button-inline">
                    <a class="button button-primary ezd-btn ezd-btn-pro btn-lg" href="<?php echo admin_url('admin.php?page=eazydocs-pricing'); ?>">
                        <?php esc_html_e( 'Go Pro', 'elementor' ); ?>
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
	public function one_page_docs_edit_content( $link, $post_ID ){
		if( 'onepage-docs'      == get_post_type( $post_ID ) ){
			$is_content         = get_the_content($post_ID);

			$ezd_doc_layout     = get_post_meta($post_ID, 'ezd_doc_layout', true);
			$doc_layout         = ! empty( $ezd_doc_layout ) ? '&doc_layout='. $ezd_doc_layout : null;

			$ezd_content_type   = get_post_meta($post_ID, 'ezd_doc_content_type', true);
			$content_type       = ! empty( $ezd_content_type ) ? '&content_type='. $ezd_content_type : null;
			
			$is_content 		= str_replace('#',';hash;', $is_content);
			$is_content 		= str_replace('style&equals;','style@', $is_content);
			$content_null       = ! empty( $is_content ) ? '&content='. $is_content : null;
			
			$ezd_content_type_right   = get_post_meta($post_ID, 'ezd_doc_content_type_right', true);
			$content_type_right       = ! empty( $ezd_content_type_right ) ? '&content_type_right='. $ezd_content_type_right : null;

			$ezd_content_right 	= '';
			if ( $ezd_content_type_right == 'widget_data_right' ) {
				$ezd_content_right = get_post_meta($post_ID, 'ezd_doc_content_box_right', true);
			} else {
				$ezd_content_right = get_post_meta($post_ID, 'ezd_doc_content_box_right', true);
			}
			
			$ezd_content_right 		   = str_replace('#',';hash;', $ezd_content_right);
			$ezd_content_right 		= str_replace('style&equals;','style@', $ezd_content_right);
			$ezd_contents_right    = ! empty( $ezd_content_right ) ? '&content_right='. $ezd_content_right : null;
			$link                  = $link . $doc_layout . $content_type . $content_null . $content_type_right . $ezd_contents_right;
		}
		return $link;
	}

	/**
	** Give Notice
	**/
	public function ezd_notify_give_review()
	{
		if (function_exists('get_current_screen')) {
			if (get_current_screen()->id == 'plugins') {
				?>
                <div class="notice notice-success is-dismissible" id="ezd_notify_review">
                    <h3><?php _e('Give EazyDocs a review', 'eazydocs')?></h3>
                    <p>
						<?php _e('Thank you for choosing EazyDocs. We hope you love it. Could you take a couple of seconds posting a nice review to share your happy experience?', 'eazydocs')?>
                    </p>
                    <p class="ezd_notify_review_subheading">
						<?php _e('We will be forever grateful. Thank you in advance.', 'eazydocs'); ?>
                    </p>
                    <p>
                        <a href="javascript:;" data="rateNow" class="button button-primary" style="margin-right: 5px"><?php _e('Rate now', 'eazydocs')?></a>
                        <a href="javascript:;" data="later" class="button" style="margin-right: 5px"><?php _e('Later', 'eazydocs')?></a>
                        <a href="javascript:;" data="alreadyDid" class="button"><?php _e('Already did', 'eazydocs')?></a>
                    </p>
                </div>
				<?php
			}
		}
	}

	/**
	 ** Save Notice
	 **/
	public function ezd_notify_save_review()
	{
		if ( isset( $_POST ) ) {
			$nonce = isset( $_POST['nonce'] ) ? sanitize_text_field( $_POST['nonce'] ) : null;
			$field = isset( $_POST['field'] ) ? sanitize_text_field( $_POST['field'] ) : null;

			if ( ! wp_verify_nonce( $nonce, 'eazydocs-admin-nonce' ) ) {
				wp_send_json_error( array( 'status' => 'Wrong nonce validate!' ) );
				exit();
			}

			if ($field == 'later'){
				update_option('ezd_notify_review', time() + 3*60*60*24); //After 3 days show
			} else if ($field == 'alreadyDid'){
				update_option('ezd_notify_review', 0);
			}
			wp_send_json_success();
		}
		wp_send_json_error( array( 'message' => 'Update fail!' ) );
	}

	public function ezd_admin_body_class($admin_body){
		$ezd_admin_classe = explode(' ', $admin_body);    
		if ( empty( eaz_fs()->is_plan__premium_only('promax') ) ) {
			$ezd_admin_classe = array_merge($ezd_admin_classe, [
				'ezd_promax' 
			]);
		}
		return implode(' ', array_unique($ezd_admin_classe)); 
	}
	
}