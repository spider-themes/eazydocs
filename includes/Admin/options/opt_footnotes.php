<?php
/**
 * Footnotes & References Settings
 * Configure how footnotes appear in your documentation.
 */
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

// Footnotes
CSF::createSection( $prefix, array(
	'id'     => 'ezd_footnotes',
	'title'  => esc_html__( 'Footnotes', 'eazydocs' ),
	'icon'   => 'dashicons dashicons-text-page',
	'fields' => [
		ezd_csf_switcher_field([
			'id'         => 'is_footnotes_heading',
			'title'      => esc_html__( 'Footnotes Section', 'eazydocs' ),
			'subtitle'   => esc_html__( 'Display a dedicated footnotes section at the bottom of documentation pages.', 'eazydocs' ),
			'text_width' => 72,
			'default'    => true,
			'class'      => 'eazydocs-promax-notice active-theme-docy active-theme-docly'
		]),

		array(
			'id'         => 'footnotes_heading_text',
			'type'       => 'text',
			'title'      => esc_html__( 'Section Title', 'eazydocs' ),
			'subtitle'   => esc_html__( 'Heading text displayed above the footnotes list.', 'eazydocs' ),
			'dependency' => array(
				array( 'is_footnotes_heading', '==', 'true' ),
			),
			'default'    => esc_html__( 'Footnotes', 'eazydocs' ),
			'class'      => 'eazydocs-promax-notice active-theme-docy active-theme-docly'
		),

		array(
			'id'         => 'footnotes_layout',
			'title'      => esc_html__( 'Default Display', 'eazydocs' ),
			'type'       => 'radio',
			'options'    => [
				'collapsed' => esc_html__( 'Collapsed (Click to expand)', 'eazydocs' ),
				'expanded'  => esc_html__( 'Expanded (Always visible)', 'eazydocs' ),
			],
			'subtitle'   => esc_html__( 'Choose how footnotes appear when the page loads.', 'eazydocs' ),
			'default'    => 'collapsed',
			'class'      => 'eazydocs-promax-notice active-theme-docy active-theme-docly',
			'dependency' => array(
				array( 'is_footnotes_heading', '==', 'true' ),
			)
		),

		array(
			'id'       => 'footnotes_column',
			'type'     => 'select',
			'title'    => esc_html__( 'Column Layout', 'eazydocs' ),
			'subtitle' => esc_html__( 'Display footnotes in multiple columns for better readability.', 'eazydocs' ),
			'options'  => [
				'1' => esc_html__( '1 Column', 'eazydocs' ),
				'2' => esc_html__( '2 Columns', 'eazydocs' ),
				'3' => esc_html__( '3 Columns', 'eazydocs' ),
				'4' => esc_html__( '4 Columns', 'eazydocs' ),
				'5' => esc_html__( '5 Columns', 'eazydocs' ),
				'6' => esc_html__( '6 Columns', 'eazydocs' ),
			],
			'chosen'   => true,
			'multiple' => false,
			'default'  => '1',
			'class'    => 'eazydocs-promax-notice active-theme-docy active-theme-docly'
		),

		array(
			'id'         => 'ezdocs_footnote_shortcode1',
			'type'       => 'text',
			'title'      => esc_html__( 'Footnote Shortcode', 'eazydocs' ),
			/* translators: %1$s - opening link tag, %2$s - closing link tag */
			'subtitle'   => sprintf( esc_html__( 'Add clickable footnote references in your content. %1$sView documentation%2$s', 'eazydocs' ),
				'<a href="https://tinyurl.com/2ewlorze" target="_blank">', '</a>' ),
			'desc'       => esc_html__( 'Usage example:', 'eazydocs' )
			                . '<br><code>[reference number="1"]Your footnote text here[/reference]</code>',
			'default'    => '[reference]',
			'attributes' => array(
				'readonly' => 'readonly',
			),
			'class'      => 'eazydocs-promax-notice active-theme-docy active-theme-docly'
		),
	]
) );

$meta = 'eazydocs_meta';
// Register a custom meta box for the Docs post type.
CSF::createMetabox( $meta, array(
	'title'     => esc_html__( 'EazyDocs :: Options', 'eazydocs' ),
	'post_type' => 'docs',
	'data_type' => 'unserialize',
	'priority'  => 'default'
) );

// Create the fields conditionally.
CSF::createSection( $meta, array(
	'id'     => 'ezd_footnotes',
	'title'  => esc_html__( 'Footnotes', 'eazydocs' ),
	'fields' => array(
		array(
			'id'     => 'footnotes_colum_opt',
			'type'   => 'fieldset',
			'title'  => esc_html__( 'Footnotes Column', 'eazydocs' ),
			'desc'   => esc_html__( 'Select Default to use the value from the settings, or choose Custom to manually select the column number from the dropdown.', 'eazydocs' ),
			'fields' => array(
				array(
					'id'       => 'footnotes_column_source',
					'type'     => 'select',
					'title'    => null,
					'options'  => [
						'default' => esc_html__( 'Default', 'eazydocs' ),
						'custom'  => esc_html__( 'Custom', 'eazydocs' ),
					],
					'multiple' => false,
					'default'  => 'default',
					'class'    => 'eazydocs-promax-notice active-theme-docy active-theme-docly'
				),
				array(
					'id'         => 'footnotes_column',
					'type'       => 'select',
					'title'      => esc_html__( 'Set Column', 'eazydocs' ),
					'options'    => array(
						'1' => esc_html__( '1 Column', 'eazydocs' ),
						'2' => esc_html__( '2 Columns', 'eazydocs' ),
						'3' => esc_html__( '3 Columns', 'eazydocs' ),
						'4' => esc_html__( '4 Columns', 'eazydocs' ),
						'5' => esc_html__( '5 Columns', 'eazydocs' ),
						'6' => esc_html__( '6 Columns', 'eazydocs' )
					),
					'default'    => ezd_get_opt( 'footnotes_column', 3 ),
					'class'      => 'eazydocs-promax-notice active-theme-docy active-theme-docly layout-inline',
					'dependency' => array(
						array( 'footnotes_column_source', '==', 'custom' ),
					)
				)
			)
		),
	)
) );


//
CSF::createSection( $meta, array(
	'id'     => 'ezd_attached_docs', // unique section ID
	'title'  => esc_html__( 'Attached Documents', 'eazydocs' ),
	'desc'   => esc_html__( 'Upload and manage the document files you want to attach to this doc.', 'eazydocs' ),

	'fields' => array(

		array(
			'id'           => 'ezd_doc_attached_files',
			'type'         => 'repeater',
			'title'        => esc_html__( 'Attached Files', 'eazydocs' ),
			'subtitle'     => esc_html__( 'Add one or more files to attach with this documentation page.', 'eazydocs' ),
			'desc'         => esc_html__( 'You can upload PDF, DOC, DOCX, or TXT files. Each file will be listed as an attachment for this document.', 'eazydocs' ),

			'fields'       => array(

				array(
					'id'         => 'ezd_upload_doc_attachment',
					'type'       => 'upload',
					'title'      => esc_html__( 'Upload File', 'eazydocs' ),
					'subtitle'   => esc_html__( 'Select or upload the file you want to attach.', 'eazydocs' ),
					'sanitize'   => false, // prevent URL stripping
					'attributes' => array(
						'accept' => 'pdf', 'zip', 'docx', 'txt',
					),
				),

			),

			'button_title' => esc_html__( 'Add New File', 'eazydocs' ),
			'class'        => 'eazydocs-pro-notice layout-inline',
		),

	),
) );
