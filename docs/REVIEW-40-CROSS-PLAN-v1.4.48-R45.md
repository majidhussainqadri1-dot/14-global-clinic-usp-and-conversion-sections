# File14 R45 — Frozen PRE-CODE 40-requirement source review

Date 2026-10-17. Exact repository/source freeze: `main@20065823df87cabe0232d52c951361530d392c9b` / WordPress plugin header **1.4.47**. These facts do not establish deployed artifact identity. Source schema targets Base 10005/Future 1; actual production DB and migrations unknown.

## Governing original documents inspected
- Attached File14 complete amended v2.0 with FR001–FR016 and FUT01–FUT24. FUT22 requires an approved, reviewed, public trust/policy change log and truthful evidence; lifecycle/privacy sections mandate retention and deletion propagation, verification and audit.
- Attached Three Central Plans Consolidated Governing Master Plan: retention, error monitoring, privacy lifecycle and non-deceptive policy disclosure; strict owner boundaries and staging/backup/rollback acceptance.
- Attached File07 Doctor Directory, File08 Worldwide Clinic/Appointments, File09 Global Doctor Onboarding, File25 Public UI documents; File01 owner event registry, File20 shell placement and File24 privacy assurance remain distinct owners. No deployed owner integration was observed.

The project CI script `scripts/review40-iterative.sh` repeats 40 source requirements 40 times; it is a source-regression approximation, NOT forty real WordPress instances/browser tests. This table freezes all distinct lenses BEFORE R45 runtime edits.

| Lens | Approved requirement | Governing function | PRE-EDIT result |
|---:|---|---|---|
| 1 | F14-FR-001 | Patient route/value proposition | F14 source/owner contract reviewed; staging/live unverified |
| 2 | F14-FR-002 | Doctor value and zero commission | F14 source/owner contract reviewed; staging/live unverified |
| 3 | F14-FR-003 | Primary CTAs and owner destinations | F14 source/owner contract reviewed; staging/live unverified |
| 4 | F14-FR-004 | Patient/doctor journey sequence | F14 source/owner contract reviewed; staging/live unverified |
| 5 | F14-FR-005 | Trust, verification and no cure guarantee | F14 source/owner contract reviewed; staging/live unverified |
| 6 | F14-FR-006 | Zero commission and voluntary support | F14 source/owner contract reviewed; staging/live unverified |
| 7 | F14-FR-007 | File20 owned route/placement registry | F14 source/owner contract reviewed; staging/live unverified |
| 8 | F14-FR-008 | File25 consumes canonical content blocks | F14 source/owner contract reviewed; staging/live unverified |
| 9 | F14-FR-009 | File07/08/09 owner destination health | F14 source/owner contract reviewed; staging/live unverified |
| 10 | F14-FR-010 | Consent/GPC attribution | F14 source/owner contract reviewed; staging/live unverified |
| 11 | F14-FR-011 | File01/07/08/09 owner-native outcomes | OPEN File01/07/08/09 #19: owner event attestation |
| 12 | F14-FR-012 | Founder experiment lifecycle | F14 source/owner contract reviewed; staging/live unverified |
| 13 | F14-FR-013 | en-US/ur-PK/ar-SA localization | F14 source/owner contract reviewed; staging/live unverified |
| 14 | F14-FR-014 | FAQ source/policy history | F14 source/owner contract reviewed; staging/live unverified |
| 15 | F14-FR-015 | Accessibility, mobile and low-data | F14 source/owner contract reviewed; staging/live unverified |
| 16 | F14-FR-016 | Claim basis/freshness/audit | NEW R45-D01: retained governance records lifecycle cleanup failure treated as result |
| 17 | F14-FUT-01 | Intent router no sensitive profiling | F14 source/owner contract reviewed; staging/live unverified |
| 18 | F14-FUT-02 | Allowlisted destination handoff | F14 source/owner contract reviewed; staging/live unverified |
| 19 | F14-FUT-03 | Trust evidence current-public gate | F14 source/owner contract reviewed; staging/live unverified |
| 20 | F14-FUT-04 | Freshness sentinel expiry/review/historic audit | F14 source/owner contract reviewed; staging/live unverified |
| 21 | F14-FUT-05 | Commission parity and source copy scan | F14 source/owner contract reviewed; staging/live unverified |
| 22 | F14-FUT-06 | Approved jurisdictional text | F14 source/owner contract reviewed; staging/live unverified |
| 23 | F14-FUT-07 | Protected semantic copy diff | F14 source/owner contract reviewed; staging/live unverified |
| 24 | F14-FUT-08 | Dark-pattern disallow list | F14 source/owner contract reviewed; staging/live unverified |
| 25 | F14-FUT-09 | Honest destination failover | F14 source/owner contract reviewed; staging/live unverified |
| 26 | F14-FUT-10 | Nonmutating scenario previews | F14 source/owner contract reviewed; staging/live unverified |
| 27 | F14-FUT-11 | Provisional evidence-based quality score | F14 source/owner contract reviewed; staging/live unverified |
| 28 | F14-FUT-12 | Auditable misleading copy reports | F14 source/owner contract reviewed; staging/live unverified |
| 29 | F14-FUT-13 | Consent and aggregate friction | F14 source/owner contract reviewed; staging/live unverified |
| 30 | F14-FUT-14 | Suppressed unverified funnel anomaly | F14 source/owner contract reviewed; staging/live unverified |
| 31 | F14-FUT-15 | Minimum-ten cohort privacy | F14 source/owner contract reviewed; staging/live unverified |
| 32 | F14-FUT-16 | Founder approval experiment preflight | F14 source/owner contract reviewed; staging/live unverified |
| 33 | F14-FUT-17 | Experiment auto-stop AND changed-placement restore | OPEN File20/25 #22: proven changed-placement rollback |
| 34 | F14-FUT-18 | FAQ gap drafts only | F14 source/owner contract reviewed; staging/live unverified |
| 35 | F14-FUT-19 | Locale/claim consistency graph | F14 source/owner contract reviewed; staging/live unverified |
| 36 | F14-FUT-20 | Translation source and terminology lock | F14 source/owner contract reviewed; staging/live unverified |
| 37 | F14-FUT-21 | AI draft/no autoshare | F14 source/owner contract reviewed; staging/live unverified |
| 38 | F14-FUT-22 | Public reviewed change log | NEW R45-D02: public policy change-log false empty on SQL read failure |
| 39 | F14-FUT-23 | Choose safely informational guidance | F14 source/owner contract reviewed; staging/live unverified |
| 40 | F14-FUT-24 | Doctor nonbinding readiness | F14 source/owner contract reviewed; staging/live unverified |

