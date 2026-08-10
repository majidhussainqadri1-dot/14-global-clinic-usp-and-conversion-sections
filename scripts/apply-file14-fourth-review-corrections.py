#!/usr/bin/env python3
from pathlib import Path
import re

ROOT = Path(__file__).resolve().parents[1]
P = ROOT / '14-global-clinic-usp-integration'

def read(rel):
    return (ROOT / rel).read_text(encoding='utf-8')

def write(rel, text):
    (ROOT / rel).write_text(text, encoding='utf-8')

def replace_once(text, old, new, label):
    if old not in text:
        raise SystemExit(f'{label}: source signature not found')
    if text.count(old) != 1:
        raise SystemExit(f'{label}: source signature is not unique ({text.count(old)})')
    return text.replace(old, new, 1)

# 1) Version identity: source changes require a new software patch version.
loader_path='14-global-clinic-usp-integration/global-clinic-usp-integration.php'
loader=read(loader_path)
loader=replace_once(loader,' * Version: 1.4.1',' * Version: 1.4.2','version header')
loader=replace_once(loader,"define( 'GCU_VERSION', '1.4.1' );","define( 'GCU_VERSION', '1.4.2' );",'version constant')
write(loader_path,loader)

# 2) File 00 authorization truth must be present for every privileged capability check.
caps_path='14-global-clinic-usp-integration/includes/class-gcu-capabilities.php'
caps=read(caps_path)
old="""\tpublic static function can( $capability, $object = null, $purpose = '' ) {\n\t\t$allowed = current_user_can( $capability );\n\t\tif ( ! $allowed ) { return false; }\n\t\treturn (bool) apply_filters( 'gcu_authorize', true, $capability, $object, sanitize_key( $purpose ) );\n\t}\n"""
new="""\tpublic static function authorization_adapter_available() {\n\t\treturn function_exists( 'has_filter' ) && false !== has_filter( 'gcu_authorize' );\n\t}\n\n\tpublic static function can( $capability, $object = null, $purpose = '' ) {\n\t\t$allowed = current_user_can( $capability );\n\t\tif ( ! $allowed ) { return false; }\n\t\t// File 00 owns versioned authorization truth. Native WordPress capabilities are necessary, never sufficient.\n\t\tif ( ! self::authorization_adapter_available() ) { return false; }\n\t\treturn (bool) apply_filters( 'gcu_authorize', true, $capability, $object, sanitize_key( $purpose ) );\n\t}\n"""
caps=replace_once(caps,old,new,'File00 authorization adapter fail-close')
write(caps_path,caps)

# 3) Campaign attribution must never retain direct identifiers/clinical details accidentally embedded in UTM/ref values.
policy_path='14-global-clinic-usp-integration/includes/class-gcu-policy.php'
policy=read(policy_path)
old="""\tpublic static function sanitize_campaign( array $input ) {\n\t\t$out = array();\n\t\tforeach ( array( 'source', 'medium', 'campaign', 'ref' ) as $key ) {\n\t\t\t$value = isset( $input[ $key ] ) ? sanitize_text_field( wp_unslash( $input[ $key ] ) ) : '';\n\t\t\t$out[ $key ] = function_exists( 'mb_substr' ) ? mb_substr( $value, 0, 100 ) : substr( $value, 0, 100 );\n\t\t}\n\t\treturn $out;\n\t}\n"""
new="""\tpublic static function sanitize_campaign( array $input ) {\n\t\t$out = array();\n\t\tforeach ( array( 'source', 'medium', 'campaign', 'ref' ) as $key ) {\n\t\t\t$value = isset( $input[ $key ] ) ? sanitize_text_field( wp_unslash( $input[ $key ] ) ) : '';\n\t\t\t$value = function_exists( 'mb_substr' ) ? mb_substr( $value, 0, 100 ) : substr( $value, 0, 100 );\n\t\t\t$out[ $key ] = self::campaign_value_is_sensitive( $value ) ? '' : $value;\n\t\t}\n\t\treturn $out;\n\t}\n\n\tpublic static function campaign_value_is_sensitive( $value ) {\n\t\t$value = trim( (string) $value );\n\t\tif ( '' === $value ) { return false; }\n\t\tif ( preg_match( '/[A-Z0-9._%+\\-]+@[A-Z0-9.\\-]+\\.[A-Z]{2,}/i', $value ) ) { return true; }\n\t\tif ( preg_match( '/(?:\\+?\\d[\\s().\\-]*){7,}/', $value ) ) { return true; }\n\t\t$lower = function_exists( 'mb_strtolower' ) ? mb_strtolower( $value ) : strtolower( $value );\n\t\t$markers = array( 'cnic','passport','patient id','medical record','diagnosis','symptom','prescription','email','phone','mobile','شناختی','شناختی کارڈ','پاسپورٹ','مریض','تشخیص','علامت','نسخ','ای میل','فون','موبائل','هوية','جواز','مريض','تشخيص','عرض','وصفة','بريد','هاتف','جوال' );\n\t\tforeach ( $markers as $marker ) { if ( false !== strpos( $lower, $marker ) ) { return true; } }\n\t\treturn false;\n\t}\n"""
policy=replace_once(policy,old,new,'campaign minimization')
write(policy_path,policy)

