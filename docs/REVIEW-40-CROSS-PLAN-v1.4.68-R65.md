# R65 — PRE-CODE FROZEN forty amended File14 / central-plan / owner review
Date 2026-10-19. Exact File14 repository `main` pre-code source HEAD `85759882e6e3610ee8047601b8ac7f2a4cc6b914`, plugin v1.4.67. Attached amended File14 Future CTI24 Word plan and Three Central Governing Master Plans read; File01/07/08/09/20/25 independent GitHub source `main` heads rechecked. GitHub is source truth only, not installed WordPress or database.

## 40 distinct plan lenses (16 FR + 24 FUT) inspected BEFORE PHP edits
| # | Requirement | Amended plan / ownership | Pre-code finding / staging status |
|---:|---|---|---|
| 1 | F14-FR-001 | Patient value proposition / emergency limitation | Source owner boundary reviewed; direct deployment acceptance unverified |
| 2 | F14-FR-002 | Doctor global-clinic promise / zero commission | Source owner boundary reviewed; direct deployment acceptance unverified |
| 3 | F14-FR-003 | Primary CTAs to Files07/09 | Source owner boundary reviewed; direct deployment acceptance unverified |
| 4 | F14-FR-004 | Patient and doctor steps / owner transition | Source owner boundary reviewed; direct deployment acceptance unverified |
| 5 | F14-FR-005 | Verified trust and no treatment guarantee | Source owner boundary reviewed; direct deployment acceptance unverified |
| 6 | F14-FR-006 | Zero-commission/optional donation copy | Source owner boundary reviewed; direct deployment acceptance unverified |
| 7 | F14-FR-007 | File20 sole placement/route owner | Issue #22 File20/25 owner CAS restore still external, not authorized for this local patch |
| 8 | F14-FR-008 | File25 visual consumer, File14 content owner | Source owner boundary reviewed; direct deployment acceptance unverified |
| 9 | F14-FR-009 | File07/08/09 destination dependency health | R65-D01: corrupt/absent policy_version silently accepted; stored policy revalidation cannot identify verified change |
| 10 | F14-FR-010 | Attribution lawful consent/GPC/retention | Source owner boundary reviewed; direct deployment acceptance unverified |
| 11 | F14-FR-011 | Actual File07/08/09 owner evidence, not click inference | R65-D01: corrupt/absent policy_version silently accepted; stored policy revalidation cannot identify verified change |
| 12 | F14-FR-012 | Founder approvals for experiments | R65-D02: identical BusinessPolicyChanged.v1 event_id processed twice overwrites receipt and re-fires do_action; idempotent consumer not established |
| 13 | F14-FR-013 | American English/Urdu/Arabic localization | Source owner boundary reviewed; direct deployment acceptance unverified |
| 14 | F14-FR-014 | FAQ policy links, version and freshness | Source owner boundary reviewed; direct deployment acceptance unverified |
| 15 | F14-FR-015 | Accessible semantic CTAs/mobile/low bandwidth | Source owner boundary reviewed; direct deployment acceptance unverified |
| 16 | F14-FR-016 | Numeric/source/claim governance | Source owner boundary reviewed; direct deployment acceptance unverified |
| 17 | F14-FUT-01 | Ethical explicit intent router | Source owner boundary reviewed; direct deployment acceptance unverified |
| 18 | F14-FUT-02 | Allowlisted doctor/clinic handoff | Source owner boundary reviewed; direct deployment acceptance unverified |
| 19 | F14-FUT-03 | Current public evidence drawer | Source owner boundary reviewed; direct deployment acceptance unverified |
| 20 | F14-FUT-04 | Claim freshness fail-close | R65-D02: identical BusinessPolicyChanged.v1 event_id processed twice overwrites receipt and re-fires do_action; idempotent consumer not established |
| 21 | F14-FUT-05 | Zero-fee parity sentinel | R65-D02: identical BusinessPolicyChanged.v1 event_id processed twice overwrites receipt and re-fires do_action; idempotent consumer not established |
| 22 | F14-FUT-06 | Founder-approved jurisdictional claims | Source owner boundary reviewed; direct deployment acceptance unverified |
| 23 | F14-FUT-07 | Semantic meaning diff | Source owner boundary reviewed; direct deployment acceptance unverified |
| 24 | F14-FUT-08 | Dark pattern governance | Source owner boundary reviewed; direct deployment acceptance unverified |
| 25 | F14-FUT-09 | Safe degraded destination failover | Source owner boundary reviewed; direct deployment acceptance unverified |
| 26 | F14-FUT-10 | Preview without cross-owner mutations | Source owner boundary reviewed; direct deployment acceptance unverified |
| 27 | F14-FUT-11 | Measured conversion quality, not raw CTR | Source owner boundary reviewed; direct deployment acceptance unverified |
| 28 | F14-FUT-12 | Misleading-copy report lifecycle | Source owner boundary reviewed; direct deployment acceptance unverified |
| 29 | F14-FUT-13 | Consent-based friction aggregation | Source owner boundary reviewed; direct deployment acceptance unverified |
| 30 | F14-FUT-14 | Owner-evidenced funnel anomaly | R65-D01: corrupt/absent policy_version silently accepted; stored policy revalidation cannot identify verified change |
| 31 | F14-FUT-15 | Minimum 10 privacy cohort and strict count types | R65-D01: corrupt/absent policy_version silently accepted; stored policy revalidation cannot identify verified change |
| 32 | F14-FUT-16 | Experiment approved preflight | Source owner boundary reviewed; direct deployment acceptance unverified |
| 33 | F14-FUT-17 | Early stop + actual owner-authorized CAS restoration | R65-D02: identical BusinessPolicyChanged.v1 event_id processed twice overwrites receipt and re-fires do_action; idempotent consumer not established |
| 34 | F14-FUT-18 | Aggregate-only FAQ suggestion | Source owner boundary reviewed; direct deployment acceptance unverified |
| 35 | F14-FUT-19 | Message/locale consistency graph | Source owner boundary reviewed; direct deployment acceptance unverified |
| 36 | F14-FUT-20 | Translation provenance and term lock | Source owner boundary reviewed; direct deployment acceptance unverified |
| 37 | F14-FUT-21 | AI drafting, no auto-publication | Source owner boundary reviewed; direct deployment acceptance unverified |
| 38 | F14-FUT-22 | Audited public trust change log | Source owner boundary reviewed; direct deployment acceptance unverified |
| 39 | F14-FUT-23 | Informational patient chooser | Source owner boundary reviewed; direct deployment acceptance unverified |
| 40 | F14-FUT-24 | Nonbinding doctor readiness self-check | Source owner boundary reviewed; direct deployment acceptance unverified |

