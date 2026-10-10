# File 14 — Eighth independent twenty-round source/code and cross-file audit (R8, v1.4.12)

**Date:** 2026-10-10. **Frozen baseline main SHA:** `7863c2bf2aa70cb34a043c10af488b08e58827aa` (v1.4.11).

## Source hierarchy and audit method

- User-supplied original File 14 Global Clinic USP and Conversion Integration master plan, File 14 Future Conversion & Trust Intelligence 24 amendment, consolidated three central governing plans, and actual File 14 code.
- Canonical related source snapshots: File07 `2f4a89707724fd2b9946600afe10ddab27ec3c2d`; File08 `70541974ce0ffb16aebef557c3016eb7447662f4`; File09 `cfc5f781a766330314dc98c42abeca0eb7786eba`; File01 `adf6dbb9980a85f25f7cf4c2ee679b52703c2e71`; File20 `8a4dbcaf4fef8e926b9b834ecfde16c21a0f00ca`. These SHAs are repository snapshots, **not** evidence about deployed files.
- Plan requirement `F14-FR-011`: an impression, CTA selection, destination arrival, and owner-started application/booking are different lifecycle facts; a click cannot imply success. Further Future CTI analytics must honor owner, consent, and evidential truth.
- Performed **20 distinct static-source rounds** before any corrective editing. Each round re-executed **15 common source and plan invariants** (FR 001–016, Future 01–24 mapping, exact baseline plugin, central zero commission, File01/07/08/09/20 bridges, privacy consent, token validation, public event blocking, install schema guards, approval capability, and same-origin link rules) plus **one round-specific adversarial assertion**. Total predicate executions: **320**. This is a focused static source audit, **not** 20 independent installed WordPress suites, simulated traffic tests, or production checks.

## Twenty rounds — frozen v1.4.11, before fixes

| Pass | Additional focus (in addition to common 15) | Review result |
|---:|---|---|
| 01 | Repository-owned measurement stage boundary | **R8-D01**: direct internal `record_event` accepted companion-owned `destination_loaded`, `application_started`, `booking_started` |
| 02 | Unverified legacy owner handoff rate | **R8-D02**: deeper method inspection showed quality-score inputs could use historical unverified destination rows; initial narrow substring probe did not reach the assignment, and its false-clean classification was corrected before patching |
| 03 | Owner-native signed/versioned outcome contract | **R8-G01 / existing Issue #19**: native File07/08/09 acknowledgement still absent; File14 deliberately fails closed |
| 04 | Historical owner stages in `funnel_summary` | **R8-D03**: pre-R6 owner-only rows still appeared as reportable funnel stage counts |
| 05 | Historical owner stages in `friction_summary` | **R8-D04**: pre-R6 owner-only rows still appeared in source stage totals |
| 06 | Rate-limit enforcement | Source probe clean |
| 07 | Global Privacy Control / consent | Source probe clean |
| 08 | Idempotency/replay control | Source probe clean |
| 09 | Transactional outbox handling | Source probe clean |
| 10 | Event inbox deduplication | Source probe clean |
| 11 | Minimum reporting cohort | Source probe clean |
| 12 | Public claim parity and trust gate | Source probe clean |
| 13 | Quality evidence suppression | Source probe clean, except R8-D02 owner-stored legacy input |
| 14 | Measured privacy effectiveness | Source probe clean |
| 15 | AI source-grounded factual-copy restrictions | Source probe clean at structural level |
| 16 | Future governance record source/reviewer/date gates | Source probe clean at structural level |
| 17 | Event consumption ownership adapter | Source probe clean |
| 18 | Source migration and rollback guards | Source probe clean; live DB unknown |
| 19 | No File14-owned booking write | Source probe clean |
| 20 | Source-level adversarial review and friction sanitization | Source probe clean |

**Original source check classification:** 15 clean, **4 File14-owned defects (passes 1, 2, 4, 5)** and **one pending cross-file implementation (pass 3)**. Four defects share the same root cause: direct acquisition events and historical companion-outcome facts were insufficiently separated below the public REST boundary.

## Single corrective stage after completing the twenty passes

- Added `GCU_Future_Policy::acquisition_stage_reportable()`: only `impression` and `cta_selected` belong to File14 acquisition. Any destination load, doctor application, or booking requires a separately approved, attested native-owner protocol.
- Enforced the same allowlist **inside** `GCU_Repository::record_event()`, preventing calls bypassing REST handlers. Unknown stage remains 400; companion-owner stage is rejected with 403. No File07/08/09 tables or status are mutated.
- Changed private `GCU_Repository::funnel_summary()` to exclude historic unverifiable owner-only stages, still applying minimum cohorts to acquisition totals, and to disclose `owner_stage_evidence_status=not_attested_excluded` without exposing suppressed legacy counts.
- Changed `GCU_Future_Intelligence::friction_summary()` similarly; unsupported cross-branch sequential dropoffs remain unavailable. The result explicitly describes the owner-stage evidence gap.
- Set owner-loaded evidence in `GCU_Future_Intelligence::quality_score()` to **zero usable owner confirmations** while no attested native contract exists; historic browser-staged rows cannot generate a positive numerical handoff-success metric. Overall score remains null/provisional.
- Added regression assertions for all three rejected owner lifecycle stages; source assertions cover repository writes, private funnel masking, friction masking and no fictitious handoff metric. Source-side regressions and CI alone do **not** prove real database or cross-module integration.
- Candidate version **1.4.12** with unchanged base schema target **10005** and Future schema **1**. Exact release/CI version gates updated.

## Open contractual work — not claimed fixed

`R8-G01` / **GitHub Issue #19**: File07/08/09 owner-side, authorized, consent-aware event contract with native per-event provenance, File01 schema registration, anti-replay/idempotency and real integration tests. No consumer may infer successful bookings/applications from File14 clicks or legacy rows. File14's safeguard excludes unverifiable legacy stage counts; enabling future confirmed owner metrics requires a new formally approved ingestion/provenance design and database migration/reconciliation if applicable.

## Operational completion conditions

Current branch exact HEAD / CI status must be checked after last change; merge must be followed by checks on the new **main** commit. Rollout requires live reality freeze: active plugin version, deployed artifact checksum, runtime errors/logs, relevant DB tables/rows, schema and migrations, options/dependencies, staging restore/rollback and real journey evidence.

Repository HEAD (baseline): `7863c2bf2aa70cb34a043c10af488b08e58827aa`.
Deployed Version: **unverified**.
DB Version: **unverified** (10005 / Future 1 are *code targets*).
Migration State: **unverified**.
Live Verification Status: **not performed**.

**Exact deployed code ابھی unverified ہے؛ repository-based diagnosis provisional ہے۔**
