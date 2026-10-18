# R55 — Frozen pre-code source/owner audit, 40 distinct FR/FUT lenses
Date: 2026-10-18. Exact GitHub main source HEAD: `43364ea2d417d787cc4843d4afcbed004b234834`; candidate before repair v1.4.57. Source only; deployment and DB not assumed.
Plans: amended File14 Future CTI-24; Three Central Governing Master Plans; File07 Doctors Directory, File08 Clinic, File09 Onboarding, File20 canonical shell/placement, File25 public visual layer.
Fresh cross-repository source heads: File07 `2f4a89707724fd2b9946600afe10ddab27ec3c2d`; File08 `33146c4c969209b37e606bd7a2d11ec723773426`; File09 `cfc5f781a766330314dc98c42abeca0eb7786eba`; File20 `8a4dbcaf4fef8e926b9b834ecfde16c21a0f00ca`; File25 `347a4ff4d4c233c5ea6cd82c7786ee5398ea9d1e`; File01 `adf6dbb9980a85f25f7cf4c2ee679b52703c2e71`. Each is REPOSITORY source truth only.

## Independent source-coverage lenses (not 40 separate live browser passes)
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

## New source-level safety gaps discovered BEFORE correction
**R55-D01 — FUT17/#22:** `GCU_Future_Policy::experiment_baseline_restoration_ready()` calls only `GCU_Experiment_Baseline::ready()` (snapshot+current no-drift) and tests pause option. For a placement-changing variant, the initial unchanged snapshot can make activation pass despite **no approved File20/25 owner-authorized CAS restore capability** for a later mutation. Snapshot capture does not prove restoration ability. Must require a distinct owner restore contract gate before approving or running placement-mutation experiments; default deny until approved, tested owner attestation. No direct cross-owner mutation.

**R55-D02 — FUT14/FUT17/#19:** File14 `GCU_Owner_Outcomes::observed_anomaly()` correctly reports SQL read failure as `severity='unverified'`, but `GCU_Fifth_Review_Hardening::transactional_early_stop_guard()` breaches only on `'high'`, allowing experimental continuation when owner-stage anomaly evidence is unreadable. Treat `unverified` anomaly as unsafe, not as healthy. Do not infer verified booking or application outcomes from browser clicks; signed File01 owner contract still missing.

## Retained external blockers, not asserted resolved
- Issue #19: File07 server render `ddd_after_directory` is NOT browser arrival; File08 `wca_appointment_requested_committed_v1` is post-commit request (not confirmed/completed); File09 `gdo_application_submitted` is post-commit submission, not verified doctor. File14 hook counts lack approved signed File01 registry/staging, so `owner_confirmation_contract_ready()` remains deliberately false. Owner-side real event registry / verified producer and consent/replay/reconciliation must be approved, tested and deployed before scoring unlock.
- Issue #22: File14 snapshot/no-drift audit is NOT a CAS mutation restore; unchanged baseline can legitimately yield verified no-op, changed state must remain paused/manual review until signed owner command available.
- Source codes and plan review do not establish WordPress active plugin, exact deployed SHA, database tables, wp_options migration, or real staging/live acceptance.

## Test and correction plan
After this fully completed freeze: apply R55-D01 + R55-D02 source fixes, 40 adversarial actual-policy variant gating tests and 40 independent mocked anomaly guard checks, run forty complete 40-source-contract iterations and full PHP 7.4/8.3 quality, two fresh review rounds, signed source package SHA/SBOM and new PR CI. Only merge on green, then exact-main retest. All staging/live findings remain separately unverified.

## Live-first truth record
Repository HEAD (pre-code): `43364ea2d417d787cc4843d4afcbed004b234834`. Deployed Version: UNVERIFIED. Actual DB Version: UNVERIFIED. Migration State: UNVERIFIED. Live Verification Status: NOT RUN.
“Exact deployed code ابھی unverified ہے؛ repository-based diagnosis provisional ہے۔”
