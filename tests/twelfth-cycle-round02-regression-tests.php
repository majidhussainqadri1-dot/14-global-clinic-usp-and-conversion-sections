<?php
$root = dirname( __DIR__ );
$future = file_get_contents( $root . '/14-global-clinic-usp-integration/includes/class-gcu-future-intelligence.php' );
$failures = array();
function t12r02_assert( $condition, $message ) { global $failures; if ( ! $condition ) { $failures[] = $message; } }

t12r02_assert( false !== strpos( $future, "'permission_callback' => array( __CLASS__, 'can_revalidate_claim' )" ), 'Claim revalidation REST route must use an object-aware permission callback.' );
t12r02_assert( false !== strpos( $future, "GCU_Capabilities::require_capability( GCU_Capabilities::APPROVE_CLAIMS, $key, 'future_intelligence_claims' )" ), 'Canonical claim revalidation must reauthorize the exact claim object.' );
t12r02_assert( false !== strpos( $future, "GCU_Capabilities::require_capability(GCU_Capabilities::MANAGE_CONTENT,$id,'future_intelligence_content')" ), 'Canonical report resolution must reauthorize the exact report object.' );
t12r02_assert( false !== strpos( $future, "GCU_Capabilities::can( GCU_Capabilities::MANAGE_CONTENT, $id, 'future_intelligence_content' )" ), 'Admin report action must bind authorization to the submitted report identifier.' );
t12r02_assert( false !== strpos( $future, 'public static function can_revalidate_claim( WP_REST_Request $request )' ) && false !== strpos( $future, "GCU_Capabilities::can( GCU_Capabilities::APPROVE_CLAIMS, $key, 'future_intelligence_claims' )" ), 'Object-aware claim permission callback must bind the route claim key.' );

if ( $failures ) { fwrite( STDERR, "Twelfth-cycle Round 02 regression tests failed:\n- " . implode( "\n- ", $failures ) . "\n" ); exit( 1 ); }
echo "Twelfth-cycle Round 02 regression tests: PASS\n";
