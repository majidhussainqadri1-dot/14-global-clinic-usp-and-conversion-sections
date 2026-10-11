# File 14 R20 — Twenty Fresh Exact-Source / Central / Companion Plan Reviews

**Date:** 2026-10-11. **Frozen pre-review main:** `3b9a926613f9d9f1b5c22c2e44174df00ef0519f` (v1.4.23). **Governing sources:** User-provided File14 Global Clinic USP and Conversion Integration v2.0 Future CTI24 amended 2026-08-10 (F14-FR-001–016 and F14-FUT-01–24); Three Central Plans Consolidated Governing Master Plan 2026; File07, File09 and File25 corrected plans; exact File14 source and its File00/01/07/08/09/19/20/24/25 companion adapter interfaces. Live deployed source, WordPress database/options/migrations/logs and Staging were not accessed.

Twenty **separate static code/specification/contract lenses**, all completed before any R20 coding. SOURCE PRESENT means the corresponding named guard/contract exists; it is **not** a twenty-pass staging or entire-feature acceptance claim.

| Round | Requirement/owner boundary | Frozen finding |
|---:|---|---|
| 01 | F14-FR-001..016 base trace | SOURCE PRESENT — 16 stable IDs; no full feature verification implied |
| 02 | F14-FUT-01..24 amendment | SOURCE PRESENT — all 24 feature identities in catalogue/trace |
| 03 | File00 Founder/native capabilities | SOURCE PRESENT — full GCU_Capabilities and File00 native adapter inspection cleared an initial overly narrow token-check false alarm |
| 04 | File01 manifests/routes | SOURCE PRESENT — canonical registry/route currentness |
| 05 | File07 doctor directory owner | SOURCE PRESENT — doctor discovery remains File07-owned |
| 06 | File08 booking/clinic truth | SOURCE PRESENT — clinic probes, no fabricated appointment |
| 07 | File09 onboarding | SOURCE PRESENT — native handoff; verification not recreated |
| 08 | File20 shell/placements | SOURCE PRESENT — only semantic slots consumed |
| 09 | File25 visual contract | SOURCE PRESENT — versioned visual fallback |
| 10 | File24 assurance | SOURCE PRESENT — successful-boot runtime boundary |
| 11 | File19 notifications | SOURCE PRESENT — native notification adapter |
| 12 | Privacy/consent/GPC/cohort | SOURCE PRESENT — consent/GPC/minimum cohort controls |
| 13 | Approved trust claim/freshness | SOURCE PRESENT — approved/current claim gates |
| 14 | Accessible public journeys | SOURCE PRESENT — semantic markup and ARIA states |
| 15 | en-US/ur-PK/ar-SA and RTL | SOURCE PRESENT — locale/direction implementation |
| 16 | Public blocks filters | **LOCAL DEFECT R20-D01** — `GCU_REST::blocks` passed nested/invalid query params to sanitizers; not rejected by an exact public query type gate |
| 17 | Public funnel event fields | **LOCAL DEFECT R20-D02** — `GCU_REST::event` and direct `GCU_Repository::record_event` accepted untyped stage/destination/event-ID inputs prior to sanitization/UUID decisions |
| 18 | Early Founder governance + replay fields | **LOCAL DEFECT R20-D03** — pre-callback status/key sanitized before typed governance check, plus nested replay stage/destination coerced |
| 19 | Real File07/08/09 owner-signed outcome events | **OPEN EXTERNAL Issue #19** — native signed/consented/versioned arrival/application/booking evidence and File01 schema registration still unproven |
| 20 | FUT17 stopped-experiment approved baseline restore | **OPEN DESIGN Issue #22** — snapshot/placement mapping and atomic restoration/rollback/staging proof not implemented |

**Before corrections:** 15 source-present lenses, 3 local defects, 2 external/design gaps. All twenty reviews finished **before** the single R20 correction batch.

## Consolidated R20 correction

- **R20-D01:** `GCU_Future_Policy::public_blocks_query_is_typed` validates optional slot/audience/locale types, lengths and canonical shapes before `GCU_REST::blocks` read; malformed request returns controlled HTTP 400.
- **R20-D02:** `acquisition_event_input_is_typed` validates stage, optional destination, optional UUID shapes. The public REST endpoint and native File14 event writer both reject invalid nested inputs before DB or UUID operations. Forbidden owner-only event stages remain forbidden: a CTA click is not owner-booking confirmation.
- **R20-D03:** `GCU_Fifth_Review_Hardening::guard_rest_request` validates input types first, before deciding whether Founder claim approval is required; verified public flag parsing prevents string truthiness. Replay guard no longer coerces nested stage/destination.
- Behavioral test cases in `tests/future-intelligence-tests.php`, regression assertions in `tests/fifth-review-regression-tests.php`. Software candidate **v1.4.24**. **Source schema targets remain base 10005 / Future 1; no schema migration or live DB assertion.**

## Unfinished cross-file / deployment acceptance

Issue #19 requires an approved File07/08/09 owner-acknowledged, consented, replay-safe event contract with File01 registry, native staged event production and end-to-end tests before conversion-success scoring. Issue #22 requires Founder-approved experiment baseline/variant snapshot, concurrency-safe restoration or safe fallback, migration plan, rollback and staging. Neither can be truthfully claimed completed by File14-only input corrections.

Before release: exact candidate PHP7.4/PHP8.3 QA and all regressions; Fresh Review1/2; deterministic package/SBOM; pull-request tests; fresh *merged-main* exact-SHA tests; independent WordPress staging backup/restore and Founder acceptance; deployed artifact/checksum parity; live browser retest.

**Live incident truth fields:** Repository HEAD: frozen prechange `3b9a926613f9d9f1b5c22c2e44174df00ef0519f`; Deployed Version: **unverified**; DB Version: **unverified**; Migration State: **unverified**; Live Verification Status: **not performed**.

**Exact deployed code ابھی unverified ہے؛ repository-based diagnosis provisional ہے۔**
