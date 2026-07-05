<?php
/**
 * Cannot access directly.
 */
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}


/**
 * Render the Antimanual AI chatbot integration notice shown at the top of the
 * Docs Assistant settings.
 *
 * The Docs Assistant ships a rule-based knowledge-base widget. Pairing it with
 * the free Antimanual plugin upgrades it into an AI chatbot trained on the
 * site's docs. The notice is state-aware: it guides the admin to install,
 * activate, or (once connected) configure Antimanual.
 *
 * @return string Escaped HTML markup for a CSF `content` field.
 */
function ezd_assistant_antimanual_info() {
	// Load plugin helpers if not already available (settings load in admin,
	// but guard so this never fatals outside the dashboard).
	if ( ! function_exists( 'is_plugin_active' ) ) {
		require_once ABSPATH . 'wp-admin/includes/plugin.php';
	}

	$plugin_file = 'antimanual/antimanual.php';
	$is_active    = is_plugin_active( $plugin_file );
	$is_installed = $is_active || file_exists( WP_PLUGIN_DIR . '/' . $plugin_file );

	$wporg_url     = 'https://wordpress.org/plugins/antimanual/';
	$learn_more    = 'https://antimanual.spider-themes.net';
	$install_url   = admin_url( 'plugin-install.php?s=antimanual&tab=search&type=term' );
	$activate_url  = wp_nonce_url( admin_url( 'plugins.php?action=activate&plugin=' . $plugin_file ), 'activate-plugin_' . $plugin_file );
	$settings_url  = admin_url( 'admin.php?page=atml-chatbot' );

	if ( $is_active ) {
		$state   = 'connected';
		$badge   = esc_html__( 'Connected', 'eazydocs' );
		$heading = esc_html__( 'AI chatbot powered by Antimanual', 'eazydocs' );
		$body    = esc_html__( 'Antimanual is active. Your Docs Assistant can now answer visitors with an AI chatbot trained on your documentation — alongside the built-in knowledge base and contact tabs below.', 'eazydocs' );
		$primary = '<a href="' . esc_url( $settings_url ) . '" class="button button-primary">' . esc_html__( 'Configure Antimanual', 'eazydocs' ) . '</a>';
	} elseif ( $is_installed ) {
		$state   = 'action';
		$badge   = esc_html__( 'Action needed', 'eazydocs' );
		$heading = esc_html__( 'Activate Antimanual to add an AI chatbot', 'eazydocs' );
		$body    = esc_html__( 'Antimanual is installed but not active. Activate it to upgrade your Docs Assistant into an AI chatbot trained on your docs, with smart semantic search.', 'eazydocs' );
		$primary = '<a href="' . esc_url( $activate_url ) . '" class="button button-primary">' . esc_html__( 'Activate Antimanual', 'eazydocs' ) . '</a>';
	} else {
		$state   = 'addon';
		$badge   = esc_html__( 'Free add-on', 'eazydocs' );
		$heading = esc_html__( 'Turn your Docs Assistant into an AI chatbot', 'eazydocs' );
		$body    = esc_html__( 'Install the free Antimanual plugin to train an AI chatbot on your documentation. Visitors get instant, conversational answers from your docs — reducing repetitive support tickets.', 'eazydocs' );
		$primary = '<a href="' . esc_url( $install_url ) . '" class="button button-primary">' . esc_html__( 'Install Antimanual', 'eazydocs' ) . '</a>';
	}

	$secondary  = '<a href="' . esc_url( $wporg_url ) . '" target="_blank" rel="noopener noreferrer" class="button button-secondary">' . esc_html__( 'View on WordPress.org', 'eazydocs' ) . '</a>';
	$secondary .= ' <a href="' . esc_url( $learn_more ) . '" target="_blank" rel="noopener noreferrer" class="button button-secondary">' . esc_html__( 'Learn More', 'eazydocs' ) . '</a>';

	$html  = ezd_assistant_antimanual_styles();
	$html .= '<div class="ezd-assistant-antimanual ezd-assistant-antimanual--' . esc_attr( $state ) . '">';
	$html .= '<div class="ezd-assistant-antimanual__head">';
	$html .= '<span class="dashicons dashicons-superhero-alt ezd-assistant-antimanual__icon"></span>';
	$html .= '<strong class="ezd-assistant-antimanual__title">' . $heading . '</strong>';
	$html .= '<span class="ezd-assistant-antimanual__badge">' . $badge . '</span>';
	$html .= '</div>';
	$html .= '<p class="ezd-assistant-antimanual__body">' . $body . '</p>';
	$html .= '<div class="ezd-assistant-antimanual__actions">' . $primary . $secondary . '</div>';
	$html .= '</div>';

	return $html;
}

