#!/usr/bin/env python3
"""Twenty-pass File 14 cross-file coding-completeness review.

Repository/source evidence only. This script does not claim staging, deployment,
database-migration, live-site, accessibility-device or operational acceptance.
"""
from pathlib import Path
import sys

ROOT = Path(__file__).resolve().parents[1]
P = ROOT / "14-global-clinic-usp-integration"

def read(rel):
    return (ROOT / rel).read_text(encoding="utf-8")

main = read("14-global-clinic-usp-integration/global-clinic-usp-integration.php")
policy = read("14-global-clinic-usp-integration/includes/class-gcu-policy.php")
caps = read("14-global-clinic-usp-integration/includes/class-gcu-capabilities.php")
contracts = read("14-global-clinic-usp-integration/includes/class-gcu-contracts.php")
companion = read("14-global-clinic-usp-integration/includes/class-gcu-companion-adapters.php")
repo = read("14-global-clinic-usp-integration/includes/class-gcu-repository.php")
privacy = read("14-global-clinic-usp-integration/includes/class-gcu-privacy.php")
front = read("14-global-clinic-usp-integration/includes/class-gcu-frontend.php")
obs = read("14-global-clinic-usp-integration/includes/class-gcu-observability.php")
install = read("14-global-clinic-usp-integration/includes/class-gcu-install.php")
rest = read("14-global-clinic-usp-integration/includes/class-gcu-rest.php")
i18n = read("14-global-clinic-usp-integration/includes/class-gcu-i18n.php")
css = read("14-global-clinic-usp-integration/assets/css/global-clinic-usp-integration.css")
quality = read("scripts/quality.sh")
status = read("STATUS.md")
trace = read("docs/REQUIREMENTS-TRACEABILITY.md")
build = read("scripts/build.py")

checks = []
def add(name, ok):
    checks.append((name, bool(ok)))

add("01 exact v1.4.7 repository identity and R3 evidence",
    "Version: 1.4.7" in main
    and "GCU_VERSION', '1.4.7" in main
    and "14-global-clinic-usp-and-conversion-sections" in main
    and "REVIEW-20-CROSS-FILE-COMPLETION-v1.4.7-R3.md" in status)

add("02 File14 governing FR/NFR/Future trace remains complete",
    all(x in trace for x in ("F14-FR-001","F14-FR-016","F14-NFR-001","F14-NFR-010","F14-FUT-01","F14-FUT-24")))

add("03 central canonical-owner boundaries remain explicit",
    "File 14" in policy and "no_cure_guarantee" in policy and "'platform_commission_percent' => 0" in policy)

add("04 File00 action-time authorization is version-gated and exception-isolated",
    all(x in companion for x in ("FILE00_MIN_VERSION","FILE00_MIN_CONTRACT","SMC_VERSION","SMC_CONTRACT_VERSION","SMC_Contracts","smc_assertions_v1","owner_call","authorization_assertions"))
    and "GCU_Companion_Adapters::authorize" in caps)

add("05 File01 registry is version-gated, canonical and exception-isolated",
    all(x in companion for x in ("FILE01_MIN_VERSION","FILE01_MIN_CONTRACT","SPF_VERSION","SPF_CONTRACT_VERSION","SPF_Registry","file01_route_registry_state","sync_file01_registry","register_manifest","map_route","list_contracts","register_contract","owner_call")))

add("06 File07 current 1.2.1 contract and owner system health gate are required",
    all(x in companion for x in ("FILE07_MIN_VERSION = '1.2.1'","FILE07_MIN_CONTRACT = '1.2.1'","DDD_VERSION","DDD_CONTRACT_VERSION","DDD_Contracts::dependency_health","DDD_Observability","system_check","owner_runtime_degraded_readable"))
    and "home_url( '/doctors/' )" in companion)

add("07 File08 clinic integration requires owner health plus API/route/business parity",
    all(x in companion for x in ("FILE08_MIN_VERSION","FILE08_MIN_API","WCA_VERSION","WCA_Contracts::contract_manifest","WCA_Observability","runtime_health","'/appointments'","commission_percent","donation_visibility_link")))

add("08 File09 onboarding remains versioned/read-only and exception-isolated",
    all(x in companion for x in ("FILE09_MIN_VERSION","FILE09_MIN_CONTRACT","GDO_VERSION","gdo_file14_onboarding_destination","'file09'","'file14'","writes_data","automatic_enrollment","automatic_verification","onboarding_destination","owner_call")))

