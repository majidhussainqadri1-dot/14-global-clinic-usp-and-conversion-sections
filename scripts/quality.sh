#!/usr/bin/env bash
set -euo pipefail
ROOT="$(cd "$(dirname "${BASH_SOURCE[0]}")/.." && pwd)"

find "$ROOT/14-global-clinic-usp-integration" "$ROOT/tests" -name '*.php' -print0 | xargs -0 -n1 php -l
php "$ROOT/tests/policy-tests.php"
php "$ROOT/tests/contract-tests.php"
php "$ROOT/tests/central-plan-tests.php"

if grep -RInE "(password|secret|api[_-]?key|private[_-]?key)[[:space:]]*[:=][[:space:]]*['\"][^'\"]+" "$ROOT/14-global-clinic-usp-integration" --exclude='*.md' --exclude='readme.txt'; then
  echo "Potential embedded secret detected" >&2
  exit 1
fi

if grep -RInE "onclick=|onerror=|onload=|javascript:" "$ROOT/14-global-clinic-usp-integration" --include='*.php' --include='*.html' --include='*.txt'; then
  echo "Inline executable markup detected" >&2
  exit 1
fi

echo "Quality suite: PASS"
