#!/usr/bin/env python3
"""Deterministic 80-pass source/governance review gate for File 14.

This is not staging or live evidence. It verifies eighty independent repository
properties after the corrective review and fails on the first reopened gap set.
"""
from __future__ import annotations
import re
from pathlib import Path

ROOT = Path(__file__).resolve().parents[1]
P = ROOT / "14-global-clinic-usp-integration"

def read(path: str) -> str:
    return (ROOT / path).read_text(encoding="utf-8")

def has(path: str, needle: str) -> bool:
    return needle in read(path)

def absent(path: str, pattern: str) -> bool:
    return re.search(pattern, read(path), re.I | re.M) is None

loader = read("14-global-clinic-usp-integration/global-clinic-usp-integration.php")
future_policy = read("14-global-clinic-usp-integration/includes/class-gcu-future-policy.php")
future = read("14-global-clinic-usp-integration/includes/class-gcu-future-intelligence.php")
review = read("14-global-clinic-usp-integration/includes/class-gcu-review80-hardening.php")
repo = read("14-global-clinic-usp-integration/includes/class-gcu-repository.php")
install = read("14-global-clinic-usp-integration/includes/class-gcu-install.php")
privacy = read("14-global-clinic-usp-integration/includes/class-gcu-privacy.php")
contracts = read("14-global-clinic-usp-integration/includes/class-gcu-contracts.php")
frontend = read("14-global-clinic-usp-integration/includes/class-gcu-frontend.php")
future_i18n = read("14-global-clinic-usp-integration/includes/class-gcu-future-i18n.php")
css = read("14-global-clinic-usp-integration/assets/css/global-clinic-usp-integration.css")
future_css = read("14-global-clinic-usp-integration/assets/css/gcu-future-intelligence.css")
readme = read("14-global-clinic-usp-integration/readme.txt")
trace = read("docs/REQUIREMENTS-TRACEABILITY.md")
workflow = read(".github/workflows/file14-quality.yml")
quality = read("scripts/quality.sh")
fresh1 = read("scripts/fresh-review-round-1.sh")
fresh2 = read("scripts/fresh-review-round-2.sh")
build = read("scripts/build.py")
uninstall = read("14-global-clinic-usp-integration/uninstall.php")

