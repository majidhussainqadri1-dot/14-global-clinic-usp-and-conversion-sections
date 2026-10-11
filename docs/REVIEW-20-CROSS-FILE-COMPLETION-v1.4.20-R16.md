# File14 R16 — Twenty source/plan/owner-contract review lenses (v1.4.20)

Date: 2026-10-11 (Asia/Karachi). Audited **frozen File14 repository main HEAD** `695dea3c727f7faf0dfb196edecb5a06e283e3c3` (plugin `1.4.19`). GitHub main is only SOURCE truth, not installed WordPress/live truth.

## Governing sources and boundaries

Examined the uploaded **File14 Global Clinic USP and Conversion Integration v2.0 Future CTI24 amended master plan** (especially FUT-11 measured conversion quality and FUT-16 experiment guardrails), the uploaded **consolidated three-central governing master plan**, relevant uploaded File07/09/25 plans, current File14 exact source and File01/07/08/09/20 repository contract snapshots. Owner source SHAs: File01 `adf6dbb9980a85f25f7cf4c2ee679b52703c2e71`, File07 `2f4a89707724fd2b9946600afe10ddab27ec3c2d`, File08 `70541974ce0ffb16aebef557c3016eb7447662f4`, File09 `cfc5f781a766330314dc98c42abeca0eb7786eba`, File20 `8a4dbcaf4fef8e926b9b834ecfde16c21a0f00ca`. Never presume their repository HEADs are installed package parity.

**Method:** finish twenty distinct static source/plan/contract review lenses before editing source, consolidate findings, only then modify File14. They are NOT twenty independently deployed staging WordPress instances or E2E acceptance journeys. SOURCE-GUARD CLEAN means only the named source predicate exists, not that the full referenced feature is ready for production.

## Frozen pre-correction results

| Round | Review area | Result at audited HEAD | What source evidence actually supports |
|---:|---|---|---|
| 1 | F14-FR-001..016 requirement trace | SOURCE-GUARD CLEAN | IDs and source trace exist; end-to-end workflows not proven |
| 2 | F14-FUT-01..24 amended feature catalog | SOURCE-GUARD CLEAN | All 24 approved IDs represented |
| 3 | Zero commission, free core and neutral donor policy | SOURCE-GUARD CLEAN | Business parity sentinel exists in File14 code |
| 4 | File00 identity/capability delegation | SOURCE-GUARD CLEAN | Native assertions/authorization adapters exist |
| 5 | File01 routes and versioned manifest | SOURCE-GUARD CLEAN | Canonical route/contract registry integration |
| 6 | File07 doctor directory ownership | SOURCE-GUARD CLEAN | Native owner destination availability probe |
| 7 | File08 clinic booking owner | SOURCE-GUARD CLEAN | No File14 canonical appointment state |
| 8 | File09 onboarding and verification owner | SOURCE-GUARD CLEAN | No File14 doctor verification state |
| 9 | File20 and File25 shell/visual ownership | SOURCE-GUARD CLEAN | Native host layout/visual interfaces |
| 10 | File24 consent/GPC privacy | SOURCE-GUARD CLEAN | Privacy/assurance native guard presence |
| 11 | Trust copy review/source freshness | SOURCE-GUARD CLEAN | Claim current-source/review-date checks exist |
| 12 | AI source vocabulary, human approval and provider throttle | SOURCE-GUARD CLEAN | Grounded draft-only pathway; AI not publish authority |
| 13 | Small-cohort privacy and owner-stage suppression | SOURCE-GUARD CLEAN | Missing owner events cannot become conversion success |
| 14 | F14-FUT-16 enabled safety guardrail values | DEFECT R16-D01 | Current source requires guardrail key presence but accepts false, null, empty and disabled control values |
| 15 | F14-FUT-16 copy/preflight type integrity | DEFECT R16-D02 | Current/prior title, body or CTA may be array/object, causing PHP cast/warning or unintended text processing |
| 16 | F14-FUT-11 measured quality evidence range | DEFECT R16-D03 | Owner-attested quality completeness checks is_numeric, but accept values below zero and above 100 |
| 17 | R15 governed record/revalidation input type safety | SOURCE-GUARD CLEAN | Typed REST input gate retained |
| 18 | R14 public change-log governed pagination | SOURCE-GUARD CLEAN | Reviewed provenance before limited display scan |
| 19 | F14-FR-011 owner-confirmed real outcomes | OPEN EXTERNAL R16-G01 | Issue #19: Files07/08/09 owner-native versioned attestation and File01 integration still absent |
| 20 | F14-FUT-17 approved experiment baseline restore | OPEN DESIGN R16-G02 | Issue #22: stopped experiment does not prove owner-approved snapshot/variant restoration |

