# File14 V12.3 — R75/R76 forty-scope focused source ledger

Date: 2026-11-15. Source candidate **v1.4.76**; based on File14 original main `d1cd343392d07607c60f0688362cee450e31774a` (v1.4.75), with File00 main `1fad051c86d530d558be3eb1890d3c6e67bce6e8` (v1.2.44) as canonical membership status/subject truth. This is a **plan/source traceability ledger**, not forty real WordPress staging runs. Source CI must be checked on the final exact commit.

## Proven change boundaries before editing

- R75 source defect: `GCU_Companion_Adapters::file00_assertions` coerced caller-supplied subject via `absint()`, potentially rewriting invalid scalar into an unrelated existing subject before native File00 read. File00 `SMC_Contracts::assertions` returns exact integer user IDs and WordPress current user ID is canonical integer. Correction: deny invalid original types and non-positive IDs.
- R76 source defect: `GCU_Companion_Adapters::authorize` relied on an incomplete blocked-status list and a lossy `sanitize_key()` of the source membership status; unrecognized status combined with malformed `eligible=true` could pass. File00 `smc_membership_state()` returns `approved` (member) and `verified` (institutional) for eligible subjects. Correction: exact allowed-status check, no lossy normalization.
- R75 has 40 direct invalid original-subject cases and a native integer positive control; R76 has 40 unknown/malformed status cases and canonical approved/verified controls. Both run in each 40-round source review and standard PHP quality.
- No owner foreign table, clinical record, user account, File20 placement or File08 booking write was performed. These are repository-source patches only.

## All sixteen V12.3 functional requirements

| ID | Governed contract | Source/release evidence boundary |
|---|---|---|
| F14-FR-001 | Patient value proposition | Existing File14 source requires independent exact-head verification |
| F14-FR-002 | Doctor value proposition | Existing File14 source requires independent exact-head verification |
| F14-FR-003 | Primary canonical CTAs | Existing File14 source requires independent exact-head verification |
| F14-FR-004 | How-it-works owner journeys | Existing File14 source requires independent exact-head verification |
| F14-FR-005 | Verification, privacy and emergency trust content | Existing File14 source requires independent exact-head verification |
| F14-FR-006 | Zero commission / support-neutral business copy | Existing File14 source requires independent exact-head verification |
| F14-FR-007 | File20 approved placement registry | Existing File14 source requires independent exact-head verification |
| F14-FR-008 | File25 reusable canonical semantic blocks | Existing File14 source requires independent exact-head verification |
| F14-FR-009 | File07/08/09 destination health | Existing File14 source requires independent exact-head verification |
| F14-FR-010 | Consent-limited campaign attribution | Existing File14 source requires independent exact-head verification |
| F14-FR-011 | True owner-verified funnel stages | Cross-owner proof **unaccepted**; fail-closed |
| F14-FR-012 | Governed founder-approved experiments | Existing File14 source requires independent exact-head verification |
| F14-FR-013 | en-US/Urdu/Arabic/RTL localizations | Existing File14 source requires independent exact-head verification |
| F14-FR-014 | Approved FAQ lifecycle | Existing File14 source requires independent exact-head verification |
| F14-FR-015 | Accessible keyboard/mobile interaction | Existing File14 source requires independent exact-head verification |
| F14-FR-016 | Claim source, owner, effective/expiry audit | Existing File14 source requires independent exact-head verification |

## Twenty-four V12.3 governed Future requirements

