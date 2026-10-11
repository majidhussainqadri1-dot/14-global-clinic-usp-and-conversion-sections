# File14 R18 — Twenty exact-source/plan/companion contract reviews (v1.4.22)

**Date:** 2026-10-11. **Frozen pre-correction main HEAD:** `223d4dbe4f1dd3e18c1e2d727b133155ec2a0203`, repository version `1.4.21`. R17 PR #30 and its independent exact-main PHP7.4/8.3 and fresh-review CI were confirmed green **before** starting R18. Neither code branch nor plan proves installed Live WordPress parity.

## Governing evidence and review boundaries

Read the user-provided **File14 Global Clinic USP and Conversion Integration v2.0 Future CTI24 amended master plan** (2026-08-10), its F14-FUT-03 approved trust proof, F14-FUT-13 privacy-safe aggregate friction analytics, F14-FUT-22 governed public/admin change-log and records boundaries, explicit API/event rules demanding server-side validation; the user-provided **Three Central Plans Consolidated Governing Master Plan**, and relevant File07/File09/File25 owner plans. Compared against exact frozen File14 GitHub repository source and explicit File01/07/08/09/20 repository HEAD and File14 companion adapters. Checked companion HEAD snapshots (File01 `adf6dbb9980a85f25f7cf4c2ee679b52703c2e71`, File07 `2f4a89707724fd2b9946600afe10ddab27ec3c2d`, File08 `70541974ce0ffb16aebef557c3016eb7447662f4`, File09 `cfc5f781a766330314dc98c42abeca0eb7786eba`, File20 `8a4dbcaf4fef8e926b9b834ecfde16c21a0f00ca`). No installed WordPress DB/runtime source fetched.

Twenty **distinct static source / requirement / owner-contract check lenses** were completed **before any R18 source edits**. “SOURCE-GUARD CLEAN” means the named source predicate exists; it is not a statement that entire feature has passed installed staging or live acceptance. No invented staging runs.

| Round | Focus | Pre-correction result | Evidence scope |
|---:|---|---|---|
| 1 | F14-FR-001..016 baseline mapping | SOURCE-GUARD CLEAN | All 16 trace IDs present; not full acceptance proof |
| 2 | F14-FUT-01..24 approved CTI roadmap | SOURCE-GUARD CLEAN | All 24 IDs present in source catalog |
| 3 | Single free tier, no commission or donation advantages | SOURCE-GUARD CLEAN | Current central business policy source sentinel |
| 4 | File00 identity/capabilities | SOURCE-GUARD CLEAN | Delegated identity/permission boundary |
| 5 | File01 versioned routes and manifest | SOURCE-GUARD CLEAN | Canonical route registry is consumed |
| 6 | File07 doctors directory | SOURCE-GUARD CLEAN | No File14 physician-search owner |
| 7 | File08 appointments | SOURCE-GUARD CLEAN | No File14 booking-state owner |
| 8 | File09 verified doctor onboarding | SOURCE-GUARD CLEAN | Owner verification not re-created |
| 9 | Files20/25 shell and visual contract | SOURCE-GUARD CLEAN | Native shell and public UI boundary |
| 10 | File24 consent/GPC | SOURCE-GUARD CLEAN | Privacy assurance/consent gates |
| 11 | FUT01/02 explicit patient/doctor/learn and typed handoff | SOURCE-GUARD CLEAN | R13/R14 source tests guard typed destinations |
| 12 | FUT03/04 approved and fresh trust claims | SOURCE-GUARD CLEAN | Claim freshness and reviewed source checks present |
| 13 | FUT12 report UUID idempotency | SOURCE-GUARD CLEAN | R11 stored report replay identity checks |
| 14 | FUT11/15 numeric quality and cohort guard | SOURCE-GUARD CLEAN | R17 measured privacy and minimum cohort checks |
| 15 | FUT21 AI draft grounding and typed source | SOURCE-GUARD CLEAN | R17 grounded draft with no autopublish |
| 16 | FUT03 Public Trust REST lookup parameter type | DEFECT R18-D01 | Request claim_key passed directly to sanitize_key before scalar validation; WP request may prefer body/query parameter over URL route |
| 17 | FUT22 Editor record/report list query, pagination and filters | DEFECT R18-D02 | type/status cast to string, limits/cursors coerced by absint; nested/negative values can return incorrect list |
| 18 | FUT13 Privacy-Safe Friction Analytics window query | DEFECT R18-D03 | days cast through absint; malformed/negative values can result in unsupported query window |
| 19 | Owner-attested File07/08/09 outcome facts | OPEN R18-G01 | Issue #19 lacks owner-native consented, versioned outcome events and File01 registration |
| 20 | FUT17 stopped experiment safe baseline restore | OPEN R18-G02 | Issue #22 no approved canonical baseline snapshot/restoration and staging rollback proof |

