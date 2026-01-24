<?php
/**
 * Cannot access directly.
 */
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}


//
// Docs Collaboration
//
CSF::createSection( $prefix, array(
	'id'     => 'contributor_fields',
	'title'  => esc_html__( 'Docs Collaboration', 'eazydocs' ),
	'icon'   => 'dashicons dashicons-groups',
	'fields' => [
		array(
			'id'         => 'is_doc_contribution',
			'type'       => 'switcher',
			'title'      => esc_html__( 'Collaboration Feature', 'eazydocs' ),
			'subtitle'   => esc_html__( 'Collaboration buttons on the doc Right Sidebar.', 'eazydocs' ),
			'desc'       => esc_html__( 'By enabling this feature, you are allowing to Administrator & Editor people to contribute the docs. This will also let you manage the contributors from the Doc post editor.',
				'eazydocs' ),
			'text_on'    => esc_html__( 'Enabled', 'eazydocs' ),
			'text_off'   => esc_html__( 'Disabled', 'eazydocs' ),
			'text_width' => 92,
			'default'    => false,
			'class'      => 'eazydocs-promax-notice'
		),

		array(
			'id'          => 'docs_frontend_login_page',
			'type'        => 'select',
			'placeholder' => 'Select page',
			'title'       => esc_html__( 'Login Page', 'eazydocs' ),
			'subtitle'    => esc_html__( 'Type in the field to select the login page. User would require to login to edit/add docs from frontend.', 'eazydocs' ),
			'desc'        => esc_html__( 'This page is required to select to show/enable the Add/Edit doc buttons. You can use the shortcode [ezd_login_form] for the login page.', 'eazydocs' ),
			'options'     => 'pages',
			'class'       => 'eazydocs-promax-notice',
			'chosen'      => true,
			'ajax'        => true,
			'query_args'  => array(
				'posts_per_page' => -1,
			),
			'dependency'  => array(
				array( 'is_doc_contribution', '==', 'true' )
			)
		),
		
		array(
			'id'         => 'ezd_add_editable_role_opt',
			'type'       => 'heading',
			'title'      => esc_html__( 'Contributor Access Control', 'eazydocs' ),
			'subtitle'   => esc_html__( 'Manage which user roles can contribute to your documentation.', 'eazydocs' ),
			'dependency' => array(
				array( 'is_doc_contribution', '==', 'true' ),
				array( 'docs_frontend_login_page', '!=', '' ),
			)
		),
		
		array(
			'id'         => 'ezd_add_editable_roles',
			'type'       => 'select',
			'title'      => esc_html__( 'Allowed User Roles', 'eazydocs' ),
			'desc'       => esc_html__( 'Select the user roles that can add or edit documentation from the frontend. These roles will have contributor privileges.', 'eazydocs' ),
			'options'    => function_exists( 'eazydocs_user_role_names' ) && ezd_is_premium() ? eazydocs_user_role_names() : array(
				'administrator' => esc_html__( 'Administrator', 'eazydocs' ),
				'editor'        => esc_html__( 'Editor', 'eazydocs' ),
				'author'        => esc_html__( 'Author', 'eazydocs' ),
				'contributor'   => esc_html__( 'Contributor', 'eazydocs' ),
				'subscriber'    => esc_html__( 'Subscriber', 'eazydocs' ),
			),
			'default'    => 'administrator',
			'chosen'     => true,
			'ajax'       => true,
			'multiple'   => true,
			'class'      => 'eazydocs-pro-notice'
		),
		
		array(
			'id'         => 'ezd_add_doc_heading',
			'type'       => 'heading',
			'title'      => esc_html__( 'Add Doc', 'eazydocs' ),
			'dependency' => array(
				array( 'is_doc_contribution', '==', 'true' ),
				array( 'docs_frontend_login_page', '!=', '' ),
			)
		),

		array(
			'id'         => 'frontend_add_switcher',
			'type'       => 'switcher',
			'title'      => esc_html__( 'Add Button', 'eazydocs' ),
			'text_on'    => esc_html__( 'Show', 'eazydocs' ),
			'text_off'   => esc_html__( 'Hide', 'eazydocs' ),
			'text_width' => 72,
			'default'    => false,
			'class'      => 'eazydocs-promax-notice',
			'dependency' => array(
				array( 'is_doc_contribution', '==', 'true' ),
				array( 'docs_frontend_login_page', '!=', '' ),
			)
		),

		array(
			'id'         => 'frontend_add_btn_text',
			'type'       => 'text',
			'title'      => esc_html__( 'Button', 'eazydocs' ),
			'default'    => esc_html__( 'Add Doc', 'eazydocs' ),
			'class'      => 'eazydocs-promax-notice',
			'dependency' => array(
				array( 'is_doc_contribution', '==', 'true' ),
				array( 'docs_frontend_login_page', '!=', '' ),
				array( 'frontend_add_switcher', '==', 'true' ),
			)
		),

		array(
			'id'         => 'frontend_edit_doc',
			'type'       => 'heading',
			'title'      => esc_html__( 'Edit Doc', 'eazydocs' ),
			'dependency' => array(
				array( 'is_doc_contribution', '==', 'true' ),
				array( 'docs_frontend_login_page', '!=', '' ),
			)
		),

		array(
			'id'         => 'frontend_edit_switcher',
			'type'       => 'switcher',
			'title'      => esc_html__( 'Edit Button', 'eazydocs' ),
			'text_on'    => esc_html__( 'Show', 'eazydocs' ),
			'text_off'   => esc_html__( 'Hide', 'eazydocs' ),
			'text_width' => 72,
			'default'    => false,
			'class'      => 'eazydocs-promax-notice',
			'dependency' => array(
				array( 'is_doc_contribution', '==', 'true' ),
				array( 'docs_frontend_login_page', '!=', '' ),
			)
		),

		array(
			'id'         => 'frontend_edit_btn_text',
			'type'       => 'text',
			'title'      => esc_html__( 'Button', 'eazydocs' ),
			'default'    => esc_html__( 'Edit Doc', 'eazydocs' ),
			'class'      => 'eazydocs-promax-notice',
			'dependency' => array(
				array( 'is_doc_contribution', '==', 'true' ),
				array( 'frontend_edit_switcher', '==', 'true' ),
				array( 'docs_frontend_login_page', '!=', '' ),
			)
		),

		array(
			'id'         => 'docs_contributor_meta',
			'type'       => 'heading',
			'title'      => esc_html__( 'Meta Content', 'eazydocs' ),
			'dependency' => array(
				array( 'is_doc_contribution', '==', 'true' ),
				array( 'docs_frontend_login_page', '!=', '' ),
			)
		),

		array(
			'id'         => 'contributor_meta_visibility',
			'type'       => 'switcher',
			'title'      => esc_html__( 'Enable / Disable', 'eazydocs' ),
			'text_on'    => esc_html__( 'Show', 'eazydocs' ),
			'text_off'   => esc_html__( 'Hide', 'eazydocs' ),
			'class'      => 'eazydocs-promax-notice',
			'text_width' => 70,
			'default'    => true,
			'dependency' => array(
				array( 'is_doc_contribution', '==', 'true' ),
				array( 'docs_frontend_login_page', '!=', '' ),
			)
		),

		array(
			'id'         => 'contributor_meta_title',
			'type'       => 'text',
			'title'      => esc_html__( 'Title', 'eazydocs' ),
			'default'    => esc_html__( 'Contributors', 'eazydocs' ),
			'class'      => 'eazydocs-promax-notice',
			'dependency' => array(
				array( 'is_doc_contribution', '==', 'true' ),
				array( 'contributor_meta_visibility', '==', true ),
				array( 'docs_frontend_login_page', '!=', '' ),
			)
		),

		array(
			'id'         => 'contributor_meta_dropdown_title',
			'type'       => 'text',
			'title'      => esc_html__( 'Dropdown Heading', 'eazydocs' ),
			'default'    => esc_html__( 'Manage Contributors', 'eazydocs' ),
			'class'      => 'eazydocs-promax-notice',
			'dependency' => array(
				array( 'is_doc_contribution', '==', 'true' ),
				array( 'contributor_meta_visibility', '==', true ),
				array( 'docs_frontend_login_page', '!=', '' ),
			)
		),

		array(
			'id'         => 'contributor_meta_search',
			'type'       => 'switcher',
			'title'      => esc_html__( 'Search', 'eazydocs' ),
			'subtitle'   => esc_html__( 'Search through the existing users', 'eazydocs' ),
			'text_on'    => esc_html__( 'Show', 'eazydocs' ),
			'text_off'   => esc_html__( 'Hide', 'eazydocs' ),
			'class'      => 'eazydocs-promax-notice',
			'text_width' => 70,
			'default'    => true,
			'dependency' => array(
				array( 'is_doc_contribution', '==', 'true' ),
				array( 'docs_frontend_login_page', '!=', '' ),
				array( 'contributor_meta_visibility', '==', true )
			)
		),

		array(
			'id'         => 'contributor_load_more',
			'type'       => 'switcher',
			'title'      => esc_html__( 'Load More By Ajax', 'eazydocs' ),
			'subtitle'   => esc_html__( 'Enable / Disable the Load more users with scrolling', 'eazydocs' ),
			'text_on'    => esc_html__( 'Show', 'eazydocs' ),
			'text_off'   => esc_html__( 'Hide', 'eazydocs' ),
			'class'      => 'eazydocs-promax-notice',
			'text_width' => 70,
			'default'    => false,
			'dependency' => array(
				array( 'is_doc_contribution', '==', 'true' ),
				array( 'docs_frontend_login_page', '!=', '' ),
				array( 'contributor_meta_visibility', '==', true )
			)
		),

		array(
			'id'         => 'contributor_load_more_text',
			'type'       => 'text',
			'title'      => esc_html__( 'Load More Text', 'eazydocs' ),
			'default'    => esc_html__( 'Loading', 'eazydocs' ),
			'class'      => 'eazydocs-promax-notice',
			'dependency' => array(
				array( 'is_doc_contribution', '==', 'true' ),
				array( 'docs_frontend_login_page', '!=', '' ),
				array( 'contributor_meta_visibility', '==', true ),
				array( 'contributor_load_more', '==', true )
			)
		),

		array(
			'id'         => 'contributor_load_per_scroll',
			'type'       => 'number',
			'title'      => esc_html__( 'Load Per Scroll', 'eazydocs' ),
			'default'    => esc_html__( '3', 'eazydocs' ),
			'class'      => 'eazydocs-promax-notice',
			'dependency' => array(
				array( 'is_doc_contribution', '==', 'true' ),
				array( 'docs_frontend_login_page', '!=', '' ),
				array( 'contributor_meta_visibility', '==', true ),
				array( 'contributor_load_more', '==', true )
			)
		),

		array(
			'id'         => 'contributor_to_add',
			'type'       => 'number',
			'title'      => esc_html__( 'Show User Number', 'eazydocs' ),
			'subtitle'   => esc_html__( 'Number of users that can be added', 'eazydocs' ),
			'class'      => 'eazydocs-promax-notice',
			'default'    => 3,
			'dependency' => array(
				array( 'is_doc_contribution', '==', 'true' ),
				array( 'docs_frontend_login_page', '!=', '' ),
				array( 'contributor_meta_visibility', '==', true ),
				array( 'contributor_load_more', '!=', true )
			)
		)
	]
) );