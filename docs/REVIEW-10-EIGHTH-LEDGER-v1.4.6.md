# File 14 — Eighth Ten-Round Corrective Review — v1.4.6

Governing plans: consolidated central plan + `SSH-F14-PLAN-2026-v1.0` + `SSH-F14-FUTURE-CTI-2026-v2.0`.

Starting exact repository candidate: `0098c63d5f695d683c7057471d9c3683ec805522` on PR #10. This ledger is repository evidence only and is never staging/live evidence.

| Round | Finding | Correction before next round |
|---|---|---|
| 01 | Concurrent conflicting reuse of one conversion-event UUID could pass the pre-read race and be returned as a successful deduplication. | Re-check authoritative stored identity after a deduplicated insert result; conflict remains HTTP 409. |
| 02 | Idempotency fingerprinting reused the structured sanitizer, truncating strings at 500 characters and allowing distinct legitimate payloads to share a fingerprint. | Canonicalize the complete request recursively with depth/node/size abuse bounds; hash the complete JSON representation. |
| 03 | First logged-in measurement-subject initialization was not serialized or read-back verified; concurrent requests/write failure could orphan export/erase linkage. | Named per-user DB lock, second read, write, exact read-back verification, guaranteed lock release. |
| 04 | Destination-owner readiness used receipt time and had no monotonic owner-event ordering; delayed older events could overwrite newer truth and look fresh. | Require valid `occurred_at`, reject invalid/future-skewed timestamps, store owner occurrence separately, ignore older/equal owner state. |
| 05 | Legacy `gcu_file20_slot_ready_v1` could authorize placement readiness despite File 20 being the sole canonical shell/slot owner. | Removed alternate authority; only `sabri_shell_slot_ready_v1` may grant readiness. |
| 06 | A stored `available=true` state with a missing/unsafe owner URL could become available by substituting a File 14 fallback URL. | Owner readiness now requires a safe owner-confirmed URL; fallback is degraded navigation only and cannot manufacture availability. |
| 07 | Funnel analytics DB read failure could be surfaced as empty/suppressed metrics. | REST analytics path clears/checks DB error state and fails closed with safe HTTP 503. |
| 08 | Destination-bound funnel stages could be accepted without a destination identifier. | CTA selected, destination loaded, application started and booking started now require a canonical destination. |
| 09 | The substantive corrections made v1.4.5 release/test/documentation identity stale and lacked dedicated eighth-cycle regression integration. | Raised software to v1.4.6 with no schema change; aligned readmes/status/release/traceability/tests/fresh reviews and added eighth regression gate. |
| 10 | Exact-head closure exposed stale retained QA/evidence contracts tied to older version/prose identities, plus missing exact historical wording for current-main/live-state gates and an explicit File 25/American-English traceability regression. | Made second/third/seventh retained regressions release-cycle forward-compatible without weakening invariants; restored exact-current-main, fresh-post-merge and live-state evidence wording; restored explicit File 25 presentation boundary and American English canonical File 14 chrome traceability; reran exact-head PHP 7.4/8.3 quality, historical six 80-pass gates, fresh reviews, baseline integrity and deterministic package/SBOM to green before closure. |

## Final round classification

Defect-bearing rounds in this eighth ten-round cycle: **01, 02, 03, 04, 05, 06, 07, 08, 09, 10**.

## Truth boundary

Base schema remains `10005`; Future schema remains `1`. No staging, deployed version, live DB, migration, Founder acceptance or live verification claim is created by this ledger.

**Exact deployed code is unverified; repository-based diagnosis is provisional with respect to the live site.**
