<?php
if ( $settings['is_ezd_search_keywords'] == 'yes' && !empty($settings['ezd_search_keywords_repeater']) ) : 
	?>
<div class="header_search_keyword justify-content-<?php echo $settings['ezd_search_keywords_align'] ?>">
    <?php 
        if ( !empty($settings['ezd_search_keywords_label']) ) : ?>
    <span class="header-search-form__keywords-label search_keyword_label">
        <?php echo $settings['ezd_search_keywords_label'] ?> </span>
    <?php
		endif;
		if ( !empty($settings['ezd_search_keywords_repeater']) && ezd_is_premium() ) : ?>
    <ul class="ezd-list-unstyled" id="ezd-search-keywords">
        <?php
				foreach ( $settings['ezd_search_keywords_repeater'] as $keyword ) :
					?>
        <li class="wow fadeInUp" data-wow-delay="0.2s" data-keywords="<?php echo esc_html($keyword['title']); ?>">
            <a class="has-bg" href="#"> <?php echo esc_html($keyword['title']); ?> </a>
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
endif;