<?php
/**
 * R51: forty direct real File14 public_change_log()/jurisdiction_disclosure()
 * PHP method tests, mocked SQL + WP. Not actual deployed/staging browser tests.
 * Includes prior R41 and R45 separate standalone historical method tests.
 */
require __DIR__ . '/r45-retention-public-history-tests.php';

class R51DB extends R45DB {
    public $history = array();
    public $jurisdiction = array();
    public $history_reads = array();
    public $region_reads = array();
    public function get_results($sql,$format=ARRAY_A) {
        if ( strpos($sql,"record_type='jurisdiction_copy'") !== false ) {
            $this->region_reads[]=$sql;
            return $this->jurisdiction;
        }
        if ( strpos($sql,"FROM wp_gcu_future_records") !== false && strpos($sql,"record_type='change_log'") !== false ) {
            $this->history_reads[]=$sql;
            if ( $this->history===false || $this->history===null ) return $this->history;
            if ( ! preg_match('/LIMIT ([0-9]+) OFFSET ([0-9]+)/',$sql,$matches) )
                throw new RuntimeException('Unbounded File14 public policy history!');
            return array_slice($this->history,(int)$matches[2],(int)$matches[1]);
        }
        return parent::get_results($sql,$format);
    }
}
function r51_row($n,$approved=false) {
    $r=r45_history_row($n);
    if(!$approved)$r['payload']=json_encode(array('title'=>'Unverified history item '.$n));
    return $r;
}
function r51_disclosure($region,$label) {
    return array('region'=>$region,'payload'=>json_encode(array(
        'title'=>'Reviewed region copy '.$label,
        'body'=>'Approved region guidance '.$label,
        'effective_date'=>'2026-10-17'
    )));
}

