# File14 R73 — 40 source review rounds, File00 authorization and cross-owner release truth

Date: 2026-10-20. Prior File14 repository main: `adeba7f3fb3d0be02953ec0851fdb07f499dd438`, software 1.4.73. Proposed File14 software candidate: 1.4.74. This is GitHub code/CI evidence, **NOT installed WordPress**, database migration or staging acceptance.

## Governing documents and 40 review lenses

Source of requirements: File14 amended v2.0 (F14-FR-001..016, F14-NFR-001..010, F14-FUT-01..24), consolidated Three Central Plans, and canonical File00/01/07/08/09/20/25 owner source. The 40 sequential iterations in `scripts/review40-iterative.sh` each run **40** plan source-marker checks, **40** native owner mocked PHP checks, **40** privacy mocked PHP checks, **40** destination/filter mocked PHP checks, and **40** newly added File00 authorization direct PHP tests. Total expected: 40 rounds x 200 source/mock checks, plus standalone quality, two fresh reviews, and package. Repetition is NOT 40 independent WordPress environments or live acceptance.

| Lens | Plan connection | Limit / owner acceptance |
|---|---|---|
| 01–04 | FR-001..004: patient/doctor claims, CTA, steps | File07/09 native destination health, not assumed route |
| 05–08 | FR-005..008: trust, value, blocks, placements | File14 governs approved USP copy; File20 shell and File25 UI stay separate |
| 09–12 | FR-009..012: destination health, attribution, events, experiment | Click never confirms arrival/booking, no invented attribution; actual CAS restore still blocked |
| 13–16 | FR-013..016: locale, FAQs, a11y, current claim | en-US/Urdu/Arabic, current policy and review owner |
| 17–26 | NFR-001..010 | authenticated role/object/file00 claim, consent, durability, performance, accessibility, audit, migrations, operability, compatible owner versions, localization |
| 27–30 | FUT-01..04 | approved evidence only, no auto-publish/no fabricated owner truth |
| 31–34 | FUT-05..08 | claim/FAQ consistency, governed copies, prohibited dark patterns |
| 35–38 | FUT-09..16 | consent, small cohort, lifecycle quality score, anomaly guardrails — owner signed events unverified |
| 39–40 | FUT-17..24 | early-stop unchanged-baseline no-op is NOT actual restoration, remaining future copy controls and educational guides are governed |

## R73 source defect and correction

File00 native `SMC_Contracts::assertions()` returns a canonical integer `user_id`, boolean `eligible` and boolean `suspended`. File14 prechange `GCU_Companion_Adapters` used `absint()` for claim identity, `empty()` for eligibility/suspension, and `(bool) apply_filters('gcu_authorize', ...)` for restriction-only authorization. Because PHP casts textual values such as `'false'` to true, a malformed external assertion/filter result could preserve authorization. New File14 code requires exact integer claimant identity, `eligible === true`, `suspended === false`, and literal boolean `true` from the restrictive filter.

40 direct cases in `tests/r73-forty-file00-claim-and-filter-auth-tests.php` exercise canonical and malformed integer/string/float/array identities, booleans, denial statuses, restrictive-filter responses and valid controls. Added to `scripts/quality.sh` and to each of 40 iterative source reviews. Database tables, migration constants and native File00 code unchanged.

## Known blockers: three distinct cross-owner applications

- [#19](https://github.com/majidhussainqadri1-dot/14-global-clinic-usp-and-conversion-sections/issues/19): File01 draft PR #7 strict schema is not a cryptographically owner-attested, consented and replay/erasure-tested File07/08/09 event transport. File07 served != destination browser loaded. File09 submitted != started. File08 requested != booked.
- [#22](https://github.com/majidhussainqadri1-dot/14-global-clinic-usp-and-conversion-sections/issues/22): File20 draft PR #42 is preflight only; no last-writer proof, authorized CAS writer, File20/25 render synchronization, backup restoration or staging race test. A no-op is not a mutated placement restore.
- [#70](https://github.com/majidhussainqadri1-dot/14-global-clinic-usp-and-conversion-sections/issues/70): File08 draft PR #42 is an unaccepted schema proposal; native consent/revocation/erasure-cohort aggregation is missing; sensitive booking REST must remain untracked. No count or rate is reportable.

**Live Reality Freeze:** Deployed Version: unverified; DB Version: unverified; Migration State: unverified; Runtime logs: unverified; Artifact checksum parity: unverified; Live Verification Status: NOT PERFORMED. No WordPress site connection. Source/CI success cannot mean production fixed or these three issues closed.

## Release verification required

Exact branch-head PHP7.4/8.3, complete 40-round script, all fresh review jobs, deterministic ZIP/SBOM; merge then **new** post-merge main CI; staging exact installed version/schema/migration/owner workflows; explicit Founder approval, backup/restore, live deploy and retest. Until this evidence exists, report source changes only and keep all three cross-owner issues OPEN.
