
<?php
global $wp;
$redirect_to            = home_url( $wp->request );
$ezd_login              = array(
     'user_login'       => $_GET['ezd_username'] ?? '',
     'user_password'    => $_GET['ezd_password'] ?? '',
     'remember'         => true
 );
 $login_info            = '';
if( isset( $_GET['ezd_login']) ){
$login_info            = wp_signon( $ezd_login, false );
}
?>
<div class="ezd_doc_login_wrap">
    <div class="ezd_doc_login_form">
        <div class="ezd-login-head">
            <div class="ezd-login-head-icon">
                <i class="icon_info_alt"></i>
            </div>
            <?php esc_html_e( 'You must log in to continue.', 'eazydocs' ); ?>
        </div>
        <div class="ezd-login-form-wrap"> 
            <p>
            <?php
                echo esc_html( 'Login to', 'eazydocs' ).' '; 
                echo bloginfo('name');
            ?>
            </p>
            <form action="" method="GET">
                <input type="text" placeholder="<?php esc_attr_e('Username', 'eazydocs'); ?>" name="ezd_username" id="username">
                <input type="password" placeholder="<?php esc_attr_e('Password', 'eazydocs'); ?>" name="ezd_password" id="password">
                <input type="hidden" name="ezd_private_doc" value="<?php echo esc_html($redirect_to); ?>">
                <input type="submit" name="ezd_login" value="<?php esc_attr_e('Log In', 'eazydocs'); ?>">
            </form>
            <a href="<?php echo esc_url( wp_lostpassword_url( get_permalink() ) ); ?>">
                <?php esc_attr_e('Forgotten account?', 'eazydocs'); ?>
            </a>
            <?php 
            if ( is_wp_error( $login_info ) ) {
                echo "<div class='ezd-login-error'>" . $login_info->get_error_message() . "</div>";
            }
            ?>
        </div>
    </div> 
</div>