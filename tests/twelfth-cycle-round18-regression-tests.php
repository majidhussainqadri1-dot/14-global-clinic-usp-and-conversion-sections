<?php
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
