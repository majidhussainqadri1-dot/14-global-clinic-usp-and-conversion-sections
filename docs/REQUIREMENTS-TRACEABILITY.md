## File14 R42 Source Candidate 1.4.45

Requirements Traceability — v1.4.45

File 14 v1.4.45 may only claim a status independently supported by exact source/CI, staging acceptance, and installed deployed artifact plus actual DB migration parity. Current R42 sources are repository-only and neither staged nor live verified.

40 distinct plan lenses trace `F14-FR-001..016` and `F14-FUT-01..24` to current source; evidence and review limitations in `docs/REVIEW-40-CROSS-PLAN-v1.4.45-R42.md`. R42-D01 native owner anomaly SQL cannot silently pass as small sample; R42-D02 native owner stage SQL fails closed in privacy-safe REST; R42-D03 browser friction SQL fails closed and preserves cohort guards. 40 direct actual-method tests added; need exact-code PR and merged-main CI, then WordPress staging/Live re-test. This does NOT close owner Issue #19 or #22.

## R41 File14 Current Source Candidate 1.4.44

Requirements Traceability — v1.4.44

File 14 v1.4.44 may only claim a status supported by exact source CI, independent staging acceptance and the live deployed artifact plus actual DB/migration parity. Approved F14-FR-001..016 and FUT-01..24 are mapped in `docs/REVIEW-40-CROSS-PLAN-v1.4.44-R41.md`. R41-D01 (FUT18 FAQ database read), R41-D02 (FUT19 consistency database read) and R41-D03 (daily governance verification/result propagation) have a proposed local source fix and 40 direct real-method mocked-SQL scenarios; exact-PR and post-merge QA required. Open owner #19 and #22 remain. This document establishes no deployed truth.

## File14 R40 Current Source Candidate 1.4.43

Requirements Traceability — v1.4.43

File 14 v1.4.43 may only claim a status independently supported by exact source/CI, staging acceptance, and installed deployed artifact + DB parity. 40 approved FR/FUT source checks and new R40 defects documented in `docs/REVIEW-40-CROSS-PLAN-v1.4.43-R40.md`. The FUT04 freshness sentinel detects failed DB read/100+ backlog and partial claim-processing failures; daily governance propagates them. Issues #19 and #22 unresolved. Not a live result.

## Current File14 R39 Source Candidate 1.4.42

Requirements Traceability — v1.4.42

File 14 v1.4.42 may only claim a status independently supported by source CI, actual staging acceptance and verified deployed artifact and database parity. Repository-only candidate is not live.

Approved FR001–016/FUT01–24 source requirements are listed individually in `docs/REVIEW-40-CROSS-PLAN-v1.4.42-R39.md`. F14-FUT-05/FUT-08 live-copy policy parity now rejects failed SQL reads and a 501st active block sentinel instead of treating a partial/failed scan as complete. R39 adds 40 direct method mocks and the existing 40-round × 40-plan source markers; all source result claims are conditional on exact candidate and post-merge CI. Issue #19 owner-attested conversion and Issue #22 provenance-safe placement restoration remain BLOCKED. No staging/live acceptance or deployed DB parity proof.

## Current File14 R38 Source Candidate 1.4.41

Requirements Traceability — v1.4.41

File 14 v1.4.41 may only claim a status that is independently supported by source CI, staging acceptance and verified deployed artifact / database parity. Source-only candidate remains provisional.

