# File14 R13 — Twenty independent source/plan and companion contract checks (v1.4.17)

**Date:** 2026-10-11. **Frozen exact main source**: `b0524faaa60c5324de4da43e6c813370c4f75084` (v1.4.16). No actual installed WordPress files or DB were obtained.

## Primary sources and scope

Read the supplied **File14 Global Clinic USP and Conversion Integration v2.0 Future CTI24 amended master plan** (especially F14-FUT-02 safe handoff, F14-FUT-06 approved jurisdiction-specific vs generic disclosures, F14-FUT-11/13 privacy/evidence, F14-FUT-17 rollback), the consolidated three-central governing master plan, related File07/09/25 plans, exact File14 main source, and current owner-side File01/07/08/09/20 GitHub source snapshots. Repository source is separate from deployed code.

Twenty independent **static/code/contract review lenses** were completed **before** modifying File14. "SOURCE CHECK CLEAN" means a specific source structure was verified; it does **not** mean all operational acceptance tests or twenty separate WordPress staging tests passed.

| Pass | Review lens | Pre-correction classification | Direct source finding and limit |
|---:|---|---|---|
| 1 | FR-001..016 mapped | SOURCE CHECK CLEAN | Plan requirement IDs traced, runtime not proven |
| 2 | Future CTI-01..24 mapped | SOURCE CHECK CLEAN | 24 approved IDs exist in source catalogue |
| 3 | Central no-commission/free core | SOURCE CHECK CLEAN | Business parity sentinel |
| 4 | File00 identity and permissions | SOURCE CHECK CLEAN | Native assertions not duplicated |
| 5 | File01 routes and module registry | SOURCE CHECK CLEAN | Canonical versioned contract probe |
| 6 | File07 doctors search | SOURCE CHECK CLEAN | Owner directory availability |
| 7 | File08 clinic/appointments | SOURCE CHECK CLEAN | Canonical booking owner retained |
| 8 | File09 doctor onboarding | SOURCE CHECK CLEAN | Canonical verified applicant state retained |
| 9 | File20 and File25 public UX | SOURCE CHECK CLEAN | Global shell and visual owner contract |
| 10 | File24 privacy and GPC | SOURCE CHECK CLEAN | Consent and assurance guards |
| 11 | Claim freshness and expiry | SOURCE CHECK CLEAN | Public trust claim freshness sentinel |
| 12 | AI source-backed copy | SOURCE CHECK CLEAN | Controlled drafts, rate guard and review |
| 13 | Public context scalar allowlist | DEFECT R13-D01 | Array/object values cast to strings before validation, PHP warnings and untrusted routing |
| 14 | Jurisdiction approved generic fallback | DEFECT R13-D02 | Invalid specific regional record hides separately valid generic ZZ record because LIMIT 1 |
| 15 | Minimum cohort + funnel provenance | SOURCE CHECK CLEAN | No unauthenticated owner-stage success |
| 16 | Owner-origin File07/08/09 event confirmation | OPEN R13-G01 | Issue #19: approved versioned owner attestation missing |
| 17 | Approved baseline restore after early stop | OPEN R13-G02 | Issue #22: canonical experiment-baseline mapping absent |
| 18 | R12 readiness and terminology | SOURCE CHECK CLEAN | Explicit checkbox truth and required protected concepts |
| 19 | Schema readiness / migration guard | SOURCE CHECK CLEAN | Source reads physical tables; deployed DB not checked |
| 20 | Exact-source PHP matrix and packaging | SOURCE CHECK CLEAN | PHP 7.4/8.3 and reproducible package configured |

**Count:** 16 limited source-clean checks; **two File14-local defects** (rounds 13/14), **two pre-existing unimplemented cross-file/design requirements** (rounds 16/17).

## One consolidated correction stage after all twenty reviews

### R13-D01 — safe typed destination context and regional query input

The public `sanitize_handoff_context()` cast array/object fields to string when processing user-supplied JSON `country`, `language` and `mode`. A crafted array could generate a PHP warning and was not rejected at the type boundary. All three fields now require strings *before* WordPress sanitization and allowlist checks; unsupported shapes are reduced to empty, never inferred as a clinical, eligibility or destination decision. `GCU_Future_Intelligence::sanitize_region()` also rejects non-string GET/input without casting. Good country/locale/consultation mode values still survive. Regression tests include valid, object and array inputs.

### R13-D02 — approved generic jurisdiction disclosure cannot be masked by malformed regional legacy record

F14-FUT-06 requires only Founder-approved regional disclosures and truthful generic copy where no valid regional variant can be used. `jurisdiction_disclosure()` read the most-specific active row with `LIMIT 1` and validated *after* the DB query. A structurally malformed active region row therefore hid a separately valid `ZZ` generic disclosure. The query now retrieves at most two ordered records, checks each against the approved source/reviewer/provenance/effective-date content rules, and returns the **first validated** candidate. Both invalid returns empty; a valid specific region still wins over generic. No legal eligibility determination, unapproved replacement text or DB rewrite is introduced. Regression fixture covers all three outcomes plus a non-scalar country.

**R13 candidate:** software v1.4.17, unchanged code schema targets base `10005` / Future `1`; no DB schema migration in this patch. Tests: `tests/future-intelligence-tests.php`.

## Two known remaining gates — explicitly not completed

- **R13-G01, Issue #19**: Real versioned, consent-aware, authenticated File07/08/09 owner-event arrival/application/booking confirmations and File01 schema registration. A File14 CTA is not owner success.
- **R13-G02, Issue #22**: F14-FUT-17 approved safe experiment baseline variant/placement restoration requires canonical snapshot/versioning, migration, reconciliation, rollback and staging acceptance. Merely stopping an experiment is not equivalent to restoration.

## Release gates and live truth

PHP 7.4/8.3, two fresh review rounds, deterministic package SHA256/SBOM and post-merge exact-main CI are required separately. Only after live reality freeze (active plugin version, deployed artifact, DB/schema versions, relevant rows/options, migrations, runtime logs and dependencies) and live retest can an operational issue be marked resolved.

Repository HEAD: `b0524faaa60c5324de4da43e6c813370c4f75084` frozen audit baseline; candidate and final merge SHA to be recorded after checks.
Deployed Version: **unverified**.
DB Version: **unverified**.
Migration State: **unverified**.
Live Verification Status: **not performed**.

**Exact deployed code ابھی unverified ہے؛ repository-based diagnosis provisional ہے۔**
