# R52 Cross-Owner Completion Handoff: Issues #19 and #22

Date: 2026-10-17. This is a **verified source evidence and staging acceptance checklist**, not a green deployed/live certificate. Owner repos each have separate source, staging and installed realities. All cases remain **OPEN** until owner-issued artifacts and real WordPress tests exist.

## A. File14 + File01/07/08/09 owner-origin conversion — Issue #19

| Owner | Frozen GitHub main | Source observation | Still needed |
|---|---|---|---|
| File01 | `adf6dbb9980a85f25f7cf4c2ee679b52703c2e71` | `SPF_Event_Bus::publish()` has canonical version/idempotent outbox, but source evidence has not demonstrated approved File14-specific registry registration | Founder-approved versioned event contract, schema registry entry, signature/producer capability and down-stream trust chain |
| File07 | `2f4a89707724fd2b9946600afe10ddab27ec3c2d` | `class-sdd-directory.php` executes `do_action('ddd_after_directory')` in the server-rendered directory; File14 subscriber labels it `destination_served` | Real active-plugin/staging observer; don't rename it `destination_loaded` unless browser-loaded is independently proven |
| File08 | `9dcc8d16ea81d0bbd27184e71bc09ba04d30ba03` | `class-wca-service.php` postcommit `wca_appointment_requested_committed_v1`; File08 `tests/t27-r1-file14-owner-outcome-regressions.php` guards the producer; File14 records only `booking_started` | Native transaction+outbox staging proof, no downstream success inference; event registry and deployed parity |
| File09 | `cfc5f781a766330314dc98c42abeca0eb7786eba` | `class-gdo-application.php` dispatches `gdo_application_submitted` after durable application/outbox post-commit reconciliation | Staged verified application-started hook, no application approval inference; event registry and deployed parity |
| File14 | source candidate 1.4.55 | `GCU_Owner_Outcomes` hooks all three server-side, opt-in consent and pseudonymous subject, suppresses success rate and `live_accepted=false`; `owner_confirmation_contract_ready()` returns false | Verified exact installed plugins, hooks, policy/GPC/sanitization, opt-in + opt-out + erasure, replay/dedup, cohorts, outbox and contract approval |

### Required owner approval and staging acceptance (cannot safely fake)
1. **Source freeze independently for every installed plugin**: active version, SHA256 of installed entry PHP/artifact, actual File01 registry/version, exact installed File07/08/09/14. GitHub SHA must not be called deployed SHA absent parity.
2. **Actual WordPress DB/migration freeze**: File14 `gcu_version`, `gcu_schema_version`, `gcu_future_schema_version`, `gcu_migration_log`, `gcu_upgrade_error`; companion version/schema and owner outbox registration; `SHOW COLUMNS` and migration errors (no private rows exported).
3. **Founder-approved versioned contract**: File01 owner-native producer identity/signature, on-transaction-success emission semantics, event type v1, idempotent opaque ID, consent/purpose/GPC and revocation, retention, correction/erasure, retry/replay protections, minimum cohort and no patient/application/doctor identifiers in File14 event storage. Names `DirectoryServed`, `BookingStarted`, `ApplicationStarted` are not booking success.
4. **Real staging**: loaded File07 directory render, File09 committed application, File08 committed booking request, each fired exactly from its owner; negative cases on failed commit/unauthorized/consent denied/GPC/no hook; distinct 10+ cohort disclosure and 9/rare suppression; real owner outbox and rollback/reconciliation.
5. Signed Founder acceptance + dated CI/staging evidence + exact deployed parity and re-test before Issue #19 is closed or any success score unlocked.

## B. File14 + File20/25 safe changed-placement restoration — Issue #22

**Current source fact:** `GCU_Experiment_Baseline::capture_approved()` stores a File14 approved private snapshot; `ready()` compares placement row shape; `reconcile_after_stop()` only returns `restored=true` on **no drift** (no-op). On drift it *does not overwrite* placement. `transactional_early_stop_guard()` stops experiment, records audit, globally pauses serving and marks manual review when baseline unverified. This is containment, NOT automatic restoration after an actual change. Exact File20 main source `8a4dbcaf4fef8e926b9b834ecfde16c21a0f00ca`, File25 is visual layer owner only.

### Required before restoration code or closure
1. Independently inspect the **deployed** File14, File20 and File25 modules, active route/slot mappings, current real File14 `placements` and `experiments` schema and rows, `wp_options` pause/snapshot state, upgrade logs, and checksum. Distinguish File14-owned placement metadata from File20 shell/slot mutations.
2. Founder approve an **exact pre-experiment baseline and owner mutation map**, source signed snapshot hash/version, experiment↔placement linkage, creator/approver identities, which owner can issue restore commands, idempotency/replay and CAS row-version rules. Never write another owner's tables directly.
3. Define an atomically safe **activate → mutate through canonical owner → stop → CAS restore** path with failed mutation handling. Drifted/unmapped placements are not safe to overwrite: serve canonical default/pause, retain immutable evidence and request manual owner reconciliation.
4. Staging race tests: two experiment activations, stop-vs-activate, missing/corrupt snapshot, changed owner slot, failed rollback, file dependency disabled, idempotent retry, crash/recovery, verified shell+UI output at 320px/400%/RTL and backup/restore, audit/outbox.
5. Only after Founder acceptance, staging, deployed-artifact parity and live rollback re-test should #22 be marked Resolved. Never relabel the current unchanged-placement no-op as an actual restore.

### Safe next action
Connect the relevant WordPress staging site for **read-only environment freeze** (not destructive deploy). Until then exact deployed artifact, DB and migration are UNKNOWN. No direct File20/25 placement writes are authorized by this review.

## Cross-owner status
| Acceptance gate | Current status |
|---|---|
| Three owner producer hooks observed in GitHub source | **Source evidenced** |
| File01 registered, approved end-to-end contract | **Not verified** |
| Real WordPress owner staging and GPC/erasure | **Not verified** |
| File14 immutable baseline / serving pause on drift | **Source evidenced** |
| Actual changed-placement CAS restore through canonical owner | **Not evidenced / Founder approval needed** |
| Installed plugin hashes and WordPress DB/migrations | **Not verified** |
| Live re-test/Production Accepted | **Not verified** |

**Mandatory statement:** Exact deployed code ابھی unverified ہے؛ repository-based diagnosis provisional ہے۔
