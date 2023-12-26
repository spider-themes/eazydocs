<?php
$opt = get_option( 'eazydocs_settings' );
$keywords_label = $opt['keywords_label'] ?? '';
$keywords = $opt['keywords'] ?? '';

if ( ezd_get_opt('is_keywords') == '1' ) : 
    ?>
    <div class="ezd_search_keywords">
        <?php 
        if ( !empty($keywords_label) ) :
            ?>
            <span class="label">
                <?php echo esc_html($keywords_label) ?>
            </span>
            <?php 
        endif;

        if ( !empty($keywords) || ezd_get_opt('is_dynamic_keywords') == '1' ) : 
            ?>
            <ul class="list-unstyled">
                <?php
                if ( !empty($keywords) ) : 
                    foreach ( $keywords as $keyword ) :
                        ?>
                        <li class="wow fadeInUp" data-wow-delay="0.2s">
                            <a href="#"> <?php echo esc_html($keyword['title']) ?> </a>
                        </li>
                        <?php
                    endforeach;
                endif;
                
                if ( ezd_get_opt('is_dynamic_keywords') == '1' ) :
                    global $wpdb;                    
                    $search_keyword = $wpdb->get_results( "SELECT keyword, COUNT(*) AS count FROM {$wpdb->prefix}eazydocs_search_keyword GROUP BY keyword ORDER BY count DESC LIMIT 20" );
                    if ( count( $search_keyword ) > 0 ) :
                        foreach ( $search_keyword as $key => $item ): 
                            ?>
                            <li class="wow fadeInUp" data-wow-delay="0.2s">
                                <a href="#"> <?php echo esc_html( $item->keyword ); ?> </a>
                            </li>
                            <?php 
                        endforeach;
                    endif;
                endif;
                ?>
            </ul>
            <?php 
        endif; 
        ?>

    </div>
    <?php 
endif;