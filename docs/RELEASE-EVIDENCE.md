# Release Evidence — v1.4.1 Repository Release Evidence

## Governing scope

- Base plan: `SSH-F14-PLAN-2026-v1.0`.
- Additive Founder-approved amendment: `SSH-F14-FUTURE-CTI-2026-v2.0`.
- Software candidate: `1.4.1`.
- Base schema: `10004`; Future CTI additive schema: `1`.
- Requirements retained: original File 14 FR/NFR plus `F14-FUT-01`–`F14-FUT-24`.
- Ownership remains bounded: no doctor/clinic/application/appointment/payment/verification/shell source of truth is created here.

## Exact-head evidence policy

Historical green results are supporting history only. For every current File 14 source state, the exact review/main SHA being accepted must independently prove:

- PHP 7.4 and PHP 8.3 quality gates.
- Policy, contract, reliability, central-plan, Future Intelligence and dedicated corrective regression tests.
- First, second and third independent eighty-pass repository gates.
- Two fresh post-code review rounds after the final accepted source change.
- Secret / inline executable / stale-token / deprecated-helper scans.
- Deterministic double-build ZIP.
- SHA-256 archive verification and file-level SBOM.
- Baseline integrity.
- PR-head parity with the exact tested SHA before merge.
- A fresh post-merge run on the exact resulting `main` SHA before that SHA is called `Automated-QA Green`.

`Automated-QA Green`, `Packaged` and `main merged` are separate evidence claims; none is inherited from an older commit.

## Third-review corrective evidence areas

The third independent review re-opened already-green repository assumptions and corrected additional failure classes, including:

- read/runtime fail-close parity during version/schema mismatch across REST, frontend, destination health and background jobs;
- base/Future schema verification of critical columns so a partial migration cannot be accepted merely from table existence or version options;
- serialized rollback and rollback coverage for Future governance records/options;
- controlled Future schema force-verification on repair/activation paths and periodic structural verification;
- stale block and linked-claim suppression at query time, not only after a scheduled sentinel;
- deterministic American-English canonical claim source truth separated from locale rendering;
- non-elevating authorization adapters over native WordPress capabilities;
- privacy exporter/eraser correction so another data subject is never conflated with the operator browser's guest cookies, plus paginated Future-report export/anonymization;
- Urdu/Arabic as well as English sensitive-data rejection for public reports and FAQ aggregate adapters;
- internal-only scenario-note publication protection;
- bounded rate and durable idempotency controls on state-changing REST paths;
- immediate revalidation cache policy for governed public copy;
- safe response trace identifiers;
- fail-closed containment if the tamper-evident audit chain or durable outbox cannot persist required evidence;
- cleanup of temporary corrective machinery before exact-head acceptance.

Round-by-round evidence is maintained in:

- `docs/REVIEW-80-LEDGER-v1.4.1.md`
- `docs/REVIEW-80-SECOND-LEDGER-v1.4.1.md`
- `docs/REVIEW-80-THIRD-LEDGER-v1.4.1.md`

## External evidence still mandatory

Repository success cannot prove WordPress/Hostinger staging or live behavior. The following remain separate external gates:

- exact deployed artifact/version/checksum parity with the accepted repository package;
- fresh install and upgrade/migration with real WordPress/MySQL/InnoDB, including column-level schema verification;
- File 00/07/08/09/20/24/25 companion integration and degraded states;
- ethical intent/handoff, trust drawer, claim freshness/revalidation, parity blocking, jurisdiction copy, report/correction, experiment preflight/early-stop, FAQ/AI bounded behavior;
- keyboard, screen reader, 320–1920px, 400% zoom, EN LTR, UR/AR RTL, reduced motion, Save-Data/GPC/consent withdrawal;
- measured p75/p95 performance and load/failure paths;
- verified backup restore and rollback rehearsal including Future governance records;
- explicit Founder staging acceptance;
- production deployment, live smoke tests, monitoring and deployed-artifact parity confirmation.

No `Staging-Accepted`, `Live-Deployed` or `Operational` claim is made by this repository document.
