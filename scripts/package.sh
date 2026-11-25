#!/usr/bin/env bash
set -euo pipefail

output_dir=${1:-.}
mkdir -p "$output_dir"
output_dir=$(cd "$output_dir" && pwd)
cd "$(dirname "$0")/.."
slug=eazydocs
runtime=(eazydocs.php includes assets build vendor languages templates shortcodes readme.txt changelog.txt wpml-config.xml sample-data)
if [[ -f blocks.php ]]; then runtime+=(blocks.php); fi
if [[ -n ${PROD_FILES:-} ]]; then
  # Match the deployment action's space-separated list when it is available.
  read -r -a runtime <<< "$PROD_FILES"
  runtime+=(languages)
fi

stage=$(mktemp -d)
trap 'rm -rf "$stage"' EXIT
mkdir "$stage/$slug"
for item in "${runtime[@]}"; do
  if [[ -e "$item" ]]; then cp -R "$item" "$stage/$slug/"; fi
done
# The deployment action treats absent entries as optional. Fail this check if
# a missing runtime entry would leave the installed plugin unusable.
for path in eazydocs.php vendor/autoload.php build/styles/styles.asset.php includes; do
  test -e "$stage/$slug/$path" || { echo "Missing from $slug ZIP: $path" >&2; exit 1; }
done
(
  cd "$stage"
  zip -qr archive.zip "$slug" -x '*/.DS_Store' '*/._*'
)
mv -f "$stage/archive.zip" "$output_dir/$slug.zip"
printf '%s\n' "$output_dir/$slug.zip"
