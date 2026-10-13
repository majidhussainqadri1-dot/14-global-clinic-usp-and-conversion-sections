# File14 R33 — Forty scoped plan and cross-file source review

**Review freeze:** 2026-10-13; Repository `main@646cdfae0ad0e6305d065dedfa83f6a72ac38924` (File14 candidate 1.4.35); File08 `main@9dcc8d16ea81d0bbd27184e71bc09ba04d30ba03` (1.2.16), File07 `2f4a89707724fd2b9946600afe10ddab27ec3c2d`, File09 `cfc5f781a766330314dc98c42abeca0eb7786eba`, File01 `adf6dbb9980a85f25f7cf4c2ee679b52703c2e71`, File20 `8a4dbcaf4fef8e926b9b834ecfde16c21a0f00ca`.

**Authority:** amended File14 v2.0 CTI24 plan, governing consolidated Three Central Plans, File07 v1.2 FUT24, File08 Future24, File09 Advanced Trust24 and File25 UI plan. These are **40 independent contractual inspection lenses**, NOT 40 executed browser/WordPress E2E runs. The first 16 inspect core/cross-file rules; the next 24 correspond one-to-one with `F14-FUT-01..24`. Source presence is not deployment acceptance.

## Frozen forty-lens findings (recorded before R33 corrections)

| # | Checked governing condition | Source outcome |
|---:|---|---|
| 1 | Core canonical scope and forbidden duplicate backend | SOURCE LOCATED — functional runtime unverified |
| 2 | File00 authorization assertions | SOURCE LOCATED — functional runtime unverified |
| 3 | File01 registry + exact manifest/route parity | SOURCE LOCATED — functional runtime unverified |
| 4 | File07 public doctor directory destination | SOURCE LOCATED — functional runtime unverified |
| 5 | File08 committed booking owner hook | SOURCE LOCATED — functional runtime unverified |
| 6 | File09 committed application owner hook | SOURCE LOCATED — functional runtime unverified |
| 7 | File19 notification producer ownership | SOURCE LOCATED — functional runtime unverified |
| 8 | File20 navigation and placement owner | SOURCE LOCATED — functional runtime unverified |
| 9 | File24 security/privacy assurance | SOURCE LOCATED — functional runtime unverified |
| 10 | File25 global visual components | SOURCE LOCATED — functional runtime unverified |
| 11 | Zero commission/free tier/support neutrality | SOURCE LOCATED — functional runtime unverified |
| 12 | Founder roles, workflow audit and approval | SOURCE LOCATED — functional runtime unverified |
| 13 | Consent, GPC, erasure, sensitive route (R33 defect) | R33 SOURCE FIX; sensitive path/erasable data |
| 14 | Atomic outbox/inbox/idempotency | SOURCE LOCATED — functional runtime unverified |
| 15 | Schema migration rollback/locking | SOURCE LOCATED — functional runtime unverified |
| 16 | Public accessible RTL/degraded state | SOURCE LOCATED — functional runtime unverified |
| 17 | F14-FUT-01 Explicit intent | SOURCE LOCATED — functional runtime unverified |
| 18 | F14-FUT-02 Smart handoff | SOURCE LOCATED — functional runtime unverified |
| 19 | F14-FUT-03 Trust evidence drawer | SOURCE LOCATED — functional runtime unverified |
| 20 | F14-FUT-04 Claim freshness sentinel | SOURCE LOCATED — functional runtime unverified |
| 21 | F14-FUT-05 Zero commission parity | SOURCE LOCATED — functional runtime unverified |
| 22 | F14-FUT-06 Jurisdiction truthful copy | SOURCE LOCATED — functional runtime unverified |
| 23 | F14-FUT-07 Semantic meaning drift | SOURCE LOCATED — functional runtime unverified |
| 24 | F14-FUT-08 Dark pattern linter | SOURCE LOCATED — functional runtime unverified |
| 25 | F14-FUT-09 Safe destination failover | SOURCE LOCATED — functional runtime unverified |
| 26 | F14-FUT-10 Scenario preview laboratory | SOURCE LOCATED — functional runtime unverified |
| 27 | F14-FUT-11 Quality score measured-only | SOURCE PRESENT, COMPOSITE PROVISIONAL |
| 28 | F14-FUT-12 Copy correction/report loop | SOURCE LOCATED — functional runtime unverified |
| 29 | F14-FUT-13 Consent-based friction aggregate | SOURCE LOCATED — functional runtime unverified |
| 30 | F14-FUT-14 Owner-native anomaly observations (R33 correction) | R33 SOURCE FIX; owner-stage integration pending staging |
| 31 | F14-FUT-15 Minimum cohort privacy | SOURCE LOCATED — functional runtime unverified |
| 32 | F14-FUT-16 Experiment safety preflight | SOURCE LOCATED — functional runtime unverified |
| 33 | F14-FUT-17 Owner-mutated layout restore (BLOCKED) | INCOMPLETE / containment only |
| 34 | F14-FUT-18 FAQ gap intelligence | SOURCE LOCATED — functional runtime unverified |
| 35 | F14-FUT-19 Consistency graph | SOURCE LOCATED — functional runtime unverified |
| 36 | F14-FUT-20 Translation provenance and terminology | SOURCE LOCATED — functional runtime unverified |
| 37 | F14-FUT-21 AI ethical copy assistant | SOURCE LOCATED — functional runtime unverified |
| 38 | F14-FUT-22 Public policy change log | SOURCE LOCATED — functional runtime unverified |
| 39 | F14-FUT-23 Patient safe-choice guide | SOURCE LOCATED — functional runtime unverified |
| 40 | F14-FUT-24 Doctor nonbinding readiness | SOURCE LOCATED — functional runtime unverified |

