<?php
$opt                        = get_option( 'eazydocs_settings' );
$is_conditional_dropdown    = $opt['is_conditional_dropdown'] ?? '';
$condition_options          = $opt['condition_options'] ?? '';

if ( $is_conditional_dropdown == '1' && !empty( $condition_options ) ) :
    wp_enqueue_style( 'font-awesome-5' );
    wp_enqueue_style( 'bootstrap-select' );
    wp_enqueue_script( 'bootstrap-select' );
    ?>
    <select id="condition_options" name="condition_options" class="bs-select">
        <?php
        foreach ( $condition_options as $option ) {
            ?>
            <option value="<?php echo sanitize_title($option['title']) ?>" data-content="<i class='<?php echo esc_attr($option['icon'])."'> </i> " . esc_html($option['title']) ?>"> </option>
            <?php
        }
        ?>
    </select>
    <script>
        jQuery(document).ready(function() {
            <?php
            foreach ( $condition_options as $option ) {
                echo '
                        if ( jQuery("#condition_options").val() == "' . esc_js(sanitize_title( $option['title'] )) . '" ) {
                            jQuery(".' . esc_js(sanitize_title( $option['title'] )) . '").show();
                        } else {
                            jQuery(".' . esc_js(sanitize_title( $option['title'] )) . '").hide();
                        }
                        jQuery("#condition_options").change(function() {
                            if ( jQuery("#condition_options").val() == "' . esc_js(sanitize_title( $option['title'] )) . '" ) {
                                jQuery(".' . esc_js(sanitize_title( $option['title'] )) . '").show();
                            } else {
                                jQuery(".' . esc_js(sanitize_title( $option['title'] )) . '").hide();
                            }
                        })
                        ';
            }
            echo "jQuery('.bs-select').selectpicker();";
            ?>
        })
    </script>
    <?php
endif;