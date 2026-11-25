#!/usr/bin/env bash
set -euo pipefail
cd "$(dirname "$0")/.."

PRO_PATH="${EZD_PRO_PATH:-../eazydocs-pro}"

# Fast PHP syntax check on plugin-owned runtime and test files.
find includes src templates shortcodes tests -name '*.php' -type f ! -path 'includes/csf/*' -print0 |
  xargs -0 -n 1 php -l >/dev/null
for entry in eazydocs.php blocks.php; do
  if [[ -f "$entry" ]]; then php -l "$entry"; fi
done

composer check-platform-reqs --lock --no-dev

# TypeScript check without triggering a full Webpack rebuild.
if [[ -f tsconfig.json && -x node_modules/.bin/tsc ]]; then
  ./node_modules/.bin/tsc --noEmit
fi

# Verify shared Webpack helper parity when sibling Pro checkout is present.
if [[ -f "$PRO_PATH/build-tools/webpack-styles.js" ]]; then
  cmp -s build-tools/webpack-styles.js "$PRO_PATH/build-tools/webpack-styles.js" || {
    echo "Drift detected: build-tools/webpack-styles.js differs from $PRO_PATH/build-tools/webpack-styles.js" >&2
    exit 1
  }
fi

php tests/security-regressions.php "$PWD" "$PRO_PATH"
