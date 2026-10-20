# R74 — File14 Forty Review Iterations and Cross-Owner Governance, 2026-10-21

**Source of truth:** File14 prechange repository main `b9cb8f0f2c3f21f10633b080449028b03740d5f0`, plugin `1.4.74`; File14 amended governing plan F14-FR-001..016, F14-NFR-001..010, F14-FUT-01..24; consolidated three-plan governing document; File00/01/07/08/09/20/25 owner HEADs. These are repository/source truths and not installed/operational evidence.

## The forty distinct plan lenses checked

| Pass | Test/review lens | Evidence and strict acceptance |
|---:|---|---|
| 01 | FR001 clinic USP scope | Only File14 content, no clinic backend ownership |
| 02 | FR002 medically accurate founder-approved copy | No guaranteed cure/unsupported professional rank |
| 03 | FR003 doctor/clinic/onboarding CTA | Native owner health + same-origin canonical URL |
| 04 | FR004 how-it-works and limitations | Local page and accurate no-auto-booking statements |
| 05 | FR005 trust evidence | Current claims, source freshness and audit |
| 06 | FR006 commission and donation parity | 0% platform commission, donation non-influence |
| 07 | FR007 reusable section placement | File20 slot registry gate |
| 08 | FR008 semantic block ownership | File25 rendering/component authority preserved |
| 09 | FR009 destination availability | Exact typed, non-elevatable native owner health |
| 10 | FR010 attribution | Opt-in only, no sensitive-domain cross tracking |
| 11 | FR011 owner funnel | Click != arrival, requested != booked, submitted != started |
| 12 | FR012 experiments | Founder approval, safe stop, rollback truth |
| 13 | FR013 localization | English/Urdu/Arabic, semantic RTL |
| 14 | FR014 FAQ claim/version | Review owner and freshness |
| 15 | FR015 accessibility | Keyboard, mobile, focus, reduced motion, contrast |
| 16 | FR016 claim governance | Freshness, immutable audit and no false-green claims |
| 17 | NFR001 File00 authorization | Native owner truth cannot be elevated by filter |
| 18 | NFR002 minimization | GPC/consent/erasure/export fail-closed |
| 19 | NFR003 data integrity | InnoDB, transaction, locks and idempotency |
| 20 | NFR004 API safety | Schema, safe errors, no sensitive source |
| 21 | NFR005 performance | Budgets and real staging testing separately |
| 22 | NFR006 accessibility | Semantic mobile, RTL, WCAG-oriented |
| 23 | NFR007 auditing | No exposed patient/provider raw data |
| 24 | NFR008 migration | Source schema != installed DB schema |
| 25 | NFR009 cross-file contract versions | Owner release and registry actual status separate |
| 26 | NFR010 operations/locales | Honest degraded health; alerts without raw PII |
| 27 | FUT01 future intelligence | Disabled unless approved |
| 28 | FUT02 policy/claims | Human approval and freshness |
| 29 | FUT03 translation and copy | Human reviewed; never automatic medical assertion |
| 30 | FUT04 claim sentinel | Bounded stale/read/failure proof |
| 31 | FUT05 comparison | Nondeceptive copy and limited evidence |
| 32 | FUT06 FAQs | Suppressed rare personal data and owner terms |
| 33 | FUT07 dynamic placements | Owner baseline and rollback gate |
| 34 | FUT08 consented analysis | Owner event is not CTR inference |
| 35 | FUT09..10 funnel and consent | No unaccepted owner conversion success |
| 36 | FUT11..12 owner stages | Signed owner evidence is pending |
| 37 | FUT13..14 anomalies and booking | No inferred success, no unsupported zero |
| 38 | FUT15..16 experiments and quality | Small cohort and provenance |
| 39 | FUT17 early stop and restoration | Actual mutation CAS BLOCKED (#22), no-op is not restore |
| 40 | FUT18..24 later features and safe gates | Governed educational/localization, no auto publish |

The executable `scripts/review40-iterative.sh` repeats **40** plan source checks, **40** native owner PHP mocks, **40** privacy PHP mocks, **40** destination-filter PHP mocks, **40** typed File00 authorization cases and **40** R74 native-owner monotonicity cases in **each** of 40 complete iterations (**9,600** source/mock checks in total, when CI actually completes). Repetition is not a substitute for 40 independent WP environments, domain simulations or live acceptance.

## New root cause (R74)

File00 `SMC_Contracts::assertions()` emits `user_id` integer, `contract_version` string, `status`, strict boolean `eligible` and `suspended`. File14 consumer invoked the compatibility `smc_assertions_v1` filter, discarded the original authoritative claim and accepted the filtered result. Despite R73 strict booleans, an arbitrary filter could change the genuine native denial (`eligible=false`) to `eligible=true`, remove suspension, rewrite native status, claimant or contract, then pass File14's post-filter authorization check. That is an exact-source File14 authorization boundary defect; it is NOT evidence of live exploitation.

**Scoped repair:** check original File00 subject, contract version, status and boolean types first; accept a filtered assertion only if its identity/version/status are unchanged and its boolean effects are equal or more restrictive. Deny otherwise. No File00/backend modifications. `tests/r74-forty-native-file00-claim-monotonic-tests.php` runs the actual `GCU_Companion_Adapters::authorize()` against forty adversarial direct PHP mock cases, without WordPress installed.

## Remaining owner issues — not resolved by R74

- [#19](https://github.com/majidhussainqadri1-dot/14-global-clinic-usp-and-conversion-sections/issues/19): native signed/deduplicated/revocable owner events, verified actor/consent, production and staging parity with File01,07,08,09. Proposed File01 draft PR #7 cannot be treated as a signed/live event producer.
- [#22](https://github.com/majidhussainqadri1-dot/14-global-clinic-usp-and-conversion-sections/issues/22): owner-approved File20/25 actual changed-placement transactional compare-and-swap restore, concurrency and rollback rehearsal. Proposed File20 draft PR #42 is schema/preflight, not executable restoration.
- [#70](https://github.com/majidhussainqadri1-dot/14-global-clinic-usp-and-conversion-sections/issues/70): File08 revocable, privacy-safe native booking aggregate with opt-in consent and independent subjects. Proposed File08 draft PR #42 is not a live producer. File08 sensitive REST must remain untracked and booking count/success null.

## Exact state and release gates

| Requirement | Current evidence |
|---|---|
| Repository source | File14 prechange main `b9cb8f0f2c3f21f10633b080449028b03740d5f0`, proposed `1.4.75`; final SHA determined on exact CI/merge |
| Source base schema constant | `GCU_SCHEMA_VERSION=10005` (not verified installed DB) |
| Future schema constant | `GCU_FUTURE_SCHEMA_VERSION=1` (not verified installed DB) |
| Deployed Version | UNVERIFIED |
| DB Version | UNVERIFIED |
| Migration State | UNVERIFIED |
| Live Verification Status | NOT PERFORMED |
| Owner issues | #19 #22 #70 OPEN |

Before declaring release: quality PHP7.4/PHP8.3, full 40-round evidence, two fresh reviews, reproducible signed/checksummed package/SBOM, merge and **fresh main-head** CI. Separately: actual installed plugin version/files and checksum, database tables/schema/options/migration/logs, owner-stage acceptance, backup/restore, staging, Founder approval, deploy, live retest and parity confirmation. **Repository-based findings remain provisional for production.**
