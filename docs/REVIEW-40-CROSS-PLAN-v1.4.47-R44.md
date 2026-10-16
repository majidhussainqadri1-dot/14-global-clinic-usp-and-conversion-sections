# R44 File14 — 40 APPROVED requirement reviews, pre-correction frozen register

**Date:** 2026-10-16. **Exact source HEAD BEFORE R44 changes:** `f4ee9baf3881c47d9a255cae920ec96204ec59ef`; PHP source plugin **v1.4.46**; Base schema target `10005`, Future target `1` (**not real DB confirmation**).

## Authoritative plans reviewed
- Actual attached `14-File-14-Global-Clinic-USP-and-Conversion-Integration-v2.0-Future-CTI-24-Amended-2026-08-10.docx`. Its explicit `F14-FUT-13` says `minimum cohort, purpose limitation, consent/GPC respect, no sensitive raw analytics`; `F14-FUT-14` demands a minimum-sample gate with no false certainty; `F14-FUT-15` requires suppression of small cohorts in analytics, anomaly and FAQ inference with **default minimum cohort 10** and stricter override possible.
- Actual attached `Sabri-Social-Homeopathy-Platform-Three-Central-Plans-Consolidated-Governing-Master-Plan-2026۔۔۔4۔۔۔۔۔.docx`: implementation truth separate from staging accepted and live deployed; canonical ownership, privacy lifecycle and real owner API acceptance are mandatory.
- Actual companion plans File07 Doctor Directory, File08 Clinic/Appointments, File09 Doctor Onboarding, File25 Public UI. File01 central registry, File20 shell and File24 security remain distinct canonical owners; source contracts not assumed installed.
- The 40 distinct lenses are 16 FR and 24 FUT, as in the approved preceding specification ledger. They are NOT 40 unique live WordPress E2E tests. The independent quality script repeats 40 source markers 40 times.