**Count:** 20 independent static lenses; 15 source-guard clean, **three File14 defects** (rounds 14, 15, 16), and **two missing external/design contracts** (rounds 19, 20).

## One correction batch AFTER the 20 rounds

**R16-D01 — F14-FUT-16 effective experiment safety controls.** `GCU_Future_Policy::experiment_preflight` formerly checked the **presence** of five keys (claim_integrity, privacy, accessibility, error_rate, complaints), not whether the controls were enabled. False/empty/array/off values could pass as safe. The policy now treats false, blank, array/object or explicitly disabled settings as `invalid_guardrail:<key>`; true, descriptive non-disabled scalar values and nonnegative numeric thresholds are allowed pending the Founder-approved versioned experimental threshold schema. This is only a conservative preflight; not activation permission or a substitute for experiment rollback verification. Regression tests cover active privacy, disabled, null, object/array values, and an explicit zero error rate threshold.

**R16-D02 — F14-FUT-16 source-safe textual copy preflight.** `copy_preflight` previously interpolated untrusted nested title/body/cta_label values via `implode` or `wp_strip_all_tags`; PHP warnings and unsupported text meanings could slip into source-approval simulation. Now current/previous record fields are validated **before** filtering/HTML-stripping/semantic scan; only strings are processed. Nested fields trigger `invalid_copy_field_type:current|previous:<field>`, making safe=false; separate dark-pattern and semantic guards still operate on correctly typed source text. Regression tests validate unchanged truthful copy and all three malformed fields in both current and previous payload.

**R16-D03 — F14-FUT-11 range-checked quality evidence.** `quality_evidence_status` previously called `is_numeric` alone; negative accessibility and performance >100 could satisfy `complete=true` if owner evidence was claimed. Measured percentages now require numeric 0..100. Invalid ranges return the pre-existing explicit `*_measurement_unavailable` missing labels; owner-origin attestation gate and min cohort remain intact. Regression tests include 0/100 limits, -2, 120 and nested unknown performance.

Proposed plugin `1.4.20`, no schema migration; code target base `10005` and Future `1` are *source targets only*.

## Explicitly unfinished, no speculative repair

- **R16-G01 / Issue #19:** File07/08/09 canonical owner-attested and consent-aware destination loaded/application started/booking started event protocol (versioned, idempotent, File01 registered) is not yet implemented/accepted. File14 must not infer success from CTA clicks.
- **R16-G02 / Issue #22:** F14-FUT-17 safe approved baseline variant/placement snapshot and restoration after experiment early-stop is not proven by current stop/audit workflow. Requires canonical approved snapshot contract, migration/rollback/concurrency tests and staging.

## Release and deployed truth

Branch PHP 7.4/8.3 + two post-code fresh reviews + deterministic SHA256 package/SBOM, PR merge checks and independent **post-merge exact-main CI** are separate required release steps; record actual SHA/checks below only after they occur. Real deployed artifact/plugin version/checksum, relevant live DB tables/columns/rows, `wp_options`, migrations, runtime logs, dependencies/config, staged backup/restore, accessibility/responsive/RTL and live re-test have **not** been obtained.

Repository HEAD frozen baseline: `695dea3c727f7faf0dfb196edecb5a06e283e3c3`.
Deployed Version: **unverified**. DB Version: **unverified**. Migration State: **unverified**. Live Verification Status: **not performed**.

**Exact deployed code ابھی unverified ہے؛ repository-based diagnosis provisional ہے۔**