# 4-10) Repository correctness: audience isolation, Founder placement approval, per-stage privacy, event identity, recent audit verification, replay conflict detection.
repo_path='14-global-clinic-usp-integration/includes/class-gcu-repository.php'
repo=read(repo_path)
old="if('all'!==$audience){$w.=\" AND (p.audience=%s OR p.audience='all')\";$a[]=$audience;}"
new="if('all'!==$audience){$w.=\" AND (p.audience=%s OR p.audience='all') AND (b.audience=%s OR b.audience='all')\";$a[]=$audience;$a[]=$audience;}"
repo=replace_once(repo,old,new,'block audience isolation')

old="if('copy'===$machine&&in_array($target,array('founder_approved','active','withdrawn'),true)||'experiment'===$machine&&in_array($target,array('approved','running','adopted'),true))"
new="if(('copy'===$machine&&in_array($target,array('founder_approved','active','withdrawn'),true))||('placement'===$machine&&'active'===$target)||('experiment'===$machine&&in_array($target,array('approved','running','adopted'),true)))"
repo=replace_once(repo,old,new,'Founder placement approval')

old="if('placement'===$machine&&'active'===$target){$b=$wpdb->get_row($wpdb->prepare(\"SELECT id,slot_key FROM {$t['blocks']} WHERE block_key=%s AND status='active' ORDER BY content_version DESC LIMIT 1\",$row['block_key']),ARRAY_A);if(!$b||$b['slot_key']!==$row['slot_key']||!GCU_Plugin::instance()->contracts()->placement_ready($row)){return new WP_Error('gcu_placement_unready',__('The active block or File 20 slot is not ready.','global-clinic-usp-integration'),array('status'=>409));}}"
new="if('placement'===$machine&&'active'===$target){$b=$wpdb->get_row($wpdb->prepare(\"SELECT id,slot_key,audience FROM {$t['blocks']} WHERE block_key=%s AND status='active' ORDER BY content_version DESC LIMIT 1\",$row['block_key']),ARRAY_A);$block_audience=$b?GCU_Policy::sanitize_audience($b['audience']):'';$placement_audience=GCU_Policy::sanitize_audience(isset($row['audience'])?$row['audience']:'all');$audience_ok=$b&&('all'===$block_audience||$block_audience===$placement_audience);if(!$b||$b['slot_key']!==$row['slot_key']||!$audience_ok||!GCU_Plugin::instance()->contracts()->placement_ready($row)){return new WP_Error('gcu_placement_unready',__('The active block, audience contract or File 20 slot is not ready.','global-clinic-usp-integration'),array('status'=>409));}}"
repo=replace_once(repo,old,new,'placement/block audience contract')

