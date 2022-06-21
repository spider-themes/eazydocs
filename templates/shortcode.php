<?php
$opt = get_option( 'eazydocs_settings' );

// Child docs per page
$layout          = 'grid';
// Check pro plugin class exists
if ( class_exists( 'EazyDocsPro' ) ) {
	$layout  = $opt['docs-archive-layout'] ?? $layout; // id of field
}
?>

<?php if ( $docs ) : ?>

<div class="eazydocs_shortcode">
    <div class="container">
        <div class="row" <?php do_action( 'eazydocs_masonry_wrap', $layout ); ?>>
            <?php
            $i = 1;
            foreach ( $docs as $main_doc ) :
                $doc_counter = get_pages( [
                    'child_of'  => $main_doc['doc']->ID,
                    'post_type' => 'docs',
                    'orderby'   => 'menu_order',
                    'order'     => 'asc'
                ] );

                $col_wrapper = $i == 1;
                if ( class_exists( 'EazyDocsPro' ) ) {
                    do_action( 'before_docs_column_wrapper', $col );
                } else { ?>
                    <div class="col-lg-<?php echo esc_attr( $col ); ?>">
                <?php } ?>

                    <div class="categories_guide_item wow fadeInUp">
                        <div class="doc-top d-flex align-items-start">
                            <?php echo get_the_post_thumbnail( $main_doc['doc']->ID, 'full', array( 'class' => 'featured-image' ) ); ?>
                            <a class="doc_tag_title" href="<?php echo get_permalink( $main_doc['doc']->ID ); ?>">
                                <h4 class="title">
                                    <?php echo $main_doc['doc']->post_title; ?>
                                </h4>
                                <span>
                                    <?php echo count( $doc_counter ) > 0 ? count( $doc_counter ) : ''; ?>
                                    <?php esc_html_e( 'Topics', 'eazyedocs' ) ?>
                                </span>
                            </a>
                        </div>
                        <?php if ( $main_doc['sections'] ) : ?>
                            <ul class="list-unstyled article_list">
                                <?php
                                foreach ( $main_doc['sections'] as $item ) :
                                    ?>
                                    <li>
                                        <a href="<?php echo get_permalink( $item->ID ); ?>">
                                            <?php echo esc_html( $item->post_title ); ?>
                                        </a>
                                    </li>
                                    <?php
                                endforeach;
                                ?>
                            </ul>
                        <?php endif; ?>
                        <a href="<?php echo get_permalink( $main_doc['doc']->ID ); ?>" class="doc_border_btn">
                            <?php echo $more; ?>
                            <i class="arrow_right"></i>
                        </a>
                    </div>
                 </div>
                <?php
            endforeach;
            ?>
        </div>
    </div>
</div>

<?php
endif;