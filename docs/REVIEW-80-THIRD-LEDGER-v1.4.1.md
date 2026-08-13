# File 14 — Third Independent Eighty-Pass Review & Corrective Ledger — v1.4.1

**Baseline re-opened:** `main` at `67cb09c7b49bcab4a338ce2402f5d3e5d14b62ab`.
**Governing scope:** consolidated central governing plan + `SSH-F14-PLAN-2026-v1.0` + `SSH-F14-FUTURE-CTI-2026-v2.0`.
**Method:** eighty independent repository failure classes. When a defect was found, its root cause was corrected before that class could be accepted.
**Truth boundary:** this ledger is source/test/package evidence only. It is not Hostinger staging, deployed-artifact, live DB/schema/migration, live workflow or operational evidence.

| Round | Independent review subject | Finding and immediate correction |
|---:|---|---|
| 01 | Exact baseline and governing identity | PASS — third review was reopened from exact `main` baseline `67cb09c7...`, not from historical PR assumptions. |
| 02 | Durable repository status truth | **DEFECT:** `STATUS.md` still described PR #4 / an older merge as the operative repository truth after PR #5 had already become `main`; replaced with an exact-current-head policy that cannot become truthful merely by age. |
| 03 | Durable release-evidence truth | **DEFECT:** release evidence had not incorporated the second/third review chain and still framed only the earlier corrective pass; rewritten to require exact current SHA evidence and all three ledgers. |
| 04 | File 14 ownership boundary | PASS — File 20 remains shell owner; Files 07/08/09 remain directory/clinic/onboarding owners; no duplicate domain backend introduced. |
| 05 | Base read/runtime readiness | **DEFECT:** base readiness guarded mutations but ordinary reads could still proceed when code/schema version truth was stale; added `GCU_Install::ready_for_runtime()` and made mutation readiness delegate to it. |
| 06 | File-14-owned destination readiness | **DEFECT:** local destination availability depended only on `gcu_enabled`; it could claim ready during a pending schema/version upgrade. Local destination health now uses base runtime readiness. |
| 07 | Public `/blocks` runtime fail-close | **DEFECT:** blocks checked safe mode only; version/schema mismatch could still serve governed copy. The route now fails closed through runtime readiness. |
| 08 | Public `/destinations` runtime fail-close | **DEFECT:** destinations checked safe mode only; it now fails closed on exact base runtime readiness as well. |
| 09 | Measurement token runtime boundary | **DEFECT:** token issue/consume permission paths did not reject a pending base runtime upgrade. Both REST and repository token paths now require current runtime/mutation readiness. |
| 10 | Frontend routes, shortcodes and measurement assets | **DEFECT:** public frontend paths relied mainly on `gcu_enabled`; routes, renderers, shortcodes and measurement asset enqueueing now use the same runtime truth as REST. |
| 11 | Base background workers | **DEFECT:** outbox, inbox and lifecycle cleanup could continue while the base runtime was unready. Workers now skip in that state. |
| 12 | Future runtime dependence on base runtime | **DEFECT:** Future CTI runtime checked base enablement and Future schema state but not the exact base code/schema version boundary. Future runtime now inherits base runtime readiness. |
| 13 | Future background/admin/report runtime paths | **DEFECT:** Future daily/hourly work, cleanup, business-policy handling, report operations, assets and admin surfaces could execute outside one uniform readiness boundary; explicit runtime guards were added. |
| 14 | Immediate stale-copy cache invalidation | **DEFECT:** public REST/HTML copy used positive-age/SWR caching, so a withdrawn or stale claim could remain client/cache-visible after DB withdrawal. Governed public responses/routes now require revalidation (`no-cache`, `max-age=0`, `must-revalidate`) while retaining ETag/Vary where applicable. |
| 15 | Privacy exporter/eraser subject separation | **DEFECT:** WordPress personal-data export/erase mixed the browser operator's guest attribution cookies into an arbitrary requested email subject. Cookie-derived guest data was removed from email-subject export/erase semantics. |
| 16 | Block-level freshness | **DEFECT:** `active_blocks()` did not exclude a block whose own `review_due_at` had elapsed. Query-time block freshness is now mandatory. |
| 17 | Linked-claim fail-close | **DEFECT:** an active block could remain public after one of its governed linked claims became unavailable/stale. Active blocks now collect linked claim keys and suppress any block whose required claim is not currently public/fresh. |
| 18 | Claim query-time freshness | **DEFECT:** `public_claims()` checked status/effective/expiry but not `review_due_at`; stale claims now fail closed immediately without waiting for cron. |
| 19 | Founder-approved copy review horizon | **DEFECT:** a custom copy transition to `founder_approved` set approver/time but no new review due date, allowing indefinite approval. The transition now assigns the governed review horizon. |
| 20 | Authorization-adapter non-elevation | **DEFECT:** `gcu_authorize` filters could return true after native `current_user_can()` returned false, allowing an adapter to elevate authority. Native denial now returns false before any adapter; filters may restrict, never elevate. |
| 21 | Canonical claim determinism | **DEFECT:** canonical claim source strings used runtime translation calls, so initial database truth/parity could depend on the installer/request locale. Canonical governed claims are now deterministic American-English source strings; locale rendering remains a separate layer. |
| 22 | Controlled safe repair lifecycle | **DEFECT:** safe repair could bypass the complete serialized repair boundary and did not make Future schema repair part of the repair result. A single locked install/upgrade repair path now performs base repair and controlled Future schema verification. |
| 23 | Public readiness anti-abuse | **DEFECT:** the public doctor-readiness calculation endpoint had no dedicated request-rate bound. A privacy-safe database rate limit was added. |
| 24 | Public report multilingual sensitive-data rejection | **DEFECT:** copy-quality report screening was English-centric; Urdu/Arabic identity/contact/clinical markers could pass. Three-locale sensitive-marker screening is now enforced. |
| 25 | FAQ aggregate multilingual sensitive-data rejection | **DEFECT:** FAQ aggregate screening had the same English-centric gap. Urdu/Arabic markers were added to the direct-identifier/clinical-data rejection gate. |
| 26 | Scenario Laboratory publication boundary | **DEFECT:** `scenario_note` records could be marked public even though scenarios are governance-only diagnostics. Public scenario-note publication is now rejected. |
| 27 | Future report privacy export/erase | **DEFECT:** logged-in users' pseudonymous Future copy-quality reports were absent from WordPress privacy export/erase. They are now exportable and their actor attribution is anonymized on erasure while governance records remain retained. |
| 28 | Future report privacy pagination | **DEFECT:** the first privacy correction exported at most the first 200 Future reports. Future report export now paginates with the WordPress exporter page/offset contract and contributes to `done`. |
| 29 | Corrective-code variable artifact | **DEFECT:** an intermediate correction generated valid but needless variable-variable syntax `${'all_claims'}`. It was normalized to ordinary `$all_claims` before final acceptance. |
| 30 | Base partial-schema detection | **DEFECT:** base schema verification proved table existence/InnoDB but not required columns, so a partial migration with a current option could look healthy. Critical-column sets are now verified per table. |
| 31 | Future partial-schema detection | **DEFECT:** Future schema verification had the same table/engine-only weakness. Required columns for Future records/reports are now verified. |
| 32 | Controlled Future force-verification | **DEFECT:** `ensure_schema()` could trust the stored Future schema option and return without a structural check even on controlled repair/activation paths. A force-verification mode was added for controlled lifecycle operations. |
| 33 | Rollback concurrency | **DEFECT:** rollback was not serialized with install/upgrade/recovery. Rollback now acquires the same named install lock and releases it in `finally`. |
| 34 | Future rollback coverage | **DEFECT:** rollback snapshots/restoration covered base governed tables only. Future governance records/reports plus Future schema/safe-mode options are now included when present. |
| 35 | Deactivation cron ownership | **DEFECT:** plugin deactivation cleared base cron hooks but left Future daily/hourly hooks scheduled. Future hooks are now cleared too. |
| 36 | Base structural-drift containment | **DEFECT:** the daily base governance check could report missing tables/engine problems but did not force base safe mode. Structural schema verification failure now records an upgrade error and enters safe mode. |
| 37 | Future structural-drift containment | **DEFECT:** Future daily governance trusted version/options after initial install. It now performs a structural schema verification and enters Future safe mode on failure. |
| 38 | REST traceability | **DEFECT:** File 14 REST errors/responses had stable codes/messages but no guaranteed request trace identifier. Post-dispatch hardening now adds `X-GCU-Trace-ID` throughout the File 14 namespace. |
| 39 | Base privileged REST rate limits | **DEFECT:** authenticated content/placement/experiment/withdraw/workflow mutations were idempotent but lacked an explicit bounded request-rate gate. Per-scope mutation rate limits were added. |
| 40 | Future state-changing REST idempotency/rate | **DEFECT:** Future report creation, governed-record writes and claim revalidation did not uniformly implement durable idempotency plus mutation rate bounds. The REST paths now use `X-GCU-Idempotency-Key`, the durable command store and scoped rate limits. |
| 41 | Audit persistence failure containment | **DEFECT:** if the audit-chain lock/insert failed after a governed write, callers could continue with the module still enabled. Audit persistence failure now forces base safe mode and structured error logging. |
| 42 | Durable outbox persistence failure containment | **DEFECT:** required event-envelope encoding/outbox insertion failure returned false but did not contain further operation. Outbox persistence failure now forces base safe mode and structured logging. |
| 43 | Corrective-runner idempotency | **DEFECT (QA machinery):** a temporary corrective workflow re-ran a superseded exact-text patch and failed after the underlying source had already advanced. The runner was narrowed to only the still-pending corrective script. |
| 44 | Corrective-script source-signature drift | **DEFECT (QA machinery):** a final patch helper expected `public_response($data,...)` while the actual method was type-hinted `public_response(array $data,...)`; the runner corrected the exact signature before applying the patch, then PHP lint passed. |
| 45 | Temporary correction machinery | **DEFECT (release hygiene):** corrective workflows/scripts are useful only during repair and must not ship as permanent release machinery. Final acceptance requires their deletion and a gate proving they are absent. |
| 46 | File 20 shell/navigation ownership | PASS — no global shell/navigation implementation moved into File 14. |
| 47 | Files 07/08/09 destination ownership | PASS — File 14 consumes availability contracts and does not elevate or duplicate canonical owners. |
| 48 | Duplicate post/content backend | PASS — no alternate WordPress post backend was introduced. |
| 49 | Public destination DTO minimization | PASS — public DTO remains an explicit allowlist and omits internal owner/contract/freshness metadata. |
| 50 | Strict same-origin handoff | PASS — scheme, host and effective port are enforced. |
| 51 | Measurement consent/GPC | PASS — measurement remains consent-bound and Global Privacy Control is honored. |
| 52 | Reduced-data behavior | PASS — Save-Data/reduced-data suppresses nonessential measurement/intelligence behavior. |
| 53 | Sensitive-route measurement exclusion | PASS — sensitive route classes remain outside measurement. |
| 54 | Attribution minimization | PASS — acquisition attribution remains allowlisted, signed/bounded and excludes direct identifiers. |
| 55 | Single-use event token | PASS — token consumption remains atomic and one-time. |
| 56 | Atomic database rate limiting | PASS — rate buckets still increment atomically in durable storage. |
| 57 | Durable idempotent command store | PASS — duplicate/retry semantics retain persisted command state and bounded stale-lock recovery. |
| 58 | Durable outbox retry/dead-letter | PASS — retry/dead-letter processing remains bounded and durable. |
| 59 | Durable inbox/idempotent inbound events | PASS — unique event identity and stale-lock recovery remain. |
| 60 | Tamper-evident audit chain | PASS — previous-hash/row-hash verification remains and audit persistence failure now also triggers containment. |
| 61 | Quality small-cohort suppression | PASS — exact sample count remains hidden below the threshold. |
| 62 | Anomaly small-cohort suppression | PASS — current/baseline exact counts remain hidden below threshold. |
| 63 | Friction per-stage privacy | PASS — per-stage small-cohort sanitization remains. |
| 64 | FAQ suggestion publication | PASS — FAQ intelligence remains suggestion-only and cannot auto-publish. |
| 65 | AI draft publication | PASS — AI copy remains draft-only/approved-claim bounded and cannot auto-publish. |
| 66 | Founder public-record approval | PASS — governed Future public records retain Founder-level approval controls. |
| 67 | Experiment safety governance | PASS — mandatory meaningful guardrails, sensitive-profiling prohibition and early-stop controls remain. |
| 68 | 0% commission policy | PASS — positive platform-commission drift remains blocked. |
| 69 | One free approved-core tier | PASS — approved core remains free. |
| 70 | Optional support/no advantage | PASS — voluntary support cannot purchase visibility/ranking/basic-service advantage. |
| 71 | EN/UR/AR locale coverage | PASS — English, Urdu and Arabic Future terminology/copy coverage remains. |
| 72 | RTL/LTR behavior | PASS at repository-control level; real browser acceptance remains external. |
| 73 | Keyboard/focus/44px target | PASS at repository-control level; human assistive-technology acceptance remains external. |
| 74 | Reduced motion/forced colors | PASS — CSS fallbacks remain present. |
| 75 | 320px reflow / 400% zoom truth | PASS at repository-control level; 400% real-browser evidence remains an external acceptance gate. |
| 76 | PHP compatibility matrix | PASS condition retained — exact final head must pass PHP 7.4 and PHP 8.3. |
| 77 | Deterministic package/SHA/SBOM | PASS condition retained — exact final head must double-build deterministically and verify archive paths/CRC/SHA/file SBOM. |
| 78 | Two fresh post-code reviews | PASS condition retained — both fresh review scripts must run after the final accepted source change. |
| 79 | Three-ledger / third-regression release truth | PASS condition defined — first, second and third eighty-pass gates plus dedicated third-review regressions must all pass on exact head. |
| 80 | Live-First deployment boundary | PASS — repository success cannot establish deployed artifact, live database, migration state, staging acceptance or operational truth. |

