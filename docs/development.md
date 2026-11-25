# EazyDocs development

## Setup and checks

Use WordPress 6.6 or later, PHP 8.1 or later, Composer, Node 24, and npm. Keep Free and Pro as sibling checkouts for paired development. Install runtime dependencies and build the assets from each repo.

```sh
composer install --no-dev
npm ci
bash scripts/check.sh
```

The check command lints plugin PHP, verifies PHP and extensions against the lockfile, builds frontend assets, and runs the isolated regressions. The isolated PHP checks include the sibling Pro sources when present. Set `EZD_PRO_PATH` for a different checkout.

## Feature map

| Feature | Entry point and main implementation | Compatibility surface |
| --- | --- | --- |
| Plugin load and migrations | [eazydocs.php](../eazydocs.php) → `core_includes()`, `init_plugin()`, activation | WordPress hook order, Freemius initialization, plugin version and database options |
| Docs Builder | [src/docs-builder](../src/docs-builder) → [controller](../includes/Admin/Api/DocsBuilderController.php) → [document queries and payloads](../includes/Admin/Api/DocsBuilderDocuments.php) | `eazydocs/v1/docs-builder` routes, response keys, permissions; order persists through `Admin::parent_nestable_callback()` and `Admin::nestable_callback()` |
| Doc hierarchy and navigation | [hierarchy.php](../includes/Docs/hierarchy.php) → tree queries, ordering and navigation cache | Global `ezd_*` helpers, including `ezd_get_doc_parent_id()`; post parents, menu order, status and language scopes |
| Visibility and access | [functions.php](../includes/functions.php) → `ezd_can_read_doc_content()`, visibility and capability helpers → Pro role visibility | Post status, password and role access, author capabilities; check access before rendering or mutating content |
| Public docs, search and widgets | [Frontend](../includes/Frontend) → [templates](../templates), [shortcodes](../shortcodes) and [Elementor](../includes/Elementor) | Enqueue handles, AJAX actions, template override paths and public shortcodes |
| Gutenberg | [blocks.php](../blocks.php) → [src](../src) block metadata and renderers → `build/` | Block names, saved markup, attributes |
| Import and export | [Import_Export](../includes/Admin/Import_Export.php) → [Markdown](../includes/Markdown) and [IO](../includes/IO) | Document hierarchy, CSV/ZIP formats, permissions and post status |

## Assets and packages

| Source | Output or loading |
| --- | --- |
| `assets/scss/` | `build/styles/*.css` and `build/styles/styles.asset.php` from Webpack |
| `src/` | Gutenberg bundles, block metadata and Docs Builder under `build/` |
| `assets/js/` and `assets/vendors/` | Direct WordPress enqueues |
| Tracked CSS under `assets/css/` | Inspect the enqueue before editing. Some files are served directly and others mirror SCSS. Admin settings has a tracked CSS copy and a Webpack entry. Verify the settings page after changes to either. |

Keep [the common Webpack helper](../build-tools/webpack-styles.js) in sync with Pro. Each repo has a copy so it can build independently. Pro CI compares the copies. Project-specific entries are listed in [webpack.config.js](../webpack.config.js) in each repo.

Run `npm run plugin-zip` after a build to create an installable ZIP in the checkout. Use `bash scripts/package.sh /absolute/output-directory` to write it elsewhere. The script uses the same `PROD_FILES` list as the deployment workflow when that variable is supplied. Otherwise it stages the plugin runtime, generated bundles, Composer vendor files, and translations from this checkout. Use a clean checkout for release verification. Production publishing is handled by the existing packaging workflow.

## WordPress and browser checks

Use a disposable MySQL database named `ezd_audit`, WP-CLI, and Playwright Chromium. Set `EZD_DB_HOST`, `EZD_DB_USER`, and `EZD_DB_PASSWORD` for the fixture. Use a fresh directory and table prefix for each run. The default WordPress fixture is 6.8.3. Set `EZD_WP_VERSION` to test another release or `EZD_WP_SOURCE` to use a previously downloaded, unconfigured distribution.

```sh
npx playwright install chromium
bash scripts/package.sh /tmp/ezd-packages
EZD_DB_PREFIX=smoke_ bash tests/run-wordpress-smoke.sh /tmp/ezd-smoke /tmp/ezd-packages/eazydocs.zip
```

Pass a built Pro ZIP as the third argument for paired smoke checks. Set `EZD_LINK_FREE=1` to activate Free from a directory symlink. The browser checks exercise admin settings, public and RTL CSS, and real pointer and keyboard reorder followed by a reload. `CHROME_PATH` optionally selects an installed Chrome executable.

With the Pro checkout available, these commands run the isolated subscription browser checks and the WordPress-backed handler suite.

```sh
node tests/subscription-ui-regressions.cjs ../eazydocs-pro /tmp/ezd-smoke/wp-includes/js/jquery/jquery.min.js
EZD_SITE_URL=http://ezd-audit.invalid EZD_DB_PREFIX=audit_ bash tests/setup-wordpress.sh /tmp/ezd-integration
php -d memory_limit=128M tests/security-integration.php /tmp/ezd-integration/wp-load.php
```

## Measure Docs Builder

Use a fresh Free fixture with the `bench_` table prefix. Seed each size in ascending order. The benchmark calls the actual REST routes as an administrator and reports SQL query count, JSON size, peak memory above the starting point, and elapsed handler/encoding time. Each invocation starts with a fresh request cache. The branch workload always renders 99 descendants.

```sh
EZD_DB_PREFIX=bench_ bash tests/setup-wordpress.sh /tmp/ezd-bench /tmp/ezd-packages/eazydocs.zip
for size in 100 1000 10000; do
  php tests/benchmark-docs-builder.php /tmp/ezd-bench/wp-load.php seed "$size"
  php tests/benchmark-docs-builder.php /tmp/ezd-bench/wp-load.php bootstrap
  php tests/benchmark-docs-builder.php /tmp/ezd-bench/wp-load.php branch
done
```

Repeat each request several times when comparing latency. Keep the PHP version, database, WordPress version, cache configuration, and fixture equal between runs. For public Search and Pro Assistant measurements, compare guest and restricted-content-capable users separately.

### Reference measurement

Disposable WordPress 6.8.3, PHP 8.4.12, MySQL 8.0.35, and three fresh CLI requests per result. Latency and memory are medians. Bootstrap still reads the complete hierarchy because it supplies counts for all root documents. The independent branch request now reads descendants of the requested root.

| Docs | Request | SQL queries | JSON bytes | Additional peak KiB | Time (ms) |
| ---: | --- | ---: | ---: | ---: | ---: |
| 100 | Bootstrap | 7 | 63,105 | 579 | 8.58 |
| 100 | Branch before | 4 | 61,166 | 481 | 7.42 |
| 1,000 | Bootstrap | 7 | 67,873 | 779 | 13.84 |
| 1,000 | Branch before | 4 | 61,884 | 654 | 9.23 |
| 10,000 | Bootstrap before | 7 | 109,318 | 6,277 | 31.27 |
| 10,000 | Branch before | 4 | 62,469 | 5,925 | 19.31 |
| 10,000 | Branch after | 6 | 62,469 | 490 | 9.19 |

The 10,000-doc branch traded two extra indexed queries for fewer rows read and less memory. The timings measure the REST handler and JSON encoding on the fixture.
