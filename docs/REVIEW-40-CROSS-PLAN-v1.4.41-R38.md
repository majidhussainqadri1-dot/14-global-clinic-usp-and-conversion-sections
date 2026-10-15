# File14 R38 — Forty Requirement Reviews, Two Source Corrections and Blocking Evidence

**Review date:** 2026-10-15. **Frozen GitHub main BEFORE changes:** `06c8468b6eef5a0c5032af4767686f309b17b00b` (source version `1.4.40`). **Approved authority:** attached File14 amended master plan `v2.0-Future-CTI-24` (2026-08-10), platform Three Central Plans, File07/08/09/25 master plans, and source owners File01/20; do not replace plan content with inferred implementation. Exact linked owner-repo frozen SHAs: File07 `2f4a89707724fd2b9946600afe10ddab27ec3c2d`, File08 `9dcc8d16ea81d0bbd27184e71bc09ba04d30ba03`, File09 `cfc5f781a766330314dc98c42abeca0eb7786eba`, File01 `adf6dbb9980a85f25f7cf4c2ee679b52703c2e71`, File20 `8a4dbcaf4fef8e926b9b834ecfde16c21a0f00ca`.

## FIRST: exact numbers, what was corrected, remaining and blockers

- **New runtime/source defects found:** **2**.
- **New runtime/source defects corrected in candidate:** **2/2** subject to exact-PR AND merged-main CI.
- **Test-only negative fixture issue:** **1**, corrected. This was not a production defect: healthy returns were incorrectly required to log an error.
- **Prior open requirements:** **2**, Issue #19 owner-origin File01/07/08/09 consent and accepted event registry; Issue #22 actual experiment-owned mutated placement rollback with File20/25 provenance and CAS. Neither closed or claimed complete.
- **Evidence limitation:** WordPress staging, server DB, active wp_options, runtime error logs, exact deployed checksum and live re-test are NOT available. No live defect or live fix claim.

## Exactly forty approved-plan source review lenses

Sixteen original File14 FR requirements and twenty-four amended FUT requirements were checked for their File14 source implementation and File01/07/08/09/20/25 owner boundary. The executable `scripts/review40-iterative.sh` must execute the entire 40-source-guard suite **forty times sequentially** (40×40 source-marker evaluations), and prior R34–R37 suites each exercise forty independent method or scenario variants. R38 adds `tests/r38-early-stop-batch-completeness-tests.php` with **40 direct method cases**, not just forty repeated static strings. These remain **isolated PHP/SQL simulations**, NOT forty WordPress E2E tests. All source checks have limited evidentiary weight for real owner integration and visual acceptance.

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
| 30 | F14-FUT-14 | Conversion Anomaly Detector | R37 complaint-count DB failure now explicit | R38 source: overflow + conflicting expiry list corrected; #22 STILL BLOCKED |
| 31 | F14-FUT-15 | Small-Cohort Privacy Guard | min 10; names/counts suppressed | Source contract checked; deployment NOT proven |
| 32 | F14-FUT-16 | Experiment Safety Preflight Simulator | mandatory approval+run gates | Source contract checked; deployment NOT proven |
| 33 | F14-FUT-17 | Experiment Early-Stop & Rollback Guard | R38 overflow/list conflict corrected; #22 STILL BLOCKED; changed-placement undo still OPEN | RELEASE BLOCKED in remaining portion |
| 34 | F14-FUT-18 | FAQ Gap Intelligence | anonymous aggregates only | Source contract checked; deployment NOT proven |
| 35 | F14-FUT-19 | Message Consistency Graph | owner claims/locale parity | Source contract checked; deployment NOT proven |
| 36 | F14-FUT-20 | Translation Provenance Lock | source and reviewer provenance | Source contract checked; deployment NOT proven |
| 37 | F14-FUT-21 | AI Ethical Copy Assistant | draft/Founder approval | Source contract checked; deployment NOT proven |
| 38 | F14-FUT-22 | Public Clinic Trust Change Log | verified public changes only | Source contract checked; deployment NOT proven |
| 39 | F14-FUT-23 | Patient Choose Safely Guide | information, not diagnosis | Source contract checked; deployment NOT proven |
| 40 | F14-FUT-24 | Doctor Readiness Self-Check | no File09 verification decision | Source contract checked; deployment NOT proven |

## Confirmed R38 root causes and completed correction AFTER review

**R38-D01 — LIMIT 50 silently hides safety backlog.** The FUT17 early-stop selected at most 50 rows and reported count=50 successful Stop results even if at least one more running experiment needed stopping after a global trust/policy breach. No backlog signal, so operational users could misread the whole sweep as finished. **Source fix:** SQL reads `LIMIT 51` as a non-mutating overflow sentinel, stops at most 50 with existing per-experiment transaction/audit/lock controls, records `backlog_detected: true` and returns the established `gcu_experiment_stop_batch_incomplete` error instead of clean success. At EXACTLY 50 it returns true success if all audits/transactions commit. Does NOT assert all remaining experiments automatically stopped, and does NOT expose individual experiment IDs.

**R38-D02 — Positive expiry COUNT followed by empty selection treated as clean.** Under healthy policy, the routine observes `COUNT(*) > 0` for expired running experiments, then the second SQL SELECT might be empty due to failure/race or inconsistent view; prior implementation returned 0 Stops with no anomaly. **Source fix:** fail closed with `gcu_experiment_expiry_list_mismatch` and structured privacy-safe operational error, not a fabricated success. Non-numeric or negative count evidence is also rejected.

**R38-T01 — non-production test fixture correction.** The new direct-method regression initially insisted on an error event after **all** types of results, including successful 0/50 and small clean batches; this mislabeled correct code as FAIL. Adjusted to demand an error log only for expected error results. Do NOT count as a third deployed/source defect.

## Verification gates and precise limitations

- `scripts/review40-iterative.sh`: 40 repeat runs, each with 40 File14 approved requirement/source markers, plus 40 distinct R34 lifecycle fault modes.
- `tests/r35-experiment-preflight-workflow-tests.php`: 40 experiment approval/run cases.
- `tests/r36-owner-cohort-privacy-tests.php`: 40 owner-stage privacy aggregation examples.
- `tests/r36-hourly-stop-error-tests.php`: 40 real method simulation outcomes for cron stop errors.
- `tests/r37-early-stop-db-failure-tests.php`: 40 DB/transaction guard outcomes.
- `tests/r38-early-stop-batch-completeness-tests.php`: 40 overflow/mismatch/boundary/normal/no-expiry cases; includes 51+ row read, exactly 50 allowed and full absence of private identifiers in logs.
- PHP 7.4, PHP 8.3, two fresh reviews, deterministic package/SBOM, exact candidate and merged-main CI must be GREEN before source-complete status.

## Final separate realities / not-yet-done

| Field | Status |
|---|---|
| Repository HEAD | Freeze above; PR and merged SHA verified separately |
| Source plugin version | `1.4.41` candidate |
| DB targets named in source | Base `10005`, Future `1` (not deployed DB versions) |
| Deployed plugin version & checksum | UNVERIFIED |
| Actual Database version/tables/rows/wp_options | UNVERIFIED |
| Migration State | UNVERIFIED |
| Staging WordPress acceptance | NOT TESTED |
| Live Verification Status | NOT TESTED |
| Open Issues #19 / #22 | BLOCKED / need separately reviewed implementations, owner approval and live proof |

Exact deployed code ابھی unverified ہے؛ repository-based diagnosis provisional ہے۔
