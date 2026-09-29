<?php
/**
 * Cannot access directly.
 */
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

if ( $settings['is_ezd_search_keywords'] == 'yes' && !empty($settings['ezd_search_keywords_repeater']) ) :
	?>
    <div class="header_search_keyword justify-content-<?php echo esc_attr($settings['ezd_search_keywords_align']); ?>">
		<?php
		if ( ! empty( $settings['ezd_search_keywords_label'] )  && ezd_is_premium() ) : ?>
            <span class="header-search-form__keywords-label search_keyword_label">
				<?php echo esc_attr($settings['ezd_search_keywords_label']) ?> 
			</span>
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
                                <li class="wow fadeInUp" data-wow-delay="0.2s" data-keywords="<?php echo esc_attr($keyword['title']); ?>">
                                    <a class="has-bg" href="#"> <?php echo esc_html($keyword['title']); ?> </a>
                                </li>
							<?php
							endforeach;
						endif;
					else :
						// Cached, capped lookup (was an unbounded GROUP BY over every search ever logged).
						$all_keys = ezd_get_popular_search_keywords( $settings['keywords_limit'] ?? 6, 'yes' === ( $settings['is_exclude_not_found'] ?? '' ) );

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
