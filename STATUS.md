# File 14 Status — v1.4.6 Second Twenty-Pass Current-Companion Repository Candidate

## Repository coding status

- Governing freeze: consolidated central governing plan + `SSH-F14-PLAN-2026-v1.0` + Founder-approved additive amendment `SSH-F14-FUTURE-CTI-2026-v2.0` dated 2026-08-10.
- Second twenty-pass review baseline: exact starting `main` `55e44d38b23304d50fad22b4d3a2c67fe4721209` (v1.4.5 first twenty-pass merge).
- Software candidate: `1.4.6`.
- Base File 14 database schema: `10005`.
- Future CTI additive schema: `1` (`gcu_future_records`, `gcu_future_reports`).
- File 14 remains the owner only of approved Worldwide Clinic USP/copy, semantic placement metadata, claim governance, experiments and privacy-minimized conversion measurement. It does not become doctor, clinic, appointment, payment, verification, notification, shell, security-assurance or visual-system source of truth.
- File 20 remains the sole global shell/navigation owner; Files 07/08/09 remain canonical doctor-directory/clinic-appointment/onboarding owners; File 00 remains institutional authorization truth; File 01 remains the platform registry; File 19 remains notification transport; File 24 remains the assurance plane; File 25 remains the public visual-system owner.
- The 2026-10-07 second twenty-pass review found that File 09 and File 25 had advanced since the 2026-10-05 freeze and that File 14 declared minimum dependency versions without consistently enforcing those compatibility floors at runtime.
- v1.4.6 therefore fail-closes Files 00/01/07/08/09/19/20/24/25 when their current runtime or published contract is missing, too old or structurally incompatible; File 25 rendering now uses its public versioned visual contract rather than an internal class call.
- Current companion source baselines reviewed: File 00 `2fa7c022ee9cd1b65432e900579512f304532442`; File 01 `adf6dbb9980a85f25f7cf4c2ee679b52703c2e71`; File 07 `67c32ec4af45a7de6e3d9c1dbf0f8614d6b5a844`; File 08 `70541974ce0ffb16aebef557c3016eb7447662f4`; File 09 `448d41f34586369ca5875693583b9cd8a6133167`; File 19 `04078025b643ab7696e4cb4e37826bf152defa18`; File 20 `8a4dbcaf4fef8e926b9b834ecfde16c21a0f00ca`; File 24 `a5b8d49968a7a5a7d6f3f4655bea541bf38a9acb`; File 25 `2d02c93356b050313e30e29aeceb57080771c2a5`. These are repository-source baselines only.
- Deterministic package target: `14-global-clinic-usp-integration-1.4.6.zip` + SHA-256 + file-level SBOM, generated only from the exact head being evaluated.

## Exact-current-head rule

Historical PRs, commits and green workflow runs are supporting history only. No historical ledger or merge may substitute for verification of the **exact current branch/main SHA** being accepted.

This v1.4.6 corrective branch remains only a repository candidate until its exact final branch SHA independently passes PHP 7.4/8.3 quality, all preserved eighty-pass regression gates, the current twenty-pass cross-file gate, both fresh post-code reviews, baseline integrity and deterministic package/SBOM. After merge, the resulting exact `main` SHA must pass the applicable workflows again before `Automated-QA Green` is claimed for `main`.

Current cross-file review ledgers:
- `docs/REVIEW-20-CROSS-FILE-COMPLETION-v1.4.5.md` — first twenty-pass cross-file completion review, historical.
- `docs/REVIEW-20-CROSS-FILE-COMPLETION-v1.4.6-R2.md` — second twenty-pass current-companion compatibility review.

Historical eighty-pass ledgers:
- `docs/REVIEW-80-LEDGER-v1.4.1.md`
- `docs/REVIEW-80-SECOND-LEDGER-v1.4.1.md`
- `docs/REVIEW-80-THIRD-LEDGER-v1.4.1.md`
- `docs/REVIEW-80-FOURTH-LEDGER-v1.4.2.md`
- `docs/REVIEW-80-FIFTH-LEDGER-v1.4.3.md`
- `docs/REVIEW-80-SIXTH-LEDGER-v1.4.4.md`

## Truth-status boundary

`Specified`, `Coded`, `Packaged`, `Automated-QA Green`, `Staging-Accepted`, `Live-Deployed`, and `Operational` remain separate states.

The following remain external release gates until independently proven in the target environment:
- exact deployed artifact/checksum parity;
- real base schema `10005` and Future schema `1` plus migration state;
- real File 00/01/07/08/09/19/20/24/25 integration and normal/degraded/unauthorized journeys;
- WordPress/MySQL backup restore and rollback rehearsal;
- 320–1920px, 400% zoom, keyboard, screen-reader, English LTR and Urdu/Arabic RTL human acceptance;
- measured p75/p95 performance and provider/queue/cache/DB failure drills;
- Founder visual/copy/functional staging acceptance;
- controlled production deployment, live smoke test, monitoring and deployed-artifact parity confirmation.

No `Staging-Accepted`, `Live-Deployed` or `Operational` claim is made by this repository status file.

**Repository evidence only:** every status in this file is source-repository evidence unless an external staging/deployment/live evidence record is explicitly cited.
