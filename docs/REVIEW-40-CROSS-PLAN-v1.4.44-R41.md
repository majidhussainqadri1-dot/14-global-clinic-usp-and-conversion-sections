# File14 R41 — frozen 40-point source/plan review (before corrective code)

**Review date:** 2026-10-16. **Repository main exact freeze:** `d2bf13a9ce71b185452c17d0ad7c915ddfd6e78f` (source plugin 1.4.43, base schema target 10005 / Future schema target 1). **Authority:** attached amended File14 Future CTI24 plan (2026-08-10), consolidated Three Central Plans v3.0 and attached File07/08/09/25 owner plans. File01/20/24 constraints are assessed as declared integration contracts, not as proof of those companions' live runtime. No deployed code/DB/WordPress facts available.

## Review method and truthful evidence level

There are 40 **distinct planned requirement lenses**, the 16 base FR and the 24 Future CTI ones. The tracked `scripts/review40-iterative.sh` executes the 40-source-requirement regression 40 times on each quality run. Repeated scans are source checks, not forty separate staging/browser/real-owner interactions. Source/companion completeness beyond the tests requires actual owner runtime/registry acceptance. Defects are collected across this round before code correction.

| Round/lens | Approved ID | Actual requirement subject | Frozen review classification |
|---:|---|---|---|
| 01 | F14-FR-001 | Patient route/value proposition | REVIEW: repository source + documented owner boundary |
| 02 | F14-FR-002 | Doctor value and zero commission | REVIEW: repository source + documented owner boundary |
| 03 | F14-FR-003 | Primary CTAs and owner destinations | REVIEW: repository source + documented owner boundary |
| 04 | F14-FR-004 | Patient/doctor journey sequence | REVIEW: repository source + documented owner boundary |
| 05 | F14-FR-005 | Trust, verification and no cure guarantee | REVIEW: repository source + documented owner boundary |
| 06 | F14-FR-006 | Zero commission and voluntary support | REVIEW: repository source + documented owner boundary |
| 07 | F14-FR-007 | File20 owned route/placement registry | REVIEW: repository source + documented owner boundary |
| 08 | F14-FR-008 | File25 consumes canonical content blocks | REVIEW: repository source + documented owner boundary |
| 09 | F14-FR-009 | File07/08/09 owner destination health | REVIEW: repository source + documented owner boundary |
| 10 | F14-FR-010 | Consent/GPC attribution | REVIEW: repository source + documented owner boundary |
| 11 | F14-FR-011 | File01/07/08/09 owner-native outcomes | OPEN: File01/07/08/09 owner acceptance #19 |
| 12 | F14-FR-012 | Founder experiment lifecycle | REVIEW: repository source + documented owner boundary |
| 13 | F14-FR-013 | en-US/ur-PK/ar-SA localization | REVIEW: repository source + documented owner boundary |
| 14 | F14-FR-014 | FAQ source/policy history | REVIEW: repository source + documented owner boundary |
| 15 | F14-FR-015 | Accessibility, mobile and low-data | REVIEW: repository source + documented owner boundary |
| 16 | F14-FR-016 | Claim basis/freshness/audit | REVIEW: repository source + documented owner boundary |
| 17 | F14-FUT-01 | Intent router no sensitive profiling | REVIEW: repository source + documented owner boundary |
| 18 | F14-FUT-02 | Allowlisted destination handoff | REVIEW: repository source + documented owner boundary |
| 19 | F14-FUT-03 | Trust evidence current-public gate | REVIEW: repository source + documented owner boundary |
| 20 | F14-FUT-04 | Freshness sentinel expiry/review/historic audit | REVIEW: repository source + documented owner boundary |
| 21 | F14-FUT-05 | Commission parity and source copy scan | R41 daily policy propagation to verify |
| 22 | F14-FUT-06 | Approved jurisdictional text | REVIEW: repository source + documented owner boundary |
| 23 | F14-FUT-07 | Protected semantic copy diff | REVIEW: repository source + documented owner boundary |
| 24 | F14-FUT-08 | Dark-pattern disallow list | REVIEW: repository source + documented owner boundary |
| 25 | F14-FUT-09 | Honest destination failover | REVIEW: repository source + documented owner boundary |
| 26 | F14-FUT-10 | Nonmutating scenario previews | REVIEW: repository source + documented owner boundary |
| 27 | F14-FUT-11 | Provisional evidence-based quality score | REVIEW: repository source + documented owner boundary |
| 28 | F14-FUT-12 | Auditable misleading copy reports | REVIEW: repository source + documented owner boundary |
| 29 | F14-FUT-13 | Consent and aggregate friction | REVIEW: repository source + documented owner boundary |
| 30 | F14-FUT-14 | Suppressed unverified funnel anomaly | REVIEW: repository source + documented owner boundary |
| 31 | F14-FUT-15 | Minimum-ten cohort privacy | REVIEW: repository source + documented owner boundary |
| 32 | F14-FUT-16 | Founder approval experiment preflight | REVIEW: repository source + documented owner boundary |
| 33 | F14-FUT-17 | Experiment auto-stop AND changed-placement restore | OPEN: File20/25 experiment provenance #22 |
| 34 | F14-FUT-18 | FAQ gap drafts only | SUSPECT: failed FAQ catalogue DB read |
| 35 | F14-FUT-19 | Locale/claim consistency graph | SUSPECT: failed consistency DB read |
| 36 | F14-FUT-20 | Translation source and terminology lock | REVIEW: repository source + documented owner boundary |
| 37 | F14-FUT-21 | AI draft/no autoshare | REVIEW: repository source + documented owner boundary |
| 38 | F14-FUT-22 | Public reviewed change log | REVIEW: repository source + documented owner boundary |
| 39 | F14-FUT-23 | Choose safely informational guidance | REVIEW: repository source + documented owner boundary |
| 40 | F14-FUT-24 | Doctor nonbinding readiness | REVIEW: repository source + documented owner boundary |

