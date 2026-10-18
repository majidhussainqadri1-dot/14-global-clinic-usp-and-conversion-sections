# R58 — Forty distinct plan-lens PRE-CODE source evidence freeze (18 October 2026)

**Source GitHub main HEAD before any runtime change**: `68872368f562064036df52ed414b32570e6c4937`, plugin source version **1.4.60**. Word governing attachments directly checked: amended File14 v2.0 CTI24, Three Central Governing Master Plans v3.0, File09 onboarding v1.1, File25 visual integration; other canonical-owner contracts viewed in GitHub. Those plan documents distinguish *specified*, *packaged*, *staging-accepted* and *production-deployed* — therefore a passed repository test cannot prove deployed/live.

## Precisely forty plan-to-code lenses
| # | Approved requirement | Exact audited function or owner contract | Pre-patch status |
|---:|---|---|---|
| 1 | F14-FR-001 | Patient value proposition / emergency limitation | Existing source candidate/traceability; runtime and staging unverified |
| 2 | F14-FR-002 | Doctor global-clinic promise / zero commission | Existing source candidate/traceability; runtime and staging unverified |
| 3 | F14-FR-003 | Primary CTAs to Files07/09 | Existing source candidate/traceability; runtime and staging unverified |
| 4 | F14-FR-004 | Patient and doctor steps / owner transition | Existing source candidate/traceability; runtime and staging unverified |
| 5 | F14-FR-005 | Verified trust and no treatment guarantee | Existing source candidate/traceability; runtime and staging unverified |
| 6 | F14-FR-006 | Zero-commission/optional donation copy | Existing source candidate/traceability; runtime and staging unverified |
| 7 | F14-FR-007 | File20 sole placement/route owner | Existing source candidate/traceability; runtime and staging unverified |
| 8 | F14-FR-008 | File25 visual consumer, File14 content owner | Existing source candidate/traceability; runtime and staging unverified |
| 9 | F14-FR-009 | File07/08/09 destination dependency health | Existing source candidate/traceability; runtime and staging unverified |
| 10 | F14-FR-010 | Attribution lawful consent/GPC/retention | Existing source candidate/traceability; runtime and staging unverified |
| 11 | F14-FR-011 | Actual File07/08/09 owner evidence, not click inference | BLOCKED outside File14: signed File01 registry and real owner producer staging #19 |
| 12 | F14-FR-012 | Founder approvals for experiments | Existing source candidate/traceability; runtime and staging unverified |
| 13 | F14-FR-013 | American English/Urdu/Arabic localization | Existing source candidate/traceability; runtime and staging unverified |
| 14 | F14-FR-014 | FAQ policy links, version and freshness | Existing source candidate/traceability; runtime and staging unverified |
| 15 | F14-FR-015 | Accessible semantic CTAs/mobile/low bandwidth | Existing source candidate/traceability; runtime and staging unverified |
| 16 | F14-FR-016 | Numeric/source/claim governance | Existing source candidate/traceability; runtime and staging unverified |
| 17 | F14-FUT-01 | Ethical explicit intent router | Existing source candidate/traceability; runtime and staging unverified |
| 18 | F14-FUT-02 | Allowlisted doctor/clinic handoff | Existing source candidate/traceability; runtime and staging unverified |
| 19 | F14-FUT-03 | Current public evidence drawer | Existing source candidate/traceability; runtime and staging unverified |
| 20 | F14-FUT-04 | Claim freshness fail-close | Existing source candidate/traceability; runtime and staging unverified |
| 21 | F14-FUT-05 | Zero-fee parity sentinel | Existing source candidate/traceability; runtime and staging unverified |
| 22 | F14-FUT-06 | Founder-approved jurisdictional claims | Existing source candidate/traceability; runtime and staging unverified |
| 23 | F14-FUT-07 | Semantic meaning diff | Existing source candidate/traceability; runtime and staging unverified |
| 24 | F14-FUT-08 | Dark pattern governance | Existing source candidate/traceability; runtime and staging unverified |
| 25 | F14-FUT-09 | Safe degraded destination failover | Existing source candidate/traceability; runtime and staging unverified |
| 26 | F14-FUT-10 | Preview without cross-owner mutations | Existing source candidate/traceability; runtime and staging unverified |
| 27 | F14-FUT-11 | Measured conversion quality, not raw CTR | Existing source candidate/traceability; runtime and staging unverified |
| 28 | F14-FUT-12 | Misleading-copy report lifecycle | Existing source candidate/traceability; runtime and staging unverified |
| 29 | F14-FUT-13 | Consent-based friction aggregation | Earlier R53-R57 source fixes present; no claim of staging acceptance |
| 30 | F14-FUT-14 | Owner-evidenced funnel anomaly | R58-D02 local malformed owner anomaly SQL identity; external #19 still open |
| 31 | F14-FUT-15 | Minimum 10 privacy cohort and strict count types | Earlier R53-R57 source fixes present; no claim of staging acceptance |
| 32 | F14-FUT-16 | Experiment approved preflight | R58-D01 local cast-based approved baseline readiness bypass |
| 33 | F14-FUT-17 | Early stop + actual owner-authorized CAS restoration | BLOCKED outside File14: File20/25 owner CAS restoration and staging #22 |
| 34 | F14-FUT-18 | Aggregate-only FAQ suggestion | Earlier R53-R57 source fixes present; no claim of staging acceptance |
| 35 | F14-FUT-19 | Message/locale consistency graph | Existing source candidate/traceability; runtime and staging unverified |
| 36 | F14-FUT-20 | Translation provenance and term lock | Existing source candidate/traceability; runtime and staging unverified |
| 37 | F14-FUT-21 | AI drafting, no auto-publication | Existing source candidate/traceability; runtime and staging unverified |
| 38 | F14-FUT-22 | Audited public trust change log | Existing source candidate/traceability; runtime and staging unverified |
| 39 | F14-FUT-23 | Informational patient chooser | Existing source candidate/traceability; runtime and staging unverified |
| 40 | F14-FUT-24 | Nonbinding doctor readiness self-check | Existing source candidate/traceability; runtime and staging unverified |

