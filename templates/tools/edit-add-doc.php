<?php
$is_constribution              = ezd_get_opt('is_doc_contribution') ?? '';
$is_add_doc                    = ezd_get_opt('frontend_add_switcher') ?? '';
$is_edit_doc                   = ezd_get_opt('frontend_edit_switcher') ?? '';
$user_login_page_id            = ezd_get_opt('docs_frontend_login_page') ?? '';

if ( eaz_fs()->is_plan('promax') ) :
    if ( $is_constribution ) :
        if ( $is_add_doc || $is_edit_doc ) :
            if ( ! empty( $user_login_page_id ) ) :
                ?>
                <div class="contribut-btns">
                    <?php
                    if ( $is_edit_doc ) {
                        do_action('eazydocs_fronted_editing', get_the_ID());                       
                    }
                    
                    if ( $is_add_doc ) {

                        // get total number of child post
                        $args = array(
                            'post_type'      => 'docs',
                            'post_parent'    => get_the_ID(),
                            'posts_per_page' => -1,
                            'post_status'    => 'publish',
                        );
                        $child_posts = get_children( $args );
                        $order       = count($child_posts) + 1;
                        echo do_action('eazydocs_fronted_submission', get_the_ID(), $order);
                    }
                    ?>
                </div>
                <?php
            endif;
        endif;
    endif;
endif;