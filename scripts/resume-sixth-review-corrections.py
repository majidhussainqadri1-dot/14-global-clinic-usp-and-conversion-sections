#!/usr/bin/env python3
from pathlib import Path
import subprocess, re, sys
ROOT=Path(__file__).resolve().parents[1]
P=ROOT/'14-global-clinic-usp-integration'

def read(rel): return (ROOT/rel).read_text(encoding='utf-8')
def write(rel,s): (ROOT/rel).write_text(s,encoding='utf-8')
def must_replace(s,old,new,label,count=1):
    if s.count(old)<count: raise SystemExit(f'{label}: expected pattern missing')
    return s.replace(old,new,count)
def commit(msg, paths=None):
    subprocess.run(['git','add','-A'] if paths is None else ['git','add',*paths],cwd=ROOT,check=True)
    if subprocess.run(['git','diff','--cached','--quiet'],cwd=ROOT).returncode==0: raise SystemExit('no staged change: '+msg)
    subprocess.run(['git','commit','-m',msg],cwd=ROOT,check=True)

# The first transformer intentionally leaves privacy edits unstaged if it stops at the
# Future freshness pattern. Continue from that exact local state.
future_rel='14-global-clinic-usp-integration/includes/class-gcu-future-intelligence.php'
future=read(future_rel)
# Stable actor identity for report rows.
future=future.replace("hash_hmac( 'sha256', is_user_logged_in() ? 'u:' . get_current_user_id() : 'guest', wp_salt( 'auth' ) )","is_user_logged_in() ? GCU_Integrity::future_actor_hash( get_current_user_id() ) : null")
# Request-bound idempotency: report endpoint.
future=must_replace(future,
"GCU_Plugin::instance()->repository()->run_idempotent_command( 'future_report', $key, static function() use ( $data ) { return self::create_report( $data ); } );",
"GCU_Plugin::instance()->repository()->run_idempotent_command( 'future_report', $key, static function() use ( $data ) { return self::create_report( $data ); }, GCU_Hardening::request_fingerprint( $data ) );",'future report fingerprint')
# Request-bound idempotency: governed record endpoint.
old="""$result = GCU_Plugin::instance()->repository()->run_idempotent_command( 'future_record_write', $idempotency, static function() use ( $type, $key, $data ) { return self::upsert_record(
			$type, $key, GCU_Policy::sanitize_locale( isset( $data['locale'] ) ? $data['locale'] : 'en-US' ),
			self::sanitize_region( isset( $data['region'] ) ? $data['region'] : 'ZZ' ), isset( $data['payload'] ) && is_array( $data['payload'] ) ? $data['payload'] : array(),
			isset( $data['status'] ) ? sanitize_key( $data['status'] ) : 'draft', ! empty( $data['is_public'] ), isset( $data['expected_version'] ) ? absint( $data['expected_version'] ) : 0, false ); } );"""
new="""$result = GCU_Plugin::instance()->repository()->run_idempotent_command( 'future_record_write', $idempotency, static function() use ( $type, $key, $data ) { return self::upsert_record(
			$type, $key, GCU_Policy::sanitize_locale( isset( $data['locale'] ) ? $data['locale'] : 'en-US' ),
			self::sanitize_region( isset( $data['region'] ) ? $data['region'] : 'ZZ' ), isset( $data['payload'] ) && is_array( $data['payload'] ) ? $data['payload'] : array(),
			isset( $data['status'] ) ? sanitize_key( $data['status'] ) : 'draft', ! empty( $data['is_public'] ), isset( $data['expected_version'] ) ? absint( $data['expected_version'] ) : 0, false ); }, GCU_Hardening::request_fingerprint( array( 'record_type'=>$type, 'record_key'=>$key, 'data'=>$data ) ) );"""
future=must_replace(future,old,new,'future record fingerprint')
# Request-bound idempotency: claim revalidation.
future=must_replace(future,
"GCU_Plugin::instance()->repository()->run_idempotent_command( 'future_claim_revalidate', $idempotency, static function() use ( $key, $expected, $reason ) { return self::revalidate_claim( $key, $expected, $reason ); } );",
"GCU_Plugin::instance()->repository()->run_idempotent_command( 'future_claim_revalidate', $idempotency, static function() use ( $key, $expected, $reason ) { return self::revalidate_claim( $key, $expected, $reason ); }, GCU_Hardening::request_fingerprint( array( 'claim_key'=>$key, 'expected_version'=>$expected, 'reason'=>$reason ) ) );",'future claim fingerprint')

