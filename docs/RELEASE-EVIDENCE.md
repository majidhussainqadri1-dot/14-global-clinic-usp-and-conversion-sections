# Release Evidence — v1.4.1 Repository Release Evidence

## Governing scope

- Base plan: `SSH-F14-PLAN-2026-v1.0`.
- Additive Founder-approved amendment: `SSH-F14-FUTURE-CTI-2026-v2.0`.
- Software candidate: `1.4.1`.
- Base schema: `10004`; Future CTI additive schema: `1`.
- Requirements retained: original File 14 FR/NFR plus `F14-FUT-01`–`F14-FUT-24`.
- Corrective scope: no new doctor/clinic/appointment/payment/verification/shell owner; v1.4.1 hardens the approved File 14 trust, copy, experiment, privacy and evidence paths.

## Exact-head evidence policy

Historical v1.4.0 success is supporting history only. For every v1.4.1 corrective change, the exact current review/main SHA must independently prove:

- PHP 7.4 quality gate.
- PHP 8.3 quality gate.
- Policy, contract, reliability, central-plan, Future Intelligence and Review80 regression tests.
- The deterministic eighty-pass repository review gate (`scripts/review80.py`).
- Fresh Review Round 1 after the final change.
- Fresh Review Round 2 after the final change.
- Secret / inline executable / stale-token / deprecated URL-helper scans.
- Deterministic double-build ZIP.
- SHA-256 archive verification and file-level SBOM.
- PR head parity with the exact tested SHA before merge.

`Automated-QA Green`, `Packaged` and `main merged` may be claimed only from evidence for the exact relevant head; prior green runs are not silently carried forward.

## Corrective evidence areas

The v1.4.1 review specifically re-opened and corrected:

- public claim freshness and policy parity at request time;
- experiment guardrail semantics, not mere key presence;
- Urdu/Arabic dark-pattern, guarantee and positive-commission language;
- privacy suppression for individual funnel stages below cohort 10;
- direct personal/contact/identity markers in aggregate FAQ signals;
- Scenario Laboratory safe-mode truth;
- quality-score provisional evidence status;
- REST post-dispatch scoping to File 14 only;
- release-version/test/workflow/documentation alignment.

The detailed round-by-round evidence is maintained in `docs/REVIEW-80-LEDGER-v1.4.1.md`.

## External evidence still mandatory

Repository success cannot prove WordPress/Hostinger staging or live behavior. The following remain separate external gates:

- fresh install and upgrade/migration with real WordPress/MySQL/InnoDB;
- File 00/07/08/09/20/24/25 companion integration and degraded states;
- ethical intent/handoff, trust drawer, claim freshness/revalidation, parity blocking, jurisdiction copy, report/correction, experiment preflight/early-stop, FAQ/AI bounded behavior;
- keyboard, screen reader, 320–1920px, 400% zoom, EN LTR, UR/AR RTL, reduced motion, Save-Data/GPC/consent withdrawal;
- measured p75/p95 performance and load/failure paths;
- verified backup restore and rollback rehearsal;
- explicit Founder staging acceptance;
- production deployment, live smoke tests, monitoring and deployed-artifact parity confirmation.

No `Staging-Accepted`, `Live-Deployed` or `Operational` claim is made by this repository document.
