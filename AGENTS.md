# EazyDocs Free

## Navigation & Context
- Development setup, feature map, and benchmarks: [docs/development.md](docs/development.md)
- Free ↔ Pro compatibility contracts (helpers, hooks, REST/AJAX, options, DB): [docs/contracts.md](docs/contracts.md)
- Distilled security, performance, and accessibility rules: [docs/ai-learnings.md](docs/ai-learnings.md)
- Historical security/regression audit reports: [docs/audits/](docs/audits/)

## Engineering Rules
- **PHP 8.1+:** Use WordPress hooks, short array syntax `[]`, strict comparisons (`===`/`!==`), Yoda conditions, and `if ( ! defined( 'ABSPATH' ) ) { exit; }` in every PHP file.
- **Global helpers:** [`includes/functions.php`](includes/functions.php) is a manifest loading domain modules under `includes/functions/` and [`includes/Docs/hierarchy.php`](includes/Docs/hierarchy.php). Add new helpers in the matching domain module rather than growing a single monolith.
- **Paired development:** Always check sibling `../eazydocs-pro` when changing shared helpers, hooks, REST/AJAX payloads, option keys, or meta keys.
- **Do not edit vendor/generated code:** Never modify `includes/csf/`, `freemius-sdk/`, `vendor/`, `build/`, or `languages/` manually (see `.aiignore`). Edit `assets/scss/` and `src/`, then rebuild with `npm run build` when assets change.

## Verification
- **Fast iteration check** (PHP lint, lockfile, TypeScript, helper parity, security regressions): `bash scripts/check-fast.sh`
- **Full pre-handoff check** (includes `npm run build`): `bash scripts/check.sh`
- Use a disposable WordPress site (`ezd_audit`) for integration and browser smoke tests.