# Freshness sentinel: one owner transaction containing history, state and mandatory audit.
old="""if ( false === $wpdb->query( 'START TRANSACTION' ) ) {
					continue;
				}"""
new="""if ( ! GCU_Plugin::instance()->repository()->begin_owned_transaction() ) {
					continue;
				}"""
future=must_replace(future,old,new,'freshness tx start')
# Replace the two rollback statements in sentinel section only.
s0=future.index('public static function claim_freshness_sentinel()')
s1=future.index('public static function revalidate_claim',s0)
seg=future[s0:s1]
seg=seg.replace("$wpdb->query( 'ROLLBACK' );","GCU_Plugin::instance()->repository()->rollback_owned_transaction();")
old="""if ( 1 !== $done || false === $wpdb->query( 'COMMIT' ) ) {
					GCU_Plugin::instance()->repository()->rollback_owned_transaction();
					continue;
				}
				$count++;
				GCU_Plugin::instance()->repository()->audit( 'claim_freshness_blocked', 'claim', $row['claim_key'], 'claim_governance', 'Review due or expiry reached', $row, array( 'status' => 'review_required', 'is_public' => 0 ) );"""
new="""if ( 1 !== $done || false === GCU_Plugin::instance()->repository()->audit( 'claim_freshness_blocked', 'claim', $row['claim_key'], 'claim_governance', 'Review due or expiry reached', $row, array( 'status' => 'review_required', 'is_public' => 0 ) ) || ! GCU_Plugin::instance()->repository()->commit_owned_transaction() ) {
					GCU_Plugin::instance()->repository()->rollback_owned_transaction();
					continue;
				}
				$count++;"""
seg=must_replace(seg,old,new,'freshness commit/audit')
future=future[:s0]+seg+future[s1:]

# Claim revalidation: same transaction manager and audit-before-commit.
r0=future.index('public static function revalidate_claim')
r1=future.index('public static function quality_score',r0)
seg=future[r0:r1]
seg=must_replace(seg,"if ( false === $wpdb->query( 'START TRANSACTION' ) ) {","if ( ! GCU_Plugin::instance()->repository()->begin_owned_transaction() ) {",'revalidate tx start')
seg=seg.replace("$wpdb->query( 'ROLLBACK' );","GCU_Plugin::instance()->repository()->rollback_owned_transaction();")
old="""if ( 1 !== $done || false === $wpdb->query( 'COMMIT' ) ) {
				GCU_Plugin::instance()->repository()->rollback_owned_transaction();
				return new WP_Error( 'gcu_future_claim_revalidation_failed', __( 'Claim revalidation could not be committed.', 'global-clinic-usp-integration' ), array( 'status' => 409 ) );
			}
			GCU_Plugin::instance()->repository()->audit( 'claim_revalidated', 'claim', $key, 'claim_governance', $reason, $row, array( 'status' => 'active', 'review_due_at' => $review_due ) );"""
new="""if ( 1 !== $done || false === GCU_Plugin::instance()->repository()->audit( 'claim_revalidated', 'claim', $key, 'claim_governance', $reason, $row, array( 'status' => 'active', 'review_due_at' => $review_due ) ) || ! GCU_Plugin::instance()->repository()->commit_owned_transaction() ) {
				GCU_Plugin::instance()->repository()->rollback_owned_transaction();
				return new WP_Error( 'gcu_future_claim_revalidation_failed', __( 'Claim revalidation could not be committed with its mandatory audit record.', 'global-clinic-usp-integration' ), array( 'status' => 409 ) );
			}"""
seg=must_replace(seg,old,new,'revalidate commit/audit')
future=future[:r0]+seg+future[r1:]
write(future_rel,future)

# Fifth-review early stop must share the repository transaction so the audit lock is held to commit.
fifth_rel='14-global-clinic-usp-integration/includes/class-gcu-fifth-review-hardening.php'
fifth=read(fifth_rel)
needle='private static function transactional_early_stop_guard'
if needle not in fifth: raise SystemExit('early-stop method missing')
e0=fifth.index(needle)
e1=fifth.index('\n\t}',e0)+3
seg=fifth[e0:e1]
seg=must_replace(seg,"if ( false === $wpdb->query( 'START TRANSACTION' ) ) {","if ( ! GCU_Plugin::instance()->repository()->begin_owned_transaction() ) {",'early-stop tx start')
seg=seg.replace("$wpdb->query( 'ROLLBACK' );","GCU_Plugin::instance()->repository()->rollback_owned_transaction();")
seg=seg.replace("false === $wpdb->query( 'COMMIT' )","! GCU_Plugin::instance()->repository()->commit_owned_transaction()")
fifth=fifth[:e0]+seg+fifth[e1:]
write(fifth_rel,fifth)
commit('Rounds 08 and 15: bind privacy identity and Future governance to stable atomic primitives',[future_rel,fifth_rel,'14-global-clinic-usp-integration/includes/class-gcu-privacy.php'])