## New verified GitHub-source defects (not proven live incidents)
- **R45-D01 (privacy lifecycle and F14-FR-016/FUT22 governance):** `GCU_Future_Intelligence::cleanup()` currently swallows `$wpdb->query()===false` for report/record retention deletion, and returns arrays containing `reports=false` or `records=false` as if it were a normal cleanup result. Cron listeners consume no return, and `daily_governance()` returns that array without a failure warning. `runtime_ready()` returning WP_Error is also demoted to `['skipped'=>'runtime_unready']`. SQL cleanup failure must be explicit privacy-safe `WP_Error` with low-cardinality logging, and cannot be falsely certified as lifecycle success.
- **R45-D02 (F14-FUT-22 reviewed public trust/change log):** `GCU_Future_Intelligence::records(type='change_log',public_only=true)` translates `$wpdb->get_results()=false/null` into an empty list. `public_change_log()` consequently reports `[]` and `rest_change_log()` publishes it as no changes; public HTML silently omits the history. A storage failure is **unknown**, not a verified empty change history. Add explicit failure propagation in the *public-only* record reader and public REST; public HTML should degrade with a generic unavailable indicator, never leak database internals.
- **Owner blockers unchanged:** Issue #19 (File01/07/08/09 accepted verified event contract + staging), Issue #22 (actual owned placement modification and authorized File20/25 CAS restore/staging). Do not claim them fixed by adding local File14 code.

## Ordered remediation AFTER the complete 40-lens review
One local code batch: explicit fail-closed retention cleanup and public change-log reads; prevent false empty REST state and show clear non-sensitive unavailable HTML. Add 40 direct actual PHP-method mock SQL/REST/HTML cases, preserve existing code tests, PHP 7.4/8.3 and 40x40 repeated source review gates, independent fresh reviews and ZIP/SBOM. Candidate source plugin **1.4.48**, schema targets unchanged. Merge only after exact PR CI; recheck merged main.

### Mandatory reality statement
Repository HEAD: 20065823df87cabe0232d52c951361530d392c9b. Deployed Version: UNVERIFIED. DB Version: UNVERIFIED. Migration State: UNVERIFIED. Live Verification Status: NOT RUN. Exact deployed code ابھی unverified ہے؛ repository-based diagnosis provisional ہے۔

## R45 one-batch corrections AFTER the frozen 40-source-lens review

**Chronology:** pre-patch 40-source-lens table and BOTH defects were recorded before runtime coding in commit `23117c3117ed6d7c825390d6aa80c336a4d58901`. No local defect was corrected during the incomplete inspection.

| Defect | Source fix | Evidence required |
|---|---|---|
| R45-D01 | `GCU_Future_Intelligence::cleanup()` now propagates existing `runtime_ready()` WP_Error instead of unverified skip. Each bounded report/record retention DELETE tests `false === $n`, emits only a privacy-safe constant error code and returns a 503 WP_Error. Actual successful/partial batch counts remain separately tracked; backlog retains its independent warning. Daily governance already returns `cleanup()` result. | 40 real-method SQL mocked cases for failure first batch, failure after varied successful 200-row batches, runtime unready, and positive cleanup. No real staging purge attempted. |
| R45-D02 | Only the public-only `records()` reader now treats false/null SQL as unverified WP_Error. `public_change_log()` propagates the error instead of publishing `[]`; `rest_change_log()` forwards WP_Error, while HTML displays non-sensitive temporarily-unavailable history message. A genuinely empty but successful SQL array still publishes empty change history, and vetted active approved change rows retain normal REST/HTML shape. | The same method-mocked 40-case test checks public read error, REST error, HTML fallback, verified empty and healthy history. |

**New regression file** `tests/r45-retention-public-history-tests.php`, with ten targeted method-level behavior groups × four cases, reuses R41's real-method test harness (which itself executes 40 test cases) without pretending it is WordPress staging. Integrated `scripts/quality.sh` plus prior 40x40 repeated plan-source sweeps. The R41 stub alone gained a configurable runtime response and approved-record test mock, not production schema changes. CI must confirm exact PHP7.4 and 8.3, source scans, 2 fresh reviews and package/SBOM, then full merged-main rerun.

**Exact scoped tally:** forty specification lenses reviewed, two NEW source defects corrected in candidate, two preexisting external owner gaps (#19/#22) OPEN, zero real staging/installed production tests performed. No assertion of live fix.

### Final five-part reality ledger
Repository HEAD before code: `20065823df87cabe0232d52c951361530d392c9b`; candidate v1.4.48 must be rechecked after CI/merge. Deployed Version: UNVERIFIED. DB Version: UNVERIFIED. Migration State: UNVERIFIED. Live Verification Status: NOT RUN. Exact deployed code ابھی unverified ہے؛ repository-based diagnosis provisional ہے۔
