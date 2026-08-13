# File 14 — Seventh Corrective Cycle — Ten-Round Ledger — v1.4.5

## Governing freeze

- Review baseline: exact merged `main` SHA `db60c4bc5c37a5c88126b78c31b34c75236f33d7` (`v1.4.4`, base schema `10005`, Future CTI schema `1`).
- Governing scope: consolidated central plan, `SSH-F14-PLAN-2026-v1.0`, and Founder-approved additive `SSH-F14-FUTURE-CTI-2026-v2.0`.
- Method: each round reviewed the corrected state produced by the prior round. A defect found in a round was corrected before the next round proceeded.
- This ledger is repository evidence only. It does not prove staging, deployed package parity, live database state, live migration state, Founder acceptance, or production operation.

## Ten sequential rounds

| Round | Review focus | Result | Immediate correction |
|---|---|---|---|
| 01 | File 00 authorization boundary and privileged REST/admin actions | **DEFECT** | `gcu_authorize` changed from truthy/default-allow semantics to explicit `true` with default `false`; native WordPress capability remains necessary but never sufficient. |
| 02 | MySQL named-lock normalization/concurrency | **DEFECT** | Reject empty normalized lock scope before constructing the lock name, preventing invalid scopes from collapsing onto a generic shared lock. |
| 03 | Files 07/08/09 destination ownership and File 14 consumer adapters | **DEFECT** | Consumer filter may only reduce owner readiness; it can no longer replace the URL asserted by the canonical owner state/event. |
| 04 | Admin safe mode, repair, rollback, nonce/capability and mandatory audit paths | CLEAN | No new substantive repository defect found. |
| 05 | Privacy export/erase, pseudonymous linkage and Future report anonymization | **DEFECT** | Privacy erasure now reads back `_gcu_measurement_subject_v1`; `done=true` is forbidden while the linkage remains. |
| 06 | Stable integrity/privacy HMAC keys, legacy migration and rollback isolation | CLEAN | No new substantive repository defect found. |
| 07 | Future governance guards, frontend ownership, routing, accessibility and measurement suppression | CLEAN | No new substantive repository defect found. |
| 08 | Observability, queue/retry/dead-letter, audit containment and runtime health boundaries | CLEAN | No new substantive repository defect found in this round. |
| 09 | Repository/package-source hygiene and generated artifacts | **DEFECT** | Removed tracked `scripts/__pycache__/*.pyc`; added `__pycache__/` and `*.py[cod]` exclusions. |
| 10 | Release/documentation-to-code reconciliation and regression evidence | **DEFECT** | Corrected a stale v1.4.4 claim: conflicting reuse of a conversion-event UUID was not actually payload-verified on the runtime path. Added pre-write identity comparison (stage, destination, pseudonymous subject, campaign), HTTP 409 conflict, v1.4.5 release identity, current-repository alias evidence, corrected changelog, forward-compatible sixth-review gates and a dedicated seventh-cycle regression gate. |

## Defect-bearing rounds

**01, 02, 03, 05, 09, 10**

Clean rounds: **04, 06, 07, 08**.

## Corrective invariants added in v1.4.5

1. File 00 authorization is explicit-grant fail closed for each action/object/purpose.
2. Invalid normalized DB-lock scopes cannot create a generic shared lock.
3. File 14 consumer code cannot rewrite owner-confirmed destination URLs from Files 07/08/09.
4. WordPress privacy erasure cannot report completion while the File 14 user-subject linkage persists.
5. A reused conversion-event UUID is idempotent only for the same stage, destination, pseudonymous subject and canonical campaign identity; conflicting reuse returns `gcu_event_identity_conflict` / HTTP 409.
6. Generated Python bytecode is neither tracked nor intended as source/package evidence.
7. Historical sixth-review regression gates preserve their security assertions without freezing the current software version to 1.4.4.

## Release identity

- Software candidate: `1.4.5`.
- Base schema remains `10005`; no database schema change was required by this ten-round cycle.
- Future CTI schema remains `1`; no Future schema change was required.
- Plan identities remain unchanged.
- Canonical planned repository identity remains `14-global-clinic-usp-and-conversion-integration`; the currently deployed GitHub source repository name is recorded in code as the historical/current alias `14-global-clinic-usp-and-conversion-sections` until a separately governed repository rename/migration is approved and executed.

## Acceptance boundary

Repository completion is not staging or live completion. Final repository candidate acceptance requires the exact final head to pass the full quality suite, all historical review gates, the new seventh-cycle regression gate, fresh post-code reviews and deterministic packaging. Staging and live status remain separate external gates.

**Exact deployed code is unverified; repository-based diagnosis remains provisional with respect to the live site.**