old="if('cta_selected'===$stage&&1===$ins){$this->publish_event('ClinicUSPCTASelected.v1',array('event_id'=>$id,'destination'=>$dest));}"
new="if('cta_selected'===$stage&&1===$ins){$this->publish_event('ClinicUSPCTASelected.v1',array('source_event_id'=>$id,'destination'=>$dest));}"
repo=replace_once(repo,old,new,'CTA source event correlation')

old="public function funnel_summary($days=30){$a=GCU_Capabilities::require_capability(GCU_Capabilities::VIEW_ANALYTICS,null,'view_funnel_summary');if(is_wp_error($a)){return$a;}global$wpdb;$days=max(1,min(self::EVENT_RETENTION_DAYS,absint($days)));$t=GCU_Install::tables();$rows=$wpdb->get_results($wpdb->prepare(\"SELECT funnel_stage,COUNT(*) AS total FROM {$t['events']} WHERE occurred_at>=DATE_SUB(UTC_TIMESTAMP(),INTERVAL %d DAY) GROUP BY funnel_stage ORDER BY funnel_stage\",$days),ARRAY_A);$total=array_sum(array_map(static function($r){return(int)$r['total'];},is_array($rows)?$rows:array()));return$total<10?array('suppressed'=>true,'threshold'=>10,'days'=>$days,'stages'=>array()):array('suppressed'=>false,'threshold'=>10,'days'=>$days,'stages'=>$rows);}"
new="public function funnel_summary($days=30){$a=GCU_Capabilities::require_capability(GCU_Capabilities::VIEW_ANALYTICS,null,'view_funnel_summary');if(is_wp_error($a)){return$a;}global$wpdb;$days=max(1,min(self::EVENT_RETENTION_DAYS,absint($days)));$t=GCU_Install::tables();$rows=$wpdb->get_results($wpdb->prepare(\"SELECT funnel_stage,COUNT(*) AS total FROM {$t['events']} WHERE occurred_at>=DATE_SUB(UTC_TIMESTAMP(),INTERVAL %d DAY) GROUP BY funnel_stage ORDER BY funnel_stage\",$days),ARRAY_A);$total=array_sum(array_map(static function($r){return(int)$r['total'];},is_array($rows)?$rows:array()));if($total<10){return array('suppressed'=>true,'threshold'=>10,'days'=>$days,'stages'=>array(),'suppressed_stages'=>array());}$safe=array();$suppressed=array();foreach(is_array($rows)?$rows:array()as$row){$n=(int)$row['total'];if($n<10){$suppressed[]=sanitize_key($row['funnel_stage']);$row['total']=null;$row['suppressed']=true;}else{$row['total']=$n;$row['suppressed']=false;}$safe[]=$row;}return array('suppressed'=>false,'threshold'=>10,'days'=>$days,'stages'=>$safe,'suppressed_stages'=>$suppressed);}"
repo=replace_once(repo,old,new,'per-stage small cohort suppression')

