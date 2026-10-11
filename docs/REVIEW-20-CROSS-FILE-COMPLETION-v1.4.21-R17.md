# File14 R17 — Twenty source/plan/owner contract reviews and corrected source integrity (v1.4.21)

**Date** 2026-10-11. **Frozen exact File14 main before modifications** `d2c1f76a18f73db3ff0fec64f786a9478c8b9f3d` (software 1.4.20). This is source-repository truth, not deployed artifact truth.

## Governing sources actually inspected

- User-supplied File14 Global Clinic USP/Conversion v2.0 amended Future CTI24 master plan (2026-08-10); its base F14-FR-001..016 and amended F14-FUT-01..24 including FUT-11 quality score, FUT-15 cohort privacy, FUT-18 FAQ gap, FUT-21 AI ethical copy, FUT-17 safety restore.
- User-supplied consolidated Three Central Governing Plans: later current **one free tier, no paid AI, zero commission, no donation preference** supersedes older contradictory pricing drafts. Versioned native source ownership and Founder control remain mandatory.
- Related supplied File07/09/25 plans, exact File14 source/traceability, GitHub source-contract snapshots File01/07/08/09/20. These are repository snapshots, not live proof.
- No deployed WordPress plugin checksum, real database state, staging runtime or migrations provided.

**Sequence:** twenty independent static source/contract checks were completed BEFORE the R17 code change batch. Clean means only the specified source predicate or trace mapping was found, not full operational acceptance or twenty staging installations.

## Frozen 20-round findings

| Round | Source/contract focus | Classification | Limit or evidence |
|---:|---|---|---|
| 1 | File14 F14-FR-001..016 plan trace | NARROW SOURCE CLEAN | Sixteen Must-IDs present in trace; no end-to-end acceptance inferred |
| 2 | File14 approved F14-FUT-01..24 catalog | NARROW SOURCE CLEAN | Twenty-four amended scoped Future feature identifiers present |
| 3 | Central free core and zero platform commission | NARROW SOURCE CLEAN | No donor entitlement/paid ranking business sentinel present |
| 4 | File00 identity and permission owner | NARROW SOURCE CLEAN | Native authentication assertion adapter |
| 5 | File01 versioned manifest/route registry | NARROW SOURCE CLEAN | Canonical route and companion contract checks |
| 6 | File07 doctor directory boundary | NARROW SOURCE CLEAN | Doctor discovery delegated to owner |
| 7 | File08 clinic/appointments boundary | NARROW SOURCE CLEAN | Owner appointment facts remain separate from clicks |
| 8 | File09 doctor onboarding/verification | NARROW SOURCE CLEAN | No File14 owner doctor application promotion |
| 9 | Files20/25 shell and visual ownership | NARROW SOURCE CLEAN | Canonical public UX contracts |
| 10 | File24 privacy/GPC assurances | NARROW SOURCE CLEAN | Explicit native privacy and GPC consent gating |
| 11 | Verified public trust source/freshness | NARROW SOURCE CLEAN | Approved claim freshness and Founder publication checks |
| 12 | Multilingual protected terminology | NARROW SOURCE CLEAN | Founder reviewed protected concept set required |
| 13 | F14-FUT-21 AI Copy Assistant source input | DEFECT R17-D01 | Nested base_text/objective or invalid provider candidate can reach strip/cast, provoking warnings or misleading copied text |
| 14 | F14-FUT-11 measured privacy quality | DEFECT R17-D02 | Unbounded privacy value can become privacy_verified=true; invalid accessibility/performance metadata can also be marked verified |
| 15 | F14-FUT-18 FAQ aggregate evidence | DEFECT R17-D03 | absint(-25) becomes 25, and nested question text can be cast, defeating real cohort/evidence meaning |
| 16 | F14-FUT-15 cohort / FUT24 self-check | NARROW SOURCE CLEAN | Minimum cohort and non-binding verification metadata guards |
| 17 | F14-FUT-22 reviewed change-log and i18n | NARROW SOURCE CLEAN | Capped provenance-first public log, protected translations |
| 18 | Schema/migration and PHP package gates | NARROW SOURCE CLEAN | Source schema guard/7.4 & 8.3 CI configured, actual live schema unknown |
| 19 | F14-FR-011 File07/08/09 owner facts | OPEN R17-G01 | Issue #19, genuine native owner attestation + File01 events contract not integrated |
| 20 | F14-FUT-17 approved experiment restoration | OPEN R17-G02 | Issue #22, stopped experiment not equivalent to safe baseline/placement restoration |

