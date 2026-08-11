# Release Evidence — v1.4.7 Ninth Ten-Round Repository Candidate

## Governing scope

- Base plan: `SSH-F14-PLAN-2026-v1.0`.
- Founder-approved additive amendment: `SSH-F14-FUTURE-CTI-2026-v2.0`.
- Ninth-cycle starting exact repository candidate: v1.4.6 PR head `677cbd70b5c8b5b727c01f5f8d8538725f75ad7f`, based on `main` `db60c4bc5c37a5c88126b78c31b34c75236f33d7`.
- Software candidate: `1.4.7`.
- Base schema: `10005`; Future CTI schema: `1`.
- Requirements retained: original File 14 FR/NFR plus `F14-FUT-01`–`F14-FUT-24`.
- Ownership remains bounded: File 20 shell/slot authority; Files 07/08/09 destination truth; File 25 public visual/design-system truth; File 00 authorization/verification authority where contracted.
- Draft integration remains PR `#10`; branch naming is historical location, not release truth.

## Exact-head evidence policy

Historical green results are supporting history only. The **exact review/main SHA being accepted** must independently pass PHP 7.4/8.3 quality, all current and retained regression/review gates, two fresh post-code reviews, baseline integrity, deterministic ZIP, SHA-256 and file-level SBOM. For the open PR this means the **exact current PR-head SHA**. GitHub workflows explicitly checkout `${{ github.event.pull_request.head.sha || github.sha }}` and verify `git rev-parse HEAD`. After merge, fresh post-merge exact-main acceptance remains mandatory.

The eighth cycle established and corrected exact-PR-head workflow execution; its historical record remains `docs/REVIEW-10-EIGHTH-LEDGER-v1.4.6.md`.

## Ninth ten-round corrective evidence areas

The ninth cycle reviewed the corrected v1.4.6 exact PR head sequentially and found defects in every round. Corrections include: transactional event-token consumption instead of permission-time burn; strict conversion-event persistence instead of broad `INSERT IGNORE`; repository-level destination requirement; stable privacy-subject fail-close; explicit equal-time owner-event ambiguity handling; strict claim-history persistence; authoritative read-back before content/placement/experiment creation commit; fail-closed Future quality/friction reads; fail-safe anomaly and early-stop complaint-query handling; and private/no-store personalized rendering plus correct `lang`/`dir` for en-US fallback blocks/FAQs.

The current cycle record is `docs/REVIEW-10-NINTH-LEDGER-v1.4.7.md`. Historical review ledgers remain supporting evidence, including `docs/REVIEW-80-THIRD-LEDGER-v1.4.1.md`, `docs/REVIEW-80-FOURTH-LEDGER-v1.4.2.md`, `docs/REVIEW-80-FIFTH-LEDGER-v1.4.3.md`, `docs/REVIEW-80-SIXTH-LEDGER-v1.4.4.md`, `docs/REVIEW-10-SEVENTH-LEDGER-v1.4.5.md`, and `docs/REVIEW-10-EIGHTH-LEDGER-v1.4.6.md`.

## External evidence still mandatory

Repository success cannot prove WordPress/Hostinger staging or live behavior. **Staging, deployed code, live DB/schema/migration** and operational behavior remain separate evidence classes. Mandatory external gates still include exact deployed artifact/checksum parity; fresh install/upgrade with real WordPress/MySQL/InnoDB; File00/07/08/09/20/24/25 integration; browser/accessibility/RTL/LTR/400% zoom; performance/provider/DB/queue failure drills; privacy export/erase; consent/GPC journeys; verified backup/restore/rollback; Founder staging acceptance; controlled production deployment; live smoke tests; and live artifact parity confirmation.

No `Staging-Accepted`, `Live-Deployed` or `Operational` claim is made by this repository document.

**Exact deployed code is unverified; repository-based diagnosis is provisional with respect to the live site.**
