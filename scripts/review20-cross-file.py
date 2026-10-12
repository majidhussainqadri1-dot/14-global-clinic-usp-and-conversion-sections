#!/usr/bin/env python3
"""Twenty-pass File 14 cross-file coding-completeness review.

Repository/source evidence only. This script does not claim staging, deployment,
database-migration, live-site, accessibility-device or operational acceptance.
"""
from pathlib import Path
import re
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
readme = read("14-global-clinic-usp-integration/readme.txt")
future_policy = read("14-global-clinic-usp-integration/includes/class-gcu-future-policy.php")

# The checked software candidate is read from source, never frozen to an old version.
match = re.search(r"\bVersion:\s*([0-9]+\.[0-9]+\.[0-9]+)", main)
current_version = match.group(1) if match else ""
current_ledger = list((ROOT / "docs").glob(f"REVIEW-20-CROSS-FILE-COMPLETION-v{current_version}-*.md")) if current_version else []

checks = []
def add(name, ok):
    checks.append((name, bool(ok)))

add("01 exact present-source version, WordPress readme and current audit ledger",
    bool(current_version)
    and f"GCU_VERSION', '{current_version}" in main
    and f"Stable tag: {current_version}" in readme
    and f"Software candidate: `{current_version}`" in status
    and bool(current_ledger)
    and "14-global-clinic-usp-and-conversion-sections" in main)

add("02 File14 governing FR/NFR/Future trace remains complete",
    all(x in trace for x in ("F14-FR-001","F14-FR-016","F14-NFR-001","F14-NFR-010","F14-FUT-01","F14-FUT-24")))

add("03 central canonical-owner boundaries remain explicit",
    "File 14" in policy and "no_cure_guarantee" in policy and "'platform_commission_percent' => 0" in policy)

add("04 File00 action-time authorization remains version-gated and exception-isolated",
    all(x in companion for x in ("FILE00_MIN_VERSION","FILE00_MIN_CONTRACT","SMC_VERSION","SMC_CONTRACT_VERSION","SMC_Contracts","smc_assertions_v1","owner_call","authorization_assertions"))
    and "GCU_Companion_Adapters::authorize" in caps)

add("05 File01 full current module identity/version and lifecycle",
    all(x in companion for x in ("file01_manifest_current( $module, $expected_manifest )","module_state","registered', 'compatible', 'active","'owner_file'","'software_version'","'global_shell_owner'"))
    and "file01_manifest_current" in companion
    and "degraded" in status and "suspended" in status and "retired" in status)

add("06 File01 canonical route key/layout/destination/redirect truth",
    all(x in companion for x in ("expected_routes","file01_route_current( $route","contract-drift","array( 'registered', 'active' )","'route_key'","'redirects'","'layout_context'","'destination'"))
    and "review20-r5-behavioral.php" in quality)

add("07 File01 readiness requires exact current API/events registry contracts",
    all(x in companion for x in ("contracts_ready","missing_contracts","incompatible_contracts","gcu.file14.api","gcu.file14.events","file01_contract_current","list_contracts_for_readiness")))

add("08 File07 current 1.2.1 contract and owner system health gate remain required",
    all(x in companion for x in ("FILE07_MIN_VERSION = '1.2.1'","FILE07_MIN_CONTRACT = '1.2.1'","array( 'DDD_Contracts', 'dependency_health' )","DDD_Observability","system_check","owner_runtime_degraded_readable")))

add("09 File08 clinic integration requires owner health plus API/route/business parity",
    all(x in companion for x in ("FILE08_MIN_VERSION","FILE08_MIN_API","array( 'WCA_Contracts', 'contract_manifest' )","WCA_Observability","runtime_health","'/appointments'","commission_percent","donation_visibility_link")))

add("10 File09 onboarding remains owner-bound/read-only and fail-closed",
    all(x in companion for x in ("FILE09_MIN_VERSION","FILE09_MIN_CONTRACT","gdo_file14_onboarding_destination","'file09'","'file14'","writes_data","automatic_enrollment","automatic_verification","onboarding_destination")))

