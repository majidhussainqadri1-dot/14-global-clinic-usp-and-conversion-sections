<?php
$root = dirname( __DIR__ );
$future = file_get_contents( $root . '/14-global-clinic-usp-integration/includes/class-gcu-future-intelligence.php' );
$failures = array();
function r12r04_assert( $condition, $message ) { global $failures; if ( ! $condition ) { $failures[] = $message; } }
r12r04_assert( false !== strpos( $future, "consume_rate_limit( 'future-report-entry', 5 )" ), 'Public Future report REST entry must be rate-limited before idempotent command storage.' );
r12r04_assert( false !== strpos( $future, 'GCU_Hardening::bounded_text( sanitize_key(' ) && false !== strpos( $future, 'gcu_future_report_block_invalid' ), 'Future report block references must be storage-bounded and validated.' );
r12r04_assert( false !== strpos( $future, 'gcu_future_report_block_query_failed' ), 'Future report block verification must fail closed on DB read failure.' );
r12r04_assert( false !== strpos( $future, 'gcu_future_report_block_invalid' ), 'Unknown Future report block references must be rejected.' );
if ( $failures ) { fwrite( STDERR, "Twelfth-cycle Round 04 regression tests failed:
- " . implode( "
- ", $failures ) . "
" ); exit( 1 ); }
echo "Twelfth-cycle Round 04 regression tests: PASS
";
