<?php
$query = new WP_Query( [
    'post_type'      => 'docs',
    'posts_per_page' => - 1,
    'post_parent'    => 0,
    'orderby'        => 'menu_order',
    'order'          => 'DESC'
]);
$count = $query->found_posts;
?>

<div class="tab-menu <?php echo $count > 12 ? '' : 'short'; ?>">
    <ul class="easydocs-navbar">
        <?php
        $i              = '';
        while ( $query->have_posts() ) : $query->the_post();
            $i ++;
            $depth_one_parents[] = get_the_ID();
            $is_active           = $i == 1 ? 'is-active' : '';
            $doc_counter    = get_pages( [
                'child_of'  => get_the_ID(),
                'post_type' => 'docs'
            ]);

	        $post_status =  get_post_status(get_the_ID());
	        global $post;

	        switch ($post_status){
                case 'publish':
                $post_format = 'admin-site-alt3';
                break;

                case 'private':
                $post_format = 'privacy';
                break;

                case 'draft':
                $post_format = 'edit-page';
                break;
            }
	        if ( !empty($post->post_password) ) {
		        $post_format = 'lock';
	        }
            ?>
            <li class="easydocs-navitem <?php echo esc_attr( $is_active ); ?>" data-rel="tab-<?php the_ID(); ?>">
                <div class="title">
                    <span title="<?php echo esc_attr($post_status); ?>" class="dashicons dashicons-<?php echo esc_attr($post_format); ?>"></span>
                    <?php the_title(); ?>
                </div>
                <div class="total-page">
                    <span>
                        <?php echo count( $doc_counter ) > 0 ? count( $doc_counter ) : ''; ?>
                    </span>
                </div>
                <div class="link">
                    <?php
                    if( current_user_can('editor') || current_user_can('administrator') ) :
                    do_action('eazydocs_parent_doc_duplicate', get_the_ID());
                    ?>
                    <a href="<?php echo get_edit_post_link( get_the_ID() ); ?>" class="link edit" target="_blank" title="<?php esc_attr_e('Edit this doc', 'eazydocs'); ?>">
                        <span class="dashicons dashicons-edit"></span>
                    </a>
                    <?php endif; ?>

                    <a href="<?php the_permalink(); ?>" class="link external-link" target="_blank" data-id="tab-<?php the_ID(); ?>" title="<?php esc_attr_e('View this doc item in new tab', 'easydocs') ?>">
                        <span class="dashicons dashicons-external"></span>
                    </a>
                    <?php
                    if( current_user_can('editor') || current_user_can('administrator') ) :
                    ?>
                    <a href="<?php echo admin_url( 'admin.php' ); ?>/Delete_Post.php?DeleteID=<?php echo get_the_ID(); ?>" class="link delete parent-delete" title="<?php esc_attr_e('Delete this doc permanently', 'eazydocs'); ?>">
                        <span class="dashicons dashicons-trash"></span>
                    </a>
                    <?php endif; ?>
                </div>
            </li>
            <?php
        endwhile;
        wp_reset_postdata();
        ?>
    </ul>
</div>