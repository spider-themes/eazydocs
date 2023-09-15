<?php 
 $sections = get_children( array(
    'post_type'      => 'docs',
    'post_status'    => 'publish',
    // 'orderby'        => 'menu_order',
    // 'order'          => $settings['order'],
    'posts_per_page' => $attributes['numberOfPosts'],
) );
?>

<div class="container">
    <div class="row">
        <?php
    foreach ( $sections as $section ) :
        $doc_items = get_children( array(
            'post_parent'    => $section->ID,
            'post_type'      => 'docs',
            'post_status'    => 'publish',
            'orderby'        => 'menu_order',
            'order'          => 'ASC',
            // 'posts_per_page' => !empty($settings['ppp_doc_items']) ? $settings['ppp_doc_items'] : -1,
        ));
        $doc_counter    = get_pages( [
            'child_of'  => $section->ID,
            'post_type' => 'docs',
        ]);
        ?>
        <div class="col-lg-4 col-sm-6">
            <div class="categories_guide_item box-item wow fadeInUp single-doc-layout-one">
                <div class="doc-top d-flex align-items-start">
                    <?php echo wp_get_attachment_image( get_post_thumbnail_id( $section->ID ) ); ?>
                    <a class="doc_tag_title" href="<?php echo get_the_permalink($section->ID); ?>">
                        <h4 class="title"> <?php echo get_the_title($section->ID); ?> </h4>
                        <span>
                            <?php echo count( $doc_counter ) > 0 ? count( $doc_counter ) : ''; ?>
                            <?php esc_html_e( 'Topics', 'eazyedocs' ) ?>
                        </span>
                    </a>
                </div>
                <ul class="list-unstyled tag_list">
                    <?php
                    foreach ( $doc_items as $doc_item ) : ?>
                    <li>
                        <a class="ct-content-text" href="<?php echo get_permalink($doc_item->ID) ?>">
                            <?php echo wp_kses_post($doc_item->post_title) ?>
                        </a>
                    </li>
                    <?php
                    endforeach;
                    ?>
                </ul>

            </div>
        </div>
        <?php
    endforeach;
    ?>
    </div>
</div>