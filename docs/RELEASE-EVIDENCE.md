# Release Evidence — v1.4.10 Sixth Twenty-Area Measurement-Evidence Repository Candidate

## R6 corrective source evidence — 2026-10-10

Frozen audited parent `843c0b4c2376f82698fdec2776da277f710f144c`; 20 source review areas, 17 clean, 3 defects. Evidence gaps: File 14 does not originate an owner-confirmed `destination_loaded` event; no source can truthfully infer patient/doctor transitions from unrelated independent totals; an unspecified accessibility measure cannot legitimately equal 100. Source fixes in `GCU_Future_Policy` and `GCU_Future_Intelligence` now return unavailable/provisional evidence and decline unverified cross-branch dropoff rates. Added regression tests and current v1.4.10 release gates. See `docs/REVIEW-20-CROSS-FILE-COMPLETION-v1.4.10-R6.md`.

External File 07/08/09 versioned owner-acknowledgement support for measured conversion is pending. Deployed artifact parity, DB/migrations, staging and live confirmation are NOT verified; exact branch CI and post-merge main CI remain required.

## Historical R5 release evidence (preserved)

Historical identity: v1.4.9 Fifth Twenty-Pass Exact-Companion Repository Candidate.

## Governing scope

- Base plan: `SSH-F14-PLAN-2026-v1.0`.
- Additive Founder-approved amendment: `SSH-F14-FUTURE-CTI-2026-v2.0`.
- Fifth twenty-pass baseline: exact starting `main` `080e2198d84dfb7491bb0b75946e14a5fe118b91` (v1.4.8 fourth twenty-pass merge).
- Software candidate: `1.4.10`.
- Base schema: `10005`; Future CTI additive schema: `1`.
- Requirements retained: original File 14 FR/NFR plus `F14-FUT-01`–`F14-FUT-24`.
- Ownership remains bounded: no doctor/clinic/application/appointment/payment/verification/shell source of truth is created here.

## Exact-head evidence policy

Historical green results are supporting history only. For every current File 14 source state, the exact review/main SHA being accepted must independently prove PHP 7.4 and PHP 8.3 quality, policy/contract/reliability/central/Future regressions, all six historical eighty-pass gates, the dedicated twenty-pass cross-file gate, two fresh post-code review rounds, secret/inline/stale-token/deprecated-helper scans, deterministic double-build ZIP, SHA-256/file-level SBOM, baseline integrity, PR-head parity and **fresh post-merge** exact-main acceptance.

The **third independent eighty-pass** review established the durable exact-current-main rule; the fourth, fifth and sixth reviews retain and strengthen that rule rather than replacing it.

`Automated-QA Green`, `Packaged`, `main merged`, `Staging-Accepted`, `Live-Deployed` and `Operational` remain separate evidence claims.

## Fifth twenty-pass exact-companion/registry-identity evidence — 2026-10-10

Exact File 14 `main` source freeze `080e2198d84dfb7491bb0b75946e14a5fe118b91`. Current companion File 25 source advanced to `347a4ff4d4c233c5ea6cd82c7786ee5398ea9d1e` (its runtime/visual interface remains 0.15.0/1.9.0/1.2.0). File 00/01/07/08/09/19/20/24 exact repository heads remained unchanged.

Twenty completed review areas yielded 5 defect-bearing and 15 clean results, documented in `docs/REVIEW-20-CROSS-FILE-COMPLETION-v1.4.9-R5.md`. File 01 readiness failed to bind its module to the exact canonical manifest/installed version and accepted route path/owner alone, including redirects and wrong canonical route identity/layout/destination. Corrected those failures *only inside File 14-owned read/health consumers and route comparison*. Nineteen PHP behavioral cases now test those boundaries in addition to existing source-regression gates.

`1.4.9` is a repository candidate until the exact post-code branch HEAD passes full PHP 7.4/8.3 quality, regression, both fresh review rounds, review20-R5, deterministic package/SBOM and baseline integrity. The exact merged main must then pass new post-merge workflows. External staging, deployed package parity, real schema/migration, backup-restore, browser accessibility and live re-test remain unverified.

## Fourth twenty-pass cross-file contract-truth corrective evidence — 2026-10-09

R4 re-froze exact File 14 main `f8b98b35a00f920dd2a74c1a4e4b7f707a8b53ae` plus current Files 00/01/07/08/09/19/20/24/25. Companion source heads were unchanged from R3, so this pass concentrated on semantic/runtime truth rather than version drift.

Six defect-bearing rounds were proven before correction. File 01 placement readiness did not reject degraded/suspended/retired File 14 module state and did not require the two canonical File 14 registry contracts. File 20 availability trusted runtime version + class presence without validating its native CentralPlanContract semantics for File 14. File 24 availability trusted `SPCRC_VERSION` even though File 24 defines it before an upgrade/schema failure can block actual boot. Regression and current release evidence did not cover these conditions.

v1.4.8 corrects those boundaries. File 01 now requires eligible module lifecycle plus exact current API/events registry contracts; File 20 validates its native File 14 row and supported 1.0.x contract semantics; File 24 requires successful `spcrc/booted` evidence and its governed-artifact service. Permanent evidence: `docs/REVIEW-20-CROSS-FILE-COMPLETION-v1.4.8-R4.md`.

Repository QA is necessary but not staging/live/deployed-state proof.

## Third twenty-pass current-companion/runtime-health corrective evidence — 2026-10-08

The third 20-pass review re-froze File 14 against the File 14 governing plan, the consolidated central plan and the exact current source heads of Files 00/01/07/08/09/19/20/24/25. File 07 had advanced to `2f4a89707724fd2b9946600afe10ddab27ec3c2d` (runtime/contract 1.2.1), File 09 to `cfc5f781a766330314dc98c42abeca0eb7786eba`, and File 25 to `e35563b7f3d8ebf0acbbc80982b7bcf2e1b78c0a`.

