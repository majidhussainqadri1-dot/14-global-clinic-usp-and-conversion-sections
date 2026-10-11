# File 14 — R15 Twenty Distinct Source/Contract Audits and Corrective Batch (v1.4.19)

Date: 2026-10-11 Asia/Karachi. **Frozen pre-change File14 repository HEAD:** `191e09ddbf41936619f85fcfd76456cc33c09630` (1.4.18). This SHA is *repository truth only*, not installed code.

## Primary governing sources

User-provided File14 original/master v2.0 Future CTI24 amended plan dated 2026-08-10, including ethical experiment preflight/guardrails, Founder-controlled copy and governance record publication; user-provided three-central consolidated governing master plan; File07, File09, File25 plans; File14 exact source/requirements register, and checked File01/07/08/09/20 exact GitHub main contract snapshots (also repository truth). Companion main source SHAs: File01 `adf6dbb9980a85f25f7cf4c2ee679b52703c2e71`, File07 `2f4a89707724fd2b9946600afe10ddab27ec3c2d`, File08 `70541974ce0ffb16aebef557c3016eb7447662f4`, File09 `cfc5f781a766330314dc98c42abeca0eb7786eba`, File20 `8a4dbcaf4fef8e926b9b834ecfde16c21a0f00ca`. No deployed source or actual database fetched.

Twenty **distinct static code/plan/owner-contract review lenses** were completed *before* R15 modifications. This is **not twenty independent WordPress staging or browser test runs**. 'Clean' applies only to the named predicate/guard; do not interpret it as blanket functionality complete.

## Frozen results across all 20 reviews

| Round | Lens | Source classification before fixes | Direct evidence boundary |
|---:|---|---|---|
| 1 | Base functional requirements F14-FR-001..016 | LIMITED SOURCE CHECK CLEAN | Sixteen mapped IDs, not proof of 16 operational workflows |
| 2 | Future CTI01..24 planned scope | LIMITED SOURCE CHECK CLEAN | All 24 feature IDs present in source catalogue |
| 3 | Central zero commission / free core | LIMITED SOURCE CHECK CLEAN | Central business parity source sentinel |
| 4 | File00 authentication/roles owner | LIMITED SOURCE CHECK CLEAN | Native identity adapter only |
| 5 | File01 manifest and canonical routes | LIMITED SOURCE CHECK CLEAN | Versioned registry contract guards |
| 6 | File07 doctors directory owner | LIMITED SOURCE CHECK CLEAN | File14 delegates discovery state |
| 7 | File08 appointments owner | LIMITED SOURCE CHECK CLEAN | Booking confirmation not inferred from click |
| 8 | File09 onboarding/verification owner | LIMITED SOURCE CHECK CLEAN | No File14-owned doctor verification |
| 9 | Files20/25 shell and visual ownership | LIMITED SOURCE CHECK CLEAN | Canonical host and UI boundaries |
| 10 | File24 GPC, consent, privacy | LIMITED SOURCE CHECK CLEAN | Consent and assurance source probes |
| 11 | Native owner-confirmed conversion events | OPEN R15-G01 | Issue #19: File07/08/09 signed versioned handoff/application/booking event protocol absent |
| 12 | Experiment early-stop baseline restore | OPEN R15-G02 | Issue #22: no approved placement/variant snapshot and reversible restore |
| 13 | Governed Future record REST scalar validation | DEFECT R15-D01 | Nested JSON record_type/key/locale/status/expected_version/payload/is_public can be cast or silently substituted before audited storage |
| 14 | Claim revalidation scalar/positive-version input | DEFECT R15-D02 | Nested reason cast to Array string; claim_key and version lacked strict REST shape checks |
| 15 | Experiment sample/privacy policy and variant type | DEFECT R15-D03 | Nested JSON sampling policy, privacy policy or variant coerced to text; preflight could return safe for unsupported structures |
| 16 | Founder-approved seed and content review | LIMITED SOURCE CHECK CLEAN | R11 seed-preservation and publication guard present |
| 17 | AI copy grounding and minimum cohort | LIMITED SOURCE CHECK CLEAN | Draft provenance and aggregated minimum sample guards |
| 18 | Public reviewed change-log pagination | LIMITED SOURCE CHECK CLEAN | R14 bounded source validation before display limit |
| 19 | Physical DB/schema/version-read safety | LIMITED SOURCE CHECK CLEAN | Runtime + physical schema checks; deployed DB not inspected |
| 20 | Exact PHP matrix/packaging automation | LIMITED SOURCE CHECK CLEAN | PHP7.4/8.3 and SBOM/ZIP configured; new exact SHA must be retested |

