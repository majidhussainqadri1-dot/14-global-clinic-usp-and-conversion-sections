#!/usr/bin/env python3
from pathlib import Path
import re, subprocess, sys

ROOT = Path(__file__).resolve().parents[1]
P = ROOT / '14-global-clinic-usp-integration'

def read(rel):
    return (ROOT / rel).read_text(encoding='utf-8')

def write(rel, text):
    (ROOT / rel).write_text(text, encoding='utf-8')

def replace(rel, old, new, count=1):
    text = read(rel)
    if text.count(old) < count:
        raise SystemExit(f'Expected pattern not found in {rel}: {old[:120]!r}')
    text = text.replace(old, new, count)
    write(rel, text)

def regex_replace(rel, pattern, repl, count=1):
    text = read(rel)
    new, n = re.subn(pattern, repl, text, count=count, flags=re.S)
    if n != count:
        raise SystemExit(f'Expected {count} regex replacement(s), got {n} in {rel}: {pattern[:120]!r}')
    write(rel, new)

def git_commit(message, paths=None):
    subprocess.run(['git','add','-A'] if paths is None else ['git','add',*paths], cwd=ROOT, check=True)
    diff = subprocess.run(['git','diff','--cached','--quiet'], cwd=ROOT)
    if diff.returncode == 0:
        raise SystemExit(f'No changes staged for commit: {message}')
    subprocess.run(['git','commit','-m',message], cwd=ROOT, check=True)

# ---------------------------------------------------------------------------
# Round 03 correction: request-time File 20 slot readiness for already-active
# and seeded placements. Transition-time validation alone is insufficient.
# ---------------------------------------------------------------------------
repo_rel='14-global-clinic-usp-integration/includes/class-gcu-repository.php'
repo=read(repo_rel)
old="""$r=array_values(array_filter($r,static function($row)use($valid){foreach($row['claim_keys']as$key){if(!isset($valid[$key])){return false;}}return true;}));return$r;}"""
new="""$r=array_values(array_filter($r,static function($row)use($valid){foreach($row['claim_keys']as$key){if(!isset($valid[$key])){return false;}}return GCU_Plugin::instance()->contracts()->placement_ready($row);}));return$r;}"""
if old not in repo:
    raise SystemExit('Round 03 active_blocks marker not found')
write(repo_rel, repo.replace(old,new,1))
git_commit('Round 03: fail closed active placements at request time', [repo_rel])

# ---------------------------------------------------------------------------
# Rounds 08-10: stable integrity/privacy keys and legacy migration.
# ---------------------------------------------------------------------------
integrity_rel='14-global-clinic-usp-integration/includes/class-gcu-integrity.php'
integrity=r'''<?php

defined( 'ABSPATH' ) || exit;

/** Stable File 14 integrity and pseudonymization keys plus one-time legacy migration. */
final class GCU_Integrity {
	const AUDIT_KEY_OPTION = 'gcu_audit_hmac_key_v1';
	const PRIVACY_KEY_OPTION = 'gcu_privacy_hmac_key_v1';
	const MIGRATION_OPTION = 'gcu_integrity_key_migration_v1';

	public static function ensure_keys() {
		foreach ( array( self::AUDIT_KEY_OPTION, self::PRIVACY_KEY_OPTION ) as $option ) {
			$current = (string) get_option( $option, '' );
			if ( preg_match( '/^[a-f0-9]{64}$/', $current ) ) { continue; }
			try { $value = bin2hex( random_bytes( 32 ) ); }
			catch ( Exception $e ) { return new WP_Error( 'gcu_integrity_entropy_unavailable', __( 'File 14 could not establish a stable integrity key.', 'global-clinic-usp-integration' ) ); }
			if ( ! add_option( $option, $value, '', 'no' ) ) {
				$current = (string) get_option( $option, '' );
				if ( ! preg_match( '/^[a-f0-9]{64}$/', $current ) ) { return new WP_Error( 'gcu_integrity_key_store_failed', __( 'File 14 could not persist a stable integrity key.', 'global-clinic-usp-integration' ) ); }
			}
		}
		return true;
	}

	private static function key( $option ) {
		$key = (string) get_option( $option, '' );
		return preg_match( '/^[a-f0-9]{64}$/', $key ) ? $key : '';
	}
	public static function audit_key() { return self::key( self::AUDIT_KEY_OPTION ); }
	public static function privacy_key() { return self::key( self::PRIVACY_KEY_OPTION ); }
	public static function audit_hash( $message ) { $key=self::audit_key(); return $key ? hash_hmac( 'sha256', (string) $message, $key ) : ''; }
	public static function privacy_hash( $message ) { $key=self::privacy_key(); return $key ? hash_hmac( 'sha256', (string) $message, $key ) : ''; }
	public static function user_subject_hash( $seed ) { return self::privacy_hash( 'event-user|' . (string) $seed ); }
	public static function future_actor_hash( $user_id ) { return $user_id ? self::privacy_hash( 'future-report-user|' . absint( $user_id ) ) : ''; }

	private static function audit_message( array $r ) {
		return implode( '|', array(
			(string) $r['previous_hash'], (string) $r['trace_id'], (string) $r['actor_id'], (string) $r['action_name'],
			(string) $r['object_type'], (string) $r['object_id'], (string) $r['purpose'], (string) $r['reason'],
			(string) $r['before_hash'], (string) $r['after_hash'], (string) $r['created_at'],
		) );
	}
	public static function stable_audit_row_hash( array $r ) { return self::audit_hash( self::audit_message( $r ) ); }
	private static function legacy_audit_row_hash( array $r ) { return hash_hmac( 'sha256', self::audit_message( $r ), wp_salt( 'auth' ) ); }

	public static function migrate_legacy_hashes() {
		$keys = self::ensure_keys(); if ( is_wp_error( $keys ) ) { return $keys; }
		$state = get_option( self::MIGRATION_OPTION, array() );
		if ( is_array( $state ) && ! empty( $state['completed'] ) ) { return true; }
		$audit = self::migrate_audit_chain(); if ( is_wp_error( $audit ) ) { return $audit; }
		$privacy = self::migrate_privacy_hashes(); if ( is_wp_error( $privacy ) ) { return $privacy; }
		update_option( self::MIGRATION_OPTION, array( 'completed'=>1, 'version'=>1, 'migrated_at'=>time() ), false );
		return true;
	}

	private static function migrate_audit_chain() {
		global $wpdb; $t=GCU_Install::tables();
		$exists=$wpdb->get_var($wpdb->prepare('SHOW TABLES LIKE %s',$wpdb->esc_like($t['audit']))); if($exists!==$t['audit']){return true;}
		$count=(int)$wpdb->get_var("SELECT COUNT(*) FROM {$t['audit']}"); if(!$count){return true;}
		$lock=GCU_Hardening::acquire_db_lock('audit-chain',5); if(!$lock){return new WP_Error('gcu_integrity_audit_lock_busy',__('Audit migration is busy.','global-clinic-usp-integration'));}
		try{
			if(false===$wpdb->query('START TRANSACTION')){return new WP_Error('gcu_integrity_audit_transaction_failed',__('Audit migration transaction could not start.','global-clinic-usp-integration'));}
			$legacy_prev=str_repeat('0',64); $stable_prev=str_repeat('0',64); $offset=0;
			while($offset<$count){
				$rows=$wpdb->get_results($wpdb->prepare("SELECT * FROM {$t['audit']} ORDER BY id ASC LIMIT 500 OFFSET %d",$offset),ARRAY_A);
				if(!is_array($rows)){ $wpdb->query('ROLLBACK'); return new WP_Error('gcu_integrity_audit_read_failed',__('Audit migration could not read the chain.','global-clinic-usp-integration')); }
				foreach($rows as$row){
					$old_hash=(string)$row['row_hash'];
					if(!hash_equals($legacy_prev,(string)$row['previous_hash'])||!hash_equals($old_hash,self::legacy_audit_row_hash($row))){$wpdb->query('ROLLBACK');return new WP_Error('gcu_integrity_legacy_audit_invalid',__('The existing audit chain could not be verified before key migration.','global-clinic-usp-integration'));}
					$legacy_prev=$old_hash; $row['previous_hash']=$stable_prev; $new_hash=self::stable_audit_row_hash($row);
					if(!$new_hash||false===$wpdb->query($wpdb->prepare("UPDATE {$t['audit']} SET previous_hash=%s,row_hash=%s WHERE id=%d AND row_hash=%s",$stable_prev,$new_hash,(int)$row['id'],$old_hash))){$wpdb->query('ROLLBACK');return new WP_Error('gcu_integrity_audit_rehash_failed',__('Audit key migration could not be completed safely.','global-clinic-usp-integration'));}
					$stable_prev=$new_hash;
				}
				$offset+=count($rows); if(!$rows){break;}
			}
			if(false===$wpdb->query('COMMIT')){$wpdb->query('ROLLBACK');return new WP_Error('gcu_integrity_audit_commit_failed',__('Audit key migration could not commit.','global-clinic-usp-integration'));}
			return true;
		}finally{GCU_Hardening::release_db_lock($lock);}
	}

	private static function migrate_privacy_hashes() {
		global $wpdb; $t=GCU_Install::tables();
		$exists=$wpdb->get_var($wpdb->prepare('SHOW TABLES LIKE %s',$wpdb->esc_like($t['events']))); if($exists!==$t['events']){return true;}
		if(false===$wpdb->query('START TRANSACTION')){return new WP_Error('gcu_integrity_privacy_transaction_failed',__('Privacy hash migration transaction could not start.','global-clinic-usp-integration'));}
		try{
			$users=$wpdb->get_results($wpdb->prepare("SELECT user_id,meta_value FROM {$wpdb->usermeta} WHERE meta_key=%s",GCU_Privacy::USER_SUBJECT_META),ARRAY_A);
			foreach(is_array($users)?$users:array()as$u){$seed=(string)$u['meta_value'];if(!preg_match('/^[a-f0-9]{64}$/',$seed)){continue;}$old=hash_hmac('sha256',$seed,wp_salt('secure_auth'));$new=self::user_subject_hash($seed);if(!$new){throw new RuntimeException('privacy-key');}$wpdb->query($wpdb->prepare("UPDATE {$t['events']} SET subject_hash=%s WHERE subject_hash=%s",$new,$old));
				if(class_exists('GCU_Future_Intelligence')){$ft=GCU_Future_Intelligence::tables();$fexists=$wpdb->get_var($wpdb->prepare('SHOW TABLES LIKE %s',$wpdb->esc_like($ft['reports'])));if($fexists===$ft['reports']){$old_actor=hash_hmac('sha256','u:'.(int)$u['user_id'],wp_salt('auth'));$new_actor=self::future_actor_hash((int)$u['user_id']);$wpdb->query($wpdb->prepare("UPDATE {$ft['reports']} SET actor_hash=%s WHERE actor_hash=%s",$new_actor,$old_actor));}}
			}
			if(false===$wpdb->query('COMMIT')){throw new RuntimeException('commit');}return true;
		}catch(Throwable$e){$wpdb->query('ROLLBACK');return new WP_Error('gcu_integrity_privacy_migration_failed',__('Privacy hash migration could not be completed safely.','global-clinic-usp-integration'));}
	}
}
'''
write(integrity_rel,integrity)