# Base REST: every durable command key is bound to canonical request content.
rest_rel='14-global-clinic-usp-integration/includes/class-gcu-rest.php'
rest=read(rest_rel)
rest=must_replace(rest,"run_idempotent_command('create_content',$k,function()use($d){return GCU_Plugin::instance()->repository()->create_content_draft(is_array($d)?$d:array(),get_current_user_id());})","run_idempotent_command('create_content',$k,function()use($d){return GCU_Plugin::instance()->repository()->create_content_draft(is_array($d)?$d:array(),get_current_user_id());},GCU_Hardening::request_fingerprint(is_array($d)?$d:array()))",'content fingerprint')
rest=must_replace(rest,"run_idempotent_command('create_placement',$k,function()use($d){return GCU_Plugin::instance()->repository()->create_placement(is_array($d)?$d:array());})","run_idempotent_command('create_placement',$k,function()use($d){return GCU_Plugin::instance()->repository()->create_placement(is_array($d)?$d:array());},GCU_Hardening::request_fingerprint(is_array($d)?$d:array()))",'placement fingerprint')
rest=must_replace(rest,"run_idempotent_command('create_experiment',$k,function()use($d){return GCU_Plugin::instance()->repository()->create_experiment(is_array($d)?$d:array());})","run_idempotent_command('create_experiment',$k,function()use($d){return GCU_Plugin::instance()->repository()->create_experiment(is_array($d)?$d:array());},GCU_Hardening::request_fingerprint(is_array($d)?$d:array()))",'experiment fingerprint')
rest=must_replace(rest,"run_idempotent_command('withdraw_claim',$k,function()use($r){return GCU_Plugin::instance()->repository()->withdraw_claim($r['claim_key'],absint($r->get_param('expected_version')),sanitize_textarea_field($r->get_param('reason')));})","run_idempotent_command('withdraw_claim',$k,function()use($r){return GCU_Plugin::instance()->repository()->withdraw_claim($r['claim_key'],absint($r->get_param('expected_version')),sanitize_textarea_field($r->get_param('reason')));},GCU_Hardening::request_fingerprint(array('claim_key'=>$r['claim_key'],'expected_version'=>absint($r->get_param('expected_version')),'reason'=>sanitize_textarea_field($r->get_param('reason')))))",'withdraw fingerprint')
rest=must_replace(rest,"run_idempotent_command('transition_'.sanitize_key($r['machine']),$k,function()use($r){return GCU_Plugin::instance()->repository()->transition($r['machine'],$r['public_id'],absint($r->get_param('expected_version')),sanitize_key($r->get_param('target')),sanitize_textarea_field($r->get_param('reason')));})","run_idempotent_command('transition_'.sanitize_key($r['machine']),$k,function()use($r){return GCU_Plugin::instance()->repository()->transition($r['machine'],$r['public_id'],absint($r->get_param('expected_version')),sanitize_key($r->get_param('target')),sanitize_textarea_field($r->get_param('reason')));},GCU_Hardening::request_fingerprint(array('machine'=>$r['machine'],'public_id'=>$r['public_id'],'expected_version'=>absint($r->get_param('expected_version')),'target'=>sanitize_key($r->get_param('target')),'reason'=>sanitize_textarea_field($r->get_param('reason')))))",'transition fingerprint')
write(rest_rel,rest)
commit('Round 16: bind every REST idempotency key to its request payload',[rest_rel])

