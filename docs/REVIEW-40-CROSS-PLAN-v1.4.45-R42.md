# File14 R42 — PRE-CODE frozen forty-lens requirements/companion review

**Date:** 2026-10-16. **Exact frozen GitHub main:** `2d4414a07b1b244f2461219f182531c768c0e942` / plugin source **1.4.44**. Governing evidence: attached `14-File-14-...v2.0-Future-CTI-24-Amended-2026-08-10.docx`, attached **Three Central Plans Consolidated Governing Master Plan v3.0**, and attached File07/08/09/25 source plans. The governing plans require owner-specific data boundaries, privacy-minimized accurate analytics, explicit versioned event contracts, and independent staged/live evidence. File01/20/24 are integration owners; their actual installed plugins and registries are not evidenced by repo source.

**Review discipline:** Enumerate 16 FR plus 24 Future CTI distinct approved requirement lenses before any R42 coding. Existing `scripts/review40-iterative.sh` will repeat the forty contract markers for forty complete consecutive source passes as its own CI gate. This does not equal forty real WP owner-integration or live passes. Distinguish correctness tests from production truth.

| Review lens | Requirement | Plan concern | PRE-PATCH source result |
|---:|---|---|---|
| 1 | F14-FR-001 | Patient route/value proposition | SOURCE SCOPE INSPECTED; no staging/deployed acceptance |
| 2 | F14-FR-002 | Doctor value and zero commission | SOURCE SCOPE INSPECTED; no staging/deployed acceptance |
| 3 | F14-FR-003 | Primary CTAs and owner destinations | SOURCE SCOPE INSPECTED; no staging/deployed acceptance |
| 4 | F14-FR-004 | Patient/doctor journey sequence | SOURCE SCOPE INSPECTED; no staging/deployed acceptance |
| 5 | F14-FR-005 | Trust, verification and no cure guarantee | SOURCE SCOPE INSPECTED; no staging/deployed acceptance |
| 6 | F14-FR-006 | Zero commission and voluntary support | SOURCE SCOPE INSPECTED; no staging/deployed acceptance |
| 7 | F14-FR-007 | File20 owned route/placement registry | SOURCE SCOPE INSPECTED; no staging/deployed acceptance |
| 8 | F14-FR-008 | File25 consumes canonical content blocks | SOURCE SCOPE INSPECTED; no staging/deployed acceptance |
| 9 | F14-FR-009 | File07/08/09 owner destination health | SOURCE SCOPE INSPECTED; no staging/deployed acceptance |
| 10 | F14-FR-010 | Consent/GPC attribution | SOURCE SCOPE INSPECTED; no staging/deployed acceptance |
| 11 | F14-FR-011 | File01/07/08/09 owner-native outcomes | OPEN owner #19, post-commit event provenance required |
| 12 | F14-FR-012 | Founder experiment lifecycle | SOURCE SCOPE INSPECTED; no staging/deployed acceptance |
| 13 | F14-FR-013 | en-US/ur-PK/ar-SA localization | SOURCE SCOPE INSPECTED; no staging/deployed acceptance |
| 14 | F14-FR-014 | FAQ source/policy history | SOURCE SCOPE INSPECTED; no staging/deployed acceptance |
| 15 | F14-FR-015 | Accessibility, mobile and low-data | SOURCE SCOPE INSPECTED; no staging/deployed acceptance |
| 16 | F14-FR-016 | Claim basis/freshness/audit | SOURCE SCOPE INSPECTED; no staging/deployed acceptance |
| 17 | F14-FUT-01 | Intent router no sensitive profiling | SOURCE SCOPE INSPECTED; no staging/deployed acceptance |
| 18 | F14-FUT-02 | Allowlisted destination handoff | SOURCE SCOPE INSPECTED; no staging/deployed acceptance |
| 19 | F14-FUT-03 | Trust evidence current-public gate | SOURCE SCOPE INSPECTED; no staging/deployed acceptance |
| 20 | F14-FUT-04 | Freshness sentinel expiry/review/historic audit | SOURCE SCOPE INSPECTED; no staging/deployed acceptance |
| 21 | F14-FUT-05 | Commission parity and source copy scan | SOURCE SCOPE INSPECTED; no staging/deployed acceptance |
| 22 | F14-FUT-06 | Approved jurisdictional text | SOURCE SCOPE INSPECTED; no staging/deployed acceptance |
| 23 | F14-FUT-07 | Protected semantic copy diff | SOURCE SCOPE INSPECTED; no staging/deployed acceptance |
| 24 | F14-FUT-08 | Dark-pattern disallow list | SOURCE SCOPE INSPECTED; no staging/deployed acceptance |
| 25 | F14-FUT-09 | Honest destination failover | SOURCE SCOPE INSPECTED; no staging/deployed acceptance |
| 26 | F14-FUT-10 | Nonmutating scenario previews | SOURCE SCOPE INSPECTED; no staging/deployed acceptance |
| 27 | F14-FUT-11 | Provisional evidence-based quality score | SOURCE SCOPE INSPECTED; no staging/deployed acceptance |
| 28 | F14-FUT-12 | Auditable misleading copy reports | SOURCE SCOPE INSPECTED; no staging/deployed acceptance |
| 29 | F14-FUT-13 | Consent and aggregate friction | NEW D02: owner-stage and browser friction unreadable DB evidence |
| 30 | F14-FUT-14 | Suppressed unverified funnel anomaly | NEW D01: owner anomaly DB read silently treated as no-data |
| 31 | F14-FUT-15 | Minimum-ten cohort privacy | owner stage small-cohort privacy; source checked |
| 32 | F14-FUT-16 | Founder approval experiment preflight | SOURCE SCOPE INSPECTED; no staging/deployed acceptance |
| 33 | F14-FUT-17 | Experiment auto-stop AND changed-placement restore | OPEN owner #22, true changed-placement rollback missing |
| 34 | F14-FUT-18 | FAQ gap drafts only | SOURCE SCOPE INSPECTED; no staging/deployed acceptance |
| 35 | F14-FUT-19 | Locale/claim consistency graph | SOURCE SCOPE INSPECTED; no staging/deployed acceptance |
| 36 | F14-FUT-20 | Translation source and terminology lock | SOURCE SCOPE INSPECTED; no staging/deployed acceptance |
| 37 | F14-FUT-21 | AI draft/no autoshare | SOURCE SCOPE INSPECTED; no staging/deployed acceptance |
| 38 | F14-FUT-22 | Public reviewed change log | SOURCE SCOPE INSPECTED; no staging/deployed acceptance |
| 39 | F14-FUT-23 | Choose safely informational guidance | SOURCE SCOPE INSPECTED; no staging/deployed acceptance |
| 40 | F14-FUT-24 | Doctor nonbinding readiness | SOURCE SCOPE INSPECTED; no staging/deployed acceptance |