main_rel='14-global-clinic-usp-integration/global-clinic-usp-integration.php'
replace(main_rel," * Version: 1.4.3"," * Version: 1.4.4")
replace(main_rel,"define( 'GCU_VERSION', '1.4.3' );","define( 'GCU_VERSION', '1.4.4' );")
replace(main_rel,"define( 'GCU_SCHEMA_VERSION', 10004 );","define( 'GCU_SCHEMA_VERSION', 10005 );")
replace(main_rel,"\t'includes/class-gcu-hardening.php',","\t'includes/class-gcu-hardening.php',\n\t'includes/class-gcu-integrity.php',")

# Hardening: deterministic canonical request fingerprints for idempotency replay binding.
hard_rel='14-global-clinic-usp-integration/includes/class-gcu-hardening.php'
hard=read(hard_rel)
marker="\tpublic static function command_key( $name, $supplied = '' ) {"
if marker not in hard: raise SystemExit('command_key marker missing')
addition=r'''	public static function request_fingerprint( $value ) {
		$normalize = static function ( $v ) use ( &$normalize ) {
			if ( is_array( $v ) ) {
				if ( array_keys( $v ) !== range( 0, count( $v ) - 1 ) ) { ksort( $v, SORT_STRING ); }
				foreach ( $v as $k => $child ) { $v[ $k ] = $normalize( $child ); }
			}
			return $v;
		};
		$encoded = wp_json_encode( $normalize( self::sanitize_structured_value( $value ) ) );
		return false === $encoded ? '' : hash( 'sha256', $encoded );
	}

'''
write(hard_rel,hard.replace(marker,addition+marker,1))

git_commit('Rounds 08-10: add stable integrity and privacy key migration', [integrity_rel,main_rel,hard_rel])

# ---------------------------------------------------------------------------
# Rounds 11-12 + 18: schema request fingerprint, stable key migration, stale
# install-lock recovery, and true Future verification before safe-mode exit.
# ---------------------------------------------------------------------------
install_rel='14-global-clinic-usp-integration/includes/class-gcu-install.php'
inst=read(install_rel)
inst=inst.replace("public static function ready_for_runtime(){if(get_option(self::LOCK_OPTION,false))", "public static function ready_for_runtime(){if(self::install_lock_active())",1)
inst=inst.replace("private static function install_or_upgrade($activation){if(!self::acquire_lock())", "private static function install_or_upgrade($activation){if(!self::acquire_lock())",1)
old="try{$snap=self::capture_snapshot(true);if(is_wp_error($snap)){return $snap;}$schema=self::schema();if(is_wp_error($schema)){return $schema;}GCU_Capabilities::install();$seed=self::seed_governed_content();if(is_wp_error($seed)){return $seed;}$migration=self::migrate_legacy();if(is_wp_error($migration)){return $migration;}self::schedule();update_option(self::VERSION_OPTION,GCU_VERSION,false);"
new="try{$keys=GCU_Integrity::ensure_keys();if(is_wp_error($keys)){return $keys;}$snap=self::capture_snapshot(true);if(is_wp_error($snap)){return $snap;}$schema=self::schema();if(is_wp_error($schema)){return $schema;}GCU_Capabilities::install();$seed=self::seed_governed_content();if(is_wp_error($seed)){return $seed;}$migration=self::migrate_legacy();if(is_wp_error($migration)){return $migration;}$integrity=GCU_Integrity::migrate_legacy_hashes();if(is_wp_error($integrity)){return $integrity;}self::schedule();update_option(self::VERSION_OPTION,GCU_VERSION,false);"
if old not in inst: raise SystemExit('install_or_upgrade body marker missing')
inst=inst.replace(old,new,1)
inst=inst.replace("command_name varchar(100) NOT NULL,actor_id bigint(20) unsigned", "command_name varchar(100) NOT NULL,request_hash char(64) NOT NULL DEFAULT '',actor_id bigint(20) unsigned",1)
inst=inst.replace("'commands'=>array('command_key','status','attempts','locked_at','result_json')", "'commands'=>array('command_key','command_name','request_hash','status','attempts','locked_at','result_json')",1)
# Add integrity options to explicit purge/snapshot ownership inventory.
inst=inst.replace("$names=array(self::VERSION_OPTION,self::SCHEMA_OPTION,'gcu_enabled','gcu_settings','gcu_legacy_migrated',self::MIGRATION_LOG);", "$names=array(self::VERSION_OPTION,self::SCHEMA_OPTION,'gcu_enabled','gcu_settings','gcu_legacy_migrated',self::MIGRATION_LOG,GCU_Integrity::AUDIT_KEY_OPTION,GCU_Integrity::PRIVACY_KEY_OPTION,GCU_Integrity::MIGRATION_OPTION);",1)
# Stale install lock recovery: the option is advisory; MySQL named lock is authoritative after a bounded grace period.
marker="\tprivate static function acquire_lock(){global$wpdb;$name=substr($wpdb->prefix.'gcu-install-upgrade',0,64);"
if marker not in inst: raise SystemExit('acquire_lock marker missing')
helper="""\tprivate static function install_lock_active(){global$wpdb;$state=get_option(self::LOCK_OPTION,false);if(!$state){return false;}$at=is_array($state)&&!empty($state['acquired_at'])?(int)$state['acquired_at']:0;if($at&&$at>time()-600){return true;}$name=substr($wpdb->prefix.'gcu-install-upgrade',0,64);$used=$wpdb->get_var($wpdb->prepare('SELECT IS_USED_LOCK(%s)',$name));if(null===$used||false===$used||0===(int)$used){delete_option(self::LOCK_OPTION);GCU_Observability::log('warning','stale_install_lock_recovered',array('age'=>$at?max(0,time()-$at):null));return false;}return true;}\n"""
inst=inst.replace(marker,helper+marker,1)
write(install_rel,inst)

