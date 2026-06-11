<?php
/**
 * Import Markdown into EazyDocs documents.
 *
 * Accepts a single .md file or a .zip of .md files. Zip entries are read in
 * memory (never extracted to disk) so directory-traversal / zip-slip is not
 * possible. Folder structure is mapped back to the parent/child doc hierarchy.
 *
 * @package EazyDocs\Markdown
 */

namespace EazyDocs\Markdown;

use WP_Error;
use ZipArchive;

defined( 'ABSPATH' ) || exit;

class Importer {

	/** Hard caps to keep an import bounded. */
	const MAX_ENTRIES   = 2000;
	const MAX_FILE_SIZE = 2097152; // 2 MB per markdown file.

	/** Number of created/updated doc IDs reported back for "view" links. */
	const MAX_REPORTED_IDS = 50;

	/**
	 * A fresh result accumulator shared across an import run.
	 *
	 * @return array
	 */
	private static function new_result() {
		return [
			'created' => 0,
			'updated' => 0,
			'failed'  => 0,
			'ids'     => [],
		];
	}

	/**
	 * Import a .zip bundle of Markdown files.
	 *
	 * @param string $zip_path    Path to an uploaded zip (validated by caller).
	 * @param int    $parent_id   Parent doc to import under (0 = top level).
	 * @param string $post_status 'draft' or 'publish'.
	 * @param string $mode        'create' (always new) or 'update' (match by eazydocs_id).
	 * @return array|WP_Error { created, updated, failed, ids } or error.
	 */
	public static function import_zip( $zip_path, $parent_id = 0, $post_status = 'draft', $mode = 'create' ) {
		if ( ! Converter::is_available() ) {
			return new WP_Error( 'ezd_md_unavailable', __( 'Markdown import requires PHP 8.1 or newer.', 'eazydocs' ) );
		}
		if ( ! class_exists( 'ZipArchive' ) ) {
			return new WP_Error( 'ezd_md_no_zip', __( 'The PHP Zip extension is required for import.', 'eazydocs' ) );
		}

		$zip = new ZipArchive();
		if ( true !== $zip->open( $zip_path, ZipArchive::RDONLY ) ) {
			return new WP_Error( 'ezd_md_bad_zip', __( 'The uploaded file is not a valid zip archive.', 'eazydocs' ) );
		}

		// Collect valid markdown entries only.
		$entries = [];
		$count   = min( $zip->numFiles, self::MAX_ENTRIES );
		for ( $i = 0; $i < $count; $i++ ) {
			$stat = $zip->statIndex( $i );
			if ( ! $stat ) {
				continue;
			}

			$name = $stat['name'];

			// Skip directories, traversal attempts and non-markdown files.
			if ( '' === $name || '/' === substr( $name, -1 ) ) {
				continue;
			}
			if ( false !== strpos( $name, '..' ) || '/' === $name[0] || false !== strpos( $name, "\0" ) ) {
				continue;
			}
			if ( ! preg_match( '/\.(md|markdown)$/i', $name ) ) {
				continue;
			}
			if ( $stat['size'] > self::MAX_FILE_SIZE ) {
				continue;
			}

			$entries[ $name ] = $i;
		}

		if ( empty( $entries ) ) {
			$zip->close();
			return new WP_Error( 'ezd_md_empty', __( 'No Markdown files were found in the archive.', 'eazydocs' ) );
		}

		// Process shallow paths first, and an index file before its siblings, so
		// folder docs exist before their children are attached.
		uksort( $entries, [ __CLASS__, 'sort_entries' ] );

		$dir_map = [ '' => (int) $parent_id ];
		$order   = 0;
		$result  = self::new_result();

		foreach ( $entries as $name => $index ) {
			$raw = $zip->getFromIndex( $index );
			if ( false === $raw ) {
				continue;
			}

			$parts = explode( '/', $name );
			$file  = array_pop( $parts );
			$dir   = implode( '/', $parts );

			$doc = self::parse( $raw, $file );

			if ( self::is_index_file( $file ) ) {
				// This file *is* the doc representing its folder.
				$grandparent = self::ensure_dir( $parts, $dir_map, (int) $parent_id, $post_status, $order, $result );
				$doc_id      = self::upsert_doc( $doc, $grandparent, $post_status, $order++, $mode, $result );
				if ( $doc_id ) {
					$dir_map[ $dir ] = $doc_id;
				}
			} else {
				$doc_parent = self::ensure_dir( $parts, $dir_map, (int) $parent_id, $post_status, $order, $result );
				self::upsert_doc( $doc, $doc_parent, $post_status, $order++, $mode, $result );
			}
		}

		$zip->close();

		return $result;
	}

