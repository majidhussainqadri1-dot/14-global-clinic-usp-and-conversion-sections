# R64 PRE-CODE forty amended File14/central/owner plan integrity review, 2026-10-19

Frozen exact repository main BEFORE any code modification: `93008dc1d49aaf8e62003bb1e032d09703e88db3`, source version v1.4.66. Word documents actually read: amended File14 v2.0 CTI24 and Consolidated Three Central Governing Plans. Original plan explicitly mandates File01-owned routing/registration, owner File07/08/09 destinations, consented funnel events with no inferred booking, File20 shell/placements, File25 UI components, safe error containment and exact trace/release evidence. GitHub != live artifact/DB reality.

## Forty separate requirement-to-source lenses: 16 FR + 24 FUT
| # | Requirement | Owner / plan coverage | Code evidence / remaining proof |
|---:|---|---|---|
| 1 | F14-FR-001 | Patient value proposition / emergency limitation | Exact source requirement traced; deployed acceptance unverified |
| 2 | F14-FR-002 | Doctor global-clinic promise / zero commission | Exact source requirement traced; deployed acceptance unverified |
| 3 | F14-FR-003 | Primary CTAs to Files07/09 | Exact source requirement traced; deployed acceptance unverified |
| 4 | F14-FR-004 | Patient and doctor steps / owner transition | Exact source requirement traced; deployed acceptance unverified |
| 5 | F14-FR-005 | Verified trust and no treatment guarantee | Exact source requirement traced; deployed acceptance unverified |
| 6 | F14-FR-006 | Zero-commission/optional donation copy | Exact source requirement traced; deployed acceptance unverified |
| 7 | F14-FR-007 | File20 sole placement/route owner | Exact source requirement traced; deployed acceptance unverified |
| 8 | F14-FR-008 | File25 visual consumer, File14 content owner | Exact source requirement traced; deployed acceptance unverified |
| 9 | F14-FR-009 | File07/08/09 destination dependency health | R64-D01 persist_option_exact falsely confirms dual JSON encode failure; destination/owner readiness option unverified |
| 10 | F14-FR-010 | Attribution lawful consent/GPC/retention | Exact source requirement traced; deployed acceptance unverified |
| 11 | F14-FR-011 | Actual File07/08/09 owner evidence, not click inference | R64-D02 queued inbox payload_hash/event_id never revalidated before consumer side-effect; #19 still OPEN |
| 12 | F14-FR-012 | Founder approvals for experiments | Exact source requirement traced; deployed acceptance unverified |
| 13 | F14-FR-013 | American English/Urdu/Arabic localization | Exact source requirement traced; deployed acceptance unverified |
| 14 | F14-FR-014 | FAQ policy links, version and freshness | Exact source requirement traced; deployed acceptance unverified |
| 15 | F14-FR-015 | Accessible semantic CTAs/mobile/low bandwidth | Exact source requirement traced; deployed acceptance unverified |
| 16 | F14-FR-016 | Numeric/source/claim governance | Exact source requirement traced; deployed acceptance unverified |
| 17 | F14-FUT-01 | Ethical explicit intent router | Exact source requirement traced; deployed acceptance unverified |
| 18 | F14-FUT-02 | Allowlisted doctor/clinic handoff | R64-D01 persist_option_exact falsely confirms dual JSON encode failure; destination/owner readiness option unverified |
| 19 | F14-FUT-03 | Current public evidence drawer | Exact source requirement traced; deployed acceptance unverified |
| 20 | F14-FUT-04 | Claim freshness fail-close | Exact source requirement traced; deployed acceptance unverified |
| 21 | F14-FUT-05 | Zero-fee parity sentinel | Exact source requirement traced; deployed acceptance unverified |
| 22 | F14-FUT-06 | Founder-approved jurisdictional claims | Exact source requirement traced; deployed acceptance unverified |
| 23 | F14-FUT-07 | Semantic meaning diff | Exact source requirement traced; deployed acceptance unverified |
| 24 | F14-FUT-08 | Dark pattern governance | Exact source requirement traced; deployed acceptance unverified |
| 25 | F14-FUT-09 | Safe degraded destination failover | R64-D01 persist_option_exact falsely confirms dual JSON encode failure; destination/owner readiness option unverified |
| 26 | F14-FUT-10 | Preview without cross-owner mutations | Exact source requirement traced; deployed acceptance unverified |
| 27 | F14-FUT-11 | Measured conversion quality, not raw CTR | Exact source requirement traced; deployed acceptance unverified |
| 28 | F14-FUT-12 | Misleading-copy report lifecycle | Exact source requirement traced; deployed acceptance unverified |
| 29 | F14-FUT-13 | Consent-based friction aggregation | Exact source requirement traced; deployed acceptance unverified |
| 30 | F14-FUT-14 | Owner-evidenced funnel anomaly | R64-D02 queued inbox payload_hash/event_id never revalidated before consumer side-effect; #19 still OPEN |
| 31 | F14-FUT-15 | Minimum 10 privacy cohort and strict count types | R64-D02 queued inbox payload_hash/event_id never revalidated before consumer side-effect; #19 still OPEN |
| 32 | F14-FUT-16 | Experiment approved preflight | Exact source requirement traced; deployed acceptance unverified |
| 33 | F14-FUT-17 | Early stop + actual owner-authorized CAS restoration | File20/25 changed-placement owner CAS external dependency #22 OPEN |
| 34 | F14-FUT-18 | Aggregate-only FAQ suggestion | Exact source requirement traced; deployed acceptance unverified |
| 35 | F14-FUT-19 | Message/locale consistency graph | Exact source requirement traced; deployed acceptance unverified |
| 36 | F14-FUT-20 | Translation provenance and term lock | Exact source requirement traced; deployed acceptance unverified |
| 37 | F14-FUT-21 | AI drafting, no auto-publication | Exact source requirement traced; deployed acceptance unverified |
| 38 | F14-FUT-22 | Audited public trust change log | Exact source requirement traced; deployed acceptance unverified |
| 39 | F14-FUT-23 | Informational patient chooser | Exact source requirement traced; deployed acceptance unverified |
| 40 | F14-FUT-24 | Nonbinding doctor readiness self-check | Exact source requirement traced; deployed acceptance unverified |