admin_rel='14-global-clinic-usp-integration/includes/class-gcu-admin.php'
admin=read(admin_rel)
admin=admin.replace("$future=GCU_Future_Intelligence::ensure_schema();", "$future=GCU_Future_Intelligence::ensure_schema(true);",1)
# Truthful operation notices require their mandatory audit append to succeed.
admin=admin.replace("GCU_Plugin::instance()->repository()->audit('safe_repair','module','file14','operations','Owner-scoped repair requested',array(),array('schema'=>GCU_SCHEMA_VERSION,'future_schema'=>GCU_FUTURE_SCHEMA_VERSION));$this->redirect('safe-repair-completed');", "$audit=GCU_Plugin::instance()->repository()->audit('safe_repair','module','file14','operations','Owner-scoped repair requested',array(),array('schema'=>GCU_SCHEMA_VERSION,'future_schema'=>GCU_FUTURE_SCHEMA_VERSION));if(false===$audit){wp_die(esc_html__('Repair completed but its mandatory audit record failed; File 14 remains contained in safe mode.','global-clinic-usp-integration'));}$this->redirect('safe-repair-completed');",1)
admin=admin.replace("if(!is_wp_error($x)){GCU_Plugin::instance()->repository()->audit('rollback_restored','module','file14','operations','Verified owner snapshot restoration',array(),array('version'=>GCU_VERSION,'schema'=>GCU_SCHEMA_VERSION,'future_schema'=>GCU_FUTURE_SCHEMA_VERSION));}$this->redirect(is_wp_error($x)?'rollback-unavailable':'snapshot-restored');", "if(!is_wp_error($x)){$audit=GCU_Plugin::instance()->repository()->audit('rollback_restored','module','file14','operations','Verified owner snapshot restoration',array(),array('version'=>GCU_VERSION,'schema'=>GCU_SCHEMA_VERSION,'future_schema'=>GCU_FUTURE_SCHEMA_VERSION));if(false===$audit){wp_die(esc_html__('Rollback completed but its mandatory audit record failed; File 14 remains contained in safe mode.','global-clinic-usp-integration'));}}$this->redirect(is_wp_error($x)?'rollback-unavailable':'snapshot-restored');",1)
admin=admin.replace("update_option('gcu_enabled',$enabled?1:0,false);GCU_Plugin::instance()->repository()->audit('safe_mode_changed','module','file14','operations','',array(),array('enabled'=>$enabled));$this->redirect($enabled?'module-enabled':'safe-mode-enabled');", "update_option('gcu_enabled',$enabled?1:0,false);$audit=GCU_Plugin::instance()->repository()->audit('safe_mode_changed','module','file14','operations','',array(),array('enabled'=>$enabled));if(false===$audit||($enabled&&!get_option('gcu_enabled',0))){wp_die(esc_html__('The safe-mode change could not be confirmed by the mandatory audit trail. File 14 remains contained.','global-clinic-usp-integration'));}$this->redirect($enabled?'module-enabled':'safe-mode-enabled');",1)
write(admin_rel,admin)

git_commit('Rounds 11-12 and 18: harden schema truth, admin audit, and stale lock recovery', [install_rel,admin_rel])

# ---------------------------------------------------------------------------
# Rounds 13-17: transaction manager; audit lock retained until commit; owner
# state + audit + outbox + idempotency result commit atomically.
# ---------------------------------------------------------------------------
repo=read(repo_rel)
repo=repo.replace("const MAX_CONTENT_TITLE=240,MAX_CONTENT_BODY=5000,MAX_REASON=500,EVENT_RETENTION_DAYS=90;", "const MAX_CONTENT_TITLE=240,MAX_CONTENT_BODY=5000,MAX_REASON=500,EVENT_RETENTION_DAYS=90;\nprivate $transaction_depth=0,$deferred_event_ids=array(),$transaction_audit_lock=false,$containment_code='';",1)
insert_after="private $transaction_depth=0,$deferred_event_ids=array(),$transaction_audit_lock=false,$containment_code='';\n"
tx_methods=r'''public function transaction_active(){return $this->transaction_depth>0;}
public function begin_owned_transaction(){global$wpdb;if($this->transaction_depth>0){$this->transaction_depth++;return true;}if(false===$wpdb->query('START TRANSACTION')){return false;}$this->transaction_depth=1;$this->deferred_event_ids=array();$this->containment_code='';return true;}
public function commit_owned_transaction(){global$wpdb;if($this->transaction_depth<=0){return false;}if($this->transaction_depth>1){$this->transaction_depth--;return true;}if($this->containment_code){$this->rollback_owned_transaction();return false;}$ok=false!==$wpdb->query('COMMIT');$this->transaction_depth=0;$events=$this->deferred_event_ids;$this->deferred_event_ids=array();$this->release_transaction_audit_lock();if(!$ok){$wpdb->query('ROLLBACK');return false;}foreach($events as$event_id){$this->dispatch_outbox($event_id,1);}return true;}
public function rollback_owned_transaction(){global$wpdb;if($this->transaction_depth>0){$wpdb->query('ROLLBACK');}$this->transaction_depth=0;$this->deferred_event_ids=array();$this->release_transaction_audit_lock();$this->apply_containment();return true;}
private function release_transaction_audit_lock(){if($this->transaction_audit_lock){GCU_Hardening::release_db_lock($this->transaction_audit_lock);$this->transaction_audit_lock=false;}}
private function mark_containment($code){$this->containment_code=sanitize_key($code);if(!$this->transaction_active()){$this->apply_containment();}}
private function apply_containment(){if(!$this->containment_code){return;}update_option('gcu_enabled',0,false);GCU_Observability::log('error',$this->containment_code,array('containment'=>'safe_mode'));$this->containment_code='';}
'''
repo=repo.replace(insert_after,insert_after+tx_methods,1)

# active_blocks was already corrected; now replace create methods and state transitions with transaction-aware variants.
def method_replace(text, name, next_marker, new_method):
    pattern=r"public function "+re.escape(name)+r"\(.*?\n"+re.escape(next_marker)
    m=re.search(pattern,text,re.S)
    if not m: raise SystemExit(f'method {name} block not found')
    return text[:m.start()]+new_method+'\n'+next_marker+text[m.end():]

create_content="""public function create_content_draft(array$d,$actor){$ready=GCU_Install::ready_for_mutation();if(is_wp_error($ready)){return$ready;}$auth=GCU_Capabilities::require_capability(GCU_Capabilities::MANAGE_CONTENT,null,'create_content_draft');if(is_wp_error($auth)){return$auth;}global$wpdb;$t=GCU_Install::tables();$key=sanitize_key(isset($d['block_key'])?$d['block_key']:'');$loc=GCU_Policy::sanitize_locale(isset($d['locale'])?$d['locale']:'en-US');$slot=sanitize_key(isset($d['slot_key'])?$d['slot_key']:'global_clinic_primary');$type=sanitize_key(isset($d['block_type'])?$d['block_type']:'content');$title=GCU_Hardening::bounded_text(sanitize_text_field(isset($d['title'])?$d['title']:''),self::MAX_CONTENT_TITLE);$body=wp_kses_post(isset($d['body'])?$d['body']:'');$plain=trim(wp_strip_all_tags($body));$dest=sanitize_key(isset($d['cta_destination'])?$d['cta_destination']:'');if(!$key||!in_array($slot,array('global_clinic_primary','global_clinic_trust','global_clinic_steps','global_clinic_faq'),true)||!in_array($type,array('hero','trust','steps','faq','content'),true)||''===$title||''===$plain){return new WP_Error('gcu_invalid_content',__('The File 14 content record is incomplete or outside its approved scope.','global-clinic-usp-integration'),array('status'=>400));}$len=function_exists('mb_strlen')?mb_strlen($plain):strlen($plain);if($len>self::MAX_CONTENT_BODY){return new WP_Error('gcu_content_too_long',__('The content body exceeds the approved File 14 bound.','global-clinic-usp-integration'),array('status'=>400));}if($dest&&!array_key_exists($dest,GCU_Plugin::instance()->contracts()->destination_registry())){return new WP_Error('gcu_invalid_destination',__('The CTA destination is not a registered File 14 contract.','global-clinic-usp-integration'),array('status'=>400));}$claims=array_values(array_unique(array_filter(array_map('sanitize_key',isset($d['claim_keys'])&&is_array($d['claim_keys'])?$d['claim_keys']:array()))));if(count($claims)>20){return new WP_Error('gcu_claim_set_too_large',__('Too many claims were attached to one content block.','global-clinic-usp-integration'),array('status'=>400));}$lock=GCU_Hardening::acquire_db_lock('content:'.$key.':'.$loc);if(!$lock){return new WP_Error('gcu_content_lock_busy',__('Another content version is being created. Please retry.','global-clinic-usp-integration'),array('status'=>409));}try{if(!$this->begin_owned_transaction()){return new WP_Error('gcu_content_transaction_failed',__('The content transaction could not start.','global-clinic-usp-integration'),array('status'=>500));}$latest=(int)$wpdb->get_var($wpdb->prepare(\"SELECT MAX(content_version) FROM {$t['blocks']} WHERE block_key=%s AND locale=%s\",$key,$loc));$now=current_time('mysql',true);$r=array('public_id'=>wp_generate_uuid4(),'block_key'=>$key,'audience'=>GCU_Policy::sanitize_audience(isset($d['audience'])?$d['audience']:'all'),'block_type'=>$type,'slot_key'=>$slot,'locale'=>$loc,'title'=>$title,'body'=>$body,'cta_label'=>GCU_Hardening::bounded_text(sanitize_text_field(isset($d['cta_label'])?$d['cta_label']:''),120),'cta_destination'=>$dest,'claim_keys'=>wp_json_encode($claims),'status'=>'draft','content_version'=>$latest+1,'created_by'=>absint($actor),'created_at'=>$now,'updated_at'=>$now);if(false===$wpdb->insert($t['blocks'],$r)){$this->rollback_owned_transaction();return new WP_Error('gcu_write_failed',__('The content version could not be created.','global-clinic-usp-integration'),array('status'=>500));}if(false===$this->audit('content_created','content_block',$r['public_id'],'content_governance','',array(),$r)){$this->rollback_owned_transaction();return new WP_Error('gcu_audit_required',__('The content version was not committed because its audit record could not be written.','global-clinic-usp-integration'),array('status'=>503));}$result=$this->content_by_public_id($r['public_id']);if(!$this->commit_owned_transaction()){return new WP_Error('gcu_content_commit_failed',__('The content version could not be committed safely.','global-clinic-usp-integration'),array('status'=>500));}return$result;}finally{GCU_Hardening::release_db_lock($lock);}}"""
repo=method_replace(repo,'create_content_draft','public function transition',create_content)

