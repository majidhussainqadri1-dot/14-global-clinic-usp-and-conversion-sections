<?php
/**
 * R49: 40 direct real PHP methods for guarded Report and Governed Record pagination.
 * WPDB and WordPress mocked; no staging/Live testing.
 */
require __DIR__ . '/r41-faq-consistency-governance-tests.php';
function absint($x) { return abs((int)$x); }
class WP_REST_Request {
    private $args;
    public function __construct($values=array()) { $this->args=$values; }
    public function get_param($key) { return isset($this->args[$key])?$this->args[$key]:null; }
}
class R49DB extends R41DB {
    public $page_reports=array();
    public $page_records=array();
    public function get_results($sql,$format=ARRAY_A) {
        if(strpos($sql,'FROM wp_gcu_future_reports')!==false) {
            $this->queries[]=$sql;
            return $this->page_reports;
        }
        if(strpos($sql,'FROM wp_gcu_future_records')!==false) {
            $this->queries[]=$sql;
            return $this->page_records;
        }
        return parent::get_results($sql,$format);
    }
}
function r49_report($id) {
    return array('id'=>(string)$id,'public_id'=>'00000000-0000-4000-8000-000000000001',
        'report_type'=>'copy_quality','route_key'=>'global_clinic','block_key'=>null,
        'locale'=>'en-US','reason_code'=>'unclear','message'=>'Approved benign text',
        'status'=>'open','resolution'=>null,'row_version'=>'1',
        'created_at'=>'2026-10-17 00:00:00','updated_at'=>'2026-10-17 00:00:00');
}
function r49_record($id,$payload='{"title":"Approved policy","effective_date":"2026-10-17"}') {
    return array('id'=>(string)$id,'record_type'=>'change_log','record_key'=>'approved_record_'.$id,
        'locale'=>'en-US','region'=>'ZZ','status'=>'active','is_public'=>'1',
        'payload'=>$payload,'row_version'=>'1','review_due_at'=>null,'updated_at'=>'2026-10-17 00:00:00');
}
$bad=array();
for($i=1;$i<=40;$i++) {
    $family=($i-1)%10;$v=intdiv($i-1,10);
    $wpdb=new R49DB();
    $wpdb->page_reports=array(r49_report(1),r49_report(2),r49_report(3));
    $wpdb->page_records=array(r49_record(1),r49_record(2),r49_record(3));
    $wpdb->faq=array();
    $wpdb->graph=r41_rows();
    $wpdb->copy=array(array('title'=>'Safe','body'=>'Approved','cta_label'=>'Read'));
    $GLOBALS['r41_signals']=array();
    GCU_Observability::$events=array();
    $ok=false;
    $reports = new ReflectionMethod('GCU_Future_Intelligence','reports_page');
    $reports->setAccessible(true);
    $records = new ReflectionMethod('GCU_Future_Intelligence','records_page');
    $records->setAccessible(true);
    if($family===0) {
        $wpdb->page_reports=$v%2?null:false;
        $r=$reports->invoke(null,'open',0,2);
        $rest=GCU_Future_Intelligence::rest_reports(new WP_REST_Request(array('status'=>'open','limit'=>2)));
        $ok=is_wp_error($r)&&$r->get_error_code()==='gcu_future_reports_page_read_failed'
            && is_wp_error($rest)&&$rest->get_error_code()==='gcu_future_reports_page_read_failed'
            && count(GCU_Observability::$events)===2;
    } elseif($family===1) {
        $wpdb->page_records=$v%2?null:false;
        $r=$records->invoke(null,'',0,2);
        $rest=GCU_Future_Intelligence::rest_records(new WP_REST_Request(array('limit'=>2)));
        $ok=is_wp_error($r)&&$r->get_error_code()==='gcu_future_records_page_read_failed'
            && is_wp_error($rest)&&$rest->get_error_code()==='gcu_future_records_page_read_failed'
            && count(GCU_Observability::$events)===2;
    } elseif($family===2) {
        $r=r49_report(1);
        if($v===0)unset($r['id']);
        if($v===1)$r['id']='0';
        if($v===2)$r['id']='1e4';
        if($v===3)$r['id']=array('1');
        $wpdb->page_reports=array($r);
        $r=$reports->invoke(null,'open',0,2);
        $ok=is_wp_error($r)&&$r->get_error_code()==='gcu_future_reports_page_row_failed'
            && count(GCU_Observability::$events)===1;
    } elseif($family===3) {
        $r=r49_record(1);
        if($v===0)unset($r['id']);
        if($v===1)$r['id']='0';
        if($v===2)$r['id']='-1';
        if($v===3)unset($r['record_key']);
        $wpdb->page_records=array($r);
        $r=$records->invoke(null,'',0,2);
        $ok=is_wp_error($r)&&$r->get_error_code()==='gcu_future_records_page_row_failed'
            && count(GCU_Observability::$events)===1;
    } elseif($family===4) {
        $wrong=array('not json','null','"text"','[broken');
        $wpdb->page_records=array(r49_record(1,$wrong[$v]));
        $r=$records->invoke(null,'',0,2);
        $rest=GCU_Future_Intelligence::rest_records(new WP_REST_Request(array('limit'=>2)));
        $ok=is_wp_error($r)&&$r->get_error_code()==='gcu_future_records_page_payload_failed'
            && is_wp_error($rest)&&$rest->get_error_code()==='gcu_future_records_page_payload_failed'
            && count(GCU_Observability::$events)===2;
    } elseif($family===5) {
        $wpdb->page_reports=array();$wpdb->page_records=array();
        $r=$reports->invoke(null,'open',0,2);
        $q=$records->invoke(null,'',0,2);
        $rest=GCU_Future_Intelligence::rest_records(new WP_REST_Request(array('limit'=>2)));
        $ok=is_array($r)&&$r['items']===array()&&$r['next_cursor']===null
            && is_array($q)&&$q['items']===array()&&$q['next_cursor']===null
            && $rest instanceof WP_REST_Response&&$rest->data['items']===array()
            && GCU_Observability::$events===array();
    } elseif($family===6) {
        $r=$reports->invoke(null,'open',$v,2);
        $rest=GCU_Future_Intelligence::rest_reports(new WP_REST_Request(array('limit'=>2,'status'=>'open')));
        $ok=($v===0 ? is_array($r)&&count($r['items'])===2&&$r['next_cursor']===2
            : ($v===1 ? is_wp_error($r) : is_wp_error($r)))
            && $rest instanceof WP_REST_Response
            && count($rest->data['items'])===2
            && !isset($rest->data['items'][0]['id']);
    } elseif($family===7) {
        $wpdb->page_records=array(r49_record(1,$v%2?'[]':'{}'),
            r49_record(2,'{"title":"Policy"}'),r49_record(3,'[]'));
        $r=$records->invoke(null,'',0,2);
        $rest=GCU_Future_Intelligence::rest_records(new WP_REST_Request(array('limit'=>2)));
        $ok=is_array($r)&&count($r['items'])===2&&$r['next_cursor']===2
            && is_array($r['items'][0]['payload'])&&$r['items'][0]['payload']===array()
            && !isset($r['items'][0]['id'])
            && $rest instanceof WP_REST_Response&&count($rest->data['items'])===2;
    } elseif($family===8) {
        $wpdb->page_reports=array(r49_report(2),r49_report($v%2?2:1));
        $r=$reports->invoke(null,'open',$v===2?2:0,2);
        $ok=is_wp_error($r)&&$r->get_error_code()==='gcu_future_reports_page_row_failed'
            && count(GCU_Observability::$events)===1;
    } else {
        $wpdb->page_records=array(r49_record(2),r49_record($v%2?2:1));
        $r=$records->invoke(null,'',$v===2?2:0,2);
        $ok=is_wp_error($r)&&$r->get_error_code()==='gcu_future_records_page_row_failed'
            && count(GCU_Observability::$events)===1;
    }
    printf("R49 actual methods governed pagination %02d/40: %s (family %d)\n",$i,$ok?'PASS':'FAIL',$family);
    if(!$ok)$bad[]=sprintf('R49 case %02d (family %d) failed',$i,$family);
}
if($bad){fwrite(STDERR,implode("\n",$bad)."\n");exit(1);}
echo "R49: 40/40 direct real File14 PHP methods SQL-mocked report/record page tests PASS, not WordPress staging/live.\n";
