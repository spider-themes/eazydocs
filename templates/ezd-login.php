
<?php
wp_enqueue_style('font-awesome-5');
$args = array(
    'post_type'     => 'docs',
    'post_parent'   => get_the_ID(),
    'posts_per_page' => -1,
    'post_status'   => 'publish',
);
$child_posts        = get_children( $args );
$order              = count($child_posts) + 1;

if ( isset( $_GET['add_new_doc'] ) && $_GET['add_new_doc'] == 'yes' ) {
    $redirect_to = admin_url('/post-new.php?post_type=docs&add_new_doc=yes').'&ezd_doc_parent='.$_GET['post_id'].'&ezd_doc_order='. $order;
} elseif ( isset( $_GET['ezd_edit_doc'] ) && $_GET['ezd_edit_doc'] == 'yes' ) {
    $redirect_to = admin_url('/post.php?post='.$_GET['post_id'].'&action=edit');
} else {
    $redirect_to = get_permalink($_GET['post_id'] ?? '');
}
?>
<div class="ezd_doc_login_wrap">
    <div class="ezd_doc_login_form">
        <div class="ezd-login-head">
            <div class="ezd-login-head-icon">
                <i class="fa fa-info-circle"></i>
            </div>
            <?php echo esc_html($login_title); ?>
        </div>
        <div class="ezd-login-form-wrap"> 
            <?php echo wpautop($login_subtitle); ?>
 
            <form action="<?php echo esc_url(site_url('wp-login.php', 'login_post')); ?>" method="POST" class="ezd-form-wrap">
                <input type="text" placeholder="<?php esc_attr_e('Username', 'eazydocs'); ?>" name="log" id="user_login">
                <input type="password" placeholder="<?php esc_attr_e('Password', 'eazydocs'); ?>" name="pwd" id="user_pass">
                <input type="hidden" name="redirect_to" value="<?php echo esc_attr($redirect_to); ?>">
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