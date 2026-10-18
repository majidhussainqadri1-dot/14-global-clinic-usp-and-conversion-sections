# R60 — FROZEN PRE-CODE forty-lens File14 & central/companion owner audit
2026-10-18. Exact GitHub source HEAD before code changes: `b8304f6bbff2c1d0e63a3c0966bc8a7302bb5aca`, plugin source v1.4.62. Actual deployed WordPress source and installed DB are separate, UNKNOWN realities.

## Governing inputs
Local uploaded amended File14 v2.0/CTI24, Three Central Governing Master Plans v3.0, File09 Advanced Trust, File25 Public UI; GitHub File20 shell contract `sabri-unified-application-shell/contracts/file20-central-plan-v4.json`, File01 registry, File07 directory, File08 clinic and File09 onboarding source interfaces independently reviewed. File08 latest repository main `92e2422d6a235632810ed4981f151fc3250784aa` differs from earlier owner-head source snapshots and is NOT deployed evidence; its latest `tests/t27-r1-file14-owner-outcome-regressions.php` explicitly marks real WP staging unverified.

## Forty distinct amended File14 owner/plan source lenses (16 FR, 24 FUT)
| # | Requirement | Plan scope | Pre-patch status |
|---:|---|---|---|
| 1 | F14-FR-001 | Patient value proposition / emergency limitation | Source contract inspected; actual staged / deployed acceptance UNVERIFIED |
| 2 | F14-FR-002 | Doctor global-clinic promise / zero commission | Source contract inspected; actual staged / deployed acceptance UNVERIFIED |
| 3 | F14-FR-003 | Primary CTAs to Files07/09 | Source contract inspected; actual staged / deployed acceptance UNVERIFIED |
| 4 | F14-FR-004 | Patient and doctor steps / owner transition | Source contract inspected; actual staged / deployed acceptance UNVERIFIED |
| 5 | F14-FR-005 | Verified trust and no treatment guarantee | Source contract inspected; actual staged / deployed acceptance UNVERIFIED |
| 6 | F14-FR-006 | Zero-commission/optional donation copy | Source contract inspected; actual staged / deployed acceptance UNVERIFIED |
| 7 | F14-FR-007 | File20 sole placement/route owner | R60-D01: owner route/audience/priority/schedule metadata may not trigger restoration gate; #22 OPEN |
| 8 | F14-FR-008 | File25 visual consumer, File14 content owner | Source contract inspected; actual staged / deployed acceptance UNVERIFIED |
| 9 | F14-FR-009 | File07/08/09 destination dependency health | Source contract inspected; actual staged / deployed acceptance UNVERIFIED |
| 10 | F14-FR-010 | Attribution lawful consent/GPC/retention | Source contract inspected; actual staged / deployed acceptance UNVERIFIED |
| 11 | F14-FR-011 | Actual File07/08/09 owner evidence, not click inference | BLOCKED #19: signed File01 registry, post-commit owner attestations, real staging |
| 12 | F14-FR-012 | Founder approvals for experiments | R60-D02: approved placement baseline shape can omit required SQL fields and be treated as immutable evidence |
| 13 | F14-FR-013 | American English/Urdu/Arabic localization | Source contract inspected; actual staged / deployed acceptance UNVERIFIED |
| 14 | F14-FR-014 | FAQ policy links, version and freshness | Source contract inspected; actual staged / deployed acceptance UNVERIFIED |
| 15 | F14-FR-015 | Accessible semantic CTAs/mobile/low bandwidth | Source contract inspected; actual staged / deployed acceptance UNVERIFIED |
| 16 | F14-FR-016 | Numeric/source/claim governance | Source contract inspected; actual staged / deployed acceptance UNVERIFIED |
| 17 | F14-FUT-01 | Ethical explicit intent router | Source contract inspected; actual staged / deployed acceptance UNVERIFIED |
| 18 | F14-FUT-02 | Allowlisted doctor/clinic handoff | Source contract inspected; actual staged / deployed acceptance UNVERIFIED |
| 19 | F14-FUT-03 | Current public evidence drawer | Source contract inspected; actual staged / deployed acceptance UNVERIFIED |
| 20 | F14-FUT-04 | Claim freshness fail-close | Source contract inspected; actual staged / deployed acceptance UNVERIFIED |
| 21 | F14-FUT-05 | Zero-fee parity sentinel | Source contract inspected; actual staged / deployed acceptance UNVERIFIED |
| 22 | F14-FUT-06 | Founder-approved jurisdictional claims | Source contract inspected; actual staged / deployed acceptance UNVERIFIED |
| 23 | F14-FUT-07 | Semantic meaning diff | Source contract inspected; actual staged / deployed acceptance UNVERIFIED |
| 24 | F14-FUT-08 | Dark pattern governance | Source contract inspected; actual staged / deployed acceptance UNVERIFIED |
| 25 | F14-FUT-09 | Safe degraded destination failover | Source contract inspected; actual staged / deployed acceptance UNVERIFIED |
| 26 | F14-FUT-10 | Preview without cross-owner mutations | R60-D02: approved placement baseline shape can omit required SQL fields and be treated as immutable evidence |
| 27 | F14-FUT-11 | Measured conversion quality, not raw CTR | Source contract inspected; actual staged / deployed acceptance UNVERIFIED |
| 28 | F14-FUT-12 | Misleading-copy report lifecycle | Source contract inspected; actual staged / deployed acceptance UNVERIFIED |
| 29 | F14-FUT-13 | Consent-based friction aggregation | Source contract inspected; actual staged / deployed acceptance UNVERIFIED |
| 30 | F14-FUT-14 | Owner-evidenced funnel anomaly | Source contract inspected; actual staged / deployed acceptance UNVERIFIED |
| 31 | F14-FUT-15 | Minimum 10 privacy cohort and strict count types | Source contract inspected; actual staged / deployed acceptance UNVERIFIED |
| 32 | F14-FUT-16 | Experiment approved preflight | R60-D01: owner route/audience/priority/schedule metadata may not trigger restoration gate; #22 OPEN |
| 33 | F14-FUT-17 | Early stop + actual owner-authorized CAS restoration | R60-D01: owner route/audience/priority/schedule metadata may not trigger restoration gate; #22 OPEN |
| 34 | F14-FUT-18 | Aggregate-only FAQ suggestion | Source contract inspected; actual staged / deployed acceptance UNVERIFIED |
| 35 | F14-FUT-19 | Message/locale consistency graph | Source contract inspected; actual staged / deployed acceptance UNVERIFIED |
| 36 | F14-FUT-20 | Translation provenance and term lock | Source contract inspected; actual staged / deployed acceptance UNVERIFIED |
| 37 | F14-FUT-21 | AI drafting, no auto-publication | Source contract inspected; actual staged / deployed acceptance UNVERIFIED |
| 38 | F14-FUT-22 | Audited public trust change log | Source contract inspected; actual staged / deployed acceptance UNVERIFIED |
| 39 | F14-FUT-23 | Informational patient chooser | Source contract inspected; actual staged / deployed acceptance UNVERIFIED |
| 40 | F14-FUT-24 | Nonbinding doctor readiness self-check | Source contract inspected; actual staged / deployed acceptance UNVERIFIED |