transition="""public function transition($machine,$id,$expected,$target,$reason=''){$ready=GCU_Install::ready_for_mutation();if(is_wp_error($ready)){return$ready;}$machine=sanitize_key($machine);$target=sanitize_key($target);$map=array('copy'=>array('table'=>'blocks','cap'=>GCU_Capabilities::MANAGE_CONTENT),'placement'=>array('table'=>'placements','cap'=>GCU_Capabilities::MANAGE_PLACEMENTS),'experiment'=>array('table'=>'experiments','cap'=>GCU_Capabilities::MANAGE_EXPERIMENTS));if(!isset($map[$machine])){return new WP_Error('gcu_invalid_machine',__('Unknown workflow type.','global-clinic-usp-integration'),array('status'=>400));}$auth=GCU_Capabilities::require_capability($map[$machine]['cap'],$id,'transition_'.$machine);if(is_wp_error($auth)){return$auth;}if(('copy'===$machine&&in_array($target,array('founder_approved','active','withdrawn'),true))||('placement'===$machine&&'active'===$target)||('experiment'===$machine&&in_array($target,array('approved','running','adopted'),true))){$a=GCU_Capabilities::require_capability(GCU_Capabilities::APPROVE_CLAIMS,$id,'governed_approval');if(is_wp_error($a)){return$a;}}global$wpdb;$t=GCU_Install::tables();$table=$t[$map[$machine]['table']];$row=$wpdb->get_row($wpdb->prepare(\"SELECT * FROM `$table` WHERE public_id=%s\",sanitize_text_field($id)),ARRAY_A);if(!$row){return new WP_Error('gcu_not_found',__('The requested record was not found.','global-clinic-usp-integration'),array('status'=>404));}if((int)$row['row_version']!==(int)$expected){return new WP_Error('gcu_version_conflict',__('The record changed. Reload it before retrying.','global-clinic-usp-integration'),array('status'=>409,'current_version'=>(int)$row['row_version']));}if(!GCU_Policy::transition_allowed($machine,$row['status'],$target)){return new WP_Error('gcu_invalid_transition',__('This state transition is not allowed.','global-clinic-usp-integration'),array('status'=>409));}$v=$this->validate_transition_target($machine,$row,$target);if(is_wp_error($v)){return$v;}$scope='workflow:'.$machine.':'.('copy'===$machine?$row['block_key'].':'.$row['locale']:$id);$lock=GCU_Hardening::acquire_db_lock($scope);if(!$lock){return new WP_Error('gcu_workflow_lock_busy',__('Another workflow update is in progress. Please retry.','global-clinic-usp-integration'),array('status'=>409));}try{if(!$this->begin_owned_transaction()){return new WP_Error('gcu_transaction_failed',__('The workflow transaction could not start.','global-clinic-usp-integration'),array('status'=>500));}$u=array('status'=>$target,'row_version'=>(int)$row['row_version']+1,'updated_at'=>current_time('mysql',true));if('copy'===$machine&&'founder_approved'===$target){$u['approved_by']=get_current_user_id();$u['approved_at']=current_time('mysql',true);$u['review_due_at']=gmdate('Y-m-d H:i:s',time()+GCU_Policy::COPY_REVIEW_DAYS*DAY_IN_SECONDS);}if('experiment'===$machine&&'approved'===$target){$u['approved_by']=get_current_user_id();}$done=$wpdb->update($table,$u,array('id'=>(int)$row['id'],'row_version'=>(int)$expected));if(1!==$done){$this->rollback_owned_transaction();return new WP_Error('gcu_concurrent_update',__('A concurrent update prevented this transition.','global-clinic-usp-integration'),array('status'=>409));}if('copy'===$machine&&'active'===$target){$n=current_time('mysql',true);$sup=$wpdb->query($wpdb->prepare(\"UPDATE {$t['blocks']} SET status='superseded',row_version=row_version+1,updated_at=%s WHERE block_key=%s AND locale=%s AND id<>%d AND status='active'\",$n,$row['block_key'],$row['locale'],(int)$row['id']));if(false===$sup){$this->rollback_owned_transaction();return new WP_Error('gcu_supersede_failed',__('Older active content versions could not be superseded safely.','global-clinic-usp-integration'),array('status'=>500));}}if(false===$this->audit('state_transition',$machine,$id,'workflow_governance',$reason,$row,array_merge($row,$u))){$this->rollback_owned_transaction();return new WP_Error('gcu_audit_required',__('The workflow transition was not committed because its audit record could not be written.','global-clinic-usp-integration'),array('status'=>503));}if('copy'===$machine&&in_array($target,array('active','withdrawn','superseded'),true)){if(false===$this->publish_event('ClinicUSPContentPublished.v1',array('public_id'=>$id,'status'=>$target))){$this->rollback_owned_transaction();return new WP_Error('gcu_outbox_required',__('The workflow transition was not committed because its owner event could not be queued.','global-clinic-usp-integration'),array('status'=>503));}}if(!$this->commit_owned_transaction()){return new WP_Error('gcu_commit_failed',__('The workflow transaction could not be committed.','global-clinic-usp-integration'),array('status'=>500));}return array('public_id'=>$id,'status'=>$target,'row_version'=>$u['row_version']);}finally{GCU_Hardening::release_db_lock($lock);}}"""
repo=method_replace(repo,'transition','private function validate_transition_target',transition)

create_placement="""public function create_placement(array$d){$ready=GCU_Install::ready_for_mutation();if(is_wp_error($ready)){return$ready;}$a=GCU_Capabilities::require_capability(GCU_Capabilities::MANAGE_PLACEMENTS,null,'create_placement');if(is_wp_error($a)){return$a;}$key=sanitize_key(isset($d['placement_key'])?$d['placement_key']:'');$block=sanitize_key(isset($d['block_key'])?$d['block_key']:'');$route=sanitize_key(isset($d['route_key'])?$d['route_key']:'global_clinic');$slot=sanitize_key(isset($d['slot_key'])?$d['slot_key']:'');if(!$key||!$block||'global_clinic'!==$route||!in_array($slot,array('global_clinic_primary','global_clinic_trust','global_clinic_steps','global_clinic_faq'),true)){return new WP_Error('gcu_invalid_placement',__('The placement contract is incomplete or outside File 14 ownership.','global-clinic-usp-integration'),array('status'=>400));}$starts=$this->sanitize_datetime(isset($d['starts_at'])?$d['starts_at']:'');$ends=$this->sanitize_datetime(isset($d['ends_at'])?$d['ends_at']:'');if($starts&&$ends&&strtotime($ends.' UTC')<=strtotime($starts.' UTC')){return new WP_Error('gcu_invalid_placement_window',__('Placement end time must be after its start time.','global-clinic-usp-integration'),array('status'=>400));}global$wpdb;$t=GCU_Install::tables();if(!$this->begin_owned_transaction()){return new WP_Error('gcu_placement_transaction_failed',__('The placement transaction could not start.','global-clinic-usp-integration'),array('status'=>500));}$now=current_time('mysql',true);$r=array('public_id'=>wp_generate_uuid4(),'placement_key'=>$key,'block_key'=>$block,'route_key'=>$route,'slot_key'=>$slot,'audience'=>GCU_Policy::sanitize_audience(isset($d['audience'])?$d['audience']:'all'),'priority'=>max(1,min(1000,absint(isset($d['priority'])?$d['priority']:100))),'status'=>'planned','starts_at'=>$starts,'ends_at'=>$ends,'created_by'=>get_current_user_id(),'created_at'=>$now,'updated_at'=>$now);if(false===$wpdb->insert($t['placements'],$r)){$this->rollback_owned_transaction();return new WP_Error('gcu_placement_write_failed',__('The placement could not be created.','global-clinic-usp-integration'),array('status'=>409));}if(false===$this->audit('placement_created','placement',$r['public_id'],'placement_governance','',array(),$r)){$this->rollback_owned_transaction();return new WP_Error('gcu_audit_required',__('The placement was not committed because its audit record could not be written.','global-clinic-usp-integration'),array('status'=>503));}$result=$this->record_by_public_id('placements',$r['public_id']);if(!$this->commit_owned_transaction()){return new WP_Error('gcu_placement_commit_failed',__('The placement could not be committed safely.','global-clinic-usp-integration'),array('status'=>500));}return$result;}"""
repo=method_replace(repo,'create_placement','public function create_experiment',create_placement)

