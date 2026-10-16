# File14 R47 — PRE-CODE governing forty-plan review and frozen source defects

**Date:** 2026-10-17. **GitHub SOURCE only frozen main:** `1146c256d668d9dd28975b43f5956a82c9fd966c`; plugin header `1.4.49`; code schema targets Base 10005 / Future 1, **not the installed WordPress schema**.

Original supplied sources consulted: amended **File14 Global Clinic USP and Conversion Integration v2.0 CTI24**, 16 FR + 24 FUT; **Three Central Plans Consolidated Governing Master Plan**; related actual File07 Doctors Directory, File08 Worldwide Clinic/Appointments, File09 Doctor Onboarding, and File25 Public UI plans. The original File14 plan requires ethical consented, origin-safe acquisition analytics; separately attested File01/07/08/09 owner outcomes; a FUT-11 quality score with verifiable inputs, FUT-13 privacy friction, FUT-15 cohort protection, independent policy truth; strict scope boundaries, staging-first and Founder approval. None of these uploaded specifications assert installed deployment.

**Review method:** 40 unique requirement/owner lenses evaluated and recorded before any R47 PHP changes. Existing `scripts/review40-iterative.sh` will run 40 consecutive *source-marker* reviews against all 40 plan markers under PHP quality. Repeated source scans and mocked SQL method-level tests are NOT 40 independent deployed WordPress/browser walkthroughs.

| Review | Approved requirement ID | Governing scope | Pre-code finding |
|---:|---|---|---|
| 1 | F14-FR-001 | Patient route/value proposition | Source and approved ownership boundary reviewed; no staging/live proof |
| 2 | F14-FR-002 | Doctor value and zero commission | Source and approved ownership boundary reviewed; no staging/live proof |
| 3 | F14-FR-003 | Primary CTAs and owner destinations | Source and approved ownership boundary reviewed; no staging/live proof |
| 4 | F14-FR-004 | Patient/doctor journey sequence | Source and approved ownership boundary reviewed; no staging/live proof |
| 5 | F14-FR-005 | Trust, verification and no cure guarantee | Source and approved ownership boundary reviewed; no staging/live proof |
| 6 | F14-FR-006 | Zero commission and voluntary support | Source and approved ownership boundary reviewed; no staging/live proof |
| 7 | F14-FR-007 | File20 owned route/placement registry | Source and approved ownership boundary reviewed; no staging/live proof |
| 8 | F14-FR-008 | File25 consumes canonical content blocks | Source and approved ownership boundary reviewed; no staging/live proof |
| 9 | F14-FR-009 | File07/08/09 owner destination health | Source and approved ownership boundary reviewed; no staging/live proof |
| 10 | F14-FR-010 | Consent/GPC attribution | Source and approved ownership boundary reviewed; no staging/live proof |
| 11 | F14-FR-011 | File01/07/08/09 owner-native outcomes | Owner #19 registered native event source NOT ACCEPTED |
| 12 | F14-FR-012 | Founder experiment lifecycle | Source and approved ownership boundary reviewed; no staging/live proof |
| 13 | F14-FR-013 | en-US/ur-PK/ar-SA localization | Source and approved ownership boundary reviewed; no staging/live proof |
| 14 | F14-FR-014 | FAQ source/policy history | Source and approved ownership boundary reviewed; no staging/live proof |
| 15 | F14-FR-015 | Accessibility, mobile and low-data | Source and approved ownership boundary reviewed; no staging/live proof |
| 16 | F14-FR-016 | Claim basis/freshness/audit | Source and approved ownership boundary reviewed; no staging/live proof |
| 17 | F14-FUT-01 | Intent router no sensitive profiling | Source and approved ownership boundary reviewed; no staging/live proof |
| 18 | F14-FUT-02 | Allowlisted destination handoff | Source and approved ownership boundary reviewed; no staging/live proof |
| 19 | F14-FUT-03 | Trust evidence current-public gate | Source and approved ownership boundary reviewed; no staging/live proof |
| 20 | F14-FUT-04 | Freshness sentinel expiry/review/historic audit | Source and approved ownership boundary reviewed; no staging/live proof |
| 21 | F14-FUT-05 | Commission parity and source copy scan | Source and approved ownership boundary reviewed; no staging/live proof |
| 22 | F14-FUT-06 | Approved jurisdictional text | Source and approved ownership boundary reviewed; no staging/live proof |
| 23 | F14-FUT-07 | Protected semantic copy diff | Source and approved ownership boundary reviewed; no staging/live proof |
| 24 | F14-FUT-08 | Dark-pattern disallow list | Source and approved ownership boundary reviewed; no staging/live proof |
| 25 | F14-FUT-09 | Honest destination failover | Source and approved ownership boundary reviewed; no staging/live proof |
| 26 | F14-FUT-10 | Nonmutating scenario previews | Quality analytics privacy source ownership check (D01) |
| 27 | F14-FUT-11 | Provisional evidence-based quality score | NEW R47-D01/D02: quality_score unfiltered funnel and false-perfect unreadable SQL |
| 28 | F14-FUT-12 | Auditable misleading copy reports | Source and approved ownership boundary reviewed; no staging/live proof |
| 29 | F14-FUT-13 | Consent and aggregate friction | Privacy/consent in quality count query (D01) |
| 30 | F14-FUT-14 | Suppressed unverified funnel anomaly | Measurement anomaly owner-only contract remains separate #19 |
| 31 | F14-FUT-15 | Minimum-ten cohort privacy | Privacy-safe underthreshold cohort; no raw output |
| 32 | F14-FUT-16 | Founder approval experiment preflight | Source and approved ownership boundary reviewed; no staging/live proof |
| 33 | F14-FUT-17 | Experiment auto-stop AND changed-placement restore | Owner #22 changed placement rollback NOT ACCEPTED |
| 34 | F14-FUT-18 | FAQ gap drafts only | Source and approved ownership boundary reviewed; no staging/live proof |
| 35 | F14-FUT-19 | Locale/claim consistency graph | Source and approved ownership boundary reviewed; no staging/live proof |
| 36 | F14-FUT-20 | Translation source and terminology lock | Source and approved ownership boundary reviewed; no staging/live proof |
| 37 | F14-FUT-21 | AI draft/no autoshare | Source and approved ownership boundary reviewed; no staging/live proof |
| 38 | F14-FUT-22 | Public reviewed change log | Source and approved ownership boundary reviewed; no staging/live proof |
| 39 | F14-FUT-23 | Choose safely informational guidance | Source and approved ownership boundary reviewed; no staging/live proof |
| 40 | F14-FUT-24 | Doctor nonbinding readiness | Source and approved ownership boundary reviewed; no staging/live proof |

