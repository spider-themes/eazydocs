<?php
global $wpdb;
// Get total docs count
$total_docs = (int) $wpdb->get_var(" SELECT COUNT(*) FROM {$wpdb->posts} WHERE post_type = 'docs' AND post_status = 'publish' ");
?>
<div class="ezd-stat-grid">
    <div class="ezd-stat-card">
        <div class="ezd-stat-header">
            <p class="ezd-stat-label"><?php esc_html_e( 'Total Docs', 'eazydocs' ); ?></p>
            <div class="ezd-stat-card-icons">
                <span class="dashicons dashicons-media-document ezd-stat-icon"></span>
                <a href="<?php echo admin_url('admin.php?page=eazydocs-builder'); ?>" title="<?php esc_html_e( 'View More', 'eazydocs' ); ?>">
                    <img src="<?php echo EAZYDOCS_IMG . '/icon/external.svg'; ?>" />
                </a>
            </div>
        </div>
        <p class="ezd-stat-value"><?php echo esc_html( $total_docs ); ?></p>
    </div>

    <div class="ezd-stat-card">
        <div class="ezd-stat-header">
            <p class="ezd-stat-label"><?php esc_html_e( 'Total Views', 'eazydocs' ); ?></p>
            <div class="ezd-stat-card-icons">
                <span class="dashicons dashicons-visibility ezd-stat-icon"></span>
                <a href="<?php echo admin_url('admin.php?page=ezd-analytics&more_state=analytics-views'); ?>" title="<?php esc_html_e( 'View More', 'eazydocs' ); ?>">
                    <img src="<?php echo EAZYDOCS_IMG . '/icon/external.svg'; ?>" />
                </a>
            </div>
        </div>
        <p class="ezd-stat-value">
            <?php
            $get_views   = $wpdb->get_var("SELECT SUM(meta_value+0) FROM $wpdb->postmeta WHERE meta_key='post_views_count'");
            $total_views = $get_views >= 1000 ? round($get_views/1000) . 'k' : ( $get_views ?: '0' );
            echo esc_html( $total_views );
            ?>
        </p>
    </div>

    <div class="ezd-stat-card">
        <div class="ezd-stat-header">
            <p class="ezd-stat-label"><?php esc_html_e( 'Positive Votes', 'eazydocs' ); ?></p>
            <div class="ezd-stat-card-icons">
                <span class="dashicons dashicons-thumbs-up ezd-stat-icon-positive"></span>
                <a href="<?php echo admin_url('admin.php?page=ezd-analytics&more_state=analytics-feedback'); ?>" title="<?php esc_html_e( 'View More', 'eazydocs' ); ?>">
                    <img src="<?php echo EAZYDOCS_IMG . '/icon/external.svg'; ?>" />
                </a>
            </div>
        </div>
        <p class="ezd-stat-value ezd-stat-value-positive">                    
            <?php echo esc_html( $total_liked ); ?>%
        </p>
    </div>

    <div class="ezd-stat-card">
        <div class="ezd-stat-header">
            <p class="ezd-stat-label"><?php esc_html_e( 'Negative Votes', 'eazydocs' ); ?></p>
            <div class="ezd-stat-card-icons">
                <span class="dashicons dashicons-thumbs-down ezd-stat-icon-negative"></span>
                <a href="<?php echo admin_url('admin.php?page=ezd-analytics&more_state=analytics-feedback'); ?>" title="<?php esc_html_e( 'View More', 'eazydocs' ); ?>">
                    <img src="<?php echo EAZYDOCS_IMG . '/icon/external.svg'; ?>" />
                </a>
            </div>
        </div>
        <p class="ezd-stat-value ezd-stat-value-negative">
            <?php echo esc_html( $total_disliked ); ?>%
        </p>
    </div>
</div>