<?php
/**
 * R57 — 40 actual File09 native source producer IDs + 40 actual duplicate
 * replay identity cases. WordPress / SQL mocks, NOT deployed acceptance.
 */
define('ABSPATH',__DIR__.'/');
define('ARRAY_A','ARRAY_A');
class GDO_Application {}
class GCU_Privacy {
    public static function measurement_allowed(){return true;}
    public static function low_bandwidth_requested(){return false;}
    public function event_subject_hash(){return str_repeat('a',64);}
}
class GCU_Policy { const EVENT_RATE_LIMIT=100; }
class GCU_Install {
    public static function ready_for_mutation(){return true;}
    public static function tables(){return array('events'=>'wp_gcu_events');}
}
class GCU_R57_Repo { public function consume_rate_limit($key,$limit){return true;} }
class GCU_Plugin {
    private static $instance;
    public static function instance(){ if(!self::$instance) self::$instance=new self();return self::$instance;}
    public function privacy(){return new GCU_Privacy();}
    public function repository(){return new GCU_R57_Repo();}
}
class GCU_Observability {
    public static $logs=array();
    public static function log($level,$name,$data=array()){self::$logs[]=array($level,$name,$data);}
}
class R57DB {
    public $query_result=1;
    public $calls=0;
    public $read_calls=0;
    public $existing=null;
    public function prepare($query){return $query;}
    public function query($query){$this->calls++;return $this->query_result;}
    public function get_row($query,$format=ARRAY_A){$this->read_calls++;return $this->existing;}
}
function __( $s, $domain='' ){return $s;}
function wp_salt($scheme='auth'){return 'source-test-static-salt';}
function current_time($type='mysql',$utc=false){return '2026-10-18 16:00:00';}
function is_wp_error($x){return $x instanceof WP_Error;}
class WP_Error {public function __construct($code,$message=''){}}
require __DIR__.'/../14-global-clinic-usp-integration/includes/class-gcu-future-policy.php';
require __DIR__.'/../14-global-clinic-usp-integration/includes/class-gcu-owner-outcomes.php';
$errors=array();
for($i=0;$i<40;$i++){
    $family=$i%10;$v=intdiv($i,10);
    $positive=10+$v;
    $identity=$positive;
    $expected=$family===0||$family===1;
    switch($family){
        case 0:$identity=$positive;break;
        case 1:$identity=(string)$positive;break;
        case 2:$identity='0'.$positive;break;
        case 3:$identity='0';break;
        case 4:$identity=0;break;
        case 5:$identity=$positive.'junk';break;
        case 6:$identity='1e2';break;
        case 7:$identity='18446744073709551616';break;
        case 8:$identity=(float)$positive;break;
        case 9:$identity=array($positive);break;
    }
    $wpdb=new R57DB();
    GCU_Observability::$logs=array();
    $actual=GCU_Owner_Outcomes::application_submitted($identity);
    $ok=$actual===$expected&&$wpdb->calls===($expected?1:0)&&$wpdb->read_calls===0;
    if(!$ok)$errors[]='R57 native ID case '.($i+1).' returned '.var_export($actual,true);
    printf("R57 native File09 canonical ID %02d/40: %s\n",$i+1,$ok?'PASS':'FAIL');
}
for($i=0;$i<40;$i++){
    $family=$i%10;$v=intdiv($i,10);$wpdb=new R57DB();
    $wpdb->query_result=0;
    $wpdb->existing=array(
        'event_type'=>'File09.ApplicationStarted.v1',
        'event_version'=>'1',
        'funnel_stage'=>'application_started',
        'destination_key'=>'doctor_onboarding',
        'consent_state'=>'granted',
        'subject_hash'=>str_repeat('a',64)
    );
    $expected=$family===0||$family===1;
    switch($family){
        case 0:$wpdb->existing['event_version']='1';break;
        case 1:$wpdb->existing['event_version']=1;break;
        case 2:$wpdb->existing['event_version']='01';break;
        case 3:$wpdb->existing['event_version']='1junk';break;
        case 4:$wpdb->existing['event_version']=true;break;
        case 5:$wpdb->existing['event_version']=1.0;break;
        case 6:unset($wpdb->existing['event_version']);break;
        case 7:unset($wpdb->existing['event_type']);break;
        case 8:$wpdb->existing['subject_hash']=str_repeat('b',64);break;
        case 9:$wpdb->existing['event_type']='ClinicUSPFunnelEvent';break;
    }
    GCU_Observability::$logs=array();
    $actual=GCU_Owner_Outcomes::application_submitted(10+$v);
    $ok=$actual===$expected && $wpdb->calls===1 && $wpdb->read_calls===1 &&
        count(GCU_Observability::$logs)===($expected?0:1);
    if(!$ok)$errors[]='R57 strict duplicate source case '.($i+1).' returned '.var_export($actual,true);
    printf("R57 duplicate owner event replay %02d/40: %s\n",$i+1,$ok?'PASS':'FAIL');
}
if($errors){fwrite(STDERR,implode("\n",$errors)."\n");exit(1);}
echo "R57 40 canonical owner-ID + 40 strict duplicate replay real method cases PASS (WordPress/SQL mocks only; no live owner registry/staging).\n";
