<?php
$opt                    = get_option( 'eazydocs_settings' );
$widget_sidebar         = $opt['is_widget_sidebar'] ?? '';
$toc_switcher           = $opt['toc_switcher'] ?? '';
$toc_heading            = $opt['toc_heading'] ??  __( 'CONTENTS', 'eazydocs' );
?>
<div class="col-xl-2 col-lg-3 doc_right_mobile_menu sticky-lg-top">
    <div class="doc_rightsidebar scroll">
        <div class="open_icon" id="right">
            <i class="arrow_carrot-left"></i>
            <i class="arrow_carrot-right"></i>
        </div>
        
        <div class="pageSideSection">
            <?php
            /**
             * Contribution Buttons
             */
            eazydocs_get_template_part('tools/edit-add-doc');

            /**
             * Share Buttons
             */
            eazydocs_get_template_part('tools/share-btns');

            /**
             * Conditional Dropdown
             */
            eazydocs_get_template_part('tools/conditional-dropdown');

            /**
             * Font Size Switcher & Print Icon
             */
            eazydocs_get_template_part('tools/font-switcher');

            /**
             * Dark Mode switcher
             */
            eazydocs_get_template_part('tools/dark-mode-switcher');
            if( ! empty ( $toc_switcher ) ) :
                ?>
                <div class="table-of-content">
                    <h6><i class="icon_ul"></i> <?php echo esc_html( $toc_heading ); ?></h6>
                    <nav class="list-unstyled doc_menu toc_right" data-toggle="toc" id="eazydocs-toc"></nav>
                </div>
                <?php
            endif;

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