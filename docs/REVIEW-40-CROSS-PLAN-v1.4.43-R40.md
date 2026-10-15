# File14 R40 — Pre-Correction Frozen 40-Requirement Inspection Register

**Source freeze:** `main@077e8cd194a2461594b9644039e922731fcdb1c7` (plugin `1.4.42`), 2026-10-15. **Scope authority:** attached F14 amended v2.0 CTI24 plan, Three Central Plans consolidated final precedence; attached File07/08/09/25 companion plans, and versioned File01/20/24 owner boundaries. These are 40 distinct plan **requirement lenses**; CI `review40-iterative.sh` separately executes the 40-marker source suite 40 times, not 40 WordPress browser runs. This register is written BEFORE any R40 runtime source edits.

| # | Plan requirement | Checked subject | Result |
|---:|---|---|---|
| 1 | F14-FR-001 | Patient route/value proposition | Source contract examined; no staging/live acceptance |
| 2 | F14-FR-002 | Doctor value and zero commission | Source contract examined; no staging/live acceptance |
| 3 | F14-FR-003 | Primary CTAs and owner destinations | Source contract examined; no staging/live acceptance |
| 4 | F14-FR-004 | Patient/doctor journey sequence | Source contract examined; no staging/live acceptance |
| 5 | F14-FR-005 | Trust, verification and no cure guarantee | Source contract examined; no staging/live acceptance |
| 6 | F14-FR-006 | Zero commission and voluntary support | Source contract examined; no staging/live acceptance |
| 7 | F14-FR-007 | File20 owned route/placement registry | Source contract examined; no staging/live acceptance |
| 8 | F14-FR-008 | File25 consumes canonical content blocks | Source contract examined; no staging/live acceptance |
| 9 | F14-FR-009 | File07/08/09 owner destination health | Source contract examined; no staging/live acceptance |
| 10 | F14-FR-010 | Consent/GPC attribution | Source contract examined; no staging/live acceptance |
| 11 | F14-FR-011 | File01/07/08/09 owner-native outcomes | OPEN owner #19 |
| 12 | F14-FR-012 | Founder experiment lifecycle | Source contract examined; no staging/live acceptance |
| 13 | F14-FR-013 | en-US/ur-PK/ar-SA localization | Source contract examined; no staging/live acceptance |
| 14 | F14-FR-014 | FAQ source/policy history | Source contract examined; no staging/live acceptance |
| 15 | F14-FR-015 | Accessibility, mobile and low-data | Source contract examined; no staging/live acceptance |
| 16 | F14-FR-016 | Claim basis/freshness/audit | Source contract examined; no staging/live acceptance |
| 17 | F14-FUT-01 | Intent router no sensitive profiling | Source contract examined; no staging/live acceptance |
| 18 | F14-FUT-02 | Allowlisted destination handoff | Source contract examined; no staging/live acceptance |
| 19 | F14-FUT-03 | Trust evidence current-public gate | Source contract examined; no staging/live acceptance |
| 20 | F14-FUT-04 | Freshness sentinel expiry/review/historic audit | NEW local defect: unverified stale-claim batches |
| 21 | F14-FUT-05 | Commission parity and source copy scan | Source contract examined; no staging/live acceptance |
| 22 | F14-FUT-06 | Approved jurisdictional text | Source contract examined; no staging/live acceptance |
| 23 | F14-FUT-07 | Protected semantic copy diff | Source contract examined; no staging/live acceptance |
| 24 | F14-FUT-08 | Dark-pattern disallow list | Source contract examined; no staging/live acceptance |
| 25 | F14-FUT-09 | Honest destination failover | Source contract examined; no staging/live acceptance |
| 26 | F14-FUT-10 | Nonmutating scenario previews | Source contract examined; no staging/live acceptance |
| 27 | F14-FUT-11 | Provisional evidence-based quality score | Source contract examined; no staging/live acceptance |
| 28 | F14-FUT-12 | Auditable misleading copy reports | Source contract examined; no staging/live acceptance |
| 29 | F14-FUT-13 | Consent and aggregate friction | Source contract examined; no staging/live acceptance |
| 30 | F14-FUT-14 | Suppressed unverified funnel anomaly | Source contract examined; no staging/live acceptance |
| 31 | F14-FUT-15 | Minimum-ten cohort privacy | Source contract examined; no staging/live acceptance |
| 32 | F14-FUT-16 | Founder approval experiment preflight | Source contract examined; no staging/live acceptance |
| 33 | F14-FUT-17 | Experiment auto-stop AND changed-placement restore | OPEN owner/design #22 |
| 34 | F14-FUT-18 | FAQ gap drafts only | Source contract examined; no staging/live acceptance |
| 35 | F14-FUT-19 | Locale/claim consistency graph | Source contract examined; no staging/live acceptance |
| 36 | F14-FUT-20 | Translation source and terminology lock | Source contract examined; no staging/live acceptance |
| 37 | F14-FUT-21 | AI draft/no autoshare | Source contract examined; no staging/live acceptance |
| 38 | F14-FUT-22 | Public reviewed change log | Source contract examined; no staging/live acceptance |
| 39 | F14-FUT-23 | Choose safely informational guidance | Source contract examined; no staging/live acceptance |
| 40 | F14-FUT-24 | Doctor nonbinding readiness | Source contract examined; no staging/live acceptance |

