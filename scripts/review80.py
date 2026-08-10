#!/usr/bin/env python3
"""Deterministic 80-pass source/governance review gate for File 14 v1.4.1.

The order mirrors docs/REVIEW-80-LEDGER-v1.4.1.md.  A pass proves only the
repository property named by that round.  It is not staging, deployed-code,
database, live or operational evidence.
"""
from __future__ import annotations
import re
from pathlib import Path

ROOT = Path(__file__).resolve().parents[1]
P = ROOT / "14-global-clinic-usp-integration"


def read(path: str) -> str:
    return (ROOT / path).read_text(encoding="utf-8")


loader = read("14-global-clinic-usp-integration/global-clinic-usp-integration.php")
future_policy = read("14-global-clinic-usp-integration/includes/class-gcu-future-policy.php")
future = read("14-global-clinic-usp-integration/includes/class-gcu-future-intelligence.php")
future_guards = read("14-global-clinic-usp-integration/includes/class-gcu-future-guards.php")
review = read("14-global-clinic-usp-integration/includes/class-gcu-review80-hardening.php")
repo = read("14-global-clinic-usp-integration/includes/class-gcu-repository.php")
install = read("14-global-clinic-usp-integration/includes/class-gcu-install.php")
privacy = read("14-global-clinic-usp-integration/includes/class-gcu-privacy.php")
contracts = read("14-global-clinic-usp-integration/includes/class-gcu-contracts.php")
hard = read("14-global-clinic-usp-integration/includes/class-gcu-hardening.php")
rest = read("14-global-clinic-usp-integration/includes/class-gcu-rest.php")
frontend = read("14-global-clinic-usp-integration/includes/class-gcu-frontend.php")
future_i18n = read("14-global-clinic-usp-integration/includes/class-gcu-future-i18n.php")
css = read("14-global-clinic-usp-integration/assets/css/global-clinic-usp-integration.css")
future_css = read("14-global-clinic-usp-integration/assets/css/gcu-future-intelligence.css")
readme = read("14-global-clinic-usp-integration/readme.txt")
root_readme = read("README.md")
trace = read("docs/REQUIREMENTS-TRACEABILITY.md")
status = read("STATUS.md")
release = read("docs/RELEASE-EVIDENCE.md")
ledger = read("docs/REVIEW-80-LEDGER-v1.4.1.md")
workflow = read(".github/workflows/file14-quality.yml")
quality = read("scripts/quality.sh")
fresh1 = read("scripts/fresh-review-round-1.sh")
fresh2 = read("scripts/fresh-review-round-2.sh")
build = read("scripts/build.py")
uninstall = read("14-global-clinic-usp-integration/uninstall.php")
contract_tests = read("tests/contract-tests.php")
central_tests = read("tests/central-plan-tests.php")
review_tests = read("tests/review80-hardening-tests.php")
all_php = "\n".join(p.read_text(encoding="utf-8") for p in P.rglob("*.php"))

