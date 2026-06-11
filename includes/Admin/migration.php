<?php
/**
 * Cannot access directly.
 */
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

// SweetAlert is already enqueued by Import_Export::enqueue_assets() on this screen.

// Ensure the function exists before calling is_plugin_active
if ( ! function_exists( 'is_plugin_active' ) ) {
    require_once ABSPATH . 'wp-admin/includes/plugin.php';
}

// Check if BetterDocs is active
$is_betterdocs_active = is_plugin_active( 'betterdocs/betterdocs.php' );

// Pre-flight: how much BetterDocs content is waiting to be migrated.
$category_count = 0;
$doc_count      = 0;
if ( $is_betterdocs_active ) {
    $category_count = (int) get_terms(
        [
            'taxonomy'   => 'doc_category',
            'hide_empty' => false,
            'fields'     => 'count',
        ]
    );

    $doc_query = new WP_Query(
        [
            'post_type'      => 'docs',
            'post_status'    => 'any',
            'fields'         => 'ids',
            'posts_per_page' => 1,
            'tax_query'      => [
                [
                    'taxonomy' => 'doc_category',
                    'operator' => 'EXISTS',
                ],
            ],
        ]
    );
    $doc_count = (int) $doc_query->found_posts;
}

// Only enable the action when there is something to migrate.
$has_content   = $is_betterdocs_active && $category_count > 0;
$disabled_attr = $has_content ? '' : 'disabled';
?>

<div class="ezd-tools-card ezd-migration-wrapper">
	<div class="ezd-migration-inner">
		<div class="ezd-migration-head">
			<img class="ezd-migration-banner" src="<?php echo esc_url( EZD_IMG . 'bdocs-ezd.png' ); ?>" alt="<?php esc_attr_e( 'BetterDocs to EazyDocs', 'eazydocs' ); ?>" />
			<h2 class="ezd-migration-title"><?php esc_html_e( 'Migrate from BetterDocs to EazyDocs', 'eazydocs' ); ?></h2>
			<p class="ezd-migration-subtitle">
				<?php
				printf(
					/* translators: 1: opening <strong> for BetterDocs, 2: closing, 3: opening <strong> for EazyDocs, 4: closing. */
					esc_html__( 'Bring your existing documentation from %1$sBetterDocs%2$s into %3$sEazyDocs%4$s. Here is exactly what happens:', 'eazydocs' ),
					'<strong>',
					'</strong>',
					'<strong>',
					'</strong>'
				);
				?>
			</p>
		</div>

		<ul class="ezd-migration-list">
			<li>
				<span class="ezd-migration-list__icon dashicons dashicons-yes-alt"></span>
				<span class="ezd-migration-list__text"><?php esc_html_e( 'All', 'eazydocs' ); ?> <strong><?php esc_html_e( 'categories', 'eazydocs' ); ?></strong> <?php esc_html_e( 'will be converted into', 'eazydocs' ); ?> <strong><?php esc_html_e( 'parent Docs', 'eazydocs' ); ?></strong>.</span>
			</li>
			<li>
				<span class="ezd-migration-list__icon dashicons dashicons-yes-alt"></span>
				<span class="ezd-migration-list__text"><?php esc_html_e( 'All existing Docs will be organized as', 'eazydocs' ); ?> <strong><?php esc_html_e( 'child Docs', 'eazydocs' ); ?></strong> <?php esc_html_e( 'under those parent Docs.', 'eazydocs' ); ?></span>
			</li>
			<li>
				<span class="ezd-migration-list__icon dashicons dashicons-yes-alt"></span>
				<span class="ezd-migration-list__text"><?php esc_html_e( 'The URL structure will reflect the parent-child relationship, e.g.', 'eazydocs' ); ?> <code>/docs/parent-doc/child-doc/</code>.</span>
			</li>
			<li>
				<span class="ezd-migration-list__icon dashicons dashicons-yes-alt"></span>
				<span class="ezd-migration-list__text"><?php esc_html_e( 'Your original categories will be preserved as taxonomy terms.', 'eazydocs' ); ?></span>
			</li>
		</ul>

		<?php if ( $has_content ) : ?>
			<div class="ezd-migration-stats">
				<span class="dashicons dashicons-portfolio"></span>
				<span>
					<?php
					printf(
						/* translators: 1: number of categories (e.g. "8 categories"), 2: number of docs (e.g. "42 docs"). */
						esc_html__( 'Found %1$s and %2$s ready to migrate.', 'eazydocs' ),
						'<strong>' . esc_html( sprintf( _n( '%d category', '%d categories', $category_count, 'eazydocs' ), $category_count ) ) . '</strong>',
						'<strong>' . esc_html( sprintf( _n( '%d doc', '%d docs', $doc_count, 'eazydocs' ), $doc_count ) ) . '</strong>'
					);
					?>
				</span>
			</div>
		<?php endif; ?>

		<?php if ( ! $is_betterdocs_active ) : ?>
			<p class="ezd-tools-help ezd-tools-note ezd-tools-note--warning">
				<span class="dashicons dashicons-warning"></span>
				<?php esc_html_e( 'BetterDocs is not active. Activate the BetterDocs plugin to enable this migration.', 'eazydocs' ); ?>
			</p>
		<?php elseif ( ! $has_content ) : ?>
			<p class="ezd-tools-help ezd-tools-note ezd-tools-note--warning">
				<span class="dashicons dashicons-info-outline"></span>
				<?php esc_html_e( 'No BetterDocs categories were found, so there is nothing to migrate yet.', 'eazydocs' ); ?>
			</p>
		<?php endif; ?>

		<div class="ezd-migration-footer">
			<button class="ezd-start-migration-btn button button-primary button-hero" <?php echo esc_attr( $disabled_attr ); ?>>
				<span class="dashicons dashicons-randomize"></span>
				<?php esc_html_e( 'Start Migration', 'eazydocs' ); ?>
			</button>
			<p class="ezd-tools-help ezd-migration-help">
				<?php esc_html_e( 'Need help?', 'eazydocs' ); ?>
				<a href="https://helpdesk.spider-themes.net/docs/eazydocs-wordpress-plugin/getting-started/migrate-from-betterdocs-to-eazydocs" target="_blank" rel="noopener"><?php esc_html_e( 'Read the full migration guide', 'eazydocs' ); ?></a>
			</p>
		</div>
	</div>
</div>
