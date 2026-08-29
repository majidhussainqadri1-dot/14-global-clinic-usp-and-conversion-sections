from pathlib import Path

repo = Path('14-global-clinic-usp-integration/includes/class-gcu-repository.php')
s = repo.read_text(encoding='utf-8')
old = "try{if(!$this->begin_owned_transaction()){return new WP_Error('gcu_transaction_failed',__('The workflow transaction could not start.','global-clinic-usp-integration'),array('status'=>500));}$u=array('status'=>$target,'row_version'=>(int)$row['row_version']+1,'updated_at'=>current_time('mysql',true));"
new = "try{if(!$this->begin_owned_transaction()){return new WP_Error('gcu_transaction_failed',__('The workflow transaction could not start.','global-clinic-usp-integration'),array('status'=>500));}$wpdb->last_error='';$locked_row=$wpdb->get_row($wpdb->prepare(\"SELECT * FROM `$table` WHERE public_id=%s\",sanitize_text_field($id)),ARRAY_A);if(''!==(string)$wpdb->last_error){$this->rollback_owned_transaction();return new WP_Error('gcu_workflow_locked_read_failed',__('The workflow record could not be revalidated under its lock.','global-clinic-usp-integration'),array('status'=>503));}if(!$locked_row||(int)$locked_row['row_version']!==(int)$expected||!hash_equals((string)$locked_row['status'],(string)$row['status'])){$this->rollback_owned_transaction();return new WP_Error('gcu_concurrent_update',__('The workflow changed before its locked transition could be applied. Reload it.','global-clinic-usp-integration'),array('status'=>409));}$locked_validation=$this->validate_transition_target($machine,$locked_row,$target);if(is_wp_error($locked_validation)){$this->rollback_owned_transaction();return$locked_validation;}$row=$locked_row;$u=array('status'=>$target,'row_version'=>(int)$row['row_version']+1,'updated_at'=>current_time('mysql',true));"
if s.count(old) != 1:
    raise SystemExit(f'transition anchor count={s.count(old)}')
s = s.replace(old, new, 1)
repo.write_text(s, encoding='utf-8')

test = Path('tests/twelfth-cycle-round05-regression-tests.php')
test.write_text("""<?php
$root = dirname( __DIR__ );
$repo = file_get_contents( $root . '/14-global-clinic-usp-integration/includes/class-gcu-repository.php' );
$failures = array();
function r12r05_assert( $condition, $message ) { global $failures; if ( ! $condition ) { $failures[] = $message; } }
r12r05_assert( false !== strpos( $repo, 'gcu_workflow_locked_read_failed' ), 'Workflow transition must re-read the record after lock/transaction acquisition.' );
r12r05_assert( false !== strpos( $repo, '$locked_validation=$this->validate_transition_target($machine,$locked_row,$target)' ), 'Workflow dependencies must be revalidated under the workflow lock.' );
r12r05_assert( false !== strpos( $repo, '$row=$locked_row;$u=array(' ), 'Mutation and audit state must use the locked authoritative row.' );
if ( $failures ) { fwrite( STDERR, "Twelfth-cycle Round 05 regression tests failed:\n- " . implode( "\n- ", $failures ) . "\n" ); exit( 1 ); }
echo "Twelfth-cycle Round 05 regression tests: PASS\n";
""", encoding='utf-8')

quality = Path('scripts/quality.sh')
q = quality.read_text(encoding='utf-8')
anchor = 'php "$ROOT/tests/twelfth-cycle-round04-regression-tests.php"\n'
if q.count(anchor) != 1:
    raise SystemExit(f'quality anchor count={q.count(anchor)}')
q = q.replace(anchor, anchor + 'php "$ROOT/tests/twelfth-cycle-round05-regression-tests.php"\n', 1)
quality.write_text(q, encoding='utf-8')
print('Round 05 locked revalidation correction applied.')