add("11 File19 optional notifications remain explicit-recipient and exception-isolated",
    all(x in companion for x in ("FILE19_MIN_VERSION","sun_register_notification_producer","sun_ingest_domain_event","gcu_operational_notification_recipients","register_producer","ingest_domain_event","owner_call")))

add("12 File20 availability validates owner-native File14 CentralPlanContract semantics",
    all(x in companion for x in ("FILE20_MIN_VERSION","FILE20_MIN_CONTRACT","FILE20_MAX_CONTRACT_EXCLUSIVE","CentralPlanContract","canonical_contracts","approved-clinic-cta","slots-only","cta-hidden","owner-aware-bounded","file20_contract")))

add("13 File20 remains sole shell owner with bounded File14 recovery",
    all(x in companion for x in ("sabri_shell_route_result_allowed","sabri_shell_system_check_sections","sabri_shell_context_navigation_fallback_url"))
    and "sabri_shell_back_home_controls" not in front)

add("14 File24 availability requires successful boot/service truth",
    all(x in companion for x in ("FILE24_MIN_VERSION","SPCRC_VERSION","did_action( 'spcrc/booted' )","spcrc/governed_artifact_registry","class_exists( 'Sabri\\\\Platform\\\\Security\\\\Plugin' )")))

add("15 File24 assurance manifest remains truthful and unassessed without fabricated evidence",
    all(x in companion for x in ("spcrc/module_manifests","'posture'                => 'unassessed'","'last_security_test'     => ''","'canonical_data_owner'","'release_gate'")))

add("16 File25 current source-visual contracts fail safely on exceptions",
    all(x in companion for x in ("FILE25_MIN_VERSION","FILE25_MIN_CONTRACT","FILE25_MIN_COMPONENT_CONTRACT","sabri_visual_experience_contract","sabri_visual_experience_render_state","visual_system_owner","global_shell_owner","owner_call"))
    and "Sabri\\PublicExperience\\Components::render_state" not in companion
    and "File 25" in status)

add("17 privacy/localization/accessibility/low-data contracts remain",
    all(x in privacy for x in ("global_privacy_control_requested","measurement_allowed","wp_privacy_personal_data_exporters","wp_privacy_personal_data_erasers","is_file14_acquisition_route"))
    and all(x in i18n for x in ("'en-US'","'ur-PK'","'ar-SA'"))
    and all(x in css for x in ("prefers-reduced-motion","prefers-reduced-data","forced-colors","44px","max-width: 360px")))

add("18 migration/schema/rollback safeguards remain",
    all(x in install for x in ("ENGINE=InnoDB","verify_schema","capture_snapshot","rollback_snapshot","GET_LOCK")))

add("19 deterministic build and present-version audit regressions remain governed",
    "Deterministic double-build mismatch" in build
    and "php -l" in quality
    and "cross-file-integration-tests.php" in quality
    and "review20-r5-behavioral.php" in quality
    and "review20-cross-file.py" in quality
    and bool(current_ledger)
    and "review20-cross-file.py" in quality)

add("20 actual release truth and owner-dependent blockers stated without source-to-live leap",
    "unverified" in status.lower()
    and "#19/#22" in status
    and "owner_confirmation_contract_ready" in future_policy
    and "#19" in readme and "#22" in readme
    and "Deployed Version" in read("docs/REVIEW-20-CROSS-FILE-COMPLETION-v1.4.34-R30.md"))

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

print("\nTwenty source-marker checks: PASS (source-only, not 20 independent E2E tests).")
print("Release blockers remain: Issue #19 owner-attested destination/application/booking events; Issue #22 approved experiment baseline restore.")
print("Staging/production deployment parity, DB migration and live runtime: UNVERIFIED. Do not call the module complete.")
