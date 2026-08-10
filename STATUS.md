# File 14 Status — v1.4.2 Repository Release State

## Repository coding status

- Governing freeze: consolidated central governing plan + `SSH-F14-PLAN-2026-v1.0` + Founder-approved additive amendment `SSH-F14-FUTURE-CTI-2026-v2.0` dated 2026-08-10.
- Software candidate: `1.4.2`.
- Base File 14 database schema: `10004`.
- Future CTI additive schema: `1` (`gcu_future_records`, `gcu_future_reports`).
- The 24 Future CTI requirements `F14-FUT-01` through `F14-FUT-24` remain inside File 14's approved trust/conversion scope; File 14 does not become doctor, clinic, appointment, payment, verification or shell source of truth.
- File 20 remains the sole shell/navigation owner; Files 07/08/09 remain canonical doctor/directory/clinic/onboarding owners.
- Repository hardening now also requires File 00 authorization-adapter presence for privileged actions, audience-safe block/placement activation, Founder approval of active placements, sensitive campaign minimization, per-stage cohort suppression, conflicting inbound-event detection, recent-tail audit verification, shortcode cache revalidation, File 20-only navigation ownership, Future-schema error propagation, non-destructive rollback preservation, expanded dependency/cron/route health and rollback audit evidence.
- Deterministic package target: `14-global-clinic-usp-integration-1.4.2.zip` + SHA-256 + file-level SBOM, generated only from the exact head being evaluated.

## Exact-current-head rule

Historical PRs, commits and green workflow runs are supporting history only. They are not the current repository truth merely because they once passed.

The current repository state may be described as `Automated-QA Green` only when the **exact current `main` SHA at evaluation time** has independently passed the required File 14 quality/package workflow, both fresh post-code reviews, the active first/second/third/fourth eighty-pass regression gates and baseline integrity. If `main` moves, that status must be re-established for the new exact SHA.

The three repository review ledgers are:

- `docs/REVIEW-80-LEDGER-v1.4.1.md` — first eighty-pass corrective review.
- `docs/REVIEW-80-SECOND-LEDGER-v1.4.1.md` — second independent eighty-pass review.
- `docs/REVIEW-80-THIRD-LEDGER-v1.4.1.md` — third independent eighty-pass review.
- `docs/REVIEW-80-FOURTH-LEDGER-v1.4.2.md` — fourth independent eighty-pass review reopened from the exact post-third-review main baseline.

No ledger or historical merge SHA is allowed to substitute for exact-current-head evidence.

## Truth-status boundary

`Specified`, `Coded`, `Packaged`, `Automated-QA Green`, `Staging-Accepted`, `Live-Deployed`, and `Operational` remain separate states.

The following remain external release gates until independently proven in the target environment:

- Hostinger-equivalent fresh install and upgrade/migration acceptance, including real verification of base schema `10004` and Future schema `1` with the exact deployed package.
- Real File 00/07/08/09/20/24/25 integration and normal/degraded/unauthorized journeys.
- Real WordPress/MySQL rollback/restore rehearsal and backup consistency evidence.
- 320–1920px, 400% zoom, keyboard, screen-reader, English LTR and Urdu/Arabic RTL human acceptance.
- Measured p75/p95 performance, slow-network behavior and provider/queue/cache/DB failure drills.
- Claim freshness/revalidation, policy parity, experiment preflight/early-stop, copy-report correction, privacy export/erase, low-bandwidth/GPC/consent and audit/outbox containment scenarios in the target environment.
- Founder visual/copy/functional staging acceptance.
- Controlled production deployment, live smoke test, monitoring and deployed-artifact parity confirmation.

No `Staging-Accepted`, `Live-Deployed` or `Operational` claim is made by this repository status file.
