# Release Evidence — v1.4.3 Fifth-Review Repository Candidate

## Governing scope

- Base plan: `SSH-F14-PLAN-2026-v1.0`.
- Additive Founder-approved amendment: `SSH-F14-FUTURE-CTI-2026-v2.0`.
- Fifth-review baseline: exact post-fourth-review `main` `b9045a4229d052103a5546477f664ac88b6ff034`.
- Software candidate: `1.4.3`.
- Base schema: `10004`; Future CTI additive schema: `1`.
- Requirements retained: original File 14 FR/NFR plus `F14-FUT-01`–`F14-FUT-24`.
- Ownership remains bounded: no doctor/clinic/application/appointment/payment/verification/shell source of truth is created here.

## Exact-head evidence policy

Historical green results are supporting history only. For every current File 14 source state, the exact review/main SHA being accepted must independently prove:

- PHP 7.4 and PHP 8.3 quality gates.
- Policy, contract, reliability, central-plan, Future Intelligence and all dedicated corrective regression tests, including fifth-review regressions.
- First, second, third, fourth and fifth independent eighty-pass repository gates.
- Two fresh post-code review rounds after the final accepted source change.
- Secret / inline executable / stale-token / deprecated-helper scans.
- Deterministic double-build ZIP.
- SHA-256 archive verification and file-level SBOM.
- Baseline integrity.
- PR-head parity with the exact tested SHA before merge.
- A fresh post-merge run on the exact resulting `main` SHA before that SHA is called `Automated-QA Green`.

`Automated-QA Green`, `Packaged` and `main merged` are separate evidence claims; none is inherited from an older commit.

## Fifth-review corrective evidence areas

The fifth independent review re-opened exact post-fourth-review `main` and corrected additional failure classes:

- base and Future runtime readiness now revalidate actual required tables, InnoDB engines and critical columns for the current request rather than trusting version options alone;
- active/public Future governance records require Founder-level approval in addition to content-management permission on the governed REST path;
- AI copy assistance rejects personal/contact/identity/clinical base text before provider invocation and removes Urdu/Arabic dark-pattern or guarantee candidates before response delivery;
- conversion event UUID replay is idempotent only for the same stage, destination, pseudonymous subject and campaign identity; conflicting reuse is rejected;
- the scheduled experiment early-stop path now commits state plus its mandatory audit record atomically or rolls both back;
- stale documentation about File 20 local shell fallback, generic Future record/report transactionality, software candidate, ledger count and active release-gate count was corrected.

The earlier third and fourth review corrections remain regression-protected, including schema/column verification, non-destructive rollback preservation, audience isolation, File 00 authorization dependency, per-stage privacy suppression, recent-tail audit verification, shortcode cache freshness and File 20-only shell ownership.

Round-by-round evidence is maintained in:

- `docs/REVIEW-80-LEDGER-v1.4.1.md`
- `docs/REVIEW-80-SECOND-LEDGER-v1.4.1.md`
- `docs/REVIEW-80-THIRD-LEDGER-v1.4.1.md`
- `docs/REVIEW-80-FOURTH-LEDGER-v1.4.2.md`
- `docs/REVIEW-80-FIFTH-LEDGER-v1.4.3.md`

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