**Pre-correction classification:** fifteen narrowly source-clean; **three File14-owned defects** (rounds 13–15) and **two still-open cross-file/design requirements** (rounds 11–12).

## Consolidated post-review local corrections

- **R15-D01 — Future governed record-write REST type safety.** Before mutation/idempotent command, `rest_record_write` now calls `GCU_Future_Policy::validate_future_record_input_types`. Record type, record key, locale, region and status must be strings if supplied; payload must be an array; `expected_version` a nonnegative integer/digit-string (no `absint` of negative), and `is_public` an explicit boolean/0/1 or digit-string 0/1. Nested arrays/objects and arbitrary nonempty 'yes' cannot be coerced into editable public governance state. Returns sanitized 400 with offending field *names only* and does not call persistence. Existing audit, optimistic versioning and Founder approval remain unchanged.
- **R15-D02 — Canonical claim-revalidation input safety.** `rest_revalidate_claim` now checks claim identifier and substantive reason are strings and expected row version is explicitly positive integer/digit-string *before* `sanitize_key`, `sanitize_textarea_field`, `absint` and the audited command. Malformed nested claim/reason/version returns sanitized 400; valid existing versioned workflow remains. Claim revalidation cannot be made true by a nested value coalescing into 'Array'.
- **R15-D03 — Experiment ethical preflight data type safety.** `experiment_preflight` now marks nested/nonstring sampling/privacy policies unsafe (`invalid_sampling_policy_type`), refuses unsupported object/bool/number variant types, and fails closed if JSON variant encoding is invalid. Ordinary array-form or string variants and explicitly stated, approved text policy inputs still undergo dark-pattern/sensitive-sampling analysis. This **does not** activate experiments or imply that safe baseline restoration is implemented.

Pure regression additions: `tests/future-intelligence-tests.php`: typed governed record inputs for all seven relevant fields, negative versions, invalid public flags; claim version/claim/reason types; unsafe nested preflight policies and variants, plus existing valid controls. Version target **1.4.19**. Planned base schema `10005` and Future `1` unchanged; no DB migration in this update.

## Two unfinished acceptance contracts — deliberately untouched

**R15-G01 / Issue #19:** File07/08/09 authentic, consented and versioned owner-origin handoff/application/booking signals and File01 event registry. File14 must never report an owner booking from its own clicks.

**R15-G02 / Issue #22:** F14-FUT-17 approved experiment pre-stop baseline state, placement/variant snapshot linkage, safe idempotent restoration, migration/rollback and race-tested staging acceptance. Status `stopped` does not establish restoration.

## Independent evidence gates

Exact candidate branch PHP7.4/8.3 quality, Fresh Post-Code Reviews 1/2 and deterministic reproducible package/SBOM, then PR checks and new **post-merge exact-main** CI must pass separately. Production cannot be declared fixed without actual deployed plugin/runtime build/checksum, relevant DB tables/columns/rows, real `wp_options`/migrations, logs/config/backup, staging acceptance and real live retest.

Repository HEAD (frozen baseline): `191e09ddbf41936619f85fcfd76456cc33c09630`. Deployed Version: **unverified**. DB Version: **unverified**. Migration State: **unverified**. Live Verification Status: **not performed**.

**Exact deployed code ابھی unverified ہے؛ repository-based diagnosis provisional ہے۔**