$errors = array();
$future_source = file_get_contents(__DIR__.'/../14-global-clinic-usp-integration/includes/class-gcu-future-intelligence.php');
for($case=1;$case<=40;$case++){
    $f=($case-1)%10;$v=intdiv($case-1,10);
    $wpdb=new R51DB();
    $wpdb->history=array();
    $wpdb->jurisdiction=array();
    $wpdb->faq=array();$wpdb->graph=array();$wpdb->copy=array();
    GCU_Install::$runtime=true;
    GCU_Observability::$events=array();
    $ok=false;
    if ($f===0) {
        // Hit all ten full pages, no approved public item: an unscanned tail
        // may contain one. Never fabricate verified absence.
        $wpdb->history=array_map(static function($n){return r51_row($n,false);},range(1,1000+$v));
        if($v===1) $wpdb->history[1000]=r51_row(1001,true);
        if($v===2) $wpdb->history[1000]=r51_row(1001,true);
        $result=GCU_Future_Intelligence::public_change_log(1+$v);
        $ok=is_wp_error($result) && $result->get_error_code()==='gcu_public_change_log_scan_incomplete'
            && count($wpdb->history_reads)===10 && count(GCU_Observability::$events)===1
            && GCU_Observability::$events[0]['fields']===array()
            && strpos(end($wpdb->history_reads),'OFFSET 900')!==false;
    } elseif ($f===1) {
        $wpdb->history=array_map(static function($n){return r51_row($n,false);},range(1,996+$v));
        $result=GCU_Future_Intelligence::public_change_log(1);
        $ok=is_array($result) && $result===array()
            && count($wpdb->history_reads)===10 && GCU_Observability::$events===array();
    } elseif ($f===2) {
        $position=array(0,200,500,900)[$v];
        $wpdb->history=array_map(static function($n){return r51_row($n,false);},range(1,1000));
        $wpdb->history[$position]=r51_row($position+1,true);
        $result=GCU_Future_Intelligence::public_change_log(1);
        $ok=is_array($result)&&count($result)===1
            && $result[0]['key']==='approved_history_'.($position+1)
            && count($wpdb->history_reads)===(int)floor($position/100)+1
            && GCU_Observability::$events===array();
    } elseif ($f===3) {
        // A partial approved prefix is insufficient evidence for the full
        // requested page if the scan reaches exactly 1000 candidates.
        $wpdb->history=array_map(static function($n){return r51_row($n,false);},range(1,1000));
        for($k=0;$k<$v;$k++)$wpdb->history[$k*170]=r51_row($k*170+1,true);
        $result=GCU_Future_Intelligence::public_change_log(4);
        $ok=is_wp_error($result)&&$result->get_error_code()==='gcu_public_change_log_scan_incomplete'
            && count($wpdb->history_reads)===10;
    } elseif ($f===4) {
        $wpdb->history=$v%2?null:false;
        $result=GCU_Future_Intelligence::public_change_log(20);
        $rest=GCU_Future_Intelligence::rest_change_log();
        $render=new ReflectionMethod('GCU_Future_Intelligence','render_change_log');
        $render->setAccessible(true);
        $html=$render->invoke(null);
        $ok=is_wp_error($result)&&$result->get_error_code()==='gcu_public_change_log_read_failed'
            && is_wp_error($rest)&&$rest->get_error_code()==='gcu_public_change_log_read_failed'
            && strpos($html,'temporarily unavailable')!==false
            && count(GCU_Observability::$events)===3;
    } elseif ($f===5) {
        $wpdb->history=array();
        for($k=0;$k<1+$v;$k++)$wpdb->history[]=r51_row($k+1,true);
        $result=GCU_Future_Intelligence::public_change_log(20);
        $rest=GCU_Future_Intelligence::rest_change_log();
        $ok=is_array($result)&&count($result)===1+$v
            && $rest instanceof WP_REST_Response && count($rest->data['items'])===1+$v
            && GCU_Observability::$events===array();
    } elseif ($f===6) {
        $wpdb->jurisdiction=$v%2?false:null;
        $disclosure=GCU_Future_Intelligence::jurisdiction_disclosure('en-US','PK');
        $ok=is_wp_error($disclosure)&&$disclosure->get_error_code()==='gcu_jurisdiction_disclosure_read_failed'
            && count(GCU_Observability::$events)===1
            && GCU_Observability::$events[0]['fields']===array()
            && strpos(implode(' ', $wpdb->region_reads),"record_type='jurisdiction_copy'")!==false
            && strpos($future_source,'gcu-future__disclosure--unavailable')!==false
            && strpos($future_source,'if ( is_wp_error( $disclosure ) )')!==false;
    } elseif ($f===7) {
        $wpdb->jurisdiction=array(r51_disclosure('PK','PK-'.$v),r51_disclosure('ZZ','General-'.$v));
        $result=GCU_Future_Intelligence::jurisdiction_disclosure('en-US','PK');
        $ok=is_array($result)&&$result['body']==='Approved region guidance PK-'.$v
            && count($wpdb->region_reads)===1
            && strpos($wpdb->region_reads[0],"region IN ('PK','ZZ')")!==false
            && GCU_Observability::$events===array();
    } elseif ($f===8) {
        $bad=array(
            array('region'=>'PK','payload'=>'{"body":"unreviewed"}'),
            array('region'=>'PK','payload'=>'null'),
            array('region'=>'PK','payload'=>'not-json'),
            array('region'=>'PK','payload'=>array('nested'))
        );
        $wpdb->jurisdiction=array($bad[$v],r51_disclosure('ZZ','fallback-'.$v));
        $result=GCU_Future_Intelligence::jurisdiction_disclosure('en-US','PK');
        $ok=is_array($result) && $result['body']==='Approved region guidance fallback-'.$v
            && GCU_Observability::$events===array();
    } else {
        if($v===0)$wpdb->jurisdiction=array();
        if($v===1)$wpdb->jurisdiction=array(array('region'=>'PK','payload'=>'{"body":"unapproved"}'));
        if($v===2)$wpdb->jurisdiction=array(array('region'=>'ZZ','payload'=>'[]'));
        if($v===3)$wpdb->jurisdiction=array(array('region'=>'PK','payload'=>'null'));
        $result=GCU_Future_Intelligence::jurisdiction_disclosure('en-US','PK');
        $ok=is_array($result)&&$result===array()&&GCU_Observability::$events===array();
    }
    printf("R51 real-method public policy/region SQL check %02d/40: %s (family %d)\n",$case,$ok?'PASS':'FAIL',$f);
    if(!$ok)$errors[]=sprintf('R51 case %02d family %d failed',$case,$f);
}
if($errors){fwrite(STDERR,implode("\n",$errors)."\n");exit(1);}
echo "R51 40/40 direct real-method SQL-mocked bounded public policy history and regional disclosure tests PASS; no installed staging/live WordPress evidence.\n";
