<?php
/**
 * Google Authentication Integration
 * Allow users to sign in using their Google accounts.
 */
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}


// Google Login
CSF::createSection( $prefix, array(
	'id'     => 'ezd_google_login',
	'title'  => esc_html__( 'Google Sign-In', 'eazydocs' ),
	'icon'   => 'dashicons dashicons-google',
	'fields' => [
		array(
			'id'         => 'is_google_login_heading',
			'type'       => 'heading',
			'title'      => esc_html__( 'Google Authentication', 'eazydocs' ),
			'subtitle'   => esc_html__( 'Enable one-click Google sign-in for your documentation login and registration forms.', 'eazydocs' ),
		),
		
		array(
			'id'         => 'is_google_login',
			'type'       => 'switcher',
			'title'      => esc_html__( 'Enable Google Sign-In', 'eazydocs' ),
			'subtitle'   => esc_html__( 'Allow visitors to authenticate using their Google account credentials.', 'eazydocs' ),
			'default'    => false
		),

		array(
			'id'         => 'google_client_id',
			'type'       => 'text',
			'title'      => esc_html__( 'Google Client ID', 'eazydocs' ),
			'subtitle'   => esc_html__( 'Your OAuth 2.0 Client ID from Google Cloud Console.', 'eazydocs' ),
			'dependency' 	=> array(
				[ 'is_google_login', '==', 'true' ]
			)
		),

		array(
			'id'         => 'google_client_secret',
			'type'       => 'text',
			'title'      => esc_html__( 'Google Client Secret', 'eazydocs' ),
			'subtitle'   => esc_html__( 'Your OAuth 2.0 Client Secret from Google Cloud Console.', 'eazydocs' ),
			'dependency' 	=> array(
				[ 'is_google_login', '==', 'true' ]
			)
		),
		
		array(
			'id'         => 'google_redirect_uri',
			'type'       => 'content',
			'title'      => esc_html__( 'Authorized Redirect URI', 'eazydocs' ),
			'content'    => '<p>' . esc_html__( 'Add this exact URL to your Google Cloud Console OAuth settings:', 'eazydocs' ) . '</p>
			<code style="display:block;padding:10px;background:#f0f0f1;margin:10px 0;">' . esc_html( home_url('/google-auth-callback/') ) . '</code>
			<p style="color: #d63638;"><strong>⚠️ ' . esc_html__( 'Copy this URL exactly as shown above', 'eazydocs' ) . '</strong></p>
			<p><small><strong>' . esc_html__( 'Alternative:', 'eazydocs' ) . '</strong> ' . esc_html__( 'You can also use your home URL:', 'eazydocs' ) . '<br><code>' . esc_html( home_url() ) . '</code></small></p>',
			'dependency' 	=> array(
				[ 'is_google_login', '==', 'true' ]
			)
		),
		
		array(
			'type'    => 'content',
			'style'   => 'success',
			'title'      => esc_html__( 'Configuration Guide', 'eazydocs' ),
  			'content' => '<ol style="margin-left:20px;">
                <li>' . esc_html__( 'Visit the', 'eazydocs' ) . ' <a href="https://console.developers.google.com/" target="_blank">Google Cloud Console</a></li>
                <li>' . esc_html__( 'Create a new project or select an existing one', 'eazydocs' ) . '</li>
                <li>' . esc_html__( 'Enable the Google+ API or Google Identity API', 'eazydocs' ) . '</li>
                <li>' . esc_html__( 'Navigate to Credentials → Create Credentials → OAuth 2.0 Client ID', 'eazydocs' ) . '</li>
                <li>' . esc_html__( 'Select "Web application" as the application type', 'eazydocs' ) . '</li>
                <li><strong>' . esc_html__( 'Add the Redirect URI shown above to "Authorized redirect URIs"', 'eazydocs' ) . '</strong></li>
                <li>' . esc_html__( 'Copy your Client ID and Client Secret into the fields above', 'eazydocs' ) . '</li>
                <li>' . esc_html__( 'Use the shortcode', 'eazydocs' ) . ' <code>[google_login]</code> ' . esc_html__( 'to display the sign-in button', 'eazydocs' ) . '</li>
            </ol>',
			'dependency' 	=> array( 
				[ 'is_google_login', '==', 'true' ]
			)
		)
	]
) );