## R58 source findings frozen before patching
**R58-D01, FR012 / FUT16/17 (experiment baseline approval proof):** `GCU_Experiment_Baseline::ready()` accepts `(int) row_version` and `(int) approved_by` instead of first validating original canonical integer/DB numeric-string. Therefore `'12junk'`, `12.5`, `'007'`, boolean or numeric scientific variants may pass an approved snapshot's identity comparison when cast. `snapshot()` itself also casts `created_by` and `approved_by` without strict proof. Fail closed at read time on noncanonical snapshot/DB approved identities and current experiment version/approver; preserve intact valid numeric SQL strings. Foreign owner placement table untouched. Note R55 still denies placement-changing experiments altogether pending owner CAS.
**R58-D02, FR011/FUT14/15 (independent native owner anomaly counts):** `GCU_Owner_Outcomes::observed_anomaly()` checks `is_string(event_type)` and `is_string(funnel_stage)` but not non-empty/non-whitespace strings. A corrupt row with empty typed identity falls through `self::reportable()` and returns apparent healthy or insufficient-owner-evidence rather than `severity=unverified`. Match R56's typed/nonblank owner summary guard; return privacy-suppressed evidence error and let R55's early-stop safety gate treat it as unsafe.

## Cross-owner blockers and deployed truth
- Issue #19 remains **OPEN** until signed File01 registry, real post-commit File07/08/09 owner attestations, consent/GPC/replay, actual deployed/staging agreement. Source hooks != booking or verification completed.
- Issue #22 remains **OPEN** until versioned File20/25 owner-authorized *changed-placement* CAS restoration contract and staged concurrency rollback. File14 may only pause safely; no foreign owner writes.
- File01/07/08/09/20/25 repository source is independent of exact installed WP code. Prior source-head context from last cross-owner review: File01 adf6dbb9980a85f25f7cf4c2ee679b52703c2e71; File07 2f4a89707724fd2b9946600afe10ddab27ec3c2d; File08 33146c4c969209b37e606bd7a2d11ec723773426; File09 cfc5f781a766330314dc98c42abeca0eb7786eba; File20 8a4dbcaf4fef8e926b9b834ecfde16c21a0f00ca; File25 347a4ff4d4c233c5ea6cd82c7786ee5398ea9d1e. This ledger **does not certify these previous head values as current**.
- No connected WordPress site. Actual active plugin/ZIP hash, DB schema/options/migrations, runtime logs, staged roles and live re-test cannot be verified.

## Correction and acceptance sequence
Freeze these forty requirement lenses first (this commit), then patch both established File14 source defects as a single remediation batch. Add forty direct native owner anomaly method SQL/WordPress mock cases and forty actual approved baseline readiness/snapshot cases. Run exact candidate PHP7.4/8.3 and 40 whole-source-plan repeated scans, both Fresh Reviews and deterministic ZIP/SHA/SBOM; only after green exact-head PR merge, repeat CI on new main. Do not count 40-marker loops as 40 independently deployed browser checks.

**Truth fields BEFORE source edits:** Repository HEAD `68872368f562064036df52ed414b32570e6c4937`; Deployed Version UNVERIFIED; DB Version UNVERIFIED; Migration State UNVERIFIED; Live Verification Status NOT RUN.
Exact deployed code ابھی unverified ہے؛ repository-based diagnosis provisional ہے۔
