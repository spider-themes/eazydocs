#!/usr/bin/env bash
set -euo pipefail
cd "$(dirname "$0")/.."

# Lint plugin-owned runtime PHP, including newly added files.
find includes src templates shortcodes tests -name '*.php' -type f ! -path 'includes/csf/*' -print0 |
  xargs -0 -n 1 php -l >/dev/null
for entry in eazydocs.php blocks.php; do
  if [[ -f "$entry" ]]; then php -l "$entry"; fi
done
composer check-platform-reqs --lock --no-dev
npm run build

php tests/security-regressions.php "$PWD" "${EZD_PRO_PATH:-../eazydocs-pro}"
