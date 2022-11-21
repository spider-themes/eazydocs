<?php
$optionReview = get_option('ezd_notify_review');
if ( time() >= (int)$optionReview && $optionReview !== '0' ) {
    add_action('admin_notices', 'ezd_notify_give_review');
}
add_action('wp_ajax_ezd_notify_save_review', 'ezd_notify_save_review');

/**
 ** Give Notice
 **/
function ezd_notify_give_review() {
    ?>
    <div class="notice notice-success is-dismissible" id="ezd_notify_review">
        <h3><?php _e('Give EazyDocs a review', 'eazydocs')?></h3>
        <p>
            <?php _e('Thank you for choosing EazyDocs. We hope you love it. Could you take a couple of seconds posting a nice review to share your happy experience?', 'eazydocs')?>
        </p>
        <p class="ezd_notify_review_subheading">
            <?php _e('We will be forever grateful. Thank you in advance.', 'eazydocs'); ?>
        </p>
        <p>
            <a href="javascript:;" data="rateNow" class="button button-primary" style="margin-right: 5px"><?php _e('Rate now', 'eazydocs')?></a>
            <a href="javascript:;" data="later" class="button" style="margin-right: 5px"><?php _e('Later', 'eazydocs')?></a>
            <a href="javascript:;" data="alreadyDid" class="button"><?php _e('Already did', 'eazydocs')?></a>
        </p>
    </div>
    <?php
}

/**
 ** Save Notice
 **/
function ezd_notify_save_review() {
    if ( isset( $_POST ) ) {
        $nonce = isset( $_POST['nonce'] ) ? sanitize_text_field( $_POST['nonce'] ) : null;
        $field = isset( $_POST['field'] ) ? sanitize_text_field( $_POST['field'] ) : null;

        if ( ! wp_verify_nonce( $nonce, 'eazydocs-admin-nonce' ) ) {
            wp_send_json_error( array( 'status' => 'Wrong nonce validate!' ) );
            exit();
        }

        if ( $field == 'later' ) {
            update_option('ezd_notify_review', time() + 3*60*60*24); //After 3 days show
        } else if ($field == 'alreadyDid') {
            update_option('ezd_notify_review', 0);
        }
        wp_send_json_success();
    }
    wp_send_json_error( array( 'message' => 'Update fail!' ) );
}