	/**
	 * Import a single Markdown string as one doc.
	 *
	 * @param string $raw         Markdown source.
	 * @param string $filename    Original filename (for title fallback).
	 * @param int    $parent_id   Parent doc (0 = top level).
	 * @param string $post_status 'draft' or 'publish'.
	 * @param string $mode        'create' or 'update'.
	 * @return array|WP_Error { created, updated, failed, ids } or error.
	 */
	public static function import_markdown_string( $raw, $filename, $parent_id = 0, $post_status = 'draft', $mode = 'create' ) {
		if ( ! Converter::is_available() ) {
			return new WP_Error( 'ezd_md_unavailable', __( 'Markdown import requires PHP 8.1 or newer.', 'eazydocs' ) );
		}

		$result = self::new_result();
		$doc    = self::parse( (string) $raw, $filename );
		self::upsert_doc( $doc, (int) $parent_id, $post_status, 0, $mode, $result );

		if ( 0 === $result['created'] + $result['updated'] ) {
			return new WP_Error( 'ezd_md_insert', __( 'The document could not be created.', 'eazydocs' ) );
		}

		return $result;
	}

	/**
	 * Ensure every folder in $parts exists as a doc, creating stub parents as needed.
	 *
	 * @param array  $parts       Folder path segments.
	 * @param array  $dir_map     Map of folder path => doc ID (by reference).
	 * @param int    $root_parent Parent ID for the top level.
	 * @param string $post_status
	 * @param int    $order       Running menu order (by reference).
	 * @param array  $result      Result accumulator (by reference).
	 * @return int Doc ID representing the deepest folder (or $root_parent).
	 */
	private static function ensure_dir( array $parts, array &$dir_map, $root_parent, $post_status, &$order, array &$result ) {
		if ( empty( $parts ) ) {
			return $root_parent;
		}

		$path = implode( '/', $parts );
		if ( isset( $dir_map[ $path ] ) ) {
			return $dir_map[ $path ];
		}

		$parent_parts = $parts;
		$name         = array_pop( $parent_parts );
		$parent_id    = self::ensure_dir( $parent_parts, $dir_map, $root_parent, $post_status, $order, $result );

		// Implicit folder docs carry no source ID, so they are always created new.
		$new_id = self::upsert_doc(
			[ 'title' => self::humanize( $name ), 'html' => '', 'id' => 0 ],
			$parent_id,
			$post_status,
			$order++,
			'create',
			$result
		);
		if ( ! $new_id ) {
			return $parent_id;
		}

		$dir_map[ $path ] = $new_id;
		return $new_id;
	}

	/**
	 * Create — or, in update mode, update in place — a docs post.
	 *
	 * In 'update' mode a doc whose front-matter eazydocs_id still resolves to a
	 * live `docs` post has its title and content refreshed (parent/status left
	 * untouched), so an export → edit → re-import round-trip updates instead of
	 * duplicating. Everything else is inserted as a new doc.
	 *
	 * @param array  $doc         { title, html, id }.
	 * @param int    $parent_id   Parent for newly created docs.
	 * @param string $post_status 'draft' or 'publish'.
	 * @param int    $order       Menu order for newly created docs.
	 * @param string $mode        'create' or 'update'.
	 * @param array  $result      Result accumulator (by reference).
	 * @return int New/existing doc ID, or 0 on failure.
	 */
	private static function upsert_doc( array $doc, $parent_id, $post_status, $order, $mode, array &$result ) {
		$post_status = in_array( $post_status, [ 'draft', 'publish' ], true ) ? $post_status : 'draft';
		$source_id   = isset( $doc['id'] ) ? (int) $doc['id'] : 0;

		if ( 'update' === $mode && $source_id > 0 ) {
			$existing = get_post( $source_id );
			if ( $existing && 'docs' === $existing->post_type ) {
				$updated = wp_update_post(
					[
						'ID'           => $source_id,
						'post_title'   => sanitize_text_field( $doc['title'] ),
						'post_content' => wp_slash( $doc['html'] ),
					],
					true
				);
				if ( is_wp_error( $updated ) ) {
					$result['failed']++;
					return 0;
				}
				$result['updated']++;
				self::remember_id( $result, $source_id );
				return $source_id;
			}
		}

		$new_id = wp_insert_post(
			[
				'post_title'   => sanitize_text_field( $doc['title'] ),
				'post_content' => wp_slash( $doc['html'] ),
				'post_status'  => $post_status,
				'post_type'    => 'docs',
				'post_parent'  => (int) $parent_id,
				'menu_order'   => (int) $order,
			],
			true
		);

		if ( is_wp_error( $new_id ) ) {
			$result['failed']++;
			return 0;
		}

		$result['created']++;
		self::remember_id( $result, (int) $new_id );
		return (int) $new_id;
	}

