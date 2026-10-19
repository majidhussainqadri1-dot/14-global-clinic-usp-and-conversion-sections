# R68 — Forty direct owner-contract behavioral reviews / current File14, central and companion plans
**Date:** 2026-10-19. **Frozen before editing:** `main@81f6200bcde69a60e8ed639f2026d35633c1715d` (source `1.4.69`).

## Source and scope
- **Governing inputs:** attached amended File14 Global Clinic USP/Conversion Master Plan (16 FR, 10 NFR and 24 Future CTI provisions), attached Sabri Three-Central-Plans Consolidated Governing Master Plan (including the 2026-08-06 amendment overriding older free-tier wording), and separately fetched owners' exact repository main files: File07 directory owner health, File08 `WCA_Contracts::contract_manifest()`/`WCA_Observability::health()` and File09 `gdo_file14_onboarding_destination()` owner contracts.
- **Ownership boundary:** File14 owns USP copy, intent/CTA and consented nonidentifying analytics, NOT File07 directory, File08 booking/payment, File09 verification, File20 shell, File25 visual/CAS public placement. No foreign-owner schema, data, permissions or routing changes in R68.
- **40 passes here are forty different actual PHP method calls** with separate owner payloads and asserted outcomes. Tests use real `GCU_Companion_Adapters::destination_probe()` code plus mocked canonical owner providers. This is significantly stronger than a text-only grep but STILL not installed WordPress, live transactions or cross-owner staging acceptance.
- **Method:** finish one complete 40-case review, collect failures, apply consolidated R68 root-cause fix, repeat complete test on PHP 7.4 and 8.3 CI. Source scans and two independent fresh reviews remain separate quality gates.

## Proven source defects / one root trust-boundary failure
- **R68-D01:** `0 === (int) commission_percent` falsely equated nonzero fractional commission and malformed textual evidence to zero; File08 actual owner emits canonical integer `0`, so only `0 === $manifest['commission_percent']` (after `array_key_exists`) proves fee parity.
- **R68-D02:** `empty($manifest['donation_visibility_link'])` and `!empty($runtime_health['ok'])` substituted coercion for signed owner policy/health. File08 canonical owner emits native `false` donation-linked visibility and native `true` health. Missing/null/string/different types must fail closed.
- **R68-D03:** File07 truthy readiness and File09 truthy `available`/coerced `writes_data`, `automatic_enrollment`, `automatic_verification` similarly allowed non-boolean facts to acquire owner-trust significance. Require exact booleans, or make the CTA unavailable.
- **All three are one File14 boundary issue:** owner-native evidence cannot be elevated or sanitized into a different policy assertion. Zero-commission is an unconditional central governing constraint.

