# File 14 Status — v1.4.5 Twenty-Pass Cross-File Repository Candidate

## Repository coding status

- Governing freeze: consolidated central governing plan + `SSH-F14-PLAN-2026-v1.0` + Founder-approved additive amendment `SSH-F14-FUTURE-CTI-2026-v2.0` dated 2026-08-10.
- Cross-file review baseline: exact starting `main` `db60c4bc5c37a5c88126b78c31b34c75236f33d7` (v1.4.4 sixth-review merge).
- Software candidate: `1.4.5`.
- Base File 14 database schema: `10005`.
- Future CTI additive schema: `1` (`gcu_future_records`, `gcu_future_reports`).
- The 24 Future CTI requirements `F14-FUT-01` through `F14-FUT-24` remain inside File 14's approved trust/conversion scope; File 14 does not become doctor, clinic, appointment, payment, verification or shell source of truth.
- File 20 remains the sole global shell/navigation owner; Files 07/08/09 remain canonical doctor-directory/clinic-appointment/onboarding owners; File 00 remains institutional authorization truth; File 24 is the assurance plane and File 25 the public visual-system owner.
- The 2026-10-05 twenty-pass cross-file review found historical placeholder integration hooks that were not present in the current companion repositories. v1.4.5 replaces those hard dependencies with current owner-native contracts while preserving fail-closed behavior and owner boundaries.
- Current companion source baselines reviewed: File 00 `2fa7c022ee9cd1b65432e900579512f304532442`; File 01 `adf6dbb9980a85f25f7cf4c2ee679b52703c2e71`; File 07 `67c32ec4af45a7de6e3d9c1dbf0f8614d6b5a844`; File 08 `70541974ce0ffb16aebef557c3016eb7447662f4`; File 09 `d35eb982becdf0224a5b850a0c6fb4ace8bf075b`; File 20 `8a4dbcaf4fef8e926b9b834ecfde16c21a0f00ca`; File 24 `a5b8d49968a7a5a7d6f3f4655bea541bf38a9acb`; File 25 `59927df876dc92c7461351420c7b7c95c65c6a93`. These are repository source baselines, not deployed-state evidence.
- Deterministic package target: `14-global-clinic-usp-integration-1.4.5.zip` + SHA-256 + file-level SBOM, generated only from the exact head being evaluated.

## Exact-current-head rule

Historical PRs, commits and green workflow runs are supporting history only. They are not current repository truth merely because they once passed. No historical ledger or merge may substitute for verification of the **exact current `main` SHA** when a main-branch release state is claimed.

This cross-file corrective branch may be described as a repository candidate only until the **exact final branch SHA** independently passes PHP 7.4/8.3 quality, all six historical eighty-pass regression gates, the new twenty-pass cross-file gate, both fresh post-code reviews, baseline integrity and deterministic package/SBOM. After merge, the exact resulting `main` SHA must pass the applicable workflows again before `Automated-QA Green` is claimed for `main`.

The six historical eighty-pass review ledgers remain immutable evidence. The new cross-file review ledger is `docs/REVIEW-20-CROSS-FILE-COMPLETION-v1.4.5.md`.

The six historical repository review ledgers are:

- `docs/REVIEW-80-LEDGER-v1.4.1.md` — first eighty-pass corrective review.
- `docs/REVIEW-80-SECOND-LEDGER-v1.4.1.md` — second independent eighty-pass review.
- `docs/REVIEW-80-THIRD-LEDGER-v1.4.1.md` — third independent eighty-pass review.
- `docs/REVIEW-80-FOURTH-LEDGER-v1.4.2.md` — fourth independent eighty-pass review.
- `docs/REVIEW-80-FIFTH-LEDGER-v1.4.3.md` — fifth independent eighty-pass review.
- `docs/REVIEW-80-SIXTH-LEDGER-v1.4.4.md` — sixth independent eighty-pass review reopened from exact post-fifth-review main.

No ledger or historical merge SHA is allowed to substitute for exact-current-head evidence.

## Truth-status boundary

`Specified`, `Coded`, `Packaged`, `Automated-QA Green`, `Staging-Accepted`, `Live-Deployed`, and `Operational` remain separate states.

The following remain external release gates until independently proven in the target environment:

- Hostinger-equivalent fresh install and upgrade/migration acceptance, including real verification of base schema `10005` and Future schema `1` with the exact deployed package.
- Real File 00/07/08/09/20/24/25 integration and normal/degraded/unauthorized journeys.
- Real WordPress/MySQL rollback/restore rehearsal and backup consistency evidence.
- 320–1920px, 400% zoom, keyboard, screen-reader, English LTR and Urdu/Arabic RTL human acceptance.
- Measured p75/p95 performance, slow-network behavior and provider/queue/cache/DB failure drills.
- Claim freshness/revalidation, policy parity, experiment preflight/early-stop, copy-report correction, privacy export/erase, low-bandwidth/GPC/consent and audit/outbox containment scenarios in the target environment.
- Founder visual/copy/functional staging acceptance.
- Controlled production deployment, live smoke test, monitoring and deployed-artifact parity confirmation.

No `Staging-Accepted`, `Live-Deployed` or `Operational` claim is made by this repository status file.
