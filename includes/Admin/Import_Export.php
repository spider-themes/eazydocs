<?php
/**
 * Import / Export admin screen and handlers.
 *
 * A single, tabbed "Tools" surface for getting docs in and out of EazyDocs:
 * Import (Markdown / CSV), Export (Markdown .zip / CSV) and Migrate (from
 * BetterDocs). Every state-changing request verifies a nonce and capability,
 * sanitises input and validates the uploaded file.
 *
 * @package EazyDocs\Admin
 */

namespace EazyDocs\Admin;

use EazyDocs\Markdown\Converter;
use EazyDocs\Markdown\Exporter;
use EazyDocs\Markdown\Importer;
use EazyDocs\IO\Csv;

defined( 'ABSPATH' ) || exit;

class Import_Export {

	/** Capability required to use the screen. */
	const CAPABILITY = 'edit_docs';

	/** Max upload size accepted for import (20 MB). */
	const MAX_UPLOAD = 20971520;

	/** Admin page slug. */
	const PAGE_SLUG = 'eazydocs-import-export';

	public function __construct() {
		add_action( 'admin_menu', [ $this, 'register_menu' ], 30 );
		add_action( 'admin_enqueue_scripts', [ $this, 'enqueue_assets' ] );
		add_action( 'admin_init', [ $this, 'redirect_legacy_migration' ] );
		add_action( 'admin_post_ezd_md_export', [ $this, 'handle_export' ] );
		add_action( 'admin_post_ezd_md_import', [ $this, 'handle_import' ] );
		add_action( 'admin_post_ezd_csv_sample', [ $this, 'handle_sample_csv' ] );
	}

	/**
	 * Whether the current user may use the screen.
	 *
	 * @return bool
	 */
	private function user_can_manage() {
		return current_user_can( self::CAPABILITY ) || current_user_can( 'manage_options' );
	}

	/**
	 * Register the submenu page.
	 */
	public function register_menu() {
		add_submenu_page(
			'eazydocs',
			esc_html__( 'Import / Export', 'eazydocs' ),
			esc_html__( 'Import / Export', 'eazydocs' ),
			self::CAPABILITY,
			self::PAGE_SLUG,
			[ $this, 'render_page' ]
		);
	}

	/**
	 * Send the retired Migration page to the unified Migrate tab so there is a
	 * single destination for getting docs in and out.
	 */
	public function redirect_legacy_migration() {
		if ( isset( $_GET['page'] ) && 'eazydocs-migration' === sanitize_key( wp_unslash( $_GET['page'] ) ) ) {
			wp_safe_redirect( $this->tab_url( 'migrate' ) );
			exit;
		}
	}

	/**
	 * Load the page stylesheet plus SweetAlert (used by the Migrate tab) only on
	 * this screen.
	 *
	 * @param string $hook Current admin page hook suffix.
	 */
	public function enqueue_assets( $hook ) {
		if ( function_exists( 'ezd_admin_pages' ) && ! ezd_admin_pages( self::PAGE_SLUG ) ) {
			return;
		}

		wp_enqueue_style( 'eazydocs-tools', EZD_STYLES . 'admin/import-export.css', [ 'eazydocs-admin' ], EZD_VERSION );
		wp_enqueue_script( 'sweetalert' );
	}

	/**
	 * Resolve the active tab from the request.
	 *
	 * @return string One of import|export|migrate.
	 */
	private function current_tab() {
		$tab = isset( $_GET['tab'] ) ? sanitize_key( wp_unslash( $_GET['tab'] ) ) : 'import';
		return in_array( $tab, [ 'import', 'export', 'migrate' ], true ) ? $tab : 'import';
	}

	/**
	 * URL for one of the page's tabs.
	 *
	 * @param string $tab
	 * @return string
	 */
	private function tab_url( $tab ) {
		return add_query_arg(
			[
				'page' => self::PAGE_SLUG,
				'tab'  => $tab,
			],
			admin_url( 'admin.php' )
		);
	}

	/** Help-centre article for the screen. */
	const DOC_URL = 'https://helpdesk.spider-themes.net/docs/eazydocs-wordpress-plugin/';

