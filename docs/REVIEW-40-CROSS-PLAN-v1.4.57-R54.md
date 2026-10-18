# R54 — PRE-CODE independent 40-lens source freeze, 18 October 2026

Repository HEAD before code: **4c717d6543f6524105f2caaa2adeec1e6a1a6fc2**, source version **1.4.56**. The 40 unique requirements are from attached amended File14 master plan (16 FR/24 FUT), constrained by Consolidated Three Central Governing Plans and companion File07/08/09/20/25 ownership. Repo code != deployed WordPress or database.

## Scope and forty independently identified source lenses

| # | Approved requirement | Main inspected owner boundary | Pre-code result |
|---:|---|---|---|
| 1 | F14-FR-001 | Patient value proposition / emergency limitation | SOURCE REVIEW: existing implementation/traceability; staging not proven |
| 2 | F14-FR-002 | Doctor global-clinic promise / zero commission | SOURCE REVIEW: existing implementation/traceability; staging not proven |
| 3 | F14-FR-003 | Primary CTAs to Files07/09 | SOURCE REVIEW: existing implementation/traceability; staging not proven |
| 4 | F14-FR-004 | Patient and doctor steps / owner transition | SOURCE REVIEW: existing implementation/traceability; staging not proven |
| 5 | F14-FR-005 | Verified trust and no treatment guarantee | SOURCE REVIEW: existing implementation/traceability; staging not proven |
| 6 | F14-FR-006 | Zero-commission/optional donation copy | SOURCE REVIEW: existing implementation/traceability; staging not proven |
| 7 | F14-FR-007 | File20 sole placement/route owner | SOURCE REVIEW: existing implementation/traceability; staging not proven |
| 8 | F14-FR-008 | File25 visual consumer, File14 content owner | SOURCE REVIEW: existing implementation/traceability; staging not proven |
| 9 | F14-FR-009 | File07/08/09 destination dependency health | SOURCE REVIEW: existing implementation/traceability; staging not proven |
| 10 | F14-FR-010 | Attribution lawful consent/GPC/retention | SOURCE REVIEW: existing implementation/traceability; staging not proven |
| 11 | F14-FR-011 | Actual File07/08/09 owner evidence, not click inference | BLOCKED #19: File01 signed producer registration and staging |
| 12 | F14-FR-012 | Founder approvals for experiments | SOURCE REVIEW: existing implementation/traceability; staging not proven |
| 13 | F14-FR-013 | American English/Urdu/Arabic localization | SOURCE REVIEW: existing implementation/traceability; staging not proven |
| 14 | F14-FR-014 | FAQ policy links, version and freshness | SOURCE REVIEW: existing implementation/traceability; staging not proven |
| 15 | F14-FR-015 | Accessible semantic CTAs/mobile/low bandwidth | SOURCE REVIEW: existing implementation/traceability; staging not proven |
| 16 | F14-FR-016 | Numeric/source/claim governance | SOURCE REVIEW: existing implementation/traceability; staging not proven |
| 17 | F14-FUT-01 | Ethical explicit intent router | SOURCE REVIEW: existing implementation/traceability; staging not proven |
| 18 | F14-FUT-02 | Allowlisted doctor/clinic handoff | SOURCE REVIEW: existing implementation/traceability; staging not proven |
| 19 | F14-FUT-03 | Current public evidence drawer | SOURCE REVIEW: existing implementation/traceability; staging not proven |
| 20 | F14-FUT-04 | Claim freshness fail-close | SOURCE REVIEW: existing implementation/traceability; staging not proven |
| 21 | F14-FUT-05 | Zero-fee parity sentinel | SOURCE REVIEW: existing implementation/traceability; staging not proven |
| 22 | F14-FUT-06 | Founder-approved jurisdictional claims | SOURCE REVIEW: existing implementation/traceability; staging not proven |
| 23 | F14-FUT-07 | Semantic meaning diff | SOURCE REVIEW: existing implementation/traceability; staging not proven |
| 24 | F14-FUT-08 | Dark pattern governance | SOURCE REVIEW: existing implementation/traceability; staging not proven |
| 25 | F14-FUT-09 | Safe degraded destination failover | SOURCE REVIEW: existing implementation/traceability; staging not proven |
| 26 | F14-FUT-10 | Preview without cross-owner mutations | SOURCE REVIEW: existing implementation/traceability; staging not proven |
| 27 | F14-FUT-11 | Measured conversion quality, not raw CTR | SOURCE REVIEW: existing implementation/traceability; staging not proven |
| 28 | F14-FUT-12 | Misleading-copy report lifecycle | SOURCE REVIEW: existing implementation/traceability; staging not proven |
| 29 | F14-FUT-13 | Consent-based friction aggregation | R54-D02: malformed friction funnel_stage typed evidence omitted without fail-close |
| 30 | F14-FUT-14 | Owner-evidenced funnel anomaly | SOURCE REVIEW: existing implementation/traceability; staging not proven |
| 31 | F14-FUT-15 | Minimum 10 privacy cohort and strict count types | Prior R53 source fixes accepted; R54 related FAQ bypass under FUT18|
| 32 | F14-FUT-16 | Experiment approved preflight | SOURCE REVIEW: existing implementation/traceability; staging not proven |
| 33 | F14-FUT-17 | Early stop + actual owner-authorized CAS restoration | BLOCKED #22: changed placement owner CAS restoration absent |
| 34 | F14-FUT-18 | Aggregate-only FAQ suggestion | R54-D01: noncanonical FAQ COUNT accepted after integer coercion |
| 35 | F14-FUT-19 | Message/locale consistency graph | SOURCE REVIEW: existing implementation/traceability; staging not proven |
| 36 | F14-FUT-20 | Translation provenance and term lock | SOURCE REVIEW: existing implementation/traceability; staging not proven |
| 37 | F14-FUT-21 | AI drafting, no auto-publication | SOURCE REVIEW: existing implementation/traceability; staging not proven |
| 38 | F14-FUT-22 | Audited public trust change log | SOURCE REVIEW: existing implementation/traceability; staging not proven |
| 39 | F14-FUT-23 | Informational patient chooser | SOURCE REVIEW: existing implementation/traceability; staging not proven |
| 40 | F14-FUT-24 | Nonbinding doctor readiness self-check | SOURCE REVIEW: existing implementation/traceability; staging not proven |

