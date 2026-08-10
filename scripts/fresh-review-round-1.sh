#!/usr/bin/env bash
set -euo pipefail
ROOT="$(cd "$(dirname "${BASH_SOURCE[0]}")/.." && pwd)"
P="$ROOT/14-global-clinic-usp-integration"
fail=0
need(){ grep -RqsF "$1" "$2" || { echo "ROUND1 missing: $1 in $2" >&2; fail=1; }; }
forbid(){ if grep -RqsE "$1" "$2"; then echo "ROUND1 forbidden pattern: $1 in $2" >&2; fail=1; fi; }
need "Version: 1.3.1" "$P/global-clinic-usp-integration.php"
need "GCU_SCHEMA_VERSION', 10004" "$P/global-clinic-usp-integration.php"
need "ENGINE=InnoDB" "$P/includes/class-gcu-install.php"
need "SHOW TABLE STATUS" "$P/includes/class-gcu-install.php"
need "SELECT GET_LOCK" "$P/includes/class-gcu-install.php"
need "snapshot_hash" "$P/includes/class-gcu-install.php"
need "START TRANSACTION" "$P/includes/class-gcu-install.php"
need "run_idempotent_command" "$P/includes/class-gcu-repository.php"
need "consumed_at IS NULL" "$P/includes/class-gcu-repository.php"
need "ON DUPLICATE KEY UPDATE counter=counter+1" "$P/includes/class-gcu-repository.php"
need "'audit-chain'" "$P/includes/class-gcu-repository.php"
need "process_inbox" "$P/includes/class-gcu-repository.php"
need "dispatch_outbox" "$P/includes/class-gcu-repository.php"
need "DATE_SUB(UTC_TIMESTAMP(),INTERVAL 10 MINUTE)" "$P/includes/class-gcu-repository.php"
need "gcu_destination_state_" "$P/includes/class-gcu-contracts.php"
need "may never elevate" "$P/includes/class-gcu-contracts.php"
need "sabri_shell_slot_ready_v1" "$P/includes/class-gcu-contracts.php"
need "strict_same_origin_url" "$P/includes/class-gcu-hardening.php"
forbid "wp_insert_post[[:space:]]*\(" "$P"
forbid "GCU_Policy::same_origin_url" "$P"
forbid "data-gcu-event-token" "$P"
forbid "onclick=|onload=|onerror=|javascript:" "$P"
if [ "$fail" -ne 0 ]; then exit 1; fi
echo "Fresh Review Round 1: PASS — architecture, ownership, migration, concurrency, authorization, reliability, queues, audit and destination fail-closed controls"
