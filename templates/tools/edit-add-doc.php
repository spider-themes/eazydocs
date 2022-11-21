<?php
$is_constribution              = eazydocs_get_option('is_doc_contribution', 'eazydocs_settings') ?? '';
$is_add_doc                    = eazydocs_get_option('frontend_add_switcher', 'eazydocs_settings') ?? '';
$is_edit_doc                   = eazydocs_get_option('frontend_edit_switcher', 'eazydocs_settings') ?? '';
$user_login_page_id            = eazydocs_get_option('docs_frontend_login_page', 'eazydocs_settings') ?? '';

if ( eaz_fs()->is_plan('promax') ) :
    if ( $is_constribution ) :
        if ( $is_add_doc || $is_edit_doc ) :
            if ( ! empty( $user_login_page_id ) ) :
                ?>
                <div class="contribut-btns">
                    <?php
                    $edit_url          = get_the_ID();
                    $doc_edit_btn_text = eazydocs_get_option('frontend_edit_btn_text', 'eazydocs_settings') ?? esc_html__('Edit', 'eazydocs');

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
                            do_action('eazydocs_fronted_submission', admin_url('/post-new.php?post_type=docs'), get_the_ID());
                        } else {
                            do_action('eazydocs_fronted_submission', '?add_new='.admin_url('/post-new.php?post_type=docs'), get_the_ID());
                        }
                    }
                    ?>
                </div>
            <?php
            endif;
        endif;
    endif;
endif;