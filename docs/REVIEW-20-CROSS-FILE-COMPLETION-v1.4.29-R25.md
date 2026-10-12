# File14 — R25 twenty fresh current-source, central-plan, and companion-owner reviews

**Date:** 2026-10-12. **Frozen pre-correction repository main:** `53fa9071373afe1f338bdf1f1d8c0540e07caf54` (source release v1.4.28). **Reviews 01–20 completed BEFORE R25 edits.** This is twenty distinct source/requirements/interface questions, **not** twenty WordPress staging/browser/live runs or twenty independent functional acceptance test sessions.

## Actual governing plan basis

Read the attached **File14 Global Clinic USP and Conversion Integration v2.0, Future CTI-24 Amended 2026-08-10** DOCX and its exact source terminology. It governs F14-FR-001..016 and F14-FUT-01..24. Relevant: F14-FUT-12 (structured copy-quality report, hashed actor, review/resolution/audit, P0); cross-cutting requirement “every write REST action: server-side validation, capability checks and audit”; section 6 CV-262 (zero-trust on identity/role/object/purpose/state), CV-265 (audit of privileged moderation), CV-280 (two-review law), and canonical role table. Also read the uploaded **Sabri Social Homeopathy Three Central Plans Consolidated Governing Master Plan 2026** and File07 Doctors Directory, File09 Global Onboarding and File25 Public UI/Timeline documents. The companion adapters for File00/01/07/08/09/19/20/24/25 are File14 *interface-source evidence*, not verified installed companion code or actual DB state. References to File14 requirements and companion ownership must not be mistaken for operational acceptance.

### Twenty independent frozen lenses

| Lens | Frozen source versus plan / companion | Pre-correction finding |
|---:|---|---|
| 01 | F14-FR-001..016 core patient and doctor USP, CTA and evidence contracts | **SOURCE PRESENT**: 16 IDs traced; no browser acceptance asserted |
| 02 | F14-FUT-01..24 Future CTI catalogue and scoped functions | **SOURCE PRESENT**: 24 IDs and policy candidates; not all operating outcomes proven |
| 03 | Three-central-plan transferred 71 CV IDs | **SOURCE PRESENT**: earlier R23 source-based `docs/CENTRAL-CV-71-OWNER-CONSUMER-TRACE-v1.4.27-R23.md`; proposed owner mapping not deployed fact |
| 04 | File00 role/Founder approval and delegated capability boundary | **SOURCE PRESENT**: `GCU_Capabilities` / `GCU_Companion_Adapters` owner assertions |
| 05 | File01 canonical module version, route and event transport | **SOURCE PRESENT**: manifest/current registration adapters; no live registry inspection |
| 06 | File07 doctor discovery contract | **SOURCE PRESENT**: directory owner handoff; File14 does not own doctor registry |
| 07 | File08 clinic and booking authority | **SOURCE PRESENT**: owner route readiness; File14 cannot call clicks successful appointments |
| 08 | File09 onboarding/verification authority | **SOURCE PRESENT**: dedicated owner handoff; no File14 self-verification |
| 09 | File19 notification ownership and queue integration | **SOURCE PRESENT**: File19 producer adapters; queue staging not checked |
| 10 | File20 sole global shell/navigation and approved placement | **SOURCE PRESENT**: canonical shell source contract; runtime parity unverified |
| 11 | File24 privacy/security/assurance, GPC & nonprofiling | **SOURCE PRESENT**: source-level consent and privacy gates |
| 12 | File25 public visual, accessibility and RTL | **SOURCE PRESENT**: module UI contract & language keys; browser/AT testing pending |
| 13 | F14-FUT-03/04 trust evidence and freshness/revalidation | **SOURCE PRESENT**: claim approval/freshness source checks |
| 14 | F14-FUT-13/15 consent, small-cohort suppression | **SOURCE PRESENT**: native measurement and aggregation gates |
| 15 | F14-FUT-12 public reporter input, actor hashing and audit | **SOURCE PRESENT**: public typed report, duplicate-identity test, audit/outbox; privileged moderation rechecked separately below |
| 16 | F14-FUT-12 moderator direct state mutation privilege under CV-262 | **R25-D01 CONFIRMED LOCAL DEFECT**: `GCU_Future_Intelligence::resolve_report_record` had no direct native File00 capability guard, relying on its current admin-post caller. Future/internal direct callers could bypass the caller-specific gate |
| 17 | F14-FUT-12 moderation optimistic-version exact identity | **R25-D02 CONFIRMED LOCAL DEFECT**: admin-post `absint($_POST['expected_version'])` silently converted negative numbers; native moderation cast/comparison permitted malformed representations. Direct method lacked up-front typed UUID/version/status/reason validation |
| 18 | F14-FUT-12 admin correction visibility / evidence honesty | **R25-D03 CONFIRMED LOCAL DEFECT**: `resolve_report()` discarded the `WP_Error` from the audited transaction and always redirected to the same UI without reporting failure; moderators could mistakenly assume update succeeded |
| 19 | F14-FR-011 actual owner-attested destination/application/booking events | **EXTERNAL OPEN Issue #19**: signed, versioned, consented owner-emitted events from Files07/08/09, registered by File01, and staged owner ack still unverified |
| 20 | F14-FUT-17 safe experiment stop AND previous baseline restoration | **EXTERNAL OPEN Issue #22**: no Founder-approved previous variant/placement snapshot, atomic restoration, DB migration/staging rollback proof |

