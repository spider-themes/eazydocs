<?php
/**
 * API Docs metabox fields (Pro Max).
 * Structured editor for the api_docs post type.
 */
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$meta = 'ezd_api_docs_meta';

CSF::createMetabox( $meta, array(
	'title'     => esc_html__( 'API Documentation', 'eazydocs' ),
	'post_type' => 'api_docs',
	'data_type' => 'serialize',
	'priority'  => 'high',
	'class'     => 'ezd-api-docs-metabox eazydocs-promax-notice',
) );

CSF::createSection( $meta, array(
	'id'     => 'ezd_api_general',
	'title'  => esc_html__( 'General', 'eazydocs' ),
	'icon'   => 'dashicons dashicons-info-outline',
	'fields' => array(
		array(
			'type'    => 'subheading',
			'content' => esc_html__( 'API Settings', 'eazydocs' ),
		),
		array(
			'id'       => 'description',
			'type'     => 'wp_editor',
			'title'    => esc_html__( 'Description', 'eazydocs' ),
			'default'  => '',
			'sanitize' => 'wp_kses_post',
			'class'    => 'eazydocs-promax-notice',
		),
		array(
			'id'          => 'base_url',
			'type'        => 'text',
			'title'       => esc_html__( 'Base URL', 'eazydocs' ),
			'placeholder' => 'https://api.example.com/v1',
			'default'     => ezd_get_opt( 'default_base_url', 'https://api.example.com/v1' ),
			'sanitize'    => 'esc_url_raw',
			'class'       => 'eazydocs-promax-notice',
		),
		array(
			'id'          => 'version',
			'type'        => 'text',
			'title'       => esc_html__( 'API Version', 'eazydocs' ),
			'placeholder' => 'v1',
			'default'     => ezd_get_opt( 'default_api_version', 'v1' ),
			'sanitize'    => 'sanitize_text_field',
			'class'       => 'eazydocs-promax-notice',
		),
		array(
			'id'      => 'authentication',
			'type'    => 'select',
			'title'   => esc_html__( 'Authentication', 'eazydocs' ),
			'options' => array(
				'none'    => esc_html__( 'None', 'eazydocs' ),
				'api_key' => esc_html__( 'API Key', 'eazydocs' ),
				'bearer'  => esc_html__( 'Bearer Token', 'eazydocs' ),
				'basic'   => esc_html__( 'Basic Auth', 'eazydocs' ),
				'oauth2'  => esc_html__( 'OAuth 2.0', 'eazydocs' ),
				'custom'  => esc_html__( 'Custom', 'eazydocs' ),
			),
			'default' => ezd_get_opt( 'default_auth', 'none' ),
			'chosen'  => true,
			'class'   => 'eazydocs-promax-notice',
		),
		array(
			'id'         => 'auth_details',
			'type'       => 'textarea',
			'title'      => esc_html__( 'Authentication Details', 'eazydocs' ),
			'default'    => '',
			'sanitize'   => 'sanitize_textarea_field',
			'dependency' => array( 'authentication', '!=', 'none' ),
			'class'      => 'eazydocs-promax-notice',
		),
	),
) );

