<?php
/**
 * R47: 40 direct actual File14 quality_score() SQL-mocked behavior cases.
 * WordPress is isolated. No staging, deployed package, database or live tests.
 */
define('ABSPATH', __DIR__ . '/');
define('ARRAY_A', 'ARRAY_A');
function __( $value, $domain = '' ) { return $value; }
function wp_strip_all_tags( $v ) { return strip_tags( (string) $v ); }
function sanitize_key($v) { return preg_replace('/[^a-z0-9_-]/','',strtolower((string)$v)); }
function apply_filters($key,$value) { return in_array($key,array('gcu_future_accessibility_score','gcu_future_performance_score','gcu_future_privacy_effectiveness_score'),true) ? 95 : $value; }
function update_option($key,$value,$autoload=false) { $GLOBALS['r47_options'][$key]=$value; return true; }
class GCU_Observability {
    public static $errors=array();
    public static function log($level,$code,$data=array()){ self::$errors[]=array($level,$code,$data); }
}
class GCU_Install {
    public static function tables(){return array('claims'=>'wp_gcu_claims','blocks'=>'wp_gcu_blocks','events'=>'wp_gcu_events');}
}
class GCU_Policy {
    public static function canonical_claims(){return array(
      'zero_platform_commission'=>array('text'=>'No platform commission'),
      'free_approved_core'=>array('text'=>'Core free'),
      'optional_support_no_ranking'=>array('text'=>'Support does not affect visibility')
    );}
    public static function business_rules(){return array('platform_commission_percent'=>0,'approved_core_tier'=>'free','support_affects_visibility'=>false);}
}
class R47_Contracts {
    public function all_destination_health(){return array(array('available'=>true),array('available'=>true));}
}
class GCU_Plugin {
    private static $instance;
    public static function instance(){if(!self::$instance)self::$instance=new self();return self::$instance;}
    public function contracts(){return new R47_Contracts();}
}
class R47DB {
    public $prefix='wp_';
    public $events=array(array('funnel_stage'=>'cta_selected','total'=>'20'));
    public $blocks=array(array('title'=>'Welcome','body'=>'Approved noncoercive text','cta_label'=>'Learn more'));
    public $stale='0';public $complaints='0';
    public $queries=array();
    public function prepare($sql,...$args){
        if(count($args)===1 && is_array($args[0]))$args=$args[0];
        foreach($args as $x)$sql=preg_replace('/%d|%s/',is_numeric($x)?(string)$x:"'".$x."'",$sql,1);
        return $sql;
    }
    public function get_row($sql,$format=ARRAY_A){
        $this->queries[]=$sql;
        foreach(GCU_Policy::canonical_claims() as $key=>$claim){
            if(strpos($sql,"'".$key."'")!==false)return array('claim_text'=>$claim['text'],
                'status'=>'active','is_public'=>1,'review_due_at'=>null,'expires_at'=>null);
        }
        throw new RuntimeException('Unexpected row SQL '.$sql);
    }
    public function get_results($sql,$format=ARRAY_A){
        $this->queries[]=$sql;
        if(strpos($sql,'FROM wp_gcu_events')!==false)return $this->events;
        if(strpos($sql,'FROM wp_gcu_blocks')!==false)return $this->blocks;
        throw new RuntimeException('Unexpected results SQL '.$sql);
    }
    public function get_var($sql){
        $this->queries[]=$sql;
        if(strpos($sql,'FROM wp_gcu_claims')!==false)return $this->stale;
        if(strpos($sql,'FROM wp_gcu_future_reports')!==false)return $this->complaints;
        throw new RuntimeException('Unexpected count SQL '.$sql);
    }
}
require __DIR__.'/../14-global-clinic-usp-integration/includes/class-gcu-future-policy.php';
require __DIR__.'/../14-global-clinic-usp-integration/includes/class-gcu-future-intelligence.php';
$failed=array();
for($case=1;$case<=40;$case++){
    $family=($case-1)%10;$v=intdiv($case-1,10);
    $wpdb=new R47DB();GCU_Observability::$errors=array();$GLOBALS['r47_options']=array();
    $ok=false;
    if($family===0){
        $wpdb->events=array(array('funnel_stage'=>'cta_selected','total'=>(string)(20+$v)));
        $r=GCU_Future_Intelligence::quality_score();
        $sql=implode("\n",$wpdb->queries);
        $ok=$r['sample_count']===20+$v && $r['score']===null && $r['provisional']
            && strpos($sql,"event_type='ClinicUSPFunnelEvent'")!==false
            && strpos($sql,'event_version=1')!==false
            && strpos($sql,"consent_state='granted'")!==false
            && strpos($sql,'subject_hash IS NOT NULL')!==false;
    }elseif($family===1){
        $wpdb->events=$v%2?false:null;
        $r=GCU_Future_Intelligence::quality_score();
        $ok=$r['provisional'] && $r['score']===null && $r['sample_count']===null
            && in_array('quality_funnel_read_unverified',$r['missing_evidence'],true)
            && count(GCU_Observability::$errors)===1;
    }elseif($family===2){
        $wpdb->events=$v===0?array(array('funnel_stage'=>'cta_selected')):
            ($v===1?array(array('funnel_stage'=>'cta_selected','total'=>'-10')):
            ($v===2?array(array('funnel_stage'=>'cta_selected','total'=>'1e5')):
            array(array('funnel_stage'=>'cta_selected','total'=>(string)PHP_INT_MAX.'0'))));
        $r=GCU_Future_Intelligence::quality_score();
        $ok=$r['sample_count']===null && $r['small_cohort_suppressed']===true
            && in_array('quality_funnel_read_unverified',$r['missing_evidence'],true);
    }elseif($family===3){
        $wpdb->stale=$v%2?false:null;
        $r=GCU_Future_Intelligence::quality_score();
        $ok=$r['metrics']['claim_freshness']===null
            && in_array('claim_freshness_count_unverified',$r['missing_evidence'],true)
            && $r['score']===null;
    }elseif($family===4){
        $wpdb->complaints=$v%2?false:null;
        $r=GCU_Future_Intelligence::quality_score();
        $ok=$r['metrics']['complaint_health']===null
            && in_array('complaint_count_unverified',$r['missing_evidence'],true)
            && $r['score']===null;
    }elseif($family===5){
        $wpdb->stale=$v%2?0:'0';
        $r=GCU_Future_Intelligence::quality_score();
        $ok=$r['metrics']['claim_freshness']===100
            && !in_array('claim_freshness_count_unverified',$r['missing_evidence'],true);
    }elseif($family===6){
        $wpdb->complaints=$v%2?0:'0';
        $r=GCU_Future_Intelligence::quality_score();
        $ok=$r['metrics']['complaint_health']===100
            && !in_array('complaint_count_unverified',$r['missing_evidence'],true);
    }elseif($family===7){
        $wpdb->stale=(string)($v+1);
        $r=GCU_Future_Intelligence::quality_score();
        $ok=$r['metrics']['claim_freshness']===100-20*($v+1)
            && $r['parity']['ok']===true;
    }elseif($family===8){
        $wpdb->complaints=(string)($v+1);
        $r=GCU_Future_Intelligence::quality_score();
        $ok=$r['metrics']['complaint_health']===100-10*($v+1)
            && $r['score']===null;
    }else{
        $wpdb->stale=$v===0?'1e5':($v===1?'-1':($v===2?'1.5':(string)PHP_INT_MAX.'0'));
        $wpdb->complaints=$v===0?'1e5':($v===1?'-1':($v===2?'1.5':(string)PHP_INT_MAX.'0'));
        $r=GCU_Future_Intelligence::quality_score();
        $ok=$r['metrics']['claim_freshness']===null && $r['metrics']['complaint_health']===null
            && in_array('complaint_count_unverified',$r['missing_evidence'],true)
            && in_array('claim_freshness_count_unverified',$r['missing_evidence'],true);
    }
    echo sprintf("R47 direct quality method SQL/evidence check %02d/40: %s (family %d)\n",$case,$ok?'PASS':'FAIL',$family);
    if(!$ok)$failed[]=sprintf('case %02d family %d failed',$case,$family);
}
if($failed){fwrite(STDERR,implode("\n",$failed)."\n");exit(1);}
echo "R47: 40 actual-method SQL-mocked FUT11 count/consent/quality checks PASS. Not deployed/staging/live WordPress tests.\n";
