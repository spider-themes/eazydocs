<?php
$is_social_links        = ezd_get_opt( 'is_social_links' );
$is_copy_link           = ezd_get_opt( 'is_copy_link' );
$copy_link_text         = ezd_get_opt( 'copy_link_text', esc_html__( 'Copy Link', 'eazydocs' ) );
$copy_link_text_success = ezd_get_opt( 'copy_link_text_success', esc_html__( 'Copied!', 'eazydocs' ) );
$is_post_share_title    = ezd_get_opt( 'is_post_share_title' );
$is_social_btns         = ezd_get_opt( 'is_social_btns' );
$copy_link_label        = ezd_get_opt( 'copy_link_label', esc_html__( 'Or copy link', 'eazydocs' ) );

if ( $is_social_links ) :
	if ( $is_copy_link || $is_social_btns ) :
		?>
        <a href="#" class="ezd-share-btn modal-toggle" data-id="2">
            <i class="social_share_square"></i>
			<?php echo esc_html( ezd_get_opt( 'share_btn_label', esc_html__( 'Share this Doc', 'eazydocs' ) ) ) ?>
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
                            <a href="https://twitter.com/share?url=<?php the_permalink(); ?>&amp;text=<?php the_title(); ?> &amp;hashtags=<?php echo esc_url( site_url() ); ?>">
                                <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" fill="currentColor" viewBox="0 0 16 16"><path d="M12.6.75h2.454l-5.36 6.142L16 15.25h-4.937l-3.867-5.07-4.425 5.07H.316l5.733-6.57L0 .75h5.063l3.495 4.633L12.601.75Zm-.86 13.028h1.36L4.323 2.145H2.865z"/></svg>
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
                                    <img src="<?php echo esc_url( EAZYDOCS_IMG . '/clone.svg' ); ?>"
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