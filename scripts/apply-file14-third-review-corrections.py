#!/usr/bin/env python3
from pathlib import Path

ROOT = Path(__file__).resolve().parents[1]


def replace_exact(path, old, new):
    p = ROOT / path
    text = p.read_text(encoding="utf-8")
    if new in text:
        return False
    if old not in text:
        raise SystemExit(f"Expected source pattern missing: {path}: {old[:120]!r}")
    text = text.replace(old, new, 1)
    p.write_text(text, encoding="utf-8")
    return True


def replace_between(path, start, end, new):
    p = ROOT / path
    text = p.read_text(encoding="utf-8")
    if new in text:
        return False
    a = text.find(start)
    b = text.find(end, a + len(start))
    if a < 0 or b < 0:
        raise SystemExit(f"Expected bounded source region missing: {path}: {start!r} -> {end!r}")
    text = text[:a] + new + text[b:]
    p.write_text(text, encoding="utf-8")
    return True

changed = False

# 1) Base runtime readiness must apply to reads/background paths as well as mutations.
changed |= replace_exact(
    "14-global-clinic-usp-integration/includes/class-gcu-install.php",
    "public static function ready_for_mutation(){if(!get_option('gcu_enabled',1)){return new WP_Error('gcu_safe_mode',__('File 14 is in safe mode.','global-clinic-usp-integration'),array('status'=>503));}if(GCU_VERSION!==(string)get_option(self::VERSION_OPTION,'')||GCU_SCHEMA_VERSION!==(int)get_option(self::SCHEMA_OPTION,0)){return new WP_Error('gcu_upgrade_pending',__('File 14 requires a verified schema upgrade before writes can continue.','global-clinic-usp-integration'),array('status'=>503));}return true;}",
    "public static function ready_for_runtime(){if(!get_option('gcu_enabled',1)){return new WP_Error('gcu_safe_mode',__('File 14 is in safe mode.','global-clinic-usp-integration'),array('status'=>503));}if(GCU_VERSION!==(string)get_option(self::VERSION_OPTION,'')||GCU_SCHEMA_VERSION!==(int)get_option(self::SCHEMA_OPTION,0)){return new WP_Error('gcu_upgrade_pending',__('File 14 requires a verified schema upgrade before runtime can continue.','global-clinic-usp-integration'),array('status'=>503));}return true;}public static function ready_for_mutation(){return self::ready_for_runtime();}"
)

# 2) File-14-owned destination health must not claim readiness during a pending base upgrade.
changed |= replace_exact(
    "14-global-clinic-usp-integration/includes/class-gcu-contracts.php",
    "if('File 14'===$def['owner']){return array('key'=>$key,'owner'=>$def['owner'],'available'=>(bool)get_option('gcu_enabled',1),'url'=>GCU_Hardening::strict_same_origin_url($def['fallback_url']),'reason'=>get_option('gcu_enabled',1)?'ready':'module_disabled','contract'=>$def['contract']);}",
    "if('File 14'===$def['owner']){$ready=GCU_Install::ready_for_runtime();$available=!is_wp_error($ready);return array('key'=>$key,'owner'=>$def['owner'],'available'=>$available,'url'=>$available?GCU_Hardening::strict_same_origin_url($def['fallback_url']):'','reason'=>$available?'ready':(is_wp_error($ready)?sanitize_key($ready->get_error_code()):'module_unready'),'contract'=>$def['contract']);}"
)

