#!/usr/bin/env python3
from pathlib import Path
import re, subprocess, sys
ROOT=Path(__file__).resolve().parents[1]

def read(rel): return (ROOT/rel).read_text(encoding='utf-8')
def write(rel,s): (ROOT/rel).write_text(s,encoding='utf-8')
def must_replace(s,old,new,label,count=1):
    if s.count(old)<count: raise SystemExit(f'{label}: expected pattern missing')
    return s.replace(old,new,count)
def replace_method(src,start_sig,next_sig,new_method,label):
    a=src.find(start_sig)
    if a<0: raise SystemExit(f'{label}: start missing')
    b=src.find(next_sig,a+len(start_sig))
    if b<0: raise SystemExit(f'{label}: next marker missing')
    return src[:a]+new_method+'\n\n'+src[b:]
def commit(msg,paths=None):
    subprocess.run(['git','add','-A'] if paths is None else ['git','add',*paths],cwd=ROOT,check=True)
    if subprocess.run(['git','diff','--cached','--quiet'],cwd=ROOT).returncode==0: raise SystemExit('no staged change: '+msg)
    subprocess.run(['git','commit','-m',msg],cwd=ROOT,check=True)

# Round 23 — integrity keys/migration state are forward-only and must not be
# reverted by owner-data rollback after audit/history rekeying.
install_rel='14-global-clinic-usp-integration/includes/class-gcu-install.php'
install=read(install_rel)
install=must_replace(install,
"$names=array(self::VERSION_OPTION,self::SCHEMA_OPTION,'gcu_enabled','gcu_settings','gcu_legacy_migrated',self::MIGRATION_LOG,GCU_Integrity::AUDIT_KEY_OPTION,GCU_Integrity::PRIVACY_KEY_OPTION,GCU_Integrity::MIGRATION_OPTION);",
"$names=array(self::VERSION_OPTION,self::SCHEMA_OPTION,'gcu_enabled','gcu_settings','gcu_legacy_migrated',self::MIGRATION_LOG);",
'rollback integrity-state isolation')
write(install_rel,install)
commit('Round 23: keep integrity migration state forward-only across owner rollback',[install_rel])