old="public function accept_inbound_event($name,array$payload){$ready=GCU_Install::ready_for_mutation();if(is_wp_error($ready)){return false;}$v=$this->validate_inbound_event($name,$payload);if(is_wp_error($v)){return false;}global$wpdb;$t=GCU_Install::tables();$clean=GCU_Hardening::sanitize_structured_value($payload);$enc=wp_json_encode($clean);$ins=$wpdb->query($wpdb->prepare(\"INSERT IGNORE INTO {$t['inbox']} (event_id,event_name,payload_hash,payload,status,attempts,created_at) VALUES (%s,%s,%s,%s,'pending',0,%s)\",sanitize_text_field($payload['event_id']),sanitize_text_field($name),hash('sha256',$enc),$enc,current_time('mysql',true)));if(false===$ins){return false;}if(1===$ins){$this->process_inbox($payload['event_id'],1);}return true;}"
new="public function accept_inbound_event($name,array$payload){$ready=GCU_Install::ready_for_mutation();if(is_wp_error($ready)){return false;}$v=$this->validate_inbound_event($name,$payload);if(is_wp_error($v)){return false;}global$wpdb;$t=GCU_Install::tables();$clean=GCU_Hardening::sanitize_structured_value($payload);$enc=wp_json_encode($clean);if(false===$enc){return false;}$hash=hash('sha256',$enc);$event_id=sanitize_text_field($payload['event_id']);$event_name=sanitize_text_field($name);$ins=$wpdb->query($wpdb->prepare(\"INSERT IGNORE INTO {$t['inbox']} (event_id,event_name,payload_hash,payload,status,attempts,created_at) VALUES (%s,%s,%s,%s,'pending',0,%s)\",$event_id,$event_name,$hash,$enc,current_time('mysql',true)));if(false===$ins){return false;}if(0===$ins){$existing=$wpdb->get_row($wpdb->prepare(\"SELECT event_name,payload_hash FROM {$t['inbox']} WHERE event_id=%s\",$event_id),ARRAY_A);if(!$existing||!hash_equals((string)$existing['payload_hash'],$hash)||!hash_equals((string)$existing['event_name'],$event_name)){GCU_Observability::log('warning','inbound_event_identity_conflict',array('event_id'=>$event_id,'event_name'=>$event_name));return false;}}if(1===$ins){$this->process_inbox($event_id,1);}return true;}"
repo=replace_once(repo,old,new,'inbound replay identity conflict')

old="public function verify_audit_chain($limit=5000){global$wpdb;$t=GCU_Install::tables();$total=(int)$wpdb->get_var(\"SELECT COUNT(*) FROM {$t['audit']}\");$limit=max(1,min(10000,(int)$limit));$rows=$wpdb->get_results($wpdb->prepare(\"SELECT * FROM {$t['audit']} ORDER BY id ASC LIMIT %d\",$limit),ARRAY_A);$prev=str_repeat('0',64);foreach(is_array($rows)?$rows:array()as$row){if(!hash_equals($prev,(string)$row['previous_hash'])||!hash_equals((string)$row['row_hash'],$this->audit_row_hash($row))){return array('valid'=>false,'scope'=>'failed','checked'=>isset($row['id'])?(int)$row['id']:0,'total'=>$total);}$prev=(string)$row['row_hash'];}return array('valid'=>true,'scope'=>$total>$limit?'partial':'full','checked'=>min($total,$limit),'total'=>$total);}"
new="public function verify_audit_chain($limit=5000){global$wpdb;$t=GCU_Install::tables();$total=(int)$wpdb->get_var(\"SELECT COUNT(*) FROM {$t['audit']}\");$limit=max(1,min(10000,(int)$limit));$offset=max(0,$total-$limit);if(0===$offset){$rows=$wpdb->get_results($wpdb->prepare(\"SELECT * FROM {$t['audit']} ORDER BY id ASC LIMIT %d\",$limit),ARRAY_A);$prev=str_repeat('0',64);$scope='full';}else{$anchor=(string)$wpdb->get_var($wpdb->prepare(\"SELECT row_hash FROM {$t['audit']} ORDER BY id ASC LIMIT 1 OFFSET %d\",$offset-1));if(!preg_match('/^[a-f0-9]{64}$/',$anchor)){return array('valid'=>false,'scope'=>'anchor_missing','checked'=>0,'total'=>$total);}$rows=$wpdb->get_results($wpdb->prepare(\"SELECT * FROM {$t['audit']} ORDER BY id ASC LIMIT %d OFFSET %d\",$limit,$offset),ARRAY_A);$prev=$anchor;$scope='recent_tail';}$checked=0;foreach(is_array($rows)?$rows:array()as$row){$checked++;if(!hash_equals($prev,(string)$row['previous_hash'])||!hash_equals((string)$row['row_hash'],$this->audit_row_hash($row))){return array('valid'=>false,'scope'=>'failed','checked'=>$checked,'total'=>$total,'offset'=>$offset);}$prev=(string)$row['row_hash'];}return array('valid'=>true,'scope'=>$scope,'checked'=>$checked,'total'=>$total,'offset'=>$offset);}"
repo=replace_once(repo,old,new,'recent audit-chain verification')
write(repo_path,repo)