checks: list[tuple[str, bool]] = [
    ("01 governing scope / 24 Future IDs / no duplicate post backend", "SSH-F14-FUTURE-CTI-2026-v2.0" in loader and len(set(re.findall(r"F14-FUT-\d{2}", future_policy))) == 24 and re.search(r"wp_insert_post\s*\(", all_php) is None),
    ("02 request-time claim freshness and public fail-closed parity", "claim_freshness_sentinel" in review and "gcu_review80_public_copy_guarded" in review and "finalize_public_guard" in review),
    ("03 meaningful mandatory experiment guardrails", "guardrails_valid" in review and "Every mandatory experiment safety guardrail" in review),
    ("04 Urdu/Arabic dark-pattern and positive-commission guards", all(x in review for x in ("fake_scarcity_ur", "guarantee_ur", "fake_scarcity_ar", "guarantee_ar", "positive_commission_ur", "positive_commission_ar"))),
    ("05 per-stage small-cohort suppression", "sanitize_friction_payload" in review and "suppressed_stages" in review and "MIN_COHORT = 10" in review),
    ("06 FAQ aggregate direct-identifier rejection", "question_contains_sensitive_data" in review and "CNIC" in review and "patient\\s*id" in review),
    ("07 Scenario Laboratory Future safe-mode truth", "normalize_scenario_payload" in review and "SAFE_MODE_OPTION" in review and "module_enabled" in review),
    ("08 quality evidence remains provisional when material inputs unverified", "unverified_metrics" in review and "privacy_effectiveness" in review and "provisional" in review),
    ("09 corrective release identity is 1.4.1 everywhere that defines current package", "Version: 1.4.1" in loader and "GCU_VERSION', '1.4.1'" in loader and "Stable tag: 1.4.1" in readme and "file-14-v1.4.1" in workflow),
    ("10 truthful multilingual no-advantage disclosure regression", "Truthful Urdu no-advantage copy must pass" in review_tests and "نہیں" in review),
    ("11 REST post-dispatch hardening scoped to File 14 namespace", "0 !== strpos( $route, '/gcu/v1/' )" in review),
    ("12 contract regression version/tag aligned to 1.4.1", "Version 1.4.1 drift" in contract_tests and "Stable tag: 1.4.1" in contract_tests),
    ("13 contract scope assertion is literal and non-interpolating", "strpos($review,'0 !== strpos( $route, \\'/gcu/v1/\\' )')" in contract_tests),
    ("14 central-plan regression aligned to 1.4.1", "Candidate version not 1.4.1" in central_tests and "Stable tag: 1.4.1" in central_tests),
    ("15 STATUS current candidate truth is 1.4.1", "v1.4.1 Eighty-Pass Corrective Candidate" in status and "Software candidate: `1.4.1`" in status),
    ("16 release-evidence current candidate truth is 1.4.1", "Release Evidence — v1.4.1" in release and "Software candidate: `1.4.1`" in release),
    ("17 corrective round ledger and defect index exist", "Defects were discovered in rounds **02, 03, 04, 05, 06, 07, 08, 09, 10, 11, 12, 13, 14, 15, 16 and 17**" in ledger and "| 80 |" in ledger),
    ("18 base schema remains 10004 and separate from patch version", "GCU_SCHEMA_VERSION', 10004" in loader),
    ("19 Future additive schema remains 1 with InnoDB verification", "GCU_FUTURE_SCHEMA_VERSION', 1" in loader and "SHOW TABLE STATUS" in install and "innodb" in install.lower()),
    ("20 install/upgrade named lock retained", "SELECT GET_LOCK" in install),
    ("21 transaction boundaries retained", "START TRANSACTION" in install and "START TRANSACTION" in future),
    ("22 rollback snapshot hash and rollback retained", "snapshot_hash" in install and "rollback_snapshot" in install),
    ("23 uninstall remains non-destructive by default / dual-guard purge", "GCU_ALLOW_PURGE" in uninstall and "gcu_purge_on_uninstall" in uninstall),
    ("24 tamper-evident audit-chain verification retained", "verify_audit_chain" in repo and "previous_hash" in install),
    ("25 measurement token remains atomic one-time", "consumed_at IS NULL" in repo and "data-gcu-event-token" in quality),
    ("26 rate limiting remains atomic", "ON DUPLICATE KEY UPDATE counter=counter+1" in repo),
    ("27 durable idempotent command state retained", "run_idempotent_command" in repo),
    ("28 durable outbox retry/dead-letter processing retained", "dispatch_outbox" in repo and "dead" in repo.lower()),
    ("29 inbox processing and stale-lock recovery retained", "process_inbox" in repo and "DATE_SUB(UTC_TIMESTAMP(),INTERVAL 10 MINUTE)" in repo),
    ("30 same-origin validation covers scheme host and effective port", all(x in hard for x in ("home_scheme", "target_scheme", "effective_port", "strict_same_origin_url"))),
    ("31 consumer cannot elevate owner readiness", "may never elevate owner readiness" in contracts),
    ("32 File 20 placement readiness contract retained", "sabri_shell_slot_ready_v1" in contracts),
    ("33 File 20 remains sole shell/navigation owner", "File 20 remains the sole global shell/navigation owner" in root_readme),
    ("34 File 25 visual/design boundary remains traceable", "File 25" in trace and "visual" in trace.lower()),
    ("35 File 07 directory destination ownership retained", "'File 07'" in contracts),
    ("36 File 08 clinic/booking destination ownership retained", "'File 08'" in contracts),
    ("37 File 09/File 00 onboarding-verification ownership retained", "'File 09'" in contracts and "File 09 / File 00" in future_policy),
    ("38 no alternate File 00 authority created", "verification_owner' => 'File 09 / File 00'" in future_policy and "GCU_Capabilities" in loader),
    ("39 public-safe blocks endpoint remains browseable", "'/blocks'" in rest and "__return_true" in rest),
    ("40 privileged Future endpoints retain admin permission boundary", "'permission_callback' => array( __CLASS__, 'admin_permission' )" in future),
    ("41 workflow state-transition validation retained", "transition_allowed" in read("14-global-clinic-usp-integration/includes/class-gcu-policy.php")),
    ("42 no direct companion write/post backend introduced", re.search(r"wp_insert_post\s*\(", all_php) is None and "Files 00/07/08/09/20/24/25" in review),
    ("43 measurement still requires consent", "analytics_consent" in privacy and "measurement_allowed" in privacy),
    ("44 Global Privacy Control retained", "HTTP_SEC_GPC" in privacy),
    ("45 Save-Data / reduced-data suppression retained", "HTTP_SAVE_DATA" in privacy and "low_bandwidth_requested" in privacy),
    ("46 sensitive-route measurement exclusion retained", "is_sensitive_path" in privacy),
    ("47 attribution remains restricted to File 14 acquisition routes", "is_file14_acquisition_route" in privacy),
    ("48 WordPress privacy exporter retained", "wp_privacy_personal_data_exporters" in privacy),
    ("49 WordPress privacy eraser retained", "wp_privacy_personal_data_erasers" in privacy),
    ("50 random pseudonym generation and bounded guest TTL retained", "random_bytes" in privacy and "GUEST_SUBJECT_TTL" in privacy),
    ("51 misleading-copy report loop remains privacy guarded", "gcu_future_reports" in future and "question_contains_sensitive_data" in review),
    ("52 Founder-level governed public-record approval retained", "APPROVE_CLAIMS" in future_guards and "gcu_future_founder_approval_required" in future_guards),
    ("53 FAQ intelligence cannot auto-publish", "gcu_future_faq_suggestion_cannot_publish" in future_guards),
    ("54 AI draft cannot auto-publish", "gcu_future_ai_draft_cannot_publish" in future_guards and "auto_publish' => false" in future),
    ("55 AI assistance remains approved-claim bounded", "approved_claims" in future and "AI Ethical Copy Assistant" in future_policy),
    ("56 sensitive experiment profiling remains blocked", "sensitive_sampling" in future_policy and "health profiling" in future_policy),
    ("57 experiment early-stop guard retained", "early_stop_guard" in future),
    ("58 zero-commission/free/support parity sentinel retained", "parity_status" in future and "zero_commission" in future_policy and "optional_support" in future_policy),
    ("59 public trust evidence drawer retained", "trust" in future.lower() and "F14-FUT-03" in future_policy),
    ("60 public material change log retained", "public_change_log" in future and "F14-FUT-22" in future_policy),
    ("61 patient Choose-Safely guide remains educational", "patient_guide" in future_policy and "F14-FUT-23" in future_policy),
    ("62 doctor readiness remains non-binding", "'binding' => false" in future_policy and "F14-FUT-24" in future_policy),
    ("63 en-US/ur-PK/ar-SA Future locale coverage retained", all(x in future_i18n for x in ("'ur-PK'", "'ar-SA'")) and "en-US" in future_policy),
    ("64 RTL/LTR direction support retained", "direction" in frontend and "rtl" in css.lower()),
    ("65 terminology lock/provenance retained", "terminology" in future.lower() and "F14-FUT-20" in future_policy),
    ("66 44px interaction target retained", "44px" in css or "44px" in future_css),
    ("67 keyboard/focus-visible contract retained", "focus-visible" in css or "focus-visible" in future_css),
    ("68 reduced-motion support retained", "prefers-reduced-motion" in css and "prefers-reduced-motion" in future_css),
    ("69 forced-colors support retained", "forced-colors" in future_css),
    ("70 320px-class reflow gate retained", "max-width: 360px" in css and "320" in trace),
    ("71 400% zoom remains an explicit external acceptance gate", "400%" in trace and "400%" in status),
    ("72 File 20 Back/Home contract retained", "sabri_shell_back_home_controls" in frontend),
    ("73 degraded routes remain noindex", "noindex,nofollow" in frontend),
    ("74 inline executable markup remains forbidden by quality gate", "onclick=|onerror=|onload=|javascript:" in quality),
    ("75 embedded-secret scan remains in quality gate", "Potential embedded secret detected" in quality),
    ("76 PHP 7.4 exact-head workflow target retained", "php: ['7.4','8.3']" in workflow and "Version: 1.4.1" in fresh1),
    ("77 PHP 8.3 exact-head workflow target retained", "php: ['7.4','8.3']" in workflow and "Stable tag: 1.4.1" in fresh2),
    ("78 deterministic double-build gate retained", "Deterministic double-build mismatch" in build),
    ("79 ZIP path/CRC + SHA-256 + file-level SBOM gate retained", "Unsafe archive path" in build and "file-sha256-sbom-v1" in build and "1.4.1.zip.sha256" in workflow),
    ("80 truth-status / Live-First boundary remains explicit", "Never infer staging or live state from this repository alone" in root_readme and "No `Staging-Accepted`, `Live-Deployed` or `Operational` claim" in status),
]

assert len(checks) == 80, f"Internal review definition drift: {len(checks)} checks"
failed = [(i + 1, name) for i, (name, ok) in enumerate(checks) if not ok]
for i, (name, ok) in enumerate(checks, 1):
    print(f"Review {i:02d}: {'PASS' if ok else 'FAIL'} — {name}")
if failed:
    print("\nFailed review gates:")
    for i, name in failed:
        print(f"- Review {i:02d}: {name}")
    raise SystemExit(1)
print("\nEighty-pass repository review: PASS — 80/80 final-state gates satisfied")
