# Release Evidence — v1.4.0 Future Conversion & Trust Intelligence Candidate

## Governing scope

- Base plan: `SSH-F14-PLAN-2026-v1.0`.
- Additive Founder-approved amendment: `SSH-F14-FUTURE-CTI-2026-v2.0`.
- Software candidate: `1.4.0`.
- Base schema: `10004`; Future CTI additive schema: `1`.
- Requirements implemented and mapped: original File 14 FR/NFR plus `F14-FUT-01`–`F14-FUT-24`.

## Exact-head evidence policy

This document intentionally does **not** carry forward historical PASS claims from v1.3.1 as proof for v1.4.0. After the final v1.4.0 coding commit, the exact tested SHA must independently prove:

- PHP 7.4 quality gate.
- PHP 8.3 quality gate.
- Policy, contract, reliability, central-plan and Future Intelligence tests.
- Fresh Review Round 1 after the final code change.
- Fresh Review Round 2 after the final code change.
- Secret / inline executable / stale-token / deprecated URL-helper scans.
- Deterministic double-build ZIP.
- SHA-256 archive verification and file-level SBOM.
- PR head parity with the exact tested SHA before any automated merge.

Until GitHub workflow evidence for the **current exact head** is read and verified, `Automated-QA Green`, `Packaged` and `main merged` remain unclaimed here.

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
