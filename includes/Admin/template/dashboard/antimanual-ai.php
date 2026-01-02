<?php
/**
 * Antimanual AI Integration Card
 * Displays information about Antimanual AI capabilities for EazyDocs users
 */

// Check if Antimanual plugin is installed
$antimanual_installed = false;
$antimanual_active = false;
$antimanual_pro_active = false;

if ( file_exists( WP_PLUGIN_DIR . '/antimanual/antimanual.php' ) ) {
    $antimanual_installed = true;
    if ( is_plugin_active( 'antimanual/antimanual.php' ) ) {
        $antimanual_active = true;
    }
}

// Check if Antimanual Pro is active
if ( is_plugin_active( 'antimanual-pro/antimanual.php' ) ) {
    $antimanual_pro_active = true;
}
?>

<div class="ezd-ai-integration-card">
    <div class="ezd-ai-card-header">
        <div class="ezd-ai-header-content">
            <span class="ezd-ai-icon">🤖</span>
            <h3 class="ezd-ai-title"><?php esc_html_e( 'Supercharge with AI', 'eazydocs' ); ?></h3>
        </div>
        <?php if ( $antimanual_active ) : ?>
            <span class="ezd-ai-badge ezd-ai-badge-active"><?php esc_html_e( 'Active', 'eazydocs' ); ?></span>
        <?php else : ?>
            <span class="ezd-ai-badge ezd-ai-badge-available"><?php esc_html_e( 'Available', 'eazydocs' ); ?></span>
        <?php endif; ?>
    </div>

    <div class="ezd-ai-card-body">
        <?php if ( $antimanual_active ) : ?>
            <!-- Antimanual is Active - Show Menu Items -->
            <p class="ezd-ai-description">
                <?php esc_html_e( 'Antimanual AI is enhancing your documentation experience!', 'eazydocs' ); ?>
            </p>
            
            <div class="ezd-ai-menu-items">
                <a href="<?php echo esc_url( admin_url( 'admin.php?page=antimanual' ) ); ?>" class="ezd-ai-menu-item">
                    <span class="ezd-ai-menu-icon dashicons dashicons-admin-network"></span>
                    <span class="ezd-ai-menu-text"><?php esc_html_e( 'API Configuration', 'eazydocs' ); ?></span>
                    <span class="dashicons dashicons-arrow-right-alt2"></span>
                </a>
                <a href="<?php echo esc_url( admin_url( 'admin.php?page=atml-knowledge-base' ) ); ?>" class="ezd-ai-menu-item">
                    <span class="ezd-ai-menu-icon dashicons dashicons-book"></span>
                    <span class="ezd-ai-menu-text"><?php esc_html_e( 'Knowledge Base', 'eazydocs' ); ?></span>
                    <span class="dashicons dashicons-arrow-right-alt2"></span>
                </a>
                <a href="<?php echo esc_url( admin_url( 'admin.php?page=atml-chatbot' ) ); ?>" class="ezd-ai-menu-item">
                    <span class="ezd-ai-menu-icon dashicons dashicons-format-chat"></span>
                    <span class="ezd-ai-menu-text"><?php esc_html_e( 'Chatbot', 'eazydocs' ); ?></span>
                    <span class="dashicons dashicons-arrow-right-alt2"></span>
                </a>
                <a href="<?php echo esc_url( admin_url( 'admin.php?page=atml-docs' ) ); ?>" class="ezd-ai-menu-item">
                    <span class="ezd-ai-menu-icon dashicons dashicons-edit-large"></span>
                    <span class="ezd-ai-menu-text"><?php esc_html_e( 'Generate Docs', 'eazydocs' ); ?></span>
                    <span class="dashicons dashicons-arrow-right-alt2"></span>
                </a>
                <a href="<?php echo esc_url( admin_url( 'admin.php?page=atml-auto-posting' ) ); ?>" class="ezd-ai-menu-item">
                    <span class="ezd-ai-menu-icon dashicons dashicons-schedule"></span>
                    <span class="ezd-ai-menu-text"><?php esc_html_e( 'Auto Posting', 'eazydocs' ); ?></span>
                    <span class="dashicons dashicons-arrow-right-alt2"></span>
                </a>
                <a href="<?php echo esc_url( admin_url( 'admin.php?page=atml-bulk-rewrite' ) ); ?>" class="ezd-ai-menu-item">
                    <span class="ezd-ai-menu-icon dashicons dashicons-update-alt"></span>
                    <span class="ezd-ai-menu-text"><?php esc_html_e( 'Bulk Rewrite', 'eazydocs' ); ?></span>
                    <span class="dashicons dashicons-arrow-right-alt2"></span>
                </a>
            </div>

        <?php elseif ( $antimanual_installed ) : ?>
            <!-- Antimanual is Installed but not Active -->
            <p class="ezd-ai-description">
                <?php esc_html_e( 'Activate Antimanual to unlock powerful AI features for your documentation!', 'eazydocs' ); ?>
            </p>
            
            <div class="ezd-ai-benefits">
                <ul class="ezd-ai-benefits-list">
                    <li><strong><?php esc_html_e( '70%+ reduction', 'eazydocs' ); ?></strong> <?php esc_html_e( 'in support tickets', 'eazydocs' ); ?></li>
                    <li><strong><?php esc_html_e( 'Instant answers', 'eazydocs' ); ?></strong> <?php esc_html_e( 'with AI chatbot trained on your docs', 'eazydocs' ); ?></li>
                    <li><strong><?php esc_html_e( 'Auto-generate', 'eazydocs' ); ?></strong> <?php esc_html_e( 'professional documentation', 'eazydocs' ); ?></li>
                    <li><strong><?php esc_html_e( 'Semantic search', 'eazydocs' ); ?></strong> <?php esc_html_e( 'that understands user intent', 'eazydocs' ); ?></li>
                </ul>
            </div>

            <div class="ezd-ai-actions">
                <a href="<?php echo wp_nonce_url( admin_url( 'plugins.php?action=activate&plugin=antimanual/antimanual.php' ), 'activate-plugin_antimanual/antimanual.php' ); ?>" 
                   class="ezd-ai-btn ezd-ai-btn-primary">
                    <?php esc_html_e( 'Activate Antimanual', 'eazydocs' ); ?>
                </a>
                <a href="https://www.youtube.com/watch?v=X9HMPBkzDeM" target="_blank" class="ezd-ai-btn ezd-ai-btn-secondary">
                    <span class="dashicons dashicons-video-alt3"></span>
                    <?php esc_html_e( 'Watch Demo', 'eazydocs' ); ?>
                </a>
            </div>

        <?php else : ?>
            <!-- Antimanual is Not Installed -->
            <p class="ezd-ai-description">
                <?php esc_html_e( 'Transform your EazyDocs knowledge base into an intelligent support hub with Antimanual AI!', 'eazydocs' ); ?>
            </p>
            
            <div class="ezd-ai-highlight">
                <div class="ezd-ai-stat">
                    <div class="ezd-ai-stat-value">70%+</div>
                    <div class="ezd-ai-stat-label"><?php esc_html_e( 'Reduced Support Tickets', 'eazydocs' ); ?></div>
                </div>
                <div class="ezd-ai-stat">
                    <div class="ezd-ai-stat-value">24/7</div>
                    <div class="ezd-ai-stat-label"><?php esc_html_e( 'AI Chatbot Support', 'eazydocs' ); ?></div>
                </div>
                <div class="ezd-ai-stat">
                    <div class="ezd-ai-stat-value">GPT-5</div>
                    <div class="ezd-ai-stat-label"><?php esc_html_e( 'Latest AI Models', 'eazydocs' ); ?></div>
                </div>
            </div>

            <div class="ezd-ai-features-grid">
                <div class="ezd-ai-feature">
                    <span class="ezd-ai-feature-icon">🤖</span>
                    <h4><?php esc_html_e( 'Intelligent Chatbot', 'eazydocs' ); ?></h4>
                    <p><?php esc_html_e( 'Auto-trained on your docs, PDFs & URLs', 'eazydocs' ); ?></p>
                </div>
                <div class="ezd-ai-feature">
                    <span class="ezd-ai-feature-icon">📚</span>
                    <h4><?php esc_html_e( 'Doc Generator', 'eazydocs' ); ?></h4>
                    <p><?php esc_html_e( 'Create comprehensive docs in seconds', 'eazydocs' ); ?></p>
                </div>
                <div class="ezd-ai-feature">
                    <span class="ezd-ai-feature-icon">🔍</span>
                    <h4><?php esc_html_e( 'AI Search', 'eazydocs' ); ?></h4>
                    <p><?php esc_html_e( 'Semantic search with intent recognition', 'eazydocs' ); ?></p>
                </div>
                <div class="ezd-ai-feature">
                    <span class="ezd-ai-feature-icon">✍️</span>
                    <h4><?php esc_html_e( 'Auto-Posting', 'eazydocs' ); ?></h4>
                    <p><?php esc_html_e( 'Schedule AI-generated content', 'eazydocs' ); ?></p>
                </div>
            </div>

            <div class="ezd-ai-actions">
                <a href="<?php echo admin_url( 'plugin-install.php?s=antimanual&tab=search&type=term' ); ?>" 
                   class="ezd-ai-btn ezd-ai-btn-primary">
                    <span class="dashicons dashicons-download"></span>
                    <?php esc_html_e( 'Install Antimanual Free', 'eazydocs' ); ?>
                </a>
                <a href="https://antimanual.spider-themes.net" target="_blank" class="ezd-ai-btn ezd-ai-btn-secondary">
                    <?php esc_html_e( 'Learn More', 'eazydocs' ); ?>
                </a>
                <a href="https://www.youtube.com/watch?v=X9HMPBkzDeM" target="_blank" class="ezd-ai-btn ezd-ai-btn-link">
                    <span class="dashicons dashicons-video-alt3"></span>
                    <?php esc_html_e( 'Watch Demo', 'eazydocs' ); ?>
                </a>
            </div>

            <div class="ezd-ai-comparison">
                <p class="ezd-ai-comparison-title">
                    <strong><?php esc_html_e( 'Why Antimanual is Superior:', 'eazydocs' ); ?></strong>
                </p>
                <ul class="ezd-ai-comparison-list">
                    <li>✓ <?php esc_html_e( 'GPT-4, GPT-5, Gemini support (not just GPT-3.5)', 'eazydocs' ); ?></li>
                    <li>✓ <?php esc_html_e( 'Train on Docs, PDFs, URLs & Custom Text', 'eazydocs' ); ?></li>
                    <li>✓ <?php esc_html_e( 'Full conversation history & context retention', 'eazydocs' ); ?></li>
                    <li>✓ <?php esc_html_e( 'Cross-domain embed & bbPress integration', 'eazydocs' ); ?></li>
                </ul>
            </div>
        <?php endif; ?>
    </div>

    <div class="ezd-ai-card-footer">
        <?php if ( $antimanual_active ) : ?>
            <div class="ezd-ai-actions">
                <a href="<?php echo esc_url( admin_url( 'admin.php?page=antimanual-settings' ) ); ?>" class="ezd-ai-btn ezd-ai-btn-secondary">
                    <span class="dashicons dashicons-book"></span>
                    <?php esc_html_e( 'Documentation', 'eazydocs' ); ?>
                </a>
                
                <?php if ( $antimanual_pro_active ) : ?>
                    <a href="https://www.youtube.com/watch?v=X9HMPBkzDeM&list=PLeCjxMdg411WBCZC-v-DDKZCMYoCzQpCB" target="_blank" class="ezd-ai-btn ezd-ai-btn-youtube">
                        <span class="dashicons dashicons-video-alt3"></span>
                        <?php esc_html_e( 'Video Tutorials', 'eazydocs' ); ?>
                    </a>
                <?php else : ?>
                    <a href="https://antimanual.spider-themes.net/pricing" target="_blank" class="ezd-ai-btn ezd-ai-btn-upgrade">
                        <span class="dashicons dashicons-star-filled"></span>
                        <?php esc_html_e( 'Upgrade to Pro', 'eazydocs' ); ?>
                    </a>
                <?php endif; ?>
            </div>
        <?php elseif ( ! $antimanual_installed ) : ?>
            <p class="ezd-ai-footer-text">
                <span class="dashicons dashicons-info"></span>
                <?php esc_html_e( 'Free version available. Upgrade to Premium for higher limits and priority support.', 'eazydocs' ); ?>
            </p>
        <?php endif; ?>
    </div>
</div>