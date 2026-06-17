<?php
/**
 * Google Authentication Integration
 * Allow users to sign in using their Google accounts.
 */
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

// Current configuration state (used for the live status / warnings below).
$ezd_g_client_id  = ezd_get_opt( 'google_client_id', '' );
$ezd_g_secret     = ezd_get_opt( 'google_client_secret', '' );
$ezd_g_configured = ! empty( $ezd_g_client_id ) && ! empty( $ezd_g_secret );
$ezd_g_can_reg    = (bool) get_option( 'users_can_register' );
$ezd_g_redirect   = home_url( '/google-auth-callback/' );

// Connection status banner.
if ( $ezd_g_configured ) {
    $ezd_g_status = '<div style="display:flex;align-items:center;gap:10px;padding:12px 16px;border-radius:8px;background:#e6f4ea;border:1px solid #b7e1c2;color:#1e7e34;">'
        . '<span class="dashicons dashicons-yes-alt"></span>'
        . '<span><strong>' . esc_html__( 'Connected.', 'eazydocs' ) . '</strong> '
        . esc_html__( 'Google credentials are saved. The "Sign in with Google" button now appears in the EazyDocs login popup and login/registration forms.', 'eazydocs' )
        . '</span></div>';
} else {
    $ezd_g_status = '<div style="display:flex;align-items:center;gap:10px;padding:12px 16px;border-radius:8px;background:#fff4e5;border:1px solid #ffd8a8;color:#a15c07;">'
        . '<span class="dashicons dashicons-warning"></span>'
        . '<span><strong>' . esc_html__( 'Setup incomplete.', 'eazydocs' ) . '</strong> '
        . esc_html__( 'Add your Client ID and Client Secret below to activate Google sign-in.', 'eazydocs' )
        . '</span></div>';
}

