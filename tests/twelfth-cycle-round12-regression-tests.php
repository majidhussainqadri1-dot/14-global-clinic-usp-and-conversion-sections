<?php
$root=dirname(__DIR__);
$policy=file_get_contents($root.'/14-global-clinic-usp-integration/includes/class-gcu-future-policy.php');
$hard=file_get_contents($root.'/14-global-clinic-usp-integration/includes/class-gcu-fifth-review-hardening.php');
$failures=array();
function r12r12_assert($ok,$m){global $failures;if(!$ok){$failures[]=$m;}}
r12r12_assert(false!==strpos($policy,'business_policy_contradiction_scan( $text )'),'Experiment variants must run the business-policy contradiction scanner.');
r12r12_assert(false!==strpos($policy,'array_merge( $scan'),'Experiment preflight must merge variant policy flags.');
r12r12_assert(false!==strpos($hard,'future_early_stop_experiment_query_failed'),'Early-stop experiment query failure must have a stable failure code.');
r12r12_assert(false!==strpos($hard,'update_option( GCU_Future_Intelligence::SAFE_MODE_OPTION, 1, false )'),'Early-stop experiment query failure must enter Future safe mode.');
if($failures){fwrite(STDERR,"Twelfth-cycle Round 12 regression tests failed:\n- ".implode("\n- ",$failures)."\n");exit(1);}echo "Twelfth-cycle Round 12 regression tests: PASS\n";
