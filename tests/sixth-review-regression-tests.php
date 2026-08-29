<?php

$root=dirname(__DIR__);$p=$root.'/14-global-clinic-usp-integration';$f=array();
function s6($ok,$msg){global$f;if(!$ok){$f[]=$msg;}}
$main=file_get_contents($p.'/global-clinic-usp-integration.php');
$install=file_get_contents($p.'/includes/class-gcu-install.php');
$repo=file_get_contents($p.'/includes/class-gcu-repository.php');
$integrity=file_get_contents($p.'/includes/class-gcu-integrity.php');
$privacy=file_get_contents($p.'/includes/class-gcu-privacy.php');
$contracts=file_get_contents($p.'/includes/class-gcu-contracts.php');
$future=file_get_contents($p.'/includes/class-gcu-future-intelligence.php');
$fifth=file_get_contents($p.'/includes/class-gcu-fifth-review-hardening.php');
$admin=file_get_contents($p.'/includes/class-gcu-admin.php');
$obs=file_get_contents($p.'/includes/class-gcu-observability.php');
$hard=file_get_contents($p.'/includes/class-gcu-hardening.php');

s6(false!==strpos($main,"GCU_SCHEMA_VERSION', 10006"),'Sixth-review schema identity drift.');
s6(false!==strpos($repo,'placement_ready($row)'),'Active placement request-time File20 fail-close missing.');
s6(false!==strpos($integrity,'AUDIT_KEY_OPTION')&&false!==strpos($integrity,'PRIVACY_KEY_OPTION')&&false!==strpos($integrity,'migrate_legacy_hashes'),'Stable integrity/privacy key migration missing.');
s6(false!==strpos($obs,'audit_chain_integrity_failed'),'Invalid audit-chain containment missing.');
s6(false!==strpos($admin,'ensure_schema(true)'),'Safe-mode exit does not force Future schema verification.');
s6(false!==strpos($repo,'begin_owned_transaction')&&false!==strpos($repo,'commit_owned_transaction')&&false!==strpos($repo,'transaction_audit_lock'),'Shared owner/audit transaction manager missing.');
s6(false!==strpos($repo,'request_hash')&&false!==strpos($repo,'gcu_idempotency_payload_conflict')&&false!==strpos($hard,'request_fingerprint'),'Payload-bound idempotency missing.');
s6(false!==strpos($install,'IS_USED_LOCK')&&false!==strpos($install,'stale_install_lock_recovered'),'Stale install-lock recovery missing.');
s6(false!==strpos($repo,'delete_batches')&&false!==strpos($repo,'lifecycle_cleanup_backlog'),'Base bounded-draining retention cleanup missing.');
s6(false!==strpos($privacy,'GCU_Integrity::user_subject_hash')&&false!==strpos($privacy,'GCU_Integrity::future_actor_hash'),'Stable privacy export/erase identities missing.');
s6(false!==strpos($contracts,'persist_option_exact')&&false!==strpos($contracts,'gcu_owner_state_persist_failed'),'Inbound owner-state persistence verification missing.');
s6(false!==strpos($integrity,'gcu_integrity_migration_state_failed')&&false!==strpos(str_replace(' ','',$integrity),'1!==$changed'),'Integrity migration does not fail closed on write/marker failure.');
$cap=substr($install,strpos($install,'capture_snapshot'),strpos($install,'safe_error_record')-strpos($install,'capture_snapshot'));
s6(false===strpos($cap,'GCU_Integrity::AUDIT_KEY_OPTION')&&false===strpos($cap,'GCU_Integrity::PRIVACY_KEY_OPTION'),'Forward-only integrity keys are incorrectly rollback-restored.');
s6(false!==strpos($repo,'gcu_rate_limit_read_failed'),'Rate-limit read failure is not fail closed.');
s6(false!==strpos($repo,'gcu_event_transaction_failed')&&false!==strpos($repo,'gcu_event_outbox_failed'),'Conversion event/outbox atomicity missing.');
s6(false!==strpos($future,'gcu_future_report_governance_failed')&&false!==strpos($future,'GCU_Integrity::future_actor_hash'),'Future report creation governance/privacy hardening missing.');
s6(false!==strpos($future,"audit('copy_quality_report_updated'")&&false!==strpos($future,'gcu_future_report_update_failed'),'Future report resolution state/audit atomicity missing.');
s6(false!==strpos($future,"audit('future_record_updated'")&&false!==strpos($future,"audit('future_record_created'")&&false!==strpos($future,'gcu_future_record_transaction_failed'),'Future record state/audit atomicity missing.');
s6(false!==strpos($future,'GCU_Fifth_Review_Hardening::transactional_early_stop_guard'),'All Future early-stop paths are not transactional.');
s6(false!==strpos($future,'reports_page')&&false!==strpos($future,'records_page')&&false!==strpos($future,'next_cursor'),'Future query cursor pagination missing.');
s6(false!==strpos($future,'future_lifecycle_cleanup_backlog'),'Future bounded-draining retention cleanup missing.');
s6(false!==strpos($fifth,'begin_owned_transaction()')&&false!==strpos($fifth,'commit_owned_transaction()'),'Transactional experiment early stop missing.');

if($f){fwrite(STDERR,"Sixth review regression tests failed:\n- ".implode("\n- ",$f)."\n");exit(1);}echo "Sixth review regression tests: PASS\n";
