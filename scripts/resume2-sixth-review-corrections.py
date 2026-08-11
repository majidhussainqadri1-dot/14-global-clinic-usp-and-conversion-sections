#!/usr/bin/env python3
from pathlib import Path
import subprocess, sys
ROOT=Path(__file__).resolve().parents[1]

def read(rel): return (ROOT/rel).read_text(encoding='utf-8')
def write(rel,s): (ROOT/rel).write_text(s,encoding='utf-8')
def must(s,old,new,label,count=1):
    if s.count(old)<count: raise SystemExit(f'{label}: pattern missing')
    return s.replace(old,new,count)
def commit(msg,paths=None):
    subprocess.run(['git','add','-A'] if paths is None else ['git','add',*paths],cwd=ROOT,check=True)
    if subprocess.run(['git','diff','--cached','--quiet'],cwd=ROOT).returncode==0: raise SystemExit('no staged change: '+msg)
    subprocess.run(['git','commit','-m',msg],cwd=ROOT,check=True)

# At this point the prior continuation has already written Future and privacy edits
# to the working tree, but stopped before committing them. Complete the early-stop
# conversion and commit the whole atomic-governance batch.
fifth_rel='14-global-clinic-usp-integration/includes/class-gcu-fifth-review-hardening.php'
fifth=read(fifth_rel)
start=fifth.find('public static function transactional_early_stop_guard()')
end=fifth.find('\n\tprivate static function contains_sensitive_copy_data',start)
if start<0 or end<0: raise SystemExit('early-stop boundaries missing')
seg=fifth[start:end]
seg=must(seg,"if ( false === $wpdb->query( 'START TRANSACTION' ) ) {","if ( ! GCU_Plugin::instance()->repository()->begin_owned_transaction() ) {",'early-stop tx start')
seg=seg.replace("$wpdb->query( 'ROLLBACK' );","GCU_Plugin::instance()->repository()->rollback_owned_transaction();")
seg=must(seg,"if ( false === $wpdb->query( 'COMMIT' ) ) {","if ( ! GCU_Plugin::instance()->repository()->commit_owned_transaction() ) {",'early-stop commit')
fifth=fifth[:start]+seg+fifth[end:]
write(fifth_rel,fifth)
commit('Rounds 08 and 15: bind privacy identity and Future governance to stable atomic primitives',[fifth_rel,'14-global-clinic-usp-integration/includes/class-gcu-future-intelligence.php','14-global-clinic-usp-integration/includes/class-gcu-privacy.php'])

# Bind all base mutating REST commands to canonical request fingerprints.
rest_rel='14-global-clinic-usp-integration/includes/class-gcu-rest.php'
rest=read(rest_rel)
rest=must(rest,"run_idempotent_command('create_content',$k,function()use($d){return GCU_Plugin::instance()->repository()->create_content_draft(is_array($d)?$d:array(),get_current_user_id());})","run_idempotent_command('create_content',$k,function()use($d){return GCU_Plugin::instance()->repository()->create_content_draft(is_array($d)?$d:array(),get_current_user_id());},GCU_Hardening::request_fingerprint(is_array($d)?$d:array()))",'content fingerprint')
rest=must(rest,"run_idempotent_command('create_placement',$k,function()use($d){return GCU_Plugin::instance()->repository()->create_placement(is_array($d)?$d:array());})","run_idempotent_command('create_placement',$k,function()use($d){return GCU_Plugin::instance()->repository()->create_placement(is_array($d)?$d:array());},GCU_Hardening::request_fingerprint(is_array($d)?$d:array()))",'placement fingerprint')
rest=must(rest,"run_idempotent_command('create_experiment',$k,function()use($d){return GCU_Plugin::instance()->repository()->create_experiment(is_array($d)?$d:array());})","run_idempotent_command('create_experiment',$k,function()use($d){return GCU_Plugin::instance()->repository()->create_experiment(is_array($d)?$d:array());},GCU_Hardening::request_fingerprint(is_array($d)?$d:array()))",'experiment fingerprint')
rest=must(rest,"run_idempotent_command('withdraw_claim',$k,function()use($r){return GCU_Plugin::instance()->repository()->withdraw_claim($r['claim_key'],absint($r->get_param('expected_version')),sanitize_textarea_field($r->get_param('reason')));})","run_idempotent_command('withdraw_claim',$k,function()use($r){return GCU_Plugin::instance()->repository()->withdraw_claim($r['claim_key'],absint($r->get_param('expected_version')),sanitize_textarea_field($r->get_param('reason')));},GCU_Hardening::request_fingerprint(array('claim_key'=>$r['claim_key'],'expected_version'=>absint($r->get_param('expected_version')),'reason'=>sanitize_textarea_field($r->get_param('reason')))))",'withdraw fingerprint')
rest=must(rest,"run_idempotent_command('transition_'.sanitize_key($r['machine']),$k,function()use($r){return GCU_Plugin::instance()->repository()->transition($r['machine'],$r['public_id'],absint($r->get_param('expected_version')),sanitize_key($r->get_param('target')),sanitize_textarea_field($r->get_param('reason')));})","run_idempotent_command('transition_'.sanitize_key($r['machine']),$k,function()use($r){return GCU_Plugin::instance()->repository()->transition($r['machine'],$r['public_id'],absint($r->get_param('expected_version')),sanitize_key($r->get_param('target')),sanitize_textarea_field($r->get_param('reason')));},GCU_Hardening::request_fingerprint(array('machine'=>$r['machine'],'public_id'=>$r['public_id'],'expected_version'=>absint($r->get_param('expected_version')),'target'=>sanitize_key($r->get_param('target')),'reason'=>sanitize_textarea_field($r->get_param('reason')))))",'transition fingerprint')
write(rest_rel,rest)
commit('Round 16: bind every REST idempotency key to its request payload',[rest_rel])

