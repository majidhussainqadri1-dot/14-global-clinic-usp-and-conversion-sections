from pathlib import Path

policy=Path('14-global-clinic-usp-integration/includes/class-gcu-future-policy.php')
p=policy.read_text(encoding='utf-8')
old="""\t\tforeach ( $variants as $index => $variant ) {\n\t\t\t$text = is_array( $variant ) ? wp_json_encode( $variant ) : (string) $variant;\n\t\t\t$scan = self::dark_pattern_scan( $text );\n\t\t\tforeach ( $scan['flags'] as $flag ) {\n\t\t\t\t$flags[] = 'variant_' . absint( $index ) . ':' . $flag;\n\t\t\t}\n\t\t}\n"""
new="""\t\tforeach ( $variants as $index => $variant ) {\n\t\t\t$text = is_array( $variant ) ? wp_json_encode( $variant ) : (string) $variant;\n\t\t\t$scan = self::dark_pattern_scan( $text );\n\t\t\t$business = self::business_policy_contradiction_scan( $text );\n\t\t\tforeach ( array_merge( $scan['flags'], $business['flags'] ) as $flag ) {\n\t\t\t\t$flags[] = 'variant_' . absint( $index ) . ':' . $flag;\n\t\t\t}\n\t\t}\n"""
if p.count(old)!=1: raise SystemExit(f'experiment preflight anchor count={p.count(old)}')
policy.write_text(p.replace(old,new,1),encoding='utf-8')

hard=Path('14-global-clinic-usp-integration/includes/class-gcu-fifth-review-hardening.php')
h=hard.read_text(encoding='utf-8')
old2="""\t\t$tables = GCU_Install::tables();\n\t\t$rows = $wpdb->get_results( \"SELECT * FROM {$tables['experiments']} WHERE status='running' LIMIT 50\", ARRAY_A );\n\t\t$count = 0;\n"""
new2="""\t\t$tables = GCU_Install::tables();\n\t\t$wpdb->last_error = '';\n\t\t$rows = $wpdb->get_results( \"SELECT * FROM {$tables['experiments']} WHERE status='running' LIMIT 50\", ARRAY_A );\n\t\tif ( '' !== (string) $wpdb->last_error || ! is_array( $rows ) ) {\n\t\t\tupdate_option( GCU_Future_Intelligence::SAFE_MODE_OPTION, 1, false );\n\t\t\tGCU_Observability::log( 'error', 'future_early_stop_experiment_query_failed', array( 'containment' => 'future_safe_mode' ) );\n\t\t\treturn new WP_Error( 'gcu_future_early_stop_experiment_query_failed', __( 'Running experiments could not be verified for the mandatory safety stop.', 'global-clinic-usp-integration' ), array( 'status' => 503 ) );\n\t\t}\n\t\t$count = 0;\n"""
if h.count(old2)!=1: raise SystemExit(f'early-stop query anchor count={h.count(old2)}')
hard.write_text(h.replace(old2,new2,1),encoding='utf-8')

test=Path('tests/twelfth-cycle-round12-regression-tests.php')
test.write_text(r'''<?php
$root=dirname(__DIR__);
$policy=file_get_contents($root.'/14-global-clinic-usp-integration/includes/class-gcu-future-policy.php');
$hard=file_get_contents($root.'/14-global-clinic-usp-integration/includes/class-gcu-fifth-review-hardening.php');
$failures=array();
function r12r12_assert($ok,$m){global $failures;if(!$ok){$failures[]=$m;}}
r12r12_assert(false!==strpos($policy,'business_policy_contradiction_scan( $text )'),'Experiment variants must run the business-policy contradiction scanner.');
r12r12_assert(false!==strpos($policy,'array_merge( $scan'),'Experiment preflight must merge variant policy flags.');
r12r12_assert(false!==strpos($hard,'future_early_stop_experiment_query_failed'),'Early-stop experiment query failure must have a stable failure code.');
r12r12_assert(false!==strpos($hard,'update_option( GCU_Future_Intelligence::SAFE_MODE_OPTION, 1, false )'),'Early-stop experiment query failure must enter Future safe mode.');
if($failures){fwrite(STDERR,"Twelfth-cycle Round 12 regression tests failed:\n- ".implode("\n- ",$failures)."\n");exit(1);}echo "Twelfth-cycle Round 12 regression tests: PASS\n";
''',encoding='utf-8')

quality=Path('scripts/quality.sh')
q=quality.read_text(encoding='utf-8')
anchor='php "$ROOT/tests/twelfth-cycle-round08-regression-tests.php"\n'
if q.count(anchor)!=1: raise SystemExit(f'quality anchor count={q.count(anchor)}')
quality.write_text(q.replace(anchor,anchor+'php "$ROOT/tests/twelfth-cycle-round12-regression-tests.php"\n',1),encoding='utf-8')
print('Round 12 experiment safety hardening applied.')
