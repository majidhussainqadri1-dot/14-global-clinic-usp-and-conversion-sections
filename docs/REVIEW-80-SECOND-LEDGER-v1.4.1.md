# File 14 — Second Independent Eighty-Pass Review & Corrective Ledger — v1.4.1

**Baseline re-opened:** `main` at `3c524fb3d6ee481bc222660a56f6192b994e30d0`.  
**Governing scope:** consolidated central governing plan + `SSH-F14-PLAN-2026-v1.0` + `SSH-F14-FUTURE-CTI-2026-v2.0`.  
**Method:** eighty independent repository failure classes; a proven defect is corrected before the corresponding final-state gate is accepted.  
**Truth boundary:** repository/source/test/package evidence only. It does not prove Hostinger staging, deployed artifact, live DB/schema/migration state, live behavior, Founder staging acceptance or operational status.

| Round | Independent review subject | Finding and immediate correction |
|---:|---|---|
| 01 | Exact software/plan identity | PASS — no new defect. |
| 02 | Requirements-traceability version truth | **DEFECT:** active traceability still named current v1.4.0 in its title/final truth rule; corrected to v1.4.1. |
| 03 | Base/Future schema identity separation | PASS — base 10004 and Future 1 remain separate. |
| 04 | Repository status wording | **DEFECT:** status title simultaneously said “Candidate” and “Merged”; replaced by durable repository-release-state wording. |
| 05 | Release-evidence temporal truth | **DEFECT:** evidence document remained frozen in pre-merge “corrective candidate / exact PR SHA” framing; converted to current exact review/main SHA policy. |
| 06 | Obsolete release automation | **DEFECT:** old manual one-shot workflow still targeted branch `complete/file-14-plan-v1.0.0`, PR #3 and release 1.4.0; removed so it cannot replay an obsolete release path. |
| 07 | Temporary correction machinery cleanup | PASS condition defined — temporary patch runner/script must be removed before final PR evidence. |
| 08 | Package folder/text-domain identity | PASS. |
| 09 | Canonical repository identity marker | PASS. |
| 10 | Trusted internal destination-health contract | PASS. |
| 11 | Public destination DTO existence | PASS after corrective DTO addition. |
| 12 | Public destination information minimization | **DEFECT:** public `/destinations` response exposed internal `owner`, `contract` and `verified_at` metadata; added allowlisted public DTO containing only key/availability/safe URL/reason. |
| 13 | Public destination safe-mode behavior | **DEFECT:** `/destinations` could still answer while File 14 base module was disabled/safe; now returns fail-closed 503 like public blocks. |
| 14 | Consumer non-elevation of owner readiness | PASS. |
| 15 | Strict same-origin scheme/host/port | PASS. |
| 16 | File 07/08/09 canonical destination ownership | PASS. |
| 17 | File 20 placement readiness | PASS. |
| 18 | Future schema work on ordinary requests | **DEFECT:** Future bootstrap called `ensure_schema()` on every request, causing schema verification DB work in the hot path; migration removed from Future bootstrap. |
| 19 | Future schema migration concurrency | **DEFECT:** Future additive schema `dbDelta()` path lacked its own named advisory lock; added `future-schema` lock with fail-safe state and guaranteed release. |
| 20 | Activation/upgrade ownership of Future schema | **DEFECT:** Future schema creation depended on later Future bootstrap rather than the controlled activation/upgrade lifecycle; activation/upgrade now explicitly invokes the independently fail-closed Future schema ensure path. |
| 21 | Future schema fast path | PASS after correction — current healthy schema returns without SHOW TABLE/STATUS queries. |
| 22 | Post-migration schema verification | PASS — explicit InnoDB/table verification remains. |
| 23 | Independent Future safe mode | PASS — schema failure records Future safe mode. |
| 24 | Future REST safe-mode fail-close | **DEFECT:** public/admin Future REST routes could execute while Future schema/base runtime was unready; added uniform `runtime_ready()` pre-callback fail-close. |
| 25 | Base mutation readiness | PASS. |
| 26 | Public browse law | PASS. |
| 27 | Content authorization | PASS. |
| 28 | Placement authorization | PASS. |
| 29 | Experiment authorization | PASS. |
| 30 | Claim approval authorization | PASS. |
| 31 | Object/state/version revalidation | PASS. |
| 32 | Idempotent mutation contract | PASS. |
| 33 | Single-use measurement token | PASS. |
| 34 | Atomic rate limiting | PASS. |
| 35 | Durable outbox | PASS. |
| 36 | Durable inbox/stale-lock recovery | PASS. |
| 37 | Tamper-evident audit chain | PASS. |
| 38 | Small-cohort privacy in quality analytics | **DEFECT:** `small_cohort_suppressed=true` still returned exact `sample_count`; exact count is now `null` below threshold and threshold is explicit. |
| 39 | Small-cohort privacy in anomaly analytics | **DEFECT:** insufficient-sample anomaly result stored/returned exact current/baseline counts below threshold; both counts are now suppressed with explicit threshold metadata. |
| 40 | Friction per-stage small-cohort privacy | PASS — prior per-stage suppression retained. |
| 41 | FAQ aggregate direct-identifier rejection | PASS. |
| 42 | Consent before measurement | PASS. |
| 43 | Global Privacy Control | PASS. |
| 44 | Save-Data/reduced-data | PASS. |
| 45 | Sensitive-route exclusion | PASS. |
| 46 | File-14 acquisition-only attribution | PASS. |
| 47 | Pseudonym generation/TTL | PASS. |
| 48 | Privacy export | PASS. |
| 49 | Privacy erasure | PASS. |
| 50 | Public copy-report minimization/rate limit | PASS. |
| 51 | 0% commission policy | PASS. |
| 52 | One free-tier policy | PASS. |
| 53 | Optional support/no advantage | PASS. |
| 54 | REST cache-policy integrity | **DEFECT:** global File-14 post-dispatch hardening overwrote explicit public cache headers with `no-store`; hardening now preserves an endpoint’s explicit Cache-Control and applies a private fallback only when no policy is set. |
| 55 | Future public cache policy | PASS after round 54 correction. |
| 56 | Private Future no-store policy | PASS. |
| 57 | F14-FR-001 patient proposition | PASS. |
| 58 | F14-FR-002 doctor proposition | PASS. |
| 59 | F14-FR-003 canonical CTAs | PASS. |
| 60 | F14-FR-004 how-it-works | PASS. |
| 61 | F14-FR-005 trust content | PASS. |
| 62 | F14-FR-006 business-copy parity | PASS. |
| 63 | F14-FR-007/008 placement/reusable blocks | PASS. |
| 64 | F14-FR-009/010 destination/attribution | PASS after public DTO correction. |
| 65 | F14-FR-011/012 funnel/experiment governance | PASS. |
| 66 | F14-FR-013/014 localization/FAQ | PASS. |
| 67 | F14-FR-015 accessibility | PASS at repository-control level; human staging remains external. |
| 68 | F14-FR-016 claim audit/freshness | PASS. |
| 69 | F14-FUT-01 through F14-FUT-24 catalog | PASS — 24/24 IDs retained. |
| 70 | Release-evidence stale-candidate wording | **DEFECT:** same temporal documentation drift identified in round 05 was confirmed at the release-evidence acceptance gate; corrected before final acceptance. |
| 71 | Status candidate/merged contradiction | **DEFECT:** same status contradiction identified in round 04 was confirmed at the status acceptance gate; corrected before final acceptance. |
| 72 | Exact Sabri Green | PASS. |
| 73 | RTL/LTR locale behavior | PASS at repository-control level. |
| 74 | Reduced motion/data/forced colors | PASS. |
| 75 | 320px reflow / 400% zoom truth boundary | PASS at repository-control level; real browser/zoom evidence remains external. |
| 76 | Deterministic package/path safety/SBOM | PASS condition retained. |
| 77 | PHP 7.4/8.3 matrix | PASS condition retained. |
| 78 | Fresh reviews + independent second-80 gate in quality | PASS after adding `review80-second.py` and second-review regression tests to the quality suite. |
| 79 | Second review traceability/defect ledger | PASS — this ledger is part of the final exact-head gate. |
| 80 | Live-First truth boundary | PASS — repository success cannot establish staging/live/deployed DB/runtime truth. |

## Defect-round index

Fresh defects were discovered in rounds **02, 04, 05, 06, 12, 13, 18, 19, 20, 24, 38, 39, 54, 70 and 71**. Rounds 70 and 71 are deliberate re-checks of the same documentation defects first found in rounds 05 and 04; therefore there are **13 distinct defect classes across 15 defect-bearing review rounds**. Every listed defect was corrected before the corresponding final-state gate could pass.

All remaining rounds found no additional repository defect after preceding corrections. Any defect exposed by exact-head CI after this ledger is written must be corrected and the exact-head gate rerun; it is not permissible to carry forward a historical green result.

## Final acceptance gate

This second ledger is valid only when the exact final review head passes PHP 7.4 and PHP 8.3 quality, the existing policy/contract/reliability/central/Future/first-Review80 suites, `tests/second-review-regression-tests.php`, the original `scripts/review80.py`, the independent `scripts/review80-second.py` 80/80 gate, both fresh post-code review rounds, baseline integrity, deterministic v1.4.1 packaging, SHA-256 verification and file-level SBOM. Staging and live remain separately unverified until tested in the target environment.
