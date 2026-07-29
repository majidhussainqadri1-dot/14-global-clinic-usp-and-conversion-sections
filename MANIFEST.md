# Source Manifest — File 14 Corrective Candidate v0.1.1

## Plugin source

| Path | Purpose |
|---|---|
| `global-clinic-usp/global-clinic-usp.php` | Plugin bootstrap, approved metadata, version and schema constants |
| `global-clinic-usp/includes/class-sgc-activator.php` | Safe activation, upgrade, repair, snapshots, rollback and health reporting |
| `global-clinic-usp/includes/class-sgc-admin.php` | Settings, diagnostics, safe repair and rollback controls |
| `global-clinic-usp/includes/class-sgc-frontend.php` | Shortcodes, guarded placements, File 20 integration and scoped assets |
| `global-clinic-usp/includes/class-sgc-helpers.php` | Strict destination validation, instance IDs and shared helpers |
| `global-clinic-usp/includes/class-sgc-plugin.php` | Runtime initialization and translation loading |
| `global-clinic-usp/templates/home-hero.php` | Policy-safe two-audience home conversion section |
| `global-clinic-usp/templates/doctor-portal.php` | Doctor Portal conversion page |
| `global-clinic-usp/templates/patient-banner.php` | Verified-doctor and clinic discovery banner |
| `global-clinic-usp/templates/mission.php` | Mission page without nested main landmarks |
| `global-clinic-usp/templates/footer-mission.php` | Route-aware footer mission output |
| `global-clinic-usp/assets/css/global-clinic-usp.css` | Main responsive and accessible component styling |
| `global-clinic-usp/assets/css/footer-mission.css` | Small independently scoped footer styling |
| `global-clinic-usp/readme.txt` | Installation, scope, safety and changelog |
| `global-clinic-usp/uninstall.php` | Plugin-owned option cleanup while preserving pages/content |

## Governance and validation

| Path | Purpose |
|---|---|
| `SOURCE-PROVENANCE.md` | Original v0.1.0 archive provenance |
| `BASELINE-CHECKSUMS.sha256` | Original v0.1.0 extracted source hashes |
| `CHECKSUMS.sha256` | Corrective v0.1.1 source hashes |
| `CORRECTIVE-REVIEW.md` | Defect-to-correction traceability and remaining gates |
| `STATUS.md` | Current evidence-based repository state |
| `tests/contract-tests.php` | Static corrective regression contracts |
| `.github/workflows/corrective-quality.yml` | PHP 7.4/8.3 lint, contracts and provenance checks |
| `.github/workflows/baseline-import-and-integrity.yml` | Immutable baseline import/provenance workflow |

## Public shortcodes

- `[sgc_home_hero]`
- `[sgc_doctor_portal]`
- `[sgc_patient_banner]`
- `[sgc_our_mission]`
