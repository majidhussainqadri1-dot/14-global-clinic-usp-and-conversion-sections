# R66 — Forty focused File14 / three-central-plan / owner-contract source checks
Date: 2026-10-19. Source frozen at `main@879bafa6523f99be2289c17988d72aae516da638`, software v1.4.68, BEFORE changes.

## Scope / meaning
These are **40 separate requirement-focused source review checks**, one for each 16 File14 FR and 24 approved Future CTI requirements, **not forty full WordPress live/staging regression cycles**. Reviewed 19 source PHP classes (about 458 KB) against the attached File14 amended Word plan, Consolidated Three-Central-Plans master, and owner architecture. Keyword/contract traces alone do NOT prove behavioral completion. Each unmet feature needs native behavior and staged acceptance. Existing R65 source/test records are historical evidence only.

## Forty focused checks
| Check | Approved requirement | Focus | Result | Specific interpretation |
|---:|---|---|---|---|
| 1 | `F14-FR-001` | Patient claim/emergency | **Source implementation marker located; not staging-accepted** | Code keyword/contract inspection only |
| 2 | `F14-FR-002` | Doctor zero-fee/verification | **Source implementation marker located; not staging-accepted** | Code keyword/contract inspection only |
| 3 | `F14-FR-003` | Primary CTAs File07/09 | **Source implementation marker located; not staging-accepted** | Code keyword/contract inspection only |
| 4 | `F14-FR-004` | How-it-works owner journeys | **Source implementation marker located; not staging-accepted** | Code keyword/contract inspection only |
| 5 | `F14-FR-005` | Verification/privacy/no cure guarantee | **Source implementation marker located; not staging-accepted** | Code keyword/contract inspection only |
| 6 | `F14-FR-006` | 0% commission/optional support | **Source implementation marker located; not staging-accepted** | Code keyword/contract inspection only |
| 7 | `F14-FR-007` | File20 placement slots | **Source implementation marker located; not staging-accepted** | Code keyword/contract inspection only |
| 8 | `F14-FR-008` | File25 semantic visual components | **Source implementation marker located; not staging-accepted** | Code keyword/contract inspection only |
| 9 | `F14-FR-009` | Fresh destination readiness | **R66: local source patch + test; CI pending** | Stale event overrides negative native health |
| 10 | `F14-FR-010` | Attribution consent and expiry | **Source implementation marker located; not staging-accepted** | Code keyword/contract inspection only |
| 11 | `F14-FR-011` | Owner-native conversion event evidence | **OPEN: owner/runtime dependency** | Issues #19/#70; owner-signature, privacy-safe booking |
| 12 | `F14-FR-012` | Experiment founder approval | **Source implementation marker located; not staging-accepted** | Code keyword/contract inspection only |
| 13 | `F14-FR-013` | en-US/ur-PK/ar-SA copy | **Source implementation marker located; not staging-accepted** | Code keyword/contract inspection only |
| 14 | `F14-FR-014` | Versioned FAQ review | **Source implementation marker located; not staging-accepted** | Code keyword/contract inspection only |
| 15 | `F14-FR-015` | Keyboard/responsive CTAs | **Source implementation marker located; not staging-accepted** | Code keyword/contract inspection only |
| 16 | `F14-FR-016` | Clinical/financial claim audit | **Source implementation marker located; not staging-accepted** | Code keyword/contract inspection only |
| 17 | `F14-FUT-01` | Ethical intent router | **Source implementation marker located; not staging-accepted** | Code keyword/contract inspection only |
| 18 | `F14-FUT-02` | Safe owner-specific destination handoff | **R66: local source patch + test; CI pending** | Filter could replace canonical owner URL |
| 19 | `F14-FUT-03` | Claim evidence drawer | **Source implementation marker located; not staging-accepted** | Code keyword/contract inspection only |
| 20 | `F14-FUT-04` | Claim freshness sentinel | **Source implementation marker located; not staging-accepted** | Code keyword/contract inspection only |
| 21 | `F14-FUT-05` | Zero-fee/free/donation parity | **Source implementation marker located; not staging-accepted** | Code keyword/contract inspection only |
| 22 | `F14-FUT-06` | Jurisdiction-approved public copy | **Source implementation marker located; not staging-accepted** | Code keyword/contract inspection only |
| 23 | `F14-FUT-07` | Semantic claim difference | **Source implementation marker located; not staging-accepted** | Code keyword/contract inspection only |
| 24 | `F14-FUT-08` | Anti-dark-pattern linter | **Source implementation marker located; not staging-accepted** | Code keyword/contract inspection only |
| 25 | `F14-FUT-09` | Safe unavailable destination state | **R66: local source patch + test; CI pending** | Owner-unavailable could display stale CTA |
| 26 | `F14-FUT-10` | Copy scenario laboratory | **Source implementation marker located; not staging-accepted** | Code keyword/contract inspection only |
| 27 | `F14-FUT-11` | Owner confirmed quality score | **OPEN: owner/runtime dependency** | Issue #19; no inferred owner conversions |
| 28 | `F14-FUT-12` | Misleading-copy reports | **Source implementation marker located; not staging-accepted** | Code keyword/contract inspection only |
| 29 | `F14-FUT-13` | Consented privacy-safe friction | **Source implementation marker located; not staging-accepted** | Code keyword/contract inspection only |
| 30 | `F14-FUT-14` | Owner volume anomaly proof | **OPEN: owner/runtime dependency** | Issue #19; owner counts unverified |
| 31 | `F14-FUT-15` | Minimum ten-person cohort | **Source implementation marker located; not staging-accepted** | Code keyword/contract inspection only |
| 32 | `F14-FUT-16` | Experiment safety preflight | **Source implementation marker located; not staging-accepted** | Code keyword/contract inspection only |
| 33 | `F14-FUT-17` | Experiment changed-placement rollback | **OPEN: owner/runtime dependency** | Issue #22; real File20/25 CAS restoration |
| 34 | `F14-FUT-18` | FAQ-gap evidence | **Source implementation marker located; not staging-accepted** | Code keyword/contract inspection only |
| 35 | `F14-FUT-19` | Message consistency graph | **Source implementation marker located; not staging-accepted** | Code keyword/contract inspection only |
| 36 | `F14-FUT-20` | Translation provenance and terminology | **Source implementation marker located; not staging-accepted** | Code keyword/contract inspection only |
| 37 | `F14-FUT-21` | AI draft/manual publication gate | **Source implementation marker located; not staging-accepted** | Code keyword/contract inspection only |
| 38 | `F14-FUT-22` | Transparent public change log | **Source implementation marker located; not staging-accepted** | Verified public_change_log()/rest_change_log() in File14 source |
| 39 | `F14-FUT-23` | Educational patient choice guide | **Source implementation marker located; not staging-accepted** | Code keyword/contract inspection only |
| 40 | `F14-FUT-24` | Nonbinding doctor readiness self-check | **Source implementation marker located; not staging-accepted** | Code keyword/contract inspection only |

