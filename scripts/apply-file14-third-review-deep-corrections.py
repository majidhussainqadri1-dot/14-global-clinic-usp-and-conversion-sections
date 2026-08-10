#!/usr/bin/env python3
from pathlib import Path
import re

ROOT = Path(__file__).resolve().parents[1]


def replace_exact(path, old, new):
    p = ROOT / path
    text = p.read_text(encoding='utf-8')
    if new in text:
        return False
    if old not in text:
        raise SystemExit(f'Expected source pattern missing: {path}: {old[:140]!r}')
    p.write_text(text.replace(old, new, 1), encoding='utf-8')
    return True


def replace_between(path, start, end, new):
    p = ROOT / path
    text = p.read_text(encoding='utf-8')
    a = text.find(start)
    b = text.find(end, a + len(start))
    if a < 0 or b < 0:
        raise SystemExit(f'Expected bounded source region missing: {path}: {start!r} -> {end!r}')
    old = text[a:b]
    if old == new:
        return False
    p.write_text(text[:a] + new + text[b:], encoding='utf-8')
    return True

changed = False

# Review: authorization adapters may restrict native capabilities, never elevate a missing native capability.
changed |= replace_exact(
    '14-global-clinic-usp-integration/includes/class-gcu-capabilities.php',
    "\tpublic static function can( $capability, $object = null, $purpose = '' ) {\n\t\t$allowed = current_user_can( $capability );\n\t\treturn (bool) apply_filters( 'gcu_authorize', $allowed, $capability, $object, sanitize_key( $purpose ) );\n\t}",
    "\tpublic static function can( $capability, $object = null, $purpose = '' ) {\n\t\t$allowed = current_user_can( $capability );\n\t\tif ( ! $allowed ) { return false; }\n\t\treturn (bool) apply_filters( 'gcu_authorize', true, $capability, $object, sanitize_key( $purpose ) );\n\t}"
)

# Review: canonical claims are deterministic American-English source truth; locale rendering happens separately.
policy_path = '14-global-clinic-usp-integration/includes/class-gcu-policy.php'
p = ROOT / policy_path
text = p.read_text(encoding='utf-8')
claim_strings = [
    'The platform charges 0% commission on approved clinic transactions.',
    'All currently approved core platform features are available in one free tier.',
    'Voluntary support is optional and does not purchase ranking, visibility, verification or basic service.',
    'Doctor access is activated only after identity, professional evidence, duplicate and risk checks, and the required review are completed.',
    'Starting an application does not guarantee verification or activation.',
    'This platform is not an emergency service. Seek immediate local emergency care for urgent or life-threatening symptoms.',
    'Verification is not an endorsement or guarantee of a cure, income or outcome.',
    'Any doctor fee is shown and handled by the canonical clinic or approved provider flow; File 14 does not collect, alter or guarantee payment.',
]
for s in claim_strings:
    old = "__( '" + s + "', 'global-clinic-usp-integration' )"
    new = "'" + s.replace("'", "\\'") + "'"
    if old in text:
        text = text.replace(old, new, 1)
        changed = True
p.write_text(text, encoding='utf-8')

# Review: public data layer fails closed on stale content and stale/missing linked claims.
repo_path = '14-global-clinic-usp-integration/includes/class-gcu-repository.php'
changed |= replace_exact(
    repo_path,
    "$w=\"b.status='active' AND p.status='active' AND b.locale=%s\";",
    "$w=\"b.status='active' AND p.status='active' AND b.locale=%s AND (b.review_due_at IS NULL OR b.review_due_at>UTC_TIMESTAMP())\";"
)
changed |= replace_exact(
    repo_path,
    "foreach($r as&$row){$row['claim_keys']=json_decode((string)$row['claim_keys'],true);if(!is_array($row['claim_keys'])){$row['claim_keys']=array();}}unset($row);return$r;}",
    "${'all_claims'}=array();foreach($r as&$row){$row['claim_keys']=json_decode((string)$row['claim_keys'],true);if(!is_array($row['claim_keys'])){$row['claim_keys']=array();}${'all_claims'}=array_merge(${'all_claims'},$row['claim_keys']);}unset($row);$valid=$this->public_claims(${'all_claims'});$r=array_values(array_filter($r,static function($row)use($valid){foreach($row['claim_keys']as$key){if(!isset($valid[$key])){return false;}}return true;}));return$r;}"
)
changed |= replace_exact(
    repo_path,
    "AND status='active' AND is_public=1 AND effective_at<=UTC_TIMESTAMP() AND (expires_at IS NULL OR expires_at>UTC_TIMESTAMP()) ORDER BY claim_key ASC",
    "AND status='active' AND is_public=1 AND effective_at<=UTC_TIMESTAMP() AND (review_due_at IS NULL OR review_due_at>UTC_TIMESTAMP()) AND (expires_at IS NULL OR expires_at>UTC_TIMESTAMP()) ORDER BY claim_key ASC"
)
changed |= replace_exact(
    repo_path,
    "if('copy'===$machine&&'founder_approved'===$target){$u['approved_by']=get_current_user_id();$u['approved_at']=current_time('mysql',true);}",
    "if('copy'===$machine&&'founder_approved'===$target){$u['approved_by']=get_current_user_id();$u['approved_at']=current_time('mysql',true);$u['review_due_at']=gmdate('Y-m-d H:i:s',time()+GCU_Policy::COPY_REVIEW_DAYS*DAY_IN_SECONDS);}"
)

