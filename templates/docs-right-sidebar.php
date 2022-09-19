<?php
$opt                        = get_option( 'eazydocs_settings' );
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
            $is_constribution              = eazydocs_get_option('is_doc_contribution', 'eazydocs_settings') ?? '';
            $is_add_doc                    = eazydocs_get_option('frontend_add_switcher', 'eazydocs_settings') ?? '';
            $is_edit_doc                   = eazydocs_get_option('frontend_edit_switcher', 'eazydocs_settings') ?? '';
            $user_login_page_id            = eazydocs_get_option('docs_frontend_login_page', 'eazydocs_settings') ?? '';

            if ( eaz_fs()->is_plan__premium_only('promax') ) :
                if( $is_constribution ) :
                    if( $is_add_doc || $is_edit_doc ) :
                        if ( ! empty( $user_login_page_id ) ) : 
                        ?>
                        <div class="contribut-btns">
                            <?php
                                $edit_url                   = get_the_ID();
                                $doc_edit_btn_text          = eazydocs_get_option('frontend_edit_btn_text', 'eazydocs_settings') ?? esc_html__('Edit', 'eazydocs-pro');

                                if ( $is_edit_doc == 1 ) {
                                    $doc_edit_url = get_edit_post_link(get_the_ID());
                                
                                    if ( is_user_logged_in() ) {
                                        do_action('eazydocs_fronted_editing', $doc_edit_url);
                                    } else {
                                        do_action('eazydocs_fronted_editing', '?edit_doc_url='.$edit_url);
                                    }
                                }
                                
                                if ( $is_add_doc == 1 ) {
                                    if ( is_user_logged_in() ) {
                                        do_action('eazydocs_fronted_submission', admin_url('/post-new.php?post_type=docs'));
                                    } else {
                                        do_action('eazydocs_fronted_submission', '?add_new='.admin_url('/post-new.php?post_type=docs'));
                                    } 
                                } 
                            ?>
                        </div>
                        <?php
                    endif;
                endif;
                endif;
            endif;

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
            ?>

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