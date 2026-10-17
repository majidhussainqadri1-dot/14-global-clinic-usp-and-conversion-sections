<?php
/**
 * R52: 40 fresh direct actual GCU_Fifth_Review_Hardening::transactional_early_stop_guard()
 * PHP-method tests, real Future Policy numeric count validation, mocked WPDB.
 * Also executes R34's historical 40 experiment lifecycle scenarios.
 * Never claimed as installed WordPress/staging/live/owner-rollback proof.
 */
require __DIR__.'/r34-experiment-lifecycle-regressions.php';
class R52DB extends R34DB {
    public $complaints='0';
    public $expiring='0';
    public function get_var($sql) {
        $this->read_sql[]=$sql;
        if(strpos($sql,'gcu_future_reports')!==false)return $this->complaints;
        if(strpos($sql,'COUNT(*)')!==false && strpos($sql,'gcu_experiments')!==false)return $this->expiring;
        throw new RuntimeException('Unknown experiment guard aggregate SQL');
    }
}
$bad=array();
$invalidCounts=array('-1','0.5','1e3',(string)PHP_INT_MAX.'0');
for($case=1;$case<=40;$case++){
    $family=($case-1)%10;$v=intdiv($case-1,10);
    $wpdb=new R52DB();
    $wpdb->rows=array(array(
        'id'=>$case,'public_id'=>sprintf('%08d-1111-4111-8111-123456789abc',$case),
        'status'=>'running','row_version'=>$case,'ends_at'=>'2026-10-16 00:00:00',
    ));
    $wpdb->include_running=true;
    $wpdb->expired=0;
    $wpdb->fail_check=false;
    $wpdb->complaints='0';
    $wpdb->expiring='0';
    $wpdb->read_sql=array();
    $r34_opts=array(GCU_Future_Intelligence::LAST_ANOMALY_OPTION=>array('severity'=>'none'));
    GCU_Future_Intelligence::$policy_ok=true;
    GCU_Experiment_Baseline::$restored=true;
    GCU_Observability::$errors=array();
    GCU_Hardening::$locks=array();
    GCU_Plugin::instance()->repository()->audits=array();
    $expectedCode=null;
    $expectedResult=0;
    if($family===0) {
        $wpdb->complaints=$v%2?false:null;
        $expectedCode='gcu_experiment_complaint_count_failed';
    }elseif($family===1){
        $wpdb->complaints=$invalidCounts[$v];
        $expectedCode='gcu_experiment_complaint_count_failed';
    }elseif($family===2){
        $wpdb->complaints=array('-0','00','5.0','7e0')[$v];
        $expectedCode='gcu_experiment_complaint_count_failed';
    }elseif($family===3){
        $wpdb->complaints=$v%2?'4':4; // genuine non-negative whole count below 5
        $expectedResult=0;
    }elseif($family===4){
        $wpdb->complaints=$v%2?'5':5;
        $expectedResult=1; // guardrail, not expiry, triggers a contained stop
    }elseif($family===5){
        $wpdb->expiring=$v%2?false:null;
        $expectedCode='gcu_experiment_expiry_check_failed';
    }elseif($family===6){
        $wpdb->expiring=$invalidCounts[$v];
        $expectedCode='gcu_experiment_expiry_check_failed';
    }elseif($family===7){
        $wpdb->expiring=array('-0','00','1.5','2e1')[$v];
        $expectedCode='gcu_experiment_expiry_check_failed';
    }elseif($family===8){
        $wpdb->expiring=$v===0?'0':($v===1?0:($v===2?'1':1));
        // R34's mock SELECT obeys its separate expired-clock flag.
        $wpdb->expired=$v<2?0:1;
        $expectedResult=$v<2?0:1;
    }else{
        $wpdb->complaints=$v===0?'0':($v===1?0:($v===2?'4':4));
        $wpdb->expiring='0';
        GCU_Future_Intelligence::$policy_ok=false;
        GCU_Experiment_Baseline::$restored=false;
        $expectedResult=1;
    }
    $r=GCU_Fifth_Review_Hardening::transactional_early_stop_guard();
    $audits=count(GCU_Plugin::instance()->repository()->audits);
    $writes=$audits;
    $ok=$expectedCode
        ? is_wp_error($r) && $r->get_error_code()===$expectedCode
            && $writes===0 && count(GCU_Observability::$errors)===1
        : is_int($r) && $r===$expectedResult
            && $writes===$expectedResult;
    $sql=implode(' ',$wpdb->read_sql);
    if(strpos($sql,"gcu_future_reports")===false) $ok=false;
    if($family===9 && empty($r34_opts['gcu_experiment_serving_paused']))$ok=false;
    if($family===4 && strpos($sql,'gcu_experiments')!==false)$ok=false;
    if($family>=5 && $family<=8 && strpos($sql,'gcu_experiments')===false)$ok=false;
    printf("R52 actual FUT17 guardrail canonical SQL-count case %02d/40: %s (family %d)\n",$case,$ok?'PASS':'FAIL',$family);
    if(!$ok)$bad[]=sprintf('R52 case %02d family %d failed',$case,$family);
}
if($bad){fwrite(STDERR,implode("\n",$bad)."\n");exit(1);}
echo "R52 40/40 direct REAL experiment guard PHP-method strict count SQL mocks PASS; not real owner rollback/staging/live tests.\n";