| Lens | Requirement | Actual plan feature/boundary | R44 PRE-CODE result |
|---:|---|---|---|
| 1 | F14-FR-001 | Patient route/value proposition | R44 scope/source reviewed; production not inspected |
| 2 | F14-FR-002 | Doctor value and zero commission | R44 scope/source reviewed; production not inspected |
| 3 | F14-FR-003 | Primary CTAs and owner destinations | R44 scope/source reviewed; production not inspected |
| 4 | F14-FR-004 | Patient/doctor journey sequence | R44 scope/source reviewed; production not inspected |
| 5 | F14-FR-005 | Trust, verification and no cure guarantee | R44 scope/source reviewed; production not inspected |
| 6 | F14-FR-006 | Zero commission and voluntary support | R44 scope/source reviewed; production not inspected |
| 7 | F14-FR-007 | File20 owned route/placement registry | R44 scope/source reviewed; production not inspected |
| 8 | F14-FR-008 | File25 consumes canonical content blocks | R44 scope/source reviewed; production not inspected |
| 9 | F14-FR-009 | File07/08/09 owner destination health | R44 scope/source reviewed; production not inspected |
| 10 | F14-FR-010 | Consent/GPC attribution | R44 scope/source reviewed; production not inspected |
| 11 | F14-FR-011 | File01/07/08/09 owner-native outcomes | OPEN File01/07/08/09 attested owner registration #19 |
| 12 | F14-FR-012 | Founder experiment lifecycle | R44 scope/source reviewed; production not inspected |
| 13 | F14-FR-013 | en-US/ur-PK/ar-SA localization | R44 scope/source reviewed; production not inspected |
| 14 | F14-FR-014 | FAQ source/policy history | R44 scope/source reviewed; production not inspected |
| 15 | F14-FR-015 | Accessibility, mobile and low-data | R44 scope/source reviewed; production not inspected |
| 16 | F14-FR-016 | Claim basis/freshness/audit | R44 scope/source reviewed; production not inspected |
| 17 | F14-FUT-01 | Intent router no sensitive profiling | R44 scope/source reviewed; production not inspected |
| 18 | F14-FUT-02 | Allowlisted destination handoff | R44 scope/source reviewed; production not inspected |
| 19 | F14-FUT-03 | Trust evidence current-public gate | R44 scope/source reviewed; production not inspected |
| 20 | F14-FUT-04 | Freshness sentinel expiry/review/historic audit | R44 scope/source reviewed; production not inspected |
| 21 | F14-FUT-05 | Commission parity and source copy scan | R44 scope/source reviewed; production not inspected |
| 22 | F14-FUT-06 | Approved jurisdictional text | R44 scope/source reviewed; production not inspected |
| 23 | F14-FUT-07 | Protected semantic copy diff | R44 scope/source reviewed; production not inspected |
| 24 | F14-FUT-08 | Dark-pattern disallow list | R44 scope/source reviewed; production not inspected |
| 25 | F14-FUT-09 | Honest destination failover | R44 scope/source reviewed; production not inspected |
| 26 | F14-FUT-10 | Nonmutating scenario previews | R44 scope/source reviewed; production not inspected |
| 27 | F14-FUT-11 | Provisional evidence-based quality score | R44 scope/source reviewed; production not inspected |
| 28 | F14-FUT-12 | Auditable misleading copy reports | R44 scope/source reviewed; production not inspected |
| 29 | F14-FUT-13 | Consent and aggregate friction | R44-D01: per-stage browser cohort protection omission |
| 30 | F14-FUT-14 | Suppressed unverified funnel anomaly | R44-D02: version-v1 owner event producer filters missing |
| 31 | F14-FUT-15 | Minimum-ten cohort privacy | R44-D01: browser stage <10 disclosure risk |
| 32 | F14-FUT-16 | Founder approval experiment preflight | R44 scope/source reviewed; production not inspected |
| 33 | F14-FUT-17 | Experiment auto-stop AND changed-placement restore | OPEN File20/25 changed-placement rollback #22 |
| 34 | F14-FUT-18 | FAQ gap drafts only | R44 scope/source reviewed; production not inspected |
| 35 | F14-FUT-19 | Locale/claim consistency graph | R44 scope/source reviewed; production not inspected |
| 36 | F14-FUT-20 | Translation source and terminology lock | R44 scope/source reviewed; production not inspected |
| 37 | F14-FUT-21 | AI draft/no autoshare | R44 scope/source reviewed; production not inspected |
| 38 | F14-FUT-22 | Public reviewed change log | R44 scope/source reviewed; production not inspected |
| 39 | F14-FUT-23 | Choose safely informational guidance | R44 scope/source reviewed; production not inspected |
| 40 | F14-FUT-24 | Doctor nonbinding readiness | R44 scope/source reviewed; production not inspected |

## Newly frozen defects — BEFORE ANY R44 source changes

**R44-D01 (F14-FUT-13 + FUT-15):** `GCU_Future_Intelligence::friction_summary()` queries `SELECT funnel_stage, COUNT(*) total`, computes total across reportable stages, and if combined total >=10 returns **all per-stage counts**. Thus e.g. 12 CTA selections and 1 destination load returns the rare `destination_loaded:1` stage despite the user plan's minimum-cohort gate of 10. This is a verified **source-level privacy defect**, not a claim real users were exposed. Fix: suppress the entire stage name+count for any stage <10, honor stricter future threshold, flag partial suppression, do not manufacture drop-off ratios.

**R44-D02 (F14-FR-011 + FUT-13/14):** `GCU_Owner_Outcomes::observed_anomaly()` and `owner_stage_summary()` WHERE filter includes named v1 owner event types and consent, but **omits the required `event_version=1` predicate**. An event row of a different version using the same string type could pollute v1-labeled owner-native observation. The producer stores v1, but the query must enforce producer contract version independently; database row provenance in production remains unverified. Fix: add both filters and 40 real-method SQL-mock tests proving v1 enforced and cohort-safe.

## Unresolved distinct cross-file owner gates