# 11) Governed shortcodes must inherit immediate no-cache semantics; File 20 remains sole navigation owner.
front_path='14-global-clinic-usp-integration/includes/class-gcu-frontend.php'
front=read(front_path)
old="public function cache_headers(){$r=sanitize_key((string)get_query_var('gcu_route'));if($r){nocache_headers();header('Cache-Control: public, no-cache, max-age=0, must-revalidate',true);header('Pragma: no-cache',true);}}"
new="public function cache_headers(){$r=sanitize_key((string)get_query_var('gcu_route'));$shortcode_page=false;if(!$r&&is_singular()){global$post;if($post instanceof WP_Post){$body=(string)$post->post_content;$shortcode_page=has_shortcode($body,'gcu_global_clinic')||has_shortcode($body,'gcu_how_it_works')||has_shortcode($body,'gcu_block');}}if($r||$shortcode_page){nocache_headers();header('Cache-Control: public, no-cache, max-age=0, must-revalidate',true);header('Pragma: no-cache',true);}}"
front=replace_once(front,old,new,'shortcode cache freshness')
old="private function navigation_controls($context,$l){$shared=apply_filters('sabri_shell_back_home_controls','',array('owner'=>'File 14','home_url'=>home_url('/'),'fallback_url'=>home_url('/global-clinic/'),'direction'=>GCU_I18n::direction($l)));if(is_string($shared)&&''!==trim($shared)){return$shared;}$back='global_clinic'===$context?home_url('/'):home_url('/global-clinic/');return'<nav class=\"gcu-context-nav\" data-gcu-shell-fallback=\"true\" aria-label=\"'.esc_attr(GCU_I18n::text('page_navigation',$l)).'\"><a href=\"'.esc_url($back).'\">'.$this->icon('back',true).'<span>'.esc_html(GCU_I18n::text('back',$l)).'</span></a><a href=\"'.esc_url(home_url('/')).'\" rel=\"home\">'.$this->icon('home').'<span>'.esc_html(GCU_I18n::text('home',$l)).'</span></a></nav>';}"
new="private function navigation_controls($context,$l){$shared=apply_filters('sabri_shell_back_home_controls','',array('owner'=>'File 14','home_url'=>home_url('/'),'fallback_url'=>home_url('/global-clinic/'),'direction'=>GCU_I18n::direction($l)));return is_string($shared)&&''!==trim($shared)?$shared:'';}"
front=replace_once(front,old,new,'File20 sole navigation ownership')
write(front_path,front)

# 12) Future schema failures must be visible to activation/upgrade truth, not silently discarded.
install_path='14-global-clinic-usp-integration/includes/class-gcu-install.php'
install=read(install_path)
old="public static function activate(){$r=self::install_or_upgrade(true);if(is_wp_error($r)){update_option(self::UPGRADE_ERROR,self::safe_error_record($r),false);update_option('gcu_enabled',0,false);wp_die(esc_html($r->get_error_message()));}self::ensure_future_schema(true);}"
new="public static function activate(){$r=self::install_or_upgrade(true);if(is_wp_error($r)){update_option(self::UPGRADE_ERROR,self::safe_error_record($r),false);update_option('gcu_enabled',0,false);wp_die(esc_html($r->get_error_message()));}$future=self::ensure_future_schema(true);if(is_wp_error($future)){update_option(self::UPGRADE_ERROR,self::safe_error_record($future),false);update_option('gcu_enabled',0,false);wp_die(esc_html($future->get_error_message()));}}"
install=replace_once(install,old,new,'activation Future schema propagation')
old="public static function maybe_upgrade(){if(GCU_VERSION===(string)get_option(self::VERSION_OPTION,'')&&GCU_SCHEMA_VERSION===(int)get_option(self::SCHEMA_OPTION,0)){self::ensure_future_schema();return true;}$r=self::install_or_upgrade(false);if(is_wp_error($r)){update_option(self::UPGRADE_ERROR,self::safe_error_record($r),false);update_option('gcu_enabled',0,false);return$r;}self::ensure_future_schema();return true;}"
new="public static function maybe_upgrade(){if(GCU_VERSION===(string)get_option(self::VERSION_OPTION,'')&&GCU_SCHEMA_VERSION===(int)get_option(self::SCHEMA_OPTION,0)){$future=self::ensure_future_schema();if(is_wp_error($future)){update_option(self::UPGRADE_ERROR,self::safe_error_record($future),false);return$future;}return true;}$r=self::install_or_upgrade(false);if(is_wp_error($r)){update_option(self::UPGRADE_ERROR,self::safe_error_record($r),false);update_option('gcu_enabled',0,false);return$r;}$future=self::ensure_future_schema(true);if(is_wp_error($future)){update_option(self::UPGRADE_ERROR,self::safe_error_record($future),false);return$future;}return true;}"
install=replace_once(install,old,new,'routine Future schema propagation')