	/**
	 * The whole doc tree (bounded) as a flat, depth-tagged list in tree order, so
	 * any doc — not just a top-level one — can be picked as an export root or an
	 * import parent.
	 *
	 * @return array[] List of [ id, title, depth ].
	 */
	private function get_docs_tree_options() {
		$docs = get_posts(
			[
				'post_type'      => 'docs',
				'post_status'    => [ 'publish', 'draft', 'private' ],
				'orderby'        => 'menu_order title',
				'order'          => 'ASC',
				'posts_per_page' => 800,
				'no_found_rows'  => true,
			]
		);

		// Object-level read filter: the screen capability (edit_docs) does not by
		// itself grant read access to every private/draft doc, so only offer docs
		// the current user is actually allowed to read.
		$docs = array_values(
			array_filter(
				$docs,
				static function ( $doc ) {
					return current_user_can( 'read_post', $doc->ID );
				}
			)
		);

		if ( empty( $docs ) ) {
			return [];
		}

		$id_set = [];
		foreach ( $docs as $doc ) {
			$id_set[ $doc->ID ] = true;
		}

		// Group by parent; treat a doc whose parent fell outside the set as a root.
		$by_parent = [];
		foreach ( $docs as $doc ) {
			$parent = ( $doc->post_parent && isset( $id_set[ $doc->post_parent ] ) ) ? $doc->post_parent : 0;
			$by_parent[ $parent ][] = $doc;
		}

		$out  = [];
		$walk = function ( $parent_id, $depth ) use ( &$walk, &$out, $by_parent ) {
			if ( empty( $by_parent[ $parent_id ] ) ) {
				return;
			}
			foreach ( $by_parent[ $parent_id ] as $doc ) {
				$out[] = [
					'id'    => (int) $doc->ID,
					'title' => $doc->post_title,
					'depth' => $depth,
				];
				$walk( $doc->ID, $depth + 1 );
			}
		};
		$walk( 0, 0 );

		return $out;
	}

	/**
	 * Indented label for a tree option (non-breaking spaces keep the indent in a
	 * native &lt;select&gt;).
	 *
	 * @param array $option [ title, depth ].
	 * @return string
	 */
	private function tree_label( $option ) {
		$indent = $option['depth'] > 0 ? str_repeat( "\xC2\xA0\xC2\xA0\xC2\xA0", $option['depth'] ) . '↳ ' : '';
		return $indent . $option['title'];
	}

	/**
	 * Render the admin screen.
	 */
	public function render_page() {
		if ( ! $this->user_can_manage() ) {
			wp_die( esc_html__( 'You do not have permission to access this page.', 'eazydocs' ) );
		}

		$docs      = $this->get_docs_tree_options();
		$available = Converter::is_available();
		$tab       = $this->current_tab();
		?>
		<div class="wrap ezd-tools">
			<div class="ezd-tools-hero">
				<span class="ezd-tools-hero__icon dashicons dashicons-migrate"></span>
				<div class="ezd-tools-hero__text">
					<h1><?php esc_html_e( 'Import &amp; Export', 'eazydocs' ); ?></h1>
					<p><?php esc_html_e( 'Move documentation in and out of EazyDocs — import Markdown or CSV, download a portable backup, or migrate from another plugin.', 'eazydocs' ); ?></p>
				</div>
			</div>

			<?php $this->render_result_notice(); ?>

			<?php if ( ! $available ) : ?>
				<div class="notice notice-warning ezd-tools-notice">
					<p><?php esc_html_e( 'Markdown import/export needs PHP 8.1 or newer, so the Markdown option is disabled. CSV import/export still works.', 'eazydocs' ); ?></p>
				</div>
			<?php endif; ?>

			<h2 class="nav-tab-wrapper ezd-tools-tabs">
				<a href="<?php echo esc_url( $this->tab_url( 'import' ) ); ?>" class="nav-tab <?php echo 'import' === $tab ? 'nav-tab-active' : ''; ?>">
					<span class="dashicons dashicons-upload"></span><?php esc_html_e( 'Import', 'eazydocs' ); ?>
				</a>
				<a href="<?php echo esc_url( $this->tab_url( 'export' ) ); ?>" class="nav-tab <?php echo 'export' === $tab ? 'nav-tab-active' : ''; ?>">
					<span class="dashicons dashicons-download"></span><?php esc_html_e( 'Export', 'eazydocs' ); ?>
				</a>
				<a href="<?php echo esc_url( $this->tab_url( 'migrate' ) ); ?>" class="nav-tab <?php echo 'migrate' === $tab ? 'nav-tab-active' : ''; ?>">
					<span class="dashicons dashicons-randomize"></span><?php esc_html_e( 'Migrate', 'eazydocs' ); ?>
				</a>
			</h2>

			<div class="ezd-tools-panel">
				<?php
				if ( 'export' === $tab ) {
					$this->render_export_tab( $docs, $available );
				} elseif ( 'migrate' === $tab ) {
					$this->render_migrate_tab();
				} else {
					$this->render_import_tab( $docs, $available );
				}
				?>
			</div>
		</div>
		<?php
		$this->render_inline_behaviour();
	}

