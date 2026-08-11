# File 14 Status — v1.4.6 Eighth Ten-Round Repository Candidate

## Repository coding status

- Governing freeze: consolidated central governing plan + `SSH-F14-PLAN-2026-v1.0` + Founder-approved additive amendment `SSH-F14-FUTURE-CTI-2026-v2.0` dated 2026-08-10.
- Eighth-cycle starting point: corrected v1.4.5 PR head `0098c63d5f695d683c7057471d9c3683ec805522`, itself based on merged `main` `db60c4bc5c37a5c88126b78c31b34c75236f33d7`.
- Software candidate: `1.4.6`.
- Base File 14 database schema: `10005`.
- Future CTI additive schema: `1` (`gcu_future_records`, `gcu_future_reports`).
- The 24 Future CTI requirements `F14-FUT-01` through `F14-FUT-24` remain inside File 14's approved trust/conversion scope; File 14 does not become doctor, clinic, appointment, payment, verification or shell source of truth.
- File 20 remains the sole shell/navigation and slot-readiness owner; Files 07/08/09 remain canonical destination owners; File 00 remains authorization/verification authority where contracted.
- Eighth ten-round corrective hardening covers concurrent event replay identity, full bounded idempotency fingerprints, serialized privacy-subject initialization, monotonic owner-event freshness, canonical File 20 slot authority, owner-confirmed destination URL readiness, analytics DB fail-close and destination-bound funnel identity.
- Deterministic package target: `14-global-clinic-usp-integration-1.4.6.zip` + SHA-256 + file-level SBOM, generated only from the exact head being evaluated.
- Draft PR: `#10` (historical branch name `file14-seventh-10-round-corrective-review` → `main`); the branch name is not release truth.

## Exact-current-head rule

Historical PRs, commits, workflow runs and packages are supporting history only. The exact current PR-head SHA must independently pass PHP 7.4/8.3 quality, all policy/contract/reliability/central/Future and retained historical regression suites, the dedicated eighth-cycle regression gate, both fresh post-code reviews, baseline integrity and deterministic package/SBOM before this candidate can be called `Automated-QA Green`.

After merge, the **exact current `main` SHA**—that is, the **exact resulting `main` SHA**—becomes the repository truth candidate and must pass the applicable workflows again. A green PR head is not a green post-merge `main` by inference.

The six repository review ledgers are: the retained historical eighty-pass set below.

- `docs/REVIEW-80-LEDGER-v1.4.1.md`
- `docs/REVIEW-80-SECOND-LEDGER-v1.4.1.md`
- `docs/REVIEW-80-THIRD-LEDGER-v1.4.1.md`
- `docs/REVIEW-80-FOURTH-LEDGER-v1.4.2.md`
- `docs/REVIEW-80-FIFTH-LEDGER-v1.4.3.md`
- `docs/REVIEW-80-SIXTH-LEDGER-v1.4.4.md`

The seventh cycle is `docs/REVIEW-10-SEVENTH-LEDGER-v1.4.5.md`; the current eighth cycle is `docs/REVIEW-10-EIGHTH-LEDGER-v1.4.6.md`.

## Truth-status boundary

`Specified`, `Coded`, `Packaged`, `Automated-QA Green`, `Staging-Accepted`, `Live-Deployed`, and `Operational` remain separate states.

The following remain external gates: real staging install/upgrade and schema verification; File 00/07/08/09/20/24/25 real-role integration; browser/accessibility/RTL/LTR/400% zoom acceptance; measured performance and provider/DB/queue failure drills; privacy export/erase and consent/GPC journeys; backup/restore/rollback rehearsal; Founder staging acceptance; controlled production deployment; live smoke test; and deployed-artifact parity confirmation.

No `Staging-Accepted`, `Live-Deployed` or `Operational` claim is made by this repository status file.

**Exact deployed code is unverified; repository-based diagnosis is provisional with respect to the live site.**
