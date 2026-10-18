# R53 — FROZEN 40-requirement review BEFORE correction
Date: 2026-10-18 (PKT). Exact source baseline: **main@a9bf74cfca80d358b6e5c3bcbeef3d30831e738d**, source plugin 1.4.55. Source-only, **not** installed or deployed truth.

## Governing sources
Original attached File 14 amended v2.0 Future CTI24 (16 FR, 10 NFR, 24 FUT); consolidated Three Central Governing Plans v3.0; File07 Directory, File08 Clinic/Appointments, File09 Onboarding/Verification, File25 Public UI companion plans; current owner registry File01 and File20 shell boundaries. Never assume repository HEAD == installed WordPress artifact.

## Frozen forty source/owner requirement lenses
These 40 are **40 distinct requirement coverage checks**, *not* forty separate browser tests. The repository quality workflow additionally runs `scripts/review40-iterative.sh` with 40 complete 40-marker iterations. The reviewer will separately verify exact-commit CI after changes.
| # | Requirement | Scope reviewed | Pre-correction disposition |
|---:|---|---|---|
| 1 | F14-FR-001 | Patient value proposition / emergency limitation | SOURCE REVIEW: existing implementation/traceability; staging not proven |
| 2 | F14-FR-002 | Doctor global-clinic promise / zero commission | SOURCE REVIEW: existing implementation/traceability; staging not proven |
| 3 | F14-FR-003 | Primary CTAs to Files07/09 | SOURCE REVIEW: existing implementation/traceability; staging not proven |
| 4 | F14-FR-004 | Patient and doctor steps / owner transition | SOURCE REVIEW: existing implementation/traceability; staging not proven |
| 5 | F14-FR-005 | Verified trust and no treatment guarantee | SOURCE REVIEW: existing implementation/traceability; staging not proven |
| 6 | F14-FR-006 | Zero-commission/optional donation copy | SOURCE REVIEW: existing implementation/traceability; staging not proven |
| 7 | F14-FR-007 | File20 sole placement/route owner | SOURCE REVIEW: existing implementation/traceability; staging not proven |
| 8 | F14-FR-008 | File25 visual consumer, File14 content owner | SOURCE REVIEW: existing implementation/traceability; staging not proven |
| 9 | F14-FR-009 | File07/08/09 destination dependency health | SOURCE REVIEW: existing implementation/traceability; staging not proven |
| 10 | F14-FR-010 | Attribution lawful consent/GPC/retention | SOURCE REVIEW: existing implementation/traceability; staging not proven |
| 11 | F14-FR-011 | Actual File07/08/09 owner evidence, not click inference | BLOCKED #19: File01 signed producer registration and staging |
| 12 | F14-FR-012 | Founder approvals for experiments | SOURCE REVIEW: existing implementation/traceability; staging not proven |
| 13 | F14-FR-013 | American English/Urdu/Arabic localization | SOURCE REVIEW: existing implementation/traceability; staging not proven |
| 14 | F14-FR-014 | FAQ policy links, version and freshness | SOURCE REVIEW: existing implementation/traceability; staging not proven |
| 15 | F14-FR-015 | Accessible semantic CTAs/mobile/low bandwidth | SOURCE REVIEW: existing implementation/traceability; staging not proven |
| 16 | F14-FR-016 | Numeric/source/claim governance | SOURCE REVIEW: existing implementation/traceability; staging not proven |
| 17 | F14-FUT-01 | Ethical explicit intent router | SOURCE REVIEW: existing implementation/traceability; staging not proven |
| 18 | F14-FUT-02 | Allowlisted doctor/clinic handoff | SOURCE REVIEW: existing implementation/traceability; staging not proven |
| 19 | F14-FUT-03 | Current public evidence drawer | SOURCE REVIEW: existing implementation/traceability; staging not proven |
| 20 | F14-FUT-04 | Claim freshness fail-close | SOURCE REVIEW: existing implementation/traceability; staging not proven |
| 21 | F14-FUT-05 | Zero-fee parity sentinel | SOURCE REVIEW: existing implementation/traceability; staging not proven |
| 22 | F14-FUT-06 | Founder-approved jurisdictional claims | SOURCE REVIEW: existing implementation/traceability; staging not proven |
| 23 | F14-FUT-07 | Semantic meaning diff | SOURCE REVIEW: existing implementation/traceability; staging not proven |
| 24 | F14-FUT-08 | Dark pattern governance | SOURCE REVIEW: existing implementation/traceability; staging not proven |
| 25 | F14-FUT-09 | Safe degraded destination failover | SOURCE REVIEW: existing implementation/traceability; staging not proven |
| 26 | F14-FUT-10 | Preview without cross-owner mutations | SOURCE REVIEW: existing implementation/traceability; staging not proven |
| 27 | F14-FUT-11 | Measured conversion quality, not raw CTR | SOURCE REVIEW: existing implementation/traceability; staging not proven |
| 28 | F14-FUT-12 | Misleading-copy report lifecycle | SOURCE REVIEW: existing implementation/traceability; staging not proven |
| 29 | F14-FUT-13 | Consent-based friction aggregation | SOURCE REVIEW: existing implementation/traceability; staging not proven |
| 30 | F14-FUT-14 | Owner-evidenced funnel anomaly | SOURCE REVIEW: existing implementation/traceability; staging not proven |
| 31 | F14-FUT-15 | Minimum 10 privacy cohort and strict count types | LOCAL D01/D02: public cohort gate and quality-evidence input accept malformed positive counts |
| 32 | F14-FUT-16 | Experiment approved preflight | SOURCE REVIEW: existing implementation/traceability; staging not proven |
| 33 | F14-FUT-17 | Early stop + actual owner-authorized CAS restoration | BLOCKED #22: changed placement owner CAS restoration absent |
| 34 | F14-FUT-18 | Aggregate-only FAQ suggestion | SOURCE REVIEW: existing implementation/traceability; staging not proven |
| 35 | F14-FUT-19 | Message/locale consistency graph | SOURCE REVIEW: existing implementation/traceability; staging not proven |
| 36 | F14-FUT-20 | Translation provenance and term lock | SOURCE REVIEW: existing implementation/traceability; staging not proven |
| 37 | F14-FUT-21 | AI drafting, no auto-publication | SOURCE REVIEW: existing implementation/traceability; staging not proven |
| 38 | F14-FUT-22 | Audited public trust change log | SOURCE REVIEW: existing implementation/traceability; staging not proven |
| 39 | F14-FUT-23 | Informational patient chooser | SOURCE REVIEW: existing implementation/traceability; staging not proven |
| 40 | F14-FUT-24 | Nonbinding doctor readiness self-check | SOURCE REVIEW: existing implementation/traceability; staging not proven |