	/**
	 * Import tab.
	 *
	 * @param array[] $docs      Doc tree options ([ id, title, depth ]).
	 * @param bool    $available Markdown availability.
	 */
	private function render_import_tab( $docs, $available ) {
		$sample_url = wp_nonce_url( admin_url( 'admin-post.php?action=ezd_csv_sample' ), 'ezd_csv_sample' );
		?>
		<div class="ezd-tools-card">
			<div class="ezd-tools-card__head">
				<h2><?php esc_html_e( 'Import documentation', 'eazydocs' ); ?></h2>
				<p>
					<?php esc_html_e( 'Upload a single .md file, a .zip of Markdown files (folders become parent docs; index.md is the folder\'s own page), or a .csv exported from EazyDocs.', 'eazydocs' ); ?>
					<a href="<?php echo esc_url( self::DOC_URL ); ?>" target="_blank" rel="noopener"><?php esc_html_e( 'Learn more', 'eazydocs' ); ?></a>
				</p>
			</div>
			<form class="ezd-tools-form js-ezd-tools-form" method="post" enctype="multipart/form-data" action="<?php echo esc_url( admin_url( 'admin-post.php' ) ); ?>">
				<input type="hidden" name="action" value="ezd_md_import">
				<?php wp_nonce_field( 'ezd_md_import' ); ?>

				<div class="ezd-tools-field">
					<label for="ezd-md-file"><?php esc_html_e( 'File', 'eazydocs' ); ?></label>
					<input type="file" name="ezd_md_file" id="ezd-md-file" accept=".md,.markdown,.zip,.csv" required>
					<span class="ezd-tools-help">
						<?php esc_html_e( 'Accepts .md, .markdown, .zip or .csv — up to 20 MB.', 'eazydocs' ); ?>
						<a href="<?php echo esc_url( $sample_url ); ?>"><?php esc_html_e( 'Download a sample CSV', 'eazydocs' ); ?></a>
					</span>
				</div>

				<div class="ezd-tools-field-row">
					<div class="ezd-tools-field">
						<label for="ezd-md-parent"><?php esc_html_e( 'Import under', 'eazydocs' ); ?></label>
						<select name="parent_id" id="ezd-md-parent">
							<option value="0"><?php esc_html_e( '— Top level —', 'eazydocs' ); ?></option>
							<?php foreach ( $docs as $doc ) : ?>
								<option value="<?php echo esc_attr( $doc['id'] ); ?>"><?php echo esc_html( $this->tree_label( $doc ) ); ?></option>
							<?php endforeach; ?>
						</select>
					</div>

					<div class="ezd-tools-field">
						<label for="ezd-md-status"><?php esc_html_e( 'Status for new docs', 'eazydocs' ); ?></label>
						<select name="post_status" id="ezd-md-status">
							<option value="draft"><?php esc_html_e( 'Draft', 'eazydocs' ); ?></option>
							<option value="publish"><?php esc_html_e( 'Published', 'eazydocs' ); ?></option>
						</select>
					</div>
				</div>

				<div class="ezd-tools-field">
					<label for="ezd-md-duplicate"><?php esc_html_e( 'If a document already exists', 'eazydocs' ); ?></label>
					<select name="duplicate_mode" id="ezd-md-duplicate">
						<option value="create"><?php esc_html_e( 'Always create new documents', 'eazydocs' ); ?></option>
						<option value="update"><?php esc_html_e( 'Update the existing document', 'eazydocs' ); ?></option>
					</select>
					<span class="ezd-tools-help"><?php esc_html_e( 'Re-importing an EazyDocs export carries each doc\'s ID, so "Update" refreshes the original instead of creating a duplicate.', 'eazydocs' ); ?></span>
				</div>

				<p class="ezd-tools-help ezd-tools-note">
					<span class="dashicons dashicons-info-outline"></span>
					<?php esc_html_e( 'Images are kept as links to their original location — they are not uploaded to your Media Library during import.', 'eazydocs' ); ?>
				</p>

				<div class="ezd-tools-actions">
					<button type="submit" class="button button-primary button-hero js-ezd-tools-submit">
						<span class="dashicons dashicons-upload"></span>
						<span class="js-ezd-tools-label"><?php esc_html_e( 'Import', 'eazydocs' ); ?></span>
					</button>
				</div>
			</form>
		</div>
		<?php
	}

