<div class="question_menu docs3">
    <ul class="nav nav-tabs mb-5" role="tablist">
        <?php
        $slug_type = $settings['docs_slug_format'] ?? '';
        $widget_id = $this->get_id();
        if ( $settings['is_custom_order'] == 'yes' && !empty($settings['docs']) ) {
            $custom_docs = !empty($settings['docs']) ? $settings['docs'] : '';
            //echo '<pre>'.print_r($custom_docs, 1).'</pre>';
            $i = 0;
            foreach ( $custom_docs as $doc_item ) {
                $doc_id = $doc_item['doc'];
                // Active Doc
                if ( !empty($settings['active_doc']) ) {
                    $active = $doc_id == $settings['active_doc'] ? ' active' : '';
                } else {
                    $active = ( $i == 0 ) ? ' active' : '';
                }
                $post_title_slug = get_post_field('post_name', $doc_id);
                $doc_name = explode( ' ', get_the_title($doc_id) );

	            if( $slug_type == 1 ) {
		            $atts = "href='#doc3-{$post_title_slug}'";
	            }else{
		            $atts = "href='#doc3-{$widget_id}-{$doc_id}'";
	            }

                $atts .= " aria-controls='doc-{$post_title_slug}'";
                ?>
                <li class="nav-item">
                    <a <?php echo $atts; ?> id="<?php echo $post_title_slug; ?>-tab" class="nav-link<?php echo esc_attr($active) ?>" data-bs-toggle="tab">
                        <?php
                        echo get_the_post_thumbnail($doc_id, 'docy_16x16');
                        if ( $settings['is_tab_title_first_word'] == 'yes' ) {
                            echo wp_kses_post($doc_name[0]);
                        } else {
                            echo wp_kses_post($doc_item->post_title);
                        }
                        ?>
                    </a>
                </li>
                <?php
                ++$i;
            }
        } else {
            if ( $parent_docs ) :
                foreach ($parent_docs as $i => $doc) :
                    // Active Doc
                    if ( !empty($settings['active_doc']) ) {
                        $active = $doc->ID == $settings['active_doc'] ? ' active' : '';
                    } else {
                        $active = ( $i == 0 ) ? ' active' : '';
                    }
                    $doc_name = explode( ' ', $doc->post_title );
	                if( $slug_type == 1 ) {
		                $href       = "href='#doc3-{$doc->post_name}'";
	                }else{
		                $href       = "href='#doc3-{$widget_id}-{$doc->ID}'";
	                }
                    $aria_controls = " aria-controls='doc-{$doc->post_name}'";
                    ?>
                    <li class="nav-item">
                        <a <?php echo $href.$aria_controls; ?> id="doc3<?php echo $doc->post_name; ?>-tab" class="nav-link<?php echo esc_attr($active) ?>" data-bs-toggle="tab">
                            <?php
                            echo get_the_post_thumbnail($doc->ID, 'docy_16x16');
                            if ( $settings['is_tab_title_first_word'] == 'yes' ) {
                                echo wp_kses_post($doc_name[0]);
                            } else {
                                echo wp_kses_post($doc->post_title);
                            }
                            ?>
                        </a>
                    </li>
                <?php
                endforeach;
            endif;
        }
        ?>
    </ul>
    <div class="topic_list_inner">
        <div class="tab-content">
            <?php
            if ( !empty($docs) ) :
            foreach ( $docs as $i => $main_doc ) :
                // Active Doc
                if ( !empty($settings['active_doc']) ) {
                    $active = $main_doc['doc']->ID == $settings['active_doc'] ? 'show active' : '';
                } else {
                    $active = ($i == 0) ? 'show active' : '';
                }

	            if( $slug_type == 1 ) {
		            $doc_id       = $main_doc['doc']->post_name;
	            }else{
		            $doc_id       = "{$widget_id}-{$main_doc['doc']->ID}";
	            }
                ?>
                <div class="tab-pane doc_tab_pane fade <?php echo $active; ?>" id="doc3-<?php echo $doc_id ?>" role="tabpanel" aria-labelledby="<?php echo $doc_id ?>-tab">
                    <div class="row">
                        <?php
                        if ( !empty($main_doc['sections']) ) :
                        foreach ( $main_doc['sections'] as $section ) :
                            ?>
                            <div class="col-lg-4 col-md-6">
                                <div class="topic_list_item">
                                    <?php if ( !empty($section->post_title) ) : ?>
                                        <h4> <?php echo wp_kses_post($section->post_title); ?> </h4>
                                    <?php endif; ?>
                                    <ul class="navbar-nav">
                                        <?php
                                        $doc_items = get_children( array(
                                            'post_parent'    => $section->ID,
                                            'post_type'      => 'docs',
                                            'post_status'    => 'publish',
                                            'orderby'        => 'menu_order',
                                            'order'          => 'ASC',
                                            'posts_per_page' => !empty($settings['ppp_doc_items']) ? $settings['ppp_doc_items'] : -1,
                                        ));
                                        foreach ( $doc_items as $doc_item ) :
                                            ?>
                                            <li>
                                                <a href="<?php echo get_permalink($doc_item->ID) ?>">
                                                    <?php echo wp_kses_post($doc_item->post_title) ?>
                                                </a>
                                            </li>
                                            <?php
                                        endforeach;
                                        ?>
                                    </ul>
                                    <?php
                                    if ( !empty($settings['read_more']) ) : ?>
                                        <a class="text_btn dark_btn" href="#">
                                            <?php echo esc_html($settings['read_more']) ?> <i class="<?php ezd_arrow() ?>"></i>
                                        </a>
                                        <?php
                                    endif;
                                    ?>
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
            endif;
            ?>
        </div>
    </div>
</div>