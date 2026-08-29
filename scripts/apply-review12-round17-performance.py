from pathlib import Path

main = Path('14-global-clinic-usp-integration/global-clinic-usp-integration.php')
s = main.read_text(encoding='utf-8')
old = "define( 'GCU_SCHEMA_VERSION', 10005 );"
new = "define( 'GCU_SCHEMA_VERSION', 10006 );"
if s.count(old) != 1:
    raise SystemExit(f'schema version anchor count={s.count(old)}')
main.write_text(s.replace(old,new,1),encoding='utf-8')

install = Path('14-global-clinic-usp-integration/includes/class-gcu-install.php')
s = install.read_text(encoding='utf-8')
repls = {
"KEY subject_time (subject_hash,occurred_at)) $e;\";": "KEY subject_time (subject_hash,occurred_at),KEY retention_time (occurred_at,id)) $e;\";",
"KEY dispatch_queue (status,next_attempt_at,id)) $e;\";": "KEY dispatch_queue (status,next_attempt_at,id),KEY sent_retention (status,dispatched_at,id)) $e;\";",
"KEY inbox_queue (status,next_attempt_at,id),KEY event_name (event_name)) $e;\";": "KEY inbox_queue (status,next_attempt_at,id),KEY event_name (event_name),KEY processed_retention (status,processed_at,id)) $e;\";",
"KEY usable_token (purpose,expires_at,consumed_at)) $e;\";": "KEY usable_token (purpose,expires_at,consumed_at),KEY expiry_cleanup (expires_at,id),KEY consumed_cleanup (consumed_at,id)) $e;\";",
"KEY status_lock (status,locked_at)) $e;\";": "KEY status_lock (status,locked_at),KEY completed_retention (status,updated_at,id)) $e;\";",
}
for old,new in repls.items():
    if s.count(old)!=1:
        raise SystemExit(f'install index anchor count={s.count(old)}: {old[:40]}')
    s=s.replace(old,new,1)
install.write_text(s,encoding='utf-8')

hard = Path('14-global-clinic-usp-integration/includes/class-gcu-eleventh-review-hardening.php')
s = hard.read_text(encoding='utf-8')
old = "\t\t$constraints = self::verify_schema_constraints();\n\t\tif ( is_wp_error( $constraints ) ) { return $constraints; }\n\t\t$snapshot = self::verify_snapshot_integrity();"
new = "\t\t$constraints = self::verify_schema_constraints();\n\t\tif ( is_wp_error( $constraints ) ) { return $constraints; }\n\t\t$cleanup_indexes = self::verify_cleanup_indexes();\n\t\tif ( is_wp_error( $cleanup_indexes ) ) { return $cleanup_indexes; }\n\t\t$snapshot = self::verify_snapshot_integrity();"
if s.count(old)!=1:
    raise SystemExit(f'verify_all anchor count={s.count(old)}')
s=s.replace(old,new,1)
anchor="\n\tprivate static function verify_snapshot_integrity() {"
method=r'''
	private static function verify_cleanup_indexes() {
		global $wpdb;
		$t = GCU_Install::tables();
		$expected = array(
			$t['events']       => array( 'retention_time' => array( 'occurred_at', 'id' ) ),
			$t['outbox']       => array( 'sent_retention' => array( 'status', 'dispatched_at', 'id' ) ),
			$t['inbox']        => array( 'processed_retention' => array( 'status', 'processed_at', 'id' ) ),
			$t['event_tokens'] => array( 'expiry_cleanup' => array( 'expires_at', 'id' ), 'consumed_cleanup' => array( 'consumed_at', 'id' ) ),
			$t['commands']     => array( 'completed_retention' => array( 'status', 'updated_at', 'id' ) ),
		);
		$missing = array();
		foreach ( $expected as $table => $indexes ) {
			$wpdb->last_error = '';
			$rows = $wpdb->get_results( "SHOW INDEX FROM `$table`", ARRAY_A );
			if ( '' !== (string) $wpdb->last_error || ! is_array( $rows ) ) {
				return new WP_Error( 'gcu_cleanup_index_probe_failed', __( 'File 14 cleanup indexes could not be verified safely.', 'global-clinic-usp-integration' ), array( 'table' => $table ) );
			}
			$actual = array();
			foreach ( $rows as $row ) {
				if ( ! isset( $row['Key_name'], $row['Column_name'] ) ) { continue; }
				$name = (string) $row['Key_name'];
				$seq = isset( $row['Seq_in_index'] ) ? max( 1, (int) $row['Seq_in_index'] ) : count( isset( $actual[$name] ) ? $actual[$name] : array() ) + 1;
				$actual[$name][$seq] = (string) $row['Column_name'];
			}
			foreach ( $actual as $name => $columns ) { ksort( $columns, SORT_NUMERIC ); $actual[$name] = array_values( $columns ); }
			foreach ( $indexes as $name => $columns ) {
				if ( ! isset( $actual[$name] ) || $columns !== $actual[$name] ) { $missing[$table][$name] = $columns; }
			}
		}
		return $missing ? new WP_Error( 'gcu_cleanup_indexes_unverified', __( 'File 14 cleanup indexes are incomplete.', 'global-clinic-usp-integration' ), array( 'missing_indexes' => $missing ) ) : true;
	}
'''
if s.count(anchor)!=1:
    raise SystemExit(f'hardening insertion anchor count={s.count(anchor)}')
s=s.replace(anchor,method+anchor,1)
hard.write_text(s,encoding='utf-8')

test=Path('tests/twelfth-cycle-round17-regression-tests.php')
test.write_text(r'''<?php
$root=dirname(__DIR__);
$main=file_get_contents($root.'/14-global-clinic-usp-integration/global-clinic-usp-integration.php');
$install=file_get_contents($root.'/14-global-clinic-usp-integration/includes/class-gcu-install.php');
$hard=file_get_contents($root.'/14-global-clinic-usp-integration/includes/class-gcu-eleventh-review-hardening.php');
$failures=array();function r12r17_assert($c,$m){global$failures;if(!$c){$failures[]=$m;}}
r12r17_assert(false!==strpos($main,"GCU_SCHEMA_VERSION', 10006"),'Base schema version must advance for cleanup-index migration.');
foreach(array('retention_time (occurred_at,id)','sent_retention (status,dispatched_at,id)','processed_retention (status,processed_at,id)','expiry_cleanup (expires_at,id)','consumed_cleanup (consumed_at,id)','completed_retention (status,updated_at,id)') as $needle){r12r17_assert(false!==strpos($install,$needle),'Missing cleanup index declaration: '.$needle);}
r12r17_assert(false!==strpos($hard,'verify_cleanup_indexes'),'Runtime/activation postconditions must verify cleanup indexes.');
r12r17_assert(false!==strpos($hard,'gcu_cleanup_indexes_unverified'),'Cleanup index verification must fail closed.');
if($failures){fwrite(STDERR,"Twelfth-cycle Round 17 regression tests failed:\n- ".implode("\n- ",$failures)."\n");exit(1);}echo "Twelfth-cycle Round 17 regression tests: PASS\n";
''',encoding='utf-8')
qf=Path('scripts/quality.sh');q=qf.read_text(encoding='utf-8')
anchor='php "$ROOT/tests/twelfth-cycle-round14-regression-tests.php"\n'
if q.count(anchor)!=1: raise SystemExit(f'quality anchor count={q.count(anchor)}')
q=q.replace(anchor,anchor+'php "$ROOT/tests/twelfth-cycle-round17-regression-tests.php"\n',1);qf.write_text(q,encoding='utf-8')
print('Round 17 cleanup-index performance correction applied.')