# Rounds 24-26 — fail-closed rate-limit reads and event+outbox atomicity.
repo_rel='14-global-clinic-usp-integration/includes/class-gcu-repository.php'
repo=read(repo_rel)
old="public function consume_rate_limit($scope,$limit=60){$ready=GCU_Install::ready_for_mutation();if(is_wp_error($ready)){return$ready;}global$wpdb;$t=GCU_Install::tables();$id=is_user_logged_in()?'u:'.get_current_user_id():'a:'.(isset($_SERVER['REMOTE_ADDR'])?sanitize_text_field(wp_unslash($_SERVER['REMOTE_ADDR'])):'unknown');$bucket=hash_hmac('sha256',sanitize_key($scope).'|'.$id.'|'.gmdate('YmdHi'),wp_salt('auth'));$sql=$wpdb->prepare(\"INSERT INTO {$t['rate_limits']} (bucket_key,counter,expires_at,updated_at) VALUES (%s,1,%s,%s) ON DUPLICATE KEY UPDATE counter=counter+1,expires_at=VALUES(expires_at),updated_at=VALUES(updated_at)\",$bucket,gmdate('Y-m-d H:i:s',time()+120),current_time('mysql',true));if(false===$wpdb->query($sql)){return new WP_Error('gcu_rate_limit_store_failed',__('The request could not be rate-limited safely.','global-clinic-usp-integration'),array('status'=>503));}$count=(int)$wpdb->get_var($wpdb->prepare(\"SELECT counter FROM {$t['rate_limits']} WHERE bucket_key=%s\",$bucket));return$count>max(1,(int)$limit)?new WP_Error('gcu_rate_limited',__('Too many requests. Please retry later.','global-clinic-usp-integration'),array('status'=>429)):$count;}"
new="public function consume_rate_limit($scope,$limit=60){$ready=GCU_Install::ready_for_mutation();if(is_wp_error($ready)){return$ready;}global$wpdb;$t=GCU_Install::tables();$id=is_user_logged_in()?'u:'.get_current_user_id():'a:'.(isset($_SERVER['REMOTE_ADDR'])?sanitize_text_field(wp_unslash($_SERVER['REMOTE_ADDR'])):'unknown');$bucket=hash_hmac('sha256',sanitize_key($scope).'|'.$id.'|'.gmdate('YmdHi'),wp_salt('auth'));$sql=$wpdb->prepare(\"INSERT INTO {$t['rate_limits']} (bucket_key,counter,expires_at,updated_at) VALUES (%s,1,%s,%s) ON DUPLICATE KEY UPDATE counter=counter+1,expires_at=VALUES(expires_at),updated_at=VALUES(updated_at)\",$bucket,gmdate('Y-m-d H:i:s',time()+120),current_time('mysql',true));if(false===$wpdb->query($sql)){return new WP_Error('gcu_rate_limit_store_failed',__('The request could not be rate-limited safely.','global-clinic-usp-integration'),array('status'=>503));}$raw=$wpdb->get_var($wpdb->prepare(\"SELECT counter FROM {$t['rate_limits']} WHERE bucket_key=%s\",$bucket));if(null===$raw){return new WP_Error('gcu_rate_limit_read_failed',__('The request could not be rate-limited safely.','global-clinic-usp-integration'),array('status'=>503));}$count=(int)$raw;return$count>max(1,(int)$limit)?new WP_Error('gcu_rate_limited',__('Too many requests. Please retry later.','global-clinic-usp-integration'),array('status'=>429)):$count;}"
repo=must_replace(repo,old,new,'rate-limit read fail-close')
start='public function record_event(array$d)'
next_sig='public function funnel_summary('
new_event="""public function record_event(array$d){$ready=GCU_Install::ready_for_mutation();if(is_wp_error($ready)){return$ready;}if(!GCU_Privacy::measurement_allowed()||GCU_Hardening::is_sensitive_path()){return new WP_Error('gcu_consent_required',__('Measurement consent is required and this route must be eligible.','global-clinic-usp-integration'),array('status'=>403));}$rate=$this->consume_rate_limit('measurement',GCU_Policy::EVENT_RATE_LIMIT);if(is_wp_error($rate)){return$rate;}$stage=sanitize_key(isset($d['stage'])?$d['stage']:'');if(!in_array($stage,array('impression','cta_selected','destination_loaded','application_started','booking_started'),true)){return new WP_Error('gcu_invalid_stage',__('Unknown funnel stage.','global-clinic-usp-integration'),array('status'=>400));}$dest=sanitize_key(isset($d['destination'])?$d['destination']:'');$reg=GCU_Plugin::instance()->contracts()->destination_registry();if($dest&&!isset($reg[$dest])){return new WP_Error('gcu_invalid_destination',__('Unknown funnel destination.','global-clinic-usp-integration'),array('status'=>400));}if(('application_started'===$stage&&'doctor_onboarding'!==$dest)||('booking_started'===$stage&&'clinic'!==$dest)){return new WP_Error('gcu_stage_destination_mismatch',__('The funnel stage does not match its owner destination.','global-clinic-usp-integration'),array('status'=>400));}global$wpdb;$t=GCU_Install::tables();$c=GCU_Policy::sanitize_campaign(isset($d['campaign'])&&is_array($d['campaign'])?$d['campaign']:array());$id=isset($d['event_id'])&&wp_is_uuid($d['event_id'])?$d['event_id']:wp_generate_uuid4();$subject=GCU_Plugin::instance()->privacy()->event_subject_hash();if(!$this->begin_owned_transaction()){return new WP_Error('gcu_event_transaction_failed',__('The conversion event transaction could not start.','global-clinic-usp-integration'),array('status'=>503));}$ins=$wpdb->query($wpdb->prepare(\"INSERT IGNORE INTO {$t['events']} (event_id,event_type,event_version,funnel_stage,destination_key,subject_hash,source_value,medium_value,campaign_value,ref_value,consent_state,occurred_at,created_at) VALUES (%s,'ClinicUSPFunnelEvent',1,%s,%s,%s,%s,%s,%s,%s,'granted',%s,%s)\",$id,$stage,$dest,$subject,$c['source'],$c['medium'],$c['campaign'],$c['ref'],current_time('mysql',true),current_time('mysql',true)));if(false===$ins){$this->rollback_owned_transaction();return new WP_Error('gcu_event_write_failed',__('The event could not be recorded.','global-clinic-usp-integration'),array('status'=>500));}if('cta_selected'===$stage&&1===$ins&&false===$this->publish_event('ClinicUSPCTASelected.v1',array('source_event_id'=>$id,'destination'=>$dest))){$this->rollback_owned_transaction();return new WP_Error('gcu_event_outbox_failed',__('The conversion event could not be durably queued for its owner event contract.','global-clinic-usp-integration'),array('status'=>503));}if(!$this->commit_owned_transaction()){$this->rollback_owned_transaction();return new WP_Error('gcu_event_commit_failed',__('The conversion event could not be committed safely.','global-clinic-usp-integration'),array('status'=>503));}return array('event_id'=>$id,'deduplicated'=>0===$ins);}"""
repo=replace_method(repo,start,next_sig,new_event,'record_event atomicity')
write(repo_rel,repo)
commit('Rounds 24-26: fail closed rate limits and atomically persist conversion outbox',[repo_rel])

