<section class="doc6_community_area docs6 bew-topics">
    <div class="doc_community_info">
        <?php
        $delay = 0.1;
        $duration = 0.5;
            foreach( $docs as $doc ) :
            $doc_id = $doc['doc']->ID;
            ?>
            <div class="doc_community_item topic-item wow fadeInUp" data-wow-delay="<?php echo esc_attr($delay) ?>s">
                <div class="doc_community_icon ezd-docs5-icon-wrap">
                    <?php echo get_the_post_thumbnail( $doc_id, 'full'); ?>
                </div>
                <div class="doc_entry_content">
                    <a href="<?php echo get_the_permalink( $doc_id ); ?>">
                        <h4><?php echo wp_kses_post( $doc['doc']->post_title ); ?></h4>
                    </a>
                    <p><?php ezd_widget_excerpt( $doc_id, 15 ); ?></p>
                    <div class="doc_entry_info">
                        <ul class="list-unstyled author_avatar">
                            <?php
                            $docs = new WP_Query(array(
                                'post_type' => 'docs',
                                'post_per_page' => -1,
                                'post_parent' => $doc_id,
                            ));
                            $doc_counter    = get_pages( [
                                'child_of'  => $doc_id,
                                'post_type' => 'docs'
                            ]);
                            $author_ids = [];
                            $author_names = '';
                            $show_avatar_count =  2;

                            $i = 1;
                            while ( $docs->have_posts() ) : $docs->the_post();
                                $author_ids[get_the_author_meta('ID')] =  '';
                                ++$i;
                            endwhile;

                            $author_count = count($author_ids);
                            $ii = 0;
                            foreach ( $author_ids as $author_id => $v ) {
                                if ( $ii == $show_avatar_count ) {
                                    break;
                                }
                                echo '<li> ' . get_avatar($author_id, 36) . ' </li>';
                                $author_separator = $ii == $author_count ? '' : ', ';
                                $author_names .= get_the_author_meta('display_name', $author_id).$author_separator;
                                ++$ii;
                            }
                            wp_reset_postdata();
                            $remaining_authors_count = $author_count - $show_avatar_count;
                            if ( $author_count > $show_avatar_count ) : ?>
                                <li class="avatar_plus">+<?php echo $remaining_authors_count; ?></li>
                            <?php endif; ?>
                        </ul>
                        <div class="text">
                            <?php echo count( $doc_counter ) ?> <?php esc_html_e('Article in this Docs.'); ?> <br>
                            <?php esc_html_e('Written by ', 'eazydocs'); echo $author_names ?>
                        </div>
                    </div>
                </div>
            </div>
        <?php endforeach; ?>
    </div>
</section>