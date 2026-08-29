<?php
$root = dirname( __DIR__ );
$f = file_get_contents( $root . '/14-global-clinic-usp-integration/includes/class-gcu-future-intelligence.php' );
$failures = array();
function r12r14_assert($c,$m){global $failures;if(!$c){$failures[]=$m;}}
r12r14_assert(false!==strpos($f,'gcu_future_claim_freshness_query_failed'),'Freshness sentinel must fail closed on DB read failure.');
r12r14_assert(false!==strpos($f,'gcu_future_consistency_query_failed'),'Consistency graph must fail closed on DB read failure.');
r12r14_assert(false!==strpos($f,"ORDER BY block_key,locale LIMIT 1001"),'Consistency root query must enforce its own ceiling probe.');
r12r14_assert(false!==strpos($f,'gcu_future_records_query_failed'),'Future records read must not turn DB failure into an empty result.');
if($failures){fwrite(STDERR,"Twelfth-cycle Round 14 regression tests failed:\n- ".implode("\n- ",$failures)."\n");exit(1);}echo "Twelfth-cycle Round 14 regression tests: PASS\n";