# 3) Base REST routes: fail closed on runtime/version/schema state and never serve stale governed public data.
rest_path = "14-global-clinic-usp-integration/includes/class-gcu-rest.php"
changed |= replace_exact(
    rest_path,
    "public function blocks(WP_REST_Request$r){if(!get_option('gcu_enabled',1)){return new WP_Error('gcu_safe_mode',__('File 14 is temporarily unavailable.','global-clinic-usp-integration'),array('status'=>503));}$rows=GCU_Plugin::instance()->repository()->active_blocks(sanitize_key($r->get_param('slot')),GCU_Policy::sanitize_audience($r->get_param('audience')?:'all'),GCU_Policy::sanitize_locale($r->get_param('locale')?:GCU_Plugin::instance()->frontend()->current_locale()));return$this->cache_public_response(new WP_REST_Response(array('items'=>$rows,'count'=>count($rows),'version'=>1),200),300);}",
    "public function blocks(WP_REST_Request$r){$ready=GCU_Install::ready_for_runtime();if(is_wp_error($ready)){return$ready;}$rows=GCU_Plugin::instance()->repository()->active_blocks(sanitize_key($r->get_param('slot')),GCU_Policy::sanitize_audience($r->get_param('audience')?:'all'),GCU_Policy::sanitize_locale($r->get_param('locale')?:GCU_Plugin::instance()->frontend()->current_locale()));return$this->cache_public_response(new WP_REST_Response(array('items'=>$rows,'count'=>count($rows),'version'=>1),200),0);}"
)
changed |= replace_exact(
    rest_path,
    "public function destinations(){if(!get_option('gcu_enabled',1)){return new WP_Error('gcu_safe_mode',__('File 14 is temporarily unavailable.','global-clinic-usp-integration'),array('status'=>503));}return$this->cache_public_response(new WP_REST_Response(array('items'=>GCU_Plugin::instance()->contracts()->public_destination_health(),'version'=>1),200),60);}",
    "public function destinations(){$ready=GCU_Install::ready_for_runtime();if(is_wp_error($ready)){return$ready;}return$this->cache_public_response(new WP_REST_Response(array('items'=>GCU_Plugin::instance()->contracts()->public_destination_health(),'version'=>1),200),0);}"
)
changed |= replace_exact(
    rest_path,
    "public function can_issue_event_token(){if(!GCU_Privacy::measurement_allowed()){return new WP_Error('gcu_measurement_not_allowed',__('Measurement is not permitted in this context.','global-clinic-usp-integration'),array('status'=>403));}return true;}",
    "public function can_issue_event_token(){$ready=GCU_Install::ready_for_runtime();if(is_wp_error($ready)){return$ready;}if(!GCU_Privacy::measurement_allowed()){return new WP_Error('gcu_measurement_not_allowed',__('Measurement is not permitted in this context.','global-clinic-usp-integration'),array('status'=>403));}return true;}"
)
changed |= replace_exact(
    rest_path,
    "public function verify_event_token(WP_REST_Request$r){if(!GCU_Privacy::measurement_allowed()){return new WP_Error('gcu_measurement_not_allowed',__('Measurement is not permitted in this context.','global-clinic-usp-integration'),array('status'=>403));}return GCU_Plugin::instance()->repository()->consume_event_token(sanitize_text_field((string)$r->get_header('X-GCU-Event-Token')),'measurement');}",
    "public function verify_event_token(WP_REST_Request$r){$ready=GCU_Install::ready_for_runtime();if(is_wp_error($ready)){return$ready;}if(!GCU_Privacy::measurement_allowed()){return new WP_Error('gcu_measurement_not_allowed',__('Measurement is not permitted in this context.','global-clinic-usp-integration'),array('status'=>403));}return GCU_Plugin::instance()->repository()->consume_event_token(sanitize_text_field((string)$r->get_header('X-GCU-Event-Token')),'measurement');}"
)
changed |= replace_exact(
    rest_path,
    "private function cache_public_response(WP_REST_Response$r,$s){$s=max(0,min(600,absint($s)));$r->header('Cache-Control','public, max-age='.$s.', stale-while-revalidate=60');$r->header('ETag','\"'.hash('sha256',wp_json_encode($r->get_data())).'\"');$r->header('Vary','Accept-Language');return$r;}",
    "private function cache_public_response(WP_REST_Response$r,$s){$s=max(0,min(600,absint($s)));$r->header('Cache-Control','public, no-cache, max-age=0, must-revalidate');$r->header('ETag','\"'.hash('sha256',wp_json_encode($r->get_data())).'\"');$r->header('Vary','Accept-Language');return$r;}"
)

