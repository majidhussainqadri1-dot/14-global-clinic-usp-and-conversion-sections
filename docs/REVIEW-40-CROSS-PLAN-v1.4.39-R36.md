# File14 R36 — Forty distinct source-to-plan checks and two verified corrective workstreams

**Date:** 2026-10-14 (Pakistan). **Frozen repository source:** File14 `main@7b6cd35be6197bc8a1acd987930a729b6d3bcd72` (candidate 1.4.38). Related owner SHAs: File07 `2f4a89707724fd2b9946600afe10ddab27ec3c2d`, File08 `9dcc8d16ea81d0bbd27184e71bc09ba04d30ba03`, File09 `cfc5f781a766330314dc98c42abeca0eb7786eba`, File01 `adf6dbb9980a85f25f7cf4c2ee679b52703c2e71`, File20 `8a4dbcaf4fef8e926b9b834ecfde16c21a0f00ca`.

**Authority:** user-supplied File14 amended master plan dated 2026-08-10; the Consolidated Three Central Plans; File07/08/09, Home 21 and UI File25 attached source plans. Plan passages particularly `F14-FR-011`, `F14-FUT-13,14,15,16,17`, `F14-NFR-002,006,007` and release criteria. Plan content is specification, not proof of code or deployment.

## What the forty reviews actually checked

These are **forty requirement lenses** anchored to the 16 mandatory FRs and 24 approved FUT additions. The source quality command `scripts/review40-iterative.sh` executes the full forty-lens source-marker suite in **forty consecutive passes** (forty sets of forty checks). This release adds forty independently varied native owner subgroup privacy examples and forty varied hourly stop-guard results; it retains forty previous distinct workflow approval/activation and forty lifecycle tests. **No claim** of forty separate real WordPress browser/staging E2E journeys, and no assumption of production parity.

