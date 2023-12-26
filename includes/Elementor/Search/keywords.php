<?php
if ( $settings['is_ezd_search_keywords'] == 'yes' ) : 
	?>
  <div class="header_search_keyword justify-content-<?php echo $settings['ezd_search_keywords_align'] ?>">
      <?php 
      if ( !empty($settings['ezd_search_keywords_label']) ) : 
          ?>
          <span class="header-search-form__keywords-label search_keyword_label">
              <?php echo $settings['ezd_search_keywords_label'] ?> </span>
          <?php
      endif;

      if ( !empty($settings['ezd_search_keywords_repeater']) || ezd_get_opt('is_dynamic_keywords') == '1' ) : 
        ?>
        <ul class="ezd-list-unstyled" id="ezd-search-keywords">
            <?php
            if ( !empty($settings['ezd_search_keywords_repeater']) ) :
              foreach ( $settings['ezd_search_keywords_repeater'] as $keyword ) :
                  ?>
                  <li class="wow fadeInUp" data-wow-delay="0.2s" data-keywords="<?php echo esc_html($keyword['title']); ?>">
                      <a class="has-bg" href="#"> <?php echo esc_html($keyword['title']); ?> </a>
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
                      <li class="wow fadeInUp" data-wow-delay="0.2s" data-keywords="<?php echo esc_html($keyword['title']); ?>">
                      <a class="has-bg" href="#"> <?php echo esc_html( $item->keyword ); ?> </a>
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