# Release Evidence — v1.4.5 Seventh Ten-Round Repository Candidate

## Governing scope

- Base plan: `SSH-F14-PLAN-2026-v1.0`.
- Additive Founder-approved amendment: `SSH-F14-FUTURE-CTI-2026-v2.0`.
- Seventh-cycle baseline: exact pre-review merged `main` `db60c4bc5c37a5c88126b78c31b34c75236f33d7` (`v1.4.4`).
- Software candidate: `1.4.5`.
- Base schema: `10005`; Future CTI additive schema: `1`.
- Requirements retained: original File 14 FR/NFR plus `F14-FUT-01`–`F14-FUT-24`.
- Ownership remains bounded: no doctor/clinic/application/appointment/payment/verification/shell source of truth is created here.
- Draft review integration: PR `#10`, branch `file14-seventh-10-round-corrective-review` → `main`.

## Exact-head evidence policy

Historical green results are supporting history only. For every current File 14 source state, the exact review/main SHA being accepted must independently prove PHP 7.4 and PHP 8.3 quality, policy/contract/reliability/central/Future regression tests, all retained historical review gates, the dedicated seventh-cycle regression test, two fresh post-code review rounds, secret/inline/stale-token/deprecated-helper scans, deterministic double-build ZIP, SHA-256/file-level SBOM, baseline integrity, PR-head parity and **fresh post-merge** exact-main acceptance.

The **third independent eighty-pass** review established the durable exact-current-main rule; the fourth, fifth, sixth and this seventh ten-round corrective cycle retain and strengthen that rule rather than replacing it.

`Automated-QA Green`, `Packaged`, `main merged`, `Staging-Accepted`, `Live-Deployed` and `Operational` remain separate evidence claims.

## Seventh ten-round corrective evidence areas

The seventh corrective cycle reopened exact `main` `db60c4bc5c37a5c88126b78c31b34c75236f33d7` and reviewed the corrected state sequentially ten times. Defects were found in rounds 01, 02, 03, 05, 09 and 10; each was corrected before the next round or, for round 10, before its final regression closure.

Corrected failure classes include: File 00 authorization adapters using truthy/default-allow semantics instead of explicit grant; normalized database lock scopes collapsing to an unsafe generic name; consumer filters being able to rewrite canonical Files 07/08/09 destination URLs; privacy erasure reporting completion without read-back verification of subject-link deletion; committed generated Python bytecode; conflicting conversion-event UUID reuse being accepted without authoritative identity comparison; and stale version-specific QA/documentation gates that no longer matched the v1.4.5 candidate.

The six repository review ledgers are:

- `docs/REVIEW-80-LEDGER-v1.4.1.md`
- `docs/REVIEW-80-SECOND-LEDGER-v1.4.1.md`
- `docs/REVIEW-80-THIRD-LEDGER-v1.4.1.md`
- `docs/REVIEW-80-FOURTH-LEDGER-v1.4.2.md`
- `docs/REVIEW-80-FIFTH-LEDGER-v1.4.3.md`
- `docs/REVIEW-80-SIXTH-LEDGER-v1.4.4.md`

The current seventh-cycle sequential ten-round record is `docs/REVIEW-10-SEVENTH-LEDGER-v1.4.5.md`; it is intentionally separate from the six historical eighty-pass ledgers.

## External evidence still mandatory

Repository success cannot prove WordPress/Hostinger staging or live behavior. Staging, deployed code, live DB/schema/migration and operational behavior remain separate evidence classes. Separate gates still include exact deployed artifact/checksum parity, fresh install/upgrade with real WordPress/MySQL/InnoDB, File00/07/08/09/20/24/25 integration, browser/accessibility/RTL/LTR/400% zoom evidence, performance/failure drills, verified backup restore and rollback rehearsal, explicit Founder staging acceptance, production deployment, live smoke tests, monitoring and deployed-artifact parity confirmation.

No `Staging-Accepted`, `Live-Deployed` or `Operational` claim is made by this repository document.

**Exact deployed code is unverified; repository-based diagnosis is provisional with respect to the live site.**
