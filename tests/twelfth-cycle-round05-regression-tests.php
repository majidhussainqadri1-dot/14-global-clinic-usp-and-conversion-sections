<?php
$root = dirname( __DIR__ );
$repo = file_get_contents( $root . '/14-global-clinic-usp-integration/includes/class-gcu-repository.php' );
$failures = array();
function r12r05_assert( $condition, $message ) { global $failures; if ( ! $condition ) { $failures[] = $message; } }
r12r05_assert( false !== strpos( $repo, 'gcu_workflow_locked_read_failed' ), 'Workflow transition must re-read the record after lock/transaction acquisition.' );
r12r05_assert( false !== strpos( $repo, '$locked_validation=$this->validate_transition_target($machine,$locked_row,$target)' ), 'Workflow dependencies must be revalidated under the workflow lock.' );
r12r05_assert( false !== strpos( $repo, '$row=$locked_row;$u=array(' ), 'Mutation and audit state must use the locked authoritative row.' );
if ( $failures ) { fwrite( STDERR, "Twelfth-cycle Round 05 regression tests failed:
- " . implode( "
- ", $failures ) . "
" ); exit( 1 ); }
echo "Twelfth-cycle Round 05 regression tests: PASS
";
