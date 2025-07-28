<?php
namespace eazyDocs;

/**
 * Class Google_Login
 *
 * Handles Google Login functionality for EazyDocs.
 */
class Google_Login {
    
    private $client_id;
    private $client_secret;
    private $redirect_uri;
    
    public function __construct() {
        add_action( 'init', array( $this, 'init' ) );
        
        // Native WP login/register forms
        add_action( 'login_form', array( $this, 'add_google_login_button' ) );
        add_action( 'register_form', array( $this, 'add_google_login_button' ) );
        
        add_action( 'template_redirect', array( $this, 'handle_google_callback' ) );
        add_action( 'login_message', array( $this, 'show_login_messages' ) );
        add_shortcode( 'ezd_google_login', array( $this, 'google_login_shortcode' ) );

        // login page 
        add_action( 'login_enqueue_scripts', function(){
            wp_enqueue_style( 'eazydocs-frontend', EAZYDOCS_ASSETS . '/css/frontend.css', EAZYDOCS_VERSION );
			wp_enqueue_script( 'eazydocs-single', EAZYDOCS_ASSETS . '/js/frontend/docs-single.js', array( 'jquery' ), EAZYDOCS_VERSION );
        });
        
        // Get plugin settings
        $this->client_id     = ezd_get_opt( 'google_client_id', '' );
        $this->client_secret = ezd_get_opt( 'google_client_secret', '' );
        $this->redirect_uri  = home_url( '/google-auth-callback/' );
    }
    
    /**
     * Initialize Google Login functionality
     */
    public function init() {
        // Add rewrite rule for callback
        add_rewrite_rule( '^google-auth-callback/?$', 'index.php?google_auth_callback=1', 'top' );
        add_filter( 'query_vars', array( $this, 'add_query_vars' ) );
    }
    
    /**
     * Add custom query vars
     *
     * @param array $vars
     * @return array
     */
    public function add_query_vars( $vars ) {
        $vars[] = 'google_auth_callback';
        return $vars;
    }     
    
    /**
     * Show login messages
     *
     * @param string $message
     * @return string
     */
    public function show_login_messages( $message ) {
        if ( isset( $_GET[ 'google_error' ] ) ) {
            $error_msg = '<div id="login_error">Google login failed. Please try again or contact support.</div>';
            return $error_msg . $message;
        }
        return $message;
    }
    /**
     * Get current page ID
     *
     * @return int|string
     */    
    private function get_current_page_id() {
        global $post;
        
        if ( is_home() || is_front_page() ) {
            return get_option( 'page_on_front', 0 );
        }
        
        if ( is_page() || is_single() ) {
            return get_the_ID();
        }
        
        if ( is_category() ) {
            return 'category_' . get_queried_object_id();
        }
        
        if ( is_tag() ) {
            return 'tag_' . get_queried_object_id();
        }
        
        if ( is_author() ) {
            return 'author_' . get_queried_object_id();
        }
        
        if ( is_archive() ) {
            return 'archive_' . get_queried_object_id();
        }
        
        // Fallback to post ID if available
        if ( isset( $post->ID ) ) {
            return $post->ID;
        }
        
        return 0;
    }
    
    /**
     * Get site URL
     *
     * @return string
     */
    private function get_site_url() {
        return site_url();
    }
    
    /**
     * Add Google Login button to login and register forms
     */
    public function add_google_login_button() {
        if ( empty( $this->client_id ) || empty( $this->client_secret ) ) {
            return;
        }
        echo $this->get_google_login_html( 'Sign in with Google', 'ezd-google-login-btn' );
    }
    
    /**
     * Shortcode for Google Login button
     *
     * @param array $atts
     * @return string
     */
    public function google_login_shortcode( $atts ) {
        if ( empty( $this->client_id ) || empty( $this->client_secret ) ) {
            return '<p>Google Login not configured. Please check settings.</p>';
        }

        $atts = shortcode_atts( array(
            'text'       => 'Sign in with Google',
            'class'      => 'ezd-google-login-btn',
            'redirect'   => '',
            'product_id' => '',
            'docs_id'    => ''
        ), $atts );

        return $this->get_google_login_html( $atts[ 'text' ], $atts[ 'class' ], $atts[ 'redirect' ], $atts[ 'product_id' ], $atts[ 'docs_id' ] );
    }
    
