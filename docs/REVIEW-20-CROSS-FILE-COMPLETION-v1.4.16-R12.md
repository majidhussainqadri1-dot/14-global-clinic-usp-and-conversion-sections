# File14 R12 — Twenty independent source-plan checks and corrections (v1.4.16)

**Date:** 2026-10-11. **Frozen audited repository main:** `82acee7a1c0ae159c67cebf0b9748dc4dfd4c531` (plugin v1.4.15).

## Authority and scope
Compared the user-supplied File14 original plan and Future CTI24 amended master plan, including **F14-FUT-20** (protected English/Urdu/Arabic terminology lock) and **F14-FUT-24** (non-binding doctor applicant readiness), to the consolidated three central governing plans, File07/09/25 plan ownership, exact File14 source and current File01/07/08/09/20 source-contract snapshots. Source-only results are not proof of installed WordPress behavior; none of these source rounds checks live database/runtime.

All **20 distinct source/code lenses were completed before the R12 correction batch**. `SOURCE CHECK CLEAN` means a narrowly inspected source guard or mapping was present, *not* that the entire specification is production complete.

| Pass | Audit lens | Frozen baseline result | Evidence and limitation |
|---:|---|---|---|
| 1 | FR-001..016 functional requirement trace | SOURCE CHECK CLEAN | IDs present; plan/trace alone cannot prove acceptance |
| 2 | FUT-01..24 approved feature catalogue | SOURCE CHECK CLEAN | 24 ids mapped in policy/trace |
| 3 | Central business policy | SOURCE CHECK CLEAN | zero commission, approved-free/voluntary support parity |
| 4 | File00 native identity and permissions | SOURCE CHECK CLEAN | delegated assertion adapter |
| 5 | File01 registry | SOURCE CHECK CLEAN | canonical contract version probe |
| 6 | File07 doctors owner | SOURCE CHECK CLEAN | doctor directory availability contract |
| 7 | File08 clinic/appointment owner | SOURCE CHECK CLEAN | clinic data truth retained with owner |
| 8 | File09 onboarding owner | SOURCE CHECK CLEAN | verified application truth retained with owner |
| 9 | Files20/25 UX owner | SOURCE CHECK CLEAN | shell/visual integration |
| 10 | File24/GPC consent | SOURCE CHECK CLEAN | privacy and assurance gates |
| 11 | Founder-controlled public copy | SOURCE CHECK CLEAN | native approval gate |
| 12 | R11 report UUID replay provenance | SOURCE CHECK CLEAN | stored normalized identity conflict prevents false dedup |
| 13 | R11 governed seed preservation | SOURCE CHECK CLEAN | reviewed records survive schema repair |
| 14 | FUT-24 readiness self-check | DEFECT R12-D01 | truthy strings and arrays counted as checked due to empty() |
| 15 | FUT-20 protected terminology lock | DEFECT R12-D02 | subset of canonical term keys could be approved if each had three locales |
| 16 | Aggregate analytics / small cohorts | SOURCE CHECK CLEAN | owner-unverified metrics suppressed |
| 17 | F14-FR-011 native owner funnel evidence | OPEN R12-G01 | Issue #19: File07/08/09 event contract still absent |
| 18 | FUT-17 safe experiment baseline restore | OPEN R12-G02 | Issue #22: snapshot/restore contract still absent |
| 19 | Future physical schema verification | SOURCE CHECK CLEAN | runtime and physical DB-read gate; installed DB unverified |
| 20 | Exact-version pipeline and package | SOURCE CHECK CLEAN | PHP7.4/8.3 configured; new candidate QA gate pending |

Pre-correction: **16 narrow source checks clean; 2 File14 defects (passes 14 and 15); 2 outstanding cross-module requirements (passes 17 and 18)**.

## Correction batch after twenty passes

**R12-D01 / F14-FUT-24 (nonbinding doctor readiness):** `GCU_Future_Policy::doctor_readiness_check()` previously used PHP `!empty()` for attacker-supplied/public REST JSON. Strings `"false"`, `"no"`, `"yes"`, and arrays were truthy and incorrectly increased the readiness score. Restrict checkbox acceptance to actual boolean `true`, integer `1`, or checkbox-form string `"1"`. Everything else remains incomplete; the result continues to be explicitly non-binding with File09/File00 as verification owners. Pure regression tests cover forged values, checked values, missing keys, and nonbinding result metadata.

**R12-D02 / F14-FUT-20 (complete protected terminology):** `validate_public_record_payload('terminology_lock')` checked three-language completeness only for the keys supplied by the applicant/editor, so a record containing only a single protected term could be marked safe. Every key from the canonical `GCU_Future_Policy::terminology_lock()` must now be present; all required en-US/ur-PK/ar-SA variants for every included entry must continue to pass nonempty validation. Founder approval and provenance remain separate mandatory publication gates. New tests prove a complete set passes and missing `zero_commission`, `verified_doctor`, and a missing Urdu label fail. This preserves approved terminology without replacing Founder-reviewed text with a guessed translation.

Candidate plugin **1.4.16**; base and Future schema code targets unchanged (base `10005`, Future `1`). No database migration or deployment performed in this source change.

## Outstanding, do not invent completion
- **Issue #19 / R12-G01:** File07/08/09 native owner-attested consented versioned arrival/application/booking events + File01 registration. File14 clicks never equal success.
- **Issue #22 / R12-G02:** F14-FUT-17 audited experiment early-stop still lacks approved baseline placement/variant snapshot linkage and restore/reconciliation, migration/rollback and staging acceptance.

## Release truth
New candidate exact branch PHP7.4/PHP8.3, two fresh reviews, package/SBOM and post-merge exact-main QA must pass independently before declaring repository QA green. Exact deployed plugin checksum, installed DB/schema, wp_options/migrations, runtime logs, current configuration, staging restoration, Founder acceptance and live re-test remain unverified.

Repository HEAD (frozen prior main): `82acee7a1c0ae159c67cebf0b9748dc4dfd4c531`.
Deployed Version: **unverified**. DB Version: **unverified**. Migration State: **unverified**. Live Verification Status: **not performed**.

**Exact deployed code ابھی unverified ہے؛ repository-based diagnosis provisional ہے۔**