	/**
	 * Export tab.
	 *
	 * @param array[] $docs      Doc tree options ([ id, title, depth ]).
	 * @param bool    $available Markdown availability.
	 */
	private function render_export_tab( $docs, $available ) {
		?>
		<div class="ezd-tools-card">
			<div class="ezd-tools-card__head">
				<h2><?php esc_html_e( 'Export documentation', 'eazydocs' ); ?></h2>
				<p>
					<?php esc_html_e( 'Download any document and its sub-tree as Markdown files (.zip, folders mirror the hierarchy) or a flat CSV spreadsheet. All statuses — published, draft and private — are included.', 'eazydocs' ); ?>
					<a href="<?php echo esc_url( self::DOC_URL ); ?>" target="_blank" rel="noopener"><?php esc_html_e( 'Learn more', 'eazydocs' ); ?></a>
				</p>
			</div>

			<?php if ( empty( $docs ) ) : ?>
				<p class="ezd-tools-empty"><?php esc_html_e( 'There are no documents to export yet.', 'eazydocs' ); ?></p>
			<?php else : ?>
				<form class="ezd-tools-form js-ezd-tools-form" method="post" action="<?php echo esc_url( admin_url( 'admin-post.php' ) ); ?>">
					<input type="hidden" name="action" value="ezd_md_export">
					<?php wp_nonce_field( 'ezd_md_export' ); ?>

					<div class="ezd-tools-field-row">
						<div class="ezd-tools-field">
							<label for="ezd-md-root"><?php esc_html_e( 'Document to export', 'eazydocs' ); ?></label>
							<select name="root_id" id="ezd-md-root">
								<?php foreach ( $docs as $doc ) : ?>
									<option value="<?php echo esc_attr( $doc['id'] ); ?>"><?php echo esc_html( $this->tree_label( $doc ) ); ?></option>
								<?php endforeach; ?>
							</select>
							<span class="ezd-tools-help"><?php esc_html_e( 'The selected document and everything beneath it is exported.', 'eazydocs' ); ?></span>
						</div>

						<div class="ezd-tools-field">
							<label for="ezd-export-format"><?php esc_html_e( 'Format', 'eazydocs' ); ?></label>
							<select name="format" id="ezd-export-format">
								<?php if ( $available ) : ?>
									<option value="markdown"><?php esc_html_e( 'Markdown (.zip)', 'eazydocs' ); ?></option>
								<?php endif; ?>
								<option value="csv"><?php esc_html_e( 'CSV (.csv)', 'eazydocs' ); ?></option>
							</select>
							<span class="ezd-tools-help"><?php esc_html_e( 'Images stay linked to this site; they are not bundled into the file.', 'eazydocs' ); ?></span>
						</div>
					</div>

					<div class="ezd-tools-actions">
						<button type="submit" class="button button-primary button-hero js-ezd-tools-submit">
							<span class="dashicons dashicons-download"></span>
							<span class="js-ezd-tools-label"><?php esc_html_e( 'Export', 'eazydocs' ); ?></span>
						</button>
					</div>
				</form>
			<?php endif; ?>
		</div>
		<?php
	}