# Audit-chain integrity failure is containment, not merely a warning.
obs_rel='14-global-clinic-usp-integration/includes/class-gcu-observability.php'
obs=read(obs_rel)
obs=must_replace(obs,"$r=$this->health_report();update_option('gcu_last_health_report',$r,false);$warn=","$r=$this->health_report();update_option('gcu_last_health_report',$r,false);if(empty($r['audit_chain']['valid'])){update_option('gcu_enabled',0,false);GCU_Observability::log('critical','audit_chain_integrity_failed',array('scope'=>isset($r['audit_chain']['scope'])?$r['audit_chain']['scope']:'unknown'));}$warn=",'audit containment')
write(obs_rel,obs)
un_rel='14-global-clinic-usp-integration/uninstall.php'
un=read(un_rel)
un=must_replace(un,"'gcu_future_last_anomaly', 'gcu_future_last_parity',","'gcu_future_last_anomaly', 'gcu_future_last_parity',\n\t'gcu_audit_hmac_key_v1', 'gcu_privacy_hmac_key_v1', 'gcu_integrity_key_migration_v1',",'purge integrity options')
write(un_rel,un)
commit('Round 10: contain invalid audit integrity and complete purge ownership',[obs_rel,un_rel])

# Release identity and tests/docs alignment.
readme_rel='14-global-clinic-usp-integration/readme.txt'
x=read(readme_rel).replace('Stable tag: 1.4.3','Stable tag: 1.4.4').replace('= 1.4.3 =','= 1.4.4 =');write(readme_rel,x)
root_rel='README.md';x=read(root_rel).replace('Software candidate: `1.4.3`','Software candidate: `1.4.4`').replace('v1.4.3','v1.4.4');write(root_rel,x)
status_rel='STATUS.md';x=read(status_rel).replace('# File 14 v1.4.3 — Fifth-Review Repository Candidate','# File 14 v1.4.4 — Sixth-Review Repository Candidate').replace('Software: `1.4.3`','Software: `1.4.4`').replace('Base schema: `10004`','Base schema: `10005`');x+='\n\n## Sixth independent 80-pass review (2026-08-11)\n\nExact-main baseline: `d40a366e8e1c2c2e8a8327f8286803a0aa95c7d7`. The sixth-review ledger is generated only after final-state QA. Repository evidence remains separate from staging/live truth.\n';write(status_rel,x)
# Forward-align exact-version/schema static tests; later sixth-review gate will assert new invariants independently.
for rel in ['tests/contract-tests.php','tests/central-plan-tests.php','tests/fifth-review-regression-tests.php','tests/fourth-review-regression-tests.php','tests/third-review-regression-tests.php','tests/second-review-regression-tests.php']:
    p=ROOT/rel
    if p.exists():
        x=read(rel).replace('1.4.3','1.4.4').replace('10004','10005')
        if rel=='tests/contract-tests.php':
            x=x.replace("'includes/class-gcu-hardening.php'","'includes/class-gcu-hardening.php','includes/class-gcu-integrity.php'",1)
            x=x.replace("$main=tx('global-clinic-usp-integration.php');$hard=","$main=tx('global-clinic-usp-integration.php');$integrity=tx('includes/class-gcu-integrity.php');$hard=",1)
            anchor="foreach(array('guaranteed income'"
            checks="ck(false!==strpos($install,'request_hash char(64)')&&false!==strpos($repo,'gcu_idempotency_payload_conflict'),'Request-bound idempotency missing.');ck(false!==strpos($repo,'begin_owned_transaction')&&false!==strpos($repo,'deferred_event_ids')&&false!==strpos($repo,'transaction_audit_lock'),'Atomic owner/audit/outbox transaction manager missing.');ck(false!==strpos($repo,'placement_ready($row)'),'Request-time File20 slot readiness missing.');ck(false!==strpos($obs,'audit_chain_integrity_failed'),'Audit integrity containment missing.');ck(false!==strpos($install,'IS_USED_LOCK'),'Stale install-lock recovery missing.');ck(false!==strpos($priv,'GCU_Integrity::user_subject_hash')&&false!==strpos($future,'GCU_Integrity::future_actor_hash'),'Stable privacy subject linkage missing.');\n"
            if anchor in x: x=x.replace(anchor,checks+anchor,1)
        write(rel,x)
build='scripts/build.sh'
if (ROOT/build).exists(): write(build,read(build).replace('1.4.3','1.4.4'))
commit('Release: align File 14 v1.4.4 schema 10005 evidence and regression contracts')

# Remove temporary corrective machinery. The branch must end with normal review/release tooling only.
for rel in ['scripts/apply-sixth-review-corrections.py','scripts/resume-sixth-review-corrections.py','.github/workflows/file14-sixth-review-corrections.yml']:
    p=ROOT/rel
    if p.exists(): p.unlink()
subprocess.run(['git','add','-A'],cwd=ROOT,check=True)
subprocess.run(['git','commit','-m','Remove temporary sixth-review corrective machinery'],cwd=ROOT,check=True)
print('Sixth-review corrective continuation completed.')
