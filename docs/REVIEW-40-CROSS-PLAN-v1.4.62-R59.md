# R59: File14 40-lens PRE-CODE exact-source review and root-cause freeze
Date 2026-10-18. Exact repo main `416c6a576a7d88260fbaa9d2c3dd8a6532fe03f8`, source v1.4.61 (not installed state). Governing documentary requirements verified from uploaded `14-File-14-Global-Clinic-USP-and-Conversion-Integration-v2.0-Future-CTI-24-Amended-2026-08-10.docx` and `Sabri-Social-Homeopathy-Platform-Three-Central-Plans-Consolidated-Governing-Master-Plan-2026۔۔۔4۔۔۔۔۔.docx`. Owners and stage contracts for File01/07/08/09/20/25 reviewed as separate repository realities. No live parity evidence.

## Forty distinct requirement scopes (16 FR + 24 FUT)
| # | Approved requirement | Plan scope | Frozen pre-code result |
|---:|---|---|---|
| 1 | F14-FR-001 | Patient value proposition / emergency limitation | Existing source trace only; code must be verified independently; staging unverified |
| 2 | F14-FR-002 | Doctor global-clinic promise / zero commission | Existing source trace only; code must be verified independently; staging unverified |
| 3 | F14-FR-003 | Primary CTAs to Files07/09 | Existing source trace only; code must be verified independently; staging unverified |
| 4 | F14-FR-004 | Patient and doctor steps / owner transition | Existing source trace only; code must be verified independently; staging unverified |
| 5 | F14-FR-005 | Verified trust and no treatment guarantee | Existing source trace only; code must be verified independently; staging unverified |
| 6 | F14-FR-006 | Zero-commission/optional donation copy | Existing source trace only; code must be verified independently; staging unverified |
| 7 | F14-FR-007 | File20 sole placement/route owner | Existing source trace only; code must be verified independently; staging unverified |
| 8 | F14-FR-008 | File25 visual consumer, File14 content owner | Existing source trace only; code must be verified independently; staging unverified |
| 9 | F14-FR-009 | File07/08/09 destination dependency health | Existing source trace only; code must be verified independently; staging unverified |
| 10 | F14-FR-010 | Attribution lawful consent/GPC/retention | Existing source trace only; code must be verified independently; staging unverified |
| 11 | F14-FR-011 | Actual File07/08/09 owner evidence, not click inference | OPEN #19: File01 signed owner registry/attested producer staging |
| 12 | F14-FR-012 | Founder approvals for experiments | R59-D01: unvalidated source actor and row_version at Founder approval snapshot capture |
| 13 | F14-FR-013 | American English/Urdu/Arabic localization | Existing source trace only; code must be verified independently; staging unverified |
| 14 | F14-FR-014 | FAQ policy links, version and freshness | Existing source trace only; code must be verified independently; staging unverified |
| 15 | F14-FR-015 | Accessible semantic CTAs/mobile/low bandwidth | Existing source trace only; code must be verified independently; staging unverified |
| 16 | F14-FR-016 | Numeric/source/claim governance | Existing source trace only; code must be verified independently; staging unverified |
| 17 | F14-FUT-01 | Ethical explicit intent router | Existing source trace only; code must be verified independently; staging unverified |
| 18 | F14-FUT-02 | Allowlisted doctor/clinic handoff | Existing source trace only; code must be verified independently; staging unverified |
| 19 | F14-FUT-03 | Current public evidence drawer | Existing source trace only; code must be verified independently; staging unverified |
| 20 | F14-FUT-04 | Claim freshness fail-close | Existing source trace only; code must be verified independently; staging unverified |
| 21 | F14-FUT-05 | Zero-fee parity sentinel | Existing source trace only; code must be verified independently; staging unverified |
| 22 | F14-FUT-06 | Founder-approved jurisdictional claims | Existing source trace only; code must be verified independently; staging unverified |
| 23 | F14-FUT-07 | Semantic meaning diff | Existing source trace only; code must be verified independently; staging unverified |
| 24 | F14-FUT-08 | Dark pattern governance | Existing source trace only; code must be verified independently; staging unverified |
| 25 | F14-FUT-09 | Safe degraded destination failover | Existing source trace only; code must be verified independently; staging unverified |
| 26 | F14-FUT-10 | Preview without cross-owner mutations | Existing source trace only; code must be verified independently; staging unverified |
| 27 | F14-FUT-11 | Measured conversion quality, not raw CTR | R59-D02: funnel quality SQL grouped stage identity accepts empty/blank values |
| 28 | F14-FUT-12 | Misleading-copy report lifecycle | Existing source trace only; code must be verified independently; staging unverified |
| 29 | F14-FUT-13 | Consent-based friction aggregation | R59-D02: funnel quality SQL grouped stage identity accepts empty/blank values |
| 30 | F14-FUT-14 | Owner-evidenced funnel anomaly | Existing source trace only; code must be verified independently; staging unverified |
| 31 | F14-FUT-15 | Minimum 10 privacy cohort and strict count types | Existing source trace only; code must be verified independently; staging unverified |
| 32 | F14-FUT-16 | Experiment approved preflight | R59-D01: unvalidated source actor and row_version at Founder approval snapshot capture |
| 33 | F14-FUT-17 | Early stop + actual owner-authorized CAS restoration | OPEN #22: File20/25 actual authorized changed-placement CAS restore |
| 34 | F14-FUT-18 | Aggregate-only FAQ suggestion | Existing source trace only; code must be verified independently; staging unverified |
| 35 | F14-FUT-19 | Message/locale consistency graph | Existing source trace only; code must be verified independently; staging unverified |
| 36 | F14-FUT-20 | Translation provenance and term lock | Existing source trace only; code must be verified independently; staging unverified |
| 37 | F14-FUT-21 | AI drafting, no auto-publication | Existing source trace only; code must be verified independently; staging unverified |
| 38 | F14-FUT-22 | Audited public trust change log | Existing source trace only; code must be verified independently; staging unverified |
| 39 | F14-FUT-23 | Informational patient chooser | Existing source trace only; code must be verified independently; staging unverified |
| 40 | F14-FUT-24 | Nonbinding doctor readiness self-check | Existing source trace only; code must be verified independently; staging unverified |

