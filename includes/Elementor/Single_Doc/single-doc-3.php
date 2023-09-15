<?php
$title_tag = !empty($settings['title_tag']) ? $settings['title_tag'] : 'h2';
?>

<div class="row topic_list_inner">
    <?php
    $delay = 0.2;
    foreach ( $sections as $section ) :
        $doc_items = get_children( array(
            'post_parent'    => $section->ID,
            'post_type'      => 'docs',
            'post_status'    => 'publish',
            'orderby'        => 'menu_order',
            'order'          => 'ASC',
            'posts_per_page' => !empty($settings['ppp_doc_items']) ? $settings['ppp_doc_items'] : -1,
        ));
        $all_doc_items = get_children( array(
            'post_parent'    => $section->ID,
            'post_type'      => 'docs',
            'post_status'    => 'publish',
            'posts_per_page' => -1,
        ));
        ?>
        <div class="col-lg-<?php echo esc_attr( $ppp_column ); ?> col-sm-6">
            <div class="topic_list_item box-item wow fadeIn" data-wow-delay="0.2s">
                <?php
                if ( !empty($section->post_title) ) :
                    ?>
                    <a href="<?php echo get_permalink($section->ID); ?>" class="topic-title">
                        <h4 class="ct-heading-text">
                            <?php  echo get_the_post_thumbnail($section->ID, 'full'); ?>
                            <?php echo wp_kses_post( $section->post_title ); ?>
                        </h4>
                        <span class="count">(<?php echo count($all_doc_items) ?>)</span>
                    </a>
                    <?php
                endif;

                if ( !empty($doc_items) ) : ?>
                    <ul class="navbar-nav">
                        <?php
                        foreach ( $doc_items as $doc_item ) :
                            ?>
                            <li>
                                <a class="ct-content-text" href="<?php echo get_permalink($doc_item->ID) ?>">
                                    <i class="icon_document_alt"></i>
                                    <?php echo wp_kses_post($doc_item->post_title) ?>
                                </a>
                            </li>
                        <?php
                        endforeach;
                        ?>
                    </ul>
                    <?php
                endif;
                ?>
                <?php
                if ( !empty($settings['read_more']) ) : ?>
                    <a href="<?php echo get_permalink($section->ID); ?>" class="text_btn dark_btn">
                        <?php echo wp_kses_post($settings['read_more']) ?>
                        <i class="<?php ezd_arrow() ?>"></i>
                    </a>
                <?php endif; ?>
            </div>
        </div>
        <?php
    endforeach;
    ?>
</div>