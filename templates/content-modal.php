<?php
$name = $email = '';
if ( ! is_user_logged_in() ) {
	$email_from = ! empty ( $_POST['email'] ) ? sanitize_email( $_POST['email'] ) : '';
} else {
	$email_from = wp_get_current_user()->user_email;
	$name       = wp_get_current_user()->display_name;
}
$options = get_option( 'eazydocs_settings' );
$title   = __( 'How can we help?', 'eazydocs' );
$desc    = __( 'A premium WordPress theme with integrated Knowledge Base, providing 24/7 community based support.', 'eazydocs' );
if ( class_exists( 'EazyDocsPro' ) ) {
	$title = ! empty ( $options['feedback-form-title'] ) ? $options['feedback-form-title'] : $title;
	$desc        = ! empty ( $options['feedback-form-desc'] ) ? $options['feedback-form-desc'] : $desc;
}
?>

<div class="modal fade img_modal" id="eazydocs_feedback" tabindex="-3" role="dialog" aria-hidden="false">
    <div class="modal-dialog help_form" role="document">
        <div class="modal-content">
            <button type="button" class="close" data-bs-dismiss="modal" aria-label="Close">
                <i class=" icon_close"></i>
            </button>
            <div class="shortcode_title">
                <h3 class="mb-2"> <?php echo esc_html( $title ); ?> </h3>
	            <?php echo wpautop( $desc ); ?>
            </div>
            <form method="post" id="edocs-contact-form" class="contact_form">
                <div class="row">
                    <div class="form-group col-md-6">
                        <input type="text" class="form-control" value="<?php echo esc_attr( $name ); ?>" name="name" id="name" placeholder="<?php esc_attr_e( 'Name', 'eazydocs' ); ?>" required>
                    </div>
                    <div class="form-group col-md-6">
                        <input type="email" class="form-control" value="<?php echo esc_attr( $email_from ); ?>" name="email" id="email"
                               placeholder="<?php esc_attr_e( 'Email', 'eazydocs' ); ?>" <?php disabled( is_user_logged_in() ); ?> required>
                    </div>
                    <div class="form-group col-md-12">
                        <input type="text" class="form-control" name="subject" id="subject" placeholder="<?php esc_attr_e( 'Subject', 'eazydocs' ); ?>" required>
                    </div>
                    <div class="form-group col-md-12">
                        <textarea name="message" id="massage" placeholder="<?php esc_attr_e( 'Massage', 'eazydocs' ); ?>" required></textarea>
                    </div>
                    <div class="form-group col-md-12">
                        <input type="hidden" name="doc_id" value="<?php the_ID(); ?>">
                        <button type="submit" name="eazydoc_feedback_submit" class="btn action_btn">
							<?php esc_html_e( 'Send', 'eazydocs' ); ?>
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>