# Rounds 27-31 — all Future governance writes must be state+audit atomic;
# policy-triggered early stop must use the transactional implementation;
# query APIs gain stable cursor pagination.
future_rel='14-global-clinic-usp-integration/includes/class-gcu-future-intelligence.php'
future=read(future_rel)
new_create=r'''\tpublic static function create_report( array $data ) {
		$ready = self::runtime_ready();
		if ( is_wp_error( $ready ) ) { return $ready; }
		$rate = GCU_Plugin::instance()->repository()->consume_rate_limit( 'future-report', 5 );
		if ( is_wp_error( $rate ) ) { return $rate; }
		$reasons = array( 'outdated', 'misleading', 'unclear', 'translation', 'broken_destination', 'faq_gap', 'other' );
		$reason = sanitize_key( isset( $data['reason_code'] ) ? $data['reason_code'] : '' );
		if ( ! in_array( $reason, $reasons, true ) ) { return new WP_Error( 'gcu_future_report_reason_invalid', __( 'Choose an approved report reason.', 'global-clinic-usp-integration' ), array( 'status' => 400 ) ); }
		$message = trim( sanitize_textarea_field( isset( $data['message'] ) ? $data['message'] : '' ) );
		$message = function_exists( 'mb_substr' ) ? mb_substr( $message, 0, 500 ) : substr( $message, 0, 500 );
		if ( self::report_contains_sensitive_data( $message ) ) { return new WP_Error( 'gcu_future_report_sensitive_data', __( 'Do not include personal, contact, identity or clinical details in a copy-quality report.', 'global-clinic-usp-integration' ), array( 'status' => 400 ) ); }
		$route = sanitize_key( isset( $data['route_key'] ) ? $data['route_key'] : 'global_clinic' );
		if ( ! in_array( $route, array( 'global_clinic', 'how_it_works' ), true ) ) { $route = 'global_clinic'; }
		$block = sanitize_key( isset( $data['block_key'] ) ? $data['block_key'] : '' );
		$locale = GCU_Policy::sanitize_locale( isset( $data['locale'] ) ? $data['locale'] : 'en-US' );
		$id = isset( $data['report_id'] ) && wp_is_uuid( $data['report_id'] ) ? $data['report_id'] : wp_generate_uuid4();
		$actor_hash = is_user_logged_in() ? GCU_Integrity::future_actor_hash( get_current_user_id() ) : null;
		global $wpdb; $t = self::tables(); $now = current_time( 'mysql', true ); $repo=GCU_Plugin::instance()->repository();
		if(!$repo->begin_owned_transaction()){return new WP_Error('gcu_future_report_transaction_failed',__('The report transaction could not start.','global-clinic-usp-integration'),array('status'=>503));}
		$insert = $wpdb->query( $wpdb->prepare( "INSERT IGNORE INTO {$t['reports']} (public_id,report_type,route_key,block_key,locale,reason_code,message,actor_hash,status,created_at,updated_at) VALUES (%s,'copy_quality',%s,%s,%s,%s,%s,%s,'open',%s,%s)", $id, $route, $block ? $block : null, $locale, $reason, $message, $actor_hash, $now, $now ) );
		if(false===$insert){$repo->rollback_owned_transaction();return new WP_Error('gcu_future_report_write_failed',__('The report could not be recorded safely.','global-clinic-usp-integration'),array('status'=>500));}
		if(1===$insert){$audit=$repo->audit('copy_quality_reported','future_report',$id,'public_feedback',$reason,array(),array('route'=>$route,'block'=>$block,'locale'=>$locale));$event=$audit!==false?$repo->publish_event('ClinicUSPCopyQualityReported.v1',array('report_id'=>$id,'reason_code'=>$reason,'route_key'=>$route,'block_key'=>$block)):false;if(false===$audit||false===$event){$repo->rollback_owned_transaction();return new WP_Error('gcu_future_report_governance_failed',__('The report was not committed because its audit or event record could not be persisted.','global-clinic-usp-integration'),array('status'=>503));}}
		if(!$repo->commit_owned_transaction()){$repo->rollback_owned_transaction();return new WP_Error('gcu_future_report_commit_failed',__('The report could not be committed safely.','global-clinic-usp-integration'),array('status'=>503));}
		return array('report_id'=>$id,'status'=>'open','deduplicated'=>0===$insert);
	}'''