## Source defects established before ANY correction
- **R59-D01 (FR012/FUT16/17; Founder actor audit):** `GCU_Experiment_Baseline::capture_approved($experiment,$approver)` uses `absint($approver)` BEFORE validating input and calculates `approval_version = (int) row_version + 1` without validating raw row_version (nor checking overflow). Negative actor `-7` can become positive `7`, fractional `7.5` becomes `7`, malformed numeric text can become a legitimate record actor, and malformed source version can become a false approved version. Reject any noncanonical signed-int-bounded actor, nonpositive ID, noncanonical version, or PHP_INT_MAX version before storing immutable baseline; do not write File20 placement rows. File14 master mandates actor capability/approval and exact versioned atomic transitions.
- **R59-D02 (FR016/FUT11/13/15; funnel quality):** `GCU_Future_Intelligence::quality_score()` verifies `is_string(funnel_stage)` but NOT nonblank identity. A source SQL row with `funnel_stage = ''` or `'   '` passes validation, is silently excluded from the acquisition allowlist, and can make partial metrics appear verified. `friction_summary()` already rejects blank stages. Match its typed nonblank SQL identity fail-close in quality_score, privacy-minimized audit and null composite; never score unverifiable owner conversion.

## External blockers (not locally completable)
- #19 OPEN: File01 attested/signed event registration, real File07/08/09 owner producer integration, consent/retention and staged deployment proof. Repository hooks do not mean actual booked/completed.
- #22 OPEN: File20/25 actual owner-approved CAS *changed-placement restore*, race and staging rollback. File14 cannot invent writes to owner state; keep mutation experiment disabled and pause/manual review.
- WordPress connector not registered. Deployed plugin/version/hash, actual database schema/wp_options, migration, real runtime logs and live re-test UNKNOWN.
- Source CI, forty repeated 40-marker scans and mocks are NOT forty independent WordPress E2E sessions.

## Ordered remediation / verification
(1) freeze 40 lenses and both defects (this commit); (2) correct only File14-owned source; (3) add 40 direct capture_approved actor/version cases + 40 direct quality_score grouped SQL identity cases; (4) PHP7.4/8.3 full quality, forty repeated plan-contract scans, two independent fresh postcode reviews, deterministic package/SHA/SBOM; (5) merge green exact candidate and repeat on new main. No production resolved claim.

**Five-state pre-code ledger:** Repository HEAD `416c6a576a7d88260fbaa9d2c3dd8a6532fe03f8` / Deployed Version UNVERIFIED / DB Version UNVERIFIED / Migration State UNVERIFIED / Live Verification Status NOT RUN.
Exact deployed code ابھی unverified ہے؛ repository-based diagnosis provisional ہے۔
