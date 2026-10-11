# File14 R10 — Twenty Independent Source Reviews (v1.4.14)

Date: 2026-10-11 PKT. Frozen baseline File14 main SHA: ba6e162c8a23b890b1aef86844dc314074162b39 (v1.4.13).

Governing documents: uploaded File14 base and Future CTI24 amended plan; consolidated central v3.0 master; File07, File09 and File25 plans; actual current File14 source and declared File01/07/08/09/20/25 owner boundaries. This is a 20-lens STATIC/SOURCE audit, not 20 WordPress live installations or functional acceptance exercises.

## All twenty source rounds (reviewed fully before beginning R10 corrections)

| Pass | Audit lens | Finding | Scope |
|---:|---|---|---|
| 1 | File14 16 functional requirements | source-trace clean | Requirements IDs mapped, not live implementation proof |
| 2 | 24 Future CTI additions | source-trace clean | Approved catalogue represented |
| 3 | Zero commission/free core/optional support | source guard clean | Central business parity checks exist |
| 4 | File00 identity and roles | source guard clean | Delegated capability checks exist |
| 5 | File01 route/manifest registry | source guard clean | Versioned integration exists |
| 6 | File07 doctor directory | source guard clean | Owner-native destination checks exist |
| 7 | File08 clinic/appointments | source guard clean | No File14-owned booking truth |
| 8 | File09 onboarding | source guard clean | Owner onboarding destination |
| 9 | File20/25 shell and visual contracts | source guard clean | No duplicate global shell |
| 10 | Consent and GPC | source guard clean | Opt-in/GPC enforcement exists |
| 11 | Owner-native outcome events | OPEN G01 | Issue #19: File07/08/09 acknowledgements still absent |
| 12 | Trust claim freshness | source guard clean | Sentinel and public eligibility |
| 13 | Approved-copy translation isolation | DEFECT D01 | Whole-page strtr could rewrite canonical copy |
| 14 | Low-bandwidth/no-JS readiness | DEFECT D02 | Visible readiness button had no working JS in low-data mode |
| 15 | Experiment safe baseline restoration | OPEN G02 | Issue #22: stop/audit without approved snapshot/placement restore |
| 16 | Minimum cohorts in analytics | source guard clean | Privacy suppression exists |
| 17 | AI-copy rate limit | source guard clean | R9 provider guard exists |
| 18 | Future schema/runtime readiness | source guard clean | R9 DB read guard exists |
| 19 | AI factual source/provenance | source guard clean | Source-bound draft validation exists |
| 20 | Idempotency/migration/package | source guard clean | Static controls; staging remains unverified |

Pre-correction classification: **16 narrow static source checks clean; 2 correctable File14 defects (passes 13 and 14); 2 outstanding specifications/integrations (passes 11 and 15).** A narrow source-clean probe does not establish complete implementation of its broad requirement; all acceptance gates remain independent.

## Consolidated corrections made after twenty rounds

D01 (F14-FUT-20, central owner-approved copy policy): Previously GCU_Future_I18n::localize_html() indiscriminately replaced dictionary substrings in the ENTIRE canonical page HTML, potentially altering already reviewed content, attribute values or text from other owners. File14 now wraps only its generated Future UI fragment in explicit markers. Translation acts only inside this fragment and matches entire visible text nodes against approved en-US/ur-PK/ar-SA dictionary entries. No arbitrary substring or HTML attribute translations; readiness data attributes are scoped. Pure-policy regressions prove Urdu/Arabic label rendering and preservation of unmatched source text and attributes.

D02 (F14-FUT-24, nonbinding choice, low-data/accessibility requirements): Doctor readiness button relied on JS, which is intentionally not enqueued under reduced-data mode and may be absent otherwise. Button and result now start hidden. A working JS listener reveals controls and suppresses a truthful static fallback; without JS only the self-preparation checklist and non-verification explanation remain visible. Author CSS protects the hidden state. Urdu/Arabic translations for the explanatory fallback and regression cases added.

Release candidate: plugin 1.4.14, unchanged code schema targets base 10005 and Future CTI 1.

## Still unresolved: no speculative patch

G01, GitHub Issue #19: approved, authenticated File07/08/09 native outcome event contract with File01 registration; don't infer booking/application from an outbound CTA.

G02, GitHub Issue #22: F14-FUT-17 experiment auto-stop is currently transactional and auditable, but does not prove **safe approved baseline/placement restoration**. Experiment/placement schemas lack approved baseline linkage. Before coding restoration: Founder-approved baseline snapshots/versioned object ownership; idempotent and concurrency-safe restore; migration, rollback, failure recovery, accessibility and staging tests. Existing protective experiment stop remains unchanged pending that contract. No fabricated completion claim.

## Status/evidence

The source changes require exact candidate SHA PHP7.4/PHP8.3 quality, two fresh reviews, deterministic package, PR/merge checks and independent POST-MERGE exact-main verification.

Repository HEAD: frozen baseline ba6e162c8a23b890b1aef86844dc314074162b39. Deployed Version: unverified. DB Version: unverified. Migration State: unverified. Live Verification Status: not performed.

Exact deployed code ابھی unverified ہے؛ repository-based diagnosis provisional ہے۔
