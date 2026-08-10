#!/usr/bin/env python3
from pathlib import Path

ROOT = Path(__file__).resolve().parents[1]


def replace_exact(path, old, new):
    p = ROOT / path
    text = p.read_text(encoding='utf-8')
    if new in text:
        return False
    if old not in text:
        raise SystemExit(f'Expected source pattern missing: {path}: {old[:180]!r}')
    p.write_text(text.replace(old, new, 1), encoding='utf-8')
    return True


def replace_between(path, start, end, new):
    p = ROOT / path
    text = p.read_text(encoding='utf-8')
    a = text.find(start)
    b = text.find(end, a + len(start))
    if a < 0 or b < 0:
        raise SystemExit(f'Expected bounded region missing: {path}: {start!r} -> {end!r}')
    old = text[a:b]
    if old == new:
        return False
    p.write_text(text[:a] + new + text[b:], encoding='utf-8')
    return True

changed = False

# A correction-script artifact exposed in the next pass: normalize ordinary variable syntax.
changed |= replace_exact(
    '14-global-clinic-usp-integration/includes/class-gcu-repository.php',
    "${'all_claims'}=array();foreach($r as&$row){$row['claim_keys']=json_decode((string)$row['claim_keys'],true);if(!is_array($row['claim_keys'])){$row['claim_keys']=array();}${'all_claims'}=array_merge(${'all_claims'},$row['claim_keys']);}unset($row);$valid=$this->public_claims(${'all_claims'});",
    "$all_claims=array();foreach($r as&$row){$row['claim_keys']=json_decode((string)$row['claim_keys'],true);if(!is_array($row['claim_keys'])){$row['claim_keys']=array();}$all_claims=array_merge($all_claims,$row['claim_keys']);}unset($row);$valid=$this->public_claims($all_claims);"
)

# Base schema verification must detect partial migrations, not just table existence/engine.
install_path = '14-global-clinic-usp-integration/includes/class-gcu-install.php'
base_verify = """\tpublic static function verify_schema(){global$wpdb;$m=array();$w=array();$mc=array();$required=array(
'claims'=>array('public_id','claim_key','claim_text','status','review_due_at','row_version'),
'claim_history'=>array('claim_key','row_version','status','claim_hash','snapshot'),
'blocks'=>array('public_id','block_key','locale','status','content_version','claim_keys','row_version','review_due_at'),
'placements'=>array('public_id','placement_key','block_key','slot_key','status','row_version'),
'experiments'=>array('public_id','experiment_key','status','guardrails','row_version','ends_at'),
'events'=>array('event_id','subject_hash','funnel_stage','destination_key','occurred_at'),
'audit'=>array('trace_id','row_hash','previous_hash','created_at'),
'outbox'=>array('event_id','status','attempts','locked_at','next_attempt_at'),
'inbox'=>array('event_id','status','attempts','locked_at','next_attempt_at'),
'event_tokens'=>array('token_hash','purpose','expires_at','consumed_at'),
'rate_limits'=>array('bucket_key','counter','expires_at'),
'commands'=>array('command_key','status','attempts','locked_at','result_json'));
foreach(self::tables()as$k=>$table){$f=$wpdb->get_var($wpdb->prepare('SHOW TABLES LIKE %s',$wpdb->esc_like($table)));if($table!==$f){$m[]=$k;continue;}$s=$wpdb->get_row($wpdb->prepare('SHOW TABLE STATUS LIKE %s',$wpdb->esc_like($table)),ARRAY_A);$e=is_array($s)&&isset($s['Engine'])?strtolower((string)$s['Engine']):'';if('innodb'!==$e){$w[$k]=$e?$e:'unknown';}$cols=$wpdb->get_col("SHOW COLUMNS FROM `$table`",0);$missing_cols=array_values(array_diff(isset($required[$k])?$required[$k]:array(),is_array($cols)?$cols:array()));if($missing_cols){$mc[$k]=$missing_cols;}}
return($m||$w||$mc)?new WP_Error('gcu_schema_verification_failed',__('File 14 database schema could not be verified safely.','global-clinic-usp-integration'),array('missing'=>$m,'non_innodb'=>$w,'missing_columns'=>$mc)):true;}
"""
changed |= replace_between(install_path, '\tpublic static function verify_schema()', '\tpublic static function seed_governed_content()', base_verify)

