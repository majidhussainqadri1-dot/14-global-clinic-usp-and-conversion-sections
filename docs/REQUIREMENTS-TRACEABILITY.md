# File 14 — Fresh Central + File Plan + Future CTI Requirements Traceability — v1.4.6

## Governing freeze

This matrix is the current File 14 traceability baseline for the 2026-08-10 governing plans plus the 2026-08-11 eighth ten-round corrective cycle. It treats the consolidated central governing plan, `SSH-F14-PLAN-2026-v1.0`, and the Founder-approved additive amendment `SSH-F14-FUTURE-CTI-2026-v2.0` as the governing specification. Repository evidence is implementation evidence only; staging/live/operational status remains separate.

Precedence used by this release: definitive Islamic rule → latest explicit Founder directive/amendment → consolidated central governing plan → fresh File 14 plan → Future CTI amendment → verified exact repository head/runtime evidence.

The v1.4.5 trace remains historical evidence. The v1.4.6 delta closes concurrent event replay identity, full request idempotency fingerprinting, privacy-subject initialization races, owner-event ordering/freshness, noncanonical File 20 slot authority, fallback-manufactured destination readiness, analytics DB silent failure, and destination-less destination-bound funnel events. Base schema remains `10005`; Future schema remains `1`.

## Canonical ownership

File 14 owns approved Worldwide Clinic value-proposition copy, reusable semantic content blocks, File 20 placement contracts, ethical patient/doctor conversion journeys, claim evidence/version history, destination health, trust/copy intelligence and privacy-minimized measurement. It does **not** own doctor/profile/verification/clinic/appointment/payment/clinical records, the global shell/navigation, or visual-system truth. File 20 is the sole shell/slot-readiness owner; Files 07/08/09 remain canonical destination owners. Future CTI may explain, lint, measure, preview or hand off to canonical owners; it may not silently become one.

## Fresh central requirements

| Governing ID | File 14 implementation evidence | Automated evidence |
|---|---|---|
| CEN-GOV-001 | Runtime records `GCU_PLAN_VERSION`, `GCU_FUTURE_PLAN_VERSION`, `GCU_CENTRAL_PLAN_BASELINE`; status model separates repository/staging/live | central-plan + eighth-review tests |
| CEN-OWN-001 | Files 07/08/09 provide destination truth; File 20 canonical `sabri_shell_slot_ready_v1` only; File 00 authorization explicit-grant fail closed | contract + central + eighth tests |
| CEN-BIZ-001 | One approved free tier; parity sentinel blocks drift | policy + future tests |
| CEN-DON-001 | Voluntary support cannot purchase ranking, visibility, verification or basic service | policy + future tests |
| CEN-BRAND-001 | Exact Sabri Green `#087A4E` | central-plan + CSS/fresh tests |
| CEN-NAV-001 | File 20 sole shell/navigation owner; no local Back/Home fallback | contract + central + fresh tests |
| CEN-LOC-001 | American English canonical chrome with complete Urdu/Arabic key parity | central + future tests |
| CEN-A11Y-001 | Semantic headings, 44px targets, focus-visible, RTL, reduced motion, forced colors, 320px-class reflow | CSS/static; staging human evidence pending |
| CEN-LOWDATA-001 | `Save-Data`/reduced-data suppress nonessential measurement | central + fresh tests |
| CEN-PRIV-001 | Consent/GPC, bounded attribution, cohort suppression, export/erase, sensitive AI-input rejection, serialized/read-back-verified logged-in pseudonym initialization | contract + central + eighth tests |

## File 14 functional requirements