| ID | Governed contract | Source/release evidence boundary |
|---|---|---|
| F14-FUT-01 | Ethical Intent Router | Existing File14 source requires independent exact-head verification |
| F14-FUT-02 | Smart Destination Handoff | Existing File14 source requires independent exact-head verification |
| F14-FUT-03 | Trust Evidence Drawer | Existing File14 source requires independent exact-head verification |
| F14-FUT-04 | Claim Freshness Sentinel | Existing File14 source requires independent exact-head verification |
| F14-FUT-05 | Zero-Commission Parity Sentinel | Existing File14 source requires independent exact-head verification |
| F14-FUT-06 | Jurisdiction-Aware Truthful Copy | Existing File14 source requires independent exact-head verification |
| F14-FUT-07 | Semantic Copy Meaning Diff | Existing File14 source requires independent exact-head verification |
| F14-FUT-08 | Dark-Pattern Linter | Existing File14 source requires independent exact-head verification |
| F14-FUT-09 | Destination Failover Matrix | Existing File14 source requires independent exact-head verification |
| F14-FUT-10 | Scenario Preview Laboratory | Existing File14 source requires independent exact-head verification |
| F14-FUT-11 | Conversion Quality Score | Cross-owner proof **unaccepted**; fail-closed |
| F14-FUT-12 | Misleading-Copy Report Loop | Existing File14 source requires independent exact-head verification |
| F14-FUT-13 | Privacy-Safe Friction Analytics | Cross-owner proof **unaccepted**; fail-closed |
| F14-FUT-14 | Conversion Anomaly Detector | Cross-owner proof **unaccepted**; fail-closed |
| F14-FUT-15 | Small-Cohort Privacy Guard | Existing File14 source requires independent exact-head verification |
| F14-FUT-16 | Experiment Safety Preflight Simulator | Existing File14 source requires independent exact-head verification |
| F14-FUT-17 | Experiment Early-Stop and Rollback Guard | Cross-owner proof **unaccepted**; fail-closed |
| F14-FUT-18 | FAQ Gap Intelligence | Existing File14 source requires independent exact-head verification |
| F14-FUT-19 | Message Consistency Graph | Existing File14 source requires independent exact-head verification |
| F14-FUT-20 | Translation Provenance/Terminology Lock | Existing File14 source requires independent exact-head verification |
| F14-FUT-21 | AI Ethical Copy Draft Assistant | Existing File14 source requires independent exact-head verification |
| F14-FUT-22 | Public Clinic Trust Change Log | Existing File14 source requires independent exact-head verification |
| F14-FUT-23 | Patient Choose-Safely Decision Guide | Existing File14 source requires independent exact-head verification |
| F14-FUT-24 | Doctor Clinic Readiness Self-Check | Existing File14 source requires independent exact-head verification |

## Ten nonfunctional requirements and two central contracts

| ID | Boundary | Acceptance evidence not inferred from source |
|---|---|---|
| F14-NFR-001 | Authorization and IDOR | Staging/lint/owner probes as applicable; live release unverified |
| F14-NFR-002 | Privacy lifecycle and erasure | Staging/lint/owner probes as applicable; live release unverified |
| F14-NFR-003 | Reliable queues/retries/reconciliation | Staging/lint/owner probes as applicable; live release unverified |
| F14-NFR-004 | Measured performance budgets | Staging/lint/owner probes as applicable; live release unverified |
| F14-NFR-005 | Accessibility/RTL acceptance | Staging/lint/owner probes as applicable; live release unverified |
| F14-NFR-006 | Observability and diagnostics | Staging/lint/owner probes as applicable; live release unverified |
| F14-NFR-007 | Migration/rollback | Staging/lint/owner probes as applicable; live release unverified |
| F14-NFR-008 | System Check/operability | Staging/lint/owner probes as applicable; live release unverified |
| F14-NFR-009 | WP/PHP/contract compatibility | Staging/lint/owner probes as applicable; live release unverified |
| F14-NFR-010 | Language, timezone and date/number accuracy | Staging/lint/owner probes as applicable; live release unverified |
| F14-CEN-01 | Canonical File14 owner + File26 projection + free/green/privacy rules | Cross-owner and independent checks required |
| F14-CEN-02 | Versioned command/read/event/index/cache/notification ownership | Real producers, retries and authorization required |

## Source tests and known blocked acceptance

- New `tests/r75-forty-file00-subject-origin-tests.php`: rejects 40 invalid original caller subjects without owner callback; validates canonical integer.
- New `tests/r76-forty-file00-native-status-tests.php`: rejects 40 invalid/unknown source statuses; allows exactly `approved` and `verified`.
- Existing R68/R69/R71/R73/R74 and all previous 80-pass ledgers remain separate historical source QA.
- Issue **#19** OPEN: signed, consented File01/07/08/09 native stage events and real owner availability.
- Issue **#22** OPEN: File20/25 owner-authorized, actually executable changed-placement CAS restoration and staging race test.
- Issue **#70** OPEN: separately consented, revocable, GPC/minor-safe File08 booking-request aggregate; current File14 reports null/unavailable, not zero.

## Exact release-truth matrix

| Reality | 2026-11-15 status |
|---|---|
| Repository base main | `d1cd343392d07607c60f0688362cee450e31774a` |
| Proposed branch runtime candidate | `1.4.76` — needs exact final-HEAD CI and package evidence |
| Deployed Version | UNVERIFIED |
| DB Version | UNVERIFIED |
| Migration State | UNVERIFIED |
| Live Verification Status | UNVERIFIED |
| Cross-owner blockers resolved in this branch | 0/3 |
| New direct source defects addressed | 2 |
| Live deployment or production resolution | NOT CLAIMED |

WordPress's connected-site integration reported disconnected Jetpack. WPVibe HTTP 530 could not establish the exact deployed WordPress code or DB. **Exact deployed code ابھی unverified ہے؛ repository-based diagnosis provisional ہے۔**