    /**
     * Generate Google Login HTML
     *
     * @param string $text
     * @param string $class
     * @param string $redirect
     * @param string $product_id
     * @param string $docs_id
     * @return string
     */
    private function get_google_login_html( $text = 'Sign in with Google', $class = 'ezd-google-login-btn', $redirect = '', $product_id = '', $docs_id = '' ) {
        if ( is_user_logged_in() ) {
            return '';
        }

        if ( ! session_id() ) {
            session_start();
        }
        
        // Save values in session
        $_SESSION[ 'gcl_redirect_url' ] = ! empty( $redirect ) ? $redirect : ( function_exists( 'wc_get_checkout_url' ) ? wc_get_checkout_url() : home_url() );
        $_SESSION[ 'gcl_product_id' ] = $product_id;
        $_SESSION[ 'gcl_docs_id' ]    = $docs_id;

        $google_url = $this->get_google_auth_url();

        $html  = '<div class="ezd-google-login-container">';
        
        $html .= '<a href="#" class="' . esc_attr( $class ) . '" data-href="' . esc_url( $google_url ) . '" data-product_id="' . esc_attr( $product_id ) . '" data-docs_id="' . esc_attr( $docs_id ) . '">';        
        $html .= '<svg width="18" height="18" version="1.1" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 48 48" class="LgbsSe-Bz112c"><g><path fill="#EA4335" d="M24 9.5c3.54 0 6.71 1.22 9.21 3.6l6.85-6.85C35.9 2.38 30.47 0 24 0 14.62 0 6.51 5.38 2.56 13.22l7.98 6.19C12.43 13.72 17.74 9.5 24 9.5z"></path><path fill="#4285F4" d="M46.98 24.55c0-1.57-.15-3.09-.38-4.55H24v9.02h12.94c-.58 2.96-2.26 5.48-4.78 7.18l7.73 6c4.51-4.18 7.09-10.36 7.09-17.65z"></path><path fill="#FBBC05" d="M10.53 28.59c-.48-1.45-.76-2.99-.76-4.59s.27-3.14.76-4.59l-7.98-6.19C.92 16.46 0 20.12 0 24c0 3.88.92 7.54 2.56 10.78l7.97-6.19z"></path><path fill="#34A853" d="M24 48c6.48 0 11.93-2.13 15.89-5.81l-7.73-6c-2.15 1.45-4.92 2.3-8.16 2.3-6.26 0-11.57-4.22-13.47-9.91l-7.98 6.19C6.51 42.62 14.62 48 24 48z"></path><path fill="none" d="M0 0h48v48H0z"></path></g></svg>';
        $html .= '<span>' . esc_html( $text ) . '</span>';
        $html .= '</a>';
        $html .= '</div>';

        return $html;
    }
    
    /**
     * Generate Google OAuth URL
     *
     * @return string
     */
    private function get_google_auth_url() {
        $state = [];

        if ( ! session_id() ) {
            session_start();
        }

        $state[ 'product_id' ] = $_SESSION[ 'gcl_product_id' ] ?? '';
        $state[ 'docs_id' ]    = $_SESSION[ 'gcl_docs_id' ] ?? '';
        $params = [
            'client_id'     => $this->client_id,
            'redirect_uri'  => $this->redirect_uri,
            'response_type' => 'code',
            'scope'         => 'openid email profile',
            'access_type'   => 'offline',
            'state'         => base64_encode( json_encode( $state ) )
        ];

        return 'https://accounts.google.com/o/oauth2/auth?' . http_build_query( $params );
    }