| Requirement | Implementation evidence | Automated evidence |
|---|---|---|
| F14-FR-001 Patient value proposition | Localized patient blocks and verified-directory destination | policy/contract tests |
| F14-FR-002 Doctor value proposition | Localized doctor blocks, zero-commission/free/support rules and onboarding destination | policy/contract tests |
| F14-FR-003 Primary CTAs | Files 07/09 owner destination registry; consumer cannot rewrite owner URL; owner availability requires safe owner-confirmed URL; monotonic owner occurrence time | contract/eighth tests |
| F14-FR-004 How it works | Patient/doctor steps and canonical process route | contract tests |
| F14-FR-005 Trust content | Verification, privacy, emergency and no-outcome-guarantee claims | policy/contract tests |
| F14-FR-006 Business copy | 0% commission, one free tier, optional support/no advantage, fee-owner boundary | policy + future parity tests |
| F14-FR-007 Placement registry | Versioned File 20 route/slot/audience/priority placements; only canonical File 20 readiness hook can authorize activation | schema/contract/eighth tests |
| F14-FR-008 Reusable content blocks | Semantic block DTO/API and File25-compatible classes/tokens | contract tests |
| F14-FR-009 Destination health | Owner occurrence-time freshness, owner-confirmed URL, fail-closed/degraded state | contract/eighth tests |
| F14-FR-010 Campaign attribution | Consent, GPC, File14 route allowlist, sanitized first/last attribution, 30-day expiry | privacy/static tests |
| F14-FR-011 Funnel events | Single-use token; destination-bound stages require destination; replay identity checked before write and after deduplication; conflicting reuse returns 409; analytics DB failure returns safe 503 | REST/repository/eighth tests |
| F14-FR-012 Experiment governance | Guardrails, Founder-gated lifecycle, transactional scheduled early-stop state+audit | policy/future/fifth tests |
| F14-FR-013 Localization | en-US canonical, ur-PK/ar-SA, RTL and parity health | contract/central tests |
| F14-FR-014 FAQ governance | Versioned FAQs, claim links and review deadlines | seed/contract tests |
| F14-FR-015 Accessibility | Unique IDs, semantic sections, 44px targets, focus, RTL/LTR, no inline script, reduced motion/data, responsive reflow | contract/central/fresh; staging pending |
| F14-FR-016 Claim audit | Basis/owner/source/effective/review/expiry/history and withdrawal event | repository/contract tests |

## Future Conversion & Trust Intelligence requirements

| Requirement | Founder-approved capability | Implementation evidence | Automated evidence |
|---|---|---|---|
| F14-FUT-01 | Ethical Intent Router | Explicit patient/doctor/learn choices; no hidden intent inference | future + fresh |
| F14-FUT-02 | Smart Destination Handoff | Allowlisted context only to canonical owner URLs after strict same-origin validation | future + contract |
| F14-FUT-03 | Trust Evidence Drawer | Public current claims expose basis/owner/effective/review data | future/static |
| F14-FUT-04 | Claim Freshness Sentinel | Stale/expired claim transition, public hide, history/audit, privileged revalidation | future/fresh |
| F14-FUT-05 | Zero-Commission Parity Sentinel | 0%/free/support parity and activation blocking on drift | future/workflow |
| F14-FUT-06 | Jurisdiction-Aware Truthful Copy Engine | Governed disclosures; no eligibility/clinical decision; Founder gate for active/public | future/fifth |
| F14-FUT-07 | Semantic Copy-Diff and Meaning Risk Detector | Protected concepts for commission/free/support/verification/emergency/outcome/payment | future |
| F14-FUT-08 | Dark-Pattern Linter | Fake scarcity/guarantee/paid visibility/hidden fee/coercive consent/shame patterns | future |
| F14-FUT-09 | Destination Failover Matrix | Truthful alternatives only; cannot manufacture availability | future/eighth |
| F14-FUT-10 | Scenario Preview Laboratory | Role/locale/GPC/low-data/reduced-motion/320px/400%/dependency/safe-mode preview | future/fresh |
| F14-FUT-11 | Conversion Quality Score | Weighted quality; provisional without measured evidence | future |
| F14-FUT-12 | Misleading-Copy Report & Correction Loop | Rate-limited, sensitive-data rejected, hashed actor, audited resolution | future |
| F14-FUT-13 | Privacy-Safe Friction Analytics | Aggregate stage/drop-off; no patient/health profiling; DB failure not treated as empty truth | future/eighth |
| F14-FUT-14 | Conversion Anomaly Detector | Minimum-sample baseline comparison | future |
| F14-FUT-15 | Small-Cohort Privacy Guard | Minimum cohort 10 | future |
| F14-FUT-16 | Experiment Safety Preflight Simulator | Claim/privacy/a11y/error/complaint guardrails; sensitive profiling forbidden | future |
| F14-FUT-17 | Experiment Early-Stop & Rollback Guard | Automatic stop; state+mandatory audit atomic | future/fifth |
| F14-FUT-18 | FAQ Gap Intelligence | Approved aggregate source; suggestions only; never auto-publish | future |
| F14-FUT-19 | Message Consistency Graph | Locale/claim consistency by semantic block identity | future |
| F14-FUT-20 | Translation Provenance & Terminology Lock | Versioned en-US/ur-PK/ar-SA terminology | future |
| F14-FUT-21 | AI Ethical Copy Assistant | Non-sensitive bounded input, approved-claim bounded output, multilingual dark-pattern guard, no auto-publish | future/fifth |
| F14-FUT-22 | Public Clinic Trust & Change Log | Material policy/change records with review lifecycle and Founder gate | future/fifth |
| F14-FUT-23 | Patient Choose-Safely Decision Guide | Educational checklist; owners remain source of doctor/booking truth | future |
| F14-FUT-24 | Doctor Global Clinic Readiness Self-Check | Non-binding preparation; File09/File00 verification owners | future/local JS |