/**
 * Render a "Preview Assistant" link for the settings panel.
 *
 * The plugin already exposes a standalone render of the widget at
 * /iframe-assistant/. Opening it in a new tab gives admins an isolated live
 * preview of their current settings without hunting for it on the front end.
 * Saved settings are reflected on the next load, so the hint notes that.
 *
 * @return string Escaped HTML for a CSF `content` field.
 */
function ezd_assistant_preview_link() {
	$preview_url = esc_url( site_url( '/iframe-assistant/' ) );

	$html  = '<p style="margin:0 0 4px;">';
	$html .= '<a href="' . $preview_url . '" target="_blank" rel="noopener noreferrer" class="button button-secondary">';
	$html .= '<span class="dashicons dashicons-external" style="vertical-align:text-bottom;"></span> ';
	$html .= esc_html__( 'Preview Assistant', 'eazydocs' );
	$html .= '</a>';
	$html .= '</p>';
	$html .= '<p class="description" style="margin:0;">' . esc_html__( 'Opens a live preview in a new tab. Save your changes first to see them reflected.', 'eazydocs' ) . '</p>';

	return $html;
}

/**
 * One-time scoped stylesheet for the Antimanual notice.
 *
 * Kept inline (rather than enqueued) so the notice stays fully self-contained
 * within the CSF `content` field. A static guard ensures the <style> block is
 * emitted only once even if the notice renders more than once on a page.
 *
 * @return string Style markup, or empty string on subsequent calls.
 */
