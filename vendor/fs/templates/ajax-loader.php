<?php
    if ( ! defined( 'ABSPATH' ) ) {
        exit;
    }
?>

<div class="fs-ajax-loader" style="display: none">
    <?php
    for ( $i = 1; $i <= 8; $i ++ ) : ?>
        <div class="fs-ajax-loader-bar fs-ajax-loader-bar-<?php echo esc_attr($i); ?>"></div>
        <?php
    endfor; ?>
</div>
