# File 14 — Fresh Central + File Plan + Future CTI Requirements Traceability — v1.4.4

## Governing freeze

This matrix is for the 2026-08-10 corrective and Future Conversion & Trust Intelligence coding pass. It treats the freshly supplied consolidated central governing plan, `SSH-F14-PLAN-2026-v1.0`, and the Founder-approved additive amendment `SSH-F14-FUTURE-CTI-2026-v2.0` as the governing specification. Repository evidence is implementation evidence only; staging/live/operational status remains separate.

Precedence used by this release: definitive Islamic rule → latest explicit Founder directive/amendment → consolidated central governing plan → fresh File 14 plan → Future CTI amendment → verified exact repository head/runtime evidence.

## Canonical ownership

File 14 owns approved Worldwide Clinic value-proposition copy, reusable semantic content blocks, File 20 placement contracts, ethical patient/doctor conversion journeys, claim evidence/version history, destination health, trust/copy intelligence and privacy-minimized measurement. It does **not** own doctor/profile/verification/clinic/appointment/payment/clinical records, the global shell/navigation, or visual-system truth. Future CTI may explain, lint, measure, preview or hand off to canonical owners; it may not silently become one.

## Fresh central requirements

| Governing ID | File 14 implementation evidence | Automated evidence |
|---|---|---|
| CEN-GOV-001 | Runtime records `GCU_PLAN_VERSION`, `GCU_FUTURE_PLAN_VERSION` and `GCU_CENTRAL_PLAN_BASELINE`; status model keeps coding separate from staging/live | `central-plan-tests.php`, future/fifth-review tests |
| CEN-OWN-001 | All doctor/onboarding/booking truth resolved through File 07/08/09 contracts; Future CTI stores only File14-owned governance records/reports | contract + central-plan + future/fifth tests |
| CEN-BIZ-001 | Canonical policy is one approved free tier; parity sentinel blocks drift | policy + future tests |
| CEN-DON-001 | Voluntary support is separate and cannot purchase ranking, visibility, verification or basic service; parity/dark-pattern rules reinforce this | policy + future tests |
| CEN-BRAND-001 | Exact primary Sabri Green `#087A4E`; future CSS consumes same token/fallback | central-plan + CSS/fresh-review tests |
| CEN-NAV-001 | File 20 remains sole global shell/navigation owner; Future CTI injects only semantic module sections and File 14 emits no local Back/Home shell fallback | contract + central-plan + fresh-review tests |
| CEN-LOC-001 | American English canonical File 14 chrome with complete Urdu and Arabic key parity; future protected terminology locks en-US/ur-PK/ar-SA | contract + central-plan + future tests |
| CEN-A11Y-001 | Semantic headings, 44px targets, focus-visible, RTL, reduced motion, forced colors, 320px-class reflow; Future CTI follows same gates | CSS/static tests; browser staging pending |
| CEN-LOWDATA-001 | `Save-Data` and reduced-data support; Future CTI JS is not loaded in low-bandwidth mode | central-plan + fresh-review tests |
| CEN-PRIV-001 | Consent/GPC, bounded attribution, small-cohort suppression, aggregate FAQ signals, PII-minimized copy reports; fifth review rejects sensitive AI-copy input before provider use | contract + central-plan + future/fifth tests |

## File 14 functional requirements

| Requirement | Implementation evidence | Automated evidence |
|---|---|---|
| F14-FR-001 Patient value proposition | Localized patient blocks and verified-directory destination | policy/contract tests |
| F14-FR-002 Doctor value proposition | Localized doctor blocks, zero-commission/free/support rules and onboarding destination | policy/contract tests |
| F14-FR-003 Primary CTAs | File 07 and File 09 versioned destination registry with safe redirects | contract tests |
| F14-FR-004 How it works | Patient/doctor step journeys and canonical process route | contract tests |
| F14-FR-005 Trust content | Verification limits, privacy, emergency and no-outcome-guarantee claims | policy/contract tests |
| F14-FR-006 Business copy | 0% commission, one free tier, optional support/no advantage, direct fee-owner boundary | policy + future parity tests |
| F14-FR-007 Placement registry | Versioned File 20 route/slot/audience/priority placements | schema/contract tests |
| F14-FR-008 Reusable content blocks | Semantic block DTO/API and File 25-compatible classes/tokens | contract tests |
| F14-FR-009 Destination health | Fail-closed owner readiness and honest degraded states | contract/frontend tests |
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
| F14-NFR-001 Authorization | Native capabilities, object/purpose/state/version checks and fail-closed REST; fifth review adds Founder approval for active/public Future governance | real-role/IDOR staging matrix |
| F14-NFR-002 Privacy lifecycle | Consent/GPC, minimization, export/erase, bounded attribution, non-destructive uninstall, cohort suppression, PII-minimized reports and sensitive AI-input rejection | WordPress privacy-tool/provider staging acceptance |
| F14-NFR-003 Reliability | Idempotent event write, outbox/inbox, bounded retry/dead-letter; base/Future actual schema fail-close; transactional scheduled experiment early-stop | queue/provider-failure staging test |
| F14-NFR-004 Performance | Bounded queries, conditional assets, low-data suppression and background work; Future quality marks performance provisional without measured input | measured p75/p95 on Hostinger-equivalent staging |
| F14-NFR-005 Accessibility | File 20 shell contract only, lang/dir, focus, 320px reflow, reduced motion/data, forced colors; Future UI has 44px/focus/reflow gates | screen reader, 320–1920px and 400% zoom acceptance |
| F14-NFR-006 Observability | Base health plus Future parity/anomaly/quality/consistency/report indicators and conversion event identity conflict warning | real logging/alert ownership acceptance |
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

`Specified`, `Coded`, `Packaged`, `Automated-QA Green`, `Staging-Accepted`, `Live-Deployed`, and `Operational` are separate statuses. File 14 v1.4.4 may only claim a status for which current exact-head evidence exists. The Future CTI implementation and sixth-review corrections do not alter that rule.
