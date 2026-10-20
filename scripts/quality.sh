#!/usr/bin/env bash
set -euo pipefail
ROOT="$(cd "$(dirname "${BASH_SOURCE[0]}")/.." && pwd)"
find "$ROOT/14-global-clinic-usp-integration" "$ROOT/tests" -name '*.php' -print0 | xargs -0 -n1 php -l
php "$ROOT/tests/policy-tests.php"
php "$ROOT/tests/contract-tests.php"
php "$ROOT/tests/reliability-tests.php"
php "$ROOT/tests/central-plan-tests.php"
php "$ROOT/tests/cross-file-integration-tests.php"
php "$ROOT/tests/review20-r5-behavioral.php"
php "$ROOT/tests/future-intelligence-tests.php"
php "$ROOT/tests/owner-outcome-contract-tests.php"
php "$ROOT/tests/owner-sensitive-rest-boundary-tests.php"
php "$ROOT/tests/forty-plan-regressions.php"
bash "$ROOT/scripts/review40-iterative.sh"
php "$ROOT/tests/r35-experiment-preflight-workflow-tests.php"
php "$ROOT/tests/r36-owner-cohort-privacy-tests.php"
php "$ROOT/tests/r36-hourly-stop-error-tests.php"
php "$ROOT/tests/r37-early-stop-db-failure-tests.php"
php "$ROOT/tests/r38-early-stop-batch-completeness-tests.php"
php "$ROOT/tests/r39-parity-completeness-tests.php"
php "$ROOT/tests/r40-claim-freshness-failure-tests.php"
php "$ROOT/tests/r41-faq-consistency-governance-tests.php"
php "$ROOT/tests/r42-analytics-read-integrity-tests.php"
php "$ROOT/tests/r43-aggregate-count-provenance-tests.php"
php "$ROOT/tests/r44-per-stage-privacy-owner-version-tests.php"
php "$ROOT/tests/r45-retention-public-history-tests.php"
php "$ROOT/tests/r46-claim-date-and-parity-row-tests.php"
php "$ROOT/tests/r47-quality-provenance-and-sql-evidence-tests.php"
php "$ROOT/tests/r48-consistency-evidence-boundary-tests.php"
php "$ROOT/tests/r49-admin-pagination-fail-closed-tests.php"
php "$ROOT/tests/r50-guarded-list-read-integrity-tests.php"
php "$ROOT/tests/r51-policy-history-region-evidence-tests.php"
php "$ROOT/tests/r52-experiment-guard-count-proof-tests.php"
php "$ROOT/tests/r53-canonical-cohort-evidence-tests.php"
php "$ROOT/tests/r54-faq-friction-evidence-tests.php"
php "$ROOT/tests/r55-owner-restore-and-anomaly-guards.php"
php "$ROOT/tests/r56-owner-stage-evidence-integrity.php"
php "$ROOT/tests/r56-approved-snapshot-reconciliation.php"
php "$ROOT/tests/r57-native-owner-identity-replay-tests.php"
php "$ROOT/tests/r58-native-owner-anomaly-identity.php"
php "$ROOT/tests/r58-approved-baseline-readiness.php"
php "$ROOT/tests/r59-founder-approved-capture-integrity.php"
php "$ROOT/tests/r59-quality-stage-evidence-integrity.php"
php "$ROOT/tests/r60-owner-placement-metadata-gates.php"
php "$ROOT/tests/r60-complete-placement-baseline-evidence.php"
php "$ROOT/tests/r61-typed-owner-placement-evidence.php"
php "$ROOT/tests/r61-inbound-owner-contract-integrity.php"
php "$ROOT/tests/r62-owner-placement-schedule-integrity.php"
php "$ROOT/tests/r62-owner-v1-contract-version-evidence.php"
php "$ROOT/tests/r63-business-policy-event-replay-integrity.php"
php "$ROOT/tests/r63-inbox-owner-version-prevalidation.php"
php "$ROOT/tests/r64-exact-options-json-evidence.php"
php "$ROOT/tests/r64-inbox-recorded-payload-integrity.php"
php "$ROOT/tests/r65-policy-event-original-version-contracts.php"
php "$ROOT/tests/r65-policy-event-idempotent-replay.php"
php "$ROOT/tests/r66-owner-health-destination-tests.php"
php "$ROOT/tests/r68-forty-native-owner-contract-behavior-tests.php"
php "$ROOT/tests/r69-forty-privacy-db-read-failure-tests.php"
php "$ROOT/tests/r70-owner-metric-availability-contract-tests.php"
php "$ROOT/tests/review80-hardening-tests.php"
php "$ROOT/tests/second-review-regression-tests.php"
php "$ROOT/tests/third-review-regression-tests.php"
php "$ROOT/tests/fourth-review-regression-tests.php"
php "$ROOT/tests/fifth-review-regression-tests.php"
php "$ROOT/tests/sixth-review-regression-tests.php"
python3 "$ROOT/scripts/review80.py"
python3 "$ROOT/scripts/review80-second.py"
python3 "$ROOT/scripts/review80-third.py"
python3 "$ROOT/scripts/review80-fourth.py"
python3 "$ROOT/scripts/review80-fifth.py"
python3 "$ROOT/scripts/review80-sixth.py"
python3 "$ROOT/scripts/review20-cross-file.py"
python3 -m py_compile "$ROOT/scripts/build.py" "$ROOT/scripts/review80.py" "$ROOT/scripts/review80-second.py" "$ROOT/scripts/review80-third.py" "$ROOT/scripts/review80-fourth.py" "$ROOT/scripts/review80-fifth.py" "$ROOT/scripts/review80-sixth.py" "$ROOT/scripts/review20-cross-file.py"
if grep -RInE "(password|secret|api[_-]?key|private[_-]?key)[[:space:]]*[:=][[:space:]]*['\"][^'\"]+" "$ROOT/14-global-clinic-usp-integration" --exclude='*.md' --exclude='readme.txt'; then echo "Potential embedded secret detected" >&2; exit 1; fi
if grep -RInE "onclick=|onerror=|onload=|javascript:" "$ROOT/14-global-clinic-usp-integration" --include='*.php' --include='*.html' --include='*.txt'; then echo "Inline executable markup detected" >&2; exit 1; fi
if grep -RIn "data-gcu-event-token" "$ROOT/14-global-clinic-usp-integration"; then echo "Single-use measurement token found in package source/HTML path" >&2; exit 1; fi
if grep -RIn "GCU_Policy::same_origin_url" "$ROOT/14-global-clinic-usp-integration" --include='*.php'; then echo "Deprecated host-only URL helper must not be called" >&2; exit 1; fi
if grep -RInF '${'"'"'all_claims'"'"'}' "$ROOT/14-global-clinic-usp-integration" --include='*.php'; then echo "Third-review correction artifact remains in runtime source" >&2; exit 1; fi
if ! grep -q "F14-FUT-24" "$ROOT/14-global-clinic-usp-integration/includes/class-gcu-future-policy.php"; then echo "24-feature Future Intelligence catalogue incomplete" >&2; exit 1; fi
if ! grep -q "SSH-F14-FUTURE-CTI-2026-v2.0" "$ROOT/14-global-clinic-usp-integration/global-clinic-usp-integration.php"; then echo "Future plan contract marker missing" >&2; exit 1; fi
if ! grep -q "GCU_Review80_Hardening" "$ROOT/14-global-clinic-usp-integration/global-clinic-usp-integration.php"; then echo "Eighty-pass hardening bootstrap missing" >&2; exit 1; fi
if ! grep -q "GCU_Fifth_Review_Hardening" "$ROOT/14-global-clinic-usp-integration/global-clinic-usp-integration.php"; then echo "Fifth eighty-pass hardening bootstrap missing" >&2; exit 1; fi
if ! grep -q "Third Independent Eighty-Pass Review" "$ROOT/docs/REVIEW-80-THIRD-LEDGER-v1.4.1.md"; then echo "Third-review ledger missing" >&2; exit 1; fi
if ! grep -q "Fourth Independent Eighty-Pass Review" "$ROOT/docs/REVIEW-80-FOURTH-LEDGER-v1.4.2.md"; then echo "Fourth-review ledger missing" >&2; exit 1; fi
if ! grep -q "Fifth Independent Eighty-Pass Review" "$ROOT/docs/REVIEW-80-FIFTH-LEDGER-v1.4.3.md"; then echo "Fifth-review ledger missing" >&2; exit 1; fi
if ! grep -q "Sixth Independent Eighty-Pass Review" "$ROOT/docs/REVIEW-80-SIXTH-LEDGER-v1.4.4.md"; then echo "Sixth-review ledger missing" >&2; exit 1; fi
for temp in \
  "$ROOT/.github/workflows/file14-third-review-corrective-patch.yml" \
  "$ROOT/scripts/apply-file14-third-review-corrections.py" \
  "$ROOT/scripts/apply-file14-third-review-deep-corrections.py" \
  "$ROOT/scripts/apply-file14-third-review-final-corrections.py" \
  "$ROOT/scripts/apply-file14-third-review-containment.py"; do
  if [ -e "$temp" ]; then echo "Temporary third-review corrective machinery remains: $temp" >&2; exit 1; fi
done
echo "Quality suite: PASS"
