<?php
$root = dirname( __DIR__ );
$repo = file_get_contents( $root . '/14-global-clinic-usp-integration/includes/class-gcu-repository.php' );
$failures = array();
function r12r07_assert( $condition, $message ) { global $failures; if ( ! $condition ) { $failures[] = $message; } }
r12r07_assert( false !== strpos( $repo, 'SELECT id,slot_key,audience,cta_destination' ), 'Placement activation must load the linked CTA destination.' );
r12r07_assert( false !== strpos( $repo, '$destination_health=GCU_Plugin::instance()->contracts()->destination($b[' . "'cta_destination'" . ']);' ), 'Placement activation must re-check destination owner health.' );
r12r07_assert( false !== strpos( $repo, '!$destination_ok||!GCU_Plugin::instance()->contracts()->placement_ready($row)' ), 'Destination readiness must be a hard placement activation gate.' );
if ( $failures ) { fwrite( STDERR, "Twelfth-cycle Round 07 regression tests failed:
- " . implode( "
- ", $failures ) . "
" ); exit( 1 ); }
echo "Twelfth-cycle Round 07 regression tests: PASS
";
