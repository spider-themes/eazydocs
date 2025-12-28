<?php
global $post;
$docs_feedback    = ezd_get_opt( 'docs-feedback', '1' );
$email_feedback   = ezd_get_opt( 'message-feedback', '1' );
$helpful_feedback = ezd_get_opt( 'helpful_feedback', '1' );
$right_alignment  = $email_feedback == '1' ? 'right' : 'left';
$feedback_count   = ezd_get_opt( 'feedback_count', '1' );
$positive         = (int) get_post_meta( $post->ID, 'positive', true );
$negative         = (int) get_post_meta( $post->ID, 'negative', true );

// translators: %d is the number of persons who found this useful
$positive_title = $positive ? sprintf( _n( '%d person found this useful', '%d persons found this useful', $positive, 'eazydocs' ),
	number_format_i18n( $positive ) ) : esc_html__( 'No votes yet', 'eazydocs' );

// translators: %d is the number of persons who found this not useful
$negative_title = $negative ? sprintf( _n( '%d person found this not useful', '%d persons found this not useful', $negative, 'eazydocs' ),
	number_format_i18n( $negative ) ) : esc_html__( 'No votes yet', 'eazydocs' );

$still_stuck        = ezd_get_opt( 'still-stuck', esc_html__( 'Still stuck?', 'eazydocs' ) );
$tags               = get_the_terms( get_the_ID(), 'doc_tag' );
$link_text          = ezd_get_opt( 'feedback-link-text', esc_html__( 'How can we help?', 'eazydocs' ) );
$doc_feedback_label = ezd_get_opt( 'feedback-label', esc_html__( 'Was this page helpful?', 'eazydocs' ) );
$enable_next_prev   = ezd_is_premium() ? ezd_get_opt( 'enable-next-prev-links' ) : false;
$is_doc_tag         = ezd_get_opt( 'is_doc_tag', true );

if ( $tags || $docs_feedback == '1' || $enable_next_prev == '1' ) :
	?>
    <div class="doc-btm">
		<?php
		$has_next_prev = '';
		if ( $tags && $is_doc_tag ) {
			echo '<ul class="nav card_tagged">';
			echo '<li>' . esc_html__( 'Tagged:', 'eazydocs' ) . '</li>';
			foreach ( $tags as $tag ) {

				echo '<li><a href="' . esc_url( get_term_link( $tag->term_id, 'doc_tag' ) ) . '">' . esc_html( $tag->name ) . '</a></li>';

			}
			echo '</ul>';
			$has_next_prev = 'has_tags_next_prev';
		}
		// Next & Previous Link
		if ( $enable_next_prev == '1' && ezd_is_premium() ) {
			do_action( 'eazydocs_prev_next_docs', get_the_ID() );
		}

		if ( $docs_feedback == '1' ) :
			?>
            <footer class="help_text" id="help">
                <div class="border_bottom"></div>
                <div class="ezd-grid ezd-grid-cols-12 feedback_link">
					<?php
					if ( $email_feedback == '1' ) :
						?>
                        <div class="ezd-lg-col-5 ezd-grid-column-full">
                            <p class="left">
                                <i class="icon_mail_alt"></i>
								<?php echo esc_html( $still_stuck ); ?>
                                <a href="#" class="modal-toggle" data-id="1">
									<?php echo esc_html( $link_text ); ?>
                                </a>
                            </p>
                        </div>
						<?php
						// Initialize the modal template
						eazydocs_get_template_part( 'content-modal' );
					endif;

					if ( $helpful_feedback == '1' ) :
						?>
                        <div class="ezd-lg-col-7 ezd-grid-column-full eazydocs-feedback-wrap eazydocs-hide-print <?php if ( $email_feedback != '1' ) {
							echo esc_attr( 'justify-content-start' );
						} ?>">
                            <p class="<?php echo esc_attr( $right_alignment ) ?>">
								<?php echo esc_html( $doc_feedback_label ) ?>
                                <span class="vote-link-wrap">
                                    <a href="#" class="h_btn positive" data-id="<?php the_ID(); ?>" data-type="positive"
                                       title="<?php echo esc_attr( $positive_title ); ?>">
                                        <?php
                                        esc_html_e( 'Yes', 'eazydocs' );

                                        if ( $positive && $feedback_count ) :
	                                        ?>
                                            <span class="count"> <?php echo esc_html( number_format_i18n( $positive ) ); ?> </span>
                                        <?php
                                        endif;
                                        ?>
                                    </a>
                                    <a href="#" class="h_btn negative red" data-id="<?php the_ID(); ?>" data-type="negative"
                                       title="<?php echo esc_attr( $negative_title ); ?>">
                                        <?php
                                        esc_html_e( 'No', 'eazydocs' );

                                        if ( $negative && $feedback_count ) :
	                                        ?>
                                            <span class="count"><?php echo esc_html( number_format_i18n( $negative ) ); ?></span>
                                        <?php
                                        endif;
                                        ?>
                                    </a>
                                </span>
                            </p>
                        </div>
					<?php
					endif;
					?>
                </div>
            </footer>
		<?php
		endif;
		?>
    </div>
<?php
endif;