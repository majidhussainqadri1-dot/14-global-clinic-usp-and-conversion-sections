<?php
/**
 * R56: forty actual GCU_Experiment_Baseline::reconcile_after_stop() method
 * scenarios with tampered approval, stale row version, variant drift and
 * snapshot corruption. Mocked WordPress + SQL; NOT actual deployed parity.
 */
define('ABSPATH',__DIR__ . '/');
define('ARRAY_A','ARRAY_A');
function wp_is_uuid($v) { return is_string($v) && (bool) preg_match('/^[0-9a-f]{8}-[0-9a-f]{4}-[0-9a-f]{4}-[0-9a-f]{4}-[0-9a-f]{12}$/D',$v); }
function sanitize_key($v) { return preg_replace('/[^a-z0-9_-]/','',strtolower((string)$v)); }
function wp_json_encode($v) { return json_encode($v); }
function current_time($f,$utc=false) { return '2026-10-18 10:00:00'; }
function absint($v) { return abs((int)$v); }
class WP_Error { public $code; public function __construct($code,$msg=''){ $this->code=$code; } }
class GCU_Install { public static function tables(){return array('placements'=>'wp_gcu_placements');} }
class GCU_Future_Intelligence {public static function tables(){return array('records'=>'wp_gcu_future_records');}}
class R56DB {
    public $snapshot_row=array();
    public $placement=array();
    public function prepare($sql) { return $sql; }
    public function get_row($sql,$format=ARRAY_A) {
        if(strpos($sql,'wp_gcu_future_records')!==false){return $this->snapshot_row;}
        if(strpos($sql,'wp_gcu_placements')!==false){return $this->placement;}
        throw new RuntimeException('Unexpected SQL in snapshot method');
    }
}
require __DIR__ . '/../14-global-clinic-usp-integration/includes/class-gcu-future-policy.php';
require __DIR__ . '/../14-global-clinic-usp-integration/includes/class-gcu-experiment-baseline.php';
$failures=array();
for($i=0;$i<40;$i++){
    $v=intdiv($i,10); $mode=$i%10;
    $uuid=sprintf('%08d-1111-4111-8111-123456789abc',$i+1);
    $approval_version=11+$v;
    $actor=7+$v;
    $experiment=array(
        'public_id'=>$uuid, 'row_version'=>$approval_version+1, 'approved_by'=>$actor,
        'status'=>'running','variants'=>json_encode(array('approved copy A','approved copy B'))
    );
    $snapshot=array(
        'contract'=>GCU_Experiment_Baseline::CONTRACT,
        'experiment'=>$uuid, 'approval_version'=>$approval_version,
        'approved_by'=>$actor, 'approved_at'=>'2026-10-18 10:00:00',
        'placements'=>array(), 'requires_manual_review_after_stop'=>true
    );
    $should_restore=($mode===0 || $mode===1);
    if($mode===1){$experiment['status']='approved';$experiment['row_version']=$approval_version;}
    if($mode===2){$experiment['row_version']=$approval_version+2;}
    if($mode===3){$experiment['row_version']=$approval_version-1;}
    if($mode===4){$experiment['approved_by']=$actor+1;}
    if($mode===5){$experiment['row_version']=(string)($approval_version+1).'.0';}
    if($mode===6){$experiment['variants']=json_encode(array(array('placement_key'=>'clinic_hero'),array('placement_key'=>'clinic_hero')));}
    if($mode===8){$snapshot['approval_version']=$approval_version+1;}
    if($mode===9){$experiment['status']='stopped';}
    $json=json_encode($snapshot);
    $wpdb=new R56DB();
    $wpdb->snapshot_row=array(
        'payload'=>$json, 'payload_hash'=>$mode===7?str_repeat('0',64):hash('sha256',$json),
        'created_by'=>$actor
    );
    $result=GCU_Experiment_Baseline::reconcile_after_stop($experiment);
    $ok=is_array($result) && $result['manual_review']===true
        && $result['restored']===$should_restore
        && ($should_restore ? $result['reason']==='approved_baseline_unchanged'
            : in_array($result['reason'],array('approved_snapshot_missing','approved_experiment_baseline_identity_unverified'),true));
    printf("R56 approved snapshot reconciliation %02d/40: %s\n",$i+1,$ok?'PASS':'FAIL');
    if(!$ok)$failures[]='R56 failed snapshot integrity scenario '.($i+1).' '.json_encode($result);
}
if($failures){fwrite(STDERR,implode("\n",$failures)."\n");exit(1);}
echo "R56 40/40 baseline approval/version/mapping real-method SQL-mock regressions PASS, staging/live unverified.\n";
