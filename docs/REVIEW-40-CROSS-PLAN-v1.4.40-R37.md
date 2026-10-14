# File14 R37 — Forty-Review Requirements, Findings and Remediation

**Audit date:** 2026-10-14. **Source frozen before correction:** File14 GitHub `main@2a8d6023b3ad1df18cb59496ed3792c1c65c3e6a`, plugin `1.4.39`.
**Sources:** Attached approved File14 amended `v2.0-Future-CTI-24` master plan (2026-08-10), consolidated Three Central Plans 2026, and attached File07/08/09 and File25 plans. Cross-file repository freeze: File07 `2f4a89707724fd2b9946600afe10ddab27ec3c2d`, File08 `9dcc8d16ea81d0bbd27184e71bc09ba04d30ba03`, File09 `cfc5f781a766330314dc98c42abeca0eb7786eba`, File01 `adf6dbb9980a85f25f7cf4c2ee679b52703c2e71`, File20 `8a4dbcaf4fef8e926b9b834ecfde16c21a0f00ca`.

## Mandatory numerical defect statement

- **NEW, independently source-evidenced defects this R37 review:** **3**.
- **New defects corrected in candidate source:** **3/3**; functional confirmation requires clean exact-PR and post-merge `main` CI.
- **Prior known separately OPEN requirements:** **2** (GitHub Issues #19, #22). They are NOT counted as newly discovered.
- **New confirmed live incidents:** **0**; no live diagnostic evidence was obtained.
- **External blockers:** no approved real File01/07/08/09 event receipt acceptance; no provenance-bound File20/25 truly changed-placement atomic CAS restore; no deployed code checksum, actual DB/options/migrations, staging/production runtime logs and live re-test.

## 40 distinct approved-plan source review lenses

The approved specification has 16 `F14-FR-` Functional Requirements and 24 `F14-FUT-` approved future enhancement requirements. The following **40 distinct requirement lenses** are reviewed against the exact File14 source and canonical File01/07/08/09/20/25 ownership; the executable `scripts/review40-iterative.sh` additionally performs **40 complete consecutive automated source-marker reviews**, each covering 40 gates (1600 repeated marker checks), with prior forty lifecycle, forty preflight, forty privacy and forty hourly-stop behavioral simulations. Those are **source and isolated-PHP tests**, not real WP integration or browser E2E evidence.

| Review | Approved requirement | Area | Owner/contract cross-check | Conclusion |
|---:|---|---|---|---|
| 1 | F14-FR-001 | Patient value proposition | File07 doctor discovery; File08 appointments; emergency limitation | Source contract checked; deployment NOT proven |
| 2 | F14-FR-002 | Doctor value proposition | File09 onboarding; File01 0% commission governance | Source contract checked; deployment NOT proven |
| 3 | F14-FR-003 | Primary CTAs | File07 Find Doctor; File09 Start Clinic; File20 navigation | Source contract checked; deployment NOT proven |
| 4 | F14-FR-004 | How it works | File07→08 patient flow; File09 doctor verification flow | Source contract checked; deployment NOT proven |
| 5 | F14-FR-005 | Trust content | File09 verification scope; evidence & no cure guarantee | Source contract checked; deployment NOT proven |
| 6 | F14-FR-006 | Business copy | File01 commission/free-tier/support neutrality | Source contract checked; deployment NOT proven |
| 7 | F14-FR-007 | Placement registry | File20 owned slots/route/audience; File25 presentation | Source contract checked; deployment NOT proven |
| 8 | F14-FR-008 | Reusable content blocks | File25 visual component consumes File14 copy | Source contract checked; deployment NOT proven |
| 9 | F14-FR-009 | Destination health | File07/08/09 owner-ready contract/route safety | Source contract checked; deployment NOT proven |
| 10 | F14-FR-010 | Campaign attribution | User consent, GPC, minimization and retention | Source contract checked; deployment NOT proven |
| 11 | F14-FR-011 | Funnel events | Owner native File07/08/09 receipts; no inferred successful booking | RELEASE BLOCKED in remaining portion |
| 12 | F14-FR-012 | Experiment governance | Founder states/variants/safe metrics & audits | Source contract checked; deployment NOT proven |
| 13 | F14-FR-013 | Localization | en-US source; ur-PK/ar-SA RTL | Source contract checked; deployment NOT proven |
| 14 | F14-FR-014 | FAQ governance | Owner-linked versioned fresh answers | Source contract checked; deployment NOT proven |
| 15 | F14-FR-015 | Accessibility | File25 keyboard/focus/reduced motion; staging devices | Source contract checked; deployment NOT proven |
| 16 | F14-FR-016 | Claim audit | Source/owner/time/status/expiry evidence | Source contract checked; deployment NOT proven |
| 17 | F14-FUT-01 | Ethical Intent Router | explicit intent, no profiling | Source contract checked; deployment NOT proven |
| 18 | F14-FUT-02 | Smart Destination Handoff | allowlisted destination owner | Source contract checked; deployment NOT proven |
| 19 | F14-FUT-03 | Trust Evidence Drawer | approved public claims | Source contract checked; deployment NOT proven |
| 20 | F14-FUT-04 | Claim Freshness Sentinel | stale claims unavailable | Source contract checked; deployment NOT proven |
| 21 | F14-FUT-05 | Zero-Commission Parity Sentinel | File01 rules; no monetized ranking | Source contract checked; deployment NOT proven |
| 22 | F14-FUT-06 | Jurisdiction-Aware Truthful Copy Engine | approved regional text | Source contract checked; deployment NOT proven |
| 23 | F14-FUT-07 | Semantic Copy-Diff Detector | protected meaning | Source contract checked; deployment NOT proven |
| 24 | F14-FUT-08 | Dark-Pattern Linter | no coercion, false scarcity | Source contract checked; deployment NOT proven |
| 25 | F14-FUT-09 | Destination Failover Matrix | honest degraded CTA | Source contract checked; deployment NOT proven |
| 26 | F14-FUT-10 | Scenario Preview Laboratory | nonmutating preview | Source contract checked; deployment NOT proven |
| 27 | F14-FUT-11 | Conversion Quality Score | only observed performance/owner events | Source contract checked; deployment NOT proven |
| 28 | F14-FUT-12 | Misleading-Copy Report Loop | validated report + audit | Source contract checked; deployment NOT proven |
| 29 | F14-FUT-13 | Privacy-Safe Friction Analytics | small groups suppress private data | Source contract checked; deployment NOT proven |
| 30 | F14-FUT-14 | Conversion Anomaly Detector | R37 complaint-count DB failure now explicit | R37 DB guard corrected in source |
| 31 | F14-FUT-15 | Small-Cohort Privacy Guard | min 10; names/counts suppressed | Source contract checked; deployment NOT proven |
| 32 | F14-FUT-16 | Experiment Safety Preflight Simulator | mandatory approval+run gates | Source contract checked; deployment NOT proven |
| 33 | F14-FUT-17 | Experiment Early-Stop & Rollback Guard | R37 partial/failed stop disclosure; changed-placement undo still OPEN | RELEASE BLOCKED in remaining portion |
| 34 | F14-FUT-18 | FAQ Gap Intelligence | anonymous aggregates only | Source contract checked; deployment NOT proven |
| 35 | F14-FUT-19 | Message Consistency Graph | owner claims/locale parity | Source contract checked; deployment NOT proven |
| 36 | F14-FUT-20 | Translation Provenance Lock | source and reviewer provenance | Source contract checked; deployment NOT proven |
| 37 | F14-FUT-21 | AI Ethical Copy Assistant | draft/Founder approval | Source contract checked; deployment NOT proven |
| 38 | F14-FUT-22 | Public Clinic Trust Change Log | verified public changes only | Source contract checked; deployment NOT proven |
| 39 | F14-FUT-23 | Patient Choose Safely Guide | information, not diagnosis | Source contract checked; deployment NOT proven |
| 40 | F14-FUT-24 | Doctor Readiness Self-Check | no File09 verification decision | Source contract checked; deployment NOT proven |

## Root causes and completed source corrections (not live fixes)

**R37-D01 — complaint safety query false-success** (F14-FUT-17, F14-NFR-003/006): A failed `get_var()` `COUNT(*)` of open copy-quality reports was coerced to `(int) 0`; this could treat an unverifiable complaint threshold as normal. **Fix:** reject `false`, `null`, nonnumeric values with explicit `gcu_experiment_complaint_count_failed` error, structured privacy-safe operational log; no suppression of an uncertain safety state.

**R37-D02 — failed experiment list appeared empty** (F14-FUT-17, F14-NFR-003/006): A failed `get_results()` read of running experiments was changed to an empty array by `is_array($rows) ? $rows : array()`; early-stop returned zero successful stops. **Fix:** refuse to claim successful safety sweep if rows cannot be read; `gcu_experiment_running_list_failed` error and structured log.

**R37-D03 — partial stop batch appeared clean** (F14-FUT-17, F14-NFR-003/006): A failed experiment lock, begin transaction, compare-and-swap update, audit write or commit `continue`d without being represented in the final count. **Fix:** count unsuccessful attempts; preserve each independent rollback/retry boundary; return `gcu_experiment_stop_batch_incomplete` and privacy-minimized counts of attempted failures and succeeded Stops for operator alert. Never log affected subject/experiment IDs.

## Test coverage and remaining tasks

New R37 `tests/r37-early-stop-db-failure-tests.php` covers 40 parameterized cases including false/null complaint count; failed running-experiment selection; failed expiry check; successful no-op; successful audited stop; lock/transaction/CAS/audit failures. Existing R34–R36 tests and quality/package workflow remain gating. Behavior is simulated against the actual R37 method with PHP DB stubs—not full WordPress DB.

**Open #19:** File07/08/09 owner-attested data, File01 registration and sensitive-route analytics consent acceptance still need real integration/staging. Browser CTA != owner-success.

**Open #22:** Experiment baseline snapshot, drift containment and paused serving exist, but **actually changed Placement** rollback via versioned provenance-bound atomic CAS and File20/25 acceptance remains unimplemented/untested in staging. Do not close as completed.

## Required three-reality release record

| Item | Evidence state |
|---|---|
| Repository HEAD | Frozen pre-fix HEAD above; update exact merged `main` SHA only after merge |
| Source Plugin Version | `1.4.40` candidate |
| Schema targets in source | Base `10005`; Future `1` (no new migration) |
| Deployed version/checksum | UNVERIFIED |
| Actual DB version / wp_options / rows / columns | UNVERIFIED |
| Migration state | UNVERIFIED |
| Staging WordPress/owner integration | NOT TESTED |
| Live Verification Status | NOT TESTED |

CI green only proves source-test quality. **Exact deployed code ابھی unverified ہے؛ repository-based diagnosis provisional ہے۔**