| Review | Source-plan ID | Requirement | Owner relationship / evidence sought | R36 disposition |
|---:|---|---|---|---|
| 1 | F14-FR-001 | Patient value proposition; emergency limits | File07/08 discovery/appointment claims; owner truth required | SOURCE TRACE; STAGING UNVERIFIED |
| 2 | F14-FR-002 | Doctor value proposition; 0% commission | File09 onboarding, File01 business rules | SOURCE TRACE; STAGING UNVERIFIED |
| 3 | F14-FR-003 | Primary CTAs | File07 destination, File09 application; File20 shell | SOURCE TRACE; STAGING UNVERIFIED |
| 4 | F14-FR-004 | Two honest How-it-works journeys | File07/08/09 canonical events; no inferred booking | SOURCE TRACE; STAGING UNVERIFIED |
| 5 | F14-FR-005 | Trust claims and verification limits | File09 owner, no cure guarantees | SOURCE TRACE; STAGING UNVERIFIED |
| 6 | F14-FR-006 | Zero commission, direct fee, optional support neutrality | File01 governance, File14 policy parity | SOURCE TRACE; STAGING UNVERIFIED |
| 7 | F14-FR-007 | Placement registry | File20 canonical shell/slots; Issue #22 changed-placement restore | SOURCE TRACE; STAGING UNVERIFIED |
| 8 | F14-FR-008 | Reusable semantic content | File25 visual owner, File14 copy version | SOURCE TRACE; STAGING UNVERIFIED |
| 9 | F14-FR-009 | Destination degradation | File07/08/09 readiness and honest fallback | SOURCE TRACE; STAGING UNVERIFIED |
| 10 | F14-FR-010 | Consent-bound campaign attribution | File14 consent, GPC, retention, privacy lifecycle | SOURCE TRACE; STAGING UNVERIFIED |
| 11 | F14-FR-011 | Native funnel event contract | File07/08/09 owner receipts; Issue #19 OPEN | EXTERNAL INTEGRATION / RESTORE NOT ACCEPTED |
| 12 | F14-FR-012 | Founder-governed experiments | FUT16 real preflight and FUT17 partial baseline | SOURCE TRACE; STAGING UNVERIFIED |
| 13 | F14-FR-013 | en-US, ur-PK, ar-SA | File06/20/25 i18n and RTL consumers | SOURCE TRACE; STAGING UNVERIFIED |
| 14 | F14-FR-014 | FAQ policy and claim freshness | File14 governed public answers | SOURCE TRACE; STAGING UNVERIFIED |
| 15 | F14-FR-015 | Accessible semantic CTA and navigation | File25 visual, File20 shell; staging needed | SOURCE TRACE; STAGING UNVERIFIED |
| 16 | F14-FR-016 | Evidence/date/version-bound claims | File14 claims and audit; no fabricated clinical truth | SOURCE TRACE; STAGING UNVERIFIED |
| 17 | F14-FUT-01 | FUT01 Ethical Intent Router | Explicit intent; no health or background profiling | SOURCE TRACE; STAGING UNVERIFIED |
| 18 | F14-FUT-02 | FUT02 Smart Destination Handoff | Allowlisted owner destination, no private payload | SOURCE TRACE; STAGING UNVERIFIED |
| 19 | F14-FUT-03 | FUT03 Trust Evidence Drawer | Approved current public claims only | SOURCE TRACE; STAGING UNVERIFIED |
| 20 | F14-FUT-04 | FUT04 Claim Freshness Sentinel | Stale claim fails closed | SOURCE TRACE; STAGING UNVERIFIED |
| 21 | F14-FUT-05 | FUT05 Zero-Commission Parity | Explicit governance check on experiment run | SOURCE TRACE; STAGING UNVERIFIED |
| 22 | F14-FUT-06 | FUT06 Jurisdiction Copy | Founder-approved variants only | SOURCE TRACE; STAGING UNVERIFIED |
| 23 | F14-FUT-07 | FUT07 Semantic Copy-Diff | Protected meanings cannot drift | SOURCE TRACE; STAGING UNVERIFIED |
| 24 | F14-FUT-08 | FUT08 Dark-Pattern Linter | No cure guarantee, fake scarcity, paid ranking | SOURCE TRACE; STAGING UNVERIFIED |
| 25 | F14-FUT-09 | FUT09 Destination Failover | Truthful unavailable states | SOURCE TRACE; STAGING UNVERIFIED |
| 26 | F14-FUT-10 | FUT10 Scenario Preview | No preview writes into owner live state | SOURCE TRACE; STAGING UNVERIFIED |
| 27 | F14-FUT-11 | FUT11 Quality Score | Provisional unless real p75/p95 and owner proof | SOURCE TRACE; STAGING UNVERIFIED |
| 28 | F14-FUT-12 | FUT12 Copy Report and Correction | Structured auditable complaint resolution | SOURCE TRACE; STAGING UNVERIFIED |
| 29 | F14-FUT-13 | FUT13 Friction Analytics | R36 fix: small native groups no longer reveal stage/name | R36 SOURCE FIX / test required |
| 30 | F14-FUT-14 | FUT14 Anomaly Detector | R36 fix: hourly stop errors propagate and log | R36 SOURCE FIX / test required |
| 31 | F14-FUT-15 | FUT15 Small Cohort Privacy | Default 10; R36 suppress label and count together | R36 SOURCE FIX / test required |
| 32 | F14-FUT-16 | FUT16 Experiment Safety Preflight | R35 verified mandatory approval+run | SOURCE TRACE; STAGING UNVERIFIED |
| 33 | F14-FUT-17 | FUT17 Early Stop and Rollback | R36 errors surfaced; changed-placement restore OPEN #22 | EXTERNAL INTEGRATION / RESTORE NOT ACCEPTED |
| 34 | F14-FUT-18 | FUT18 FAQ Gaps | Only coarsened non-sensitive aggregates | SOURCE TRACE; STAGING UNVERIFIED |
| 35 | F14-FUT-19 | FUT19 Message Consistency | Versioned locale/claim comparison | SOURCE TRACE; STAGING UNVERIFIED |
| 36 | F14-FUT-20 | FUT20 Terminology Lock | en-US/ur-PK/ar-SA ownership provenance | SOURCE TRACE; STAGING UNVERIFIED |
| 37 | F14-FUT-21 | FUT21 Ethical AI Copy | Draft-only; never auto-publish | SOURCE TRACE; STAGING UNVERIFIED |
| 38 | F14-FUT-22 | FUT22 Public Trust Changelog | Dated approved changes; no private data | SOURCE TRACE; STAGING UNVERIFIED |
| 39 | F14-FUT-23 | FUT23 Patient Safe Choice Guide | Educational only, no diagnosis/ranking | SOURCE TRACE; STAGING UNVERIFIED |
| 40 | F14-FUT-24 | FUT24 Doctor Readiness Checklist | Not File09 verification decision | SOURCE TRACE; STAGING UNVERIFIED |