# Snapshot and rollback cover Future-owned governance data and are serialized by the install lock.
capture = """\tpublic static function capture_snapshot($force=false){if(!$force&&get_option(self::SNAPSHOT_OPTION,false)){return true;}global$wpdb;$names=array(self::VERSION_OPTION,self::SCHEMA_OPTION,'gcu_enabled','gcu_settings','gcu_legacy_migrated',self::MIGRATION_LOG);if(class_exists('GCU_Future_Intelligence')){$names[]=GCU_Future_Intelligence::SCHEMA_OPTION;$names[]=GCU_Future_Intelligence::SAFE_MODE_OPTION;}$s=array('captured_at'=>time(),'options'=>array(),'tables'=>array());foreach($names as$n){$sent='__gcu_missing__'.wp_generate_uuid4();$v=get_option($n,$sent);$s['options'][$n]=array('exists'=>$sent!==$v,'value'=>$sent!==$v?$v:null);}$maps=array();$base=self::tables();foreach(array('claims','claim_history','blocks','placements','experiments')as$k){$maps[$k]=$base[$k];}if(class_exists('GCU_Future_Intelligence')){$ft=GCU_Future_Intelligence::tables();$maps['future_records']=$ft['records'];$maps['future_reports']=$ft['reports'];}foreach($maps as$k=>$table){$exists=$wpdb->get_var($wpdb->prepare('SHOW TABLES LIKE %s',$wpdb->esc_like($table)));if($table!==$exists){$s['tables'][$k]=array();continue;}$count=(int)$wpdb->get_var("SELECT COUNT(*) FROM `$table`");if($count>self::SNAPSHOT_ROW_MAX){return new WP_Error('gcu_snapshot_too_large',__('File 14 owner data exceeds the bounded rollback snapshot limit.','global-clinic-usp-integration'));}$rows=$wpdb->get_results("SELECT * FROM `$table` ORDER BY id ASC",ARRAY_A);if(null===$rows){return new WP_Error('gcu_snapshot_read_failed',__('File 14 rollback snapshot could not read owner data.','global-clinic-usp-integration'));}$s['tables'][$k]=$rows;}$enc=wp_json_encode($s);if(false===$enc||strlen($enc)>self::SNAPSHOT_MAX){return new WP_Error('gcu_snapshot_size_failed',__('File 14 rollback snapshot exceeds its safe storage bound.','global-clinic-usp-integration'));}$s['snapshot_hash']=hash('sha256',$enc);update_option(self::SNAPSHOT_OPTION,$s,false);return true;}
"""
changed |= replace_between(install_path, '\tpublic static function capture_snapshot(', '\tpublic static function rollback_options()', capture)
rollback = """\tpublic static function rollback_snapshot(){global$wpdb;if(!self::acquire_lock()){return new WP_Error('gcu_rollback_locked',__('A File 14 install, upgrade or recovery operation is already running.','global-clinic-usp-integration'));}try{$s=get_option(self::SNAPSHOT_OPTION,array());if(empty($s['snapshot_hash'])||!isset($s['options'],$s['tables'])){return new WP_Error('gcu_no_snapshot',__('No verified rollback snapshot is available.','global-clinic-usp-integration'));}$copy=$s;$hash=(string)$copy['snapshot_hash'];unset($copy['snapshot_hash']);$enc=wp_json_encode($copy);if(false===$enc||!hash_equals($hash,hash('sha256',$enc))){return new WP_Error('gcu_snapshot_integrity_failed',__('The File 14 rollback snapshot failed integrity verification.','global-clinic-usp-integration'));}$v=self::verify_schema();if(is_wp_error($v)){return $v;}if(isset($s['tables']['future_records'])||isset($s['tables']['future_reports'])){$fv=class_exists('GCU_Future_Intelligence')?GCU_Future_Intelligence::verify_schema():new WP_Error('gcu_future_schema_unavailable',__('Future schema verifier is unavailable.','global-clinic-usp-integration'));if(is_wp_error($fv)){return $fv;}}$base=self::tables();$maps=array('claim_history'=>$base['claim_history'],'claims'=>$base['claims'],'blocks'=>$base['blocks'],'placements'=>$base['placements'],'experiments'=>$base['experiments']);if(class_exists('GCU_Future_Intelligence')){$ft=GCU_Future_Intelligence::tables();$maps['future_records']=$ft['records'];$maps['future_reports']=$ft['reports'];}if(false===$wpdb->query('START TRANSACTION')){return new WP_Error('gcu_rollback_transaction_failed',__('Rollback transaction could not start.','global-clinic-usp-integration'));}try{foreach($maps as$k=>$table){if(!array_key_exists($k,$s['tables'])){continue;}if(false===$wpdb->query("DELETE FROM `$table`")){throw new RuntimeException('delete-'.$k);}foreach(is_array($s['tables'][$k])?$s['tables'][$k]:array()as$row){if(!is_array($row)||false===$wpdb->insert($table,$row)){throw new RuntimeException('insert-'.$k);}}}if(false===$wpdb->query('COMMIT')){throw new RuntimeException('commit');}}catch(Exception$e){$wpdb->query('ROLLBACK');return new WP_Error('gcu_rollback_write_failed',__('File 14 rollback could not be completed atomically.','global-clinic-usp-integration'));}foreach($s['options']as$n=>$entry){if(empty($entry['exists'])){delete_option($n);}else{update_option($n,$entry['value'],false);}}return true;}finally{self::release_lock();}}
"""
changed |= replace_between(install_path, '\tpublic static function rollback_snapshot()', '\tpublic static function schedule()', rollback)

