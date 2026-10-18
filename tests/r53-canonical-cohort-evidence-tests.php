<?php
/**
 * R53 — 40 real policy-method privacy cohort test vectors.
 * Source unit/regression only, not WordPress staging/live acceptance.
 */
define( 'ABSPATH', __DIR__ );
require_once dirname( __DIR__ ) . '/14-global-clinic-usp-integration/includes/class-gcu-future-policy.php';

$cases = array(
	array( '0', 10, false ),
	array( '1', 10, false ),
	array( '9', 10, false ),
	array( '10', 10, true ),
	array( '11', 10, true ),
	array( '99', 10, true ),
	array( 0, 10, false ),
	array( 1, 10, false ),
	array( 9, 10, false ),
	array( 10, 10, true ),
	array( 11, 10, true ),
	array( 99, 10, true ),
	array( true, 10, false ),
	array( false, 10, false ),
	array( null, 10, false ),
	array( array(), 10, false ),
	array( array('10'), 10, false ),
	array( 10.0, 10, false ),
	array( 10.5, 10, false ),
	array( 9.9, 10, false ),
	array( '10.0', 10, false ),
	array( '10.5', 10, false ),
	array( '10junk', 10, false ),
	array( '1e2', 10, false ),
	array( '01', 10, false ),
	array( '0010', 10, false ),
	array( '+10', 10, false ),
	array( '-10', 10, false ),
	array( ' 10', 10, false ),
	array( '10 ', 10, false ),
	array( '0xA', 10, false ),
	array( '١٠', 10, false ),
	array( '999999999999999999999999999999999999', 10, false ),
	array( 20, 11, true ),
	array( 20, 21, false ),
	array( 20, '20', true ),
	array( 20, '20.5', false ),
	array( 20, -1, false ),
	array( 20, true, false ),
	array( 20, '020', false ),
);
if ( 40 !== count( $cases ) ) { fwrite( STDERR, "R53 test-vector completeness failed\n" ); exit( 2 ); }
$failures = array();
foreach ( $cases as $i => $case ) {
	$actual = GCU_Future_Policy::cohort_allowed( $case[0], $case[1] );
	if ( $actual !== $case[2] ) { $failures[] = sprintf( 'R53 cohort case %02d expected %s got %s', $i + 1, var_export( $case[2], true ), var_export( $actual, true ) ); }
}
foreach ( array( '1junk', '1.0', '01', '1e2', -1, 0, null, array(), 1.5 ) as $i => $owner_loaded ) {
	$proof = GCU_Future_Policy::quality_evidence_status( 20, $owner_loaded, 90, 90, true );
	if ( ! is_array( $proof ) || ! empty( $proof['complete'] ) ||
		! in_array( 'owner_handoff_confirmation_unavailable', isset( $proof['missing'] ) ? $proof['missing'] : array(), true ) ) {
		$failures[] = sprintf( 'R53 malformed owner loaded %02d incorrectly accepted', $i + 1 );
	}
}
foreach ( array( 1, '1', 8, '8' ) as $owner_loaded ) {
	$proof = GCU_Future_Policy::quality_evidence_status( 20, $owner_loaded, 90, 90, true );
	if ( empty( $proof['complete'] ) ) { $failures[] = 'R53 canonical positive owner evidence rejected'; }
}
$withheld = GCU_Future_Policy::quality_evidence_status( '10.4', 1, 90, 90, true );
if ( ! in_array( 'insufficient_cta_sample', $withheld['missing'], true ) ) {
	$failures[] = 'R53 malformed CTA sample accepted';
}
if ( $failures ) { fwrite( STDERR, implode( "\n", $failures ) . "\n" ); exit( 1 ); }
echo "R53: 40 canonical cohort vectors + 14 quality-evidence vectors PASS. Source only.\n";
