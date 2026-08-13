# File 14 — Eighty-Pass Review & Corrective Ledger — v1.4.1

**Scope:** repository/source/governance/test/package evidence for File 14 only.
**Governing sources:** consolidated central governing plan; `SSH-F14-PLAN-2026-v1.0`; Founder-approved `SSH-F14-FUTURE-CTI-2026-v2.0`.
**Baseline reviewed:** `main` at `34f44f95646ce21299b6f416d5fa39bae4beb769`.
**Corrective release:** `1.4.1`; base schema `10004`; Future CTI schema `1`.
**Truth boundary:** repository review is not Hostinger staging/live evidence and does not establish deployed-code, DB/schema/migration or operational truth.

Every round re-opened one defined failure class. A defect was corrected before the corresponding final-state gate could pass. “QA-harness defect” means the production implementation was already correct but the newly written review assertion was itself inaccurate and was immediately corrected rather than weakening the product rule.

| Round | Review subject | Result / correction |
|---:|---|---|
| 01 | Governing scope, 24 Future IDs, ownership | PASS — no new defect. |
| 02 | Request-time claim freshness | **DEFECT:** scheduled-only window could leave stale claim public; added immediate freshness/parity fail-closed guard. |
| 03 | Experiment guardrail semantics | **DEFECT:** key presence could accept false/empty values; meaningful mandatory-value validation added. |
| 04 | Urdu/Arabic ethical-copy protection | **DEFECT:** protection was English-centric; Urdu/Arabic scarcity/guarantee/commission guards added. |
| 05 | Small-cohort friction privacy | **DEFECT:** individual stages below 10 could leak under total cohort ≥10; per-stage/drop-off suppression added. |
| 06 | FAQ aggregate minimization | **DEFECT:** direct contact/identity/patient markers could enter aggregate signal; rejected before governed use/storage. |
| 07 | Scenario safe-mode truth | **DEFECT:** safe-mode scenario could reflect module-enabled state; Future safe-mode and module-enabled split. |
| 08 | Quality evidence truth | **DEFECT:** unmeasured evidence could appear insufficiently provisional; `unverified_metrics` + provisional enforcement added. |
| 09 | Corrective release identity | **DEFECT:** changed source under 1.4.0 would collide with prior artifact identity; patch release bumped/aligned to 1.4.1. |
| 10 | Multilingual false-positive safety | **DEFECT:** first paid-visibility regex could reject truthful “support does not buy ranking”; negation-aware guard + regression added. |
| 11 | REST post-dispatch scope | **DEFECT:** first hardening could affect non-File14 REST responses; restricted to `/gcu/v1/`. |
| 12 | Contract regression version | **DEFECT (CI):** contract suite still asserted 1.4.0; aligned to 1.4.1. |
| 13 | Contract scope assertion | **DEFECT (CI):** double-quoted `$route` assertion interpolated variable; changed to literal non-interpolating assertion. |
| 14 | Central-plan regression version | **DEFECT (CI):** central-plan suite still asserted 1.4.0; aligned to 1.4.1. |
| 15 | `STATUS.md` truth | **DEFECT:** stale v1.4.0/old merge status; rewritten as truthful 1.4.1 corrective candidate. |
| 16 | Release-evidence truth | **DEFECT:** release evidence still named 1.4.0 current candidate; corrected to 1.4.1/exact-head policy. |
| 17 | Corrective traceability | **DEFECT:** no dedicated v1.4.1 round ledger; this 80-pass ledger added. |
| 18 | Base schema/version separation | PASS — no new defect. |
| 19 | Future schema/InnoDB verification | PASS — no new defect. |
| 20 | Install/upgrade locking | PASS — no new defect. |
| 21 | Transaction boundaries | PASS — no new defect. |
| 22 | Rollback snapshot integrity | PASS — no new defect. |
| 23 | Non-destructive uninstall | PASS — no new defect. |
| 24 | Audit-chain integrity | PASS — no new defect. |
| 25 | Single-use measurement token | PASS — no new defect. |
| 26 | Atomic rate limiting | PASS — no new defect. |
| 27 | Durable idempotency | PASS — no new defect. |
| 28 | Outbox retry/dead-letter | PASS — no new defect. |
| 29 | Inbox/stale-lock recovery | PASS — no new defect. |
| 30 | Same-origin scheme/host/port | PASS — no new defect. |
| 31 | Owner-readiness non-elevation | PASS — no new defect. |
| 32 | File 20 placement readiness | PASS — no new defect. |
| 33 | File 20 shell/navigation ownership | PASS — no new defect. |
| 34 | File 25 visual/design boundary | PASS — no new defect. |
| 35 | File 07 directory boundary | PASS — no new defect. |
| 36 | File 08 clinic/booking boundary | PASS — no new defect. |
| 37 | File 09/File 00 verification boundary | PASS — no new defect. |
| 38 | File 00 authority review assertion | **QA-HARNESS DEFECT:** first gate incorrectly searched loader for class symbol rather than loaded capability file; assertion corrected to real load/boundary evidence. Product authority boundary remained intact. |
| 39 | Public browsing law | PASS — no new defect. |
| 40 | Privileged Future REST permission assertion | **QA-HARNESS DEFECT:** first gate expected nonexistent generic `admin_permission`; implementation correctly uses least-privilege `can_manage_content`, `can_manage_experiments`, `can_view_analytics`, `can_system_check`, `can_approve_claims`; gate corrected to those callbacks. |
| 41 | Object/state transition revalidation | PASS — no new defect. |
| 42 | Direct companion write / IDOR boundary | PASS — no new defect. |
| 43 | Consent before measurement | PASS — no new defect. |
| 44 | Global Privacy Control | PASS — no new defect. |
| 45 | Save-Data/reduced-data | PASS — no new defect. |
| 46 | Sensitive-route exclusion | PASS — no new defect. |
| 47 | Attribution route/expiry boundary | PASS — no new defect. |
| 48 | Privacy export | PASS — no new defect. |
| 49 | Privacy erasure | PASS — no new defect. |
| 50 | Pseudonym generation/TTL | PASS — no new defect. |
| 51 | Misleading-copy report privacy | PASS — no new defect. |
| 52 | Founder-governed public records | PASS — no new defect. |
| 53 | FAQ suggestion non-publication | PASS — no new defect. |
| 54 | AI non-auto-publication | PASS — no new defect. |
| 55 | AI approved-claim boundedness | PASS — no new defect. |
| 56 | Sensitive experiment profiling | PASS — no new defect. |
| 57 | Experiment early-stop | PASS — no new defect. |
| 58 | Business-parity review assertion | **QA-HARNESS DEFECT:** first gate assumed internal keys `zero_commission`/`optional_support`; actual policy expresses the canonical parity statement and `parity_status()` sentinel. Gate corrected to real contract evidence. Product parity sentinel remained intact. |
| 59 | Trust evidence drawer | PASS — no new defect. |
| 60 | Public material change log | PASS — no new defect. |
| 61 | Patient Choose-Safely guide | PASS — no new defect. |
| 62 | Doctor readiness non-binding | PASS — no new defect. |
| 63 | en-US/ur-PK/ar-SA completeness | PASS — no new defect. |
| 64 | RTL/LTR behavior | PASS — no new defect. |
| 65 | Terminology lock/provenance | PASS — no new defect. |
| 66 | 44px target | PASS — no new defect. |
| 67 | Keyboard/focus | PASS — no new defect; human staging remains external. |
| 68 | Reduced motion | PASS — no new defect. |
| 69 | Forced colors | PASS — no new defect. |
| 70 | 320px-class reflow | PASS — no new defect; browser staging remains external. |
| 71 | 400% zoom evidence boundary | PASS — no false repository claim; human acceptance remains pending. |
| 72 | Back/Home contract | PASS — no new defect. |
| 73 | Degraded SEO/noindex | PASS — no new defect. |
| 74 | Inline executable markup gate | PASS — no new defect. |
| 75 | Embedded-secret scan | PASS — no new defect. |
| 76 | PHP 7.4 quality target | PASS at final-state gate required. |
| 77 | PHP 8.3 quality target | PASS at final-state gate required. |
| 78 | Deterministic double build | PASS at final-state gate required. |
| 79 | ZIP path/CRC + SHA-256 + SBOM | PASS at final-state gate required. |
| 80 | Truth-status / Live-First boundary | PASS — repository success never equals staging/live/operational success. |

## Defect-round index

Defects were discovered in rounds **02, 03, 04, 05, 06, 07, 08, 09, 10, 11, 12, 13, 14, 15, 16, 17, 38, 40 and 58**. Of these, rounds **38, 40 and 58** were defects in the new review harness, not failures in the production File 14 behavior; they were corrected immediately so the gate validates the real architecture instead of a fictitious implementation detail. Round **01** and all other rounds found no additional repository defect.

## Acceptance condition

This ledger is valid only if the **exact final PR head containing it** passes PHP 7.4 + PHP 8.3 quality, policy/contract/reliability/central/Future/Review80 tests, `scripts/review80.py` 80/80, both fresh post-code reviews, baseline integrity, deterministic 1.4.1 packaging, SHA-256 and SBOM. Any later change reopens the exact-head gate.