# Non-destructive rollback: never DELETE all live rows; preserve post-snapshot records and only restore unchanged snapshot rows.
old="if(false===$wpdb->query('START TRANSACTION')){return new WP_Error('gcu_rollback_transaction_failed',__('Rollback transaction could not start.','global-clinic-usp-integration'));}try{foreach($maps as$k=>$table){if(!array_key_exists($k,$s['tables'])){continue;}if(false===$wpdb->query(\"DELETE FROM `$table`\")){throw new RuntimeException('delete-'.$k);}foreach(is_array($s['tables'][$k])?$s['tables'][$k]:array()as$row){if(!is_array($row)||false===$wpdb->insert($table,$row)){throw new RuntimeException('insert-'.$k);}}}if(false===$wpdb->query('COMMIT')){throw new RuntimeException('commit');}}"
new="if(false===$wpdb->query('START TRANSACTION')){return new WP_Error('gcu_rollback_transaction_failed',__('Rollback transaction could not start.','global-clinic-usp-integration'));}try{$captured_at=gmdate('Y-m-d H:i:s',isset($s['captured_at'])?(int)$s['captured_at']:0);foreach($maps as$k=>$table){if(!array_key_exists($k,$s['tables'])){continue;}foreach(is_array($s['tables'][$k])?$s['tables'][$k]:array()as$row){if(!is_array($row)||empty($row['id'])){throw new RuntimeException('invalid-'.$k);}$current=$wpdb->get_row($wpdb->prepare(\"SELECT * FROM `$table` WHERE id=%d\",(int)$row['id']),ARRAY_A);if($current){$changed_at=!empty($current['updated_at'])?$current['updated_at']:(isset($current['created_at'])?$current['created_at']:'');if($captured_at&&$changed_at&&strtotime($changed_at.' UTC')>strtotime($captured_at.' UTC')){continue;}$ok=$wpdb->replace($table,$row);if(false===$ok){throw new RuntimeException('replace-'.$k);}}else{$ok=$wpdb->insert($table,$row);if(false===$ok){throw new RuntimeException('insert-'.$k);}}}}if(false===$wpdb->query('COMMIT')){throw new RuntimeException('commit');}}"
install=replace_once(install,old,new,'non-destructive rollback preservation')
write(install_path,install)

# 13) Upgrade errors remain observable while hooks still register safe degraded/admin surfaces.
plugin_path='14-global-clinic-usp-integration/includes/class-gcu-plugin.php'
plugin=read(plugin_path)
old="GCU_Install::maybe_upgrade();"
new="$upgrade=GCU_Install::maybe_upgrade();if(is_wp_error($upgrade)){GCU_Observability::log('error','runtime_upgrade_pending',array('code'=>$upgrade->get_error_code()));}"
plugin=replace_once(plugin,old,new,'plugin upgrade observability')
write(plugin_path,plugin)

