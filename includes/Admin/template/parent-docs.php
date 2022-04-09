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
        $child_counter = [];
        $i              = '';
        while ( $query->have_posts() ) : $query->the_post();
            $i ++;
            $depth_one_parents[] = get_the_ID();
            $is_active           = $i == 1 ? 'is-active' : '';
            $doc_counter    = get_pages( [
                'child_of'  => get_the_ID(),
                'post_type' => 'docs'
            ]);

            if ( is_array( $doc_counter ) ) {
                foreach ( $doc_counter as $docs ) {
                    $child_counter[] = $docs->ID;
                }
            }
            $child_docs  = implode( ",", $child_counter );




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

	        $doc_count = get_pages( array( 'child_of' => get_the_ID(), 'post_type' => 'docs'));
	        $child_counter = [];
	        foreach($doc_count as $count){
		        $child_counter[] = $count->ID;
	        }
            ?>
            <li class="easydocs-navitem <?php echo esc_attr( $is_active ); ?>" data-rel="tab-<?php the_ID(); ?>">
                <div class="title">
                    <span class="dashicons dashicons-<?php echo esc_attr($post_format); ?>"></span>
                    <?php the_title(); ?>
                </div>
                <div class="total-page">
                    <span>
                        <?php echo count( $doc_counter ) > 0 ? count( $doc_counter ) : ''; ?>
                    </span>
                </div>
                <div class="link">
                    <a href="<?php echo get_edit_post_link( get_the_ID() ); ?>" class="link edit" target="_blank">
                        <img src="<?php echo EAZYDOCS_IMG ?>/admin/edit.svg" alt="<?php esc_attr_e( 'Edit Icon', 'eazydocs' ); ?>" class="edit-img">
                    </a>
                    <a href="<?php the_permalink(); ?>" class="link external-link" target="_blank" data-id="tab-<?php the_ID(); ?>">
                        <img src="<?php echo EAZYDOCS_IMG ?>/icon/external.svg" alt="<?php esc_attr_e('External icon', 'eazydocs') ?>">
                    </a>
                    <a href="<?php echo admin_url( 'admin.php' ); ?>/Delete_Post.php?ID=<?php echo implode(", ", $child_counter) .','.get_the_ID(); ?>" class="link delete parent-delete">
                        <img src="<?php echo EAZYDOCS_IMG ?>/admin/delete2.svg" alt="<?php esc_attr_e( 'Delete Icon', 'eazydocs' ); ?>">
                    </a>
                </div>
            </li>
            <?php
        endwhile;
        wp_reset_postdata();
        ?>
    </ul>
</div>