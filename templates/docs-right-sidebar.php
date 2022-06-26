<?php
$opt                        = get_option( 'eazydocs_settings' );
$is_conditional_dropdown    = $opt['is_conditional_dropdown'] ?? '';
$condition_options          = $opt['condition_options'] ?? '';
$article_print              = $opt['pr-icon-switcher'] ?? '';
$font_size_switcher         = $opt['font-size-switcher'] ?? '1';
$widget_sidebar             = $opt['is_widget_sidebar'] ?? '';
?>
<div class="col-xl-2 col-lg-3 doc_right_mobile_menu sticky-lg-top">
    <div class="doc_rightsidebar scroll">
        <div class="open_icon" id="right">
            <i class="arrow_carrot-left"></i>
            <i class="arrow_carrot-right"></i>
        </div>
        <div class="pageSideSection">
            <?php
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
                        if( jQuery("#condition_options").val() == "' . esc_js(sanitize_title( $option['title'] )) . '" ) {
                            jQuery(".' . esc_js(sanitize_title( $option['title'] )) . '").show();
                        } else {
                            jQuery(".' . esc_js(sanitize_title( $option['title'] )) . '").hide();
                        }
                        jQuery("#condition_options").change(function() {
                            if( jQuery("#condition_options").val() == "' . esc_js(sanitize_title( $option['title'] )) . '" ) {
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
            if( ! empty( $font_size_switcher == 1 ) ) :
            ?>
            <div id="font-switcher" class="d-flex justify-content-between align-items-center">
                <div id="rvfs-controllers" class="fontsize-controllers group">
                    <div class="btn-group">
                        <button id="switcher-small" class="rvfs-decrease btn" title="<?php esc_attr_e( 'Decrease font size', 'eazydocs' ); ?>">
							<?php esc_html_e( 'A-', 'eazydocs' ); ?>
                        </button>
                        <button id="switcher-default" class="rvfs-reset btn" title="<?php esc_attr_e( 'Default font size', 'eazydocs' ); ?>">
							<?php esc_html_e( 'A', 'eazydocs' ); ?>
                        </button>
                        <button id="switcher-large" class="rvfs-increase btn" title="<?php esc_attr_e( 'Increase font size', 'eazydocs' ); ?>">
							<?php esc_html_e( 'A+', 'eazydocs' ); ?>
                        </button>
                    </div>
                </div>
				<?php
                endif;
				if ( $article_print == '1' ) : ?>
                    <a href="#" class="print"><i class="icon_printer"></i></a>
				<?php endif; ?>
            </div>

            <?php
            $is_dark_switcher = $opt['is_dark_switcher'] ?? '';
            if ( $is_dark_switcher == '1' ) : ?>
                <div class="doc_switch d-flex align-items-center">
                    <label for="ezd_dark_switch" class="tab-btn tab-btns light-mode"><i class="icon_lightbulb_alt"></i></label>
                    <input type="checkbox" name="ezd_dark_switch" id="ezd_dark_switch" class="tab_switcher">
                    <label for="ezd_dark_switch" class="tab-btn dark-mode"><i class="far fa-moon"></i></label>
                </div>
            <?php endif; ?>

            <div class="table-of-content">
                <h6><i class="icon_ul"></i> <?php esc_html_e( 'CONTENTS', 'eazydocs' ); ?></h6>
                <nav class="list-unstyled doc_menu toc_right" data-toggle="toc" id="eazydocs-toc"></nav>
            </div>

            <?php
            // Widgets area
            if ( is_active_sidebar('doc_sidebar') && $widget_sidebar == 1 ) :
                ?>
                <div class="ezd-widgets">
                    <?php dynamic_sidebar('doc_sidebar') ?>
                </div>
                <?php
            endif;
            ?>
        </div>
    </div>
</div>