<div class="ezd-header">
    <div class="ezd-logo-container" title="EazyDocs Dashboard">
        <img src="<?php echo EAZYDOCS_IMG . '/eazydocs-logo.png'; ?>" alt="EazyDocs Logo">
        <h1 class="ezd-logo-text"> <?php esc_html_e('Dashboard', 'eazydocs'); ?> </h1>
    </div>
    <div class="ezd-header-actions">
        <div class="ezd-action-item">
            <?php
            if (current_user_can('edit_posts')):
                $nonce = wp_create_nonce('parent_doc_nonce');
                ?>
                <button type="button"
                    data-url="<?php echo esc_url(admin_url('admin.php') . "?Create_doc=yes&_wpnonce={$nonce}&parent_title="); ?>"
                    id="parent-doc" class="easydocs-btn easydocs-btn-outline-blue easydocs-btn-sm easydocs-btn-round">
                    <span class="dashicons dashicons-plus-alt2"></span>
                    <?php esc_html_e('Add Doc', 'eazydocs'); ?>
                </button>
                <?php
            endif;
            ?>
            <button type="button" id="ezd-create-doc-with-ai"
                class="easydocs-btn easydocs-btn-ai-gold easydocs-btn-sm easydocs-btn-round" style="margin-left: 10px;">
                🪄 <?php esc_html_e('Create Doc with AI', 'eazydocs'); ?>
            </button>
        </div>
    </div>
</div>