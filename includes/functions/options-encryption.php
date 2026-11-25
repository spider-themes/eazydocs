<?php
/**
 * Cannot access directly.
 */
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Get the value of a settings field.
 *
 * @param string $option  settings field name
 * @param string $section the section name this field belongs to
 * @param string $default default text if it's not found
 *
 * @return mixed
 */
function ezd_get_opt( $option, $default = '' ) {
	$options = get_option( 'eazydocs_settings' );

	if ( isset( $options[ $option ] ) ) {
		return $options[ $option ];
	}

	return $default;
}

/** Authorize settings saves independently of the bundled framework's nonce. */
function ezd_authorize_settings_save( $settings = null ) {
	$capability = ezd_get_opt( 'settings-edit-access', 'manage_options' );
	$capability = in_array( $capability, [ 'manage_options', 'publish_pages', 'publish_posts' ], true ) ? $capability : 'manage_options';
	if ( ! current_user_can( $capability ) ) {
		wp_die( esc_html__( 'You do not have permission to change EazyDocs settings.', 'eazydocs' ), '', [ 'response' => 403 ] );
	}
	return $settings;
}
add_action( 'wp_ajax_csf_eazydocs_settings_ajax_save', 'ezd_authorize_settings_save', 0 );
add_filter( 'csf_eazydocs_settings_save', 'ezd_authorize_settings_save', 0 );

/**
 * Marker prefix used to identify EazyDocs-encrypted secrets at rest.
 */
const EZD_ENC_PREFIX = 'ezd_enc::';

/**
 * Derive the symmetric encryption key from the site's secret auth salts.
 *
 * Using wp_salt() keeps the key out of the database and the codebase. The key
 * is hashed to a fixed 32-byte length for AES-256.
 *
 * @return string 32-byte binary key.
 */
function ezd_encryption_key() {
	return hash( 'sha256', wp_salt( 'secure_auth' ), true );
}

/**
 * Encrypt a sensitive value (e.g. an API secret) for storage at rest.
 *
 * Returns a prefixed, base64-encoded "IV + ciphertext" string so the value can
 * be recognised and decrypted later. Empty values and already-encrypted values
 * are returned unchanged to keep saves idempotent.
 *
 * @param string $value Plaintext value to encrypt.
 * @return string Encrypted, prefixed value (or the original input when empty/already encrypted).
 */
function ezd_encrypt( $value ) {
	if ( ! is_string( $value ) || '' === $value ) {
		return $value;
	}

	// Already encrypted — do not double-encrypt on re-save.
	if ( 0 === strpos( $value, EZD_ENC_PREFIX ) ) {
		return $value;
	}

	if ( ! function_exists( 'openssl_encrypt' ) ) {
		return $value; // Graceful fallback when OpenSSL is unavailable.
	}

	$iv         = random_bytes( 16 );
	$ciphertext = openssl_encrypt( $value, 'aes-256-cbc', ezd_encryption_key(), OPENSSL_RAW_DATA, $iv );

	if ( false === $ciphertext ) {
		return $value;
	}

	return EZD_ENC_PREFIX . base64_encode( $iv . $ciphertext );
}

/**
 * Decrypt a value previously encrypted with ezd_encrypt().
 *
 * Values without the EazyDocs marker prefix (e.g. legacy plaintext secrets) are
 * returned as-is so existing configurations keep working until re-saved.
 *
 * @param string $value Stored value to decrypt.
 * @return string Decrypted plaintext (or the original input when not encrypted).
 */
function ezd_decrypt( $value ) {
	if ( ! is_string( $value ) || 0 !== strpos( $value, EZD_ENC_PREFIX ) ) {
		return $value;
	}

	if ( ! function_exists( 'openssl_decrypt' ) ) {
		return '';
	}

	$payload = base64_decode( substr( $value, strlen( EZD_ENC_PREFIX ) ), true );

	if ( false === $payload || strlen( $payload ) <= 16 ) {
		return '';
	}

	$iv         = substr( $payload, 0, 16 );
	$ciphertext = substr( $payload, 16 );
	$plaintext  = openssl_decrypt( $ciphertext, 'aes-256-cbc', ezd_encryption_key(), OPENSSL_RAW_DATA, $iv );

	return ( false === $plaintext ) ? '' : $plaintext;
}