# Future schema verification: critical columns + controlled force verification.
future_path = '14-global-clinic-usp-integration/includes/class-gcu-future-intelligence.php'
changed |= replace_exact(future_path, '\tpublic static function ensure_schema() {\n\t\tif ( self::SCHEMA_VERSION === (int) get_option( self::SCHEMA_OPTION, 0 ) && ! get_option( self::SAFE_MODE_OPTION, 0 ) ) {', '\tpublic static function ensure_schema( $force_verify = false ) {\n\t\tif ( ! $force_verify && self::SCHEMA_VERSION === (int) get_option( self::SCHEMA_OPTION, 0 ) && ! get_option( self::SAFE_MODE_OPTION, 0 ) ) {')
future_verify = """\tpublic static function verify_schema() {
\t\tglobal $wpdb;
\t\t$missing = array();
\t\t$non_innodb = array();
\t\t$missing_columns = array();
\t\t$required = array(
\t\t\t'records' => array( 'record_type','record_key','locale','region','status','is_public','payload','payload_hash','row_version','review_due_at' ),
\t\t\t'reports' => array( 'public_id','report_type','route_key','locale','reason_code','actor_hash','status','row_version','created_at','updated_at' ),
\t\t);
\t\tforeach ( self::tables() as $key => $table ) {
\t\t\t$found = $wpdb->get_var( $wpdb->prepare( 'SHOW TABLES LIKE %s', $wpdb->esc_like( $table ) ) );
\t\t\tif ( $table !== $found ) { $missing[] = $key; continue; }
\t\t\t$status = $wpdb->get_row( $wpdb->prepare( 'SHOW TABLE STATUS LIKE %s', $wpdb->esc_like( $table ) ), ARRAY_A );
\t\t\t$engine = is_array( $status ) && isset( $status['Engine'] ) ? strtolower( (string) $status['Engine'] ) : '';
\t\t\tif ( 'innodb' !== $engine ) { $non_innodb[ $key ] = $engine ? $engine : 'unknown'; }
\t\t\t$columns = $wpdb->get_col( "SHOW COLUMNS FROM `$table`", 0 );
\t\t\t$delta = array_values( array_diff( $required[ $key ], is_array( $columns ) ? $columns : array() ) );
\t\t\tif ( $delta ) { $missing_columns[ $key ] = $delta; }
\t\t}
\t\treturn ( $missing || $non_innodb || $missing_columns ) ? new WP_Error( 'gcu_future_schema_unverified', __( 'Future Conversion and Trust Intelligence storage is not safely verified.', 'global-clinic-usp-integration' ), array( 'missing' => $missing, 'non_innodb' => $non_innodb, 'missing_columns' => $missing_columns ) ) : true;
\t}

"""
changed |= replace_between(future_path, '\tpublic static function verify_schema()', '\tprivate static function seed_defaults()', future_verify)

