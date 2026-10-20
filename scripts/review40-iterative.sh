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
  # R68: each complete iteration additionally exercises forty actual PHP
  # companion-owner method calls. Complete the full audit before any next
  # iteration; never misstate mocked contracts as WordPress staging tests.
  if ! php "$ROOT/tests/r68-forty-native-owner-contract-behavior-tests.php" >"$tmp" 2>&1; then
    cat "$tmp" >&2
    echo "R68 native owner behavior review $round: FAILED; subsequent rounds not started." >&2
    exit 1
  fi
  if [[ $(grep -c '^R68 native case [0-9][0-9]/40 PASS:' "$tmp") -ne 40 ]] ||
      ! grep -q 'R68 40/40 owner manifest source method behavior checks PASS' "$tmp"; then
    cat "$tmp" >&2
    echo "R68 owner behavior review $round incomplete." >&2
    exit 1
  fi
  if ! php "$ROOT/tests/r69-forty-privacy-db-read-failure-tests.php" >"$tmp" 2>&1; then
    cat "$tmp" >&2
    echo "R69 privacy export and erasure behavior review $round: FAILED; stop here." >&2
    exit 1
  fi
  if [[ $(grep -c '^R69 privacy behavior [0-9][0-9]/40 PASS:' "$tmp") -ne 40 ]] ||
     ! grep -q 'R69 40/40 native File14 privacy export/erasure mock behavior tests PASS' "$tmp"; then
    cat "$tmp" >&2
    echo "R69 privacy behavior review $round incomplete." >&2
    exit 1
  fi
  count=$((count+1))
  echo "R41 full plan source review $round/40: PASS (40 source contracts; FUT17 blocked)"
  echo "R69 complete source iteration $round/40: PASS (40 plan + 40 owner + 40 privacy behaviors; FUT17 owner CAS remains blocked)"
  echo "R68 complete source iteration $round/40: PASS (40 requirements + 40 native owner behavior cases; FUT17 blocked)"
done
php "$ROOT/tests/r34-experiment-lifecycle-regressions.php"
echo "R69: 40 repeated full source rounds × (40 plan contracts + 40 native owner method tests + 40 privacy export/erasure DB-failure tests); FUT17 changed-placement CAS remains blocked; NOT live/staging WordPress E2E."
