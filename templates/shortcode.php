<?php
$depth_one_parents = [];
$btn_text          = esc_html__( 'View Details', 'eazydocs' );
$query             = new WP_Query( [
	'post_type'      => 'docs',
	'posts_per_page' => - 1,
	'post_parent'    => 0
] );
?>
<div class="container">
    <div class="row">
        <?php
        $i = 1;
        while ( $query->have_posts() ) : $query->the_post();
            $depth_one_parents[] = get_the_ID();
            $col_wrapper         = $i == 1;

            if ( class_exists( 'EazyDocsPro' ) ) {
                $cz_options = get_option( 'eazydocs_customizer' ); // prefix of framework
                $docs_col   = $cz_options['docs-column']; // id of field
                $btn_text   = $cz_options['docs-view-more']; // id of field
                do_action( 'before_docs_column_wrapper', $docs_col );
            } else { ?>
                <div class="col-lg-4 col-sm-6">
            <?php } ?>

            <div class="categories_guide_item wow fadeInUp">
                <?php the_post_thumbnail( 'full' ) ?>
                <a class="doc_tag_title" href="<?php the_permalink(); ?>">
                    <h4 class="title"><?php the_title(); ?></h4>
                </a>
                <ul class="list-unstyled tag_list">
                    <?php
                    if ( is_array( $depth_one_parents ) ) :
                        foreach ( $depth_one_parents as $item ) :
                            $children = get_children( array( 'post_parent' => $item ) );
                            if ( is_array( $children ) ) :
                                foreach ( $children as $child )  :
                                    ?>
                                    <li>
                                        <a href="<?php echo get_permalink( $child->ID ); ?>">
                                            <?php echo $child->post_title; ?>
                                        </a>
                                    </li>
                                <?php endforeach;
                            endif;
                        endforeach;
                    endif;
                    ?>
                </ul>
                <a href="<?php the_permalink(); ?>" class="doc_border_btn">
                    <?php echo esc_html( $btn_text ); ?>
                    <i class="arrow_right"></i>
                </a>
            </div>
            </div>
            <?php
            $i ++;
        endwhile;
        ?>
    </div>
</div>
<?php
wp_reset_postdata();