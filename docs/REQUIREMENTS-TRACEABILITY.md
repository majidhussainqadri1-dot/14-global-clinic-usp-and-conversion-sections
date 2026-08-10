# File 14 — Fresh Central + File Plan Requirements Traceability — v1.3.0

## Governing freeze

This matrix is for the 2026-08-10 corrective coding pass. It treats the freshly supplied consolidated central governing plan and `SSH-F14-PLAN-2026-v1.0` as the governing specification. Repository evidence is implementation evidence only; staging/live/operational status remains separate.

Precedence used by this release: definitive Islamic rule → latest explicit Founder directive/amendment → consolidated central governing plan → fresh File 14 plan → verified exact repository head/runtime evidence.

## Canonical ownership

File 14 owns approved Worldwide Clinic value-proposition copy, reusable semantic content blocks, File 20 placement contracts, ethical patient/doctor conversion journeys, claim evidence/version history, destination health and privacy-minimized measurement. It does **not** own doctor/profile/verification/clinic/appointment/payment/clinical records, the global shell/navigation, or visual-system truth.

## Fresh central requirements

| Governing ID | File 14 implementation evidence | Automated evidence |
|---|---|---|
| CEN-GOV-001 | Runtime records `GCU_PLAN_VERSION` and `GCU_CENTRAL_PLAN_BASELINE`; status model keeps coding separate from staging/live | `central-plan-tests.php` |
| CEN-OWN-001 | All doctor/onboarding/booking truth resolved through File 07/08/09 contracts; no WordPress page or companion-domain creation | contract + central-plan tests |
| CEN-BIZ-001 | Canonical policy is one approved free tier; File 14 does not create a paid tier or entitlement | policy + forbidden-copy tests |
| CEN-DON-001 | Voluntary support is separate and cannot purchase ranking, visibility, verification or basic service | policy tests |
| CEN-BRAND-001 | Exact primary Sabri Green `#087A4E`; orange is not a primary token | central-plan + CSS tests |
| CEN-NAV-001 | File 20 remains sole global shell/navigation owner through `sabri_shell_back_home_controls`; local fallback is bounded, accessible and script-free; RTL/LTR directional icons are logical | contract + central-plan tests |
| CEN-LOC-001 | American English canonical File 14 chrome with complete Urdu and Arabic key parity; partial/mixed File 14 chrome is a health/release failure | contract + central-plan tests |
| CEN-A11Y-001 | Semantic headings, unique IDs, 44px targets, focus-visible, RTL, reduced motion, forced colors, 320px-class reflow and zoom-safe fluid layout | CSS/static tests; browser staging pending |
| CEN-LOWDATA-001 | `Save-Data` and `prefers-reduced-data` support; nonessential measurement is suppressed for low-data clients | central-plan tests |
| CEN-PRIV-001 | Consent plus Global Privacy Control; acquisition attribution is restricted to File 14 public acquisition routes and remains bounded/signed | contract + central-plan tests |

## File 14 functional requirements

