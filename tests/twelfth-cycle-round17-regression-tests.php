<?php
$root=dirname(__DIR__);
$main=file_get_contents($root.'/14-global-clinic-usp-integration/global-clinic-usp-integration.php');
$install=file_get_contents($root.'/14-global-clinic-usp-integration/includes/class-gcu-install.php');
$hard=file_get_contents($root.'/14-global-clinic-usp-integration/includes/class-gcu-eleventh-review-hardening.php');
$failures=array();function r12r17_assert($c,$m){global$failures;if(!$c){$failures[]=$m;}}
r12r17_assert(false!==strpos($main,"GCU_SCHEMA_VERSION', 10006"),'Base schema version must advance for cleanup-index migration.');
foreach(array('retention_time (occurred_at,id)','sent_retention (status,dispatched_at,id)','processed_retention (status,processed_at,id)','expiry_cleanup (expires_at,id)','consumed_cleanup (consumed_at,id)','completed_retention (status,updated_at,id)') as $needle){r12r17_assert(false!==strpos($install,$needle),'Missing cleanup index declaration: '.$needle);}
r12r17_assert(false!==strpos($hard,'verify_cleanup_indexes'),'Runtime/activation postconditions must verify cleanup indexes.');
r12r17_assert(false!==strpos($hard,'gcu_cleanup_indexes_unverified'),'Cleanup index verification must fail closed.');
if($failures){fwrite(STDERR,"Twelfth-cycle Round 17 regression tests failed:\n- ".implode("\n- ",$failures)."\n");exit(1);}echo "Twelfth-cycle Round 17 regression tests: PASS\n";
