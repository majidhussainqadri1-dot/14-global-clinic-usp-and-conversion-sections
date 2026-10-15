# File14 R39 — Forty-plan source review and incomplete copy-parity correction

**Date:** 2026-10-15. **Frozen source main:** `675a26bcdb44087f1767dde0b664bd9f1c44ac07`, plugin **1.4.41**. **Candidate:** **1.4.42**, review branch `review/file14-r39-parity-completeness-20261015`. **Authority:** user-provided *File14 Global Clinic USP and Conversion Integration v2.0 Future CTI24 Amended (2026-08-10)*; Three Central Plans Consolidated Governing Master Plan; the supplied Files07/08/09/25 master plans and File01/20/24 owner boundary contracts. No plan ownership has been transferred by source changes.

## Numbers and evidence classes

- **40 separate approved requirements:** 16 FR and 24 FUT below. These are 40 source/specification **lenses**, not 40 browser workflows.
- **Repeated 40-round rechecks:** the tracked `scripts/review40-iterative.sh` calls the full 40-requirement source regression for **40 sequential rounds** (40×40 source-marker evaluations), through `scripts/quality.sh`; CI evidence is required on exact branch and merged SHA. Repetition is not independent WordPress/staging confirmation.
- **New source defects found:** **2** — unsafe success on failed active-copy SQL read; unsafe success when more than 500 active blocks exceed scan capacity.
- **Locally corrected:** **2/2** in candidate (CI and deployed parity are separate proof gates).
- **New direct method test cases:** **40** with negative/failure/overflow/clean/bad-copy families using the real `parity_status()` and a mock WPDB.
- **Previously open, NOT fixed:** **2** — Issue #19 owner-attested File01/07/08/09 outcome events; Issue #22 real changed-placement provenance/CAS restoration with File20/25.
- **Staging, live and deployed exact code/database/migrations:** NOT VERIFIED. Do not treat CI success as production acceptance.

## Source/plan/owner audit matrix

| Review | Approved ID | Subject | Owner boundary inspected | Finding |
|---:|---|---|---|---|
| 01 | F14-FR-001 | Patient value proposition | Files 07/08 | Source-only reviewed (not staging accepted) |
| 02 | F14-FR-002 | Doctor zero-commission value proposition | Files 09/01 | Source-only reviewed (not staging accepted) |
| 03 | F14-FR-003 | Primary CTAs | Files 07/09/20 | Source-only reviewed (not staging accepted) |
| 04 | F14-FR-004 | How-it-works journeys | Files 07/08/09 | Source-only reviewed (not staging accepted) |
| 05 | F14-FR-005 | Public trust and verifiable evidence | Files 09 | Source-only reviewed (not staging accepted) |
| 06 | F14-FR-006 | Business and free-tier copy | Files 01 | Source-only reviewed (not staging accepted) |
| 07 | F14-FR-007 | Shell placement registry | Files 20/25 | Source-only reviewed (not staging accepted) |
| 08 | F14-FR-008 | Reusable visual blocks | Files 25 | Source-only reviewed (not staging accepted) |
| 09 | F14-FR-009 | 07/08/09 destination health | Files 07/08/09 | Source-only reviewed (not staging accepted) |
| 10 | F14-FR-010 | Consent-safe acquisition attribution | Files 24 | Source-only reviewed (not staging accepted) |
| 11 | F14-FR-011 | Native owner funnel event contract | Files 01/07/08/09 | RELEASE-BLOCKED |
| 12 | F14-FR-012 | Approved A/B experiment governance | Files 01/20 | Source-only reviewed (not staging accepted) |
| 13 | F14-FR-013 | en-US / ur-PK / ar-SA and RTL | Files 25 | Source-only reviewed (not staging accepted) |
| 14 | F14-FR-014 | Versioned FAQ truth | Files 07/09 | Source-only reviewed (not staging accepted) |
| 15 | F14-FR-015 | Accessibility and reduced motion | Files 25 | Source-only reviewed (not staging accepted) |
| 16 | F14-FR-016 | Claim substantiation audit | Files 01/24 | Source-only reviewed (not staging accepted) |
| 17 | F14-FUT-01 | Ethical intent routing | Files 24 | Source-only reviewed (not staging accepted) |
| 18 | F14-FUT-02 | Approved owner handoff | Files 07/08 | Source-only reviewed (not staging accepted) |
| 19 | F14-FUT-03 | Trust evidence drawer | Files 09 | Source-only reviewed (not staging accepted) |
| 20 | F14-FUT-04 | Claim freshness | Files 09/24 | Source-only reviewed (not staging accepted) |
| 21 | F14-FUT-05 | Zero-commission parity scan | Files 01 | Source-only reviewed (not staging accepted) |
| 22 | F14-FUT-06 | Jurisdictional truthful copy | Files 25 | Source-only reviewed (not staging accepted) |
| 23 | F14-FUT-07 | Semantic copy difference | Files 20/25 | Source-only reviewed (not staging accepted) |
| 24 | F14-FUT-08 | Dark-pattern linter | Files 01/24 | Source-only reviewed (not staging accepted) |
| 25 | F14-FUT-09 | Destination fallback | Files 07/08/09 | Source-only reviewed (not staging accepted) |
| 26 | F14-FUT-10 | Nonmutating scenario preview | Files 20/25 | Source-only reviewed (not staging accepted) |
| 27 | F14-FUT-11 | Owner-observed conversion quality | Files 01/07/08/09 | Source-only reviewed (not staging accepted) |
| 28 | F14-FUT-12 | Misleading copy reports and correction | Files 24 | Source-only reviewed (not staging accepted) |
| 29 | F14-FUT-13 | Privacy-minimized friction data | Files 01/07/08/09 | Source-only reviewed (not staging accepted) |
| 30 | F14-FUT-14 | Conversion anomalies | Files 07/08/09 | Source-only reviewed (not staging accepted) |
| 31 | F14-FUT-15 | Small-cohort privacy | Files 24 | Source-only reviewed (not staging accepted) |
| 32 | F14-FUT-16 | Founder experiment preflight | Files 01/20/25 | Source-only reviewed (not staging accepted) |
| 33 | F14-FUT-17 | Early stop and baseline restoration | Files 20/25 | RELEASE-BLOCKED |
| 34 | F14-FUT-18 | FAQ gap aggregates | Files 24 | Source-only reviewed (not staging accepted) |
| 35 | F14-FUT-19 | Message consistency | Files 01/20/25 | Source-only reviewed (not staging accepted) |
| 36 | F14-FUT-20 | Translation provenance | Files 20/25 | Source-only reviewed (not staging accepted) |
| 37 | F14-FUT-21 | Ethical AI copy assistant | Files 07/08/09 | Source-only reviewed (not staging accepted) |
| 38 | F14-FUT-22 | Public trust change log | Files 24 | Source-only reviewed (not staging accepted) |
| 39 | F14-FUT-23 | Patient choose safely | Files 07/08 | Source-only reviewed (not staging accepted) |
| 40 | F14-FUT-24 | Doctor readiness checklist | Files 09 | Source-only reviewed (not staging accepted) |

