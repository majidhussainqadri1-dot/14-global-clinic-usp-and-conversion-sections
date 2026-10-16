# File14 R49 PRE-PATCH 40-source-requirement review and governed pagination integrity

**Source baseline** GitHub `main@cb1572184abf25bd4f084f6555f8986bd523d28f` plugin **v1.4.51**; date 2026-10-17. Base schema source target 10005, Future schema target 1, **not verified installed DB**.

## Original approved specification authority and scope
Actually inspected the user's attached `14-File-14-Global-Clinic-USP-and-Conversion-Integration-v2.0-Future-CTI-24-Amended-2026-08-10.docx` including its explicit rules on privacy, auditable event/record history, governed publishing and review, bounded admin actions, amendment and rollback approvals, FUT12 reporter moderation and FUT22 public reviewed change log; `Sabri-Social-Homeopathy-Platform-Three-Central-Plans-Consolidated-Governing-Master-Plan-2026۔۔۔4۔۔۔۔۔.docx` source ownership/production evidence/review and fail-safe architecture; actual File07, File08, File09 and File25 plans with restricted doctor/clinical content owner contracts. The File14 source does not own booking, doctor verification, File20 placement rollback or private clinical records. Requirements and repository code do not constitute proof of production deployment.

## Exactly 40 inspected File14 FR+FUT requirements, before editing any runtime code

| No. | Requirement | Governing area | Inspection result |
|---:|---|---|---|
| 1 | F14-FR-001 | Patient route/value proposition | Approved source requirement compared; not staging/live verified |
| 2 | F14-FR-002 | Doctor value and zero commission | Approved source requirement compared; not staging/live verified |
| 3 | F14-FR-003 | Primary CTAs and owner destinations | Approved source requirement compared; not staging/live verified |
| 4 | F14-FR-004 | Patient/doctor journey sequence | Approved source requirement compared; not staging/live verified |
| 5 | F14-FR-005 | Trust, verification and no cure guarantee | Approved source requirement compared; not staging/live verified |
| 6 | F14-FR-006 | Zero commission and voluntary support | Approved source requirement compared; not staging/live verified |
| 7 | F14-FR-007 | File20 owned route/placement registry | Approved source requirement compared; not staging/live verified |
| 8 | F14-FR-008 | File25 consumes canonical content blocks | Approved source requirement compared; not staging/live verified |
| 9 | F14-FR-009 | File07/08/09 owner destination health | Approved source requirement compared; not staging/live verified |
| 10 | F14-FR-010 | Consent/GPC attribution | Approved source requirement compared; not staging/live verified |
| 11 | F14-FR-011 | File01/07/08/09 owner-native outcomes | OPEN owner #19: File01/07/08/09 attested native outcome source |
| 12 | F14-FR-012 | Founder experiment lifecycle | Approved source requirement compared; not staging/live verified |
| 13 | F14-FR-013 | en-US/ur-PK/ar-SA localization | Approved source requirement compared; not staging/live verified |
| 14 | F14-FR-014 | FAQ source/policy history | Approved source requirement compared; not staging/live verified |
| 15 | F14-FR-015 | Accessibility, mobile and low-data | Approved source requirement compared; not staging/live verified |
| 16 | F14-FR-016 | Claim basis/freshness/audit | Approved source requirement compared; not staging/live verified |
| 17 | F14-FUT-01 | Intent router no sensitive profiling | Approved source requirement compared; not staging/live verified |
| 18 | F14-FUT-02 | Allowlisted destination handoff | Approved source requirement compared; not staging/live verified |
| 19 | F14-FUT-03 | Trust evidence current-public gate | Approved source requirement compared; not staging/live verified |
| 20 | F14-FUT-04 | Freshness sentinel expiry/review/historic audit | Approved source requirement compared; not staging/live verified |
| 21 | F14-FUT-05 | Commission parity and source copy scan | Approved source requirement compared; not staging/live verified |
| 22 | F14-FUT-06 | Approved jurisdictional text | Approved source requirement compared; not staging/live verified |
| 23 | F14-FUT-07 | Protected semantic copy diff | Approved source requirement compared; not staging/live verified |
| 24 | F14-FUT-08 | Dark-pattern disallow list | Approved source requirement compared; not staging/live verified |
| 25 | F14-FUT-09 | Honest destination failover | Approved source requirement compared; not staging/live verified |
| 26 | F14-FUT-10 | Nonmutating scenario previews | Approved source requirement compared; not staging/live verified |
| 27 | F14-FUT-11 | Provisional evidence-based quality score | Approved source requirement compared; not staging/live verified |
| 28 | F14-FUT-12 | Auditable misleading copy reports | NEW D01: moderator reports_page SQL failure disguised as empty |
| 29 | F14-FUT-13 | Consent and aggregate friction | Approved source requirement compared; not staging/live verified |
| 30 | F14-FUT-14 | Suppressed unverified funnel anomaly | Approved source requirement compared; not staging/live verified |
| 31 | F14-FUT-15 | Minimum-ten cohort privacy | Approved source requirement compared; not staging/live verified |
| 32 | F14-FUT-16 | Founder approval experiment preflight | Approved source requirement compared; not staging/live verified |
| 33 | F14-FUT-17 | Experiment auto-stop AND changed-placement restore | OPEN owner #22: File20/25 Founder-approved real changed-placement rollback |
| 34 | F14-FUT-18 | FAQ gap drafts only | Approved source requirement compared; not staging/live verified |
| 35 | F14-FUT-19 | Locale/claim consistency graph | Approved source requirement compared; not staging/live verified |
| 36 | F14-FUT-20 | Translation source and terminology lock | Approved source requirement compared; not staging/live verified |
| 37 | F14-FUT-21 | AI draft/no autoshare | Approved source requirement compared; not staging/live verified |
| 38 | F14-FUT-22 | Public reviewed change log | NEW D02: governed records_page SQL failure/malformed row/JSON disguised as empty or valid |
| 39 | F14-FUT-23 | Choose safely informational guidance | Approved source requirement compared; not staging/live verified |
| 40 | F14-FUT-24 | Doctor nonbinding readiness | Approved source requirement compared; not staging/live verified |

