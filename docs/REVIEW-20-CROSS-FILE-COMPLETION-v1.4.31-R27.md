# File14 R27 — Twenty Fresh Frozen Code/Plan/Cross-Owner Review Lenses (v1.4.31 Candidate)

**Review date:** 2026-10-12 (Pakistan local). **Exact pre-correction GitHub main:** `3952dd6857106bfafa0ae67fb7b36f2a1566a650`, source v1.4.30. **All twenty independently scoped frozen-source reviews were completed before R27 edits.** These are twenty distinct source/requirements/adapter examinations, **not** 20 WordPress live/staging browser test executions.

## Actual controlling source and boundaries

User-uploaded File14 Global Clinic USP and Conversion Integration v2.0 (Future CTI24 Amended 2026-08-10) governs core F14-FR-001..016, the F14-FUT-01..24 addendum, and explicit **F14-FUT-12 [P0] — Misleading-Copy Report & Correction Loop**, including report, review, resolution and audit. The uploaded **Three Central Plans Consolidated Governing Master Plan** requires safe zero-trust on identity, role, object, purpose, state (**CV-262**), privileged read/write moderation audit (**CV-265**), and source/staging/live and fresh review separation. User-uploaded File07 Doctors Directory, File09 Doctor Onboarding, File25 Public UI establish companion owner boundaries; File00/01/07/08/09/19/20/24/25 interface names are read from frozen File14 adapter source, **not** proof of deployed versions/DB/contracts in those owner plugins. The 71 exact transferred CV IDs are separately indexed at `docs/CENTRAL-CV-71-OWNER-CONSUMER-TRACE-v1.4.27-R23.md`.

| # | Independent frozen source and governing-plan lens | Source-status before R27 edits |
|---:|---|---|
| 01 | File14 F14-FR-001..016 canonical scope and trace | SOURCE-LOCATED (16 IDs); no live acceptance claim |
| 02 | Future addendum F14-FUT-01..24 module/catalogue | SOURCE-LOCATED (24 IDs); source guard ≠ operational completeness |
| 03 | Three Central Plans and 71 transferred CV owner/consumer mapping | SOURCE-LOCATED R23 71-ID index; owner assignments not deployed proof |
| 04 | File00 role/Founder and delegated authorization purpose | SOURCE-LOCATED File00 adapter / GCU capabilities |
| 05 | File01 canonical manifest and current routes | SOURCE-LOCATED owner registry read adapters |
| 06 | File07 doctor-discovery authority | SOURCE-LOCATED, no parallel doctor registry |
| 07 | File08 appointment/clinic authority | SOURCE-LOCATED, no false booking success |
| 08 | File09 verification and doctor-onboarding authority | SOURCE-LOCATED native owner handoff |
| 09 | File19 notifications producer | SOURCE-LOCATED File19 integration surface |
| 10 | File20 unified shell and placements | SOURCE-LOCATED File20 interface |
| 11 | File24 GPC, purpose-limited measurement and consent | SOURCE-LOCATED privacy guards |
| 12 | File25 public accessible visual and RTL contract | SOURCE-LOCATED source adapter, browser evidence separate |
| 13 | Consent-sensitive acquisition identities and minimum cohort | SOURCE-LOCATED replay identity and cohort guard |
| 14 | Public trust claim freshness and approved revalidation | SOURCE-LOCATED owner evidence/freshness source guards |
| 15 | F14-FUT-12 public reporter input, sensitive-data prevention and replay identity | SOURCE-LOCATED typed report and sensitive-data guards |
| 16 | F14-FUT-12 native `resolve_report_record()` privilege and versioned audited CAS | SOURCE-LOCATED R25 native File00 permission/mutation check |
| 17 | F14-FUT-12, CV-262 native `reports()` and `review_queue_reports()` privilege on **data reads**, independently of UI | **LOCAL ROOT CAUSE R27-D01:** both public static PHP reader methods returned individual report text/count without rechecking File00 capability, even though R26 protected normal admin-page caller |
| 18 | CV-262, F14-FUT-12 directly invoked public REST callback privilege on **data reads**, independently of WP route dispatcher | **LOCAL ROOT CAUSE R27-D02:** `rest_reports()` relied on `permission_callback`, but had no native authorization check if invoked directly by other PHP code |
| 19 | File07/08/09 signed, consented owner-native destination/application/booking event evidence | **OPEN EXTERNAL #19**: no confirmed File01-registered, owner-attested real outcome and staging-proof producer |
| 20 | Founder-approved previous variant/placement experiment baseline restoration | **OPEN EXTERNAL #22**: no proven approved pre-experiment snapshot, safe restore/migration/staged rollback |

**Frozen total:** sixteen narrowly source-located safeguards; **two** locally owned zero-trust read-boundary defects (a common missing native-authorization root cause); **two** cross-owner/design gates. No R27 code change was made before the twentieth frozen lens was completed. Data read protection is particularly relevant under CV-262: hiding a UI control does not authorize another code path.

## One corrective phase after review 20

**R27-D01:** Add private `GCU_Future_Intelligence::require_report_read()` using **already approved File00** `GCU_Capabilities::MANAGE_CONTENT` with existing `future_intelligence_content` purpose. Apply it at entry to both public native `reports()` and `review_queue_reports()` methods, before any DB query. Unauthorized callers receive native `WP_Error(403)` rather than sensitive report rows. Existing queue status/pagination bound (`open` + `reviewing`, max 200), R25 state mutation CAS and R26 admin behavior are preserved.

**R27-D02:** Apply the same native File00 read guard at the start of `rest_reports()`, even when called directly outside the WordPress REST dispatcher. WP `permission_callback` remains as a redundant outer guard; no authorization is relaxed. No new File00 purpose invented.

**Regression tests:** standalone PHP allowed/denied capability spy, DB reader spy proving forbidden calls do **not** issue report SQL, a denied direct REST callback, permitted moderator filtered list, and source-level assertions covering all three read surfaces plus preservation of R26 queue semantics. PHP7.4/8.3, complete Source/Plan/cross-file checks, two fresh reviewers, deterministic ZIP/SBOM, exact PR and fresh **new merged main** tests required; do not predeclare CI green.

Candidate software **v1.4.31**, source schema Base `10005`, Future `1`, **no schema revision or migration**. No File07/08/09 or File01 source-of-truth state changed.

## Outstanding external/operational evidence

- **#19** File07/08/09 actual signed owner-generated, consented, versioned destination/application/booking outcomes and File01 registry/acknowledgement; a File14 CTA cannot prove a booking.
- **#22** Founder-approved baseline snapshot and atomic replacement/rollback of actual File20/14 variants/placements with staged backup/restore, migration checks and positive/negative E2E.
- Production-grade completion requires exact deployed plugin/version/hash, wp_options and migrations, actual DB tables/columns/rows, runtime errors, locale/RTL/mobile/a11y, backups and live re-test, separate from repository CI.

**Exact code-state matrix before corrections:** Repository HEAD `3952dd6857106bfafa0ae67fb7b36f2a1566a650`; Deployed Version **unverified**; DB Version **unverified**; Migration State **unverified**; Live Verification Status **not performed**.

**Exact deployed code ابھی unverified ہے؛ repository-based diagnosis provisional ہے۔**
