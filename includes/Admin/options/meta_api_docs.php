<?php
/**
 * API Docs metabox fields (Pro Max).
 * Structured editor for the api_docs post type.
 */
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! ezd_is_promax() ) {
	return;
}

$meta = 'ezd_api_docs_meta';

CSF::createMetabox( $meta, array(
	'title'     => esc_html__( 'API Docs', 'eazydocs' ),
	'post_type' => 'api_docs',
	'data_type' => 'serialize',
	'priority'  => 'high',
	'class'     => 'ezd-api-docs-metabox',
) );

CSF::createSection( $meta, array(
	'id'     => 'ezd_api_general',
	'title'  => esc_html__( 'General', 'eazydocs' ),
	'icon'   => 'dashicons dashicons-info-outline',
	'fields' => array(
		array(
			'id'       => 'description',
			'type'     => 'wp_editor',
			'title'    => esc_html__( 'Overview Content', 'eazydocs' ),
			'subtitle' => esc_html__( 'Introduction shown on the API overview page.', 'eazydocs' ),
			'default'  => '',
			'sanitize' => 'wp_kses_post',
		),
		array(
			'id'          => 'base_url',
			'type'        => 'text',
			'title'       => esc_html__( 'Base URL', 'eazydocs' ),
			'subtitle'    => esc_html__( 'Root URL for this API. Endpoint paths are added after it.', 'eazydocs' ),
			'placeholder' => 'https://api.example.com/v1',
			'default'     => ezd_get_opt( 'default_base_url', 'https://api.example.com/v1' ),
			'sanitize'    => 'esc_url_raw',
		),
		array(
			'id'          => 'version',
			'type'        => 'text',
			'title'       => esc_html__( 'Version Label', 'eazydocs' ),
			'subtitle'    => esc_html__( 'Shown next to the API name. Does not change URLs.', 'eazydocs' ),
			'placeholder' => 'v1',
			'default'     => ezd_get_opt( 'default_api_version', 'v1' ),
			'sanitize'    => 'sanitize_text_field',
		),
		array(
			'id'       => 'authentication',
			'type'     => 'select',
			'title'    => esc_html__( 'Authentication Type', 'eazydocs' ),
			'subtitle' => esc_html__( 'How clients authenticate with this API.', 'eazydocs' ),
			'options'  => array(
				'none'    => esc_html__( 'None', 'eazydocs' ),
				'api_key' => esc_html__( 'API Key', 'eazydocs' ),
				'bearer'  => esc_html__( 'Bearer Token', 'eazydocs' ),
				'basic'   => esc_html__( 'Basic Auth', 'eazydocs' ),
				'oauth2'  => esc_html__( 'OAuth 2.0', 'eazydocs' ),
				'custom'  => esc_html__( 'Custom', 'eazydocs' ),
			),
			'default'  => ezd_get_opt( 'default_auth', 'none' ),
			'chosen'   => true,
		),
		array(
			'id'          => 'auth_details',
			'type'        => 'textarea',
			'title'       => esc_html__( 'Authentication Instructions', 'eazydocs' ),
			'subtitle'    => esc_html__( 'Explain how clients should send credentials.', 'eazydocs' ),
			'placeholder' => 'Authorization: Bearer {token}',
			'default'     => '',
			'sanitize'    => 'sanitize_textarea_field',
			'dependency'  => array( 'authentication', '!=', 'none' ),
		),
	),
) );

