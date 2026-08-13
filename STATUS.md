# File 14 Status — v1.4.8 Current Twenty-Round Repository Candidate

## Repository coding status
- Governing freeze: consolidated central governing plan + `SSH-F14-PLAN-2026-v1.0` + Founder-approved additive amendment `SSH-F14-FUTURE-CTI-2026-v2.0` dated 2026-08-10.
- Current-cycle starting exact repository candidate: v1.4.7 PR #10 head `938b2e4945c2689336997a29ef53abf0a9d8b7b2`, based on `main` `db60c4bc5c37a5c88126b78c31b34c75236f33d7`.
- Software candidate: `1.4.8`.
- Base File 14 database schema: `10005`; Future CTI additive schema: `1`.
- The current 2026-08-13 twenty-round sequential corrective review is complete at the review/repair level. Every round was fully reviewed before that round's corrections began; correction/retest completed before the next round. Final ledger: `docs/REVIEW-20-2026-08-13-LEDGER-v1.4.8.md`.
- Defect rounds in this current cycle: `01, 06, 07, 08, 09, 10, 11, 19, 20`.
- Deterministic package target: `14-global-clinic-usp-integration-1.4.8.zip` + SHA-256 + file-level SBOM generated only from the exact final head being evaluated.
- Draft PR: `#10` (historical branch name `file14-seventh-10-round-corrective-review` → `main`); branch naming does not define release identity.

## Exact-current-head rule
Historical PRs, commits, workflows and packages are supporting history only. The exact final PR-head SHA produced by the Round 20 corrections must independently pass PHP 7.4/8.3 quality, retained policy/contract/reliability/central/Future/history gates, current regression coverage, both fresh post-code reviews, baseline integrity and deterministic package/SBOM before this candidate can be called `Automated-QA Green`.

After merge, the exact resulting `main` SHA becomes the exact current `main` SHA and repository truth, and must pass applicable workflows again. A green PR head is not a green post-merge main by inference.

## Retained review evidence
Historical ledgers remain retained through:
- `docs/REVIEW-80-LEDGER-v1.4.1.md`
- `docs/REVIEW-80-SECOND-LEDGER-v1.4.1.md`
- `docs/REVIEW-80-THIRD-LEDGER-v1.4.1.md`
- `docs/REVIEW-80-FOURTH-LEDGER-v1.4.2.md`
- `docs/REVIEW-80-FIFTH-LEDGER-v1.4.3.md`
- `docs/REVIEW-80-SIXTH-LEDGER-v1.4.4.md`
- `docs/REVIEW-10-SEVENTH-LEDGER-v1.4.5.md`
- `docs/REVIEW-10-EIGHTH-LEDGER-v1.4.6.md`
- `docs/REVIEW-10-NINTH-LEDGER-v1.4.7.md`
- prior `docs/REVIEW-20-TENTH-LEDGER-v1.4.8.md`
- current `docs/REVIEW-20-2026-08-13-LEDGER-v1.4.8.md`.

## Truth-status boundary
`Specified`, `Coded`, `Packaged`, `Automated-QA Green`, `Staging-Accepted`, `Live-Deployed`, and `Operational` remain separate states.

External gates remain: real staging install/upgrade and DB/schema verification; File00/07/08/09/20/24/25 real-role integration; browser/accessibility/RTL/LTR/400% zoom testing; performance/provider/DB/queue failure drills; privacy export/erase/legal-hold and consent/GPC journeys; backup/restore/rollback rehearsal; Founder staging acceptance; controlled production deployment; live smoke test; and deployed-artifact checksum/parity confirmation.

No `Staging-Accepted`, `Live-Deployed` or `Operational` claim is made here.

**Exact deployed code is unverified; repository-based diagnosis is provisional with respect to the live site.**
