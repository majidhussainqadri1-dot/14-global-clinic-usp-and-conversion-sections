<?php
/**
 * R45: forty actual File14 PHP-method SQL-mocked scenarios.
 * Reuses R41's isolated WP/DB test harness (runs its own forty checks first).
 * NOT real WordPress, production DB, backend retention or browser E2E.
 */
require __DIR__ . '/r41-faq-consistency-governance-tests.php';
function esc_html( $value ) {
    return htmlspecialchars( (string) $value, ENT_QUOTES, 'UTF-8' );
}

class R45DB extends R41DB {
    public $public_records = array();
    public $report_deletes = array();
    public $record_deletes = array();
    public $read_queries = array();
    public $delete_queries = array();
    public function get_results( $sql, $format = ARRAY_A ) {
        if ( strpos( $sql, 'FROM wp_gcu_future_records' ) !== false
            && strpos( $sql, 'record_type=' ) !== false ) {
            $this->read_queries[] = $sql;
            return $this->public_records;
        }
        return parent::get_results( $sql, $format );
    }
    public function query( $sql ) {
        if ( strpos( $sql, 'DELETE FROM wp_gcu_future_reports' ) !== false ) {
            $this->delete_queries[] = 'reports';
            return $this->report_deletes ? array_shift( $this->report_deletes ) : 0;
        }
        if ( strpos( $sql, 'DELETE FROM wp_gcu_future_records' ) !== false ) {
            $this->delete_queries[] = 'records';
            return $this->record_deletes ? array_shift( $this->record_deletes ) : 0;
        }
        return parent::query( $sql );
    }
}
function r45_history_row( $n ) {
    return array(
        'record_type' => 'change_log', 'record_key' => 'approved_history_' . $n,
        'locale' => 'en-US', 'region' => 'ZZ', 'status' => 'active', 'is_public' => 1,
        'payload' => json_encode( array(
            'title' => 'Approved policy change ' . $n,
            'summary' => 'Policy notice with reviewed effective date',
            'effective_date' => '2026-10-17'
        ) ),
        'row_version' => 1, 'review_due_at' => null,
        'updated_at' => '2026-10-17 00:00:00',
    );
}

