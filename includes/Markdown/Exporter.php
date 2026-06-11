<?php
/**
 * Export EazyDocs documents to a Markdown bundle (.zip).
 *
 * The doc hierarchy is mirrored as folders: a doc with children becomes a folder
 * containing its own `index.md` plus its children; a leaf doc becomes `slug.md`.
 * This mirrors the layout MkDocs-style static generators expect.
 *
 * @package EazyDocs\Markdown
 */

namespace EazyDocs\Markdown;

use WP_Error;
use ZipArchive;

defined( 'ABSPATH' ) || exit;

class Exporter {

	/**
	 * Build a Markdown .zip for a doc and all of its descendants.
	 *
	 * @param int $root_id Root doc ID to export (its whole subtree is included).
	 * @return array|WP_Error { file: tmp path, name: download filename } or error.
	 */
	public static function export_to_zip( $root_id ) {
		if ( ! Converter::is_available() ) {
			return new WP_Error( 'ezd_md_unavailable', __( 'Markdown export requires PHP 8.1 or newer.', 'eazydocs' ) );
		}

		if ( ! class_exists( 'ZipArchive' ) ) {
			return new WP_Error( 'ezd_md_no_zip', __( 'The PHP Zip extension is required for export.', 'eazydocs' ) );
		}

		$root = get_post( $root_id );
		if ( ! $root || ! in_array( $root->post_type, [ 'docs', 'onepage-docs' ], true ) ) {
			return new WP_Error( 'ezd_md_bad_root', __( 'Selected document could not be found.', 'eazydocs' ) );
		}

		$files = [];
		self::collect( $root, '', $files );

		$tmp = wp_tempnam( 'ezd-md-export' );
		$zip = new ZipArchive();

		if ( true !== $zip->open( $tmp, ZipArchive::OVERWRITE ) ) {
			return new WP_Error( 'ezd_md_zip_open', __( 'Could not create the export archive.', 'eazydocs' ) );
		}

		foreach ( $files as $path => $content ) {
			$zip->addFromString( $path, $content );
		}

		$zip->close();

		$name = 'eazydocs-' . sanitize_title( $root->post_title ?: 'export' ) . '-' . gmdate( 'Ymd' ) . '.zip';

		return [
			'file' => $tmp,
			'name' => $name,
		];
	}

	/**
	 * Recursively collect a doc and its children into the file map.
	 *
	 * @param \WP_Post $post      Current doc.
	 * @param string   $base_path Folder path prefix within the archive.
	 * @param array    $files     File map (path => content), by reference.
	 */
	private static function collect( $post, $base_path, array &$files ) {
		$slug     = self::safe_name( $post );
		$children = get_children(
			[
				'post_parent' => $post->ID,
				'post_type'   => $post->post_type,
				// Match the CSV exporter so a Markdown export is a complete backup,
				// not just the published subset (drafts/private were silently dropped).
				'post_status' => [ 'publish', 'draft', 'private' ],
				'orderby'     => 'menu_order title',
				'order'       => 'ASC',
			]
		);

		if ( ! empty( $children ) ) {
			$dir = $base_path . $slug . '/';
			$files[ $dir . 'index.md' ] = self::to_markdown( $post );

			foreach ( $children as $child ) {
				self::collect( $child, $dir, $files );
			}
		} else {
			$files[ $base_path . $slug . '.md' ] = self::to_markdown( $post );
		}
	}

	/**
	 * Build the Markdown file body (front matter + converted content).
	 *
	 * @param \WP_Post $post
	 * @return string
	 */
	private static function to_markdown( $post ) {
		// Render blocks and shortcodes to HTML before converting to Markdown.
		$html     = do_shortcode( do_blocks( $post->post_content ) );
		$markdown = Converter::html_to_markdown( $html );

		$title = str_replace( '"', "'", (string) $post->post_title );

		$front_matter  = "---\n";
		$front_matter .= 'title: "' . $title . "\"\n";
		$front_matter .= 'eazydocs_id: ' . (int) $post->ID . "\n";
		$front_matter .= "---\n\n";

		return $front_matter . $markdown . "\n";
	}

	/**
	 * Filesystem-safe, hierarchy-unique name for a doc (its slug, falling back to ID).
	 *
	 * @param \WP_Post $post
	 * @return string
	 */
	private static function safe_name( $post ) {
		$name = $post->post_name ? $post->post_name : sanitize_title( $post->post_title );
		$name = sanitize_file_name( $name );
		return $name !== '' ? $name : 'doc-' . (int) $post->ID;
	}
}