# 4) Front-end routes/shortcodes must use the same runtime truth and emit revalidation headers.
front_path = "14-global-clinic-usp-integration/includes/class-gcu-frontend.php"
changed |= replace_exact(
    front_path,
    "public function hooks(){add_action('init',array($this,'rewrites'));add_filter('query_vars',array($this,'query_vars'));add_action('template_redirect',array($this,'route_actions'),1);add_filter('template_include',array($this,'template'),99);add_action('wp_enqueue_scripts',array($this,'assets'));add_action('wp_head',array($this,'head_meta'),1);add_shortcode('gcu_global_clinic',array($this,'shortcode_global_clinic'));add_shortcode('gcu_how_it_works',array($this,'shortcode_how_it_works'));add_shortcode('gcu_block',array($this,'shortcode_block'));}",
    "public function hooks(){add_action('init',array($this,'rewrites'));add_filter('query_vars',array($this,'query_vars'));add_action('template_redirect',array($this,'route_actions'),1);add_action('send_headers',array($this,'cache_headers'),20,0);add_filter('template_include',array($this,'template'),99);add_action('wp_enqueue_scripts',array($this,'assets'));add_action('wp_head',array($this,'head_meta'),1);add_shortcode('gcu_global_clinic',array($this,'shortcode_global_clinic'));add_shortcode('gcu_how_it_works',array($this,'shortcode_how_it_works'));add_shortcode('gcu_block',array($this,'shortcode_block'));}"
)
changed |= replace_exact(
    front_path,
    "public function route_actions(){$this->current_route=sanitize_key((string)get_query_var('gcu_route'));if(!$this->current_route){return;}if(!get_option('gcu_enabled',1)){$this->degraded_destination='module_disabled';status_header(503);return;}$m=array('find_doctor'=>'doctor_directory','start_clinic'=>'doctor_onboarding');",
    "public function route_actions(){$this->current_route=sanitize_key((string)get_query_var('gcu_route'));if(!$this->current_route){return;}$ready=GCU_Install::ready_for_runtime();if(is_wp_error($ready)){$this->degraded_destination=sanitize_key($ready->get_error_code());status_header(503);return;}$m=array('find_doctor'=>'doctor_directory','start_clinic'=>'doctor_onboarding');"
)
changed |= replace_exact(
    front_path,
    "public function template($t){if(!$this->current_route){$this->current_route=sanitize_key((string)get_query_var('gcu_route'));}return$this->current_route?GCU_DIR.'templates/public-page.php':$t;}",
    "public function cache_headers(){$r=sanitize_key((string)get_query_var('gcu_route'));if($r){header('Cache-Control: public, no-cache, max-age=0, must-revalidate',true);header('Pragma: no-cache',true);}}\npublic function template($t){if(!$this->current_route){$this->current_route=sanitize_key((string)get_query_var('gcu_route'));}return$this->current_route?GCU_DIR.'templates/public-page.php':$t;}"
)
changed |= replace_exact(
    front_path,
    "if(get_option('gcu_enabled',1)&&GCU_Privacy::measurement_allowed()&&!GCU_Privacy::low_bandwidth_requested())",
    "if(!is_wp_error(GCU_Install::ready_for_runtime())&&GCU_Privacy::measurement_allowed()&&!GCU_Privacy::low_bandwidth_requested())"
)
changed |= replace_exact(
    front_path,
    "public function render_route(){$r=$this->current_route?$this->current_route:sanitize_key((string)get_query_var('gcu_route'));if($this->degraded_destination||!get_option('gcu_enabled',1)){return$this->render_degraded($this->degraded_destination?$this->degraded_destination:'module_disabled');}return'how_it_works'===$r?$this->render_how_it_works():$this->render_global_clinic();}",
    "public function render_route(){$r=$this->current_route?$this->current_route:sanitize_key((string)get_query_var('gcu_route'));$ready=GCU_Install::ready_for_runtime();if($this->degraded_destination||is_wp_error($ready)){return$this->render_degraded($this->degraded_destination?$this->degraded_destination:(is_wp_error($ready)?sanitize_key($ready->get_error_code()):'module_unready'));}return'how_it_works'===$r?$this->render_how_it_works():$this->render_global_clinic();}"
)
changed |= replace_exact(
    front_path,
    "public function shortcode_global_clinic(){return get_option('gcu_enabled',1)?$this->render_global_clinic():$this->render_degraded('module_disabled');}public function shortcode_how_it_works(){return get_option('gcu_enabled',1)?$this->render_how_it_works():$this->render_degraded('module_disabled');}\npublic function shortcode_block($a){if(!get_option('gcu_enabled',1)){return$this->render_degraded('module_disabled');}",
    "public function shortcode_global_clinic(){$ready=GCU_Install::ready_for_runtime();return is_wp_error($ready)?$this->render_degraded(sanitize_key($ready->get_error_code())):$this->render_global_clinic();}public function shortcode_how_it_works(){$ready=GCU_Install::ready_for_runtime();return is_wp_error($ready)?$this->render_degraded(sanitize_key($ready->get_error_code())):$this->render_how_it_works();}\npublic function shortcode_block($a){$ready=GCU_Install::ready_for_runtime();if(is_wp_error($ready)){return$this->render_degraded(sanitize_key($ready->get_error_code()));}"
)

