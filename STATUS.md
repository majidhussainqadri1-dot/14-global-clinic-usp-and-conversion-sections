# File 14 Status — v1.4.1 Eighty-Pass Corrective Candidate

## Repository coding status

- Governing freeze: consolidated central governing plan + `SSH-F14-PLAN-2026-v1.0` + Founder-approved additive amendment `SSH-F14-FUTURE-CTI-2026-v2.0` dated 2026-08-10.
- Software candidate: `1.4.1`.
- Base File 14 database schema: `10004`.
- Future CTI additive schema: `1` (`gcu_future_records`, `gcu_future_reports`) with InnoDB verification/fail-safe state.
- The original 24 Future CTI requirements `F14-FUT-01` through `F14-FUT-24` remain implemented; v1.4.1 is a corrective hardening release rather than a new ownership/scope expansion.
- v1.4.1 corrective controls: immediate public claim-freshness/parity gating; meaningful mandatory experiment guardrail validation; Urdu/Arabic dark-pattern, guarantee and positive-commission checks; per-stage small-cohort suppression; direct-identifier rejection from FAQ aggregate signals; corrected Future safe-mode scenario truth; explicit provisional quality evidence; File-14-scoped REST post-dispatch hardening; dedicated eighty-pass regression gate.
- File 20 remains the sole shell/navigation owner; Files 07/08/09 remain doctor/directory/clinic/onboarding canonical owners. File 14 creates no substitute backend for those domains.
- Deterministic package target: `14-global-clinic-usp-integration-1.4.1.zip` + SHA-256 + file-level SBOM.

## Current review/merge boundary

- Corrective work is carried on Pull Request #4 from `review/file14-80-round-2026-08-10` into `main`.
- The exact PR head, not a historical commit, is the repository candidate truth.
- The eighty-pass review ledger and v1.4.1 corrective traceability are recorded in `docs/REVIEW-80-LEDGER-v1.4.1.md` once the final review/documentation pass is frozen.
- No merge claim is made in this pre-merge status file. A merge may be claimed only after the exact final PR head passes Quality/Package, both fresh post-code reviews, and baseline integrity.

## Truth-status boundary

`Specified`, `Coded`, `Packaged`, `Automated-QA Green`, `Staging-Accepted`, `Live-Deployed`, and `Operational` remain separate states.

The following remain external release gates until independently proven in the target environment:

- Hostinger-equivalent fresh install and upgrade/migration acceptance, including Future CTI additive schema verification.
- Real File 00/07/08/09/20/24/25 integration and degraded-state journeys.
- 320–1920px, 400% zoom, keyboard, screen-reader, English LTR and Urdu/Arabic RTL human acceptance.
- Measured p75/p95 performance and provider/queue/cache/DB failure tests; Future quality score must cease being provisional only when measured inputs exist.
- Claim freshness/revalidation, parity fail-closed, experiment preflight/early-stop, copy-report resolution and low-bandwidth/GPC/consent scenarios in real WordPress/MySQL.
- Backup restore and rollback rehearsal.
- Founder visual/copy/functional staging acceptance.
- Controlled production deployment, live smoke test, monitoring and deployed-artifact parity confirmation.

No `Staging-Accepted`, `Live-Deployed` or `Operational` claim is made by this repository status file.
