<?php
$root = dirname( __DIR__ );
$policy = file_get_contents( $root . '/14-global-clinic-usp-integration/includes/class-gcu-future-policy.php' );
$future = file_get_contents( $root . '/14-global-clinic-usp-integration/includes/class-gcu-future-intelligence.php' );
$failures = array();
function r12r06_assert( $condition, $message ) { global $failures; if ( ! $condition ) { $failures[] = $message; } }
r12r06_assert( false !== strpos( $policy, 'business_policy_contradiction_scan' ), 'Business-policy contradiction scanner must exist.' );
r12r06_assert( false !== strpos( $policy, 'nonzero_platform_commission' ), 'Non-zero platform commission copy must be blocked.' );
r12r06_assert( false !== strpos( $policy, '$business = self::business_policy_contradiction_scan( $current );' ), 'Copy preflight must include business-policy contradiction flags.' );
r12r06_assert( false !== strpos( $future, 'GCU_Future_Policy::business_policy_contradiction_scan( $active_text )' ), 'Active-copy parity sentinel must scan business-policy contradictions.' );
if ( $failures ) { fwrite( STDERR, "Twelfth-cycle Round 06 regression tests failed:\n- " . implode( "\n- ", $failures ) . "\n" ); exit( 1 ); }
echo "Twelfth-cycle Round 06 regression tests: PASS\n";
