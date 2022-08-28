
<?php
wp_enqueue_style('font-awesome-5');
global $wp;
$redirect_to                = home_url( $wp->request );
$redirect_to			    = $_GET['after_login'] ?? '';
$add_doc                    = $_GET['after_login'] ?? '';
$private_doc                = $_GET['private_doc'] ?? '';

if( $add_doc ){
    $add_new_doc			= $_GET['after_login'] ?? '';
}else{
    $add_new_doc			= get_the_ID();
}
$add_new                    = $_GET['add_new_doc'] ?? '';
$ezd_login                  = array(
     'user_login'           => $_GET['ezd_username'] ?? '',
     'user_password'        => $_GET['ezd_password'] ?? '',
     'remember'             => true
);
$login_info                 = '';
if( isset( $_GET['ezd_login']) ){
    $login_info             = wp_signon( $ezd_login, false );
}
?>
<div class="ezd_doc_login_wrap">
    <div class="ezd_doc_login_form">
        <div class="ezd-login-head">
            <div class="ezd-login-head-icon">
                <i class="fa fa-info-circle"></i>
            </div>
            <?php echo esc_html($ezd_login_title); ?>
        </div>
        <div class="ezd-login-form-wrap"> 
            <?php echo wpautop($ezd_login_subtitle); ?>
            <form action="" method="GET">
                <input type="text" placeholder="<?php esc_attr_e('Username', 'eazydocs'); ?>" name="ezd_username" id="username">
                <input type="password" placeholder="<?php esc_attr_e('Password', 'eazydocs'); ?>" name="ezd_password" id="password">
                <input type="hidden" name="ezd_private_doc" value="<?php echo esc_attr($redirect_to); ?>">
                <input type="hidden" name="after_login" value="<?php echo esc_attr($add_new_doc); ?>">
                <input type="hidden" name="add_new_doc" value="<?php echo esc_attr($add_new); ?>">
                <input type="hidden" name="private_doc" value="<?php echo esc_attr($private_doc); ?>" id="">
                <input type="submit" name="ezd_login" value="<?php echo esc_attr($ezd_login_btn); ?>">
            </form>
            <a href="<?php echo esc_url( wp_lostpassword_url( get_permalink() ) ); ?>">
                <?php echo esc_html($ezd_login_forgot_btn); ?>
            </a>
            <?php 
            if ( is_wp_error( $login_info ) ) {
                echo "<div class='ezd-login-error'>" . $login_info->get_error_message() . "</div>";
            }
            ?>
        </div>
    </div> 
</div>