    /**
     * Handle Google OAuth callback
     */
    public function handle_google_callback() {
        $is_callback = get_query_var( 'google_auth_callback' ) || isset( $_GET[ 'code' ] );

        if ( ! $is_callback) {
            return;
        }

        if ( isset( $_GET[ 'error' ] ) ) {
            $error              = sanitize_text_field( $_GET[ 'error' ] );
            $error_description  = isset( $_GET[ 'error_description' ] ) ? sanitize_text_field( $_GET[ 'error_description' ] ) : '';
            error_log( 'Google OAuth Error: ' . $error . ' - ' . $error_description );
            wp_redirect( wp_login_url() . '?google_error=1' );
            exit;
        }

        if ( isset( $_GET[ 'code' ] ) ) {
            $code       = sanitize_text_field( $_GET[ 'code' ] );
            $token_data = $this->exchange_code_for_token( $code );

            if ( $token_data && isset( $token_data[ 'access_token' ] ) ) {
                $user_data = $this->get_user_info( $token_data[ 'access_token' ] );

                if ( $user_data ) {
                    $this->login_or_register_user( $user_data );

                    if ( ! session_id() ) {
                        session_start();
                    }

                    // Fallback via state param if session fails
                    $product_id = 0;
                    $docs_id = 0;

                    if ( isset( $_SESSION[ 'gcl_product_id' ] ) ) {
                        $product_id = intval( $_SESSION[ 'gcl_product_id' ] );
                    }

                    if ( isset( $_SESSION[ 'gcl_docs_id' ] ) ) {
                        $docs_id = intval( $_SESSION[ 'gcl_docs_id' ] );
                    }

                    // Try to recover from state param if session empty
                    if ( isset( $_GET[ 'state' ] ) ) {
                        $state_data = json_decode( base64_decode( $_GET[ 'state' ] ), true );
                        if ( is_array( $state_data ) ) {
                            $product_id = ! empty( $state_data[ 'product_id' ] ) ? intval( $state_data[ 'product_id' ] ) : $product_id;
                            $docs_id    = ! empty( $state_data[ 'docs_id' ] ) ? intval( $state_data[ 'docs_id' ] ) : $docs_id;
                        }
                    }

                    // Clear session
                    unset( $_SESSION[ 'gcl_product_id' ], $_SESSION[ 'gcl_docs_id' ], $_SESSION[ 'gcl_redirect_url' ] );

                    $redirect = home_url();

                    // WooCommerce session fix
                    if (function_exists( 'WC' ) ) {
                        if ( ! WC()->session->has_session() ) {
                            WC()->session->set_customer_session_cookie( true );
                        }
                    }

                    // ✅ Pro Course — Add to cart and redirect to checkout
                    if ( $product_id && function_exists( 'WC' ) ) {
                        $already_enrolled = false;

                        if ( $docs_id ) {

                            $current_user     = wp_get_current_user();
                            $username         = $current_user->user_login;
                            $eazy_course_data = get_post_meta( $docs_id, 'eazy_course_data', true );
                            $existing_data    = ! empty( $eazy_course_data ) ? maybe_unserialize( $eazy_course_data ) : [];

                            foreach ( $existing_data as $data ) {
                                if ( isset( $data[ 'username' ] ) && $data[ 'username' ] === $username ) {
                                    $already_enrolled = true;
                                    break;
                                }
                            }
                        }

                        if ( ! $already_enrolled ) {
                            $cart_data = [];

                            if ( $docs_id ) {
                                $cart_data[ 'docs_id' ] = $docs_id;
                            }

                            WC()->cart->add_to_cart( $product_id, 1, 0, [], $cart_data );
                            WC()->cart->calculate_totals();
                            $redirect = wc_get_checkout_url();
                        } else {
                            // Optional: Redirect to course page or dashboard instead of checkout
                            $redirect = get_permalink( $docs_id );
                        }
                    }

                    // ✅ Free Course — Enroll directly
                    elseif ( $docs_id && ! $product_id ) {

                        $current_user     = wp_get_current_user();
                        $username         = $current_user->user_login;
                        $eazy_course_data = get_post_meta( $docs_id, 'eazy_course_data', true );
                        $existing_data    = ! empty( $eazy_course_data ) ? maybe_unserialize( $eazy_course_data ) : [];
                        $already_enrolled = false;

                        foreach ( $existing_data as $data ) {
                            if ( isset( $data[ 'username' ] ) && $data[ 'username' ] === $username ) {
                                $already_enrolled = true;
                                break;
                            }
                        }

                        if ( ! $already_enrolled ) {
                            $existing_data[] = [
                                'enrolled' => 1,
                                'username' => $username,
                            ];
                            update_post_meta( $docs_id, 'eazy_course_data', maybe_serialize( $existing_data ) );
                        }

                        $redirect = get_permalink( $docs_id );
                    }

                    // ✅ Output redirect and close popup
                    echo '<!DOCTYPE html><html><head><title>Redirecting…</title></head><body>';
                    echo '<script>
                        if ( window.opener) {
                            window.opener.location.href = "' . esc_url( $redirect ) . '";
                            window.close();
                        } else {
                            window.location.href = "' . esc_url( $redirect ) . '";
                        }
                    </script>';
                    echo '</body></html>';
                    exit;
                }
            }

            // Fallback on failure
            wp_redirect( wp_login_url() . '?google_error=1' );
            exit;
        }

        wp_redirect( home_url() );
        exit;
    }
    