// Registration warning — the OAuth callback cannot create new accounts when
// "Anyone can register" is off, so first-time Google users would be blocked.
$ezd_g_reg_warning = $ezd_g_can_reg
    ? ''
    : '<div style="display:flex;align-items:flex-start;gap:10px;padding:12px 16px;border-radius:8px;background:#fdecea;border:1px solid #f5c2c0;color:#b71c1c;margin-top:12px;">'
        . '<span class="dashicons dashicons-info-outline"></span>'
        . '<span>' . sprintf(
            /* translators: %s: Settings → General link label. */
            esc_html__( 'New visitors cannot be registered because membership is disabled. Existing users can still sign in. To allow new accounts, enable %s.', 'eazydocs' ),
            '<a href="' . esc_url( admin_url( 'options-general.php' ) ) . '" target="_blank"><strong>' . esc_html__( 'Settings → General → Anyone can register', 'eazydocs' ) . '</strong></a>'
        ) . '</span></div>';

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
			'subtitle'   => esc_html__( 'Enable one-click Google sign-in across the EazyDocs login popup and the WordPress login/registration forms.', 'eazydocs' ),
		),

		array(
			'id'         => 'is_google_login',
			'type'       => 'switcher',
			'title'      => esc_html__( 'Enable Google Sign-In', 'eazydocs' ),
			'subtitle'   => esc_html__( 'Allow visitors to authenticate using their Google account credentials.', 'eazydocs' ),
			'default'    => false,
			'text_on'    => esc_html__( 'On', 'eazydocs' ),
			'text_off'   => esc_html__( 'Off', 'eazydocs' ),
		),

		array(
			'id'         => 'ezd_google_status',
			'type'       => 'content',
			'content'    => $ezd_g_status . $ezd_g_reg_warning,
			'dependency' => array(
				[ 'is_google_login', '==', 'true' ]
			)
		),

		array(
			'id'         => 'google_client_id',
			'type'       => 'text',
			'title'      => esc_html__( 'Google Client ID', 'eazydocs' ),
			'subtitle'   => esc_html__( 'Your OAuth 2.0 Client ID from Google Cloud Console.', 'eazydocs' ),
			'placeholder' => '1234567890-abcdefg.apps.googleusercontent.com',
			'dependency' 	=> array(
				[ 'is_google_login', '==', 'true' ]
			)
		),

		array(
			'id'         => 'google_client_secret',
			'type'       => 'text',
			'title'      => esc_html__( 'Google Client Secret', 'eazydocs' ),
			'subtitle'   => esc_html__( 'Your OAuth 2.0 Client Secret from Google Cloud Console.', 'eazydocs' ),
			'desc'       => esc_html__( 'Stored encrypted. The masked value is never shown in plain text.', 'eazydocs' ),
			'sanitize'   => 'ezd_sanitize_encrypted_secret',
			'attributes' => array(
				'type'         => 'password',
				'autocomplete' => 'new-password',
				'placeholder'  => '••••••••••••••••',
			),
			'dependency' 	=> array(
				[ 'is_google_login', '==', 'true' ]
			)
		),

		array(
			'id'         => 'google_redirect_uri',
			'type'       => 'content',
			'title'      => esc_html__( 'Authorized Redirect URI', 'eazydocs' ),
			'content'    => '<p>' . esc_html__( 'Add this exact URL to the "Authorized redirect URIs" list in your Google Cloud Console OAuth client:', 'eazydocs' ) . '</p>
			<span style="display:inline-flex;gap:8px;align-items:center;flex-wrap:wrap;">
				<input type="text" readonly onfocus="this.select()" value="' . esc_attr( $ezd_g_redirect ) . '" style="width:100%;max-width:420px;padding:8px 12px;font-family:monospace;background:#f6f7f7;border:1px solid #dcdcde;border-radius:6px;" />
				<button type="button" class="button button-secondary ezd-copy-redirect" data-copy="' . esc_attr( $ezd_g_redirect ) . '">' . esc_html__( 'Copy', 'eazydocs' ) . '</button>
			</span>
			<p style="color:#d63638;margin-top:8px;"><strong>⚠️ ' . esc_html__( 'Copy this URL exactly as shown.', 'eazydocs' ) . '</strong></p>',
			'dependency' 	=> array(
				[ 'is_google_login', '==', 'true' ]
			)
		),

		array(
			'id'         => 'ezd_google_test',
			'type'       => 'content',
			'title'      => esc_html__( 'Test Connection', 'eazydocs' ),
			'content'    => '<style>.ezd-google-test-result{font-weight:600;}.ezd-test-ok{color:#1e7e34;}.ezd-test-fail{color:#b71c1c;}</style>
			<button type="button" class="button button-primary ezd-google-test-btn">' . esc_html__( 'Test Connection', 'eazydocs' ) . '</button>
			<span class="ezd-google-test-result" style="margin-left:10px;"></span>
			<p class="description" style="margin-top:8px;">' . esc_html__( 'Verifies your saved Client ID, Secret and Redirect URI directly with Google. Save your changes first.', 'eazydocs' ) . '</p>',
			'dependency' 	=> array(
				[ 'is_google_login', '==', 'true' ]
			)
		),

		array(
			'type'    => 'content',
			'title'   => esc_html__( 'Configuration Guide', 'eazydocs' ),
			'content' => '
			<style>
				.ezd-gg{font-size:13px;line-height:1.6;color:#3c434a;}
				.ezd-gg-steps{counter-reset:ezd-gg;list-style:none;margin:0;padding:0;}
				.ezd-gg-steps > li{position:relative;padding:0 0 16px 42px;margin:0;}
				.ezd-gg-steps > li:not(:last-child)::after{content:"";position:absolute;left:14px;top:32px;bottom:4px;width:2px;background:#e2e6ea;}
				.ezd-gg-steps > li::before{counter-increment:ezd-gg;content:counter(ezd-gg);position:absolute;left:0;top:0;width:28px;height:28px;border-radius:50%;background:#1a73e8;color:#fff;font-weight:600;display:flex;align-items:center;justify-content:center;}
				.ezd-gg-steps > li > strong{display:block;font-size:14px;color:#1d2327;margin-bottom:2px;}
				.ezd-gg code{background:#f0f0f1;padding:1px 6px;border-radius:4px;font-size:12px;}
				.ezd-gg .ezd-gg-path{color:#1a73e8;font-weight:600;white-space:nowrap;}
				.ezd-gg-field{display:block;margin-top:6px;padding:7px 10px;background:#f6f7f7;border:1px solid #dcdcde;border-radius:6px;font-family:monospace;font-size:12px;word-break:break-all;}
				.ezd-gg-tips{margin-top:18px;padding:14px 16px;border-radius:8px;background:#fff8e1;border:1px solid #f3d98b;}
				.ezd-gg-tips h4{margin:0 0 8px;font-size:13px;}
				.ezd-gg-tips ul{margin:0;padding-left:18px;list-style:disc;}
				.ezd-gg-tips li{padding:0 0 6px;}
			</style>
			<div class="ezd-gg">
				<ol class="ezd-gg-steps">
					<li>
						<strong>' . esc_html__( 'Create a Google Cloud project', 'eazydocs' ) . '</strong>
						' . sprintf(
							/* translators: %s: link to Google Cloud Console. */
							esc_html__( 'Open the %s and create a new project (or select an existing one) using the project picker in the top bar.', 'eazydocs' ),
							'<a href="https://console.cloud.google.com/" target="_blank" rel="noopener">Google Cloud Console</a>'
						) . '
					</li>
					<li>
						<strong>' . esc_html__( 'Open the Google Auth Platform', 'eazydocs' ) . '</strong>
						' . sprintf(
							/* translators: %s: link to the Google Auth Platform. */
							esc_html__( 'Go to %s. The first time, click "Get started" to begin configuring your sign-in (this replaces the old "OAuth consent screen" page).', 'eazydocs' ),
							'<a href="https://console.cloud.google.com/auth/overview" target="_blank" rel="noopener"><span class="ezd-gg-path">APIs &amp; Services &rarr; Google Auth Platform</span></a>'
						) . '
					</li>
					<li>
						<strong>' . esc_html__( 'Set Branding & Audience', 'eazydocs' ) . '</strong>
						' . esc_html__( 'Under', 'eazydocs' ) . ' <span class="ezd-gg-path">Branding</span>, ' . esc_html__( 'enter an app name and your support email. Under', 'eazydocs' ) . ' <span class="ezd-gg-path">Audience</span>, ' . esc_html__( 'choose User type', 'eazydocs' ) . ' <span class="ezd-gg-path">External</span>.
						<br>' . esc_html__( 'While the app stays in "Testing", add the Google accounts that may sign in under "Test users" — or click "Publish app" to allow anyone.', 'eazydocs' ) . '
					</li>
					<li>
						<strong>' . esc_html__( 'Add the sign-in scopes', 'eazydocs' ) . '</strong>
						' . esc_html__( 'Open the', 'eazydocs' ) . ' <span class="ezd-gg-path">Data Access</span> ' . esc_html__( 'tab, click "Add or remove scopes", and select', 'eazydocs' ) . ' <code>openid</code>, <code>email</code> ' . esc_html__( 'and', 'eazydocs' ) . ' <code>profile</code>.
					</li>
					<li>
						<strong>' . esc_html__( 'Create an OAuth client', 'eazydocs' ) . '</strong>
						' . sprintf(
							/* translators: %s: link to the Clients page. */
							esc_html__( 'Open the %s tab and click "Create client". For Application type choose', 'eazydocs' ),
							'<a href="https://console.cloud.google.com/auth/clients" target="_blank" rel="noopener"><span class="ezd-gg-path">Clients</span></a>'
						) . ' <span class="ezd-gg-path">Web application</span>.
					</li>
					<li>
						<strong>' . esc_html__( 'Add the Authorized JavaScript origin', 'eazydocs' ) . '</strong>
						' . esc_html__( 'In the client, under "Authorized JavaScript origins", add your site origin:', 'eazydocs' ) . '
						<span class="ezd-gg-field">' . esc_html( home_url() ) . '</span>
					</li>
					<li>
						<strong>' . esc_html__( 'Add the Authorized Redirect URI', 'eazydocs' ) . '</strong>
						' . esc_html__( 'In the same client, under "Authorized redirect URIs", paste this exact URL, then click Create:', 'eazydocs' ) . '
						<span class="ezd-gg-field">' . esc_html( $ezd_g_redirect ) . '</span>
					</li>
					<li>
						<strong>' . esc_html__( 'Copy your credentials here', 'eazydocs' ) . '</strong>
						' . esc_html__( 'Copy the generated Client ID and Client Secret into the fields above, click Save, then press "Test Connection" to confirm everything works.', 'eazydocs' ) . '
					</li>
				</ol>

				<div class="ezd-gg-tips">
					<h4>🛠️ ' . esc_html__( 'Troubleshooting', 'eazydocs' ) . '</h4>
					<ul>
						<li><strong>redirect_uri_mismatch</strong> — ' . esc_html__( 'the Redirect URI in Google must match the one above character-for-character (including http/https and the trailing slash).', 'eazydocs' ) . '</li>
						<li><strong>' . esc_html__( 'Access blocked / app not verified', 'eazydocs' ) . '</strong> — ' . esc_html__( 'add the user under "Test users", or publish the consent screen.', 'eazydocs' ) . '</li>
						<li><strong>' . esc_html__( 'First-time visitors cannot sign in', 'eazydocs' ) . '</strong> — ' . esc_html__( 'enable Settings → General → Anyone can register so new Google users can be created.', 'eazydocs' ) . '</li>
						<li>' . esc_html__( 'Changes in Google can take a few minutes to take effect.', 'eazydocs' ) . '</li>
					</ul>
				</div>

				<p style="margin-top:16px;"><strong>' . esc_html__( 'Where the button appears:', 'eazydocs' ) . '</strong> '
					. esc_html__( 'Once configured, the "Sign in with Google" button shows automatically in the EazyDocs login popup and the WordPress login/registration forms. To place it anywhere else, use the shortcode', 'eazydocs' )
					. ' <code>[ezd_google_login]</code>.</p>
			</div>',
			'dependency' 	=> array(
				[ 'is_google_login', '==', 'true' ]
			)
		)
	]
) );
