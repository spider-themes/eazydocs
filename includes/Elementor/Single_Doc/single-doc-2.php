<?php
?>
<section class="recommended_topic_area">
    <div class="recommended_topic_inner">
        <?php ezd_el_image($settings['bg_shape'], 'curve shape', 'doc_shap_one') ?>
        <?php if ( $settings['is_bg_objects'] == 'yes' ) : ?>
        <div class="doc_round one" data-parallax='{"x": -80, "y": -100, "rotateY":0}'></div>
        <div class="doc_round two" data-parallax='{"x": -10, "y": 70, "rotateY":0}'></div>
        <?php endif; ?>
        <?php if ( !empty($settings['title'] || $settings['subtitle']) ) : ?>
        <div class="doc_title text-center">
            <?php echo !empty($settings['title']) ? sprintf( '<%1$s class="title" data-animation="wow fadeInUp" data-wow-delay="0.2s"> %2$s </%1$s>', $title_tag, nl2br($settings['title']) ) : ''; ?>
            <?php if (!empty($settings['subtitle']) ) : ?>
            <p class="subtitle wow fadeInUp" data-wow-delay="0.4s">
                <?php echo wp_kses_post($settings['subtitle']) ?> </p>
            <?php endif; ?>
        </div>
        <?php endif; ?>
        <div class="ezd-grid <?php echo esc_attr( $ppp_column ); ?>">
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
                    ?>
            <div class="recommended_item box-item wow fadeInUp" data-wow-delay="<?php echo esc_attr($delay) ?>s">
                <?php
                            if ( has_post_thumbnail($section->ID) ) {
                                echo get_the_post_thumbnail($section->ID, 'full');
                            }

                            if ( !empty($section->post_title) ) { ?>
                <a href="<?php echo get_permalink($section->ID); ?>">
                    <h3 class="ct-heading-text"> <?php echo wp_kses_post( $section->post_title ); ?> </h3>
                </a>
                <?php
                            }

                            if ( !empty($doc_items) ) : ?>
                <ul class="list-unstyled">
                    <?php
                                    foreach ( $doc_items as $doc_item ) :
                                        ?>
                    <li>
                        <a class="ct-content-text" href="<?php echo get_permalink($doc_item->ID) ?>">
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
            </div>
            <?php
                    $delay = $delay + 0.1;
                endforeach;
                ?>
        </div>
        <?php
        if ( $settings['section_btn'] == 'yes' && !empty($settings['section_btn_txt']) ) : ?>
        <div class="text-center wow fadeInUp" data-wow-delay="0.2s">
            <a href="<?php echo esc_url($settings['section_btn_url']); ?>" class="question_text">
                <?php echo wp_kses_post($settings['section_btn_txt']) ?>
            </a>
        </div>
        <?php
        endif;
        ?>
    </div>
</section>