CSF::createSection( $meta, array(
	'id'     => 'ezd_api_collections',
	'title'  => esc_html__( 'Collections', 'eazydocs' ),
	'icon'   => 'dashicons dashicons-category',
	'fields' => array(
		array(
			'id'                     => 'collections',
			'type'                   => 'group',
			'title'                  => esc_html__( 'Collections', 'eazydocs' ),
			'button_title'           => esc_html__( 'Add Collection', 'eazydocs' ),
			'accordion_title_number' => true,
			'accordion_title_by'     => array( 'title', 'slug' ),
			'default'                => array(),
			'sanitize'               => 'ezd_sanitize_api_docs_collections',
			'fields'                 => array(
				array(
					'id'      => 'title',
					'type'    => 'text',
					'title'   => esc_html__( 'Collection Name', 'eazydocs' ),
					'default' => '',
				),
				array(
					'id'          => 'slug',
					'type'        => 'text',
					'title'       => esc_html__( 'URL Slug', 'eazydocs' ),
					'subtitle'    => esc_html__( 'Used in the page URL. Auto-generated if left empty.', 'eazydocs' ),
					'placeholder' => 'users',
					'default'     => '',
				),
				array(
					'id'         => 'enabled',
					'type'       => 'switcher',
					'title'      => esc_html__( 'Visibility', 'eazydocs' ),
					'text_on'    => esc_html__( 'Show', 'eazydocs' ),
					'text_off'   => esc_html__( 'Hide', 'eazydocs' ),
					'text_width' => 80,
					'default'    => true,
				),
				array(
					'id'                     => 'endpoints',
					'type'                   => 'group',
					'title'                  => esc_html__( 'Endpoints', 'eazydocs' ),
					'button_title'           => esc_html__( 'Add Endpoint', 'eazydocs' ),
					'accordion_title_number' => true,
					'accordion_title_by'     => array( 'method', 'path', 'title' ),
					'default'                => array(),
					'fields'                 => array(
						array(
							'id'      => 'title',
							'type'    => 'text',
							'title'   => esc_html__( 'Endpoint Name', 'eazydocs' ),
							'default' => '',
						),
						array(
							'id'      => 'method',
							'type'    => 'select',
							'title'   => esc_html__( 'HTTP Method', 'eazydocs' ),
							'options' => array(
								'GET'    => 'GET',
								'POST'   => 'POST',
								'PUT'    => 'PUT',
								'PATCH'  => 'PATCH',
								'DELETE' => 'DELETE',
							),
							'default' => 'GET',
						),
						array(
							'id'          => 'path',
							'type'        => 'text',
							'title'       => esc_html__( 'Endpoint Path', 'eazydocs' ),
							'subtitle'    => esc_html__( 'Added after the Base URL.', 'eazydocs' ),
							'placeholder' => '/users/{id}',
							'default'     => '',
						),
						array(
							'id'      => 'description',
							'type'    => 'textarea',
							'title'   => esc_html__( 'Endpoint Description', 'eazydocs' ),
							'default' => '',
						),
						array(
							'id'    => 'details',
							'type'  => 'tabbed',
							'title' => esc_html__( 'Endpoint Details', 'eazydocs' ),
							'tabs'  => array(
								array(
									'title'  => esc_html__( 'Parameters', 'eazydocs' ),
									'fields' => array(
										array(
											'id'                     => 'parameters',
											'type'                   => 'group',
											'title'                  => esc_html__( 'Parameters', 'eazydocs' ),
											'button_title'           => esc_html__( 'Add Parameter', 'eazydocs' ),
											'accordion_title_number' => true,
											'accordion_title_by'     => array( 'name', 'in' ),
											'default'                => array(),
											'fields'                 => array(
												array(
													'id'          => 'name',
													'type'        => 'text',
													'title'       => esc_html__( 'Parameter Name', 'eazydocs' ),
													'placeholder' => 'limit',
													'default'     => '',
												),
												array(
													'id'      => 'in',
													'type'    => 'select',
													'title'   => esc_html__( 'Parameter Location', 'eazydocs' ),
													'options' => array(
														'query'  => esc_html__( 'Query', 'eazydocs' ),
														'path'   => esc_html__( 'Path', 'eazydocs' ),
														'header' => esc_html__( 'Header', 'eazydocs' ),
													),
													'default' => 'query',
												),
												array(
													'id'      => 'type',
													'type'    => 'select',
													'title'   => esc_html__( 'Data Type', 'eazydocs' ),
													'options' => array(
														'string'  => 'string',
														'integer' => 'integer',
														'number'  => 'number',
														'boolean' => 'boolean',
														'array'   => 'array',
														'object'  => 'object',
														'file'    => 'file',
													),
													'default' => 'string',
												),
												array(
													'id'       => 'required',
													'type'     => 'switcher',
													'title'    => esc_html__( 'Required', 'eazydocs' ),
													'text_on'  => esc_html__( 'Yes', 'eazydocs' ),
													'text_off' => esc_html__( 'No', 'eazydocs' ),
													'default'  => false,
												),
												array(
													'id'      => 'default',
													'type'    => 'text',
													'title'   => esc_html__( 'Default Value', 'eazydocs' ),
													'default' => '',
												),
												array(
													'id'      => 'example',
													'type'    => 'text',
													'title'   => esc_html__( 'Example Value', 'eazydocs' ),
													'default' => '',
												),
												array(
													'id'      => 'description',
													'type'    => 'textarea',
													'title'   => esc_html__( 'Parameter Description', 'eazydocs' ),
													'default' => '',
												),
											),
										),
									),
								),
								array(
									'title'  => esc_html__( 'Request', 'eazydocs' ),
									'fields' => array(
										array(
											'id'      => 'request_content_type',
											'type'    => 'select',
											'title'   => esc_html__( 'Request Content Type', 'eazydocs' ),
											'options' => array(
												'application/json'                  => 'application/json',
												'multipart/form-data'               => 'multipart/form-data',
												'application/x-www-form-urlencoded' => 'application/x-www-form-urlencoded',
												'text/plain'                        => 'text/plain',
												'application/xml'                   => 'application/xml',
											),
											'default' => 'application/json',
										),
										array(
											'id'           => 'request_headers',
											'type'         => 'group',
											'title'        => esc_html__( 'Request Headers', 'eazydocs' ),
											'button_title' => esc_html__( 'Add Header', 'eazydocs' ),
											'default'      => array(),
											'fields'       => array(
												array(
													'id'          => 'name',
													'type'        => 'text',
													'title'       => esc_html__( 'Header Name', 'eazydocs' ),
													'placeholder' => 'Content-Type',
													'default'     => '',
												),
												array(
													'id'          => 'value',
													'type'        => 'text',
													'title'       => esc_html__( 'Example Value', 'eazydocs' ),
													'placeholder' => 'application/json',
													'default'     => '',
												),
												array(
													'id'      => 'description',
													'type'    => 'text',
													'title'   => esc_html__( 'Header Description', 'eazydocs' ),
													'default' => '',
												),
											),
										),
										array(
											'id'      => 'request_body',
											'type'    => 'textarea',
											'title'   => esc_html__( 'Request Body Description', 'eazydocs' ),
											'default' => '',
										),
										array(
											'id'       => 'request_example',
											'type'     => 'code_editor',
											'title'    => esc_html__( 'Request JSON Example', 'eazydocs' ),
											'default'  => '',
											'settings' => array(
												'theme' => 'default',
												'mode'  => 'javascript',
											),
										),
									),
								),
								array(
									'title'  => esc_html__( 'Responses', 'eazydocs' ),
									'fields' => array(
										array(
											'id'                     => 'responses',
											'type'                   => 'group',
											'title'                  => esc_html__( 'Responses', 'eazydocs' ),
											'button_title'           => esc_html__( 'Add Response', 'eazydocs' ),
											'accordion_title_number' => true,
											'accordion_title_by'     => array( 'status', 'description' ),
											'default'                => array(),
											'fields'                 => array(
												array(
													'id'      => 'status',
													'type'    => 'number',
													'title'   => esc_html__( 'Status Code', 'eazydocs' ),
													'default' => 200,
												),
												array(
													'id'      => 'description',
													'type'    => 'text',
													'title'   => esc_html__( 'Status Label', 'eazydocs' ),
													'default' => '',
												),
												array(
													'id'      => 'content_type',
													'type'    => 'select',
													'title'   => esc_html__( 'Response Content Type', 'eazydocs' ),
													'options' => array(
														'application/json'                  => 'application/json',
														'multipart/form-data'               => 'multipart/form-data',
														'application/x-www-form-urlencoded' => 'application/x-www-form-urlencoded',
														'text/plain'                        => 'text/plain',
														'application/xml'                   => 'application/xml',
													),
													'default' => 'application/json',
												),
												array(
													'id'           => 'headers',
													'type'         => 'group',
													'title'        => esc_html__( 'Response Headers', 'eazydocs' ),
													'button_title' => esc_html__( 'Add Header', 'eazydocs' ),
													'default'      => array(),
													'fields'       => array(
														array(
															'id'          => 'name',
															'type'        => 'text',
															'title'       => esc_html__( 'Header Name', 'eazydocs' ),
															'placeholder' => 'X-Request-Id',
															'default'     => '',
														),
														array(
															'id'      => 'description',
															'type'    => 'text',
															'title'   => esc_html__( 'Header Description', 'eazydocs' ),
															'default' => '',
														),
													),
												),
												array(
													'id'      => 'body',
													'type'    => 'textarea',
													'title'   => esc_html__( 'Response Body Description', 'eazydocs' ),
													'default' => '',
												),
												array(
													'id'       => 'example',
													'type'     => 'code_editor',
													'title'    => esc_html__( 'Response JSON Example', 'eazydocs' ),
													'default'  => '',
													'settings' => array(
														'theme' => 'default',
														'mode'  => 'javascript',
													),
												),
											),
										),
									),
								),
								array(
									'title'  => esc_html__( 'Code Examples', 'eazydocs' ),
									'fields' => array(
										array(
											'id'                 => 'examples',
											'type'               => 'group',
											'title'              => esc_html__( 'Code Examples', 'eazydocs' ),
											'button_title'       => esc_html__( 'Add Code Example', 'eazydocs' ),
											'accordion_title_by' => array( 'language' ),
											'default'            => array(),
											'fields'             => array(
												array(
													'id'          => 'language',
													'type'        => 'text',
													'title'       => esc_html__( 'Code Language', 'eazydocs' ),
													'subtitle'    => esc_html__( 'Any language name, such as Python, Go, or Ruby.', 'eazydocs' ),
													'placeholder' => 'cURL',
													'default'     => 'cURL',
												),
												array(
													'id'      => 'label',
													'type'    => 'text',
													'title'   => esc_html__( 'Custom Tab Name', 'eazydocs' ),
													'subtitle' => esc_html__( 'Leave empty to use the language name.', 'eazydocs' ),
													'default' => '',
												),
												array(
													'id'       => 'code',
													'type'     => 'code_editor',
													'title'    => esc_html__( 'Code Snippet', 'eazydocs' ),
													'default'  => '',
													'settings' => array(
														'theme' => 'default',
														'mode'  => 'javascript',
													),
												),
											),
										),
									),
								),
							),
						),
					),
				),
			),
		),
	),
) );

