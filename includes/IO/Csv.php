<?php
/**
 * CSV import / export for EazyDocs documents.
 *
 * A flat, spreadsheet-friendly representation: one row per doc. Hierarchy is
 * encoded with the original `id` / `parent_id` columns and rebuilt on import by
 * remapping old IDs to the newly created ones. Unlike the Markdown tools this
 * needs no external library and works on PHP 7.4.
 *
 * @package EazyDocs\IO
 */

namespace EazyDocs\IO;

use WP_Error;

defined( 'ABSPATH' ) || exit;

class Csv {

	/** CSV column order. */
	const COLUMNS = [ 'id', 'parent_id', 'menu_order', 'status', 'title', 'slug', 'content' ];

	/** Max rows accepted on import. */
	const MAX_ROWS = 5000;

	/**
	 * Export a doc subtree to a CSV string.
	 *
	 * @param int $root_id Root doc ID (its whole subtree is included).
	 * @return string|WP_Error
	 */
	public static function export_to_string( $root_id ) {
		$root = get_post( $root_id );
		if ( ! $root || ! in_array( $root->post_type, [ 'docs', 'onepage-docs' ], true ) ) {
			return new WP_Error( 'ezd_csv_bad_root', __( 'Selected document could not be found.', 'eazydocs' ) );
		}

		$rows = [];
		self::collect( $root, $rows );

		$handle = fopen( 'php://temp', 'r+' );
		if ( false === $handle ) {
			return new WP_Error( 'ezd_csv_stream', __( 'Could not create the export stream.', 'eazydocs' ) );
		}

		// Pass the separator/enclosure/escape explicitly: the implicit $escape is
		// deprecated in PHP 8.4. An empty escape gives standard RFC-4180 quoting and
		// must match the value used on import for a clean round-trip.
		fputcsv( $handle, self::COLUMNS, ',', '"', '' );

		foreach ( $rows as $row ) {
			fputcsv(
				$handle,
				[
					(int) $row['id'],
					(int) $row['parent_id'],
					(int) $row['menu_order'],
					$row['status'],
					self::escape_cell( $row['title'] ),
					self::escape_cell( $row['slug'] ),
					self::escape_cell( $row['content'] ),
				],
				',',
				'"',
				''
			);
		}

		rewind( $handle );
		$csv = stream_get_contents( $handle );
		fclose( $handle );

		return $csv;
	}

	/**
	 * Recursively collect a doc and its children, in tree order.
	 *
	 * @param \WP_Post $post
	 * @param array    $rows By reference.
	 */
	private static function collect( $post, array &$rows ) {
		$rows[] = [
			'id'         => $post->ID,
			'parent_id'  => $post->post_parent,
			'menu_order' => $post->menu_order,
			'status'     => $post->post_status,
			'title'      => $post->post_title,
			'slug'       => $post->post_name,
			'content'    => $post->post_content,
		];

		$children = get_children(
			[
				'post_parent' => $post->ID,
				'post_type'   => $post->post_type,
				'post_status' => [ 'publish', 'draft', 'private' ],
				'orderby'     => 'menu_order title',
				'order'       => 'ASC',
			]
		);

		foreach ( $children as $child ) {
			// Object-level read enforcement: never export a private/draft descendant
			// the current user is not allowed to read. Skipping a doc also skips its
			// subtree, so no hidden branch leaks through the flat CSV.
			if ( ! current_user_can( 'read_post', $child->ID ) ) {
				continue;
			}
			self::collect( $child, $rows );
		}
	}

	/** Number of created/updated doc IDs reported back for "view" links. */
	const MAX_REPORTED_IDS = 50;

