#!/usr/bin/env bash
set -euo pipefail
ROOT="$(cd "$(dirname "${BASH_SOURCE[0]}")/.." && pwd)"
PLUGIN="14-global-clinic-usp-integration"
DIST="$ROOT/dist"
rm -rf "$DIST"
mkdir -p "$DIST"
(
  cd "$ROOT"
  zip -X -r "$DIST/${PLUGIN}-1.0.0.zip" "$PLUGIN" \
    -x "*/.DS_Store" "*/node_modules/*" "*/vendor/*" "*/tests/*"
)
sha256sum "$DIST/${PLUGIN}-1.0.0.zip" > "$DIST/${PLUGIN}-1.0.0.zip.sha256"
echo "Built $DIST/${PLUGIN}-1.0.0.zip"