# Review: install/upgrade lock is also a runtime gate; controlled repair reuses the locked upgrade path.
install_path = '14-global-clinic-usp-integration/includes/class-gcu-install.php'
changed |= replace_exact(
    install_path,
    "public static function ready_for_runtime(){if(!get_option('gcu_enabled',1)){return new WP_Error('gcu_safe_mode',__('File 14 is in safe mode.','global-clinic-usp-integration'),array('status'=>503));}",
    "public static function ready_for_runtime(){if(get_option(self::LOCK_OPTION,false)){return new WP_Error('gcu_install_in_progress',__('File 14 install, upgrade or recovery is in progress.','global-clinic-usp-integration'),array('status'=>503));}if(!get_option('gcu_enabled',1)){return new WP_Error('gcu_safe_mode',__('File 14 is in safe mode.','global-clinic-usp-integration'),array('status'=>503));}"
)
# Add a public, serialized repair operation immediately before maybe_upgrade.
changed |= replace_exact(
    install_path,
    "\tpublic static function maybe_upgrade(){",
    "\tpublic static function safe_repair(){\n\t\t$result=self::install_or_upgrade(false);\n\t\tif(is_wp_error($result)){return $result;}\n\t\treturn self::ensure_future_schema();\n\t}\n\tpublic static function maybe_upgrade(){"
)

# Review: safe repair must not bypass install serialization; leaving safe mode requires Future schema truth too.
admin_path = '14-global-clinic-usp-integration/includes/class-gcu-admin.php'
changed |= replace_between(
    admin_path,
    'public function repair(){',
    'public function rollback(){',
    "public function repair(){$this->authorize('gcu_repair',GCU_Capabilities::SYSTEM_CHECK);$x=GCU_Install::safe_repair();if(is_wp_error($x)){wp_die(esc_html($x->get_error_message()));}GCU_Plugin::instance()->repository()->audit('safe_repair','module','file14','operations','Owner-scoped repair requested',array(),array('schema'=>GCU_SCHEMA_VERSION,'future_schema'=>GCU_FUTURE_SCHEMA_VERSION));$this->redirect('safe-repair-completed');}\n"
)
changed |= replace_exact(
    admin_path,
    "if($enabled){$schema=GCU_Install::verify_schema();$current=GCU_VERSION===(string)get_option(GCU_Install::VERSION_OPTION,'')&&GCU_SCHEMA_VERSION===(int)get_option(GCU_Install::SCHEMA_OPTION,0);if(is_wp_error($schema)||!$current){wp_die(esc_html__('Safe mode cannot be left until the current File 14 code and schema are verified.','global-clinic-usp-integration'));}}",
    "if($enabled){$schema=GCU_Install::verify_schema();$future=GCU_Future_Intelligence::ensure_schema();$current=GCU_VERSION===(string)get_option(GCU_Install::VERSION_OPTION,'')&&GCU_SCHEMA_VERSION===(int)get_option(GCU_Install::SCHEMA_OPTION,0)&&GCU_FUTURE_SCHEMA_VERSION===(int)get_option(GCU_Future_Intelligence::SCHEMA_OPTION,0);if(is_wp_error($schema)||is_wp_error($future)||!$current){wp_die(esc_html__('Safe mode cannot be left until the current File 14 base and Future schemas are verified.','global-clinic-usp-integration'));}}"
)

