<?php
$keywords_label = ezd_get_opt( 'keywords_label' );
$keywords       = ezd_get_opt( 'keywords' );

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
                    // Cached, capped lookup (was an uncached GROUP BY over every search ever logged).
                    $all_keys = ezd_get_popular_search_keywords( ezd_get_opt( 'keywords_limit', 6 ), '1' == ezd_get_opt( 'is_exclude_not_found' ) );

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