# R63 PRE-CODE exact-source forty amended File14/Three Central Plan/Owner audit
Date 2026-10-18. Frozen File14 GitHub `main` HEAD `f3166a19dd42ce6e03e7f3c3534a9e8c9f6539c6`, source plugin v1.4.65. This is repository SOURCE, never deployed WordPress evidence. Governing File14 amended Future CTI24 and Consolidated Three Central Plans compared with File01,07,08,09,20,25 owner scopes and prior owner source.

## 40 distinct FR/FUT plan lenses (16 foundational requirements + 24 future capabilities)
| # | Requirement | Approved plan scope | Pre-code code/owner status |
|---:|---|---|---|
| 1 | F14-FR-001 | Patient value proposition / emergency limitation | Source-contract requirement traced; local staging and deployed proof still UNKNOWN |
| 2 | F14-FR-002 | Doctor global-clinic promise / zero commission | Source-contract requirement traced; local staging and deployed proof still UNKNOWN |
| 3 | F14-FR-003 | Primary CTAs to Files07/09 | Source-contract requirement traced; local staging and deployed proof still UNKNOWN |
| 4 | F14-FR-004 | Patient and doctor steps / owner transition | Source-contract requirement traced; local staging and deployed proof still UNKNOWN |
| 5 | F14-FR-005 | Verified trust and no treatment guarantee | Source-contract requirement traced; local staging and deployed proof still UNKNOWN |
| 6 | F14-FR-006 | Zero-commission/optional donation copy | Source-contract requirement traced; local staging and deployed proof still UNKNOWN |
| 7 | F14-FR-007 | File20 sole placement/route owner | Source-contract requirement traced; local staging and deployed proof still UNKNOWN |
| 8 | F14-FR-008 | File25 visual consumer, File14 content owner | Source-contract requirement traced; local staging and deployed proof still UNKNOWN |
| 9 | F14-FR-009 | File07/08/09 destination dependency health | R63-D02: repository inbound owner .v1 validator fails to reject unknown contract_version pre-inbox; #19 open |
| 10 | F14-FR-010 | Attribution lawful consent/GPC/retention | R63-D01: BusinessPolicyChanged.v1 policy revalidation cached without real UUID; owner trust unverified |
| 11 | F14-FR-011 | Actual File07/08/09 owner evidence, not click inference | External #19 File01 signed producer registration and actual owner event staging OPEN |
| 12 | F14-FR-012 | Founder approvals for experiments | Source-contract requirement traced; local staging and deployed proof still UNKNOWN |
| 13 | F14-FR-013 | American English/Urdu/Arabic localization | Source-contract requirement traced; local staging and deployed proof still UNKNOWN |
| 14 | F14-FR-014 | FAQ policy links, version and freshness | Source-contract requirement traced; local staging and deployed proof still UNKNOWN |
| 15 | F14-FR-015 | Accessible semantic CTAs/mobile/low bandwidth | Source-contract requirement traced; local staging and deployed proof still UNKNOWN |
| 16 | F14-FR-016 | Numeric/source/claim governance | Source-contract requirement traced; local staging and deployed proof still UNKNOWN |
| 17 | F14-FUT-01 | Ethical explicit intent router | Source-contract requirement traced; local staging and deployed proof still UNKNOWN |
| 18 | F14-FUT-02 | Allowlisted doctor/clinic handoff | R63-D02: repository inbound owner .v1 validator fails to reject unknown contract_version pre-inbox; #19 open |
| 19 | F14-FUT-03 | Current public evidence drawer | Source-contract requirement traced; local staging and deployed proof still UNKNOWN |
| 20 | F14-FUT-04 | Claim freshness fail-close | R63-D01: BusinessPolicyChanged.v1 policy revalidation cached without real UUID; owner trust unverified |
| 21 | F14-FUT-05 | Zero-fee parity sentinel | R63-D01: BusinessPolicyChanged.v1 policy revalidation cached without real UUID; owner trust unverified |
| 22 | F14-FUT-06 | Founder-approved jurisdictional claims | Source-contract requirement traced; local staging and deployed proof still UNKNOWN |
| 23 | F14-FUT-07 | Semantic meaning diff | Source-contract requirement traced; local staging and deployed proof still UNKNOWN |
| 24 | F14-FUT-08 | Dark pattern governance | Source-contract requirement traced; local staging and deployed proof still UNKNOWN |
| 25 | F14-FUT-09 | Safe degraded destination failover | R63-D02: repository inbound owner .v1 validator fails to reject unknown contract_version pre-inbox; #19 open |
| 26 | F14-FUT-10 | Preview without cross-owner mutations | Source-contract requirement traced; local staging and deployed proof still UNKNOWN |
| 27 | F14-FUT-11 | Measured conversion quality, not raw CTR | Source-contract requirement traced; local staging and deployed proof still UNKNOWN |
| 28 | F14-FUT-12 | Misleading-copy report lifecycle | Source-contract requirement traced; local staging and deployed proof still UNKNOWN |
| 29 | F14-FUT-13 | Consent-based friction aggregation | Source-contract requirement traced; local staging and deployed proof still UNKNOWN |
| 30 | F14-FUT-14 | Owner-evidenced funnel anomaly | Source-contract requirement traced; local staging and deployed proof still UNKNOWN |
| 31 | F14-FUT-15 | Minimum 10 privacy cohort and strict count types | Source-contract requirement traced; local staging and deployed proof still UNKNOWN |
| 32 | F14-FUT-16 | Experiment approved preflight | Source-contract requirement traced; local staging and deployed proof still UNKNOWN |
| 33 | F14-FUT-17 | Early stop + actual owner-authorized CAS restoration | R63-D01: BusinessPolicyChanged.v1 policy revalidation cached without real UUID; owner trust unverified |
| 34 | F14-FUT-18 | Aggregate-only FAQ suggestion | Source-contract requirement traced; local staging and deployed proof still UNKNOWN |
| 35 | F14-FUT-19 | Message/locale consistency graph | Source-contract requirement traced; local staging and deployed proof still UNKNOWN |
| 36 | F14-FUT-20 | Translation provenance and term lock | Source-contract requirement traced; local staging and deployed proof still UNKNOWN |
| 37 | F14-FUT-21 | AI drafting, no auto-publication | Source-contract requirement traced; local staging and deployed proof still UNKNOWN |
| 38 | F14-FUT-22 | Audited public trust change log | Source-contract requirement traced; local staging and deployed proof still UNKNOWN |
| 39 | F14-FUT-23 | Informational patient chooser | Source-contract requirement traced; local staging and deployed proof still UNKNOWN |
| 40 | F14-FUT-24 | Nonbinding doctor readiness self-check | Source-contract requirement traced; local staging and deployed proof still UNKNOWN |