## Defect-round index

Fresh defects were discovered in rounds **02, 03, 05, 06, 07, 08, 09, 10, 11, 12, 13, 14, 15, 16, 17, 18, 19, 20, 21, 22, 23, 24, 25, 26, 27, 28, 29, 30, 31, 32, 33, 34, 35, 36, 37, 38, 39, 40, 41, 42, 43, 44 and 45** — **43 defect-bearing rounds**. The ledger treats each listed failure class separately; rounds 43–45 are QA/release-machinery defects rather than shipped application-domain defects. Every listed defect must be corrected before the exact final head is accepted.

The remaining **37 rounds** found no additional repository defect after the preceding corrections.

## Final acceptance gate

This ledger becomes accepted evidence only when the exact final review head has no temporary corrective workflow/scripts and passes:

1. PHP 7.4 and PHP 8.3 quality;
2. policy/contract/reliability/central/Future/first-review/second-review regressions;
3. `tests/third-review-regression-tests.php`;
4. `scripts/review80.py`, `scripts/review80-second.py`, and `scripts/review80-third.py` (80/80 each);
5. both fresh post-code review rounds;
6. baseline integrity;
7. deterministic v1.4.1 double-build, archive safety/CRC, SHA-256 and file-level SBOM;
8. exact PR-head parity before merge; and
9. fresh post-merge checks on the exact resulting `main` SHA.

Staging, deployed code, live database/schema/migration state and operational behavior remain independently unverified until measured in the target environment.
