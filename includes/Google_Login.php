<?php
namespace EazyDocs;

/**
 * Cannot access directly.
 */
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

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

        // EazyDocs login popup (private/role gate + collaboration).
        add_action( 'ezd_login_popup_alt_methods', array( $this, 'render_popup_button' ), 10, 1 );
        
        add_action( 'template_redirect', array( $this, 'handle_google_callback' ) );
        add_action( 'login_message', array( $this, 'show_login_messages' ) );
        add_shortcode( 'ezd_google_login', array( $this, 'google_login_shortcode' ) );

        // login page 
        add_action( 'login_enqueue_scripts', function(){
            wp_enqueue_style( 'eazydocs-frontend', EZD_STYLES . 'frontend.css', array(), EZD_VERSION );
            wp_enqueue_script( 'eazydocs-single', EZD_ASSETS . 'js/frontend/docs-single.js', array( 'jquery' ), EZD_VERSION, true );
        });
        
        // Get plugin settings
        $this->client_id     = ezd_get_opt( 'google_client_id', '' );
        // Secret is stored encrypted at rest; decrypt for use (legacy plaintext is returned as-is).
        $this->client_secret = ezd_decrypt( ezd_get_opt( 'google_client_secret', '' ) );
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
        if ( isset( $_GET[ 'google_error' ] ) ) { // phpcs:ignore WordPress.Security.NonceVerification.Recommended
            $error_msg = '<div id="login_error">' . esc_html__( 'Google login failed. Please try again or contact support.', 'eazydocs' ) . '</div>';
            return $error_msg . $message;
        }
        return $message;
    }
    private function get_current_page_id() {
        if ( is_home() || is_front_page() ) {
            return get_option( 'page_on_front', 0 );
        }

        if ( is_page() || is_single() ) {
            return get_the_ID();
        }

        $type = is_category() ? 'category_' : ( is_tag() ? 'tag_' : ( is_author() ? 'author_' : ( is_archive() ? 'archive_' : '' ) ) );
        if ( $type ) {
            return $type . get_queried_object_id();
        }

        global $post;
        return isset( $post->ID ) ? $post->ID : 0;
    }
    
    /**
     * Add Google Login button to login and register forms
     */
    public function add_google_login_button() {
        if ( empty( $this->client_id ) || empty( $this->client_secret ) ) {
            return;
        }
        echo wp_kses_post( $this->get_google_login_html( __( 'Sign in with Google', 'eazydocs' ), 'ezd-google-login-btn' ) );
    }

    /**
     * Render the Google button inside the EazyDocs login popup.
     *
     * Hooked to `ezd_login_popup_alt_methods`. Includes an "or" divider and
     * passes a redirect so the user returns to where they started after sign-in.
     *
     * @param string $redirect Where to send the user after a successful sign-in.
     *
     * @return void
     */
    public function render_popup_button( $redirect = '' ) {
        if ( empty( $this->client_id ) || empty( $this->client_secret ) ) {
            return;
        }

        $redirect = $redirect ? esc_url_raw( $redirect ) : '';

        // get_google_login_html() returns trusted, internally-escaped markup
        // (svg + data-* attributes that wp_kses_post would strip).
        echo '<div class="ezd-login-or"><span>' . esc_html__( 'or', 'eazydocs' ) . '</span></div>';
        echo $this->get_google_login_html( __( 'Sign in with Google', 'eazydocs' ), 'ezd-google-login-btn', $redirect ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
    }
    
    /**
     * Shortcode for Google Login button
     *
     * @param array $atts
     * @return string
     */
    public function google_login_shortcode( $atts ) {
        if ( empty( $this->client_id ) || empty( $this->client_secret ) ) {
            return '<p>' . esc_html__( 'Google Login not configured. Please check settings.', 'eazydocs' ) . '</p>';
        }

        $atts = shortcode_atts( array(
            'text'       => __( 'Sign in with Google', 'eazydocs' ),
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
    private function get_google_login_html( $text = '', $class = 'ezd-google-login-btn', $redirect = '', $product_id = '', $docs_id = '' ) {
        if ( is_user_logged_in() ) {
            return '';
        }

        $text = $text ? $text : __( 'Sign in with Google', 'eazydocs' );

        // Pass flow context via the signed OAuth `state` param only — never start
        // a PHP session here. session_start() sets PHPSESSID, which forces a cache
        // MISS on server-level / full-page caches for every page that renders this
        // button (e.g. the docs login popup in the footer for logged-out visitors),
        // and serialises concurrent requests behind PHP's session file lock.
        $google_url = $this->get_google_auth_url( $redirect, $product_id, $docs_id );

        $html  = '<div class="ezd-google-login-container">';
        $html .= '<a href="#" class="' . esc_attr( $class ) . '" data-href="' . esc_url( $google_url ) . '" data-product_id="' . esc_attr( $product_id ) . '" data-docs_id="' . esc_attr( $docs_id ) . '" aria-label="' . esc_attr__( 'Sign in with Google', 'eazydocs' ) . '">';
        $html .= '<svg width="18" height="18" version="1.1" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 48 48" class="LgbsSe-Bz112c" role="img" aria-hidden="true"><g><path fill="#EA4335" d="M24 9.5c3.54 0 6.71 1.22 9.21 3.6l6.85-6.85C35.9 2.38 30.47 0 24 0 14.62 0 6.51 5.38 2.56 13.22l7.98 6.19C12.43 13.72 17.74 9.5 24 9.5z"></path><path fill="#4285F4" d="M46.98 24.55c0-1.57-.15-3.09-.38-4.55H24v9.02h12.94c-.58 2.96-2.26 5.48-4.78 7.18l7.73 6c4.51-4.18 7.09-10.36 7.09-17.65z"></path><path fill="#FBBC05" d="M10.53 28.59c-.48-1.45-.76-2.99-.76-4.59s.27-3.14.76-4.59l-7.98-6.19C.92 16.46 0 20.12 0 24c0 3.88.92 7.54 2.56 10.78l7.97-6.19z"></path><path fill="#34A853" d="M24 48c6.48 0 11.93-2.13 15.89-5.81l-7.73-6c-2.15 1.45-4.92 2.3-8.16 2.3-6.26 0-11.57-4.22-13.47-9.91l-7.98 6.19C6.51 42.62 14.62 48 24 48z"></path><path fill="none" d="M0 0h48v48H0z"></path></g></svg>';
        $html .= '<span>' . esc_html( $text ) . '</span>';
        $html .= '</a>';
        $html .= '</div>';

        return $html;
    }
    
    /**
     * Generate Google OAuth URL.
     *
     * Context (redirect / product / docs) is stored in the signed OAuth `state`
     * parameter so no PHP session cookie is required on cacheable frontend pages.
     *
     * @param string $redirect   Optional post-login redirect URL.
     * @param string $product_id Optional WooCommerce product ID.
     * @param string $docs_id    Optional docs ID for enrollment flows.
     * @return string
     */
    private function get_google_auth_url( $redirect = '', $product_id = '', $docs_id = '' ) {
        $state = $this->encode_state( [
            'product_id' => absint( $product_id ),
            'docs_id'    => absint( $docs_id ),
            'redirect'   => $redirect ? esc_url_raw( $redirect ) : '',
        ] );

        $params = [
            'client_id'              => $this->client_id,
            'redirect_uri'           => $this->redirect_uri,
            'response_type'          => 'code',
            'scope'                  => apply_filters( 'eazydocs_google_scopes', 'openid email profile' ),
            'access_type'            => 'offline',
            'include_granted_scopes' => 'true',
            'state'                  => $state,
            // 'prompt' => 'consent' // Optionally force consent each time.
        ];

        // Use latest OAuth endpoint
        return 'https://accounts.google.com/o/oauth2/v2/auth?' . http_build_query( $params, '', '&', PHP_QUERY_RFC3986 );
    }

    /**
     * Handle Google OAuth callback
     */
    public function handle_google_callback() {
        if ( ! $this->is_callback_request() ) {
            return;
        }

        if ( isset( $_GET[ 'error' ] ) ) { // phpcs:ignore WordPress.Security.NonceVerification.Recommended
            $error              = sanitize_text_field( wp_unslash( $_GET[ 'error' ] ) );
            $error_description  = isset( $_GET[ 'error_description' ] ) ? sanitize_text_field( wp_unslash( $_GET[ 'error_description' ] ) ) : '';
            error_log( 'Google OAuth Error: ' . $error . ' - ' . $error_description ); // phpcs:ignore WordPress.PHP.DevelopmentFunctions.error_log_error_log
            wp_safe_redirect( wp_login_url() . '?google_error=1' );
            exit;
        }

        if ( isset( $_GET[ 'code' ] ) ) { // phpcs:ignore WordPress.Security.NonceVerification.Recommended
            // Validate the signed state and the browser-bound CSRF token BEFORE
            // exchanging the code or logging anyone in. Previously the check ran
            // after login (and was skipped entirely when `state` was absent),
            // which allowed login CSRF.
            $state_data = $this->decode_state( isset( $_GET['state'] ) ? sanitize_text_field( wp_unslash( $_GET['state'] ) ) : '' ); // phpcs:ignore WordPress.Security.NonceVerification.Recommended
            $this->clear_csrf_cookie();

            if ( null === $state_data ) {
                wp_safe_redirect( wp_login_url() . '?google_error=1' );
                exit;
            }

            $code       = sanitize_text_field( wp_unslash( $_GET[ 'code' ] ) );
            $token_data = $this->exchange_code_for_token( $code );

            if ( $token_data && isset( $token_data[ 'access_token' ] ) ) {
                $user_data = $this->get_user_info( $token_data[ 'access_token' ] );

                if ( $user_data && $this->login_or_register_user( $user_data ) ) {
                    $product_id        = absint( $state_data['product_id'] ?? 0 );
                    $docs_id           = absint( $state_data['docs_id'] ?? 0 );
                    $explicit_redirect = ! empty( $state_data['redirect'] ) ? esc_url_raw( $state_data['redirect'] ) : '';

                    // Default to the caller-provided destination (validated to this
                    // site); WooCommerce/course flows below may still override it.
                    $redirect = $explicit_redirect ? wp_validate_redirect( $explicit_redirect, home_url() ) : home_url();

                    // WooCommerce session fix
                    if ( function_exists( 'WC' ) && WC()->session ) {
                        if ( ! WC()->session->has_session() ) {
                            WC()->session->set_customer_session_cookie( true );
                        }
                    }

                    // ✅ Pro Course — Add to cart and redirect to checkout
                    if ( $product_id && function_exists( 'WC' ) && WC()->cart ) {
                        if ( ! $this->is_user_enrolled( $docs_id, wp_get_current_user()->user_login ) ) {
                            $cart_data = $docs_id ? [ 'docs_id' => $docs_id ] : [];
                            WC()->cart->add_to_cart( $product_id, 1, 0, [], $cart_data );
                            WC()->cart->calculate_totals();
                            $redirect = wc_get_checkout_url();
                        } else {
                            $redirect = get_permalink( $docs_id );
                        }
                    }
                    // ✅ Free Course — Enroll directly
                    elseif ( $docs_id && ! $product_id ) {
                        $this->enroll_user( $docs_id, wp_get_current_user()->user_login );
                        $redirect = get_permalink( $docs_id );
                    }

                    $redirect = apply_filters( 'eazydocs_google_login_redirect', $redirect, $product_id, $docs_id );

                    // ✅ Output redirect and close popup
                    echo '<!DOCTYPE html><html><head><meta charset="' . esc_attr( get_bloginfo( 'charset' ) ) . '"><title>' . esc_html__( 'Redirecting…', 'eazydocs' ) . '</title></head><body>';
                    // Note: the old single-quoted string emitted literal "\n" sequences
                    // into the script (a JS syntax error), so the popup never redirected.
                    $redirect_js = wp_json_encode( esc_url_raw( $redirect ) );
                    echo '<script>'
                        . 'if ( window.opener ) { window.opener.location.href = ' . $redirect_js . '; window.close(); }'
                        . ' else { window.location.href = ' . $redirect_js . '; }'
                        . '</script>'; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- JSON-encoded, esc_url_raw()'d URL.
                    echo '</body></html>';
                    exit;
                }
            }

            // Fallback on failure
            wp_safe_redirect( wp_login_url() . '?google_error=1' );
            exit;
        }

        wp_safe_redirect( home_url() );
        exit;
    }

    /**
     * Whether this request is the OAuth callback.
     *
     * Matches the callback path itself rather than "any URL with ?code=": the old
     * check hijacked every front-end request carrying a `code` query arg (other
     * OAuth plugins, coupon links…) and bounced it to wp-login.php. The path
     * match also works before rewrite rules are flushed.
     *
     * @return bool
     */
    private function is_callback_request() {
        if ( get_query_var( 'google_auth_callback' ) ) {
            return true;
        }

        $request_path  = isset( $_SERVER['REQUEST_URI'] ) ? wp_parse_url( sanitize_text_field( wp_unslash( $_SERVER['REQUEST_URI'] ) ), PHP_URL_PATH ) : '';
        $callback_path = wp_parse_url( $this->redirect_uri, PHP_URL_PATH );

        return $request_path && untrailingslashit( (string) $request_path ) === untrailingslashit( (string) $callback_path );
    }

    /**
     * Sign the sign-in context into an OAuth `state` value.
     *
     * The front-end script appends ".<random token>" and stores the same token
     * in a short-lived cookie, binding the flow to the visitor's browser.
     *
     * @param array $data Context (product_id, docs_id, redirect).
     * @return string
     */
    private function encode_state( $data ) {
        $payload = rtrim( strtr( base64_encode( wp_json_encode( $data ) ), '+/', '-_' ), '=' );

        return $payload . '.' . hash_hmac( 'sha256', $payload, wp_salt( 'auth' ) );
    }

    /**
     * Verify a returned `state` (signature + CSRF cookie) and decode it.
     *
     * @param string $state Raw state from Google.
     * @return array|null Context array, or null when invalid.
     */
    private function decode_state( $state ) {
        $parts = explode( '.', (string) $state );
        if ( 3 !== count( $parts ) ) {
            return null;
        }

        list( $payload, $signature, $csrf ) = $parts;
        $cookie = isset( $_COOKIE['ezd_g_csrf'] ) ? sanitize_text_field( wp_unslash( $_COOKIE['ezd_g_csrf'] ) ) : '';

        if (
            ! hash_equals( hash_hmac( 'sha256', $payload, wp_salt( 'auth' ) ), $signature )
            || strlen( $csrf ) < 16 || '' === $cookie || ! hash_equals( $cookie, $csrf )
        ) {
            return null;
        }

        $data = json_decode( base64_decode( strtr( $payload, '-_', '+/' ) ), true );

        return is_array( $data ) ? $data : null;
    }

    /**
     * Expire the one-time CSRF cookie.
     */
    private function clear_csrf_cookie() {
        if ( isset( $_COOKIE['ezd_g_csrf'] ) && ! headers_sent() ) {
            setcookie( 'ezd_g_csrf', '', time() - HOUR_IN_SECONDS, '/' );
        }
    }
    
    /**
     * Check if user is already enrolled in docs
     *
     * @param int $docs_id
     * @param string $username
     * @return bool
     */
    private function is_user_enrolled( $docs_id, $username ) {
        if ( ! $docs_id ) {
            return false;
        }
        
        $eazy_course_data = get_post_meta( $docs_id, 'eazy_course_data', true );
        $existing_data    = ! empty( $eazy_course_data ) ? maybe_unserialize( $eazy_course_data ) : [];

        foreach ( $existing_data as $data ) {
            if ( isset( $data['username'] ) && $data['username'] === $username ) {
                return true;
            }
        }
        return false;
    }

    /**
     * Enroll user in free docs
     *
     * @param int $docs_id
     * @param string $username
     */
    private function enroll_user( $docs_id, $username ) {
        if ( ! $docs_id || $this->is_user_enrolled( $docs_id, $username ) ) {
            return;
        }

        $eazy_course_data = get_post_meta( $docs_id, 'eazy_course_data', true );
        $existing_data    = ! empty( $eazy_course_data ) ? maybe_unserialize( $eazy_course_data ) : [];
        $existing_data[]  = [ 'enrolled' => 1, 'username' => $username ];
        update_post_meta( $docs_id, 'eazy_course_data', maybe_serialize( $existing_data ) );
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
        // Only trust addresses Google has verified. Without this, anyone could
        // create a Google account on someone else's (unverified) address and be
        // logged straight into the matching WordPress account — admins included.
        $verified = $user_data['verified_email'] ?? ( $user_data['email_verified'] ?? false );
        if ( true !== $verified && 'true' !== $verified ) {
            return false;
        }

        $email  = sanitize_email( $user_data[ 'email' ] ?? '' );
        if ( ! is_email( $email ) ) {
            return false;
        }

        $user   = get_user_by( 'email', $email );

        if ( $user) {
            // User exists, log them in
            wp_set_auth_cookie( $user->ID );
            wp_set_current_user( $user->ID );
            return true;
        } else {
            // Security: Check if user registration is enabled in WordPress settings
            if ( ! get_option( 'users_can_register' ) ) {
                return false; // The caller redirects to the login error screen.
            }

            // Create new user
            $username = $this->generate_username( $email );
            $password = wp_generate_password();
            $user_id = wp_create_user( $username, $password, $email );
            
            if ( !is_wp_error( $user_id ) ) {
                // Update user meta (Google omits name fields for some accounts).
                update_user_meta( $user_id, 'first_name', sanitize_text_field( $user_data[ 'given_name' ] ?? '' ) );
                update_user_meta( $user_id, 'last_name', sanitize_text_field( $user_data[ 'family_name' ] ?? '' ) );
                update_user_meta( $user_id, 'google_id', sanitize_text_field( $user_data[ 'id' ] ?? '' ) );

                // Log user in
                wp_set_auth_cookie( $user_id );
                wp_set_current_user( $user_id );
                return true;
            }
        }

        return false;
    }
    
    /**
     * Generate a unique username based on email
     *
     * @param string $email
     * @return string
     */
    private function generate_username( $email ) {
        $username = sanitize_user( current( explode( '@', $email ) ) );

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

