from pathlib import Path

future = Path('14-global-clinic-usp-integration/includes/class-gcu-future-intelligence.php')
s = future.read_text(encoding='utf-8')

def once(old, new, label):
    global s
    if s.count(old) != 1:
        raise SystemExit(f'{label} anchor count={s.count(old)}')
    s = s.replace(old, new, 1)

old = "\t\t$rows = $wpdb->get_results( \"SELECT * FROM {$t['claims']} WHERE status='active' AND ((review_due_at IS NOT NULL AND review_due_at<=UTC_TIMESTAMP()) OR (expires_at IS NOT NULL AND expires_at<=UTC_TIMESTAMP())) LIMIT 100\", ARRAY_A );\n\t\t$count = 0;"
new = "\t\t$wpdb->last_error = '';\n\t\t$rows = $wpdb->get_results( \"SELECT * FROM {$t['claims']} WHERE status='active' AND ((review_due_at IS NOT NULL AND review_due_at<=UTC_TIMESTAMP()) OR (expires_at IS NOT NULL AND expires_at<=UTC_TIMESTAMP())) LIMIT 100\", ARRAY_A );\n\t\tif ( '' !== (string) $wpdb->last_error || ! is_array( $rows ) ) {\n\t\t\treturn new WP_Error( 'gcu_future_claim_freshness_query_failed', __( 'Claim freshness could not be evaluated safely.', 'global-clinic-usp-integration' ), array( 'status' => 503 ) );\n\t\t}\n\t\t$count = 0;"
once(old, new, 'claim freshness')

old = "\t\t$rows = $wpdb->get_results( \"SELECT block_key,locale,title,body,cta_label,claim_keys FROM {$t['blocks']} WHERE status='active' ORDER BY block_key,locale\", ARRAY_A );\n\t\t$nodes = array();"
new = "\t\t$wpdb->last_error = '';\n\t\t$rows = $wpdb->get_results( \"SELECT block_key,locale,title,body,cta_label,claim_keys FROM {$t['blocks']} WHERE status='active' ORDER BY block_key,locale LIMIT 1001\", ARRAY_A );\n\t\tif ( '' !== (string) $wpdb->last_error || ! is_array( $rows ) ) {\n\t\t\treturn new WP_Error( 'gcu_future_consistency_query_failed', __( 'Message consistency could not be evaluated safely.', 'global-clinic-usp-integration' ), array( 'status' => 503 ) );\n\t\t}\n\t\tif ( count( $rows ) > 1000 ) {\n\t\t\treturn new WP_Error( 'gcu_future_consistency_scan_ceiling', __( 'Message consistency requires operator review because the active-content scan ceiling was exceeded.', 'global-clinic-usp-integration' ), array( 'status' => 503, 'ceiling' => 1000 ) );\n\t\t}\n\t\t$nodes = array();"
once(old, new, 'consistency graph')

old = "\t\t$rows = $args ? $wpdb->get_results( $wpdb->prepare( $sql, $args ), ARRAY_A ) : $wpdb->get_results( $sql, ARRAY_A );\n\t\tforeach ( is_array( $rows ) ? $rows : array() as &$row ) {"
new = "\t\t$wpdb->last_error = '';\n\t\t$rows = $args ? $wpdb->get_results( $wpdb->prepare( $sql, $args ), ARRAY_A ) : $wpdb->get_results( $sql, ARRAY_A );\n\t\tif ( '' !== (string) $wpdb->last_error || ! is_array( $rows ) ) {\n\t\t\treturn new WP_Error( 'gcu_future_records_query_failed', __( 'Future records could not be read safely.', 'global-clinic-usp-integration' ), array( 'status' => 503 ) );\n\t\t}\n\t\tforeach ( $rows as &$row ) {"
once(old, new, 'records read')
old = "\t\treturn is_array( $rows ) ? $rows : array();"
new = "\t\treturn $rows;"
# This exact return occurs in records() in the current root source.
if old not in s:
    raise SystemExit('records return anchor missing')
s = s.replace(old, new, 1)
future.write_text(s, encoding='utf-8')

test = Path('tests/twelfth-cycle-round14-regression-tests.php')
test.write_text(r'''<?php
$root = dirname( __DIR__ );
$f = file_get_contents( $root . '/14-global-clinic-usp-integration/includes/class-gcu-future-intelligence.php' );
$failures = array();
function r12r14_assert($c,$m){global $failures;if(!$c){$failures[]=$m;}}
r12r14_assert(false!==strpos($f,'gcu_future_claim_freshness_query_failed'),'Freshness sentinel must fail closed on DB read failure.');
r12r14_assert(false!==strpos($f,'gcu_future_consistency_query_failed'),'Consistency graph must fail closed on DB read failure.');
r12r14_assert(false!==strpos($f,"ORDER BY block_key,locale LIMIT 1001"),'Consistency root query must enforce its own ceiling probe.');
r12r14_assert(false!==strpos($f,'gcu_future_records_query_failed'),'Future records read must not turn DB failure into an empty result.');
if($failures){fwrite(STDERR,"Twelfth-cycle Round 14 regression tests failed:\n- ".implode("\n- ",$failures)."\n");exit(1);}echo "Twelfth-cycle Round 14 regression tests: PASS\n";
''', encoding='utf-8')

qf = Path('scripts/quality.sh')
q = qf.read_text(encoding='utf-8')
anchor = 'php "$ROOT/tests/twelfth-cycle-round13-regression-tests.php"\n'
if q.count(anchor) != 1:
    raise SystemExit(f'quality anchor count={q.count(anchor)}')
q = q.replace(anchor, anchor + 'php "$ROOT/tests/twelfth-cycle-round14-regression-tests.php"\n', 1)
qf.write_text(q, encoding='utf-8')
print('Round 14 DB fail-closed corrections applied.')