## Defect discovery finished before fixes; grouped fixes only after review

**R36-D01 — Rare cohort *presence* leaked in analytics.** File14 `GCU_Owner_Outcomes::owner_stage_summary()` returned `stage` and `owner_contract` for a group smaller than minimum 10 whenever other groups caused the combined total to reach ten; only the count became `null`. Therefore a rare actual application or booking group was still identifiable from its category label. The plan FUT13 and FUT15 require suppressing small cohorts, not just exact counts. **Source change:** omit the *entire* small row, do not leak owner type or stage, and report generic suppression without an owner-specific count. Retains authorization and aggregate-only no-correlated-journey semantics.

**R36-D02 — Safety stop failure was silently discarded.** `GCU_Future_Intelligence::hourly_intelligence()` and `business_policy_changed()` invoked `early_stop_guard()` without propagating or logging its `WP_Error`; a failed DB/clock check could therefore be invisible to the parent job. **Source change:** common checked stop helper returns the original error and emits structured, low-cardinality operational error code (no patient/applicant/placement data).

**R36-D03 — Missing hardening implementation counted as successful stop.** `early_stop_guard()` used to return integer 0 when the required safety class did not load. **Source change:** fail closed with explicit `WP_Error` and structured alert, rather than reporting that zero experiments needed stopping.

## New verification, ownership boundaries and outstanding gates

- `tests/r36-owner-cohort-privacy-tests.php`: 40 varying group shapes (all eligible, mixed eligible/rare, all rare, malicious cross-owner category), plus denied-capability test; verifies *actual* owner summary output and JSON category disclosure suppression.
- `tests/r36-hourly-stop-error-tests.php`: 40 alternating error/healthy stop returns from actual `hourly_intelligence()`, requiring 20 propagated WP_Errors with corresponding privacy-safe operational logs and 20 unobstructed successful results.
- Original R34 and R35 tests continue: forty repeated full source-review passes, forty lifecycle simulations, forty approval/activation policy scenarios. CI must run both PHP 7.4 and 8.3 plus deterministic ZIP/SBOM and two fresh reviews on exact PR and merged `main`.
- **Release blockers remain:** Issue #19 File01 registry and actual File07/08/09 owner event acceptance, authenticated/consented sensitive journey proof; Issue #22 atomic provenance-bound restore of genuinely experiment-mutated placements with File20/25 integration. Neither may be closed based on these source tests.
- No change to owner clinical, doctor, application, booking or financial data. No newly invented API owner contract or DB migration.

## Three separated realities — final required register

**Repository HEAD:** original freeze above; exact PR and merged HEAD recorded separately after CI.
**Source Version:** 1.4.39 candidate; Base Schema target `10005`, Future target `1`.
**Deployed Version:** UNVERIFIED.
**Actual DB Version / rows / wp_options:** UNVERIFIED.
**Migration State:** UNVERIFIED.
**Live Verification Status:** NOT RUN.
**Staging:** NOT RUN; backup/restore and Founder role/visual acceptance not proven.

Exact deployed code ابھی unverified ہے؛ repository-based diagnosis provisional ہے۔
