<?php
global $post;
$options             = get_option( 'eazydocs_settings' );
$email_feedback      = $options['message-feedback'] ?? '1';
$helpful_feedback    = $options['helpful_feedback'] ?? '1';
$right_alignment     = $email_feedback == '1' ? 'right' : 'left';
$feedback_count      = $options['feedback_count'] ?? '1';
$positive            = (int) get_post_meta( $post->ID, 'positive', true );
$negative            = (int) get_post_meta( $post->ID, 'negative', true );
$positive_title      = $positive ? sprintf( _n( '%d person found this useful', '%d persons found this useful', $positive, 'eazydocs' ), number_format_i18n( $positive ) ) : esc_html__( 'No votes yet', 'eazydocs' );
$negative_title      = $negative ? sprintf( _n( '%d person found this not useful', '%d persons found this not useful', $negative, 'eazydocs' ), number_format_i18n( $negative ) ) : esc_html__( 'No votes yet', 'eazydocs' );
$still_stuck         = ! empty ( $options['still-stuck'] ) ? $options['still-stuck'] : __( 'Still stuck?', 'eazydocs' );;
$tags                = get_the_terms( get_the_ID(), 'doc_tag' );
$link_text           = ! empty ( $options['feedback-link-text'] ) ? $options['feedback-link-text'] : __( 'How can we help?', 'eazydocs' );
$doc_feedback_label  = ! empty ( $options['feedback-label'] ) ? $options['feedback-label'] : __( 'Was this page helpful?', 'eazydocs' );
$enable_next_prev    = ! empty ( $options['enable-next-prev-links'] ) ?? '';
?>
<div class="doc-btm">
    <?php
	$has_next_prev = '';
	if ( $tags ) {
		echo '<ul class="nav card_tagged">';
		echo '<li>' . esc_html__( 'Tagged:', 'eazydocs' ) . '</li>';
		foreach ( $tags as $tag ) {
			echo "<li><a href='" . get_term_link( $tag->term_id, 'doc_tag' ) . "'>{$tag->name}</a></li>";
		}
		echo '</ul>';
		$has_next_prev = 'has_tags_next_prev';
	}
	// Next & Previous Link
    if( $enable_next_prev == '1' ) {
	    do_action( 'eazydocs_prev_next_docs', get_the_ID() );
    }
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
                    <a href="#" data-bs-toggle="modal" data-bs-target="#eazydocs_feedback">
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
            <div class="ezd-lg-col-7 ezd-grid-column-full eazydocs-feedback-wrap eazydocs-hide-print">
                <p class="<?php echo esc_attr( $right_alignment ) ?>">
                    <?php echo esc_html( $doc_feedback_label ) ?>
                    <span class="vote-link-wrap">
                        <a href="#" class="h_btn positive" data-id="<?php the_ID(); ?>" data-type="positive"
                            title="<?php echo esc_attr( $positive_title ); ?>">
                            <?php esc_html_e( 'Yes', 'eazydocs' ); ?>
                            <?php if ( $positive && $feedback_count ) { ?>
                            <span class="count"> <?php echo number_format_i18n( $positive ); ?> </span>
                            <?php } ?>
                        </a>
                        <a href="#" class="h_btn negative red" data-id="<?php the_ID(); ?>" data-type="negative"
                            title="<?php echo esc_attr( $negative_title ); ?>">
                            <?php esc_html_e( 'No', 'eazydocs' ); ?>
                            <?php if ( $negative && $feedback_count ) { ?>
                            <span class="count"><?php echo number_format_i18n( $negative ); ?></span>
                            <?php } ?>
                        </a>
                    </span>
                </p>
            </div>
            <?php
            endif;
            ?>
        </div>
    </footer>
</div>