## Proven File14-local source integrity gaps, identified in completed scan BEFORE code edits
R64-D01 — F14-FR009/FUT02/FUT09, consented owner readiness option persistence. `GCU_Contracts::persist_option_exact()` executes `hash('sha256',wp_json_encode($value))` and same for stored value without checking `wp_json_encode()` succeeded. When both encodings return boolean false (e.g. corrupt non-UTF-8 bytes), both false values coerce to empty string for hash; identical SHA-256 values cause a **false positive verified persistence**. Require string JSON encoding for both original/stored before constant-time compare. WordPress mock should test actual encoded failure with 40 diverse options, maintaining successful valid values.
R64-D02 — F14-FR011/FUT14, File01/07/08/09 inbound owner replay trust. `GCU_Repository::accept_inbound_event()` records `payload_hash` and `event_id` at ingest, but `process_inbox()` passes `json_decode((string)$row['payload'],true)` directly to `GCU_Contracts::apply_inbound_event()` without validating unchanged SHA256 digest, row-event_id vs payload-event_id or verifying typed, valid original .v1 envelope. Modified/corrupt DB payload can cause a different owner readiness/policy side effect, despite initial hash serving as recorded evidence. Fail closed on incomplete/incorrect stored row envelope/digest/ID/source schema BEFORE calling consumer; retry/dead-letter rather than misapply. Hash is an internal integrity check, NOT a File01 authorized producer signature.

## Separate unresolved owners and deployed realities
Issue #19 OPEN: File01 Founder-approved signed owner event registry + File07 actual destination arrival, File08 real committed request and File09 real submitted application attestation + consent, replay and staged/live verification unavailable. Hash != owner signature.
Issue #22 OPEN: File20/25 executable owner-authorized changed-placement CAS restore, optimistic concurrency and staged rollback unavailable; File14 never mutates foreign tables.
Installed plugin/version/deployed ZIP/checksum, actual DB schema/rows/wp_options, migration state, runtime logs and staging/live re-tests UNKNOWN. Repository HEAD alone is NOT a deployed-code symptom/root cause.

## Post-audit remediation acceptance
Only AFTER frozen forty plan-source lenses: fix D01+D02 within File14. Add 40 direct actual option-verify PHP/WordPress mock scenarios plus 40 native stored inbox envelope/hash/ID source-method cases, including a consumer-noninvocation test on corrupt row. All previous tests, PHP 7.4/8.3, existing repeated 40 complete 40-marker scans, two independent Fresh Reviews, deterministic ZIP/SHA256/SBOM on exact PR and merged main. Do not claim forty independent browser E2E runs.

Pre-edit truth fields: Repository HEAD `93008dc1d49aaf8e62003bb1e032d09703e88db3`; Deployed Version UNVERIFIED; DB Version UNVERIFIED; Migration State UNVERIFIED; Live Verification Status NOT RUN.
Exact deployed code ابھی unverified ہے؛ repository-based diagnosis provisional ہے۔