<?php
$is_social_links        = eazydocs_get_option('is_social_links', 'eazydocs_settings') ?? '';
$is_copy_link           = eazydocs_get_option('is_copy_link', 'eazydocs_settings') ?? '';
$copy_link_text         = eazydocs_get_option('copy_link_text', 'eazydocs_settings') ?? 'Copy Link';
$copy_link_text_success = eazydocs_get_option('copy_link_text_success', 'eazydocs_settings') ?? 'Copied!';
$is_post_share_title    = eazydocs_get_option('is_post_share_title', 'eazydocs_settings') ?? '';
$is_social_btns         = eazydocs_get_option('is_social_btns', 'eazydocs_settings') ?? '';
$copy_link_label        = eazydocs_get_option('copy_link_label', 'eazydocs_settings') ?? __( 'Or copy link', 'eazydocs' );
$share_btn_label        = eazydocs_get_option('share_btn_label', 'eazydocs_settings') ?? __( 'Share', 'eazydocs' );

if ( $is_social_links ) :
    if ( $is_copy_link || $is_social_btns ) :
    ?>
    <a href="#" class="ezd-share-btn" data-bs-toggle="modal" data-bs-target="#eazydocs_share">
        <i class="social_share_square"></i>
        <?php echo esc_html($share_btn_label) ?>
    </a>

    <div class="modal fade" id="eazydocs_share" tabindex="-3" role="dialog" aria-hidden="false">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <a class="close" data-bs-dismiss="modal">
                    <i class=" icon_close"></i>
                </a>
                <div class="eazydocs-share-wrap">
                    <h2> <?php the_title(); ?> </h2>
                    <div class="social-links">
                        <a href="mailto:?subject=<?php the_title(); ?>&amp;body= <?php esc_html_e('Check out this doc', 'eazydocs'); the_permalink(); ?>">
                            <i class="icon_mail"></i>
                        </a>
                        <a href="https://www.facebook.com/share.php?u=<?php the_permalink(); ?>">
                            <i class="social_facebook_circle"></i>
                        </a>
                        <a href="https://www.linkedin.com/shareArticle?mini=true&url=<?php the_permalink(); ?>">
                            <i class="social_linkedin_square"></i>
                        </a>
                        <a href="https://twitter.com/share?url=<?php the_permalink(); ?>&amp;text=<?php the_title(); ?> &amp;hashtags=<?php echo site_url(); ?>">
                            <i class="social_twitter"></i>
                        </a>
                    </div>
                    <?php

                    if ( $is_copy_link ) : 
                        ?>
                        <p> <?php echo esc_html($copy_link_label); ?> </p>
                        <div class="copy-url-wrap"> 
                            <input readonly type="text" value="<?php the_permalink(); ?>" class="word-wrap">
                            <div class="share-this-doc" data-success-message="<?php echo esc_attr($copy_link_text_success) ?>">
                            <img src="<?php echo EAZYDOCS_IMG . '/clone.svg'; ?>" alt="<?php esc_attr_e( 'Clipboard Icon', 'eazydocs' ); ?>">
                            </div>
                        </div>
                        <?php 
                    endif;
                    ?>
                </div>
                    
            </div>
        </div>
    </div>    
    <?php 
endif;
endif; 