# File 14 — R9 Twenty Independent Source/Contract Audit, v1.4.13

**2026-10-11. Frozen baseline main:** `ce098bc239cb8e761b92d0bb92302b3bf8758c07` (v1.4.12).

**Primary governing sources:** File 14 original plan and approved Future Conversion & Trust Intelligence 24 amendment; consolidated three central governing master plans; canonical related repo/contract snapshots. User-approved rule: a plan is not proof of coding; GitHub is not proof of installed/live behavior. Full runtime truth is a separate freeze.

**Companion source snapshot HEADs checked:** File01 `adf6dbb9980a85f25f7cf4c2ee679b52703c2e71`; File07 `2f4a89707724fd2b9946600afe10ddab27ec3c2d`; File08 `70541974ce0ffb16aebef557c3016eb7447662f4`; File09 `cfc5f781a766330314dc98c42abeca0eb7786eba`; File20 `8a4dbcaf4fef8e926b9b834ecfde16c21a0f00ca`. Source snapshots are not deployed artifact parity.

## All twenty source rounds — completed before source modification

| Pass | Independent focus | Frozen baseline finding | Evidence limit |
|---:|---|---|---|
| 01 | File14 FR-001..016 requirements trace | SOURCE CHECK CLEAN | All 16 identifiers present; implementation not proved by IDs |
| 02 | Future CTI-01..24 scope trace/catalog | SOURCE CHECK CLEAN | All 24 approved future IDs mapped in source |
| 03 | Free/core zero commission/donor parity | SOURCE CHECK CLEAN | Canonical policy and parity sentinel found |
| 04 | File00 role/verification owner | SOURCE CHECK CLEAN | Native authorization adapters; no duplicate owner |
| 05 | File01 manifest/route contract registration | SOURCE CHECK CLEAN | Versioned registry read probe |
| 06 | File07 doctor discovery boundary | SOURCE CHECK CLEAN | Owner destination availability probe |
| 07 | File08 clinic/booking boundary | SOURCE CHECK CLEAN | Owner destination availability probe; no duplicate appointment |
| 08 | File09 verified onboarding/outcome event contract | GAP R9-G01 | Actual owner-signed lifecycle acknowledgements unavailable, Issue #19 |
| 09 | File20 shell/File25 visual ownership | SOURCE CHECK CLEAN | Canonical shell and visual adapters |
| 10 | File24 security/privacy assurance | SOURCE CHECK CLEAN | GPC gate and assurance manifest |
| 11 | Browser/internal stage spoofing | SOURCE CHECK CLEAN | R8 repository and public route acquisition limits |
| 12 | REST capabilities | SOURCE CHECK CLEAN | Native callbacks enforce relevant read/write roles |
| 13 | Consent, GPC, retention and attribution | SOURCE CHECK CLEAN | Measurement opt-in, browser-artifact purge |
| 14 | Record idempotency/provenance | SOURCE CHECK CLEAN | Record command fingerprints and governance evidence |
| 15 | Claim freshness/copy policy | SOURCE CHECK CLEAN | Sentinel and source-grounded provider copy checks |
| 16 | Funnel attribution truth / small cohorts | SOURCE CHECK CLEAN | Owner-unsupported stages excluded; min cohort |
| 17 | Unready/stale Future schema on DB-dependent REST reads | DEFECT R9-D01 | REST read functions accessed Future tables without runtime + physical schema verification |
| 18 | AI Copy Provider invocation control | DEFECT R9-D02 | Editor-facing provider endpoint lacked dedicated rate limit |
| 19 | en-US/ur-PK/ar-SA RTL/visual source hooks | SOURCE CHECK CLEAN | Structural language hooks only; real browser tests pending |
| 20 | PHP matrices/reproducible release pipeline | SOURCE CHECK CLEAN | CI configured; completion must be checked on exact new HEAD |

**Outcome before fixes:** 17 limited source checks clean; two File14-owned defects in rounds 17 and 18; one still-incomplete cross-file requirement in round 8. This is **20 source-oriented tests/readings**—not 20 full WordPress runtime trials. "Clean" means the noted source contract guard exists, **not** that every behavior was proven in staging.

## Consolidated correction after all twenty rounds

**R9-D01 (schema state):** Several private and public Future REST read handlers (quality, friction, published change log, copy/experiment preflight, records, reports, scenario, FAQ inference, consistency graph and AI copy input) accessed DB-backed Future views without checking `runtime_ready()` AND physical `verify_schema()` first. Version/options can be stale or a migration partial. Added a cached-per-PHP-request `rest_storage_ready()` barrier that first validates base/runtime state and then the actual Future tables/columns, returning only a sanitized 503 if unavailable—never SQL/table details to the caller. Fail-closed on 11 affected REST handlers. A degraded Future projection now leaves the canonical File14 public landing HTML intact rather than querying broken Future tables. No DB schema mutation, no automatic deployment.

**R9-D02 (provider load protection):** `rest_ai_copy()` had no provider-specific invocation throttle despite approved AI provider filter. Added DB-backed `consume_rate_limit('future-ai-copy', 12)` per authenticated actor per minute and fail-closed on store error before invoking a provider. Existing required capability, draft-only and semantic/provenance checks remain.

Regression source and policy tests were added to `tests/future-intelligence-tests.php` for schema barrier, 11 handler guards, sanitized response, canonical page fallback and AI rate. Release candidate **1.4.13**, unchanged schema **base 10005 / Future 1**.

## Not fixed by inventing external facts

**R9-G01 / Issue #19:** File07/08/09 canonical, authenticated and consented, versioned acknowledgement of destination arrival/application/booking events and File01 schema registration are still pending. File14 must not infer those outcomes from its own click metrics. Owner modules remain unmodified in this change. Staging integration requires owner-producer signatures, event deduplication, privacy review, real contracts and founder approval.

## Verification and release gates

Branch PHP7.4 + PHP8.3 QA, two fresh reviews, deterministic ZIP/SBOM and **post-merge exact-main re-test** must be observed after final source commit. No claim of Staging-Approved/Live-Deployed/Operational unless verified installed artifact checksum, DB version, migrations, active config, actual WordPress logs and real journeys are frozen independently.

- Repository HEAD: frozen baseline `ce098bc239cb8e761b92d0bb92302b3bf8758c07`; branch candidate SHA must be recorded after final CI.
- Deployed Version: **unverified**.
- DB Version: **unverified** (10005/1 are planned targets, not observed DB).
- Migration State: **unverified**.
- Live Verification Status: **not performed**.

**Exact deployed code ابھی unverified ہے؛ repository-based diagnosis provisional ہے۔**
