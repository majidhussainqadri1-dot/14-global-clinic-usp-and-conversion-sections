from pathlib import Path

repo = Path('14-global-clinic-usp-integration/includes/class-gcu-repository.php')
s = repo.read_text(encoding='utf-8')
old = "SELECT id,slot_key,audience FROM {$t['blocks']} WHERE block_key=%s AND status='active' ORDER BY content_version DESC LIMIT 1"
new = "SELECT id,slot_key,audience,cta_destination FROM {$t['blocks']} WHERE block_key=%s AND status='active' ORDER BY content_version DESC LIMIT 1"
if s.count(old) != 1:
    raise SystemExit(f'placement block select anchor count={s.count(old)}')
s = s.replace(old, new, 1)
old2 = "$audience_ok=$b&&('all'===$block_audience||$block_audience===$placement_audience);if(!$b||$b['slot_key']!==$row['slot_key']||!$audience_ok||!GCU_Plugin::instance()->contracts()->placement_ready($row)){return new WP_Error('gcu_placement_unready'"
new2 = "$audience_ok=$b&&('all'===$block_audience||$block_audience===$placement_audience);$destination_ok=true;if($b&&!empty($b['cta_destination'])){$destination_health=GCU_Plugin::instance()->contracts()->destination($b['cta_destination']);$destination_ok=!empty($destination_health['available'])&&!empty($destination_health['url']);}if(!$b||$b['slot_key']!==$row['slot_key']||!$audience_ok||!$destination_ok||!GCU_Plugin::instance()->contracts()->placement_ready($row)){return new WP_Error('gcu_placement_unready'"
if s.count(old2) != 1:
    raise SystemExit(f'placement readiness anchor count={s.count(old2)}')
s = s.replace(old2, new2, 1)
repo.write_text(s, encoding='utf-8')

test = Path('tests/twelfth-cycle-round07-regression-tests.php')
test.write_text("""<?php
$root = dirname( __DIR__ );
$repo = file_get_contents( $root . '/14-global-clinic-usp-integration/includes/class-gcu-repository.php' );
$failures = array();
function r12r07_assert( $condition, $message ) { global $failures; if ( ! $condition ) { $failures[] = $message; } }
r12r07_assert( false !== strpos( $repo, 'SELECT id,slot_key,audience,cta_destination' ), 'Placement activation must load the linked CTA destination.' );
r12r07_assert( false !== strpos( $repo, '$destination_health=GCU_Plugin::instance()->contracts()->destination($b[' . "'cta_destination'" . ']);' ), 'Placement activation must re-check destination owner health.' );
r12r07_assert( false !== strpos( $repo, '!$destination_ok||!GCU_Plugin::instance()->contracts()->placement_ready($row)' ), 'Destination readiness must be a hard placement activation gate.' );
if ( $failures ) { fwrite( STDERR, "Twelfth-cycle Round 07 regression tests failed:\n- " . implode( "\n- ", $failures ) . "\n" ); exit( 1 ); }
echo "Twelfth-cycle Round 07 regression tests: PASS\n";
""", encoding='utf-8')

quality = Path('scripts/quality.sh')
q = quality.read_text(encoding='utf-8')
anchor = 'php "$ROOT/tests/twelfth-cycle-round06-regression-tests.php"\n'
if q.count(anchor) != 1:
    raise SystemExit(f'quality anchor count={q.count(anchor)}')
q = q.replace(anchor, anchor + 'php "$ROOT/tests/twelfth-cycle-round07-regression-tests.php"\n', 1)
quality.write_text(q, encoding='utf-8')
print('Round 07 destination readiness hardening applied.')