# Install lifecycle calls can force verification; deactivation clears Future cron hooks too.
changed |= replace_exact(install_path, "\tpublic static function activate(){$r=self::install_or_upgrade(true);if(is_wp_error($r)){update_option(self::UPGRADE_ERROR,self::safe_error_record($r),false);update_option('gcu_enabled',0,false);wp_die(esc_html($r->get_error_message()));}self::ensure_future_schema();}", "\tpublic static function activate(){$r=self::install_or_upgrade(true);if(is_wp_error($r)){update_option(self::UPGRADE_ERROR,self::safe_error_record($r),false);update_option('gcu_enabled',0,false);wp_die(esc_html($r->get_error_message()));}self::ensure_future_schema(true);}")
changed |= replace_exact(install_path, "\tpublic static function deactivate(){foreach(array('gcu_daily_governance_check','gcu_process_outbox','gcu_process_inbox','gcu_lifecycle_cleanup')as$h){wp_clear_scheduled_hook($h);}flush_rewrite_rules();}", "\tpublic static function deactivate(){foreach(array('gcu_daily_governance_check','gcu_process_outbox','gcu_process_inbox','gcu_lifecycle_cleanup','gcu_future_daily_governance','gcu_future_hourly_intelligence')as$h){wp_clear_scheduled_hook($h);}flush_rewrite_rules();}")
changed |= replace_exact(install_path, "\t\treturn self::ensure_future_schema();", "\t\treturn self::ensure_future_schema(true);")
changed |= replace_exact(install_path, "\tprivate static function ensure_future_schema(){if(!class_exists('GCU_Future_Intelligence')){return true;}$r=GCU_Future_Intelligence::ensure_schema();", "\tprivate static function ensure_future_schema($force_verify=false){if(!class_exists('GCU_Future_Intelligence')){return true;}$r=GCU_Future_Intelligence::ensure_schema($force_verify);")

# Base daily governance detects structural schema drift and enters base safe mode before further normal runtime use.
obs_path = '14-global-clinic-usp-integration/includes/class-gcu-observability.php'
changed |= replace_exact(obs_path, "public function daily_governance_check(){$r=$this->health_report();", "public function daily_governance_check(){$schema=GCU_Install::verify_schema();if(is_wp_error($schema)){update_option('gcu_enabled',0,false);update_option(GCU_Install::UPGRADE_ERROR,array('code'=>$schema->get_error_code(),'occurred_at'=>time(),'version'=>GCU_VERSION,'schema'=>GCU_SCHEMA_VERSION),false);} $r=$this->health_report();")

# Future daily governance performs a real structural check; it does not trust the schema option alone forever.
changed |= replace_exact(future_path, "\tpublic static function daily_governance() {\n\t\t$ready = self::runtime_ready();\n\t\tif ( is_wp_error( $ready ) ) { return $ready; }", "\tpublic static function daily_governance() {\n\t\t$ready = self::runtime_ready();\n\t\tif ( is_wp_error( $ready ) ) { return $ready; }\n\t\t$schema = self::verify_schema();\n\t\tif ( is_wp_error( $schema ) ) { update_option( self::SAFE_MODE_OPTION, 1, false ); return $schema; }")

# REST error/response traceability: every File-14 response carries a safe trace identifier.
review_path = '14-global-clinic-usp-integration/includes/class-gcu-review80-hardening.php'
changed |= replace_exact(review_path, "\t\tif ( 0 !== strpos( $route, '/gcu/v1/' ) ) {\n\t\t\treturn $response;\n\t\t}\n\t\t$data = $response->get_data();", "\t\tif ( 0 !== strpos( $route, '/gcu/v1/' ) ) {\n\t\t\treturn $response;\n\t\t}\n\t\t$response->header( 'X-GCU-Trace-ID', GCU_Policy::trace_id() );\n\t\t$data = $response->get_data();")

# Privileged base REST mutations are rate bounded as required by the API constitution.
rest_path = '14-global-clinic-usp-integration/includes/class-gcu-rest.php'
for name, scope in [('create_content','content'),('create_placement','placement'),('create_experiment','experiment')]:
    old = f"public function {name}(WP_REST_Request$r){{$d=$r->get_json_params();"
    new = f"public function {name}(WP_REST_Request$r){{$rate=$this->mutation_rate('{scope}');if(is_wp_error($rate)){{return$rate;}}$d=$r->get_json_params();"
    changed |= replace_exact(rest_path, old, new)
