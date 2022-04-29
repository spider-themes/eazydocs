<?php
/**
 * Ajax Search Results
 */

$opt = get_option( 'eazydocs_settings' );
$is_keywords = $opt['is_keywords'] ?? '';
$keywords_label = $opt['keywords_label'] ?? '';
$keywords = $opt['keywords'] ?? '';
?>

<?php if ( $is_keywords == '1' ) : ?>
    <div class="ezd_search_keywords">
        <?php if ( !empty($keywords_label) ) :  ?>
            <span class="label">
                <?php echo esc_html($keywords_label) ?>
            </span>
        <?php endif; ?>
        <?php if ( !empty($keywords) ) : ?>
            <ul class="list-unstyled">
                <?php
                foreach ($keywords as $keyword) :
                    ?>
                    <li class="wow fadeInUp" data-wow-delay="0.2s">
                        <a href="#"> <?php echo esc_html($keyword['title']) ?> </a>
                    </li>
                    <?php
                endforeach;
                ?>
            </ul>
        <?php endif; ?>
    </div>
<?php endif;