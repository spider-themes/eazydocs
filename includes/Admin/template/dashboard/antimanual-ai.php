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

<style>
.ezd-ai-integration-card {
    background: linear-gradient(135deg, var(--clr-blue) 0%, #667eea 100%);
    border-radius: 12px;
    padding: 0;
    box-shadow: 0 10px 40px rgba(102, 126, 234, 0.2);
    overflow: hidden;
    display: flex;
    flex-direction: column;
    height: 100%;
}

.ezd-ai-card-header {
    background: rgba(255, 255, 255, 0.1);
    padding: 20px 24px;
    display: flex;
    justify-content: space-between;
    align-items: center;
    border-bottom: 1px solid rgba(255, 255, 255, 0.1);
}

.ezd-ai-header-content {
    display: flex;
    align-items: center;
    gap: 12px;
}

.ezd-ai-icon {
    font-size: 32px;
    line-height: 1;
}

.ezd-ai-title {
    margin: 0;
    font-size: 22px;
    font-weight: 600;
    color: #ffffff;
}

.ezd-ai-badge {
    padding: 6px 14px;
    border-radius: 20px;
    font-size: 12px;
    font-weight: 600;
    text-transform: uppercase;
    letter-spacing: 0.5px;
}

.ezd-ai-badge-active {
    background: #10b981;
    color: #ffffff;
}

.ezd-ai-badge-available {
    background: rgba(255, 255, 255, 0.2);
    color: #ffffff;
}

.ezd-ai-card-body {
    padding: 24px;
    background: #ffffff;
    flex: 1;
    overflow-y: auto;
}

.ezd-ai-description {
    font-size: 15px;
    line-height: 1.6;
    color: #4b5563;
    margin: 0 0 20px;
}

.ezd-ai-highlight {
    display: grid;
    grid-template-columns: repeat(3, 1fr);
    gap: 16px;
    margin-bottom: 24px;
    padding: 20px;
    background: linear-gradient(135deg, #f5f7fa 0%, #e8ecf1 100%);
    border-radius: 8px;
}

.ezd-ai-stat {
    text-align: center;
}

.ezd-ai-stat-value {
    font-size: 28px;
    font-weight: 700;
    color: #4c4cf1;
    line-height: 1;
    margin-bottom: 6px;
}

.ezd-ai-stat-label {
    font-size: 12px;
    color: #6b7280;
    font-weight: 500;
}

.ezd-ai-features-grid {
    display: grid;
    grid-template-columns: repeat(2, 1fr);
    gap: 16px;
    margin-bottom: 24px;
}

.ezd-ai-feature {
    padding: 16px;
    background: #f9fafb;
    border-radius: 8px;
    border: 1px solid #e5e7eb;
}

.ezd-ai-feature-icon {
    font-size: 24px;
    margin-bottom: 8px;
    display: block;
}

.ezd-ai-feature h4 {
    margin: 0 0 6px;
    font-size: 14px;
    font-weight: 600;
    color: #1f2937;
}

.ezd-ai-feature p {
    margin: 0;
    font-size: 12px;
    color: #6b7280;
    line-height: 1.5;
}

.ezd-ai-features-active {
    display: grid;
    grid-template-columns: repeat(2, 1fr);
    gap: 12px;
    margin-bottom: 20px;
}

.ezd-ai-feature-item {
    display: flex;
    align-items: center;
    gap: 8px;
    font-size: 14px;
    color: #374151;
}

.ezd-ai-feature-item .dashicons {
    color: #10b981;
    font-size: 18px;
    width: 18px;
    height: 18px;
}

/* Antimanual Menu Items */
.ezd-ai-menu-items {
    display: grid;
    grid-template-columns: repeat(2, 1fr);
    gap: 12px;
    margin-bottom: 20px;
}

.ezd-ai-menu-item {
    display: flex;
    align-items: center;
    gap: 10px;
    padding: 12px 12px;
    background: #f9fafb;
    border: 1px solid #e5e7eb;
    border-radius: 8px;
    text-decoration: none;
    transition: all 0.2s ease;
    text-align: center;
}

.ezd-ai-menu-item:hover {
    background: #f3f4f6;
    border-color: #4c4cf1;
    transform: translateY(-2px);
}

.ezd-ai-menu-item .ezd-ai-menu-icon {
    width: 32px;
    height: 32px;
    background: linear-gradient(135deg, #4c4cf1 0%, #667eea 100%);
    border-radius: 6px;
    display: flex;
    align-items: center;
    justify-content: center;
    color: #ffffff;
    font-size: 16px;
}

.ezd-ai-menu-item .ezd-ai-menu-text {
    font-size: 13px;
    font-weight: 600;
    color: #1f2937;
    line-height: 1.3;
}

.ezd-ai-menu-item .dashicons-arrow-right-alt2 {
    display: none;
}

.ezd-ai-benefits-list {
    margin: 0 0 20px;
    padding-left: 0;
    list-style: none;
}

.ezd-ai-benefits-list li {
    padding: 10px 0;
    font-size: 14px;
    color: #4b5563;
    line-height: 1.5;
    border-bottom: 1px solid #e5e7eb;
}

.ezd-ai-benefits-list li:last-child {
    border-bottom: none;
}

.ezd-ai-actions {
    display: flex;
    gap: 12px;
    flex-wrap: wrap;
    justify-content: space-between;
}

.ezd-ai-btn {
    display: inline-flex;
    align-items: center;
    gap: 6px;
    padding: 12px 24px;
    border-radius: 6px;
    font-size: 14px;
    font-weight: 600;
    text-decoration: none;
    transition: all 0.2s;
    border: none;
    cursor: pointer;
}

.ezd-ai-btn-primary {
    background: linear-gradient(135deg, #4c4cf1 0%, #3d3dd6 100%);
    color: #ffffff;
    box-shadow: 0 4px 12px rgba(76, 76, 241, 0.3);
}

.ezd-ai-btn-primary:hover {
    transform: translateY(-2px);
    box-shadow: 0 6px 16px rgba(76, 76, 241, 0.4);
    color: #ffffff;
}

.ezd-ai-btn-secondary {
    background: #ffffff;
    color: #4c4cf1;
    border: 2px solid #4c4cf1;
}

.ezd-ai-btn-secondary:hover {
    background: #4c4cf1;
    color: #ffffff;
}

.ezd-ai-btn-upgrade {
    background: linear-gradient(135deg, #f59e0b 0%, #d97706 100%);
    color: #ffffff;
    border: none;
}

.ezd-ai-btn-upgrade:hover {
    transform: translateY(-2px);
    box-shadow: 0 6px 16px rgba(245, 158, 11, 0.4);
    color: #ffffff;
}

.ezd-ai-btn-youtube {
    background: linear-gradient(135deg, #FF0000 0%, #CC0000 100%);
    color: #ffffff;
    border: none;
    box-shadow: 0 4px 12px rgba(255, 0, 0, 0.3);
}

.ezd-ai-btn-youtube:hover {
    background: linear-gradient(135deg, #CC0000 0%, #990000 100%);
    transform: translateY(-2px);
    box-shadow: 0 6px 16px rgba(255, 0, 0, 0.5);
    color: #ffffff;
}

.ezd-ai-btn-link {
    background: transparent;
    color: #4c4cf1;
    padding: 12px 16px;
}

.ezd-ai-btn-link:hover {
    background: #f3f4f6;
    color: #3d3dd6;
}

.ezd-ai-btn .dashicons {
    font-size: 18px;
    width: 18px;
    height: 18px;
}

.ezd-ai-comparison {
    margin-top: 24px;
    padding: 20px;
    background: #fef3c7;
    border-radius: 8px;
    border-left: 4px solid #f59e0b;
}

.ezd-ai-comparison-title {
    margin: 0 0 12px;
    color: #92400e;
    font-size: 14px;
}

.ezd-ai-comparison-list {
    margin: 0;
    padding-left: 0;
    list-style: none;
}

.ezd-ai-comparison-list li {
    padding: 6px 0;
    font-size: 13px;
    color: #78350f;
    line-height: 1.5;
}

.ezd-ai-card-footer {
    padding: 16px 24px;
    background: #f9fafb;
    border-top: 1px solid #e5e7eb;
    margin-top: auto;
}

.ezd-ai-card-footer .ezd-ai-actions {
    margin: 0;
    margin-top: 0;
}

.ezd-ai-footer-text {
    margin: 0;
    font-size: 13px;
    color: #6b7280;
    display: flex;
    align-items: center;
    gap: 8px;
}

.ezd-ai-footer-text .dashicons {
    color: #9ca3af;
    font-size: 16px;
    width: 16px;
    height: 16px;
}

@media (max-width: 768px) {
    .ezd-ai-highlight,
    .ezd-ai-features-grid,
    .ezd-ai-features-active {
        grid-template-columns: 1fr;
    }
    
    .ezd-ai-actions {
        flex-direction: column;
    }
    
    .ezd-ai-btn {
        width: 100%;
        justify-content: center;
    }
    
    .ezd-ai-menu-items {
        grid-template-columns: 1fr;
    }
    
    .ezd-ai-menu-item {
        padding: 12px 14px;
    }
    
    .ezd-ai-menu-item .ezd-ai-menu-icon {
        width: 28px;
        height: 28px;
        font-size: 14px;
    }
}
</style>
