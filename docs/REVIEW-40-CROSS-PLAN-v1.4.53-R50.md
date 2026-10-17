# R50 File14 frozen PRE-CODE 40-requirement and companion/central review

**Date:** 2026-10-17. **Exact repository MAIN source baseline:** `5689742d9d9848f65d2e610c5b0d43738ea1b4c3`; plugin header **v1.4.52**. Target source schema Base 10005 / Future 1; neither claims actual installed DB state.

## Source documents and ownership

Read actual attached **File14 Global Clinic USP and Conversion Integration v2.0 Amended CTI24**, particularly `F14-FUT-12` structured misleading-copy reporting, moderator review/resolution and audit without leaked personal data; `F14-FUT-22` reviewed, dated, versioned public Trust & Policy Change Log; and `F14-FR-016` evidence/audit discipline. Attached consolidated **Three Central Governing Plans** independently distinguish specified, coded, staged, deployed, operational and verified, and require native capability/retention/privacy/audit ownership. Actual companion File07 Doctor Directory, File08 Clinic/Appointments, File09 Doctor Verification and File25 Public UI confirm File14 is *not* canonical owner of doctor verification, appointment truth, cross-platform shell, protected clinical records or File20 placement mutations.

40 distinct FR/FUT approved requirements and the corresponding File14 source/owner contract were inspected; this is a source review, not 40 independent live WordPress browser passes. Separate 40×40 automated source-marker scans will be required after changes.

| Review lens | Approved requirement | Approved source area | R50 PRE-CODE finding |
|---:|---|---|---|
| 1 | F14-FR-001 | Patient route/value proposition | Reviewed original requirement and source boundary; no live/staging evidence |
| 2 | F14-FR-002 | Doctor value and zero commission | Reviewed original requirement and source boundary; no live/staging evidence |
| 3 | F14-FR-003 | Primary CTAs and owner destinations | Reviewed original requirement and source boundary; no live/staging evidence |
| 4 | F14-FR-004 | Patient/doctor journey sequence | Reviewed original requirement and source boundary; no live/staging evidence |
| 5 | F14-FR-005 | Trust, verification and no cure guarantee | Reviewed original requirement and source boundary; no live/staging evidence |
| 6 | F14-FR-006 | Zero commission and voluntary support | Reviewed original requirement and source boundary; no live/staging evidence |
| 7 | F14-FR-007 | File20 owned route/placement registry | Reviewed original requirement and source boundary; no live/staging evidence |
| 8 | F14-FR-008 | File25 consumes canonical content blocks | Reviewed original requirement and source boundary; no live/staging evidence |
| 9 | F14-FR-009 | File07/08/09 owner destination health | Reviewed original requirement and source boundary; no live/staging evidence |
| 10 | F14-FR-010 | Consent/GPC attribution | Reviewed original requirement and source boundary; no live/staging evidence |
| 11 | F14-FR-011 | File01/07/08/09 owner-native outcomes | OPEN #19 exact File01/07/08/09 owner attestation; not File14 truth |
| 12 | F14-FR-012 | Founder experiment lifecycle | Reviewed original requirement and source boundary; no live/staging evidence |
| 13 | F14-FR-013 | en-US/ur-PK/ar-SA localization | Reviewed original requirement and source boundary; no live/staging evidence |
| 14 | F14-FR-014 | FAQ source/policy history | Reviewed original requirement and source boundary; no live/staging evidence |
| 15 | F14-FR-015 | Accessibility, mobile and low-data | Reviewed original requirement and source boundary; no live/staging evidence |
| 16 | F14-FR-016 | Claim basis/freshness/audit | Reviewed original requirement and source boundary; no live/staging evidence |
| 17 | F14-FUT-01 | Intent router no sensitive profiling | Reviewed original requirement and source boundary; no live/staging evidence |
| 18 | F14-FUT-02 | Allowlisted destination handoff | Reviewed original requirement and source boundary; no live/staging evidence |
| 19 | F14-FUT-03 | Trust evidence current-public gate | Reviewed original requirement and source boundary; no live/staging evidence |
| 20 | F14-FUT-04 | Freshness sentinel expiry/review/historic audit | Reviewed original requirement and source boundary; no live/staging evidence |
| 21 | F14-FUT-05 | Commission parity and source copy scan | Reviewed original requirement and source boundary; no live/staging evidence |
| 22 | F14-FUT-06 | Approved jurisdictional text | Reviewed original requirement and source boundary; no live/staging evidence |
| 23 | F14-FUT-07 | Protected semantic copy diff | Reviewed original requirement and source boundary; no live/staging evidence |
| 24 | F14-FUT-08 | Dark-pattern disallow list | Reviewed original requirement and source boundary; no live/staging evidence |
| 25 | F14-FUT-09 | Honest destination failover | Reviewed original requirement and source boundary; no live/staging evidence |
| 26 | F14-FUT-10 | Nonmutating scenario previews | Reviewed original requirement and source boundary; no live/staging evidence |
| 27 | F14-FUT-11 | Provisional evidence-based quality score | Reviewed original requirement and source boundary; no live/staging evidence |
| 28 | F14-FUT-12 | Auditable misleading copy reports | NEW R50-D01: guarded moderation services return false/unverified SQL rows |
| 29 | F14-FUT-13 | Consent and aggregate friction | Reviewed original requirement and source boundary; no live/staging evidence |
| 30 | F14-FUT-14 | Suppressed unverified funnel anomaly | Reviewed original requirement and source boundary; no live/staging evidence |
| 31 | F14-FUT-15 | Minimum-ten cohort privacy | Reviewed original requirement and source boundary; no live/staging evidence |
| 32 | F14-FUT-16 | Founder approval experiment preflight | Reviewed original requirement and source boundary; no live/staging evidence |
| 33 | F14-FUT-17 | Experiment auto-stop AND changed-placement restore | OPEN #22 File20/25 approved changed-placement CAS restoration |
| 34 | F14-FUT-18 | FAQ gap drafts only | Reviewed original requirement and source boundary; no live/staging evidence |
| 35 | F14-FUT-19 | Locale/claim consistency graph | Reviewed original requirement and source boundary; no live/staging evidence |
| 36 | F14-FUT-20 | Translation source and terminology lock | Reviewed original requirement and source boundary; no live/staging evidence |
| 37 | F14-FUT-21 | AI draft/no autoshare | Reviewed original requirement and source boundary; no live/staging evidence |
| 38 | F14-FUT-22 | Public reviewed change log | NEW R50-D02: privileged governance record reader erases SQL failures or corrupt JSON |
| 39 | F14-FUT-23 | Choose safely informational guidance | Reviewed original requirement and source boundary; no live/staging evidence |
| 40 | F14-FUT-24 | Doctor nonbinding readiness | Reviewed original requirement and source boundary; no live/staging evidence |