create_experiment="""public function create_experiment(array$d){$ready=GCU_Install::ready_for_mutation();if(is_wp_error($ready)){return$ready;}$a=GCU_Capabilities::require_capability(GCU_Capabilities::MANAGE_EXPERIMENTS,null,'create_experiment');if(is_wp_error($a)){return$a;}$key=sanitize_key(isset($d['experiment_key'])?$d['experiment_key']:'');$vars=isset($d['variants'])&&is_array($d['variants'])?GCU_Hardening::sanitize_structured_value($d['variants']):array();$guards=isset($d['guardrails'])&&is_array($d['guardrails'])?GCU_Hardening::sanitize_structured_value($d['guardrails']):array();$hyp=GCU_Hardening::bounded_text(sanitize_textarea_field(isset($d['hypothesis'])?$d['hypothesis']:''),1000);$metric=GCU_Hardening::bounded_text(sanitize_text_field(isset($d['success_metric'])?$d['success_metric']:''),191);$sample=GCU_Hardening::bounded_text(sanitize_textarea_field(isset($d['sample_policy'])?$d['sample_policy']:''),1000);$privacy=GCU_Hardening::bounded_text(sanitize_textarea_field(isset($d['privacy_policy'])?$d['privacy_policy']:''),1000);$ends=$this->sanitize_datetime(isset($d['ends_at'])?$d['ends_at']:'');$end=$ends?strtotime($ends.' UTC'):0;if(!$key||!$hyp||count($vars)<2||!$guards||!$metric||!$sample||!$privacy||!$end||$end<=time()||$end>time()+90*DAY_IN_SECONDS){return new WP_Error('gcu_invalid_experiment',__('The experiment requires complete governance fields and an end date within 90 days.','global-clinic-usp-integration'),array('status'=>400));}$scan=strtolower($sample.' '.$privacy);foreach(array('minor profiling','health profiling','patient profiling','identity evidence','diagnosis targeting')as$f){if(false!==strpos($scan,$f)){return new WP_Error('gcu_sensitive_experiment_forbidden',__('Sensitive or minor profiling is not permitted by the default File 14 experiment contract.','global-clinic-usp-integration'),array('status'=>400));}}global$wpdb;$t=GCU_Install::tables();if(!$this->begin_owned_transaction()){return new WP_Error('gcu_experiment_transaction_failed',__('The experiment transaction could not start.','global-clinic-usp-integration'),array('status'=>500));}$now=current_time('mysql',true);$r=array('public_id'=>wp_generate_uuid4(),'experiment_key'=>$key,'hypothesis'=>$hyp,'variants'=>wp_json_encode($vars),'audience'=>GCU_Policy::sanitize_audience(isset($d['audience'])?$d['audience']:'all'),'guardrails'=>wp_json_encode($guards),'success_metric'=>$metric,'sample_policy'=>$sample,'privacy_policy'=>$privacy,'status'=>'proposed','starts_at'=>null,'ends_at'=>$ends,'created_by'=>get_current_user_id(),'created_at'=>$now,'updated_at'=>$now);if(false===$wpdb->insert($t['experiments'],$r)){$this->rollback_owned_transaction();return new WP_Error('gcu_experiment_write_failed',__('The experiment could not be created.','global-clinic-usp-integration'),array('status'=>409));}if(false===$this->audit('experiment_proposed','experiment',$r['public_id'],'experiment_governance','',array(),$r)){$this->rollback_owned_transaction();return new WP_Error('gcu_audit_required',__('The experiment was not committed because its audit record could not be written.','global-clinic-usp-integration'),array('status'=>503));}$result=$this->record_by_public_id('experiments',$r['public_id']);if(!$this->commit_owned_transaction()){return new WP_Error('gcu_experiment_commit_failed',__('The experiment could not be committed safely.','global-clinic-usp-integration'),array('status'=>500));}return$result;}"""
repo=method_replace(repo,'create_experiment','public function withdraw_claim',create_experiment)

withdraw="""public function withdraw_claim($claim,$expected,$reason){$ready=GCU_Install::ready_for_mutation();if(is_wp_error($ready)){return$ready;}$a=GCU_Capabilities::require_capability(GCU_Capabilities::APPROVE_CLAIMS,$claim,'withdraw_claim');if(is_wp_error($a)){return$a;}$reason=GCU_Hardening::bounded_text(sanitize_textarea_field($reason),self::MAX_REASON);if((function_exists('mb_strlen')?mb_strlen($reason):strlen($reason))<8){return new WP_Error('gcu_reason_required',__('A meaningful bounded withdrawal reason is required.','global-clinic-usp-integration'),array('status'=>400));}global$wpdb;$t=GCU_Install::tables();$key=sanitize_key($claim);$lock=GCU_Hardening::acquire_db_lock('claim:'.$key);if(!$lock){return new WP_Error('gcu_claim_lock_busy',__('Another claim update is in progress.','global-clinic-usp-integration'),array('status'=>409));}try{$row=$wpdb->get_row($wpdb->prepare(\"SELECT * FROM {$t['claims']} WHERE claim_key=%s\",$key),ARRAY_A);if(!$row){return new WP_Error('gcu_claim_not_found',__('The claim was not found.','global-clinic-usp-integration'),array('status'=>404));}if((int)$row['row_version']!==(int)$expected){return new WP_Error('gcu_version_conflict',__('The claim changed. Reload it before retrying.','global-clinic-usp-integration'),array('status'=>409));}if(!$this->begin_owned_transaction()){return new WP_Error('gcu_claim_transaction_failed',__('Claim withdrawal transaction could not start.','global-clinic-usp-integration'),array('status'=>500));}$h=array('claim_key'=>$key,'row_version'=>(int)$row['row_version'],'status'=>$row['status'],'claim_hash'=>hash('sha256',wp_json_encode($row)),'reason'=>$reason,'snapshot'=>wp_json_encode($row),'actor_id'=>get_current_user_id(),'created_at'=>current_time('mysql',true));$ins=$wpdb->query($wpdb->prepare(\"INSERT IGNORE INTO {$t['claim_history']} (claim_key,row_version,status,claim_hash,reason,snapshot,actor_id,created_at) VALUES (%s,%d,%s,%s,%s,%s,%d,%s)\",$h['claim_key'],$h['row_version'],$h['status'],$h['claim_hash'],$h['reason'],$h['snapshot'],$h['actor_id'],$h['created_at']));if(false===$ins){$this->rollback_owned_transaction();return new WP_Error('gcu_claim_history_failed',__('Claim history could not be recorded safely.','global-clinic-usp-integration'),array('status'=>500));}$u=array('status'=>'withdrawn','is_public'=>0,'row_version'=>(int)$row['row_version']+1,'updated_at'=>current_time('mysql',true));$done=$wpdb->update($t['claims'],$u,array('id'=>(int)$row['id'],'row_version'=>(int)$expected));if(1!==$done){$this->rollback_owned_transaction();return new WP_Error('gcu_claim_withdraw_failed',__('The claim could not be withdrawn atomically.','global-clinic-usp-integration'),array('status'=>409));}if(false===$this->audit('claim_withdrawn','claim',$key,'claim_governance',$reason,$row,array_merge($row,$u))||false===$this->publish_event('ClinicUSPClaimWithdrawn.v1',array('claim_key'=>$key))){$this->rollback_owned_transaction();return new WP_Error('gcu_claim_governance_commit_failed',__('The claim was not committed because its audit or owner event could not be persisted.','global-clinic-usp-integration'),array('status'=>503));}if(!$this->commit_owned_transaction()){return new WP_Error('gcu_claim_commit_failed',__('The claim withdrawal could not be committed safely.','global-clinic-usp-integration'),array('status'=>500));}return array('claim_key'=>$key,'status'=>'withdrawn','row_version'=>$u['row_version']);}finally{GCU_Hardening::release_db_lock($lock);}}"""
repo=method_replace(repo,'withdraw_claim','public function record_by_public_id',withdraw)

