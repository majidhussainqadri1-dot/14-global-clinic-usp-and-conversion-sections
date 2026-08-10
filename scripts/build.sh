#!/usr/bin/env bash
set -euo pipefail
ROOT="$(cd "$(dirname "${BASH_SOURCE[0]}")/.." && pwd)"
PLUGIN="14-global-clinic-usp-integration"
VERSION="1.3.0"
DIST="$ROOT/dist"
rm -rf "$DIST"
mkdir -p "$DIST"
(
  cd "$ROOT"
  zip -X -r "$DIST/${PLUGIN}-${VERSION}.zip" "$PLUGIN" \
    -x "*/.DS_Store" "*/node_modules/*" "*/vendor/*" "*/tests/*"
)
sha256sum "$DIST/${PLUGIN}-${VERSION}.zip" > "$DIST/${PLUGIN}-${VERSION}.zip.sha256"
echo "Built $DIST/${PLUGIN}-${VERSION}.zip"