**Static check summary:** 38/40 source entrypoint markers found; #30 had a hardcoded no-owner-confirmation branch preventing any native volume anomaly; #33 only certified the baseline was unchanged, with **no provenance-linked reversal of an experiment-owned placement mutation**. Additional adversarial review of #13 found that a separate native hook bypassed the sensitive-route measurement guard, and its NULL subject hash was not eligible for the existing subject erasure/export lifecycle. These findings make four distinct R33 work items, of which one is external design/staging-gated.

## R33 verified-source corrective scope

1. Owner events now call `GCU_Privacy::measurement_allowed()` as the canonical consent/GPC/sensitive route gate. Ineligible File08/File09 protected request paths will **not** emit analytics; a future approved owner consent receipt/async contract is necessary for broader booking attribution.
2. Pseudonymous subject hash uses the existing File14 privacy function so owner data participates in the native exporter/eraser. Rate limiting added; old unerasable rows explicitly excluded from reports. No raw clinical, appointment, applicant or doctor identifiers are stored.
3. Cohort-protected independent native event **volume** anomaly analysis, with at least 10 current and 70 historical events per supported owner/stage and no fabricated cross-stage success rate, registered as observation not population demand.
4. The guarded experiment system still captures approved baseline and stops/audits; drift is quarantined and never overwritten. The actual self-owned variant mutation and atomic reverse replay are still unimplemented; F14-FUT-17 production acceptance remains BLOCKED. No false “restored” claim.

## Defect accounting

- **R33-D01:** Owner conversion hooks bypass canonical sensitive-route measurement boundary — SOURCE FIXED, staging pending.
- **R33-D02:** NULL owner subject hash makes privacy subject erasure impossible — SOURCE FIXED for new events; historical rows require an explicitly approved purge policy, not automatic reassignment.
- **R33-D03:** Owner anomaly gate permanently masks independently observed native counts — SOURCE FIXED as optional-consent native **volume** indicator, no fabricated funnel success rate.
- **R33-D04:** No provenance-CAS replay engine for experiment-owned changed placements — OPEN; safe baseline no-op and drift containment must not be called automatic restoration.
- **R33-D05:** Owner consent provenance/registry and real browser/WordPress validation remain UNVERIFIED, in particular File01 owner-event registry and relevant File07/08/09 deployed versions — EXTERNAL ACCEPTANCE.

## Required acceptance and absolute release truth

- Execute fresh exact PR and **merged-main** PHP7.4/8.3 tests, R33 behavioral regressions, 40-lens source policy script, reproducible ZIP/SBOM and the existing extensive review scripts.
- Verify actual installed plugin files/checksum, DB schema, `wp_options`, migration and error logs BEFORE alleging a Live fix. Stage with backup/recovery exercises; confirm File07/08/09 owner hooks and consent/privacy erasure under real WP, plus File20/25 layout and RTL/low-data.
- **Repository HEAD:** frozen prior `646cdfae0ad0e6305d065dedfa83f6a72ac38924`; new exact merged HEAD must be recorded after merge.
- **Deployed Version:** UNVERIFIED.
- **DB Version:** UNVERIFIED.
- **Migration State:** UNVERIFIED.
- **Live Verification Status:** NOT RUN.

Exact deployed code ابھی unverified ہے؛ repository-based diagnosis provisional ہے۔
