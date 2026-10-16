# File14 R48 — PRE-PATCH 40-requirement source/ownership inspection ledger

**Frozen source HEAD:** `f67e49a4a0f1cdcf7da8cf25f2833f05033410b7` (GitHub main); plugin **v1.4.50**. **Date:** 17 October 2026. This ledger is committed BEFORE any R48 code changes.

**Authoritative supplied plans actually reviewed:** amended File14 v2.0 CTI24, explicitly `F14-FUT-19` consistency of semantic block/claim **locale, placement and page variants** with mandatory missing-locale/divergent-linked-claim/outdated-variant detection; `F14-FUT-20` authoritative terminology/locale governance and `F14-FR-016` claim evidence/audit; consolidated **Three Central Plans Governing Master Plan** canonical owner/source truth, no fabricated implementation status and privacy policy; attached File07/08/09 companion workflow ownership and File25 presentation semantics. The source repository is NOT live deployed or companion-owner truth.

**40 distinct lenses below:** 16 FR + 24 FUT requirements reviewed as exact approved scopes. `scripts/review40-iterative.sh` will separately repeat the 40 source-marker contracts for 40 sequential rounds; this is not forty independent staging/browser/live audits.

| Lens | Approved ID | Governing concern | Source review result BEFORE changes |
|---:|---|---|---|
| 1 | F14-FR-001 | Patient route/value proposition | Reviewed as source boundary; not a WordPress E2E/live attestation |
| 2 | F14-FR-002 | Doctor value and zero commission | Reviewed as source boundary; not a WordPress E2E/live attestation |
| 3 | F14-FR-003 | Primary CTAs and owner destinations | Reviewed as source boundary; not a WordPress E2E/live attestation |
| 4 | F14-FR-004 | Patient/doctor journey sequence | Reviewed as source boundary; not a WordPress E2E/live attestation |
| 5 | F14-FR-005 | Trust, verification and no cure guarantee | Reviewed as source boundary; not a WordPress E2E/live attestation |
| 6 | F14-FR-006 | Zero commission and voluntary support | Reviewed as source boundary; not a WordPress E2E/live attestation |
| 7 | F14-FR-007 | File20 owned route/placement registry | Reviewed as source boundary; not a WordPress E2E/live attestation |
| 8 | F14-FR-008 | File25 consumes canonical content blocks | Reviewed as source boundary; not a WordPress E2E/live attestation |
| 9 | F14-FR-009 | File07/08/09 owner destination health | Reviewed as source boundary; not a WordPress E2E/live attestation |
| 10 | F14-FR-010 | Consent/GPC attribution | Reviewed as source boundary; not a WordPress E2E/live attestation |
| 11 | F14-FR-011 | File01/07/08/09 owner-native outcomes | OWNER #19: File01/07/08/09 native outcomes still OPEN |
| 12 | F14-FR-012 | Founder experiment lifecycle | Reviewed as source boundary; not a WordPress E2E/live attestation |
| 13 | F14-FR-013 | en-US/ur-PK/ar-SA localization | Reviewed as source boundary; not a WordPress E2E/live attestation |
| 14 | F14-FR-014 | FAQ source/policy history | Reviewed as source boundary; not a WordPress E2E/live attestation |
| 15 | F14-FR-015 | Accessibility, mobile and low-data | FR15 locale/400% scope; Live UX unverified |
| 16 | F14-FR-016 | Claim basis/freshness/audit | FR16 evidence integrity: consistency failures must not be clean |
| 17 | F14-FUT-01 | Intent router no sensitive profiling | Reviewed as source boundary; not a WordPress E2E/live attestation |
| 18 | F14-FUT-02 | Allowlisted destination handoff | Reviewed as source boundary; not a WordPress E2E/live attestation |
| 19 | F14-FUT-03 | Trust evidence current-public gate | Reviewed as source boundary; not a WordPress E2E/live attestation |
| 20 | F14-FUT-04 | Freshness sentinel expiry/review/historic audit | Reviewed as source boundary; not a WordPress E2E/live attestation |
| 21 | F14-FUT-05 | Commission parity and source copy scan | Reviewed as source boundary; not a WordPress E2E/live attestation |
| 22 | F14-FUT-06 | Approved jurisdictional text | Reviewed as source boundary; not a WordPress E2E/live attestation |
| 23 | F14-FUT-07 | Protected semantic copy diff | Reviewed as source boundary; not a WordPress E2E/live attestation |
| 24 | F14-FUT-08 | Dark-pattern disallow list | Reviewed as source boundary; not a WordPress E2E/live attestation |
| 25 | F14-FUT-09 | Honest destination failover | Reviewed as source boundary; not a WordPress E2E/live attestation |
| 26 | F14-FUT-10 | Nonmutating scenario previews | Reviewed as source boundary; not a WordPress E2E/live attestation |
| 27 | F14-FUT-11 | Provisional evidence-based quality score | Reviewed as source boundary; not a WordPress E2E/live attestation |
| 28 | F14-FUT-12 | Auditable misleading copy reports | Reviewed as source boundary; not a WordPress E2E/live attestation |
| 29 | F14-FUT-13 | Consent and aggregate friction | Reviewed as source boundary; not a WordPress E2E/live attestation |
| 30 | F14-FUT-14 | Suppressed unverified funnel anomaly | Reviewed as source boundary; not a WordPress E2E/live attestation |
| 31 | F14-FUT-15 | Minimum-ten cohort privacy | Reviewed as source boundary; not a WordPress E2E/live attestation |
| 32 | F14-FUT-16 | Founder approval experiment preflight | Reviewed as source boundary; not a WordPress E2E/live attestation |
| 33 | F14-FUT-17 | Experiment auto-stop AND changed-placement restore | OWNER #22: File20/25 actual placement rollback still OPEN |
| 34 | F14-FUT-18 | FAQ gap drafts only | Reviewed as source boundary; not a WordPress E2E/live attestation |
| 35 | F14-FUT-19 | Locale/claim consistency graph | NEW D01 malformed claim set/duplicate locale; NEW D02 unbounded 501 sentinel missing |
| 36 | F14-FUT-20 | Translation source and terminology lock | FUT20 terminology lock distinct File14+File25 locale ownership |
| 37 | F14-FUT-21 | AI draft/no autoshare | Reviewed as source boundary; not a WordPress E2E/live attestation |
| 38 | F14-FUT-22 | Public reviewed change log | Reviewed as source boundary; not a WordPress E2E/live attestation |
| 39 | F14-FUT-23 | Choose safely informational guidance | Reviewed as source boundary; not a WordPress E2E/live attestation |
| 40 | F14-FUT-24 | Doctor nonbinding readiness | Reviewed as source boundary; not a WordPress E2E/live attestation |

