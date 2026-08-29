<?php
$root=dirname(__DIR__);$f=file_get_contents($root.'/14-global-clinic-usp-integration/includes/class-gcu-frontend.php');$failures=array();
function r12r13_assert($ok,$m){global $failures;if(!$ok){$failures[]=$m;}}
r12r13_assert(false!==strpos($f,"array('global_clinic','how_it_works','find_doctor','start_clinic')"),'Canonical route allowlist must be explicit.');
r12r13_assert(false!==strpos($f,'$this->route_not_found=true;status_header(404);'),'Unknown File 14 routes must be 404.');
r12r13_assert(false!==strpos($f,'$this->route_not_found||$this->degraded_destination'),'Unknown routes must be noindex and receive no canonical link.');
r12r13_assert(false!==strpos($f,'render_not_found()'),'Unknown routes must render a distinct not-found state.');
if($failures){fwrite(STDERR,"Twelfth-cycle Round 13 regression tests failed:\n- ".implode("\n- ",$failures)."\n");exit(1);}echo "Twelfth-cycle Round 13 regression tests: PASS\n";
