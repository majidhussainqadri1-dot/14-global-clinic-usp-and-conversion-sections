<?php
$root = dirname( __DIR__ );
$plugin = $root . '/14-global-clinic-usp-integration';
$failures = array();
function tenth_text( $path ) { return file_exists( $path ) ? file_get_contents( $path ) : ''; }
function tenth_assert( $condition, $message ) { global $failures; if ( ! $condition ) { $failures[] = $message; } }
$main=tenth_text($plugin.'/global-clinic-usp-integration.php');
$rest=tenth_text($plugin.'/includes/class-gcu-rest.php');
$privacy=tenth_text($plugin.'/includes/class-gcu-privacy.php');
$repo=tenth_text($plugin.'/includes/class-gcu-repository.php');
$future=tenth_text($plugin.'/includes/class-gcu-future-intelligence.php');
$install=tenth_text($plugin.'/includes/class-gcu-install.php');
$integrity=tenth_text($plugin.'/includes/class-gcu-integrity.php');
$front=tenth_text($plugin.'/includes/class-gcu-frontend.php');
$obs=tenth_text($plugin.'/includes/class-gcu-observability.php');
$uninstall=tenth_text($plugin.'/uninstall.php');
$status=tenth_text($root.'/STATUS.md');
$release=tenth_text($root.'/docs/RELEASE-EVIDENCE.md');
$version=''; if(preg_match('/Version:\s*([0-9]+\.[0-9]+\.[0-9]+)/',$main,$m)){$version=$m[1];}
tenth_assert(''!==$version && version_compare($version,'1.4.8','>=') && false!==strpos($main,"GCU_VERSION', '".$version."'"),'Tenth-cycle candidate must be v1.4.8 or later with matching GCU_VERSION.');
tenth_assert(false!==strpos($main,"GCU_SCHEMA_VERSION', 10005")&&false!==strpos($main,"GCU_FUTURE_SCHEMA_VERSION', 1"),'Tenth cycle must not invent a schema change.');
foreach(array('gcu_event_identity_query_failed') as$m){tenth_assert(false!==strpos($rest,$m),'REST DB fail-close missing: '.$m);}
foreach(array('privacy_export_event_query_failed','privacy_export_report_query_failed','legal_hold_applies','gcu_privacy_legal_hold','privacy_legal_hold_check_failed') as$m){tenth_assert(false!==strpos($privacy,$m),'Privacy lifecycle hardening missing: '.$m);}
foreach(array('gcu_event_token_store_failed','gcu_event_duplicate_query_failed','gcu_event_identity_conflict','content_superseded','gcu_supersede_governance_failed','gcu_content_version_read_failed','gcu_workflow_read_failed','gcu_placement_dependency_read_failed','gcu_claim_read_failed','gcu_claim_history_read_failed','gcu_command_read_failed','fail_command_claim','lifecycle_cleanup_failed','outbox_select_failed','inbox_select_failed') as$m){tenth_assert(false!==strpos($repo,$m),'Repository tenth-cycle hardening missing: '.$m);}
tenth_assert(false!==strpos($future,'future_record_write')&&false!==strpos($future,'future_record_publish'),'Owner-native Future publication authorization missing.');
foreach(array('gcu_snapshot_table_probe_failed','gcu_snapshot_count_failed','gcu_snapshot_read_failed','gcu_snapshot_persist_failed') as$m){tenth_assert(false!==strpos($install,$m),'Rollback snapshot fail-close missing: '.$m);}
foreach(array('audit_read_failed','audit_anchor_invalid','audit_payload_encode_failed') as$m){tenth_assert(false!==strpos($repo,$m),'Audit-chain fail-close missing: '.$m);}
foreach(array('gcu_integrity_audit_probe_failed','gcu_integrity_audit_count_failed','gcu_integrity_privacy_probe_failed') as$m){tenth_assert(false!==strpos($integrity,$m),'Integrity migration fail-close missing: '.$m);}
tenth_assert(false!==strpos($future,'future_lifecycle_cleanup_failed')&&false!==strpos($future,'legal_hold_applies'),'Future retention/legal-hold protection missing.');
tenth_assert(false!==strpos($front,"\$content_locale='en-US'")&&false!==strpos($front,'GCU_I18n::language($content_locale)')&&false!==strpos($front,'GCU_I18n::direction($content_locale)'),'Fallback content language semantics missing.');
tenth_assert(false!==strpos($obs,"'query_errors'")&&false!==strpos($obs,'query_errors'),'Health query-error truth missing.');
foreach(array('gcu_purge_approval_v1','backup_verified_at','restore_verified_at','gcu_purge_receipt_v1','gcu_destructive_purge_authorized_v1','gcu_destructive_purge_completed_v1') as$m){tenth_assert(false!==strpos($uninstall,$m),'Guarded destructive purge evidence missing: '.$m);}
tenth_assert(false!==strpos($status,'Tenth Twenty-Round Repository Candidate')&&false!==strpos($release,'Tenth Twenty-Round Repository Candidate'),'Current evidence must identify the tenth twenty-round cycle.');
tenth_assert(false!==strpos($status,'Exact deployed code is unverified')&&false!==strpos($release,'Exact deployed code is unverified'),'Live-First truth boundary must remain explicit.');
if($failures){fwrite(STDERR,"Tenth review regression tests failed:\n- ".implode("\n- ",$failures)."\n");exit(1);} echo "Tenth review regression tests: PASS\n";