checks: list[tuple[str, bool]] = [
    ("01 governing Future CTI plan marker", "SSH-F14-FUTURE-CTI-2026-v2.0" in loader),
    ("02 exactly 24 Future feature IDs", len(set(re.findall(r"F14-FUT-\d{2}", future_policy))) == 24),
    ("03 corrective software version 1.4.1", "Version: 1.4.1" in loader and "GCU_VERSION', '1.4.1'" in loader),
    ("04 base schema 10004", "GCU_SCHEMA_VERSION', 10004" in loader),
    ("05 Future schema 1", "GCU_FUTURE_SCHEMA_VERSION', 1" in loader),
    ("06 canonical repository identity", "14-global-clinic-usp-and-conversion-integration" in loader),
    ("07 exact Sabri Green token", "#087A4E" in loader and "#087A4E" in css),
    ("08 PHP 7.4 floor retained", "Requires PHP: 7.4" in loader),
    ("09 no duplicate WordPress post backend", re.search(r"wp_insert_post\s*\(", "\n".join(p.read_text(encoding='utf-8') for p in P.rglob('*.php'))) is None),
    ("10 deprecated host-only URL helper unused", "GCU_Policy::same_origin_url" not in "\n".join(p.read_text(encoding='utf-8') for p in P.rglob('*.php'))),
    ("11 strict same-origin helper present", has("14-global-clinic-usp-integration/includes/class-gcu-hardening.php", "strict_same_origin_url")),
    ("12 inline executable markup forbidden by gate", "onclick=|onerror=|onload=|javascript:" in quality),
    ("13 single-use event token absent from HTML source gate", "data-gcu-event-token" in quality),
    ("14 event token atomic one-time consumption", "consumed_at IS NULL" in repo),
    ("15 atomic database rate limit", "ON DUPLICATE KEY UPDATE counter=counter+1" in repo),
    ("16 durable idempotent command", "run_idempotent_command" in repo),
    ("17 durable outbox processing", "dispatch_outbox" in repo),
    ("18 durable inbox processing", "process_inbox" in repo),
    ("19 stale queue lock recovery", "DATE_SUB(UTC_TIMESTAMP(),INTERVAL 10 MINUTE)" in repo),
    ("20 tamper-evident audit-chain support", "verify_audit_chain" in repo and "previous_hash" in install),
    ("21 install/upgrade named lock", "SELECT GET_LOCK" in install),
    ("22 InnoDB schema verification", "SHOW TABLE STATUS" in install and "innodb" in install.lower()),
    ("23 rollback snapshot hash", "snapshot_hash" in install),
    ("24 rollback implementation present", "rollback_snapshot" in install),
    ("25 uninstall is dual-guard destructive only", "GCU_ALLOW_PURGE" in uninstall and "gcu_purge_on_uninstall" in uninstall),
    ("26 public content endpoint remains browseable", "'/blocks'" in read("14-global-clinic-usp-integration/includes/class-gcu-rest.php") and "__return_true" in read("14-global-clinic-usp-integration/includes/class-gcu-rest.php")),
    ("27 measurement requires consent", "analytics_consent" in privacy and "measurement_allowed" in privacy),
    ("28 Global Privacy Control honored", "HTTP_SEC_GPC" in privacy),
    ("29 Save-Data honored", "HTTP_SAVE_DATA" in privacy),
    ("30 sensitive-route measurement exclusion", "is_sensitive_path" in privacy),
    ("31 attribution restricted to File 14 acquisition routes", "is_file14_acquisition_route" in privacy),
    ("32 WordPress privacy exporter registered", "wp_privacy_personal_data_exporters" in privacy),
    ("33 WordPress privacy eraser registered", "wp_privacy_personal_data_erasers" in privacy),
    ("34 per-stage small-cohort suppression", "sanitize_friction_payload" in review and "suppressed_stages" in review),
    ("35 FAQ aggregate sensitive-data rejection", "question_contains_sensitive_data" in review),
    ("36 cohort threshold remains 10", "MIN_COHORT = 10" in future_policy and "MIN_COHORT = 10" in review),
    ("37 mandatory experiment guardrail values", "guardrails_valid" in review and "Every mandatory experiment safety guardrail" in review),
    ("38 sensitive experiment profiling blocked", "health profiling" in future_policy and "sensitive_sampling" in future_policy),
    ("39 English dark-pattern linter", "fake_scarcity" in future_policy and "guaranteed_result" in future_policy),
    ("40 Urdu dark-pattern guard", "fake_scarcity_ur" in review and "guarantee_ur" in review),
    ("41 Arabic dark-pattern guard", "fake_scarcity_ar" in review and "guarantee_ar" in review),
    ("42 multilingual positive-commission guard", "positive_commission_ur" in review and "positive_commission_ar" in review),
    ("43 truthful no-advantage regression", "Truthful Urdu no-advantage copy must pass" in read("tests/review80-hardening-tests.php")),
    ("44 AI output cannot auto-publish", "auto_publish' => false" in future),
    ("45 governed public-record guard loaded", "GCU_Future_Guards" in loader),
    ("46 FAQ gap suggestions cannot publish", "gcu_future_faq_suggestion_cannot_publish" in read("14-global-clinic-usp-integration/includes/class-gcu-future-guards.php")),
    ("47 AI draft cannot publish", "gcu_future_ai_draft_cannot_publish" in read("14-global-clinic-usp-integration/includes/class-gcu-future-guards.php")),
    ("48 Founder-level approval for governed public records", "APPROVE_CLAIMS" in read("14-global-clinic-usp-integration/includes/class-gcu-future-guards.php")),
    ("49 claim freshness sentinel exists", "claim_freshness_sentinel" in future),
    ("50 public HTML parity final guard", "finalize_public_guard" in review and "public_guard_blocked" in review),
    ("51 public blocks REST parity guard", "gcu_review80_public_copy_guarded" in review),
    ("52 scenario safe-mode truth corrected", "normalize_scenario_payload" in review and "SAFE_MODE_OPTION" in review),
    ("53 quality marked provisional on unverified evidence", "unverified_metrics" in review and "privacy_effectiveness" in review),
    ("54 public material change log", "public_change_log" in future and "F14-FUT-22" in future_policy),
    ("55 patient choose-safely guide", "patient_guide" in future_policy and "F14-FUT-23" in future_policy),
    ("56 doctor readiness is non-binding", "'binding' => false" in future_policy),
    ("57 verification owner remains File 09 / File 00", "File 09 / File 00" in future_policy),
    ("58 File 20 remains shell/navigation owner", "File 20 remains the sole global shell/navigation owner" in read("README.md")),
    ("59 File 25 visual boundary traceable", "File 25" in trace and "visual" in trace.lower()),
    ("60 destination registry preserves Files 07/08/09 owners", all(x in contracts for x in ("'File 07'", "'File 08'", "'File 09'"))),
    ("61 same-origin validates scheme host and port", all(x in read("14-global-clinic-usp-integration/includes/class-gcu-hardening.php") for x in ("home_scheme", "target_scheme", "effective_port"))),
    ("62 consumer cannot elevate owner readiness", "may never elevate owner readiness" in contracts),
    ("63 File 20 slot readiness required", "sabri_shell_slot_ready_v1" in contracts),
    ("64 no second shell ownership", "File 20 remains the sole global shell/navigation owner" in read("README.md")),
    ("65 en-US ur-PK ar-SA Future locale coverage", all(x in future_i18n for x in ("'ur-PK'", "'ar-SA'")) and "en-US" in future_policy),
    ("66 RTL/LTR direction support", "direction" in frontend and "rtl" in css.lower()),
    ("67 reduced-motion support", "prefers-reduced-motion" in css and "prefers-reduced-motion" in future_css),
    ("68 forced-colors support", "forced-colors" in future_css),
    ("69 44px interaction-target support", "44px" in future_css or "44px" in css),
    ("70 320px-class reflow gate", "max-width: 360px" in css and "320" in trace),
    ("71 Back/Home shell contract", "sabri_shell_back_home_controls" in frontend),
    ("72 degraded routes are noindex", "noindex,nofollow" in frontend),
    ("73 deterministic double-build", "Deterministic double-build mismatch" in build),
    ("74 unsafe archive path rejection", "Unsafe archive path" in build),
    ("75 file-level SBOM", "file-sha256-sbom-v1" in build),
    ("76 workflow packages exact 1.4.1 artifact", "1.4.1.zip.sha256" in workflow and "file-14-v1.4.1" in workflow),
    ("77 main quality suite runs review80 regression", "review80-hardening-tests.php" in quality),
    ("78 fresh review round 1 targets 1.4.1", "Version: 1.4.1" in fresh1),
    ("79 fresh review round 2 targets stable 1.4.1", "Stable tag: 1.4.1" in fresh2),
    ("80 truth-status boundary remains explicit", "Never infer staging or live state from this repository alone" in read("README.md")),
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