**Frozen result:** 15 narrowly source-present; **three** source-provable File14-local defects; **two** missing external owner/design evidence gates. No implementation change before all twenty lenses ended. Source-presence is not end-to-end operational acceptance.

## Single corrective phase after all twenty lenses

1. **R25-D01 — native zero-trust:** `resolve_report_record` independently requires File00-delegated `GCU_Capabilities::MANAGE_CONTENT` with already established File14 content purpose `future_intelligence_content`. No new unauthorized File00 purpose inferred. The requirement is evaluated at the mutation, not just a visible form/nonce; unauthorized calls return 403 before reading/modifying the report DB.
2. **R25-D02 — strict optimistic concurrency:** reusable pure `GCU_Future_Policy::validate_report_resolution_input` validates UUID syntax, bounded positive `strict_mutation_version`, exact status enum and bounded scalar reason; native moderation checks both validator and WordPress UUID, uses validated version for row-version CAS and audited transaction. Admin-post passes raw unslashed submitted version (no `absint` conversion). Existing nonempty minimum resolution guard remains.
3. **R25-D03 — truthful moderator status:** admin-post captures actual `resolve_report_record` success/error and redirects with a bounded success/failed marker. Restricted admin page renders escaped success or generic failure notice (no sensitive report text/technical details in URL), retaining the existing audit/transactional state before success.

New tests: positive and adverse typed/version/status/identity cases (including negative, overflow, nested/boolean/float and excessive resolution) plus exact-source checks for native guard, consistent File00 purpose, uncoerced admin POST and meaningful success/failure UI. PHP 7.4 compatibility and current quality/release gates required.

**Candidate:** source v1.4.29. **No source schema change** (Base target 10005, Future target 1). File01/07/08/09 source of truth unaffected.

## Outstanding nonlocal and production prerequisites

- Issue #19: authentic File07/08/09 source-owner, versioned, signed, consented destination/application/booking confirmation through File01, exact companion HEAD/installed contract, staging and PII/consent tests.
- Issue #22: Founder-approved pre-experiment baseline snapshot/mapping, atomic restoration, migration and real staged backup/restore.
- Do not infer fixes from this repository to deployed WordPress. Require exact branch PR tests, two fresh reviews, deterministic artifact/SBOM, **post-merge** exact current main CI; then deployment parity freeze, real installed base/Future schema and wp_options, logs, browser/RTL/privacy/accessibility and Founder approval/staging/live re-test.

**Live reality matrix at code review:** Repository HEAD (frozen prepatch) `53fa9071373afe1f338bdf1f1d8c0540e07caf54`; Deployed Version **unverified**; DB Version **unverified**; Migration State **unverified**; Live Verification Status **not performed**. Later merge SHA and CI must be independently checked, not forecast here.

**Exact deployed code ابھی unverified ہے؛ repository-based diagnosis provisional ہے۔**
