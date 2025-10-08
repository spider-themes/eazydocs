<?php
wp_enqueue_style( 'elegant-icon' );
$args = array(
    'post_type'     => 'docs',
    'post_parent'   => get_the_ID(),
    'posts_per_page' => -1,
    'post_status'   => 'publish',
);
$child_posts        = get_children( $args );
$order              = count($child_posts) + 1;

// Sanitize and validate $_GET parameters
$add_new_doc = isset( $_GET['add_new_doc'] ) ? sanitize_text_field( $_GET['add_new_doc'] ) : '';
$ezd_edit_doc = isset( $_GET['ezd_edit_doc'] ) ? sanitize_text_field( $_GET['ezd_edit_doc'] ) : '';
$post_id = isset( $_GET['post_id'] ) ? absint( $_GET['post_id'] ) : 0;

if ( $add_new_doc === 'yes' && $post_id > 0 ) {
    $redirect_to    = admin_url('/post-new.php?post_type=docs&add_new_doc=yes').'&ezd_doc_parent='. $post_id .'&ezd_doc_order='. $order;
    $login_type     = 'add';
} elseif ( $ezd_edit_doc === 'yes' && $post_id > 0 ) {
    $redirect_to    = admin_url('/post.php?post='. $post_id .'&action=edit');
    $login_type     = 'edit';
} else {
    $redirect_to    = $post_id > 0 ? get_permalink( $post_id ) : home_url();
    $login_type     = 'view';
}
?>
<div class="ezd_doc_login_wrap">
    <div class="ezd_doc_login_form">
        <div class="ezd-login-head">
            <div class="ezd-login-head-icon">
                <i class="icon_info_alt"></i>
            </div>
            <?php echo esc_html($login_title); ?>
        </div>
        <div class="ezd-login-form-wrap"> 
            <?php echo wp_kses_post(wpautop($login_subtitle)); ?>
 
            <form action="<?php echo esc_url(site_url('wp-login.php', 'login_post')); ?>" method="POST" class="ezd-form-wrap">
                <input type="text" placeholder="<?php esc_attr_e('Username', 'eazydocs'); ?>" name="log" id="user_login">
                <input type="password" placeholder="<?php esc_attr_e('Password', 'eazydocs'); ?>" name="pwd" id="user_pass">
                <input type="hidden" name="redirect_to" value="<?php echo esc_attr($redirect_to); ?>">
                <input type="hidden" name="login_type" value="<?php echo esc_attr($login_type); ?>">
                <input type="submit" name="ezd_login" value="<?php echo esc_attr($login_btn); ?>">
                <input type="hidden" name="testcookie" value="1" />
            </form>

            <a href="<?php echo esc_url( wp_lostpassword_url( get_permalink() ) ); ?>">
                <?php echo esc_html($login_forgot_btn); ?>
            </a>
            <div class='ezd-login-error'></div>
        </div>
    </div> 
</div>