**Before corrections:** 15 narrowly source-clean; **three File14 source defects** (rounds 13–15); **two known missing owner/design contracts** (rounds 19–20).

## Single corrective coding stage after all twenty passes

**R17-D01 / F14-FUT-21 — Source-safe AI Draft Input and Candidate Types.** `GCU_Future_Intelligence::ai_copy_assist` previously used `wp_strip_all_tags` and `sanitize_key` on possibly nested JSON editor `base_text` and `objective`, and also on provider suggestion `text` of unsupported type. This could produce warnings or invented 'Array' text. `GCU_Future_Policy::valid_ai_copy_inputs` now accepts only a textual base and a sanctioned clarity/concise/trust objective. `normalize_ai_candidate_text` accepts typed string or a string text field and rejects objects/nested proposals; typed invalid provider suggestions are rejected rather than rendered or published. Existing source-grounding, protected claims, rate limiting and human editorial review remain mandatory. Regressions cover all supported/unsupported shapes. No AI draft may auto-publish.

**R17-D02 / F14-FUT-11 — Strict measured privacy evidence and quality status.** A numeric privacy score outside 0..100 previously passed `is_numeric` into `privacy_verified=true` and was forcibly clamped in metrics; separate accessibility and performance verified booleans could also contradict their 0–100 evidence guards. One `valid_quality_percentage` guard now checks nonbool real finite numeric [0,100] and is used by `quality_evidence_status`, quality_score composite completeness, source metrics and `privacy_verified` / `performance_verified` / `accessibility_verified` metadata. Failing any required evidence yields an explicit provisional quality result, never a fabricated score. Historical owner stages remain ineligible until File07/08/09 approval.

**R17-D03 / F14-FUT-18 plus FUT-15 — Genuine nonnegative FAQ question aggregates.** `faq_gap_candidates` previously called `absint` on a source-provided aggregate count, so -25 became a fabricated positive cohort of 25; nonstring question could trigger coercion. `valid_faq_aggregate_signal` now checks a textual question, nonnegative whole-number real count and minimum aggregate threshold BEFORE generating any suggested FAQ gap. Invalid/low-frequency signals are suppressed; all suggestions remain `suggested_only` and `auto_publish=false`. No raw patient questions or individual identifiers are written by this change.

**Candidate software** 1.4.21; source schema targets base 10005 / Future 1 unchanged. No migrations or cross-owner mutation introduced.

## Still incomplete, not hidden by clean tests

- **Issue #19, R17-G01**: File07/08/09 signed, consent-aware, versioned/idempotent native owner arrival/application/booking lifecycle events and File01 registry schema. No File14 click may be counted as confirmed owner success.
- **Issue #22, R17-G02**: approved experiment pre-stop baseline snapshot, placement/variant restoration, concurrency/rollback/migration staging verification. Status `stopped` does not mean restored.

## Exact release and live truth gates

Candidate PHP7.4 & PHP8.3 quality, Fresh Reviews 1/2, reproducible ZIP/checksum/SBOM, PR and independent new exact-main post-merge CI must each be observed before repo QA success. Actual live deployed plugin/version/checksum, WordPress options, DB tables/columns/rows, migration state, runtime logs/dependencies, staging backup/restore and live browser retest have not been verified.

**Repository HEAD** `d2c1f76a18f73db3ff0fec64f786a9478c8b9f3d` audited frozen baseline. **Deployed Version** unknown. **DB Version** unknown. **Migration State** unknown. **Live Verification Status** not performed.

**Exact deployed code ابھی unverified ہے؛ repository-based diagnosis provisional ہے۔**