# Review: public readiness calculation receives a bounded anti-abuse rate limit.
future_path = '14-global-clinic-usp-integration/includes/class-gcu-future-intelligence.php'
changed |= replace_exact(
    future_path,
    "\tpublic static function rest_readiness( WP_REST_Request $request ) {\n\t\t$data = $request->get_json_params();",
    "\tpublic static function rest_readiness( WP_REST_Request $request ) {\n\t\t$rate = GCU_Plugin::instance()->repository()->consume_rate_limit( 'future-readiness', 60 );\n\t\tif ( is_wp_error( $rate ) ) { return $rate; }\n\t\t$data = $request->get_json_params();"
)
# Multilingual sensitive-text rejection for public reports.
changed |= replace_exact(
    future_path,
    "return (bool) preg_match( '/[A-Z0-9._%+\\-]+@[A-Z0-9.\\-]+\\.[A-Z]{2,}|\\+?\\d[\\d\\s\\-]{6,}\\d|\\b(?:CNIC|passport|diagnosis|prescription|patient id)\\b/i', $message );",
    "return (bool) preg_match( '/[A-Z0-9._%+\\-]+@[A-Z0-9.\\-]+\\.[A-Z]{2,}|\\+?\\d[\\d\\s\\-]{6,}\\d|\\b(?:CNIC|NICOP|passport|diagnosis|prescription|patient\\s*id|medical\\s*record|case\\s*(?:no|number))\\b|(?:شناختی\\s*کارڈ|پاسپورٹ|مریض|تشخیص|نسخہ|میڈیکل\\s*ریکارڈ|فون|موبائل|ای\\s*میل)|(?:هوية|جواز\\s*السفر|مريض|تشخيص|وصفة|سجل\\s*طبي|هاتف|جوال|بريد\\s*إلكتروني)/iu', $message );"
)

# Review: scenario notes are internal-only; only explicitly governed public record types can be public.
changed |= replace_exact(
    '14-global-clinic-usp-integration/includes/class-gcu-future-guards.php',
    "\t\tif ( 'ai_draft' === $type && ( 'active' === $status || $public ) ) {\n\t\t\treturn new WP_Error( 'gcu_future_ai_draft_cannot_publish', __( 'AI Ethical Copy Assistant output is draft-only and can never auto-publish.', 'global-clinic-usp-integration' ), array( 'status' => 409 ) );\n\t\t}\n\t\treturn $response;",
    "\t\tif ( 'ai_draft' === $type && ( 'active' === $status || $public ) ) {\n\t\t\treturn new WP_Error( 'gcu_future_ai_draft_cannot_publish', __( 'AI Ethical Copy Assistant output is draft-only and can never auto-publish.', 'global-clinic-usp-integration' ), array( 'status' => 409 ) );\n\t\t}\n\t\tif ( 'scenario_note' === $type && $public ) {\n\t\t\treturn new WP_Error( 'gcu_future_scenario_note_private', __( 'Scenario notes are internal governance records and cannot be published.', 'global-clinic-usp-integration' ), array( 'status' => 409 ) );\n\t\t}\n\t\treturn $response;"
)

# Review: FAQ aggregate sensitive-text detector has parity with three approved locales.
review_path = '14-global-clinic-usp-integration/includes/class-gcu-review80-hardening.php'
changed |= replace_exact(
    review_path,
    "return (bool) preg_match( '/[A-Z0-9._%+\\-]+@[A-Z0-9.\\-]+\\.[A-Z]{2,}|\\+?\\d[\\d\\s\\-]{6,}\\d|\\b(?:CNIC|NICOP|passport|patient\\s*id|medical\\s*record|prescription\\s*(?:no|number)|case\\s*(?:no|number))\\b/i', $question );",
    "return (bool) preg_match( '/[A-Z0-9._%+\\-]+@[A-Z0-9.\\-]+\\.[A-Z]{2,}|\\+?\\d[\\d\\s\\-]{6,}\\d|\\b(?:CNIC|NICOP|passport|patient\\s*id|medical\\s*record|prescription\\s*(?:no|number)|case\\s*(?:no|number))\\b|(?:شناختی\\s*کارڈ|پاسپورٹ|مریض|تشخیص|نسخہ|میڈیکل\\s*ریکارڈ|فون|موبائل|ای\\s*میل)|(?:هوية|جواز\\s*السفر|مريض|تشخيص|وصفة|سجل\\s*طبي|هاتف|جوال|بريد\\s*إلكتروني)/iu', $question );"
)

