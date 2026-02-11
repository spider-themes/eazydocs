<?php

namespace EazyDocs\Admin;

/**
 * Antimanual Integration Notice
 * 
 * Displays admin notices to inform EazyDocs users about Antimanual AI capabilities
 */
class AntimanualNotice {

    /**
     * Singleton instance.
     *
     * @var self|null
     */
    private static $instance = null;

    /**
     * Bootstrap (safe to call multiple times).
     */
    public static function init() {
        if ( null === self::$instance ) {
            self::$instance = new self();
        }

        // Always re-register hooks (EazyDocs wipes admin_notices on its pages).
        self::$instance->register_hooks();

        return self::$instance;
    }

    /**
     * Initialize the class
     */
    public function __construct() {
        $this->register_hooks();
    }

    /**
     * Register hooks (idempotent).
     */
    public function register_hooks() {
        remove_action( 'admin_notices', [ $this, 'display_antimanual_notice' ] );
        add_action( 'admin_notices', [ $this, 'display_antimanual_notice' ] );

        remove_action( 'wp_ajax_ezd_dismiss_antimanual_notice', [ $this, 'dismiss_notice' ] );
        add_action( 'wp_ajax_ezd_dismiss_antimanual_notice', [ $this, 'dismiss_notice' ] );
    }

