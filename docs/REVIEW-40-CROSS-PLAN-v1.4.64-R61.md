# R61 — FROZEN 40-scope File14/Three Central Plans/owner contracts review, pre-code
Date 2026-10-18. Exact GitHub source HEAD **58b4c0e453c37ca195adffe681237ee98e294c10**, plugin candidate v1.4.63. Attached amended File14 v2.0 CTI24 Word plan, Consolidated Three Central Plans v3, File07/09/25 Word owner plans; File01/08/20 current-source contracts reviewed separately. GitHub source is NOT deployed WordPress; no runtime DB/schema, plugin SHA parity, wp_options, staging or live proof.

## Forty distinct requirement-to-implementation lenses — 16 FR and 24 FUT
| # | Requirement | Plan scope | Pre-code finding |
|---:|---|---|---|
| 1 | F14-FR-001 | Patient value proposition / emergency limitation | Existing source/plan trace reviewed, staging and Live unverified |
| 2 | F14-FR-002 | Doctor global-clinic promise / zero commission | Existing source/plan trace reviewed, staging and Live unverified |
| 3 | F14-FR-003 | Primary CTAs to Files07/09 | Existing source/plan trace reviewed, staging and Live unverified |
| 4 | F14-FR-004 | Patient and doctor steps / owner transition | Existing source/plan trace reviewed, staging and Live unverified |
| 5 | F14-FR-005 | Verified trust and no treatment guarantee | Existing source/plan trace reviewed, staging and Live unverified |
| 6 | F14-FR-006 | Zero-commission/optional donation copy | Existing source/plan trace reviewed, staging and Live unverified |
| 7 | F14-FR-007 | File20 sole placement/route owner | R61-D01: canonical owner placement row field types/priority/date integrity is not verified; #22 owner CAS restore OPEN |
| 8 | F14-FR-008 | File25 visual consumer, File14 content owner | Existing source/plan trace reviewed, staging and Live unverified |
| 9 | F14-FR-009 | File07/08/09 destination dependency health | R61-D02: inbound File07/08/09 availability event contract_version coerced and owner availability recheck absent |
| 10 | F14-FR-010 | Attribution lawful consent/GPC/retention | Existing source/plan trace reviewed, staging and Live unverified |
| 11 | F14-FR-011 | Actual File07/08/09 owner evidence, not click inference | Owner attested File01 signed event registry and staging #19 OPEN |
| 12 | F14-FR-012 | Founder approvals for experiments | Existing source/plan trace reviewed, staging and Live unverified |
| 13 | F14-FR-013 | American English/Urdu/Arabic localization | Existing source/plan trace reviewed, staging and Live unverified |
| 14 | F14-FR-014 | FAQ policy links, version and freshness | Existing source/plan trace reviewed, staging and Live unverified |
| 15 | F14-FR-015 | Accessible semantic CTAs/mobile/low bandwidth | Existing source/plan trace reviewed, staging and Live unverified |
| 16 | F14-FR-016 | Numeric/source/claim governance | Existing source/plan trace reviewed, staging and Live unverified |
| 17 | F14-FUT-01 | Ethical explicit intent router | Existing source/plan trace reviewed, staging and Live unverified |
| 18 | F14-FUT-02 | Allowlisted doctor/clinic handoff | R61-D02: inbound File07/08/09 availability event contract_version coerced and owner availability recheck absent |
| 19 | F14-FUT-03 | Current public evidence drawer | Existing source/plan trace reviewed, staging and Live unverified |
| 20 | F14-FUT-04 | Claim freshness fail-close | Existing source/plan trace reviewed, staging and Live unverified |
| 21 | F14-FUT-05 | Zero-fee parity sentinel | Existing source/plan trace reviewed, staging and Live unverified |
| 22 | F14-FUT-06 | Founder-approved jurisdictional claims | Existing source/plan trace reviewed, staging and Live unverified |
| 23 | F14-FUT-07 | Semantic meaning diff | Existing source/plan trace reviewed, staging and Live unverified |
| 24 | F14-FUT-08 | Dark pattern governance | Existing source/plan trace reviewed, staging and Live unverified |
| 25 | F14-FUT-09 | Safe degraded destination failover | R61-D02: inbound File07/08/09 availability event contract_version coerced and owner availability recheck absent |
| 26 | F14-FUT-10 | Preview without cross-owner mutations | Existing source/plan trace reviewed, staging and Live unverified |
| 27 | F14-FUT-11 | Measured conversion quality, not raw CTR | Existing source/plan trace reviewed, staging and Live unverified |
| 28 | F14-FUT-12 | Misleading-copy report lifecycle | Existing source/plan trace reviewed, staging and Live unverified |
| 29 | F14-FUT-13 | Consent-based friction aggregation | Existing source/plan trace reviewed, staging and Live unverified |
| 30 | F14-FUT-14 | Owner-evidenced funnel anomaly | Existing source/plan trace reviewed, staging and Live unverified |
| 31 | F14-FUT-15 | Minimum 10 privacy cohort and strict count types | Existing source/plan trace reviewed, staging and Live unverified |
| 32 | F14-FUT-16 | Experiment approved preflight | R61-D01: canonical owner placement row field types/priority/date integrity is not verified; #22 owner CAS restore OPEN |
| 33 | F14-FUT-17 | Early stop + actual owner-authorized CAS restoration | R61-D01: canonical owner placement row field types/priority/date integrity is not verified; #22 owner CAS restore OPEN |
| 34 | F14-FUT-18 | Aggregate-only FAQ suggestion | Existing source/plan trace reviewed, staging and Live unverified |
| 35 | F14-FUT-19 | Message/locale consistency graph | Existing source/plan trace reviewed, staging and Live unverified |
| 36 | F14-FUT-20 | Translation provenance and term lock | Existing source/plan trace reviewed, staging and Live unverified |
| 37 | F14-FUT-21 | AI drafting, no auto-publication | Existing source/plan trace reviewed, staging and Live unverified |
| 38 | F14-FUT-22 | Audited public trust change log | Existing source/plan trace reviewed, staging and Live unverified |
| 39 | F14-FUT-23 | Informational patient chooser | Existing source/plan trace reviewed, staging and Live unverified |
| 40 | F14-FUT-24 | Nonbinding doctor readiness self-check | Existing source/plan trace reviewed, staging and Live unverified |

