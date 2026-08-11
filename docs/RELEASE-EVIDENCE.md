# Release Evidence — v1.4.6 Eighth Ten-Round Repository Candidate

## Governing scope

- Base plan: `SSH-F14-PLAN-2026-v1.0`.
- Additive Founder-approved amendment: `SSH-F14-FUTURE-CTI-2026-v2.0`.
- Eighth-cycle starting point: corrected v1.4.5 PR head `0098c63d5f695d683c7057471d9c3683ec805522`, based on merged `main` `db60c4bc5c37a5c88126b78c31b34c75236f33d7`.
- Software candidate: `1.4.6`.
- Base schema: `10005`; Future CTI additive schema: `1`.
- Requirements retained: original File 14 FR/NFR plus `F14-FUT-01`–`F14-FUT-24`.
- Ownership remains bounded: File 20 is shell/slot authority; Files 07/08/09 own destination truth; File 25 owns public visual/design-system truth; File 00 remains authorization/verification authority where contracted.
- Draft review integration remains PR `#10`; its historical branch name does not determine release identity.

## Exact-head evidence policy

Historical green results are supporting history only. The **exact review/main SHA being accepted** must independently prove PHP 7.4 and PHP 8.3 quality, policy/contract/reliability/central/Future regression tests, all retained historical review gates, the dedicated eighth-cycle regression test, two fresh post-code review rounds, secret/inline/stale-token/deprecated-helper scans, deterministic double-build ZIP, SHA-256/file-level SBOM and baseline integrity. For the open PR this means the **exact current PR-head SHA**. After merge, **fresh post-merge** exact-main acceptance is mandatory.

The **third independent eighty-pass** review established the durable exact-current-main/post-merge policy. Later corrective cycles retain that policy; they do not replace it.

`Automated-QA Green`, `Packaged`, `main merged`, `Staging-Accepted`, `Live-Deployed` and `Operational` remain separate evidence claims.

## Eighth ten-round corrective evidence areas

The eighth corrective cycle continued from the exact corrected v1.4.5 PR head and reviewed the corrected state sequentially. Repository defects found before final exact-head closure include: concurrent event replay TOCTOU; semantically truncated idempotency fingerprints; concurrent logged-in privacy-subject initialization; owner readiness using receipt-time/no monotonic ordering; a noncanonical File 20 slot-readiness fallback; fallback URLs manufacturing owner readiness; analytics DB read failure masquerading as empty/suppressed metrics; and destination-bound funnel stages without mandatory destination identity. Release/test/documentation identity was then advanced to v1.4.6 without a schema change.

Round 10 additionally exposed stale retained QA/evidence contracts after the new cycle: second/third/seventh review assertions were tied to older prose/version identities, and the current traceability matrix had lost the explicit File 25 visual/design-owner statement. Those defects are corrected while preserving the historical safety invariants rather than weakening them.

The historical six eighty-pass ledgers remain:

- `docs/REVIEW-80-LEDGER-v1.4.1.md`
- `docs/REVIEW-80-SECOND-LEDGER-v1.4.1.md`
- `docs/REVIEW-80-THIRD-LEDGER-v1.4.1.md`
- `docs/REVIEW-80-FOURTH-LEDGER-v1.4.2.md`
- `docs/REVIEW-80-FIFTH-LEDGER-v1.4.3.md`
- `docs/REVIEW-80-SIXTH-LEDGER-v1.4.4.md`

The seventh ten-round record is `docs/REVIEW-10-SEVENTH-LEDGER-v1.4.5.md`. The current record is `docs/REVIEW-10-EIGHTH-LEDGER-v1.4.6.md`.

## External evidence still mandatory

Repository success cannot prove WordPress/Hostinger staging or live behavior. Separate gates still include exact deployed artifact/checksum parity; fresh install/upgrade with real WordPress/MySQL/InnoDB; File00/07/08/09/20/24/25 integration; browser/accessibility/RTL/LTR/400% zoom evidence; performance/failure drills; verified backup restore and rollback rehearsal; explicit Founder staging acceptance; production deployment; live smoke tests; monitoring; and deployed-artifact parity confirmation.

No `Staging-Accepted`, `Live-Deployed` or `Operational` claim is made by this repository document.

**Exact deployed code is unverified; repository-based diagnosis is provisional with respect to the live site.**
