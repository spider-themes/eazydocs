#!/usr/bin/env bash
set -euo pipefail

site=${1:?Usage: bash tests/run-wordpress-smoke.sh SITE_DIRECTORY FREE_ZIP [PRO_ZIP]}
free_zip=${2:?Pass a built Free ZIP}
pro_zip=${3:-}
wp_cli=${EZD_WP_CLI:-wp}
export EZD_SITE_URL=${EZD_SITE_URL:-http://127.0.0.1:38765}
if [[ ! "$EZD_SITE_URL" =~ ^http://127\.0\.0\.1:[0-9]+$ ]]; then
  echo 'Use a loopback HTTP URL with an explicit port for the disposable browser test.' >&2
  exit 1
fi

repo_dir=$(cd "$(dirname "$0")/.." && pwd)
bash "$repo_dir/tests/setup-wordpress.sh" "$site" "$free_zip" "$pro_zip"
site=$(cd "$site" && pwd)
export EZD_PLAYWRIGHT_OUTPUT="$site/test-results"
export EZD_PUBLIC_DOC_URL
EZD_PUBLIC_DOC_URL=$("$wp_cli" --path="$site" --url="$EZD_SITE_URL" eval-file "$repo_dir/tests/wordpress-fixture.php")
if [[ -n "$pro_zip" ]]; then export EZD_EXPECT_PRO=1; fi

server_log=$(mktemp)
php -S "${EZD_SITE_URL#http://}" -t "$site" > "$server_log" 2>&1 &
server=$!
cleanup() {
  kill "$server" 2>/dev/null || true
  wait "$server" 2>/dev/null || true
  rm -f "$server_log"
}
trap cleanup EXIT
if ! curl -fsS --retry 10 --retry-connrefused --retry-delay 1 "$EZD_SITE_URL/wp-login.php" >/dev/null 2>&1 || ! kill -0 "$server" 2>/dev/null; then
  cat "$server_log" >&2
  exit 1
fi
cd "$repo_dir"
if ! npx --no-install playwright test --config tests/playwright.config.cjs; then
  tail -n 50 "$server_log" >&2
  exit 1
fi