## Preliminary proven gaps — no PHP patched during this freeze
* **R53-D01 (FUT15):** `GCU_Future_Policy::cohort_allowed($count,$threshold)` uses `(int)$count` before source validation. Passing `'10.5'`, `'10junk'` or positive floating 10.9 is accepted as cohort of ten even though it is not an observed canonical nonnegative whole count. This public privacy policy entrypoint must fail closed with `valid_aggregate_count`; threshold similarly requires a canonical count.
* **R53-D02 (FUT11/FUT15):** `GCU_Future_Policy::quality_evidence_status` uses `(int)$owner_loaded < 1`, allowing invalid positive count forms (e.g. `'1oops'`) to masquerade as evidence if future owner-attestation is explicitly true. Although Issue #19 currently keeps owner attestation gated off, its policy should reject noncanonical counts before future enablement.
* **Blocker #19**: owner-native File07/08/09 hooks exist in GitHub; File01 specific registry attestation, staging and deployed parity remain UNVERIFIED. No invented success scoring; keep disabled.
* **Blocker #22**: File14 verified no-drift baseline and safe stop/pause are not changed-placement CAS restore through File20/25 owner; preserve containment, require approved owner command and staging. Do not invent direct writes.

## Forty-repeat verification protocol
After preserving this frozen evidence, execute `scripts/review40-iterative.sh` in exact-head GitHub PHP CI (40 complete 40-marker source cycles); new direct-policy regression includes forty input cases. Require PHP7.4 and PHP8.3, both independent fresh-review jobs, source/schema static integrity, deterministic ZIP+SBOM on the exact PR candidate and merged-main. Automated marker loops are not 40 fresh real-world deploy tests.

## Five reality fields
Repository HEAD: a9bf74cfca80d358b6e5c3bcbeef3d30831e738d (pre-correction baseline).
Deployed Version: UNVERIFIED.
Actual DB Version: UNVERIFIED (source targets Base 10005/Future 1).
Migration State: UNVERIFIED.
Live Verification Status: NOT RUN.
Exact deployed code ابھی unverified ہے؛ repository-based diagnosis provisional ہے۔
