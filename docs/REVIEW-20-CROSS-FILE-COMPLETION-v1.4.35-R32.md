# File14 R32 — Two remaining cross-file requirements: source integration and safety gates

Date: 2026-10-13. Frozen source baseline: main `efa7b328b8fefcc82e1c209ff839062f80d6be28`. Reviewed governing amended File14 CTI24, consolidated Three Central Plans and File07/08/09/20/25 canonical ownership. Prior 20-lens R31 ledger is retained.

## #19 — Owner-origin outcomes

- Current File07 `ddd_after_directory` is a native server-render callback. This is `destination_served`, **not browser-loaded**.
- Current File09 `gdo_application_submitted` fires after its owner commit. File14 observes it without fetching private evidence.
- File08 branch PR #21 adds `wca_appointment_requested_committed_v1` **only after** the real owner transaction. Optional analytics exceptions never change clinical booking results.
- File14 consumer stores HMAC-derived event IDs, no raw appointment/application/user IDs, no subject_hash, no campaign cross-identity. Consent + GPC + low-data controls. Source identities are independent owner-specific event names. Aggregate suppression threshold 10 per cohort/stage.
- Existing browser `ClinicUSPFunnelEvent` must never be treated as a verified appointment/application. The funnel does not fabricate correlated dropoff or success rates.
- **Not yet accepted:** File01 schema/provenance registration, independent cross-plugin WordPress staging, actual File07/08/09 deployed contract versions, privacy retraction tests and live validation. Issue #19 remains OPEN.

## #22 — Experiment baseline, no silent rollback claim

- On Founder approval the File14 owner saves an immutable, hash-bound versioned baseline in the existing private governed-record table; it contains strictly scoped File14 placement rows and creator/effective version.
- On activation the owner rechecks the approved snapshot and placement row versions, including a second check in the transaction.
- On early-stop the experiment is transitioned and audited atomically. When placements remain unchanged, it records a verifiable no-op baseline reconciliation; when missing, drifted or ambiguous it cannot claim restore, and enforces a persistent serving pause plus manual review.
- No unrelated File20 shell, File07 profile, File08 booking or File09 verification state may be mutated by this feature.
- **Not yet accepted:** an actual experiment-owned placement mutation pathway with approved ownership CAS ledger, verified atomic reverse mutation and staging backup/replay/rollback proof. Issue #22 remains OPEN.

## Evidence gates

Source candidate version `1.4.35`, schema target Base `10005`/Future `1`; no new table. Current exact PR/merged-main PHP 7.4/8.3, regressions, independent reviews, deterministic package/SBOM required. Source CI does not prove operational readiness. 

Repository HEAD: frozen prepatch `efa7b328b8fefcc82e1c209ff839062f80d6be28`; current PR/merged final HEAD must be retrieved separately.
Deployed Version: UNVERIFIED.
DB Version: UNVERIFIED.
Migration State: UNVERIFIED.
Live Verification Status: NOT RUN.

**Exact deployed code ابھی unverified ہے؛ repository-based diagnosis provisional ہے۔**
