#!/usr/bin/env python3
from pathlib import Path

ROOT = Path(__file__).resolve().parents[1]

def replace_exact(path: Path, old: str, new: str) -> None:
    text = path.read_text(encoding='utf-8')
    if old not in text:
        raise SystemExit(f'Exact correction anchor missing: {path}')
    path.write_text(text.replace(old, new), encoding='utf-8')

review = ROOT / 'scripts/review80.py'
replace_exact(
    review,
    '("15 STATUS current candidate truth is 1.4.1", "v1.4.1 Eighty-Pass Corrective Candidate" in status and "Software candidate: `1.4.1`" in status),',
    '("15 STATUS current repository truth is 1.4.1", "v1.4.1 Repository Release State" in status and "Software candidate: `1.4.1`" in status),'
)

ledger = ROOT / 'docs/REVIEW-80-SECOND-LEDGER-v1.4.1.md'
replace_exact(
    ledger,
    '| 71 | Status candidate/merged contradiction | **DEFECT:** same status contradiction identified in round 04 was confirmed at the status acceptance gate; corrected before final acceptance. |',
    '| 71 | Status truth + inherited Review80 assertion | **DEFECT:** the status contradiction identified in round 04 was re-confirmed, and exact-head CI then exposed a second QA-harness drift: the inherited first `scripts/review80.py` still required the obsolete “Corrective Candidate” phrase. The status remained truthful; the inherited gate was corrected to require v1.4.1 + `Repository Release State`. |'
)
replace_exact(
    ledger,
    'Rounds 70 and 71 are deliberate re-checks of the same documentation defects first found in rounds 05 and 04; therefore there are **13 distinct defect classes across 15 defect-bearing review rounds**.',
    'Round 70 is a deliberate re-check of the release-evidence defect first found in round 05. Round 71 re-checks the status defect from round 04 and additionally records the inherited first-Review80 QA-harness drift exposed by exact-head CI; therefore there are **14 distinct defect classes across 15 defect-bearing review rounds**.'
)
print('Second-review exact-head QA harness correction applied.')
