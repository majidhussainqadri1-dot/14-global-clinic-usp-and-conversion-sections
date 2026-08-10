#!/usr/bin/env bash
set -euo pipefail
ROOT="$(cd "$(dirname "${BASH_SOURCE[0]}")/.." && pwd)"
P="$ROOT/14-global-clinic-usp-integration"
fail=0
need(){ grep -RqsF "$1" "$2" || { echo "ROUND2 missing: $1 in $2" >&2; fail=1; }; }
forbid(){ if grep -RqsE "$1" "$2"; then echo "ROUND2 forbidden pattern: $1 in $2" >&2; fail=1; fi; }
need "--gcu-brand-primary: #087A4E" "$P/assets/css/global-clinic-usp-integration.css"
need "prefers-reduced-motion" "$P/assets/css/global-clinic-usp-integration.css"
need "prefers-reduced-data" "$P/assets/css/global-clinic-usp-integration.css"
need "forced-colors" "$P/assets/css/global-clinic-usp-integration.css"
need "max-width: 360px" "$P/assets/css/global-clinic-usp-integration.css"
need "global_privacy_control_requested" "$P/includes/class-gcu-privacy.php"
need "low_bandwidth_requested" "$P/includes/class-gcu-privacy.php"
need "is_file14_acquisition_route" "$P/includes/class-gcu-privacy.php"
need "wp_privacy_personal_data_exporters" "$P/includes/class-gcu-privacy.php"
need "wp_privacy_personal_data_erasers" "$P/includes/class-gcu-privacy.php"
need "sabri_shell_back_home_controls" "$P/includes/class-gcu-frontend.php"
need "data-gcu-shell-fallback" "$P/includes/class-gcu-frontend.php"
need "noindex,nofollow" "$P/includes/class-gcu-frontend.php"
need "tokenEndpoint" "$P/assets/js/global-clinic-usp-integration.js"
need "Stable tag: 1.3.1" "$P/readme.txt"
need "GCU_ALLOW_PURGE" "$P/uninstall.php"
need "Deterministic double-build mismatch" "$ROOT/scripts/build.py"
need "Unsafe archive path" "$ROOT/scripts/build.py"
need "sbom" "$ROOT/scripts/build.py"
for id in CEN-GOV-001 CEN-OWN-001 CEN-BIZ-001 CEN-DON-001 CEN-BRAND-001 CEN-NAV-001 CEN-LOC-001 CEN-A11Y-001 CEN-LOWDATA-001 CEN-PRIV-001 F14-FR-001 F14-FR-016 F14-NFR-010 DoD-11 DoD-13; do need "$id" "$ROOT/docs/REQUIREMENTS-TRACEABILITY.md"; done
forbid "<main class=\"gcu-page\"" "$P/includes/class-gcu-frontend.php"
forbid "guaranteed income|guaranteed cure|limited spots|act now|instant verification" "$P"
if [ "$fail" -ne 0 ]; then exit 1; fi
echo "Fresh Review Round 2: PASS — privacy, consent, localization, RTL/LTR, accessibility, low-data, CSP-safe markup, packaging, uninstall and truth-status controls"