F14-FUT-17 R38: 2 new source defects fixed for stop backlog detection and expiry count/list disagreement; R38 forty-lens report `docs/REVIEW-40-CROSS-PLAN-v1.4.41-R38.md`. File07/08/09 owner completion (#19) and File20/25 changed-placement restoration (#22) still BLOCKED. Prior historical ledgers preserved below.

## Current File14 R37 Source Candidate 1.4.40

Requirements Traceability — v1.4.40

File 14 v1.4.40 may only claim a status that is independently supported by source CI, staging acceptance and verified deployed artifact / database parity. Source-only candidate remains provisional.

F14-FUT-14 and F14-FUT-17: three fail-closed source defects fixed. R37 forty-review requirements report `docs/REVIEW-40-CROSS-PLAN-v1.4.40-R37.md`; prior ledgers remain historical. Issue #19 File01/07/08/09 owner attestation and Issue #22 File20/25 real changed-placement restoration are pending. Staging and live verification unproven.

**File 14 v1.4.39 may only claim a status** supported by exact PR/merged-source CI and separately verified WordPress runtime/deployment parity. R36: `docs/REVIEW-40-CROSS-PLAN-v1.4.39-R36.md`. F14-FUT-13/15 small subgroup presence leak and F14-FUT-14/17 missing stop-guard error signal source fixes; remaining #19/#22 acceptance blockers.

Requirements Traceability — v1.4.39

**File 14 v1.4.39 may only claim a status** proven by exact-source CI, owner-contract acceptance and independent deployed/runtime parity. R35 F14-FUT-16 now enforces safe experiments on actual Founder approval/run; root-cause audit and 40-lens evidence: `docs/REVIEW-40-CROSS-PLAN-v1.4.39-R35.md`. F14-FUT-17 changed-placement restore and F14-FR-011 owner-stage acceptance remain release blockers.

Requirements Traceability — v1.4.39

**File 14 v1.4.37 may only claim a status** supported by exact-source CI and separately verified staging/live/owner-contract acceptance. R34 forty-iteration status: `docs/REVIEW-40-CROSS-PLAN-v1.4.37-R34.md`. F14-FUT-17 changed-placement atomic rollback remains unaccepted.

Requirements Traceability — v1.4.37

**File 14 v1.4.37 may only claim a status** supported by exact source CI, independently verified runtime deployment parity and accepted staging/live results. R33 privacy/anomaly source fix is not a substitute for FUT17 changed-placement restoration.

# Requirements Traceability — v1.4.37

File14 v1.4.37 may only claim a status proven by exact source CI and separately verified live/staging evidence. R33 40-scoped review located two functional gaps; source repaired native privacy/anomaly with remaining restored-placement proof external. See docs/REVIEW-40-CROSS-PLAN-v1.4.37-R33.md.

**File 14 v1.4.35 may only claim a status** supported by exact-source CI and separately accepted staging/live deployment. Current source has not closed owner registry #19 or full auto-restore #22.

**R32 — 2026-10-13:** File14 owner-origin anonymized outcome source + governed experiment immutable baseline/containment. Partial coding only; File01 owner registry, correlated owner-stage acceptance, actual experiment mutation restoration and staging/live parity pending. `docs/REVIEW-20-CROSS-FILE-COMPLETION-v1.4.35-R32.md`.

# File 14 — Fresh Central + File Plan + Future CTI Requirements Traceability — v1.4.35

**R30 2026-10-12:** Twenty frozen File14/central/related File07/08/09/25 source checks before editing (16 source-located, two File14 local defects corrected: explicit governed record identity/state/locale/region not silently rewritten; FUT-24 nonbinding Doctor Readiness JSON/checklist values validated, two owner/design issues #19/#22 still open). Evidence ledger `docs/REVIEW-20-CROSS-FILE-COMPLETION-v1.4.34-R30.md`. No schema migration or staging/live parity proof.


**R29 — 2026-10-12:** Twenty frozen File14/central/companion source reviews before edits (16 code/trace locations, two proven local CV-262/265 native authorization defects fixed, Issues #19/#22 still external). Eleven protected REST callbacks explicitly recheck canonical File00 delegated role/purpose at entry; internal nonpublic record lists and claim revalidation independently authorize, and internal system write can no longer be called as a public service. Ledger `docs/REVIEW-20-CROSS-FILE-COMPLETION-v1.4.33-R29.md`. No DB schema migration or verified deployed/staging/live parity.


**R28 2026-10-12:** 20 independently scoped File14/Three Central/companion (including amended File08 appointment event owner) source reviews completed before modifications: 16 limited source-code trace guards, two local FUT-12/CV-262/265 moderation corrections (terminal states cannot be re-opened including concurrent SQL CAS; revoked File00 permission/failed database reads produce restricted/unavailable state, not fatal/misleading output), two open owner/design issues #19/#22. Full ledger `docs/REVIEW-20-CROSS-FILE-COMPLETION-v1.4.32-R28.md`. No source DB migration, staging acceptance or deployed parity proof.


**R27 2026-10-12:** Twenty frozen File14/central/companion source checks finished before editing (16 limited source-located, two local CV-262/F14-FUT-12 native report-read fixes, two external Issues #19/#22 unresolved). Using current File00 delegated `MANAGE_CONTENT` purpose, list reports, unresolved queue and direct REST callback now check authorization at each native entry, before reporter DB reads. Ledger `docs/REVIEW-20-CROSS-FILE-COMPLETION-v1.4.31-R27.md`. No schema migration or live/staging parity claim.


**R26 — 2026-10-12:** Twenty distinct File14 source/central/owner lenses completed before R26 coding: 16 limited guards present, two File14 FUT-12 P0 correction loop/CV-262 least-privilege issues corrected (reviewing reports remain actionable; reporter message/count/actions accessible only to File00-delegated content moderation) and two owner/design Issues #19/#22 still open. Evidence `docs/REVIEW-20-CROSS-FILE-COMPLETION-v1.4.30-R26.md`. No source schema change or deployed/staging/live proof.


**R25 2026-10-12:** Twenty exact main source/plan/companion reviews before edits: 15 scoped source-presence results, three local F14-FUT-12/CV-262/265 report-moderation fixes (independent File00 authorization, strict expected version/typed identity/state, truthful admin failure result) and Issues #19/#22 still open. Full ledger: `docs/REVIEW-20-CROSS-FILE-COMPLETION-v1.4.29-R25.md`. No source DB migration or deployed/staging/live parity proof.


**R24 2026-10-12:** All 20 frozen File14/central/companion source reviews completed before changes (15 narrowly source guard present, 3 File14 local corrections, 2 external gates Issues #19/#22 remain open). FUT-12 public feedback preserves all 500 Unicode characters or rejects too-long content; FUT-02 handoff explicit context is strictly validated; FUT-10/16 preflight rejects malformed supplied JSON. Full ledger: `docs/REVIEW-20-CROSS-FILE-COMPLETION-v1.4.28-R24.md`. No source DB migration or live/parity proof.


**R23 2026-10-12:** Twenty frozen source/central/companion review lenses completed before R23 code changes: 14 local source evidence indicators, four local corrections and two unresolved external/design gates. The 71 transferred CV IDs are individually mapped by owner/consumer role in `docs/CENTRAL-CV-71-OWNER-CONSUMER-TRACE-v1.4.27-R23.md`; no 71-source-code ownership claim. FUT-12 report typed input, UUID, route and locale fail-closed; Issues #19/#22 open. Full evidence `docs/REVIEW-20-CROSS-FILE-COMPLETION-v1.4.27-R23.md`. Actual deployment, installed DB and staging unverified.


**R22 — 2026-10-11:** Frozen main `1563ad3c9c9ac352902293174a2a209661ffb1fe`, twenty source/central/File07/09/25 lenses completed before edits, 15 source-present, three strictly bounded Future record/revalidation/workflow preflight corrections. Issues #19 and #22 unresolved; no schema change or live parity claim. Ledger `docs/REVIEW-20-CROSS-FILE-COMPLETION-v1.4.26-R22.md`.


**R21 2026-10-11:** Twenty distinct frozen source/central/File07/09/25 reviews completed before coding; File14 atomic duplicate UUID identity, invalid explicitly supplied UUID, and unsupported explicit locale guards hardened. Open Issue #19 owner-verified outcomes and #22 experiment baseline restoration remain gated. Source schema unchanged; no live/staging parity claimed. Full ledger: `docs/REVIEW-20-CROSS-FILE-COMPLETION-v1.4.25-R21.md`.


**R20 2026-10-11:** Twenty static source/central/File07/09/25 contract checks finished before edits. Three local public input typing defects corrected; Issues #19 native owner events and #22 baseline restoration remain separately open. Source target schema unchanged, no live acceptance. Ledger: `docs/REVIEW-20-CROSS-FILE-COMPLETION-v1.4.24-R20.md`.


**File 14 v1.4.34 may only claim a status** that is independently evidenced for its exact current source and deployed artifact. 

**R19 2026-10-11:** 20 frozen exact-source/cross-file lenses before corrections; legacy REST typed funnel/claim/workflow validations and AI non-text guards corrected in 1.4.23. Requirement IDs remain unchanged. Native owner outcome confirmation (Issue #19) and safe experiment baseline restore (Issue #22) are **not implemented** by this amendment. Full source audit: `docs/REVIEW-20-CROSS-FILE-COMPLETION-v1.4.23-R19.md`. Staging/live status unverified.


## Governing freeze

This matrix is for the 2026-08-10 corrective and Future Conversion & Trust Intelligence coding pass. It treats the freshly supplied consolidated central governing plan, `SSH-F14-PLAN-2026-v1.0`, and the Founder-approved additive amendment `SSH-F14-FUTURE-CTI-2026-v2.0` as the governing specification. Repository evidence is implementation evidence only; staging/live/operational status remains separate.

Precedence used by this release: definitive Islamic rule → latest explicit Founder directive/amendment → consolidated central governing plan → fresh File 14 plan → Future CTI amendment → verified exact repository head/runtime evidence.

## Canonical ownership

File 14 owns approved Worldwide Clinic value-proposition copy, reusable semantic content blocks, File 20 placement contracts, ethical patient/doctor conversion journeys, claim evidence/version history, destination health, trust/copy intelligence and privacy-minimized measurement. It does **not** own doctor/profile/verification/clinic/appointment/payment/clinical records, the global shell/navigation, or visual-system truth. Future CTI may explain, lint, measure, preview or hand off to canonical owners; it may not silently become one.

## Fresh central requirements

| Governing ID | File 14 implementation evidence | Automated evidence |
|---|---|---|
| CEN-GOV-001 | Runtime records `GCU_PLAN_VERSION`, `GCU_FUTURE_PLAN_VERSION` and `GCU_CENTRAL_PLAN_BASELINE`; status model keeps coding separate from staging/live | `central-plan-tests.php`, future/fifth-review tests |
| CEN-OWN-001 | Doctor/onboarding/booking truth is consumed through version-gated current File 07 `DDD_Contracts`, File 08 `WCA_Contracts` and File 09 dedicated `gdo_file14_onboarding_destination()` contract; File 00 supplies version-gated `SMC_Contracts::assertions()` plus current assertion hardening; File 01 holds File 14 manifest/routes/contracts; Future CTI stores only File14-owned governance records/reports | contract + cross-file + central-plan + future tests |
| CEN-BIZ-001 | Canonical policy is one approved free tier; parity sentinel blocks drift | policy + future tests |
| CEN-DON-001 | Voluntary support is separate and cannot purchase ranking, visibility, verification or basic service; parity/dark-pattern rules reinforce this | policy + future tests |
| CEN-BRAND-001 | Exact primary Sabri Green `#087A4E`; future CSS consumes same token/fallback | central-plan + CSS/fresh-review tests |
| CEN-NAV-001 | File 20 remains sole global shell/navigation owner. File 14 consumes File 20's route-result, system-check and contextual-navigation hooks and validates File 20's owner-native File 14 CentralPlanContract row before treating shell placement as available; bounded local Back/Home recovery exists only when File 20 is unavailable | contract + cross-file + central-plan tests |
| CEN-LOC-001 | American English canonical File 14 chrome with complete Urdu and Arabic key parity; future protected terminology locks en-US/ur-PK/ar-SA | contract + central-plan + future tests |
| CEN-A11Y-001 | Semantic headings, 44px targets, focus-visible, RTL, reduced motion, forced colors, 320px-class reflow; Future CTI follows same gates | CSS/static tests; browser staging pending |
| CEN-LOWDATA-001 | `Save-Data` and reduced-data support; Future CTI JS is not loaded in low-bandwidth mode | central-plan + fresh-review tests |
| CEN-PRIV-001 | Consent/GPC, bounded attribution, small-cohort suppression, aggregate FAQ signals, PII-minimized copy reports; fifth review rejects sensitive AI-copy input before provider use | contract + central-plan + future/fifth tests |

## File 14 functional requirements

| Requirement | Implementation evidence | Automated evidence |
|---|---|---|
| F14-FR-001 Patient value proposition | Localized patient blocks and verified-directory destination | policy/contract tests |
| F14-FR-002 Doctor value proposition | Localized doctor blocks, zero-commission/free/support rules and onboarding destination | policy/contract tests |
| F14-FR-003 Primary CTAs | File 07 `DDD_Contracts::dependency_health()` and canonical `/doctors/`; File 09 dedicated `gdo_file14_onboarding_destination()` owner contract; same-origin fail-closed redirect | cross-file + contract tests |
| F14-FR-004 How it works | Patient/doctor step journeys and canonical process route | contract tests |
| F14-FR-005 Trust content | Verification limits, privacy, emergency and no-outcome-guarantee claims | policy/contract tests |
| F14-FR-006 Business copy | 0% commission, one free tier, optional support/no advantage, direct fee-owner boundary | policy + future parity tests |
| F14-FR-007 Placement registry | File14-owned semantic route/slot/audience/priority placements; readiness requires an eligible File 01 lifecycle state and exact current File 14 module manifest, all four canonical routes with correct key/owner/status/layout/destination and no unapproved redirects, both current File 14 API/events registry contracts, and validated File 20 owner-native shell contract semantics while File 20 remains the surrounding shell/navigation owner | schema/cross-file/contract tests |
| F14-FR-008 Reusable content blocks | Semantic block DTO/API plus File 25 public `sabri_visual_experience_contract()` and `sabri_visual_experience_render_state()` integration; local scoped fallback remains available when the versioned visual contract is incompatible | cross-file + contract tests |
| F14-FR-009 Destination health | Request-time owner-native File 07/08/09 health probes with minimum runtime/contract compatibility enforcement; File 07 also requires owner system health and File 08 requires owner runtime health before a CTA is exposed; malformed/throwing companions fail closed and unavailable destinations keep an honest no-URL state | cross-file + contract/frontend tests |
| F14-FR-010 Campaign attribution | Consent, GPC, File 14 route allowlist, sanitized first/last attribution and 30-day expiry | privacy/static tests |
| F14-FR-011 Funnel events | Impression/CTA stages, single-use event token, no click-as-success inference; fifth review rejects conflicting replay identity for an existing event UUID | REST/repository/fifth tests |
| F14-FR-012 Experiment governance | Hypothesis/variants/audience/guardrails/sample/privacy and Founder-gated lifecycle; automatic early-stop state+audit is transactional in the scheduled path | policy/contract + future/fifth tests |
| F14-FR-013 Localization | en-US canonical, complete ur-PK/ar-SA File 14 chrome, RTL and runtime parity health | contract/central-plan tests |
| F14-FR-014 FAQ governance | Versioned FAQs, claim links and review deadlines | seed/contract tests |
| F14-FR-015 Accessibility | Unique IDs, semantic sections, 44px targets, focus, RTL/LTR, no inline script, reduced motion/data, responsive reflow | contract/central-plan/fresh-review tests; human staging pending |
| F14-FR-016 Claim audit | Basis/owner/source/effective/review/expiry/history and withdrawal event | repository/contract tests |

## Future Conversion & Trust Intelligence requirements

| Requirement | Founder-approved capability | Implementation evidence | Automated evidence |
|---|---|---|---|
| F14-FUT-01 | Ethical Intent Router | Explicit patient/doctor/learn choices rendered on File14 public routes; no hidden intent inference | `future-intelligence-tests.php`; fresh review |
| F14-FUT-02 | Smart Destination Handoff | Allowlisted country/language/mode context passed only to canonical owner URLs after strict same-origin validation | future tests; contract boundaries |
| F14-FUT-03 | Trust Evidence Drawer | Public current claims expose basis, owner, effective and review dates in accessible `<details>` | future policy/static review |
| F14-FUT-04 | Claim Freshness Sentinel | Daily stale/expired claim transition to `review_required`, public hide, history and audit; privileged revalidation path | future/fresh-review gates |
| F14-FUT-05 | Zero-Commission Parity Sentinel | Compares 0% commission/free tier/optional support rules and canonical claim text; copy/experiment activation blocks on drift | future tests + workflow preflight |
| F14-FUT-06 | Jurisdiction-Aware Truthful Copy Engine | Versioned `jurisdiction_copy` records; approved disclosure only; no eligibility or clinical decision; active/public REST writes require Founder-level approval | future records API + fifth-review approval gate |
| F14-FUT-07 | Semantic Copy-Diff and Meaning Risk Detector | Protected concept comparison for commission/free/support/verification/emergency/outcome/payment | future tests |
| F14-FUT-08 | Dark-Pattern Linter | Detects fake scarcity, guarantees, paid visibility, hidden-fee, coercive-consent and shame patterns | future tests + workflow preflight |
| F14-FUT-09 | Destination Failover Matrix | Truthful File14-owned alternatives when canonical destination is degraded; no fake availability | future handoff API/public rendering |
| F14-FUT-10 | Scenario Preview Laboratory | Admin/API matrix for roles/locales/GPC/low-data/reduced-motion/320px/400%/dependency/safe-mode states | future scenario endpoint + fresh review |
| F14-FUT-11 | Conversion Quality Score | Weighted handoff/accessibility/freshness/privacy/complaints/destination/performance score; provisional unless measurement evidence exists | future policy tests + analytics API |
| F14-FUT-12 | Misleading-Copy Report & Correction Loop | Rate-limited structured reports, sensitive-data rejection, hashed actor, audited resolution workflow | future report API/admin/public form |
| F14-FUT-13 | Privacy-Safe Friction Analytics | Aggregate stage/drop-off view; no raw patient/health profile | future friction API + policy tests |
| F14-FUT-14 | Conversion Anomaly Detector | Minimum-sample current-vs-baseline destination-load anomaly detector | hourly intelligence + cohort guard |
| F14-FUT-15 | Small-Cohort Privacy Guard | Minimum cohort 10 suppresses analytics/FAQ/anomaly decisions below threshold | future tests |
| F14-FUT-16 | Experiment Safety Preflight Simulator | Requires claim-integrity/privacy/accessibility/error-rate/complaint guardrails and forbids sensitive profiling | future tests + activation hook |
| F14-FUT-17 | Experiment Early-Stop & Rollback Guard | Running experiments stop on high anomaly, policy drift, destination failure or complaint threshold; fifth review replaces scheduled stop worker so state update and mandatory audit commit or roll back together | future + fifth-review gates |
| F14-FUT-18 | FAQ Gap Intelligence | Consumes only approved aggregate question adapter, cohort-gates, produces suggestions only, never auto-publishes | future API/daily suggestions + tests |
| F14-FUT-19 | Message Consistency Graph | Compares locale availability and linked claim sets by semantic block identity | future consistency API + governance checks |
| F14-FUT-20 | Translation Provenance & Terminology Lock | Versioned protected terminology for en-US/ur-PK/ar-SA in future governance records | future tests + records API |
| F14-FUT-21 | AI Ethical Copy Assistant | Optional provider adapter receives only non-sensitive bounded copy plus approved claims through the governed REST path; candidates are approved-claim bounded, English + Urdu/Arabic dark-pattern guarded, deterministic fallback, no auto-publish | future tests + fifth-review AI guards |
| F14-FUT-22 | Public Clinic Trust & Change Log | Public material File14 policy/change records with review lifecycle; active/public REST writes require Founder-level approval | seeded public change log + fifth approval gate |
| F14-FUT-23 | Patient Choose-Safely Decision Guide | Educational safety checklist; canonical owners remain source of doctor/booking truth | future policy/public rendering |
| F14-FUT-24 | Doctor Global Clinic Readiness Self-Check | Local non-binding preparation checklist; code explicitly identifies File 09/File 00 as verification owners | future tests + local-only JS |

## Non-functional requirements

| Requirement | Repository implementation evidence | Remaining external evidence |
|---|---|---|
| F14-NFR-001 Authorization | Native File14 capabilities plus action-time File 00 versioned assertions, object/purpose/state/version checks and fail-closed REST; compatibility filter may restrict but never elevate | real-role/IDOR staging matrix |
| F14-NFR-002 Privacy lifecycle | Consent/GPC, minimization, export/erase, bounded attribution, non-destructive uninstall, cohort suppression, PII-minimized reports and sensitive AI-input rejection | WordPress privacy-tool/provider staging acceptance |
| F14-NFR-003 Reliability | Idempotent event write, outbox/inbox, bounded retry/dead-letter; base/Future actual schema fail-close; transactional scheduled experiment early-stop; companion-owner exceptions are isolated into fail-closed/degraded results with bounded logging | queue/provider-failure staging test |
| F14-NFR-004 Performance | Bounded queries, conditional assets, low-data suppression and background work; Future quality marks performance provisional without measured input | measured p75/p95 on Hostinger-equivalent staging |
| F14-NFR-005 Accessibility | File 20 global shell contract, File 25 reusable public state components, bounded local recovery only when shell unavailable, lang/dir, focus, 320px reflow, reduced motion/data and forced colors | screen reader, 320–1920px and 400% zoom acceptance |
| F14-NFR-006 Observability | Base health plus current File 00/01/07/08/09/19/20/24/25 dependency compatibility states; File 01 health exposes lifecycle/route/contract readiness, File 20 requires owner-native semantic contract truth, and File 24 availability requires successful boot evidence; Future parity/anomaly/quality/consistency/report indicators and conversion-event identity conflict warning remain | real logging/alert ownership acceptance |
| F14-NFR-007 Migration/rollback | Install lock, dbDelta, owner-scoped snapshots, safe repair; base and Future table/engine/column truth now gates runtime | fresh install/upgrade/concurrency/restore drill |
| F14-NFR-008 Operability | System Check, safe mode, Future Intelligence admin/scenario lab, retention and reconciliation | operator runbook rehearsal |
| F14-NFR-009 Compatibility | PHP 7.4/8.3 matrix, WordPress 6.6+ metadata and versioned contracts | WordPress 7.0.1/PHP 8.3 staging |
| F14-NFR-010 Localization | Complete File 14-owned en-US/ur-PK/ar-SA chrome plus protected terminology lock and multilingual AI output guard | human locale QA and translation sign-off |

## Definition of Done mapping

- DoD-08 — all user states, 320–1920px, RTL/LTR, keyboard/focus, zoom, contrast and reduced motion: repository controls are coded; human staging remains mandatory.
- DoD-09 — performance/load/provider outage/queue/dead-letter/cache/DB failure and measured SLO: failure-path code exists; measured staging evidence remains mandatory.
- DoD-10 — verified backup restore/cache rebuild/rollback: tooling exists; real restore/rollback drill remains mandatory.
- DoD-11 — two separate fresh review/fix rounds after final coding change: executable fresh review gates include sixth-review corrections; exact-head regression must be green after the final commit.
- DoD-12 — real-role staging, Founder acceptance, production monitoring/rollback window: external release gates; not claimed by repository completion.
- DoD-13 — zero known unresolved blocker/critical defects at repository gate; residual external risks remain documented in release evidence.

## Truth-status rule

`Specified`, `Coded`, `Packaged`, `Automated-QA Green`, `Staging-Accepted`, `Live-Deployed`, and `Operational` are separate statuses. File 14 v1.4.22 may only claim a status for which current exact-head evidence exists. The Future CTI implementation and sixth-review corrections do not alter that rule.

## R5 registry-truth continuation

| R5-CROSS-01 | File 01 current module manifest identity/version and exact canonical route key/path/owner/layout/destination/redirect state are necessary for placement readiness; legacy redirect aliases cannot masquerade as canonical File 14 routes | executable 19-case `tests/review20-r5-behavioral.php`, File 01 current source DTO, current review20 and regression gates |

Latest reviewed File 25 repository source HEAD: `347a4ff4d4c233c5ea6cd82c7786ee5398ea9d1e`; source-interface compatibility only. Live/staging evidence remains external.


## R6 F14-FUT-11 / F14-FUT-13 measurement truth clarification

- The current code correctly refuses to publish a composite Conversion Quality Score without a sufficiently large CTA cohort, **attested** owner-confirmed destination arrival events, measured accessibility and measured performance.
- The public event REST endpoint accepts only impression/CTA events; owner-only arrival/application/booking events cannot be self-asserted. Owner-confirmation readiness defaults fail-closed until File07/08/09 versioned contracts are verified. Stage counts can be shown after privacy thresholds, but unrelated doctor application and patient booking paths must not be combined to invent a sequential dropoff rate. Current truthful status is `owner_correlated_transition_evidence_unavailable`.
- Complete source-side measurement requires separate, approved versioned owner acknowledgements from Files 07/08/09, File 01 registration and real staging validation. Do not classify unimplemented owner-sourced outcome instrumentation as complete.
- Review evidence: `docs/REVIEW-20-CROSS-FILE-COMPLETION-v1.4.10-R6.md` and `tests/future-intelligence-tests.php`.


## R7 compliance and measurement provenance trace (2026-10-10)

- F14-FUT-21 → `GCU_Future_Policy::approved_vocabulary_guard` → AI provider candidate filter → `tests/future-intelligence-tests.php` → R7-D01. Provider candidates with out-of-source tokens rejected, human semantic approval retained.
- F14-FUT-11 → `GCU_Future_Intelligence::quality_score` + `GCU_Future_Policy::conversion_quality_score` → measured privacy required; unverifiable metric/null explicitly surfaced → regression R7-D02.
- F14-FUT-06, 20, 22 → `GCU_Future_Policy::validate_public_record_payload` → `GCU_Future_Intelligence::upsert_record` and public projection checks → regression R7-D03. Existing incomplete public governance records fail closed pending managed correction.
- F14-FR-011 and F14-FUT-11/13/14 → R7-G01 deferred. Only native File07/08/09 approved, authenticated, versioned owner events can affirm arrival/application/booking. File14 cannot invent those facts; no scoring until approved contracts, integration QA and live parity checks.
- Twenty-area source audit ledger: `docs/REVIEW-20-CROSS-FILE-COMPLETION-v1.4.11-R7.md`. Historical R6 source evidence remains independently preserved.


## R8 — F14-FR-011 canonical lifecycle evidence, 2026-10-10

- `F14-FR-011` / `F14-FUT-11/13/14`: File14 owns `impression` and `cta_selected` acquisition facts only. The actual loaded destination, doctor application, and booking state are canonical native File07/08/09 owner facts, not browser/inferred success.
- `GCU_Future_Policy::acquisition_stage_reportable` governs both `GCU_Repository::record_event` and historical acquisition report filtering. Repository-level event validation is independent of the REST check.
- `GCU_Repository::funnel_summary` and `GCU_Future_Intelligence::friction_summary` exclude legacy unverified owner-only rows and explicitly mark `owner_stage_evidence_status=not_attested_excluded`.
- `GCU_Future_Intelligence::quality_score` uses no historic unauthenticated `destination_loaded` rows as confirmed handoffs. Composite score remains unavailable until formally registered native owner-producer evidence and other necessary measurements exist.
- R8 owner confirmations beyond File14 remain deferred under Issue #19. The source audit is not staging or live acceptance. Detailed 20 source rounds: `docs/REVIEW-20-CROSS-FILE-COMPLETION-v1.4.12-R8.md`.


## R9 requirements-to-code safety trace (2026-10-11)

- F14-FR-NFR for safe degradation, migration readiness and operational reliability → `GCU_Future_Intelligence::rest_storage_ready()` → 11 DB-dependent Future REST endpoints, guarded by runtime + physically verified schema and sanitized 503 → `tests/future-intelligence-tests.php`. Public File14 base page remains readable when Future storage is unavailable.
- F14-FUT-21 and REST abuse/cost controls → `rest_ai_copy()` + `GCU_Repository::consume_rate_limit('future-ai-copy',12)` → provider calls limited per actor per minute and fail closed on DB/rate issue; draft-only/semantic review unchanged.
- F14-FR-011, F14-FUT-11/13/14 → R9-G01, native owner-authenticated File07/08/09 lifecycle confirmations and File01 registration still unimplemented; Issue #19 is open. Do not equate File14 CTA with booking/application success.
- Twenty independent code/plan source reviews recorded in `docs/REVIEW-20-CROSS-FILE-COMPLETION-v1.4.13-R9.md`; verified deployed artifact/runtime is a separate truth.


## R10 plan-to-code provenance (2026-10-11)

- F14-FUT-20 and central Founder-approved source meaning: GCU_Future_Intelligence::filter_public_route_html scopes the generated Future markup, GCU_Future_I18n::localize_fragment applies exact visible en-US/ur-PK/ar-SA text-node translations only; R10 fixture protects canonical owner copy/attributes.
- F14-FUT-24 / low-data/accessibility: readiness controls start hidden and JS reveals only after listeners registered; static translated fallback stays visible if JS is absent. Script intentionally suppressed in reduced-data mode; no bogus readiness calculation.
- F14-FUT-17 safe baseline restore is not proven by a stopped status: Issue #22 requires approved snapshot/placement state and rollback design before operational acceptance.
- F14-FR-011 and F14-FUT-11/13/14 owner outcome proofs remain Issue #19; self-reported File14 clicks are not appointment/application success.
- Twenty source audit checks and limitations: docs/REVIEW-20-CROSS-FILE-COMPLETION-v1.4.14-R10.md.


## R11 requirement-to-code and governance safety (2026-10-11)

- F14-FUT-12 — Misleading-Copy Report/Correction Loop: `GCU_Future_Intelligence::report_replay_matches` binds public report UUID idempotency to stored normalized content/actor/route/reason; conflicting UUID returns 409, no false complaint-success or disclosure, current review status preserved; `tests/future-intelligence-tests.php`.
- F14-FUT-20/22 — Founder-controlled terminology/change-log records: `seed_record_if_absent` preserves existing reviewed record content, version, visibility and lifecycle during repair; `ensure_schema` fails closed if seeding failed. No silent overwrite or invented restored schema.
- F14-FR-011 and Future CTI11/13/14 real File07/08/09 owner-attested conversion outcomes remain incomplete under Issue #19; File01 registration and staging are mandatory.
- F14-FUT-17 safe approved experiment baseline restore remains incomplete under Issue #22; do not substitute 'stopped' status for rollback proof.
- R11 per-round evidence `docs/REVIEW-20-CROSS-FILE-COMPLETION-v1.4.15-R11.md`; versioned source QA does not establish deployed artifact parity.


## R12 plan-to-code safety trace (2026-10-11)

- **F14-FUT-24**: `GCU_Future_Policy::doctor_readiness_check` counts only strict boolean/checkbox-confirmed state (true/1/"1"); public JSON "false", "no", arbitrary arrays and text must not inflate a self-assessed score. File09/File00 remain the only verified doctor identity/activation owners; `tests/future-intelligence-tests.php`.
- **F14-FUT-20**: `validate_public_record_payload('terminology_lock')` requires every canonical protected `terminology_lock()` concept key and en-US/ur-PK/ar-SA values. Founder publication approval and actual word-choice review remain distinct. A three-language **subset** can no longer become authoritative.
- **F14-FR-011/F14-FUT-11/13/14**: Issue #19 owner-origin verified conversion events still pending. **F14-FUT-17**: Issue #22 safe experiment baseline restoration pending.
- Source audit ledger: `docs/REVIEW-20-CROSS-FILE-COMPLETION-v1.4.16-R12.md`. GitHub QA is not deployed parity.


## R13 handoff input and jurisdiction fallback requirements (2026-10-11)
- **F14-FUT-02**: `GCU_Future_Policy::sanitize_handoff_context` only accepts string values for country/language/mode; untrusted nested JSON/array/object context is discarded, never cast into a guessed canonical journey. `GCU_Future_Intelligence::sanitize_region` handles non-scalar GET parameters fail-closed.
- **F14-FUT-06**: `GCU_Future_Intelligence::jurisdiction_disclosure` tries only ordered active/public unexpired region+generic `ZZ` records, each requiring source/reviewer/provenance/valid effective date. Invalid legacy regional record does not hide an otherwise valid generic. No new legal eligibility decision.
- Native File07/08/09 owner event attestation still unimplemented under Issue #19; approved experiment baseline restoration still unimplemented under Issue #22.
- Per-pass source evidence: `docs/REVIEW-20-CROSS-FILE-COMPLETION-v1.4.17-R13.md`. Installed/lived code parity separate.


## R14 — exact-source requirement/code evidence (2026-10-11)

- **F14-FUT-01**: `sanitize_explicit_intent` accepts only explicit patient/doctor/learn string choices; rejects arrays/objects/boolean/non-approved names before use as journey selection. Called by REST `rest_handoff`. No inferred medical intent.
- **F14-FUT-22**: Returned `records()` rows decode JSON payloads in the actual returned row array (R14-D03, discovered by PHP regression), not in a discarded temporary expression. Public trust & policy change-log selection validates effective date, source, reviewer and provenance for records **before** applying requested display limit. `records` supports sanitized offset, stable order and bounded batches; 1,000-candidate scan is not an exhaustive historical-archive guarantee. No unapproved text is made public.
- **F14-FR-011**: File07/08/09 native owner stage events still awaiting File01-vetted contracts (Issue #19). **F14-FUT-17**: approved safe baseline restore pending (Issue #22).
- R14 twenty-round source evidence: `docs/REVIEW-20-CROSS-FILE-COMPLETION-v1.4.18-R14.md`. Repository main is not deployed runtime truth.


## R15 plan-to-code input-boundary safety (2026-10-11)
- Future governance record REST: `GCU_Future_Policy::validate_future_record_input_types` enforces named string fields, an array payload, a nonnegative integer expected version and explicit boolean public visibility before `rest_record_write` mutation, including Founder/editor capability checks and idempotent command.
- Canonical claim revalidation: `validate_revalidation_input_types` requires string claim/reason and a positive row version before `rest_revalidate_claim` queues a governed mutation. Nested JSON cannot be coerced into string provenance.
- Ethical experiment preflight: `GCU_Future_Policy::experiment_preflight` marks unsupported variant shapes and nested sampling/privacy text as unsafe, not as a falsely approved experiment.
- **F14-FR-011** owner-outcome confirmation remains Issue #19 pending; **F14-FUT-17** safe baseline placement/variant restore remains Issue #22 pending.
- R15 twenty-source-pass record: `docs/REVIEW-20-CROSS-FILE-COMPLETION-v1.4.19-R15.md`. Source QA ≠ actual deployed runtime.


## R16 plan-to-code safety evidence (2026-10-11)
- **F14-FUT-16:** `GCU_Future_Policy::experiment_preflight` verifies guardrail values, not just keys. Explicit disabled/malformed privacy, accessibility, error, claim-integrity and complaints controls fail closed. Founder-approved quantitative thresholds and experiment activation remain separate gates.
- **F14-FUT-16:** `copy_preflight` requires scalar string title/body/CTA in both candidate and comparison record; nested JSON cannot become semantic approval copy.
- **F14-FUT-11:** `quality_evidence_status` only counts measured numeric accessibility/performance within 0..100, while requiring native owner attestation and cohort size. Do not report unverified click/owner events as confirmed quality.
- **F14-FR-011** signed owner-outcome Issue #19 and **F14-FUT-17** approved experiment baseline restore Issue #22 are still incomplete.
- Detailed independent 20-pass source ledger: `docs/REVIEW-20-CROSS-FILE-COMPLETION-v1.4.20-R16.md`; deployed code and live database separate.


## R17 2026-10-11 plan-to-code source evidence
- **F14-FUT-21**: `GCU_Future_Policy::valid_ai_copy_inputs` and `normalize_ai_candidate_text` protect against non-string base/objective and malformed provider candidate types. Existing source-grounding and no auto-publication controls remain mandatory.
- **F14-FUT-11**: `valid_quality_percentage` is required for privacy, accessibility and performance measured fields and verified flags; invalid measurements imply provisional/insufficient verified evidence.
- **F14-FUT-18/15**: `valid_faq_aggregate_signal` requires nonnegative integral count, textual question and minimum sample; never promote a negative count via `absint`; FAQ suggestions remain non-publishing and aggregate-only.
- Owner-native conversion Issue #19 and approved experiment baseline restoration Issue #22 still incomplete. Twenty source lenses in `docs/REVIEW-20-CROSS-FILE-COMPLETION-v1.4.21-R17.md`; deployed state not implied.


## R18 plan-to-code typed REST GET query boundary (2026-10-11)
- **F14-FUT-03/04:** public trust lookup `safe_public_claim_key` validates route/query precedence shapes before claim query; malformed nested/uppercase/unknown-shape value returns controlled 400, not PHP string coercion. Actual public approved/fresh claim eligibility remains repository owner truth.
- **F14-FUT-22:** `validate_future_read_query` verifies admin `records` and `reports` list filters/status, cursor and positive bounded limit before SQL. Negative/array/object/unsupported values cannot map silently to other content. Existing capability and DTO constraints remain.
- **F14-FUT-13/15:** `bounded_query_integer` enforces privacy-safe analytics friction day-window parsing (default 30, bounded 1..90), while minimum cohort and owner-event evidence suppression remain unchanged.
- Native File07/08/09 owner-event Issue #19 and approved experiment safe restore Issue #22 remain incomplete. Full ledger `docs/REVIEW-20-CROSS-FILE-COMPLETION-v1.4.22-R18.md`. GitHub `main`/CI never establishes deployed parity.
