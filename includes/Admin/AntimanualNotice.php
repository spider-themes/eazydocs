<?php

namespace EazyDocs\Admin;

/**
 * Antimanual Integration Notice
 * 
 * Displays admin notices to inform EazyDocs users about Antimanual AI capabilities
 */
class AntimanualNotice {

    /**
     * Initialize the class
     */
    public function __construct() {
        add_action( 'admin_notices', [ $this, 'display_antimanual_notice' ] );
        add_action( 'wp_ajax_ezd_dismiss_antimanual_notice', [ $this, 'dismiss_notice' ] );
    }

    /**
     * Display Antimanual integration notice
     */
    public function display_antimanual_notice() {
        // Only show on EazyDocs pages
        if ( ! $this->is_eazydocs_page() ) {
            return;
        }

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
        <div class="notice notice-info is-dismissible ezd-antimanual-notice" data-notice="antimanual">
            <div class="ezd-notice-content">
                <div class="ezd-notice-icon">
                    🤖
                </div>
                <div class="ezd-notice-body">
                    <h3><?php esc_html_e( 'Supercharge Your EazyDocs with AI!', 'eazydocs' ); ?></h3>
                    
                    <?php if ( $antimanual_installed ) : ?>
                        <p>
                            <?php esc_html_e( 'Activate Antimanual to unlock powerful AI features for your documentation:', 'eazydocs' ); ?>
                        </p>
                        <ul class="ezd-notice-features">
                            <li><strong>✓</strong> <?php esc_html_e( 'AI Chatbot trained on your docs - Reduce support tickets by 70%+', 'eazydocs' ); ?></li>
                            <li><strong>✓</strong> <?php esc_html_e( 'Auto-generate professional documentation with GPT-5 & Gemini', 'eazydocs' ); ?></li>
                            <li><strong>✓</strong> <?php esc_html_e( 'Semantic AI search that understands user intent', 'eazydocs' ); ?></li>
                            <li><strong>✓</strong> <?php esc_html_e( 'Auto-posting and content automation for fresh content', 'eazydocs' ); ?></li>
                        </ul>
                        <div class="ezd-notice-actions">
                            <a href="<?php echo esc_url( wp_nonce_url( admin_url( 'plugins.php?action=activate&plugin=antimanual/antimanual.php' ), 'activate-plugin_antimanual/antimanual.php' ) ); ?>" 
                               class="button button-primary">
                                <?php esc_html_e( 'Activate Antimanual Now', 'eazydocs' ); ?>
                            </a>
                            <a href="https://www.youtube.com/watch?v=X9HMPBkzDeM" target="_blank" class="button button-secondary">
                                <span class="dashicons dashicons-video-alt3"></span>
                                <?php esc_html_e( 'Watch Demo', 'eazydocs' ); ?>
                            </a>
                        </div>
                    <?php else : ?>
                        <p>
                            <?php esc_html_e( 'Integrate EazyDocs with Antimanual - the ultimate AI copilot for WordPress. Transform your knowledge base into an intelligent support hub!', 'eazydocs' ); ?>
                        </p>
                        <ul class="ezd-notice-features">
                            <li><strong>🤖</strong> <?php esc_html_e( 'Intelligent AI Chatbot - 24/7 support trained on your documentation', 'eazydocs' ); ?></li>
                            <li><strong>📚</strong> <?php esc_html_e( 'AI Doc Generator - Create comprehensive docs in seconds', 'eazydocs' ); ?></li>
                            <li><strong>🔍</strong> <?php esc_html_e( 'Semantic Search - Understand user intent, not just keywords', 'eazydocs' ); ?></li>
                            <li><strong>✍️</strong> <?php esc_html_e( 'Auto-Posting - Schedule AI-generated content automatically', 'eazydocs' ); ?></li>
                            <li><strong>🚀</strong> <?php esc_html_e( 'GPT-5 & Gemini Support - Latest AI models for superior accuracy', 'eazydocs' ); ?></li>
                        </ul>
                        <div class="ezd-notice-highlight">
                            <span class="ezd-notice-badge"><?php esc_html_e( '70%+ Reduction in Support Tickets', 'eazydocs' ); ?></span>
                            <span class="ezd-notice-badge"><?php esc_html_e( 'Enterprise-Grade AI', 'eazydocs' ); ?></span>
                            <span class="ezd-notice-badge"><?php esc_html_e( 'Free Version Available', 'eazydocs' ); ?></span>
                        </div>
                        <div class="ezd-notice-actions">
                            <a href="<?php echo esc_url( admin_url( 'plugin-install.php?s=antimanual&tab=search&type=term' ) ); ?>" 
                               class="button button-primary">
                                <span class="dashicons dashicons-download"></span>
                                <?php esc_html_e( 'Install Antimanual Free', 'eazydocs' ); ?>
                            </a>
                            <a href="https://antimanual.spider-themes.net" target="_blank" class="button button-secondary">
                                <?php esc_html_e( 'Learn More', 'eazydocs' ); ?>
                            </a>
                            <a href="https://www.youtube.com/watch?v=X9HMPBkzDeM" target="_blank" class="button button-link">
                                <span class="dashicons dashicons-video-alt3"></span>
                                <?php esc_html_e( 'Watch Demo', 'eazydocs' ); ?>
                            </a>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>

        <style>
            .ezd-antimanual-notice {
                border-left: 4px solid #4c4cf1;
                padding: 20px;
                background: linear-gradient(135deg, #f5f5ff 0%, #ebebff 100%);
            }
            
            .ezd-notice-content {
                display: flex;
                gap: 20px;
                align-items: flex-start;
            }
            
            .ezd-notice-icon {
                font-size: 48px;
                line-height: 1;
                flex-shrink: 0;
            }
            
            .ezd-notice-body {
                flex: 1;
            }
            
            .ezd-notice-body h3 {
                margin: 0 0 10px;
                font-size: 18px;
                color: #1f2937;
            }
            
            .ezd-notice-body p {
                margin: 0 0 15px;
                font-size: 14px;
                color: #4b5563;
                line-height: 1.6;
            }
            
            .ezd-notice-features {
                list-style: none;
                margin: 15px 0;
                padding: 0;
            }
            
            .ezd-notice-features li {
                padding: 8px 0;
                font-size: 14px;
                color: #374151;
                line-height: 1.5;
            }
            
            .ezd-notice-features li strong {
                color: #10b981;
                margin-right: 8px;
            }
            
            .ezd-notice-highlight {
                display: flex;
                gap: 10px;
                flex-wrap: wrap;
                margin: 15px 0;
            }
            
            .ezd-notice-badge {
                display: inline-block;
                padding: 6px 14px;
                background: linear-gradient(135deg, #4c4cf1 0%, #3d3dd6 100%);
                color: #ffffff;
                border-radius: 20px;
                font-size: 12px;
                font-weight: 600;
            }
            
            .ezd-notice-actions {
                display: flex;
                gap: 10px;
                flex-wrap: wrap;
                margin-top: 15px;
            }
            
            .ezd-notice-actions .button {
                display: inline-flex;
                align-items: center;
                gap: 6px;
            }
            
            .ezd-notice-actions .button-primary {
                background: linear-gradient(135deg, #4c4cf1 0%, #3d3dd6 100%);
                border-color: #4c4cf1;
                text-shadow: none;
                box-shadow: 0 2px 8px rgba(76, 76, 241, 0.3);
            }
            
            .ezd-notice-actions .button-primary:hover {
                background: linear-gradient(135deg, #3d3dd6 0%, #2e2ec2 100%);
                border-color: #3d3dd6;
                box-shadow: 0 4px 12px rgba(76, 76, 241, 0.4);
            }
            
            .ezd-notice-actions .button .dashicons {
                font-size: 16px;
                width: 16px;
                height: 16px;
                line-height: 16px;
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

        return in_array( $screen->id, $eazydocs_pages ) || $screen->post_type === 'docs';
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

new AntimanualNotice();