# Invalid audit-chain integrity is a containment condition.
obs_rel='14-global-clinic-usp-integration/includes/class-gcu-observability.php'
obs=read(obs_rel)
obs=must(obs,"$r=$this->health_report();update_option('gcu_last_health_report',$r,false);$warn=","$r=$this->health_report();update_option('gcu_last_health_report',$r,false);if(empty($r['audit_chain']['valid'])){update_option('gcu_enabled',0,false);GCU_Observability::log('critical','audit_chain_integrity_failed',array('scope'=>isset($r['audit_chain']['scope'])?$r['audit_chain']['scope']:'unknown'));}$warn=",'audit containment')
write(obs_rel,obs)
un_rel='14-global-clinic-usp-integration/uninstall.php'
un=read(un_rel)
un=must(un,"'gcu_future_last_anomaly', 'gcu_future_last_parity',","'gcu_future_last_anomaly', 'gcu_future_last_parity',\n\t'gcu_audit_hmac_key_v1', 'gcu_privacy_hmac_key_v1', 'gcu_integrity_key_migration_v1',",'purge integrity options')
write(un_rel,un)
commit('Round 10: contain invalid audit integrity and complete purge ownership',[obs_rel,un_rel])

# Align release identity and regression fixtures to v1.4.4 / schema10005.
readme_rel='14-global-clinic-usp-integration/readme.txt';x=read(readme_rel).replace('Stable tag: 1.4.3','Stable tag: 1.4.4').replace('= 1.4.3 =','= 1.4.4 =');write(readme_rel,x)
root_rel='README.md';x=read(root_rel).replace('Software candidate: `1.4.3`','Software candidate: `1.4.4`').replace('v1.4.3','v1.4.4');write(root_rel,x)
status_rel='STATUS.md';x=read(status_rel).replace('# File 14 v1.4.3 — Fifth-Review Repository Candidate','# File 14 v1.4.4 — Sixth-Review Repository Candidate').replace('Software: `1.4.3`','Software: `1.4.4`').replace('Base schema: `10004`','Base schema: `10005`');x+='\n\n## Sixth independent 80-pass review (2026-08-11)\n\nExact-main baseline: `d40a366e8e1c2c2e8a8327f8286803a0aa95c7d7`. The sixth-review ledger is generated only after final-state QA. Repository evidence remains separate from staging/live truth.\n';write(status_rel,x)
for rel in ['tests/contract-tests.php','tests/central-plan-tests.php','tests/fifth-review-regression-tests.php','tests/fourth-review-regression-tests.php','tests/third-review-regression-tests.php','tests/second-review-regression-tests.php']:
    p=ROOT/rel
    if p.exists():
        x=read(rel).replace('1.4.3','1.4.4').replace('10004','10005')
        write(rel,x)
build='scripts/build.sh'
if (ROOT/build).exists(): write(build,read(build).replace('1.4.3','1.4.4'))
commit('Release: align File 14 v1.4.4 schema 10005 evidence and regression fixtures')

# Remove all one-shot correction machinery before final push.
for rel in ['scripts/apply-sixth-review-corrections.py','scripts/resume-sixth-review-corrections.py','scripts/resume2-sixth-review-corrections.py','.github/workflows/file14-sixth-review-corrections.yml']:
    p=ROOT/rel
    if p.exists(): p.unlink()
subprocess.run(['git','add','-A'],cwd=ROOT,check=True)
subprocess.run(['git','commit','-m','Remove temporary sixth-review corrective machinery'],cwd=ROOT,check=True)
print('Second continuation completed.')
