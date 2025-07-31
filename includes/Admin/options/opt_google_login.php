<?php

// Footnotes
CSF::createSection( $prefix, array(
	'id'     => 'ezd_google_login',
	'title'  => esc_html__( 'Google Login', 'eazydocs' ),
	'icon'   => 'dashicons dashicons-google',
	'fields' => [
		array(
			'id'         => 'is_google_login_heading',
			'type'       => 'heading',
			'title'      => esc_html__( 'Google Login', 'eazydocs' ),
			'subtitle'   => esc_html__( 'Adds a Google login button to EazyDocs login and sign-up forms, including extension forms.', 'eazydocs' ),
		),
		
		array(
			'id'         => 'is_google_login',
			'type'       => 'switcher',
			'title'      => esc_html__( 'Enable Google Login', 'eazydocs' ),
			'subtitle'   => esc_html__( 'Enable Google login functionality for your users', 'eazydocs' ),
			'default'    => false
		),

		array(
			'id'         => 'google_client_id',
			'type'       => 'text',
			'title'      => esc_html__( 'Client ID', 'eazydocs' ),
			'subtitle'   => esc_html__( 'Enter your Google Client ID', 'eazydocs' ),
			'dependency' 	=> array( 'is_google_login', '==', 'true' )
		),

		array(
			'id'         => 'google_client_secret',
			'type'       => 'text',
			'title'      => esc_html__( 'Client Secret', 'eazydocs' ),
			'subtitle'   => esc_html__( 'Enter your Google Client Secret', 'eazydocs' ),
			'dependency' 	=> array( 'is_google_login', '==', 'true' )
		),
		
		array(
			'id'         => 'google_redirect_uri',
			'type'       => 'content',
			'title'      => esc_html__( 'Redirect URI', 'eazydocs' ),
			'content'    => 'This is the URI where Google will redirect users after they authenticate.<br>
			You need to add this URI in your Google Console OAuth settings. <br> <code>' . esc_html( home_url('/google-auth-callback/') ) . '</code> <br> 
			<small style="color: #d63638;">⚠️ Add this EXACT URL to your Google Console OAuth settings</small><br>
			<small><strong>Alternative:</strong> You can also use your home URL:<br><code>' . esc_html( home_url() ) . '</code></small>',
			'dependency' 	=> array( 'is_google_login', '==', 'true' )
		),
		
		array(
			'type'    => 'content',
			'style'   => 'success',
			'title'      => esc_html__( 'Setup Instructions', 'eazydocs' ),
  			'content' => '<ol>
                <li>Go to <a href="https://console.developers.google.com/" target="_blank">Google Developers Console</a></li>
                <li>Create a new project or select existing one</li>
                <li>Enable Google+ API or Google Identity API</li>
                <li>Go to "Credentials" → "Create Credentials" → "OAuth 2.0 Client ID"</li>
                <li>Select "Web application" as application type</li>
                <li><strong>IMPORTANT:</strong> Add the redirect URI above to "Authorized redirect URIs" (copy it exactly)</li>
                <li>Copy Client ID and Client Secret to the form above</li>
                <li>Use shortcode [google_login] to display the login button anywhere</li>
                <li>The plugin will automatically detect the site URL and current page ID</li>
            </ol>',
			'dependency' 	=> array( 'is_google_login', '==', 'true' )
		)
	]
) );