function ezd_assistant_antimanual_styles() {
	static $printed = false;
	if ( $printed ) {
		return '';
	}
	$printed = true;

	return '<style>
		.ezd-assistant-antimanual{border:1px solid #d8d0fb;border-left:4px solid #5E3AEE;border-radius:8px;background:#faf9fe;padding:16px 18px;margin-bottom:18px;}
		.ezd-assistant-antimanual__head{display:flex;align-items:center;gap:10px;margin-bottom:6px;flex-wrap:wrap;}
		.ezd-assistant-antimanual__icon{color:#5E3AEE;font-size:22px;width:22px;height:22px;}
		.ezd-assistant-antimanual__title{font-size:15px;color:#1e1e1e;}
		.ezd-assistant-antimanual__badge{font-size:11px;font-weight:600;text-transform:uppercase;letter-spacing:.4px;color:#5E3AEE;background:#ece7fd;border-radius:10px;padding:2px 10px;}
		.ezd-assistant-antimanual__body{margin:0 0 12px;font-size:13px;line-height:1.6;color:#50575e;}
		.ezd-assistant-antimanual__actions{display:flex;gap:8px;flex-wrap:wrap;}
		.ezd-assistant-antimanual--connected{border-left-color:#1a7f37;}
		.ezd-assistant-antimanual--connected .ezd-assistant-antimanual__badge{color:#1a7f37;background:#dff4e4;}
		.ezd-assistant-antimanual--action{border-left-color:#bf8700;}
		.ezd-assistant-antimanual--action .ezd-assistant-antimanual__badge{color:#8a6500;background:#fcf3da;}
	</style>';
}

// Function to generate dynamic embed code box
function ezd_generate_embed_code_box() {
    $site_url 		= site_url(); // Current site URL
    // Get assistant icon options using get_option like in Assistant.php
    $open_icon 		= ezd_get_opt( 'assistant_open_icon', [] );
    $close_icon 	= ezd_get_opt( 'assistant_close_icon', [] );
    $open_icon_url 	= isset($open_icon['url']) && $open_icon['url'] ? $open_icon['url'] : "{$site_url}/wp-content/plugins/eazydocs-pro/assets/images/frontend/chat.svg";
    $close_icon_url = isset($close_icon['url']) && $close_icon['url'] ? $close_icon['url'] : "{$site_url}/wp-content/plugins/eazydocs-pro/assets/images/frontend/close.svg";

    // Get spacing options from assistant_tab_settings
    $tab_settings 		= ezd_get_opt( 'assistant_tab_settings', [] );
    $spacing_vertical 	= $tab_settings['assistant_spacing_vertical'] ?? '';
    $spacing_horizontal = $tab_settings['assistant_spacing_horizontal'] ?? '';
    $vertical_unit 		= (is_numeric($spacing_vertical) && $spacing_vertical !== '') ? $spacing_vertical . '%' : '';
    $horizontal_unit 	= (is_numeric($spacing_horizontal) && $spacing_horizontal !== '') ? $spacing_horizontal . '%' : '';
    $iframe_bottom 		= (is_numeric($spacing_vertical) && $spacing_vertical !== '') ? 'calc(' . $spacing_vertical . '% + 76px)' : '';

    $chat_toggle_style = '';
    $chat_toggle_style_arr = [];
    if ($vertical_unit !== '') {
        $chat_toggle_style_arr[] = 'bottom:' . $vertical_unit . ';';
    }
    if ($horizontal_unit !== '') {
        $chat_toggle_style_arr[] = 'right:' . $horizontal_unit . ';';
    }
    if (!empty($chat_toggle_style_arr)) {
        $chat_toggle_style = implode(' ', $chat_toggle_style_arr);
    }

    $iframe_wrap_style = '';
    $iframe_wrap_style_arr = [];
    if ($iframe_bottom !== '') {
        $iframe_wrap_style_arr[] = 'bottom:' . $iframe_bottom . ';';
    }
    if ($horizontal_unit !== '') {
        $iframe_wrap_style_arr[] = 'right:' . $horizontal_unit . ';';
    }
    if (!empty($iframe_wrap_style_arr)) {
        $iframe_wrap_style = implode(' ', $iframe_wrap_style_arr);
    }

    $code  = '<div class="eazydocs-cross-domain-code">' . "\n";

	$code .= '   <!-- Embed Assistant Styles -->' . "\n";
	// phpcs:ignore WordPress.WP.EnqueuedResources.NonEnqueuedStylesheet
	$code .= '   <link rel="stylesheet" href="' . esc_url( $site_url ) . '/wp-content/plugins/eazydocs-pro/build/styles/embed-assistant.css" media="all">' . "\n";
	// phpcs:ignore WordPress.WP.EnqueuedResources.NonEnqueuedScript
	$code .= '   <script src="' . esc_url( $site_url ) . '/wp-content/plugins/eazydocs-pro/assets/js/embed-assistant.js"></script>' . "\n";

	$code .= '   <div class="chat-toggle" ' . ( $chat_toggle_style ? 'style="' . esc_attr( $chat_toggle_style ) . '"' : '' ) . '>' . "\n";
	$code .= '      <img class="wp-spotlight-chat" src="' . esc_url( $open_icon_url ) . '" alt="' . esc_attr__( 'Chat Icon', 'eazydocs' ) . '">' . "\n";
	$code .= '      <img class="wp-spotlight-hide" src="' . esc_url( $close_icon_url ) . '" alt="' . esc_attr__( 'Close Icon', 'eazydocs' ) . '" style="display: none;">' . "\n";
	$code .= '   </div>' . "\n";
	$code .= '   <button class="close-chat-sm"><span>' . esc_html__( 'Hide', 'eazydocs' ) . '</span><span class="icon">❮</span></button>' . "\n";
	$code .= '   <div class="chatbox-iframe-wraper" ' . ( $iframe_wrap_style ? 'style="' . esc_attr( $iframe_wrap_style ) . '"' : '' ) . '>' . "\n";
	$code .= '      <iframe src="' . esc_url( $site_url ) . '/iframe-assistant/" style="border: none;" frameborder="0"></iframe>' . "\n";
	$code .= '   </div>' . "\n";
	$code .= '</div>';

    // The Site / Product Label + guidance only makes sense when EazyDocs is
    // merged with the Antimanual chatbot, because Antimanual is what honors
    // the ?ctx= query parameter end-to-end (reads it on the iframe page,
    // forwards it to the LLM as scope, and locks it onto the conversation).
    // Plain EazyDocs assistant ignores the param, so showing the field there
    // would mislead the admin into thinking it does something.
    $merge_active = function_exists( 'atml_option' )
        ? (bool) atml_option( 'chatbot_merge_ezd' )
        : false;

    $guidelines  = '';
    $label_field = '';
    $script      = '';

    if ( $merge_active ) {
        $guidelines  = "<div class='ezd-embed-guidelines' style='background:#eef2ff;border:1px solid #c7d2fe;border-left:3px solid #6366f1;border-radius:6px;padding:12px 14px;margin-bottom:14px;font-size:13px;line-height:1.55;color:#1e293b;'>";
        $guidelines .= "<strong style='display:block;margin-bottom:6px;color:#3730a3;'>" . esc_html__( 'How to embed the assistant on another site', 'eazydocs' ) . "</strong>";
        $guidelines .= "<ol style='margin:0;padding-left:18px;'>";
        $guidelines .= "<li>" . esc_html__( '(Optional) Enter a Site / Product Label below — the name of the site or product where you are pasting this embed. This tells the assistant which product the visitor is asking about so it does not confuse multiple products in one knowledge base.', 'eazydocs' ) . "</li>";
        $guidelines .= "<li>" . esc_html__( 'Copy the generated Embed Code from the box below.', 'eazydocs' ) . "</li>";
        $guidelines .= "<li>" . esc_html__( 'Paste it into the HTML of the target site — anywhere inside the <body>. The assistant loads as a floating widget.', 'eazydocs' ) . "</li>";
        $guidelines .= "</ol>";
        $guidelines .= "<div style='margin-top:8px;color:#475569;font-size:12px;'><strong>" . esc_html__( 'When to fill the label:', 'eazydocs' ) . "</strong> " . esc_html__( 'only when one knowledge base serves multiple products or brands. If you have one product on one site, leave it empty.', 'eazydocs' ) . "</div>";
        $guidelines .= "<div style='margin-top:4px;color:#475569;font-size:12px;'><strong>" . esc_html__( 'Example:', 'eazydocs' ) . "</strong> " . esc_html__( 'on eazydocs.com use label "Eazydocs"; on antimanual.com use "Antimanual". Both iframes load the same KB but answer in their own product context.', 'eazydocs' ) . "</div>";
        $guidelines .= "</div>";

        $label_field  = "<div class='ezd-embed-label-field' style='margin-bottom:12px;'>";
        $label_field .= "<label for='ezd-embed-context-input' style='display:block;font-weight:600;font-size:13px;margin-bottom:4px;color:#1e293b;'>" . esc_html__( 'Site / Product Label (optional)', 'eazydocs' ) . "</label>";
        $label_field .= "<input type='text' id='ezd-embed-context-input' maxlength='200' placeholder='" . esc_attr__( 'e.g., Eazydocs, Acme CRM, Plan Pro', 'eazydocs' ) . "' style='width:100%;padding:8px 10px;border:1px solid #cbd5e1;border-radius:4px;font-size:13px;' />";
        $label_field .= "<div style='font-size:12px;color:#64748b;margin-top:4px;'>" . esc_html__( 'Max 200 characters. The label is passed as scope only — it never appears in the user message.', 'eazydocs' ) . "</div>";
        $label_field .= "</div>";

        // Inline script: when the label input changes, rewrite the iframe src
        // in the snippet to append/replace ?ctx=<encoded label>. Pure
        // client-side, no server round-trip — the embed snippet is just
        // text the admin copies.
        $script = "
        <script>
        (function(){
            var input = document.getElementById('ezd-embed-context-input');
            var ta = document.querySelector('.assistant-embed-code-box textarea');
            if (!input || !ta) return;
            var original = ta.value;
            function rebuild() {
                var v = (input.value || '').trim().slice(0, 200);
                var src = original;
                if (v) {
                    var enc = encodeURIComponent(v);
                    src = original.replace(/iframe-assistant\\/(\\?[^\"\\s]*)?/, function(_match, qs) {
                        if (!qs) return 'iframe-assistant/?ctx=' + enc;
                        if (/[?&]ctx=/.test(qs)) {
                            return 'iframe-assistant/' + qs.replace(/([?&])ctx=[^&\"]*/, '$1ctx=' + enc);
                        }
                        return 'iframe-assistant/' + qs + '&ctx=' + enc;
                    });
                }
                ta.value = src;
            }
            input.addEventListener('input', rebuild);
        })();
        </script>";
    }

    return "
    {$guidelines}
    {$label_field}
    <div class='assistant-embed-code-box' style='position:relative;margin-bottom:15px;'>
        <textarea readonly >{$code}</textarea>
        <button class='button admin-copy-embed-code' >Copy</button>
    </div>
    {$script}";
}


//
// Instant Answer
//
CSF::createSection( $prefix, array(
	'id'     => 'eazydocs_instant_answer',
	'title'  => esc_html__( 'Docs Assistant', 'eazydocs' ),
	'icon'   => 'dashicons dashicons-format-chat',
	'fields' => [
		array(
			'type'    => 'content',
			'content' => ezd_assistant_antimanual_info(),
		),

		array(
			'type'  => 'heading',
			'title' => esc_html__( 'Assistant Settings', 'eazydocs' ),
		),

		array(
			'id'         => 'assistant_visibility',
			'type'       => 'switcher',
			'title'      => esc_html__( 'Enable Assistant', 'eazydocs' ),
			'text_on'    => esc_html__( 'Enabled', 'eazydocs' ),
			'text_off'   => esc_html__( 'Disabled', 'eazydocs' ),
			'class'      => 'eazydocs-pro-notice',
			'text_width' => 92,
			'default'    => false
		),

		array(
			'type'       => 'content',
			'content'    => ezd_assistant_preview_link(),
			'dependency' => array(
				array( 'assistant_visibility', '==', 'true' )
			),
		),

		array(
			'id'         => 'assistant_visibility_by',
			'type'       => 'button_set',
			'title'      => esc_html__( 'Display Location', 'eazydocs' ),
			'subtitle' 	 => esc_html__( 'Set your assistant where should be appears.', 'eazydocs' ),
			'class'      => 'eazydocs-pro-notice',
			'options'    => array(
			  'global' 		=>  esc_html__( 'Everywhere', 'eazydocs' ),
			  'pages'  		=>  esc_html__( 'Pages', 'eazydocs' ),
			  'post_type' 	=>  esc_html__( 'Post Type', 'eazydocs' ),
			),
			'default'    => 'global',
			'dependency' => array(
				array( 'assistant_visibility', '==', 'true' )
			),
		),

		array(
			'id'         => 'assistant_pages',
			'type'       => 'select',
			'title'      => esc_html__( 'Select Pages', 'eazydocs' ),
			'subtitle' 	 => esc_html__( 'Select pages where should be appears.', 'eazydocs' ),
			'options'    => 'pages',
			'class'      => 'eazydocs-pro-notice',
			'chosen'     => true,
			'multiple'   => true,
			'dependency' => array(
				array( 'assistant_visibility_by', '==', 'pages' ),
				array( 'assistant_visibility', '==', 'true' )
			)
		),

		array(
			'id'         => 'assistant_post_types',
			'type'       => 'select',
			'title'      => esc_html__( 'Select Post Types', 'eazydocs' ),
			'subtitle' 	 => esc_html__( 'Pick your preferred post types where should be appears.', 'eazydocs' ),
			'options'    => 'post_types',
			'class'      => 'eazydocs-pro-notice',
			'chosen'     => true,
			'multiple'   => true,
			'dependency' => array(
				array( 'assistant_visibility_by', '==', 'post_type' ),
				array( 'assistant_visibility', '==', 'true' )
			)
		),

		array(
			'id'             => 'assistant_open_icon',
			'type'           => 'media',
			'title'          => esc_html__( 'Open Icon', 'eazydocs' ),
			'library'        => 'image',
			'url'            => false,
			'preview_width'  => '60',
			'preview_height' => '60',
			'class'          => 'eazydocs-pro-notice',
			'dependency'     => array(
				array( 'assistant_visibility', '==', 'true' )
			)
		),

		array(
			'id'             => 'assistant_close_icon',
			'type'           => 'media',
			'title'          => esc_html__( 'Close Icon', 'eazydocs' ),
			'library'        => 'image',
			'class'          => 'eazydocs-pro-notice',
			'url'            => false,
			'preview_width'  => '60',
			'preview_height' => '60',
			'dependency'     => array(
				array( 'assistant_visibility', '==', 'true' )
			)
		),

		array(
			'id'         => 'assistant_tab_settings',
			'type'       => 'tabbed',
			'class'      => 'eazydocs-pro-notice',
			'title'      => esc_html__( 'Tab Settings', 'eazydocs' ),
			'dependency' => array(
				array( 'assistant_visibility', '==', 'true' )
			),
			'tabs'       => array(
				array(
					'title'  => esc_html__( 'Knowledge Base', 'eazydocs' ),
					'fields' => array(
						ezd_csf_switcher_field([
							'id'         => 'kb_visibility',
							'title'      => esc_html__( 'Knowledge-base Tab', 'eazydocs' ),
							'text_width' => 70,
							'default'    => true
						]),

						array(
							'id'         => 'kb_label',
							'type'       => 'text',
							'title'      => esc_html__( 'Heading', 'eazydocs' ),
							'default'    => esc_html__( 'Knowledge Base', 'eazydocs' ),
							'dependency' => array(
								array( 'kb_visibility', '==', 'true' ),
							)
						),

						array(
							'id'         => 'docs_instant_answer',
							'type'       => 'switcher',
							'title'      => esc_html__( 'Instant Answer', 'eazydocs' ),
							'subtitle'   => esc_html__( 'Open docs inside the assistant panel instead of navigating away from the page.', 'eazydocs' ),
							'text_on'    => esc_html__( 'Enabled', 'eazydocs' ),
							'text_off'   => esc_html__( 'Disabled', 'eazydocs' ),
							'text_width' => 83,
							'default'    => true,
							'dependency' => array(
								array( 'kb_visibility', '==', 'true' )
							)
						),

						ezd_csf_switcher_field([
							'id'         => 'assistant_search',
							'title'      => esc_html__( 'Search', 'eazydocs' ),
							'text_width' => 70,
							'default'    => true,
							'dependency' => array(
								array( 'kb_visibility', '==', 'true' )
							)
						]),

						array(
							'id'         => 'kb_search_placeholder',
							'type'       => 'text',
							'title'      => esc_html__( 'Search Placeholder', 'eazydocs' ),
							'default'    => esc_html__( 'Search...', 'eazydocs' ),
							'dependency' => array(
								array( 'assistant_search', '==', 'true' ),
								array( 'kb_visibility', '==', 'true' )
							)
						),

						ezd_csf_switcher_field([
							'id'         => 'assistant_breadcrumb',
							'title'      => esc_html__( 'Breadcrumb', 'eazydocs' ),
							'text_width' => 70,
							'default'    => true,
							'dependency' => array(
								array( 'kb_visibility', '==', 'true' )
							)
						]),

						array(
							'id'         => 'docs_not_found',
							'type'       => 'text',
							'title'      => esc_html__( 'Docs not Found', 'eazydocs' ),
							'default'    => esc_html__( 'Docs not Found', 'eazydocs' ),
							'dependency' => array(
								array( 'kb_visibility', '==', 'true' )
							)
						),

						array(
							'id'         => 'assistant_docs_show',
							'type'       => 'number',
							'title'      => esc_html__( 'Number of Docs', 'eazydocs' ),
							'desc'       => esc_html__( 'How many docs to list in the Knowledge Base tab. Defaults to 20 if left empty. Maximum 100.', 'eazydocs' ),
							'default'    => 20,
							'dependency' => array(
								array( 'kb_visibility', '==', 'true' ),
							),
							'attributes' => array(
								'min'  => 1,
								'max'  => 100,
								'step' => 1
							)
						)
					)
				),

				array(
					'title'  => esc_html__( 'Contact', 'eazydocs' ),
					'fields' => array(
						ezd_csf_switcher_field([
							'id'         => 'contact_visibility',
							'title'      => esc_html__( 'Contact Tab', 'eazydocs' ),
							'text_width' => 70,
							'default'    => true
						]),

						array(
							'id'         => 'contact_label',
							'type'       => 'text',
							'title'      => esc_html__( 'Heading', 'eazydocs' ),
							'default'    => esc_html__( 'Contact', 'eazydocs' ),
							'dependency' => array(
								array( 'contact_visibility', '==', 'true' ),
							)
						),

						array(
							'id'         => 'assistant_contact_mail',
							'type'       => 'text',
							'title'      => esc_html__( 'Receiver Email', 'eazydocs' ),
							'default'    => get_option( 'admin_email' ),
							'validate'   => 'csf_validate_email',
							'dependency' => array(
								array( 'contact_visibility', '==', 'true' ),
							)
						),

						array(
							'type'       => 'subheading',
							'title'      => esc_html__( 'Form Input', 'eazydocs' ),
							'dependency' => array(
								array( 'contact_visibility', '==', 'true' ),
							)
						),

						array(
							'id'         => 'contact_fullname',
							'type'       => 'text',
							'title'      => esc_html__( 'Full name', 'eazydocs' ),
							'default'    => esc_html__( 'Full name', 'eazydocs' ),
							'dependency' => array(
								array( 'contact_visibility', '==', 'true' ),
							)
						),

						array(
							'id'         => 'contact_mail',
							'type'       => 'text',
							'title'      => esc_html__( 'Email', 'eazydocs' ),
							'default'    => esc_html__( 'name@example.com', 'eazydocs' ),
							'dependency' => array(
								array( 'contact_visibility', '==', 'true' ),
							)
						),

						array(
							'id'         => 'contact_subject',
							'type'       => 'text',
							'title'      => esc_html__( 'Subject', 'eazydocs' ),
							'default'    => esc_html__( 'Subject', 'eazydocs' ),
							'dependency' => array(
								array( 'contact_visibility', '==', 'true' ),
							)
						),

						array(
							'id'         => 'contact_message',
							'type'       => 'text',
							'title'      => esc_html__( 'Message', 'eazydocs' ),
							'default'    => esc_html__( 'Write Your Message', 'eazydocs' ),
							'dependency' => array(
								array( 'contact_visibility', '==', 'true' ),
							)
						),

						array(
							'id'         => 'contact_submit',
							'type'       => 'text',
							'title'      => esc_html__( 'Submit Button', 'eazydocs' ),
							'default'    => esc_html__( 'Send Message', 'eazydocs' ),
							'dependency' => array(
								array( 'contact_visibility', '==', 'true' ),
							)
						),
					)
				),

				array(
					'title'  => esc_html__( 'Color', 'eazydocs' ),
					'fields' => array(
						array(
							'id'          => 'assistant_bg',
							'type'        => 'color',
							'title'       => esc_html__( 'Icon Color', 'eazydocs' ),
							'output'      => '.chat-toggle a',
							'output_mode' => 'background-color',
						),

						array(
							'id'          => 'icon_bg_hover',
							'type'        => 'color',
							'title'       => esc_html__( 'Icon Hover Color', 'eazydocs' ),
							'output'      => '.chat-toggle a:hover',
							'output_mode' => 'background-color',
						),

						array(
							'id'          => 'assistant_header_bg',
							'type'        => 'color',
							'title'       => esc_html__( 'Header Background', 'eazydocs' ),
							'output'      => '.chatbox-header',
							'output_mode' => 'background-color',
						),

						array(
							'id'          => 'assistant_body_bg',
							'type'        => 'color',
							'title'       => esc_html__( 'Background', 'eazydocs' ),
							'output'      => '.chatbox-body,.kb-content-wrap.opened',
							'output_mode' => 'background-color',
						),

						array(
							'id'          => 'assistant_card_bg',
							'type'        => 'color',
							'title'       => esc_html__( 'Card Background', 'eazydocs' ),
							'subtitle'    => esc_html__( 'Background of each doc item in the Knowledge Base list.', 'eazydocs' ),
							'output'      => '.chatbox-posts .post-item',
							'output_mode' => 'background-color',
						),

						array(
							'id'          => 'assistant_title_color',
							'type'        => 'color',
							'title'       => esc_html__( 'Doc Title Color', 'eazydocs' ),
							'output'      => '.chatbox-posts .post-item h2 a,.kb-content-wrap.opened h1.ezd-kbase-extend-heading',
							'output_mode' => 'color',
						),

						array(
							'id'          => 'assistant_text_color',
							'type'        => 'color',
							'title'       => esc_html__( 'Doc Text Color', 'eazydocs' ),
							'output'      => '.chatbox-posts .post-item p,.kb-content-wrap.opened p',
							'output_mode' => 'color',
						),

						array(
							'id'          => 'assistant_submit_bg',
							'type'        => 'color',
							'title'       => esc_html__( 'Submit Button', 'eazydocs' ),
							'output'      => '.chatbox-form input[type="submit"]',
							'output_mode' => 'background-color'
						),
					)
				),

				array(
					'title'  => esc_html__( 'Position', 'eazydocs' ),
					'fields' => array(
						array(
							'id'    => 'assistant_position_heading',
							'type'  => 'heading',
							'title' => esc_html__( 'Position', 'eazydocs' ),
						),

						array(
							'id'          => 'assistant_spacing_vertical',
							'type'        => 'slider',
							'title'       => esc_html__( 'Vertical Position', 'eazydocs' ),
							'min'         => 0,
							'max'         => 54,
							'step'        => 1,
							'unit'        => '%',
							'output'      => '.chat-toggle,.chatbox-wrapper',
							'output_mode' => 'margin-bottom'
						),

						array(
							'id'          => 'assistant_spacing_horizontal',
							'type'        => 'slider',
							'title'       => esc_html__( 'Horizontal Position', 'eazydocs' ),
							'min'         => 0,
							'max'         => 94,
							'step'        => 1,
							'unit'        => '%',
							'output'      => '.chat-toggle,.chatbox-wrapper',
							'output_mode' => 'margin-right'
						),

						array(
							'id'    => 'assistant_appearance_heading',
							'type'  => 'subheading',
							'title' => esc_html__( 'Appearance', 'eazydocs' ),
						),

						array(
							'id'          => 'assistant_panel_width',
							'type'        => 'slider',
							'title'       => esc_html__( 'Panel Width', 'eazydocs' ),
							'subtitle'    => esc_html__( 'Width of the assistant panel on desktop. It still shrinks to fit small screens.', 'eazydocs' ),
							'min'         => 320,
							'max'         => 520,
							'step'        => 5,
							'unit'        => 'px',
							'default'     => 410,
							'output'      => '.chatbox-wrapper:not(.extend)',
							'output_mode' => 'width'
						),

						array(
							'id'          => 'assistant_border_radius',
							'type'        => 'slider',
							'title'       => esc_html__( 'Corner Radius', 'eazydocs' ),
							'min'         => 0,
							'max'         => 28,
							'step'        => 1,
							'unit'        => 'px',
							'default'     => 12,
							'output'      => '.chatbox-wrapper',
							'output_mode' => 'border-radius'
						)
					)
				),
			)
		),
		array(
			'type'    => 'content',
			'title'   => esc_html__( 'Cross Domain Embed', 'eazydocs' ),
			'subtitle' => esc_html__( 'Extend your docs assistant to other websites by embedding it across multiple domains with a simple code snippet.', 'eazydocs' ),
			'content' => ezd_generate_embed_code_box(),
			'dependency' => array(
				array( 'assistant_visibility', '==', 'true' )
			),
		),
	]
) );
