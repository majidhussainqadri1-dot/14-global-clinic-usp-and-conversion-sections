<?php
$root = dirname( __DIR__ );
$hard = file_get_contents( $root . '/14-global-clinic-usp-integration/includes/class-gcu-eleventh-review-hardening.php' );
$failures = array();
function t12r01_assert( $condition, $message ) { global $failures; if ( ! $condition ) { $failures[] = $message; } }
t12r01_assert( false !== strpos( $hard, 'verify_schema_constraints' ), 'Postcondition verification must include database constraint verification.' );
t12r01_assert( false !== strpos( $hard, 'SHOW INDEX FROM' ), 'Schema postconditions must inspect actual database indexes.' );
t12r01_assert( false !== strpos( $hard, "'record_identity' => array( 'record_type', 'record_key', 'locale', 'region' )" ), 'Future record composite identity must be verified.' );
t12r01_assert( false !== strpos( $hard, "'event_id' => array( 'event_id' )" ) && false !== strpos( $hard, "'command_key' => array( 'command_key' )" ), 'Critical idempotency identities must retain verified unique indexes.' );
t12r01_assert( false !== strpos( $hard, 'gcu_schema_index_probe_failed' ) && false !== strpos( $hard, 'gcu_schema_constraints_unverified' ), 'Index query failure and missing constraints must fail closed.' );
t12r01_assert( substr_count( $hard, "update_option( 'gcu_enabled', 0, false )" ) >= 2, 'Activation and runtime postcondition failure must disable File 14 base runtime.' );
if ( $failures ) { fwrite( STDERR, "Twelfth-cycle Round 01 regression tests failed:\n- " . implode( "\n- ", $failures ) . "\n" ); exit( 1 ); }
echo "Twelfth-cycle Round 01 regression tests: PASS\n";
