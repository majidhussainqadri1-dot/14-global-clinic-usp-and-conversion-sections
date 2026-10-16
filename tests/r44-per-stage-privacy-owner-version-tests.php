<?php
/**
 * R44: 40 distinct direct REAL-method SQL-mocked regressions for
 * F14-FUT-13/15 per-stage privacy and F14-FR-011/FUT-14 owner v1 provenance.
 * Intentionally not WordPress staging or production acceptance.
 */
require __DIR__ . '/r42-analytics-read-integrity-tests.php';
$failures = array();
for ( $case = 1; $case <= 40; $case++ ) {
    $family = ($case - 1) % 10;
    $variation = intdiv($case - 1, 10);
    $wpdb->friction = array();
    $wpdb->owner = array( array(
        'event_type' => 'File09.ApplicationStarted.v1',
        'funnel_stage' => 'application_started',
        'total' => (string)(10 + $variation)
    ) );
    $wpdb->anomaly = array( array(
        'event_type' => 'File08.BookingStarted.v1',
        'funnel_stage' => 'booking_started',
        'current_count' => (string)(10 + $variation),
        'previous_seven_days' => (string)(90 + $variation * 7)
    ) );
    $wpdb->called = array();
    GCU_Observability::$logs = array();
    $ok = false;
    if ( 0 === $family ) {
        // One compliant stage, one rare stage: never disclose its name/count.
        $wpdb->friction = array(
            array('funnel_stage'=>'cta_selected','total'=>(string)(10 + $variation)),
            array('funnel_stage'=>'destination_loaded','total'=>(string)(1 + $variation))
        );
        $r = GCU_Future_Intelligence::friction_summary(30);
        $enc = json_encode($r);
        $ok = is_array($r) && $r['suppressed'] === true
            && count($r['stages']) === 1
            && $r['stages']['cta_selected'] === 10 + $variation
            && strpos($enc, 'destination_loaded') === false && $r['dropoffs'] === array();
    } elseif ( 1 === $family ) {
        $wpdb->friction = array(
            array('funnel_stage'=>'cta_selected','total'=>(string)(1 + $variation)),
            array('funnel_stage'=>'destination_loaded','total'=>(string)(10 + $variation))
        );
        $r = GCU_Future_Intelligence::friction_summary(30);
        $enc = json_encode($r);
        $ok = is_array($r) && $r['suppressed'] === true
            && count($r['stages']) === 1
            && $r['stages']['destination_loaded'] === 10 + $variation
            && strpos($enc, 'cta_selected') === false;
    } elseif ( 2 === $family ) {
        // Combined sample equals ten; neither independently reaches ten.
        $wpdb->friction = array(
            array('funnel_stage'=>'cta_selected','total'=>(string)(6 + $variation)),
            array('funnel_stage'=>'destination_loaded','total'=>(string)(4 - $variation))
        );
        $r = GCU_Future_Intelligence::friction_summary(30);
        $ok = is_array($r) && $r['suppressed'] === true && $r['stages'] === array()
            && $r['dropoff_status'] === 'insufficient_stage_sample' && $r['dropoffs'] === array();
    } elseif ( 3 === $family ) {
        $wpdb->friction = array(
            array('funnel_stage'=>'cta_selected','total'=>(string)(10 + $variation)),
            array('funnel_stage'=>'destination_loaded','total'=>(string)(10 + $variation))
        );
        $r = GCU_Future_Intelligence::friction_summary(30);
        $ok = is_array($r) && $r['suppressed'] === false
            && count($r['stages']) === 2 && $r['dropoffs'] === array();
    } elseif ( 4 === $family ) {
        $wpdb->friction = array(array('funnel_stage'=>'cta_selected','total'=>(string)(1 + $variation)));
        $r = GCU_Future_Intelligence::friction_summary(30);
        $ok = is_array($r) && $r['suppressed'] === true && $r['stages'] === array()
            && $r['dropoff_status'] === 'insufficient_sample';
    } elseif ( 5 === $family ) {
        $r = GCU_Owner_Outcomes::owner_stage_summary(30);
        $sql = implode(' ', $wpdb->called);
        $ok = is_array($r) && !$r['suppressed']
            && $r['success_rate'] === null && $r['linked_journey'] === false
            && strpos($sql, 'event_version=1') !== false
            && strpos($sql, "consent_state='granted'") !== false;
    } elseif ( 6 === $family ) {
        $r = GCU_Owner_Outcomes::observed_anomaly();
        $sql = implode(' ', $wpdb->called);
        $ok = $r['status'] === 'consented_owner_volume_observed'
            && $r['booking_success_rate'] === null
            && strpos($sql,'event_version=1') !== false
            && strpos($sql,"consent_state='granted'") !== false;
    } elseif ( 7 === $family ) {
        $wpdb->friction = array(
            array('funnel_stage'=>'cta_selected','total'=>(string)(10 + $variation)),
            array('funnel_stage'=>'destination_loaded','total'=>(string)(1 + $variation))
        );
        $r = GCU_Future_Intelligence::rest_friction(new WP_REST_Request());
        $enc = json_encode($r->data);
        $ok = $r instanceof WP_REST_Response && $r->data['suppressed'] === true
            && strpos($enc,'destination_loaded') === false
            && $r->data['owner_native_observations']['success_rate'] === null;
    } elseif ( 8 === $family ) {
        // Same underlying event table must never mix File14 telemetry with owner events.
        $wpdb->friction = array(array('funnel_stage'=>'cta_selected','total'=>(string)(10 + $variation)));
        $r = GCU_Future_Intelligence::friction_summary(30);
        $sql = implode(' ', $wpdb->called);
        $ok = !$r['suppressed'] && strpos($sql, "event_type='ClinicUSPFunnelEvent'") !== false
            && strpos($sql,'event_version=1') !== false
            && strpos($sql,"consent_state='granted'") !== false
            && strpos($sql,'subject_hash IS NOT NULL') !== false;
    } else {
        // Unregistered owner type/stage cannot be counted as trusted conversion.
        $wpdb->anomaly = array(array(
            'event_type'=>'File08.BookingStarted.v1','funnel_stage'=>'application_started',
            'current_count'=>(string)(30+$variation),'previous_seven_days'=>'200'
        ));
        $r = GCU_Owner_Outcomes::observed_anomaly();
        $sql = implode(' ', $wpdb->called);
        $ok = $r['status'] === 'insufficient_owner_event_evidence'
            && $r['signals'] === array() && $r['booking_success_rate'] === null
            && strpos($sql,'event_version=1') !== false;
    }
    printf("R44 actual-method privacy/version case %02d/40: %s (family %d)\n",$case,$ok?'PASS':'FAIL',$family);
    if (!$ok) $failures[] = sprintf('case %02d family %d FAILED', $case, $family);
}
if ($failures) { fwrite(STDERR,implode("\n",$failures)."\n"); exit(1); }
echo "R44: 40/40 direct real File14 PHP method SQL-mocked privacy/version tests PASS; no staging/live acceptance.\n";
