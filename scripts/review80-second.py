#!/usr/bin/env python3
"""Independent second eighty-pass repository audit for File 14.

Each numbered gate re-opens one failure class after the first eighty-pass review.
This is repository evidence only; it is never staging/live/deployed-state evidence.
Version/status assertions follow the current candidate so later corrective patch
releases preserve the safety invariants instead of being rejected for advancing.
"""
from __future__ import annotations
import re
from pathlib import Path

ROOT = Path(__file__).resolve().parents[1]
P = ROOT / "14-global-clinic-usp-integration"


def read(path: str) -> str:
    return (ROOT / path).read_text(encoding="utf-8")


def exists(path: str) -> bool:
    return (ROOT / path).exists()


def has(path: str, needle: str) -> bool:
    return needle in read(path)


def section(text: str, start: str, end: str) -> str:
    a = text.find(start)
    if a < 0:
        return ""
    b = text.find(end, a + len(start))
    return text[a:] if b < 0 else text[a:b]

loader = read("14-global-clinic-usp-integration/global-clinic-usp-integration.php")
contracts = read("14-global-clinic-usp-integration/includes/class-gcu-contracts.php")
rest = read("14-global-clinic-usp-integration/includes/class-gcu-rest.php")
future = read("14-global-clinic-usp-integration/includes/class-gcu-future-intelligence.php")
review = read("14-global-clinic-usp-integration/includes/class-gcu-review80-hardening.php")
install = read("14-global-clinic-usp-integration/includes/class-gcu-install.php")
repo = read("14-global-clinic-usp-integration/includes/class-gcu-repository.php")
privacy = read("14-global-clinic-usp-integration/includes/class-gcu-privacy.php")
policy = read("14-global-clinic-usp-integration/includes/class-gcu-policy.php")
future_policy = read("14-global-clinic-usp-integration/includes/class-gcu-future-policy.php")
frontend = read("14-global-clinic-usp-integration/includes/class-gcu-frontend.php")
hard = read("14-global-clinic-usp-integration/includes/class-gcu-hardening.php")
fi18n = read("14-global-clinic-usp-integration/includes/class-gcu-future-i18n.php")
css = read("14-global-clinic-usp-integration/assets/css/global-clinic-usp-integration.css")
future_css = read("14-global-clinic-usp-integration/assets/css/gcu-future-intelligence.css")
readme = read("14-global-clinic-usp-integration/readme.txt")
trace = read("docs/REQUIREMENTS-TRACEABILITY.md")
release = read("docs/RELEASE-EVIDENCE.md")
status = read("STATUS.md")
quality = read("scripts/quality.sh")
build = read("scripts/build.py")
fresh1 = read("scripts/fresh-review-round-1.sh")
fresh2 = read("scripts/fresh-review-round-2.sh")
public_dto = section(contracts, "public function public_destination($key)", "public function public_destination_health()")
bootstrap = section(future, "public static function bootstrap()", "public static function tables()")
ensure = section(future, "public static function ensure_schema( $force_verify = false )", "public static function runtime_ready()")
quality_fn = section(future, "public static function quality_score()", "public static function friction_summary(")
anomaly_fn = section(future, "public static function anomaly_detector()", "public static function early_stop_guard()")
harden_response = section(review, "public static function harden_rest_response", "private static function json_array")
vm = re.search(r"Version:\s*([0-9]+\.[0-9]+\.[0-9]+)", loader)
cm = re.search(r"GCU_VERSION'\s*,\s*'([^']+)'", loader)
current_version = vm.group(1) if vm else ""
version_ok = bool(vm and cm and current_version == cm.group(1))

