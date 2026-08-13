<?php
$root=dirname(__DIR__);$repo=file_get_contents($root.'/14-global-clinic-usp-integration/includes/class-gcu-repository.php');$fail=array();
function r10ck($c,$m){global$fail;if(!$c){$fail[]=$m;}}
r10ck(substr_count($repo,'$wpdb->last_error=\'\';')>=2,'Public repository reads must clear DB last_error.');
r10ck(false!==strpos($repo,'active_blocks_read_failed'),'active_blocks DB failure observability missing.');
r10ck(false!==strpos($repo,'public_claims_read_failed'),'public_claims DB failure observability missing.');
r10ck(false!==strpos($repo,"''!==(string)\$wpdb->last_error||!is_array(\$r)"),'active_blocks DB failure check missing.');
r10ck(false!==strpos($repo,"''!==(string)\$wpdb->last_error||!is_array(\$rows)"),'public_claims DB failure check missing.');
if($fail){fwrite(STDERR,"Eleventh review regression tests failed:\n- ".implode("\n- ",$fail)."\n");exit(1);}echo "Eleventh review regression tests: PASS\n";
