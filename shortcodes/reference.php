<?php
add_shortcode('reference', function ($atts, $content) {
    static $counter = 1;
    if (!ezd_unlock_themes()) return false;
    $number = $counter++;
    ob_start(); ?>
    <span ezd-note-serial="<?php echo esc_attr($number); ?>" id="serial-id-<?php echo esc_attr($number); ?>" class="ezd-footnotes-link-item" data-bs-original-title="<?php echo esc_attr($content); ?>"><i onclick="location.href='#note-name-<?php echo esc_attr($number); ?>'">[<?php echo esc_html($number); ?>]</i><span class="ezd-footnote-content"><?php $output = preg_replace(['/<br\s*\/?>/i', '/<p([^>]*)>/', '/<\/p>/'], ['', '<span$1>', '</span>'], do_shortcode($content)); echo wp_kses_post( $output ); ?></span></span> <?php return ob_get_clean();
});