    /**
     * Display Antimanual integration notice
     */
    public function display_antimanual_notice() {
        // Check if notice was dismissed
        $dismissed = get_user_meta( get_current_user_id(), 'ezd_dismissed_antimanual_notice', true );
        if ( $dismissed ) {
            return;
        }

        // Check if Antimanual is active
        if ( is_plugin_active( 'antimanual/antimanual.php' ) ) {
            return; // Don't show notice if already active
        }

        // Check if Antimanual is installed
        $antimanual_installed = file_exists( WP_PLUGIN_DIR . '/antimanual/antimanual.php' );

        ?>
        <div class="notice ezd-antimanual-notice is-dismissible" data-notice="antimanual">
            <div class="ezd-notice-content">
                <div class="ezd-notice-icon">
                    <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <circle cx="12" cy="12" r="12" fill="#5E3AEE"/>
                        <path d="M12 6C11.4477 6 11 6.44772 11 7V13C11 13.5523 11.4477 14 12 14C12.5523 14 13 13.5523 13 13V7C13 6.44772 12.5523 6 12 6Z" fill="white"/>
                        <circle cx="12" cy="17" r="1" fill="white"/>
                    </svg>
                </div>
                <div class="ezd-notice-body">
                    <?php if ( $antimanual_installed ) : ?>
                        <strong><?php esc_html_e( 'Activate Antimanual to supercharge EazyDocs with AI', 'eazydocs' ); ?></strong>
                        <p><?php esc_html_e( 'Antimanual is installed but not active. Activate it to unlock AI chatbot, smart search, and auto-generated documentation features.', 'eazydocs' ); ?></p>
                    <?php else : ?>
                        <strong><?php esc_html_e( 'Supercharge EazyDocs with AI-powered Antimanual', 'eazydocs' ); ?></strong>
                        <p><?php esc_html_e( 'Add an AI chatbot trained on your docs, smart semantic search, and auto-generated documentation. Reduce support tickets by 70%+.', 'eazydocs' ); ?></p>
                    <?php endif; ?>
                    <div class="ezd-notice-actions">
                        <?php if ( $antimanual_installed ) : ?>
                            <a href="<?php echo esc_url( wp_nonce_url( admin_url( 'plugins.php?action=activate&plugin=antimanual/antimanual.php' ), 'activate-plugin_antimanual/antimanual.php' ) ); ?>" 
                               class="button button-primary">
                                <?php esc_html_e( 'Activate Plugin', 'eazydocs' ); ?>
                            </a>
                        <?php else : ?>
                            <a href="<?php echo esc_url( admin_url( 'plugin-install.php?s=antimanual&tab=search&type=term' ) ); ?>" 
                               class="button button-primary">
                                <?php esc_html_e( 'Install Plugin', 'eazydocs' ); ?>
                            </a>
                        <?php endif; ?>
                        <a href="https://antimanual.spider-themes.net" target="_blank" class="button button-secondary">
                            <?php esc_html_e( 'Learn More', 'eazydocs' ); ?>
                        </a>
                    </div>
                </div>
            </div>
        </div>

        <style>
            .ezd-antimanual-notice {
                position: relative;
                border: none;
                border-left: 4px solid #5E3AEE;
                padding: 16px 40px 16px 16px;
                background: #faf9fe;
                box-shadow: none;
                margin: 15px 0;
            }
            
            .ezd-antimanual-notice .notice-dismiss {
                padding: 10px;
            }
            
            .ezd-notice-content {
                display: flex;
                gap: 14px;
                align-items: flex-start;
            }
            
            .ezd-notice-icon {
                flex-shrink: 0;
                width: 24px;
                height: 24px;
                margin-top: 2px;
            }
            
            .ezd-notice-icon svg {
                display: block;
                width: 24px;
                height: 24px;
            }
            
            .ezd-notice-body {
                flex: 1;
            }
            
            .ezd-notice-body strong {
                display: block;
                margin: 0 0 4px;
                font-size: 14px;
                font-weight: 600;
                color: #1e1e1e;
                line-height: 1.4;
            }
            
            .ezd-notice-body p {
                margin: 0 0 12px;
                font-size: 13px;
                color: #50575e;
                line-height: 1.5;
            }
            
            .ezd-notice-actions {
                display: flex;
                gap: 8px;
                flex-wrap: wrap;
            }
            
            .ezd-notice-actions .button {
                height: 32px;
                line-height: 30px;
                padding: 0 14px;
                font-size: 13px;
                font-weight: 500;
                border-radius: 3px;
            }
            
            .ezd-notice-actions .button-primary {
                background: #5E3AEE;
                border-color: #5E3AEE;
                color: #fff;
                text-shadow: none;
                box-shadow: none;
            }
            
            .ezd-notice-actions .button-primary:hover,
            .ezd-notice-actions .button-primary:focus {
                background: #4e2ed6;
                border-color: #4e2ed6;
                color: #fff;
            }
            
            .ezd-notice-actions .button-secondary {
                background: transparent;
                border-color: #5E3AEE;
                color: #5E3AEE;
            }
            
            .ezd-notice-actions .button-secondary:hover,
            .ezd-notice-actions .button-secondary:focus {
                background: rgba(94, 58, 238, 0.05);
                border-color: #4e2ed6;
                color: #4e2ed6;
            }
        </style>

        <script>
        jQuery(document).ready(function($) {
            $('.ezd-antimanual-notice').on('click', '.notice-dismiss', function() {
                $.ajax({
                    url: ajaxurl,
                    type: 'POST',
                    data: {
                        action: 'ezd_dismiss_antimanual_notice',
                        nonce: '<?php echo wp_create_nonce( 'ezd_dismiss_antimanual_notice' ); ?>'
                    }
                });
            });
        });
        </script>
        <?php
    }

    /**
     * Check if current page is EazyDocs admin page
     */
    private function is_eazydocs_page() {
        $screen = get_current_screen();
        
        if ( ! $screen ) {
            return false;
        }

        // Check if on EazyDocs admin pages
        $eazydocs_pages = [
            'toplevel_page_eazydocs-builder',
            'eazydocs_page_eazydocs-settings',
            'eazydocs_page_eazydocs',
            'eazydocs_page_ezd-analytics',
            'eazydocs_page_ezd-user-feedback',
        ];

        return in_array( $screen->id, $eazydocs_pages ) || 'docs' === $screen->post_type;
    }

    /**
     * Dismiss the notice
     */
    public function dismiss_notice() {
        check_ajax_referer( 'ezd_dismiss_antimanual_notice', 'nonce' );
        
        update_user_meta( get_current_user_id(), 'ezd_dismissed_antimanual_notice', true );
        
        wp_send_json_success();
    }
}

AntimanualNotice::init();