## Two demonstrable defects in exact v1.4.68 source — found before edits
**R66-D01 — stale owner-state resurrection**: `GCU_Contracts::destination()` used `if(!is_array($base)||empty($base['available']))` then a day-old `gcu_destination_state_*` availability Event could replace an explicit File07/08/09 `destination_probe()` returned `available=false`. That can falsely re-enable broken/disabled owner CTAs. Native probe takes precedence over historical Events. Fix: never grant owner destination availability from the historical event cache. Unknown or negative owner proof is fail-closed.

**R66-D02 — owner URL substitution by consumer filter**: The same method called `gcu_destination_contract_v1` and accepted any same-origin replacement URL from an untrusted consumer filter while still reporting the original owner as verified. This permits an altered route unrelated to the owner. Fix: filtering may veto but any missing, invalid, or different URL fails closed; only exact native-approved URL remains.

**Direct regression coverage**: `tests/r66-owner-health-destination-tests.php` invokes the actual `GCU_Contracts::destination()` with isolated WordPress+owner-contract mocks. It checks unavailable, incompatible, missing owner probes despite misleading cached Events, healthy native routes, filter veto, same-origin/off-origin/empty/non-string filter replacements, malformed owner URL, and that no legacy cached event is consulted. Integrated with `scripts/quality.sh`, subject to remote CI and independent reviews.

## Known cross-owner blockers (not falsely resolved)
- **Issue #19**: File01 registered, approved owner-signed File07/08/09 post-commit producer contracts; consent/GPC, delivery, replay, real staging.
- **Issue #22**: File20/25 authorized *actually changed* placement/variant CAS restoration; existing no-drift equality is NOT a restore. Requires approved baseline/mapping, schema/parity, staging/race/rollback.
- **Issue #70**: File08 sensitive booking REST event must not be silently tracked. Approval-dependent privacy-safe aggregate contract; otherwise reports must explicitly say unavailable.
No File07/08/09/20/25 owner writes were made. No production deploy attempted.

## Truth matrix
| Reality | Status |
|---|---|
| Repository HEAD at review start | `879bafa6523f99be2289c17988d72aae516da638` |
| Candidate source version after correction | `1.4.69` (branch only pending CI/merge) |
| Deployed Version | UNVERIFIED; WPVibe had no connected sites on 2026-10-19 |
| DB Version | UNVERIFIED |
| Migration State | UNVERIFIED |
| Live Verification Status | NOT RUN |
| Repository CI / independent reviews | MUST BE CHECKED against exact candidate/merge SHA |
| Production completion | NOT CLAIMED |

Exact deployed code ابھی unverified ہے؛ repository-based diagnosis provisional ہے۔

## Next acceptance gates
1. Run PHP7.4/8.3 full CI, direct native R66 tests, two independent post-code reviews, deterministic package hash and manifest.
2. Founder approve File01/07/08/09 signed analytics owner contract and File20/25 placement CAS protocol; reconcile privacy Issue #70.
3. Connect WordPress staging site read-only; freeze exact deployed plugin files/version/hash, WordPress options, schemas, migration logs, owner versions, required tables/columns, operational logs.
4. Only after parity verified: staging negative/positive/integration/race/rollback tests, signed acceptance, package→deploy→live smoke/retest→parity confirmation.

Plan-complete vs source-coded vs packaged vs staging-accepted vs deployed vs operational are distinct; do not conflate.
