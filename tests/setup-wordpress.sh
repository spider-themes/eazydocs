#!/usr/bin/env bash
set -euo pipefail

site=${1:?Usage: bash tests/setup-wordpress.sh SITE_DIRECTORY [FREE_ZIP [PRO_ZIP]]}
free_zip=${2:-}
pro_zip=${3:-}
wp_cli=${EZD_WP_CLI:-wp}
site_url=${EZD_SITE_URL:-http://127.0.0.1:38765}
site_host=${site_url#*://}
export HTTP_HOST=${site_host%%/*}
wp() { command "$wp_cli" --path="$site" "$@"; }

mkdir -p "$site"
site=$(cd "$site" && pwd)
if [[ -e "$site/wp-config.php" ]]; then
  echo "Choose a fresh disposable WordPress directory: $site" >&2
  exit 1
fi
if [[ -n ${EZD_WP_SOURCE:-} ]]; then
  # Use a previously downloaded WordPress distribution only.
  if [[ -e "$EZD_WP_SOURCE/wp-config.php" ]]; then
    echo 'EZD_WP_SOURCE must point to an unconfigured WordPress distribution.' >&2
    exit 1
  fi
  cp -R "$EZD_WP_SOURCE/." "$site/"
else
  wp core download --version="${EZD_WP_VERSION:-6.8.3}" --quiet
fi
wp config create --dbname=ezd_audit --dbuser="${EZD_DB_USER:-root}" \
  --dbpass="${EZD_DB_PASSWORD:-}" --dbhost="${EZD_DB_HOST:-127.0.0.1:3306}" \
  --dbprefix="${EZD_DB_PREFIX:-wp_}" --skip-check --quiet
if wp core is-installed; then
  echo "Choose a fresh table prefix in the disposable ezd_audit database." >&2
  exit 1
fi

mkdir -p "$site/wp-content/mu-plugins"
cat > "$site/wp-content/mu-plugins/ezd-test-site.php" <<'PHP'
<?php
// Keep the disposable site's requests local and discard outgoing mail.
add_filter( 'pre_http_request', static function () { return new WP_Error( 'ezd_fixture_no_network' ); } );
add_filter( 'pre_wp_mail', '__return_true' );
add_action( 'init', static function () {
	if ( isset( $_GET['ezd_smoke_rtl'] ) ) {
		$GLOBALS['wp_locale']->text_direction = 'rtl';
	}
} );
PHP

wp core install --url="$site_url" --title='EazyDocs test site' \
  --admin_user=ezd_test_admin --admin_password=ezd-disposable-test --admin_email=test@example.invalid \
  --skip-email --quiet
wp --url="$site_url" option update siteurl "$site_url" --quiet
wp --url="$site_url" option update home "$site_url" --quiet

if [[ -z "$free_zip" ]]; then exit 0; fi

if [[ ${EZD_LINK_FREE:-0} == 1 ]]; then
  # Keep the actual files outside WordPress, like a linked development checkout.
  linked="$site-linked-plugin"
  mkdir "$linked"
  unzip -q "$free_zip" -d "$linked"
  ln -s "$linked/eazydocs" "$site/wp-content/plugins/eazydocs"
  wp plugin activate eazydocs --quiet
else
  wp plugin install "$free_zip" --activate --quiet
fi
if [[ -n "$pro_zip" ]]; then wp plugin install "$pro_zip" --activate --quiet; fi
# Decline the optional Freemius connection on the fixture so its setup screen
# leaves the plugin's normal admin pages available.
wp --url="$site_url" eval 'eaz_fs()->skip_connection();'
wp option update eazydocs_settings '{"setup_wizard_completed":true}' --format=json --quiet
wp option delete ezd_get_setup_wizard --quiet || true
