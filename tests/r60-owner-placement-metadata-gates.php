<?php
/**
 * R60 / FUT16+FUT17: 40 actual File14 owner-placement mutation classifier
 * and preflight activation cases. Source-only mocks: NO File20/25 writes,
 * deployed staging or live proof.
 */
define('ABSPATH',__DIR__.'/');
function wp_json_encode($v){return json_encode($v);}
function get_option($key,$default=false){return $default;}
class GCU_Experiment_Baseline {public static function ready($experiment){return true;}}
require __DIR__.'/../14-global-clinic-usp-integration/includes/class-gcu-future-policy.php';
$errors=array();
for($i=0;$i<40;$i++){
    $family=$i%10;$v=intdiv($i,10);
    $variants=array('headline A','headline B');$requires=false;
    if($family===1)$variants=array(array('headline'=>'copy A'),array('headline'=>'copy B'));
    if($family===2){$variants=array(array('route_key'=>'directory'.$v),array('route_key'=>'clinic'.$v));$requires=true;}
    if($family===3){$variants=array(array('priority'=>1+$v),array('priority'=>2+$v));$requires=true;}
    if($family===4){$variants=array(array('audience'=>'patient'),array('audience'=>'doctor'));$requires=true;}
    if($family===5){$variants=array(array('status'=>'active'),array('status'=>'paused'));$requires=true;}
    if($family===6){$variants=array(array('starts_at'=>'2026-10-19'),array('starts_at'=>'2026-10-20'));$requires=true;}
    if($family===7){$variants=array(array('ends_at'=>'2026-10-25'),array('ends_at'=>'2026-10-27'));$requires=true;}
    if($family===8){$variants=array(array('copy'=>'A','meta'=>array('route_key'=>'directory')),array('copy'=>'B','meta'=>array('route_key'=>'clinic')));$requires=true;}
    if($family===9){$variants=array(array('copy'=>'A','meta'=>array('priority'=>3)),array('copy'=>'B','meta'=>array('priority'=>4)));$requires=true;}
    $actual=GCU_Future_Policy::experiment_requires_baseline_restore($variants);
    $gate=GCU_Future_Policy::experiment_baseline_restoration_ready(array('variants'=>json_encode($variants)));
    $ok=$actual===$requires && $gate===!$requires;
    if(!$ok)$errors[]='R60 owner placement meta #'.($i+1).' expected '.($requires?'mutation':'copy-only');
    printf("R60 File20 owner metadata gate %02d/40: %s\n",$i+1,$ok?'PASS':'FAIL');
}
if($errors){fwrite(STDERR,implode("\n",$errors)."\n");exit(1);}
echo "R60 owner metadata 40/40 actual method/activation source cases PASS; real owner CAS #22 still OPEN.\n";
