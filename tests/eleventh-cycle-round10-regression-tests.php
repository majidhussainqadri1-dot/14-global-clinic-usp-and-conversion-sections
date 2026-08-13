<?php
$root = dirname( __DIR__ );
$obs = file_get_contents( $root . '/14-global-clinic-usp-integration/includes/class-gcu-observability.php' );
$failures = array();
function e11r10_assert( $condition, $message ) { global $failures; if ( ! $condition ) { $failures[] = $message; } }
e11r10_assert( false !== strpos( $obs, "$wpdb->last_error='';$found=$wpdb->get_var" ), 'Table existence probes must clear DB error state before reading.' );
e11r10_assert( false !== strpos( $obs, "query_errors[]='table_probe_'.$n" ), 'Table probe DB failures must be classified as query errors.' );
e11r10_assert( false !== strpos( $obs, "$wpdb->last_error='';$s=$wpdb->get_row" ), 'Table-engine probes must clear DB error state before reading.' );
e11r10_assert( false !== strpos( $obs, "query_errors[]='engine_probe_'.$n" ), 'Engine probe DB failures must be classified as query errors.' );
if ( $failures ) { fwrite( STDERR, "Eleventh-cycle Round 10 regression tests failed:\n- " . implode( "\n- ", $failures ) . "\n" ); exit( 1 ); }
echo "Eleventh-cycle Round 10 regression tests: PASS\n";