# 14) System Check must cover Future schema/safe-mode, File00 authorization adapter, File20 shell adapter, cron and rewrite readiness; partial audit scope must warn.
obs_path='14-global-clinic-usp-integration/includes/class-gcu-observability.php'
obs=read(obs_path)
old="$lm=GCU_I18n::missing_keys();$audit=$missing?array('valid'=>false,'scope'=>'unavailable','checked'=>0,'total'=>0):GCU_Plugin::instance()->repository()->verify_audit_chain(5000);return array('version'=>GCU_VERSION,'plan_version'=>GCU_PLAN_VERSION,'central_plan_baseline'=>GCU_CENTRAL_PLAN_BASELINE,'brand_primary'=>GCU_BRAND_PRIMARY,'schema_version'=>(int)get_option(GCU_Install::SCHEMA_OPTION,0),'expected_schema'=>GCU_SCHEMA_VERSION,'enabled'=>(bool)get_option('gcu_enabled',1),'upgrade_error'=>get_option(GCU_Install::UPGRADE_ERROR,array()),'missing_tables'=>$missing,'table_engines'=>$engines,'non_innodb_tables'=>$non,'stale_claims'=>$stale,'destinations'=>GCU_Plugin::instance()->contracts()->all_destination_health(),'queues'=>$q,'audit_chain'=>$audit,'localization_complete'=>empty($lm),'localization_missing'=>$lm,'legacy_migration'=>get_option(GCU_Install::MIGRATION_LOG,array()),'policy_revalidation'=>get_option(GCU_Contracts::REVALIDATION_OPTION,array()),'generated_at'=>gmdate('c'));}"
new="$lm=GCU_I18n::missing_keys();$audit=$missing?array('valid'=>false,'scope'=>'unavailable','checked'=>0,'total'=>0):GCU_Plugin::instance()->repository()->verify_audit_chain(5000);$future=array('schema_version'=>0,'expected_schema'=>GCU_FUTURE_SCHEMA_VERSION,'safe_mode'=>true,'schema_verified'=>false,'schema_error'=>'unavailable');if(class_exists('GCU_Future_Intelligence')){$fv=GCU_Future_Intelligence::verify_schema();$future=array('schema_version'=>(int)get_option(GCU_Future_Intelligence::SCHEMA_OPTION,0),'expected_schema'=>GCU_FUTURE_SCHEMA_VERSION,'safe_mode'=>(bool)get_option(GCU_Future_Intelligence::SAFE_MODE_OPTION,0),'schema_verified'=>!is_wp_error($fv),'schema_error'=>is_wp_error($fv)?sanitize_key($fv->get_error_code()):'');}$cron=array();foreach(array('gcu_daily_governance_check','gcu_process_outbox','gcu_process_inbox','gcu_lifecycle_cleanup','gcu_future_daily_governance','gcu_future_hourly_intelligence')as$hook){$cron[$hook]=(bool)wp_next_scheduled($hook);}$rules=get_option('rewrite_rules',array());$routes=array('global_clinic'=>false,'find_doctor'=>false,'start_clinic'=>false,'how_it_works'=>false);if(is_array($rules)){foreach($rules as$pattern=>$target){if(false!==strpos((string)$target,'gcu_route=global_clinic')){$routes['global_clinic']=true;}if(false!==strpos((string)$target,'gcu_route=find_doctor')){$routes['find_doctor']=true;}if(false!==strpos((string)$target,'gcu_route=start_clinic')){$routes['start_clinic']=true;}if(false!==strpos((string)$target,'gcu_route=how_it_works')){$routes['how_it_works']=true;}}}$dependencies=array('file00_authorization_adapter'=>GCU_Capabilities::authorization_adapter_available(),'file20_navigation_adapter'=>false!==has_filter('sabri_shell_back_home_controls'),'file20_slot_adapter'=>false!==has_filter('sabri_shell_slot_ready_v1'));return array('version'=>GCU_VERSION,'plan_version'=>GCU_PLAN_VERSION,'central_plan_baseline'=>GCU_CENTRAL_PLAN_BASELINE,'brand_primary'=>GCU_BRAND_PRIMARY,'schema_version'=>(int)get_option(GCU_Install::SCHEMA_OPTION,0),'expected_schema'=>GCU_SCHEMA_VERSION,'future'=>$future,'enabled'=>(bool)get_option('gcu_enabled',1),'upgrade_error'=>get_option(GCU_Install::UPGRADE_ERROR,array()),'missing_tables'=>$missing,'table_engines'=>$engines,'non_innodb_tables'=>$non,'stale_claims'=>$stale,'destinations'=>GCU_Plugin::instance()->contracts()->all_destination_health(),'dependencies'=>$dependencies,'cron'=>$cron,'routes'=>$routes,'queues'=>$q,'audit_chain'=>$audit,'localization_complete'=>empty($lm),'localization_missing'=>$lm,'legacy_migration'=>get_option(GCU_Install::MIGRATION_LOG,array()),'policy_revalidation'=>get_option(GCU_Contracts::REVALIDATION_OPTION,array()),'generated_at'=>gmdate('c'));}"
obs=replace_once(obs,old,new,'expanded system check')
old="$warn=!empty($r['missing_tables'])||!empty($r['non_innodb_tables'])||$r['stale_claims']>0||!$r['localization_complete']||empty($r['audit_chain']['valid'])||$r['queues']['outbox']['dead']>0||$r['queues']['inbox']['dead']>0;"
new="$warn=!empty($r['missing_tables'])||!empty($r['non_innodb_tables'])||$r['stale_claims']>0||!$r['localization_complete']||empty($r['audit_chain']['valid'])||'full'!==$r['audit_chain']['scope']||empty($r['future']['schema_verified'])||!empty($r['future']['safe_mode'])||in_array(false,$r['dependencies'],true)||in_array(false,$r['cron'],true)||in_array(false,$r['routes'],true)||$r['queues']['outbox']['dead']>0||$r['queues']['inbox']['dead']>0;"
obs=replace_once(obs,old,new,'health warning completeness')
write(obs_path,obs)

