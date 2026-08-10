#!/usr/bin/env python3
"""Apply the second File 14 eighty-pass corrective review delta.

This script is intentionally exact-match based: if the reviewed baseline has moved,
it fails instead of silently applying a patch to an unverified source shape.
"""
from pathlib import Path

ROOT = Path(__file__).resolve().parents[1]
P = ROOT / "14-global-clinic-usp-integration"


def replace_exact(path: Path, old: str, new: str) -> None:
    text = path.read_text(encoding="utf-8")
    if old not in text:
        raise SystemExit(f"Reviewed baseline drift: replacement anchor missing in {path}")
    path.write_text(text.replace(old, new), encoding="utf-8")


# 1. Public destination-health DTO: never expose internal owner/contract/freshness metadata.
contracts = P / "includes/class-gcu-contracts.php"
replace_exact(
    contracts,
    "public function all_destination_health(){$out=array();foreach(array_keys($this->destination_registry())as$key){$out[$key]=$this->destination($key);}return$out;}",
    "public function all_destination_health(){$out=array();foreach(array_keys($this->destination_registry())as$key){$out[$key]=$this->destination($key);}return$out;}\n"
    "public function public_destination($key){$d=$this->destination($key);return array('key'=>isset($d['key'])?sanitize_key($d['key']):sanitize_key($key),'available'=>!empty($d['available']),'url'=>!empty($d['url'])?GCU_Hardening::strict_same_origin_url($d['url']):'','reason'=>isset($d['reason'])?sanitize_key($d['reason']):'unavailable');}\n"
    "public function public_destination_health(){$out=array();foreach(array_keys($this->destination_registry())as$key){$out[$key]=$this->public_destination($key);}return$out;}",
)
rest = P / "includes/class-gcu-rest.php"
replace_exact(
    rest,
    "public function destinations(){return$this->cache_public_response(new WP_REST_Response(array('items'=>GCU_Plugin::instance()->contracts()->all_destination_health(),'version'=>1),200),60);}",
    "public function destinations(){if(!get_option('gcu_enabled',1)){return new WP_Error('gcu_safe_mode',__('File 14 is temporarily unavailable.','global-clinic-usp-integration'),array('status'=>503));}return$this->cache_public_response(new WP_REST_Response(array('items'=>GCU_Plugin::instance()->contracts()->public_destination_health(),'version'=>1),200),60);}",
)