future=replace_method(future,'\tpublic static function create_report( array $data ) {','\tprivate static function report_contains_sensitive_data',new_create,'create_report')
new_resolve=r'''\tpublic static function resolve_report_record( $id, $expected, $status, $resolution ) {
		$ready=self::runtime_ready();if(is_wp_error($ready)){return$ready;}
		if(!in_array($status,array('reviewing','resolved','rejected'),true)){return new WP_Error('gcu_future_report_status_invalid',__('Invalid report status.','global-clinic-usp-integration'));}
		$resolution=trim(sanitize_textarea_field($resolution));if(in_array($status,array('resolved','rejected'),true)&&strlen($resolution)<8){return new WP_Error('gcu_future_report_resolution_required',__('A meaningful resolution is required.','global-clinic-usp-integration'));}
		global$wpdb;$t=self::tables();$row=$wpdb->get_row($wpdb->prepare("SELECT * FROM {$t['reports']} WHERE public_id=%s",$id),ARRAY_A);if(!$row){return new WP_Error('gcu_future_report_not_found',__('Report not found.','global-clinic-usp-integration'));}if((int)$row['row_version']!==(int)$expected){return new WP_Error('gcu_future_report_version_conflict',__('The report changed. Reload it.','global-clinic-usp-integration'));}
		$repo=GCU_Plugin::instance()->repository();if(!$repo->begin_owned_transaction()){return new WP_Error('gcu_future_report_transaction_failed',__('The report transaction could not start.','global-clinic-usp-integration'),array('status'=>503));}
		$done=$wpdb->query($wpdb->prepare("UPDATE {$t['reports']} SET status=%s,resolution=%s,row_version=row_version+1,updated_at=%s WHERE id=%d AND row_version=%d",$status,$resolution,current_time('mysql',true),(int)$row['id'],(int)$expected));if(1!==$done||false===$repo->audit('copy_quality_report_updated','future_report',$id,'public_feedback_review',$resolution,$row,array('status'=>$status))){$repo->rollback_owned_transaction();return new WP_Error('gcu_future_report_update_failed',__('The report could not be updated with its mandatory audit record.','global-clinic-usp-integration'),array('status'=>409));}if(!$repo->commit_owned_transaction()){$repo->rollback_owned_transaction();return new WP_Error('gcu_future_report_commit_failed',__('The report update could not be committed safely.','global-clinic-usp-integration'),array('status'=>503));}return array('report_id'=>$id,'status'=>$status,'row_version'=>(int)$expected+1);
	}'''
