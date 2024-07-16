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

        if ( ezd_get_opt('keywords_by') == 'static' || ezd_get_opt('keywords_by') == 'dynamic' ) : 
            ?>
            <ul class="list-unstyled">
                <?php
                if ( ezd_get_opt('keywords_by') == 'static' ) : 
                    if ( !empty($keywords) ) : 
                        foreach ( $keywords as $keyword ) :
                            ?>
                            <li class="wow fadeInUp" data-wow-delay="0.2s">
                                <a href="#"> <?php echo esc_html($keyword['title']) ?> </a>
                            </li>
                            <?php
                        endforeach;
                    endif;
                else :
                    global $wpdb;    
                    // @codingStandardsIgnoreLine WordPress.DB.DirectDatabaseQuery.DirectQuery                
                    $search_keyword = $wpdb->get_results( "SELECT keyword, COUNT(*) AS count FROM {$wpdb->prefix}eazydocs_search_keyword GROUP BY keyword ORDER BY count DESC" );
                    $all_keys = [];
                    if ( count( $search_keyword ) > 0 ) :
                        foreach ( $search_keyword as $item ): 
                           $all_keys[] =  $item->keyword;
                        endforeach;
                    endif;
                    
                    if ( ezd_get_opt('is_exclude_not_found') == '1' ) {
                        $notFoundKeywords   = ezd_get_search_keywords();
                        $notFound_keys      = [];
                        if ( count( $notFoundKeywords ) > 0 ) :
                            foreach ( $notFoundKeywords as $notFoundKey ) : 
                            $notFound_keys[] =  $notFoundKey->keyword;
                            endforeach; 
                        endif;
                        $all_keys = array_diff($all_keys, $notFound_keys);
                    }

                    if ( count( $all_keys ) > 0 ) :
                        $i = 0;
                        foreach ( $all_keys as $key => $search_item ): 
                            $i++;
                            ?>
                            <li class="wow fadeInUp" data-wow-delay="0.2s">
                                <a href="#"> <?php echo esc_html( $search_item ); ?> </a>
                            </li>
                            <?php
                            if ( $i == ezd_get_opt('keywords_limit') ) {
                                break;
                            }
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