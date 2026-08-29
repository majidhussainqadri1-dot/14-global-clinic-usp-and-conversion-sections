<?php
$root=dirname(__DIR__);
$privacy=file_get_contents($root.'/14-global-clinic-usp-integration/includes/class-gcu-privacy.php');
$failures=array();
function r12r08_assert($ok,$m){global $failures;if(!$ok){$failures[]=$m;}}
r12r08_assert(false!==strpos($privacy,'!self::low_bandwidth_requested()'),'Save-Data/low-data must disable File 14 measurement.');
r12r08_assert(false!==strpos($privacy,'purge_browser_measurement_artifacts'),'Measurement denial must purge browser measurement artifacts.');
if($failures){fwrite(STDERR,"Twelfth-cycle Round 08 regression tests failed:
- ".implode("
- ",$failures)."
");exit(1);} echo "Twelfth-cycle Round 08 regression tests: PASS
";