### R39-D01 — failed active-copy query interpreted as zero policy issues
`GCU_Future_Intelligence::parity_status()` previously used `foreach (is_array($blocks) ? $blocks : array())`. A failed read returns null/false and silently produces an empty scan: the business/claim subset can remain healthy while active marketing copy is unchecked, yielding `ok=true`. **Correction:** explicit `active_copy_scan_unverified` parity issue on invalid result. The zero-commission parity gate now fails closed rather than silently green.

### R39-D02 — 500-row ceiling excluded uninspected active copy
The same method previously queried `LIMIT 500` and scanned that bounded result without overflow information. At least one additional active block could remain unexamined while the method reported `ok=true`. **Correction:** query **LIMIT 501** as overflow sentinel, scan at most 500, and record `active_copy_scan_limit_exceeded` when 501 are present. This remains a *bounded* parity check; >500 requires pagination/inventory review for full assurance. No invented page/claim counts.

### R39 tests
`tests/r39-parity-completeness-tests.php` directly calls the current real method in 40 parameterized SQL-mocked cases: false read, null read, 501-row sentinel, small clean sets, and unsafe copy in the final inspected row. The tracked quality suite calls this test, existing 40×40 source scans, other regression families, two fresh reviews, PHP 7.4/8.3, and package/SBOM checks.

### Release gates and strict truth
**OPEN #19:** Separate owner-origin postcommit record facts must be signed/versioned/accepted by File01 and verified end-to-end in Files07/08/09. Browser CTAs are not completed application/booking evidence.

**OPEN #22:** Existing hashed approved baseline refuses to overwrite unexplained drift. It **does not** restore a proven experiment-owned changed placement and lacks staging proof of atomic rollback. No synthetic restoration or owner-table mutation has been introduced by R39.

**Deployment-parity fields:** Repository HEAD = freeze listed above, exact PR/main recheck after approval needed. Source plugin candidate 1.4.42; source schema targets Base 10005 / Future 1 **not** actual DB. Deployed Version = UNVERIFIED; DB Version = UNVERIFIED; Migration State = UNVERIFIED; Live Verification Status = NOT VERIFIED.

**No deployment authorization given here; no production fix or complete status is claimed.**