add("09 File19 notification transport is version-gated, addressed and exception-isolated",
    all(x in companion for x in ("FILE19_MIN_VERSION","SUN_VERSION","sun_register_notification_producer","sun_ingest_domain_event","gcu_operational_notification_recipients","register_producer","ingest_domain_event","owner_call")))

add("10 File20 remains the sole shell owner and minimum-version gated",
    all(x in companion for x in ("FILE20_MIN_VERSION","SABRI_SHELL_VERSION","sabri_shell_route_result_allowed","sabri_shell_system_check_sections","sabri_shell_context_navigation_fallback_url"))
    and "sabri_shell_back_home_controls" not in front)

add("11 File24 assurance stays version-gated without fabricated acceptance",
    all(x in companion for x in ("FILE24_MIN_VERSION","SPCRC_VERSION","spcrc/module_manifests","'canonical_data_owner'","'release_gate'"))
    and "'posture'                => 'unassessed'" in companion
    and "'last_security_test'     => ''" in companion)

add("12 File25 public visual contracts fail safely on owner exceptions",
    all(x in companion for x in ("FILE25_MIN_VERSION","FILE25_MIN_CONTRACT","FILE25_MIN_COMPONENT_CONTRACT","sabri_visual_experience_contract","sabri_visual_experience_render_state","visual_system_owner","global_shell_owner","visual_contract","render_state","owner_call"))
    and "Sabri\\PublicExperience\\Components::render_state" not in companion
    and "sabri-ui-card" in front
    and "sabri-ui-button" in front)

add("13 companion exception boundary logs bounded non-PII context and fails closed",
    all(x in companion for x in ("private static function owner_call","catch ( Throwable $exception )","companion_contract_exception","exception_class","return $fallback")))

add("14 destination readiness remains request-time owner verified and no-permissive-URL",
    "GCU_Companion_Adapters::destination_probe" in contracts
    and "owner_runtime_probe" in companion
    and "owner_unconfirmed" in contracts)

add("15 privacy-minimized attribution and user data rights remain",
    all(x in privacy for x in ("global_privacy_control_requested","measurement_allowed","wp_privacy_personal_data_exporters","wp_privacy_personal_data_erasers","is_file14_acquisition_route")))

add("16 reliability/idempotency/audit/queue controls remain",
    all(x in repo for x in ("run_idempotent_command","verify_audit_chain","dispatch_outbox","process_inbox","cleanup_lifecycle")))

add("17 localization/accessibility/RTL/low-data contracts remain",
    all(x in i18n for x in ("'en-US'","'ur-PK'","'ar-SA'"))
    and all(x in css for x in ("prefers-reduced-motion","prefers-reduced-data","forced-colors","44px","max-width: 360px")))

add("18 migration/schema/rollback safeguards remain",
    all(x in install for x in ("ENGINE=InnoDB","verify_schema","capture_snapshot","rollback_snapshot","GET_LOCK")))

add("19 deterministic package and current regression gates remain",
    "Deterministic double-build mismatch" in build
    and "php -l" in quality
    and "cross-file-integration-tests.php" in quality
    and "review20-cross-file.py" in quality)

add("20 repository/live truth separation and exact 2026-10-08 companion freeze are explicit",
    "No `Staging-Accepted`, `Live-Deployed` or `Operational` claim" in status
    and "repository evidence only" in status.lower()
    and "2f4a89707724fd2b9946600afe10ddab27ec3c2d" in status
    and "cfc5f781a766330314dc98c42abeca0eb7786eba" in status
    and "e35563b7f3d8ebf0acbbc80982b7bcf2e1b78c0a" in status)

if len(checks) != 20:
    print(f"Review definition error: {len(checks)} passes", file=sys.stderr)
    sys.exit(2)

failed = []
for number, (label, ok) in enumerate(checks, 1):
    state = "PASS" if ok else "FAIL"
    print(f"Review {number:02d}: {state} — {label}")
    if not ok:
        failed.append((number, label))

if failed:
    print("\nTwenty-pass review failed:", file=sys.stderr)
    for number, label in failed:
        print(f"- Review {number:02d}: {label}", file=sys.stderr)
    sys.exit(1)

print("\nTwenty-pass File 14 cross-file repository review: PASS — 20/20 final-state gates satisfied")
