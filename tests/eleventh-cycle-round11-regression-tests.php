<?php
$root = dirname( __DIR__ );
$main = file_get_contents( $root . '/14-global-clinic-usp-integration/global-clinic-usp-integration.php' );
$failures = array();
function e11r11_assert( $condition, $message ) { global $failures; if ( ! $condition ) { $failures[] = $message; } }
e11r11_assert( false !== strpos( $main, "array( 'global_clinic', 'how_it_works', 'find_doctor', 'start_clinic' )" ), 'Public route query-var must have an explicit allowlist.' );
e11r11_assert( false !== strpos( $main, "set_query_var( 'gcu_route', '' )" ), 'Unknown File 14 routes must be removed before the File 14 template resolver runs.' );
e11r11_assert( false !== strpos( $main, '$wp_query->set_404()' ) && false !== strpos( $main, 'status_header( 404 )' ), 'Unknown File 14 routes must become an explicit 404.' );
e11r11_assert( false !== strpos( $main, 'nocache_headers()' ), 'Unknown File 14 route failures must not be cacheable.' );
if ( $failures ) { fwrite( STDERR, "Eleventh-cycle Round 11 regression tests failed:\n- " . implode( "\n- ", $failures ) . "\n" ); exit( 1 ); }
echo "Eleventh-cycle Round 11 regression tests: PASS\n";