The review found that File 14's File 07 floor still admitted 1.2.0, File 07/File 08 destination readiness did not consume owner runtime health, and exceptions from companion calls could escape instead of degrading/failing closed. v1.4.7 raises File 07 to the reviewed 1.2.1 family, consumes File 07 system health and File 08 owner health before exposing destination CTAs, and adds bounded non-PII exception isolation for Files 00/01/07/08/09/19/25.

The permanent ledger is `docs/REVIEW-20-CROSS-FILE-COMPLETION-v1.4.7-R3.md`. Repository QA remains separate from staging/live/deployed-state evidence.

## Second twenty-pass current-companion corrective evidence — 2026-10-07

The second 20-pass review re-froze File 14 against the governing File 14 plan, the consolidated central plan and current exact source heads for Files 00/01/07/08/09/19/20/24/25. File 09 had advanced to `448d41f34586369ca5875693583b9cd8a6133167` and File 25 to `2d02c93356b050313e30e29aeceb57080771c2a5`; both remained semantically compatible, but File 14's runtime adapters did not consistently enforce the minimum versions already declared in its File 01 dependency manifest.

v1.4.6 therefore added one fail-closed compatibility baseline: File 00/01/07/08/09/19/20/24/25 runtime/version checks; File 08 API/route/business-policy parity; File 09 exact owner/consumer/read-only/no-auto-verification contract invariants; and File 25 public design-system/component contract validation through `sabri_visual_experience_contract()` and `sabri_visual_experience_render_state()`. Declared dependency floors and runtime enforcement now share the same constants.

The permanent round ledger is `docs/REVIEW-20-CROSS-FILE-COMPLETION-v1.4.6-R2.md`. The earlier v1.4.5 twenty-pass ledger remains historical evidence and is not rewritten.

## Twenty-pass cross-file corrective evidence

The 2026-10-05 review compared File 14 against its governing plan, the consolidated central plan and the current source-repository contracts of Files 00/01/07/08/09/19/20/24/25. It identified and corrected historical compatibility assumptions that were not present in the current companion repositories:

- File 00 authorization now consumes current `SMC_Contracts::assertions()` plus the `smc_assertions_v1` action-time hardening filter; File 14 capabilities participate in File 00 restricted-capability containment, while the legacy `gcu_authorize` bridge may only restrict.
- Files 07/08/09 destination health now uses their current owner-native runtime contracts; historical availability events remain compatibility-only.
- File 14 semantic content placements no longer require the nonexistent File 20 `sabri_shell_slot_ready_v1` hook.
- File 01 canonical route-registry readiness is now checked for all four File 14 public routes before semantic placements are treated as ready.
- File 20 integration now uses the actual `sabri_shell_route_result_allowed`, `sabri_shell_system_check_sections` and `sabri_shell_context_navigation_fallback_url` contracts.
- File 25 current reusable state/card/button contract is consumed without transferring domain ownership.
- File 24 receives a bounded module assurance manifest while native File 14 security/authorization/privacy controls remain local; repository code declares the assurance posture `unassessed` and does not fabricate a `last_security_test` timestamp.
- File 19 integration now registers a bounded File 14 producer and submits only privacy-minimized operational alerts to explicitly supplied canonical recipients; File 14 never guesses recipients or takes delivery ownership.
- Observability now exposes actual File 00/01/07/08/09/19/20/24/25 dependency states.
- Exact companion repository baselines are recorded in `STATUS.md` and the twenty-pass review ledger; they are repository truth only and do not prove deployed parity.

Round-by-round evidence is maintained in `docs/REVIEW-20-CROSS-FILE-COMPLETION-v1.4.5.md`.

## Sixth-review corrective evidence areas

The sixth independent review reopened exact post-fifth-review `main` and corrected additional failure classes: request-time File20 slot readiness, stable audit/privacy HMAC identities and guarded legacy migration, audit-integrity containment, mandatory admin audit truth, actual Future schema verification before safe-mode exit, nested owner mutation/audit/outbox atomicity, payload-bound idempotency and command completion, stale install-lock recovery, bounded-draining retention, stable privacy export/erase linkage, read-back verified inbound owner state, fail-closed migration/rate-limit failures, conversion-event/outbox atomicity, atomic Future report/record governance, transactional early-stop parity, and stable bounded cursor pagination.

Round-by-round evidence is maintained in:

- `docs/REVIEW-80-LEDGER-v1.4.1.md`
- `docs/REVIEW-80-SECOND-LEDGER-v1.4.1.md`
- `docs/REVIEW-80-THIRD-LEDGER-v1.4.1.md`
- `docs/REVIEW-80-FOURTH-LEDGER-v1.4.2.md`
- `docs/REVIEW-80-FIFTH-LEDGER-v1.4.3.md`
- `docs/REVIEW-80-SIXTH-LEDGER-v1.4.4.md`

## External evidence still mandatory

Repository success cannot prove WordPress/Hostinger staging or live behavior. Staging, deployed code, live DB/schema/migration and operational behavior remain separate evidence classes. Separate gates still include exact deployed artifact/checksum parity, fresh install/upgrade with real WordPress/MySQL/InnoDB, File00/01/07/08/09/19/20/24/25 versioned integration, browser/accessibility/RTL/LTR/400% zoom evidence, performance/failure drills, verified backup restore and rollback rehearsal, explicit Founder staging acceptance, production deployment, live smoke tests, monitoring and deployed-artifact parity confirmation.

No `Staging-Accepted`, `Live-Deployed` or `Operational` claim is made by this repository document.
