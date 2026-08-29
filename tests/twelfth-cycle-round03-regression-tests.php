<?php
$root = dirname( __DIR__ );
$future = file_get_contents( $root . '/14-global-clinic-usp-integration/includes/class-gcu-future-intelligence.php' );
$main = file_get_contents( $root . '/14-global-clinic-usp-integration/global-clinic-usp-integration.php' );
$failures = array();
function t12r03_assert( $condition, $message ) { global $failures; if ( ! $condition ) { $failures[] = $message; } }

t12r03_assert( false !== strpos( $future, '<input type="hidden" name="route_key" value="' ), 'Public report form must emit a valid route_key hidden field.' );
t12r03_assert( false === strpos( $future, 'name="route_key value="' ), 'Malformed route_key hidden-field markup must not recur.' );
t12r03_assert( false !== strpos( $future, "in_array( \$route, array( 'global_clinic', 'how_it_works' ), true )" ), 'Report storage must retain the two canonical reportable File 14 routes.' );
t12r03_assert( false !== strpos( $main, "array( 'global_clinic', 'how_it_works', 'find_doctor', 'start_clinic' )" ), 'Public route handling must retain an explicit canonical allowlist.' );
t12r03_assert( false !== strpos( $main, "set_query_var( 'gcu_route', '' )" ) && false !== strpos( $main, '$wp_query->set_404()' ), 'Unknown File 14 routes must continue to fail as 404 instead of rendering a hidden landing path.' );

if ( $failures ) { fwrite( STDERR, "Twelfth-cycle Round 03 regression tests failed:\n- " . implode( "\n- ", $failures ) . "\n" ); exit( 1 ); }
echo "Twelfth-cycle Round 03 regression tests: PASS\n";
