# File14 R35 — Forty source-plan reviews and real Founder workflow preflight correction

**Date:** 2026-10-14. **Frozen pre-patch GitHub main:** `6ca1344b8aa8b0056454d453066784e1628cda75`; plugin 1.4.37. **Authority:** attached approved File14 `14-File-14-Global-Clinic-USP-and-Conversion-Integration-v2.0-Future-CTI-24-Amended-2026-08-10.docx`, Three-Central-Plans governing charter v3.0, and File07/08/09 + File25 master plans. Source owners File01 and File20 separately confirmed. This is a source/plan audit, not a live website diagnosis.

## Review execution protocol and scope

`scripts/review40-iterative.sh` performs **40 actual consecutive runs** of all **40 source contract assertions** in `tests/forty-plan-regressions.php` (1600 source-marker evaluations), plus **40 parameterized experiment stop/continue/fail-closed scenarios** in `tests/r34-experiment-lifecycle-regressions.php`. R35 adds **40 independently varying actual repository-workflow policy cases** in `tests/r35-experiment-preflight-workflow-tests.php`, reaching `GCU_Repository::validate_transition_target()`, the real safety policy, the parity check and the pause rule. Reviews retain the same frozen source during a pass; no edit in a partially completed pass. Tests are deterministic **WordPress-free source/policy simulations**, not real-site E2E journeys or empirical independent reviewer votes.

## Requirements-to-source plan audit: sixteen FR and twenty-four FUT lenses