future=replace_method(future,'\tpublic static function resolve_report_record( $id, $expected, $status, $resolution ) {','\tpublic static function upsert_record(',new_resolve,'resolve report')
# Rewrite upsert atomically while preserving validation and system mode.
new_upsert=r'''\tpublic static function upsert_record( $type, $key, $locale, $region, array $payload, $status = 'draft', $is_public = false, $expected = 0, $system = false ) {
		$ready=self::verify_schema();if(is_wp_error($ready)){return$ready;}$type=sanitize_key($type);$key=sanitize_key($key);$locale=GCU_Policy::sanitize_locale($locale);$region=self::sanitize_region($region);$status=sanitize_key($status);if(!in_array($status,array('draft','suggested','review','active','superseded','rejected'),true)){$status='draft';}$payload=GCU_Hardening::sanitize_structured_value($payload);$encoded=wp_json_encode($payload);if(false===$encoded||strlen($encoded)>self::RECORD_PAYLOAD_MAX){return new WP_Error('gcu_future_record_payload_invalid',__('Future Intelligence record payload is invalid or too large.','global-clinic-usp-integration'),array('status'=>400));}
		global$wpdb;$t=self::tables();$row=$wpdb->get_row($wpdb->prepare("SELECT * FROM {$t['records']} WHERE record_type=%s AND record_key=%s AND locale=%s AND region=%s",$type,$key,$locale,$region),ARRAY_A);if($row&&!$system&&(int)$expected!==(int)$row['row_version']){return new WP_Error('gcu_future_record_version_conflict',__('The Future Intelligence record changed. Reload it.','global-clinic-usp-integration'),array('status'=>409,'current_version'=>(int)$row['row_version']));}$now=current_time('mysql',true);$review_due=gmdate('Y-m-d H:i:s',time()+GCU_Policy::COPY_REVIEW_DAYS*DAY_IN_SECONDS);$repo=GCU_Plugin::instance()->repository();if(!$repo->begin_owned_transaction()){return new WP_Error('gcu_future_record_transaction_failed',__('The Future Intelligence transaction could not start.','global-clinic-usp-integration'),array('status'=>503));}
		if($row){$done=$wpdb->query($wpdb->prepare("UPDATE {$t['records']} SET status=%s,is_public=%d,payload=%s,payload_hash=%s,row_version=row_version+1,review_due_at=%s,updated_at=%s WHERE id=%d AND row_version=%d",$status,$is_public?1:0,$encoded,hash('sha256',$encoded),$review_due,$now,(int)$row['id'],(int)$row['row_version']));if(1!==$done||false===$repo->audit('future_record_updated','future_record',$type.':'.$key,'future_intelligence_governance','',$row,array('status'=>$status,'hash'=>hash('sha256',$encoded)))){$repo->rollback_owned_transaction();return new WP_Error('gcu_future_record_update_failed',__('The Future Intelligence record could not be updated with its mandatory audit record.','global-clinic-usp-integration'),array('status'=>409));}$result=array('record_type'=>$type,'record_key'=>$key,'locale'=>$locale,'region'=>$region,'status'=>$status,'row_version'=>(int)$row['row_version']+1);}else{$data=array('record_type'=>$type,'record_key'=>$key,'locale'=>$locale,'region'=>$region,'status'=>$status,'is_public'=>$is_public?1:0,'payload'=>$encoded,'payload_hash'=>hash('sha256',$encoded),'review_due_at'=>$review_due,'created_by'=>$system?0:get_current_user_id(),'created_at'=>$now,'updated_at'=>$now);if(false===$wpdb->insert($t['records'],$data)||false===$repo->audit('future_record_created','future_record',$type.':'.$key,'future_intelligence_governance','',array(),array('status'=>$status,'hash'=>hash('sha256',$encoded)))){$repo->rollback_owned_transaction();return new WP_Error('gcu_future_record_insert_failed',__('The Future Intelligence record could not be created with its mandatory audit record.','global-clinic-usp-integration'),array('status'=>500));}$result=array('record_type'=>$type,'record_key'=>$key,'locale'=>$locale,'region'=>$region,'status'=>$status,'row_version'=>1);}
		if(!$repo->commit_owned_transaction()){$repo->rollback_owned_transaction();return new WP_Error('gcu_future_record_commit_failed',__('The Future Intelligence record could not be committed safely.','global-clinic-usp-integration'),array('status'=>503));}return$result;
	}'''
