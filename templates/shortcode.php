<?php
$opt = get_option( 'eazydocs_settings' ); // prefix of framework

$orderby            = 'ID';
$order              = 'desc';
$showpost           = $opt['docs-number'] ?? -1;
$articles_number    = $opt['articles_number'] ?? -1;
$layout             = 'grid';
if ( class_exists( 'EazyDocsPro' ) ) {
	$orderby        = $opt['docs-order-by'] ?? 'ID'; // id of field
	$order          = $opt['docs-order'] ?? 'desc'; // id of field
	$layout         = $opt['docs-archive-layout'] ?? 'masonry'; // id of field
}

$depth_one_parents = [];
$btn_text          = esc_html__( 'View Details', 'eazydocs' );
$query             = new WP_Query( [
	'post_type'      => 'docs',
	'posts_per_page' => $showpost,
	'orderby'        => $orderby,
	'post_status'    => ['publish'],
	'order'          => $order,
	'post_parent'    => 0
]);
?>

<div class="eazydocs_shortcode">
    <div class="container">
        <div class="row" <?php do_action('eazydocs_masonry_wrap', $layout); ?>>
            <?php
            $i = 1;

            while ( $query->have_posts() ) : $query->the_post();
                $doc_counter    = get_pages( [
                    'child_of'  => get_the_ID(),
                    'post_type' => 'docs',
                ]);

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
                    <div class="doc-top d-flex align-items-start">
                        <?php the_post_thumbnail( 'full', array('class', 'featured-image') ) ?>
                        <a class="doc_tag_title" href="<?php the_permalink(); ?>">
                            <h4 class="title"> <?php the_title(); ?> </h4>
                            <span>
                                <?php echo count( $doc_counter ) > 0 ? count( $doc_counter ) : ''; ?>
                                <?php esc_html_e( 'Topics', 'eazyedocs' ) ?>
                            </span>
                        </a>
                    </div>
                    <ul class="list-unstyled article_list">
                        <?php
                        $children = get_children([
                            'post_parent'       => get_the_ID(),
                            'post_status'       => ['publish'],
                            'posts_per_page'    => $articles_number
                        ]);
                        if ( is_array( $children ) ) :
                            foreach ( $children as $item ) :
                                ?>
                                <li>
                                    <a href="<?php echo get_permalink( $item->ID ); ?>">
                                        <?php echo esc_html($item->post_title); ?>
                                    </a>
                                </li>
                                <?php
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
</div>
<?php
wp_reset_postdata();