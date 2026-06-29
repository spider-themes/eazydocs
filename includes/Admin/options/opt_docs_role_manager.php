<?php
/**
 * User Permissions & Role Management
 * Control who can create, edit, and manage documentation.
 */
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

// Roles are sourced dynamically so custom roles appear here too, consistently
// with the Docs Collaboration screen.
$ezd_role_options = function_exists( 'ezd_assignable_role_options' )
	? ezd_assignable_role_options()
	: [
		'administrator' => esc_html__( 'Administrator', 'eazydocs' ),
		'editor'        => esc_html__( 'Editor', 'eazydocs' ),
		'author'        => esc_html__( 'Author', 'eazydocs' ),
		'contributor'   => esc_html__( 'Contributor', 'eazydocs' ),
		'subscriber'    => esc_html__( 'Subscriber', 'eazydocs' ),
	];

//
// Docs role manager Fields
//
CSF::createSection( $prefix, array(
	'id'     => 'role_manager_fields',
	'title'  => esc_html__( 'User Permissions', 'eazydocs' ),
	'icon'   => 'dashicons dashicons-businessman',
	'fields' => [

		// ── Intro ────────────────────────────────────────────────────────
		array(
			'id'      => 'permissions_intro',
			'type'    => 'content',
			'content' => '
				<div class="ezd-settings-intro">
					<div class="ezd-settings-intro__inner">
						<div class="ezd-settings-intro__icon">
							<span class="dashicons dashicons-businessman"></span>
						</div>
						<div class="ezd-settings-intro__content">
							<h2>' . esc_html__( 'User Permissions', 'eazydocs' ) . '</h2>
							<p>' . esc_html__( 'Decide which user roles can author documentation, manage EazyDocs settings, and view analytics. Permissions are applied as WordPress capabilities, so they work everywhere — the Docs Builder, the block editor and the front end.', 'eazydocs' ) . '</p>
							<div class="ezd-settings-intro__features">
								<span><span class="dashicons dashicons-edit"></span>' . esc_html__( 'Authoring access', 'eazydocs' ) . '</span>
								<span><span class="dashicons dashicons-admin-generic"></span>' . esc_html__( 'Settings access', 'eazydocs' ) . '</span>
								<span><span class="dashicons dashicons-chart-bar"></span>' . esc_html__( 'Analytics access', 'eazydocs' ) . '</span>
							</div>
						</div>
					</div>
				</div>
			',
		),

		// ── Documentation access ─────────────────────────────────────────
		array(
			'id'    => 'permissions_authoring_heading',
			'type'  => 'heading',
			'title' => esc_html__( 'Documentation Access', 'eazydocs' ),
		),

		array(
			'id'       => 'docs-write-access',
			'type'     => 'select',
			'title'    => esc_html__( 'Documentation Authors', 'eazydocs' ),
			'subtitle' => esc_html__( 'Select which user roles can create and manage documentation from the Docs Builder interface.', 'eazydocs' ),
			'desc'     => esc_html__( 'These roles receive the doc authoring capabilities (create, edit, publish and delete docs). Roles that can already edit others\' posts also gain manager capabilities for private and others\' docs.', 'eazydocs' ),
			'options'  => $ezd_role_options,
			'chosen'   => true,
			'multiple' => true,
			'default'  => 'administrator',
			'class'    => 'eazydocs-pro-notice'
		),

		// Overlap note: Docs Collaboration grants the same authoring caps.
		array(
			'id'      => 'permissions_collaboration_note',
			'type'    => 'content',
			'content' => '
				<div class="ezd-settings-info ezd-settings-info--info">
					<span class="dashicons dashicons-info-outline"></span>
					<div>
						<strong>' . esc_html__( 'Heads up: Docs Collaboration also grants authoring access.', 'eazydocs' ) . '</strong>
						<p>' . esc_html__( 'Any roles selected under Docs Collaboration → Allowed User Roles receive the same doc-editing capabilities as the authors chosen here. Both lists are combined, so review that screen too if you intend to restrict who can edit docs.', 'eazydocs' ) . '</p>
					</div>
				</div>
			',
		),

		// Low-privilege warning.
		array(
			'id'      => 'permissions_low_role_warning',
			'type'    => 'content',
			'content' => '
				<div class="ezd-settings-info ezd-settings-info--warning">
					<span class="dashicons dashicons-warning"></span>
					<div>
						<strong>' . esc_html__( 'Granting access to low-privilege roles', 'eazydocs' ) . '</strong>
						<p>' . esc_html__( 'Roles such as Subscriber or Contributor cannot normally edit content. Adding them as Documentation Authors lets them create and edit docs — only do this if you trust those users.', 'eazydocs' ) . '</p>
					</div>
				</div>
			',
		),

		// ── Administration access ────────────────────────────────────────
		array(
			'id'    => 'permissions_admin_heading',
			'type'  => 'heading',
			'title' => esc_html__( 'Administration Access', 'eazydocs' ),
		),

		array(
			'id'       => 'settings-edit-access',
			'type'     => 'select',
			'title'    => esc_html__( 'Settings Managers', 'eazydocs' ),
			'subtitle' => esc_html__( 'Define which user roles have access to modify EazyDocs settings.', 'eazydocs' ),
			'desc'     => esc_html__( 'Access is granted by capability tier, so each option includes every role above it.', 'eazydocs' ),
			'options'  => [
				'manage_options' => esc_html__( 'Administrator only', 'eazydocs' ),
				'publish_pages'  => esc_html__( 'Administrator & Editor', 'eazydocs' ),
				'publish_posts'  => esc_html__( 'Administrator, Editor & Author', 'eazydocs' ),
			],
			'chosen'   => true,
			'default'  => 'manage_options',
			'multiple' => false,
			'class'    => 'eazydocs-pro-notice'
		),

		array(
			'id'       => 'analytics-access',
			'type'     => 'select',
			'title'    => esc_html__( 'Analytics Viewers', 'eazydocs' ),
			'subtitle' => esc_html__( 'Choose which user roles can view documentation analytics and performance metrics.', 'eazydocs' ),
			'options'  => $ezd_role_options,
			'chosen'   => true,
			'default'  => 'administrator',
			'multiple' => true,
			'class'    => 'eazydocs-promax-notice'
		),

		// ── Capability reference ─────────────────────────────────────────
		array(
			'id'      => 'permissions_capability_reference',
			'type'    => 'content',
			'content' => '
				<div class="ezd-perm-matrix">
					<h4><span class="dashicons dashicons-lock"></span>' . esc_html__( 'What each access level can do', 'eazydocs' ) . '</h4>
					<table class="ezd-perm-matrix__table">
						<thead>
							<tr>
								<th>' . esc_html__( 'Access level', 'eazydocs' ) . '</th>
								<th>' . esc_html__( 'Capabilities granted', 'eazydocs' ) . '</th>
							</tr>
						</thead>
						<tbody>
							<tr>
								<td>' . esc_html__( 'Documentation Author', 'eazydocs' ) . '</td>
								<td>' . esc_html__( 'Create, edit, publish and delete their own docs from the Docs Builder.', 'eazydocs' ) . '</td>
							</tr>
							<tr>
								<td>' . esc_html__( 'Documentation Manager', 'eazydocs' ) . '</td>
								<td>' . esc_html__( 'All author actions, plus editing and deleting others\' docs and private docs. Applied automatically to roles that can edit others\' posts.', 'eazydocs' ) . '</td>
							</tr>
							<tr>
								<td>' . esc_html__( 'Settings Manager', 'eazydocs' ) . '</td>
								<td>' . esc_html__( 'Open and change the EazyDocs settings screens.', 'eazydocs' ) . '</td>
							</tr>
							<tr>
								<td>' . esc_html__( 'Analytics Viewer', 'eazydocs' ) . '</td>
								<td>' . esc_html__( 'View documentation analytics and user feedback reports.', 'eazydocs' ) . '</td>
							</tr>
						</tbody>
					</table>
				</div>
			',
		),
	]
) );