# 5) WordPress privacy export/erase must never attach or delete the operator/request browser's guest cookies for an arbitrary email subject.
privacy_path = "14-global-clinic-usp-integration/includes/class-gcu-privacy.php"
new_export = """public function export_data($email,$page=1){global$wpdb;$page=max(1,absint($page));$offset=($page-1)*200;$data=array();$user=get_user_by('email',sanitize_email($email));if(!$user){return array('data'=>$data,'done'=>true);}$subject=$this->user_subject_hash($user->ID,false);if(!$subject){return array('data'=>$data,'done'=>true);}$t=GCU_Install::tables();$rows=$wpdb->get_results($wpdb->prepare(\"SELECT event_id,funnel_stage,destination_key,source_value,medium_value,campaign_value,ref_value,occurred_at FROM {$t['events']} WHERE subject_hash=%s ORDER BY id ASC LIMIT 201 OFFSET %d\",$subject,$offset),ARRAY_A);$more=is_array($rows)&&count($rows)>200;$rows=is_array($rows)?array_slice($rows,0,200):array();foreach($rows as$row){$fields=array();foreach($row as$k=>$v){$fields[]=array('name'=>sanitize_key($k),'value'=>(string)$v);}$data[]=array('group_id'=>'gcu-conversion-events','group_label'=>__('Global Clinic conversion events','global-clinic-usp-integration'),'item_id'=>'gcu-event-'.sanitize_text_field($row['event_id']),'data'=>$fields);}return array('data'=>$data,'done'=>!$more);}
"""
changed |= replace_between(privacy_path, "public function export_data(", "public function erase_data(", new_export)
new_erase = """public function erase_data($email,$page=1){global$wpdb;$user=get_user_by('email',sanitize_email($email));$removed=false;$retained=false;$messages=array();if(!$user){return array('items_removed'=>false,'items_retained'=>false,'messages'=>$messages,'done'=>true);}$subject=$this->user_subject_hash($user->ID,false);if($subject){$t=GCU_Install::tables();$deleted=$wpdb->query($wpdb->prepare(\"DELETE FROM {$t['events']} WHERE subject_hash=%s LIMIT 500\",$subject));if(false===$deleted){$retained=true;$messages[]=__('Some File 14 conversion events could not be erased in this pass.','global-clinic-usp-integration');return array('items_removed'=>false,'items_retained'=>true,'messages'=>$messages,'done'=>false);}$removed=$deleted>0;$remaining=(int)$wpdb->get_var($wpdb->prepare(\"SELECT COUNT(*) FROM {$t['events']} WHERE subject_hash=%s\",$subject));if($remaining>0){return array('items_removed'=>$removed,'items_retained'=>true,'messages'=>$messages,'done'=>false);}}$meta_removed=delete_user_meta($user->ID,self::USER_SUBJECT_META);$removed=$removed||$meta_removed;return array('items_removed'=>$removed,'items_retained'=>$retained,'messages'=>$messages,'done'=>true);}
"""
changed |= replace_between(privacy_path, "public function erase_data(", "public function capture_attribution(", new_erase)

