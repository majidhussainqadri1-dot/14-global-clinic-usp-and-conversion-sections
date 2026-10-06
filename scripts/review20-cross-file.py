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

add("01 exact repository/release identity",
    "Version: 1.4.5" in main
    and "GCU_VERSION', '1.4.5" in main
    and "14-global-clinic-usp-and-conversion-sections" in main)

add("02 File14 governing FR/NFR/Future trace remains complete",
    all(x in trace for x in ("F14-FR-001","F14-FR-016","F14-NFR-001","F14-NFR-010","F14-FUT-01","F14-FUT-24")))

add("03 central canonical-owner boundaries remain explicit",
    "File 14" in policy and "no_cure_guarantee" in policy and "'platform_commission_percent' => 0" in policy)

add("04 File00 action-time authorization uses current versioned assertions",
    "SMC_Contracts" in companion
    and "smc_assertions_v1" in companion
    and "smc_restricted_capabilities" in companion
    and "GCU_Companion_Adapters::authorize" in caps)

add("05 File07 doctor-directory integration uses current runtime contract",
    "DDD_Contracts::dependency_health" in companion
    and "home_url( '/doctors/' )" in companion)

add("06 File08 clinic/appointment integration uses current runtime contract",
    "WCA_Contracts::contract_manifest" in companion
    and "home_url( '/appointments/' )" in companion)

add("07 File09 onboarding integration uses current runtime contract",
    "gdo_file14_onboarding_destination" in companion)

add("08 File20 remains shell/navigation owner through current hooks",
    "sabri_shell_route_result_allowed" in companion
    and "sabri_shell_system_check_sections" in companion
    and "sabri_shell_context_navigation_fallback_url" in companion
    and "sabri_shell_back_home_controls" not in front)

add("09 File25 visual system is consumed without taking domain truth",
    "Sabri\\\\PublicExperience\\\\Components" in companion
    and "sabri-ui-card" in front
    and "sabri-ui-button" in front)

add("10 File24 assurance-plane manifest is registered without fabricated acceptance evidence",
    "spcrc/module_manifests" in companion
    and "'canonical_data_owner'" in companion
    and "'release_gate'" in companion
    and "'posture'                => 'unassessed'" in companion
    and "'last_security_test'     => ''" in companion)

add("11 File14 placements require File01 canonical routes and File20 shell readiness",
    "gcu_file14_placement_ready_v1" in contracts
    and "GCU_Companion_Adapters::placement_contract_ready" in contracts
    and "file01_route_registry_state" in companion
    and "sync_file01_registry" in companion
    and "SPF_Registry::register_manifest" in companion
    and "SPF_Registry::map_route" in companion
    and "SPF_Registry::register_contract" in companion
    and "sabri_shell_slot_ready_v1" not in contracts)

add("12 destination readiness is request-time owner verified and fail-closed",
    "GCU_Companion_Adapters::destination_probe" in contracts
    and "owner_runtime_probe" in companion
    and "owner_unconfirmed" in contracts)

add("13 USP/trust/zero-commission content remains governed and localized",
    all(x in policy for x in ("patient_hero","doctor_hero","zero_platform_commission","optional_support_no_ranking","verification_required")))

add("14 privacy-minimized attribution and user data rights remain",
    all(x in privacy for x in ("global_privacy_control_requested","measurement_allowed","wp_privacy_personal_data_exporters","wp_privacy_personal_data_erasers","is_file14_acquisition_route")))

add("15 reliability/idempotency/audit/queue controls remain",
    all(x in repo for x in ("run_idempotent_command","verify_audit_chain","dispatch_outbox","process_inbox","cleanup_lifecycle")))

add("16 security and abuse controls remain fail-closed",
    all(x in rest for x in ("X-GCU-Idempotency-Key","consume_event_token","permission_callback"))
    and "strict_same_origin_url" in read("14-global-clinic-usp-integration/includes/class-gcu-hardening.php"))

add("17 localization/accessibility/RTL/low-data contracts remain",
    all(x in i18n for x in ("'en-US'","'ur-PK'","'ar-SA'"))
    and all(x in css for x in ("prefers-reduced-motion","prefers-reduced-data","forced-colors","44px","max-width: 360px")))

add("18 migration/schema/rollback safeguards remain",
    all(x in install for x in ("ENGINE=InnoDB","verify_schema","capture_snapshot","rollback_snapshot","GET_LOCK")))

add("19 deterministic package and automated repository QA remain",
    "Deterministic double-build mismatch" in build
    and "php -l" in quality
    and "cross-file-integration-tests.php" in quality)

add("20 repository/staging/live truth separation and File19 addressed-alert integration remain explicit",
    "No `Staging-Accepted`, `Live-Deployed` or `Operational` claim" in status
    and "repository evidence only" in status.lower()
    and "file19_notifications" in companion
    and "sun_register_notification_producer" in companion
    and "sun_ingest_domain_event" in companion
    and "gcu_operational_notification_recipients" in companion)

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
