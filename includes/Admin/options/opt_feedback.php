<?php
/**
 * Feedback Settings
 * Configure feedback features and options for documentation pages.
 */
if (!defined('ABSPATH')) {
	exit;
}

// Feedback Main Section
CSF::createSection($prefix, array(
	'id' => 'feedback',
	'title' => esc_html__('Feedback', 'eazydocs'),
	'icon' => 'dashicons dashicons-feedback',
));

//
// Feedback > General Feedback Options
//
CSF::createSection($prefix, array(
	'parent' => 'feedback',
	'title' => esc_html__('Feedback Options', 'eazydocs'),
	'icon' => '',
	'fields' => array(
		ezd_csf_switcher_field([
			'id' => 'docs-feedback',
			'title' => esc_html__('Feedback Area', 'eazydocs'),
			'text_width' => 70,
			'default' => true,
		]),

		array(
			'type' => 'heading',
			'content' => esc_html__('Feedback Area Options', 'eazydocs'),
			'dependency' => array(
				'docs-feedback',
				'==',
				'true'
			),
		),

		ezd_csf_switcher_field([
			'id' => 'message-feedback',
			'title' => esc_html__('Message Feedback', 'eazydocs'),
			'default' => true,
			'text_width' => 70,
			'dependency' => array(
				'docs-feedback',
				'==',
				'true',
			)
		]),

		array(
			'id' => 'still-stuck',
			'type' => 'text',
			'title' => esc_html__('Still Stuck', 'eazydocs'),
			'default' => esc_html__('Still stuck?', 'eazydocs'),
			'dependency' => array(
				array('docs-feedback', '==', 'true'),
				array('message-feedback', '==', 'true'),
			)
		),

		array(
			'id' => 'feedback-link-text',
			'type' => 'text',
			'title' => esc_html__('Help form link text', 'eazydocs'),
			'default' => esc_html__('How can we help?', 'eazydocs'),
			'dependency' => array(
				array('docs-feedback', '==', 'true'),
				array('message-feedback', '==', 'true'),
			)
		),

		array(
			'id' => 'feedback-admin-email',
			'type' => 'text',
			'title' => esc_html__('Email Address', 'eazydocs'),
			'default' => get_option('admin_email'),
			'dependency' => array(
				array('docs-feedback', '==', 'true'),
				array('message-feedback', '==', 'true'),
			)
		),

		array(
			'type' => 'subheading',
			'content' => esc_html__('Feedback Modal', 'eazydocs'),
			'dependency' => array(
				array('docs-feedback', '==', 'true'),
				array('message-feedback', '==', 'true'),
			)
		),

		array(
			'id' => 'feedback-form-title',
			'type' => 'text',
			'title' => esc_html__('Form Title', 'eazydocs'),
			'default' => esc_html__('How can we help?', 'eazydocs'),
			'dependency' => array(
				array('docs-feedback', '==', 'true'),
				array('message-feedback', '==', 'true'),
			)
		),

		array(
			'id' => 'feedback-form-desc',
			'type' => 'textarea',
			'title' => esc_html__('Form Subtitle', 'eazydocs'),
			'dependency' => array(
				array('docs-feedback', '==', 'true'),
				array('message-feedback', '==', 'true'),
			)
		),

		array(
			'type' => 'heading',
			'title' => esc_html__('Voting Feedback', 'eazydocs'),
			'dependency' => array(
				array('docs-feedback', '==', 'true'),
			)
		),

		array(
			'id' => 'helpful_feedback',
			'type' => 'switcher',
			'title' => esc_html__('Helpful feedback', 'eazydocs'),
			'default' => true,
			'dependency' => array(
				'docs-feedback',
				'==',
				'true',
			)
		),

		array(
			'id' => 'feedback-label',
			'type' => 'text',
			'title' => esc_html__('Feedback Label', 'eazydocs'),
			'default' => esc_html__('Was this page helpful?', 'eazydocs'),
			'dependency' => array(
				array('docs-feedback', '==', 'true'),
				array('helpful_feedback', '==', 'true'),
			)
		),
		array(
			'id' => 'feedback_count',
			'type' => 'switcher',
			'title' => esc_html__('Feedback Count', 'eazydocs'),
			'default' => true,
			'dependency' => array(
				array('docs-feedback', '==', 'true'),
				array('helpful_feedback', '==', 'true'),
			)
		),
	)
));