## R42 New defects frozen before correction

**R42-D01 — FUT14 / F14-FUT-14:** `GCU_Owner_Outcomes::observed_anomaly()` maps an SQL `get_results()` false/null to zero observed rows and returns `insufficient_owner_event_evidence` / `severity=none`. Such a database failure is **unverified measurement**, not evidence of an uneventful low-volume window. The caller `GCU_Future_Intelligence::anomaly_detector()` stores that misleading status as LAST_ANOMALY_OPTION. Require explicit read-unverified suppression + a privacy-safe operational log; preserve existing cohort threshold and owner-native noncausal interpretation.

**R42-D02 — FUT13 / F14-FUT-13:** `GCU_Owner_Outcomes::owner_stage_summary()` maps SQL read failure to empty suppressed subgroup. The protected `rest_friction()` cannot distinguish a real below-threshold owner cohort from a broken event table. Error must propagate as `WP_Error` through existing permission/REST gate; reject malformed evidence rather than fabricating zeros.

**R42-D03 — FUT13 / F14-FUT-13:** `GCU_Future_Intelligence::friction_summary()` maps SQL read failure to an empty below-threshold browser funnel, falsely reporting `insufficient_sample`. Return error rather than silently valid suppressed sample; `rest_friction()` must not attach fields to `WP_Error` as though it were an array.

