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
  # R71: complete all forty direct File14 destination/filter adversarial cases
  # before beginning the next complete review pass; no live proof inferred.
  if ! php "$ROOT/tests/r71-forty-destination-filter-fail-closed-tests.php" >"$tmp" 2>&1; then
    cat "$tmp" >&2
    echo "R71 owner destination filter review $round: FAILED; stopping." >&2
    exit 1
  fi
  if [[ $(grep -c '^R71 filter behavior case [0-9][0-9]/40 PASS:' "$tmp") -ne 40 ]] ||
     ! grep -q 'R71 40/40 exact File14 owner destination/filter behavior tests PASS' "$tmp"; then
    cat "$tmp" >&2
    echo "R71 destination filter review $round incomplete." >&2
    exit 1
  fi
  # R73: check the actual File00 authorization adapter against forty
  # strictly typed identity, eligibility and filter outcomes in each round.
  if ! php "$ROOT/tests/r73-forty-file00-claim-and-filter-auth-tests.php" >"$tmp" 2>&1; then
    cat "$tmp" >&2
    echo "R73 File00 authorization round $round FAILED; stop before next round." >&2
    exit 1
  fi
  if [[ $(grep -c '^R73 File00 authorization case [0-9][0-9]/40 PASS' "$tmp") -ne 40 ]] ||
     ! grep -q 'R73: 40/40 direct File14 File00-claim/filter authorization cases PASS' "$tmp"; then
    cat "$tmp" >&2
    echo "R73 authorization review $round incomplete." >&2
    exit 1
  fi
  # R74: native File00 authoritative denials remain immutable after
  # WordPress smc_assertions_v1 compatibility-filter processing.
  if ! php "$ROOT/tests/r74-forty-native-file00-claim-monotonic-tests.php" >"$tmp" 2>&1; then
    cat "$tmp" >&2
    echo "R74 native File00 claim monotonic review $round FAILED." >&2
    exit 1
  fi
  if [[ $(grep -c '^R74 native assertion case [0-9][0-9]/40: PASS' "$tmp") -ne 40 ]] ||
     ! grep -q 'R74 40/40 actual File14 native-assertion authorization PHP mock cases PASS' "$tmp"; then
    cat "$tmp" >&2
    echo "R74 native File00 claim review $round INCOMPLETE." >&2
    exit 1
  fi
  count=$((count+1))
  echo "R74 complete source iteration $round/40: PASS (40 plan + 40 owner + 40 privacy + 40 destinations + 40 File00 typed + 40 native File00 filter escalation cases; FUT17 CAS blocked)"
  echo "R73 complete source iteration $round/40: PASS (40 plan + 40 native owner + 40 privacy + 40 CTA filters + 40 File00 authorization direct PHP cases; FUT17 CAS blocked)"
  echo "R71 complete source iteration $round/40: PASS (40 FR/FUT source requirements + 40 owner + 40 privacy + 40 CTA filter behavior cases; FUT17 CAS blocked)"
  echo "R41 full plan source review $round/40: PASS (40 source contracts; FUT17 blocked)"
  echo "R69 complete source iteration $round/40: PASS (40 plan + 40 owner + 40 privacy behaviors; FUT17 owner CAS remains blocked)"
  echo "R68 complete source iteration $round/40: PASS (40 requirements + 40 native owner behavior cases; FUT17 blocked)"
done
php "$ROOT/tests/r34-experiment-lifecycle-regressions.php"
echo "R71: 40 complete iterative source rounds × (40 plan checks + 40 owner behavior + 40 privacy behavior + 40 destination filter behavior checks) passed; FUT17 changed-placement CAS remains BLOCKED; no staging or live WordPress acceptance."

echo 'R73: 40 complete source review rounds x 5 distinct 40-case tests PASS; owner signed events, File20 CAS and File08 aggregate pending; no live WordPress parity.'

echo 'R74: 40 complete rounds x 6 families of forty source/mock PHP checks PASS; File01 signed events, File20 CAS and File08 aggregates remain blocked; staging/live unverified.'
