<?php
/**
 * Global helper functions manifest.
 *
 * Domain-specific helpers live under includes/functions/ and includes/Docs/hierarchy.php.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

require_once __DIR__ . '/Docs/hierarchy.php';
require_once __DIR__ . '/functions/options-encryption.php'; // Options, settings authorization, encryption, and Google connection check.
require_once __DIR__ . '/functions/dashboard-stats.php'; // Meta cache priming and admin dashboard KPI stats.
require_once __DIR__ . '/functions/templates-plan.php'; // Plan/theme checks, template loaders, and reading time.
require_once __DIR__ . '/functions/navigation-breadcrumbs.php'; // Doc list/breadcrumbs, sorting, OnePage list, and color utilities.
require_once __DIR__ . '/functions/sidebars-onepage-metabox.php'; // Sidebars, reusable blocks, OnePage metabox, and password form.
require_once __DIR__ . '/functions/context-ui-helpers.php'; // Context detection, dark mode, Elementor helpers, nestable helpers, and block-theme wrappers.
require_once __DIR__ . '/functions/access-visibility.php'; // Access control, doc visibility filters, status indicators, and slug validation.
require_once __DIR__ . '/functions/footnotes-setup.php'; // Footnotes, customizer visibility, setup wizard AJAX, RSS, and comments template.
require_once __DIR__ . '/functions/capabilities-access.php'; // Role/capability sync, user_has_cap filter, private doc access, and docs slug.
require_once __DIR__ . '/functions/cache-search-onepage.php'; // Search keywords, WPML helpers, OnePage/Elementor caches, and ranked vote queries.
require_once __DIR__ . '/functions/migration-import-ai.php'; // BetterDocs migration, sample data import, Article Walker, and AI Summary providers.