changed |= replace_exact(rest_path, "public function withdraw_claim(WP_REST_Request$r){$k=$this->required_idempotency_key($r);", "public function withdraw_claim(WP_REST_Request$r){$rate=$this->mutation_rate('claim-withdraw');if(is_wp_error($rate)){return$rate;}$k=$this->required_idempotency_key($r);")
changed |= replace_exact(rest_path, "public function transition(WP_REST_Request$r){$k=$this->required_idempotency_key($r);", "public function transition(WP_REST_Request$r){$rate=$this->mutation_rate('workflow');if(is_wp_error($rate)){return$rate;}$k=$this->required_idempotency_key($r);")
changed |= replace_exact(rest_path, "private function required_idempotency_key(WP_REST_Request$r){", "private function mutation_rate($scope){return GCU_Plugin::instance()->repository()->consume_rate_limit('rest-'.sanitize_key($scope),120);}\nprivate function required_idempotency_key(WP_REST_Request$r){")

# Future state-changing REST APIs use both rate limits and durable idempotency.
changed |= replace_exact(future_path, "\tpublic static function rest_report( WP_REST_Request $request ) {\n\t\t$data = $request->get_json_params();\n\t\t$data = is_array( $data ) ? $data : array();\n\t\t$result = self::create_report( $data );", "\tpublic static function rest_report( WP_REST_Request $request ) {\n\t\t$key = self::required_idempotency_key( $request );\n\t\tif ( is_wp_error( $key ) ) { return $key; }\n\t\t$data = $request->get_json_params();\n\t\t$data = is_array( $data ) ? $data : array();\n\t\t$result = GCU_Plugin::instance()->repository()->run_idempotent_command( 'future_report', $key, static function() use ( $data ) { return self::create_report( $data ); } );")
changed |= replace_exact(future_path, "\tpublic static function rest_record_write( WP_REST_Request $request ) {\n\t\t$data = $request->get_json_params();", "\tpublic static function rest_record_write( WP_REST_Request $request ) {\n\t\t$rate = GCU_Plugin::instance()->repository()->consume_rate_limit( 'future-record-write', 120 );\n\t\tif ( is_wp_error( $rate ) ) { return $rate; }\n\t\t$idempotency = self::required_idempotency_key( $request );\n\t\tif ( is_wp_error( $idempotency ) ) { return $idempotency; }\n\t\t$data = $request->get_json_params();")
old_call = "\t\t$result = self::upsert_record(\n\t\t\t$type,\n\t\t\t$key,\n\t\t\tGCU_Policy::sanitize_locale( isset( $data['locale'] ) ? $data['locale'] : 'en-US' ),\n\t\t\tself::sanitize_region( isset( $data['region'] ) ? $data['region'] : 'ZZ' ),\n\t\t\tisset( $data['payload'] ) && is_array( $data['payload'] ) ? $data['payload'] : array(),\n\t\t\tisset( $data['status'] ) ? sanitize_key( $data['status'] ) : 'draft',\n\t\t\t! empty( $data['is_public'] ),\n\t\t\tisset( $data['expected_version'] ) ? absint( $data['expected_version'] ) : 0,\n\t\t\tfalse\n\t\t);"
new_call = "\t\t$result = GCU_Plugin::instance()->repository()->run_idempotent_command( 'future_record_write', $idempotency, static function() use ( $type, $key, $data ) { return self::upsert_record(\n\t\t\t$type, $key, GCU_Policy::sanitize_locale( isset( $data['locale'] ) ? $data['locale'] : 'en-US' ),\n\t\t\tself::sanitize_region( isset( $data['region'] ) ? $data['region'] : 'ZZ' ), isset( $data['payload'] ) && is_array( $data['payload'] ) ? $data['payload'] : array(),\n\t\t\tisset( $data['status'] ) ? sanitize_key( $data['status'] ) : 'draft', ! empty( $data['is_public'] ), isset( $data['expected_version'] ) ? absint( $data['expected_version'] ) : 0, false ); } );"
changed |= replace_exact(future_path, old_call, new_call)
changed |= replace_exact(future_path, "\tpublic static function rest_revalidate_claim( WP_REST_Request $request ) {\n\t\t$key = sanitize_key( $request['claim_key'] );", "\tpublic static function rest_revalidate_claim( WP_REST_Request $request ) {\n\t\t$rate = GCU_Plugin::instance()->repository()->consume_rate_limit( 'future-claim-revalidate', 60 );\n\t\tif ( is_wp_error( $rate ) ) { return $rate; }\n\t\t$idempotency = self::required_idempotency_key( $request );\n\t\tif ( is_wp_error( $idempotency ) ) { return $idempotency; }\n\t\t$key = sanitize_key( $request['claim_key'] );")
changed |= replace_exact(future_path, "\t\t$result = self::revalidate_claim( $key, $expected, $reason );\n\t\treturn is_wp_error( $result ) ? $result : self::no_store_response( $result );\n\t}", "\t\t$result = GCU_Plugin::instance()->repository()->run_idempotent_command( 'future_claim_revalidate', $idempotency, static function() use ( $key, $expected, $reason ) { return self::revalidate_claim( $key, $expected, $reason ); } );\n\t\treturn is_wp_error( $result ) ? $result : self::no_store_response( $result );\n\t}")
# Insert helper before public_response helper if available.
changed |= replace_exact(future_path, "\tprivate static function public_response( $data, $status = 200 ) {", "\tprivate static function required_idempotency_key( WP_REST_Request $request ) {\n\t\t$key = GCU_Hardening::bounded_text( sanitize_text_field( (string) $request->get_header( GCU_REST::IDEMPOTENCY_HEADER ) ), 191 );\n\t\treturn strlen( $key ) < 8 ? new WP_Error( 'gcu_idempotency_key_required', __( 'A stable idempotency key is required for this mutation.', 'global-clinic-usp-integration' ), array( 'status' => 400 ) ) : $key;\n\t}\n\n\tprivate static function public_response( $data, $status = 200 ) {")