$failed = array();
for ( $case = 1; $case <= 40; $case++ ) {
    $family = ( $case-1 ) % 10;
    $variant = intdiv( $case-1, 10 );
    $wpdb = new R45DB();
    $wpdb->faq = array();
    $wpdb->graph = array();
    $wpdb->copy = array();
    GCU_Install::$runtime = true;
    GCU_Observability::$events = array();
    $ok = false;
    if ( $family === 0 ) {
        $wpdb->report_deletes = array( false );
        $result = GCU_Future_Intelligence::cleanup();
        $ok = is_wp_error($result) && $result->get_error_code()==='gcu_future_report_cleanup_failed'
            && $wpdb->delete_queries === array('reports')
            && count(GCU_Observability::$events) === 1;
    } elseif ( $family === 1 ) {
        $wpdb->record_deletes = array( false );
        $result = GCU_Future_Intelligence::cleanup();
        $ok = is_wp_error($result) && $result->get_error_code()==='gcu_future_record_cleanup_failed'
            && $wpdb->delete_queries === array('reports','records')
            && count(GCU_Observability::$events) === 1;
    } elseif ( $family === 2 ) {
        GCU_Install::$runtime = new WP_Error('gcu_runtime_unready');
        $result = GCU_Future_Intelligence::cleanup();
        $ok = is_wp_error($result) && $result->get_error_code()==='gcu_runtime_unready'
            && $wpdb->delete_queries === array()
            && count(GCU_Observability::$events) === 1;
    } elseif ( $family === 3 ) {
        if ( $variant > 0 ) {
            $wpdb->report_deletes = array($variant);
            $wpdb->record_deletes = array($variant);
        }
        $result = GCU_Future_Intelligence::cleanup();
        $ok = is_array($result) && $result['reports']===$variant
            && $result['records']===$variant
            && $wpdb->delete_queries === array('reports','records')
            && GCU_Observability::$events === array();
    } elseif ( $family === 4 ) {
        $wpdb->report_deletes = array_merge( array_fill( 0, $variant + 1, 200 ), array( false ) );
        $result = GCU_Future_Intelligence::cleanup();
        $ok = is_wp_error($result) && $result->get_error_code()==='gcu_future_report_cleanup_failed'
            && count($wpdb->delete_queries)===$variant+2 && count(array_unique($wpdb->delete_queries))===1
            && count(GCU_Observability::$events) === 1;
    } elseif ( $family === 5 ) {
        $wpdb->record_deletes = array_merge( array_fill( 0, $variant + 1, 200 ), array( false ) );
        $result = GCU_Future_Intelligence::cleanup();
        $ok = is_wp_error($result) && $result->get_error_code()==='gcu_future_record_cleanup_failed'
            && count($wpdb->delete_queries)===$variant+3 && $wpdb->delete_queries[0]==='reports' && $wpdb->delete_queries[1]==='records'
            && count(GCU_Observability::$events) === 1;
    } elseif ( $family === 6 ) {
        $wpdb->public_records = ($variant % 2) ? null : false;
        $log = GCU_Future_Intelligence::public_change_log(20);
        $rest = GCU_Future_Intelligence::rest_change_log();
        $private = new ReflectionMethod('GCU_Future_Intelligence','render_change_log');
        $private->setAccessible(true);
        $markup = $private->invoke(null);
        $ok = is_wp_error($log) && $log->get_error_code()==='gcu_public_change_log_read_failed'
            && is_wp_error($rest) && $rest->get_error_code()==='gcu_public_change_log_read_failed'
            && strpos($markup,'temporarily unavailable')!==false
            && strpos($markup,'SELECT ')===false && strpos($markup,'wp_gcu')===false
            && count(GCU_Observability::$events)===3;
    } elseif ( $family === 7 ) {
        $wpdb->public_records = ($variant % 2) ? null : false;
        $result = GCU_Future_Intelligence::records('change_log',true,100,0);
        $ok = is_wp_error($result) && $result->get_error_code()==='gcu_public_change_log_read_failed'
            && count(GCU_Observability::$events)===1;
    } elseif ( $family === 8 ) {
        $log = GCU_Future_Intelligence::public_change_log(20);
        $rest = GCU_Future_Intelligence::rest_change_log();
        $ok = is_array($log) && $log===array() && $rest instanceof WP_REST_Response
            && isset($rest->data['items']) && $rest->data['items']===array()
            && GCU_Observability::$events===array();
    } else {
        $wpdb->public_records = array_map('r45_history_row',range(1,$variant+1));
        $log = GCU_Future_Intelligence::public_change_log(20);
        $rest = GCU_Future_Intelligence::rest_change_log();
        $private = new ReflectionMethod('GCU_Future_Intelligence','render_change_log');
        $private->setAccessible(true);
        $html = $private->invoke(null);
        $ok = is_array($log) && count($log)===$variant+1
            && isset($log[0]['payload']['effective_date'])
            && $rest instanceof WP_REST_Response
            && count($rest->data['items'])===$variant+1
            && strpos($html,'Approved policy change 1')!==false
            && strpos($html,'temporarily unavailable')===false;
    }
    printf("R45 actual-method governance/retention scenario %02d/40: %s (family %d)\n", $case,$ok?'PASS':'FAIL',$family);
    if (!$ok) $failed[] = sprintf("R45 case %02d family %d failed", $case,$family);
}
GCU_Install::$runtime = true;
if($failed) {fwrite(STDERR,implode("\n",$failed)."\n");exit(1);}
echo "R45 40/40 real-method SQL mocked retention, public change-log, REST/HTML cases PASS. No WordPress staging or production tests.\n";
