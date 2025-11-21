<?php
global $wpdb;
// Get total docs count
$total_docs = (int) $wpdb->get_var(" SELECT COUNT(*) FROM {$wpdb->posts} WHERE post_type = 'docs' AND post_status = 'publish' ");
?>
<div class="ezd-stat-grid">
    <div class="ezd-stat-card">
        <div class="ezd-stat-header">
            <p class="ezd-stat-label"><?php esc_html_e( 'Total Docs', 'eazydocs' ); ?></p>
            <span class="dashicons dashicons-media-document ezd-stat-icon"></span>
        </div>
        <p class="ezd-stat-value"><?php echo esc_html( $total_docs ); ?></p>
    </div>

    <div class="ezd-stat-card">
        <div class="ezd-stat-header">
            <p class="ezd-stat-label"><?php esc_html_e( 'Total Views', 'eazydocs' ); ?></p>
            <span class="dashicons dashicons-visibility ezd-stat-icon"></span>
        </div>
        <p class="ezd-stat-value">
            <?php
            $get_views   = $wpdb->get_var("SELECT SUM(meta_value+0) FROM $wpdb->postmeta WHERE meta_key='post_views_count'");
            $total_views = $get_views >= 1000 ? round($get_views/1000) . 'k' : $get_views;
            echo esc_html( $total_views );
            ?>
        </p>
    </div>

    <div class="ezd-stat-card">
        <div class="ezd-stat-header">
            <p class="ezd-stat-label"><?php esc_html_e( 'Positive Votes', 'eazydocs' ); ?></p>
            <span class="dashicons dashicons-thumbs-up ezd-stat-icon-positive"></span>
        </div>
        <p class="ezd-stat-value ezd-stat-value-positive">                    
            <?php echo esc_html( $total_liked ); ?>%
        </p>
    </div>

    <div class="ezd-stat-card">
        <div class="ezd-stat-header">
            <p class="ezd-stat-label"><?php esc_html_e( 'Negative Votes', 'eazydocs' ); ?></p>
            <span class="dashicons dashicons-thumbs-down ezd-stat-icon-negative"></span>
        </div>
        <p class="ezd-stat-value ezd-stat-value-negative">
            <?php echo esc_html( $total_disliked ); ?>%
        </p>
    </div>
</div>