# Idempotency now binds key to request fingerprint and completes in the same owner transaction.
run_cmd="""public function run_idempotent_command($name,$supplied,callable$callback,$request_hash=''){$ready=GCU_Install::ready_for_mutation();if(is_wp_error($ready)){return$ready;}if(!preg_match('/^[a-f0-9]{64}$/',(string)$request_hash)){return new WP_Error('gcu_request_fingerprint_required',__('A canonical request fingerprint is required for this idempotent command.','global-clinic-usp-integration'),array('status'=>400));}global$wpdb;$t=GCU_Install::tables();$key=GCU_Hardening::command_key($name,$supplied);$now=current_time('mysql',true);$ins=$wpdb->query($wpdb->prepare(\"INSERT IGNORE INTO {$t['commands']} (command_key,command_name,request_hash,actor_id,status,attempts,locked_at,created_at,updated_at) VALUES (%s,%s,%s,%d,'processing',1,%s,%s,%s)\",$key,sanitize_key($name),$request_hash,get_current_user_id(),$now,$now,$now));if(false===$ins){return new WP_Error('gcu_command_store_failed',__('The idempotent command state could not be stored.','global-clinic-usp-integration'),array('status'=>503));}if(0===$ins){$row=$wpdb->get_row($wpdb->prepare(\"SELECT * FROM {$t['commands']} WHERE command_key=%s\",$key),ARRAY_A);if(!$row||empty($row['request_hash'])||!hash_equals((string)$row['request_hash'],$request_hash)){return new WP_Error('gcu_idempotency_payload_conflict',__('This idempotency key is already bound to a different or legacy request. Use a new key.','global-clinic-usp-integration'),array('status'=>409));}if('complete'===$row['status']){$r=json_decode((string)$row['result_json'],true);return is_array($r)?$r:array();}$stale=!empty($row['locked_at'])&&strtotime($row['locked_at'].' UTC')<time()-300;if('processing'===$row['status']&&!$stale||(int)$row['attempts']>=3){return new WP_Error('gcu_command_conflict',__('This command is already processing or has exhausted its safe retries.','global-clinic-usp-integration'),array('status'=>409));}$claimed=$wpdb->query($wpdb->prepare(\"UPDATE {$t['commands']} SET status='processing',attempts=attempts+1,error_code=NULL,locked_at=%s,updated_at=%s WHERE command_key=%s AND request_hash=%s AND (status='failed' OR (status='processing' AND locked_at<DATE_SUB(UTC_TIMESTAMP(),INTERVAL 5 MINUTE)))\",$now,$now,$key,$request_hash));if(1!==$claimed){return new WP_Error('gcu_command_conflict',__('This command could not be safely reclaimed.','global-clinic-usp-integration'),array('status'=>409));}}$lock=GCU_Hardening::acquire_db_lock('command:'.substr($key,0,40),3);if(!$lock){return new WP_Error('gcu_command_lock_busy',__('This command is already being finalized.','global-clinic-usp-integration'),array('status'=>409));}try{if(!$this->begin_owned_transaction()){$this->fail_command($key,'transaction_start');return new WP_Error('gcu_command_transaction_failed',__('The command transaction could not start.','global-clinic-usp-integration'),array('status'=>503));}try{$r=call_user_func($callback);}catch(Throwable$e){$this->rollback_owned_transaction();$this->fail_command($key,'exception');return new WP_Error('gcu_command_failed',__('The command failed safely.','global-clinic-usp-integration'),array('status'=>500));}if(is_wp_error($r)){$this->rollback_owned_transaction();$this->fail_command($key,$r->get_error_code());return$r;}$encoded=wp_json_encode($r);if(false===$encoded||strlen($encoded)>20000){$this->rollback_owned_transaction();$this->fail_command($key,'result_invalid');return new WP_Error('gcu_command_result_invalid',__('The command result could not be stored safely.','global-clinic-usp-integration'),array('status'=>500));}$done=$wpdb->query($wpdb->prepare(\"UPDATE {$t['commands']} SET status='complete',result_json=%s,error_code=NULL,locked_at=NULL,updated_at=%s WHERE command_key=%s AND request_hash=%s AND status='processing'\",$encoded,current_time('mysql',true),$key,$request_hash));if(1!==$done){$this->rollback_owned_transaction();$this->fail_command($key,'completion_failed');return new WP_Error('gcu_command_completion_failed',__('The command result could not be committed safely.','global-clinic-usp-integration'),array('status'=>500));}if(!$this->commit_owned_transaction()){$this->fail_command($key,'commit_unknown');return new WP_Error('gcu_command_commit_unknown',__('The command commit could not be confirmed. Retry with the same idempotency key to obtain authoritative status.','global-clinic-usp-integration'),array('status'=>503));}return$r;}finally{GCU_Hardening::release_db_lock($lock);}}"""
repo=method_replace(repo,'run_idempotent_command','private function fail_command',run_cmd)

# Audit uses stable key; audit named lock is retained through outer transaction commit.
audit="""public function audit($action,$type,$id,$purpose,$reason,array$before,array$after){global$wpdb;$t=GCU_Install::tables();$owned=false;if($this->transaction_active()){if(!$this->transaction_audit_lock){$this->transaction_audit_lock=GCU_Hardening::acquire_db_lock('audit-chain',3);if(!$this->transaction_audit_lock){$this->mark_containment('audit_lock_failed');return false;}}$lock=$this->transaction_audit_lock;}else{$lock=GCU_Hardening::acquire_db_lock('audit-chain',3);$owned=true;if(!$lock){$this->mark_containment('audit_lock_failed');return false;}}try{$prev=(string)$wpdb->get_var(\"SELECT row_hash FROM {$t['audit']} ORDER BY id DESC LIMIT 1\");if(''===$prev){$prev=str_repeat('0',64);}$r=array('trace_id'=>GCU_Policy::trace_id(),'actor_id'=>get_current_user_id(),'action_name'=>sanitize_key($action),'object_type'=>sanitize_key($type),'object_id'=>GCU_Hardening::bounded_text(sanitize_text_field($id),191),'purpose'=>GCU_Hardening::bounded_text(sanitize_key($purpose),100),'reason'=>GCU_Hardening::bounded_text(sanitize_textarea_field($reason),self::MAX_REASON),'before_hash'=>$before?hash('sha256',wp_json_encode($before)):null,'after_hash'=>$after?hash('sha256',wp_json_encode($after)):null,'previous_hash'=>$prev,'created_at'=>current_time('mysql',true));$r['row_hash']=$this->audit_row_hash($r);if(!$r['row_hash']||false===$wpdb->insert($t['audit'],$r)){$this->mark_containment('audit_write_failed');return false;}return$r['trace_id'];}finally{if($owned){GCU_Hardening::release_db_lock($lock);}}}"""
repo=method_replace(repo,'audit','private function audit_row_hash',audit)
repo=regex_replace.__name__ if False else repo
# literal replacement for one-line audit hash method.
repo=re.sub(r"private function audit_row_hash\(array\$r\)\{.*?\}\npublic function verify_audit_chain", "private function audit_row_hash(array$r){return GCU_Integrity::stable_audit_row_hash($r);}\npublic function verify_audit_chain", repo, count=1, flags=re.S)

# Outbox insertion is transactionally durable; dispatch is deferred until outer commit.
publish="""public function publish_event($name,array$payload){global$wpdb;$t=GCU_Install::tables();$id=wp_generate_uuid4();$payload['event_id']=$id;$payload['event_name']=sanitize_text_field($name);$payload['event_version']=1;$payload['occurred_at']=gmdate('c');$payload=GCU_Hardening::sanitize_structured_value($payload);$enc=wp_json_encode($payload);if(false===$enc||strlen($enc)>20000){$this->mark_containment('outbox_payload_invalid');return false;}$ins=$wpdb->insert($t['outbox'],array('event_id'=>$id,'event_name'=>sanitize_text_field($name),'event_version'=>1,'payload'=>$enc,'status'=>'pending','attempts'=>0,'created_at'=>current_time('mysql',true)));if(false===$ins){$this->mark_containment('outbox_write_failed');return false;}if($this->transaction_active()){$this->deferred_event_ids[]=$id;}else{$this->dispatch_outbox($id,1);}return$id;}"""
repo=method_replace(repo,'publish_event','public function dispatch_outbox',publish)

# Lifecycle retention is bounded but draining; a fixed single 500-row deletion can permanently lag high-volume traffic.
repo=re.sub(r"public function cleanup_lifecycle\(\)\{.*?\}\nprivate function sanitize_datetime", r'''private function delete_batches($sql,$max_batches=20){global$wpdb;$total=0;for($i=0;$i<$max_batches;$i++){$n=$wpdb->query($sql);if(false===$n){return false;}$total+=(int)$n;if((int)$n<500){return$total;}}GCU_Observability::log('warning','lifecycle_cleanup_backlog',array('deleted'=>$total));return$total;}
public function cleanup_lifecycle(){global$wpdb;$t=GCU_Install::tables();$event_sql=$wpdb->prepare("DELETE FROM {$t['events']} WHERE occurred_at<DATE_SUB(UTC_TIMESTAMP(),INTERVAL %d DAY) LIMIT 500",self::EVENT_RETENTION_DAYS);return array('tokens'=>$this->delete_batches("DELETE FROM {$t['event_tokens']} WHERE expires_at<UTC_TIMESTAMP() OR consumed_at<DATE_SUB(UTC_TIMESTAMP(),INTERVAL 1 DAY) LIMIT 500"),'rate_limits'=>$this->delete_batches("DELETE FROM {$t['rate_limits']} WHERE expires_at<UTC_TIMESTAMP() LIMIT 500"),'events'=>$this->delete_batches($event_sql),'outbox'=>$this->delete_batches("DELETE FROM {$t['outbox']} WHERE status='sent' AND dispatched_at<DATE_SUB(UTC_TIMESTAMP(),INTERVAL 30 DAY) LIMIT 500"),'inbox'=>$this->delete_batches("DELETE FROM {$t['inbox']} WHERE status='processed' AND processed_at<DATE_SUB(UTC_TIMESTAMP(),INTERVAL 30 DAY) LIMIT 500"),'commands'=>$this->delete_batches("DELETE FROM {$t['commands']} WHERE status='complete' AND updated_at<DATE_SUB(UTC_TIMESTAMP(),INTERVAL 7 DAY) LIMIT 500"));}
private function sanitize_datetime''',repo,count=1,flags=re.S)
write(repo_rel,repo)
git_commit('Rounds 13-17 and 19: make owner mutations, audit, outbox, and idempotency atomic', [repo_rel])

