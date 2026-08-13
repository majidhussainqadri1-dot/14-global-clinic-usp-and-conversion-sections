#!/usr/bin/env bash
set -euo pipefail
ROOT="$(cd "$(dirname "${BASH_SOURCE[0]}")/.." && pwd)"; P="$ROOT/14-global-clinic-usp-integration"; fail=0
need(){ grep -RqsF -- "$1" "$2" || { echo "ROUND1 missing: $1 in $2" >&2; fail=1; }; }
forbid(){ if grep -RqsE -- "$1" "$2"; then echo "ROUND1 forbidden pattern: $1 in $2" >&2; fail=1; fi; }
version="$(sed -nE 's/^\s*\*\s*Version:\s*([0-9]+\.[0-9]+\.[0-9]+)\s*$/\1/p' "$P/global-clinic-usp-integration.php" | head -1)"
[ -n "$version" ] || { echo 'ROUND1 version missing' >&2; exit 1; }
php -r 'exit(version_compare($argv[1],"1.4.8",">=")?0:1);' "$version" || { echo "ROUND1 candidate must be >=1.4.8" >&2; exit 1; }
need "GCU_VERSION', '$version'" "$P/global-clinic-usp-integration.php"; need "Stable tag: $version" "$P/readme.txt"
for x in "GCU_SCHEMA_VERSION', 10005" "SSH-F14-FUTURE-CTI-2026-v2.0" "GCU_FUTURE_SCHEMA_VERSION', 1" GCU_Review80_Hardening GCU_Fifth_Review_Hardening GCU_CURRENT_REPOSITORY_ALIAS; do need "$x" "$P/global-clinic-usp-integration.php"; done
need "apply_filters( 'gcu_authorize', false" "$P/includes/class-gcu-capabilities.php"; need "true === apply_filters" "$P/includes/class-gcu-capabilities.php"
for x in validate_event_token consume_event_token gcu_event_subject_unavailable gcu_event_duplicate_query_failed gcu_workflow_read_failed fail_command_claim legal_hold_applies outbox_select_failed inbox_select_failed; do need "$x" "$P/includes/class-gcu-repository.php"; done
for x in gcu_snapshot_table_probe_failed gcu_snapshot_count_failed gcu_snapshot_read_failed gcu_snapshot_persist_failed; do need "$x" "$P/includes/class-gcu-install.php"; done
for x in gcu_integrity_audit_probe_failed gcu_integrity_audit_count_failed gcu_integrity_privacy_probe_failed; do need "$x" "$P/includes/class-gcu-integrity.php"; done
need "gcu_event_identity_query_failed" "$P/includes/class-gcu-rest.php"; need "gcu_privacy_legal_hold" "$P/includes/class-gcu-privacy.php"; need "future_lifecycle_cleanup_failed" "$P/includes/class-gcu-future-intelligence.php"; need "query_errors" "$P/includes/class-gcu-observability.php"
need "sabri_shell_slot_ready_v1" "$P/includes/class-gcu-contracts.php"; forbid "gcu_file20_slot_ready_v1" "$P/includes/class-gcu-contracts.php"; need "strict_same_origin_url" "$P/includes/class-gcu-hardening.php"
forbid "wp_insert_post[[:space:]]*\(" "$P"; forbid "GCU_Policy::same_origin_url" "$P"; forbid "data-gcu-event-token" "$P"; forbid "onclick=|onload=|onerror=|javascript:" "$P"
[ "$fail" -eq 0 ] || exit 1
echo "Fresh Review Round 1: PASS — v$version architecture, authorization, schema, transactional/replay integrity, DB fail-close, privacy/legal-hold, queues/audit and owner boundaries"