# 6) Measurement and inbound event storage: repository-level readiness defense in depth.
repo_path = "14-global-clinic-usp-integration/includes/class-gcu-repository.php"
for old, new in [
    ("public function issue_event_token($purpose='measurement'){if(!GCU_Privacy::measurement_allowed()", "public function issue_event_token($purpose='measurement'){$ready=GCU_Install::ready_for_mutation();if(is_wp_error($ready)){return$ready;}if(!GCU_Privacy::measurement_allowed()"),
    ("public function consume_event_token($token,$purpose='measurement'){$token=", "public function consume_event_token($token,$purpose='measurement'){$ready=GCU_Install::ready_for_mutation();if(is_wp_error($ready)){return$ready;}$token="),
    ("public function consume_rate_limit($scope,$limit=60){global$wpdb;", "public function consume_rate_limit($scope,$limit=60){$ready=GCU_Install::ready_for_mutation();if(is_wp_error($ready)){return$ready;}global$wpdb;"),
    ("public function record_event(array$d){if(!GCU_Privacy::measurement_allowed()", "public function record_event(array$d){$ready=GCU_Install::ready_for_mutation();if(is_wp_error($ready)){return$ready;}if(!GCU_Privacy::measurement_allowed()"),
    ("public function accept_inbound_event($name,array$payload){$v=", "public function accept_inbound_event($name,array$payload){$ready=GCU_Install::ready_for_mutation();if(is_wp_error($ready)){return false;}$v=")
]:
    changed |= replace_exact(repo_path, old, new)

# 7) Base scheduled workers must skip writes when base runtime is unready.
obs_path = "14-global-clinic-usp-integration/includes/class-gcu-observability.php"
changed |= replace_exact(
    obs_path,
    "public function process_outbox(){return GCU_Plugin::instance()->repository()->dispatch_outbox('',50);}public function process_inbox(){return GCU_Plugin::instance()->repository()->process_inbox('',50);}public function lifecycle_cleanup(){return GCU_Plugin::instance()->repository()->cleanup_lifecycle();}",
    "public function process_outbox(){$ready=GCU_Install::ready_for_runtime();return is_wp_error($ready)?0:GCU_Plugin::instance()->repository()->dispatch_outbox('',50);}public function process_inbox(){$ready=GCU_Install::ready_for_runtime();return is_wp_error($ready)?0:GCU_Plugin::instance()->repository()->process_inbox('',50);}public function lifecycle_cleanup(){$ready=GCU_Install::ready_for_runtime();return is_wp_error($ready)?array('skipped'=>'runtime_unready'):GCU_Plugin::instance()->repository()->cleanup_lifecycle();}"
)