//
// Feedback > Feedback on Selected Text
//
CSF::createSection($prefix, array(
	'parent' => 'feedback',
	'title' => esc_html__('Feedback on Text', 'eazydocs'),
	'icon' => '',
	'fields' => array(
		array(
			'type' => 'heading',
			'content' => esc_html__('Feedback on Selected Text', 'eazydocs'),
		),

		array(
			'id' => 'enable-selected-comment',
			'type' => 'switcher',
			'title' => esc_html__('Feedback on Selected Text', 'eazydocs'),
			'subtitle' => esc_html__('Enable the feature to allow users to comment on selected text.', 'eazydocs'),
			'desc' => esc_html__('Note: if enabled, a switcher will appear in the doc meta area to allow visitors to turn On/Off the feature.', 'eazydocs'),
			'text_on' => esc_html__('Enabled', 'eazydocs'),
			'text_off' => esc_html__('Disabled', 'eazydocs'),
			'class' => 'eazydocs-promax-notice',
			'text_width' => 95,
			'default' => false
		),

		array(
			'id' => 'selected-comment-meta-title',
			'type' => 'text',
			'title' => esc_html__('Frontend Switcher Level', 'eazydocs'),
			'subtitle' => esc_html__('This title will be shown on the frontend to On/Off the feature.', 'eazydocs'),
			'default' => esc_html__('Feedback', 'eazydocs'),
			'class' => 'eazydocs-promax-notice',
			'dependency' => array('enable-selected-comment', '==', 'true'),
		),

		array(
			'id' => 'selected-comment-roles',
			'type' => 'select',
			'title' => esc_html__('Who can view comments?', 'eazydocs'),
			'options' => 'roles',
			'default' => 'administrator',
			'class' => 'eazydocs-promax-notice',
			'dependency' => array('enable-selected-comment', '==', 'true'),
			'multiple' => true,
			'chosen' => true
		),

		array(
			'id' => 'selected_comment_form',
			'type' => 'subheading',
			'title' => esc_html__('Feedback Form Settings', 'eazydocs'),
			'class' => 'eazydocs-promax-notice',
			'dependency' => array('enable-selected-comment', '==', 'true'),
		),

		array(
			'id' => 'selected_comment_options_heading',
			'type' => 'text',
			'title' => esc_html__('Predefined Options Heading', 'eazydocs'),
			'class' => 'eazydocs-promax-notice',
			'default' => esc_html__('What is the issue with this selection?', 'eazydocs'),
			'dependency' => array('enable-selected-comment', '==', 'true'),
		),

		array(
			'id' => 'selected_comment_options',
			'type' => 'repeater',
			'title' => esc_html__('Predefined Options', 'eazydocs'),
			'class' => 'eazydocs-promax-notice',
			'dependency' => array('enable-selected-comment', '==', 'true'),
			'fields' => array(
				array(
					'id' => 'label',
					'type' => 'text',
					'title' => esc_html__('Option Label', 'eazydocs'),
				),
			),
			'default' => array(
				array(
					'label' => "Inaccurate - doesn't match what I see in the product",
				),
				array(
					'label' => 'Hard to understand - unclear or translation is wrong',
				),
			)
		),

		array(
			'id' => 'selected_comment_option_other',
			'type' => 'text',
			'title' => esc_html__('Other Option Label', 'eazydocs'),
			'class' => 'eazydocs-promax-notice',
			'default' => esc_html__('Others', 'eazydocs'),
			'dependency' => array('enable-selected-comment', '==', 'true'),
		),

		array(
			'id' => 'selected_comment_form_heading',
			'type' => 'text',
			'title' => esc_html__('Form Title', 'eazydocs'),
			'class' => 'eazydocs-promax-notice',
			'default' => esc_html__('Share additional info or suggestions', 'eazydocs'),
			'dependency' => array('enable-selected-comment', '==', 'true'),
		),

		array(
			'id' => 'selected_comment_form_subheading',
			'type' => 'text',
			'title' => esc_html__('Form Subtitle', 'eazydocs'),
			'class' => 'eazydocs-promax-notice',
			'default' => esc_html__('Do not share any personal info', 'eazydocs'),
			'dependency' => array('enable-selected-comment', '==', 'true'),
		),

		array(
			'id' => 'selected_comment_form_footer',
			'type' => 'textarea',
			'title' => esc_html__('Form Disclaimer', 'eazydocs'),
			'class' => 'eazydocs-promax-notice',
			'default' => esc_html__('By continuing, you allow Google to use your answers and account info to improve services, as explained in our Privacy & Terms.', 'eazydocs'),
			'dependency' => array('enable-selected-comment', '==', 'true'),
		)
	)
));
