<?php
/**
 * R50 forty actual File14 guarded PHP reader method SQL-mocked regressions.
 * Reuses R41 WordPress stubs/tests. These are NOT installed WordPress, staging
 * or production database/browser evidence.
 */
require __DIR__ . '/r41-faq-consistency-governance-tests.php';

class R50DB extends R41DB {
    public $reports_data = array();
    public $records_data = array();
    public function get_results( $sql, $format = ARRAY_A ) {
        if ( strpos( $sql, 'FROM wp_gcu_future_reports' ) !== false ) {
            $this->queries[] = $sql;
            return $this->reports_data;
        }
        if ( strpos( $sql, 'FROM wp_gcu_future_records' ) !== false ) {
            $this->queries[] = $sql;
            return $this->records_data;
        }
        return parent::get_results( $sql, $format );
    }
}
function r50_report( $v = 1 ) {
    return array(
        'public_id'=>'00000000-0000-4000-8000-00000000000' . $v,
        'report_type'=>'copy_quality', 'route_key'=>'global_clinic',
        'block_key'=>null, 'locale'=>'en-US', 'reason_code'=>'unclear',
        'message'=>'Reviewed benign feedback', 'status'=>'open', 'resolution'=>null,
        'row_version'=>'1', 'created_at'=>'2026-10-17 00:00:00',
        'updated_at'=>'2026-10-17 00:00:00'
    );
}
function r50_record( $v = 1, $payload = '{"title":"Policy","effective_date":"2026-10-17"}' ) {
    return array(
        'record_type'=>'change_log', 'record_key'=>'approved_note_' . $v,
        'locale'=>'en-US', 'region'=>'ZZ', 'status'=>'active',
        'is_public'=>1, 'payload'=>$payload, 'row_version'=>'1',
        'review_due_at'=>null, 'updated_at'=>'2026-10-17 00:00:00'
    );
}
$failures = array();
for ( $case = 1; $case <= 40; $case++ ) {
    $family = ( $case - 1 ) % 10;
    $v = intdiv( $case - 1, 10 );
    $wpdb = new R50DB();
    $wpdb->reports_data = array( r50_report(1), r50_report(2) );
    $wpdb->records_data = array( r50_record(1), r50_record(2) );
    $wpdb->copy = array( array( 'title'=>'Approved','body'=>'Safe','cta_label'=>'Read' ) );
    $wpdb->graph = r41_rows();
    $wpdb->faq = array();
    $GLOBALS['r41_signals'] = array();
    $wpdb->queries = array();
    GCU_Observability::$events = array();
    $ok = false;

    if ( $family === 0 ) {
        $wpdb->reports_data = $v % 2 ? null : false;
        $r = GCU_Future_Intelligence::reports('open',10);
        $ok = is_wp_error($r) && $r->get_error_code()==='gcu_future_moderation_read_failed'
            && count(GCU_Observability::$events)===1
            && GCU_Observability::$events[0]['fields']===array();
    } elseif ( $family === 1 ) {
        $wpdb->reports_data = $v % 2 ? false : null;
        $r = GCU_Future_Intelligence::review_queue_reports(15);
        $ok = is_wp_error($r) && $r->get_error_code()==='gcu_future_moderation_read_failed'
            && count(GCU_Observability::$events)===1;
    } elseif ( $family === 2 || $family === 3 ) {
        $bad = r50_report(1);
        if ($v===0) unset($bad['public_id']);
        if ($v===1) $bad['message']=array('nested user report');
        if ($v===2) $bad['row_version']='1e2';
        if ($v===3) $bad['status']='';
        $wpdb->reports_data = array($bad);
        $r = $family===2 ? GCU_Future_Intelligence::reports('open',10)
            : GCU_Future_Intelligence::review_queue_reports(10);
        $ok = is_wp_error($r) && $r->get_error_code()==='gcu_future_moderation_row_failed'
            && count(GCU_Observability::$events)===1;
    } elseif ( $family === 4 ) {
        $wpdb->records_data = $v%2 ? false : null;
        $r = GCU_Future_Intelligence::records('change_log',false,100,0);
        $ok = is_wp_error($r) && $r->get_error_code()==='gcu_future_governed_list_read_failed'
            && count(GCU_Observability::$events)===1;
    } elseif ( $family === 5 ) {
        $wpdb->records_data = $v%2 ? null : false;
        $r = GCU_Future_Intelligence::records('change_log',true,100,0);
        $log = GCU_Future_Intelligence::public_change_log(20);
        $ok = is_wp_error($r) && $r->get_error_code()==='gcu_public_change_log_read_failed'
            && is_wp_error($log) && $log->get_error_code()==='gcu_public_change_log_read_failed'
            && count(GCU_Observability::$events)===2;
    } elseif ( $family === 6 ) {
        $row = r50_record(1);
        if($v===0) unset($row['record_key']);
        if($v===1) $row['locale']=array('not a string');
        if($v===2) $row['payload']=array('nested');
        if($v===3) $row['row_version']='-1';
        $wpdb->records_data=array($row);
        $r = GCU_Future_Intelligence::records('change_log',false,50,0);
        $ok = is_wp_error($r) && $r->get_error_code()==='gcu_future_governed_list_row_failed'
            && count(GCU_Observability::$events)===1;
    } elseif ( $family === 7 ) {
        $bad = array( 'not-json', 'null', '"scalar"', '37' );
        $wpdb->records_data=array(r50_record(1,$bad[$v]));
        $r = GCU_Future_Intelligence::records('change_log',false,50,0);
        $ok = is_wp_error($r) && $r->get_error_code()==='gcu_future_governed_list_payload_failed'
            && count(GCU_Observability::$events)===1;
    } elseif ( $family === 8 ) {
        $wpdb->reports_data = $v===0 ? array() : array( r50_report(1) );
        $wpdb->records_data = $v===1 ? array() : array( r50_record(1, $v%2 ? '[]' : '{}') );
        $r = GCU_Future_Intelligence::reports('open',10);
        $queue = GCU_Future_Intelligence::review_queue_reports(10);
        $record = GCU_Future_Intelligence::records('change_log',false,20,0);
        $ok = is_array($r) && count($r)===($v===0?0:1)
            && is_array($queue) && count($queue)===($v===0?0:1)
            && is_array($record) && count($record)===($v===1?0:1)
            && ( $v===1 || $record[0]['payload']===array() )
            && count(GCU_Observability::$events)===0;
    } else {
        $wpdb->records_data = $v===0 ? array() : array( r50_record($v) );
        $r = GCU_Future_Intelligence::records('change_log',true,40,0);
        $log = GCU_Future_Intelligence::public_change_log(20);
        $ok = is_array($r) && count($r)===($v===0?0:1)
            && is_array($log) && count($log)===($v===0?0:1)
            && ($v===0 || $log[0]['payload']['title']==='Policy')
            && count(GCU_Observability::$events)===0;
    }

    printf("R50 direct guarded SQL list method check %02d/40: %s (family %d)\n",
        $case, $ok?'PASS':'FAIL', $family );
    if(!$ok) $failures[] = sprintf('R50 case %02d family %d FAILED',$case,$family);
}
if($failures) { fwrite(STDERR,implode("\n",$failures)."\n");exit(1); }
echo "R50: 40/40 actual File14 guarded reader methods SQL-mocked PASS. Not staging/live WordPress.\n";
