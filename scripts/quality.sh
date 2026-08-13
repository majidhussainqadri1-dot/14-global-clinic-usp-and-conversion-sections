#!/usr/bin/env bash
set -euo pipefail
ROOT="$(cd "$(dirname "${BASH_SOURCE[0]}")/.." && pwd)"
find "$ROOT/14-global-clinic-usp-integration" "$ROOT/tests" -name '*.php' -print0 | xargs -0 -n1 php -l
php "$ROOT/tests/policy-tests.php"
php "$ROOT/tests/contract-tests.php"
php "$ROOT/tests/reliability-tests.php"
php "$ROOT/tests/central-plan-tests.php"
php "$ROOT/tests/future-intelligence-tests.php"
php "$ROOT/tests/review80-hardening-tests.php"
php "$ROOT/tests/second-review-regression-tests.php"
php "$ROOT/tests/third-review-regression-tests.php"
php "$ROOT/tests/fourth-review-regression-tests.php"
php "$ROOT/tests/fifth-review-regression-tests.php"
php "$ROOT/tests/sixth-review-regression-tests.php"
php "$ROOT/tests/seventh-review-regression-tests.php"
php "$ROOT/tests/eighth-review-regression-tests.php"
php "$ROOT/tests/ninth-review-regression-tests.php"
php "$ROOT/tests/tenth-review-regression-tests.php"
php "$ROOT/tests/eleventh-review-regression-tests.php"
php "$ROOT/tests/eleventh-cycle-round02-regression-tests.php"
php "$ROOT/tests/eleventh-cycle-round05-regression-tests.php"
python3 "$ROOT/scripts/review80.py"
python3 "$ROOT/scripts/review80-second.py"
python3 "$ROOT/scripts/review80-third.py"
python3 "$ROOT/scripts/review80-fourth.py"
python3 "$ROOT/scripts/review80-fifth.py"
python3 "$ROOT/scripts/review80-sixth.py"
python3 -m py_compile "$ROOT/scripts/build.py" "$ROOT/scripts/review80.py" "$ROOT/scripts/review80-second.py" "$ROOT/scripts/review80-third.py" "$ROOT/scripts/review80-fourth.py" "$ROOT/scripts/review80-fifth.py" "$ROOT/scripts/review80-sixth.py"
if grep -RInE "(password|secret|api[_-]?key|private[_-]?key)[[:space:]]*[:=][[:space:]]*['\"][^'\"]+" "$ROOT/14-global-clinic-usp-integration" --exclude='*.md' --exclude='readme.txt'; then echo "Potential embedded secret detected" >&2; exit 1; fi
if grep -RInE "onclick=|onerror=|onload=|javascript:" "$ROOT/14-global-clinic-usp-integration" --include='*.php' --include='*.html' --include='*.txt'; then echo "Inline executable markup detected" >&2; exit 1; fi
if grep -RIn "data-gcu-event-token" "$ROOT/14-global-clinic-usp-integration"; then echo "Single-use measurement token found in package source/HTML path" >&2; exit 1; fi
if grep -RIn "GCU_Policy::same_origin_url" "$ROOT/14-global-clinic-usp-integration" --include='*.php'; then echo "Deprecated host-only URL helper must not be called" >&2; exit 1; fi
if ! grep -q "F14-FUT-24" "$ROOT/14-global-clinic-usp-integration/includes/class-gcu-future-policy.php"; then echo "24-feature Future Intelligence catalogue incomplete" >&2; exit 1; fi
if ! grep -q "SSH-F14-FUTURE-CTI-2026-v2.0" "$ROOT/14-global-clinic-usp-integration/global-clinic-usp-integration.php"; then echo "Future plan contract marker missing" >&2; exit 1; fi
if ! grep -q "GCU_Review80_Hardening" "$ROOT/14-global-clinic-usp-integration/global-clinic-usp-integration.php"; then echo "Eighty-pass hardening bootstrap missing" >&2; exit 1; fi
if ! grep -q "GCU_Fifth_Review_Hardening" "$ROOT/14-global-clinic-usp-integration/global-clinic-usp-integration.php"; then echo "Fifth eighty-pass hardening bootstrap missing" >&2; exit 1; fi
if ! grep -q "GCU_Eleventh_Review_Hardening" "$ROOT/14-global-clinic-usp-integration/global-clinic-usp-integration.php"; then echo "Eleventh-cycle postcondition hardening bootstrap missing" >&2; exit 1; fi
for ledger in REVIEW-80-THIRD-LEDGER-v1.4.1.md REVIEW-80-FOURTH-LEDGER-v1.4.2.md REVIEW-80-FIFTH-LEDGER-v1.4.3.md REVIEW-80-SIXTH-LEDGER-v1.4.4.md REVIEW-10-EIGHTH-LEDGER-v1.4.6.md REVIEW-10-NINTH-LEDGER-v1.4.7.md REVIEW-20-TENTH-LEDGER-v1.4.8.md; do [ -f "$ROOT/docs/$ledger" ] || { echo "Review ledger missing: $ledger" >&2; exit 1; }; done
for temp in "$ROOT/.github/workflows/file14-third-review-corrective-patch.yml" "$ROOT/scripts/apply-file14-third-review-corrections.py" "$ROOT/scripts/apply-file14-third-review-deep-corrections.py" "$ROOT/scripts/apply-file14-third-review-final-corrections.py" "$ROOT/scripts/apply-file14-third-review-containment.py" "$ROOT/.github/workflows/file14-ninth-review-apply.yml" "$ROOT/scripts/apply-file14-ninth-review-corrections.py"; do if [ -e "$temp" ]; then echo "Temporary corrective machinery remains: $temp" >&2; exit 1; fi; done
echo "Quality suite: PASS"