## Detailed forty native test cases
| # | Plan / owner gate | Owner payload variation | Observable test requirement |
|---:|---|---|---|
| 01 | File08 zero-commission native type | integer 0 (approved) | Native `destination_probe()` called; exactly typed owner result and URL asserted |
| 02 | File08 zero-commission native type | string '0' | Native `destination_probe()` called; exactly typed owner result and URL asserted |
| 03 | File08 zero-commission native type | float 0.0 | Native `destination_probe()` called; exactly typed owner result and URL asserted |
| 04 | File08 zero-commission native type | string '0.0' | Native `destination_probe()` called; exactly typed owner result and URL asserted |
| 05 | File08 zero-commission native type | float +0.5 | Native `destination_probe()` called; exactly typed owner result and URL asserted |
| 06 | File08 zero-commission native type | string '+0.5' | Native `destination_probe()` called; exactly typed owner result and URL asserted |
| 07 | File08 zero-commission native type | float -0.5 | Native `destination_probe()` called; exactly typed owner result and URL asserted |
| 08 | File08 zero-commission native type | malformed text | Native `destination_probe()` called; exactly typed owner result and URL asserted |
| 09 | File08 zero-commission native type | empty text | Native `destination_probe()` called; exactly typed owner result and URL asserted |
| 10 | File08 zero-commission native type | false | Native `destination_probe()` called; exactly typed owner result and URL asserted |
| 11 | File08 zero-commission native type | true | Native `destination_probe()` called; exactly typed owner result and URL asserted |
| 12 | File08 zero-commission native type | null | Native `destination_probe()` called; exactly typed owner result and URL asserted |
| 13 | File08 zero-commission native type | empty array | Native `destination_probe()` called; exactly typed owner result and URL asserted |
| 14 | File08 zero-commission native type | integer 1 | Native `destination_probe()` called; exactly typed owner result and URL asserted |
| 15 | File08 zero-commission native type | integer -1 | Native `destination_probe()` called; exactly typed owner result and URL asserted |
| 16 | File08 donation neutrality typed false | native false (approved) | Native `destination_probe()` called; exactly typed owner result and URL asserted |
| 17 | File08 donation neutrality typed false | integer 0 | Native `destination_probe()` called; exactly typed owner result and URL asserted |
| 18 | File08 donation neutrality typed false | string '0' | Native `destination_probe()` called; exactly typed owner result and URL asserted |
| 19 | File08 donation neutrality typed false | null | Native `destination_probe()` called; exactly typed owner result and URL asserted |
| 20 | File08 donation neutrality typed false | empty string | Native `destination_probe()` called; exactly typed owner result and URL asserted |
| 21 | File08 donation neutrality typed false | empty array | Native `destination_probe()` called; exactly typed owner result and URL asserted |
| 22 | File08 donation neutrality typed false | true | Native `destination_probe()` called; exactly typed owner result and URL asserted |
| 23 | File08 health strict true | native true (approved) | Native `destination_probe()` called; exactly typed owner result and URL asserted |
| 24 | File08 health strict true | false | Native `destination_probe()` called; exactly typed owner result and URL asserted |
| 25 | File08 health strict true | string 'false' | Native `destination_probe()` called; exactly typed owner result and URL asserted |
| 26 | File08 health strict true | string 'true' | Native `destination_probe()` called; exactly typed owner result and URL asserted |
| 27 | File08 health strict true | integer 1 | Native `destination_probe()` called; exactly typed owner result and URL asserted |
| 28 | File08 health strict true | array | Native `destination_probe()` called; exactly typed owner result and URL asserted |
| 29 | File08 health strict true | missing 'ok' | Native `destination_probe()` called; exactly typed owner result and URL asserted |
| 30 | File07 doctor directory health strict true | native true (approved) | Native `destination_probe()` called; exactly typed owner result and URL asserted |
| 31 | File07 doctor directory health strict true | false | Native `destination_probe()` called; exactly typed owner result and URL asserted |
| 32 | File07 doctor directory health strict true | string 'false' | Native `destination_probe()` called; exactly typed owner result and URL asserted |
| 33 | File07 doctor directory health strict true | integer 1 | Native `destination_probe()` called; exactly typed owner result and URL asserted |
| 34 | File07 doctor directory health strict true | array | Native `destination_probe()` called; exactly typed owner result and URL asserted |
| 35 | File09 onboarding readiness/no-write strict booleans | native available true (approved) | Native `destination_probe()` called; exactly typed owner result and URL asserted |
| 36 | File09 onboarding readiness/no-write strict booleans | string 'false' as available | Native `destination_probe()` called; exactly typed owner result and URL asserted |
| 37 | File09 onboarding readiness/no-write strict booleans | integer 1 as available | Native `destination_probe()` called; exactly typed owner result and URL asserted |
| 38 | File09 onboarding readiness/no-write strict booleans | integer 0 as writes_data | Native `destination_probe()` called; exactly typed owner result and URL asserted |
| 39 | File09 onboarding readiness/no-write strict booleans | text '0' as automatic_enrollment | Native `destination_probe()` called; exactly typed owner result and URL asserted |
| 40 | File09 onboarding readiness/no-write strict booleans | null as automatic_verification | Native `destination_probe()` called; exactly typed owner result and URL asserted |

## Cross-file acceptance and remaining dependencies
1. File07/08/09 source repository versions are *not* proof of exact deployed plugins. Contract mock cases cannot close real signed source-of-truth producer acceptance. **Issue #19 OPEN.**
2. File08 sensitive REST booking origin is intentionally blocked from File14 optional pseudonymous event collection; no clinical data may leak, and no absent sample may be represented as zero bookings. Founder-approved privacy-safe aggregate or explicit unavailable state. **Issue #70 OPEN.**
3. File20/25 changed-placement CAS baseline restoration, typed pre-experiment owner snapshot, race checks and staging rollback remain missing. File14's no-change snapshot can never be called actual restoration of a mutated placement. **Issue #22 OPEN.**
4. Real WordPress installed plugin version, schema, `wp_options`, logged migrations and deployed checksum cannot be verified while no WordPress site is connected; earlier endpoint check produced Cloudflare HTTP 530. No live patch or staging acceptance is claimed.

## Release acceptance checklist (not inferred)
- [ ] Exact `1.4.70` candidate CI PHP 7.4 pass
- [ ] Exact `1.4.70` candidate CI PHP 8.3 pass
- [ ] Forty actual native cases pass, at least two fresh reviews pass
- [ ] Deterministic artifact, SHA256 checksum, SBOM pass
- [ ] PR merged and full tests repeated on merged `main` SHA
- [ ] Canonical signed Event/Owner contracts #19 accepted
- [ ] Real changed-placement CAS #22 designed/approved/tested
- [ ] Consent-safe File08 owner-outcome contract #70 approved/tested
- [ ] Staging exact code/DB/schema/migration parity and role/consent tests
- [ ] Live deploy, live re-test and final checksum parity

**Repository HEAD at freeze:** `81f6200bcde69a60e8ed639f2026d35633c1715d`.
**Deployed Version:** UNVERIFIED. **DB Version:** UNVERIFIED. **Migration State:** UNVERIFIED. **Live Verification Status:** NOT RUN. Exact deployed code ابھی unverified ہے؛ repository-based diagnosis provisional ہے۔