- **Issue #19:** true File01-registered/attested File07/08/09 owner-origin application/booking/directory events, consent/retraction and staging-real acceptance. Source hooks are not live proof.
- **Issue #22:** approved actual changed-placement restoration from File14 experiment to File20/25, with provenance and compare-and-swap + staging rollback. Baseline no-op is not actual restoration.

## Ordered corrective discipline

This full set of **forty approved plan lenses is frozen before any R44 PHP edits**. Once all source defects are gathered, do one corrective batch with separate 40 real-method SQL-mocked scenarios and existing 40×40 source-review loops, PHP7.4/8.3, fresh reviews, reproducible package and SBOM; merge only after exact PR QA, re-test exact merged main. Proposed source plugin v1.4.47, no schema migration; keep source, staging, production separate.

Repository HEAD = `f4ee9baf3881c47d9a255cae920ec96204ec59ef`; Deployed Version = UNVERIFIED; Actual DB Version = UNVERIFIED; Migration State = UNVERIFIED; Live Verification Status = NOT RUN. Exact deployed code ابھی unverified ہے؛ repository-based diagnosis provisional ہے۔

## R44 corrected source batch — after all forty requirement lenses were inspected

**Chronological evidence:** the full pre-code R44 ledger was first committed at `db277fa13e955b59cc32ff0b03a242ad9db10cfe`, BEFORE modifying any runtime PHP. The following fixes therefore happened only after the 40-lens review was fully recorded.

| Finding | Exact source code correction | New test |
|---|---|---|
| **R44-D01 (FUT13/FUT15)** | `GCU_Future_Intelligence::friction_summary()` now checks `GCU_Future_Policy::cohort_allowed` separately for **each** stage. A stage <10 is withheld with its category label and count; no stage names or inferred drop-offs are returned if all are small. For mixed cohorts, only >=10 stage counts are included and `suppressed=true`. No fabricated linked conversion. | `tests/r44-per-stage-privacy-owner-version-tests.php` actual PHP method SQL mocks test mixed 10+1, 1+10, combined ten but two small, both large, total small, protected REST and telemetry origin. |
| **R44-D02 (FR011/FUT14)** | `GCU_Owner_Outcomes::observed_anomaly()` and `owner_stage_summary()` filter `AND event_version=1` in independent owner analytics SQL along with the preexisting owner type/stage verification, consent-granted and pseudonymous subject. | Same test file verifies exact SQL owner v1 predicate in actual owner PHP methods; isolates browser events from owner-confirmed outcomes. |

**Regression collection:** 40 new R44 direct real-method cases spanning 10 behavior families × four variations, plus historical R42 method mocks. Existing 40-loop `scripts/review40-iterative.sh` checks 40 approved source markers each round. PHP7.4/8.3 quality, independent fresh post-code checks, deterministic ZIP/SBOM and post-merge same-SHA CI are mandatory. No test is claimed to have used a real WordPress production database or browser.

**Count by review scope:** forty distinct requirement lenses evaluated; **2 newly demonstrated local source defects** corrected in the branch; **2 previously OPEN cross-file owner/design acceptance gaps** (#19, #22) still remain; live/source parity and migration unverified. Rows marked 'source scope reviewed' cannot be called WordPress-staging-accepted.

**Owner #19** requires legitimate File01-registered native event contract and real File07/08/09 staging & deployed event origin with privacy erasure/retraction proof. **Owner #22** requires approved actual File20/25 experiment placement mutation/restore contract, immutable provenance, CAS and real staging rollback. No false closure.

**Source freeze/DB parity statement:** Repository HEAD before changes `f4ee9baf3881c47d9a255cae920ec96204ec59ef`; target v1.4.47 Base 10005 / Future1 **source only**, actual Deployed Version UNKNOWN; Actual DB Version UNKNOWN; Migration State UNKNOWN; Live Verification NOT DONE. Exact deployed code ابھی unverified ہے؛ repository-based diagnosis provisional ہے۔
