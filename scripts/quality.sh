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
python3 -m py_compile "$ROOT/scripts/build.py"
if grep -RInE "(password|secret|api[_-]?key|private[_-]?key)[[:space:]]*[:=][[:space:]]*['\"][^'\"]+" "$ROOT/14-global-clinic-usp-integration" --exclude='*.md' --exclude='readme.txt'; then echo "Potential embedded secret detected" >&2; exit 1; fi
if grep -RInE "onclick=|onerror=|onload=|javascript:" "$ROOT/14-global-clinic-usp-integration" --include='*.php' --include='*.html' --include='*.txt'; then echo "Inline executable markup detected" >&2; exit 1; fi
if grep -RIn "data-gcu-event-token" "$ROOT/14-global-clinic-usp-integration"; then echo "Single-use measurement token found in package source/HTML path" >&2; exit 1; fi
if grep -RIn "GCU_Policy::same_origin_url" "$ROOT/14-global-clinic-usp-integration" --include='*.php'; then echo "Deprecated host-only URL helper must not be called" >&2; exit 1; fi
if ! grep -q "F14-FUT-24" "$ROOT/14-global-clinic-usp-integration/includes/class-gcu-future-policy.php"; then echo "24-feature Future Intelligence catalogue incomplete" >&2; exit 1; fi
if ! grep -q "SSH-F14-FUTURE-CTI-2026-v2.0" "$ROOT/14-global-clinic-usp-integration/global-clinic-usp-integration.php"; then echo "Future plan contract marker missing" >&2; exit 1; fi
if ! grep -q "GCU_Review80_Hardening" "$ROOT/14-global-clinic-usp-integration/global-clinic-usp-integration.php"; then echo "Eighty-pass hardening bootstrap missing" >&2; exit 1; fi
echo "Quality suite: PASS"