# 8) Future runtime, background jobs, admin/report paths and public cache truth.
future_path = "14-global-clinic-usp-integration/includes/class-gcu-future-intelligence.php"
changed |= replace_exact(
    future_path,
    "\tpublic static function runtime_ready() {\n\t\tif ( ! get_option( 'gcu_enabled', 1 ) ) {\n\t\t\treturn new WP_Error( 'gcu_safe_mode', __( 'File 14 is temporarily unavailable.', 'global-clinic-usp-integration' ), array( 'status' => 503 ) );\n\t\t}\n\t\tif ( self::SCHEMA_VERSION !== (int) get_option( self::SCHEMA_OPTION, 0 ) || get_option( self::SAFE_MODE_OPTION, 0 ) ) {",
    "\tpublic static function runtime_ready() {\n\t\t$base_ready = GCU_Install::ready_for_runtime();\n\t\tif ( is_wp_error( $base_ready ) ) {\n\t\t\treturn $base_ready;\n\t\t}\n\t\tif ( self::SCHEMA_VERSION !== (int) get_option( self::SCHEMA_OPTION, 0 ) || get_option( self::SAFE_MODE_OPTION, 0 ) ) {"
)
changed |= replace_exact(
    future_path,
    "\tpublic static function daily_governance() {\n\t\tself::claim_freshness_sentinel();",
    "\tpublic static function daily_governance() {\n\t\t$ready = self::runtime_ready();\n\t\tif ( is_wp_error( $ready ) ) { return $ready; }\n\t\tself::claim_freshness_sentinel();"
)
changed |= replace_exact(
    future_path,
    "\tpublic static function hourly_intelligence() {\n\t\tself::anomaly_detector();",
    "\tpublic static function hourly_intelligence() {\n\t\t$ready = self::runtime_ready();\n\t\tif ( is_wp_error( $ready ) ) { return $ready; }\n\t\tself::anomaly_detector();"
)
changed |= replace_exact(
    future_path,
    "\tpublic static function business_policy_changed() {\n\t\tself::parity_status();",
    "\tpublic static function business_policy_changed() {\n\t\t$ready = self::runtime_ready();\n\t\tif ( is_wp_error( $ready ) ) { return $ready; }\n\t\tself::parity_status();"
)
changed |= replace_exact(
    future_path,
    "\tpublic static function cleanup() {\n\t\tglobal $wpdb;",
    "\tpublic static function cleanup() {\n\t\t$ready = self::runtime_ready();\n\t\tif ( is_wp_error( $ready ) ) { return array( 'skipped' => 'runtime_unready' ); }\n\t\tglobal $wpdb;"
)
changed |= replace_exact(
    future_path,
    "\tpublic static function create_report( array $data ) {\n\t\t$rate =",
    "\tpublic static function create_report( array $data ) {\n\t\t$ready = self::runtime_ready();\n\t\tif ( is_wp_error( $ready ) ) { return $ready; }\n\t\t$rate ="
)
changed |= replace_exact(
    future_path,
    "\tpublic static function resolve_report_record( $id, $expected, $status, $resolution ) {\n\t\tif ( ! in_array( $status, array( 'reviewing', 'resolved', 'rejected' ), true ) ) {",
    "\tpublic static function resolve_report_record( $id, $expected, $status, $resolution ) {\n\t\t$ready = self::runtime_ready();\n\t\tif ( is_wp_error( $ready ) ) { return $ready; }\n\t\tif ( ! in_array( $status, array( 'reviewing', 'resolved', 'rejected' ), true ) ) {"
)
changed |= replace_exact(
    future_path,
    "\tpublic static function enqueue_assets() {\n\t\t$route =",
    "\tpublic static function enqueue_assets() {\n\t\t$ready = self::runtime_ready();\n\t\tif ( is_wp_error( $ready ) ) { return; }\n\t\t$route ="
)
changed |= replace_exact(
    future_path,
    "\tpublic static function admin_page() {\n\t\tif ( ! self::can_system_check() ) {\n\t\t\twp_die( esc_html__( 'You are not authorized to view this page.', 'global-clinic-usp-integration' ) );\n\t\t}\n\t\t$catalog =",
    "\tpublic static function admin_page() {\n\t\tif ( ! self::can_system_check() ) {\n\t\t\twp_die( esc_html__( 'You are not authorized to view this page.', 'global-clinic-usp-integration' ) );\n\t\t}\n\t\t$ready = self::runtime_ready();\n\t\tif ( is_wp_error( $ready ) ) { echo '<div class=\"notice notice-error\"><p>' . esc_html( $ready->get_error_message() ) . '</p></div>'; return; }\n\t\t$catalog ="
)
changed |= replace_exact(
    future_path,
    "\t\t$response->header( 'Cache-Control', 'public, max-age=60, stale-while-revalidate=60' );",
    "\t\t$response->header( 'Cache-Control', 'public, no-cache, max-age=0, must-revalidate' );"
)

# 9) The independent hardening gate must cover every base namespace route except privileged health diagnostics.
hard_path = "14-global-clinic-usp-integration/includes/class-gcu-review80-hardening.php"
changed |= replace_exact(
    hard_path,
    "\t\t$route = $request->get_route();\n\t\tif ( 0 === strpos( $route, '/gcu/v1/future/' ) ) {",
    "\t\t$route = $request->get_route();\n\t\tif ( 0 === strpos( $route, '/gcu/v1/' ) && '/gcu/v1/health' !== $route ) {\n\t\t\t$base_ready = GCU_Install::ready_for_runtime();\n\t\t\tif ( is_wp_error( $base_ready ) ) { return $base_ready; }\n\t\t}\n\t\tif ( 0 === strpos( $route, '/gcu/v1/future/' ) ) {"
)

print("third-review corrective source changes:", "applied" if changed else "already applied")