    /**
     * Exchange authorization code for access token
     *
     * @param string $code
     * @return array|false
     */
    private function exchange_code_for_token( $code ) {
        $token_url = 'https://oauth2.googleapis.com/token';
        
        $params = array(
            'client_id'     => $this->client_id,
            'client_secret' => $this->client_secret,
            'code'          => $code,
            'grant_type'    => 'authorization_code',
            'redirect_uri'  => $this->redirect_uri
        );
        
        $response = wp_remote_post( $token_url, array(
            'body'    => $params,
            'headers' => array( 'Content-Type' => 'application/x-www-form-urlencoded' )
        ) );
        
        if ( is_wp_error( $response ) ) {
            return false;
        }
        
        return json_decode( wp_remote_retrieve_body( $response ), true );
    }
    
    /**
     * Get user info from Google
     *
     * @param string $access_token
     * @return array|false
     */
    private function get_user_info( $access_token ) {
        $user_info_url = 'https://www.googleapis.com/oauth2/v2/userinfo';
        
        $response = wp_remote_get( $user_info_url, array(
            'headers' => array( 'Authorization' => 'Bearer ' . $access_token )
        ) );
        
        if ( is_wp_error( $response ) ) {
            return false;
        }
        
        return json_decode( wp_remote_retrieve_body( $response ), true );
    }
    
    /**
     * Login or register user based on Google data
     *
     * @param array $user_data
     */
    private function login_or_register_user( $user_data ) {
        $email  = sanitize_email( $user_data[ 'email' ] );
        $user   = get_user_by( 'email', $email );
        
        if ( $user) {
            // User exists, log them in
            wp_set_auth_cookie( $user->ID );
            wp_set_current_user( $user->ID );
        } else {
            // Create new user
            $username = $this->generate_username( $email );
            $password = wp_generate_password();
            $user_id = wp_create_user( $username, $password, $email );
            
            if ( !is_wp_error( $user_id ) ) {
                // Update user meta
                update_user_meta( $user_id, 'first_name', sanitize_text_field( $user_data[ 'given_name' ] ) );
                update_user_meta( $user_id, 'last_name', sanitize_text_field( $user_data[ 'family_name' ] ) );
                update_user_meta( $user_id, 'google_id', sanitize_text_field( $user_data[ 'id' ] ) );
                
                // Log user in
                wp_set_auth_cookie( $user_id );
                wp_set_current_user( $user_id );
            }
        }
    }
    
    /**
     * Generate a unique username based on email
     *
     * @param string $email
     * @return string
     */
    private function generate_username( $email ) {
        $username = sanitize_user(current( explode( '@', $email ) ) );
        
        if ( username_exists( $username ) ) {
            $i = 1;
            while ( username_exists( $username . $i ) ) {
                $i++;
            }
            $username = $username . $i;
        }
        
        return $username;
    }
}