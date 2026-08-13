<?php
$root=dirname(__DIR__);$p=$root.'/14-global-clinic-usp-integration';$f=array();
function c($x,$m){global$f;if(!$x){$f[]=$m;}} function t($x){return file_exists($x)?file_get_contents($x):'';}
$main=t($p.'/global-clinic-usp-integration.php');$front=t($p.'/includes/class-gcu-frontend.php');$priv=t($p.'/includes/class-gcu-privacy.php');$hard=t($p.'/includes/class-gcu-hardening.php');$fifth=t($p.'/includes/class-gcu-fifth-review-hardening.php');$con=t($p.'/includes/class-gcu-contracts.php');$repo=t($p.'/includes/class-gcu-repository.php');$install=t($p.'/includes/class-gcu-install.php');$i18n=t($p.'/includes/class-gcu-i18n.php');$fi18n=t($p.'/includes/class-gcu-future-i18n.php');$future=t($p.'/includes/class-gcu-future-intelligence.php');$futurePolicy=t($p.'/includes/class-gcu-future-policy.php');$review=t($p.'/includes/class-gcu-review80-hardening.php');$css=t($p.'/assets/css/global-clinic-usp-integration.css');$futureCss=t($p.'/assets/css/gcu-future-intelligence.css');$trace=t($root.'/docs/REQUIREMENTS-TRACEABILITY.md');$read=t($p.'/readme.txt');$build=t($root.'/scripts/build.py');
$version='';if(preg_match('/Version:\s*([0-9]+\.[0-9]+\.[0-9]+)/',$main,$m)){$version=$m[1];}
c(''!==$version&&version_compare($version,'1.4.8','>=')&&false!==strpos($main,"GCU_VERSION', '".$version."'"),'Candidate version must be >=1.4.8 and match GCU_VERSION.');
c(false!==strpos($read,'Stable tag: '.$version),'Readme stable tag must match current plugin version.');
c(false!==strpos($main,"GCU_SCHEMA_VERSION', 10005")&&false!==strpos($main,"GCU_FUTURE_SCHEMA_VERSION', 1"),'Schema identity drift.');
foreach(array("GCU_CENTRAL_PLAN_BASELINE', '2026-08-10","GCU_PLAN_VERSION', 'SSH-F14-PLAN-2026-v1.0","GCU_FUTURE_PLAN_VERSION', 'SSH-F14-FUTURE-CTI-2026-v2.0","GCU_CANONICAL_REPOSITORY', '14-global-clinic-usp-and-conversion-integration","GCU_CURRENT_REPOSITORY_ALIAS', '14-global-clinic-usp-and-conversion-sections") as$n){c(false!==strpos($main,$n),'Main contract missing: '.$n);}
c(false!==strpos($css,'--gcu-brand-primary: #087A4E')&&false!==strpos($futureCss,'#087A4E'),'Sabri Green token drift.');
c(false!==strpos($front,'sabri_shell_back_home_controls')&&false===strpos($front,'data-gcu-shell-fallback'),'File 20 shell ownership drift.');
foreach(array('onclick=','onload=','onerror=','javascript:','<script') as$n){c(false===stripos($front,$n),'Inline executable markup present: '.$n);} c(false===strpos($front,'<main class="gcu-page"'),'Duplicate main landmark present.');
c(false!==strpos($con,"'File 07'")&&false!==strpos($con,"'File 08'")&&false!==strpos($con,"'File 09'"),'Destination owner contracts missing.');
c(false!==strpos($con,'may never elevate')&&false!==strpos($con,'sabri_shell_slot_ready_v1')&&false===strpos($con,'gcu_file20_slot_ready_v1'),'Owner/File20 readiness boundary drift.');
c(false!==strpos($con,'owner_occurred_at')&&false!==strpos($con,'gcu_owner_event_order_ambiguous'),'Owner event ordering truth missing.');
c(false!==strpos($hard,'strict_same_origin_url')&&false!==strpos($hard,'effective_port')&&false!==strpos($hard,'request_fingerprint')&&false!==strpos($hard,'1048576'),'Hardening contracts incomplete.');
c(false!==strpos($priv,'is_file14_acquisition_route')&&false!==strpos($priv,'global_privacy_control_requested')&&false!==strpos($priv,'low_bandwidth_requested'),'Privacy/low-data boundary incomplete.');
c(false!==strpos($priv,'USER_SUBJECT_META')&&false!==strpos($priv,'GUEST_SUBJECT_TTL')&&false!==strpos($priv,'legal_hold_applies'),'Pseudonym/retention/legal-hold lifecycle incomplete.');
c(false!==strpos($i18n,"'en-US'")&&false!==strpos($i18n,"'ur-PK'")&&false!==strpos($i18n,"'ar-SA'")&&false!==strpos($i18n,'missing_keys'),'Base localization parity incomplete.');
c(false!==strpos($fi18n,"'ur-PK'")&&false!==strpos($fi18n,"'ar-SA'"),'Future localization incomplete.');
c(false!==strpos($css,'prefers-reduced-motion')&&false!==strpos($css,'prefers-reduced-data')&&false!==strpos($css,'forced-colors')&&false!==strpos($css,'max-width: 360px'),'Accessibility/low-data CSS incomplete.');
c(false!==strpos($install,'ENGINE=InnoDB')&&false!==strpos($install,'verify_schema')&&false!==strpos($install,'gcu_snapshot_persist_failed'),'Migration/snapshot reliability gate missing.');
c(false!==strpos($repo,'run_idempotent_command')&&false!==strpos($repo,'process_inbox')&&false!==strpos($repo,'verify_audit_chain'),'Core reliability controls incomplete.');
foreach(array('validate_event_token','gcu_event_subject_unavailable','gcu_content_readback_failed','gcu_event_duplicate_query_failed','gcu_workflow_read_failed','legal_hold_applies') as$n){c(false!==strpos($repo,$n),'Repository hardening missing: '.$n);}
c(false!==strpos($future,'gcu_future_records')&&false!==strpos($future,'gcu_future_reports')&&false!==strpos($future,'claim_freshness_sentinel')&&false!==strpos($future,'early_stop_guard'),'Future CTI implementation incomplete.');
c(false!==strpos($future,'gcu_future_quality_query_failed')&&false!==strpos($future,'gcu_future_friction_query_failed')&&false!==strpos($future,'future_record_publish'),'Future DB/auth fail-close missing.');
c(false!==strpos($futurePolicy,'F14-FUT-01')&&false!==strpos($futurePolicy,'F14-FUT-24')&&false!==strpos($futurePolicy,'MIN_COHORT = 10'),'Future CTI policy catalog incomplete.');
c(false!==strpos($review,'guardrails_valid')&&false!==strpos($review,'sanitize_friction_payload')&&false!==strpos($review,'question_contains_sensitive_data'),'Review80 contracts incomplete.');
c(false!==strpos($fifth,'GCU_Install::verify_schema()')&&false!==strpos($fifth,'GCU_Future_Intelligence::verify_schema()')&&false!==strpos($fifth,'transactional_early_stop_guard'),'Retained fifth-review hardening incomplete.');
c(false!==strpos($front,'private, no-store')&&false!==strpos($front,'Vary: Accept-Language, Cookie'),'Personalized cache boundary missing.');
c(false!==strpos($build,'Deterministic double-build mismatch')&&false!==strpos($build,'sbom')&&false!==strpos($build,'Unsafe archive path'),'Deterministic packaging incomplete.');
foreach(array('CEN-GOV-001','CEN-OWN-001','CEN-BIZ-001','CEN-DON-001','CEN-BRAND-001','CEN-NAV-001','CEN-LOC-001','CEN-A11Y-001','CEN-LOWDATA-001','CEN-PRIV-001','F14-FR-001','F14-FR-016','F14-NFR-010','F14-FUT-01','F14-FUT-24','DoD-11','DoD-13')as$id){c(false!==strpos($trace,$id),'Traceability missing '.$id);}
if($f){fwrite(STDERR,"Central-plan tests failed:\n- ".implode("\n- ",$f)."\n");exit(1);} echo"Central-plan tests: PASS\n";
