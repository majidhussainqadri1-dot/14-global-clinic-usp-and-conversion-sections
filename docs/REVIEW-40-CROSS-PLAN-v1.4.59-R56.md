# R56 PRE-CODE audit freeze: forty amended requirement lenses (2026-10-18)

Source HEAD: `c4bb87ea6fcb80bf21d4b315acb0e90cd2c84488` (source plugin v1.4.58). Attached governing File14 v2.0 Future CTI24, Consolidated Three Central Governing Master Plans, File07, File09 and File25 companion plans; File08, File01, File20 GitHub current source contracts. Frozen before any runtime modification. GitHub != installed WordPress or actual schema.

## Forty separate source-vs-plan lenses

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
| 30 | F14-FUT-14 | Owner-evidenced funnel anomaly | SOURCE REVIEW: existing implementation/traceability; staging not proven |  // R56-D01 typed owner event/stage source evidence
| 31 | F14-FUT-15 | Minimum 10 privacy cohort and strict count types | Prior R53 source fixes accepted; R54 related FAQ bypass under FUT18|
| 32 | F14-FUT-16 | Experiment approved preflight | SOURCE REVIEW: existing implementation/traceability; staging not proven |
| 33 | F14-FUT-17 | Early stop + actual owner-authorized CAS restoration | BLOCKED #22: changed placement owner CAS restoration absent |  // R56-D02 experiment/snapshot approval linkage
| 34 | F14-FUT-18 | Aggregate-only FAQ suggestion | R54-D01: noncanonical FAQ COUNT accepted after integer coercion |
| 35 | F14-FUT-19 | Message/locale consistency graph | SOURCE REVIEW: existing implementation/traceability; staging not proven |
| 36 | F14-FUT-20 | Translation provenance and term lock | SOURCE REVIEW: existing implementation/traceability; staging not proven |
| 37 | F14-FUT-21 | AI drafting, no auto-publication | SOURCE REVIEW: existing implementation/traceability; staging not proven |
| 38 | F14-FUT-22 | Audited public trust change log | SOURCE REVIEW: existing implementation/traceability; staging not proven |
| 39 | F14-FUT-23 | Informational patient chooser | SOURCE REVIEW: existing implementation/traceability; staging not proven |
| 40 | F14-FUT-24 | Nonbinding doctor readiness self-check | SOURCE REVIEW: existing implementation/traceability; staging not proven |

These are forty distinct requirement scopes. The existing executable `scripts/review40-iterative.sh` repeats its 40-source-marker review 40 times; its source-marker passes are not 40 independent deployed E2E browser experiments.

## Proven local source gaps (before code)

1. **R56-D01 — F14-FUT-13/14/15; cross File01/07/08/09**. `GCU_Owner_Outcomes::owner_stage_summary()` verifies SQL result `COUNT(*)`, but neglects `is_string()`/nonblank validation of `event_type` and `funnel_stage`. A corrupted row with array/number/empty group identity is silently ignored by strict `reportable()`, producing success-looking *partial* aggregate response rather than 503 unverified evidence. Reject malformed group identity with privacy-neutral error, while continuing to intentionally exclude well-typed but unapproved event/stage combinations.
2. **R56-D02 — F14-FUT-17; File20/25 baseline ownership**. `GCU_Experiment_Baseline::reconcile_after_stop()` authenticates snapshot hash and UUID but does NOT re-confirm the approved experiment's row-version progression, approver identity or currently declared placement keys against the frozen approved snapshot before declaring `restored=true` on a no-drift path. Experiment metadata may change after snapshot, including an unknown concurrent source state. Return `restored=false`/manual-review/pause for version, approver, status or mapping mismatch. Do not write any File20/25 owner state; restore CAS still external blocker #22.

## Distinct cross-file blockers remain OPEN
- #19 File01 signed registry and producer-authenticated, consented File07/08/09 events; GitHub event hooks alone not deployed/staging acceptance.
- #22 Founder-approved File20/25 owner-authorized CAS changed-placement restoration command, snapshot mutation mapping and concurrency staging evidence; no invented foreign owner write.
- Need actual installed plugin/version/checksum; DB tables, `wp_options`, schema and migration state; deployed owner-parity, real-role staging, rollback/restore rehearsal, live re-test. Do not claim Production Resolved.

## Corrections and test acceptance
After frozen all-40 review: repair the two proven File14-only root causes; 40 direct owner SQL aggregate type cases, 40 real baseline-reconciliation cases with immutable snapshot, approved versions, role and mapping; exact PHP7.4/8.3 quality and 40 actual full 40-marker loops, independent Fresh Reviews, deterministic ZIP/checksum/SBOM on candidate and post-merge exact main. Do not enable #19 owner scoring or #22 placement mutation.

Repository HEAD: `c4bb87ea6fcb80bf21d4b315acb0e90cd2c84488`. Deployed Version: UNVERIFIED. DB Version: UNVERIFIED. Migration State: UNVERIFIED. Live Verification Status: NOT RUN.
Exact deployed code ابھی unverified ہے؛ repository-based diagnosis provisional ہے۔
