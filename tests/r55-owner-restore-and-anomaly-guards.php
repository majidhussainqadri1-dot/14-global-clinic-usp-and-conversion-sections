<?php
/**
 * R55 two independent forty-case actual File14 PHP safety regressions.
 * Mock database/WordPress mode, not staging, deployed owners or Live.
 */
require __DIR__ . '/r34-experiment-lifecycle-regressions.php';
$errors = array();
$r34_opts = array( 'gcu_experiment_serving_paused' => 0 );
for ( $i = 0; $i < 40; ++$i ) {
    $family = $i % 10;
    $v = intdiv( $i, 10 );
    $requires_restoration = in_array( $family, array( 1, 2, 3, 4, 5, 6, 7, 8, 9 ), true );
    $variants = array( 'Ethical approved copy A', 'Ethical approved copy B' );
    if ( 1 === $family ) { $variants = array( array( 'placement_key' => 'clinic_hero', 'copy' => 'A' ), array( 'placement_key' => 'clinic_hero', 'copy' => 'B' ) ); }
    if ( 2 === $family ) { $variants = array( array( 'layout' => 'wide' ), array( 'layout' => 'compact' ) ); }
    if ( 3 === $family ) { $variants = array( array( 'navigation' => 'test' ), array( 'navigation' => 'original' ) ); }
    if ( 4 === $family ) { $variants = array( 'placement changes', 'slot changes' ); }
    if ( 5 === $family ) { $variants = array( array( 'shell' => 'one' ), array( 'shell' => 'two' ) ); }
    if ( 6 === $family ) { $variants = array( array( 'block_key' => 'x' ), array( 'block_key' => 'y' ) ); }
    if ( 7 === $family ) { $variants = array( array( 'slot_key' => 'x' ), array( 'slot_key' => 'y' ) ); }
    if ( 8 === $family ) { $variants = array( array( 'placement_id' => 101 + $v ), array( 'placement_id' => 201 + $v ) ); }
    if ( 9 === $family ) { $variants = null; }
    $row = array( 'variants' => json_encode( $variants ), 'row_version' => $i + 1 );
    $result = GCU_Future_Policy::experiment_baseline_restoration_ready( $row );
    $ok = ! $requires_restoration === $result;
    if ( ! $ok ) { $errors[] = 'Placement owner-CAS gate ' . ( $i + 1 ) . ' unexpectedly allowed/blocked'; }
    printf( "R55 owner restoration gate %02d/40: %s\n", $i + 1, $ok ? 'PASS' : 'FAIL' );
}
for ( $i = 0; $i < 40; ++$i ) {
    $family = $i % 10;
    $variation = intdiv( $i, 10 );
    $anomaly = array( 'severity' => 'none' );
    if ( 0 === $family ) { $anomaly = array( 'severity' => 'unverified' ); }
    if ( 1 === $family ) { $anomaly = array( 'severity' => 'none', 'status' => 'owner_event_volume_read_unverified' ); }
    if ( 2 === $family ) { $anomaly = array( 'status' => 'owner_event_volume_read_unverified' ); }
    if ( 3 === $family ) { $anomaly = false; }
    if ( 4 === $family ) { $anomaly = array( 'severity' => 'high' ); }
    if ( 5 === $family ) { $anomaly = array( 'severity' => 'none' ); }
    if ( 6 === $family ) { $anomaly = array( 'severity' => 'medium' ); }
    if ( 7 === $family ) { $anomaly = array( 'severity' => 'unavailable' ); }
    if ( 8 === $family ) { $anomaly = array( 'severity' => 0 ); }
    if ( 9 === $family ) { $anomaly = array(); }
    $should_stop = ! in_array( $family, array( 5, 6 ), true );
    $wpdb = new R34DB();
    $wpdb->rows = array( array(
        'id' => 1000 + $i, 'public_id' => sprintf( '%08d-2222-4222-8222-123456789abc', $i + 1 ),
        'status' => 'running', 'row_version' => 2 + $variation, 'ends_at' => '2027-10-13 15:30:00'
    ) );
    $wpdb->expired = 0;
    $wpdb->fail_check = false;
    GCU_Experiment_Baseline::$restored = true;
    GCU_Future_Intelligence::$policy_ok = true;
    $r34_opts = array( GCU_Future_Intelligence::LAST_ANOMALY_OPTION => $anomaly );
    $before = count( GCU_Plugin::instance()->repository()->audits );
    $result = GCU_Fifth_Review_Hardening::transactional_early_stop_guard();
    $delta = count( GCU_Plugin::instance()->repository()->audits ) - $before;
    $ok = $should_stop ? ( 1 === $result && 1 === $delta ) : ( 0 === $result && 0 === $delta );
    if ( ! $ok ) { $errors[] = 'Owner anomaly failure evidence ' . ( $i + 1 ) . ' did not fail closed correctly'; }
    printf( "R55 owner anomaly fail-close %02d/40: %s\n", $i + 1, $ok ? 'PASS' : 'FAIL' );
}
if ( $errors ) { fwrite( STDERR, implode( "\n", $errors ) . "\n" ); exit( 1 ); }
echo "R55: 40 owner restore contract activation guards + 40 owner anomaly direct stop guards PASS. SOURCE MOCKS ONLY. #19/#22 remain unverified operational blockers.\n";
