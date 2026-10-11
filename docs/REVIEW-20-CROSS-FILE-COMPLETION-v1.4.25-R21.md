# File 14 — R21 Twenty Independent Current-Source, Central-Plan and Companion-Contract Review Lenses

**Review date:** 2026-10-11. **Frozen source HEAD:** `faf6be5f7860a6517b380b894b2d3fe6a1e15733` (`main`, release `1.4.24`), captured before R21 changes. **Review standard:** File14 Global Clinic USP and Conversion Integration v2.0 Future CTI24 amended 2026-08-10; Three Central Plans Consolidated Governing Master Plan 2026; File07 Doctors Directory v1.2; File09 Global Doctor Onboarding v1.1; File25 Complete Public UI/Timeline final; current File14 repository's source, active tests and File00/01/07/08/09/19/20/24/25 contract adapters. User-provided Word source governs intent; repository defines implemented source truth. **Not** twenty live/browser/staging executions.

All twenty named lenses were completed **before** the first R21 corrective edit. **SOURCE PRESENT** indicates the named source marker/implementation was found and is *not* full end-to-end acceptance.

| Review | Source/plan/cross-file question | Frozen R21 finding |
|---:|---|---|
| 01 | Base F14-FR-001..016 and owner boundaries | SOURCE PRESENT — 16 identities traceable, owner contract declared |
| 02 | Future F14-FUT-01..24 amendment | SOURCE PRESENT — complete 24-feature catalogue, with stated external gates |
| 03 | File00 authentication/role and Founder approval | SOURCE PRESENT — native capability and delegated owner checks |
| 04 | File01 versioned manifests/routes/events | SOURCE PRESENT — canonical manifest, route and permission boundary |
| 05 | File07 doctor directory ownership | SOURCE PRESENT — no replacement doctor master record |
| 06 | File08 clinic/booking ownership | SOURCE PRESENT — destination probing, no false booking confirmation |
| 07 | File09 onboarding/verification | SOURCE PRESENT — owner handoff, verification not self-granted |
| 08 | File19 notification owner/event producer | SOURCE PRESENT — producer/dispatch adapter, owner dependency |
| 09 | File20 sole shell/placement owner | SOURCE PRESENT — semantic slots, canonical shell not duplicated |
| 10 | File24 privacy/security owner | SOURCE PRESENT — consent and security assurance adapter |
| 11 | File25 public visual and state system | SOURCE PRESENT — owner visual contract/fallback |
| 12 | Claim evidence, approvals and expiry | SOURCE PRESENT — public claim freshness, withdrawal and approval gating |
| 13 | en-US, ur-PK, ar-SA RTL/keyboard public journey | SOURCE PRESENT — source language/direction and accessible controls |
| 14 | Consent/GPC/Save-Data/cohort suppression | SOURCE PRESENT — native consent, privacy/aggregation safeguards |
| 15 | Transactional File14 event/outbox and idempotent commands | SOURCE PRESENT — transaction, outbox, unique event_id and expiry infrastructure; **does not imply safe conflicting event UUID deduplication** |
| 16 | Duplicate UUID identity after DB unique-key collision | **R21-D01 confirmed local defect:** `GCU_Repository::record_event()` used `INSERT IGNORE` + `deduplicated=>0===$ins` without DB-transaction recheck of existing event identity. Pre-callback check has an unavoidable race; owner-writer direct caller can bypass REST check |
| 17 | Supplied malformed UUID behaviour | **R21-D02 confirmed local defect:** `wp_is_uuid` failing on a user-supplied syntactically shaped UUID silently minted a new random ID, violating caller's intended idempotency and confusing retries |
| 18 | Explicit public language selector | **R21-D03 confirmed local defect:** public block locale validator accepted arbitrary letter locale; `sanitize_locale` then silently fell back to en-US even when the client explicitly requested an unsupported language |
| 19 | Real File07/08/09 owner-acknowledged booking/application outcomes | **EXTERNAL OPEN #19** — no verified owner-signed consent-safe event contract/schema registration/staging across File01,07,08,09 |
| 20 | FUT-17 experiment baseline restoration | **DESIGN/DEPLOYMENT OPEN #22** — early-stop and audited state change does not prove pre-experiment variant/placement restoration, migration or real staging rollback |

**Frozen result before corrections:** 15 limited source-presence findings, **three** File14 local defects, **two** external/design obligations. The externally dependent results are not incorrectly labelled "fixed" because GitHub CI passes.

## Corrections performed only after review 20

- **R21-D01:** On an ignored event insert, `GCU_Repository::record_event` performs an in-transaction locked `SELECT ... FOR UPDATE` against the unique event ID and compares **event type/version, consent and all seven identity fields** with the new request using `GCU_Future_Policy::same_acquisition_event_identity`. Exact duplicate replays may deduplicate; conflicting UUID reuse rolls back and returns HTTP 409; unprovable 0-row insert rolls back and returns 503. Existing File14 outbox is emitted **only** on successful insert. This protects direct owner-internal calls as well as REST, including SQL uniqueness races.
- **R21-D02:** If a caller explicitly supplied an invalid `event_id`, return HTTP 400 rather than generating a different ID. Accepted UUIDs are normalized to lowercase; missing optional UUID still gets securely generated.
- **R21-D03:** `public_blocks_query_is_typed` now accepts only supported explicit en-US/ur-PK/ar-SA locale forms/shorthands, returning 400 for unsupported explicitly requested locales rather than misrepresenting English as the selected translation; an omitted locale still follows public page context.
- Regressions: new exact-event-identity positive/negative payload cases, type/version/consent/identity field mismatch and unsupported locale cases; source tests mandate locked duplicate proof and explicit UUID validation. **Candidate `1.4.25`; no schema migration** (source base `10005`, Future `1`).

## Unchanged unresolved owner gates

- **Issue #19**: File07/08/09 actual destination/application/booking owner-signed, replay-safe and consented event emissions registered in File01; CTA `selected` must never be called `booking started`.
- **Issue #22**: Founder-approved experiment pre-state mapping/snapshot, variant safety fallback, atomic restoration, schema/migration contract, rollback rehearsal with real staging DB. Status `stopped` alone is not a rollback proof.
- **Staging/live**: fresh install/upgrade, database schema rows/options/migration, checksum parity, browser/RTL/multi-device accessibility, legal/privacy flows, backups and Founder consent require external evidence.

**Truth table:** Repository HEAD = frozen pre-correction `faf6be5f7860a6517b380b894b2d3fe6a1e15733` (candidate exact SHA to be verified separately); Deployed Version = unverified; DB Version = unverified; Migration State = unverified; Live Verification Status = not performed.

**Exact deployed code ابھی unverified ہے؛ repository-based diagnosis provisional ہے۔**
