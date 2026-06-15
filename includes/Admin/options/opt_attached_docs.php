<?php
/**
 * Attached Documents Metabox
 * Adds an "Attached Documents" section to the per-doc EazyDocs meta box,
 * letting authors attach downloadable files to a documentation page.
 *
 * Requires the `eazydocs_meta` metabox to be registered first
 * (see opt_footnotes.php).
 */
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$meta = 'eazydocs_meta';

CSF::createSection( $meta, array(
	'id'     => 'ezd_attached_docs',
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
						'accept' => '.pdf,.zip,.docx,.txt',
					),
				),
			),
			'button_title' => esc_html__( 'Add New File', 'eazydocs' ),
			'class'        => 'eazydocs-pro-notice layout-inline',
		),
	),
) );