future=replace_method(future,'\tpublic static function upsert_record(','\tpublic static function records(',new_upsert,'upsert record')
# All paths, including BusinessPolicyChanged.v1, use the reviewed transactional early-stop guard.
new_early=r'''\tpublic static function early_stop_guard() {
		if ( class_exists( 'GCU_Fifth_Review_Hardening' ) ) { return GCU_Fifth_Review_Hardening::transactional_early_stop_guard(); }
		return 0;
	}'''
future=replace_method(future,'\tpublic static function early_stop_guard() {','\tpublic static function faq_gap_candidates()',new_early,'early stop delegation')
# Bounded draining cleanup instead of one fixed 200-row pass.
old_cleanup="""\t\t$reports = $wpdb->query( $wpdb->prepare( \"DELETE FROM {$t['reports']} WHERE status IN ('resolved','rejected') AND updated_at<DATE_SUB(UTC_TIMESTAMP(),INTERVAL %d DAY) LIMIT 200\", self::REPORT_RETENTION_DAYS ) );
\t\t$records = $wpdb->query( \"DELETE FROM {$t['records']} WHERE status IN ('superseded','rejected') AND updated_at<DATE_SUB(UTC_TIMESTAMP(),INTERVAL 730 DAY) LIMIT 200\" );
\t\treturn array( 'reports' => $reports, 'records' => $records );"""
new_cleanup="""\t\t$reports=0;$records=0;for($i=0;$i<10;$i++){$n=$wpdb->query($wpdb->prepare(\"DELETE FROM {$t['reports']} WHERE status IN ('resolved','rejected') AND updated_at<DATE_SUB(UTC_TIMESTAMP(),INTERVAL %d DAY) LIMIT 200\",self::REPORT_RETENTION_DAYS));if(false===$n){$reports=false;break;}$reports+=$n;if($n<200){break;}}for($i=0;$i<10;$i++){$n=$wpdb->query(\"DELETE FROM {$t['records']} WHERE status IN ('superseded','rejected') AND updated_at<DATE_SUB(UTC_TIMESTAMP(),INTERVAL 730 DAY) LIMIT 200\");if(false===$n){$records=false;break;}$records+=$n;if($n<200){break;}}if(2000===$reports||2000===$records){GCU_Observability::log('warning','future_lifecycle_cleanup_backlog',array('reports'=>$reports,'records'=>$records));}return array('reports'=>$reports,'records'=>$records);"""
future=must_replace(future,old_cleanup,new_cleanup,'future lifecycle draining')
# REST query cursor pagination.
old="""\tpublic static function rest_records( WP_REST_Request $request ) {
\t\t$type = sanitize_key( (string) $request->get_param( 'type' ) );
\t\treturn self::no_store_response( array( 'items' => self::records( $type, false, 100 ) ) );
\t}"""
new="""\tpublic static function rest_records( WP_REST_Request $request ) { $type=sanitize_key((string)$request->get_param('type'));$limit=max(1,min(100,absint($request->get_param('limit')?:50)));$cursor=absint($request->get_param('cursor'));return self::no_store_response(self::records_page($type,$cursor,$limit)); }"""
future=must_replace(future,old,new,'records REST cursor')
old="""\tpublic static function rest_reports() {
\t\treturn self::no_store_response( array( 'items' => self::reports( 'open', 100 ) ) );
\t}"""
new="""\tpublic static function rest_reports( WP_REST_Request $request ) { $status=sanitize_key((string)$request->get_param('status'));if(!in_array($status,array('open','reviewing','resolved','rejected'),true)){$status='open';}$limit=max(1,min(100,absint($request->get_param('limit')?:50)));$cursor=absint($request->get_param('cursor'));return self::no_store_response(self::reports_page($status,$cursor,$limit)); }"""
future=must_replace(future,old,new,'reports REST cursor')
# Add page helpers immediately before legacy reports() method.
marker='\tpublic static function reports( $status = \'open\', $limit = 100 ) {'
helpers=r'''\tprivate static function reports_page($status,$cursor,$limit){global$wpdb;$t=self::tables();$rows=$wpdb->get_results($wpdb->prepare("SELECT id,public_id,report_type,route_key,block_key,locale,reason_code,message,status,resolution,row_version,created_at,updated_at FROM {$t['reports']} WHERE status=%s AND id>%d ORDER BY id ASC LIMIT %d",$status,absint($cursor),$limit+1),ARRAY_A);$rows=is_array($rows)?$rows:array();$more=count($rows)>$limit;if($more){$rows=array_slice($rows,0,$limit);}$next=$more&&!empty($rows)?(int)$rows[count($rows)-1]['id']:null;foreach($rows as&$row){unset($row['id']);}unset($row);return array('items'=>$rows,'next_cursor'=>$next,'limit'=>$limit);}
	private static function records_page($type,$cursor,$limit){global$wpdb;$t=self::tables();$where='id>%d';$args=array(absint($cursor));if($type){$where.=' AND record_type=%s';$args[]=sanitize_key($type);}$args[]=$limit+1;$sql="SELECT id,record_type,record_key,locale,region,status,is_public,payload,row_version,review_due_at,updated_at FROM {$t['records']} WHERE $where ORDER BY id ASC LIMIT %d";$rows=$wpdb->get_results($wpdb->prepare($sql,$args),ARRAY_A);$rows=is_array($rows)?$rows:array();$more=count($rows)>$limit;if($more){$rows=array_slice($rows,0,$limit);}$next=$more&&!empty($rows)?(int)$rows[count($rows)-1]['id']:null;foreach($rows as&$row){unset($row['id']);$row['payload']=self::json_array($row['payload']);}unset($row);return array('items'=>$rows,'next_cursor'=>$next,'limit'=>$limit);}
'''
if marker not in future: raise SystemExit('pagination helper marker missing')
future=future.replace(marker,helpers+marker,1)
write(future_rel,future)
commit('Rounds 27-31: make Future writes atomic and add stable cursor pagination',[future_rel])

# Reliability test now recognizes the transaction manager rather than requiring
# multiple literal START TRANSACTION sites.
rel='tests/reliability-tests.php';x=read(rel)
x=must_replace(x,"must(substr_count($src['repo'],'START TRANSACTION')>=2,'Governed transactional mutations are insufficient.');","must(false!==strpos($src['repo'],'begin_owned_transaction')&&false!==strpos($src['repo'],'commit_owned_transaction')&&false!==strpos($src['repo'],'rollback_owned_transaction'),'Governed transaction manager is insufficient.');",'reliability transaction assertion')
write(rel,x)
commit('QA: align reliability assertion with nested owner transaction manager',[rel])

print('Sixth review final corrective patch applied.')
