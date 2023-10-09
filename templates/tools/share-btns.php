<?php
$is_social_links        = ezd_get_opt( 'is_social_links' ) ?? '';
$is_copy_link           = ezd_get_opt( 'is_copy_link' ) ?? '';
$copy_link_text         = ezd_get_opt( 'copy_link_text' ) ?? 'Copy Link';
$copy_link_text_success = ezd_get_opt( 'copy_link_text_success' ) ?? 'Copied!';
$is_post_share_title    = ezd_get_opt( 'is_post_share_title' ) ?? '';
$is_social_btns         = ezd_get_opt( 'is_social_btns' ) ?? '';
$copy_link_label        = ezd_get_opt( 'copy_link_label' ) ?? __( 'Or copy link', 'eazydocs' );

if ( $is_social_links ) :
	if ( $is_copy_link || $is_social_btns ) :
		?>
<a href="#" class="ezd-share-btn modal-toggle" data-id="2">
    <i class="social_share_square"></i>
    <?php echo ezd_get_opt( 'share_btn_label', __( 'Share', 'eazydocs' ) ) ?>
</a>

<div class="ezd-modal" id='eazydocs_share' data-id="modal2">
    <div class="ezd-modal-overlay modal-toggle"></div>
    <div class="ezd-modal-dialog" role="document">
        <div class="ezd-modal-content">
            <a class="close ezd-close">
                <i class=" icon_close"></i>
            </a>
            <div class="eazydocs-share-wrap">
                <h2> <?php the_title(); ?> </h2>
                <div class="social-links">
                    <a href="mailto:?subject=<?php the_title(); ?>&amp;body= <?php esc_html_e( 'Check out this doc', 'eazydocs' );
							the_permalink(); ?>" target="_blank">
                        <i class="icon_mail"></i>
                    </a>
                    <a href="https://www.facebook.com/share.php?u=<?php the_permalink(); ?>">
                        <i class="social_facebook_circle"></i>
                    </a>
                    <a href="https://www.linkedin.com/shareArticle?mini=true&url=<?php the_permalink(); ?>">
                        <i class="social_linkedin_square"></i>
                    </a>
                    <a
                        href="https://twitter.com/share?url=<?php the_permalink(); ?>&amp;text=<?php the_title(); ?> &amp;hashtags=<?php echo site_url(); ?>">
                        <i class="social_twitter"></i>
                    </a>
                </div>
                <?php

						if ( $is_copy_link ) :
							?>
                <p> <?php echo esc_html( $copy_link_label ); ?> </p>
                <div class="copy-url-wrap">
                    <input readonly type="text" value="<?php the_permalink(); ?>" class="word-wrap">
                    <div class="share-this-doc"
                        data-success-message="<?php echo esc_attr( $copy_link_text_success ) ?>">
                        <img src="<?php echo EAZYDOCS_IMG . '/clone.svg'; ?>"
                            alt="<?php esc_attr_e( 'Clipboard Icon', 'eazydocs' ); ?>">
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