checks: list[tuple[str, bool]] = [
    ("01 exact software and plan identity", version_ok and "SSH-F14-PLAN-2026-v1.0" in loader and "SSH-F14-FUTURE-CTI-2026-v2.0" in loader),
    ("02 traceability current-version truth", current_version and f"Requirements Traceability — v{current_version}" in trace and f"File 14 v{current_version} may only claim a status" in trace),
    ("03 current schema identities remain separated", "GCU_SCHEMA_VERSION', 10006" in loader and "GCU_FUTURE_SCHEMA_VERSION', 1" in loader),
    ("04 status prose is not contradictory candidate-plus-merged state", current_version and f"v{current_version}" in status and "Repository Candidate" in status and "Corrective Candidate — Merged" not in status),
    ("05 release evidence uses current candidate framing", current_version and f"v{current_version}" in release and "Repository Candidate" in release and "exact review/main SHA being accepted" in release and "fresh post-merge" in release),
    ("06 obsolete v1.4.0 PR-3 one-shot release automation removed", not exists(".github/workflows/file14-one-shot-release-gate.yml")),
    ("07 temporary corrective patch machinery removed", not exists(".github/workflows/file14-second-review-corrective-patch.yml") and not exists("scripts/apply-file14-second-review-corrections.py")),
    ("08 canonical package folder and text domain retained", "global-clinic-usp-integration" in loader and "Text Domain: global-clinic-usp-integration" in loader),
    ("09 canonical repository identity retained", "14-global-clinic-usp-and-conversion-integration" in loader),
    ("10 public destination internal health remains available only for trusted internal consumers", "all_destination_health" in contracts),
    ("11 dedicated public destination DTO exists", "public function public_destination($key)" in contracts and "public function public_destination_health()" in contracts),
    ("12 public destination DTO strips internal owner/contract/freshness fields", all(x not in public_dto for x in ("'owner'=>", "'contract'=>", "'verified_at'=>"))),
    ("13 public destinations endpoint uses safe DTO and obeys runtime fail-close", "public_destination_health()" in rest and "public function destinations(){$ready=GCU_Install::ready_for_runtime();" in rest),
    ("14 destination availability cannot be elevated by consumer filter", "false===(bool)$filtered['available']" in contracts and "available=true" not in section(contracts, "apply_filters('gcu_destination_contract_v1'", "return array('key'=>$key")),
    ("15 strict same-origin scheme host and effective port retained", all(x in hard for x in ("strict_same_origin_url", "home_scheme", "target_scheme", "effective_port"))),
    ("16 File 07/08/09 remain destination owners", all(x in contracts for x in ("'File 07'", "'File 08'", "'File 09'"))),
    ("17 File 20 placement readiness contract retained", "sabri_shell_slot_ready_v1" in contracts),
    ("18 Future bootstrap performs no schema migration on every request", "ensure_schema" not in bootstrap),
    ("19 Future schema migration is serialized by named advisory lock", "acquire_db_lock( 'future-schema', 5 )" in ensure and "release_db_lock( $lock )" in ensure),
    ("20 activation and controlled repair explicitly ensure Future schema", "activate(){$r=self::install_or_upgrade(true)" in install and "self::ensure_future_schema(true);" in install and "private static function ensure_future_schema($force_verify=false)" in install),
    ("21 Future schema fast path avoids SHOW TABLE queries when current and healthy", "if ( ! $force_verify && self::SCHEMA_VERSION ===" in ensure and "&& ! get_option( self::SAFE_MODE_OPTION, 0 ) ) {\n\t\t\treturn true;" in ensure),
    ("22 Future schema verification remains explicit after actual migration", "self::verify_schema()" in ensure and "future_schema_verification_failed" in ensure),
    ("23 Future safe mode is independently recorded on schema failure", "SAFE_MODE_OPTION" in ensure and "gcu_future_schema_lock_busy" in ensure),
    ("24 all Future REST routes fail closed when base/Future runtime is not ready", "'/gcu/v1/future/'" in review and "GCU_Future_Intelligence::runtime_ready()" in review and "gcu_future_schema_pending" in future),
    ("25 base writes still require verified base version/schema", "ready_for_mutation" in repo and "gcu_upgrade_pending" in install),
    ("26 public browsing remains allowed for safe blocks", "'/blocks'" in rest and "permission_callback'=>'__return_true'" in rest),
    ("27 protected content creation remains capability gated", "can_manage_content" in rest and "MANAGE_CONTENT" in read("14-global-clinic-usp-integration/includes/class-gcu-capabilities.php")),
    ("28 placement writes remain capability gated", "can_manage_placements" in rest and "MANAGE_PLACEMENTS" in read("14-global-clinic-usp-integration/includes/class-gcu-capabilities.php")),
    ("29 experiment writes remain capability gated", "can_manage_experiments" in rest and "MANAGE_EXPERIMENTS" in read("14-global-clinic-usp-integration/includes/class-gcu-capabilities.php")),
    ("30 governed claim writes retain approval capability", "can_approve_claims" in rest and "APPROVE_CLAIMS" in read("14-global-clinic-usp-integration/includes/class-gcu-capabilities.php")),
    ("31 workflow object/state revalidation retained", "row_version" in repo and "transition_allowed" in repo and "validate_transition_target" in repo),
    ("32 mutating REST commands retain idempotency key", "X-GCU-Idempotency-Key" in rest and "run_idempotent_command" in rest),
    ("33 event measurement token remains single-use", "consumed_at IS NULL" in repo and "event-token" in rest),
    ("34 atomic DB rate limiting retained", "ON DUPLICATE KEY UPDATE counter=counter+1" in repo),
    ("35 durable outbox retained", "dispatch_outbox" in repo and "dead" in repo),
    ("36 durable inbox and stale-lock recovery retained", "process_inbox" in repo and "DATE_SUB(UTC_TIMESTAMP(),INTERVAL 10 MINUTE)" in repo),
    ("37 audit-chain verification retained", "verify_audit_chain" in repo and "previous_hash" in install and "row_hash" in install),
    ("38 quality analytics suppress exact sub-threshold sample_count", "'sample_count' => GCU_Future_Policy::cohort_allowed( $selected ) ? $selected : null" in quality_fn and "'cohort_threshold' => GCU_Future_Policy::MIN_COHORT" in quality_fn),
    ("39 anomaly analytics suppress exact sub-threshold current/baseline counts", "'current_sample' => null" in anomaly_fn and "'baseline_sample' => null" in anomaly_fn and "'suppressed' => true" in anomaly_fn),
    ("40 friction per-stage small-cohort sanitization retained", "sanitize_friction_payload" in review and "suppressed_stages" in review),
    ("41 FAQ aggregates reject direct identifiers", "question_contains_sensitive_data" in review and "patient\\s*id" in review),
    ("42 measurement still requires consent", "measurement_allowed" in privacy and "analytics_consent" in privacy),
    ("43 Global Privacy Control remains honored", "HTTP_SEC_GPC" in privacy or "global_privacy_control_requested" in privacy),
    ("44 Save-Data/reduced-data remains honored", "HTTP_SAVE_DATA" in privacy or "low_bandwidth_requested" in privacy),
    ("45 sensitive routes remain outside measurement", "is_sensitive_path" in privacy),
    ("46 attribution remains File-14 acquisition-route bounded", "is_file14_acquisition_route" in privacy),
    ("47 bounded pseudonym lifecycle retained", "USER_SUBJECT_META" in privacy and "GUEST_SUBJECT_TTL" in privacy and "random_bytes" in privacy),
    ("48 WordPress privacy exporter retained", "wp_privacy_personal_data_exporters" in privacy),
    ("49 WordPress privacy eraser retained", "wp_privacy_personal_data_erasers" in privacy),
    ("50 public report path rejects sensitive data and is rate limited", "report_contains_sensitive_data" in future and "future-report" in future and "consume_rate_limit" in future),
    ("51 0% commission rule retained", "platform_commission_percent" in policy and "zero_platform_commission" in policy),
    ("52 one approved free tier retained", "approved_core_tier" in policy and "free_approved_core" in policy),
    ("53 optional support cannot purchase advantage", "support_affects_visibility" in policy and "optional_support_no_ranking" in policy),
    ("54 response hardening preserves an endpoint's explicit cache policy", "get_headers()" in harden_response and "empty( $headers['Cache-Control'] )" in harden_response),
    ("55 public Future response retains explicit public revalidation policy", "public, no-cache, max-age=0, must-revalidate" in future and "public_response" in future),
    ("56 private Future responses remain no-store", "no-store, private" in future and "no_store_response" in future),
    ("57 F14-FR-001 patient value proposition remains traceable", "F14-FR-001" in trace and "patient_hero" in policy),
    ("58 F14-FR-002 doctor value proposition remains traceable", "F14-FR-002" in trace and "doctor_hero" in policy),
    ("59 F14-FR-003 primary CTA destinations remain canonical", "F14-FR-003" in trace and "doctor_directory" in contracts and "doctor_onboarding" in contracts),
    ("60 F14-FR-004 how-it-works route remains owned locally", "F14-FR-004" in trace and "how_it_works" in contracts),
    ("61 F14-FR-005 trust content remains governed", "F14-FR-005" in trace and "emergency" in policy.lower() and "verification" in policy.lower()),
    ("62 F14-FR-006 business-copy parity remains guarded", "F14-FR-006" in trace and "parity_status" in future),
    ("63 F14-FR-007/008 placement and reusable-block contracts remain", "F14-FR-007" in trace and "F14-FR-008" in trace and "gcu_placements" in install and "gcu_content_blocks" in install),
    ("64 F14-FR-009/010 destination health and attribution remain", "F14-FR-009" in trace and "F14-FR-010" in trace and "public_destination_health" in contracts and "campaign" in privacy),
    ("65 F14-FR-011/012 funnel and experiment governance remain", "F14-FR-011" in trace and "F14-FR-012" in trace and "gcu_conversion_events" in install and "gcu_experiments" in install),
    ("66 F14-FR-013/014 localization and FAQ governance remain", "F14-FR-013" in trace and "F14-FR-014" in trace and "'ur-PK'" in fi18n and "'ar-SA'" in fi18n and "faq" in policy.lower()),
    ("67 F14-FR-015 accessibility remains", "F14-FR-015" in trace and "min-height: 44px" in css and "focus-visible" in css),
    ("68 F14-FR-016 claim audit/freshness remains", "F14-FR-016" in trace and "claim_freshness_sentinel" in future and "claim_history" in repo),
    ("69 all 24 Future CTI capability IDs remain present", len(set(re.findall(r"F14-FUT-\d{2}", future_policy))) == 24 and "F14-FUT-24" in trace),
    ("70 release-evidence pre-merge candidate wording is truthful", "Eighty-Pass Corrective Candidate" not in release and "Repository Candidate" in release and "main merged" in release),
    ("71 status candidate/merged wording is truthful", "Corrective Candidate — Merged" not in status and "Repository Candidate" in status and "exact resulting `main` SHA" in status),
    ("72 Sabri Green remains exact", "#087A4E" in loader and "#087A4E" in css and "#087A4E" in future_css),
    ("73 RTL/LTR localization support retained", "direction" in frontend and "rtl" in css.lower() and "'ur-PK'" in fi18n and "'ar-SA'" in fi18n),
    ("74 reduced motion/data and forced-colors retained", "prefers-reduced-motion" in css and "prefers-reduced-data" in css and "forced-colors" in future_css),
    ("75 320px-class reflow and external 400% zoom truth retained", "max-width: 360px" in css and "400%" in trace and "staging" in trace.lower()),
    ("76 deterministic package, unsafe-path guard and SBOM retained", "Deterministic double-build mismatch" in build and "Unsafe archive path" in build and "file-sha256-sbom-v1" in build),
    ("77 PHP 7.4 and 8.3 exact quality matrix retained", "php: ['7.4','8.3']" in read(".github/workflows/file14-quality.yml")),
    ("78 both fresh review rounds plus second audit are in the quality gate", "fresh-review-round-1" in read(".github/workflows/file14-fresh-reviews.yml") and "fresh-review-round-2" in read(".github/workflows/file14-fresh-reviews.yml") and "review80-second.py" in quality and "second-review-regression-tests.php" in quality),
    ("79 second eighty-pass ledger and defect index are present", exists("docs/REVIEW-80-SECOND-LEDGER-v1.4.1.md") and "Defect-round index" in read("docs/REVIEW-80-SECOND-LEDGER-v1.4.1.md")),
    ("80 Live-First truth boundary remains explicit", "Staging-Accepted" in status and "Live-Deployed" in status and "Operational" in status and "deployed-artifact parity" in release),
]

assert len(checks) == 80, f"Second review definition drift: {len(checks)} checks"
failed = [(i, name) for i, (name, ok) in enumerate(checks, 1) if not ok]
for i, (name, ok) in enumerate(checks, 1):
    print(f"Second Review {i:02d}: {'PASS' if ok else 'FAIL'} — {name}")
if failed:
    print("\nFailed second-review gates:")
    for i, name in failed:
        print(f"- Review {i:02d}: {name}")
    raise SystemExit(1)
print("\nSecond eighty-pass repository review: PASS — 80/80 final-state gates satisfied")
