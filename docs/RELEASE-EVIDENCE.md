# Release Evidence — v1.4.6 Second Twenty-Pass Current-Companion Repository Candidate

## Governing scope

- Base plan: `SSH-F14-PLAN-2026-v1.0`.
- Additive Founder-approved amendment: `SSH-F14-FUTURE-CTI-2026-v2.0`.
- Second twenty-pass baseline: exact starting `main` `55e44d38b23304d50fad22b4d3a2c67fe4721209` (v1.4.5 first twenty-pass merge).
- Software candidate: `1.4.6`.
- Base schema: `10005`; Future CTI additive schema: `1`.
- Requirements retained: original File 14 FR/NFR plus `F14-FUT-01`–`F14-FUT-24`.
- Ownership remains bounded: no doctor/clinic/application/appointment/payment/verification/shell source of truth is created here.

## Exact-head evidence policy

Historical green results are supporting history only. For every current File 14 source state, the exact review/main SHA being accepted must independently prove PHP 7.4 and PHP 8.3 quality, policy/contract/reliability/central/Future regressions, all six historical eighty-pass gates, the dedicated twenty-pass cross-file gate, two fresh post-code review rounds, secret/inline/stale-token/deprecated-helper scans, deterministic double-build ZIP, SHA-256/file-level SBOM, baseline integrity, PR-head parity and **fresh post-merge** exact-main acceptance.

The **third independent eighty-pass** review established the durable exact-current-main rule; the fourth, fifth and sixth reviews retain and strengthen that rule rather than replacing it.

`Automated-QA Green`, `Packaged`, `main merged`, `Staging-Accepted`, `Live-Deployed` and `Operational` remain separate evidence claims.

## Second twenty-pass current-companion corrective evidence — 2026-10-07

The second 20-pass review re-froze File 14 against the governing File 14 plan, the consolidated central plan and current exact source heads for Files 00/01/07/08/09/19/20/24/25. File 09 had advanced to `448d41f34586369ca5875693583b9cd8a6133167` and File 25 to `2d02c93356b050313e30e29aeceb57080771c2a5`; both remained semantically compatible, but File 14's runtime adapters did not consistently enforce the minimum versions already declared in its File 01 dependency manifest.

v1.4.6 therefore adds one fail-closed compatibility baseline: File 00/01/07/08/09/19/20/24/25 runtime/version checks; File 08 API/route/business-policy parity; File 09 exact owner/consumer/read-only/no-auto-verification contract invariants; and File 25 public design-system/component contract validation through `sabri_visual_experience_contract()` and `sabri_visual_experience_render_state()`. Declared dependency floors and runtime enforcement now share the same constants.

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
