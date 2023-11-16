<?php
$opt                        = get_option( 'eazydocs_settings' );
$is_conditional_dropdown    = $opt['is_conditional_dropdown'] ?? '';
$condition_options          = $opt['condition_options'] ?? '';

if ( $is_conditional_dropdown == '1' && !empty( $condition_options ) ) :
    ?>
<select id="condition_options" name="condition_options" class="vodiapicker ezd-d-none">
    <?php
        foreach ( $condition_options as $option ) {
            ?>
    <option value="<?php echo sanitize_title($option['title']) ?>"
        data-content=" <?php echo esc_attr($option['icon']) ?>"><?php echo sanitize_title($option['title']) ?>
    </option>
    <?php
        }
        ?>
</select>
<div class="lang-select">
    <button class="ezd_btn_select" value=""></button>
    <div class="ezd_b">
        <ul id="ezd_a" class="ezd-list-unstyled"></ul>
    </div>
</div>
<?php
endif;