	/**
	 * Migrate tab — reuses the existing BetterDocs migration screen.
	 *
	 * Migration rebuilds the whole doc tree, so it keeps the higher capability the
	 * standalone Migration page required rather than the screen's edit_docs.
	 */
	private function render_migrate_tab() {
		$settings_cap = function_exists( 'ezd_get_opt' ) ? ezd_get_opt( 'settings-edit-access', 'manage_options' ) : 'manage_options';

		if ( ! current_user_can( $settings_cap ) && ! current_user_can( 'manage_options' ) ) {
			echo '<div class="ezd-tools-card"><p class="ezd-tools-empty">' . esc_html__( 'You do not have permission to run a migration.', 'eazydocs' ) . '</p></div>';
			return;
		}

		$migration = __DIR__ . '/migration.php';
		if ( file_exists( $migration ) ) {
			include $migration;
		}
	}

	/**
	 * Render the post-import result notice from the stored transient (then clear it).
	 */
	private function render_result_notice() {
		$key    = 'ezd_md_import_result_' . get_current_user_id();
		$result = get_transient( $key );
		if ( false === $result ) {
			return;
		}
		delete_transient( $key );

		if ( ! empty( $result['error'] ) ) {
			?>
			<div class="notice notice-error is-dismissible ezd-tools-notice">
				<p><?php echo esc_html( $result['error'] ); ?></p>
			</div>
			<?php
			return;
		}

		$created = isset( $result['created'] ) ? (int) $result['created'] : 0;
		$updated = isset( $result['updated'] ) ? (int) $result['updated'] : 0;
		$failed  = isset( $result['failed'] ) ? (int) $result['failed'] : 0;
		$ids     = isset( $result['ids'] ) && is_array( $result['ids'] ) ? $result['ids'] : [];

		$summary = [];
		if ( $created ) {
			/* translators: %d: number of documents created */
			$summary[] = sprintf( _n( '%d created', '%d created', $created, 'eazydocs' ), $created );
		}
		if ( $updated ) {
			/* translators: %d: number of documents updated */
			$summary[] = sprintf( _n( '%d updated', '%d updated', $updated, 'eazydocs' ), $updated );
		}
		if ( $failed ) {
			/* translators: %d: number of documents that failed to import */
			$summary[] = sprintf( _n( '%d failed', '%d failed', $failed, 'eazydocs' ), $failed );
		}
		if ( empty( $summary ) ) {
			$summary[] = esc_html__( 'Nothing to import', 'eazydocs' );
		}

		$class = $failed && ! $created && ! $updated ? 'notice-error' : 'notice-success';
		?>
		<div class="notice <?php echo esc_attr( $class ); ?> is-dismissible ezd-tools-notice">
			<p>
				<strong><?php esc_html_e( 'Import finished:', 'eazydocs' ); ?></strong>
				<?php echo esc_html( implode( ' · ', $summary ) ); ?>
			</p>
			<?php if ( ! empty( $ids ) ) : ?>
				<p class="ezd-tools-result-links">
					<?php
					$links = [];
					foreach ( $ids as $id ) {
						$edit = get_edit_post_link( (int) $id );
						$post = get_post( (int) $id );
						if ( $edit && $post ) {
							$links[] = '<a href="' . esc_url( $edit ) . '">' . esc_html( $post->post_title ) . '</a>';
						}
					}
					// translators: %s: comma-separated list of document edit links.
					echo wp_kses_post( implode( ', ', $links ) );
					?>
				</p>
			<?php endif; ?>
		</div>
		<?php
	}

	/**
	 * Inline behaviour: disable the submit button and show progress on submit so
	 * the user gets feedback during a long import (which runs synchronously).
	 */
	private function render_inline_behaviour() {
		?>
		<script>
		( function () {
			document.querySelectorAll( '.js-ezd-tools-form' ).forEach( function ( form ) {
				form.addEventListener( 'submit', function () {
					var btn   = form.querySelector( '.js-ezd-tools-submit' );
					var label = form.querySelector( '.js-ezd-tools-label' );
					if ( ! btn || ! label ) {
						return;
					}
					btn.classList.add( 'is-busy' );
					label.textContent = <?php echo wp_json_encode( __( 'Working… please wait', 'eazydocs' ) ); ?>;
					// Export streams a file without navigating away — re-enable shortly.
					if ( 'ezd_md_export' === ( form.querySelector( 'input[name=action]' ) || {} ).value ) {
						setTimeout( function () {
							btn.classList.remove( 'is-busy' );
							label.textContent = <?php echo wp_json_encode( __( 'Export', 'eazydocs' ) ); ?>;
						}, 4000 );
					} else {
						btn.disabled = true;
					}
				} );
			} );
		}() );
		</script>
		<?php
	}

