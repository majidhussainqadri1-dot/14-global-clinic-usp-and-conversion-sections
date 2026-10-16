<?php
/**
 * R43: forty direct REAL File14 method/SQL-source checks.
 * R42 supplies isolated WordPress/SQL stubs and reruns the prior 40 checks.
 * This is not a deployed WordPress plugin, DB schema or live E2E test.
 */
require __DIR__ . '/r42-analytics-read-integrity-tests.php';
$failure = array();
$bad_count = array( '-1', '1e2', 12.5, (string) PHP_INT_MAX . '0' );
$bad_text = array( '00', '01', ' 10', '+10' );
$good_count = array( '0', '9', '10', (string) PHP_INT_MAX );
$good_positive = array( '10', '11', '20', '100' );
for ( $case = 1; $case <= 40; $case++ ) {
    $family = ( $case - 1 ) % 10;
    $v = intdiv( $case - 1, 10 );
    $wpdb->called = array();
    $wpdb->owner = array(array( 'event_type'=>'File09.ApplicationStarted.v1', 'funnel_stage'=>'application_started','total'=>'11' ));
    $wpdb->friction = array(array( 'funnel_stage'=>'cta_selected', 'total'=>'11' ));
    $wpdb->anomaly = array(array( 'event_type'=>'File08.BookingStarted.v1', 'funnel_stage'=>'booking_started', 'current_count'=>'11', 'previous_seven_days'=>'210' ));
    GCU_Observability::$logs = array();
    $ok = false;
    if ( $family === 0 ) {
        if ( $v === 0 ) { $wpdb->anomaly = array( null ); }
        if ( $v === 1 ) { $wpdb->anomaly = array( array('event_type'=>'File08.BookingStarted.v1') ); }
        if ( $v === 2 ) { $wpdb->anomaly[0]['funnel_stage'] = array( 'booking_started' ); }
        if ( $v === 3 ) { $wpdb->anomaly[0]['event_type'] = 2; }
        $result = GCU_Owner_Outcomes::observed_anomaly();
        $ok = is_array($result) && $result['status'] === 'owner_event_volume_read_unverified'
            && $result['suppressed'] && !$result['signals'] && $result['booking_success_rate'] === null
            && count(GCU_Observability::$logs) === 1;
    } elseif ( $family === 1 || $family === 2 ) {
        $wpdb->anomaly[0][$family === 1 ? 'current_count':'previous_seven_days'] = $bad_count[$v];
        $result = GCU_Owner_Outcomes::observed_anomaly();
        $ok = $result['status'] === 'owner_event_volume_read_unverified' && $result['suppressed']
            && count(GCU_Observability::$logs) === 1;
    } elseif ( $family === 3 || $family === 4 ) {
        if ( $family === 3 ) { $wpdb->owner[0]['total'] = $bad_count[$v]; }
        else { $wpdb->friction[0]['total'] = $bad_count[$v]; }
        $result = $family === 3 ? GCU_Owner_Outcomes::owner_stage_summary(30)
            : GCU_Future_Intelligence::friction_summary(30);
        $code = $family === 3 ? 'gcu_owner_stage_row_unverified' : 'gcu_friction_stage_row_unverified';
        $ok = is_wp_error($result) && $result->get_error_code() === $code
            && count(GCU_Observability::$logs) === 1;
    } elseif ( $family === 5 ) {
        $wpdb->friction[0]['total'] = $good_positive[$v];
        $r = GCU_Future_Intelligence::friction_summary(30);
        $sql = implode("\n", $wpdb->called);
        $ok = is_array($r) && !$r['suppressed'] && $r['dropoffs'] === array()
            && strpos($sql, "event_type='ClinicUSPFunnelEvent'") !== false
            && strpos($sql, "event_version=1") !== false
            && strpos($sql, "consent_state='granted'") !== false
            && strpos($sql, "subject_hash IS NOT NULL") !== false;
    } elseif ( $family === 6 ) {
        $value = $good_count[$v];
        $ok = GCU_Future_Policy::valid_aggregate_count($value);
        $wpdb->owner[0]['total'] = $value;
        $r = GCU_Owner_Outcomes::owner_stage_summary(30);
        $ok = $ok && is_array($r)
            && ($v >= 2 ? (!$r['suppressed'] && count($r['items']) === 1) : ($r['suppressed'] && $r['items']===array()));
    } elseif ( $family === 7 ) {
        $value = $bad_text[$v];
        $ok = !GCU_Future_Policy::valid_aggregate_count($value);
        $wpdb->friction[0]['total'] = $value;
        $r = GCU_Future_Intelligence::friction_summary(30);
        $ok = $ok && is_wp_error($r) && $r->get_error_code() === 'gcu_friction_stage_row_unverified';
    } elseif ( $family === 8 ) {
        $wpdb->anomaly[0]['current_count'] = $good_positive[$v];
        $r = GCU_Owner_Outcomes::observed_anomaly();
        $ok = $r['status'] === 'consented_owner_volume_observed' && $r['suppressed'] === false
            && $r['booking_success_rate'] === null && $r['owner_correlated_funnel'] === false;
    } else {
        $request = new WP_REST_Request();
        if ($v === 0) { $wpdb->owner[0]['total'] = '-4'; }
        if ($v === 1) { $wpdb->friction[0]['total'] = '4.2'; }
        if ($v === 2) { $wpdb->owner[0]['total'] = '1e2'; }
        if ($v === 3) { $wpdb->friction[0]['total'] = (string) PHP_INT_MAX . '0'; }
        $r = GCU_Future_Intelligence::rest_friction($request);
        $want = in_array($v,array(0,2),true)?'gcu_owner_stage_row_unverified':'gcu_friction_stage_row_unverified';
        $ok = is_wp_error($r) && $r->get_error_code() === $want;
    }
    printf("R43 actual-method SQL/privacy case %02d/40: %s (family %d)\n", $case, $ok?'PASS':'FAIL', $family);
    if (!$ok) { $failure[] = sprintf('R43 case %02d family %d failed', $case, $family); }
}
if ($failure) { fwrite(STDERR,implode("\n",$failure)."\n");exit(1); }
echo "R43: 40/40 direct File14 methods with SQL-mocked corrupt aggregates, consent query, suppression and REST errors PASS. Not staging/live.\n";