## New source observations (all collected before patches)

- **R41-D01 / F14-FUT-18 — false draft FAQ gaps on storage failure.** `GCU_Future_Intelligence::faq_gap_candidates()` does `$wpdb->get_col(...)` but treats a non-array/failed lookup as zero existing FAQs. A failed read can fabricate up to 20 unanswered FAQ candidates; downstream REST exposes them and daily governance attempts to store them. This contradicts approved aggregate-only **suggested, never auto-publish** gaps based on verified existing knowledge.
- **R41-D02 / F14-FUT-19 — false passing consistency graph.** `GCU_Future_Intelligence::consistency_graph()` treats failed `get_results()` as an empty rowset, returning `ok=true` with no detected missing locale/claim drift. A read error must not certify cross-locale consistency.
- **R41-D03 / FUT-05/18/19 — incomplete daily governance result propagation.** `daily_governance()` currently discards `parity_status()` and `consistency_graph()` failure status; it blindly iterates `faq_gap_candidates()`. This can record unverified FAQ suggestions and finish a daily check without reflecting the failed verification.
- **Preexisting owner obligation #19 — NOT SOLVED:** real File07/08/09 application/booking/destination confirmations require File01 versioned owner provenance, consent and WordPress staging; do not equate a File14 click with a booking.
- **Preexisting owner obligation #22 — NOT SOLVED:** File20/25 experiment-owned changed placements require Founder-approved provenance/CAS reversal and real staging rollback proof; existing no-op baseline reconciliation does not constitute changed-placement restoration.

## Intended safe local corrective batch, AFTER 40-lens review

Fail closed on invalid FAQ lookup without fabricating gaps; preserve error through REST and daily governance. Fail closed on invalid consistency graph DB read without exposing raw data. Ensure daily governing job propagates unverified parity/consistency/FAQ status while not silently saving unverified suggestions. Add 40 scenario checks with the REAL File14 PHP methods using isolated WordPress and SQL mocks; PHP 7.4/8.3 CI, 40 repeated source scans and post-code fresh reviews before any merge. Candidate version planned **1.4.44** subject to review; no schema or inter-owner data ownership changes.

## Required separate evidence
Repository HEAD = d2bf13a9ce71b185452c17d0ad7c915ddfd6e78f; Deployed Version = UNVERIFIED; Actual DB Version = UNVERIFIED; Migration State = UNVERIFIED; Live Verification Status = NOT RUN. Exact deployed code ابھی unverified ہے؛ repository-based diagnosis provisional ہے۔