	/**
	 * Handle the export request and stream the file.
	 */
	public function handle_export() {
		if ( ! $this->user_can_manage() ) {
			wp_die( esc_html__( 'Unauthorized.', 'eazydocs' ), '', [ 'response' => 403 ] );
		}
		check_admin_referer( 'ezd_md_export' );

		$this->raise_limits();

		$root_id = absint( $_POST['root_id'] ?? 0 );
		$format  = ( isset( $_POST['format'] ) && 'csv' === $_POST['format'] ) ? 'csv' : 'markdown';

		// Object-level authorization: the export screen requires only edit_docs,
		// which on sites that delegate that capability is not sufficient to read
		// every private/draft doc. Confirm the user may actually read the selected
		// root before streaming its subtree (descendants are filtered in the
		// exporters). read_post maps through the docs CPT meta caps, so private
		// docs correctly require read_private_docs.
		if ( ! $root_id || ! current_user_can( 'read_post', $root_id ) ) {
			wp_die( esc_html__( 'You are not allowed to export this document.', 'eazydocs' ), '', [ 'response' => 403 ] );
		}

		if ( 'csv' === $format ) {
			$csv = Csv::export_to_string( $root_id );
			if ( is_wp_error( $csv ) ) {
				wp_die( esc_html( $csv->get_error_message() ) );
			}

			$root = get_post( $root_id );
			$name = 'eazydocs-' . sanitize_title( $root ? $root->post_title : 'export' ) . '-' . gmdate( 'Ymd' ) . '.csv';

			nocache_headers();
			header( 'Content-Type: text/csv; charset=utf-8' );
			header( 'Content-Disposition: attachment; filename="' . sanitize_file_name( $name ) . '"' );
			echo $csv; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- CSV file body, not HTML.
			exit;
		}

		$result = Exporter::export_to_zip( $root_id );

		if ( is_wp_error( $result ) ) {
			wp_die( esc_html( $result->get_error_message() ) );
		}

		nocache_headers();
		header( 'Content-Type: application/zip' );
		header( 'Content-Disposition: attachment; filename="' . sanitize_file_name( $result['name'] ) . '"' );
		header( 'Content-Length: ' . filesize( $result['file'] ) );

		readfile( $result['file'] );
		wp_delete_file( $result['file'] );
		exit;
	}

	/**
	 * Stream a tiny example CSV so users can see the expected column layout.
	 */
	public function handle_sample_csv() {
		if ( ! $this->user_can_manage() ) {
			wp_die( esc_html__( 'Unauthorized.', 'eazydocs' ), '', [ 'response' => 403 ] );
		}
		check_admin_referer( 'ezd_csv_sample' );

		$rows = [
			Csv::COLUMNS,
			[ 1, 0, 0, 'publish', 'Getting Started', 'getting-started', '<p>Welcome to your documentation.</p>' ],
			[ 2, 1, 0, 'publish', 'Installation', 'installation', '<p>How to install the product.</p>' ],
			[ 3, 0, 1, 'draft', 'Advanced Guide', 'advanced-guide', '<p>Deeper topics go here.</p>' ],
		];

		$handle = fopen( 'php://temp', 'r+' );
		foreach ( $rows as $row ) {
			fputcsv( $handle, $row, ',', '"', '' );
		}
		rewind( $handle );
		$csv = stream_get_contents( $handle );
		fclose( $handle );

		nocache_headers();
		header( 'Content-Type: text/csv; charset=utf-8' );
		header( 'Content-Disposition: attachment; filename="eazydocs-sample.csv"' );
		echo $csv; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- CSV file body, not HTML.
		exit;
	}