| Round | Approved requirement | Exact plan scope / integration | Evidence disposition |
|---:|---|---|---|
| 1 | F14-FR-001 | Patient value proposition; verified discovery and emergency scope | Source trace/contract present; staging/live NOT CLAIMED |
| 2 | F14-FR-002 | Doctor value proposition; zero commission and consent | Source trace/contract present; staging/live NOT CLAIMED |
| 3 | F14-FR-003 | Owner destinations and primary CTAs to File07/09 | Source trace/contract present; staging/live NOT CLAIMED |
| 4 | F14-FR-004 | Patient/doctor How-it-works and File08 appointment stages | Source trace/contract present; staging/live NOT CLAIMED |
| 5 | F14-FR-005 | Claims about verification, reviews, privacy, emergency care | Source trace/contract present; staging/live NOT CLAIMED |
| 6 | F14-FR-006 | 0% commission, direct fees, support cannot purchase ranking | Source trace/contract present; staging/live NOT CLAIMED |
| 7 | F14-FR-007 | File20-owned placement slots and route safety | Source trace/contract present; staging/live NOT CLAIMED |
| 8 | F14-FR-008 | File25-owned visual-system content components | Source trace/contract present; staging/live NOT CLAIMED |
| 9 | F14-FR-009 | File07/08/09 destination health and honest degradation | Source trace/contract present; staging/live NOT CLAIMED |
| 10 | F14-FR-010 | Consent-bound source/medium/campaign retention | Source trace/contract present; staging/live NOT CLAIMED |
| 11 | F14-FR-011 | Actual owner events not inferred from clicks (Issue #19) | Owner integration source present; acceptance BLOCKED (#19) |
| 12 | F14-FR-012 | Hypothesis, variant safety, sample, privacy, Founder approval (R35 FIX) | R35 corrected on actual Founder workflow; source-test PASS pending version CI |
| 13 | F14-FR-013 | en-US approved copy, Urdu/Arabic RTL and regional differences | Source trace/contract present; staging/live NOT CLAIMED |
| 14 | F14-FR-014 | Owner/versioned FAQ and stale-answer reviews | Source trace/contract present; staging/live NOT CLAIMED |
| 15 | F14-FR-015 | Readable headings/focus/contrast and reduced-motion acceptance | Source trace/contract present; staging/live NOT CLAIMED |
| 16 | F14-FR-016 | Source/owner/effective/expiry evidence for every claim | Source trace/contract present; staging/live NOT CLAIMED |
| 17 | F14-FUT-01 | Ethical intent routing without silent profiling | Source trace/contract present; staging/live NOT CLAIMED |
| 18 | F14-FUT-02 | Smart destination handoff; owner allowlist | Source trace/contract present; staging/live NOT CLAIMED |
| 19 | F14-FUT-03 | Trust evidence drawer | Source trace/contract present; staging/live NOT CLAIMED |
| 20 | F14-FUT-04 | Claim freshness sentinel | Source trace/contract present; staging/live NOT CLAIMED |
| 21 | F14-FUT-05 | Zero-commission policy parity (R35 activation gate) | Source trace/contract present; staging/live NOT CLAIMED |
| 22 | F14-FUT-06 | Jurisdiction truthful copy | Source trace/contract present; staging/live NOT CLAIMED |
| 23 | F14-FUT-07 | Semantic copy-diff/meaning risk | Source trace/contract present; staging/live NOT CLAIMED |
| 24 | F14-FUT-08 | Dark-pattern linter | Source trace/contract present; staging/live NOT CLAIMED |
| 25 | F14-FUT-09 | Destination failover | Source trace/contract present; staging/live NOT CLAIMED |
| 26 | F14-FUT-10 | Scenario previews, non-mutating | Source trace/contract present; staging/live NOT CLAIMED |
| 27 | F14-FUT-11 | Measured-only conversion quality score; owner claims provisional | Source trace/contract present; staging/live NOT CLAIMED |
| 28 | F14-FUT-12 | Misleading-copy report, review and correction | Source trace/contract present; staging/live NOT CLAIMED |
| 29 | F14-FUT-13 | Purpose-bound aggregate friction, not patient profiling | Source trace/contract present; staging/live NOT CLAIMED |
| 30 | F14-FUT-14 | Owner-event anomaly; min samples | Source trace/contract present; staging/live NOT CLAIMED |
| 31 | F14-FUT-15 | Small cohort threshold and suppression | Source trace/contract present; staging/live NOT CLAIMED |
| 32 | F14-FUT-16 | Real workflow approval/activation safety preflight (R35 FIX) | R35 corrected on actual Founder workflow; source-test PASS pending version CI |
| 33 | F14-FUT-17 | Experiment auto-stop and approved changed-placement rollback (Issue #22) | Safe-stop source present; changed-placement rollback BLOCKED (#22) |
| 34 | F14-FUT-18 | FAQ gap intelligence | Source trace/contract present; staging/live NOT CLAIMED |
| 35 | F14-FUT-19 | Message consistency graph | Source trace/contract present; staging/live NOT CLAIMED |
| 36 | F14-FUT-20 | Translation provenance/terminology lock | Source trace/contract present; staging/live NOT CLAIMED |
| 37 | F14-FUT-21 | AI-assisted copy needs Founder approval | Source trace/contract present; staging/live NOT CLAIMED |
| 38 | F14-FUT-22 | Public trust and change log | Source trace/contract present; staging/live NOT CLAIMED |
| 39 | F14-FUT-23 | Patient choice guide; nonmedical claims | Source trace/contract present; staging/live NOT CLAIMED |
| 40 | F14-FUT-24 | Doctor readiness remains nonbinding; File09 authoritative | Source trace/contract present; staging/live NOT CLAIMED |

## Root-cause evidence, batched corrective action

**R35-D01 — Actual Founder approval/run bypassed F14-FUT-16.** The REST preview called `GCU_Future_Policy::experiment_preflight()` but `GCU_Repository::validate_transition_target()` accepted a nonempty `guardrails` object, without the actual policy verdict. Thus an experiment could be approved and activated despite disabled privacy/claim/accessibility controls, fake scarcity or prohibited sensitive profiling. **Fix:** mandatory real preflight for both `approved` and `running` owner transitions; invalid policy returns `gcu_experiment_policy_preflight_failed` (HTTP 409) and never mutates experiment status.

**R35-D02 — Disabled mandatory controls could be encoded as numeric zero.** Previously the pure preflight accepted a finite numeric zero for `privacy`, `claim_integrity` or `accessibility` even though its documentation said explicitly disabled settings fail closed. **Fix:** zero rejects these three; a **zero maximum allowable `error_rate` or `complaints`** remains valid.

**R35-D03 — Central business-policy drift was not a hard experiment activation gate.** F14-FUT-05 prohibits activating experiments when zero-commission/free-tier parity is not confirmed. **Fix:** `running` transition now requires the actual `GCU_Future_Intelligence::parity_status()['ok']` after safe preflight, failing HTTP 409 when unverified.

The R35 policy tests use eight legal cases and 32 invalid/unsafe cases, including false/0/off guards, missing guards, dark patterns, minors/health/identity targeting, stale expiration, malformed JSON, a global pause and central policy drift. They also prove permitted zero error/complaint ceilings and neutral copy remain usable.

## Unclosed owner contracts, safety and deployment status

- **Issue #19 (OPEN):** owner-attested cross-file destination/application/booking completion and File01 event registry + consent receipt across File07/08/09 require independent real WordPress staging acceptance. File14 must not promote CTA click to successful booking or claim verified user-level conversion.
- **Issue #22 (OPEN):** current baseline snapshot plus audit, drift containment and safety stop cannot restore an **experiment-owned changed placement** using provenance-bound atomic CAS. Actual File20/25 placement acceptance and staged rollback proof are missing. No speculative undo of unrelated editorial changes.
- Source plugin candidate **1.4.38**, schema targets Base `10005`, Future `1`; no schema migration in this code change.
- **Repository HEAD** before correction: `6ca1344b8aa8b0056454d453066784e1628cda75`. PR/merged HEAD must be separately retrieved and tested.
- **Deployed Version / checksum:** UNVERIFIED.
- **Actual DB Version / tables / wp_options:** UNVERIFIED.
- **Migration State:** UNVERIFIED.
- **Staging Verification:** NOT RUN.
- **Live Verification Status:** NOT RUN.
- **Founder release/acceptance:** NOT ESTABLISHED.

**Next proof:** PHP 7.4 & 8.3, full repeated 40 plan reviews and 40 lifecycle + 40 real preflight simulation cases, two fresh reviews, reproducible package/SBOM on exact final PR and merged main; staging owner-contract permissions/consent/rollback, then documented live deployment parity. No source CI alone can close issues #19/#22.

Exact deployed code ابھی unverified ہے؛ repository-based diagnosis provisional ہے۔
