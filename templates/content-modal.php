<?php
$name           = $email    = '';
if ( ! is_user_logged_in() ) {
	$email_from = ! empty ( $_POST['email'] ) ? sanitize_email( $_POST['email'] ) : '';
} else {
	$email_from = wp_get_current_user()->user_email;
	$name       = wp_get_current_user()->display_name;
}
$options        = get_option( 'eazydocs_settings' );
$title_text     = __( 'How can we help?', 'eazydocs' );
$title          = ! empty ( $options['feedback-form-title'] ) ? $options['feedback-form-title'] : $title_text;
$desc           = ! empty ( $options['feedback-form-desc'] ) ? $options['feedback-form-desc'] : '';
?>

<div class="ezd-modal" id="eazydocs_feedback" data-id="modal1">
    <div class="ezd-modal-overlay"></div>
    <div class="ezd-modal-dialog help_form" role="document">
        <div class="ezd-modal-content">
            <button type="button" class="close ezd-close">
                <i class="icon_close"></i>
            </button>
            <div class="shortcode_title">
                <h3 class="title mb-2"> <?php echo esc_html( $title ); ?> </h3>
                <?php echo wpautop( $desc ); ?>
            </div>
            <form method="post" id="edocs-contact-form" class="contact_form">
                <div class="ezd-grid ezd-grid-cols-12">
                    <div class="form-group ezd-md-col-6 ezd-grid-column-full form-name-field">
                        <input type="text" class="form-control" value="<?php echo esc_attr( $name ); ?>" name="name"
                            id="name" placeholder="<?php esc_attr_e( 'Name', 'eazydocs' ); ?>" required>
                    </div>
                    <div class="form-group ezd-md-col-6 ezd-grid-column-full form-email-field">
                        <input type="email" class="form-control" value="<?php echo esc_attr( $email_from ); ?>"
                               name="email" id="email" placeholder="<?php esc_attr_e( 'Email', 'eazydocs' ); ?>"
                            <?php disabled( is_user_logged_in() ); ?> required>
                    </div>
                    <div class="form-group ezd-grid-column-full">
                        <input type="text" class="form-control" name="subject" id="subject"
                            placeholder="<?php esc_attr_e( 'Subject', 'eazydocs' ); ?>" required>
                    </div>
                    <div class="form-group ezd-grid-column-full">
                        <textarea name="message" id="massage"
                            placeholder="<?php esc_attr_e( 'Message', 'eazydocs' ); ?>" required></textarea>
                    </div>
                    <div class="form-group ezd-grid-column-full submit-area">
                        <input type="hidden" name="doc_id" id="doc_id" value="<?php the_ID(); ?>">
                        <button type="submit" name="eazydoc_feedback_submit" class="btn action_btn">
                            <?php esc_html_e( 'Send', 'eazydocs' ); ?>
                        </button>
                    </div>
                    <div class="form-group ezd-grid-column-full">
                        <div class="eazydocs-form-result"></div>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>