	/**
	 * Handle the import upload.
	 */
	public function handle_import() {
		if ( ! $this->user_can_manage() ) {
			wp_die( esc_html__( 'Unauthorized.', 'eazydocs' ), '', [ 'response' => 403 ] );
		}
		check_admin_referer( 'ezd_md_import' );

		$this->raise_limits();

		$parent_id   = absint( $_POST['parent_id'] ?? 0 );
		$post_status = ( isset( $_POST['post_status'] ) && 'publish' === $_POST['post_status'] ) ? 'publish' : 'draft';
		$mode        = ( isset( $_POST['duplicate_mode'] ) && 'update' === $_POST['duplicate_mode'] ) ? 'update' : 'create';

		// Object-level authorization: importing under a parent effectively adds a
		// child to it, so the current user must be able to edit that specific doc
		// — edit_docs (the screen gate) does not itself grant that on every doc.
		// The parent selector is already filtered client-side, but the request is
		// the real security boundary.
		if ( $parent_id && ! current_user_can( 'edit_post', $parent_id ) ) {
			set_transient(
				'ezd_md_import_result_' . get_current_user_id(),
				[ 'error' => __( 'You are not allowed to import under the selected document.', 'eazydocs' ) ],
				MINUTE_IN_SECONDS
			);
			wp_safe_redirect( $this->tab_url( 'import' ) );
			exit;
		}

		// Post-status restriction: publishing requires publish_docs, otherwise
		// every imported doc is forced to draft regardless of what was requested.
		if ( 'publish' === $post_status && ! current_user_can( 'publish_docs' ) ) {
			$post_status = 'draft';
		}

		$result = $this->process_upload( $parent_id, $post_status, $mode );

		$payload = is_wp_error( $result )
			? [ 'error' => $result->get_error_message() ]
			: [
				'created' => (int) ( $result['created'] ?? 0 ),
				'updated' => (int) ( $result['updated'] ?? 0 ),
				'failed'  => (int) ( $result['failed'] ?? 0 ),
				'ids'     => array_slice( array_map( 'intval', $result['ids'] ?? [] ), 0, 50 ),
			];

		set_transient( 'ezd_md_import_result_' . get_current_user_id(), $payload, MINUTE_IN_SECONDS );

		wp_safe_redirect( $this->tab_url( 'import' ) );
		exit;
	}

	/**
	 * Give bulk imports/exports more room before timing out on shared hosting.
	 */
	private function raise_limits() {
		wp_raise_memory_limit( 'admin' );
		if ( function_exists( 'set_time_limit' ) ) {
			@set_time_limit( 0 ); // phpcs:ignore WordPress.PHP.NoSilencedErrors.Discouraged -- disabled on some hosts.
		}
	}

	/**
	 * Validate the uploaded file and dispatch to the importer.
	 *
	 * @param int    $parent_id
	 * @param string $post_status
	 * @param string $mode        'create' or 'update'.
	 * @return array|\WP_Error
	 */
	private function process_upload( $parent_id, $post_status, $mode = 'create' ) {
		if ( empty( $_FILES['ezd_md_file']['tmp_name'] ) || ! is_uploaded_file( $_FILES['ezd_md_file']['tmp_name'] ) ) {
			return new \WP_Error( 'ezd_md_no_file', __( 'No file uploaded.', 'eazydocs' ) );
		}

		$tmp_name = $_FILES['ezd_md_file']['tmp_name'];
		$size     = (int) ( $_FILES['ezd_md_file']['size'] ?? 0 );
		$filename = sanitize_file_name( wp_unslash( $_FILES['ezd_md_file']['name'] ?? '' ) );
		$ext      = strtolower( pathinfo( $filename, PATHINFO_EXTENSION ) );

		if ( $size <= 0 || $size > self::MAX_UPLOAD ) {
			return new \WP_Error( 'ezd_md_size', __( 'File is empty or too large.', 'eazydocs' ) );
		}
		if ( ! in_array( $ext, [ 'zip', 'md', 'markdown', 'csv' ], true ) ) {
			return new \WP_Error( 'ezd_md_ext', __( 'Only .md, .markdown, .zip or .csv files are allowed.', 'eazydocs' ) );
		}

		if ( 'csv' === $ext ) {
			return Csv::import_from_file( $tmp_name, $parent_id, $post_status, $mode );
		}

		if ( 'zip' === $ext ) {
			return Importer::import_zip( $tmp_name, $parent_id, $post_status, $mode );
		}

		$raw = file_get_contents( $tmp_name ); // phpcs:ignore WordPress.WP.AlternativeFunctions.file_get_contents_file_get_contents -- local uploaded temp file.
		if ( false === $raw ) {
			return new \WP_Error( 'ezd_md_read', __( 'Could not read the uploaded file.', 'eazydocs' ) );
		}

		return Importer::import_markdown_string( $raw, $filename, $parent_id, $post_status, $mode );
	}
}