## Root-cause evidence BEFORE code: local File14 source only
**R65-D01 — BusinessPolicyChanged.v1 unverified policy_version:** `GCU_Contracts::queue_policy_revalidation()` reads optional `policy_version` with fallback empty string, sanitizes, and persists it as if an actionable verified owner policy change. A policy revalidation record with no original version cannot prove File01/central approved policy version; malformed versions can also be coerced to empty. File14 `GCU_Repository::validate_inbound_event()` validates only UUID, event name and .v1 contract_version for BusinessPolicyChanged. Require nonempty bounded original policy_version text and validate at first repository admission AND at stored consumer replay. Reject without wp_options writes or do_action. This is input completeness, not a substitute for File01 signed authenticity (#19).
**R65-D02 — BusinessPolicyChanged.v1 replay duplicates local side effects:** current `queue_policy_revalidation()` unconditionally replaces `$current[$id]` and calls `do_action('gcu_policy_revalidation_queued_v1', ...)` for each replay. At-least-once inbox processing, stale processing locks or failed SQL processed-state commit may deliver the same event twice; stored original received_at is rewritten and callbacks run multiple times. Implement source-level exact Event ID deduplication: matching approved payload identity returns true with no new option mutation or callback, while reused ID with changed `policy_version` / `changed_keys` fails closed without overwriting prior evidence. Snapshot/canonical material must be checked BEFORE dedup to prevent malformed duplicate acceptance. First delivery preserved and bounded. Source-only protection cannot guarantee exactly-once external callbacks under crashes; no such claim.

## Separate unresolved cross-owner dependencies and live truth
Issue #19 OPEN: Founder-signed File01 production owner event registry, original attested File07/08/09 events, consent/GPC/replay and actual staging/livesite proof unavailable; no fake signatures. Issue #22 OPEN: approved File20/25 changed-placement CAS restore/race/rollback and actual owner schema parity unavailable; no foreign DB writes.
Source owner head snapshots (not deployed): File01 adf6dbb9980a85f25f7cf4c2ee679b52703c2e71; File07 2f4a89707724fd2b9946600afe10ddab27ec3c2d; File08 92e2422d6a235632810ed4981f151fc3250784aa; File09 cfc5f781a766330314dc98c42abeca0eb7786eba; File20 8a4dbcaf4fef8e926b9b834ecfde16c21a0f00ca; File25 347a4ff4d4c233c5ea6cd82c7786ee5398ea9d1e.

## Acceptance AFTER freeze
Then (not during each scan) make two bounded File14-only source corrections, update legacy test mocks minimally, add forty direct policy-version first-entry/replay scenarios plus forty direct same-ID/idempotence/conflict scenarios including actual native method and no-write/no-callback proof. PR head then merged main: PHP 7.4/8.3 complete tests, forty repeated 40-source-contract scans, two independent Fresh Reviews, deterministic ZIP checksum/SBOM. 40 source rounds are not 40 live WordPress browser acceptance tests.

Five truth fields before source changes: Repository HEAD `85759882e6e3610ee8047601b8ac7f2a4cc6b914`; Deployed Version UNVERIFIED; Actual DB Version UNVERIFIED; Migration State UNVERIFIED; Live Verification Status NOT RUN.
Exact deployed code ابھی unverified ہے؛ repository-based diagnosis provisional ہے۔