# Review: WordPress personal-data tools cover pseudonymous logged-in copy reports without deleting the governance report itself.
privacy_path = '14-global-clinic-usp-integration/includes/class-gcu-privacy.php'
new_export = """public function export_data($email,$page=1){global$wpdb;$page=max(1,absint($page));$offset=($page-1)*200;$data=array();$user=get_user_by('email',sanitize_email($email));if(!$user){return array('data'=>$data,'done'=>true);}$subject=$this->user_subject_hash($user->ID,false);$t=GCU_Install::tables();$rows=$subject?$wpdb->get_results($wpdb->prepare(\"SELECT event_id,funnel_stage,destination_key,source_value,medium_value,campaign_value,ref_value,occurred_at FROM {$t['events']} WHERE subject_hash=%s ORDER BY id ASC LIMIT 201 OFFSET %d\",$subject,$offset),ARRAY_A):array();$more=is_array($rows)&&count($rows)>200;$rows=is_array($rows)?array_slice($rows,0,200):array();foreach($rows as$row){$fields=array();foreach($row as$k=>$v){$fields[]=array('name'=>sanitize_key($k),'value'=>(string)$v);}$data[]=array('group_id'=>'gcu-conversion-events','group_label'=>__('Global Clinic conversion events','global-clinic-usp-integration'),'item_id'=>'gcu-event-'.sanitize_text_field($row['event_id']),'data'=>$fields);}if(1===$page&&class_exists('GCU_Future_Intelligence')){$ft=GCU_Future_Intelligence::tables();$actor=hash_hmac('sha256','u:'.absint($user->ID),wp_salt('auth'));$reports=$wpdb->get_results($wpdb->prepare(\"SELECT public_id,report_type,route_key,block_key,locale,reason_code,message,status,resolution,created_at,updated_at FROM {$ft['reports']} WHERE actor_hash=%s ORDER BY id ASC LIMIT 200\",$actor),ARRAY_A);foreach(is_array($reports)?$reports:array()as$row){$fields=array();foreach($row as$k=>$v){$fields[]=array('name'=>sanitize_key($k),'value'=>(string)$v);}$data[]=array('group_id'=>'gcu-copy-quality-reports','group_label'=>__('Global Clinic copy-quality reports','global-clinic-usp-integration'),'item_id'=>'gcu-report-'.sanitize_text_field($row['public_id']),'data'=>$fields);}}return array('data'=>$data,'done'=>!$more);}
"""
changed |= replace_between(privacy_path, 'public function export_data(', 'public function erase_data(', new_export)
new_erase = """public function erase_data($email,$page=1){global$wpdb;$user=get_user_by('email',sanitize_email($email));$removed=false;$retained=false;$messages=array();if(!$user){return array('items_removed'=>false,'items_retained'=>false,'messages'=>$messages,'done'=>true);}$subject=$this->user_subject_hash($user->ID,false);if($subject){$t=GCU_Install::tables();$deleted=$wpdb->query($wpdb->prepare(\"DELETE FROM {$t['events']} WHERE subject_hash=%s LIMIT 500\",$subject));if(false===$deleted){$messages[]=__('Some File 14 conversion events could not be erased in this pass.','global-clinic-usp-integration');return array('items_removed'=>false,'items_retained'=>true,'messages'=>$messages,'done'=>false);}$removed=$deleted>0;$remaining=(int)$wpdb->get_var($wpdb->prepare(\"SELECT COUNT(*) FROM {$t['events']} WHERE subject_hash=%s\",$subject));if($remaining>0){return array('items_removed'=>$removed,'items_retained'=>true,'messages'=>$messages,'done'=>false);}}if(class_exists('GCU_Future_Intelligence')){$ft=GCU_Future_Intelligence::tables();$actor=hash_hmac('sha256','u:'.absint($user->ID),wp_salt('auth'));$anon=$wpdb->query($wpdb->prepare(\"UPDATE {$ft['reports']} SET actor_hash=NULL WHERE actor_hash=%s LIMIT 500\",$actor));if(false===$anon){$messages[]=__('Some File 14 report attribution could not be anonymized in this pass.','global-clinic-usp-integration');return array('items_removed'=>$removed,'items_retained'=>true,'messages'=>$messages,'done'=>false);}$removed=$removed||$anon>0;$remaining_reports=(int)$wpdb->get_var($wpdb->prepare(\"SELECT COUNT(*) FROM {$ft['reports']} WHERE actor_hash=%s\",$actor));if($remaining_reports>0){return array('items_removed'=>$removed,'items_retained'=>true,'messages'=>$messages,'done'=>false);}}$meta_removed=delete_user_meta($user->ID,self::USER_SUBJECT_META);$removed=$removed||$meta_removed;return array('items_removed'=>$removed,'items_retained'=>$retained,'messages'=>$messages,'done'=>true);}
"""
changed |= replace_between(privacy_path, 'public function erase_data(', 'public function capture_attribution(', new_erase)

print('third-review deeper corrective source changes:', 'applied' if changed else 'already applied')
