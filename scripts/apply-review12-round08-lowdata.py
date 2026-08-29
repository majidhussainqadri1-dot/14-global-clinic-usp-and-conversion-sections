from pathlib import Path
p=Path('14-global-clinic-usp-integration/includes/class-gcu-privacy.php')
s=p.read_text(encoding='utf-8')
old="public static function measurement_allowed(){return!self::global_privacy_control_requested()&&!GCU_Hardening::is_sensitive_path()&&GCU_Policy::analytics_consent();}"
new="public static function measurement_allowed(){return!self::global_privacy_control_requested()&&!self::low_bandwidth_requested()&&!GCU_Hardening::is_sensitive_path()&&GCU_Policy::analytics_consent();}"
if s.count(old)!=1: raise SystemExit(f'measurement gate anchor count={s.count(old)}')
p.write_text(s.replace(old,new,1),encoding='utf-8')
t=Path('tests/twelfth-cycle-round08-regression-tests.php')
t.write_text("""<?php
$root=dirname(__DIR__);
$privacy=file_get_contents($root.'/14-global-clinic-usp-integration/includes/class-gcu-privacy.php');
$failures=array();
function r12r08_assert($ok,$m){global $failures;if(!$ok){$failures[]=$m;}}
r12r08_assert(false!==strpos($privacy,'!self::low_bandwidth_requested()'),'Save-Data/low-data must disable File 14 measurement.');
r12r08_assert(false!==strpos($privacy,'purge_browser_measurement_artifacts'),'Measurement denial must purge browser measurement artifacts.');
if($failures){fwrite(STDERR,"Twelfth-cycle Round 08 regression tests failed:\n- ".implode("\n- ",$failures)."\n");exit(1);} echo "Twelfth-cycle Round 08 regression tests: PASS\n";
""",encoding='utf-8')
q=Path('scripts/quality.sh');x=q.read_text(encoding='utf-8');a='php "$ROOT/tests/twelfth-cycle-round07-regression-tests.php"\n'
if x.count(a)!=1: raise SystemExit(f'quality anchor count={x.count(a)}')
q.write_text(x.replace(a,a+'php "$ROOT/tests/twelfth-cycle-round08-regression-tests.php"\n',1),encoding='utf-8')
print('Round 08 low-data privacy gate applied.')