## Confirmed File14-owned root causes before any code edit
1. **R61-D01 — F14-FR-007/FUT16/17, immutable owner placement baseline evidence**. R60 strengthened `GCU_Experiment_Baseline::shape()` to require required columns/UUID/placement_key/row_version, but it does not type-check `block_key`, `route_key`, `slot_key`, `audience`, `status`, `starts_at`, `ends_at` and signed SQL `priority`. SQL/plugin row values like array status, bool audience, `'1junk'` priority or datetime array may be captured as Founder-approved baseline and later `ready()` may attest a matching corrupt partial owner state as 'unchanged'. Require original type and non-empty text where declared NOT NULL, nullable schedule to be NULL or nonempty canonical string, and priority valid signed integer or canonical signed decimal string bounded to actual signed SQL INT range; reject malformed stored snapshots too. Do not invent owner CAS changed-placement writes.
2. **R61-D02 — F14-FR-009/FUT02/FUT09 and File07/08/09 versioned readiness event consumers**. In `GCU_Contracts::apply_inbound_event()`, `contract_version` is persisted using `max(1,absint($p['contract_version']))`. This accepts `-1`, `'0'`, `'1junk'`, float or booleans as legitimate versioned owner readiness evidence, while source `GCU_Repository::validate_inbound_event()` only checks event UUID, availability boolean and same-origin URL, not `contract_version`. Preserve optional omitted =1 for historical v1 producers; reject if explicitly supplied version is not canonical positive integer/decimal within PHP_INT_MAX. Also ensure `apply_inbound_event` itself fail-closes nonboolean availability and missing owner event UUID (e.g., malformed stored inbox records) before state write, without introducing foreign owner writes. Keep destination safe fallback.

## Separate owner blockers
- #19 OPEN: Founder-approved File01 signed event registry, File07/08/09 actual postcommit producer attestation, consent/replay/GPC, staging/install/live proof unavailable. Event-ready is not a booking success.
- #22 OPEN: actual File20/25 approved changed-placement CAS restore contract/race tests/staging absent; File14 only checks/pauses, cannot modify foreign owners.
- Relevant owner GitHub HEADs are separate source realities. File08 latest cross-owner source `92e2422d6a235632810ed4981f151fc3250784aa`. No connected WP live site; actual deployed code unverified.

## Acceptance sequence (freeze FIRST)
After this ledger, correct two actual File14 local defects in one source batch; add forty real PHP baseline snapshot SQL type/signed-priority regression cases and forty direct owner-availability contract version boolean/UUID cases. Exact PR head and merged main full PHP7.4/8.3 quality, 40 repeated complete 40-contract source scans, 2 independent fresh reviews, deterministic ZIP/SHA256/SBOM. No implicit claim of 40 independent real browser tests or Production Resolved.

Five-field status before edits: Repository HEAD `58b4c0e453c37ca195adffe681237ee98e294c10`; Deployed Version UNKNOWN; DB Version UNKNOWN; Migration State UNKNOWN; Live Verification NOT RUN.
Exact deployed code ابھی unverified ہے؛ repository-based diagnosis provisional ہے۔
