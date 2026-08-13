# File 14 — Requirements Traceability Addendum — v1.4.5

## Status and relationship to the v1.4.4 matrix

`docs/REQUIREMENTS-TRACEABILITY.md` is preserved as the historical v1.4.4 full matrix. This addendum carries the exact v1.4.5 corrective delta so historical evidence is not silently rewritten while current release truth remains complete.

Governing sources remain the consolidated central governing plan, `SSH-F14-PLAN-2026-v1.0`, and `SSH-F14-FUTURE-CTI-2026-v2.0`. Base schema remains `10005`; Future schema remains `1`.

## v1.4.5 corrective traceability

| Corrective control | Governing requirement/boundary | Implementation evidence | Automated evidence |
|---|---|---|---|
| Explicit-grant File 00 authorization | CEN-OWN-001; authorization is never inferred from capability/feature presence | `includes/class-gcu-capabilities.php` uses native capability plus `gcu_authorize` default `false` and exact `true` grant | `tests/seventh-review-regression-tests.php`; updated fresh review round 1 |
| Empty DB-lock scope rejection | F14-NFR reliability/concurrency; fail closed on invalid synchronization identity | `includes/class-gcu-hardening.php::acquire_db_lock()` rejects empty normalized scope | seventh regression + fresh review round 1 |
| Canonical owner destination URL integrity | CEN-OWN-001; Files 07/08/09 remain destination truth owners | `includes/class-gcu-contracts.php`; consumer filter may restrict readiness only, never rewrite owner-confirmed URL | seventh regression; contract/fresh review gates |
| Privacy erasure completion truth | CEN-PRIV-001; privacy lifecycle must not claim completion before durable removal | `includes/class-gcu-privacy.php::erase_data()` reads `_gcu_measurement_subject_v1` back after deletion | seventh regression + contract/fresh review gates |
| Conversion-event UUID identity binding | F14-FR-011; deduplication must not accept changed event binding | `includes/class-gcu-rest.php::event_identity_guard()` compares stage, destination, pseudonymous subject and campaign context; conflict returns HTTP 409 | seventh regression + contract/fresh review gates |
| Repository source hygiene | public-safe source/package constitution | tracked `scripts/__pycache__/*.pyc` removed; `.gitignore` excludes `__pycache__/` and `*.py[cod]` | seventh regression + fresh review round 2 |
| Current release identity and QA consistency | exact-head evidence rule / DoD-11 / DoD-13 | v1.4.5 plugin header, `GCU_VERSION`, readme stable tag, STATUS, RELEASE-EVIDENCE and QA gates aligned | contract, central, historical regression, fresh reviews, quality workflow |

## Truth-status boundary

This addendum proves only repository traceability for the v1.4.5 delta. It does not prove staging acceptance, live deployment, deployed artifact parity, live database/schema version, migration state or operational success.

**Exact deployed code is unverified; repository-based diagnosis is provisional with respect to the live site.**
