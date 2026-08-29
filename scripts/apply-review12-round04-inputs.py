from pathlib import Path

future = Path('14-global-clinic-usp-integration/includes/class-gcu-future-intelligence.php')
s = future.read_text(encoding='utf-8')

old_rest = "\tpublic static function rest_report( WP_REST_Request $request ) {\n\t\t$key = self::required_idempotency_key( $request );"
new_rest = "\tpublic static function rest_report( WP_REST_Request $request ) {\n\t\t$rate = GCU_Plugin::instance()->repository()->consume_rate_limit( 'future-report-entry', 5 );\n\t\tif ( is_wp_error( $rate ) ) { return $rate; }\n\t\t$key = self::required_idempotency_key( $request );"
if s.count(old_rest) != 1:
    raise SystemExit(f'rest_report anchor count={s.count(old_rest)}')
s = s.replace(old_rest, new_rest, 1)

old_block = "\t\t$block = sanitize_key( isset( $data['block_key'] ) ? $data['block_key'] : '' );\n\t\t$locale = GCU_Policy::sanitize_locale( isset( $data['locale'] ) ? $data['locale'] : 'en-US' );"
new_block = "\t\t$block = GCU_Hardening::bounded_text( sanitize_key( isset( $data['block_key'] ) ? $data['block_key'] : '' ), 191 );\n\t\t$locale = GCU_Policy::sanitize_locale( isset( $data['locale'] ) ? $data['locale'] : 'en-US' );\n\t\tif ( $block ) {\n\t\t\tglobal $wpdb;\n\t\t\t$base_tables = GCU_Install::tables();\n\t\t\t$wpdb->last_error = '';\n\t\t\t$known_block = $wpdb->get_var( $wpdb->prepare( \"SELECT block_key FROM {$base_tables['blocks']} WHERE block_key=%s LIMIT 1\", $block ) );\n\t\t\tif ( '' !== (string) $wpdb->last_error ) {\n\t\t\t\treturn new WP_Error( 'gcu_future_report_block_query_failed', __( 'The reported content reference could not be verified safely.', 'global-clinic-usp-integration' ), array( 'status' => 503 ) );\n\t\t\t}\n\t\t\tif ( ! $known_block || ! hash_equals( (string) $known_block, (string) $block ) ) {\n\t\t\t\treturn new WP_Error( 'gcu_future_report_block_invalid', __( 'The reported content reference is not a known File 14 block.', 'global-clinic-usp-integration' ), array( 'status' => 400 ) );\n\t\t\t}\n\t\t}"
if s.count(old_block) != 1:
    raise SystemExit(f'block anchor count={s.count(old_block)}')
s = s.replace(old_block, new_block, 1)
future.write_text(s, encoding='utf-8')

test = Path('tests/twelfth-cycle-round04-regression-tests.php')
test.write_text("""<?php
$root = dirname( __DIR__ );
$future = file_get_contents( $root . '/14-global-clinic-usp-integration/includes/class-gcu-future-intelligence.php' );
$failures = array();
function r12r04_assert( $condition, $message ) { global $failures; if ( ! $condition ) { $failures[] = $message; } }
r12r04_assert( false !== strpos( $future, "consume_rate_limit( 'future-report-entry', 5 )" ), 'Public Future report REST entry must be rate-limited before idempotent command storage.' );
r12r04_assert( false !== strpos( $future, "GCU_Hardening::bounded_text( sanitize_key( isset( $data['block_key'] ) ? $data['block_key'] : '' ), 191 )" ), 'Future report block references must be storage-bounded.' );
r12r04_assert( false !== strpos( $future, 'gcu_future_report_block_query_failed' ), 'Future report block verification must fail closed on DB read failure.' );
r12r04_assert( false !== strpos( $future, 'gcu_future_report_block_invalid' ), 'Unknown Future report block references must be rejected.' );
if ( $failures ) { fwrite( STDERR, "Twelfth-cycle Round 04 regression tests failed:\n- " . implode( "\n- ", $failures ) . "\n" ); exit( 1 ); }
echo "Twelfth-cycle Round 04 regression tests: PASS\n";
""", encoding='utf-8')

quality = Path('scripts/quality.sh')
q = quality.read_text(encoding='utf-8')
anchor = 'php "$ROOT/tests/twelfth-cycle-round03-regression-tests.php"\n'
if q.count(anchor) != 1:
    raise SystemExit(f'quality anchor count={q.count(anchor)}')
q = q.replace(anchor, anchor + 'php "$ROOT/tests/twelfth-cycle-round04-regression-tests.php"\n', 1)
temp_anchor = '"$ROOT/scripts/apply-review12-round03-route.py"; do'
if q.count(temp_anchor) != 1:
    raise SystemExit(f'temp anchor count={q.count(temp_anchor)}')
q = q.replace(temp_anchor, '"$ROOT/scripts/apply-review12-round03-route.py" "$ROOT/.github/workflows/file14-review12-round04-apply.yml" "$ROOT/scripts/apply-review12-round04-inputs.py"; do', 1)
quality.write_text(q, encoding='utf-8')

print('Round 04 input/abuse corrections and regression gate applied.')