CSF::createSection( $meta, array(
	'id'     => 'ezd_api_settings',
	'title'  => esc_html__( 'Settings', 'eazydocs' ),
	'icon'   => 'dashicons dashicons-admin-generic',
	'fields' => array(
		array(
			'type'    => 'subheading',
			'content' => esc_html__( 'Documentation Layout', 'eazydocs' ),
		),
		array(
			'id'       => 'display_format',
			'type'     => 'button_set',
			'title'    => esc_html__( 'Display Mode', 'eazydocs' ),
			'subtitle' => esc_html__( 'Multi-page uses separate URLs for collections and endpoints. One-page shows the full API on a single page.', 'eazydocs' ),
			'options'  => array(
				'multi'   => esc_html__( 'Multi-page', 'eazydocs' ),
				'onepage' => esc_html__( 'One-page', 'eazydocs' ),
			),
			'default'  => ezd_get_opt( 'default_api_display_format', 'multi' ),
		),
		array(
			'id'         => 'page_layout',
			'type'       => 'image_select',
			'title'      => esc_html__( 'Multi-page Sidebar Layout', 'eazydocs' ),
			'subtitle'   => esc_html__( 'Applies when Display Mode is Multi-page.', 'eazydocs' ),
			'options'    => array(
				'both_sidebar'  => EZD_IMG . 'customizer/both_sidebar.jpg',
				'left_sidebar'  => EZD_IMG . 'customizer/sidebar_left.jpg',
				'right_sidebar' => EZD_IMG . 'customizer/sidebar_right.jpg',
			),
			'default'    => ezd_get_opt( 'default_api_page_layout', 'both_sidebar' ),
			'class'      => 'single-layout-img-wrap',
			'dependency' => array( 'display_format', '==', 'multi' ),
		),
		array(
			'id'         => 'onepage_layout',
			'type'       => 'image_select',
			'title'      => esc_html__( 'One-page Sidebar Layout', 'eazydocs' ),
			'subtitle'   => esc_html__( 'Applies when Display Mode is One-page.', 'eazydocs' ),
			'options'    => array(
				'classic-onepage-layout' => EZD_IMG . 'customizer/both_sidebar.jpg',
				'fullscreen-layout'      => EZD_IMG . 'customizer/sidebar_left.jpg',
			),
			'default'    => ezd_get_opt( 'default_api_onepage_layout', 'classic-onepage-layout' ),
			'class'      => 'single-layout-img-wrap',
			'dependency' => array( 'display_format', '==', 'onepage' ),
		),
		array(
			'type'    => 'subheading',
			'content' => esc_html__( 'Display Options', 'eazydocs' ),
		),
		ezd_csf_switcher_field( array(
			'id'       => 'show_method_badges',
			'title'    => esc_html__( 'Show HTTP Method Badges', 'eazydocs' ),
			'default'  => ezd_get_opt( 'default_show_method_badges', true ),
		) ),
		ezd_csf_switcher_field( array(
			'id'       => 'show_try_it_placeholder',
			'title'    => esc_html__( 'Show Code Panel', 'eazydocs' ),
			'subtitle' => esc_html__( 'Shows sample code on the right. Does not send live API requests.', 'eazydocs' ),
			'default'  => ezd_get_opt( 'default_show_try_it_placeholder', true ),
		) ),
		array(
			'id'          => 'default_example_language',
			'type'        => 'text',
			'title'       => esc_html__( 'Default Code Language', 'eazydocs' ),
			'subtitle'    => esc_html__( 'Must match a Code Language used in your examples.', 'eazydocs' ),
			'placeholder' => 'cURL',
			'default'     => ezd_get_opt( 'default_example_language', 'cURL' ),
			'sanitize'    => 'sanitize_text_field',
		),
	),
) );
