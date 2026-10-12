# File14 R30 — Twenty fresh frozen source, governing-plan and companion-owner review lenses

**2026-10-12. Frozen exact GitHub main before any R30 code modification:** `605fcaa5b96d1a46efee65a309c0df8efd3e9fbe`, version **1.4.33**. All 20 independent source/specification/contract lenses were completed before the R30 implementation edits below. They are **NOT 20 installed/staging/live E2E tests**.

## Governing original sources

The directly user-uploaded `14-File-14-Global-Clinic-USP-and-Conversion-Integration-v2.0-Future-CTI-24-Amended-2026-08-10.docx` governs **F14-FR-001..016**, **F14-FUT-01..24**, particularly F14-FUT-10 and F14-FUT-16 preflight, F14-FUT-24 nonbinding Doctor Readiness Self-Check, verified multilingual/jurisdiction-safe identity, audit and strict native state. The user-uploaded `Sabri-Social-Homeopathy-Platform-Three-Central-Plans-Consolidated-Governing-Master-Plan-2026....docx` governs **CV-262** zero-trust role/object/state/purpose and **CV-265** audited state changes, exact-valid-input/owner boundaries and no silent semantics. Companion uploaded File07 Doctors Directory, File08 Clinic/Appointments amended, File09 Global Onboarding and File25 Public UI/RTL plans bound source-of-truth doctor and booking data elsewhere. File08 real AppointmentRequested/Confirmed/Completed owner-native events are not established by File14 click events; this owner dependency remains external.

The 71 transferred central CV IDs have a provisional source index at `docs/CENTRAL-CV-71-OWNER-CONSUMER-TRACE-v1.4.27-R23.md`, not 71 production feature sign-offs. Evidence compared with exact main File14 policy, future-intelligence, repository, capabilities, privacy, REST and companion adapters. Their presence proves File14 source declarations only, not companion deployed versions or WordPress runtime/DB state.

| # | Independent frozen source/plan/companion lens | Prepatch evidence |
|---:|---|---|
| 01 | F14-FR-001..016 core value/CTA/trust/localization scope | SOURCE LOCATED: 16 trace IDs and source protections; live acceptance unknown |
| 02 | F14-FUT-01..24 future addendum | SOURCE LOCATED: 24 source catalog labels; owner delivery not fully proven |
| 03 | Consolidated central CV-014..025/188..199/239..285 mapping | SOURCE LOCATED: 71 distinct source IDs with provisional owner/consumer trace |
| 04 | File00 object authorization and Founder policy | SOURCE LOCATED: native delegated capability adapters, current owner truth unverified |
| 05 | File01 current route/manifest/event registry | SOURCE LOCATED: adapter contracts; no installed schema proof |
| 06 | File07 doctor discovery/verified claims | SOURCE LOCATED: read-only owner handoff |
| 07 | File08 booking/appointment owner and F08-FR-009/014 | SOURCE LOCATED: owner boundary; real booking confirmation unverified |
| 08 | File09 verification/onboarding identity | SOURCE LOCATED: delegated owner boundary |
| 09 | File19 notification delivery | SOURCE LOCATED: producer contract |
| 10 | File20 unified shell and placement lifecycle | SOURCE LOCATED: placement/render contract |
| 11 | File24 privacy, GPC, no covert analytics | SOURCE LOCATED: consent and privacy safety gates |
| 12 | File25 locale/RTL/accessibility | SOURCE LOCATED: UI adapter, not browser acceptance |
| 13 | Claim freshness, approved provenance and expiry | SOURCE LOCATED: revalidation/freshness sentinel |
| 14 | Acquisition dedup/consent/cohort suppression | SOURCE LOCATED: canonical event identity and aggregation policy, not real appointments |
| 15 | F14-FUT-12 private correction workflow | SOURCE LOCATED: typed input, read/write authorization, final-state CAS/audit |
| 16 | CV-262 protected Future REST and governance reads | SOURCE LOCATED: R29 11 callbacks and private writer; tested source authorization, not deployed parity |
| 17 | Future record identity and status on explicit input | **LOCAL R30-D01:** `rest_record_write` allowed explicit text `record_key`, `status`, `locale`, `region` to be silently altered by sanitizers / default fallback; e.g. `ACTIVE!`→`active`, invalid status→`draft`, invalid region→`ZZ`, unsupported locale→en-US. That misattributes deliberate external editor commands to a different identity/state/jurisdiction and can affect audited data. Source input-type validator checked types only, not exact canonical values |
| 18 | F14-FUT-24 nonbinding Doctor Readiness JSON public self-check | **LOCAL R30-D02:** `rest_readiness` treated non-JSON-object data as empty checklist and unknown nested/invalid checklist answers as an ordinary successful 0/partial score. The score is nonbinding and cannot verify a real doctor, but a malformed supplied request must receive a controlled 400 rather than a misleading accepted assessment |
| 19 | Signed owner-confirmed destination/application/booking outcomes (File07/08/09 through File01) | **EXTERNAL OPEN #19:** no proven consented replay-protected, versioned owner-ack native events and real staging integration; File14 CTA ≠ booking |
| 20 | Founder-approved experiment previous-variant snapshot and staging rollback | **EXTERNAL OPEN #22:** owner-approved snapshot, atomic baseline restore/migration and staging backup-replay proof remain unavailable |

**Frozen audit result: 16 narrowly SOURCE LOCATED, 2 proven local request-semantics defects, 2 external owner/design dependencies.** Findings were frozen **before coding**. Source presence is not a 16/20 production-completion score.

## Single corrective batch, only after review #20

**R30-D01:** Enforce typed, bounded **canonical exact** `record_type`, `record_key`, `status`, approved module locales and 2-letter region on *explicit governance requests* before their sanitizing/DB/idempotency code. Invalid or conflicting original values return controlled HTTP400, not an unrelated default or another object's identity. Omitted optional fields retain deliberate defaults. Private system-seeded records keep existing dedicated internal path. Preserve allowed existing state enum `draft/suggested/review/active/superseded/rejected` and locale aliases where canonical policy explicitly allows them.

**R30-D02:** Public `rest_readiness` must require JSON object and known typed checkbox answers, reject nested/strings unrelated to boolean decisions and unknown keys as 400, while preserving omitted choices as incomplete, valid true/false/0/1 semantics and the original explicit `binding:false` and File09/00 real verification ownership. Do not issue verification through File14.

Add pure adversarial PHP tests for accepted/invalid exact fields, noncanonical keys, region, locale/status and readiness values, source regressions proving guards run before coercion. Exact PHP7.4/8.3, all historical regression/source suites, two fresh reviews and deterministic package/SBOM required on PR SHA then on fresh post-merge current main. Do **not** predeclare CI outcomes.

**Candidate version v1.4.34**; source Base DB `10005`, Future schema `1` unchanged. **No migration** or changes to companion source-of-truth data.

## Required separate live-reality matrix

**Repository HEAD frozen prepatch:** `605fcaa5b96d1a46efee65a309c0df8efd3e9fbe`. **Deployed Version and checksum:** unverified. **Actual DB Version/tables/rows:** unverified. **Migration state/wp_options:** unverified. **Live Verification Status:** not performed. No real WordPress environment, File08 owner DB or deployment checksum was inspected. Green source CI alone cannot close a production incident; source-to-live parity must be independently established before Staging/Live acceptance.

**Exact deployed code ابھی unverified ہے؛ repository-based diagnosis provisional ہے۔**
