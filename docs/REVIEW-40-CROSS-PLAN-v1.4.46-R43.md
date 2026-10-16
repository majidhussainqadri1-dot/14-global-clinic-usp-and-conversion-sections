# File14 R43 PRE-PATCH forty requirement lens register — Frozen at exact deployed-unknown source

## Source and plan authority
Date 2026-10-16. Frozen **GitHub main** `ae691d9f16969a62935f5043fb3707dec44d5c1d`, source plugin **v1.4.45**, Base schema TARGET 10005 and Future schema TARGET 1. Read actual attached File14 amended CTI24 v2.0, its F14-FUT-01..24 and FR 16 requirements, Consolidated Three Central Plans, and companion File07/08/09/25 sources. Plan explicitly requires consent/GPC and privacy-safe source-of-truth analytics, event type/version integrity, and owner-native outcome evidence. File20 shell owns layout; File01 versioned integration registry; File24 assurance; File14 does not own clinical/booking/doctor truth. This document is a **source inspection**, not a WordPress installed-state inspection.

## Forty review lenses — pre-code status

| # | Requirement ID | Governing concern | Inspection evidence/status |
|---:|---|---|---|
| 1 | F14-FR-001 | Patient route/value proposition | Source-plan requirement examined (not live certified) |
| 2 | F14-FR-002 | Doctor value and zero commission | Source-plan requirement examined (not live certified) |
| 3 | F14-FR-003 | Primary CTAs and owner destinations | Source-plan requirement examined (not live certified) |
| 4 | F14-FR-004 | Patient/doctor journey sequence | Source-plan requirement examined (not live certified) |
| 5 | F14-FR-005 | Trust, verification and no cure guarantee | Source-plan requirement examined (not live certified) |
| 6 | F14-FR-006 | Zero commission and voluntary support | Source-plan requirement examined (not live certified) |
| 7 | F14-FR-007 | File20 owned route/placement registry | Source-plan requirement examined (not live certified) |
| 8 | F14-FR-008 | File25 consumes canonical content blocks | Source-plan requirement examined (not live certified) |
| 9 | F14-FR-009 | File07/08/09 owner destination health | Source-plan requirement examined (not live certified) |
| 10 | F14-FR-010 | Consent/GPC attribution | Source-plan requirement examined (not live certified) |
| 11 | F14-FR-011 | File01/07/08/09 owner-native outcomes | BLOCKER #19 unverified owner native event contract |
| 12 | F14-FR-012 | Founder experiment lifecycle | Source-plan requirement examined (not live certified) |
| 13 | F14-FR-013 | en-US/ur-PK/ar-SA localization | Source-plan requirement examined (not live certified) |
| 14 | F14-FR-014 | FAQ source/policy history | Source-plan requirement examined (not live certified) |
| 15 | F14-FR-015 | Accessibility, mobile and low-data | Source-plan requirement examined (not live certified) |
| 16 | F14-FR-016 | Claim basis/freshness/audit | Source-plan requirement examined (not live certified) |
| 17 | F14-FUT-01 | Intent router no sensitive profiling | Source-plan requirement examined (not live certified) |
| 18 | F14-FUT-02 | Allowlisted destination handoff | Source-plan requirement examined (not live certified) |
| 19 | F14-FUT-03 | Trust evidence current-public gate | Source-plan requirement examined (not live certified) |
| 20 | F14-FUT-04 | Freshness sentinel expiry/review/historic audit | Source-plan requirement examined (not live certified) |
| 21 | F14-FUT-05 | Commission parity and source copy scan | Source-plan requirement examined (not live certified) |
| 22 | F14-FUT-06 | Approved jurisdictional text | Source-plan requirement examined (not live certified) |
| 23 | F14-FUT-07 | Protected semantic copy diff | Source-plan requirement examined (not live certified) |
| 24 | F14-FUT-08 | Dark-pattern disallow list | Source-plan requirement examined (not live certified) |
| 25 | F14-FUT-09 | Honest destination failover | Source-plan requirement examined (not live certified) |
| 26 | F14-FUT-10 | Nonmutating scenario previews | Source-plan requirement examined (not live certified) |
| 27 | F14-FUT-11 | Provisional evidence-based quality score | Source-plan requirement examined (not live certified) |
| 28 | F14-FUT-12 | Auditable misleading copy reports | Source-plan requirement examined (not live certified) |
| 29 | F14-FUT-13 | Consent and aggregate friction | NEW D03 browser consent+origin filter; D02 count integrity |
| 30 | F14-FUT-14 | Suppressed unverified funnel anomaly | NEW D01 malformed owner volume; D02 count integrity |
| 31 | F14-FUT-15 | Minimum-ten cohort privacy | Source-plan requirement examined (not live certified) |
| 32 | F14-FUT-16 | Founder approval experiment preflight | Source-plan requirement examined (not live certified) |
| 33 | F14-FUT-17 | Experiment auto-stop AND changed-placement restore | BLOCKER #22 provenance rollback |
| 34 | F14-FUT-18 | FAQ gap drafts only | Source-plan requirement examined (not live certified) |
| 35 | F14-FUT-19 | Locale/claim consistency graph | Source-plan requirement examined (not live certified) |
| 36 | F14-FUT-20 | Translation source and terminology lock | Source-plan requirement examined (not live certified) |
| 37 | F14-FUT-21 | AI draft/no autoshare | Source-plan requirement examined (not live certified) |
| 38 | F14-FUT-22 | Public reviewed change log | Source-plan requirement examined (not live certified) |
| 39 | F14-FUT-23 | Choose safely informational guidance | Source-plan requirement examined (not live certified) |
| 40 | F14-FUT-24 | Doctor nonbinding readiness | Source-plan requirement examined (not live certified) |

