# File 14 — V12.3 exact-source and deployed-state execution ledger

Date: 2026-11-15. Governing plan: Sabri Website Master Plan V12.3 (2026-11-13).
This ledger separates **plan**, **repository**, **package**, **staging**, and **live** realities.

## Verified repository snapshot before this branch

- GitHub repository: majidhussainqadri1-dot/14-global-clinic-usp-and-conversion-sections
- Starting main HEAD: `d1cd343392d07607c60f0688362cee450e31774a`
- Source plugin version: `1.4.75`, source schema constant: `10005`; future schema constant: `1`.
- Baseline predecessor R74/PR90 merged; original File00 assertions/filter escalation fixed in source, not proven installed.
- Relevant independent owner candidates: File01 2.0.1, File07 1.2.2, File08 1.2.16, File09 1.3.0, File20 existing 1.4.x, File25 0.15.0. These are **repository/source identities only**.
- V12.3 File14 requirements: **16 FR + 10 NFR + 2 CEN + 24 FUT = 52 distinct IDs**. A requirement ID is not a tested or completed feature.

## Unresolved verified source boundaries

| Issue | Scope | Exact source evidence | Release decision |
|---|---|---|---|
| #19 | F14-FR-011; F14-FUT-11/13/14 | `GCU_Future_Policy::owner_confirmation_contract_ready()` returns false; owner event/arrival/consent signature contract still unaccepted. | No owner-confirmed funnel/success score claim |
| #22 | F14-FUT-17 | `experiment_baseline_restoration_ready()` explicitly denies placement-changing experiments; `reconcile_after_stop()` cannot substitute a File20/25 owner-authorized CAS restore. | Deny placement-mutating experiments pending tested atomic rollback |
| #70 | F14-FR-011 | `GCU_Owner_Outcomes::measurement_capabilities()` truthfully records private File08 booking measurement unavailable and booking success rate null. | No inferred booking count, synthetic zero or patient-level tracking |

**Unique V12.3 requirement IDs affected by these known blockers: 5**. This is not a statement that every other requirement is deployed or fully accepted.

## Read-only work delivered on this branch

1. `scripts/v123_source_gate.py` performs a deterministic **repository-only** preflight, distinguishes 52 IDs from completion, tests protected boundaries, enumerates three known cross-owner blocks and emits machine-readable JSON. Source preflight success does not mean operational acceptance.
2. `tests/v123-source-gate-tests.py` checks scope identity, fail-closed parsing, explicit blocker provenance and prevents fabricated live completion.
3. `tools/file14-readonly-deployed-freeze.php` is a host-side **read-only WP-CLI** inventory for exact installed source version, plugin path checksum/tree checksum, selected option-state types and versions, and File14 table/column structure. It emits no raw table rows, medical data, credentials or migration log text. It can only describe the site where executed.
4. `scripts/quality.sh` runs the new static preflight tests and PHP syntax check.

## R77 source-preflight fail-closed correction (2026-11-15)

**Defect proved against merged v1.4.76 audit tooling:** the first version of `scripts/v123_source_gate.py` could mark owner contract #19, changed-placement CAS #22, or booking aggregate #70 as "accepted" merely because a PHP function stopped containing a literal `return false` or a particular null marker. Source text alone can never authenticate a real owner producer, transactionally tested restoration, revocable consent, or deployed WordPress state.

**Correction:** Those three owner acceptance results are permanently false in this source-only script. Instead, the checker separately confirms that the current PHP source still explicitly fails closed. If a future change removes a denial, the source test fails; it never promotes source text into owner/staging/live acceptance. Real acceptance requires independently verified cryptographic/owner/DB/staging evidence outside this tool and an explicit updated release procedure.

**Regression:** `tests/v123-source-gate-tests.py::test_fake_source_activation_never_certifies_owner_acceptance` injects fake PHP `return true` for owner and CAS functions plus a fabricated measured booking count, and checks that no owner issue is falsely closed and the source control gate fails.

This R77 change modifies **repository tooling/tests only**; plugin version remains `1.4.76`. Three owner issues remain OPEN, and deployed version/DB/migration/live remain UNVERIFIED.

## Current external reality and limits

WPVibe had no connected site on 2026-11-15. The WordPress connector lists `https://www.sabrihomeopathy.com` but reports the Jetpack site connection unavailable. Therefore **the installed File14 plugin, deployed checksum, DB/schema version, migration state, wp_options values and runtime logs were not retrieved**.

The read-only freeze utility has not run on the live host and must not be cited as live evidence. Its tree digest is a sorted file-path/SHA-256 content digest, not a ZIP checksum, and parity requires comparing the **same algorithm** against the approved artifact.

## Required acceptance sequence

1. Restore authenticated site access or provide an owner-approved, exact read-only snapshot.
2. Run the freeze utility on the exact live/staging host, independently record migrations, plugin versions, runtime errors and authenticated dependency health. Never infer one environment from another.
3. If deployed source differs from main, halt routine debugging and conduct deployment-parity audit before any live fix.
4. Resolve #19 at File01/07/08/09 owner contracts, with versioned/typed source provenance, consent, dedupe, expiry and staging owner-to-consumer tests.
5. Resolve #70 only after approved revocable optional aggregate design; do not weaken sensitive REST exclusion, GPC, minor privacy, cohort suppression or File08 canonical booking boundaries.
6. Resolve #22 with File20/25 owner-authorized CAS restoration, immutable baseline, transactional race handling, idempotency, rollback and real WordPress staging test.
7. Re-check all 52 V12.3 IDs, D11 approved copy and multi-device WCAG/performance acceptance with File24 independent evidence.
8. Execute exact HEAD PHP7.4/8.3 QA, 40 source rounds, two fresh reviews, deterministic ZIP/SBOM/checksum. Stage, approve, deploy, re-test live, then verify parity.

## Final incident/status fields as of this report

| Field | State |
|---|---|
| Repository HEAD (pre-branch) | `d1cd343392d07607c60f0688362cee450e31774a` |
| Deployed Version | **Unverified** |
| DB Version | **Unverified** |
| Migration State | **Unverified** |
| Live Verification Status | **Unverified** |
| Known open external GitHub blockers | 3 |
| Newly resolved cross-owner blockers | 0 |
| Production resolution | **Not claimed** |

> Exact deployed code ابھی unverified ہے؛ repository-based diagnosis provisional ہے۔
