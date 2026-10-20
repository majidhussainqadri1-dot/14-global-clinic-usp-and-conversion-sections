# R69 — Forty distinct WordPress Privacy Callback behavioral cases vs amended File14 and central rules
Date: 2026-10-20 (source review). Frozen original repository HEAD: `0b72b866c8708af6a8e4bae980f0f5ac1fd25336` (File14 source `1.4.70`).
Candidate: File14 source `1.4.71`.

## Inputs and review boundary
1. Attached amended `14-File-14-Global-Clinic-USP-and-Conversion-Integration-v2.0-Future-CTI-24-Amended-2026-08-10.docx` with canonical 16 functional, 10 non-functional, 24 Future CTI scope. Scope includes F14-FR-010/011, F14-NFR-002/003, consented measurements, erasure/export and operations.
2. Attached `Sabri-Social-Homeopathy-Platform-Three-Central-Plans-Consolidated-Governing-Master-Plan-2026۔۔۔4۔۔۔۔۔.docx` (governing privacy/canonical owner restrictions).
3. Existing File14 File07/08/09 owner adapters, File20/25 placement contract boundaries, and source regressions R66–R68; no other owner module files are edited by R69.
4. **40 distinct real File14 PHP callback invocations on mocked database result states** below. GitHub CI separately repeats the complete approved 40 plan-contract scans + R68 40 native owner probes + these R69 40 privacy mocks for **40 complete passes**. This does **not** mean 4,800 independent feature requirements or 40 WordPress browser runs.

## Proven source defects
**R69-D01 — Incomplete personal data export could be called complete.** `GCU_Privacy::export_data()` used `is_array($rows)?array_slice(...):array()` and computed `$event_more`/`$report_more` as false on failed `$wpdb->get_results()` responses. A failed read could therefore return `done=true` with no/partial data. Corrected to return `done=false`, no unverified rows, for both event and report reads.

**R69-D02 — Erasure could be falsely complete while DB COUNT failed.** `GCU_Privacy::erase_data()` cast results of `$wpdb->get_var(COUNT(*))` to integer; null/false became zero, allowing premature `done=true` and user subject-meta removal. Count results now require a canonical nonnegative integer or digits-only decimal string. Failed/invalid counts produce `done=false`, `items_retained=true`, non-sensitive message, and preserve subject-meta for subsequent retries. Successfully verified zero still completes; positive counts still continue. Only File14 source is changed.

## Forty exact scenario reviews
| # | Gate | DB result injected | Required behavior |
|---:|---|---|---|
| 1 | Conversion event export get_results() | false | Failure must never report export done |
| 2 | Conversion event export get_results() | null | Failure must never report export done |
| 3 | Conversion event export get_results() | empty string | Failure must never report export done |
| 4 | Conversion event export get_results() | true | Failure must never report export done |
| 5 | Conversion event export get_results() | floating 3.5 | Failure must never report export done |
| 6 | Conversion event export get_results() | object | Failure must never report export done |
| 7 | Conversion event export get_results() | error text | Failure must never report export done |
| 8 | Conversion event export get_results() | negative integer | Failure must never report export done |
| 9 | Copy-quality report export get_results() | false | Failure must never return partial export marked done |
| 10 | Copy-quality report export get_results() | null | Failure must never return partial export marked done |
| 11 | Copy-quality report export get_results() | empty string | Failure must never return partial export marked done |
| 12 | Copy-quality report export get_results() | true | Failure must never return partial export marked done |
| 13 | Copy-quality report export get_results() | floating 3.5 | Failure must never return partial export marked done |
| 14 | Copy-quality report export get_results() | object | Failure must never return partial export marked done |
| 15 | Copy-quality report export get_results() | error text | Failure must never return partial export marked done |
| 16 | Copy-quality report export get_results() | negative integer | Failure must never return partial export marked done |
| 17 | Conversion event erasure COUNT(*) | false | Failure must not clear subject meta or claim erasure |
| 18 | Conversion event erasure COUNT(*) | null | Failure must not clear subject meta or claim erasure |
| 19 | Conversion event erasure COUNT(*) | empty string | Failure must not clear subject meta or claim erasure |
| 20 | Conversion event erasure COUNT(*) | true | Failure must not clear subject meta or claim erasure |
| 21 | Conversion event erasure COUNT(*) | floating 3.5 | Failure must not clear subject meta or claim erasure |
| 22 | Conversion event erasure COUNT(*) | object | Failure must not clear subject meta or claim erasure |
| 23 | Conversion event erasure COUNT(*) | error text | Failure must not clear subject meta or claim erasure |
| 24 | Conversion event erasure COUNT(*) | negative integer | Failure must not clear subject meta or claim erasure |
| 25 | Copy-quality report anonymization COUNT(*) | false | Failure must not clear subject meta or claim erasure |
| 26 | Copy-quality report anonymization COUNT(*) | null | Failure must not clear subject meta or claim erasure |
| 27 | Copy-quality report anonymization COUNT(*) | empty string | Failure must not clear subject meta or claim erasure |
| 28 | Copy-quality report anonymization COUNT(*) | true | Failure must not clear subject meta or claim erasure |
| 29 | Copy-quality report anonymization COUNT(*) | floating 3.5 | Failure must not clear subject meta or claim erasure |
| 30 | Copy-quality report anonymization COUNT(*) | object | Failure must not clear subject meta or claim erasure |
| 31 | Copy-quality report anonymization COUNT(*) | error text | Failure must not clear subject meta or claim erasure |
| 32 | Copy-quality report anonymization COUNT(*) | negative integer | Failure must not clear subject meta or claim erasure |
| 33 | Export | Verified empty events and reports | Normal success or continuation must remain intact |
| 34 | Export | One event | Normal success or continuation must remain intact |
| 35 | Export | One copy-quality report | Normal success or continuation must remain intact |
| 36 | Export | 201 events, page size 200 | Normal success or continuation must remain intact |
| 37 | Export | 201 reports, page size 200 | Normal success or continuation must remain intact |
| 38 | Erasure | Verified counts zero + metadata removed | Normal success or continuation must remain intact |
| 39 | Erasure | One remaining event | Normal success or continuation must remain intact |
| 40 | Erasure | One remaining report | Normal success or continuation must remain intact |

## Release and cross-owner truth
- File14 v1.4.71 WordPress plugin/release docs, central traceability and forty direct PHP regressions are prepared. CI must pass on the final candidate commit, and then separately on merged `main` before GitHub "Fixed" status is reported.
- File14 cannot infer consented booking outcomes from CTA clicks or from sensitive File08 REST without a separately approved File07/08/09 owner-native event contract. Issues **#19 / #70 OPEN**.
- File20/25 actual changed-placement compare-and-swap baseline restoration is not attested; zero-mutation no-op is not changed-placement repair. Issue **#22 OPEN**.
- Exact installed production/staging WordPress plugin version, deployed files/hash, wp_options, DB version, migration state, owner schema/actual rows, runtime logs, live re-test and deployment parity: **NOT VERIFIED**. Repository HEAD is not installed-code truth.
- Exact deployed code ابھی unverified ہے؛ repository-based diagnosis provisional ہے۔

## Mandatory incident fields
| Field | State |
|---|---|
| Repository HEAD (original frozen) | `0b72b866c8708af6a8e4bae980f0f5ac1fd25336` |
| Deployed Version | UNVERIFIED |
| DB Version | UNVERIFIED |
| Migration State | UNVERIFIED |
| Live Verification Status | NOT RUN / access unverified |