## Local source defects established BEFORE code edits
**R63-D01 — BusinessPolicyChanged.v1 replay identity:** `GCU_Contracts::queue_policy_revalidation()` sanitizes (rather than validates) an event_id and only rejects empty strings; a corrupt queued policy event with `not-a-uuid`, a string containing control characters, or non-string scalar can become persistent `gcu_policy_revalidation` option and cause callbacks despite lack of canonical event identity. Upstream `GCU_Repository::validate_inbound_event()` validates UUID only at ingest, which cannot protect against bad legacy/corrupt stored inbox records on retry. Require a raw canonical UUID string and preserve original before policy option persistence/notification. No signatures invented; Issue #19 owner-signed attestation still OPEN.
**R63-D02 — malformed schema allowed into inbound inbox:** `GCU_Repository::validate_inbound_event()` has no explicit `.v1` contract-version check; `accept_inbound_event()` consequently hashes/stores a DoctorDirectoryAvailable.v1/ClinicBookingAvailable.v1/DoctorOnboardingAvailable.v1 or BusinessPolicyChanged.v1 payload with explicit unsupported `contract_version` 2/999, but `GCU_Contracts::apply_inbound_event()` (R62) correctly fails closed for destination events later. The mismatch unnecessarily consumes inbox records, retries and dead-letters; it is not reliable first-boundary schema enforcement. Reject any explicitly included noncanonical v1 version before persistence, retaining omitted historical v1. Also fail-closed BusinessPolicyChanged.v1 schema version at consumer boundary.

## Remaining external blockers, not locally resolved
Issue #19 OPEN — signed/versioned File01 owner registry and authentic File07/08/09 producer receipts, consent/replay/staging/deployed proof missing; repository event hashes are not producer signatures.
Issue #22 OPEN — executable File20/25 authorized CAS changed-placement restore, concurrency/rollback staging and live parity missing. File14 must never invent foreign placement writes.
Exact installed WordPress code/ZIP hash, active plugin settings, database schema/wp_options and migrations, runtime logs and live role tests still UNKNOWN.

## Post-freeze corrections and tests
After frozen 40-lens ledger commit: bounded File14 code changes only, forty native policy event replay UUID/option tests plus forty repository private schema validator real-method cases; full PHP 7.4/8.3 quality, existing 40 repeated plan-source scans, two fresh post-code reviews, deterministic ZIP/hash/SBOM; exact PR merged and post-merge main re-test. These source tests are not forty independent browser sessions.

Repository HEAD (pre-edit) `f3166a19dd42ce6e03e7f3c3534a9e8c9f6539c6`; Deployed Version UNVERIFIED; DB Version UNVERIFIED; Migration State UNVERIFIED; Live Verification Status NOT RUN.
Exact deployed code ابھی unverified ہے؛ repository-based diagnosis provisional ہے۔