# 2. Future schema lifecycle: no dbDelta/SHOW TABLE work on every request; serialize real upgrades.
future = P / "includes/class-gcu-future-intelligence.php"
replace_exact(future, "\t\tself::ensure_schema();\n\t\tself::schedule();", "\t\tself::schedule();")
old_ensure = '''\tpublic static function ensure_schema() {
\t\tif ( self::SCHEMA_VERSION === (int) get_option( self::SCHEMA_OPTION, 0 ) ) {
\t\t\t$verified = self::verify_schema();
\t\t\tif ( true === $verified ) {
\t\t\t\tdelete_option( self::SAFE_MODE_OPTION );
\t\t\t\treturn true;
\t\t\t}
\t\t}
\t\tglobal $wpdb;
\t\trequire_once ABSPATH . 'wp-admin/includes/upgrade.php';
\t\t$c = $wpdb->get_charset_collate();
\t\t$t = self::tables();
\t\t$engine = "ENGINE=InnoDB $c";
\t\t$sql = array();
\t\t$sql[] = "CREATE TABLE {$t['records']} (id bigint(20) unsigned NOT NULL AUTO_INCREMENT,record_type varchar(64) NOT NULL,record_key varchar(191) NOT NULL,locale varchar(32) NOT NULL DEFAULT 'en-US',region varchar(16) NOT NULL DEFAULT 'ZZ',status varchar(32) NOT NULL DEFAULT 'draft',is_public tinyint(1) NOT NULL DEFAULT 0,payload longtext NOT NULL,payload_hash char(64) NOT NULL,row_version bigint(20) unsigned NOT NULL DEFAULT 1,review_due_at datetime NULL,created_by bigint(20) unsigned NOT NULL DEFAULT 0,created_at datetime NOT NULL,updated_at datetime NOT NULL,PRIMARY KEY (id),UNIQUE KEY record_identity (record_type,record_key,locale,region),KEY public_lookup (record_type,status,is_public,locale,region),KEY review_due (status,review_due_at)) $engine;";
\t\t$sql[] = "CREATE TABLE {$t['reports']} (id bigint(20) unsigned NOT NULL AUTO_INCREMENT,public_id char(36) NOT NULL,report_type varchar(64) NOT NULL,route_key varchar(64) NOT NULL,block_key varchar(191) NULL,locale varchar(32) NOT NULL DEFAULT 'en-US',reason_code varchar(64) NOT NULL,message text NULL,actor_hash char(64) NULL,status varchar(32) NOT NULL DEFAULT 'open',resolution text NULL,row_version bigint(20) unsigned NOT NULL DEFAULT 1,created_at datetime NOT NULL,updated_at datetime NOT NULL,PRIMARY KEY (id),UNIQUE KEY public_id (public_id),KEY review_queue (status,created_at),KEY block_lookup (block_key,status)) $engine;";
\t\tforeach ( $sql as $statement ) {
\t\t\tdbDelta( $statement );
\t\t}
\t\t$verified = self::verify_schema();
\t\tif ( is_wp_error( $verified ) ) {
\t\t\tupdate_option( self::SAFE_MODE_OPTION, 1, false );
\t\t\tGCU_Observability::log( 'error', 'future_schema_verification_failed', array( 'code' => $verified->get_error_code() ) );
\t\t\treturn $verified;
\t\t}
\t\tupdate_option( self::SCHEMA_OPTION, self::SCHEMA_VERSION, false );
\t\tdelete_option( self::SAFE_MODE_OPTION );
\t\tself::seed_defaults();
\t\treturn true;
\t}
'''
new_ensure = '''\tpublic static function ensure_schema() {
\t\tif ( self::SCHEMA_VERSION === (int) get_option( self::SCHEMA_OPTION, 0 ) && ! get_option( self::SAFE_MODE_OPTION, 0 ) ) {
\t\t\treturn true;
\t\t}
\t\t$lock = GCU_Hardening::acquire_db_lock( 'future-schema', 5 );
\t\tif ( ! $lock ) {
\t\t\tupdate_option( self::SAFE_MODE_OPTION, 1, false );
\t\t\treturn new WP_Error( 'gcu_future_schema_lock_busy', __( 'Future Conversion and Trust Intelligence schema upgrade is already running. Please retry shortly.', 'global-clinic-usp-integration' ), array( 'status' => 503 ) );
\t\t}
\t\ttry {
\t\t\tif ( self::SCHEMA_VERSION === (int) get_option( self::SCHEMA_OPTION, 0 ) ) {
\t\t\t\t$verified = self::verify_schema();
\t\t\t\tif ( true === $verified ) {
\t\t\t\t\tdelete_option( self::SAFE_MODE_OPTION );
\t\t\t\t\treturn true;
\t\t\t\t}
\t\t\t}
\t\t\tglobal $wpdb;
\t\t\trequire_once ABSPATH . 'wp-admin/includes/upgrade.php';
\t\t\t$c = $wpdb->get_charset_collate();
\t\t\t$t = self::tables();
\t\t\t$engine = "ENGINE=InnoDB $c";
\t\t\t$sql = array();
\t\t\t$sql[] = "CREATE TABLE {$t['records']} (id bigint(20) unsigned NOT NULL AUTO_INCREMENT,record_type varchar(64) NOT NULL,record_key varchar(191) NOT NULL,locale varchar(32) NOT NULL DEFAULT 'en-US',region varchar(16) NOT NULL DEFAULT 'ZZ',status varchar(32) NOT NULL DEFAULT 'draft',is_public tinyint(1) NOT NULL DEFAULT 0,payload longtext NOT NULL,payload_hash char(64) NOT NULL,row_version bigint(20) unsigned NOT NULL DEFAULT 1,review_due_at datetime NULL,created_by bigint(20) unsigned NOT NULL DEFAULT 0,created_at datetime NOT NULL,updated_at datetime NOT NULL,PRIMARY KEY (id),UNIQUE KEY record_identity (record_type,record_key,locale,region),KEY public_lookup (record_type,status,is_public,locale,region),KEY review_due (status,review_due_at)) $engine;";
\t\t\t$sql[] = "CREATE TABLE {$t['reports']} (id bigint(20) unsigned NOT NULL AUTO_INCREMENT,public_id char(36) NOT NULL,report_type varchar(64) NOT NULL,route_key varchar(64) NOT NULL,block_key varchar(191) NULL,locale varchar(32) NOT NULL DEFAULT 'en-US',reason_code varchar(64) NOT NULL,message text NULL,actor_hash char(64) NULL,status varchar(32) NOT NULL DEFAULT 'open',resolution text NULL,row_version bigint(20) unsigned NOT NULL DEFAULT 1,created_at datetime NOT NULL,updated_at datetime NOT NULL,PRIMARY KEY (id),UNIQUE KEY public_id (public_id),KEY review_queue (status,created_at),KEY block_lookup (block_key,status)) $engine;";
\t\t\tforeach ( $sql as $statement ) {
\t\t\t\tdbDelta( $statement );
\t\t\t}
\t\t\t$verified = self::verify_schema();
\t\t\tif ( is_wp_error( $verified ) ) {
\t\t\t\tupdate_option( self::SAFE_MODE_OPTION, 1, false );
\t\t\t\tGCU_Observability::log( 'error', 'future_schema_verification_failed', array( 'code' => $verified->get_error_code() ) );
\t\t\t\treturn $verified;
\t\t\t}
\t\t\tupdate_option( self::SCHEMA_OPTION, self::SCHEMA_VERSION, false );
\t\t\tdelete_option( self::SAFE_MODE_OPTION );
\t\t\tself::seed_defaults();
\t\t\treturn true;
\t\t} finally {
\t\t\tGCU_Hardening::release_db_lock( $lock );
\t\t}
\t}

\tpublic static function runtime_ready() {
\t\tif ( ! get_option( 'gcu_enabled', 1 ) ) {
\t\t\treturn new WP_Error( 'gcu_safe_mode', __( 'File 14 is temporarily unavailable.', 'global-clinic-usp-integration' ), array( 'status' => 503 ) );
\t\t}
\t\tif ( self::SCHEMA_VERSION !== (int) get_option( self::SCHEMA_OPTION, 0 ) || get_option( self::SAFE_MODE_OPTION, 0 ) ) {
\t\t\treturn new WP_Error( 'gcu_future_schema_pending', __( 'Future Conversion and Trust Intelligence is temporarily unavailable until its schema is verified.', 'global-clinic-usp-integration' ), array( 'status' => 503 ) );
\t\t}
\t\treturn true;
\t}
'''
replace_exact(future, old_ensure, new_ensure)
replace_exact(
    future,
    "return array( 'score' => GCU_Future_Policy::conversion_quality_score( $metrics ), 'provisional' => ! $performance_verified || ! GCU_Future_Policy::cohort_allowed( $selected ), 'metrics' => $metrics, 'sample_count' => $selected, 'small_cohort_suppressed' => ! GCU_Future_Policy::cohort_allowed( $selected ), 'parity' => $parity, 'performance_verified' => $performance_verified );",
    "return array( 'score' => GCU_Future_Policy::conversion_quality_score( $metrics ), 'provisional' => ! $performance_verified || ! GCU_Future_Policy::cohort_allowed( $selected ), 'metrics' => $metrics, 'sample_count' => GCU_Future_Policy::cohort_allowed( $selected ) ? $selected : null, 'small_cohort_suppressed' => ! GCU_Future_Policy::cohort_allowed( $selected ), 'cohort_threshold' => GCU_Future_Policy::MIN_COHORT, 'parity' => $parity, 'performance_verified' => $performance_verified );",
)
replace_exact(
    future,
    "\t\tif ( ! GCU_Future_Policy::cohort_allowed( $cs ) || ! GCU_Future_Policy::cohort_allowed( $bs ) ) {\n\t\t\t$result = array( 'status' => 'insufficient_sample', 'severity' => 'none', 'current_sample' => $cs, 'baseline_sample' => $bs, 'checked_at' => gmdate( 'c' ) );",
    "\t\tif ( ! GCU_Future_Policy::cohort_allowed( $cs ) || ! GCU_Future_Policy::cohort_allowed( $bs ) ) {\n\t\t\t$result = array( 'status' => 'insufficient_sample', 'severity' => 'none', 'current_sample' => null, 'baseline_sample' => null, 'suppressed' => true, 'threshold' => GCU_Future_Policy::MIN_COHORT, 'checked_at' => gmdate( 'c' ) );",
)