## Two NEW, confirmed SOURCE-level defects frozen before any edit

**R48-D01 — FUT19 consistency integrity / incomplete claim sets and duplicate locale ambiguity.** `GCU_Future_Intelligence::consistency_graph()` tests only `isset` on row fields and decodes `claim_keys` using `json_array`, which returns `[]` when populated JSON is malformed, JSON scalar, or otherwise not a claim list. The graph can compare invalid claim sets as empty and certify `ok=true`, `verified=true`. It also indexes `$nodes[$key][$locale]` without detecting two active rows for the same key+locale: later content hides earlier linked-claim divergence. Further, array/nested non-string identity or textual fields may raise PHP warnings or create erroneous hashes. Fix fail-closed per-row strict scalar validation, canonical nonempty key/locale, well-formed JSON list of canonical claim strings (empty JSON list allowed as a truthful explicit set) and duplicate block-locale rejection, without leaking raw content in operator logs. This is source evidence, NOT proof malformed live rows exist.

**R48-D02 — FUT19 / source completeness and availability.** `consistency_graph()` currently loads every active block without an explicit SQL bound; its memory cost may scale without limit. A finite bounded governance audit must use **LIMIT 501 sentinel**, refuse `verified=true` if 501 rows are returned (the remaining records are uninspected), and preserve existing explicit SQL false/null error status. It must not call an incomplete scan clean. For valid 0..500 rows, normal missing-locale and divergent-claim checks remain intact. A bounded source scan cannot certify production database content absent real staging/live evidence.

## Existing OWNER/acceptance blockers, NOT local file defects

Issue **#19**: authentic File01-registered, versioned, consented native File07/08/09 destination/application/booking event contracts and real staged owner acknowledgments. Issue **#22**: Founder-approved actual File20/25 changed-placement CAS/provenance rollback with staging baseline restoration. Both remain OPEN. Source fixes may not close them.

## Completion sequence, only AFTER frozen 40-lens review

One local batch fixes D01/D02 in Future Intelligence consistency graph; adds 40 direct actual-PHP-method SQL/WordPress mocked tests (invalid claim JSON, duplicate locale, incomplete scalar, 501 limit, healthy/empty/missing-locale/claim divergence, protected REST and daily fail-closed); applies PHP7.4/8.3 quality gate, forty repeated 40-source-marker scans, two fresh reviews and reproducible ZIP/SBOM. Candidate version **v1.4.51**; Base schema target `10005`, Future schema target `1` unchanged. Merge only after exact PR HEAD checks succeed, then recheck merged main.

**Repository HEAD:** `f67e49a4a0f1cdcf7da8cf25f2833f05033410b7`; **Deployed Version:** UNKNOWN; **Actual DB Version:** UNKNOWN; **Migration State:** UNKNOWN; **Live Verification:** NOT RUN. Exact deployed code ابھی unverified ہے؛ repository-based diagnosis provisional ہے۔