## Confirmed R54 local gaps, before ANY code modification
1. **R54-D01 — F14-FUT-18/FUT-15**: `GCU_Future_Policy::valid_faq_aggregate_signal()` first accepts any `ctype_digit` string up to 12 digits and then does `self::cohort_allowed((int)$count)`. The cast undoes the R53 strict canonical validation: `'000000000010'` becomes 10 and is accepted, despite `valid_aggregate_count('000000000010') === false`. FAQ gap candidates consume this result and can surface source-unverified small group activity. Root cause is raw -> int coercion before the canonical privacy cohort gate. Fix must validate *raw count* with `valid_aggregate_count()` then call `cohort_allowed($count)` without coercion.
2. **R54-D02 — F14-FUT-13/F14-FR-010**: `GCU_Future_Intelligence::friction_summary()` validates `total` but not whether `funnel_stage` is a string. A corrupt SQL row with `funnel_stage` as array or number may be silently ignored, yielding valid-looking aggregate statistics when evidence is malformed. Fail closed for non-string/empty stage while preserving explicit exclusions of honest unknown string stage and privacy suppressed rare stages.

## Existing external blockers (rechecked at source)
- **#19 OPEN**: Versioned File01 signed registered File07/08/09 real owner outcome event contract, deployed parity and actual staging proof are missing. Existing source adapters/hooks are not production acceptance. Do not invent success rates.
- **#22 OPEN**: `GCU_Experiment_Baseline::reconcile_after_stop()` recognizes no drift, but changed-placement CAS restoration via File20/25 owner has not been approved or proven in staging. Leave safety pause and manual review; no speculative cross-owner mutation.
- Source schema constants Base 10005 / Future 1; actual installed schema, wp_options and migration state UNKNOWN.

## Review and correction sequence
Freeze complete 40-requirement source ledger **first**, then patch both independently established File14 owner defects as one batch, add 40 direct FAQ policy test vectors plus 40 adversarial mocked WordPress Friction cases, execute 40 full plan-check cycles on the exact new commit, PHP 7.4 and 8.3, two fresh review rounds, deterministic ZIP/SHA/SBOM, candidate PR and fresh post-merge exact-main CI.

**Truth ledger (precode):** Repository HEAD 4c717d6543f6524105f2caaa2adeec1e6a1a6fc2; Deployed Version UNVERIFIED; Actual DB Version UNVERIFIED; Migration State UNVERIFIED; Live Verification NOT RUN.
Exact deployed code ابھی unverified ہے؛ repository-based diagnosis provisional ہے۔
