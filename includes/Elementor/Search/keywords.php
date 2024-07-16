<?php
if ( $settings['is_ezd_search_keywords'] == 'yes' && !empty($settings['ezd_search_keywords_repeater']) ) : 
    ?>
    <div class="header_search_keyword justify-content-<?php esc_attr($settings['ezd_search_keywords_align']); ?>">
        <?php 
        if ( !empty($settings['ezd_search_keywords_label']) ) : ?>
            <span class="header-search-form__keywords-label search_keyword_label">
                <?php echo esc_attr($settings['ezd_search_keywords_label']) ?> </span>
            <?php
        endif;
        
        if ( ezd_is_premium() ) : 
            if ( $settings['keywords_by'] == 'static' || $settings['keywords_by'] == 'dynamic' ) : 
                ?>
                <ul class="ezd-list-unstyled" id="ezd-search-keywords">
                <?php
                if ( $settings['keywords_by'] == 'static' ) :
                    if ( ! empty( $settings['ezd_search_keywords_repeater'] ) ) :
                        foreach ( $settings['ezd_search_keywords_repeater'] as $keyword ) :
                            ?>
                            <li class="wow fadeInUp" data-wow-delay="0.2s" data-keywords="<?php echo esc_html($keyword['title']); ?>">
                                <a class="has-bg" href="#"> <?php echo esc_html($keyword['title']); ?> </a>
                            </li>
                            <?php 
                        endforeach;
                    endif;
                else :
                    global $wpdb;

                    // Attempt to get cached results
                    $cache_key = 'eazydocs_search_keyword';
                    $search_keyword = wp_cache_get( $cache_key, 'eazydocs' );

                    if ( false === $search_keyword ) {
                        // Cache miss, perform the query
                        // Suppress direct query warning since we need to perform a custom SQL query
                        // @codingStandardsIgnoreLine WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching
                        $search_keyword = $wpdb->get_results( "SELECT keyword, COUNT(*) AS count FROM {$wpdb->prefix}eazydocs_search_keyword GROUP BY keyword ORDER BY count DESC" );
                        
                        // Store the results in cache for 12 hours
                        wp_cache_set( $cache_key, $search_keyword, 'eazydocs', 12 * HOUR_IN_SECONDS );
                    }

                    $all_keys = [];
                    if ( count( $search_keyword ) > 0 ) :
                        foreach ( $search_keyword as $item ) :
                            $all_keys[] =  $item->keyword;
                        endforeach;
                    endif;
                    
                    if ( $settings['is_exclude_not_found'] == 'yes' ) {
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
                            <li class="wow fadeInUp" data-wow-delay="0.2s" data-keywords="<?php echo esc_attr( $search_item ); ?>">
                                <a class="has-bg" href="#"> <?php echo esc_html( $search_item ); ?> </a>
                            </li>
                            <?php
                            if ( $i == $settings['keywords_limit'] ) {
                                break;
                            }
                        endforeach;
                    endif;

                endif;
                ?>
                </ul>
                <?php 
            endif; 
        endif;  
        ?>
    </div>
    <?php
endif;