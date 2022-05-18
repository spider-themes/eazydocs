<?php
global $post;
$options             = get_option( 'eazydocs_settings' );
$email_feedback      = $options['message-feedback'] ?? '1';
$helpful_feedback    = $options['helpful_feedback'] ?? '1';
$right_alignment     = $email_feedback == '1' ? 'right' : 'left';
$positive            = (int) get_post_meta( $post->ID, 'positive', true );
$negative            = (int) get_post_meta( $post->ID, 'negative', true );
$positive_title      = $positive ? sprintf( _n( '%d person found this useful', '%d persons found this useful', $positive, 'eazydocs' ), number_format_i18n( $positive ) ) : esc_html__( 'No votes yet', 'eazydocs' );
$negative_title      = $negative ? sprintf( _n( '%d person found this not useful', '%d persons found this not useful', $negative, 'eazydocs' ), number_format_i18n( $negative ) ) : esc_html__( 'No votes yet', 'eazydocs' );
$still_stuck         = esc_html__( 'Still stuck?', 'eazydocs' );
$link_text           = esc_html__( 'How can we help?', 'eazydocs' );
$doc_feedback_label  = esc_html__( 'Was this page helpful?', 'eazydocs' );
$tags                = get_the_terms( get_the_ID(), 'doc_tag' );

if( class_exists('EazyDocsPro')){
	$still_stuck            = ! empty ( $options['still-stuck'] ) ? $options['still-stuck'] : $still_stuck;
	$link_text              = ! empty ( $options['feedback-link-text'] ) ? $options['feedback-link-text'] : $link_text;
	$doc_feedback_label     = ! empty ( $options['feedback-label'] ) ? $options['feedback-label'] : $doc_feedback_label;
}
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
	do_action('eazydocs_prev_next_docs', $has_next_prev);
	?>
    <footer class="help_text" id="help">
        <div class="border_bottom"></div>
        <div class="row feedback_link">
            <?php
            if ( $email_feedback == '1' ) :
                ?>
                <div class="col-lg-5">
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
                <div class="col-lg-7 eazydocs-feedback-wrap eazydocs-hide-print">
                    <p class="<?php echo esc_attr( $right_alignment ) ?>">
                        <?php echo esc_html( $doc_feedback_label ) ?>
                        <span class="vote-link-wrap">
                            <a href="#" class="h_btn positive" data-id="<?php the_ID(); ?>" data-type="positive" title="<?php echo esc_attr( $positive_title ); ?>">
                                <?php esc_html_e( 'Yes', 'eazydocs' ); ?>
                                <?php if ( $positive ) { ?>
                                    <span class="count"> <?php echo number_format_i18n( $positive ); ?> </span>
                                <?php } ?>
                            </a>
                            <a href="#" class="h_btn negative red" data-id="<?php the_ID(); ?>" data-type="negative" title="<?php echo esc_attr( $negative_title ); ?>">
                                <?php esc_html_e( 'No', 'eazydocs' ); ?>
                                <?php if ( $negative ) { ?>
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