## NEW source defects recorded BEFORE changing runtime code

**R43-D01 / F14-FUT-14 — owner anomaly malformed-row fail-open:** `GCU_Owner_Outcomes::observed_anomaly()` dereferences `event_type`, `funnel_stage`, `current_count`, `previous_seven_days` without validating row shape or canonical nonnegative whole counts. SQL corruption/mock/DB edge can raise PHP warnings or silently coerce invalid amounts into no-anomaly/false high anomaly. Requirement: no invented owner-volume inference; guard the full result before calculating, return `owner_event_volume_read_unverified` and anonymous log.

**R43-D02 / F14-FUT-13/14/15 — aggregate numeric integrity:** native `owner_stage_summary()` and browser `friction_summary()` accept `is_numeric()` (negative, decimal, scientific string), then `(int)` coercion, permitting incorrect cohort suppression or reported totals. Valid SQL `COUNT(*)` and `SUM(CASE...)` must be canonical nonnegative integer values. Reject fractional, negative, scientific, huge overflow, null, arbitrary types; maintain cohort threshold 10 and error fail-closed.

**R43-D03 / F14-FUT-13, Privacy:** browser `friction_summary()` groups all records from File14's events table by `funnel_stage` without checking `consent_state='granted'`, `subject_hash IS NOT NULL`, canonical `event_type='ClinicUSPFunnelEvent'` and `event_version=1`. Historical/imported/retracted/foreign event rows, if present, could contribute to anonymized funnel counts against the plan's consent & provenance boundary. Fix File14-only query so it only considers consented, attributable **File14-origin acquisition** events; no owner event counts as a browser click. This is a source-level risk, not evidence such invalid rows exist in installed DB.

## Distinct already-open OWNER gates, not local source defects

GitHub Issue #19: independently owner-registered and stage-tested File01/07/08/09 native events with consent retraction/live evidence. GitHub Issue #22: true provenance/compare-and-swap restoration for experiment-owned changed File20/25 placements. Neither is inferred completed by source tests or a GitHub merge.

## R43 plan for ONE batch of corrective code only AFTER these forty lenses

Introduce bounded canonical SQL aggregate count validator; reject and log any malformed native anomaly/owner-stage/browser-friction result, including invalid counts; add explicit consent + event origin/version predicate for browser friction; 40 new direct-real-PHP-method SQL mocks; run exact PHP7.4/8.3, 40 repeated 40-source-marker CI, fresh reviews, deterministic zip/SBOM; merged-main re-test. Candidate source v1.4.46; source schema targets unchanged. No application/booking backend or companion plugin changes.

**Required post-run separate report:** Repository HEAD / Deployed Version / Actual DB Version / Migration State / Live Verification Status. Current: source frozen above / UNVERIFIED / UNVERIFIED / UNVERIFIED / NOT RUN. Exact deployed code ابھی unverified ہے؛ repository-based diagnosis provisional ہے۔