CSF::createSection( $meta, array(
	'id'     => 'ezd_api_collections',
	'title'  => esc_html__( 'Collections', 'eazydocs' ),
	'icon'   => 'dashicons dashicons-category',
	'fields' => array(
		array(
			'type'    => 'notice',
			'style'   => 'info',
			'content' => esc_html__( 'Collections group related endpoints (e.g. Users, Products, Orders). Expand a collection to manage endpoints, parameters, request, responses, and code examples. Drag to reorder.', 'eazydocs' ),
		),
		array(
			'id'                     => 'collections',
			'type'                   => 'group',
			'title'                  => esc_html__( 'Collections', 'eazydocs' ),
			'button_title'           => esc_html__( 'Add Collection', 'eazydocs' ),
			'accordion_title_number' => true,
			'accordion_title_by'     => array( 'title', 'slug' ),
			'default'                => array(),
			'sanitize'               => 'ezd_sanitize_api_docs_collections',
			'class'                  => 'eazydocs-promax-notice',
			'fields'                 => array(
				array(
					'id'      => 'title',
					'type'    => 'text',
					'title'   => esc_html__( 'Title', 'eazydocs' ),
					'default' => '',
				),
				array(
					'id'          => 'slug',
					'type'        => 'text',
					'title'       => esc_html__( 'Slug', 'eazydocs' ),
					'placeholder' => 'users',
					'default'     => '',
				),
				array(
					'id'       => 'enabled',
					'type'     => 'switcher',
					'title'    => esc_html__( 'Enabled', 'eazydocs' ),
					'text_on'  => esc_html__( 'Yes', 'eazydocs' ),
					'text_off' => esc_html__( 'No', 'eazydocs' ),
					'default'  => true,
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
							'title'   => esc_html__( 'Endpoint Title', 'eazydocs' ),
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
							'title'       => esc_html__( 'Path', 'eazydocs' ),
							'placeholder' => '/users/{id}',
							'default'     => '',
						),
						array(
							'id'      => 'description',
							'type'    => 'textarea',
							'title'   => esc_html__( 'Description', 'eazydocs' ),
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
													'id'      => 'name',
													'type'    => 'text',
													'title'   => esc_html__( 'Name', 'eazydocs' ),
													'default' => '',
												),
												array(
													'id'      => 'in',
													'type'    => 'select',
													'title'   => esc_html__( 'Location', 'eazydocs' ),
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
													'title'   => esc_html__( 'Type', 'eazydocs' ),
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
													'title'   => esc_html__( 'Default', 'eazydocs' ),
													'default' => '',
												),
												array(
													'id'      => 'example',
													'type'    => 'text',
													'title'   => esc_html__( 'Example', 'eazydocs' ),
													'default' => '',
												),
												array(
													'id'      => 'description',
													'type'    => 'textarea',
													'title'   => esc_html__( 'Description', 'eazydocs' ),
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
											'title'   => esc_html__( 'Content Type', 'eazydocs' ),
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
											'title'        => esc_html__( 'Headers', 'eazydocs' ),
											'button_title' => esc_html__( 'Add Header', 'eazydocs' ),
											'default'      => array(),
											'fields'       => array(
												array(
													'id'      => 'name',
													'type'    => 'text',
													'title'   => esc_html__( 'Name', 'eazydocs' ),
													'default' => '',
												),
												array(
													'id'      => 'value',
													'type'    => 'text',
													'title'   => esc_html__( 'Example Value', 'eazydocs' ),
													'default' => '',
												),
												array(
													'id'      => 'description',
													'type'    => 'text',
													'title'   => esc_html__( 'Description', 'eazydocs' ),
													'default' => '',
												),
											),
										),
										array(
											'id'      => 'request_body',
											'type'    => 'textarea',
											'title'   => esc_html__( 'Body Description', 'eazydocs' ),
											'default' => '',
										),
										array(
											'id'       => 'request_example',
											'type'     => 'code_editor',
											'title'    => esc_html__( 'Request Example', 'eazydocs' ),
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
													'title'   => esc_html__( 'Description', 'eazydocs' ),
													'default' => '',
												),
												array(
													'id'      => 'content_type',
													'type'    => 'select',
													'title'   => esc_html__( 'Content Type', 'eazydocs' ),
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
													'title'        => esc_html__( 'Headers', 'eazydocs' ),
													'button_title' => esc_html__( 'Add Header', 'eazydocs' ),
													'default'      => array(),
													'fields'       => array(
														array(
															'id'      => 'name',
															'type'    => 'text',
															'title'   => esc_html__( 'Name', 'eazydocs' ),
															'default' => '',
														),
														array(
															'id'      => 'description',
															'type'    => 'text',
															'title'   => esc_html__( 'Description', 'eazydocs' ),
															'default' => '',
														),
													),
												),
												array(
													'id'      => 'body',
													'type'    => 'textarea',
													'title'   => esc_html__( 'Response Body', 'eazydocs' ),
													'default' => '',
												),
												array(
													'id'       => 'example',
													'type'     => 'code_editor',
													'title'    => esc_html__( 'Response Example', 'eazydocs' ),
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
									'title'  => esc_html__( 'Examples', 'eazydocs' ),
									'fields' => array(
										array(
											'id'                 => 'examples',
											'type'               => 'group',
											'title'              => esc_html__( 'Code Examples', 'eazydocs' ),
											'button_title'       => esc_html__( 'Add Example', 'eazydocs' ),
											'accordion_title_by' => array( 'language' ),
											'default'            => array(),
											'fields'             => array(
												array(
													'id'      => 'language',
													'type'    => 'select',
													'title'   => esc_html__( 'Language', 'eazydocs' ),
													'options' => array(
														'curl'       => 'cURL',
														'javascript' => 'JavaScript',
														'php'        => 'PHP',
													),
													'default' => 'curl',
												),
												array(
													'id'      => 'label',
													'type'    => 'text',
													'title'   => esc_html__( 'Label', 'eazydocs' ),
													'default' => '',
												),
												array(
													'id'       => 'code',
													'type'     => 'code_editor',
													'title'    => esc_html__( 'Code', 'eazydocs' ),
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
			'content' => esc_html__( 'Display', 'eazydocs' ),
		),
		ezd_csf_switcher_field( array(
			'id'       => 'show_method_badges',
			'title'    => esc_html__( 'Method Badges', 'eazydocs' ),
			'subtitle' => esc_html__( 'Show HTTP method badges in documentation navigation.', 'eazydocs' ),
			'default'  => true,
			'class'    => 'eazydocs-promax-notice',
		) ),
		ezd_csf_switcher_field( array(
			'id'       => 'show_try_it_placeholder',
			'title'    => esc_html__( 'Implementation Panel', 'eazydocs' ),
			'subtitle' => esc_html__( 'Show request/response examples and code samples in the right-hand implementation area.', 'eazydocs' ),
			'default'  => true,
			'class'    => 'eazydocs-promax-notice',
		) ),
		array(
			'id'      => 'default_example_language',
			'type'    => 'select',
			'title'   => esc_html__( 'Default Example Language', 'eazydocs' ),
			'options' => array(
				'curl'       => 'cURL',
				'javascript' => 'JavaScript',
				'php'        => 'PHP',
			),
			'default' => 'curl',
			'chosen'  => true,
			'class'   => 'eazydocs-promax-notice',
		),
	),
) );