	/**
	 * Record a created/updated doc ID for the post-import "view" links (bounded).
	 *
	 * @param array $result By reference.
	 * @param int   $id
	 */
	private static function remember_id( array &$result, $id ) {
		if ( count( $result['ids'] ) < self::MAX_REPORTED_IDS ) {
			$result['ids'][] = $id;
		}
	}

	/**
	 * Parse a Markdown document: pull optional front matter, resolve a title and
	 * convert the body to sanitised HTML.
	 *
	 * @param string $raw      Markdown source.
	 * @param string $filename Filename for title fallback.
	 * @return array { title, html, id }
	 */
	private static function parse( $raw, $filename ) {
		$raw   = (string) $raw;
		$title = '';
		$id    = 0;

		// Optional YAML-ish front matter at the very top: --- ... ---
		// The leading UTF-8 BOM (if any) is matched as one optional group so files
		// without a BOM — the common case, including our own exports — still match.
		if ( preg_match( '/^(?:\xEF\xBB\xBF)?---\s*\n(.*?)\n---\s*\n/s', $raw, $m ) ) {
			if ( preg_match( '/^\s*title\s*:\s*(.+)$/mi', $m[1], $tm ) ) {
				$title = trim( $tm[1], " \t\"'" );
			}
			// eazydocs_id lets an export → re-import round-trip update in place.
			if ( preg_match( '/^\s*eazydocs_id\s*:\s*(\d+)\s*$/mi', $m[1], $im ) ) {
				$id = (int) $im[1];
			}
			$raw = substr( $raw, strlen( $m[0] ) );
		}

		// Fall back to the first H1, then the filename.
		if ( '' === $title && preg_match( '/^\s*#\s+(.+)$/m', $raw, $hm ) ) {
			$title = trim( $hm[1] );
		}
		if ( '' === $title ) {
			$title = self::humanize( preg_replace( '/\.(md|markdown)$/i', '', $filename ) );
		}

		return [
			'title' => $title,
			'html'  => Converter::markdown_to_html( $raw ),
			'id'    => $id,
		];
	}

	/**
	 * Turn a slug/folder name into a human-readable title.
	 *
	 * @param string $name
	 * @return string
	 */
	private static function humanize( $name ) {
		$name = str_replace( [ '-', '_' ], ' ', (string) $name );
		$name = trim( preg_replace( '/\s+/', ' ', $name ) );
		return $name !== '' ? ucwords( $name ) : __( 'Untitled', 'eazydocs' );
	}

	/**
	 * Whether a filename is a folder index file.
	 *
	 * @param string $file
	 * @return bool
	 */
	private static function is_index_file( $file ) {
		$lower = strtolower( $file );
		return 'index.md' === $lower || '_index.md' === $lower || 'index.markdown' === $lower;
	}

	/**
	 * Sort entries: shallower paths first, index files before siblings.
	 *
	 * @param string $a
	 * @param string $b
	 * @return int
	 */
	private static function sort_entries( $a, $b ) {
		$depth_a = substr_count( $a, '/' );
		$depth_b = substr_count( $b, '/' );
		if ( $depth_a !== $depth_b ) {
			return $depth_a <=> $depth_b;
		}

		$a_index = self::is_index_file( basename( $a ) ) ? 0 : 1;
		$b_index = self::is_index_file( basename( $b ) ) ? 0 : 1;
		if ( $a_index !== $b_index ) {
			return $a_index <=> $b_index;
		}

		return strcmp( $a, $b );
	}
}