# 3. Activation/upgrade invokes Future migration as an independently fail-closed additive schema.
install = P / "includes/class-gcu-install.php"
replace_exact(
    install,
    "public static function activate(){$r=self::install_or_upgrade(true);if(is_wp_error($r)){update_option(self::UPGRADE_ERROR,self::safe_error_record($r),false);update_option('gcu_enabled',0,false);wp_die(esc_html($r->get_error_message()));}}",
    "public static function activate(){$r=self::install_or_upgrade(true);if(is_wp_error($r)){update_option(self::UPGRADE_ERROR,self::safe_error_record($r),false);update_option('gcu_enabled',0,false);wp_die(esc_html($r->get_error_message()));}self::ensure_future_schema();}",
)
replace_exact(
    install,
    "public static function maybe_upgrade(){if(GCU_VERSION===(string)get_option(self::VERSION_OPTION,'')&&GCU_SCHEMA_VERSION===(int)get_option(self::SCHEMA_OPTION,0)){return true;}$r=self::install_or_upgrade(false);if(is_wp_error($r)){update_option(self::UPGRADE_ERROR,self::safe_error_record($r),false);update_option('gcu_enabled',0,false);}return $r;}",
    "public static function maybe_upgrade(){if(GCU_VERSION===(string)get_option(self::VERSION_OPTION,'')&&GCU_SCHEMA_VERSION===(int)get_option(self::SCHEMA_OPTION,0)){self::ensure_future_schema();return true;}$r=self::install_or_upgrade(false);if(is_wp_error($r)){update_option(self::UPGRADE_ERROR,self::safe_error_record($r),false);update_option('gcu_enabled',0,false);return$r;}self::ensure_future_schema();return true;}\n\tprivate static function ensure_future_schema(){if(!class_exists('GCU_Future_Intelligence')){return true;}$r=GCU_Future_Intelligence::ensure_schema();if(is_wp_error($r)){update_option(GCU_Future_Intelligence::SAFE_MODE_OPTION,1,false);GCU_Observability::log('error','future_schema_upgrade_deferred',array('code'=>$r->get_error_code()));}return$r;}",
)