## New frozen source deficiencies, discovered BEFORE correction

**R40-D01 (FUT-04, potentially P0):** `GCU_Future_Intelligence::claim_freshness_sentinel()` at 1.4.42 queries a capped `LIMIT 100`, then `foreach (is_array($rows)?$rows:array())`, treating false/null database result as an empty clean set. For 101+ stale claims, it processes only 100 without backlog signaling.

**R40-D02 (FUT-04, governance):** The same operation `continue`s on failed locks, owned transaction, history insert, compare-and-swap, audit or commit, but returns only success count; `daily_governance()` ignores the returned result and continues with fresh-looking later steps. Unprocessed claims need explicit fail-closed error/operational alert. Never claim 100 rows clean means whole backlog is clean.

**R40-G01 (already-open #19):** owner-attested, consented, versioned File01/07/08/09 destination/application/booking outcomes remain incomplete; no CTA-click-as-booking inference.

**R40-G02 (already-open #22):** experiment-owned modified File20/25 placements cannot yet be provenance/CAS restored atomically and staging tested; approved baseline drift currently pauses and requests manual review. No synthetic restoration.

## Corrective discipline
Finish this 40-requirement inspection before editing runtime. Scope local fix to File14-owned claim freshness and daily governance. Add 40 direct actual-method SQL/transaction mocked cases and exact-source repeat suite; preserve owner boundaries. Target plugin 1.4.43 (no source schema change). Gate PR SHA and merged-main SHA with PHP7.4/8.3, fresh reviews, deterministic package/SBOM. **Exact deployed build, DB and migrations are UNVERIFIED; no deployment/live claim.**

## Separate release-state ledger
Repository HEAD: pre-edit freeze above; Candidate source target 1.4.43; Deployed version: UNVERIFIED; DB version: UNVERIFIED; Migration: UNVERIFIED; Live verification: NOT DONE.

## Post-review R40 corrective batch — tracked separately from the pre-code ledger

**Code correction AFTER all 40 plan lenses were inspected (not at the point each defect was discovered):**

- **R40-D01 FIXED at source:** explicit `gcu_claim_freshness_read_failed` on null/false SQL read. Ordered `LIMIT 101` permits at most 100 bounded changes while reporting a nonzero unprocessed backlog through `gcu_claim_freshness_batch_incomplete`. The result cannot silently certify the complete stale-claim inventory.
- **R40-D02 FIXED at source:** skipped lock, begin transaction, history insert, optimistic CAS, audit or commit counts toward `failed_count`; the method returns `WP_Error` after the sweep if any work remained incomplete. `daily_governance()` now directly checks that error, records only a low-cardinality code and stops the daily process rather than silently treating failed claim governance as completed.

**Evidence from exact PR HEAD `dd0331d0a3528154a11f7315b2120841005a1cf9`:** CI successful on PHP 7.4 and 8.3 with full quality suite, 40 sequential source review rounds × 40 approved markers per round, and 40 direct SQL/transaction-mocked FUT-04 method scenarios. Both independent fresh source reviews and deterministic ZIP/SBOM succeeded. GitHub actions are source/package evidence, not a live WordPress re-test.

**Per-review defect findings in these 40 requirement lenses:** requirement 20 (`F14-FUT-04`) contained the **two local source defects**, now corrected in proposed 1.4.43. Requirement 11 (`F14-FR-011`) remains blocked by preexisting external owner Issue #19. Requirement 33 (`F14-FUT-17`) remains blocked by preexisting changed-placement owner Issue #22. Other 37 lenses had an examined source-level marker/boundary, **not 37 verified live E2E passes**. No other new local defect has been evidenced in this scoped review. No inference that the 2 external gaps are resolved.

### R40 numeric summary
| Classification | Count | Status |
|---|---:|---|
| Scoped plan requirement lenses | 40 | Inspected |
| Freshly found local source defects | 2 | 2 corrected in PR |
| Newly added direct-method SQL-mock test cases | 40 | 40 passed exact PR QA |
| Repeat source-plan sweeps | 40 | Passed exact PR QA |
| Preexisting cross-file acceptance blockers | 2 | Still OPEN |
| Verified actual WordPress/staging/live deployments | 0 | Not performed |

**Release gates:** review branch/PR green; approval/merge into main requires independent **new HEAD** quality + 2 Fresh Reviews + package/SBOM, and separately the actual deployed plugin/version/hash, actual DB columns/rows/options, migration state, staging and Live validation. Thus R40 source correction is not evidence of a production fix.