# 15) Rollback itself is a governance mutation and must be auditable after successful restoration.
admin_path='14-global-clinic-usp-integration/includes/class-gcu-admin.php'
admin=read(admin_path)
old="public function rollback(){$this->authorize('gcu_rollback',GCU_Capabilities::SYSTEM_CHECK);$x=GCU_Install::rollback_snapshot();$this->redirect(is_wp_error($x)?'rollback-unavailable':'snapshot-restored');}"
new="public function rollback(){$this->authorize('gcu_rollback',GCU_Capabilities::SYSTEM_CHECK);$x=GCU_Install::rollback_snapshot();if(!is_wp_error($x)){GCU_Plugin::instance()->repository()->audit('rollback_restored','module','file14','operations','Verified owner snapshot restoration',array(),array('version'=>GCU_VERSION,'schema'=>GCU_SCHEMA_VERSION,'future_schema'=>GCU_FUTURE_SCHEMA_VERSION));}$this->redirect(is_wp_error($x)?'rollback-unavailable':'snapshot-restored');}"
admin=replace_once(admin,old,new,'rollback audit')
old="<tr><th>State</th><td><?php echo esc_html($r['enabled']?'enabled':'safe-mode disabled');?></td></tr><tr><th>Missing tables</th>"
new="<tr><th>State</th><td><?php echo esc_html($r['enabled']?'enabled':'safe-mode disabled');?></td></tr><tr><th>Future schema</th><td><?php echo esc_html($r['future']['schema_version'].' / '.$r['future']['expected_schema'].' / '.($r['future']['schema_verified']?'verified':'unverified').($r['future']['safe_mode']?' / safe mode':''));?></td></tr><tr><th>Dependencies</th><td><?php echo esc_html(wp_json_encode($r['dependencies']));?></td></tr><tr><th>Cron</th><td><?php echo esc_html(wp_json_encode($r['cron']));?></td></tr><tr><th>Routes</th><td><?php echo esc_html(wp_json_encode($r['routes']));?></td></tr><tr><th>Missing tables</th>"
admin=replace_once(admin,old,new,'admin system check completeness')
write(admin_path,admin)

print('Fourth-review source corrections applied')
