<?php
$query = new WP_Query([
    'post_type'      => 'docs',
    'posts_per_page' => -1,
    'post_parent'    => 0,
    'orderby'        => 'menu_order',
    'order'          => 'ASC',
]);
$count = $query->found_posts;
?>

<div class="dd parent-nestable tab-menu <?php echo $count > 12 ? '' : 'short'; ?>">
    <ol class="easydocs-navbar sortabled dd-list">
        <?php
        $i = '';
        while ( $query->have_posts() ) : $query->the_post();
            $i++;
            $depth_one_parents[]    = get_the_ID();
            $is_active              = $i == 1 ? 'is-active' : '';
            $doc_counter            = get_pages([
                'child_of'          => get_the_ID(),
                'post_type'         => 'docs',
                'post_status'       => ['publish', 'draft', 'private']
            ]);

            $post_status =  get_post_status(get_the_ID());
            global $post;

            switch ( $post_status ) {
                case 'publish':
                    $post_format = 'admin-site-alt3';
                    $doc_status = esc_html__('Public Doc', 'eazydocs');
                    break;

                case 'private':
                    $post_format = 'privacy';
                    $doc_status = esc_html__('Private Doc', 'eazydocs');
                    break;

                case 'draft':
                    $post_format = 'edit-page';
                    $doc_status = esc_html__('Drafted Doc', 'eazydocs');
                    break;
            }

            if ( !empty($post->post_password) ) {
                $post_format = 'lock';
                $doc_status = esc_html__('Password Protected Doc', 'eazydocs');
            }
            ?>
            <li class="easydocs-navitem dd-item dd3-item <?php echo esc_attr($is_active); ?>" data-rel="tab-<?php the_ID(); ?>" data-id="<?php the_ID(); ?>">
                <div class="title">
                    <span title="<?php echo esc_attr($doc_status); ?>" class="dashicons dashicons-<?php echo esc_attr($post_format); ?>"></span>
                    <?php the_title(); ?>
                </div>
                <div class="total-page">
                    <span>
                        <?php echo count($doc_counter) > 0 ? count($doc_counter) : ''; ?>
                    </span>
                </div>
                <div class="link">
                    <?php
                    if ( ezd_is_premium() ) {
                        do_action('eazydocs_parent_doc_drag');
                    }

                    if ( current_user_can('publish_pages') ) :
                        ?>
                        <a href="<?php echo get_edit_post_link(get_the_ID()); ?>" class="link edit" target="_blank" title="<?php esc_attr_e('Edit this doc', 'eazydocs'); ?>">
                            <span class="dashicons dashicons-edit"></span>
                        </a>
                        <?php
                    endif;
                    ?>

                    <a href="<?php the_permalink(); ?>" class="link external-link" target="_blank" data-id="tab-<?php the_ID(); ?>" title="<?php esc_attr_e('View this doc item in new tab', 'eazydocs') ?>">
                        <span class="dashicons dashicons-external"></span>
                    </a>

                    <?php 
                    if ( current_user_can('publish_pages') ) : 
                        ?>
                        
                        <a href="<?php echo admin_url('admin.php'); ?>?Doc_Delete=yes&_wpnonce=<?php echo wp_create_nonce(get_the_ID()); ?>&DeleteID=<?php echo get_the_ID(); ?>" class="link delete parent-delete" title="<?php esc_attr_e('Move to Trash', 'eazydocs'); ?>">
                            <span class="dashicons dashicons-trash"></span>
                        </a>
                        
                        <span class="ezd-admin-bulk-options" id="bulk-options-<?php echo get_the_ID(); ?>">
                            <span class="dashicons dashicons-arrow-down-alt2"></span>
                            <span class="ezd-admin-bulk-actions">
                                <?php
                                    if ( ezd_is_premium() ) :
                                        do_action('eazydocs_parent_doc_duplicate', get_the_ID());
                                        do_action('eazydocs_doc_visibility', get_the_ID());

                                        $left_type      = get_post_meta(get_the_ID(), 'ezd_doc_left_sidebar_type', true);
                                        $left_type      = '&left_type=' . $left_type;

                                        $left_content   = get_post_meta(get_the_ID(), 'ezd_doc_left_sidebar', true);
                                        $left_content   = '&left_content=' . $left_content;

                                        $right_type     = get_post_meta(get_the_ID(), 'ezd_doc_right_sidebar_type', true);
                                        $right_type     = '&right_type=' . $right_type;

                                        $right_content  = get_post_meta(get_the_ID(), 'ezd_doc_right_sidebar', true);
                                        $right_content  = '&right_content=' . $right_content;

                                        do_action('eazydocs_doc_sidebar', get_the_ID(), $left_type, $left_content, $right_type, $right_content);
                                    else :
                                        ?>
                                        <a href="admin.php?page=eazydocs-pricing" target="_blank" class="docs-duplicate eazydocs-pro-notice" title="<?php esc_attr_e('Duplicate this doc with the child docs.', 'eazydocs'); ?>">
                                            <span class="dashicons dashicons-admin-page"></span>
                                            <span><?php esc_html_e('Duplicate', 'eazydocs'); ?></span>
                                        </a>
                                        <a href="admin.php?page=eazydocs-pricing" target="_blank" class="docs-visibility eazydocs-pro-notice" title="<?php esc_attr_e('Docs visibility', 'eazydocs'); ?>">
                                            <span class="dashicons dashicons-visibility"></span>
                                            <span> <?php esc_html_e('Visibility', 'eazydocs'); ?> </span>
                                        </a>
                                        <a href="admin.php?page=eazydocs-pricing" target="_blank" class="docs-sidebar eazydocs-pro-notice" title="<?php esc_attr_e('Docs sidebar', 'eazydocs'); ?>">
                                            <span class="dashicons dashicons-welcome-widgets-menus"></span>
                                            <span> <?php esc_html_e('Sidebar', 'eazydocs'); ?> </span>
                                        </a>
                                        <?php
                                    endif;
                                ?>
                            </span>
                        </span>
                        <?php 
                    endif;
                    ?>

                </div>
            </li>
            <?php
        endwhile;
        wp_reset_postdata();
        ?>
    </ol>
</div>