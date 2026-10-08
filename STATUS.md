# File 14 Status — v1.4.7 Third Twenty-Pass Current-Companion Repository Candidate

## Repository coding status

- Governing freeze: consolidated central governing plan + `SSH-F14-PLAN-2026-v1.0` + Founder-approved additive amendment `SSH-F14-FUTURE-CTI-2026-v2.0`.
- Third twenty-pass review baseline: exact starting `main` `f64e7d17268daff4e3097c18ad510116e6eaf105` (v1.4.6 second twenty-pass merge).
- Software candidate: `1.4.7`.
- Base File 14 database schema: `10005`.
- Future CTI additive schema: `1`.
- File 14 remains the owner only of approved Worldwide Clinic USP/copy, semantic placement metadata, claim governance, experiments and privacy-minimized conversion measurement.
- File 20 remains the sole global shell/navigation owner; Files 07/08/09 remain canonical doctor-directory/clinic-appointment/onboarding owners; File 00 remains institutional authorization truth; File 01 remains the platform registry; File 19 remains notification transport; File 24 remains the assurance plane; File 25 remains the public visual-system owner.
- The 2026-10-08 third twenty-pass review found ten defect-bearing rounds and ten clean rounds before any corrections were applied.
- v1.4.7 raises the required File 07 compatibility floor to 1.2.1, requires owner runtime health for File 07/File 08 destination readiness, and exception-isolates companion-owner calls so dependency failures degrade/fail closed instead of escaping as File 14 fatals.
- Current companion source baselines reviewed: File 00 `2fa7c022ee9cd1b65432e900579512f304532442`; File 01 `adf6dbb9980a85f25f7cf4c2ee679b52703c2e71`; File 07 `2f4a89707724fd2b9946600afe10ddab27ec3c2d`; File 08 `70541974ce0ffb16aebef557c3016eb7447662f4`; File 09 `cfc5f781a766330314dc98c42abeca0eb7786eba`; File 19 `04078025b643ab7696e4cb4e37826bf152defa18`; File 20 `8a4dbcaf4fef8e926b9b834ecfde16c21a0f00ca`; File 24 `a5b8d49968a7a5a7d6f3f4655bea541bf38a9acb`; File 25 `e35563b7f3d8ebf0acbbc80982b7bcf2e1b78c0a`. These are repository-source baselines only.
- Fresh exact-head companion CI observed before correction: File 07 Quality Gates #315 and Final Quality Gates #284 success; File 09 RC6 Exact-Head Assurance #958 success; File 25 CI #1689 success.
- Deterministic package target: `14-global-clinic-usp-integration-1.4.7.zip` + SHA-256 + file-level SBOM, generated only from the exact head being evaluated.

## Exact-current-head rule

Historical PRs, commits and green workflow runs are supporting history only. No historical ledger or merge may substitute for verification of the **exact current branch/main SHA** being accepted.

This v1.4.7 corrective branch remains only a repository candidate until its exact final branch SHA independently passes PHP 7.4/8.3 quality, all preserved regression suites, the current twenty-pass cross-file gate, both fresh post-code review rounds, baseline integrity and deterministic package/SBOM. After merge, the resulting exact `main` SHA must pass the applicable workflows again before `Automated-QA Green` is claimed for `main`.

Current cross-file review ledgers:
- `docs/REVIEW-20-CROSS-FILE-COMPLETION-v1.4.5.md` — first twenty-pass review, historical.
- `docs/REVIEW-20-CROSS-FILE-COMPLETION-v1.4.6-R2.md` — second twenty-pass current-companion review, historical.
- `docs/REVIEW-20-CROSS-FILE-COMPLETION-v1.4.7-R3.md` — third twenty-pass current-companion/runtime-health review.

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

## Mandatory live incident dimensions

Repository HEAD / Deployed Version / DB Version / Migration State / Live Verification Status must remain separately reported for any live incident.

**Repository evidence only:** every status in this file is source-repository evidence unless an external staging/deployment/live evidence record is explicitly cited.
