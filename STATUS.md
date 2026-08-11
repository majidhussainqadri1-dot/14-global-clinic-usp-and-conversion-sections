# File 14 Status — v1.4.5 Seventh Ten-Round Repository Candidate

## Repository coding status

- Governing freeze: consolidated central governing plan + `SSH-F14-PLAN-2026-v1.0` + Founder-approved additive amendment `SSH-F14-FUTURE-CTI-2026-v2.0` dated 2026-08-10.
- Seventh-cycle baseline: exact pre-review merged `main` `db60c4bc5c37a5c88126b78c31b34c75236f33d7` (`v1.4.4`).
- Software candidate: `1.4.5`.
- Base File 14 database schema: `10005`.
- Future CTI additive schema: `1` (`gcu_future_records`, `gcu_future_reports`).
- The 24 Future CTI requirements `F14-FUT-01` through `F14-FUT-24` remain inside File 14's approved trust/conversion scope; File 14 does not become doctor, clinic, appointment, payment, verification or shell source of truth.
- File 20 remains the sole shell/navigation owner; Files 07/08/09 remain canonical doctor/directory/clinic/onboarding owners; File 00 remains authorization/verification authority where contracted.
- Seventh ten-round corrective hardening adds explicit-grant File 00 authorization, empty normalized DB-lock rejection, owner-controlled destination URL integrity, privacy erasure read-back verification, conversion-event UUID identity conflict detection, source-tree bytecode hygiene, and forward-compatible historical regression/review gates.
- Deterministic package target: `14-global-clinic-usp-integration-1.4.5.zip` + SHA-256 + file-level SBOM, generated only from the exact head being evaluated.
- Draft PR: `#10` (`file14-seventh-10-round-corrective-review` → `main`).

## Exact-current-head rule

Historical PRs, commits and green workflow runs are supporting history only. They are not current repository truth merely because they once passed. The durable post-merge rule is that the **exact current `main` SHA** must be independently verified; while a PR is still unmerged, its exact review-head SHA is a separate candidate truth and cannot be described as current `main`.

This v1.4.5 branch may be described as a repository candidate only until the **exact final branch SHA** independently passes PHP 7.4/8.3 quality, policy/contract/reliability/central/Future regression suites, all retained eighty-pass review gates, the dedicated seventh-cycle regression gate, both fresh post-code reviews, baseline integrity and deterministic package/SBOM. After merge, the **exact resulting `main` SHA** becomes the repository truth candidate and must pass the applicable workflows again before `Automated-QA Green` is claimed for current `main`.

The six repository review ledgers are: the retained historical eighty-pass set below.

- `docs/REVIEW-80-LEDGER-v1.4.1.md` — first eighty-pass corrective review.
- `docs/REVIEW-80-SECOND-LEDGER-v1.4.1.md` — second independent eighty-pass review.
- `docs/REVIEW-80-THIRD-LEDGER-v1.4.1.md` — third independent eighty-pass review.
- `docs/REVIEW-80-FOURTH-LEDGER-v1.4.2.md` — fourth independent eighty-pass review.
- `docs/REVIEW-80-FIFTH-LEDGER-v1.4.3.md` — fifth independent eighty-pass review.
- `docs/REVIEW-80-SIXTH-LEDGER-v1.4.4.md` — sixth independent eighty-pass review.

The current sequential ten-round corrective record is `docs/REVIEW-10-SEVENTH-LEDGER-v1.4.5.md`.

No ledger or historical merge SHA is allowed to substitute for exact-current-head evidence.

## Truth-status boundary

`Specified`, `Coded`, `Packaged`, `Automated-QA Green`, `Staging-Accepted`, `Live-Deployed`, and `Operational` remain separate states.

The following remain external release gates until independently proven in the target environment:

- Hostinger-equivalent fresh install and upgrade/migration acceptance, including real verification of base schema `10005` and Future schema `1` with the exact deployed package.
- Real File 00/07/08/09/20/24/25 integration and normal/degraded/unauthorized journeys.
- Real WordPress/MySQL rollback/restore rehearsal and backup consistency evidence.
- 320–1920px, 400% zoom, keyboard, screen-reader, American English LTR and Urdu/Arabic RTL human acceptance.
- Measured p75/p95 performance, slow-network behavior and provider/queue/cache/DB failure drills.
- Claim freshness/revalidation, policy parity, experiment preflight/early-stop, copy-report correction, privacy export/erase, low-bandwidth/GPC/consent and audit/outbox containment scenarios in the target environment.
- Founder visual/copy/functional staging acceptance.
- Controlled production deployment, live smoke test, monitoring and deployed-artifact parity confirmation.

No `Staging-Accepted`, `Live-Deployed` or `Operational` claim is made by this repository status file.

**Exact deployed code is unverified; repository-based diagnosis is provisional with respect to the live site.**
