# File14 v1.4.77 — R77 V12.3 Cross-File Source Completion / Release Identity Audit

Date: 2026-11-15. Base main: `48ea2cbff43f987bf5b5c4065d0564d6ca04acc7` (v1.4.76); candidate branch: `fix/file14-v123-rtl-approved-locale-20261115`.

**Scope:** File14-only source and versioned contracts; source-review evidence **is not** staging, real device WCAG, production installed build, migrated database or live acceptance.

## Frozen source defects and repairs

| ID | V12.3 requirement | Root cause in the verified File14 source | Exact scoped repair | Direct test |
|---|---|---|---|---|
| R77-D01 | F14-FR-013, F14-NFR-010 | `GCU_Frontend::navigation_controls()` chose Arabic labels for **both** Urdu and Arabic based solely on RTL direction, ignoring existing approved locale keys. | Look up approved `page_navigation`, `back`, `home` for exactly en-US, ur-PK and ar-SA; escape output; retain File20 ownership and canonical URLs. | `tests/v123-navigation-localization.php`: three native PHP locale cases including Urdu != Arabic and accessible labels |
| R77-D02 | F14-FUT-20, F14-FR-013 | Unavailable Urdu/Arabic governed content/FAQ silently reused English records under Urdu/Arabic page lang/dir; no approval/terminology provenance for that mixed-language publication. | Fail closed on missing locale-specific approved governed blocks; show truthful localized review state, withhold untranslated FAQs; retain normal approved locale reads. | `tests/v123-locale-fail-closed.php`: English-only mock database entries must not render in Urdu; exactly two locale-scoped source reads |

No owner booking outcome, experiment placement or confirmation authority is enabled by these changes. Security gates, GPC, optional analytics consent, per-minor rules and emergency diversion remain unchanged.

## Cross-file source check scopes

The existing `scripts/review20-cross-file.py` twenty static/source-marker gates remain the reproducible source framework, **not twenty real multi-plugin E2E tests**.

1. Software version/header, stable readme, status and this version's ledger.
2. Approved F14 functional/nonfunctional/Future IDs trace.
3. File14 no-cure and zero-commission policy owner boundaries.
4. Original File00 authorization and no independent identity minting.
5. File01 canonical module manifests/roles.
6. File01 route and redirect registration.
7. File01 API/owner event contract validation.
8. File07 verified directory and owner health.
9. File08 clinic/booking health and zero-commission.
10. File09 professional verification/onboarding ownership.
11. File19 optional notification outbox.
12. File20 canonical global shell contract and slots.
13. File20 single shell / bounded navigation fallback.
14. File24 security runtime readiness.
15. File24 independent release assurance.
16. File25 visual-token and component ownership.
17. GPC/erasure, en-US/ur-PK/ar-SA, WCAG/focus/reduced-motion.
18. Installed/migration/rollback source safeguards.
19. PHP source QC, deterministic double build, twenty-source gate.
20. Explicit blocked owner issues and separate deployed/live truth.

Only the **two newly evidenced local defects above** are claimed source-corrected here. Passing the twenty existing static gates does not settle the three owner-dependent issues.

## Acceptance/status boundaries

| Item | Status |
|---|---|
| Source plugin version | Candidate 1.4.77 |
| Base schema target | 10005 (unchanged) |
| Future additive schema target | 1 (unchanged) |
| File07/08/09 producer signer registry | Issue #19 OPEN |
| File20/25 changed-placement atomic CAS restoration | Issue #22 OPEN |
| File08 separate opt-in, revocable and anonymized booking aggregate | Issue #70 OPEN |
| Deployment artifact SHA-256 parity | UNVERIFIED |
| Active WordPress DB/migration/options | UNVERIFIED |
| Hostinger-equivalent staging acceptance | NOT ESTABLISHED |
| Live re-test/operational acceptance | NOT ESTABLISHED |

## Required CI and independent closure

On the final PR **exact HEAD**, run PHP7.4/8.3 source quality; both fresh review workflows; all four native adversarial/locale tests in standard QA; the historical eighty-pass source-marker checks; production deterministic ZIP, SBOM, CRC and hashes. On merge, re-run the exact new main HEAD. No prior run green can be reused for a newer commit.

For deployment, obtain site/owner approval, freeze exact installed plugin/version/checksum, DB schema and migration log, identify live/staging/main divergence, test real Urdu/Arabic locale paths, 320px/400% keyboard/RTL, backup/rollback, and then compare deployed package to accepted source. Keep blocked owner analytics and placement experiments disabled.

**Exact deployed code ابھی unverified ہے؛ repository-based diagnosis provisional ہے۔**
