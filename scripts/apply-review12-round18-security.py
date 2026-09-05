from pathlib import Path
root=Path('.')
repository=root/'14-global-clinic-usp-integration/includes/class-gcu-repository.php'
s=repository.read_text(encoding='utf-8')
old="private function validate_inbound_event($name,array$p){if(!in_array($name,array('DoctorDirectoryAvailable.v1','ClinicBookingAvailable.v1','DoctorOnboardingAvailable.v1','BusinessPolicyChanged.v1'),true)||empty($p['event_id'])||!wp_is_uuid($p['event_id'])){return new WP_Error('gcu_inbound_schema_invalid','Invalid event envelope.');}if('BusinessPolicyChanged.v1'!==$name){"
new="private function validate_inbound_event($name,array$p){if(!in_array($name,array('DoctorDirectoryAvailable.v1','ClinicBookingAvailable.v1','DoctorOnboardingAvailable.v1','BusinessPolicyChanged.v1'),true)||empty($p['event_id'])||!wp_is_uuid($p['event_id'])){return new WP_Error('gcu_inbound_schema_invalid','Invalid event envelope.');}$contract_version=isset($p['contract_version'])?absint($p['contract_version']):1;if(1!==$contract_version){return new WP_Error('gcu_inbound_contract_version_unsupported','Unsupported inbound owner contract version.');}if('BusinessPolicyChanged.v1'!==$name){"
if s.count(old)!=1: raise SystemExit(f'repository anchor count={s.count(old)}')
repository.write_text(s.replace(old,new,1),encoding='utf-8')

uninstall=root/'14-global-clinic-usp-integration/uninstall.php'
u=uninstall.read_text(encoding='utf-8')
old_u="\t&& ! empty( $approval['backup_verified_at'] )\n\t&& (int) $approval['backup_verified_at'] <= $now\n\t&& ! empty( $approval['restore_verified_at'] )\n\t&& (int) $approval['restore_verified_at'] <= $now;"
new_u="\t&& ! empty( $approval['backup_verified_at'] )\n\t&& (int) $approval['backup_verified_at'] <= $now\n\t&& (int) $approval['backup_verified_at'] >= $now - DAY_IN_SECONDS\n\t&& (int) $approval['backup_verified_at'] <= (int) $approval['approved_at']\n\t&& ! empty( $approval['restore_verified_at'] )\n\t&& (int) $approval['restore_verified_at'] <= $now\n\t&& (int) $approval['restore_verified_at'] >= $now - DAY_IN_SECONDS\n\t&& (int) $approval['restore_verified_at'] <= (int) $approval['approved_at'];"
if u.count(old_u)!=1: raise SystemExit(f'uninstall anchor count={u.count(old_u)}')
uninstall.write_text(u.replace(old_u,new_u,1),encoding='utf-8')

test=root/'tests/twelfth-cycle-round18-regression-tests.php'
test.write_text(r"""<?php
$root=dirname(__DIR__);
$repository=file_get_contents($root.'/14-global-clinic-usp-integration/includes/class-gcu-repository.php');
$uninstall=file_get_contents($root.'/14-global-clinic-usp-integration/uninstall.php');
$failures=array();function r12r18_assert($c,$m){global $failures;if(!$c){$failures[]=$m;}}
r12r18_assert(false!==strpos($repository,'gcu_inbound_contract_version_unsupported'),'Unknown inbound owner contract versions must fail closed.');
r12r18_assert(false!==strpos($repository,'1!==$contract_version'),'Inbound v1 event handlers must enforce the exact supported contract version.');
r12r18_assert(false!==strpos($uninstall,"(int) \$approval['backup_verified_at'] >= \$now - DAY_IN_SECONDS"),'Destructive purge requires a fresh backup verification.');
r12r18_assert(false!==strpos($uninstall,"(int) \$approval['restore_verified_at'] >= \$now - DAY_IN_SECONDS"),'Destructive purge requires a fresh restore verification.');
r12r18_assert(false!==strpos($uninstall,"(int) \$approval['backup_verified_at'] <= (int) \$approval['approved_at']"),'Backup proof must predate or equal purge approval.');
r12r18_assert(false!==strpos($uninstall,"(int) \$approval['restore_verified_at'] <= (int) \$approval['approved_at']"),'Restore proof must predate or equal purge approval.');
if($failures){fwrite(STDERR,"Twelfth-cycle Round 18 regression tests failed:\n- ".implode("\n- ",$failures)."\n");exit(1);}echo "Twelfth-cycle Round 18 regression tests: PASS\n";
""",encoding='utf-8')

quality=root/'scripts/quality.sh'
q=quality.read_text(encoding='utf-8')
anchor='php "$ROOT/tests/twelfth-cycle-round17-regression-tests.php"\n'
insert=anchor+'php "$ROOT/tests/twelfth-cycle-round18-regression-tests.php"\n'
if q.count(anchor)!=1: raise SystemExit(f'quality anchor count={q.count(anchor)}')
quality.write_text(q.replace(anchor,insert,1),encoding='utf-8')
print('Round 18 security corrections and regression gate applied')