## Verified NEW GitHub source defects recorded BEFORE correction

**R49-D01 / FUT12 moderation, governed reports**: `GCU_Future_Intelligence::reports_page()`, called via authorized `rest_reports()`, uses `$rows=is_array($rows)?$rows:array()`; a WPDB `get_results=false/null` is thus rendered as successful `items=[]` with null cursor (a falsely empty moderation queue). A malformed report row missing `id` can corrupt pagination and hide cases; 0-id/invalid shapes must be rejected. Must fail closed using 503 `WP_Error`, bounded safe code-level log with no complaint or personal data, preserve correct genuinely empty arrays and pagination cursor semantics.

**R49-D02 / FUT22 governed records and change history, FR016 audit**: `GCU_Future_Intelligence::records_page()`, invoked by privileged `rest_records()`, likewise maps `get_results=false/null` to empty, and casts broken/missing IDs into cursors; corrupted `payload` is passed through `json_array()` which normalizes malformed JSON/nested unexpected formats into valid-looking `[]`. It can falsely hide historically stored records or convert a corrupted governed record into apparent valid empty content. Must fail closed with explicit 503 error for unverified reads/row identity or payload JSON, preserve genuinely empty authorized arrays and well-formed payloads (list or object as policy allows), existing authorization, bounded pagination and no raw sensitive data logs. SQL cursor monotonicity and limit validation required.

These are source-level bugs in R49 code baseline, **NOT a claim that a deployed WordPress database contains the bad rows**.

## Distinct preexisting integration blockers remain
- Issue #19: exact File01 registered, actual staged File07/08/09 owner event/booking/application truth.
- Issue #22: Founder-approved File20/25 genuine changed-placement restoration, CAS, provenance, staging rollback.
Do not count either as R49 source fixed.

## Post-review corrective batch and evidence discipline
After freezing all 40 requirement lenses and these two defects, patch only confirmed local Source methods, add 40 direct real PHP-method SQL/REST mocks (false/null, malformed rows/payload, cursor and positive/empty pages); PHP7.4/8.3, 40 repeated 40-marker source reviews, fresh post-code reviews and deterministic ZIP/SBOM. Candidate plugin `v1.4.52`; no schema change, no owner plugin changes. Merge only after exact PR SHA fully green, then independently verify exact merged-main SHA.

**Repository HEAD:** `cb1572184abf25bd4f084f6555f8986bd523d28f`; **Deployed Version:** UNVERIFIED; **Actual DB Version:** UNVERIFIED; **Migration State:** UNVERIFIED; **Live Verification Status:** NOT RUN. Exact deployed code ابھی unverified ہے؛ repository-based diagnosis provisional ہے۔

## After-forty-lens R49 source corrective batch (not live)

**Strict chronology:** all 40 original FR/FUT source requirement lenses and both proven defects were committed in `456cc678efb5263b89fef0ad9b88b6c6f23ab1b7` BEFORE any PHP runtime code changed.

| Newly proven defect | Corrective code and fail-safe behavior | Regression evidence |
|---|---|---|
| **R49-D01 / FUT12** | `reports_page()` now rejects WPDB false/null with redacted `gcu_future_reports_page_read_failed` 503 error and constant operator log, validates each required report record and strictly increasing positive canonical page ID, refuses nonmonotonic/corrupt pagination. Legitimate SQL `[]` still returns a valid empty authorized page; complete rows provide a bounded cursor and strip raw internal row ID. REST `rest_reports` preserves native auth and propagates WP_Error through `no_store_response()`. | Forty real-method PHP SQL mocks cover false/null, missing/invalid row ID, page ordering, valid empty, positive page and REST error. |
| **R49-D02 / FUT22 / FR016** | `records_page()` similarly rejects SQL false/null, incomplete rows, bad monotonic ID or non-array valid JSON payload, with privacy-safe error and no fake history. Valid JSON object/list decoded to approved array shape and correct first/next cursor. `rest_records()` carries the WP_Error unchanged. | Same forty-case suite covers unreadable DB, corrupted JSON, scalar JSON masquerading as record, malformed/missing keys, good 0–3 record pages and no leak of internal row IDs. |

**Test scope:** 40 newly added direct actual candidate File14 PHP service+REST methods against WPDB/WordPress mocks in `tests/r49-admin-pagination-fail-closed-tests.php` (ten families × four variations), plus previous R41 forty cases and forty sequential repetitions of 40 contract source markers in `scripts/review40-iterative.sh`. GitHub PHP7.4/8.3, Fresh Post-Code Reviews and deterministic package+SBOM still must be green on exact PR commit and new merged main. **Not** 40 actual WordPress staging/live browser E2E runs.

**Counts:** 40 distinct requirement source lenses, 2 newly identified local source errors, 2 source corrections in R49, 2 previous external owner acceptance gaps #19 and #22 remain OPEN, zero verified live deployments.

**Final five-field deployed-evidence ledger:** Repository prepatch main `cb1572184abf25bd4f084f6555f8986bd523d28f`, candidate 1.4.52; Deployed Version **UNVERIFIED**; DB Version **UNVERIFIED**; Migration State **UNVERIFIED**; Live Verification Status **NOT RUN**. Exact deployed code ابھی unverified ہے؛ repository-based diagnosis provisional ہے۔