# WordPress privacy exporter paginates Future reports too; no data beyond the first 200 can be stranded.
privacy_path = '14-global-clinic-usp-integration/includes/class-gcu-privacy.php'
export = """public function export_data($email,$page=1){global$wpdb;$page=max(1,absint($page));$offset=($page-1)*200;$data=array();$user=get_user_by('email',sanitize_email($email));if(!$user){return array('data'=>$data,'done'=>true);}$subject=$this->user_subject_hash($user->ID,false);$t=GCU_Install::tables();$rows=$subject?$wpdb->get_results($wpdb->prepare("SELECT event_id,funnel_stage,destination_key,source_value,medium_value,campaign_value,ref_value,occurred_at FROM {$t['events']} WHERE subject_hash=%s ORDER BY id ASC LIMIT 201 OFFSET %d",$subject,$offset),ARRAY_A):array();$event_more=is_array($rows)&&count($rows)>200;$rows=is_array($rows)?array_slice($rows,0,200):array();foreach($rows as$row){$fields=array();foreach($row as$k=>$v){$fields[]=array('name'=>sanitize_key($k),'value'=>(string)$v);}$data[]=array('group_id'=>'gcu-conversion-events','group_label'=>__('Global Clinic conversion events','global-clinic-usp-integration'),'item_id'=>'gcu-event-'.sanitize_text_field($row['event_id']),'data'=>$fields);}$report_more=false;if(class_exists('GCU_Future_Intelligence')){$ft=GCU_Future_Intelligence::tables();$actor=hash_hmac('sha256','u:'.absint($user->ID),wp_salt('auth'));$reports=$wpdb->get_results($wpdb->prepare("SELECT public_id,report_type,route_key,block_key,locale,reason_code,message,status,resolution,created_at,updated_at FROM {$ft['reports']} WHERE actor_hash=%s ORDER BY id ASC LIMIT 201 OFFSET %d",$actor,$offset),ARRAY_A);$report_more=is_array($reports)&&count($reports)>200;$reports=is_array($reports)?array_slice($reports,0,200):array();foreach($reports as$row){$fields=array();foreach($row as$k=>$v){$fields[]=array('name'=>sanitize_key($k),'value'=>(string)$v);}$data[]=array('group_id'=>'gcu-copy-quality-reports','group_label'=>__('Global Clinic copy-quality reports','global-clinic-usp-integration'),'item_id'=>'gcu-report-'.sanitize_text_field($row['public_id']),'data'=>$fields);}}return array('data'=>$data,'done'=>!$event_more&&!$report_more);}
"""
changed |= replace_between(privacy_path, 'public function export_data(', 'public function erase_data(', export)

print('third-review final corrective source changes:', 'applied' if changed else 'already applied')