## Non-functional requirements

| Requirement | Repository implementation evidence | Remaining external evidence |
|---|---|---|
| F14-NFR-001 Authorization | Native capabilities + explicit File00 grant, object/purpose/state/version gates | real-role/IDOR staging |
| F14-NFR-002 Privacy lifecycle | Consent/GPC, minimization, export/erase, bounded attribution, pseudonym lock/read-back, cohort suppression, non-destructive uninstall | real privacy-tool staging |
| F14-NFR-003 Reliability | Full payload idempotency fingerprint, replay identity post-dedup check, monotonic owner events, outbox/inbox/retry/dead-letter, schema fail-close | queue/provider-failure staging |
| F14-NFR-004 Performance | Bounded queries/assets/background work; measured evidence remains external | p75/p95 staging |
| F14-NFR-005 Accessibility | File20 shell contract, lang/dir, focus, reflow, reduced motion/data, forced colors | screen reader/400% staging |
| F14-NFR-006 Observability | Health, parity/anomaly/quality/report indicators; DB analytics query fails closed | real alert/log acceptance |
| F14-NFR-007 Migration/rollback | Install lock, dbDelta, snapshots, verified base/Future schema | fresh install/upgrade/restore drill |
| F14-NFR-008 Operability | System Check, safe mode, Future admin/scenario lab, retention/reconciliation | operator rehearsal |
| F14-NFR-009 Compatibility | PHP 7.4/8.3 and WordPress 6.6+ metadata | WordPress 7.0.1/PHP8.3 staging |
| F14-NFR-010 Localization | File14-owned en-US/ur-PK/ar-SA + terminology lock and multilingual AI guard | human locale sign-off |

## Definition of Done mapping

- DoD-08 — user states, 320–1920px, RTL/LTR, keyboard/focus, zoom, contrast, reduced motion: repository controls coded; human staging mandatory.
- DoD-09 — performance/load/provider outage/queue/dead-letter/cache/DB failure and measured SLO: code paths exist; measured staging mandatory.
- DoD-10 — verified backup restore/cache/index rebuild/rollback: tooling exists; real drill mandatory.
- DoD-11 — two separate fresh review/fix rounds after final coding change; affected regression suite must pass exact current head.
- DoD-12 — staging real-role journeys, Founder acceptance, production monitoring/rollback window: external release gates.
- DoD-13 — zero known unresolved blocker/critical defect at repository gate; residual external risks documented.

## Truth-status rule

`Specified`, `Coded`, `Packaged`, `Automated-QA Green`, `Staging-Accepted`, `Live-Deployed`, and `Operational` are separate statuses. File 14 v1.4.6 may only claim a status for which current exact-head evidence exists. The Future CTI implementation and eighth ten-round corrections do not alter that rule.
