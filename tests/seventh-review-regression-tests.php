<?php

$root=dirname(__DIR__);$p=$root.'/14-global-clinic-usp-integration';$f=array();
function s7($ok,$msg){global$f;if(!$ok){$f[]=$msg;}}
$main=file_get_contents($p.'/global-clinic-usp-integration.php');
$cap=file_get_contents($p.'/includes/class-gcu-capabilities.php');
$hard=file_get_contents($p.'/includes/class-gcu-hardening.php');
$contracts=file_get_contents($p.'/includes/class-gcu-contracts.php');
$privacy=file_get_contents($p.'/includes/class-gcu-privacy.php');
$rest=file_get_contents($p.'/includes/class-gcu-rest.php');
$ignore=file_get_contents($root.'/.gitignore');
$version='';if(preg_match('/Version:\s*([0-9]+\.[0-9]+\.[0-9]+)/',$main,$m)){$version=$m[1];}

s7(''!==$version&&version_compare($version,'1.4.5','>=')&&false!==strpos($main,"GCU_VERSION', '".$version."'")&&false!==strpos($main,"GCU_SCHEMA_VERSION', 10006"),'Seventh-review release/schema baseline regressed.');
s7(false!==strpos($main,"GCU_CURRENT_REPOSITORY_ALIAS', '14-global-clinic-usp-and-conversion-sections'"),'Current GitHub repository alias is not recorded beside the plan-canonical repository identity.');
s7(false!==strpos($cap,"apply_filters( 'gcu_authorize', false")&&false!==strpos($cap,"true === apply_filters"),'File 00 authorization adapter is not explicit-grant fail closed.');
s7(false!==strpos($hard,"if ( '' === \$scope ) { return false; }") ,'Normalized empty DB lock scopes are not rejected.');
s7(false===strpos($contracts,"\$filtered['url']")&&false!==strpos($contracts,"array_key_exists('available',\$filtered)"),'Destination consumer filter can still rewrite owner-confirmed URL or cannot restrict readiness.');
s7(false!==strpos($privacy,'$remaining_meta')&&false!==strpos($privacy,'measurement subject linkage could not be erased'),'Privacy erasure does not read back subject-link deletion before done=true.');
s7(false!==strpos($rest,'event_identity_guard')&&false!==strpos($rest,'gcu_event_identity_conflict')&&false!==strpos($rest,'subject_hash'),'Conversion-event UUID conflicting-payload guard is missing.');
s7(false!==strpos($ignore,'__pycache__/')&&false!==strpos($ignore,'*.py[cod]'),'Generated Python bytecode is not excluded.');
s7(!is_dir($root.'/scripts/__pycache__'),'Tracked/generated scripts/__pycache__ remains in the reviewed source tree.');

if($f){fwrite(STDERR,"Seventh review regression tests failed:\n- ".implode("\n- ",$f)."\n");exit(1);}echo "Seventh review regression tests: PASS\n";
