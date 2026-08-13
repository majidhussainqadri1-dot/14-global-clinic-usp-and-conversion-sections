#!/usr/bin/env bash
set -euo pipefail
ROOT="$(cd "$(dirname "${BASH_SOURCE[0]}")/.." && pwd)"; P="$ROOT/14-global-clinic-usp-integration"; fail=0
need(){ grep -RqsF -- "$1" "$2" || { echo "ROUND2 missing: $1 in $2" >&2; fail=1; }; }
forbid(){ if grep -RqsE -- "$1" "$2"; then echo "ROUND2 forbidden pattern: $1 in $2" >&2; fail=1; fi; }
version="$(sed -nE 's/^\s*\*\s*Version:\s*([0-9]+\.[0-9]+\.[0-9]+)\s*$/\1/p' "$P/global-clinic-usp-integration.php" | head -1)"; [ -n "$version" ] || exit 1
need "Stable tag: $version" "$P/readme.txt"
need "--gcu-brand-primary: #087A4E" "$P/assets/css/global-clinic-usp-integration.css"; need "#087A4E" "$P/assets/css/gcu-future-intelligence.css"
for x in prefers-reduced-motion prefers-reduced-data forced-colors; do need "$x" "$P/assets/css/global-clinic-usp-integration.css"; done
for x in global_privacy_control_requested low_bandwidth_requested is_file14_acquisition_route wp_privacy_personal_data_exporters wp_privacy_personal_data_erasers legal_hold_applies; do need "$x" "$P/includes/class-gcu-privacy.php"; done
need "sabri_shell_back_home_controls" "$P/includes/class-gcu-frontend.php"; forbid "data-gcu-shell-fallback" "$P/includes/class-gcu-frontend.php"; need "private, no-store" "$P/includes/class-gcu-frontend.php"; need "Vary: Accept-Language, Cookie" "$P/includes/class-gcu-frontend.php"; need "content_locale='en-US'" "$P/includes/class-gcu-frontend.php"
need "Small-Cohort Privacy Guard" "$P/includes/class-gcu-future-policy.php"; need "Dark-Pattern Linter" "$P/includes/class-gcu-future-policy.php"; need "AI Ethical Copy Assistant" "$P/includes/class-gcu-future-policy.php"; need "auto_publish' => false" "$P/includes/class-gcu-future-intelligence.php"
for x in GCU_ALLOW_PURGE gcu_purge_approval_v1 backup_verified_at restore_verified_at gcu_purge_receipt_v1 gcu_destructive_purge_authorized_v1; do need "$x" "$P/uninstall.php"; done
need "Deterministic double-build mismatch" "$ROOT/scripts/build.py"; need "Unsafe archive path" "$ROOT/scripts/build.py"; need "sbom" "$ROOT/scripts/build.py"
need "tenth-review-regression-tests.php" "$ROOT/scripts/quality.sh"; need "REVIEW-20-TENTH-LEDGER-v1.4.8.md" "$ROOT/scripts/quality.sh"
for id in CEN-GOV-001 CEN-OWN-001 CEN-BIZ-001 CEN-DON-001 CEN-BRAND-001 CEN-NAV-001 CEN-LOC-001 CEN-A11Y-001 CEN-LOWDATA-001 CEN-PRIV-001 F14-FR-001 F14-FR-016 F14-NFR-010 F14-FUT-01 F14-FUT-24 DoD-11 DoD-13; do need "$id" "$ROOT/docs/REQUIREMENTS-TRACEABILITY.md"; done
forbid "<main class=\"gcu-page\"" "$P/includes/class-gcu-frontend.php"; forbid "guaranteed income|guaranteed cure|limited spots|act now|instant verification" "$P/includes/class-gcu-policy.php"; forbid "guaranteed income|guaranteed cure|limited spots|act now|instant verification" "$P/includes/class-gcu-frontend.php"
[ "$fail" -eq 0 ] || exit 1
echo "Fresh Review Round 2: PASS — v$version privacy, purge evidence, caching, locale/RTL, accessibility, ethical conversion, source hygiene and tenth-cycle evidence"
