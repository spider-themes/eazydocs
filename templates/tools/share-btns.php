<?php
$is_copy_link = eazydocs_get_option('is_copy_link', 'eazydocs_settings') ?? '';
$copy_link_text = eazydocs_get_option('copy_link_text', 'eazydocs_settings') ?? 'Copy Link';
$copy_link_text_success = eazydocs_get_option('copy_link_text_success', 'eazydocs_settings') ?? 'Copied!';
?>

<?php if ( $is_copy_link ) : ?>
    <div class="share-this-doc" data-success-message="<?php echo esc_attr($copy_link_text_success) ?>">
        <i class="icon_link_alt"></i>
        <span> <?php echo esc_html($copy_link_text) ?> </span>
    </div>
<?php endif; ?>