## Existing hard owner blockers (not new defects)

**#19** requires File01-approved and File07/08/09 owner-attested real destination/application/appointment events and staging proof; source callback presence is not a verified correlated conversion. **#22** requires authorized actual File14 experiment-caused changed-placement restoration with Founder-approved original snapshot, CAS and File20/25 staging rollback evidence; safe early-stop alone is not restoration.

## Planned one-batch remediation after review

Fail closed on the three above independent SQL integrity omissions with anonymous operational diagnostic status (no user or event IDs); keep small-cohort suppression; add 40 direct actual-method mocked SQL/privacy failure, healthy, REST propagation and anomaly cases, integrate into PHP7.4/8.3 build; exact SHA quality, forty repeated source sweeps, fresh reviews, deterministic package/SBOM; post-merge main re-test. Candidate **v1.4.45**, source schema Base `10005`, Future `1` unchanged. No owner plugin mutation or guessed deployment.

Repository HEAD = 2d4414a07b1b244f2461219f182531c768c0e942; Deployed Version = UNVERIFIED; DB Version = UNVERIFIED; Migration State = UNVERIFIED; Live Verification Status = UNVERIFIED. Exact deployed code ابھی unverified ہے؛ repository-based diagnosis provisional ہے۔

## Post-complete-inspection corrective batch — NOT part of initial freeze

All forty source requirement lenses above were frozen on GitHub **before** modifying any R42 code. Only afterward the following corrective changes were made:

| Defect | Exact source fix | Confidence boundary |
|---|---|---|
| R42-D01 | `GCU_Owner_Outcomes::observed_anomaly()` SQL false/null now returns explicit `owner_event_volume_read_unverified`, suppressed owner-stage signals and no fabricated booking success rate; logs privacy-safe operator error; caller `anomaly_detector()` preserves this actual status in last-option, not `insufficient_owner_event_evidence`. | Source under PR test; no deployed events or actual correlated conversion inference. |
| R42-D02 | `GCU_Owner_Outcomes::owner_stage_summary()` SQL false/null and malformed result rows return `WP_Error` instead of an apparently healthy small-cohort suppression. Authorized `rest_friction` already checks owner errors and forwards them. | No raw subject, application, doctor, appointment or event IDs exposed. |
| R42-D03 | `GCU_Future_Intelligence::friction_summary()` SQL false/null and malformed result rows return `WP_Error` and privacy-safe low-cardinality error; `rest_friction()` checks that value before accessing it as an array and forwards the protected error. | Existing cohort threshold 10 and prohibition of inferred stage drop-offs remain in place. |

Added **40** different method-level SQL/WordPress mocked cases in `tests/r42-analytics-read-integrity-tests.php`, divided into ten failure/success/privacy/REST branches, four parameter variations each. Added to `scripts/quality.sh`. The full forty-pass source-review suite remains in CI, **but repeating a source marker forty times is not forty independent deployments or browser runs**. Actual deployed, owner plugin and migration evidence is absent.

**R42 tally:** 40 distinct requirements inspected; 3 new local source defects and 3 proposed corrections; 40 new direct-method test cases; 2 preexisting cross-file owner acceptance blockers (#19/#22) still OPEN. All source corrections are conditional on CI, then staging and Live parity for a real-world completion claim.

**Required final acceptance separately:** Repository HEAD (after review and merge) / Deployed Version (not known) / DB Version (not known) / Migration State (not known) / Live Verification Status (not verified). GitHub success does not equal installed production health.