	/**
	 * Import docs from a CSV file.
	 *
	 * @param string $path        Path to an uploaded CSV (validated by caller).
	 * @param int    $parent_id   Parent doc to import under (0 = top level).
	 * @param string $post_status Fallback status when a row has none.
	 * @param string $mode        'create' (always new) or 'update' (match by id column).
	 * @return array|WP_Error { created, updated, failed, ids }
	 */
	public static function import_from_file( $path, $parent_id = 0, $post_status = 'draft', $mode = 'create' ) {
		$handle = fopen( $path, 'r' );
		if ( false === $handle ) {
			return new WP_Error( 'ezd_csv_read', __( 'Could not read the uploaded file.', 'eazydocs' ) );
		}

		$header = fgetcsv( $handle, 0, ',', '"', '' );
		if ( ! is_array( $header ) ) {
			fclose( $handle );
			return new WP_Error( 'ezd_csv_empty', __( 'The CSV file is empty.', 'eazydocs' ) );
		}

		$index = array_flip( array_map( 'strtolower', array_map( 'trim', $header ) ) );
		if ( ! isset( $index['title'] ) ) {
			fclose( $handle );
			return new WP_Error( 'ezd_csv_no_title', __( 'The CSV file must include a "title" column.', 'eazydocs' ) );
		}

		$fallback    = in_array( $post_status, [ 'draft', 'publish' ], true ) ? $post_status : 'draft';
		$can_publish = current_user_can( 'publish_docs' );
		$cell        = static function ( $data, $index, $key ) {
			return ( isset( $index[ $key ], $data[ $index[ $key ] ] ) ) ? $data[ $index[ $key ] ] : '';
		};

		$update  = ( 'update' === $mode );
		$map     = [];   // original id => new id (created rows only)
		$entries = [];   // newly created rows pending reparent: [ new, orig_parent ]
		$order   = 0;
		$read    = 0;
		$result  = [ 'created' => 0, 'updated' => 0, 'failed' => 0, 'ids' => [] ];

		while ( ( $data = fgetcsv( $handle, 0, ',', '"', '' ) ) !== false ) {
			if ( ++$read > self::MAX_ROWS ) {
				break;
			}
			// Skip fully blank rows.
			if ( 0 === count( array_filter( $data, static function ( $v ) {
				return '' !== trim( (string) $v );
			} ) ) ) {
				continue;
			}

			$row_status_raw = (string) $cell( $data, $index, 'status' );
			$row_status     = in_array( $row_status_raw, [ 'draft', 'publish', 'private' ], true ) ? $row_status_raw : $fallback;
			$menu_order     = (int) $cell( $data, $index, 'menu_order' );
			$orig_id        = (int) $cell( $data, $index, 'id' );

			// Post-status restriction: the status column is attacker-controlled row
			// data, so publish/private is only honoured when the importing user
			// actually holds publish_docs; otherwise the row is imported as a draft.
			if ( 'draft' !== $row_status && ! $can_publish ) {
				$row_status = 'draft';
			}

			// Update mode: refresh an existing doc in place (parent left untouched).
			if ( $update && $orig_id > 0 ) {
				$existing = get_post( $orig_id );
				if ( $existing && 'docs' === $existing->post_type ) {
					// Object-level authorization: edit_docs (the screen gate) does not
					// itself grant permission to modify every doc, so require edit_post
					// on the specific target before overwriting it.
					if ( ! current_user_can( 'edit_post', $orig_id ) ) {
						$result['failed']++;
						continue;
					}

					$updated = wp_update_post(
						[
							'ID'           => $orig_id,
							'post_title'   => sanitize_text_field( $cell( $data, $index, 'title' ) ),
							'post_content' => wp_slash( wp_kses_post( $cell( $data, $index, 'content' ) ) ),
							'post_status'  => $row_status,
						],
						true
					);
					if ( is_wp_error( $updated ) ) {
						$result['failed']++;
					} else {
						$result['updated']++;
						self::remember_id( $result, $orig_id );
					}
					continue;
				}
			}

			$new_id = wp_insert_post(
				[
					'post_title'   => sanitize_text_field( $cell( $data, $index, 'title' ) ),
					'post_name'    => sanitize_title( $cell( $data, $index, 'slug' ) ),
					'post_content' => wp_slash( wp_kses_post( $cell( $data, $index, 'content' ) ) ),
					'post_status'  => $row_status,
					'post_type'    => 'docs',
					'post_parent'  => (int) $parent_id,
					'menu_order'   => $menu_order ?: $order++,
				],
				true
			);

			if ( is_wp_error( $new_id ) ) {
				$result['failed']++;
				continue;
			}

			$result['created']++;
			self::remember_id( $result, (int) $new_id );

			if ( $orig_id ) {
				$map[ $orig_id ] = $new_id;
			}
			$entries[] = [
				'new'         => $new_id,
				'orig_parent' => (int) $cell( $data, $index, 'parent_id' ),
			];
		}

		fclose( $handle );

		if ( 0 === $result['created'] + $result['updated'] ) {
			return new WP_Error( 'ezd_csv_no_rows', __( 'No rows could be imported from the CSV.', 'eazydocs' ) );
		}

		// Second pass: reparent newly created rows whose original parent was also
		// created in this run (existing docs updated in place keep their parent).
		foreach ( $entries as $entry ) {
			if ( $entry['orig_parent'] && isset( $map[ $entry['orig_parent'] ] ) ) {
				wp_update_post(
					[
						'ID'          => $entry['new'],
						'post_parent' => $map[ $entry['orig_parent'] ],
					]
				);
			}
		}

		return $result;
	}

	/**
	 * Record a created/updated doc ID for the post-import "view" links (bounded).
	 *
	 * @param array $result By reference.
	 * @param int   $id
	 */
	private static function remember_id( array &$result, $id ) {
		if ( count( $result['ids'] ) < self::MAX_REPORTED_IDS ) {
			$result['ids'][] = (int) $id;
		}
	}

	/**
	 * Neutralise spreadsheet formula injection in an exported cell.
	 *
	 * Cells beginning with =, +, -, @, tab or CR can be executed as formulas when
	 * the CSV is opened in Excel/Sheets; prefixing a single quote defuses them.
	 *
	 * @param string $value
	 * @return string
	 */
	private static function escape_cell( $value ) {
		$value = (string) $value;
		if ( '' !== $value && in_array( $value[0], [ '=', '+', '-', '@', "\t", "\r" ], true ) ) {
			return "'" . $value;
		}
		return $value;
	}
}
