<?php

//
// Instant Answer
//
CSF::createSection( $prefix, array(
	'id'     => 'eazydocs_instant_answer',
	'title'  => esc_html__( 'Docs Assistant', 'eazydocs' ),
	'icon'   => 'dashicons dashicons-format-chat',
	'fields' => [
		array(
			'type'  => 'heading',
			'title' => esc_html__( 'Assistant Settings', 'eazydocs' ),
		),

		array(
			'id'         => 'assistant_visibility',
			'type'       => 'switcher',
			'title'      => esc_html__( 'Docs Assistant', 'eazydocs' ),
			'text_on'    => esc_html__( 'Enabled', 'eazydocs' ),
			'text_off'   => esc_html__( 'Disabled', 'eazydocs' ),
			'class'      => 'eazydocs-pro-notice',
			'text_width' => 92,
			'default'    => false
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
				array( 'assistant_visibility', '==', '1' )
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
						array(
							'id'         => 'kb_visibility',
							'type'       => 'switcher',
							'title'      => esc_html__( 'Knowledge-base Tab', 'eazydocs' ),
							'text_on'    => esc_html__( 'Show', 'eazydocs' ),
							'text_off'   => esc_html__( 'Hide', 'eazydocs' ),
							'text_width' => 70,
							'default'    => true
						),

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
							'id'         => 'assistant_search',
							'type'       => 'switcher',
							'title'      => esc_html__( 'Search', 'eazydocs' ),
							'text_on'    => esc_html__( 'Show', 'eazydocs' ),
							'text_off'   => esc_html__( 'Hide', 'eazydocs' ),
							'text_width' => 70,
							'default'    => true,
							'dependency' => array(
								array( 'kb_visibility', '==', 'true' )
							)
						),

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

						array(
							'id'         => 'assistant_breadcrumb',
							'type'       => 'switcher',
							'title'      => esc_html__( 'Breadcrumb', 'eazydocs' ),
							'text_on'    => esc_html__( 'Show', 'eazydocs' ),
							'text_off'   => esc_html__( 'Hide', 'eazydocs' ),
							'text_width' => 70,
							'default'    => true,
							'dependency' => array(
								array( 'kb_visibility', '==', 'true' )
							)
						),

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
							'id'         => 'docs_instant_answer',
							'type'       => 'switcher',
							'title'      => esc_html__( 'Instant Answer', 'eazydocs' ),
							'text_on'    => esc_html__( 'Eanble', 'eazydocs' ),
							'text_off'   => esc_html__( 'Disable', 'eazydocs' ),
							'text_width' => 83,
							'default'    => false,
							'dependency' => array(
								array( 'kb_visibility', '==', 'true' )
							)
						),

						array(
							'id'         => 'assistant_docs_show',
							'type'       => 'number',
							'title'      => esc_html__( 'Number of Docs', 'eazydocs' ),
							'desc'       => esc_html__( 'Leave this field empty to display all available docs.', 'eazydocs' ),
							'default'    => 12,
							'dependency' => array(
								array( 'kb_visibility', '==', 'true' ),
							),
							'attributes' => array(
								'min'  => 1,
								'step' => 1
							)
						)
					)
				),

				array(
					'title'  => esc_html__( 'Contact', 'eazydocs' ),
					'fields' => array(
						array(
							'id'         => 'contact_visibility',
							'type'       => 'switcher',
							'title'      => esc_html__( 'Contact Tab', 'eazydocs' ),
							'text_on'    => esc_html__( 'Show', 'eazydocs' ),
							'text_off'   => esc_html__( 'Hide', 'eazydocs' ),
							'text_width' => 70,
							'default'    => true
						),

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
							'output'      => '.chatbox-body',
							'output_mode' => 'background-color',
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
						)
					)
				),
			)
		),
		array(
			'type'    => 'content',
			'title'   => esc_html__( 'Cross Domain Embed', 'eazydocs' ),
			'subtitle' => esc_html__( 'Extend your docs assistant to other websites by embedding it across multiple domains with a simple code snippet.', 'eazydocs' ),
			'content' => generate_embed_code_box(),
			'dependency' => array(
				array( 'assistant_visibility', '==', 'true' )
			),
		),
	]
) );

// Function to generate dynamic embed code box
function generate_embed_code_box() {
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
        $chat_toggle_style = 'style="' . implode(' ', $chat_toggle_style_arr) . '"';
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
        $iframe_wrap_style = 'style="' . implode(' ', $iframe_wrap_style_arr) . '"';
    }

    $code  = '<div class="eazydocs-cross-domain-code">' . "\n";

	$code .= '   <!-- Embed Assistant Styles -->' . "\n";
	// phpcs:ignore WordPress.WP.EnqueuedResources.NonEnqueuedStylesheet
	$code .= '   <link rel="stylesheet" href="' . esc_url( $site_url ) . '/wp-content/plugins/eazydocs-pro/assets/css/embed-assistant.css" media="all">' . "\n";
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

    return "
    <div class='assistant-embed-code-box' style='position:relative;margin-bottom:15px;'>
        <textarea readonly >{$code}</textarea>
        <button class='button admin-copy-embed-code' >Copy</button>
    </div>";
}