<section class="doc_tag_area" id="Arrow_slides-<?php echo esc_attr($this->get_id()) ?>">
    <div class="tabs_sliders">
        <span class="scroller-btn left"><i class="arrow_carrot-left"></i></span>
        <ul class="nav nav-tabs doc_tag ezd-tab-menu slide_nav_tabs list-unstyled">
            <?php
            $slug_type = $settings[ 'docs_slug_format' ] ?? '';
            $widget_id = $this->get_id();
            if ($settings[ 'is_custom_order' ] == 'yes' && !empty($settings[ 'docs' ])) {
                $custom_docs = !empty($settings[ 'docs' ]) ? $settings[ 'docs' ] : '';
                $i = 0;
                foreach ( $custom_docs as $doc_item ) {
                    $doc_id = $doc_item[ 'doc' ];
                    // Active Doc
                    if (!empty($settings[ 'active_doc' ])) {
                        $active = $doc_id == $settings[ 'active_doc' ] ? ' active' : '';
                    } else {
                        $active = ($i == 0) ? ' active' : '';
                    }
                    $post_title_slug = get_post_field('post_name', $doc_id);
                    $doc_name = explode(' ', get_the_title($doc_id));

                    if ($slug_type == 1) {
                        $atts = "href='#doc-{$post_title_slug}'";
                    } else {
                        $atts = "href='#doc-{$widget_id}-{$doc_id}'";
                    }
                    ?>
                    <li class="nav-item">
                        <a data-rel="doc-<?php echo $post_title_slug; ?>"
                           class="nav-link ezd_tab_title<?php echo esc_attr($active) ?>">
                            <i class="icon_document_alt"></i>
                            <?php
                            if ($settings[ 'is_tab_title_first_word' ] == 'yes') {
                                echo wp_kses_post($doc_name[ 0 ]);
                            } else {
                                echo get_the_title($doc_id);
                            }
                            ?>
                        </a>
                    </li>
                    <?php
                    ++$i;
                }
            } else {
                if ($parent_docs) {
                    foreach ( $parent_docs as $i => $doc ) {
                        // Active Doc
                        if (!empty($settings[ 'active_doc' ])) {
                            $active = $doc->ID == $settings[ 'active_doc' ] ? ' active' : '';
                        } else {
                            $active = ($i == 0) ? ' active' : '';
                        }
                        $post_title_slug = $doc->post_name;
                        $doc_name = explode(' ', $doc->post_title);

                        if ($slug_type == 1) {
                            $atts = "href='#doc-{$post_title_slug}'";
                        } else {
                            $atts = "href='#doc-{$widget_id}-{$doc->ID}'";
                        }
                        ?>
                        <li class="nav-item">
                            <a data-rel="doc-<?php echo esc_attr($this->get_id()) ?>-<?php echo $post_title_slug; ?>"
                               class="nav-link ezd_tab_title<?php echo esc_attr($active) ?>">
                                <?php
                                if ($settings[ 'is_tab_title_first_word' ] == 'yes') {
                                    echo wp_kses_post($doc_name[ 0 ]);
                                } else {
                                    echo wp_kses_post($doc->post_title);
                                }
                                ?>
                            </a>
                        </li>
                        <?php
                    }
                }
            }
            ?>
        </ul>
        <span class="scroller-btn right"><i class="arrow_carrot-right"></i></span>
    </div>
    <div class="tab-content">
        <?php
        foreach ( $docs as $i => $main_doc ) :
            // Active Doc
            if (!empty($settings[ 'active_doc' ])) {
                $active = $main_doc[ 'doc' ]->ID == $settings[ 'active_doc' ] ? 'show active' : '';
            } else {
                $active = ($i == 0) ? 'show active' : '';
            }
            if ($slug_type == 1) {
                $doc_id = $main_doc[ 'doc' ]->post_name;
            } else {
                $doc_id = "{$widget_id}-{$main_doc['doc']->ID}";
            }
            ?>
            <div class="doc_tab_pane ezd-tab-box <?php echo $active; ?>" id="doc-<?php echo esc_attr($this->get_id()) ?>-<?php echo $doc_id ?>">
                <div class="ezd-grid ezd-grid-cols-12">
                    <?php
                    if (!empty($main_doc[ 'sections' ])) :
                        foreach ( $main_doc[ 'sections' ] as $section ) :
                            ?>
                            <div class="ezd-grid-column-full ezd-sm-col-6 ezd-lg-col-4">
                                <div class="doc_tag_item">
                                    <?php if (!empty($section->post_title)) : ?>
                                        <div class="doc_tag_title">
                                            <h4 class="ezd_item_title"><?php echo wp_kses_post($section->post_title); ?></h4>
                                            <div class="line"></div>
                                        </div>
                                    <?php endif; ?>
                                    <?php
                                    $doc_items = get_children(array(
                                        'post_parent' => $section->ID,
                                        'post_type' => 'docs',
                                        'post_status' => 'publish',
                                        'orderby' => 'menu_order',
                                        'order' => 'ASC',
                                        'posts_per_page' => !empty($settings[ 'ppp_doc_items' ]) ? $settings[ 'ppp_doc_items' ] : -1,
                                    ));
                                    if (!empty($doc_items)) : ?>
                                        <ul class="list-unstyled tag_list">
                                            <?php
                                            foreach ( $doc_items as $doc_item ) :
                                                ?>
                                                <li>
                                                    <a href="<?php echo get_permalink($doc_item->ID) ?>"
                                                       class="ezd_item_list_title">
                                                        <?php echo wp_kses_post($doc_item->post_title) ?>
                                                    </a>
                                                </li>
                                            <?php
                                            endforeach;
                                            ?>
                                        </ul>
                                    <?php
                                    endif;
                                    if (!empty($settings[ 'read_more' ])) : ?>
                                        <a href="<?php echo get_permalink($section->ID); ?>" class="learn_btn ezd_btn">
                                            <?php echo esc_html($settings[ 'read_more' ]) ?>
                                            <i class="<?php ezd_arrow() ?>"></i>
                                        </a>
                                    <?php endif; ?>
                                </div>
                            </div>
                        <?php
                        endforeach;
                    endif;
                    ?>
                </div>
            </div>
        <?php
        endforeach;
        ?>
    </div>
</section>