| Requirement | Implementation evidence | Automated evidence |
|---|---|---|
| F14-FR-001 Patient value proposition | Localized patient blocks and verified-directory destination | policy/contract tests |
| F14-FR-002 Doctor value proposition | Localized doctor blocks, zero-commission/free/support rules and onboarding destination | policy/contract tests |
| F14-FR-003 Primary CTAs | File 07 and File 09 versioned destination registry with safe redirects | contract tests |
| F14-FR-004 How it works | Patient/doctor step journeys and canonical process route | contract tests |
| F14-FR-005 Trust content | Verification limits, privacy, emergency and no-outcome-guarantee claims | policy/contract tests |
| F14-FR-006 Business copy | 0% commission, one free tier, optional support/no advantage, direct fee-owner boundary | policy + forbidden-copy tests |
| F14-FR-007 Placement registry | Versioned File 20 route/slot/audience/priority placements | schema/contract tests |
| F14-FR-008 Reusable content blocks | Semantic block DTO/API and File 25-compatible classes/tokens | contract tests |
| F14-FR-009 Destination health | Fail-closed owner readiness and honest degraded states | contract/frontend tests |
| F14-FR-010 Campaign attribution | Consent, GPC, File 14 route allowlist, sanitized first/last attribution and 30-day expiry | privacy/static tests |
| F14-FR-011 Funnel events | Impression/CTA stages, single-use event token, no click-as-success inference | REST/repository tests |
| F14-FR-012 Experiment governance | Hypothesis/variants/audience/guardrails/sample/privacy and Founder-gated lifecycle | policy/contract tests |
| F14-FR-013 Localization | en-US canonical, complete ur-PK/ar-SA File 14 chrome, RTL and runtime parity health | contract/central-plan tests |
| F14-FR-014 FAQ governance | Versioned FAQs, claim links and review deadlines | seed/contract tests |
| F14-FR-015 Accessibility | Unique IDs, semantic sections, 44px targets, focus, RTL/LTR, no inline script, reduced motion/data, responsive reflow | contract/central-plan tests; human staging pending |
| F14-FR-016 Claim audit | Basis/owner/source/effective/review/expiry/history and withdrawal event | repository/contract tests |

## Non-functional requirements

| Requirement | Repository implementation evidence | Remaining external evidence |
|---|---|---|
| F14-NFR-001 Authorization | Native capabilities, object/purpose/state/version checks and fail-closed REST | real-role/IDOR staging matrix |
| F14-NFR-002 Privacy lifecycle | Consent/GPC, minimization, export/erase, bounded attribution, non-destructive uninstall | WordPress privacy-tool staging acceptance |
| F14-NFR-003 Reliability | Idempotent event write, outbox/inbox, bounded retry/dead-letter and replay checks | queue/provider-failure staging test |
| F14-NFR-004 Performance | Bounded queries, conditional assets, low-data suppression and background work | measured p75/p95 on Hostinger-equivalent staging |
| F14-NFR-005 Accessibility | Script-free fallback nav, lang/dir, focus, 320px reflow, reduced motion/data and forced colors | screen reader, 320–1920px and 400% zoom acceptance |
| F14-NFR-006 Observability | Health report includes queues, stale claims, destination state, brand baseline and localization parity | real logging/alert ownership acceptance |
| F14-NFR-007 Migration/rollback | Install lock, dbDelta, owner-scoped snapshots and safe repair | fresh install/upgrade/concurrency/restore drill |
| F14-NFR-008 Operability | System Check, safe mode, repair, retention and reconciliation | operator runbook rehearsal |
| F14-NFR-009 Compatibility | PHP 7.4/8.3 matrix, WordPress 6.6+ metadata and versioned contracts | WordPress 7.0.1/PHP 8.3 staging |
| F14-NFR-010 Localization | Complete File 14-owned en-US/ur-PK/ar-SA chrome with parity health gate | human locale QA and translation sign-off |

## Definition of Done mapping

- DoD-08 — all user states, 320–1920px, RTL/LTR, keyboard/focus, zoom, contrast and reduced motion: repository controls are coded; human staging remains mandatory.
- DoD-09 — performance/load/provider outage/queue/dead-letter/cache/DB failure and measured SLO: failure-path code exists; measured staging evidence remains mandatory.
- DoD-10 — verified backup restore/cache rebuild/rollback: tooling exists; real restore/rollback drill remains mandatory.
- DoD-11 — two separate fresh review/fix rounds after final coding change: recorded in `docs/REVIEW-CENTRAL-PLAN-FINAL-1.md` and `docs/REVIEW-CENTRAL-PLAN-FINAL-2.md`; exact-head regression must still be green.
- DoD-12 — real-role staging, Founder acceptance, production monitoring/rollback window: external release gates; not claimed by repository completion.
- DoD-13 — zero known unresolved blocker/critical defects at repository gate; residual external risks remain documented in release evidence.

## Truth-status rule

`Specified`, `Coded`, `Packaged`, `Automated-QA Green`, `Staging-Accepted`, `Live-Deployed`, and `Operational` are separate statuses. This repository may only claim a status for which current exact-head evidence exists.