# ---------------------------------------------------------------------------
# Round 08 privacy callers move to stable File 14 pseudonym key.
# ---------------------------------------------------------------------------
priv_rel='14-global-clinic-usp-integration/includes/class-gcu-privacy.php'
priv=read(priv_rel)
priv=priv.replace("return hash_hmac( 'sha256', $s, wp_salt( 'secure_auth' ) );", "return GCU_Integrity::user_subject_hash( $s );",1)
priv=priv.replace("return hash_hmac( 'sha256', $subject, wp_salt( 'secure_auth' ) );", "return GCU_Integrity::privacy_hash( 'event-guest|' . $subject );",1)
# Future report privacy export/erase lookups must use the same stable actor identity.
priv=priv.replace("$actor = hash_hmac( 'sha256', 'u:' . $user->ID, wp_salt( 'auth' ) );", "$actor = GCU_Integrity::future_actor_hash( $user->ID );")
write(priv_rel,priv)

# Future Intelligence: stable actor hash, transaction manager for freshness/revalidation, request fingerprints.
future_rel='14-global-clinic-usp-integration/includes/class-gcu-future-intelligence.php'
future=read(future_rel)
future=future.replace("hash_hmac( 'sha256', is_user_logged_in() ? 'u:' . get_current_user_id() : 'guest', wp_salt( 'auth' ) )", "is_user_logged_in() ? GCU_Integrity::future_actor_hash( get_current_user_id() ) : null")
# Bind future idempotency keys to canonical payloads.
future=future.replace("run_idempotent_command( 'future_report', $key, static function() use ( $data ) { return self::create_report( $data ); } )", "run_idempotent_command( 'future_report', $key, static function() use ( $data ) { return self::create_report( $data ); }, GCU_Hardening::request_fingerprint( $data ) )",1)
future=future.replace("} );\n\t\treturn is_wp_error( $result ) ? $result : self::no_store_response( $result, 201 );\n\t}\n\n\tpublic static function rest_reports", "}, GCU_Hardening::request_fingerprint( array( 'record_type'=>$type, 'record_key'=>$key, 'payload'=>$data ) ) );\n\t\treturn is_wp_error( $result ) ? $result : self::no_store_response( $result, 201 );\n\t}\n\n\tpublic static function rest_reports",1)
future=future.replace("run_idempotent_command( 'future_claim_revalidate', $idempotency, static function() use ( $key, $expected, $reason ) { return self::revalidate_claim( $key, $expected, $reason ); } )", "run_idempotent_command( 'future_claim_revalidate', $idempotency, static function() use ( $key, $expected, $reason ) { return self::revalidate_claim( $key, $expected, $reason ); }, GCU_Hardening::request_fingerprint( array( 'claim_key'=>$key, 'expected_version'=>$expected, 'reason'=>$reason ) ) )",1)
# Freshness sentinel transaction + audit are one unit.
future=future.replace("if ( false === $wpdb->query( 'START TRANSACTION' ) ) {\n\t\t\t\t\tcontinue;\n\t\t\t\t}", "if ( ! GCU_Plugin::instance()->repository()->begin_owned_transaction() ) {\n\t\t\t\t\tcontinue;\n\t\t\t\t}",1)
future=future.replace("$wpdb->query( 'ROLLBACK' );\n\t\t\t\t\tcontinue;", "GCU_Plugin::instance()->repository()->rollback_owned_transaction();\n\t\t\t\t\tcontinue;",2)
old="""\t\t\t\tif ( 1 !== $done || false === $wpdb->query( 'COMMIT' ) ) {
\t\t\t\t\t$wpdb->query( 'ROLLBACK' );
\t\t\t\t\tcontinue;
\t\t\t\t}
\t\t\t\t$count++;
\t\t\t\tGCU_Plugin::instance()->repository()->audit( 'claim_freshness_blocked', 'claim', $row['claim_key'], 'claim_governance', 'Review due or expiry reached', $row, array( 'status' => 'review_required', 'is_public' => 0 ) );"""
new="""\t\t\t\tif ( 1 !== $done || false === GCU_Plugin::instance()->repository()->audit( 'claim_freshness_blocked', 'claim', $row['claim_key'], 'claim_governance', 'Review due or expiry reached', $row, array( 'status' => 'review_required', 'is_public' => 0 ) ) || ! GCU_Plugin::instance()->repository()->commit_owned_transaction() ) {
\t\t\t\t\tGCU_Plugin::instance()->repository()->rollback_owned_transaction();
\t\t\t\t\tcontinue;
\t\t\t\t}
\t\t\t\t$count++;"""
if old not in future: raise SystemExit('future freshness commit block missing')
future=future.replace(old,new,1)
# Revalidate claim uses nested-safe repository transaction and writes mandatory audit before commit.
future=future.replace("if ( false === $wpdb->query( 'START TRANSACTION' ) ) {\n\t\t\t\treturn new WP_Error( 'gcu_future_claim_transaction_failed'", "if ( ! GCU_Plugin::instance()->repository()->begin_owned_transaction() ) {\n\t\t\t\treturn new WP_Error( 'gcu_future_claim_transaction_failed'",1)
# within revalidate only, replace rollback calls after method marker.
idx=future.find('public static function revalidate_claim')
end=future.find('public static function quality_score',idx)
seg=future[idx:end]
seg=seg.replace("$wpdb->query( 'ROLLBACK' );", "GCU_Plugin::instance()->repository()->rollback_owned_transaction();")
old2="""\t\t\tif ( 1 !== $done || false === $wpdb->query( 'COMMIT' ) ) {
\t\t\t\tGCU_Plugin::instance()->repository()->rollback_owned_transaction();
\t\t\t\treturn new WP_Error( 'gcu_future_claim_revalidation_failed', __( 'Claim revalidation could not be committed.', 'global-clinic-usp-integration' ), array( 'status' => 409 ) );
\t\t\t}
\t\t\tGCU_Plugin::instance()->repository()->audit( 'claim_revalidated', 'claim', $key, 'claim_governance', $reason, $row, array( 'status' => 'active', 'review_due_at' => $review_due ) );"""
new2="""\t\t\tif ( 1 !== $done || false === GCU_Plugin::instance()->repository()->audit( 'claim_revalidated', 'claim', $key, 'claim_governance', $reason, $row, array( 'status' => 'active', 'review_due_at' => $review_due ) ) || ! GCU_Plugin::instance()->repository()->commit_owned_transaction() ) {
\t\t\t\tGCU_Plugin::instance()->repository()->rollback_owned_transaction();
\t\t\t\treturn new WP_Error( 'gcu_future_claim_revalidation_failed', __( 'Claim revalidation could not be committed with its mandatory audit record.', 'global-clinic-usp-integration' ), array( 'status' => 409 ) );
\t\t\t}"""
if old2 not in seg: raise SystemExit('revalidate commit/audit block missing')
seg=seg.replace(old2,new2,1)
future=future[:idx]+seg+future[end:]
write(future_rel,future)

# Fifth early-stop must use the same transaction manager so its audit-chain lock remains held through commit.
fifth_rel='14-global-clinic-usp-integration/includes/class-gcu-fifth-review-hardening.php'
fifth=read(fifth_rel)
fifth=fifth.replace("if ( false === $wpdb->query( 'START TRANSACTION' ) ) {", "if ( ! GCU_Plugin::instance()->repository()->begin_owned_transaction() ) {",1)
# Replace rollback/commit only within transactional_early_stop_guard method.
idx=fifth.find('transactional_early_stop_guard')
end=fifth.find('\n\t}',idx)+3
seg=fifth[idx:end]
seg=seg.replace("$wpdb->query( 'ROLLBACK' );", "GCU_Plugin::instance()->repository()->rollback_owned_transaction();")
seg=seg.replace("false === $wpdb->query( 'COMMIT' )", "! GCU_Plugin::instance()->repository()->commit_owned_transaction()")
fifth=fifth[:idx]+seg+fifth[end:]
write(fifth_rel,fifth)

git_commit('Rounds 08 and 15: bind privacy identity and Future governance to stable atomic primitives', [priv_rel,future_rel,fifth_rel])