## Two NEW demonstrated SOURCE defects, recorded before any PHP edits

**R50-D01 / FUT12 Moderator Report & Queue (native service read evidence):** The `reports()` and `review_queue_reports()` native capability-guarded PHP services still forward `$wpdb->get_results()` unchecked; WordPress SQL read failure `false/null` goes to consumers as false/null rather than a typed operational 503 and non-sensitive audit/error signal. Their legacy column result arrays are also trusted without minimum row shapes, so incomplete or malformed moderation rows can be mistaken for normal queue entries. This remains unaddressed after R49 fixed *REST-paginated* `reports_page`, which is a different method. Fix both direct source service APIs fail-closed on unreadable SQL/invalid row fields, retaining native auth and legitimately empty arrays. No evidence that installed live reports are lost.

**R50-D02 / FUT22 and FR016 governed admin records (source truth):** `GCU_Future_Intelligence::records(type, public_only=false)` still maps false/null SQL read to a successful empty list and silently uses `json_array()` on corrupted, non-JSON-syntactic/scalar or missing `payload`, fabricating valid-looking empty administrative records. `records(public_only=true)` was guarded in R45 against SQL failure, but its result shapes remained unverified too. Do NOT invent a new public publishing policy; for either reader, reject unreadable SQL and malformed row/payload with privacy-safe `WP_Error` (503), preserve an intentional JSON object/list and valid SQL empty result, and preserve the original native capability requirement when `public_only=false`. Do not expose raw record or report text in operational logs.

Existing separate unresolved EXTERNAL owner blockers #19 (File01/07/08/09 native verified outcomes and actual staging acceptance) and #22 (File20/25 Founder-approved real experiment changed-placement baseline CAS rollback/staging) remain OPEN; R50 cannot close them.

## Ordered correction only after full review freeze

Only after this full 40-lens register is committed, implement both corrections together, add 40 **actual File14 method** mocked WordPress/SQL cases for false/null/invalid row/invalid JSON/valid empty and positive rows, ensure public change-log doesn't falsely publish empty when storage fails; run exact PHP7.4/8.3, 40×40 source requirement loops, 2 Fresh Reviews and deterministic ZIP/SBOM on exact branch HEAD, merge and repeat QA against exact new main HEAD. Candidate v1.4.53; SQL schema target unchanged, no actual deploy planned.

Final required evidence fields: Repository prepatch HEAD `5689742d9d9848f65d2e610c5b0d43738ea1b4c3`; Deployed Version **UNVERIFIED**; Actual DB Version **UNVERIFIED**; Migration State **UNVERIFIED**; Live Verification Status **NOT RUN**. Exact deployed code ابھی unverified ہے؛ repository-based diagnosis provisional ہے۔
