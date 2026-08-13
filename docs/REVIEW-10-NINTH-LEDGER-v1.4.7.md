# File 14 — Ninth Ten-Round Corrective Review — v1.4.7

Governing plans: consolidated central plan + `SSH-F14-PLAN-2026-v1.0` + `SSH-F14-FUTURE-CTI-2026-v2.0`.

Starting exact repository candidate: `677cbd70b5c8b5b727c01f5f8d8538725f75ad7f` on PR #10. Each round reviewed the corrected state produced by the preceding round. This ledger is repository evidence only; it is never staging/live evidence.

| Round | Finding | Correction before next round |
|---|---|---|
| 01 | The one-time measurement event token was consumed in the REST permission callback before the event mutation. A later validation/write failure could therefore burn a token although no event was committed. | Split non-consuming token validation from consumption; REST permission now validates only, while `record_event()` consumes the token inside the owned event transaction so rollback preserves token truth on failed mutation. |
| 02 | Conversion-event persistence used broad `INSERT IGNORE`; a non-duplicate database error could be misclassified as successful deduplication. | Replaced broad ignore semantics with strict INSERT; only an authoritative existing identical event identifier is treated as a duplicate, otherwise the transaction fails. |
| 03 | Destination-required funnel invariants existed at REST level only, leaving internal repository callers able to create destination-bound stages without a destination. | Enforced destination requirement inside `record_event()` for `cta_selected`, `destination_loaded`, `application_started`, and `booking_started`. |
| 04 | Failure to establish a stable pseudonymous measurement subject could allow an empty subject hash to enter event persistence. | Require a valid 64-character privacy-safe subject hash; inability to establish it fails closed with a safe 503 before persistence. |
| 05 | Two distinct owner-readiness events with the same occurrence second were silently resolved by ignoring the later-received event. | Older events remain stale/idempotent; same timestamp + same event ID is idempotent; same timestamp + different event ID is now an explicit ambiguous-order 409 with observability warning. |
| 06 | Claim-history persistence also used broad `INSERT IGNORE`, potentially masking non-duplicate write failures. | Use strict INSERT; on uniqueness conflict, accept only an authoritative equivalent status/hash/snapshot for the same claim/version, otherwise rollback. |
| 07 | Content, placement, and experiment creation could commit after write without requiring authoritative read-back of the created state. | Added mandatory public-id/status/version read-back verification before commit; missing or mismatched state rolls back and fails closed. |
| 08 | Future CTI quality/friction reads could turn database failures into empty, suppressed, or apparently healthy analytics. | Added explicit DB-error checks and safe 503 propagation for event, claim, report, and friction reads; admin rendering also fails safely. |
| 09 | Anomaly/experiment early-stop safety could fail open when anomaly or complaint-report queries failed, because missing results could resemble insufficient sample or zero complaints. | Anomaly query failure becomes `query_failed`/high-severity suppressed state; complaint-query failure is an explicit early-stop safety breach and is logged. |
| 10 | Logged-in/personalized File 14 surfaces used public revalidation caching, risking shared-cache leakage; en-US fallback blocks/FAQs on Urdu/Arabic routes could inherit the wrong page `lang`/`dir`. | Logged-in responses are private/no-store; guest shared-cache responses vary by language/cookie; en-US fallback block/FAQ containers carry explicit correct language/direction. Final QA also removed temporary corrective machinery, advanced release identity to v1.4.7 without schema change, and added permanent regression/evidence gates. |

## Final round classification

Defect-bearing rounds in this ninth ten-round cycle: **01, 02, 03, 04, 05, 06, 07, 08, 09, 10**.

## Release identity

- Software candidate: `1.4.7`.
- Base File 14 schema: `10005`.
- Future CTI schema: `1`.
- No database migration/version increase was invented for these code-only corrections.

## Truth boundary

No staging, deployed version, live DB, migration, Founder acceptance or live verification claim is created by this ledger. Exact repository-head QA must still pass after the final permanent tests/docs/cleanup changes, and after merge the exact resulting `main` SHA must be tested again.

**Exact deployed code is unverified; repository-based diagnosis is provisional with respect to the live site.**