## Newly verified source defects — frozen BEFORE any fix

**R47-D01 (FUT11/13/15 & FR010):** `GCU_Future_Intelligence::quality_score()` queries 30d selected funnel activity from every `gcu_events` row grouped by stage, omitting `event_type='ClinicUSPFunnelEvent'`, `event_version=1`, `consent_state='granted'`, and nonnull pseudonymous `subject_hash` filters. By contrast the adjacent `friction_summary()` source includes precisely these rules (R43). Imported/retracted/unconsented/owner event records could inflate the measured `sample_count` and privacy cohort or false completeness. Fix the query to count only consented **File14 acquisition telemetry**; validate each SQL aggregate count before conversion; fail closed on a false/null read or malformed row. No installed DB data assertions.

**R47-D02 (FUT11 & FR016):** `quality_score()` calls `get_var()` for expired/review-required claims and open/reviewing complaint rows, casts SQL false/null to integer zero, and reports `claim_freshness=100` and `complaint_health=100` despite unverified DB reads. Also row scan SQL fail previously implied zero selected. Instead represent unknown component score as **null**, surface explicit `missing_evidence` flags; force quality status provisional and composite score null when any required counts are unverified, while preserving business parity and zero-commission guard. Valid truly zero is different from unreadable. `rest_quality()` authorized response must retain clearly provisional status, not reveal raw SQL.

## Two preexisting owner/staging blockers, NOT newly found defects

**#19** File01 registered File07/08/09 native successful owner outcome events with independent actual WordPress staging proof; File14 browser click cannot prove booking. **#22** File20/25 owner CAS/provenance and approved changed-placement rollback with Founder acceptance. They remain OPEN.

## Planned single corrective batch after complete review

Candidate plugin 1.4.50; add fail-closed FUT11 query and metrics evidence, **40 new direct real-method PHP mocked WPDB** cases for false/null, malformed counts, denied consent/source and successful zero vs unknown, tested PHP7.4 + PHP8.3, original forty source review repeated 40 times, fresh post-code independent checks, reproducible ZIP + SBOM. Exact PR HEAD green, then merge and exact **new main HEAD recheck**. NO SQL schema changes, no touching owner plugins, no staging or live deployment.

Final reality ledger: Repository HEAD `1146c256d668d9dd28975b43f5956a82c9fd966c` (pre-patch); Deployed Version **UNVERIFIED**; Actual DB Version **UNVERIFIED**; Migration State **UNVERIFIED**; Live Verification Status **NOT RUN**. Exact deployed code ابھی unverified ہے؛ repository-based diagnosis provisional ہے۔