**Count frozen before corrections:** 15 narrowly source-clean, **three File14 code defects** (rounds 16, 17, 18), **two unresolved owner/design gaps** (rounds 19, 20).

## One consolidated corrective batch only after all twenty reviews

**R18-D01 / F14-FUT-03 — typed public trust claim lookup:** `rest_trust` accepted `$request['claim_key']` into WordPress `sanitize_key` without checking source type. A WordPress REST request's JSON/form/query parameters can override route names. Nested Array/Object must not be coerced to string, produce warnings, or turn into a different public claim. New pure `GCU_Future_Policy::safe_public_claim_key` accepts only exact lowercase canonical-shaped scalar key; malformed lookup returns sanitized `400 gcu_future_claim_query_invalid` **before claim repository access**. The owner-reviewed freshness/public eligibility still gates valid keys. Pure regressions cover valid and malformed keys.

**R18-D02 / F14-FUT-22 — governed record/report read pagination:** Admin listing methods `rest_records` and `rest_reports` previously cast arbitrary type/status filter shapes to string and converted negative limit/cursor with `absint`, potentially masking malformed filters as an unfiltered or wrong-period list. New pure `validate_future_read_query` checks exact string filter shapes, approved report status, nonnegative cursor, positive limit, numerical length and bounds. Bad queries return field-name-only `400 gcu_future_records_query_invalid` or `gcu_future_reports_query_invalid`. Valid requests retain default first-page and max 100 results; no native owner data written or disclosed by change. Tests include authorized normal filters, malformed arrays, object, unknown status, negative cursor and bounded large limits.

**R18-D03 / F14-FUT-13 — privacy-safe time-window parsing:** Friction analytics `days` used `absint` on malformed/non-scalar/negative input and silently interpreted it as a valid 1..90-day interval. New bounded nonnegative integer parser validates scalar shape before deriving the period; invalid input returns sanitized `400 gcu_future_friction_window_invalid`. Missing input retains 30 days, valid long window safely caps at 90. All privacy/minimum-cohort and owner-stage evidence suppression remains in `friction_summary`.

**Candidate:** v1.4.22, no schema migration or change to source DB schema targets (base `10005`, Future `1`). New regression: `tests/future-intelligence-tests.php`. Exact source version must pass PHP7.4 and PHP8.3, Fresh Reviews 1/2, deterministic package/SBOM; independent PR and post-merge exact-main CI required.

## What remains outside File14's proven ownership

- **Issue #19 / R18-G01**: File07/08/09 native owner-acknowledged, versioned, consented and idempotent arrival/application/booking events and File01 schema/event registry are not established. File14 CTA clicks must not be represented as real appointments.
- **Issue #22 / R18-G02**: F14-FUT-17 approved pre-stop experiment variant/placement baseline snapshot with owner authorization, atomic restoration, idempotency, rollback and staging tests is still absent. A stopped experiment does NOT mean baseline was restored.

## Strict Live-First Exact-Deployed-State discipline

Only repository source was auditable this run. Real active plugin/version/package checksum, actual WordPress database tables/schema/rows, `wp_options`, migration history, runtime PHP logs, config/dependencies and staging/live browser tests were **not obtained**. Never infer deployed parity or production resolution from GitHub CI green.

Repository HEAD (frozen audited baseline): `223d4dbe4f1dd3e18c1e2d727b133155ec2a0203`.
Deployed Version: **unverified**.
DB Version: **unverified**.
Migration State: **unverified**.
Live Verification Status: **not performed**.

**Exact deployed code ابھی unverified ہے؛ repository-based diagnosis provisional ہے۔**