# ---------------------------------------------------------------------------
# Round 16: all base REST idempotent commands supply canonical fingerprints.
# ---------------------------------------------------------------------------
rest_rel='14-global-clinic-usp-integration/includes/class-gcu-rest.php'
rest=read(rest_rel)
rest=rest.replace("run_idempotent_command('create_content',$k,function()use($d){return GCU_Plugin::instance()->repository()->create_content_draft(is_array($d)?$d:array(),get_current_user_id());})", "run_idempotent_command('create_content',$k,function()use($d){return GCU_Plugin::instance()->repository()->create_content_draft(is_array($d)?$d:array(),get_current_user_id());},GCU_Hardening::request_fingerprint(is_array($d)?$d:array()))",1)
rest=rest.replace("run_idempotent_command('create_placement',$k,function()use($d){return GCU_Plugin::instance()->repository()->create_placement(is_array($d)?$d:array());})", "run_idempotent_command('create_placement',$k,function()use($d){return GCU_Plugin::instance()->repository()->create_placement(is_array($d)?$d:array());},GCU_Hardening::request_fingerprint(is_array($d)?$d:array()))",1)
rest=rest.replace("run_idempotent_command('create_experiment',$k,function()use($d){return GCU_Plugin::instance()->repository()->create_experiment(is_array($d)?$d:array());})", "run_idempotent_command('create_experiment',$k,function()use($d){return GCU_Plugin::instance()->repository()->create_experiment(is_array($d)?$d:array());},GCU_Hardening::request_fingerprint(is_array($d)?$d:array()))",1)
rest=rest.replace("run_idempotent_command('withdraw_claim',$k,function()use($r){return GCU_Plugin::instance()->repository()->withdraw_claim($r['claim_key'],absint($r->get_param('expected_version')),sanitize_textarea_field($r->get_param('reason')));})", "run_idempotent_command('withdraw_claim',$k,function()use($r){return GCU_Plugin::instance()->repository()->withdraw_claim($r['claim_key'],absint($r->get_param('expected_version')),sanitize_textarea_field($r->get_param('reason')));},GCU_Hardening::request_fingerprint(array('claim_key'=>$r['claim_key'],'expected_version'=>absint($r->get_param('expected_version')),'reason'=>sanitize_textarea_field($r->get_param('reason')))))",1)
rest=rest.replace("run_idempotent_command('transition_'.sanitize_key($r['machine']),$k,function()use($r){return GCU_Plugin::instance()->repository()->transition($r['machine'],$r['public_id'],absint($r->get_param('expected_version')),sanitize_key($r->get_param('target')),sanitize_textarea_field($r->get_param('reason')));})", "run_idempotent_command('transition_'.sanitize_key($r['machine']),$k,function()use($r){return GCU_Plugin::instance()->repository()->transition($r['machine'],$r['public_id'],absint($r->get_param('expected_version')),sanitize_key($r->get_param('target')),sanitize_textarea_field($r->get_param('reason')));},GCU_Hardening::request_fingerprint(array('machine'=>$r['machine'],'public_id'=>$r['public_id'],'expected_version'=>absint($r->get_param('expected_version')),'target'=>sanitize_key($r->get_param('target')),'reason'=>sanitize_textarea_field($r->get_param('reason')))))",1)
write(rest_rel,rest)
git_commit('Round 16: bind every REST idempotency key to its request payload', [rest_rel])

# ---------------------------------------------------------------------------
# Round 10: invalid audit integrity is a containment condition, not only alert.
# ---------------------------------------------------------------------------
obs_rel='14-global-clinic-usp-integration/includes/class-gcu-observability.php'
obs=read(obs_rel)
old="$r=$this->health_report();update_option('gcu_last_health_report',$r,false);$warn="
new="$r=$this->health_report();update_option('gcu_last_health_report',$r,false);if(empty($r['audit_chain']['valid'])){update_option('gcu_enabled',0,false);GCU_Observability::log('critical','audit_chain_integrity_failed',array('scope'=>isset($r['audit_chain']['scope'])?$r['audit_chain']['scope']:'unknown'));}$warn="
if old not in obs: raise SystemExit('daily governance marker missing')
obs=obs.replace(old,new,1)
write(obs_rel,obs)

# Explicit purge removes newly-owned key/migration options.
un_rel='14-global-clinic-usp-integration/uninstall.php'
un=read(un_rel)
un=un.replace("'gcu_future_last_anomaly', 'gcu_future_last_parity',", "'gcu_future_last_anomaly', 'gcu_future_last_parity',\n\t'gcu_audit_hmac_key_v1', 'gcu_privacy_hmac_key_v1', 'gcu_integrity_key_migration_v1',",1)
write(un_rel,un)
git_commit('Round 10: contain invalid audit integrity and complete purge ownership', [obs_rel,un_rel])

# ---------------------------------------------------------------------------
# Release identity + tests/docs minimum forward alignment for v1.4.4/schema10005.
# ---------------------------------------------------------------------------
readme_rel='14-global-clinic-usp-integration/readme.txt'
readme=read(readme_rel).replace('Stable tag: 1.4.3','Stable tag: 1.4.4').replace('= 1.4.3 =','= 1.4.4 =')
write(readme_rel,readme)
root_rel='README.md'
root=read(root_rel).replace('Software candidate: `1.4.3`','Software candidate: `1.4.4`').replace('v1.4.3','v1.4.4')
write(root_rel,root)
status_rel='STATUS.md'
status=read(status_rel).replace('# File 14 v1.4.3 — Fifth-Review Repository Candidate','# File 14 v1.4.4 — Sixth-Review Repository Candidate').replace('Software: `1.4.3`','Software: `1.4.4`').replace('Base schema: `10004`','Base schema: `10005`')
status += "\n\n## Sixth independent 80-pass review (2026-08-11)\n\nA new exact-main-derived review began at `d40a366e8e1c2c2e8a8327f8286803a0aa95c7d7`. Defect-bearing rounds are recorded in `docs/REVIEW-80-SIXTH-LEDGER-v1.4.4.md`. Repository and automated evidence remain separate from staging/live truth.\n"
write(status_rel,status)

# Contract test forward alignment and new invariants.
test_rel='tests/contract-tests.php'
test=read(test_rel)
test=test.replace("'includes/class-gcu-hardening.php'", "'includes/class-gcu-hardening.php','includes/class-gcu-integrity.php'",1)
test=test.replace("Version: 1.4.3", "Version: 1.4.4").replace("GCU_VERSION', '1.4.3", "GCU_VERSION', '1.4.4").replace("Version 1.4.3 drift.","Version 1.4.4 drift.").replace("GCU_SCHEMA_VERSION', 10004","GCU_SCHEMA_VERSION', 10005").replace("Schema 10004 missing.","Schema 10005 missing.")
test=test.replace("$main=tx('global-clinic-usp-integration.php');$hard=", "$main=tx('global-clinic-usp-integration.php');$integrity=tx('includes/class-gcu-integrity.php');$hard=",1)
test=test.replace("ck(false!==strpos($main,'class-gcu-hardening.php')", "ck(false!==strpos($main,'class-gcu-integrity.php')&&false!==strpos($integrity,'migrate_legacy_hashes')&&false!==strpos($integrity,'stable_audit_row_hash'),'Stable integrity migration missing.');ck(false!==strpos($main,'class-gcu-hardening.php')",1)
test=test.replace("'commands'=>array('command_key'", "'commands'=>array('command_key'",1) if False else test
# Append static invariants before final failure block.
needle="foreach(array('guaranteed income'"
extra="ck(false!==strpos($install,'request_hash char(64)')&&false!==strpos($repo,'gcu_idempotency_payload_conflict'),'Request-bound idempotency missing.');ck(false!==strpos($repo,'begin_owned_transaction')&&false!==strpos($repo,'deferred_event_ids')&&false!==strpos($repo,'transaction_audit_lock'),'Atomic owner/audit/outbox transaction manager missing.');ck(false!==strpos($repo,'placement_ready($row)'),'Request-time File20 slot readiness missing.');ck(false!==strpos($obs,'audit_chain_integrity_failed'),'Audit integrity containment missing.');ck(false!==strpos($install,'IS_USED_LOCK'),'Stale install-lock recovery missing.');ck(false!==strpos($priv,'GCU_Integrity::user_subject_hash')&&false!==strpos($future,'GCU_Integrity::future_actor_hash'),'Stable privacy subject linkage missing.');\n"
if needle not in test: raise SystemExit('contract final needle missing')
test=test.replace(needle,extra+needle,1)
write(test_rel,test)

# Broad version/schema tests that still pin previous corrective release are made current.
for rel in ['tests/central-plan-tests.php','tests/fifth-review-regression-tests.php','tests/fourth-review-regression-tests.php','tests/third-review-regression-tests.php','tests/second-review-regression-tests.php']:
    if not (ROOT/rel).exists(): continue
    x=read(rel).replace('1.4.3','1.4.4').replace('10004','10005')
    write(rel,x)

# Build shell target if hard-coded.
build_sh='scripts/build.sh'
if (ROOT/build_sh).exists():
    x=read(build_sh).replace('1.4.3','1.4.4')
    write(build_sh,x)

git_commit('Release: align File 14 v1.4.4 schema 10005 evidence and regression contracts')

# Remove this temporary transformer and one-shot workflow in the final correction commit.
for rel in ['scripts/apply-sixth-review-corrections.py','.github/workflows/file14-sixth-review-corrections.yml']:
    p=ROOT/rel
    if p.exists(): p.unlink()
subprocess.run(['git','add','-A'],cwd=ROOT,check=True)
subprocess.run(['git','commit','-m','Remove temporary sixth-review corrective machinery'],cwd=ROOT,check=True)
print('Sixth-review corrections applied in ordered commits.')
