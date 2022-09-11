<?php
$opt                    = get_option( 'eazydocs_settings' );
$article_print          = $opt['pr-icon-switcher'] ?? '';
$font_size_switcher     = $opt['font-size-switcher'] ?? '1';

if ( $font_size_switcher == 1 || $article_print == '1' ) :
    ?>
    <div id="font-switcher" class="d-flex justify-content-between align-items-center">
        <?php if ( $font_size_switcher == 1 ) : ?>
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
        <?php endif; ?>
        <?php
        if ( $article_print == '1' ) : ?>
            <a href="#" class="print"><i class="icon_printer"></i></a>
        <?php endif; ?>
    </div>
    <?php
endif;