/**
 * CSF sanitize callback: encrypt a secret field value before it is saved.
 *
 * @param string $value Submitted field value.
 * @return string Encrypted value safe for storage.
 */
function ezd_sanitize_encrypted_secret( $value ) {
	return ezd_encrypt( sanitize_text_field( $value ) );
}

/**
 * Test the saved Google OAuth credentials against Google.
 *
 * Sends a token request with a dummy authorization code. Google validates the
 * client (and the redirect URI) before the code, so the error it returns tells
 * us exactly what is wrong without needing a full sign-in flow:
 *   - invalid_grant        => credentials + redirect URI are valid (success).
 *   - invalid_client       => wrong Client ID / Secret.
 *   - redirect_uri_mismatch => credentials OK, redirect URI not authorized.
 *
 * @return void Sends a JSON response.
 */
function ezd_google_test_connection_ajax() {
	if ( ! check_ajax_referer( 'eazydocs-admin-nonce', 'nonce', false ) ) {
		wp_send_json_error( [ 'message' => esc_html__( 'Invalid security token. Please refresh and try again.', 'eazydocs' ) ] );
	}

	if ( ! current_user_can( 'manage_options' ) ) {
		wp_send_json_error( [ 'message' => esc_html__( 'You are not allowed to do this.', 'eazydocs' ) ] );
	}

	$client_id = ezd_get_opt( 'google_client_id', '' );
	$secret    = ezd_decrypt( ezd_get_opt( 'google_client_secret', '' ) );

	if ( empty( $client_id ) || empty( $secret ) ) {
		wp_send_json_error( [ 'message' => esc_html__( 'Enter and save your Client ID and Client Secret first.', 'eazydocs' ) ] );
	}

	$response = wp_remote_post( 'https://oauth2.googleapis.com/token', [
		'timeout' => 15,
		'body'    => [
			'client_id'     => $client_id,
			'client_secret' => $secret,
			'code'          => 'ezd-connection-test',
			'grant_type'    => 'authorization_code',
			'redirect_uri'  => home_url( '/google-auth-callback/' ),
		],
	] );

	if ( is_wp_error( $response ) ) {
		wp_send_json_error( [ 'message' => esc_html__( 'Could not reach Google. Check your server connectivity and try again.', 'eazydocs' ) ] );
	}

	$body  = json_decode( wp_remote_retrieve_body( $response ), true );
	$error = is_array( $body ) && ! empty( $body['error'] ) ? $body['error'] : '';

	if ( 'invalid_grant' === $error ) {
		wp_send_json_success( [ 'message' => esc_html__( 'Success — your Client ID, Secret and Redirect URI are all valid.', 'eazydocs' ) ] );
	}

	if ( in_array( $error, [ 'invalid_client', 'unauthorized_client' ], true ) ) {
		wp_send_json_error( [ 'message' => esc_html__( 'Invalid Client ID or Client Secret. Re-copy both values from Google Cloud Console.', 'eazydocs' ) ] );
	}

	if ( 'redirect_uri_mismatch' === $error ) {
		wp_send_json_error( [ 'message' => esc_html__( 'Credentials are valid, but the Redirect URI is not authorized. Add the exact URI shown above to your Google OAuth client.', 'eazydocs' ) ] );
	}

	wp_send_json_error( [ 'message' => esc_html__( 'Unexpected response from Google. Please re-check your credentials.', 'eazydocs' ) ] );
}
add_action( 'wp_ajax_ezd_google_test_connection', 'ezd_google_test_connection_ajax' );
