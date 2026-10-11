# File 14 — R14 twenty independent source/contract checks, v1.4.18

**Date:** 2026-10-11. **Frozen audited File14 main:** `cfe8d9c51932014702967acc27c10d4382c4de0e` (v1.4.17).

## Primary governing sources and boundaries

Compared supplied File14 original/master amendment v2.0 with Future CTI 01–24, supplied consolidated three-central governing master plan, and File07/09/25 plans to actual File14 exact source and current File01/07/08/09/20 GitHub contract snapshots. Governing acceptance examples: **F14-FUT-01** requires *explicit* patient/doctor/learn selection (no hidden inference), **F14-FUT-22** requires *versioned, dated, reviewed* concise public policy/copy change log (no sensitive records), and the central plan requires privacy, Founder approval, and canonical owner boundaries. Repository code and planned requirements are not evidence of deployed runtime or DB version.

**Audit method:** Complete twenty distinct static source/plan lenses first; consolidate findings and only then edit. Not twenty WordPress or staging behavioral suites. Source-clean = only the stated code guard exists, NOT production feature completeness.

| Pass | Focus | Initial result | Actual source conclusion |
|---:|---|---|---|
| 1 | F14-FR-001..016 mapping | SOURCE-CLEAN (limited) | 16 base requirement identifiers mapped; static mapping not feature acceptance |
| 2 | F14-FUT-01..24 mapping | SOURCE-CLEAN (limited) | All 24 future IDs present in approved source/trace |
| 3 | Central free-tier, zero commission | SOURCE-CLEAN (limited) | Central copy business parity rule present |
| 4 | File00 native identity/capabilities | SOURCE-CLEAN (limited) | No File14-owned professional account authority |
| 5 | File01 versioned route registry | SOURCE-CLEAN (limited) | Canonical manifest/route adapter present |
| 6 | File07 doctor directory owner | SOURCE-CLEAN (limited) | Directory destination owner preserved |
| 7 | File08 booking/availability owner | SOURCE-CLEAN (limited) | File14 does not persist canonical booking state |
| 8 | File09 doctor onboarding owner | SOURCE-CLEAN (limited) | Verification left to native File09/File00 |
| 9 | Files20/25 shell and visual UX | SOURCE-CLEAN (limited) | No second global shell/visual system |
| 10 | File24 GPC/consent privacy | SOURCE-CLEAN (limited) | Source consent and assurance gate |
| 11 | F14-FUT-01 explicit intent input | DEFECT R14-D01 | REST controller passed non-string array/object JSON directly to sanitize_key |
| 12 | F14-FUT-02 typed destination context | SOURCE-CLEAN (limited) | R13 typed country/language/consultation-mode allowlist |
| 13 | F14-FUT-03/04 claim provenance | SOURCE-CLEAN (limited) | Public current, effective, review-due claim checks |
| 14 | F14-FUT-05/07 business-copy risk | SOURCE-CLEAN (limited) | Parities, semantic difference and dark pattern guards |
| 15 | F14-FUT-12 complaint identity | SOURCE-CLEAN (limited) | R11 conflicting UUID/report replay guard |
| 16 | F14-FUT-22 published change log | DEFECT R14-D02 | Requested output limit applied to public candidates before evidence validation, so invalid newest rows hid approved older ones |
| 17 | F14-FUT-11/13 cohort privacy | SOURCE-CLEAN (limited) | Suppressed owner-unconfirmed stages and minimum reporting cohort |
| 18 | F14-FR-011 true owner conversion | OPEN R14-G01 | Issue #19: File07/08/09 owner-attested outcome event contract incomplete |
| 19 | F14-FUT-17 baseline restore | OPEN R14-G02 | Issue #22: experiment stop/audit does not prove approved baseline variant restoration |
| 20 | Exact CI and physical schema guards | SOURCE-CLEAN (limited) | PHP 7.4/8.3, verified-table read gate and packaging exist; no live DB proof |

**Before fixes:** 16 narrow source-clean rounds; **two File14 local defects** in passes 11/16; **two contract gaps** in passes 18/19. **Additional R14-D03 detected during PHP CI regression testing** after the review batch; initial source counts remain unchanged, but three File14 coding defects were ultimately corrected.

## Consolidated correction batch (after all twenty rounds)

**R14-D01 / F14-FUT-01, explicit intent type safety:** The JSON `intent` field was passed to `sanitize_key` before a scalar-type check. Array/object clients can trigger PHP cast warnings and risk non-explicit inference. Introduced `GCU_Future_Policy::sanitize_explicit_intent`: only string input, sanitized and verified against the *three* canonical `patient`, `doctor`, `learn` keys; all other shapes fail closed to the existing controlled invalid-intent response. `rest_handoff` uses this pure guard. Regression covers each approved selection and invalid string/array/object/boolean/number/null.

**R14-D02 / F14-FUT-22, validate public history before applying display limit:** `public_change_log($limit)` previously fetched only the newest $limit active/public, nonexpired records and filtered them for source/reviewer/provenance/date *afterward*. Legacy invalid changes could obscure a separately approved older record and make public history misleadingly empty. `records` now accepts a sanitized optional offset and stable `updated_at DESC, id DESC` ordering. Public change-log projection scans bounded 100-row batches, up to **1,000 source rows**, validates evidence on each, then returns the requested maximum approved items. No unapproved copy is displayed; preserved older records are read-only. Pagination is deliberately bounded, so exhaustion of the 1,000-row window **is not proof the historical archive contains no approved change beyond it**. Regression fixture creates 100 newest invalid entries and one older approved entry and tests both supported fallback and all-invalid suppression.

**Version** 1.4.18. Planned schema targets unchanged (base 10005, Future 1). No new migration, database change or live deployment.

## Unfulfilled owner/design requirements

**Issue #19:** File07/08/09 signed, consent-aware, idempotent, versioned native destination/application/booking confirmations and File01 registration remain unimplemented. Do not infer a booking from a File14 CTA.

**Issue #22:** F14-FUT-17 safe baseline placement/variant snapshot and restoration after experiment early stop remain unproven. The stopped/audited status must not be described as baseline restored.

## Acceptance evidence to collect

Exact new candidate branch PHP7.4/8.3 and fresh reviews 1/2, deterministic package/SBOM; then PR, merge, and **separate new exact-main CI**. Live Reality Freeze before any deployed issue is pronounced resolved: actual active plugin build/checksum, DB/schema, wp_options/migrations, relevant rows/logs, configurations, staging backup/restore and real browser journeys.

Repository HEAD (frozen baseline): `cfe8d9c51932014702967acc27c10d4382c4de0e`. Deployed Version: **unverified**. DB Version: **unverified**. Migration State: **unverified**. Live Verification Status: **not performed**.

**Exact deployed code ابھی unverified ہے؛ repository-based diagnosis provisional ہے۔**


**R14-D03 / regression-discovered storage decoding defect:** Initial candidate PHP CI failed with an actual `TypeError`: `GCU_Future_Intelligence::records()` looped by reference over `is_array( $rows ) ? $rows : array()`, which is a temporary expression. Decoding changed only the temporary rows, leaving the real returned record `payload` as a JSON string. `public_change_log()` passed that string into the type-hinted public record validator, resulting in a PHP fatal. Corrected `records()` to normalize the actual `$rows` variable first, then decode each payload **inside the returned array**. Added explicit record-decode regression beyond the public change-log pagination fixture. Failed candidate run is retained as history; fresh CI must succeed at the exact new SHA before merge.