# 4. Runtime fail-closed Future routes, small-cohort defense-in-depth, and cache-policy preservation.
review = P / "includes/class-gcu-review80-hardening.php"
replace_exact(
    review,
    "\t\t$route = $request->get_route();\n\t\tif ( '/gcu/v1/blocks' === $route || 0 === strpos( $route, '/gcu/v1/future/trust/' ) ) {",
    "\t\t$route = $request->get_route();\n\t\tif ( 0 === strpos( $route, '/gcu/v1/future/' ) ) {\n\t\t\t$future_ready = GCU_Future_Intelligence::runtime_ready();\n\t\t\tif ( is_wp_error( $future_ready ) ) {\n\t\t\t\treturn $future_ready;\n\t\t\t}\n\t\t}\n\t\tif ( '/gcu/v1/blocks' === $route || 0 === strpos( $route, '/gcu/v1/future/trust/' ) ) {",
)
replace_exact(
    review,
    "\t\tif ( '/gcu/v1/future/quality' === $route ) {\n\t\t\t$unverified = array();",
    "\t\tif ( '/gcu/v1/future/quality' === $route ) {\n\t\t\tif ( ! empty( $data['small_cohort_suppressed'] ) ) {\n\t\t\t\t$data['sample_count'] = null;\n\t\t\t}\n\t\t\t$unverified = array();",
)
replace_exact(
    review,
    "\t\t$response->header( 'Cache-Control', 'no-store, private' );\n\t\treturn $response;",
    "\t\t$headers = $response->get_headers();\n\t\tif ( empty( $headers['Cache-Control'] ) ) {\n\t\t\t$response->header( 'Cache-Control', 'no-store, private' );\n\t\t}\n\t\treturn $response;",
)

# 5. Remove the obsolete v1.4.0/PR-3 release automation so it cannot be manually replayed.
obsolete = ROOT / ".github/workflows/file14-one-shot-release-gate.yml"
if not obsolete.exists():
    raise SystemExit("Reviewed baseline drift: obsolete one-shot release gate is already absent")
obsolete.unlink()

# 6. Current documentation truth must name v1.4.1 and not freeze a pre-merge candidate state.
trace = ROOT / "docs/REQUIREMENTS-TRACEABILITY.md"
replace_exact(trace, "Requirements Traceability — v1.4.0", "Requirements Traceability — v1.4.1")
replace_exact(trace, "File 14 v1.4.0 may only claim a status", "File 14 v1.4.1 may only claim a status")
release = ROOT / "docs/RELEASE-EVIDENCE.md"
replace_exact(release, "# Release Evidence — v1.4.1 Eighty-Pass Corrective Candidate", "# Release Evidence — v1.4.1 Repository Release Evidence")
replace_exact(release, "After the final v1.4.1 coding/documentation commit, the exact tested PR SHA must independently prove:", "For every v1.4.1 corrective change, the exact current review/main SHA must independently prove:")
status = ROOT / "STATUS.md"
replace_exact(status, "# File 14 Status — v1.4.1 Eighty-Pass Corrective Candidate — Merged", "# File 14 Status — v1.4.1 Repository Release State")

print("Second-review corrective patch applied successfully.")
