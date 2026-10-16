#!/usr/bin/env bash
set -euo pipefail
ROOT="$(cd "$(dirname "$0")/.." && pwd)"
tmp="$(mktemp)"
trap 'rm -f "$tmp"' EXIT
count=0
for round in $(seq 1 40); do
  if ! php "$ROOT/tests/forty-plan-regressions.php" >"$tmp" 2>&1; then
    cat "$tmp" >&2
    echo "R41 repeated plan review $round: FAILED; subsequent rounds not started." >&2
    exit 1
  fi
  if [[ $(grep -c '^Review [0-9][0-9]: ' "$tmp") -ne 40 ]] ||
      ! grep -q 'FUT17 changed-placement restore BLOCKED' "$tmp"; then
    cat "$tmp" >&2
    echo "R41 review $round incomplete or false restoration claim." >&2
    exit 1
  fi
  count=$((count+1))
  echo "R41 full plan source review $round/40: PASS (40 source contracts; FUT17 blocked)"
done
php "$ROOT/tests/r34-experiment-lifecycle-regressions.php"
echo "R41: 40 repeated exact-source plan scans plus prior 40 parameterized R34 lifecycle cases. F14 R41-specific 40 actual-method SQL mocks run separately in quality.sh. No staging/live acceptance."