## Confirmed local root causes BEFORE PHP edits
1. **R60-D01 (FR007/FUT16/17, File20/25 ownership):** `GCU_Future_Policy::experiment_requires_baseline_restore()` looks for `placement_key`, `slot_key`, `block_key`, `layout` etc. But File14's own File20-style placement snapshot fields include `route_key`, `audience`, `priority`, `status`, `starts_at`, `ends_at`, `public_id`, `row_version`. An experiment variant affecting `route_key`, `priority` or scheduling metadata can be misclassified as safe copy-only and pass `experiment_baseline_restoration_ready()` although no authorized File20/25 changed-placement CAS restore exists (#22). Fail closed on all explicitly modelled owner placement mutation field names, including nested array metadata, preserving legitimate copy-only variants.
2. **R60-D02 (FR012/FUT16/17, snapshot integrity):** `GCU_Experiment_Baseline::shape()` uses only `array_intersect_key` and does not require all declared placement evidence fields. `capture_approved()` accepts missing `row_version`, `route_key` or `status` in a row returned by SQL and stores a partial snapshot; `snapshot()` then only checks hash/experiment/actor and not complete field shape. `ready()` and `reconcile_after_stop()` may therefore treat consistent *partial* owner state as approved/no-op. Require complete canonical placement shape (keeping legitimate NULL start/end times), verified row-version/public UUID/key, explicit key↔snapshot congruence, and reject malformed legacy snapshot evidence before claiming ready/no-op. Do not perform any unauthorized cross-owner write.

## Remain explicitly unresolved
- GitHub Issue **#19 OPEN**: Founder-approved File01 signed owner event registry, File07/08/09 genuine postcommit producer attestation, legal consent/GPC/replay and deployed WP staging evidence missing. Never infer booking completion from clicks or requested appointments.
- GitHub Issue **#22 OPEN**: actual File20/25 approved changed-placement CAS restore, concurrency/collision/rollback and deployed schema parity missing. Deny mutating experiments by default; no invented owner-table writes.
- The automatically repeated forty 40-marker scans are *source* contract checks, not forty independent browser/E2E/DB acceptance runs.

## Required acceptance after this freeze
R60 patches only two approved File14 source defects; 40 direct PHP route/meta gating test vectors + 40 direct placement shape/snapshot integrity SQL mock vectors; exact candidate and merged-main PHP7.4/8.3 complete quality, 40 repeated full plan scans, 2 independent fresh reviews, deterministic ZIP/SHA256/SBOM and complete evidence ledger.

Repository HEAD (before patch): `b8304f6bbff2c1d0e63a3c0966bc8a7302bb5aca`; Deployed Version UNVERIFIED; DB Version UNVERIFIED; Migration State UNVERIFIED; Live Verification Status NOT RUN.
Exact deployed code ابھی unverified ہے؛ repository-based diagnosis provisional ہے۔
